<?php
/**
 * Color card component — circular swatch + label below.
 *
 * Expected variable: $args['term'] (a PARENT color term — the homepage
 * Shop by Colors section only ever surfaces parent colors).
 *
 * @package Lily
 */

if ( ! defined( 'ABSPATH' ) || empty( $args['term'] ) || ! $args['term'] instanceof WP_Term ) {
	return;
}

$term      = $args['term'];
$image_url = lily_get_color_image_url( $term, 'large' );
$link      = lily_get_attribute_filter_url( 'pa_color', $term );
$label     = function_exists( 'lily_term_name' ) ? lily_term_name( $term ) : $term->name;

// Visual identity: optional marketing image, else the canonical swatch
// (lily_swatch_color term meta) with the slug tone map as the last fallback.
// NOTE: these are product-data colors (actual lens eye colors), not website
// UI — they are exempt from the six-color interface palette, exactly like
// product/collection photography, and must stay truthful.
$lily_swatch = '';
if ( ! $image_url ) {
	$lily_swatch = lily_get_color_swatch_color( $term->slug );
}
?>
<article class="lily-color-card">
	<a href="<?php echo esc_url( $link ); ?>">
		<?php if ( $image_url ) : ?>
			<img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $label ); ?>" loading="lazy">
		<?php else : ?>
			<span class="lily-color-card__swatch" style="background:<?php echo esc_attr( $lily_swatch ); ?>;" aria-hidden="true"></span>
		<?php endif; ?>
		<span class="lily-color-card__label"><?php echo esc_html( $label ); ?></span>
	</a>
</article>
