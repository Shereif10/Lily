<?php
/**
 * Native Homepage Settings admin page.
 *
 * @package Lily
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register Lily admin pages.
 *
 * The top-level "Lily" menu is the simplified Pages hub; the original
 * full-section editor remains available as "Homepage Settings (Advanced)".
 */
function lily_register_homepage_settings_page() {
	add_menu_page(
		esc_html__( 'Lily', 'lily' ),
		esc_html__( 'Lily', 'lily' ),
		'manage_options',
		'lily',
		'lily_render_lily_page',
		'dashicons-admin-home',
		58
	);

	add_submenu_page(
		'lily',
		esc_html__( 'Pages', 'lily' ),
		esc_html__( 'Pages', 'lily' ),
		'manage_options',
		'lily-pages',
		'lily_render_lily_page'
	);

	add_submenu_page(
		'lily',
		esc_html__( 'Homepage Settings (Advanced)', 'lily' ),
		esc_html__( 'Homepage Settings (Advanced)', 'lily' ),
		'manage_options',
		'lily-homepage',
		'lily_render_homepage_settings_page'
	);
}
add_action( 'admin_menu', 'lily_register_homepage_settings_page' );

/**
 * Enqueue admin media helpers.
 *
 * @param string $hook Current admin page hook.
 */
