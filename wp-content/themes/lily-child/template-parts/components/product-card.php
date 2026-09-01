<?php
/**
 * Product card component.
 *
 * Expected variable: $args['product'].
 *
 * @package Lily
 */

if ( ! defined( 'ABSPATH' ) || empty( $args['product'] ) || ! $args['product'] instanceof WC_Product ) {
	return;
}

$product = $args['product'];
$link    = get_permalink( $product->get_id() );

$can_ajax = $product->is_purchasable()
	&& $product->is_in_stock()
	&& $product->supports( 'ajax-add-to-cart' );

$cart_classes = array( 'lily-button', 'lily-button--small' );

if ( $can_ajax ) {
	$cart_classes[] = 'add_to_cart_button';
	$cart_classes[] = 'ajax_add_to_cart';
	$cart_classes[] = 'product_type_' . $product->get_type();
}

$lily_brand_taxonomy = function_exists( 'lily_get_brand_taxonomy' ) ? lily_get_brand_taxonomy() : '';
$lily_product_brand   = '';
if ( $lily_brand_taxonomy ) {
	$lily_brand_terms = wp_get_post_terms( $product->get_id(), $lily_brand_taxonomy, array( 'fields' => 'names' ) );
	if ( ! is_wp_error( $lily_brand_terms ) && ! empty( $lily_brand_terms ) ) {
		$lily_product_brand = $lily_brand_terms[0];
	}
}
?>
<article class="lily-product-card">
	<a class="lily-product-card__stretched" href="<?php echo esc_url( $link ); ?>" tabindex="-1" aria-hidden="true"></a>
	<a class="lily-product-card__image" href="<?php echo esc_url( $link ); ?>" aria-label="<?php echo esc_attr( $product->get_name() ); ?>">
		<?php echo wp_kses_post( $product->get_image( 'woocommerce_thumbnail' ) ); ?>
	</a>
	<div class="lily-product-card__content">
		<span class="lily-product-card__brand"><?php echo esc_html( $lily_product_brand ); ?></span>
		<h3>
			<a href="<?php echo esc_url( $link ); ?>">
				<?php echo esc_html( $product->get_name() ); ?>
			</a>
		</h3>
		<div class="lily-product-card__price"><?php echo wp_kses_post( $product->get_price_html() ); ?></div>
		<?php if ( $product->is_purchasable() && $product->is_in_stock() ) : ?>
			<a
				class="<?php echo esc_attr( implode( ' ', $cart_classes ) ); ?>"
				href="<?php echo esc_url( $product->add_to_cart_url() ); ?>"
				data-quantity="1"
				data-product_id="<?php echo esc_attr( $product->get_id() ); ?>"
				data-product_sku="<?php echo esc_attr( $product->get_sku() ); ?>"
				<?php echo $can_ajax ? 'rel="nofollow"' : ''; ?>
			>
				<?php echo esc_html( $product->add_to_cart_text() ); ?>
			</a>
		<?php endif; ?>
	</div>
</article>
