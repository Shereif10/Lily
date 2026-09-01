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
?>
<article class="lily-collection-card">
	<a href="<?php echo esc_url( $link ); ?>">
		<img src="<?php echo esc_url( $image_url ? $image_url : $placeholder ); ?>" alt="<?php echo esc_attr( $term->name ); ?>" loading="lazy">
		<h3><?php echo esc_html( $term->name ); ?></h3>
	</a>
</article>
