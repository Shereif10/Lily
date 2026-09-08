<?php
/**
 * Lily Contact page dashboard fields.
 *
 * Reuses the existing Lily settings system (same option, same save flow,
 * same admin helpers as homepage/about/faq): the contact_* keys live inside
 * `lily_homepage_settings` and render as a "Contact Page" tab on the
 * existing Lily admin page. Empty values fall back to the approved
 * English copy in page-contact.php; Arabic values win on Arabic requests.
 *
 * @package Lily
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Default Contact settings (empty English = use approved template fallback;
 * Arabic defaults hold the actual Arabic translations).
 *
 * @return array
 */
function lily_contact_settings_defaults() {
	return array(
		'contact_eyebrow'          => '',
		'contact_eyebrow_ar'       => 'احنا هنا عشانك',
		'contact_title'            => '',
		'contact_title_ar'         => 'تواصلي معنا',
		'contact_subtitle'         => '',
		'contact_subtitle_ar'      => 'احنا هنا لمساعدتك في العدسات أو الطلبات أو أي حاجة تحتاجيها.',
		'contact_info_title'       => '',
		'contact_info_title_ar'    => 'تواصلي معنا',
		'contact_info_copy'        => '',
		'contact_info_copy_ar'     => 'فريقنا جاهز يساعدك في أي سؤال عن العدسات أو الطلبات أو العناية بالعين. وبنحاول نرد عليكِ في أسرع وقت.',
		'contact_form_title'       => '',
		'contact_form_title_ar'    => 'ابعتيلنا رسالة',
		'contact_success'          => '',
		'contact_success_ar'       => 'شكراً لكِ! وصلتنا رسالتك وفريقنا هيرد عليكِ في أقرب وقت.',
		'contact_error'            => '',
		'contact_error_ar'         => 'حصلت مشكلة — راجعي بياناتك وحاولي مرة تانية.',
		'contact_cta_eyebrow'      => '',
		'contact_cta_eyebrow_ar'   => 'محتاجة مساعدة في اختيار عدساتك؟',
		'contact_cta_lead'         => '',
		'contact_cta_lead_ar'      => 'خلينا نساعدك تلاقي العدسة المناسبة لإطلالتك.',
		'contact_cta_button'       => '',
		'contact_cta_button_ar'    => 'اعثري على عدساتك',
	);
}

/**
 * Sanitize Contact settings.
 *
 * @param array $raw Raw submitted values.
 * @return array
 */
function lily_sanitize_contact_settings( array $raw ) {
	$data = array();

	$text_fields = array(
		'contact_eyebrow',
		'contact_eyebrow_ar',
		'contact_title',
		'contact_title_ar',
		'contact_info_title',
		'contact_info_title_ar',
		'contact_form_title',
		'contact_form_title_ar',
		'contact_cta_eyebrow',
		'contact_cta_eyebrow_ar',
		'contact_cta_button',
		'contact_cta_button_ar',
	);

	foreach ( $text_fields as $field ) {
		$data[ $field ] = isset( $raw[ $field ] ) ? sanitize_text_field( $raw[ $field ] ) : '';
	}

	$textarea_fields = array(
		'contact_subtitle',
		'contact_subtitle_ar',
		'contact_info_copy',
		'contact_info_copy_ar',
		'contact_success',
		'contact_success_ar',
		'contact_error',
		'contact_error_ar',
		'contact_cta_lead',
		'contact_cta_lead_ar',
	);

	foreach ( $textarea_fields as $field ) {
		$data[ $field ] = isset( $raw[ $field ] ) ? sanitize_textarea_field( $raw[ $field ] ) : '';
	}

	return wp_parse_args( $data, lily_contact_settings_defaults() );
}

/**
 * Render the Contact Page tab on the existing Lily admin page.
 *
 * Layout follows the required bilingual pattern:
 * ENGLISH fields first, then a clearly separated العربية block.
 *
 * @param array $settings Current settings.
 */
