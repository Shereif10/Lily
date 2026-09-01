<?php
/**
 * Lily Cart Drawer.
 *
 * A native WooCommerce presentation layer: the drawer always renders the real
 * WC()->cart session contents. Add / update / remove all go through core cart
 * methods, so validation (incl. Prescription Power), shipping and totals stay
 * exactly as implemented. No custom cart storage of any kind.
 *
 * @package Lily
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* ── Shared item rendering (used by the Cart Page AND the drawer) ───── */

if ( ! function_exists( 'lily_cart_items_html' ) ) {
	/**
	 * Render the Lily cart items list from the live WooCommerce cart.
	 *
	 * @param string $context 'page' renders native qty input names; 'drawer' uses data attributes only.
	 * @return string
	 */
	function lily_cart_items_html( $context = 'page' ) {
		if ( ! function_exists( 'WC' ) || ! WC()->cart || WC()->cart->is_empty() ) {
			return '';
		}

		ob_start();
		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
			$_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
			$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

			if ( ! $_product || ! $_product->exists() || $cart_item['quantity'] <= 0 || ! apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
				continue;
			}

			$permalink    = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
			$item_data    = apply_filters( 'woocommerce_get_item_data', array(), $cart_item );
			$thumb_markup = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image( 'woocommerce_thumbnail' ), $cart_item, $cart_item_key );
			$name_markup  = apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key );
			$price_markup = apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key );
			$subtotal     = apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key );
			$remove_url   = wc_get_cart_remove_url( $cart_item_key );

			/* Brand for this product */
			$brand_taxonomy = function_exists( 'lily_get_brand_taxonomy' ) ? lily_get_brand_taxonomy() : '';
			$brand_name     = '';
			if ( $brand_taxonomy ) {
				$brand_terms = wp_get_post_terms( $_product->get_id(), $brand_taxonomy, array( 'fields' => 'names' ) );
				if ( ! is_wp_error( $brand_terms ) && ! empty( $brand_terms ) ) {
					$brand_name = $brand_terms[0];
				}
			}
			?>
			<div class="lily-cart-item">

				<a class="lily-cart-item__image" href="<?php echo esc_url( $permalink ); ?>">
					<?php echo wp_kses_post( $thumb_markup ); ?>
				</a>

				<div class="lily-cart-item__body">
					<?php if ( $brand_name ) : ?>
						<span class="lily-cart-item__brand"><?php echo esc_html( $brand_name ); ?></span>
					<?php endif; ?>

					<a class="lily-cart-item__name" href="<?php echo esc_url( $permalink ); ?>">
						<?php echo wp_kses_post( $name_markup ); ?>
					</a>

					<?php if ( ! empty( $item_data ) ) : ?>
						<dl class="lily-cart-item__meta">
							<?php foreach ( $item_data as $data_row ) : ?>
								<div class="lily-cart-item__meta-row">
									<dt><?php echo wp_kses_post( $data_row['key'] ); ?></dt>
									<dd><?php echo wp_kses_post( $data_row['value'] ); ?></dd>
								</div>
							<?php endforeach; ?>
						</dl>
					<?php endif; ?>

					<div class="lily-cart-item__row">
						<span class="lily-cart-item__price"><?php echo wp_kses_post( $price_markup ); ?></span>

						<div class="lily-cart-item__qty">
							<?php
							if ( $_product->is_sold_individually() ) {
								echo '<input type="hidden" class="qty" value="1" />';
							} else {
								echo woocommerce_quantity_input( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- WC template output.
									array(
										'input_value'  => $cart_item['quantity'],
										'max_value'    => $_product->get_max_purchase_quantity(),
										'min_value'    => '0',
										'product_name' => $_product->get_name(),
									),
									$_product,
									false
								);
							}
							?>
						</div>

						<span class="lily-cart-item__subtotal"><?php echo wp_kses_post( $subtotal ); ?></span>
					</div>
				</div>

				<a href="<?php echo esc_url( $remove_url ); ?>" class="lily-cart-item__remove" aria-label="<?php echo esc_attr( sprintf( __( 'Remove %s from cart', 'lily' ), wp_strip_all_tags( $name_markup ) ) ); ?>" data-product_id="<?php echo esc_attr( $product_id ); ?>" data-cart_item_key="<?php echo esc_attr( $cart_item_key ); ?>">
					<svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 6h18"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/></svg>
				</a>
			</div>
			<?php
		}
		return ob_get_clean();
	}
}

/* ── Drawer body + markup ────────────────────────────────────────────── */

