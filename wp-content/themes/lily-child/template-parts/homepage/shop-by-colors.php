<?php
/**
 * Shop by Colors section — centered editorial swatch grid.
 *
 * @package Lily
 */

if ( ! defined( 'ABSPATH' ) || ! lily_show_section( 'show_shop_by_colors' ) ) {
	return;
}

$heading     = trim( (string) lily_get_option( 'colors_heading', '' ) );
$description = lily_get_option( 'colors_description', '' );
$terms       = lily_get_selected_terms( 'colors_to_display', 'pa_color' );
$view_all    = lily_get_option( 'view_all_colors_link', array() );

if ( '' === $heading ) {
	$heading = esc_html__( 'Find the color that expresses you', 'lily' );
}

// Development fallback only; a configured Dashboard description always wins.
if ( '' === trim( (string) $description ) ) {
	$description = esc_html__( 'Discover shades designed to match your unique look.', 'lily' );
}

// Nothing selected yet: show the real parent pa_color terms so the section
// still renders; explicit Dashboard selection remains the override. Child
// shades never surface here — parent colors only.
if ( empty( $terms ) ) {
	$terms = function_exists( 'lily_color_parents' )
		? lily_color_parents( array( 'hide_empty' => false ) )
		: get_terms(
			array(
				'taxonomy'   => 'pa_color',
				'parent'     => 0,
				'hide_empty' => false,
			)
		);

	if ( is_wp_error( $terms ) ) {
		$terms = array();
	}
}

// Safety net: even a Dashboard selection can only ever show parent colors.
$terms = array_values(
	array_filter(
		(array) $terms,
		static function ( $term ) {
			return $term instanceof WP_Term && ! $term->parent;
		}
	)
);

if ( empty( $terms ) ) {
	return;
}

// Centered editorial grid: ten colors, five per row.
$terms = array_slice( $terms, 0, 10 );

// Default label + destination for the existing View All dashboard link.
if ( empty( $view_all['title'] ) ) {
	$view_all['title'] = __( 'View All Colors', 'lily' );
}

if ( function_exists( 'lily_ml_value' ) && ! empty( $view_all['title_ar'] ) ) {
	$view_all['title'] = lily_ml_value( $view_all['title'], $view_all['title_ar'] );
}

if ( empty( $view_all['url'] ) ) {
	$view_all['url'] = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' );
}
?>
<section class="lily-colors" aria-label="<?php esc_attr_e( 'Shop by Colors', 'lily' ); ?>">
	<?php lily_container_open(); ?>
		<div class="lily-colors__intro">
			<p class="lily-colors__eyebrow"><?php esc_html_e( 'Shop by Colors', 'lily' ); ?></p>
			<?php lily_section_heading( $heading, $description ); ?>
		</div>

		<div class="lily-colors__grid">
			<?php
			foreach ( $terms as $term ) {
				get_template_part( 'template-parts/components/color-card', null, array( 'term' => $term ) );
			}
			?>
		</div>

		<div class="lily-colors__all">
			<a class="lily-colors__cta" href="<?php echo esc_url( $view_all['url'] ); ?>"<?php echo ! empty( $view_all['target'] ) && '_blank' === $view_all['target'] ? ' target="_blank" rel="noopener noreferrer"' : ''; ?>>
				<?php echo esc_html( $view_all['title'] ); ?>
				<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
			</a>
		</div>
	<?php lily_container_close(); ?>
</section>
