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
 */
function lily_register_homepage_settings_page() {
	add_menu_page(
		esc_html__( 'Lily', 'lily' ),
		esc_html__( 'Lily', 'lily' ),
		'manage_options',
		'lily',
		'lily_render_homepage_settings_page',
		'dashicons-admin-home',
		58
	);

	add_submenu_page(
		'lily',
		esc_html__( 'Homepage Settings', 'lily' ),
		esc_html__( 'Homepage Settings', 'lily' ),
		'manage_options',
		'lily',
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
	if ( 'toplevel_page_lily' !== $hook && false === strpos( $hook, 'edit-tags.php' ) && false === strpos( $hook, 'term.php' ) ) {
		return;
	}

	wp_enqueue_media();
	wp_enqueue_script( 'jquery-ui-sortable' );
	wp_enqueue_script( 'lily-admin', LILY_THEME_URI . '/assets/js/admin.js', array( 'jquery', 'jquery-ui-sortable' ), LILY_THEME_VERSION, true );
	wp_enqueue_style( 'lily-admin', LILY_THEME_URI . '/assets/css/admin.css', array(), LILY_THEME_VERSION );
}
add_action( 'admin_enqueue_scripts', 'lily_admin_assets' );

/**
 * Default homepage settings.
 *
 * @return array
 */
function lily_homepage_settings_defaults() {
	return array(
		'show_announcement_bar'              => 1,
		'announcement_items'                 => array(
			array(
				'text' => 'Fast Delivery',
				'link' => array( 'title' => '', 'url' => '', 'target' => '_self' ),
			),
			array(
				'text' => 'Cash on Delivery',
				'link' => array( 'title' => '', 'url' => '', 'target' => '_self' ),
			),
			array(
				'text' => 'Premium Quality',
				'link' => array( 'title' => '', 'url' => '', 'target' => '_self' ),
			),
		),
		'show_hero'                          => 1,
		'hero_slides'                        => array(
			array(
				'enabled'     => 1,
				'image'       => 0,
				'mobile_image' => 0,
				'title'       => 'Colored Lenses',
				'description' => '',
				'cta_text'    => '',
				'cta_url'     => '',
			),
			array(
				'enabled'     => 1,
				'image'       => 0,
				'mobile_image' => 0,
				'title'       => 'Clear Lenses',
				'description' => '',
				'cta_text'    => '',
				'cta_url'     => '',
			),
			array(
				'enabled'     => 1,
				'image'       => 0,
				'mobile_image' => 0,
				'title'       => 'Accessories & Lens Care',
				'description' => '',
				'cta_text'    => '',
				'cta_url'     => '',
			),
		),
		'show_brands'                        => 1,
		'brands_heading'                     => '',
		'brands'                             => array(),
		'show_shop_by_collections'           => 1,
		'collections_heading'                => '',
		'collections_description'            => '',
		'collections_to_display'             => array(),
		'show_shop_by_colors'                => 1,
		'colors_heading'                     => '',
		'colors_description'                 => '',
		'colors_to_display'                  => array(),
		'view_all_colors_link'               => array( 'title' => '', 'url' => '', 'target' => '_self' ),
		'show_best_sellers'                  => 1,
		'best_sellers_heading'               => '',
		'best_sellers_description'           => '',
		'best_sellers_products'              => array(),
		'show_lens_finder'                   => 1,
		'lens_finder_heading'                => '',
		'lens_finder_eyebrow'                => '',
		'lens_finder_description'            => '',
		'lens_finder_start_text'             => esc_html__( 'Start Lens Finder', 'lily' ),
		'lens_finder_image'                  => 0,
		'lens_finder_image_alt'              => '',
		'lens_finder_steps'                  => array(
			array( 'number' => '01', 'title' => __( 'Tell us about you', 'lily' ), 'description' => __( 'Choose your preferences and lens needs.', 'lily' ) ),
			array( 'number' => '02', 'title' => __( 'Find your match', 'lily' ), 'description' => __( 'We narrow down the options that fit you best.', 'lily' ) ),
			array( 'number' => '03', 'title' => __( 'Explore your shades', 'lily' ), 'description' => __( 'See the colors and styles that suit you.', 'lily' ) ),
			array( 'number' => '04', 'title' => __( 'Choose your lenses', 'lily' ), 'description' => __( 'Pick your favorite and shop with confidence.', 'lily' ) ),
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
		'lens_finder_responsibility_text'    => '',
		'lens_finder_no_results_message'     => '',
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

	wp_safe_redirect( add_query_arg( 'updated', 'true', menu_page_url( 'lily', false ) ) );
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
		'collections_heading',
		'colors_heading',
		'best_sellers_heading',
		'lens_finder_heading',
		'lens_finder_eyebrow',
		'lens_finder_start_text',
		'lens_finder_image_alt',
	);

	foreach ( $text_fields as $field ) {
		$data[ $field ] = isset( $raw[ $field ] ) ? sanitize_text_field( $raw[ $field ] ) : '';
	}

	$textarea_fields = array(
		'collections_description',
		'colors_description',
		'best_sellers_description',
		'lens_finder_description',
		'lens_finder_responsibility_text',
		'lens_finder_no_results_message',
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
			'number'      => sanitize_text_field( (string) ( $lily_step['number'] ?? '' ) ),
			'title'       => sanitize_text_field( (string) ( $lily_step['title'] ?? '' ) ),
			'description' => sanitize_text_field( (string) ( $lily_step['description'] ?? '' ) ),
		);
	}
	$data['lens_finder_steps'] = $lily_steps;

	$lily_question_defaults = wp_list_pluck( lily_lens_finder_question_defaults(), 'default', 'key' );
	$lily_questions         = array();
	foreach ( $lily_question_defaults as $lily_qkey => $lily_qdefault ) {
		$lily_questions[ $lily_qkey ] = isset( $raw['lens_finder_questions'][ $lily_qkey ] )
			? sanitize_text_field( wp_unslash( $raw['lens_finder_questions'][ $lily_qkey ] ) )
			: '';
	}
	$data['lens_finder_questions'] = $lily_questions;

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
		'title'  => isset( $raw['title'] ) ? sanitize_text_field( $raw['title'] ) : '',
		'url'    => isset( $raw['url'] ) ? esc_url_raw( $raw['url'] ) : '',
		'target' => ! empty( $raw['target'] ) && '_blank' === $raw['target'] ? '_blank' : '_self',
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
		$link = lily_sanitize_link_field( isset( $item['link'] ) && is_array( $item['link'] ) ? $item['link'] : array() );

		if ( '' === $text && empty( $link['url'] ) ) {
			continue;
		}

		$items[] = array(
			'text' => $text,
			'link' => $link,
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
		$description = isset( $slide['description'] ) ? sanitize_textarea_field( $slide['description'] ) : '';
		$cta_text    = isset( $slide['cta_text'] ) ? sanitize_text_field( $slide['cta_text'] ) : '';
		$cta_url     = isset( $slide['cta_url'] ) ? esc_url_raw( $slide['cta_url'] ) : '';

		if ( ! $enabled && ! $image && ! $mobile && '' === $title && '' === $description && '' === $cta_url ) {
			continue;
		}

		$slides[] = array(
			'enabled'     => $enabled ? 1 : 0,
			'image'       => $image,
			'mobile_image' => $mobile,
			'title'       => $title,
			'description' => $description,
			'cta_text'    => $cta_text,
			'cta_url'     => $cta_url,
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
	printf( '<input type="url" name="lily_homepage[%1$s][url]" value="%2$s" placeholder="%3$s">', esc_attr( $name ), esc_url( $link['url'] ?? '' ), esc_attr__( 'URL', 'lily' ) );
	printf( '<label class="lily-inline"><input type="checkbox" name="lily_homepage[%1$s][target]" value="_blank" %2$s> %3$s</label>', esc_attr( $name ), checked( '_blank', $link['target'] ?? '_self', false ), esc_html__( 'Open in new tab', 'lily' ) );
	echo '</fieldset>';
}

function lily_admin_image_field( $settings, $name, $label ) {
	$image_id = absint( $settings[ $name ] ?? 0 );
	echo '<div class="lily-image-field">';
	echo '<span>' . esc_html( $label ) . '</span>';
	printf( '<input type="hidden" name="lily_homepage[%1$s]" value="%2$d" data-lily-image-input>', esc_attr( $name ), $image_id );
	echo '<div class="lily-image-preview" data-lily-image-preview>';
	if ( $image_id ) {
		echo wp_get_attachment_image( $image_id, 'thumbnail' );
	}
	echo '</div>';
	echo '<button type="button" class="button" data-lily-image-select>' . esc_html__( 'Choose Image', 'lily' ) . '</button> ';
	echo '<button type="button" class="button" data-lily-image-remove>' . esc_html__( 'Remove', 'lily' ) . '</button>';
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
	$text = isset( $item['text'] ) ? $item['text'] : '';
	$link = isset( $item['link'] ) && is_array( $item['link'] ) ? $item['link'] : array();

	echo '<div class="lily-announcement-row">';
	echo '<span class="dashicons dashicons-move"></span>';
	printf( '<input type="text" name="lily_homepage[announcement_items][%1$s][text]" value="%2$s" placeholder="%3$s">', esc_attr( $index ), esc_attr( $text ), esc_attr__( 'Announcement message', 'lily' ) );
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
	$enabled     = ! empty( $slide['enabled'] );
	$image       = absint( $slide['image'] ?? 0 );
	$mobile      = absint( $slide['mobile_image'] ?? 0 );
	$title       = $slide['title'] ?? '';
	$description = $slide['description'] ?? '';
	$cta_text    = $slide['cta_text'] ?? '';
	$cta_url     = $slide['cta_url'] ?? '';

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
	echo '</div>';
	echo '</div>';

	printf( '<input type="text" name="lily_homepage[hero_slides][%1$s][title]" value="%2$s" placeholder="%3$s">', esc_attr( $index ), esc_attr( $title ), esc_attr__( 'Slide title', 'lily' ) );
	printf( '<textarea name="lily_homepage[hero_slides][%1$s][description]" rows="2" placeholder="%2$s">%3$s</textarea>', esc_attr( $index ), esc_attr__( 'Short description', 'lily' ), esc_textarea( $description ) );
	echo '<div class="lily-hero-row__cta">';
	printf( '<input type="text" name="lily_homepage[hero_slides][%1$s][cta_text]" value="%2$s" placeholder="%3$s">', esc_attr( $index ), esc_attr( $cta_text ), esc_attr__( 'CTA text', 'lily' ) );
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
	lily_admin_taxonomy_checkboxes( 'product_cat', 'collections_to_display', $settings['collections_to_display'] ?? array(), esc_html__( 'Collections to Display', 'lily' ) );
	echo '</section>';
}

function lily_render_color_fields( $settings ) {
	echo '<section id="lily-tab-colors" class="lily-admin-panel"><h2>' . esc_html__( 'Shop by Colors', 'lily' ) . '</h2>';
	lily_admin_toggle_field( $settings, 'show_shop_by_colors', esc_html__( 'Show Shop by Colors', 'lily' ) );
	lily_admin_text_field( $settings, 'colors_heading', esc_html__( 'Colors Heading', 'lily' ) );
	lily_admin_textarea_field( $settings, 'colors_description', esc_html__( 'Colors Description', 'lily' ) );
	lily_admin_taxonomy_checkboxes( 'pa_color', 'colors_to_display', $settings['colors_to_display'] ?? array(), esc_html__( 'Colors to Display', 'lily' ) );
	lily_admin_link_field( $settings, 'view_all_colors_link', esc_html__( 'View All Colors Link', 'lily' ) );
	echo '</section>';
}

function lily_render_best_seller_fields( $settings ) {
	$product_ids = isset( $settings['best_sellers_products'] ) && is_array( $settings['best_sellers_products'] ) ? array_values( $settings['best_sellers_products'] ) : array();

	echo '<section id="lily-tab-best" class="lily-admin-panel"><h2>' . esc_html__( 'Best Sellers', 'lily' ) . '</h2>';
	lily_admin_toggle_field( $settings, 'show_best_sellers', esc_html__( 'Show Best Sellers', 'lily' ) );
	lily_admin_text_field( $settings, 'best_sellers_heading', esc_html__( 'Best Sellers Heading', 'lily' ) );
	lily_admin_textarea_field( $settings, 'best_sellers_description', esc_html__( 'Best Sellers Description', 'lily' ) );
	echo '<p class="description">' . esc_html__( 'Pick the products to feature and drag them into the display order. Leave empty to hide the section.', 'lily' ) . '</p>';
	echo '<div class="lily-best-rows" data-lily-best-rows>';
	foreach ( $product_ids as $index => $product_id ) {
		lily_render_best_seller_row( absint( $product_id ), $index );
	}
	echo '</div><button type="button" class="button button-secondary" data-lily-add-best>' . esc_html__( 'Add Product', 'lily' ) . '</button>';
	echo '<script type="text/html" id="tmpl-lily-best-row">';
	lily_render_best_seller_row( 0, '__INDEX__' );
	echo '</script></section>';
}

function lily_render_best_seller_row( $product_id, $index ) {
	$products = get_posts(
		array(
			'post_type'        => 'product',
			'post_status'      => 'publish',
			'numberposts'      => -1,
			'orderby'          => 'title',
			'order'            => 'ASC',
			'suppress_filters' => false,
		)
	);

	echo '<div class="lily-best-row">';
	echo '<span class="dashicons dashicons-move"></span>';
	printf( '<select name="lily_homepage[best_sellers_products][%1$s]">', esc_attr( $index ) );
	echo '<option value="0">' . esc_html__( 'Select product…', 'lily' ) . '</option>';
	foreach ( $products as $product_post ) {
		printf(
			'<option value="%1$d"%2$s>%3$s</option>',
			absint( $product_post->ID ),
			selected( absint( $product_id ), absint( $product_post->ID ), false ),
			esc_html( $product_post->post_title )
		);
	}
	echo '</select>';
	echo '<button type="button" class="button-link-delete" data-lily-remove-best>' . esc_html__( 'Remove', 'lily' ) . '</button>';
	echo '</div>';
}

function lily_render_lens_finder_fields( $settings ) {
	echo '<section id="lily-tab-lens" class="lily-admin-panel"><h2>' . esc_html__( 'Find Your Best Lenses', 'lily' ) . '</h2>';
	lily_admin_toggle_field( $settings, 'show_lens_finder', esc_html__( 'Show Find Your Best Lenses', 'lily' ) );

	echo '<h3>' . esc_html__( 'Lens Finder Content', 'lily' ) . '</h3>';
	lily_admin_text_field( $settings, 'lens_finder_eyebrow', esc_html__( 'Eyebrow', 'lily' ) );
	lily_admin_text_field( $settings, 'lens_finder_heading', esc_html__( 'Section Heading', 'lily' ) );
	lily_admin_textarea_field( $settings, 'lens_finder_description', esc_html__( 'Section Description', 'lily' ) );
	lily_admin_text_field( $settings, 'lens_finder_start_text', esc_html__( 'CTA Button Text', 'lily' ) );

	echo '<h3>' . esc_html__( 'Lens Finder Image', 'lily' ) . '</h3>';
	lily_admin_image_field( $settings, 'lens_finder_image', esc_html__( 'Image', 'lily' ) );
	lily_admin_text_field( $settings, 'lens_finder_image_alt', esc_html__( 'Image Alt Text', 'lily' ) );

	echo '<h3>' . esc_html__( 'How It Works (4 Steps)', 'lily' ) . '</h3>';
	$lily_steps = isset( $settings['lens_finder_steps'] ) && is_array( $settings['lens_finder_steps'] ) ? $settings['lens_finder_steps'] : array();
	for ( $lily_i = 0; $lily_i < 4; $lily_i++ ) {
		$lily_step = isset( $lily_steps[ $lily_i ] ) && is_array( $lily_steps[ $lily_i ] ) ? $lily_steps[ $lily_i ] : array();
		echo '<fieldset class="lily-link-field"><legend>' . esc_html( sprintf( __( 'Step %d', 'lily' ), $lily_i + 1 ) ) . '</legend>';
		printf( '<input type="text" name="lily_homepage[lens_finder_steps][%1$d][number]" value="%2$s" placeholder="%3$s" style="max-width:90px">', absint( $lily_i ), esc_attr( $lily_step['number'] ?? '' ), esc_attr__( 'Number', 'lily' ) );
		printf( '<input type="text" name="lily_homepage[lens_finder_steps][%1$d][title]" value="%2$s" placeholder="%3$s">', absint( $lily_i ), esc_attr( $lily_step['title'] ?? '' ), esc_attr__( 'Title', 'lily' ) );
		printf( '<input type="text" name="lily_homepage[lens_finder_steps][%1$d][description]" value="%2$s" placeholder="%3$s">', absint( $lily_i ), esc_attr( $lily_step['description'] ?? '' ), esc_attr__( 'Description', 'lily' ) );
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

	lily_admin_textarea_field( $settings, 'lens_finder_responsibility_text', esc_html__( 'Responsibility Confirmation Text', 'lily' ) );
	lily_admin_textarea_field( $settings, 'lens_finder_no_results_message', esc_html__( 'No Results Message', 'lily' ) );
	echo '</section>';
}

function lily_admin_taxonomy_checkboxes( $taxonomy, $name, $selected, $label ) {
	$terms    = taxonomy_exists( $taxonomy ) ? get_terms( array( 'taxonomy' => $taxonomy, 'hide_empty' => false ) ) : array();
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
