<?php
/**
 * Hero section — single editorial image, dashboard-controlled.
 *
 * @package Lily
 */

if ( ! defined( 'ABSPATH' ) || ! lily_show_section( 'show_hero' ) ) {
	return;
}

$slides_raw = (array) lily_get_option( 'hero_slides', array() );

$slide = null;

foreach ( $slides_raw as $candidate ) {
	if ( ! is_array( $candidate ) || empty( $candidate['enabled'] ) ) {
		continue;
	}

	$image = (int) ( $candidate['image'] ?? 0 );
	$title = trim( (string) ( $candidate['title'] ?? '' ) );

	// A slide with only Arabic copy still counts as content.
	$title_ar_check = function_exists( 'lily_ml_value' ) ? trim( (string) ( $candidate['title_ar'] ?? '' ) ) : '';

	if ( ! $image && '' === $title && '' === $title_ar_check ) {
		continue;
	}

	$slide = array(
		'image'       => $image,
		'mobile'      => (int) ( $candidate['mobile_image'] ?? 0 ),
		'title'       => function_exists( 'lily_ml_value' ) ? (string) lily_ml_value( $title, $candidate['title_ar'] ?? '' ) : $title,
		'description' => function_exists( 'lily_ml_value' ) ? trim( (string) lily_ml_value( $candidate['description'] ?? '', $candidate['description_ar'] ?? '' ) ) : trim( (string) ( $candidate['description'] ?? '' ) ),
		'cta_text'    => function_exists( 'lily_ml_value' ) ? trim( (string) lily_ml_value( $candidate['cta_text'] ?? '', $candidate['cta_text_ar'] ?? '' ) ) : trim( (string) ( $candidate['cta_text'] ?? '' ) ),
		'cta_url'     => trim( (string) ( $candidate['cta_url'] ?? '' ) ),
	);

	break;
}

if ( null === $slide ) {
	return;
}

// Dynamic CTA fallback: match the slide to its category, otherwise the shop.
if ( '' === $slide['cta_url'] ) {
	$haystack = strtolower( $slide['title'] . ' ' . $slide['cta_text'] );

	if ( false !== strpos( $haystack, 'colored' ) ) {
		$slide['cta_url'] = lily_nav_product_cat_url( 'colored-lenses' );
	} elseif ( false !== strpos( $haystack, 'clear' ) ) {
		$slide['cta_url'] = lily_nav_product_cat_url( 'clear-lenses' );
	} elseif ( false !== strpos( $haystack, 'accessor' ) || false !== strpos( $haystack, 'care' ) ) {
		$slide['cta_url'] = lily_nav_product_cat_url( 'accessories' );
	} else {
		$slide['cta_url'] = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' );
	}
}

if ( '' === $slide['cta_text'] ) {
	$slide['cta_text'] = __( 'Shop Now', 'lily' );
}
?>
<section class="lily-section lily-hero" aria-label="<?php esc_attr_e( 'Featured collections', 'lily' ); ?>">
	<figure class="lily-hero__media">
			<?php if ( $slide['image'] ) : ?>
				<?php
				$image_args = array(
					'loading'       => 'eager',
					'fetchpriority' => 'high',
					'alt'           => '' !== $slide['title'] ? $slide['title'] : get_bloginfo( 'name' ),
				);
				?>
				<?php if ( $slide['mobile'] ) : ?>
					<picture>
						<source media="(max-width: 767px)" srcset="<?php echo esc_url( wp_get_attachment_image_url( $slide['mobile'], 'large' ) ); ?>">
						<?php echo wp_get_attachment_image( $slide['image'], 'full', false, $image_args ); ?>
					</picture>
				<?php else : ?>
					<?php echo wp_get_attachment_image( $slide['image'], 'full', false, $image_args ); ?>
				<?php endif; ?>
			<?php endif; ?>

			<div class="lily-hero__container lily-container">
				<div class="lily-hero__content">
					<p class="lily-hero__eyebrow"><?php esc_html_e( 'Lily Contact Lenses', 'lily' ); ?></p>

					<?php if ( '' !== $slide['title'] ) : ?>
					<?php
					// "Primary | Accent": text after the pipe gets the editorial accent styling.
					$title_parts = array_map( 'trim', explode( '|', $slide['title'], 2 ) );
					?>
					<h1 class="lily-hero__title">
						<?php echo esc_html( $title_parts[0] ); ?>
						<?php if ( ! empty( $title_parts[1] ) ) : ?>
							<span class="lily-hero__title-accent"><?php echo esc_html( $title_parts[1] ); ?></span>
						<?php endif; ?>
					</h1>
				<?php endif; ?>

					<?php if ( '' !== $slide['description'] ) : ?>
						<p class="lily-hero__description"><?php echo esc_html( $slide['description'] ); ?></p>
					<?php endif; ?>

					<a class="lily-hero__cta" href="<?php echo esc_url( $slide['cta_url'] ); ?>">
						<?php echo esc_html( $slide['cta_text'] ); ?>
					</a>
				</div>
			</div>
		</figure>
</section>
