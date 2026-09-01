<?php
/**
 * Lily cart page.
 *
 * Replaces WooCommerce's default cart/cart.php with an editorial layout.
 *
 * @package Lily
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'woocommerce_before_cart' ); ?>
<form class="lily-cart-form" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">

	<div class="lily-cart-items">
		<?php
		if ( function_exists( 'lily_cart_items_html' ) ) {
			// Same renderer that powers the Lily Cart Drawer (single source of truth).
			echo lily_cart_items_html( 'page' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped internally.
		} else {
			foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
			$_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
			$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

			if ( ! $_product || ! $_product->exists() || $cart_item['quantity'] <= 0 || ! apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
				continue;
			}

			$permalink     = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
			$item_data     = apply_filters( 'woocommerce_get_item_data', array(), $cart_item );
			$thumb_markup  = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image( 'woocommerce_thumbnail' ), $cart_item, $cart_item_key );
			$name_markup   = apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key );
			$price_markup  = apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key );
			$subtotal      = apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key );
			$remove_url    = wc_get_cart_remove_url( $cart_item_key );

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
								printf( '1 <input type="hidden" name="cart[%s][qty]" value="1" />', esc_attr( $cart_item_key ) );
							} else {
								echo woocommerce_quantity_input( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- WC template output.
									array(
										'input_name'   => "cart[{$cart_item_key}][qty]",
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
		}
		?>
	</div>

	<?php do_action( 'woocommerce_after_cart_contents' ); ?>

	<div class="lily-cart-summary">
		<h2 class="lily-cart-summary__title"><?php esc_html_e( 'Order Summary', 'lily' ); ?></h2>

		<dl class="lily-cart-totals">
			<div class="lily-cart-totals__row">
				<dt><?php esc_html_e( 'Subtotal', 'lily' ); ?></dt>
				<dd><?php echo wp_kses_post( WC()->cart->get_cart_subtotal() ); ?></dd>
			</div>
			<div class="lily-cart-totals__row">
				<dt><?php esc_html_e( 'Shipping', 'lily' ); ?></dt>
				<dd><?php echo esc_html( apply_filters( 'lily_cart_shipping_note', __( 'Calculated at checkout', 'lily' ) ) ); ?></dd>
			</div>
			<div class="lily-cart-totals__row lily-cart-totals__row--total">
				<dt><?php esc_html_e( 'Total', 'lily' ); ?></dt>
				<dd><?php echo wp_kses_post( WC()->cart->get_cart_total() ); ?></dd>
			</div>
		</dl>

		<button type="submit" class="lily-cart-checkout-btn lily-cart-checkout-btn--update" name="update_cart" value="<?php esc_attr_e( 'Update cart', 'lily' ); ?>">
			<?php esc_html_e( 'Update cart', 'lily' ); ?>
		</button>

		<a href="<?php echo esc_url( wc_get_checkout_url() ); ?>" class="lily-cart-checkout-btn lily-cart-checkout-btn--primary">
			<?php esc_html_e( 'Proceed to Checkout', 'lily' ); ?>
		</a>

		<a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" class="lily-cart-continue">
			<?php esc_html_e( 'Continue Shopping', 'lily' ); ?>
		</a>

		<?php do_action( 'woocommerce_after_cart_totals' ); ?>
	</div>

	<?php wp_nonce_field( 'woocommerce-cart', 'woocommerce-cart-nonce' ); ?>
</form>

<?php do_action( 'woocommerce_after_cart' ); ?>
