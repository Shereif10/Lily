<?php
/**
 * Lily About page dashboard fields.
 *
 * Reuses the existing Lily settings system (same option, same save flow,
 * same admin helpers and media picker as the homepage settings): the
 * about_* keys live inside `lily_homepage_settings` and are rendered as
 * an "About Page" tab on the existing Lily admin page. Empty values fall
 * back to the approved on-page content in page-about.php.
 *
 * @package Lily
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Default About settings (empty = use the approved template fallback).
 *
 * @return array
 */
function lily_about_settings_defaults() {
	return array(
		'about_title'       => '',
		'about_title_ar'    => 'عن ليلي',
		'about_subtitle'    => '',
		'about_subtitle_ar' => 'جمال طبيعي يحسسك إنكِ على طبيعتك.',
		'about_image'       => 0,
		'story_eyebrow'     => '',
		'story_eyebrow_ar'  => 'حكايتنا',
		'story_lead'        => '',
		'story_lead_ar'     => 'اتعملت ليلي لفكرة بسيطة — إن تغيير إطلالتك مش معناه تغيري نفسك.',
		'story_p1'          => '',
		'story_p1_ar'       => 'اختيار العدسة الملونة ممكن يكون محير، عشان كده بنخلي كل حاجة هادية وواضحة: درجات صادقة، ونتائج طبيعية، ونصايح تحسي إنها معمولة لكِ.',
		'story_p2'          => '',
		'story_p2_ar'       => 'كل مجموعة مختارة بعناية، عشان تقارني وتستكشفي وتلاقي اللون اللي شبهك.',
		'story_image'       => 0,
		'mission_eyebrow'   => '',
		'mission_eyebrow_ar' => 'مهمتنا',
		'mission_statement' => '',
		'mission_statement_ar' => 'نخلي التعبير عن نفسك سهل بألوان تكمل صورتك عن نفسك.',
		'mission_text'      => '',
		'mission_text_ar'   => '',
		'vision_eyebrow'    => '',
		'vision_eyebrow_ar' => 'رؤيتنا',
		'vision_statement'  => '',
		'vision_statement_ar' => 'عالم يكون فيه الجمال شخصي وطبيعي وبتاعك بالكامل.',
		'vision_text'       => '',
		'vision_text_ar'    => 'عايزين ليلي تكون المكان اللي ترجعيله كل ما تحبي تجددي — وجهة هادية وموثوقة لاكتشاف إطلالتك الجاية.',
		'approach_eyebrow'  => '',
		'approach_eyebrow_ar' => 'أسلوبنا',
		'approach_1_num'    => '',
		'approach_1_name'   => '',
		'approach_1_name_ar' => 'طبيعي',
		'approach_1_desc'   => '',
		'approach_1_desc_ar' => 'مصممة لتكمل جمالك مش لتغطي عليه.',
		'approach_2_num'    => '',
		'approach_2_name'   => '',
		'approach_2_name_ar' => 'ثقة',
		'approach_2_desc'   => '',
		'approach_2_desc_ar' => 'تغيير بسيط ممكن يغير إحساسك بنفسك.',
		'approach_3_num'    => '',
		'approach_3_name'   => '',
		'approach_3_name_ar' => 'جودة',
		'approach_3_desc'   => '',
		'approach_3_desc_ar' => 'معمولة بعناية لراحة وجمال كل يوم.',
		'cta_heading'       => '',
		'cta_heading_ar'    => 'اعثري على عدستك المثالية',
		'cta_text'          => '',
		'cta_text_ar'       => 'اكتشفي اللون اللي شبهك.',
		'cta_button'        => '',
		'cta_button_ar'     => 'تسوقي العدسات',
		'cta_url'           => '',
	);
}

/**
 * Sanitize About settings.
 *
 * @param array $raw Raw submitted values.
 * @return array
 */
