<?php
/**
 * Collection card component — editorial image + title below.
 *
 * Expected variable: $args['term'].
 *
 * @package Lily
 */

if ( ! defined( 'ABSPATH' ) || empty( $args['term'] ) || ! $args['term'] instanceof WP_Term ) {
	return;
}

$term         = $args['term'];
$image_url    = lily_get_product_cat_image_url( $term, 'large' );
$link         = get_term_link( $term );
$placeholder  = LILY_THEME_URI . '/assets/images/collection-placeholder.svg';

if ( is_wp_error( $link ) ) {
	return;
}

/* Intrinsic dimensions reserve the card's space before the image loads
 * (CLS). CSS still controls the rendered size (4:5 aspect ratio). */
$thumbnail_id = absint( get_term_meta( $term->term_id, 'thumbnail_id', true ) );
$image_meta   = $thumbnail_id ? wp_get_attachment_image_src( $thumbnail_id, 'large' ) : false;
$width        = $image_meta ? (int) $image_meta[1] : 800;
$height       = $image_meta ? (int) $image_meta[2] : 1000;
?>
<article class="lily-collection-card">
	<a href="<?php echo esc_url( $link ); ?>">
		<img src="<?php echo esc_url( $image_url ? $image_url : $placeholder ); ?>" alt="<?php echo esc_attr( $term->name ); ?>" width="<?php echo esc_attr( $width ); ?>" height="<?php echo esc_attr( $height ); ?>" loading="lazy">
		<h3><?php echo esc_html( $term->name ); ?></h3>
	</a>
</article>
