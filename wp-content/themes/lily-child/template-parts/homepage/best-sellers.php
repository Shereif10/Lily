<?php
/**
 * Best Sellers section — centered editorial product carousel.
 *
 * @package Lily
 */

if ( ! defined( 'ABSPATH' ) || ! lily_show_section( 'show_best_sellers' ) || ! class_exists( 'WooCommerce' ) ) {
	return;
}

$heading     = trim( (string) lily_get_option( 'best_sellers_heading', '' ) );
$description = lily_get_option( 'best_sellers_description', '' );
$product_ids = array_map( 'absint', (array) lily_get_option( 'best_sellers_products', array() ) );

if ( '' === $heading ) {
	$heading = esc_html__( 'The shades our customers love most', 'lily' );
}

// Development fallback only; a configured Dashboard description always wins.
if ( '' === trim( (string) $description ) ) {
	$description = esc_html__( 'Discover best-selling colors that suit every look and mood.', 'lily' );
}

/*
 * Manual curation first: exactly the selected products, in the chosen order.
 * With no dashboard selection yet, fall back to the real WooCommerce
 * best sellers (by total sales) so the section shows actual products.
 */
$products = array();
foreach ( $product_ids as $product_id ) {
	if ( ! $product_id || isset( $products[ $product_id ] ) ) {
		continue;
	}

	$product = wc_get_product( $product_id );

	if ( $product && 'publish' === $product->get_status() ) {
		$products[ $product_id ] = $product;
	}
}

if ( count( $products ) < 8 ) {
	global $wpdb;

	// Fill the row with real WooCommerce best sellers (by total_sales),
	// appended after the dashboard curation and never overriding it.
	$lily_best_ids = array_map(
		'absint',
		(array) $wpdb->get_col(
			"SELECT p.ID
			FROM {$wpdb->posts} p
			LEFT JOIN {$wpdb->postmeta} m ON m.post_id = p.ID AND m.meta_key = 'total_sales'
			WHERE p.post_type = 'product' AND p.post_status = 'publish'
			ORDER BY COALESCE( m.meta_value + 0, 0 ) DESC, p.ID ASC
			LIMIT 12"
		)
	);

	foreach ( $lily_best_ids as $lily_id ) {
		if ( count( $products ) >= 8 ) {
			break;
		}

		if ( isset( $products[ $lily_id ] ) ) {
			continue;
		}

		$lily_fallback_product = wc_get_product( $lily_id );

		if ( $lily_fallback_product && 'publish' === $lily_fallback_product->get_status() ) {
			$products[ $lily_id ] = $lily_fallback_product;
		}
	}
}

if ( empty( $products ) ) {
	return;
}

$products = array_values( $products );

$shop_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' );

