<?php
/**
 * Find Your Best Lenses — editorial two-column Lens Finder intro.
 *
 * @package Lily
 */

if ( ! defined( 'ABSPATH' ) || ! lily_show_section( 'show_lens_finder' ) ) {
	return;
}

$eyebrow     = trim( (string) lily_get_option( 'lens_finder_eyebrow', '' ) );
$heading     = trim( (string) lily_get_option( 'lens_finder_heading', '' ) );
$description = trim( (string) lily_get_option( 'lens_finder_description', '' ) );
$button_text = lily_get_option( 'lens_finder_start_text', esc_html__( 'Start Lens Finder', 'lily' ) );
$image_id    = absint( lily_get_option( 'lens_finder_image', 0 ) );
$image_alt   = trim( (string) lily_get_option( 'lens_finder_image_alt', '' ) );
$steps       = (array) lily_get_option( 'lens_finder_steps', array() );

// Neutral development fallbacks so the section stays previewable;
// configured Dashboard values replace them automatically.
if ( '' === $eyebrow ) {
	$eyebrow = esc_html__( 'Lens Finder', 'lily' );
}

if ( '' === $heading ) {
	$heading = esc_html__( 'Find your perfect lenses', 'lily' );
}

if ( '' === $description ) {
	$description = esc_html__( "Answer a few simple questions and we'll recommend the lenses that are right for you.", 'lily' );
}

$lily_benefits = array(
	array(
		__( 'Personalized recommendations', 'lily' ),
		__( 'Tailored picks based on your eyes and style.', 'lily' ),
	),
	array(
		__( 'RX & non-RX options', 'lily' ),
		__( 'Find lenses that fit your prescription or lifestyle.', 'lily' ),
	),
	array(
		__( 'Comfort & style matched for you', 'lily' ),
		__( 'Beautiful lenses that feel as good as they look.', 'lily' ),
	),
);

/**
 * Step icons.
 */
$lily_step_icons = array(

	// Step 01 — Eye.
	'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
		<path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6S2.5 12 2.5 12Z"></path>
		<circle cx="12" cy="12" r="2.5"></circle>
	</svg>',

	// Step 02 — Preferences / Sliders.
	'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
		<line x1="4" y1="6" x2="20" y2="6"></line>
		<line x1="4" y1="12" x2="20" y2="12"></line>
		<line x1="4" y1="18" x2="20" y2="18"></line>
		<circle cx="9" cy="6" r="1.5"></circle>
		<circle cx="15" cy="12" r="1.5"></circle>
		<circle cx="11" cy="18" r="1.5"></circle>
	</svg>',

	// Step 03 — Explore / Target.
	'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
		<circle cx="12" cy="12" r="8"></circle>
		<circle cx="12" cy="12" r="3"></circle>
	</svg>',

	// Step 04 — Choose / Heart.
	'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
		<path d="M20.8 4.8a5.5 5.5 0 0 0-7.8 0L12 5.8l-1-1a5.5 5.5 0 0 0-7.8 7.8l1 1L12 21l7.8-7.4 1-1a5.5 5.5 0 0 0 0-7.8Z"></path>
	</svg>',
);

?>

<section
	id="find-your-best-lenses"
	class="lily-section lily-lens-finder"
	aria-labelledby="lily-lens-finder-heading"
