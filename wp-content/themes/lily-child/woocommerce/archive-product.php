<?php
/**
 * Lily product archive — used by the main Shop page and every product
 * category archive. Reuses the real WooCommerce loop, the shared Lily
 * product-card component, the existing attribute filter URLs and the
 * native WooCommerce pagination + ordering.
 *
 * @package Lily
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$lily_is_shop = is_shop();

// Compact intro: dynamic title on archives, fixed "Shop" heading plus a
// development fallback description so the page looks complete pre-copy.
$lily_title = $lily_is_shop ? esc_html__( 'Shop', 'lily' ) : woocommerce_page_title( false );

$lily_description = '';
if ( $lily_is_shop ) {
	// A configured Shop page description wins; otherwise the Lily fallback.
	$lily_description = trim( wp_strip_all_tags( (string) get_the_archive_description() ) );
	if ( '' === $lily_description ) {
		$lily_description = esc_html__( 'Explore our collection of lenses and everyday eye-care essentials.', 'lily' );
	}
}

/*
 * Category controls resolve the three real Lily collections from the live
 * product_cat taxonomy — no hardcoded URLs, no duplicate category system.
 */
$lily_categories = array();
foreach (
	array(
		array( 'colored-lenses' ),
		array( 'clear-lenses' ),
		array( 'accessories-lens-care', 'accessories', 'lens-care' ),
	) as $lily_slugs
) {
	foreach ( $lily_slugs as $lily_slug ) {
		$lily_term = get_term_by( 'slug', $lily_slug, 'product_cat' );
		if ( $lily_term && ! is_wp_error( $lily_term ) ) {
			$lily_categories[] = $lily_term;
			break;
		}
	}
}

$lily_current_cat = $lily_is_shop ? 0 : ( is_product_category() ? get_queried_object_id() : 0 );

/*
 * Sidebar filter terms reuse the existing pa_brand / pa_color taxonomies
 * through lily_get_attribute_filter_url(), so homepage brand/color links,
 * navbar dropdown links and these controls share one filtering system.
 */
$lily_brand_taxonomy = function_exists( 'lily_get_brand_taxonomy' ) ? lily_get_brand_taxonomy() : '';
$lily_filter_groups  = array(
	'brand' => array(
		'label'    => __( 'Brand', 'lily' ),
		'taxonomy' => $lily_brand_taxonomy,
		'param'    => $lily_brand_taxonomy ? 'filter_' . str_replace( 'pa_', '', $lily_brand_taxonomy ) : '',
	),
	'color' => array(
		'label'    => __( 'Color', 'lily' ),
		'taxonomy' => taxonomy_exists( 'pa_color' ) ? 'pa_color' : '',
		'param'    => taxonomy_exists( 'pa_color' ) ? 'filter_color' : '',
	),
);

/*
 * Price slider bounds come from the real catalog prices for the current
 * (already filtered) query via WooCommerce's own filtered-price helper.
 */
$lily_price_min = 0;
$lily_price_max = 0;
if ( isset( WC()->query ) && method_exists( WC()->query, 'get_filtered_price' ) ) {
	// Classic WooCommerce helper.
	$lily_filtered_price = WC()->query->get_filtered_price();
	if ( $lily_filtered_price && isset( $lily_filtered_price->min, $lily_filtered_price->max ) && (float) $lily_filtered_price->max > 0 ) {
		$lily_price_min = floor( (float) $lily_filtered_price->min );
		$lily_price_max = ceil( (float) $lily_filtered_price->max );
	}
} else {
	/*
	 * WooCommerce 11+ fallback: derive the real catalog price range from
	 * the _price meta of visible, published products.
	 */
	global $wpdb;
	$lily_price_row = $wpdb->get_row( // phpcs:ignore WordPress.DB.DirectDatabaseQuery -- small catalog, cached below.
		"SELECT MIN(CAST(m.meta_value AS DECIMAL(10,4))) AS minp, MAX(CAST(m.meta_value AS DECIMAL(10,4))) AS maxp
		 FROM {$wpdb->postmeta} m
		 JOIN {$wpdb->posts} p ON p.ID = m.post_id AND p.post_status = 'publish' AND p.post_type = 'product'
		 WHERE m.meta_key = '_price' AND m.meta_value <> ''"
	);
	if ( $lily_price_row && (float) $lily_price_row->maxp > 0 ) {
		$lily_price_min = (int) floor( (float) $lily_price_row->minp );
		$lily_price_max = (int) ceil( (float) $lily_price_row->maxp );
	}
}
set_transient( 'lily_shop_price_range', array( $lily_price_min, $lily_price_max ), HOUR_IN_SECONDS );

