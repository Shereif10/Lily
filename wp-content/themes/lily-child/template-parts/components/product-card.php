<?php
/**
 * Master product card component — the single card system used everywhere
 * (Shop grid, category/search archives, Best Sellers carousel, Related
 * Products, Wishlist, Lens Finder results, Search results).
 *
 * Sections control only LAYOUT around the card (columns, gaps, width); the
 * card design itself never changes. Every card reads its content and states
 * from the real WooCommerce product:
 *
 * - Badges: BEST SELLER (dashboard checkbox, see lily_is_best_seller()) and
 *   SALE (WooCommerce's native is_on_sale()) — identical warm-brown badge
 *   design, stacked top-left in that order. Both render only when the
 *   product's dashboard state says so.
 * - Metadata: manufacturer brand (pa_brand / product_brand term) and product
 *   color (pa_color term + swatch), title, rating, price — real product data
 *   only; rows without data are hidden (spacing stays reserved).
 * - Add to Cart: native WooCommerce flow; `drawer_ajax` switches the
 *   homepage carousel to the theme's drawer endpoint, which still runs the
 *   full woocommerce_add_to_cart_validation chain (incl. Prescription Power).
 *
 * Expected variables:
 *   $args['product']     WC_Product (required).
 *   $args['show_color']  bool    Render the color row (default true; hidden automatically without data).
 *   $args['show_rating'] bool    Render the rating row (default true; hidden automatically without reviews).
 *   $args['drawer_ajax'] bool    Use the theme's drawer add-to-cart endpoint (homepage carousel flow).
 *
 * @package Lily
 */

if ( ! defined( 'ABSPATH' ) || empty( $args['product'] ) || ! $args['product'] instanceof WC_Product ) {
	return;
}

$product = $args['product'];

$args = wp_parse_args(
	$args,
	array(
		'show_color'  => true,
		'show_rating' => true,
		'drawer_ajax' => false,
	)
);

$link = get_permalink( $product->get_id() );

/*
 * Add to Cart — two existing flows, one visual system:
 * - default: native WooCommerce add-to-cart (core AJAX classes when supported);
 * - drawer_ajax: the theme's drawer endpoint, which runs the full
 *   woocommerce_add_to_cart_validation chain (incl. Prescription Power).
 */
$can_ajax = $product->is_purchasable()
	&& $product->is_in_stock()
	&& $product->supports( 'ajax_add_to_cart' );

$cart_classes = array( 'lily-button', 'lily-button--small' );

if ( $args['drawer_ajax'] ) {
	$can_ajax = $product->is_type( 'simple' ) && $can_ajax;
	$cart_classes[] = 'lily-best-card__cart';
	if ( $can_ajax ) {
		$cart_classes[] = 'lily-best-card__cart--ajax';
	}
} elseif ( $can_ajax ) {
	$cart_classes[] = 'add_to_cart_button';
	$cart_classes[] = 'ajax_add_to_cart';
	$cart_classes[] = 'product_type_' . $product->get_type();
}

$cart_url  = $can_ajax ? $product->add_to_cart_url() : $link;
/* One label everywhere the button adds directly; section flows only change behavior, never wording. */
$cart_text = $can_ajax ? __( 'Add to Cart', 'lily' ) : $product->add_to_cart_text();

/* Out of Stock: no cart action of any kind — an unavailable state instead,
 * in the same button design. The product itself stays fully viewable. */
$out_of_stock = ! $product->is_in_stock();

if ( $out_of_stock ) {
	$cart_url  = '';
	$cart_text = __( 'Out of Stock', 'lily' );
}

/*
 * Badges — dashboard state only, never a section-provided label.
 * Order: BEST SELLER, SALE. One identical badge design for both.
 * Out of Stock is NOT a badge — it replaces the Add to Cart button below.
 */
$card_badges = array();
if ( function_exists( 'lily_is_best_seller' ) && lily_is_best_seller( $product ) ) {
	$card_badges[] = __( 'Best Seller', 'lily' );
}
if ( $product->is_on_sale() ) {
	$card_badges[] = __( 'Sale', 'lily' );
}

/* Manufacturer brand — real product brand term (pa_brand / product_brand), localized name. */
$lily_product_brand = function_exists( 'lily_product_brand_name' )
	? lily_product_brand_name( $product->get_id() )
	: '';

