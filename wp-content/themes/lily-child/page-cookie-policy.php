<?php
/**
 * Lily Cookie Policy page (slug: cookie-policy).
 *
 * Cookie-only template: centered hero, seven numbered editorial policy
 * sections, and a soft help CTA band linking to the Contact page. Copy
 * preserves the approved Lily cookie source — no specific analytics,
 * marketing, targeting or third-party cookie behavior is claimed, and
 * anything unconfirmed is left as plain editable placeholder text.
 * Policy page only: no consent banner, settings panel, tracking
 * scripts or third-party services are added here. Uses the existing
 * global header/footer and the shared lily-container helper.
 *
 * @package Lily
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* Cookie-only body class so the Cookie CSS scope applies without touching globals. */
add_filter(
	'body_class',
	static function ( $classes ) {
		$classes[] = 'lily-cookie-page';
		return $classes;
	}
);

get_header();

$lily_contact_page = get_page_by_path( 'contact' );
$lily_contact_url  = $lily_contact_page instanceof WP_Post ? get_permalink( $lily_contact_page ) : home_url( '/contact/' );

$lily_sections = array(
	array(
		'num'   => '01',
		'title' => __( 'What Are Cookies?', 'lily' ),
		'body'  => __( 'Cookies are small files stored by your browser that help websites remember information between pages and visits.', 'lily' ),
	),
	array(
		'num'   => '02',
		'title' => __( 'How We Use Cookies', 'lily' ),
		'body'  => __( 'We use cookies to keep the store working correctly, to remember your cart and preferences, and to understand how the website is used.', 'lily' ),
	),
	array(
		'num'   => '03',
		'title' => __( 'Types of Cookies We Use', 'lily' ),
		'body'  => __( 'Essential cookies are required for basic functions such as cart sessions and checkout — the store cannot work properly without them. Preference cookies remember choices such as language to make your visit smoother.', 'lily' ),
	),
	array(
		'num'   => '04',
		'title' => __( 'Third-Party Cookies', 'lily' ),
		'body'  => __( 'This website runs on the standard platforms and services needed to operate an online store. We do not sell your personal data. If any third-party service sets its own cookies, those cookies are governed by that service’s own policy.', 'lily' ),
	),
	array(
		'num'   => '05',
		'title' => __( 'Managing Cookies', 'lily' ),
		'body'  => __( 'You can control or delete cookies through your browser settings. Blocking some cookies may affect how the store works — for example, your cart may not be remembered between visits.', 'lily' ),
	),
	array(
		'num'   => '06',
		'title' => __( 'Your Consent', 'lily' ),
		'body'  => __( 'Essential cookies are required for the store to function. For anything beyond what is strictly necessary, you can manage your choices at any time through your browser settings described above.', 'lily' ),
	),
	array(
		'num'   => '07',
		'title' => __( 'Changes to This Policy', 'lily' ),
		'body'  => __( 'We may update this policy from time to time; the current version will always appear on this page.', 'lily' ),
	),
);
?>

<main id="primary" class="site-main lily-cookie">
	<section class="lily-cookie-band lily-cookie-hero">
		<?php lily_container_open( 'lily-cookie-hero__inner' ); ?>
			<h1 class="lily-cookie-hero__title"><?php esc_html_e( 'Cookie Policy', 'lily' ); ?></h1>
			<span class="lily-cookie-hero__rule" aria-hidden="true"></span>
			<p class="lily-cookie-hero__sub"><?php esc_html_e( 'We use cookies to enhance your browsing experience on Lily. Please read this policy carefully.', 'lily' ); ?></p>
		<?php lily_container_close(); ?>
	</section>

	<section class="lily-cookie-band lily-cookie-policy" aria-label="<?php esc_attr_e( 'Cookie policy details', 'lily' ); ?>">
		<?php lily_container_open( 'lily-cookie-policy__inner' ); ?>
			<ol class="lily-cookie-list">
				<?php foreach ( $lily_sections as $lily_section ) : ?>
					<li class="lily-cookie-row">
						<span class="lily-cookie-row__num" aria-hidden="true"><?php echo esc_html( $lily_section['num'] ); ?></span>
						<span class="lily-cookie-row__divider" aria-hidden="true"></span>
						<span class="lily-cookie-row__body">
							<h2 class="lily-cookie-row__title"><?php echo esc_html( $lily_section['title'] ); ?></h2>
							<p class="lily-cookie-row__text"><?php echo esc_html( $lily_section['body'] ); ?></p>
						</span>
					</li>
				<?php endforeach; ?>
			</ol>
		<?php lily_container_close(); ?>
	</section>

	<section class="lily-cookie-band lily-cookie-cta" aria-labelledby="lily-cookie-cta-heading">
		<?php lily_container_open( 'lily-cookie-cta__inner' ); ?>
			<p class="lily-cookie-eyebrow lily-cookie-eyebrow--center"><?php esc_html_e( 'Need more help?', 'lily' ); ?></p>
			<p class="lily-cookie-cta__lead" id="lily-cookie-cta-heading"><?php esc_html_e( 'Have a question about cookies?', 'lily' ); ?></p>
			<a class="lily-button lily-cookie-cta__btn" href="<?php echo esc_url( $lily_contact_url ); ?>"><?php esc_html_e( 'Contact Us', 'lily' ); ?> <span aria-hidden="true">&rarr;</span></a>
		<?php lily_container_close(); ?>
	</section>
</main>

<?php
get_footer();
