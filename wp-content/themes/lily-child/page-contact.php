<?php
/**
 * Lily Contact Us page (slug: contact).
 *
 * Contact-only template: centered hero, two-column contact info + form,
 * and a soft lens-finder CTA band. Uses the existing global
 * header/footer, the shared lily-container helper, the native
 * WooCommerce Shop URL, and the existing Lily contact-form handler
 * (same field names, nonce action and honeypot as inc/content-pages.php).
 *
 * @package Lily
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* Contact-only body class so the Contact CSS scope applies without touching globals. */
add_filter(
	'body_class',
	static function ( $classes ) {
		$classes[] = 'lily-contact-page';
		return $classes;
	}
);

get_header();

$lily_shop_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );

$lily_status = isset( $_GET['lily_contact'] ) ? sanitize_key( wp_unslash( $_GET['lily_contact'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- display-only status flag.
?>

<main id="primary" class="site-main lily-contact">
	<section class="lily-contact-band lily-contact-hero">
		<?php lily_container_open( 'lily-contact-hero__inner' ); ?>
			<p class="lily-contact-eyebrow"><?php echo esc_html( lily_get_option( 'contact_eyebrow', esc_html__( 'We are here for you', 'lily' ) ) ); ?></p>
			<h1 class="lily-contact-hero__title"><?php echo esc_html( lily_get_option( 'contact_title', esc_html__( 'Contact Us', 'lily' ) ) ); ?></h1>
			<span class="lily-contact-hero__rule" aria-hidden="true"></span>
			<p class="lily-contact-hero__sub"><?php echo esc_html( lily_get_option( 'contact_subtitle', esc_html__( 'We are here to help with your lenses, orders, or anything else you need.', 'lily' ) ) ); ?></p>
		<?php lily_container_close(); ?>
	</section>

	<section class="lily-contact-band lily-contact-main" aria-label="<?php esc_attr_e( 'Contact information and form', 'lily' ); ?>">
		<?php lily_container_open( 'lily-contact-main__inner' ); ?>
			<?php if ( 'sent' === $lily_status ) : ?>
				<p class="lily-contact-notice lily-contact-notice--sent" role="status"><?php echo esc_html( lily_get_option( 'contact_success', esc_html__( 'Thank you! Your message has been sent. Our team will get back to you as soon as possible.', 'lily' ) ) ); ?></p>
			<?php elseif ( 'error' === $lily_status ) : ?>
				<p class="lily-contact-notice lily-contact-notice--error" role="alert"><?php echo esc_html( lily_get_option( 'contact_error', esc_html__( 'Something went wrong — please check your details and try again.', 'lily' ) ) ); ?></p>
			<?php endif; ?>

			<div class="lily-contact-grid">
				<div class="lily-contact-info">
					<h2 class="lily-contact-info__title"><?php echo esc_html( lily_get_option( 'contact_info_title', esc_html__( 'Get in Touch', 'lily' ) ) ); ?></h2>
					<ul class="lily-contact-info__list">
						<li>
							<span class="lily-contact-info__label"><?php esc_html_e( 'Phone', 'lily' ); ?></span>
							<a class="lily-contact-info__value" href="tel:+201234567890" dir="ltr">+20 123 456 7890</a>
						</li>
						<li>
							<span class="lily-contact-info__label"><?php esc_html_e( 'Email', 'lily' ); ?></span>
							<a class="lily-contact-info__value" href="mailto:hello@lily.com">hello@lily.com</a>
						</li>
						<li>
							<span class="lily-contact-info__label"><?php esc_html_e( 'WhatsApp', 'lily' ); ?></span>
							<a class="lily-contact-info__value" href="https://wa.me/201234567890" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Chat with us', 'lily' ); ?></a>
						</li>
					</ul>
					<p class="lily-contact-info__eyebrow"><?php echo esc_html( lily_get_option( 'contact_eyebrow', esc_html__( 'We are here for you', 'lily' ) ) ); ?></p>
					<p class="lily-contact-info__copy"><?php echo esc_html( lily_get_option( 'contact_info_copy', esc_html__( 'Our team is ready to help you with any questions about our lenses, orders, or eye care. We aim to respond as soon as possible.', 'lily' ) ) ); ?></p>
				</div>

				<div class="lily-contact-formwrap">
					<h2 class="lily-contact-formwrap__title"><?php echo esc_html( lily_get_option( 'contact_form_title', esc_html__( 'Send Us a Message', 'lily' ) ) ); ?></h2>
					<form class="lily-contact-form" method="post" action="">
						<input type="hidden" name="lily_contact_handler" value="1">
						<?php wp_nonce_field( 'lily_contact_form', 'lily_contact_nonce' ); ?>
						<p class="lily-contact-form__hp" aria-hidden="true"><label><?php esc_html_e( 'Leave this field empty', 'lily' ); ?><input type="text" name="lily_cf_hp" tabindex="-1" autocomplete="off"></label></p>
						<p class="lily-contact-form__row">
							<label for="lily_cf_name"><?php esc_html_e( 'Full Name', 'lily' ); ?></label>
							<input class="lily-contact-form__input" type="text" id="lily_cf_name" name="lily_cf_name" autocomplete="name" required>
						</p>
						<p class="lily-contact-form__row">
							<label for="lily_cf_phone"><?php esc_html_e( 'Phone Number', 'lily' ); ?></label>
							<input class="lily-contact-form__input" type="tel" id="lily_cf_phone" name="lily_cf_phone" autocomplete="tel" dir="ltr">
						</p>
						<p class="lily-contact-form__row">
							<label for="lily_cf_email"><?php esc_html_e( 'Email Address', 'lily' ); ?></label>
							<input class="lily-contact-form__input" type="email" id="lily_cf_email" name="lily_cf_email" autocomplete="email" required>
						</p>
						<p class="lily-contact-form__row">
							<label for="lily_cf_subject"><?php esc_html_e( 'Subject', 'lily' ); ?></label>
							<input class="lily-contact-form__input" type="text" id="lily_cf_subject" name="lily_cf_subject" autocomplete="off">
						</p>
						<p class="lily-contact-form__row">
							<label for="lily_cf_message"><?php esc_html_e( 'Message', 'lily' ); ?></label>
							<textarea class="lily-contact-form__input lily-contact-form__textarea" id="lily_cf_message" name="lily_cf_message" rows="5" required></textarea>
						</p>
						<p class="lily-contact-form__row lily-contact-form__row--submit">
							<button class="lily-button lily-contact-form__submit" type="submit"><?php esc_html_e( 'Send Message', 'lily' ); ?> <span aria-hidden="true">&rarr;</span></button>
						</p>
					</form>
				</div>
			</div>
		<?php lily_container_close(); ?>
	</section>

	<section class="lily-contact-band lily-contact-cta" aria-labelledby="lily-contact-cta-heading">
		<?php lily_container_open( 'lily-contact-cta__inner' ); ?>
			<p class="lily-contact-eyebrow lily-contact-eyebrow--center"><?php echo esc_html( lily_get_option( 'contact_cta_eyebrow', esc_html__( 'Need help finding your lenses?', 'lily' ) ) ); ?></p>
			<p class="lily-contact-cta__lead" id="lily-contact-cta-heading"><?php echo esc_html( lily_get_option( 'contact_cta_lead', esc_html__( 'Let us help you find the right pair for your look.', 'lily' ) ) ); ?></p>
			<a class="lily-button lily-contact-cta__btn" href="<?php echo esc_url( $lily_shop_url ); ?>"><?php echo esc_html( lily_get_option( 'contact_cta_button', esc_html__( 'Find Your Lenses', 'lily' ) ) ); ?> <span aria-hidden="true">&rarr;</span></a>
		<?php lily_container_close(); ?>
	</section>
</main>

<?php
get_footer();
