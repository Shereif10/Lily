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
	$terms = wp_get_post_terms( $product->get_id(), $taxonomy, array( 'fields' => 'names' ) );
	return ( ! is_wp_error( $terms ) && ! empty( $terms ) ) ? implode( ', ', $terms ) : '';
};

/*
 * ── Gather product data ────────────────────────────────────────────
 */
$lily_brand_taxonomy = function_exists( 'lily_get_brand_taxonomy' ) ? lily_get_brand_taxonomy() : '';
$lily_brand          = '';
if ( $lily_brand_taxonomy ) {
	$brand_terms = wp_get_post_terms( $product->get_id(), $lily_brand_taxonomy, array( 'fields' => 'names' ) );
	if ( ! is_wp_error( $brand_terms ) && ! empty( $brand_terms ) ) {
		$lily_brand = $brand_terms[0];
	}
}

$lily_duration    = $lily_get_attr_label( $product, 'pa_duration' );
$lily_diameter    = $lily_get_attr_label( $product, 'pa_diameter' );
$lily_water       = $lily_get_attr_label( $product, 'pa_water_content' );
$lily_base_curve  = $lily_get_attr_label( $product, 'pa_base_curve' );
$lily_look        = $lily_get_attr_label( $product, 'pa_look' );
$lily_color_label = $lily_get_attr_label( $product, 'pa_color' );

/*
 * Skin Tone: check the taxonomy first, fall back to Lens Finder post meta
 * for backward compatibility with existing products.
 */
