<?php
/**
 * Lily single product page.
 *
 * Replaces WooCommerce's default content-single-product.php.
 * Uses real product data only — no hardcoded content.
 *
 * @package Lily
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product;

if ( ! $product instanceof WC_Product ) {
	return;
}

/*
 * ── Attribute helpers ──────────────────────────────────────────────
 */
$lily_get_attr_label = static function ( $product, $taxonomy ) {
	$terms = wp_get_post_terms( $product->get_id(), $taxonomy );
	if ( is_wp_error( $terms ) || empty( $terms ) ) {
		return '';
	}

	$labels = array();
	foreach ( $terms as $term ) {
		$labels[] = function_exists( 'lily_term_name' ) ? lily_term_name( $term ) : $term->name;
	}

	return implode( ', ', $labels );
};

/*
 * ── Gather product data ────────────────────────────────────────────
 */
$lily_brand_taxonomy = function_exists( 'lily_get_brand_taxonomy' ) ? lily_get_brand_taxonomy() : '';
$lily_brand          = '';
if ( $lily_brand_taxonomy ) {
	$brand_terms = wp_get_post_terms( $product->get_id(), $lily_brand_taxonomy );
	if ( ! is_wp_error( $brand_terms ) && ! empty( $brand_terms ) ) {
		$lily_brand = function_exists( 'lily_term_name' ) ? lily_term_name( $brand_terms[0] ) : $brand_terms[0]->name;
	}
}

$lily_duration    = $lily_get_attr_label( $product, 'pa_duration' );
$lily_look        = $lily_get_attr_label( $product, 'pa_look' );

/*
 * Lens specifications — canonical numeric meta values with units, with a
 * read-only fallback to the legacy attribute-term names for older products.
 */
$lily_diameter   = lily_format_spec( 'diameter', lily_get_product_spec( $product->get_id(), 'diameter' ) );
$lily_water      = lily_format_spec( 'water_content', lily_get_product_spec( $product->get_id(), 'water_content' ) );
$lily_base_curve = lily_format_spec( 'base_curve', lily_get_product_spec( $product->get_id(), 'base_curve' ) );

/*
 * Color = the product's SPECIFIC shade (child term when assigned, else the
 * parent color), localized — never a separate product-page field.
 */
$lily_color_label = '';
if ( function_exists( 'lily_product_color_term' ) ) {
	$lily_color_term = lily_product_color_term( $product->get_id() );

	if ( $lily_color_term instanceof WP_Term ) {
		$lily_color_label = function_exists( 'lily_term_name' ) ? lily_term_name( $lily_color_term ) : $lily_color_term->name;
	}
}

/* Occasions (canonical pa_occasion terms, localized). */
$lily_occasions_label = '';
if ( taxonomy_exists( 'pa_occasion' ) ) {
	$lily_occasion_terms = wp_get_post_terms( $product->get_id(), 'pa_occasion' );

	if ( ! is_wp_error( $lily_occasion_terms ) && ! empty( $lily_occasion_terms ) ) {
		$lily_occasion_names = array();

		foreach ( $lily_occasion_terms as $lily_occasion_term ) {
			$lily_occasion_names[] = function_exists( 'lily_term_name' ) ? lily_term_name( $lily_occasion_term ) : $lily_occasion_term->name;
		}

		$lily_occasions_label = implode( '  ·  ', $lily_occasion_names );
	}
}

/*
 * Perfect Match data — Skin Colors, Eye Colors (canonical term slugs from
 * pa_skin_color / pa_eye_color, dashboard-managed) and Occasions, localized.
 */
$lily_skin_names = lily_catalog_names_by_slugs(
	'skin_color',
	(array) get_post_meta( $product->get_id(), 'best_skin_tones', true )
);

$lily_eye_names = lily_catalog_names_by_slugs(
	'eye_color',
	(array) get_post_meta( $product->get_id(), 'best_eye_colors', true )
);

$lily_skin_label = implode( '  ·  ', $lily_skin_names );
$lily_eye_label  = implode( '  ·  ', $lily_eye_names );

$lily_short_desc = wp_strip_all_tags( $product->get_short_description() );
$lily_has_desc   = '' !== $lily_short_desc;
$lily_has_gallery = ! empty( $product->get_gallery_image_ids() );
$lily_thumbnail_id = $product->get_image_id();

