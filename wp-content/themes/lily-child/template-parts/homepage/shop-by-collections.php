<?php
/**
 * Shop by Collections section — editorial split layout.
 *
 * @package Lily
 */

if ( ! defined( 'ABSPATH' ) || ! lily_show_section( 'show_shop_by_collections' ) ) {
	return;
}

$heading     = trim( (string) lily_get_option( 'collections_heading', '' ) );
$description = lily_get_option( 'collections_description', '' );

if ( '' === $heading ) {
	$heading = esc_html__( 'Find the lens that fits you', 'lily' );
}

// Development fallback only; a configured Dashboard description always wins.
if ( '' === trim( (string) $description ) ) {
	$description = esc_html__( 'Explore our collections and choose the style that matches you.', 'lily' );
}
$terms       = lily_get_selected_terms( 'collections_to_display', 'product_cat' );

// Nothing selected yet: show Lily's three main collections, resolved
// dynamically from the real WooCommerce categories (no hardcoded URLs);
// explicit Dashboard selection remains the override.
if ( empty( $terms ) ) {
	foreach ( array(
		array( 'colored-lenses' ),
		array( 'clear-lenses' ),
		array( 'accessories', 'lens-care', 'accessories-lens-care' ),
	) as $lily_slugs ) {
		foreach ( $lily_slugs as $lily_slug ) {
			$lily_term = get_term_by( 'slug', $lily_slug, 'product_cat' );

			if ( $lily_term && ! is_wp_error( $lily_term ) ) {
				$terms[] = $lily_term;
				break;
			}
		}
	}
}

$lily_cta_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' );
?>
<section class="lily-section lily-collections" aria-label="<?php esc_attr_e( 'Shop by Collection', 'lily' ); ?>">
	<?php lily_container_open(); ?>
		<div class="lily-collections__layout">
			<div class="lily-collections__intro">
				<p class="lily-collections__eyebrow"><?php esc_html_e( 'Shop by Collection', 'lily' ); ?></p>
				<?php lily_section_heading( $heading, $description ); ?>
				<a class="lily-collections__cta" href="<?php echo esc_url( $lily_cta_url ); ?>">
					<?php esc_html_e( 'Explore Collections', 'lily' ); ?>
					<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
				</a>
			</div>

			<div class="lily-collections__grid">
				<?php
				foreach ( $terms as $term ) {
					get_template_part( 'template-parts/components/collection-card', null, array( 'term' => $term ) );
				}
				?>
			</div>
		</div>
	<?php lily_container_close(); ?>
</section>
