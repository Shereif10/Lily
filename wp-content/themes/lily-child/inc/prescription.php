<?php
/**
 * Lily Prescription Power.
 *
 * Product-level Yes/No setting. When enabled, the single product page
 * shows Right Eye (OD) / Left Eye (OS) power selectors that travel with
 * the cart item into the order as standard WooCommerce metadata.
 *
 * @package Lily
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Available power options (development range).
 *
 * Style-focused feature: no medical claims are made about any value.
 */
function lily_rx_power_options() {
	return array(
		'+6.00', '+5.50', '+5.00', '+4.50', '+4.00', '+3.50', '+3.00',
		'+2.50', '+2.00', '+1.50', '+1.00', '+0.50', '0.00', '-0.50',
		'-1.00', '-1.50', '-2.00', '-2.50', '-3.00', '-3.50', '-4.00',
		'-4.50', '-5.00', '-5.50', '-6.00',
	);
}

/**
 * Whether a product supports prescription power.
 *
 * @param WC_Product|int $product Product object or ID.
 * @return bool
 */
function lily_rx_is_enabled( $product ) {
	$product_id = ( $product instanceof WC_Product ) ? $product->get_id() : absint( $product );
	return 'yes' === get_post_meta( $product_id, '_lily_prescription_power', true );
}

/* ── Admin: managed in the Lily Product Editor (Section 6) ──────────── */
/* The With/Without Power field renders in the Lily Product Editor; the save
 * below still processes the same canonical POST key. */

add_action( 'woocommerce_admin_process_product_object', 'lily_rx_admin_save' );

/**
 * Save the admin Prescription Power value.
 *
 * @param WC_Product $product Product being saved.
 */
function lily_rx_admin_save( $product ) {
	$value = isset( $_POST['_lily_prescription_power'] ) ? wc_clean( wp_unslash( $_POST['_lily_prescription_power'] ) ) : 'no'; // phpcs:ignore WordPress.Security.NonceVerification.Missing -- nonce verified by WooCommerce.
	$product->update_meta_data( '_lily_prescription_power', 'yes' === $value ? 'yes' : 'no' );
}

/* ── Frontend: two eye selectors inside the add-to-cart form ────────── */

add_action( 'woocommerce_before_add_to_cart_button', 'lily_rx_render_selectors' );

/**
 * Render Right/Left eye selects inside form.cart so values submit natively.
 */
function lily_rx_render_selectors() {
	global $product;

	if ( ! $product instanceof WC_Product || ! lily_rx_is_enabled( $product ) ) {
		return;
	}

	// Out of Stock products never present the selectors — nothing can be
	// added to the cart, so there is nothing to select powers for.
	if ( ! $product->is_in_stock() ) {
		return;
	}

	$options = lily_rx_power_options();
	?>
	<div class="lily-rx">
		<h2 class="lily-sp-section-title">
			<svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="6" cy="15" r="4"/><circle cx="18" cy="15" r="4"/><path d="M9.5 13.5 14 6h3"/></svg>
			<?php esc_html_e( 'Prescription Power', 'lily' ); ?>
		</h2>
		<div class="lily-rx__grid">
			<p class="lily-rx__field">
				<label for="lily-power-od"><?php esc_html_e( 'Right Eye (OD)', 'lily' ); ?></label>
				<select id="lily-power-od" name="lily_power_od" class="lily-rx__select" required>
					<option value="" selected><?php esc_html_e( 'Select power', 'lily' ); ?></option>
					<?php foreach ( $options as $option ) : ?>
						<option value="<?php echo esc_attr( $option ); ?>"><?php echo esc_html( $option ); ?></option>
					<?php endforeach; ?>
				</select>
			</p>
			<p class="lily-rx__field">
				<label for="lily-power-os"><?php esc_html_e( 'Left Eye (OS)', 'lily' ); ?></label>
				<select id="lily-power-os" name="lily_power_os" class="lily-rx__select" required>
					<option value="" selected><?php esc_html_e( 'Select power', 'lily' ); ?></option>
					<?php foreach ( $options as $option ) : ?>
						<option value="<?php echo esc_attr( $option ); ?>"><?php echo esc_html( $option ); ?></option>
					<?php endforeach; ?>
				</select>
			</p>
		</div>
		<p class="lily-rx__hint"><?php esc_html_e( 'Choose 0.00 for an eye that does not need power.', 'lily' ); ?></p>
	</div>
	<?php
}

add_action( 'woocommerce_before_add_to_cart_quantity', 'lily_rx_quantity_label' );

/**
 * Small QUANTITY heading above the quantity control, per the reference.
 */
function lily_rx_quantity_label() {
	echo '<p class="lily-sp-qty-label">' . esc_html__( 'Quantity', 'lily' ) . '</p>';
}

/* ── Validation + storage ───────────────────────────────────────────── */

add_filter( 'woocommerce_add_to_cart_validation', 'lily_rx_validate_add_to_cart', 10, 3 );

/**
 * Require both eye selections for products with prescription power.
 * 0.00 counts as a valid explicit selection.
 *
 * @param bool $passed     Validation result.
 * @param int  $product_id Product ID being added.
 * @return bool
 */