/*
 * ── Breadcrumb ─────────────────────────────────────────────────────
 */
$lily_breadcrumb = '';
if ( function_exists( 'woocommerce_breadcrumb' ) ) {
	ob_start();
	woocommerce_breadcrumb();
	$lily_breadcrumb = ob_get_clean();
}

/*
 * ── Related products ───────────────────────────────────────────────
 */
$lily_related_ids = wc_get_related_products( $product->get_id(), 4 );
$lily_related     = array();
if ( ! empty( $lily_related_ids ) ) {
	foreach ( $lily_related_ids as $related_id ) {
		$related_product = wc_get_product( $related_id );
		if ( $related_product && $related_product->is_visible() ) {
			$lily_related[] = $related_product;
		}
	}
}

/*
 * ── Shipping / Returns page ────────────────────────────────────────
 */
$lily_shipping_url = '';
$lily_shipping_page = get_page_by_path( 'shipping-returns' );
if ( ! empty( $lily_shipping_page ) && 'publish' === $lily_shipping_page->post_status ) {
	$lily_shipping_url = get_permalink( $lily_shipping_page->ID );
}

/*
 * ── Reviews scope + approved-only count for the REVIEWS accordion row ─
 */
$lily_review_scope_id = function_exists( 'lily_review_scope_product_id' )
	? lily_review_scope_product_id( $product )
	: $product->get_id();
$lily_review_stats = function_exists( 'lily_get_product_review_stats' )
	? lily_get_product_review_stats( $lily_review_scope_id )
	: array( 'count' => 0, 'average' => 0.0 );
