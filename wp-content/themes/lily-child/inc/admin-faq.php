<?php
/**
 * Lily FAQ page dashboard fields.
 *
 * Reuses the existing Lily settings system (same option, same save flow,
 * same admin helpers as the homepage/about settings): the faq_* keys live
 * inside `lily_homepage_settings` and render as an "FAQ Page" tab on the
 * existing Lily admin page. Empty values fall back to the approved
 * English FAQ content in page-faqs.php.
 *
 * @package Lily
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Default FAQ settings (empty = use the approved template fallback).
 *
 * @return array
 */
function lily_faq_settings_defaults() {
	$defaults = array(
		'faq_title'       => '',
		'faq_title_ar'    => 'الأسئلة الشائعة',
		'faq_subtitle'    => '',
		'faq_subtitle_ar' => 'الأسئلة الأكثر شيوعاً عن العدسات اللاصقة الملونة — اختيار اللون والعناية والاستخدام اليومي.',
	);

	$faq_ar = array(
		1 => array(
			'q_ar' => 'إزاي أختار لون العدسة المناسب لبشرتي ولون عيني؟',
			'a_ar' => 'البشرة الدافئة أو القمحية يناسبها العسلي والأخضر الزيتي والبني الدافئ. والبشرة الفاتحة يناسبها الرمادي والأزرق والأخضر الزمردي. ولو لون عينك الطبيعي غامق، اختاري ألوان بتغطية أقوى عشان يبان اللون بوضوح.',
		),
		2 => array(
			'q_ar' => 'إيه الفرق بين العدسات المحددة والعدسات بدون تحديد؟',
			'a_ar' => 'العدسات المحددة فيها إطار غامق حوالين الحرف بيكبر العين ويبرزها، بينما العدسات بدون تحديد بتدي لوك طبيعي مندمج مع لون عينك الأصلي من غير ما يغير شكل حدقة العين.',
		),
		3 => array(
			'q_ar' => 'إزاي أعتني بالعدسات وأحميها من التلف؟',
			'a_ar' => 'اغسلي إيدك ونشفيها كويس قبل ما تلمسي العدسات. استخدمي محلول عدسات معقم مخصص للتنظيف اليومي، ومتغسليش العدسات ولا العلبة بمية الحنفية أبداً. ويُفضل تغيري علبة العدسات كل شهر إلى ٣ شهور.',
		),
		4 => array(
			'q_ar' => 'كام ساعة أقدر ألبس العدسات في اليوم؟',
			'a_ar' => 'يُفضل تلبسي العدسات من ٦ إلى ٨ ساعات في اليوم عشان تتجنبي إجهاد أو جفاف العين. ولازم تخلعيها فوراً قبل النوم أو السباحة.',
		),
		5 => array(
			'q_ar' => 'لو حسيت بحرقان أو شكة وأنا لابسة العدسة أعمل إيه؟',
			'a_ar' => 'اخلعي العدسة فوراً وافحصيها للتأكد إن مفيهاش شوائب أو شروخ. نضفيها كويس بالمحلول وتأكدي إنها مش مقلوبة قبل ما تلبسيها تاني. ولو التهيج استمر، ريحي عينك واستشيري طبيب.',
		),
		6 => array(
			'q_ar' => 'إزاي أعرف إن العدسة مقلوبة ولا معدولة؟',
			'a_ar' => 'حطي العدسة على طرف صباعك؛ لو أطرافها لفوق على شكل كوب أو حرف U تبقى معدولة. ولو الأطراف مفرودة لبره زي الطبق تبقى مقلوبة ولازم تعدليها.',
		),
		7 => array(
			'q_ar' => 'هل ينفع أنام وأنا لابسة العدسات؟',
			'a_ar' => 'النوم بالعدسات غير مُنصح به، لأنه بيقلل الأكسجين اللي واصل للقرنية وممكن يسبب جفاف شديد أو التهابات.',
		),
	);

	for ( $i = 1; $i <= 7; $i++ ) {
		$defaults[ "faq_{$i}_q" ]    = '';
		$defaults[ "faq_{$i}_q_ar" ] = $faq_ar[ $i ]['q_ar'];
		$defaults[ "faq_{$i}_a" ]    = '';
		$defaults[ "faq_{$i}_a_ar" ] = $faq_ar[ $i ]['a_ar'];
	}

	return $defaults;
}

/**
 * Sanitize FAQ settings.
 *
 * @param array $raw Raw submitted values.
 * @return array
 */