/* Specific product color: the Child Shade when one is assigned, otherwise
 * the Parent Color — localized, with the canonical term swatch. */
$lily_color_term = null;
if ( $args['show_color'] && function_exists( 'lily_product_color_term' ) ) {
	$lily_color_term = lily_product_color_term( $product->get_id() );
}

$lily_color_label = $lily_color_term instanceof WP_Term && function_exists( 'lily_term_name' )
	? lily_term_name( $lily_color_term )
	: ( $lily_color_term instanceof WP_Term ? $lily_color_term->name : '' );

/* Real WooCommerce review data: stars + the actual average (e.g. 4.8). */
$lily_rating_html    = $args['show_rating'] ? wc_get_rating_html( $product->get_average_rating() ) : '';
$lily_review_count   = $args['show_rating'] ? (int) $product->get_review_count() : 0;
$lily_rating_average = $lily_review_count > 0 ? number_format_i18n( round( (float) $product->get_average_rating(), 1 ), 1 ) : '';
?>
<article class="lily-product-card">
	<a class="lily-product-card__stretched" href="<?php echo esc_url( $link ); ?>" tabindex="-1" aria-hidden="true"></a>
	<?php if ( function_exists( 'lily_wishlist_button' ) ) { lily_wishlist_button( $product->get_id() ); } ?>
	<?php if ( ! empty( $card_badges ) ) : ?>
		<div class="lily-product-card__badges" aria-hidden="true">
			<?php foreach ( $card_badges as $card_badge ) : ?>
				<span class="lily-sale-badge"><?php echo esc_html( $card_badge ); ?></span>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
	<a class="lily-product-card__image" href="<?php echo esc_url( $link ); ?>" aria-label="<?php echo esc_attr( $product->get_name() ); ?>">
		<?php echo wp_kses_post( $product->get_image( 'woocommerce_thumbnail' ) ); ?>
	</a>
	<div class="lily-product-card__content">
		<div class="lily-product-card__meta">
			<?php if ( '' !== $lily_product_brand ) : ?>
				<span class="lily-product-card__brand"><?php echo esc_html( $lily_product_brand ); ?></span>
			<?php endif; ?>
			<?php if ( $lily_color_term instanceof WP_Term ) : ?>
				<?php
				$lily_swatch_image = function_exists( 'lily_get_color_image_url' ) ? lily_get_color_image_url( $lily_color_term, 'thumbnail' ) : '';
				$lily_swatch_style = $lily_swatch_image
					? 'background-image:url(' . esc_url( $lily_swatch_image ) . ')'
					: 'background:' . lily_get_color_swatch_color( $lily_color_term->slug );
				?>
				<span class="lily-product-card__color">
					<span class="lily-product-card__swatch" style="<?php echo esc_attr( $lily_swatch_style ); ?>"></span>
					<span class="lily-product-card__color-name"><?php echo esc_html( $lily_color_label ); ?></span>
				</span>
			<?php endif; ?>
		</div>

		<h3>
			<a href="<?php echo esc_url( $link ); ?>">
				<?php echo esc_html( $product->get_name() ); ?>
			</a>
		</h3>

		<span class="lily-product-card__rating">
			<?php if ( $lily_review_count > 0 ) : ?>
				<?php echo wp_kses_post( $lily_rating_html ); ?>
				<span class="lily-product-card__rating-count">(<?php echo esc_html( $lily_rating_average ); ?>)</span>
			<?php endif; ?>
		</span>

		<div class="lily-product-card__price"><?php echo wp_kses_post( $product->get_price_html() ); ?></div>

		<?php if ( $out_of_stock ) : ?>
			<span class="lily-button lily-button--small lily-product-card__cart lily-product-card__cart--outofstock" aria-disabled="true"><?php echo esc_html( $cart_text ); ?></span>
		<?php elseif ( $product->is_purchasable() ) : ?>
			<a
				class="<?php echo esc_attr( implode( ' ', $cart_classes ) ); ?>"
				href="<?php echo esc_url( $cart_url ); ?>"
				data-quantity="1"
				data-product_id="<?php echo esc_attr( $product->get_id() ); ?>"
				data-product_sku="<?php echo esc_attr( $product->get_sku() ); ?>"
				<?php echo $can_ajax ? 'rel="nofollow"' : ''; ?>
			>
				<?php echo esc_html( $cart_text ); ?>
			</a>
		<?php endif; ?>
	</div>
</article>