function lily_sanitize_about_settings( array $raw ) {
	$data = array();

	$text_fields = array(
		'about_title',
		'about_title_ar',
		'about_subtitle',
		'about_subtitle_ar',
		'story_eyebrow',
		'story_eyebrow_ar',
		'mission_eyebrow',
		'mission_eyebrow_ar',
		'vision_eyebrow',
		'vision_eyebrow_ar',
		'approach_eyebrow',
		'approach_eyebrow_ar',
		'approach_1_num',
		'approach_1_name',
		'approach_1_name_ar',
		'approach_2_num',
		'approach_2_name',
		'approach_2_name_ar',
		'approach_3_num',
		'approach_3_name',
		'approach_3_name_ar',
		'cta_heading',
		'cta_heading_ar',
		'cta_button',
		'cta_button_ar',
	);

	foreach ( $text_fields as $field ) {
		$data[ $field ] = isset( $raw[ $field ] ) ? sanitize_text_field( $raw[ $field ] ) : '';
	}

	$textarea_fields = array(
		'story_lead',
		'story_lead_ar',
		'story_p1',
		'story_p1_ar',
		'story_p2',
		'story_p2_ar',
		'mission_statement',
		'mission_statement_ar',
		'mission_text',
		'mission_text_ar',
		'vision_statement',
		'vision_statement_ar',
		'vision_text',
		'vision_text_ar',
		'approach_1_desc',
		'approach_1_desc_ar',
		'approach_2_desc',
		'approach_2_desc_ar',
		'approach_3_desc',
		'approach_3_desc_ar',
		'cta_text',
		'cta_text_ar',
	);

	foreach ( $textarea_fields as $field ) {
		$data[ $field ] = isset( $raw[ $field ] ) ? sanitize_textarea_field( $raw[ $field ] ) : '';
	}

	$data['about_image'] = absint( $raw['about_image'] ?? 0 );
	$data['story_image'] = absint( $raw['story_image'] ?? 0 );
	$data['cta_url']     = isset( $raw['cta_url'] ) ? esc_url_raw( $raw['cta_url'] ) : '';

	return wp_parse_args( $data, lily_about_settings_defaults() );
}

/**
 * Render the About Page tab panels on the existing Lily admin page.
 *
 * @param array $settings Current settings.
 */