$lily_skin_tone = $lily_get_attr_label( $product, 'pa_skin_tone' );
if ( '' === $lily_skin_tone ) {
	$skin_meta = (array) get_post_meta( $product->get_id(), 'best_skin_tones', true );
	$skin_meta = array_filter( $skin_meta );
	if ( ! empty( $skin_meta ) ) {
		$skin_labels = array(
			'fair'         => 'Fair',
			'light-medium' => 'Light / Medium',
			'medium'       => 'Medium',
			'tan'          => 'Tan',
			'deep'         => 'Deep',
		);
		$displayed   = array();
		foreach ( $skin_meta as $slug ) {
			if ( isset( $skin_labels[ $slug ] ) ) {
				$displayed[] = $skin_labels[ $slug ];
			}
		}
		$lily_skin_tone = implode( '  ·  ', $displayed );
	}
}

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
?>
<div class="lily-single-product" itemscope itemtype="https://schema.org/Product">

	<?php if ( $lily_breadcrumb ) : ?>
		<nav class="lily-sp-breadcrumb" aria-label="<?php esc_attr_e( 'Breadcrumb', 'lily' ); ?>">
			<?php echo $lily_breadcrumb; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- WooCommerce breadcrumb is trusted. ?>
		</nav>
	<?php endif; ?>

	<div class="lily-sp-main">

		<div class="lily-sp-gallery">
			<div class="lily-sp-gallery__main">
				<?php echo wp_kses_post( $product->get_image( 'woocommerce_single' ) ); ?>
				<button type="button" class="lily-sp-gallery__arrow lily-sp-gallery__arrow--prev" data-lily-gallery-prev aria-label="<?php esc_attr_e( 'Previous image', 'lily' ); ?>">
					<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="15 18 9 12 15 6"/></svg>
				</button>
				<button type="button" class="lily-sp-gallery__arrow lily-sp-gallery__arrow--next" data-lily-gallery-next aria-label="<?php esc_attr_e( 'Next image', 'lily' ); ?>">
					<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="9 18 15 12 9 6"/></svg>
				</button>
			</div>

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
			</div>

			<?php if ( $lily_has_desc ) : ?>
				<div class="lily-sp-short-desc">
					<?php echo wp_kses_post( wpautop( $product->get_short_description() ) ); ?>
				</div>
			<?php endif; ?>

			<?php /* ── Lens Specifications ────────────────────── */ ?>
			<?php
			$lily_spec_rows = array(
				__( 'Duration', 'lily' )     => $lily_duration,
				__( 'Diameter', 'lily' )     => $lily_diameter,
				__( 'Water Content', 'lily' ) => $lily_water,
				__( 'Base Curve', 'lily' )   => $lily_base_curve,
			);
			$lily_spec_rows = array_filter( $lily_spec_rows );
			?>
			<?php if ( ! empty( $lily_spec_rows ) ) : ?>
				<div class="lily-sp-specs">
					<h2 class="lily-sp-section-title">
						<svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7Z"/><circle cx="12" cy="12" r="3"/></svg>
						<?php esc_html_e( 'Lens Information', 'lily' ); ?>
					</h2>
					<dl class="lily-sp-specs-grid">
						<?php foreach ( $lily_spec_rows as $lily_spec_label => $lily_spec_value ) : ?>
							<div class="lily-sp-spec-row">
								<dt><?php echo esc_html( $lily_spec_label ); ?></dt>
								<dd><?php echo esc_html( $lily_spec_value ); ?></dd>
							</div>
						<?php endforeach; ?>
					</dl>
				</div>
			<?php endif; ?>

			<?php /* ── Color / Skin Tone / Look ─────────────────── */ ?>
			<?php if ( '' !== $lily_color_label || '' !== $lily_skin_tone || '' !== $lily_look ) : ?>
				<div class="lily-sp-meta">
					<?php if ( '' !== $lily_color_label ) : ?>
						<div class="lily-sp-meta__item">
							<span class="lily-sp-label"><?php esc_html_e( 'Color', 'lily' ); ?></span>
							<span class="lily-sp-meta__value"><?php echo esc_html( $lily_color_label ); ?></span>
						</div>
					<?php endif; ?>
					<?php if ( '' !== $lily_skin_tone ) : ?>
						<div class="lily-sp-meta__item">
							<span class="lily-sp-label"><?php esc_html_e( 'Best suited for', 'lily' ); ?></span>
							<span class="lily-sp-meta__value"><?php echo esc_html( $lily_skin_tone ); ?></span>
						</div>
					<?php endif; ?>
					<?php if ( '' !== $lily_look ) : ?>
						<div class="lily-sp-meta__item">
							<span class="lily-sp-label"><?php esc_html_e( 'Look', 'lily' ); ?></span>
							<span class="lily-sp-meta__value"><?php echo esc_html( $lily_look ); ?></span>
						</div>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<?php /* ── Add to Cart form ───────────────────────── */ ?>
			<div class="lily-sp-cart-area">
				<?php woocommerce_template_single_add_to_cart(); ?>
			</div>

			<?php /* ── Color selection notice ─────────────────── */ ?>
			<?php if ( $product->is_type( 'variable' ) || $product->is_type( 'simple' ) ) : ?>
				<p class="lily-sp-color-notice">
					<?php esc_html_e( 'Please make sure you select the correct color before confirming your order.', 'lily' ); ?>
				</p>
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

	<div class="lily-sp-lower">
		<?php /* ── Product Details (full description) ─────────────── */ ?>
		<?php if ( $product->get_description() ) : ?>
			<div class="lily-sp-details">
				<h2 class="lily-sp-section-heading"><?php esc_html_e( 'Product Details', 'lily' ); ?></h2>
				<div class="lily-sp-details__content">
					<?php echo wp_kses_post( wpautop( $product->get_description() ) ); ?>
				</div>
			</div>
		<?php endif; ?>

		<?php /* ── Related Products ──────────────────────────────── */ ?>
		<?php if ( ! empty( $lily_related ) ) : ?>
			<div class="lily-sp-related">
				<h2 class="lily-sp-section-heading"><?php esc_html_e( 'You May Also Like', 'lily' ); ?></h2>
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
			</div>
		<?php endif; ?>
	</div>

</div>