// Shared swatch tones for products whose color terms have no image yet.
$lily_swatch_tones = array(
	'blue'  => '#7d94a8',
	'gray'  => '#aaa49a',
	'green' => '#6f7d5f',
	'hazel' => '#9c7b4f',
	'brown' => '#885337',
	'other' => '#d8d2bd',
);
?>
<section class="lily-section lily-best-sellers" aria-label="<?php esc_attr_e( 'Best Sellers', 'lily' ); ?>">
	<?php lily_container_open(); ?>
		<div class="lily-best-sellers__intro">
			<p class="lily-best-sellers__eyebrow"><?php esc_html_e( 'Best Sellers', 'lily' ); ?></p>
			<?php lily_section_heading( $heading, $description ); ?>
			<a class="lily-best-sellers__cta" href="<?php echo esc_url( $shop_url ); ?>">
				<?php esc_html_e( 'Shop Best Sellers', 'lily' ); ?>
				<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
			</a>
		</div>

		<div class="lily-best-sellers__carousel">
			<button class="lily-best-sellers__arrow lily-best-sellers__arrow--prev" type="button" data-lily-carousel-prev aria-label="<?php esc_attr_e( 'Previous products', 'lily' ); ?>">
				<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><polyline points="15 18 9 12 15 6"></polyline></svg>
			</button>

			<div class="lily-products-row lily-products-row--best" data-lily-best-track tabindex="0" aria-label="<?php esc_attr_e( 'Best-selling products', 'lily' ); ?>">
				<?php foreach ( $products as $product ) : ?>
					<?php
					$lily_link = get_permalink( $product->get_id() );

					// Existing color attribute data: first term only (swatch + name).
					$lily_color_terms = wp_get_post_terms( $product->get_id(), 'pa_color' );
					if ( is_wp_error( $lily_color_terms ) ) {
						$lily_color_terms = array();
					}
					$lily_color_term = ! empty( $lily_color_terms ) ? $lily_color_terms[0] : null;

					/*
					 * Real WooCommerce add-to-cart: simple purchasable products add
					 * directly through the theme's drawer endpoint (which runs the
					 * full woocommerce_add_to_cart_validation chain, incl. RX power);
					 * everything else uses the native product-page flow.
					 */
					$lily_can_ajax = $product->is_type( 'simple' )
						&& $product->is_purchasable()
						&& $product->is_in_stock()
						&& $product->supports( 'ajax_add_to_cart' );

					$lily_cart_text = $lily_can_ajax ? __( 'Add to Cart', 'lily' ) : $product->add_to_cart_text();
					$lily_cart_url  = $lily_can_ajax ? $product->add_to_cart_url() : $lily_link;
					?>
					<article class="lily-product-card lily-product-card--best">
						<a class="lily-best-card__media" href="<?php echo esc_url( $lily_link ); ?>" aria-label="<?php echo esc_attr( $product->get_name() ); ?>">
							<span class="lily-best-card__badge"><?php esc_html_e( 'Best Seller', 'lily' ); ?></span>
							<?php echo wp_kses_post( $product->get_image( 'woocommerce_thumbnail' ) ); ?>
						</a>
						<div class="lily-best-card__body">
							<?php if ( $lily_color_term ) : ?>
								<?php
								$lily_swatch_image = function_exists( 'lily_get_color_image_url' ) ? lily_get_color_image_url( $lily_color_term, 'thumbnail' ) : '';
								$lily_swatch_style = $lily_swatch_image
									? 'background-image:url(' . esc_url( $lily_swatch_image ) . ')'
									: 'background:' . ( isset( $lily_swatch_tones[ $lily_color_term->slug ] ) ? $lily_swatch_tones[ $lily_color_term->slug ] : '#d8d2bd' );
								?>
								<span class="lily-best-card__color">
									<span class="lily-best-card__swatch" style="<?php echo esc_attr( $lily_swatch_style ); ?>"></span>
									<span class="lily-best-card__color-name"><?php echo esc_html( $lily_color_term->name ); ?></span>
								</span>
							<?php endif; ?>

							<a class="lily-best-card__name" href="<?php echo esc_url( $lily_link ); ?>"><?php echo esc_html( $product->get_name() ); ?></a>

							<?php if ( $product->get_review_count() > 0 || wc_get_rating_html( $product->get_average_rating() ) ) : ?>
								<span class="lily-best-card__rating">
									<?php echo wp_kses_post( wc_get_rating_html( $product->get_average_rating() ) ); ?>
									<?php if ( $product->get_review_count() > 0 ) : ?>
										<span class="lily-best-card__count">(<?php echo esc_html( $product->get_review_count() ); ?>)</span>
									<?php endif; ?>
								</span>
							<?php endif; ?>

							<span class="lily-best-card__price"><?php echo wp_kses_post( $product->get_price_html() ); ?></span>

							<a
								class="lily-best-card__cart<?php echo $lily_can_ajax ? ' lily-best-card__cart--ajax' : ''; ?>"
								href="<?php echo esc_url( $lily_cart_url ); ?>"
								data-quantity="1"
								data-product_id="<?php echo esc_attr( $product->get_id() ); ?>"
								data-product_sku="<?php echo esc_attr( $product->get_sku() ); ?>"
								<?php echo $lily_can_ajax ? 'rel="nofollow"' : ''; ?>
							>
								<?php echo esc_html( $lily_cart_text ); ?>
							</a>
						</div>
					</article>
				<?php endforeach; ?>
			</div>

			<button class="lily-best-sellers__arrow lily-best-sellers__arrow--next" type="button" data-lily-carousel-next aria-label="<?php esc_attr_e( 'Next products', 'lily' ); ?>">
				<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><polyline points="9 18 15 12 9 6"></polyline></svg>
			</button>
		</div>

		<div class="lily-best-sellers__dots" data-lily-best-dots aria-hidden="true"></div>
	<?php lily_container_close(); ?>
</section>