function lily_render_contact_fields( $settings ) {
	echo '<section id="lily-tab-contact" class="lily-admin-panel"><h2>' . esc_html__( 'Contact Page', 'lily' ) . '</h2>';
	echo '<p class="description">' . esc_html__( 'Every field below is optional. Leave anything blank to keep the approved on-page content.', 'lily' ) . '</p>';

	echo '<h3>' . esc_html__( 'Contact Page — English', 'lily' ) . '</h3>';
	echo '<p class="description">' . esc_html__( 'Shown to English visitors.', 'lily' ) . '</p>';
	lily_admin_text_field( $settings, 'contact_eyebrow', esc_html__( 'Hero Eyebrow', 'lily' ) );
	lily_admin_text_field( $settings, 'contact_title', esc_html__( 'Page Title', 'lily' ) );
	lily_admin_textarea_field( $settings, 'contact_subtitle', esc_html__( 'Hero Subtitle', 'lily' ) );
	lily_admin_text_field( $settings, 'contact_info_title', esc_html__( 'Info Column Title', 'lily' ) );
	lily_admin_textarea_field( $settings, 'contact_info_copy', esc_html__( 'Info Column Copy', 'lily' ) );
	lily_admin_text_field( $settings, 'contact_form_title', esc_html__( 'Form Title', 'lily' ) );
	lily_admin_textarea_field( $settings, 'contact_success', esc_html__( 'Success Notice', 'lily' ) );
	lily_admin_textarea_field( $settings, 'contact_error', esc_html__( 'Error Notice', 'lily' ) );
	lily_admin_text_field( $settings, 'contact_cta_eyebrow', esc_html__( 'CTA Eyebrow', 'lily' ) );
	lily_admin_textarea_field( $settings, 'contact_cta_lead', esc_html__( 'CTA Lead', 'lily' ) );
	lily_admin_text_field( $settings, 'contact_cta_button', esc_html__( 'CTA Button', 'lily' ) );

	echo '<h3>' . esc_html__( 'Contact Page', 'lily' ) . ' — ' . esc_html__( 'العربية', 'lily' ) . '</h3>';
	echo '<p class="description">' . esc_html__( 'Shown on Arabic pages only. Leave any field blank to reuse its English text.', 'lily' ) . '</p>';

	$ar_fields = array(
		'contact_eyebrow'    => array( esc_html__( 'Hero Eyebrow', 'lily' ), 'text' ),
		'contact_title'      => array( esc_html__( 'Page Title', 'lily' ), 'text' ),
		'contact_subtitle'   => array( esc_html__( 'Hero Subtitle', 'lily' ), 'textarea' ),
		'contact_info_title' => array( esc_html__( 'Info Column Title', 'lily' ), 'text' ),
		'contact_info_copy'  => array( esc_html__( 'Info Column Copy', 'lily' ), 'textarea' ),
		'contact_form_title' => array( esc_html__( 'Form Title', 'lily' ), 'text' ),
		'contact_success'    => array( esc_html__( 'Success Notice', 'lily' ), 'textarea' ),
		'contact_error'      => array( esc_html__( 'Error Notice', 'lily' ), 'textarea' ),
		'contact_cta_eyebrow' => array( esc_html__( 'CTA Eyebrow', 'lily' ), 'text' ),
		'contact_cta_lead'   => array( esc_html__( 'CTA Lead', 'lily' ), 'textarea' ),
		'contact_cta_button' => array( esc_html__( 'CTA Button', 'lily' ), 'text' ),
	);

	foreach ( $ar_fields as $name => $field ) {
		$label = sprintf( __( '%s (Arabic)', 'lily' ), $field[0] );

		if ( 'textarea' === $field[1] ) {
			lily_admin_textarea_field( $settings, $name . '_ar', $label );
		} else {
			lily_admin_text_field( $settings, $name . '_ar', $label );
		}
	}

	echo '</section>';
}
