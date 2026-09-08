<?php
/**
 * Lily About Us page (slug: about).
 *
 * About-only template: exact editorial section order ending with the
 * Find Your Perfect Lens CTA. Every visible string and image reads from
 * the Lily dashboard (About Page tab, same settings system as the
 * homepage) with the approved on-page content as fallback. Uses the
 * existing global header/footer, the shared lily-container helper and
 * the native WooCommerce Shop URL.
 *
 * @package Lily
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* About-only body class so the About CSS scope applies without touching globals. */
add_filter(
	'body_class',
	static function ( $classes ) {
		$classes[] = 'lily-about-page';
		return $classes;
	}
);

get_header();

$lily_shop_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );

$lily_cta_custom = lily_get_option( 'cta_url', '' );
$lily_cta_url    = '' !== $lily_cta_custom ? $lily_cta_custom : $lily_shop_url;

/* Masthead image: dashboard choice wins, then the page featured image, then the palette fallback. */
$lily_masthead_id = (int) lily_get_option( 'about_image', 0 );
if ( ! $lily_masthead_id && has_post_thumbnail() ) {
	$lily_masthead_id = get_post_thumbnail_id();
}
$lily_masthead_alt = $lily_masthead_id ? (string) get_post_meta( $lily_masthead_id, '_wp_attachment_image_alt', true ) : '';
if ( '' === $lily_masthead_alt ) {
	$lily_masthead_alt = esc_attr__( 'Lily editorial', 'lily' );
}

/* Story image: dashboard choice wins, then the palette fallback. */
$lily_story_id  = (int) lily_get_option( 'story_image', 0 );
$lily_story_alt = $lily_story_id ? (string) get_post_meta( $lily_story_id, '_wp_attachment_image_alt', true ) : '';
if ( '' === $lily_story_alt ) {
	$lily_story_alt = esc_attr__( 'Lily editorial', 'lily' );
}

$lily_masthead_fallback = LILY_THEME_URI . '/assets/images/collection-placeholder.svg';
$lily_story_fallback    = LILY_THEME_URI . '/assets/images/gallery-2.svg';

$lily_mission_text = lily_get_option( 'mission_text', '' );
?>