$lily_review_count = absint( $lily_review_stats['count'] );
?>
<div class="lily-single-product" itemscope itemtype="https://schema.org/Product">

	<?php
	if ( function_exists( 'lily_container_open' ) ) {
		lily_container_open( 'lily-sp-wrap' );
	} else {
		echo '<div class="lily-container">';
	}
	?>

	<?php if ( $lily_breadcrumb ) : ?>
		<nav class="lily-sp-breadcrumb" aria-label="<?php esc_attr_e( 'Breadcrumb', 'lily' ); ?>">
			<?php echo $lily_breadcrumb; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- WooCommerce breadcrumb is trusted. ?>
		</nav>
	<?php endif; ?>

	<div class="lily-sp-main">

		<div class="lily-sp-gallery">
			<?php if ( $lily_has_gallery ) : ?>
				<div class="lily-sp-gallery__thumbs" role="list">
					<?php
					$gallery_ids = $product->get_gallery_image_ids();
					$all_ids     = array_merge( array( $lily_thumbnail_id ), $gallery_ids );
					$first       = true;
					foreach ( $all_ids as $img_id ) :
						$img_url  = wp_get_attachment_image_url( $img_id, 'woocommerce_thumbnail' );
						$img_alt  = get_post_meta( $img_id, '_wp_attachment_image_alt', true );
						$img_full = wp_get_attachment_image_url( $img_id, 'woocommerce_single' );
						if ( ! $img_url ) {
							$first = false;
							continue;
						}
						?>
						<button
							class="lily-sp-gallery__thumb<?php echo $first ? ' is-active' : ''; ?>"
							type="button"
							data-full="<?php echo esc_url( $img_full ); ?>"
							role="listitem"
							aria-label="<?php echo esc_attr( $img_alt ?: $product->get_name() ); ?>"
						>
							<img src="<?php echo esc_url( $img_url ); ?>" alt="<?php echo esc_attr( $img_alt ); ?>" loading="lazy" width="72" height="72">
						</button>
					<?php
						$first = false;
					endforeach;
					?>
				</div>
			<?php endif; ?>

			<div class="lily-sp-gallery__main">
				<?php echo wp_kses_post( $product->get_image( 'woocommerce_single' ) ); ?>
				<button type="button" class="lily-sp-gallery__arrow lily-sp-gallery__arrow--prev" data-lily-gallery-prev aria-label="<?php esc_attr_e( 'Previous image', 'lily' ); ?>">
					<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="15 18 9 12 15 6"/></svg>
				</button>
				<button type="button" class="lily-sp-gallery__arrow lily-sp-gallery__arrow--next" data-lily-gallery-next aria-label="<?php esc_attr_e( 'Next image', 'lily' ); ?>">
					<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="9 18 15 12 9 6"/></svg>
				</button>
			</div>
		</div>

		<div class="lily-sp-info">

			<?php if ( '' !== $lily_brand ) : ?>
				<p class="lily-sp-brand" itemprop="brand"><?php echo esc_html( $lily_brand ); ?></p>
			<?php endif; ?>

			<h1 class="lily-sp-title" itemprop="name"><?php echo wp_kses_post( $product->get_name() ); ?></h1>

			<div class="lily-sp-price" itemprop="offers" itemscope itemtype="https://schema.org/Offer">
				<meta itemprop="priceCurrency" content="<?php echo esc_attr( get_woocommerce_currency() ); ?>">
				<?php echo wp_kses_post( $product->get_price_html() ); ?>
				<link itemprop="availability" href="https://schema.org/<?php echo $product->is_in_stock() ? 'InStock' : 'OutOfStock'; ?>">
				<?php if ( ! $product->is_in_stock() ) : ?>
					<span class="lily-sale-badge lily-sp-stock-badge"><?php esc_html_e( 'Out of Stock', 'lily' ); ?></span>
				<?php endif; ?>
			</div>

			<?php if ( $lily_has_desc ) : ?>
				<div class="lily-sp-short-desc">
					<?php echo wp_kses_post( wpautop( $product->get_short_description() ) ); ?>
				</div>
			<?php endif; ?>

			<?php /* ── Lens Details — grouped technical information ── */ ?>
			<?php
			$lily_spec_rows = array(
				__( 'Duration', 'lily' )      => $lily_duration,
				__( 'Diameter', 'lily' )      => $lily_diameter,
				__( 'Water Content', 'lily' ) => $lily_water,
				__( 'Base Curve', 'lily' )    => $lily_base_curve,
				__( 'Look', 'lily' )          => $lily_look,
				__( 'Color', 'lily' )         => $lily_color_label,
			);
			$lily_spec_rows = array_filter( $lily_spec_rows, static function ( $row ) {
				return '' !== trim( (string) $row );
			} );
			?>
			<?php if ( ! empty( $lily_spec_rows ) ) : ?>
				<div class="lily-sp-specs">
					<h2 class="lily-sp-section-title">
						<svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7Z"/><circle cx="12" cy="12" r="3"/></svg>
						<?php echo esc_html( lily_ml_value( __( 'Lens Details', 'lily' ), '' ) ); ?>
					</h2>
					<dl class="lily-sp-specs-grid lily-sp-specs-grid--3">
						<?php foreach ( $lily_spec_rows as $lily_spec_label => $lily_spec_value ) : ?>
							<div class="lily-sp-spec-row">
								<dt><?php echo esc_html( $lily_spec_label ); ?></dt>
								<dd><?php echo esc_html( $lily_spec_value ); ?></dd>
							</div>
						<?php endforeach; ?>
					</dl>
				</div>
			<?php endif; ?>

			<?php /* ── Perfect Match — recommendation information ──── */ ?>
			<?php
			$lily_match_rows = array(
				__( 'Best Suited For', 'lily' )   => $lily_skin_label,
				__( 'Original Eye Color', 'lily' ) => $lily_eye_label,
				__( 'Perfect For', 'lily' )       => $lily_occasions_label,
			);
			$lily_match_rows = array_filter( $lily_match_rows, static function ( $row ) {
				return '' !== trim( (string) $row );
			} );
			?>
			<?php if ( ! empty( $lily_match_rows ) ) : ?>
				<div class="lily-sp-perfect">
					<h2 class="lily-sp-section-title">
						<svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.6l-1-1a5.5 5.5 0 0 0-7.8 7.8l1 1L12 21.2l7.8-7.8 1-1a5.5 5.5 0 0 0 0-7.8z"/></svg>
						<?php echo esc_html( lily_ml_value( __( 'Perfect Match', 'lily' ), '' ) ); ?>
					</h2>
					<dl class="lily-sp-perfect__rows">
						<?php foreach ( $lily_match_rows as $lily_match_label => $lily_match_value ) : ?>
							<div class="lily-sp-perfect__row">
								<dt><?php echo esc_html( $lily_match_label ); ?></dt>
								<dd><?php echo esc_html( $lily_match_value ); ?></dd>
							</div>
						<?php endforeach; ?>
					</dl>
				</div>
			<?php endif; ?>

			<?php /* ── Add to Cart form ───────────────────────── */ ?>
			<div class="lily-sp-cart-area">
				<?php
				// Server-side validation notices (e.g. failed native add-to-cart
				// with JS disabled) print exactly where the customer acts.
				wc_print_notices();
				woocommerce_template_single_add_to_cart();
				?>
			</div>

			<?php /* ── Color selection notice ─────────────────── */ ?>
			<?php if ( $product->is_type( 'variable' ) || $product->is_type( 'simple' ) ) : ?>
				<p class="lily-sp-color-notice">
					<?php esc_html_e( 'Please make sure you select the correct color before confirming your order.', 'lily' ); ?>
				</p>
			<?php endif; ?>

			<?php /* ── Add to Wishlist (reuses the shared wishlist system) ── */ ?>
			<?php if ( function_exists( 'lily_wishlist_button' ) ) : ?>
				<div class="lily-sp-wishlist">
					<?php lily_wishlist_button( $product->get_id() ); ?>
					<span class="lily-sp-wishlist__text lily-sp-wishlist__text--default"><?php esc_html_e( 'Add to Wishlist', 'lily' ); ?></span>
					<span class="lily-sp-wishlist__text lily-sp-wishlist__text--saved"><?php esc_html_e( 'Saved to Wishlist', 'lily' ); ?></span>
				</div>
			<?php endif; ?>

			<?php /* ── Service information row ─────────────────── */ ?>
			<div class="lily-sp-services">
				<div class="lily-sp-service">
					<svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="1" y="5" width="14" height="12" rx="1"/><path d="M15 9h4l3 3v5h-7z"/><circle cx="6" cy="19" r="1.6"/><circle cx="18" cy="19" r="1.6"/></svg>
					<div>
						<p class="lily-sp-service__title"><?php esc_html_e( 'Cash on Delivery', 'lily' ); ?></p>
						<p class="lily-sp-service__sub"><?php esc_html_e( 'Available', 'lily' ); ?></p>
					</div>
				</div>
				<div class="lily-sp-service">
					<svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 12a9 9 0 0 1 18 0"/><rect x="2" y="12" width="4" height="7" rx="1.5"/><rect x="18" y="12" width="4" height="7" rx="1.5"/></svg>
					<div>
						<p class="lily-sp-service__title"><?php esc_html_e( 'Customer Service & Complaints', 'lily' ); ?></p>
						<p class="lily-sp-service__sub"><a href="tel:01060760098">01060760098</a></p>
					</div>
				</div>
				<div class="lily-sp-service">
					<svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 8 12 3 3 8v8l9 5 9-5z"/><path d="M3 8l9 5 9-5"/><path d="M12 13v8"/></svg>
					<div>
						<?php if ( $lily_shipping_url ) : ?>
							<p class="lily-sp-service__title"><a href="<?php echo esc_url( $lily_shipping_url ); ?>"><?php esc_html_e( 'Shipping & Exchange', 'lily' ); ?></a></p>
						<?php else : ?>
							<p class="lily-sp-service__title"><?php esc_html_e( 'Shipping & Exchange', 'lily' ); ?></p>
						<?php endif; ?>
						<p class="lily-sp-service__sub"><?php esc_html_e( 'Easy and hassle-free', 'lily' ); ?></p>
					</div>
				</div>
			</div>

		</div>
	</div>

	<?php /* ── Product accordions (full container width) ──────────── */ ?>
	<div class="lily-sp-accordions" data-lily-sp-acc>

			<?php /* DESCRIPTION — the full product description. */ ?>
			<?php if ( $product->get_description() ) : ?>
				<div class="lily-sp-acc__item" data-lily-sp-acc-item>
					<h2 class="lily-sp-acc__h">
						<button class="lily-sp-acc__q" type="button" aria-expanded="false" aria-controls="lily-sp-panel-desc" id="lily-sp-tab-desc" data-lily-sp-acc-toggle>
							<span class="lily-sp-acc__label"><?php esc_html_e( 'Description', 'lily' ); ?></span>
							<span class="lily-sp-acc__icon" aria-hidden="true"></span>
						</button>
					</h2>
					<div class="lily-sp-acc__collapse" id="lily-sp-panel-desc" role="region" aria-labelledby="lily-sp-tab-desc" data-lily-sp-acc-panel>
						<div class="lily-sp-acc__inner">
							<div class="lily-sp-details__content">
								<?php echo wp_kses_post( wpautop( $product->get_description() ) ); ?>
							</div>
						</div>
					</div>
				</div>
			<?php endif; ?>

			<?php /* HOW TO USE — step-based instructions as a numbered list. */ ?>
			<?php $lily_how_to_use_steps = lily_get_how_to_use_steps( $product->get_id() ); ?>
			<?php if ( ! empty( $lily_how_to_use_steps ) ) : ?>
				<div class="lily-sp-acc__item" data-lily-sp-acc-item>
					<h2 class="lily-sp-acc__h">
						<button class="lily-sp-acc__q" type="button" aria-expanded="false" aria-controls="lily-sp-panel-howto" id="lily-sp-tab-howto" data-lily-sp-acc-toggle>
							<span class="lily-sp-acc__label"><?php esc_html_e( 'How to use', 'lily' ); ?></span>
							<span class="lily-sp-acc__icon" aria-hidden="true"></span>
						</button>
					</h2>
					<div class="lily-sp-acc__collapse" id="lily-sp-panel-howto" role="region" aria-labelledby="lily-sp-tab-howto" data-lily-sp-acc-panel>
						<div class="lily-sp-acc__inner">
							<div class="lily-sp-details__content">
								<ol class="lily-sp-howto-steps">
									<?php foreach ( $lily_how_to_use_steps as $lily_how_to_use_step ) : ?>
										<li><?php echo esc_html( $lily_how_to_use_step ); ?></li>
									<?php endforeach; ?>
								</ol>
							</div>
						</div>
					</div>
				</div>
			<?php endif; ?>

			<div class="lily-sp-acc__item lily-sp-acc__item--reviews" data-lily-sp-acc-item>
				<h2 class="lily-sp-acc__h">
					<button class="lily-sp-acc__q" type="button" aria-expanded="false" aria-controls="lily-sp-panel-reviews" id="lily-sp-tab-reviews" data-lily-sp-acc-toggle>
						<span class="lily-sp-acc__label">
							<?php
							printf(
								/* translators: %d: number of approved reviews. */
								esc_html__( 'Reviews (%d)', 'lily' ),
								esc_html( $lily_review_count )
							);
							?>
						</span>
						<span class="lily-sp-acc__icon" aria-hidden="true"></span>
					</button>
				</h2>
				<div class="lily-sp-acc__collapse" id="lily-sp-panel-reviews" role="region" aria-labelledby="lily-sp-tab-reviews" data-lily-sp-acc-panel>
					<div class="lily-sp-acc__inner">
						<?php
						if ( function_exists( 'lily_render_product_reviews' ) ) {
							lily_render_product_reviews( $product );
						}
						?>
					</div>
				</div>
			</div>

	</div>

	<?php /* ── You May Also Like (independent section, after accordions) ── */ ?>
	<?php if ( ! empty( $lily_related ) ) : ?>
		<section class="lily-sp-related" aria-labelledby="lily-sp-related-title">
			<div class="lily-sp-related__head">
				<h2 class="lily-sp-section-heading lily-sp-related__title" id="lily-sp-related-title"><?php esc_html_e( 'You May Also Like', 'lily' ); ?></h2>
				<a class="lily-sp-related__all" href="<?php echo esc_url( function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' ) ); ?>">
					<?php esc_html_e( 'View All', 'lily' ); ?>
					<span class="lily-sp-related__all-arrow" aria-hidden="true">&rarr;</span>
				</a>
			</div>
			<div class="lily-sp-related__grid">
			<?php
			foreach ( $lily_related as $related_product ) {
				get_template_part(
					'template-parts/components/product-card',
					null,
					array(
						'product' => $related_product,
					)
				);
			}
			?>
			</div>
		</section>
	<?php endif; ?>

	<?php
	if ( function_exists( 'lily_container_close' ) ) {
		lily_container_close();
	} else {
		echo '</div>';
	}
	?>

</div>