$lily_currency_symbol = function_exists( 'get_woocommerce_currency_symbol' ) ? get_woocommerce_currency_symbol() : '';

$lily_current_min = isset( $_GET['min_price'] ) && is_numeric( $_GET['min_price'] ) ? floatval( $_GET['min_price'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- public catalog filter links.
$lily_current_max = isset( $_GET['max_price'] ) && is_numeric( $_GET['max_price'] ) ? floatval( $_GET['max_price'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- public catalog filter links.

$lily_has_filters = ! empty( $_GET ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- public catalog filter links.

/**
 * Preserve active filters inside small GET forms (sort / price).
 *
 * @return string Hidden input HTML for every active filter except skipped keys.
 */
$lily_render_filter_inputs = static function () use ( $lily_has_filters ) {
	$html = '';
	foreach ( $_GET as $key => $value ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( in_array( $key, array( 'orderby', 'paged', 'min_price', 'max_price', 'add-to-cart' ), true ) ) {
			continue;
		}
		foreach ( (array) $value as $single_value ) {
			$html .= '<input type="hidden" name="' . esc_attr( $key ) . '" value="' . esc_attr( $single_value ) . '">';
		}
	}
	return $html;
};

/*
 * WooCommerce-native ordering keys; "title" (Name A–Z) is wired up in the
 * Lily query bridge so no custom JS sorting exists anywhere.
 */
$lily_sort_options = array(
	'menu_order' => __( 'Featured', 'lily' ),
	'date'       => __( 'Newest', 'lily' ),
	'price'      => __( 'Price: Low to High', 'lily' ),
	'price-desc' => __( 'Price: High to Low', 'lily' ),
	'title'      => __( 'Name: A–Z', 'lily' ),
);
$lily_sort_current = isset( $_GET['orderby'] ) ? sanitize_key( wp_unslash( $_GET['orderby'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
if ( ! array_key_exists( $lily_sort_current, $lily_sort_options ) ) {
	$lily_sort_current = 'menu_order';
}

$lily_result_count = (int) $GLOBALS['wp_query']->found_posts;

/* translators: %s: number of products. */
$lily_count_text = sprintf( _n( '%s Product', '%s Products', $lily_result_count, 'lily' ), number_format_i18n( $lily_result_count ) );

$lily_brands = $lily_filter_groups['brand']['taxonomy'] ? get_terms(
	array(
		'taxonomy'   => $lily_filter_groups['brand']['taxonomy'],
		'hide_empty' => true,
	)
) : array();
if ( is_wp_error( $lily_brands ) ) {
	$lily_brands = array();
}
$lily_brands_shown = apply_filters( 'lily_shop_sidebar_brand_limit', 8, $lily_brands );

$lily_colors = $lily_filter_groups['color']['taxonomy'] ? get_terms(
	array(
		'taxonomy'   => $lily_filter_groups['color']['taxonomy'],
		'hide_empty' => true,
	)
) : array();
if ( is_wp_error( $lily_colors ) ) {
	$lily_colors = array();
}
?>
<section class="lily-shop">
	<?php lily_container_open( 'lily-shop__inner' ); ?>

		<header class="lily-shop__header lily-reveal">
			<h1 class="lily-shop__title"><?php echo esc_html( wp_strip_all_tags( $lily_title ) ); ?></h1>
			<?php if ( '' !== $lily_description ) : ?>
				<p class="lily-shop__description"><?php echo esc_html( $lily_description ); ?></p>
			<?php endif; ?>
		</header>

		<div class="lily-shop__layout lily-reveal">

			<button class="lily-shop-filter-toggle" type="button" aria-expanded="false" aria-controls="lily-shop-sidebar">
				<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true" focusable="false"><line x1="4" y1="7" x2="20" y2="7"></line><line x1="7" y1="12" x2="17" y2="12"></line><line x1="10" y1="17" x2="14" y2="17"></line></svg>
				<?php esc_html_e( 'Filter', 'lily' ); ?>
			</button>

			<div class="lily-shop-drawer-overlay" hidden></div>

			<aside class="lily-shop__sidebar" id="lily-shop-sidebar" aria-label="<?php esc_attr_e( 'Filter products', 'lily' ); ?>">
				<div class="lily-shop-sidebar-head">
					<span class="lily-shop__sidebar-title"><?php esc_html_e( 'Filter By', 'lily' ); ?></span>
					<button class="lily-shop-drawer-close" type="button" aria-label="<?php esc_attr_e( 'Close filters', 'lily' ); ?>">
						<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true" focusable="false"><line x1="6" y1="6" x2="18" y2="18"></line><line x1="18" y1="6" x2="6" y2="18"></line></svg>
					</button>
				</div>

				<?php /* Collections — open by default. */ ?>
				<details class="lily-shop-acc" open>
					<summary class="lily-shop-acc__summary">
						<span><?php esc_html_e( 'Collections', 'lily' ); ?></span>
						<span class="lily-shop-acc__chevron" aria-hidden="true">
							<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
						</span>
					</summary>
					<div class="lily-shop-acc__body">
						<ul class="lily-shop-list">
							<li>
								<a class="lily-shop-link<?php echo $lily_is_shop ? ' is-active' : ''; ?>" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>">
									<?php esc_html_e( 'All Products', 'lily' ); ?>
								</a>
							</li>
							<?php foreach ( $lily_categories as $lily_term ) : ?>
								<li>
									<a class="lily-shop-link<?php echo (int) $lily_term->term_id === $lily_current_cat ? ' is-active' : ''; ?>" href="<?php echo esc_url( get_term_link( $lily_term ) ); ?>">
										<?php echo esc_html( $lily_term->name ); ?>
									</a>
								</li>
							<?php endforeach; ?>
						</ul>
					</div>
				</details>

				<?php foreach ( $lily_filter_groups as $lily_group_key => $lily_group ) : ?>
					<?php
					$lily_terms = 'brand' === $lily_group_key ? $lily_brands : $lily_colors;

					if ( '' === $lily_group['taxonomy'] || empty( $lily_terms ) ) {
						continue;
					}

					$lily_selected = isset( $_GET[ $lily_group['param'] ] ) ? sanitize_title( wp_unslash( $_GET[ $lily_group['param'] ] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
					?>
					<details class="lily-shop-acc"<?php echo 'color' === $lily_group_key && '' !== $lily_selected ? ' open' : ''; ?>>
						<summary class="lily-shop-acc__summary">
							<span><?php echo esc_html( $lily_group['label'] ); ?></span>
							<span class="lily-shop-acc__chevron" aria-hidden="true">
								<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
							</span>
						</summary>
						<div class="lily-shop-acc__body">
							<ul class="lily-shop-list">
								<?php foreach ( $lily_terms as $lily_index => $lily_term ) : ?>
									<li class="<?php echo $lily_index >= $lily_brands_shown && 'brand' === $lily_group_key ? 'is-extra is-hidden' : ''; ?>">
										<a class="lily-shop-link lily-shop-link--swatch<?php echo $lily_selected === $lily_term->slug ? ' is-active' : ''; ?>" href="<?php echo esc_url( lily_get_attribute_filter_url( $lily_group['taxonomy'], $lily_term ) ); ?>">
											<?php if ( 'color' === $lily_group_key ) : ?>
												<span class="lily-swatch-dot" aria-hidden="true"></span>
											<?php endif; ?>
											<span class="lily-shop-link__label"><?php echo esc_html( $lily_term->name ); ?></span>
										</a>
									</li>
								<?php endforeach; ?>
							</ul>
							<?php if ( 'brand' === $lily_group_key && count( $lily_brands ) > $lily_brands_shown ) : ?>
								<button class="lily-shop-show-more" type="button" data-lily-show-more data-more-text="<?php esc_attr_e( 'Show more', 'lily' ); ?>" data-less-text="<?php esc_attr_e( 'Show less', 'lily' ); ?>">
									<?php esc_html_e( 'Show more', 'lily' ); ?>
								</button>
							<?php endif; ?>
							<?php if ( '' !== $lily_selected ) : ?>
								<a class="lily-shop-clear-one" href="<?php echo esc_url( remove_query_arg( $lily_group['param'] ) ); ?>">
									<?php
									/* translators: %s: filter group label. */
									printf( esc_html__( 'Clear %s', 'lily' ), esc_html( $lily_group['label'] ) );
									?>
								</a>
							<?php endif; ?>
						</div>
					</details>
				<?php endforeach; ?>

				<?php /* Price — closed by default. */ ?>
				<?php if ( $lily_price_max > 0 ) : ?>
					<details class="lily-shop-acc"<?php echo ( '' !== $lily_current_min || '' !== $lily_current_max ) ? ' open' : ''; ?>>
						<summary class="lily-shop-acc__summary">
							<span><?php esc_html_e( 'Price', 'lily' ); ?></span>
							<span class="lily-shop-acc__chevron" aria-hidden="true">
								<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
							</span>
						</summary>
						<div class="lily-shop-acc__body">
							<form class="lily-price-form" method="get" action="<?php echo esc_url( is_product_category() ? get_term_link( get_queried_object() ) : wc_get_page_permalink( 'shop' ) ); ?>">
								<?php echo $lily_render_filter_inputs(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped per value above. ?>
								<div class="lily-price-range" data-min="<?php echo esc_attr( $lily_price_min ); ?>" data-max="<?php echo esc_attr( $lily_price_max ); ?>">
									<div class="lily-price-range__track" aria-hidden="true"><span class="lily-price-range__fill"></span></div>
									<input class="lily-price-range__input lily-price-range__input--min" type="range" min="<?php echo esc_attr( $lily_price_min ); ?>" max="<?php echo esc_attr( $lily_price_max ); ?>" step="1" value="<?php echo esc_attr( '' !== $lily_current_min ? $lily_current_min : $lily_price_min ); ?>" aria-label="<?php esc_attr_e( 'Minimum price', 'lily' ); ?>">
									<input class="lily-price-range__input lily-price-range__input--max" type="range" min="<?php echo esc_attr( $lily_price_min ); ?>" max="<?php echo esc_attr( $lily_price_max ); ?>" step="1" value="<?php echo esc_attr( '' !== $lily_current_max ? $lily_current_max : $lily_price_max ); ?>" aria-label="<?php esc_attr_e( 'Maximum price', 'lily' ); ?>">
								</div>
								<p class="lily-price-hint">
									<span class="lily-price-hint__min"><span class="lily-price-cur"><?php echo esc_html( $lily_currency_symbol ); ?></span> <span class="lily-price-num"><?php echo esc_html( number_format_i18n( '' !== $lily_current_min ? $lily_current_min : $lily_price_min ) ); ?></span></span>
									<span class="lily-price-hint__max"><span class="lily-price-cur"><?php echo esc_html( $lily_currency_symbol ); ?></span> <span class="lily-price-num"><?php echo esc_html( number_format_i18n( '' !== $lily_current_max ? $lily_current_max : $lily_price_max ) ); ?></span></span>
								</p>
								<div class="lily-price-fields">
									<label>
										<span><?php esc_html_e( 'Min', 'lily' ); ?></span>
										<input type="number" name="min_price" min="0" value="<?php echo esc_attr( '' !== $lily_current_min ? $lily_current_min : '' ); ?>" placeholder="<?php echo esc_attr( $lily_price_min ); ?>">
									</label>
									<label>
										<span><?php esc_html_e( 'Max', 'lily' ); ?></span>
										<input type="number" name="max_price" min="0" value="<?php echo esc_attr( '' !== $lily_current_max ? $lily_current_max : '' ); ?>" placeholder="<?php echo esc_attr( $lily_price_max ); ?>">
									</label>
									<button class="lily-button lily-button--small lily-price-apply" type="submit"><?php esc_html_e( 'Apply', 'lily' ); ?></button>
								</div>
							</form>
						</div>
					</details>
				<?php endif; ?>
			</aside>

			<div class="lily-shop__main">
				<div class="lily-shop-toolbar">
					<p class="lily-shop-count"><?php echo esc_html( $lily_count_text ); ?></p>

					<form class="lily-sort-form" method="get" action="">
						<?php echo $lily_render_filter_inputs(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped per value above. ?>
						<label class="lily-sort-form__label" for="lily-shop-orderby"><?php esc_html_e( 'Sort by:', 'lily' ); ?></label>
						<select id="lily-shop-orderby" name="orderby">
							<?php foreach ( $lily_sort_options as $lily_value => $lily_label ) : ?>
								<option value="<?php echo esc_attr( $lily_value ); ?>" <?php selected( $lily_sort_current, $lily_value ); ?>><?php echo esc_html( $lily_label ); ?></option>
							<?php endforeach; ?>
						</select>
						<noscript><button class="lily-sort-form__go" type="submit"><?php esc_html_e( 'Go', 'lily' ); ?></button></noscript>
					</form>
				</div>

				<?php if ( have_posts() ) : ?>
					<div class="lily-shop-grid lily-products-row--results">
						<?php
						while ( have_posts() ) {
							the_post();
							global $product;

							if ( ! $product instanceof WC_Product ) {
								$product = wc_get_product( get_the_ID() );
							}

							if ( ! $product ) {
								continue;
							}

							get_template_part(
								'template-parts/components/product-card',
								null,
								array(
									'product' => $product,
								)
							);
						}
						?>
					</div>

					<?php do_action( 'woocommerce_after_shop_loop' ); ?>
				<?php else : ?>
					<div class="lily-shop-empty">
						<p><?php echo $lily_has_filters ? esc_html__( 'No products were found matching your selection.', 'lily' ) : esc_html__( 'Products coming soon.', 'lily' ); ?></p>
					</div>
				<?php endif; ?>
			</div>
		</div>

	<?php lily_container_close(); ?>
</section>
<?php

get_footer();