>

	<?php lily_container_open( 'lily-lens-finder__inner' ); ?>

		<div class="lily-lens-finder__intro">

			<p class="lily-lens-finder__eyebrow">
				<?php echo esc_html( $eyebrow ); ?>
			</p>

			<h2 id="lily-lens-finder-heading">
				<?php echo esc_html( $heading ); ?>
			</h2>

			<span
				class="lily-lens-finder__rule"
				aria-hidden="true"
			></span>

			<p class="lily-lens-finder__description">
				<?php echo esc_html( $description ); ?>
			</p>

			<ul class="lily-lens-finder__benefits">

				<?php foreach ( $lily_benefits as $lily_benefit ) : ?>

					<li>

						<span class="lily-lens-finder__benefit-title">
							<?php echo esc_html( $lily_benefit[0] ); ?>
						</span>

						<span class="lily-lens-finder__benefit-text">
							<?php echo esc_html( $lily_benefit[1] ); ?>
						</span>

					</li>

				<?php endforeach; ?>

			</ul>

			<button
				class="lily-lens-finder__cta"
				type="button"
				data-lily-lens-start
			>

				<?php echo esc_html( $button_text ); ?>

				<svg
					width="15"
					height="15"
					viewBox="0 0 24 24"
					fill="none"
					stroke="currentColor"
					stroke-width="1.8"
					stroke-linecap="round"
					stroke-linejoin="round"
					aria-hidden="true"
					focusable="false"
				>
					<line x1="5" y1="12" x2="19" y2="12"></line>
					<polyline points="12 5 19 12 12 19"></polyline>
				</svg>

			</button>

			<p class="lily-lens-finder__support">
				<?php esc_html_e( 'Secure & private. Your data is safe with us.', 'lily' ); ?>
			</p>

		</div>

		<div class="lily-lens-finder__stage">

			<figure class="lily-lens-finder__visual">

				<?php if ( $image_id ) : ?>

					<?php
					echo wp_get_attachment_image(
						$image_id,
						'large',
						false,
						array(
							'alt' => '' !== $image_alt ? $image_alt : $heading,
						)
					);
					?>

				<?php else : ?>

					<img
						src="<?php echo esc_url( LILY_THEME_URI . '/assets/images/collection-placeholder.svg' ); ?>"
						alt="<?php echo esc_attr( '' !== $image_alt ? $image_alt : $heading ); ?>"
						loading="lazy"
					>

				<?php endif; ?>

			</figure>

			<div
				class="lily-lens-finder__app"
				data-lily-lens-finder
				hidden
			></div>

		</div>

	<?php lily_container_close(); ?>


	<?php lily_container_open( 'lily-lens-finder__steps' ); ?>

		<ul class="lily-lens-finder__steps-list">

			<?php foreach ( array_slice( (array) $steps, 0, 4 ) as $lily_index => $lily_step ) : ?>

				<?php

				$lily_number = trim(
					(string) ( $lily_step['number'] ?? '' )
				);

				$lily_step_title = function_exists( 'lily_ml_value' )
					? trim(
						(string) lily_ml_value(
							$lily_step['title'] ?? '',
							$lily_step['title_ar'] ?? ''
						)
					)
					: trim(
						(string) ( $lily_step['title'] ?? '' )
					);

				$lily_step_desc = function_exists( 'lily_ml_value' )
					? trim(
						(string) lily_ml_value(
							$lily_step['description'] ?? '',
							$lily_step['description_ar'] ?? ''
						)
					)
					: trim(
						(string) ( $lily_step['description'] ?? '' )
					);

				if (
					'' === $lily_number &&
					'' === $lily_step_title &&
					'' === $lily_step_desc
				) {
					continue;
				}

				$lily_icon = isset( $lily_step_icons[ $lily_index ] )
					? $lily_step_icons[ $lily_index ]
					: '';

				?>

				<li>

					<div class="lily-lens-finder__step-top">

						<span class="lily-lens-finder__step-number">
							<?php echo esc_html( $lily_number ); ?>
						</span>

						<?php if ( '' !== $lily_icon ) : ?>

							<span
								class="lily-lens-finder__step-icon"
								aria-hidden="true"
							>
								<?php
								echo $lily_icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Static inline SVG.
								?>
							</span>

						<?php endif; ?>

					</div>

					<span class="lily-lens-finder__step-title">
						<?php echo esc_html( $lily_step_title ); ?>
					</span>

					<span class="lily-lens-finder__step-text">
						<?php echo esc_html( $lily_step_desc ); ?>
					</span>

				</li>

			<?php endforeach; ?>

		</ul>

	<?php lily_container_close(); ?>

</section>