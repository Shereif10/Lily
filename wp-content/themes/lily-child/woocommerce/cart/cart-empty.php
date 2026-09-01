<?php
/**
 * Lily empty cart state.
 *
 * @package Lily
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'woocommerce_cart_is_empty' );
?>
<div class="lily-cart-empty">
	<p class="lily-cart-empty__title"><?php esc_html_e( 'Your cart is empty.', 'lily' ); ?></p>
	<p class="lily-cart-empty__sub"><?php esc_html_e( 'Discover our lenses and find the shade that suits you best.', 'lily' ); ?></p>
	<a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" class="lily-cart-empty__cta">
		<?php esc_html_e( 'Return to Shop', 'lily' ); ?>
	</a>
</div>