function lily_admin_assets( $hook ) {
	$lily_allowed = 'toplevel_page_lily' === $hook || 0 === strpos( $hook, 'lily_page_' );
	$is_product_edit = in_array( $hook, array( 'post.php', 'post-new.php' ), true )
		&& ( ( isset( $_GET['post'] ) && 'product' === get_post_type( absint( $_GET['post'] ) ) ) // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- display routing only.
			|| ( isset( $_GET['post_type'] ) && 'product' === sanitize_key( wp_unslash( $_GET['post_type'] ) ) ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( ! $lily_allowed && ! $is_product_edit && false === strpos( $hook, 'edit-tags.php' ) && false === strpos( $hook, 'term.php' ) ) {
		return;
	}

	wp_enqueue_media();
	wp_enqueue_script( 'jquery-ui-sortable' );
	wp_enqueue_script( 'lily-admin', LILY_THEME_URI . '/assets/js/admin.js', array( 'jquery', 'jquery-ui-sortable' ), LILY_THEME_VERSION, true );
	wp_localize_script(
		'lily-admin',
		'lilyAdminI18n',
		array(
			'chooseImage' => esc_html__( 'Choose Image', 'lily' ),
			'useImage'    => esc_html__( 'Use Image', 'lily' ),
			'productImageGuidance' => esc_html__( 'Recommended size: 1000 × 1000 px (square — same source for the main image and every gallery image)', 'lily' ),
		)
	);
	wp_enqueue_style( 'lily-admin', LILY_THEME_URI . '/assets/css/admin.css', array(), LILY_THEME_VERSION );
}
add_action( 'admin_enqueue_scripts', 'lily_admin_assets' );

/**
 * Default homepage settings.
 *
 * @return array
 */
function lily_homepage_settings_defaults() {
	return array_merge(
		array(
		'show_announcement_bar'              => 1,
		'announcement_items'                 => array(
			array(
				'text'    => 'Fast Delivery',
				'text_ar' => 'توصيل سريع',
				'link'    => array( 'title' => '', 'url' => '', 'target' => '_self' ),
			),
			array(
				'text'    => 'Cash on Delivery',
				'text_ar' => 'الدفع عند الاستلام',
				'link'    => array( 'title' => '', 'url' => '', 'target' => '_self' ),
			),
			array(
				'text'    => 'Premium Quality',
				'text_ar' => 'جودة فاخرة',
				'link'    => array( 'title' => '', 'url' => '', 'target' => '_self' ),
			),
		),
		'show_hero'                          => 1,
		'hero_slides'                        => array(
			array(
				'enabled'        => 1,
				'image'          => 0,
				'mobile_image'   => 0,
				'title'          => 'Colored Lenses',
				'title_ar'       => 'عدسات ملونة',
				'description'    => '',
				'description_ar' => '',
				'cta_text'       => 'Shop Now',
				'cta_text_ar'    => 'تسوقي الآن',
				'cta_url'        => '',
			),
			array(
				'enabled'        => 1,
				'image'          => 0,
				'mobile_image'   => 0,
				'title'          => 'Clear Lenses',
				'title_ar'       => 'عدسات شفافة',
				'description'    => '',
				'description_ar' => '',
				'cta_text'       => 'Shop Now',
				'cta_text_ar'    => 'تسوقي الآن',
				'cta_url'        => '',
			),
			array(
				'enabled'        => 1,
				'image'          => 0,
				'mobile_image'   => 0,
				'title'          => 'Accessories & Lens Care',
				'title_ar'       => 'إكسسوارات والعناية بالعدسات',
				'description'    => '',
				'description_ar' => '',
				'cta_text'       => 'Shop Now',
				'cta_text_ar'    => 'تسوقي الآن',
				'cta_url'        => '',
			),
		),
		'show_brands'                        => 1,
		'brands_heading'                     => '',
		'brands_heading_ar'                  => '',
		'brands'                             => array(),
		'show_shop_by_collections'           => 1,
		'collections_heading'                => '',
		'collections_heading_ar'             => 'اعثري على العدسة المناسبة لكِ',
		'collections_description'            => '',
		'collections_description_ar'         => 'اكتشفي مجموعاتنا واختاري الستايل اللي يعبر عنكِ.',
		'collections_to_display'             => array(),
		'show_shop_by_colors'                => 1,
		'colors_heading'                     => '',
		'colors_heading_ar'                  => 'اعثري على اللون اللي يعبر عنكِ',
		'colors_description'                 => '',
		'colors_description_ar'              => 'اكتشفي درجات مصممة لتناسب إطلالتك المميزة.',
		'colors_to_display'                  => array(),
		'view_all_colors_link'               => array( 'title' => 'View All Colors', 'title_ar' => 'عرض كل الألوان', 'url' => '', 'target' => '_self' ),
		'show_best_sellers'                  => 1,
		'best_sellers_heading'               => '',
		'best_sellers_heading_ar'            => 'الدرجات اللي عميلاتنا حبوها أكتر',
		'best_sellers_description'           => '',
		'best_sellers_description_ar'        => 'اكتشفي الألوان الأكثر مبيعاً اللي تناسب كل إطلالة ومزاج.',
		'best_sellers_products'              => array(),
		'show_lens_finder'                   => 1,
		'lens_finder_heading'                => '',
		'lens_finder_heading_ar'             => 'اعثري على عدساتك المثالية',
		'lens_finder_eyebrow'                => '',
		'lens_finder_eyebrow_ar'             => 'دليل اختيار العدسات',
		'lens_finder_description'            => '',
		'lens_finder_description_ar'         => 'جاوبي على كام سؤال بسيط وهنرشح لكِ العدسات الأنسب لكِ.',
		'lens_finder_start_text'             => esc_html__( 'Start Lens Finder', 'lily' ),
		'lens_finder_start_text_ar'          => 'ابدئي دليل العدسات',
		'lens_finder_image'                  => 0,
		'lens_finder_image_alt'              => '',
		'lens_finder_image_alt_ar'           => '',
		'lens_finder_steps'                  => array(
			array( 'number' => '01', 'title' => __( 'Tell us about you', 'lily' ), 'title_ar' => 'احكيلنا عنكِ', 'description' => __( 'Choose your preferences and lens needs.', 'lily' ), 'description_ar' => 'اختاري تفضيلاتك واحتياجاتك من العدسات.' ),
			array( 'number' => '02', 'title' => __( 'Find your match', 'lily' ), 'title_ar' => 'اعثري على المناسب لكِ', 'description' => __( 'We narrow down the options that fit you best.', 'lily' ), 'description_ar' => 'بنختار لكِ الأنسب من بين الخيارات.' ),
			array( 'number' => '03', 'title' => __( 'Explore your shades', 'lily' ), 'title_ar' => 'اكتشفي درجاتك', 'description' => __( 'See the colors and styles that suit you.', 'lily' ), 'description_ar' => 'شوفي الألوان والستايلات اللي تناسبك.' ),
			array( 'number' => '04', 'title' => __( 'Choose your lenses', 'lily' ), 'title_ar' => 'اختاري عدساتك', 'description' => __( 'Pick your favorite and shop with confidence.', 'lily' ), 'description_ar' => 'اختاري المفضل لكِ وتسوقي بثقة.' ),
		),
		'lens_finder_questions'              => array(
			'lens_type'    => '',
			'prescription' => '',
			'look'         => '',
			'eye_color'    => '',
			'skin_tone'    => '',
			'color'        => '',
			'duration'     => '',
		),
		'lens_finder_questions_ar'           => array(
			'lens_type'    => 'نوع العدسة',
			'prescription' => 'المقاس',
			'look'         => 'اللوك المفضل',
			'eye_color'    => 'لون عينك الطبيعي',
			'skin_tone'    => 'لون البشرة',
			'color'        => 'اللون المفضل',
			'duration'     => 'مدة الاستبدال',
		),
		'lens_finder_responsibility_text'    => '',
		'lens_finder_responsibility_text_ar' => 'أتفهم أن هذه الاختيارات مسؤوليتي ولا تغني عن استشارة طبيب العيون.',
		'lens_finder_no_results_message'     => '',
		'lens_finder_no_results_message_ar'  => 'لم نجد عدسات مطابقة. جربي تغيير اختيار أو اختيارين.',
		),
		function_exists( 'lily_about_settings_defaults' ) ? lily_about_settings_defaults() : array(),
		function_exists( 'lily_faq_settings_defaults' ) ? lily_faq_settings_defaults() : array(),
		function_exists( 'lily_contact_settings_defaults' ) ? lily_contact_settings_defaults() : array(),
		function_exists( 'lily_terms_settings_defaults' ) ? lily_terms_settings_defaults() : array(),
		function_exists( 'lily_shipping_policy_settings_defaults' ) ? lily_shipping_policy_settings_defaults() : array(),
		function_exists( 'lily_returns_settings_defaults' ) ? lily_returns_settings_defaults() : array(),
		function_exists( 'lily_privacy_settings_defaults' ) ? lily_privacy_settings_defaults() : array()
	);
}

/**
 * Save Homepage Settings.
 */
function lily_save_homepage_settings() {
	if ( empty( $_POST['lily_homepage_settings_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['lily_homepage_settings_nonce'] ) ), 'lily_save_homepage_settings' ) ) {
		return;
	}

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have permission to edit Lily settings.', 'lily' ) );
	}

	$raw      = isset( $_POST['lily_homepage'] ) ? (array) wp_unslash( $_POST['lily_homepage'] ) : array();
	$settings = lily_sanitize_homepage_settings( $raw );

	update_option( 'lily_homepage_settings', $settings );

	wp_safe_redirect( add_query_arg( 'updated', 'true', admin_url( 'admin.php?page=lily-homepage' ) ) );
	exit;
}
add_action( 'admin_post_lily_save_homepage_settings', 'lily_save_homepage_settings' );

/**
 * Sanitize homepage settings.
 *
 * @param array $raw Raw submitted values.
 * @return array
 */
function lily_sanitize_homepage_settings( array $raw ) {
	$defaults = lily_homepage_settings_defaults();
	$data     = array();

	foreach ( $defaults as $key => $default ) {
		if ( 0 === strpos( $key, 'show_' ) ) {
			$data[ $key ] = ! empty( $raw[ $key ] ) ? 1 : 0;
		}
	}

	$text_fields = array(
		'brands_heading',
		'brands_heading_ar',
		'collections_heading',
		'collections_heading_ar',
		'colors_heading',
		'colors_heading_ar',
		'best_sellers_heading',
		'best_sellers_heading_ar',
		'lens_finder_heading',
		'lens_finder_heading_ar',
		'lens_finder_eyebrow',
		'lens_finder_eyebrow_ar',
		'lens_finder_start_text',
		'lens_finder_start_text_ar',
		'lens_finder_image_alt',
		'lens_finder_image_alt_ar',
	);

	foreach ( $text_fields as $field ) {
		$data[ $field ] = isset( $raw[ $field ] ) ? sanitize_text_field( $raw[ $field ] ) : '';
	}

	$textarea_fields = array(
		'collections_description',
		'collections_description_ar',
		'colors_description',
		'colors_description_ar',
		'best_sellers_description',
		'best_sellers_description_ar',
		'lens_finder_description',
		'lens_finder_description_ar',
		'lens_finder_responsibility_text',
		'lens_finder_responsibility_text_ar',
		'lens_finder_no_results_message',
		'lens_finder_no_results_message_ar',
	);

	foreach ( $textarea_fields as $field ) {
		$data[ $field ] = isset( $raw[ $field ] ) ? sanitize_textarea_field( $raw[ $field ] ) : '';
	}

	foreach ( array( 'view_all_colors_link' ) as $field ) {
		$data[ $field ] = lily_sanitize_link_field( isset( $raw[ $field ] ) && is_array( $raw[ $field ] ) ? $raw[ $field ] : array() );
	}

	$data['hero_slides']                   = lily_sanitize_hero_slides( isset( $raw['hero_slides'] ) && is_array( $raw['hero_slides'] ) ? $raw['hero_slides'] : array() );
	$data['announcement_items']            = lily_sanitize_announcement_items( isset( $raw['announcement_items'] ) && is_array( $raw['announcement_items'] ) ? $raw['announcement_items'] : array() );
	$data['collections_to_display']       = isset( $raw['collections_to_display'] ) ? array_map( 'absint', (array) $raw['collections_to_display'] ) : array();
	$data['colors_to_display']            = isset( $raw['colors_to_display'] ) ? array_map( 'absint', (array) $raw['colors_to_display'] ) : array();
	$data['best_sellers_products']        = array_values( array_filter( array_map( 'absint', (array) ( $raw['best_sellers_products'] ?? array() ) ) ) );
	$data['brands']                       = lily_sanitize_brands( isset( $raw['brands'] ) && is_array( $raw['brands'] ) ? $raw['brands'] : array() );

	// Lens Finder image, steps and question labels.
	$data['lens_finder_image'] = absint( $raw['lens_finder_image'] ?? 0 );

	$lily_steps = array();
	foreach ( array_slice( (array) ( $raw['lens_finder_steps'] ?? array() ), 0, 4 ) as $lily_step ) {
		$lily_steps[] = array(
			'number'         => sanitize_text_field( (string) ( $lily_step['number'] ?? '' ) ),
			'title'          => sanitize_text_field( (string) ( $lily_step['title'] ?? '' ) ),
			'title_ar'       => sanitize_text_field( (string) ( $lily_step['title_ar'] ?? '' ) ),
			'description'    => sanitize_text_field( (string) ( $lily_step['description'] ?? '' ) ),
			'description_ar' => sanitize_text_field( (string) ( $lily_step['description_ar'] ?? '' ) ),
		);
	}
	$data['lens_finder_steps'] = $lily_steps;

	$lily_question_defaults = wp_list_pluck( lily_lens_finder_question_defaults(), 'default', 'key' );
	$lily_questions         = array();
	$lily_questions_ar      = array();
	foreach ( $lily_question_defaults as $lily_qkey => $lily_qdefault ) {
		$lily_questions[ $lily_qkey ] = isset( $raw['lens_finder_questions'][ $lily_qkey ] )
			? sanitize_text_field( wp_unslash( $raw['lens_finder_questions'][ $lily_qkey ] ) )
			: '';
		$lily_questions_ar[ $lily_qkey ] = isset( $raw['lens_finder_questions_ar'][ $lily_qkey ] )
			? sanitize_text_field( wp_unslash( $raw['lens_finder_questions_ar'][ $lily_qkey ] ) )
			: '';
	}
	$data['lens_finder_questions']    = $lily_questions;
	$data['lens_finder_questions_ar'] = $lily_questions_ar;

	if ( function_exists( 'lily_sanitize_about_settings' ) ) {
		$data = array_merge( $data, lily_sanitize_about_settings( $raw ) );
	}

	if ( function_exists( 'lily_sanitize_faq_settings' ) ) {
		$data = array_merge( $data, lily_sanitize_faq_settings( $raw ) );
	}

	if ( function_exists( 'lily_sanitize_contact_settings' ) ) {
		$data = array_merge( $data, lily_sanitize_contact_settings( $raw ) );
	}

	if ( function_exists( 'lily_sanitize_terms_settings' ) ) {
		$data = array_merge( $data, lily_sanitize_terms_settings( $raw ) );
	}

	if ( function_exists( 'lily_sanitize_shipping_policy_settings' ) ) {
		$data = array_merge( $data, lily_sanitize_shipping_policy_settings( $raw ) );
	}

	if ( function_exists( 'lily_sanitize_returns_settings' ) ) {
		$data = array_merge( $data, lily_sanitize_returns_settings( $raw ) );
	}

	if ( function_exists( 'lily_sanitize_privacy_settings' ) ) {
		$data = array_merge( $data, lily_sanitize_privacy_settings( $raw ) );
	}

	return wp_parse_args( $data, $defaults );
}

/**
 * Existing Lens Finder question keys with their original labels.
 * Keys are the internal mapping — they must never change.
 *
 * @return array[]
 */
function lily_lens_finder_question_defaults() {
	return array(
		array( 'key' => 'lens_type', 'default' => __( 'Lens Type', 'lily' ) ),
		array( 'key' => 'prescription', 'default' => __( 'Prescription', 'lily' ) ),
		array( 'key' => 'look', 'default' => __( 'Preferred Look', 'lily' ) ),
		array( 'key' => 'eye_color', 'default' => __( 'Natural Eye Color', 'lily' ) ),
		array( 'key' => 'skin_tone', 'default' => __( 'Skin Tone', 'lily' ) ),
		array( 'key' => 'color', 'default' => __( 'Preferred Color', 'lily' ) ),
		array( 'key' => 'duration', 'default' => __( 'Replacement Duration', 'lily' ) ),
	);
}

/**
 * Sanitize link-like fields.
 *
 * @param array $raw Raw link data.
 * @return array
 */
function lily_sanitize_link_field( array $raw ) {
	return array(
		'title'    => isset( $raw['title'] ) ? sanitize_text_field( $raw['title'] ) : '',
		'title_ar' => isset( $raw['title_ar'] ) ? sanitize_text_field( $raw['title_ar'] ) : '',
		'url'      => isset( $raw['url'] ) ? esc_url_raw( $raw['url'] ) : '',
		'target'   => ! empty( $raw['target'] ) && '_blank' === $raw['target'] ? '_blank' : '_self',
	);
}

/**
 * Sanitize announcement items.
 *
 * @param array $raw Raw submitted rows.
 * @return array
 */
function lily_sanitize_announcement_items( array $raw ) {
	$items = array();

	foreach ( $raw as $item ) {
		if ( ! is_array( $item ) ) {
			continue;
		}

		$text = isset( $item['text'] ) ? sanitize_text_field( $item['text'] ) : '';
		$text_ar = isset( $item['text_ar'] ) ? sanitize_text_field( $item['text_ar'] ) : '';
		$link = lily_sanitize_link_field( isset( $item['link'] ) && is_array( $item['link'] ) ? $item['link'] : array() );

		if ( '' === $text && empty( $link['url'] ) ) {
			continue;
		}

		$items[] = array(
			'text'    => $text,
			'text_ar' => $text_ar,
			'link'    => $link,
		);
	}

	return array_values( $items );
}

/**
 * Sanitize hero slides.
 *
 * @param array $raw Raw submitted slides.
 * @return array
 */
function lily_sanitize_hero_slides( array $raw ) {
	$slides = array();

	foreach ( $raw as $slide ) {
		if ( ! is_array( $slide ) ) {
			continue;
		}

		$enabled     = ! empty( $slide['enabled'] );
		$image       = isset( $slide['image'] ) ? absint( $slide['image'] ) : 0;
		$mobile      = isset( $slide['mobile_image'] ) ? absint( $slide['mobile_image'] ) : 0;
		$title       = isset( $slide['title'] ) ? sanitize_text_field( $slide['title'] ) : '';
		$title_ar    = isset( $slide['title_ar'] ) ? sanitize_text_field( $slide['title_ar'] ) : '';
		$description = isset( $slide['description'] ) ? sanitize_textarea_field( $slide['description'] ) : '';
		$description_ar = isset( $slide['description_ar'] ) ? sanitize_textarea_field( $slide['description_ar'] ) : '';
		$cta_text    = isset( $slide['cta_text'] ) ? sanitize_text_field( $slide['cta_text'] ) : '';
		$cta_text_ar = isset( $slide['cta_text_ar'] ) ? sanitize_text_field( $slide['cta_text_ar'] ) : '';
		$cta_url     = isset( $slide['cta_url'] ) ? esc_url_raw( $slide['cta_url'] ) : '';

		if ( ! $enabled && ! $image && ! $mobile && '' === $title && '' === $description && '' === $cta_url ) {
			continue;
		}

		$slides[] = array(
			'enabled'        => $enabled ? 1 : 0,
			'image'          => $image,
			'mobile_image'   => $mobile,
			'title'          => $title,
			'title_ar'       => $title_ar,
			'description'    => $description,
			'description_ar' => $description_ar,
			'cta_text'       => $cta_text,
			'cta_text_ar'    => $cta_text_ar,
			'cta_url'        => $cta_url,
		);
	}

	return $slides;
}

/**
 * Sanitize brand rows.
 *
 * Each row references a real Brand taxonomy term; the URL is an optional
 * manual override only.
 *
 * @param array $raw Raw brand rows.
 * @return array
 */
function lily_sanitize_brands( array $raw ) {
	$brands = array();

	foreach ( $raw as $brand ) {
		if ( ! is_array( $brand ) ) {
			continue;
		}

		$term_id = isset( $brand['brand_term'] ) ? absint( $brand['brand_term'] ) : 0;
		$url     = isset( $brand['url'] ) ? esc_url_raw( $brand['url'] ) : '';
		$target  = ! empty( $brand['target'] ) && '_blank' === $brand['target'] ? '_blank' : '_self';

		if ( ! $term_id && '' === $url ) {
			continue;
		}

		$brands[] = array(
			'brand_term' => $term_id,
			'url'        => $url,
			'target'     => $target,
		);
	}

	return $brands;
}

/**
 * Render Homepage Settings page.
 */
function lily_render_homepage_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	if ( ! function_exists( 'submit_button' ) ) {
		require_once ABSPATH . 'wp-admin/includes/template.php';
	}

	$settings = wp_parse_args( get_option( 'lily_homepage_settings', array() ), lily_homepage_settings_defaults() );
	?>
	<div class="wrap lily-admin">
		<h1><?php esc_html_e( 'Homepage Settings', 'lily' ); ?></h1>
		<?php if ( isset( $_GET['updated'] ) ) : ?>
			<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Homepage settings saved.', 'lily' ); ?></p></div>
		<?php endif; ?>

		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="lily_save_homepage_settings">
			<?php wp_nonce_field( 'lily_save_homepage_settings', 'lily_homepage_settings_nonce' ); ?>

			<nav class="lily-admin-tabs" aria-label="<?php esc_attr_e( 'Homepage sections', 'lily' ); ?>">
				<?php
				$tabs = array(
					'announcement' => esc_html__( 'Announcement Bar', 'lily' ),
					'hero'         => esc_html__( 'Hero', 'lily' ),
					'brands'       => esc_html__( 'Brands', 'lily' ),
					'collections'  => esc_html__( 'Shop by Collections', 'lily' ),
					'colors'       => esc_html__( 'Shop by Colors', 'lily' ),
					'best'         => esc_html__( 'Best Sellers', 'lily' ),
					'lens'         => esc_html__( 'Find Your Best Lenses', 'lily' ),
					'about'        => esc_html__( 'About Page', 'lily' ),
					'faq'          => esc_html__( 'FAQ Page', 'lily' ),
					'contact'      => esc_html__( 'Contact Page', 'lily' ),
					'terms'        => esc_html__( 'Terms Page', 'lily' ),
					'shipping'     => esc_html__( 'Shipping Page', 'lily' ),
					'returns'      => esc_html__( 'Returns Page', 'lily' ),
					'privacy'      => esc_html__( 'Privacy Page', 'lily' ),
				);
				foreach ( $tabs as $id => $label ) :
					?>
					<a href="#lily-tab-<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $label ); ?></a>
				<?php endforeach; ?>
			</nav>

			<?php lily_render_announcement_fields( $settings ); ?>
			<?php lily_render_hero_fields( $settings ); ?>
			<?php lily_render_brand_fields( $settings ); ?>
			<?php lily_render_collection_fields( $settings ); ?>
			<?php lily_render_color_fields( $settings ); ?>
			<?php lily_render_best_seller_fields( $settings ); ?>
			<?php lily_render_lens_finder_fields( $settings ); ?>
			<?php
			if ( function_exists( 'lily_render_about_fields' ) ) {
				lily_render_about_fields( $settings );
			}
			if ( function_exists( 'lily_render_faq_fields' ) ) {
				lily_render_faq_fields( $settings );
			}
			if ( function_exists( 'lily_render_contact_fields' ) ) {
				lily_render_contact_fields( $settings );
			}
			if ( function_exists( 'lily_render_terms_fields' ) ) {
				lily_render_terms_fields( $settings );
			}
			if ( function_exists( 'lily_render_shipping_policy_fields' ) ) {
				lily_render_shipping_policy_fields( $settings );
			}
			if ( function_exists( 'lily_render_returns_fields' ) ) {
				lily_render_returns_fields( $settings );
			}
			if ( function_exists( 'lily_render_privacy_fields' ) ) {
				lily_render_privacy_fields( $settings );
			}
			?>

			<?php submit_button( esc_html__( 'Save Homepage Settings', 'lily' ) ); ?>
		</form>
	</div>
	<?php
}

function lily_admin_text_field( $settings, $name, $label ) {
	printf( '<label><span>%1$s</span><input type="text" name="lily_homepage[%2$s]" value="%3$s"></label>', esc_html( $label ), esc_attr( $name ), esc_attr( $settings[ $name ] ?? '' ) );
}

function lily_admin_textarea_field( $settings, $name, $label ) {
	printf( '<label><span>%1$s</span><textarea name="lily_homepage[%2$s]" rows="3">%3$s</textarea></label>', esc_html( $label ), esc_attr( $name ), esc_textarea( $settings[ $name ] ?? '' ) );
}

function lily_admin_toggle_field( $settings, $name, $label ) {
	printf( '<label class="lily-toggle"><input type="checkbox" name="lily_homepage[%1$s]" value="1" %2$s> <span>%3$s</span></label>', esc_attr( $name ), checked( ! empty( $settings[ $name ] ), true, false ), esc_html( $label ) );
}

function lily_admin_link_field( $settings, $name, $label ) {
	$link = isset( $settings[ $name ] ) && is_array( $settings[ $name ] ) ? $settings[ $name ] : array();
	echo '<fieldset class="lily-link-field"><legend>' . esc_html( $label ) . '</legend>';
	printf( '<input type="text" name="lily_homepage[%1$s][title]" value="%2$s" placeholder="%3$s">', esc_attr( $name ), esc_attr( $link['title'] ?? '' ), esc_attr__( 'Button text', 'lily' ) );
	printf( '<input type="text" name="lily_homepage[%1$s][title_ar]" value="%2$s" placeholder="%3$s">', esc_attr( $name ), esc_attr( $link['title_ar'] ?? '' ), esc_attr__( 'Button text (Arabic)', 'lily' ) );
	printf( '<input type="url" name="lily_homepage[%1$s][url]" value="%2$s" placeholder="%3$s">', esc_attr( $name ), esc_url( $link['url'] ?? '' ), esc_attr__( 'URL', 'lily' ) );
	printf( '<label class="lily-inline"><input type="checkbox" name="lily_homepage[%1$s][target]" value="_blank" %2$s> %3$s</label>', esc_attr( $name ), checked( '_blank', $link['target'] ?? '_self', false ), esc_html__( 'Open in new tab', 'lily' ) );
	echo '</fieldset>';
}

/**
 * Render a grouped "Arabic Content" block for one dashboard section.
 *
 * Each entry maps an existing English option key to [ label, type ] where
 * type is "text" or "textarea". The Arabic input is stored as "<key>_ar"
 * in the same option array. Blanks reuse the English value on the site.
 *
 * @param array  $settings    Current settings.
 * @param string $group_title Group heading (without the Arabic suffix).
 * @param array  $fields      Option key => array( label, type ).
 */
function lily_render_ar_fields( $settings, $group_title, $fields ) {
	echo '<h3>' . esc_html( $group_title ) . ' — ' . esc_html__( 'Arabic Content', 'lily' ) . '</h3>';
	echo '<p class="description">' . esc_html__( 'Shown on Arabic pages only. Leave any field blank to reuse its English text.', 'lily' ) . '</p>';

	foreach ( $fields as $name => $field ) {
		$label = isset( $field[0] ) ? $field[0] : $name;
		$type  = isset( $field[1] ) ? $field[1] : 'text';
		$label = sprintf( __( '%s (Arabic)', 'lily' ), $label );

		if ( 'textarea' === $type ) {
			lily_admin_textarea_field( $settings, $name . '_ar', $label );
		} else {
			lily_admin_text_field( $settings, $name . '_ar', $label );
		}
	}
}

/**
 * Print one consistent image-size guidance line for dashboard image fields.
 *
 * Every Lily image field shows this directly under the upload control so the
 * store owner always knows the recommended pixel dimensions for that exact
 * location. Dimensions are derived from the current frontend implementation
 * (container size, aspect ratio, object-fit, responsive behavior).
 *
 * @param int $width  Recommended width in pixels.
 * @param int $height Recommended height in pixels.
 */
function lily_image_guidance( $width, $height ) {
	printf(
		'<p class="lily-image-guidance">%s</p>',
		esc_html( sprintf( __( 'Recommended size: %1$d × %2$d px', 'lily' ), (int) $width, (int) $height ) )
	);
}

function lily_admin_image_field( $settings, $name, $label, $width = 0, $height = 0, $field_prefix = 'lily_homepage' ) {
	$image_id = absint( $settings[ $name ] ?? 0 );
	echo '<div class="lily-image-field">';
	echo '<span>' . esc_html( $label ) . '</span>';
	printf( '<input type="hidden" name="%3$s[%1$s]" value="%2$d" data-lily-image-input>', esc_attr( $name ), $image_id, esc_attr( $field_prefix ) );
	echo '<div class="lily-image-preview" data-lily-image-preview>';
	if ( $image_id ) {
		echo wp_get_attachment_image( $image_id, 'thumbnail' );
	}
	echo '</div>';
	echo '<button type="button" class="button" data-lily-image-select>' . esc_html__( 'Choose Image', 'lily' ) . '</button> ';
	echo '<button type="button" class="button" data-lily-image-remove>' . esc_html__( 'Remove', 'lily' ) . '</button>';
	if ( $width && $height ) {
		lily_image_guidance( $width, $height );
	}
	echo '</div>';
}

function lily_render_announcement_fields( $settings ) {
	$items = isset( $settings['announcement_items'] ) && is_array( $settings['announcement_items'] ) ? array_values( $settings['announcement_items'] ) : array();

	echo '<section id="lily-tab-announcement" class="lily-admin-panel"><h2>' . esc_html__( 'Announcement Bar', 'lily' ) . '</h2>';
	lily_admin_toggle_field( $settings, 'show_announcement_bar', esc_html__( 'Show Announcement Bar', 'lily' ) );
	echo '<p class="description">' . esc_html__( 'Short benefit messages shown in the top bar. Drag the handle to reorder.', 'lily' ) . '</p>';
	echo '<div class="lily-announcement-rows" data-lily-announcement-rows>';
	foreach ( $items as $index => $item ) {
		lily_render_announcement_row( $item, $index );
	}
	echo '</div><button type="button" class="button button-secondary" data-lily-add-announcement>' . esc_html__( 'Add Message', 'lily' ) . '</button>';
	echo '<script type="text/html" id="tmpl-lily-announcement-row">';
	lily_render_announcement_row( array(), '__INDEX__' );
	echo '</script></section>';
}

function lily_render_announcement_row( $item, $index ) {
	$text    = isset( $item['text'] ) ? $item['text'] : '';
	$text_ar = isset( $item['text_ar'] ) ? $item['text_ar'] : '';
	$link = isset( $item['link'] ) && is_array( $item['link'] ) ? $item['link'] : array();

	echo '<div class="lily-announcement-row">';
	echo '<span class="dashicons dashicons-move"></span>';
	printf( '<input type="text" name="lily_homepage[announcement_items][%1$s][text]" value="%2$s" placeholder="%3$s">', esc_attr( $index ), esc_attr( $text ), esc_attr__( 'Announcement message', 'lily' ) );
	printf( '<input type="text" name="lily_homepage[announcement_items][%1$s][text_ar]" value="%2$s" placeholder="%3$s">', esc_attr( $index ), esc_attr( $text_ar ), esc_attr__( 'Announcement message (Arabic)', 'lily' ) );
	printf( '<input type="url" name="lily_homepage[announcement_items][%1$s][link][url]" value="%2$s" placeholder="%3$s">', esc_attr( $index ), esc_url( $link['url'] ?? '' ), esc_attr__( 'Optional URL', 'lily' ) );
	printf( '<label><input type="checkbox" name="lily_homepage[announcement_items][%1$s][link][target]" value="_blank" %2$s> %3$s</label>', esc_attr( $index ), checked( '_blank', $link['target'] ?? '_self', false ), esc_html__( 'New tab', 'lily' ) );
	echo '<button type="button" class="button-link-delete" data-lily-remove-announcement>' . esc_html__( 'Remove', 'lily' ) . '</button>';
	echo '</div>';
}

function lily_render_hero_fields( $settings ) {
	$slides = isset( $settings['hero_slides'] ) && is_array( $settings['hero_slides'] ) ? array_values( $settings['hero_slides'] ) : array();

	echo '<section id="lily-tab-hero" class="lily-admin-panel"><h2>' . esc_html__( 'Hero', 'lily' ) . '</h2>';
	lily_admin_toggle_field( $settings, 'show_hero', esc_html__( 'Show Hero', 'lily' ) );
	echo '<p class="description">' . esc_html__( 'Hero slides play in order. Drag the handle to reorder. If a slide has no CTA URL, it links automatically to its matching category (Colored / Clear) or the Shop page.', 'lily' ) . '</p>';
	echo '<div class="lily-hero-rows" data-lily-hero-rows>';
	foreach ( $slides as $index => $slide ) {
		lily_render_hero_row( $slide, $index );
	}
	echo '</div><button type="button" class="button button-secondary" data-lily-add-hero>' . esc_html__( 'Add Slide', 'lily' ) . '</button>';
	echo '<script type="text/html" id="tmpl-lily-hero-row">';
	lily_render_hero_row( array(), '__INDEX__' );
	echo '</script></section>';
}

function lily_render_hero_row( $slide, $index ) {
	$enabled        = ! empty( $slide['enabled'] );
	$image          = absint( $slide['image'] ?? 0 );
	$mobile         = absint( $slide['mobile_image'] ?? 0 );
	$title          = $slide['title'] ?? '';
	$title_ar       = $slide['title_ar'] ?? '';
	$description    = $slide['description'] ?? '';
	$description_ar = $slide['description_ar'] ?? '';
	$cta_text       = $slide['cta_text'] ?? '';
	$cta_text_ar    = $slide['cta_text_ar'] ?? '';
	$cta_url        = $slide['cta_url'] ?? '';

	echo '<div class="lily-hero-row">';
	echo '<span class="dashicons dashicons-move"></span>';
	echo '<div class="lily-hero-row__fields">';
	printf( '<label class="lily-inline"><input type="checkbox" name="lily_homepage[hero_slides][%1$s][enabled]" value="1" %2$s> %3$s</label>', esc_attr( $index ), checked( $enabled, true, false ), esc_html__( 'Enabled', 'lily' ) );

	echo '<div class="lily-hero-row__images">';
	echo '<div class="lily-image-field">';
	echo '<span>' . esc_html__( 'Desktop Image', 'lily' ) . '</span>';
	printf( '<input type="hidden" name="lily_homepage[hero_slides][%1$s][image]" value="%2$d" data-lily-image-input>', esc_attr( $index ), $image );
	echo '<div class="lily-image-preview" data-lily-image-preview>';
	if ( $image ) {
		echo wp_get_attachment_image( $image, 'thumbnail' );
	}
	echo '</div>';
	echo '<button type="button" class="button" data-lily-image-select>' . esc_html__( 'Choose Image', 'lily' ) . '</button> ';
	echo '<button type="button" class="button" data-lily-image-remove>' . esc_html__( 'Remove', 'lily' ) . '</button>';
	lily_image_guidance( 1920, 1080 );
	echo '</div>';

	echo '<div class="lily-image-field">';
	echo '<span>' . esc_html__( 'Mobile Image (optional)', 'lily' ) . '</span>';
	printf( '<input type="hidden" name="lily_homepage[hero_slides][%1$s][mobile_image]" value="%2$d" data-lily-image-input>', esc_attr( $index ), $mobile );
	echo '<div class="lily-image-preview" data-lily-image-preview>';
	if ( $mobile ) {
		echo wp_get_attachment_image( $mobile, 'thumbnail' );
	}
	echo '</div>';
	echo '<button type="button" class="button" data-lily-image-select>' . esc_html__( 'Choose Image', 'lily' ) . '</button> ';
	echo '<button type="button" class="button" data-lily-image-remove>' . esc_html__( 'Remove', 'lily' ) . '</button>';
	lily_image_guidance( 1080, 1620 );
	echo '</div>';
	echo '</div>';

	printf( '<input type="text" name="lily_homepage[hero_slides][%1$s][title]" value="%2$s" placeholder="%3$s">', esc_attr( $index ), esc_attr( $title ), esc_attr__( 'Slide title', 'lily' ) );
	printf( '<input type="text" name="lily_homepage[hero_slides][%1$s][title_ar]" value="%2$s" placeholder="%3$s">', esc_attr( $index ), esc_attr( $title_ar ), esc_attr__( 'Slide title (Arabic)', 'lily' ) );
	printf( '<textarea name="lily_homepage[hero_slides][%1$s][description]" rows="2" placeholder="%2$s">%3$s</textarea>', esc_attr( $index ), esc_attr__( 'Short description', 'lily' ), esc_textarea( $description ) );
	printf( '<textarea name="lily_homepage[hero_slides][%1$s][description_ar]" rows="2" placeholder="%2$s">%3$s</textarea>', esc_attr( $index ), esc_attr__( 'Short description (Arabic)', 'lily' ), esc_textarea( $description_ar ) );
	echo '<div class="lily-hero-row__cta">';
	printf( '<input type="text" name="lily_homepage[hero_slides][%1$s][cta_text]" value="%2$s" placeholder="%3$s">', esc_attr( $index ), esc_attr( $cta_text ), esc_attr__( 'CTA text', 'lily' ) );
	printf( '<input type="text" name="lily_homepage[hero_slides][%1$s][cta_text_ar]" value="%2$s" placeholder="%3$s">', esc_attr( $index ), esc_attr( $cta_text_ar ), esc_attr__( 'CTA text (Arabic)', 'lily' ) );
	printf( '<input type="url" name="lily_homepage[hero_slides][%1$s][cta_url]" value="%2$s" placeholder="%3$s">', esc_attr( $index ), esc_url( $cta_url ), esc_attr__( 'CTA URL (optional)', 'lily' ) );
	echo '</div>';
	echo '</div>';
	echo '<button type="button" class="button-link-delete" data-lily-remove-hero>' . esc_html__( 'Remove', 'lily' ) . '</button>';
	echo '</div>';
}

function lily_render_brand_fields( $settings ) {
	$brands = isset( $settings['brands'] ) && is_array( $settings['brands'] ) ? $settings['brands'] : array();
	echo '<section id="lily-tab-brands" class="lily-admin-panel"><h2>' . esc_html__( 'Brands', 'lily' ) . '</h2>';
	lily_admin_toggle_field( $settings, 'show_brands', esc_html__( 'Show Brands', 'lily' ) );
	lily_admin_text_field( $settings, 'brands_heading', esc_html__( 'Brands Heading', 'lily' ) );
	lily_render_ar_fields(
		$settings,
		esc_html__( 'Brands', 'lily' ),
		array(
			'brands_heading' => array( esc_html__( 'Brands Heading', 'lily' ), 'text' ),
		)
	);
	echo '<p class="description">' . esc_html__( 'Pick real brands below to choose the order shown on the homepage. Leave the list empty to show every brand automatically. Logos are managed under Products → Attributes → Brand.', 'lily' ) . '</p>';
	echo '<div class="lily-brand-rows" data-lily-brand-rows>';
	foreach ( $brands as $index => $brand ) {
		lily_render_brand_row( $brand, $index );
	}
	echo '</div><button type="button" class="button button-secondary" data-lily-add-brand>' . esc_html__( 'Add Brand', 'lily' ) . '</button>';
	echo '<script type="text/html" id="tmpl-lily-brand-row">';
	lily_render_brand_row( array(), '__INDEX__' );
	echo '</script></section>';
}

function lily_render_brand_row( $brand, $index ) {
	$term_id = absint( $brand['brand_term'] ?? 0 );
	$url     = isset( $brand['url'] ) ? $brand['url'] : '';
	$target  = ! empty( $brand['target'] ) && '_blank' === $brand['target'];

	echo '<div class="lily-brand-row">';
	echo '<span class="dashicons dashicons-move"></span>';

	$taxonomy = function_exists( 'lily_get_brand_taxonomy' ) ? lily_get_brand_taxonomy() : '';
	$terms    = $taxonomy && taxonomy_exists( $taxonomy ) ? get_terms( array( 'taxonomy' => $taxonomy, 'hide_empty' => false ) ) : array();

	if ( is_wp_error( $terms ) || empty( $terms ) ) {
		echo '<p class="description">' . esc_html__( 'No brands yet — create them under Products → Attributes → Brand.', 'lily' ) . '</p>';
		printf( '<input type="hidden" name="lily_homepage[brands][%1$s][brand_term]" value="0">', esc_attr( $index ) );
	} else {
		printf( '<select name="lily_homepage[brands][%1$s][brand_term]">', esc_attr( $index ) );
		echo '<option value="0">' . esc_html__( 'Select brand…', 'lily' ) . '</option>';
		foreach ( $terms as $term ) {
			printf(
				'<option value="%1$d"%2$s>%3$s</option>',
				absint( $term->term_id ),
				selected( $term_id, (int) $term->term_id, false ),
				esc_html( $term->name )
			);
		}
		echo '</select>';
	}

	printf( '<input type="url" name="lily_homepage[brands][%1$s][url]" value="%2$s" placeholder="%3$s">', esc_attr( $index ), esc_url( $url ), esc_attr__( 'Custom URL (optional)', 'lily' ) );
	printf( '<label><input type="checkbox" name="lily_homepage[brands][%1$s][target]" value="_blank" %2$s> %3$s</label>', esc_attr( $index ), checked( $target, true, false ), esc_html__( 'New tab', 'lily' ) );
	echo '<button type="button" class="button-link-delete" data-lily-remove-brand>' . esc_html__( 'Remove', 'lily' ) . '</button>';
	echo '</div>';
}

function lily_render_collection_fields( $settings ) {
	echo '<section id="lily-tab-collections" class="lily-admin-panel"><h2>' . esc_html__( 'Shop by Collections', 'lily' ) . '</h2>';
	lily_admin_toggle_field( $settings, 'show_shop_by_collections', esc_html__( 'Show Shop by Collections', 'lily' ) );
	lily_admin_text_field( $settings, 'collections_heading', esc_html__( 'Collections Heading', 'lily' ) );
	lily_admin_textarea_field( $settings, 'collections_description', esc_html__( 'Collections Description', 'lily' ) );
	lily_render_ar_fields(
		$settings,
		esc_html__( 'Shop by Collections', 'lily' ),
		array(
			'collections_heading'     => array( esc_html__( 'Collections Heading', 'lily' ), 'text' ),
			'collections_description' => array( esc_html__( 'Collections Description', 'lily' ), 'textarea' ),
		)
	);
	lily_admin_taxonomy_checkboxes( 'product_cat', 'collections_to_display', $settings['collections_to_display'] ?? array(), esc_html__( 'Collections to Display', 'lily' ) );
	echo '</section>';
}

function lily_render_color_fields( $settings ) {
	echo '<section id="lily-tab-colors" class="lily-admin-panel"><h2>' . esc_html__( 'Shop by Colors', 'lily' ) . '</h2>';
	lily_admin_toggle_field( $settings, 'show_shop_by_colors', esc_html__( 'Show Shop by Colors', 'lily' ) );
	lily_admin_text_field( $settings, 'colors_heading', esc_html__( 'Colors Heading', 'lily' ) );
	lily_admin_textarea_field( $settings, 'colors_description', esc_html__( 'Colors Description', 'lily' ) );
	lily_render_ar_fields(
		$settings,
		esc_html__( 'Shop by Colors', 'lily' ),
		array(
			'colors_heading'     => array( esc_html__( 'Colors Heading', 'lily' ), 'text' ),
			'colors_description' => array( esc_html__( 'Colors Description', 'lily' ), 'textarea' ),
		)
	);
	lily_admin_taxonomy_checkboxes( 'pa_color', 'colors_to_display', $settings['colors_to_display'] ?? array(), esc_html__( 'Colors to Display', 'lily' ), true );
	lily_admin_link_field( $settings, 'view_all_colors_link', esc_html__( 'View All Colors Link', 'lily' ) );
	echo '</section>';
}

function lily_render_best_seller_fields( $settings ) {
	echo '<section id="lily-tab-best" class="lily-admin-panel"><h2>' . esc_html__( 'Best Sellers', 'lily' ) . '</h2>';
	lily_admin_toggle_field( $settings, 'show_best_sellers', esc_html__( 'Show Best Sellers', 'lily' ) );
	lily_admin_text_field( $settings, 'best_sellers_heading', esc_html__( 'Best Sellers Heading', 'lily' ) );
	lily_admin_textarea_field( $settings, 'best_sellers_description', esc_html__( 'Best Sellers Description', 'lily' ) );
	lily_render_ar_fields(
		$settings,
		esc_html__( 'Best Sellers', 'lily' ),
		array(
			'best_sellers_heading'     => array( esc_html__( 'Best Sellers Heading', 'lily' ), 'text' ),
			'best_sellers_description' => array( esc_html__( 'Best Sellers Description', 'lily' ), 'textarea' ),
		)
	);
	echo '<p class="description">' . esc_html__( 'Best Sellers are managed per product: edit a product (Products → Edit) and tick the "Best Seller" checkbox. Flagged products appear in this section and receive the BEST SELLER badge everywhere; unticking removes both automatically.', 'lily' ) . '</p>';
	echo '</section>';
}

function lily_render_lens_finder_fields( $settings ) {
	echo '<section id="lily-tab-lens" class="lily-admin-panel"><h2>' . esc_html__( 'Find Your Best Lenses', 'lily' ) . '</h2>';
	lily_admin_toggle_field( $settings, 'show_lens_finder', esc_html__( 'Show Find Your Best Lenses', 'lily' ) );

	echo '<h3>' . esc_html__( 'Lens Finder Content', 'lily' ) . '</h3>';
	lily_admin_text_field( $settings, 'lens_finder_eyebrow', esc_html__( 'Eyebrow', 'lily' ) );
	lily_admin_text_field( $settings, 'lens_finder_heading', esc_html__( 'Section Heading', 'lily' ) );
	lily_admin_textarea_field( $settings, 'lens_finder_description', esc_html__( 'Section Description', 'lily' ) );
	lily_admin_text_field( $settings, 'lens_finder_start_text', esc_html__( 'CTA Button Text', 'lily' ) );
	lily_render_ar_fields(
		$settings,
		esc_html__( 'Lens Finder Content', 'lily' ),
		array(
			'lens_finder_eyebrow'     => array( esc_html__( 'Eyebrow', 'lily' ), 'text' ),
			'lens_finder_heading'     => array( esc_html__( 'Section Heading', 'lily' ), 'text' ),
			'lens_finder_description' => array( esc_html__( 'Section Description', 'lily' ), 'textarea' ),
			'lens_finder_start_text'  => array( esc_html__( 'CTA Button Text', 'lily' ), 'text' ),
		)
	);

	echo '<h3>' . esc_html__( 'Lens Finder Image', 'lily' ) . '</h3>';
	lily_admin_image_field( $settings, 'lens_finder_image', esc_html__( 'Image', 'lily' ), 800, 1000 );
	lily_admin_text_field( $settings, 'lens_finder_image_alt', esc_html__( 'Image Alt Text', 'lily' ) );
	lily_render_ar_fields(
		$settings,
		esc_html__( 'Lens Finder Image', 'lily' ),
		array(
			'lens_finder_image_alt' => array( esc_html__( 'Image Alt Text', 'lily' ), 'text' ),
		)
	);

	echo '<h3>' . esc_html__( 'How It Works (4 Steps)', 'lily' ) . '</h3>';
	$lily_steps = isset( $settings['lens_finder_steps'] ) && is_array( $settings['lens_finder_steps'] ) ? $settings['lens_finder_steps'] : array();
	for ( $lily_i = 0; $lily_i < 4; $lily_i++ ) {
		$lily_step = isset( $lily_steps[ $lily_i ] ) && is_array( $lily_steps[ $lily_i ] ) ? $lily_steps[ $lily_i ] : array();
		echo '<fieldset class="lily-link-field"><legend>' . esc_html( sprintf( __( 'Step %d', 'lily' ), $lily_i + 1 ) ) . '</legend>';
		printf( '<input type="text" name="lily_homepage[lens_finder_steps][%1$d][number]" value="%2$s" placeholder="%3$s" style="max-width:90px">', absint( $lily_i ), esc_attr( $lily_step['number'] ?? '' ), esc_attr__( 'Number', 'lily' ) );
		printf( '<input type="text" name="lily_homepage[lens_finder_steps][%1$d][title]" value="%2$s" placeholder="%3$s">', absint( $lily_i ), esc_attr( $lily_step['title'] ?? '' ), esc_attr__( 'Title', 'lily' ) );
		printf( '<input type="text" name="lily_homepage[lens_finder_steps][%1$d][title_ar]" value="%2$s" placeholder="%3$s">', absint( $lily_i ), esc_attr( $lily_step['title_ar'] ?? '' ), esc_attr__( 'Title (Arabic)', 'lily' ) );
		printf( '<input type="text" name="lily_homepage[lens_finder_steps][%1$d][description]" value="%2$s" placeholder="%3$s">', absint( $lily_i ), esc_attr( $lily_step['description'] ?? '' ), esc_attr__( 'Description', 'lily' ) );
		printf( '<input type="text" name="lily_homepage[lens_finder_steps][%1$d][description_ar]" value="%2$s" placeholder="%3$s">', absint( $lily_i ), esc_attr( $lily_step['description_ar'] ?? '' ), esc_attr__( 'Description (Arabic)', 'lily' ) );
		echo '</fieldset>';
	}

	echo '<h3>' . esc_html__( 'Lens Finder Questions', 'lily' ) . '</h3>';
	echo '<p class="description">' . esc_html__( 'Question labels shown during the quiz. Leave blank to use the original wording. The question order and answers never change.', 'lily' ) . '</p>';
	$lily_questions = isset( $settings['lens_finder_questions'] ) && is_array( $settings['lens_finder_questions'] ) ? $settings['lens_finder_questions'] : array();
	foreach ( lily_lens_finder_question_defaults() as $lily_question ) {
		printf(
			'<label><span>%1$s</span><input type="text" name="lily_homepage[lens_finder_questions][%2$s]" value="%3$s" placeholder="%4$s"></label>',
			esc_html( $lily_question['default'] ),
			esc_attr( $lily_question['key'] ),
			esc_attr( $lily_questions[ $lily_question['key'] ] ?? '' ),
			esc_html( $lily_question['default'] )
		);
	}

	echo '<h3>' . esc_html__( 'Lens Finder Questions', 'lily' ) . ' — ' . esc_html__( 'Arabic Content', 'lily' ) . '</h3>';
	echo '<p class="description">' . esc_html__( 'Shown on Arabic pages only. Leave any field blank to reuse its English text.', 'lily' ) . '</p>';
	$lily_questions_ar = isset( $settings['lens_finder_questions_ar'] ) && is_array( $settings['lens_finder_questions_ar'] ) ? $settings['lens_finder_questions_ar'] : array();
	foreach ( lily_lens_finder_question_defaults() as $lily_question ) {
		printf(
			'<label><span>%1$s</span><input type="text" name="lily_homepage[lens_finder_questions_ar][%2$s]" value="%3$s" placeholder="%4$s"></label>',
			esc_html( sprintf( __( '%s (Arabic)', 'lily' ), $lily_question['default'] ) ),
			esc_attr( $lily_question['key'] ),
			esc_attr( $lily_questions_ar[ $lily_question['key'] ] ?? '' ),
			esc_html( $lily_question['default'] )
		);
	}

	lily_admin_textarea_field( $settings, 'lens_finder_responsibility_text', esc_html__( 'Responsibility Confirmation Text', 'lily' ) );
	lily_admin_textarea_field( $settings, 'lens_finder_no_results_message', esc_html__( 'No Results Message', 'lily' ) );
	lily_render_ar_fields(
		$settings,
		esc_html__( 'Lens Finder Messages', 'lily' ),
		array(
			'lens_finder_responsibility_text' => array( esc_html__( 'Responsibility Confirmation Text', 'lily' ), 'textarea' ),
			'lens_finder_no_results_message'  => array( esc_html__( 'No Results Message', 'lily' ), 'textarea' ),
		)
	);
	echo '</section>';
}

function lily_admin_taxonomy_checkboxes( $taxonomy, $name, $selected, $label, $parents_only = false ) {
	$args = array( 'taxonomy' => $taxonomy, 'hide_empty' => false );

	if ( $parents_only ) {
		$args['parent'] = 0;
	}

	$terms    = taxonomy_exists( $taxonomy ) ? get_terms( $args ) : array();
	$selected = array_map( 'absint', (array) $selected );
	echo '<fieldset><legend>' . esc_html( $label ) . '</legend>';
	if ( empty( $terms ) || is_wp_error( $terms ) ) {
		echo '<p>' . esc_html__( 'No items are available yet.', 'lily' ) . '</p></fieldset>';
		return;
	}
	foreach ( $terms as $term ) {
		printf( '<label class="lily-check"><input type="checkbox" name="lily_homepage[%1$s][]" value="%2$d" %3$s> %4$s</label>', esc_attr( $name ), absint( $term->term_id ), checked( in_array( (int) $term->term_id, $selected, true ), true, false ), esc_html( $term->name ) );
	}
	echo '</fieldset>';
}

function lily_admin_taxonomy_select( $taxonomy, $name, $selected, $label ) {
	$terms = taxonomy_exists( $taxonomy ) ? get_terms( array( 'taxonomy' => $taxonomy, 'hide_empty' => false ) ) : array();
	echo '<label><span>' . esc_html( $label ) . '</span><select name="lily_homepage[' . esc_attr( $name ) . ']"><option value="0">' . esc_html__( 'Select a category', 'lily' ) . '</option>';
	if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
		foreach ( $terms as $term ) {
			printf( '<option value="%1$d" %2$s>%3$s</option>', absint( $term->term_id ), selected( $selected, (int) $term->term_id, false ), esc_html( $term->name ) );
		}
	}
	echo '</select></label>';
}
