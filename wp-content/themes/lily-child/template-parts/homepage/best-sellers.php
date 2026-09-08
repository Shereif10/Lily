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

if ( '' === $heading ) {
	$heading = esc_html__( 'The shades our customers love most', 'lily' );
}

// Development fallback only; a configured Dashboard description always wins.
if ( '' === trim( (string) $description ) ) {
	$description = esc_html__( 'Discover best-selling colors that suit every look and mood.', 'lily' );
}

/*
 * Single source of truth: exactly the products flagged "Best Seller" in the
 * dashboard (per-product checkbox). Flagging/unflagging a product updates
 * this section and the shared product card badges everywhere — no manual
 * list to maintain. With nothing flagged the section renders nothing.
 */
$products = array();
$lily_bs_best = new WP_Query(
	array(
		'post_type'              => 'product',
		'post_status'            => 'publish',
		'posts_per_page'         => 12,
		'ignore_sticky_posts'    => true,
		'no_found_rows'          => true,
		'meta_query'             => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- small catalog.
			array(
				'key'   => '_lily_best_seller',
				'value' => '1',
			),
		),
		'orderby'                => array(
			'menu_order' => 'ASC',
			'date'       => 'DESC',
		),
		'update_post_term_cache' => false,
	)
);

foreach ( $lily_bs_best->posts as $lily_bs_post ) {
	$lily_bs_product = wc_get_product( $lily_bs_post->ID );

	if ( $lily_bs_product && 'publish' === $lily_bs_product->get_status() ) {
		$products[ $lily_bs_post->ID ] = $lily_bs_product;
	}
}

if ( empty( $products ) ) {
	return;
}

$products = array_values( $products );

$shop_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' );
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
					// The one master product card — the BEST SELLER badge comes
					// from the product's own dashboard flag; the drawer
					// add-to-cart flow is enabled for this section only.
					get_template_part(
						'template-parts/components/product-card',
						null,
						array(
							'product'     => $product,
							'drawer_ajax' => true,
						)
					);
					?>
				<?php endforeach; ?>
			</div>

			<button class="lily-best-sellers__arrow lily-best-sellers__arrow--next" type="button" data-lily-carousel-next aria-label="<?php esc_attr_e( 'Next products', 'lily' ); ?>">
				<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><polyline points="9 18 15 12 9 6"></polyline></svg>
			</button>
		</div>

		<div class="lily-best-sellers__dots" data-lily-best-dots aria-hidden="true"></div>
	<?php lily_container_close(); ?>
</section>
