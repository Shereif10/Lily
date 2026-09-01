<?php
/**
 * Color card component — circular swatch + label below.
 *
 * Expected variable: $args['term'].
 *
 * @package Lily
 */

if ( ! defined( 'ABSPATH' ) || empty( $args['term'] ) || ! $args['term'] instanceof WP_Term ) {
	return;
}

$term      = $args['term'];
$image_url = lily_get_color_image_url( $term, 'large' );
$link      = lily_get_attribute_filter_url( 'pa_color', $term );

// Visual fallback when no marketing image exists yet: a calm swatch tone
// derived from the real term slug so every color keeps a visible identity.
$lily_swatch = '';
if ( ! $image_url ) {
	$lily_tones = array(
		'blue'  => '#7d94a8',
		'gray'  => '#aaa49a',
		'green' => '#6f7d5f',
		'hazel' => '#9c7b4f',
		'brown' => '#885337',
		'other' => '#d8d2bd',
	);
	$lily_swatch = isset( $lily_tones[ $term->slug ] ) ? $lily_tones[ $term->slug ] : '#e3ddc9';
}
?>
<article class="lily-color-card">
	<a href="<?php echo esc_url( $link ); ?>">
		<?php if ( $image_url ) : ?>
			<img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $term->name ); ?>" loading="lazy">
		<?php else : ?>
			<span class="lily-color-card__swatch" style="background:<?php echo esc_attr( $lily_swatch ); ?>;" aria-hidden="true"></span>
		<?php endif; ?>
		<span class="lily-color-card__label"><?php echo esc_html( $term->name ); ?></span>
	</a>
</article>