<main id="primary" class="site-main lily-about">
	<?php /* 1. ABOUT LILY — masthead. */ ?>
	<section class="lily-about-band lily-about-masthead">
		<?php lily_container_open( 'lily-about-masthead__inner' ); ?>
			<h1 class="lily-about-masthead__title"><?php echo esc_html( lily_get_option( 'about_title', esc_html__( 'About Lily', 'lily' ) ) ); ?></h1>
			<p class="lily-about-masthead__sub"><?php echo esc_html( lily_get_option( 'about_subtitle', esc_html__( 'Beauty that feels naturally yours.', 'lily' ) ) ); ?></p>
			<figure class="lily-about-masthead__media">
				<?php if ( $lily_masthead_id ) : ?>
					<?php echo wp_get_attachment_image( $lily_masthead_id, 'large', false, array( 'alt' => $lily_masthead_alt ) ); ?>
				<?php else : ?>
					<img src="<?php echo esc_url( $lily_masthead_fallback ); ?>" alt="<?php echo esc_attr( $lily_masthead_alt ); ?>" loading="eager">
				<?php endif; ?>
			</figure>
		<?php lily_container_close(); ?>
	</section>

	<?php /* 2. OUR STORY — text left, image right. */ ?>
	<section class="lily-about-band lily-about-story" aria-labelledby="lily-about-story-heading">
		<?php lily_container_open( 'lily-about-story__inner' ); ?>
			<div class="lily-about-story__text">
				<p class="lily-about-eyebrow" id="lily-about-story-heading"><?php echo esc_html( lily_get_option( 'story_eyebrow', esc_html__( 'Our Story', 'lily' ) ) ); ?></p>
				<p class="lily-about-story__lead"><?php echo esc_html( lily_get_option( 'story_lead', esc_html__( 'Lily was created around one simple idea — that changing your look does not have to mean changing who you are.', 'lily' ) ) ); ?></p>
				<p><?php echo esc_html( lily_get_option( 'story_p1', esc_html__( 'Choosing a colored lens can feel overwhelming, so we keep everything calm and clear: honest shades, natural-looking results and guidance that feels personal.', 'lily' ) ) ); ?></p>
				<p><?php echo esc_html( lily_get_option( 'story_p2', esc_html__( 'Every collection is edited with care, so you can compare, explore and find the color that feels like you.', 'lily' ) ) ); ?></p>
			</div>
			<figure class="lily-about-story__media">
				<?php if ( $lily_story_id ) : ?>
					<?php echo wp_get_attachment_image( $lily_story_id, 'large', false, array( 'alt' => $lily_story_alt ) ); ?>
				<?php else : ?>
					<img src="<?php echo esc_url( $lily_story_fallback ); ?>" alt="<?php echo esc_attr( $lily_story_alt ); ?>" loading="lazy">
				<?php endif; ?>
			</figure>
		<?php lily_container_close(); ?>
	</section>

	<?php /* 3. OUR MISSION — centered statement. */ ?>
	<section class="lily-about-band lily-about-mission" aria-labelledby="lily-about-mission-heading">
		<?php lily_container_open( 'lily-about-mission__inner' ); ?>
			<p class="lily-about-eyebrow lily-about-eyebrow--center" id="lily-about-mission-heading"><?php echo esc_html( lily_get_option( 'mission_eyebrow', esc_html__( 'Our Mission', 'lily' ) ) ); ?></p>
			<p class="lily-about-mission__statement"><?php echo esc_html( lily_get_option( 'mission_statement', esc_html__( 'To make self-expression feel effortless — through colors that complement the way you see yourself.', 'lily' ) ) ); ?></p>
			<?php if ( '' !== $lily_mission_text ) : ?>
				<p class="lily-about-mission__text"><?php echo esc_html( $lily_mission_text ); ?></p>
			<?php endif; ?>
		<?php lily_container_close(); ?>
	</section>

	<?php /* 4. OUR VISION — statement left, supporting text right. */ ?>
	<section class="lily-about-band lily-about-vision" aria-labelledby="lily-about-vision-heading">
		<?php lily_container_open( 'lily-about-vision__inner' ); ?>
			<div class="lily-about-vision__statement">
				<p class="lily-about-eyebrow" id="lily-about-vision-heading"><?php echo esc_html( lily_get_option( 'vision_eyebrow', esc_html__( 'Our Vision', 'lily' ) ) ); ?></p>
				<p class="lily-about-vision__lead"><?php echo esc_html( lily_get_option( 'vision_statement', esc_html__( 'A world where beauty feels personal, natural, and entirely your own.', 'lily' ) ) ); ?></p>
			</div>
			<div class="lily-about-vision__text">
				<p><?php echo esc_html( lily_get_option( 'vision_text', esc_html__( 'We want Lily to be the place you return to whenever you feel like a refresh — a quiet, trusted destination for discovering your next look.', 'lily' ) ) ); ?></p>
			</div>
		<?php lily_container_close(); ?>
	</section>

	<?php /* 5. OUR APPROACH — three hairline rows, no cards. */ ?>
	<section class="lily-about-band lily-about-approach" aria-labelledby="lily-about-approach-heading">
		<?php lily_container_open( 'lily-about-approach__inner' ); ?>
			<p class="lily-about-eyebrow" id="lily-about-approach-heading"><?php echo esc_html( lily_get_option( 'approach_eyebrow', esc_html__( 'Our Approach', 'lily' ) ) ); ?></p>
			<ol class="lily-about-approach__list">
				<li class="lily-about-approach__row">
					<span class="lily-about-approach__num" aria-hidden="true"><?php echo esc_html( lily_get_option( 'approach_1_num', esc_html__( '01', 'lily' ) ) ); ?></span>
					<span class="lily-about-approach__body">
						<span class="lily-about-approach__name"><?php echo esc_html( lily_get_option( 'approach_1_name', esc_html__( 'Natural', 'lily' ) ) ); ?></span>
						<span class="lily-about-approach__desc"><?php echo esc_html( lily_get_option( 'approach_1_desc', esc_html__( 'Designed to complement, not overpower.', 'lily' ) ) ); ?></span>
					</span>
				</li>
				<li class="lily-about-approach__row">
					<span class="lily-about-approach__num" aria-hidden="true"><?php echo esc_html( lily_get_option( 'approach_2_num', esc_html__( '02', 'lily' ) ) ); ?></span>
					<span class="lily-about-approach__body">
						<span class="lily-about-approach__name"><?php echo esc_html( lily_get_option( 'approach_2_name', esc_html__( 'Confidence', 'lily' ) ) ); ?></span>
						<span class="lily-about-approach__desc"><?php echo esc_html( lily_get_option( 'approach_2_desc', esc_html__( 'A subtle change can change the way you feel.', 'lily' ) ) ); ?></span>
					</span>
				</li>
				<li class="lily-about-approach__row">
					<span class="lily-about-approach__num" aria-hidden="true"><?php echo esc_html( lily_get_option( 'approach_3_num', esc_html__( '03', 'lily' ) ) ); ?></span>
					<span class="lily-about-approach__body">
						<span class="lily-about-approach__name"><?php echo esc_html( lily_get_option( 'approach_3_name', esc_html__( 'Quality', 'lily' ) ) ); ?></span>
						<span class="lily-about-approach__desc"><?php echo esc_html( lily_get_option( 'approach_3_desc', esc_html__( 'Created with care for everyday comfort and beauty.', 'lily' ) ) ); ?></span>
					</span>
				</li>
			</ol>
		<?php lily_container_close(); ?>
	</section>

	<?php /* 6. FIND YOUR PERFECT LENS — final closing CTA. */ ?>
	<section class="lily-about-band lily-about-cta" aria-labelledby="lily-about-cta-heading">
		<?php lily_container_open( 'lily-about-cta__inner' ); ?>
			<h2 class="lily-about-cta__title" id="lily-about-cta-heading"><?php echo esc_html( lily_get_option( 'cta_heading', esc_html__( 'Find Your Perfect Lens', 'lily' ) ) ); ?></h2>
			<p class="lily-about-cta__sub"><?php echo esc_html( lily_get_option( 'cta_text', esc_html__( 'Discover the color that feels like you.', 'lily' ) ) ); ?></p>
			<a class="lily-about-cta__btn" href="<?php echo esc_url( $lily_cta_url ); ?>"><?php echo esc_html( lily_get_option( 'cta_button', esc_html__( 'Shop Lenses', 'lily' ) ) ); ?></a>
		<?php lily_container_close(); ?>
	</section>
</main>

<?php
get_footer();
