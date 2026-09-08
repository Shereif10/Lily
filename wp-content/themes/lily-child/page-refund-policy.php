<?php
/**
 * Lily Refund Policy page (slug: refund-policy).
 *
 * Refund-only template: centered hero, seven numbered editorial policy
 * sections, and a soft help CTA band linking to the Contact page. Copy
 * preserves the approved Lily refund source — no refund rules,
 * deadlines, methods, timelines or guarantees are invented, and anything
 * still unconfirmed is left as plain editable placeholder text. Uses the
 * existing global header/footer and the shared lily-container helper.
 *
 * @package Lily
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* Refund-only body class so the Refund CSS scope applies without touching globals. */
add_filter(
	'body_class',
	static function ( $classes ) {
		$classes[] = 'lily-refund-page';
		return $classes;
	}
);

get_header();

$lily_contact_page = get_page_by_path( 'contact' );
$lily_contact_url  = $lily_contact_page instanceof WP_Post ? get_permalink( $lily_contact_page ) : home_url( '/contact/' );

$lily_sections = array(
	array(
		'num'   => '01',
		'title' => __( 'Eligibility for Refunds', 'lily' ),
		'body'  => __( 'We want every customer to be satisfied. If something went wrong with your order, you may request a refund — because contact lenses are personal-care products, eligibility rules are strict and every request is reviewed individually.', 'lily' ),
	),
	array(
		'num'   => '02',
		'title' => __( 'Non-Refundable Items', 'lily' ),
		'body'  => __( 'Opened or used lenses generally cannot be resold and are therefore generally non-refundable, except where required by law or where the product reached you faulty.', 'lily' ),
	),
	array(
		'num'   => '03',
		'title' => __( 'How to Request a Refund', 'lily' ),
		'body'  => __( 'Contact us by phone or through the contact page with your order number and a short description of the issue, along with photos if the product arrived damaged or incorrect.', 'lily' ),
	),
	array(
		'num'   => '04',
		'title' => __( 'Refund Process', 'lily' ),
		'body'  => __( 'Returned items are inspected before any refund decision is made. Our team reviews your request and the item condition, then confirms whether a refund is approved.', 'lily' ),
	),
	array(
		'num'   => '05',
		'title' => __( 'Refund Methods', 'lily' ),
		'body'  => __( 'Once a refund is approved, our team confirms the refund method with you directly. Final refund-method details are still being confirmed and will be published here.', 'lily' ),
	),
	array(
		'num'   => '06',
		'title' => __( 'Refund Timeline', 'lily' ),
		'body'  => __( 'Refund timing is confirmed with you when your refund is approved. Final refund timeframes are still being confirmed and will be published here — if you are waiting on a refund, contact us with your order number.', 'lily' ),
	),
	array(
		'num'   => '07',
		'title' => __( 'Important Notes', 'lily' ),
		'body'  => __( 'If your order arrived damaged or you received the wrong item, contact us as soon as possible with photos and your order number so we can make it right.', 'lily' ),
	),
);
?>

<main id="primary" class="site-main lily-refund">
	<section class="lily-refund-band lily-refund-hero">
		<?php lily_container_open( 'lily-refund-hero__inner' ); ?>
			<h1 class="lily-refund-hero__title"><?php esc_html_e( 'Refund Policy', 'lily' ); ?></h1>
			<span class="lily-refund-hero__rule" aria-hidden="true"></span>
			<p class="lily-refund-hero__sub"><?php esc_html_e( 'Our goal is your satisfaction. Please read our refund policy carefully.', 'lily' ); ?></p>
		<?php lily_container_close(); ?>
	</section>

	<section class="lily-refund-band lily-refund-policy" aria-label="<?php esc_attr_e( 'Refund policy details', 'lily' ); ?>">
		<?php lily_container_open( 'lily-refund-policy__inner' ); ?>
			<ol class="lily-refund-list">
				<?php foreach ( $lily_sections as $lily_section ) : ?>
					<li class="lily-refund-row">
						<span class="lily-refund-row__num" aria-hidden="true"><?php echo esc_html( $lily_section['num'] ); ?></span>
						<span class="lily-refund-row__divider" aria-hidden="true"></span>
						<span class="lily-refund-row__body">
							<h2 class="lily-refund-row__title"><?php echo esc_html( $lily_section['title'] ); ?></h2>
							<p class="lily-refund-row__text"><?php echo esc_html( $lily_section['body'] ); ?></p>
						</span>
					</li>
				<?php endforeach; ?>
			</ol>
		<?php lily_container_close(); ?>
	</section>

	<section class="lily-refund-band lily-refund-cta" aria-labelledby="lily-refund-cta-heading">
		<?php lily_container_open( 'lily-refund-cta__inner' ); ?>
			<p class="lily-refund-eyebrow lily-refund-eyebrow--center"><?php esc_html_e( 'Need more help?', 'lily' ); ?></p>
			<p class="lily-refund-cta__lead" id="lily-refund-cta-heading"><?php esc_html_e( 'Have a question about your refund?', 'lily' ); ?></p>
			<a class="lily-button lily-refund-cta__btn" href="<?php echo esc_url( $lily_contact_url ); ?>"><?php esc_html_e( 'Contact Us', 'lily' ); ?> <span aria-hidden="true">&rarr;</span></a>
		<?php lily_container_close(); ?>
	</section>
</main>

<?php
get_footer();
