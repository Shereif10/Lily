<?php
/**
 * Lily Shipping Policy page (slug: shipping-policy).
 *
 * Shipping-only template: centered hero, six numbered editorial policy
 * sections, and a soft help CTA band linking to the Contact page. Copy is
 * drawn from the approved Lily shipping-policy source — no fees, times,
 * locations or guarantees are invented. Uses the existing global
 * header/footer and the shared lily-container helper.
 *
 * @package Lily
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* Shipping-only body class so the Shipping CSS scope applies without touching globals. */
add_filter(
	'body_class',
	static function ( $classes ) {
		$classes[] = 'lily-shipping-page';
		return $classes;
	}
);

get_header();

$lily_contact_page = get_page_by_path( 'contact' );
$lily_contact_url  = $lily_contact_page instanceof WP_Post ? get_permalink( $lily_contact_page ) : home_url( '/contact/' );

$lily_sections = array(
	array(
		'num'   => '01',
		'title' => __( 'Shipping & Delivery', 'lily' ),
		'body'  => __( 'Lily currently delivers within Egypt. Orders are prepared after confirmation, and our team makes sure every parcel leaves carefully packed and ready for its journey to you.', 'lily' ),
	),
	array(
		'num'   => '02',
		'title' => __( 'Delivery Areas', 'lily' ),
		'body'  => __( 'At checkout you choose your Governorate / Area from the delivery areas we support across Egypt. Some groups cover several detailed locations, which are shown inside the area selection to help you pick correctly.', 'lily' ),
	),
	array(
		'num'   => '03',
		'title' => __( 'Delivery Times', 'lily' ),
		'body'  => __( 'Order preparation and delivery timing depend on your area and order details. Any timing shared during checkout or order confirmation applies — if anything is unclear, just ask us before ordering.', 'lily' ),
	),
	array(
		'num'   => '04',
		'title' => __( 'Shipping Fees', 'lily' ),
		'body'  => __( 'Shipping fees are calculated based on the selected delivery area. The exact fee is always shown during checkout before you place your order.', 'lily' ),
	),
	array(
		'num'   => '05',
		'title' => __( 'How Your Order Is Delivered', 'lily' ),
		'body'  => __( 'Please provide a detailed address — area, street, building, floor, apartment and a nearby landmark — along with a reachable phone number so the courier can find you easily. If your delivery is delayed or something looks wrong, contact us with your order number and we will follow up with the courier.', 'lily' ),
	),
	array(
		'num'   => '06',
		'title' => __( 'Important Notes', 'lily' ),
		'body'  => __( 'Please double-check your address before ordering. If you notice a mistake after ordering, contact us immediately and we will do our best to update it before dispatch. If an item arrived damaged or incorrect, reach out as soon as possible with photos and your order number.', 'lily' ),
	),
);
?>

<main id="primary" class="site-main lily-shipping">
	<section class="lily-shipping-band lily-shipping-hero">
		<?php lily_container_open( 'lily-shipping-hero__inner' ); ?>
			<h1 class="lily-shipping-hero__title"><?php esc_html_e( 'Shipping Policy', 'lily' ); ?></h1>
			<span class="lily-shipping-hero__rule" aria-hidden="true"></span>
			<p class="lily-shipping-hero__sub"><?php esc_html_e( 'Everything you need to know about delivery with Lily.', 'lily' ); ?></p>
			<p class="lily-shipping-hero__intro"><?php esc_html_e( 'Lily currently delivers within Egypt. This page explains how delivery works — the exact fee and details for your order are always confirmed during checkout.', 'lily' ); ?></p>
		<?php lily_container_close(); ?>
	</section>

	<section class="lily-shipping-band lily-shipping-policy" aria-label="<?php esc_attr_e( 'Shipping policy details', 'lily' ); ?>">
		<?php lily_container_open( 'lily-shipping-policy__inner' ); ?>
			<ol class="lily-shipping-list">
				<?php foreach ( $lily_sections as $lily_section ) : ?>
					<li class="lily-shipping-row">
						<span class="lily-shipping-row__num" aria-hidden="true"><?php echo esc_html( $lily_section['num'] ); ?></span>
						<span class="lily-shipping-row__divider" aria-hidden="true"></span>
						<span class="lily-shipping-row__body">
							<h2 class="lily-shipping-row__title"><?php echo esc_html( $lily_section['title'] ); ?></h2>
							<p class="lily-shipping-row__text"><?php echo esc_html( $lily_section['body'] ); ?></p>
						</span>
					</li>
				<?php endforeach; ?>
			</ol>
		<?php lily_container_close(); ?>
	</section>

	<section class="lily-shipping-band lily-shipping-cta" aria-labelledby="lily-shipping-cta-heading">
		<?php lily_container_open( 'lily-shipping-cta__inner' ); ?>
			<p class="lily-shipping-eyebrow lily-shipping-eyebrow--center"><?php esc_html_e( 'Need more help?', 'lily' ); ?></p>
			<p class="lily-shipping-cta__lead" id="lily-shipping-cta-heading"><?php esc_html_e( 'Have a question about your delivery?', 'lily' ); ?></p>
			<a class="lily-button lily-shipping-cta__btn" href="<?php echo esc_url( $lily_contact_url ); ?>"><?php esc_html_e( 'Contact Us', 'lily' ); ?> <span aria-hidden="true">&rarr;</span></a>
		<?php lily_container_close(); ?>
	</section>
</main>

<?php
get_footer();