if ( ! function_exists( 'lily_drawer_body_html' ) ) {
	/**
	 * The scrollable middle of the drawer: items or empty state, plus totals + CTA.
	 *
	 * @return string
	 */
	function lily_drawer_body_html() {
		ob_start();

		if ( empty( WC()->cart ) || WC()->cart->is_empty() ) :
			?>
			<div class="lily-drawer__empty">
				<p class="lily-drawer__empty-title"><?php esc_html_e( 'Your cart is empty.', 'lily' ); ?></p>
				<a class="lily-drawer__empty-cta" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>">
					<?php esc_html_e( 'RETURN TO SHOP', 'lily' ); ?>
				</a>
			</div>
		<?php else : ?>
			<div class="lily-drawer__items">
				<?php echo lily_cart_items_html( 'drawer' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped internally. ?>
			</div>

			<div class="lily-drawer__footer">
				<dl class="lily-cart-totals lily-drawer__totals">
					<div class="lily-cart-totals__row">
						<dt><?php esc_html_e( 'Subtotal', 'lily' ); ?></dt>
						<dd><?php echo wp_kses_post( WC()->cart->get_cart_subtotal() ); ?></dd>
					</div>
					<div class="lily-cart-totals__row">
						<dt><?php esc_html_e( 'Shipping', 'lily' ); ?></dt>
						<dd><?php echo esc_html( apply_filters( 'lily_cart_shipping_note', __( 'Calculated at checkout', 'lily' ) ) ); ?></dd>
					</div>
				</dl>

				<a class="lily-cart-checkout-btn lily-cart-checkout-btn--primary lily-drawer__checkout"
					href="<?php echo esc_url( wc_get_checkout_url() ); ?>">
					<?php esc_html_e( 'CHECKOUT', 'lily' ); ?>
				</a>
			</div>
		<?php endif;

		return ob_get_clean();
	}
}

add_action( 'wp_footer', 'lily_cart_drawer_output', 40 );

/**
 * Print the drawer skeleton in the footer. Content is server-rendered from the
 * real WooCommerce session on every page load and refreshed via AJAX afterwards.
 */
function lily_cart_drawer_output() {
	if ( ! function_exists( 'WC' ) || is_admin() ) {
		return;
	}

	$count        = WC()->cart ? (int) WC()->cart->get_cart_contents_count() : 0;
	$current_link = home_url( '/' );
	?>
	<div class="lily-drawer-overlay" hidden></div>
	<aside id="lily-cart-drawer" class="lily-cart-drawer" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Shopping cart', 'lily' ); ?>" aria-hidden="true" tabindex="-1" hidden>
		<header class="lily-drawer__header">
			<h2 class="lily-drawer__title">
				<?php esc_html_e( 'Your Cart', 'lily' ); ?>
				<span class="lily-drawer__count-badge"><?php echo esc_html( $count ); ?></span>
			</h2>
			<button type="button" class="lily-drawer__close" aria-label="<?php esc_attr_e( 'Close cart', 'lily' ); ?>">
				<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M18 6L6 18"/><path d="M6 6l12 12"/></svg>
			</button>
		</header>
		<div class="lily-drawer__notices" hidden></div>
		<div class="lily-drawer__body">
			<?php echo lily_drawer_body_html(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped internally. ?>
		</div>
	</aside>
	<?php
}

/* ── AJAX endpoints ──────────────────────────────────────────────────── */

/**
 * Capture queued WooCommerce notices (kept out of the next page view).
 *
 * @return string HTML of any queued notices.
 */
function lily_capture_wc_notices() {
	ob_start();
	wc_print_notices();
	$html = ob_get_clean();
	wc_clear_notices();
	return $html;
}

/**
 * Shared JSON payload for every drawer mutation.
 */
function lily_send_drawer_json( $added_flag = null ) {
	WC()->cart->calculate_totals();
	wp_send_json(
		array(
			'count'    => (int) WC()->cart->get_cart_contents_count(),
			'subtotal' => WC()->cart->get_cart_subtotal(),
			'body'     => lily_drawer_body_html(),
			'notices'  => lily_capture_wc_notices(),
			'added'    => $added_flag,
		)
	);
}

add_action( 'wc_ajax_lily_add_to_cart', 'lily_ajax_add_to_cart' );
add_action( 'wp_ajax_nopriv_lily_add_to_cart', 'lily_ajax_add_to_cart' );
add_action( 'wp_ajax_lily_add_to_cart', 'lily_ajax_add_to_cart' );

/**
 * Add a product through the REAL WooCommerce API using the posted product form,
 * so add-to-cart validation (incl. Prescription Power) stays authoritative.
 */
function lily_ajax_add_to_cart() {
	if ( ! function_exists( 'WC' ) ) {
		wp_send_json_error( array( 'notices' => '' ) );
	}
	if ( ! WC()->cart && function_exists( 'wc_load_cart' ) ) {
		wc_load_cart();
	}

	// Accept both transports: a nested 'form' query-string or flat POST fields.
	// The product ID arrives as lily_product_id (never `add-to-cart`, which would
	// trigger WooCommerce's native Form-Handler redirect inside admin-ajax).
	if ( isset( $_POST['form'] ) && is_string( $_POST['form'] ) ) {
		parse_str( wp_unslash( $_POST['form'] ), $data ); // phpcs:ignore WordPress.Security.NonceVerification.Missing -- mirrors native add-to-cart POST behavior.
	} else {
		$data = $_POST; // phpcs:ignore WordPress.Security.NonceVerification.Missing
	}

	$product_id = apply_filters(
		'woocommerce_add_to_cart_product_id',
		absint(
			isset( $data['add-to-cart'] )
				? $data['add-to-cart']
				: ( isset( $data['lily_product_id'] )
					? $data['lily_product_id']
					: ( isset( $data['product_id'] ) ? $data['product_id'] : 0 ) )
		)
	);
	$quantity   = wc_stock_amount( isset( $data['quantity'] ) ? $data['quantity'] : 1 );

	if ( $product_id < 1 || $quantity < 1 ) {
		wc_add_notice( __( 'Sorry, this product could not be added.', 'lily' ), 'error' );
		lily_send_drawer_json( false );
	}

	// Re-present the submitted form as $_POST so every existing hook
	// (variation handling, Prescription Power validation, stock checks) behaves exactly as on the page.
	foreach ( $data as $key => $value ) {
		if ( is_string( $value ) ) {
			$_POST[ $key ] = wp_unslash( $value );
		}
	}

	$variation_id = absint( isset( $data['variation_id'] ) ? $data['variation_id'] : 0 );
	$variation    = array();
	foreach ( $data as $key => $value ) {
		if ( 0 === strpos( (string) $key, 'attribute_' ) && is_string( $value ) ) {
			$variation[ sanitize_title( wp_unslash( $key ) ) ] = wp_unslash( $value );
		}
	}

	$passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity, $variation_id, $variation );
	$added             = false;

	if ( $passed_validation ) {
		$added = WC()->cart->add_to_cart( $product_id, $quantity, $variation_id, $variation );
	}

	if ( $added ) {
		lily_send_drawer_json( true );
	}

	// Validation failed — do NOT report success; hand back the real notices.
	lily_send_drawer_json( false );
}

add_action( 'wc_ajax_lily_update_cart', 'lily_ajax_update_cart' );
add_action( 'wp_ajax_nopriv_lily_update_cart', 'lily_ajax_update_cart' );
add_action( 'wp_ajax_lily_update_cart', 'lily_ajax_update_cart' );

/**
 * Get the real Brand taxonomy used across the site.
 *
 * Restored helper (previously defined here) — wraps the navigation
 * taxonomy finder so templates keep one authoritative entry point.
 *
 * @return string Empty string when brand taxonomy is not available yet.
 */
function lily_get_brand_taxonomy() {
	return function_exists( 'lily_nav_find_taxonomy' )
		? lily_nav_find_taxonomy( array( 'pa_brand', 'product_brand' ) )
		: '';
}

/**
 * Quantity change / remove through core cart methods.
 */
function lily_ajax_update_cart() {
	if ( ! function_exists( 'WC' ) ) {
		wp_send_json_error( array( 'notices' => '' ) );
	}
	if ( ! WC()->cart && function_exists( 'wc_load_cart' ) ) {
		wc_load_cart();
	}

	$key    = isset( $_POST['key'] ) ? sanitize_text_field( wp_unslash( $_POST['key'] ) ) : '';
	$action = isset( $_POST['cart_action'] ) ? sanitize_text_field( wp_unslash( $_POST['cart_action'] ) ) : '';

	if ( '' === $key || ! WC()->cart->get_cart_item( $key ) ) {
		lily_send_drawer_json(); // Item already gone — return current truth.
		return;
	}

	if ( 'remove' === $action ) {
		WC()->cart->remove_cart_item( $key );
	} elseif ( isset( $_POST['qty'] ) ) {
		WC()->cart->set_quantity( $key, wc_stock_amount( wp_unslash( $_POST['qty'] ) ), true ); // phpcs:ignore WordPress.Security.NonceVerification.Missing -- read-only qty like the native cart form.
	}

	lily_send_drawer_json();
}

/* ── Never route a normal customer flow to the Cart Page ─────────────── */

add_filter( 'woocommerce_add_to_cart_redirect', 'lily_no_redirect_to_cart' );

/**
 * Any non-AJAX native add should land back where the customer was, not the cart page.
 *
 * @param string $url Default redirect URL (the cart).
 * @return string
 */
function lily_no_redirect_to_cart( $url ) {
	$referer = wp_get_referer();
	return $referer ? remove_query_arg( array( 'added-to-cart', 'add-to-cart' ), $referer ) : home_url( '/' );
}