function lily_rx_validate_add_to_cart( $passed, $product_id ) {
	if ( ! $passed || ! lily_rx_is_enabled( $product_id ) ) {
		return $passed;
	}

	$valid = lily_rx_power_options();
	$od    = isset( $_POST['lily_power_od'] ) ? wc_clean( wp_unslash( $_POST['lily_power_od'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing
	$os    = isset( $_POST['lily_power_os'] ) ? wc_clean( wp_unslash( $_POST['lily_power_os'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing

	if ( '' === $od || '' === $os || ! in_array( $od, $valid, true ) || ! in_array( $os, $valid, true ) ) {
		wc_add_notice( __( 'Please select the prescription power for both eyes.', 'lily' ), 'error' );
		return false;
	}

	return true;
}

add_filter( 'woocommerce_add_cart_item_data', 'lily_rx_cart_item_data', 10, 2 );

/**
 * Store the selected powers on the cart item.
 *
 * @param array $cart_item_data Cart item data.
 * @param int   $product_id     Product ID.
 * @return array
 */
function lily_rx_cart_item_data( $cart_item_data, $product_id ) {
	if ( ! lily_rx_is_enabled( $product_id ) || empty( $_POST['lily_power_od'] ) && empty( $_POST['lily_power_os'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
		return $cart_item_data;
	}

	$valid = lily_rx_power_options();
	$od    = isset( $_POST['lily_power_od'] ) ? wc_clean( wp_unslash( $_POST['lily_power_od'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing
	$os    = isset( $_POST['lily_power_os'] ) ? wc_clean( wp_unslash( $_POST['lily_power_os'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing

	if ( in_array( $od, $valid, true ) && in_array( $os, $valid, true ) ) {
		$cart_item_data['lily_rx'] = array(
			'od' => $od,
			'os' => $os,
		);
	}

	return $cart_item_data;
}

add_filter( 'woocommerce_get_cart_item_from_session', 'lily_rx_restore_session', 10, 2 );

/**
 * Keep prescription data across page loads / sessions.
 *
 * @param array $cart_item Cart item.
 * @param array $values    Stored session values.
 * @return array
 */
function lily_rx_restore_session( $cart_item, $values ) {
	if ( ! empty( $values['lily_rx'] ) ) {
		$cart_item['lily_rx'] = $values['lily_rx'];
	}
	return $cart_item;
}

/* ── Display ────────────────────────────────────────────────────────── */

add_filter( 'woocommerce_get_item_data', 'lily_rx_cart_display', 10, 2 );

/**
 * Show prescription details beneath the product name in cart/checkout.
 *
 * @param array $item_data Display data.
 * @param array $cart_item Cart item.
 * @return array
 */
function lily_rx_cart_display( $item_data, $cart_item ) {
	if ( empty( $cart_item['lily_rx'] ) ) {
		return $item_data;
	}

	$item_data[] = array(
		'key'     => __( 'Prescription Power', 'lily' ),
		/* translators: 1: right eye power, 2: left eye power */
		'value'   => sprintf( __( 'Right Eye: %1$s · Left Eye: %2$s', 'lily' ), $cart_item['lily_rx']['od'], $cart_item['lily_rx']['os'] ),
		'display' => '',
	);

	return $item_data;
}

add_action( 'woocommerce_check_cart_items', 'lily_rx_check_cart_items' );

/**
 * Cart + checkout validation layer.
 *
 * Fires when the cart page renders, when the checkout page renders and —
 * critically — inside WC_Checkout::validate_checkout() during order
 * processing, so an incomplete prescription item can never reach the
 * "order created" step. Non-prescription products are untouched.
 */
function lily_rx_check_cart_items() {
	if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
		return;
	}

	$valid = lily_rx_power_options();

	foreach ( WC()->cart->get_cart() as $cart_item ) {
		$product_id   = ! empty( $cart_item['product_id'] ) ? absint( $cart_item['product_id'] ) : 0;
		$variation_id = ! empty( $cart_item['variation_id'] ) ? absint( $cart_item['variation_id'] ) : 0;

		// The setting lives on the parent product; check both IDs for variations.
		if ( ! lily_rx_is_enabled( $product_id ) && ! lily_rx_is_enabled( $variation_id ) ) {
			continue;
		}

		$od = isset( $cart_item['lily_rx']['od'] ) ? (string) $cart_item['lily_rx']['od'] : '';
		$os = isset( $cart_item['lily_rx']['os'] ) ? (string) $cart_item['lily_rx']['os'] : '';

		if ( '' === $od || '' === $os || ! in_array( $od, $valid, true ) || ! in_array( $os, $valid, true ) ) {
			$_product = wc_get_product( $product_id );
			$name     = ( $_product && ! $_product->is_type( 'variation' ) ) ? $_product->get_name() : '';

			if ( '' === $name && $variation_id ) {
				$variation = wc_get_product( $variation_id );
				if ( $variation && function_exists( 'wc_get_product' ) ) {
					$parent = wc_get_product( $variation->get_parent_id() );
					$name   = $parent ? $parent->get_name() : '';
				}
			}

			if ( $name ) {
				wc_add_notice(
					sprintf( __( '%1$s requires prescription power selection for both eyes.', 'lily' ), $name ),
					'error'
				);
			} else {
				wc_add_notice( __( 'Please select the prescription power for both eyes.', 'lily' ), 'error' );
			}
		}
	}
}

add_action( 'woocommerce_checkout_create_order_line_item', 'lily_rx_order_item_meta', 10, 4 );

/**
 * Persist readable prescription values on the order item.
 *
 * @param WC_Order_Item_Product $item          Order item.
 * @param string                $cart_item_key Cart item key.
 * @param array                 $values        Cart item values.
 */
function lily_rx_order_item_meta( $item, $cart_item_key, $values ) {
	if ( empty( $values['lily_rx'] ) ) {
		return;
	}

	$item->add_meta_data( __( 'Prescription Power', 'lily' ), __( 'Yes', 'lily' ), true );
	$item->add_meta_data( __( 'Right Eye (OD)', 'lily' ), $values['lily_rx']['od'], true );
	$item->add_meta_data( __( 'Left Eye (OS)', 'lily' ), $values['lily_rx']['os'], true );
}