function lily_sanitize_faq_settings( array $raw ) {
	$data = array();

	$data['faq_title']       = isset( $raw['faq_title'] ) ? sanitize_text_field( $raw['faq_title'] ) : '';
	$data['faq_title_ar']    = isset( $raw['faq_title_ar'] ) ? sanitize_text_field( $raw['faq_title_ar'] ) : '';
	$data['faq_subtitle']    = isset( $raw['faq_subtitle'] ) ? sanitize_textarea_field( $raw['faq_subtitle'] ) : '';
	$data['faq_subtitle_ar'] = isset( $raw['faq_subtitle_ar'] ) ? sanitize_textarea_field( $raw['faq_subtitle_ar'] ) : '';

	for ( $i = 1; $i <= 7; $i++ ) {
		$data[ "faq_{$i}_q" ]    = isset( $raw[ "faq_{$i}_q" ] ) ? sanitize_text_field( $raw[ "faq_{$i}_q" ] ) : '';
		$data[ "faq_{$i}_q_ar" ] = isset( $raw[ "faq_{$i}_q_ar" ] ) ? sanitize_text_field( $raw[ "faq_{$i}_q_ar" ] ) : '';
		$data[ "faq_{$i}_a" ]    = isset( $raw[ "faq_{$i}_a" ] ) ? sanitize_textarea_field( $raw[ "faq_{$i}_a" ] ) : '';
		$data[ "faq_{$i}_a_ar" ] = isset( $raw[ "faq_{$i}_a_ar" ] ) ? sanitize_textarea_field( $raw[ "faq_{$i}_a_ar" ] ) : '';
	}

	return wp_parse_args( $data, lily_faq_settings_defaults() );
}

/**
 * Render the FAQ Page tab panels on the existing Lily admin page.
 *
 * @param array $settings Current settings.
 */
function lily_render_faq_fields( $settings ) {
	echo '<section id="lily-tab-faq" class="lily-admin-panel"><h2>' . esc_html__( 'FAQ Page', 'lily' ) . '</h2>';
	echo '<p class="description">' . esc_html__( 'Every field below is optional. Leave anything blank to keep the approved FAQ content.', 'lily' ) . '</p>';

	echo '<h3>' . esc_html__( 'FAQ Page', 'lily' ) . '</h3>';
	lily_admin_text_field( $settings, 'faq_title', esc_html__( 'Page Title', 'lily' ) );
	lily_admin_textarea_field( $settings, 'faq_subtitle', esc_html__( 'Page Subtitle', 'lily' ) );

	echo '<h3>' . esc_html__( 'FAQ Items', 'lily' ) . '</h3>';
	for ( $i = 1; $i <= 7; $i++ ) {
		echo '<fieldset class="lily-link-field"><legend>' . esc_html( sprintf( __( 'FAQ %d', 'lily' ), $i ) ) . '</legend>';
		printf( '<input type="text" name="lily_homepage[faq_%1$d_q]" value="%2$s" placeholder="%3$s">', absint( $i ), esc_attr( $settings[ "faq_{$i}_q" ] ?? '' ), esc_attr__( 'Question', 'lily' ) );
		printf( '<textarea name="lily_homepage[faq_%1$d_a]" rows="3" placeholder="%2$s">%3$s</textarea>', absint( $i ), esc_attr__( 'Answer', 'lily' ), esc_textarea( $settings[ "faq_{$i}_a" ] ?? '' ) );
		echo '</fieldset>';
	}

	if ( function_exists( 'lily_render_ar_fields' ) ) {
		lily_render_ar_fields(
			$settings,
			esc_html__( 'FAQ Page', 'lily' ),
			array(
				'faq_title'    => array( esc_html__( 'Page Title', 'lily' ), 'text' ),
				'faq_subtitle' => array( esc_html__( 'Page Subtitle', 'lily' ), 'textarea' ),
			)
		);

		echo '<h3>' . esc_html__( 'FAQ Items', 'lily' ) . ' — ' . esc_html__( 'Arabic Content', 'lily' ) . '</h3>';
		echo '<p class="description">' . esc_html__( 'Shown on Arabic pages only. Leave any field blank to reuse its English text.', 'lily' ) . '</p>';
		for ( $i = 1; $i <= 7; $i++ ) {
			echo '<fieldset class="lily-link-field"><legend>' . esc_html( sprintf( __( 'FAQ %d (Arabic)', 'lily' ), $i ) ) . '</legend>';
			printf( '<input type="text" name="lily_homepage[faq_%1$d_q_ar]" value="%2$s" placeholder="%3$s">', absint( $i ), esc_attr( $settings[ "faq_{$i}_q_ar" ] ?? '' ), esc_attr__( 'Question (Arabic)', 'lily' ) );
			printf( '<textarea name="lily_homepage[faq_%1$d_a_ar]" rows="3" placeholder="%2$s">%3$s</textarea>', absint( $i ), esc_attr__( 'Answer (Arabic)', 'lily' ), esc_textarea( $settings[ "faq_{$i}_a_ar" ] ?? '' ) );
			echo '</fieldset>';
		}
	}

	echo '</section>';
}