function lily_render_about_fields( $settings ) {
	echo '<section id="lily-tab-about" class="lily-admin-panel"><h2>' . esc_html__( 'About Page', 'lily' ) . '</h2>';
	echo '<p class="description">' . esc_html__( 'Every field below is optional. Leave anything blank to keep the approved on-page content.', 'lily' ) . '</p>';

	echo '<h3>' . esc_html__( 'About Lily', 'lily' ) . '</h3>';
	lily_admin_text_field( $settings, 'about_title', esc_html__( 'Page Title', 'lily' ) );
	lily_admin_text_field( $settings, 'about_subtitle', esc_html__( 'Subtitle', 'lily' ) );
	lily_admin_image_field( $settings, 'about_image', esc_html__( 'Hero Image', 'lily' ), 1920, 600 );

	echo '<h3>' . esc_html__( 'Our Story', 'lily' ) . '</h3>';
	lily_admin_text_field( $settings, 'story_eyebrow', esc_html__( 'Eyebrow', 'lily' ) );
	lily_admin_textarea_field( $settings, 'story_lead', esc_html__( 'Lead Statement', 'lily' ) );
	lily_admin_textarea_field( $settings, 'story_p1', esc_html__( 'Paragraph 1', 'lily' ) );
	lily_admin_textarea_field( $settings, 'story_p2', esc_html__( 'Paragraph 2', 'lily' ) );
	lily_admin_image_field( $settings, 'story_image', esc_html__( 'Story Image', 'lily' ), 800, 1000 );

	echo '<h3>' . esc_html__( 'Our Mission', 'lily' ) . '</h3>';
	lily_admin_text_field( $settings, 'mission_eyebrow', esc_html__( 'Eyebrow', 'lily' ) );
	lily_admin_textarea_field( $settings, 'mission_statement', esc_html__( 'Main Statement', 'lily' ) );
	lily_admin_textarea_field( $settings, 'mission_text', esc_html__( 'Supporting Text (optional)', 'lily' ) );

	echo '<h3>' . esc_html__( 'Our Vision', 'lily' ) . '</h3>';
	lily_admin_text_field( $settings, 'vision_eyebrow', esc_html__( 'Eyebrow', 'lily' ) );
	lily_admin_textarea_field( $settings, 'vision_statement', esc_html__( 'Main Statement', 'lily' ) );
	lily_admin_textarea_field( $settings, 'vision_text', esc_html__( 'Supporting Text', 'lily' ) );

	echo '<h3>' . esc_html__( 'Our Approach', 'lily' ) . '</h3>';
	lily_admin_text_field( $settings, 'approach_eyebrow', esc_html__( 'Section Title', 'lily' ) );
	foreach ( array( 1, 2, 3 ) as $i ) {
		echo '<fieldset class="lily-link-field"><legend>' . esc_html( sprintf( __( 'Principle %d', 'lily' ), $i ) ) . '</legend>';
		printf( '<input type="text" name="lily_homepage[approach_%1$d_num]" value="%2$s" placeholder="%3$s" style="max-width:90px">', absint( $i ), esc_attr( $settings[ "approach_{$i}_num" ] ?? '' ), esc_attr__( 'Number', 'lily' ) );
		printf( '<input type="text" name="lily_homepage[approach_%1$d_name]" value="%2$s" placeholder="%3$s">', absint( $i ), esc_attr( $settings[ "approach_{$i}_name" ] ?? '' ), esc_attr__( 'Name', 'lily' ) );
		printf( '<input type="text" name="lily_homepage[approach_%1$d_desc]" value="%2$s" placeholder="%3$s">', absint( $i ), esc_attr( $settings[ "approach_{$i}_desc" ] ?? '' ), esc_attr__( 'Description', 'lily' ) );
		echo '</fieldset>';
	}

	echo '<h3>' . esc_html__( 'Find Your Perfect Lens', 'lily' ) . '</h3>';
	lily_admin_text_field( $settings, 'cta_heading', esc_html__( 'Heading', 'lily' ) );
	lily_admin_textarea_field( $settings, 'cta_text', esc_html__( 'Supporting Text', 'lily' ) );
	lily_admin_text_field( $settings, 'cta_button', esc_html__( 'CTA Button Text', 'lily' ) );
	lily_admin_text_field( $settings, 'cta_url', esc_html__( 'CTA URL (blank = Shop page)', 'lily' ) );

	if ( function_exists( 'lily_render_ar_fields' ) ) {
		lily_render_ar_fields(
			$settings,
			esc_html__( 'About Lily', 'lily' ),
			array(
				'about_title'    => array( esc_html__( 'Page Title', 'lily' ), 'text' ),
				'about_subtitle' => array( esc_html__( 'Subtitle', 'lily' ), 'text' ),
			)
		);
		lily_render_ar_fields(
			$settings,
			esc_html__( 'Our Story', 'lily' ),
			array(
				'story_eyebrow' => array( esc_html__( 'Eyebrow', 'lily' ), 'text' ),
				'story_lead'    => array( esc_html__( 'Lead Statement', 'lily' ), 'textarea' ),
				'story_p1'      => array( esc_html__( 'Paragraph 1', 'lily' ), 'textarea' ),
				'story_p2'      => array( esc_html__( 'Paragraph 2', 'lily' ), 'textarea' ),
			)
		);
		lily_render_ar_fields(
			$settings,
			esc_html__( 'Our Mission', 'lily' ),
			array(
				'mission_eyebrow'   => array( esc_html__( 'Eyebrow', 'lily' ), 'text' ),
				'mission_statement' => array( esc_html__( 'Main Statement', 'lily' ), 'textarea' ),
				'mission_text'      => array( esc_html__( 'Supporting Text (optional)', 'lily' ), 'textarea' ),
			)
		);
		lily_render_ar_fields(
			$settings,
			esc_html__( 'Our Vision', 'lily' ),
			array(
				'vision_eyebrow'   => array( esc_html__( 'Eyebrow', 'lily' ), 'text' ),
				'vision_statement' => array( esc_html__( 'Main Statement', 'lily' ), 'textarea' ),
				'vision_text'      => array( esc_html__( 'Supporting Text', 'lily' ), 'textarea' ),
			)
		);
		lily_render_ar_fields(
			$settings,
			esc_html__( 'Our Approach', 'lily' ),
			array(
				'approach_eyebrow'  => array( esc_html__( 'Section Title', 'lily' ), 'text' ),
				'approach_1_name'   => array( esc_html__( 'Principle 1 — Name', 'lily' ), 'text' ),
				'approach_1_desc'   => array( esc_html__( 'Principle 1 — Description', 'lily' ), 'textarea' ),
				'approach_2_name'   => array( esc_html__( 'Principle 2 — Name', 'lily' ), 'text' ),
				'approach_2_desc'   => array( esc_html__( 'Principle 2 — Description', 'lily' ), 'textarea' ),
				'approach_3_name'   => array( esc_html__( 'Principle 3 — Name', 'lily' ), 'text' ),
				'approach_3_desc'   => array( esc_html__( 'Principle 3 — Description', 'lily' ), 'textarea' ),
			)
		);
		lily_render_ar_fields(
			$settings,
			esc_html__( 'Find Your Perfect Lens', 'lily' ),
			array(
				'cta_heading' => array( esc_html__( 'Heading', 'lily' ), 'text' ),
				'cta_text'    => array( esc_html__( 'Supporting Text', 'lily' ), 'textarea' ),
				'cta_button'  => array( esc_html__( 'CTA Button Text', 'lily' ), 'text' ),
			)
		);
	}

	echo '</section>';
}
