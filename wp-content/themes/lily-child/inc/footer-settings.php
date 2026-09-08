<?php
/**
 * Footer Settings — dashboard-controlled footer content.
 *
 * Reuses the Lily admin architecture (media uploader, sanitizers, save flow).
 *
 * @package Lily
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Footer settings defaults.
 *
 * @return array
 */
function lily_footer_settings_defaults() {
	return array(
		'brand_logo'      => 0,
		'since_text'      => 'Since 2014',
		'since_text_ar'   => '',
		'brand_tagline_1' => '',
		'brand_tagline_1_ar' => '',
		'brand_tagline_2' => '',
		'brand_tagline_2_ar' => '',
		'brand_tagline_3' => '',
		'brand_tagline_3_ar' => '',
		'cta_label'       => '',
		'cta_label_ar'    => '',
		'shop_heading'    => 'Shop',
		'shop_heading_ar' => '',
		'help_heading'    => 'Help',
		'help_heading_ar' => '',
		'about_heading'   => 'Company',
		'about_heading_ar' => '',
		'contact_heading' => 'Contact',
		'contact_heading_ar' => '',
		'whatsapp_label'  => 'WhatsApp',
		'whatsapp_label_ar' => '',
		'whatsapp_number' => '01060760098',
		'service_label'   => 'Customer Service & Complaints',
		'service_label_ar' => '',
		'service_number'  => '01060760098',
		'shop_links'      => array(
			array( 'enabled' => 1, 'label' => 'Colored Lenses', 'label_ar' => 'عدسات ملونة', 'destination' => 'term:colored-lenses', 'order' => 1 ),
			array( 'enabled' => 1, 'label' => 'Clear Lenses', 'label_ar' => 'عدسات شفافة', 'destination' => 'term:clear-lenses', 'order' => 2 ),
			array( 'enabled' => 1, 'label' => 'Best Sellers', 'label_ar' => 'الأكثر مبيعاً', 'destination' => 'route:shop', 'order' => 3 ),
			array( 'enabled' => 0, 'label' => 'Sale', 'label_ar' => 'خصم', 'destination' => 'route:shop', 'order' => 4 ),
		),
		'help_links'      => array(
			array( 'enabled' => 1, 'label' => 'Lens Finder', 'label_ar' => 'دليل اختيار العدسات', 'destination' => 'route:find-your-best-lenses', 'order' => 1 ),
			array( 'enabled' => 1, 'label' => 'Shipping & Delivery', 'label_ar' => 'الشحن والتوصيل', 'destination' => 'page:shipping-policy', 'order' => 2 ),
			array( 'enabled' => 1, 'label' => 'Returns & Exchanges', 'label_ar' => 'الاسترجاع والاستبدال', 'destination' => 'page:returns-exchange', 'order' => 3 ),
			array( 'enabled' => 1, 'label' => 'FAQ', 'label_ar' => 'الأسئلة الشائعة', 'destination' => 'page:faqs', 'order' => 4 ),
		),
		'about_links'     => array(
			array( 'enabled' => 1, 'label' => 'Contact Us', 'label_ar' => 'تواصلي معنا', 'destination' => 'page:contact', 'order' => 1 ),
		),
		'features'        => array(
			array( 'enabled' => 1, 'icon' => 'leaf', 'title' => 'Premium Quality', 'title_ar' => 'جودة فاخرة', 'description' => 'High quality lenses with exceptional comfort.', 'description_ar' => 'عدسات عالية الجودة براحة استثنائية.', 'order' => 1 ),
			array( 'enabled' => 1, 'icon' => 'shield', 'title' => 'Safe & Secure', 'title_ar' => 'آمن ومضمون', 'description' => 'Your data is safe with us. Always.', 'description_ar' => 'بياناتك في أمان معنا. دايماً.', 'order' => 2 ),
			array( 'enabled' => 1, 'icon' => 'box', 'title' => 'Fast Delivery', 'title_ar' => 'توصيل سريع', 'description' => 'Quick and reliable shipping to your doorstep.', 'description_ar' => 'شحن سريع وموثوق لحد باب البيت.', 'order' => 3 ),
		),
		'newsletter_enabled' => 1,
		'newsletter_heading' => 'Stay in the know',
		'newsletter_heading_ar' => 'خليكِ على اطلاع',
		'newsletter_text'    => 'Join our community and get 10% off your first order.',
		'newsletter_text_ar' => 'انضمي لمجتمعنا واحصلي على خصم ١٠٪ على أول طلب.',
		'newsletter_placeholder' => 'Your email address',
		'newsletter_placeholder_ar' => 'بريدك الإلكتروني',
		'newsletter_agreement'   => 'By subscribing, you agree to our Privacy Policy and consent to receive updates from Lily.',
		'newsletter_agreement_ar' => 'بالاشتراك، أنتِ توافقين على سياسة الخصوصية وتوافقين على استلام تحديثات من ليلي.',
		'social_facebook'    => array( 'enabled' => 1, 'label' => 'Facebook', 'label_ar' => '', 'url' => 'https://www.facebook.com/share/1BhEmgfAvG/?mibextid=wwXIfr' ),
		'social_facebook_group' => array( 'enabled' => 1, 'label' => 'Facebook Group', 'label_ar' => '', 'url' => 'https://www.facebook.com/share/g/195YLb3DNv/?mibextid=wwXIfr' ),
		'social_tiktok'      => array( 'enabled' => 1, 'label' => 'TikTok', 'label_ar' => '', 'url' => 'https://www.tiktok.com/@lily_original_lenses?_r=1&_t=ZS-992IHcql4B6' ),
		'social_youtube'     => array( 'enabled' => 1, 'label' => 'YouTube', 'label_ar' => '', 'url' => 'https://youtube.com/@lilyoriginallenses?si=g5aTwOvLzAkibB8c' ),
		'social_instagram'   => array( 'enabled' => 1, 'label' => 'Instagram', 'label_ar' => '', 'url' => 'https://www.instagram.com/lily_original_lenses/' ),
		'social_pinterest'   => array( 'enabled' => 0, 'label' => 'Pinterest', 'label_ar' => '', 'url' => '' ),
		'bottom_enabled'     => 1,
		'copyright_text'     => '',
		'copyright_text_ar'  => '',
		'bottom_note'        => 'Cash on Delivery Available',
		'bottom_note_ar'     => '',
		'bottom_links'       => array(
			array( 'enabled' => 1, 'label' => 'Terms & Conditions', 'label_ar' => 'الشروط والأحكام', 'destination' => 'page:terms-conditions', 'order' => 1 ),
			array( 'enabled' => 1, 'label' => 'Privacy Policy', 'label_ar' => 'سياسة الخصوصية', 'destination' => 'page:privacy', 'order' => 2 ),
			array( 'enabled' => 1, 'label' => 'Accessibility', 'label_ar' => 'سهولة الوصول', 'destination' => 'page:accessibility', 'order' => 3 ),
			array( 'enabled' => 1, 'label' => 'Contact Us', 'label_ar' => 'تواصلي معنا', 'destination' => 'page:contact', 'order' => 4 ),
		),
	);
}

/**
 * Pick the Arabic variant of a footer string when appropriate.
 *
 * Saved Arabic wins; blank Arabic falls back to the shared Arabic
 * dictionary (real Arabic for known Lily strings), then to English.
 *
 * @param mixed $value_en English value.
 * @param mixed $value_ar Arabic value.
 * @return mixed
 */
function lily_footer_ml( $value_en, $value_ar = '' ) {
	if ( function_exists( 'lily_is_arabic_request' ) && lily_is_arabic_request() ) {
		if ( is_scalar( $value_ar ) && '' !== trim( (string) $value_ar ) ) {
			return $value_ar;
		}

		if ( is_string( $value_en ) && '' !== trim( $value_en ) && function_exists( 'lily_ar_fallback' ) ) {
			return lily_ar_fallback( $value_en );
		}
	}

	return $value_en;
}

/**
 * Read footer settings with defaults merged.
 *
 * @return array
 */
function lily_get_footer_settings() {
	$settings = get_option( 'lily_footer_settings', array() );

	return is_array( $settings ) && $settings
		? wp_parse_args( $settings, lily_footer_settings_defaults() )
		: lily_footer_settings_defaults();
}

/**
 * One-time seed: fill the SAVED footer option's social destinations and
 * WhatsApp number with the official Lily values when they are still empty.
 * Runs once; afterwards the dashboard fully controls every value.
 *
 * @return void
 */
function lily_footer_seed_social_values() {
	if ( get_option( 'lily_footer_social_seeded' ) ) {
		return;
	}

	$saved    = get_option( 'lily_footer_settings', array() );
	$saved    = is_array( $saved ) ? $saved : array();
	$defaults = lily_footer_settings_defaults();
	$changed  = false;

	foreach ( array( 'social_instagram', 'social_facebook', 'social_facebook_group', 'social_tiktok', 'social_youtube' ) as $key ) {
		$saved_social = isset( $saved[ $key ] ) && is_array( $saved[ $key ] ) ? $saved[ $key ] : array();

		if ( empty( $saved_social['url'] ) ) {
			$saved[ $key ]                  = $defaults[ $key ];
			$saved[ $key ]['label_ar']      = $saved_social['label_ar'] ?? '';
			$saved[ $key ]['label']         = $saved_social['label'] ?? $defaults[ $key ]['label'];
			$changed                        = true;
		}
	}

	if ( empty( $saved['whatsapp_number'] ) ) {
		$saved['whatsapp_number'] = $defaults['whatsapp_number'];
		$changed                  = true;
	}

	if ( $changed ) {
		update_option( 'lily_footer_settings', $saved );
	}

	update_option( 'lily_footer_social_seeded', 1 );
}
add_action( 'init', 'lily_footer_seed_social_values', 5 );

/**
 * Destination options: real pages + WooCommerce categories + internal routes.
 *
 * @return array[] value => label
 */
function lily_footer_destination_options() {
	$options = array(
		'route:shop'                  => __( 'Shop', 'lily' ),
		'route:find-your-best-lenses' => __( 'Lens Finder', 'lily' ),
	);

	$pages = get_pages( array( 'post_status' => 'publish', 'sort_column' => 'post_title' ) );
	foreach ( (array) $pages as $page ) {
		$options[ 'page:' . $page->post_name ] = 'Page — ' . $page->post_title;
	}

	if ( taxonomy_exists( 'product_cat' ) ) {
		foreach ( get_terms( array( 'taxonomy' => 'product_cat', 'hide_empty' => false ) ) as $term ) {
			if ( ! is_wp_error( $term ) ) {
				$options[ 'term:' . $term->slug ] = 'Category — ' . $term->name;
			}
		}
	}

	return $options;
}

/**
 * WhatsApp click-to-chat URL from a (local or international) number.
 *
 * @param string $number Phone number.
 * @return string
 */
function lily_footer_whatsapp_url( $number ) {
	$digits = preg_replace( '/\D+/', '', (string) $number );

	if ( '' === $digits ) {
		return '';
	}

	/* Egyptian local numbers (starting 01) get the +20 country prefix. */
	if ( 0 === strpos( $digits, '0' ) ) {
		$digits = '20' . ltrim( $digits, '0' );
	}

	return 'https://wa.me/' . $digits;
}

/**
 * Click-to-call URL from a (local or international) number.
 *
 * @param string $number Phone number.
 * @return string
 */
function lily_footer_tel_url( $number ) {
	$digits = preg_replace( '/\D+/', '', (string) $number );

	if ( '' === $digits ) {
		return '';
	}

	if ( 0 === strpos( $digits, '0' ) ) {
		$digits = '20' . ltrim( $digits, '0' );
	}

	return 'tel:+' . $digits;
}

/**
 * Resolve one saved destination to a real URL.
 *
 * @param string $destination Saved destination token.
 * @return string Real permalink, or '' when the target does not exist.
 */
function lily_footer_resolve_destination( $destination ) {	$destination = trim( (string) $destination );

	if ( '' === $destination ) {
		return '';
	}

	if ( 0 === strpos( $destination, 'page:' ) ) {
		$page = get_page_by_path( substr( $destination, 5 ) );

		return ( $page && 'publish' === $page->post_status ) ? get_permalink( $page ) : '';
	}

	if ( 0 === strpos( $destination, 'term:' ) ) {
		$term = get_term_by( 'slug', substr( $destination, 5 ), 'product_cat' );

		return ( $term && ! is_wp_error( $term ) ) ? get_term_link( $term ) : '';
	}

	if ( 0 === strpos( $destination, 'route:' ) ) {
		$route = substr( $destination, 6 );

		if ( 'shop' === $route && function_exists( 'wc_get_page_permalink' ) ) {
			return wc_get_page_permalink( 'shop' );
		}

		return home_url( '/#' . $route );
	}

	return esc_url_raw( $destination );
}

/**
 * Order + filter link rows.
 *
 * @param array $rows   Saved rows.
 * @param array $defaults Default rows used for empty groups.
 * @return array[]
 */
function lily_footer_prepare_rows( $rows, $defaults = array() ) {
	$rows = is_array( $rows ) && $rows ? $rows : $defaults;

	$rows = array_values(
		array_filter(
			$rows,
			static function ( $row ) {
				return is_array( $row ) && ! empty( $row['enabled'] ) && '' !== trim( (string) ( $row['label'] ?? '' ) );
			}
		)
	);

	// Resolve the display label per active language without mutating URLs,
	// order or enabled flags. Saved Arabic wins; blank Arabic consults the
	// shared dictionary; unknown custom labels stay in English.
	foreach ( $rows as &$row ) {
		if ( function_exists( 'lily_footer_ml' ) ) {
			$row['label'] = lily_footer_ml( $row['label'] ?? '', $row['label_ar'] ?? '' );
		}
	}
	unset( $row );

	usort(
		$rows,
		static function ( $a, $b ) {
			return ( absint( $a['order'] ?? 0 ) <=> absint( $b['order'] ?? 0 ) );
		}
	);

	return $rows;
}

/**
 * Register the Footer Settings admin page.
 */
function lily_register_footer_settings_page() {
	add_submenu_page(
		'lily',
		esc_html__( 'Footer Settings', 'lily' ),
		esc_html__( 'Footer Settings', 'lily' ),
		'manage_options',
		'lily-footer',
		'lily_render_footer_settings_page'
	);
}
add_action( 'admin_menu', 'lily_register_footer_settings_page' );

/**
 * Render the Footer Settings page.
 */
function lily_render_footer_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	if ( ! function_exists( 'submit_button' ) ) {
		require_once ABSPATH . 'wp-admin/includes/template.php';
	}

	$settings  = lily_get_footer_settings();
	$destinations = lily_footer_destination_options();
	?>
	<div class="wrap lily-admin">
		<h1><?php esc_html_e( 'Footer Settings', 'lily' ); ?></h1>
		<?php if ( isset( $_GET['updated'] ) ) : ?>
			<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Footer settings saved.', 'lily' ); ?></p></div>
		<?php endif; ?>

		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="lily_save_footer_settings">
			<?php wp_nonce_field( 'lily_save_footer_settings', 'lily_footer_settings_nonce' ); ?>

			<?php lily_render_footer_fields( $settings, $destinations ); ?>

			<?php submit_button(); ?>
		</form>

		<script>
		(function () {
			var options = <?php echo wp_json_encode( $destinations ); ?>;
			document.querySelectorAll('[data-lily-add-link-row]').forEach(function (button) {
				button.addEventListener('click', function () {
					var group = button.getAttribute('data-group');
					var wrap = button.previousElementSibling;
					var index = wrap.querySelectorAll('fieldset').length;
					var fs = document.createElement('fieldset');
					fs.className = 'lily-link-field lily-footer-link-row';
					var opts = '';
					Object.keys(options).forEach(function (value) {
						opts += '<option value="' + value + '">' + options[value] + '</option>';
					});
					fs.innerHTML =
						'<label class="lily-inline"><input type="checkbox" name="lily_footer[' + group + '][' + index + '][enabled]" value="1" checked> Enabled</label>' +
						'<label><span>Label</span><input type="text" name="lily_footer[' + group + '][' + index + '][label]"></label>' +
						'<label><span>Destination</span><select name="lily_footer[' + group + '][' + index + '][destination]">' + opts + '</select></label>' +
						'<label><span>Order</span><input type="number" name="lily_footer[' + group + '][' + index + '][order]" value="' + (index + 1) + '"></label>';
					wrap.appendChild(fs);
				});
			});
		})();
		</script>
	</div>
	<?php
}

/**
 * Render the footer field set (no page chrome, no form, no inline script).
 *
 * Reused by the legacy Footer Settings page and the simplified Pages →
 * Home Page → Global Site Elements → Footer card. Field names
 * (lily_footer[...]) and the save flow stay canonical.
 *
 * @param array $settings     Footer settings.
 * @param array $destinations Destination options (value => label).
 */
function lily_render_footer_fields( $settings, $destinations ) {
	?>
			<h2><?php esc_html_e( 'Brand', 'lily' ); ?></h2>
			<?php lily_admin_image_field( $settings, 'brand_logo', esc_html__( 'Logo', 'lily' ), 300, 90, 'lily_footer' ); ?>
			<label><span><?php esc_html_e( 'Since text', 'lily' ); ?></span><input type="text" name="lily_footer[since_text]" value="<?php echo esc_attr( $settings['since_text'] ); ?>"></label>
			<label><span><?php esc_html_e( 'Since text (Arabic)', 'lily' ); ?></span><input type="text" name="lily_footer[since_text_ar]" value="<?php echo esc_attr( $settings['since_text_ar'] ?? '' ); ?>" placeholder="<?php esc_attr_e( 'Arabic — blank reuses English', 'lily' ); ?>"></label>
			<label><span><?php esc_html_e( 'CTA label', 'lily' ); ?></span><input type="text" name="lily_footer[cta_label]" value="<?php echo esc_attr( $settings['cta_label'] ); ?>" placeholder="<?php esc_attr_e( 'Blank uses “Find Your Best Lenses”', 'lily' ); ?>"></label>
			<label><span><?php esc_html_e( 'CTA label (Arabic)', 'lily' ); ?></span><input type="text" name="lily_footer[cta_label_ar]" value="<?php echo esc_attr( $settings['cta_label_ar'] ?? '' ); ?>" placeholder="<?php esc_attr_e( 'Arabic — blank reuses English', 'lily' ); ?>"></label>
			<h3><?php esc_html_e( 'Brand — English', 'lily' ); ?></h3>
			<label><span><?php esc_html_e( 'Tagline Line 1', 'lily' ); ?></span><input type="text" name="lily_footer[brand_tagline_1]" value="<?php echo esc_attr( $settings['brand_tagline_1'] ); ?>"></label>
			<label><span><?php esc_html_e( 'Tagline Line 2', 'lily' ); ?></span><input type="text" name="lily_footer[brand_tagline_2]" value="<?php echo esc_attr( $settings['brand_tagline_2'] ); ?>"></label>
			<label><span><?php esc_html_e( 'Tagline Line 3', 'lily' ); ?></span><input type="text" name="lily_footer[brand_tagline_3]" value="<?php echo esc_attr( $settings['brand_tagline_3'] ); ?>"></label>
			<p class="description"><?php esc_html_e( 'Optional three-line brand description. When every line is blank, the footer keeps the site tagline from Settings → General.', 'lily' ); ?></p>
			<h3><?php esc_html_e( 'Brand', 'lily' ); ?> — <?php esc_html_e( 'العربية', 'lily' ); ?></h3>
			<p class="description"><?php esc_html_e( 'Shown on Arabic pages only. Leave any field blank to reuse its English text.', 'lily' ); ?></p>
			<label><span><?php esc_html_e( 'Tagline Line 1 (Arabic)', 'lily' ); ?></span><input type="text" name="lily_footer[brand_tagline_1_ar]" value="<?php echo esc_attr( $settings['brand_tagline_1_ar'] ?? '' ); ?>"></label>
			<label><span><?php esc_html_e( 'Tagline Line 2 (Arabic)', 'lily' ); ?></span><input type="text" name="lily_footer[brand_tagline_2_ar]" value="<?php echo esc_attr( $settings['brand_tagline_2_ar'] ?? '' ); ?>"></label>
			<label><span><?php esc_html_e( 'Tagline Line 3 (Arabic)', 'lily' ); ?></span><input type="text" name="lily_footer[brand_tagline_3_ar]" value="<?php echo esc_attr( $settings['brand_tagline_3_ar'] ?? '' ); ?>"></label>

			<h2><?php esc_html_e( 'Contact', 'lily' ); ?></h2>
			<label><span><?php esc_html_e( 'Contact heading', 'lily' ); ?></span><input type="text" name="lily_footer[contact_heading]" value="<?php echo esc_attr( $settings['contact_heading'] ); ?>"></label>
			<label><span><?php esc_html_e( 'Contact heading (Arabic)', 'lily' ); ?></span><input type="text" name="lily_footer[contact_heading_ar]" value="<?php echo esc_attr( $settings['contact_heading_ar'] ?? '' ); ?>" placeholder="<?php esc_attr_e( 'Arabic — blank reuses English', 'lily' ); ?>"></label>
			<label><span><?php esc_html_e( 'WhatsApp label', 'lily' ); ?></span><input type="text" name="lily_footer[whatsapp_label]" value="<?php echo esc_attr( $settings['whatsapp_label'] ); ?>"></label>
			<label><span><?php esc_html_e( 'WhatsApp label (Arabic)', 'lily' ); ?></span><input type="text" name="lily_footer[whatsapp_label_ar]" value="<?php echo esc_attr( $settings['whatsapp_label_ar'] ?? '' ); ?>" placeholder="<?php esc_attr_e( 'Arabic — blank reuses English', 'lily' ); ?>"></label>
			<label><span><?php esc_html_e( 'WhatsApp number', 'lily' ); ?></span><input type="text" name="lily_footer[whatsapp_number]" value="<?php echo esc_attr( $settings['whatsapp_number'] ); ?>"></label>
			<label><span><?php esc_html_e( 'Customer Service label', 'lily' ); ?></span><input type="text" name="lily_footer[service_label]" value="<?php echo esc_attr( $settings['service_label'] ); ?>"></label>
			<label><span><?php esc_html_e( 'Customer Service label (Arabic)', 'lily' ); ?></span><input type="text" name="lily_footer[service_label_ar]" value="<?php echo esc_attr( $settings['service_label_ar'] ?? '' ); ?>" placeholder="<?php esc_attr_e( 'Arabic — blank reuses English', 'lily' ); ?>"></label>
			<label><span><?php esc_html_e( 'Customer Service number', 'lily' ); ?></span><input type="text" name="lily_footer[service_number]" value="<?php echo esc_attr( $settings['service_number'] ); ?>"></label>
			<p class="description"><?php esc_html_e( 'The WhatsApp link is built from the WhatsApp number (Egyptian numbers starting with 01 get the +20 prefix automatically).', 'lily' ); ?></p>

			<h2><?php esc_html_e( 'Navigation Columns', 'lily' ); ?></h2>
			<?php
			$lily_link_groups = array(
				'shop_links'  => $settings['shop_heading'],
				'help_links'  => $settings['help_heading'],
				'about_links' => $settings['about_heading'],
			);

			foreach ( $lily_link_groups as $lily_group_key => $lily_group_label ) :
				?>
				<h3><?php echo esc_html( $lily_group_label ); ?></h3>
				<label><span><?php esc_html_e( 'Column Heading', 'lily' ); ?></span>
					<input type="text" name="lily_footer[<?php echo esc_attr( $lily_group_key ); ?>_heading]" value="<?php echo esc_attr( $settings[ $lily_group_key . '_heading' ] ); ?>">
				</label>
				<label><span><?php esc_html_e( 'Column Heading (Arabic)', 'lily' ); ?></span>
					<input type="text" name="lily_footer[<?php echo esc_attr( $lily_group_key ); ?>_heading_ar]" value="<?php echo esc_attr( $settings[ $lily_group_key . '_heading_ar' ] ?? '' ); ?>" placeholder="<?php esc_attr_e( 'Arabic — blank reuses English', 'lily' ); ?>">
				</label>
				<div class="lily-footer-link-rows" data-lily-link-rows>
					<?php
					$lily_rows = $settings[ $lily_group_key ];
					foreach ( $lily_rows as $lily_row ) {
						lily_footer_link_row_fields( $lily_row, $destinations, $lily_group_key );
					}
					?>
				</div>
				<button type="button" class="button button-secondary" data-lily-add-link-row data-group="<?php echo esc_attr( $lily_group_key ); ?>"><?php esc_html_e( '+ Add Link', 'lily' ); ?></button>
			<?php endforeach; ?>

			<h2><?php esc_html_e( 'Trust Features', 'lily' ); ?></h2>
			<?php
			foreach ( $settings['features'] as $lily_index => $lily_feature ) :
				$lily_icon_options = array( 'leaf' => 'Leaf', 'shield' => 'Shield', 'box' => 'Box', 'eye' => 'Eye' );
				?>
				<fieldset class="lily-link-field">
					<legend><?php echo esc_html( sprintf( __( 'Feature %d', 'lily' ), $lily_index + 1 ) ); ?></legend>
					<label class="lily-inline"><input type="checkbox" name="lily_footer[features][<?php echo esc_attr( $lily_index ); ?>][enabled]" value="1" <?php checked( ! empty( $lily_feature['enabled'] ) ); ?>> <?php esc_html_e( 'Enabled', 'lily' ); ?></label>
					<label><span><?php esc_html_e( 'Icon', 'lily' ); ?></span>
						<select name="lily_footer[features][<?php echo esc_attr( $lily_index ); ?>][icon]">
							<?php foreach ( $lily_icon_options as $lily_icon_key => $lily_icon_label ) : ?>
								<option value="<?php echo esc_attr( $lily_icon_key ); ?>" <?php selected( $lily_feature['icon'] ?? '', $lily_icon_key ); ?>><?php echo esc_html( $lily_icon_label ); ?></option>
							<?php endforeach; ?>
						</select>
					</label>
					<label><span><?php esc_html_e( 'Title', 'lily' ); ?></span><input type="text" name="lily_footer[features][<?php echo esc_attr( $lily_index ); ?>][title]" value="<?php echo esc_attr( $lily_feature['title'] ?? '' ); ?>"></label>
					<label><span><?php esc_html_e( 'Title (Arabic)', 'lily' ); ?></span><input type="text" name="lily_footer[features][<?php echo esc_attr( $lily_index ); ?>][title_ar]" value="<?php echo esc_attr( $lily_feature['title_ar'] ?? '' ); ?>"></label>
					<label><span><?php esc_html_e( 'Description', 'lily' ); ?></span><input type="text" name="lily_footer[features][<?php echo esc_attr( $lily_index ); ?>][description]" value="<?php echo esc_attr( $lily_feature['description'] ?? '' ); ?>"></label>
					<label><span><?php esc_html_e( 'Description (Arabic)', 'lily' ); ?></span><input type="text" name="lily_footer[features][<?php echo esc_attr( $lily_index ); ?>][description_ar]" value="<?php echo esc_attr( $lily_feature['description_ar'] ?? '' ); ?>"></label>
					<label><span><?php esc_html_e( 'Order', 'lily' ); ?></span><input type="number" name="lily_footer[features][<?php echo esc_attr( $lily_index ); ?>][order]" value="<?php echo esc_attr( $lily_feature['order'] ?? $lily_index + 1 ); ?>"></label>
				</fieldset>
			<?php endforeach; ?>

			<h2><?php esc_html_e( 'Newsletter', 'lily' ); ?></h2>
			<label class="lily-toggle"><input type="checkbox" name="lily_footer[newsletter_enabled]" value="1" <?php checked( ! empty( $settings['newsletter_enabled'] ) ); ?>> <span><?php esc_html_e( 'Enable Newsletter', 'lily' ); ?></span></label>
			<h3><?php esc_html_e( 'Newsletter — English', 'lily' ); ?></h3>
			<label><span><?php esc_html_e( 'Newsletter Heading', 'lily' ); ?></span><input type="text" name="lily_footer[newsletter_heading]" value="<?php echo esc_attr( $settings['newsletter_heading'] ); ?>"></label>
			<label><span><?php esc_html_e( 'Newsletter Description', 'lily' ); ?></span><input type="text" name="lily_footer[newsletter_text]" value="<?php echo esc_attr( $settings['newsletter_text'] ); ?>"></label>
			<label><span><?php esc_html_e( 'Email Placeholder', 'lily' ); ?></span><input type="text" name="lily_footer[newsletter_placeholder]" value="<?php echo esc_attr( $settings['newsletter_placeholder'] ); ?>"></label>
			<label><span><?php esc_html_e( 'Agreement Text', 'lily' ); ?></span><textarea name="lily_footer[newsletter_agreement]" rows="2"><?php echo esc_textarea( $settings['newsletter_agreement'] ); ?></textarea></label>
			<h3><?php esc_html_e( 'Newsletter', 'lily' ); ?> — <?php esc_html_e( 'العربية', 'lily' ); ?></h3>
			<p class="description"><?php esc_html_e( 'Shown on Arabic pages only. Leave any field blank to reuse its English text.', 'lily' ); ?></p>
			<label><span><?php esc_html_e( 'Newsletter Heading (Arabic)', 'lily' ); ?></span><input type="text" name="lily_footer[newsletter_heading_ar]" value="<?php echo esc_attr( $settings['newsletter_heading_ar'] ?? '' ); ?>"></label>
			<label><span><?php esc_html_e( 'Newsletter Description (Arabic)', 'lily' ); ?></span><input type="text" name="lily_footer[newsletter_text_ar]" value="<?php echo esc_attr( $settings['newsletter_text_ar'] ?? '' ); ?>"></label>
			<label><span><?php esc_html_e( 'Email Placeholder (Arabic)', 'lily' ); ?></span><input type="text" name="lily_footer[newsletter_placeholder_ar]" value="<?php echo esc_attr( $settings['newsletter_placeholder_ar'] ?? '' ); ?>"></label>
			<label><span><?php esc_html_e( 'Agreement Text (Arabic)', 'lily' ); ?></span><textarea name="lily_footer[newsletter_agreement_ar]" rows="2"><?php echo esc_textarea( $settings['newsletter_agreement_ar'] ?? '' ); ?></textarea></label>

			<h2><?php esc_html_e( 'Social', 'lily' ); ?></h2>
			<?php
			$lily_socials = array( 'facebook' => 'Facebook', 'facebook_group' => 'Facebook Group', 'tiktok' => 'TikTok', 'youtube' => 'YouTube', 'instagram' => 'Instagram', 'pinterest' => 'Pinterest' );
			foreach ( $lily_socials as $lily_social_key => $lily_social_label ) :
				$lily_social = $settings[ 'social_' . $lily_social_key ];
				?>
				<fieldset class="lily-link-field">
					<legend><?php echo esc_html( $lily_social_label ); ?></legend>
					<label class="lily-inline"><input type="checkbox" name="lily_footer[social_<?php echo esc_attr( $lily_social_key ); ?>][enabled]" value="1" <?php checked( ! empty( $lily_social['enabled'] ) ); ?>> <?php esc_html_e( 'Enabled', 'lily' ); ?></label>
					<label><span><?php esc_html_e( 'Label', 'lily' ); ?></span><input type="text" name="lily_footer[social_<?php echo esc_attr( $lily_social_key ); ?>][label]" value="<?php echo esc_attr( $lily_social['label'] ?? '' ); ?>"></label>
					<label><span><?php esc_html_e( 'Label (Arabic)', 'lily' ); ?></span><input type="text" name="lily_footer[social_<?php echo esc_attr( $lily_social_key ); ?>][label_ar]" value="<?php echo esc_attr( $lily_social['label_ar'] ?? '' ); ?>" placeholder="<?php esc_attr_e( 'Arabic — blank reuses English', 'lily' ); ?>"></label>
					<label><span>URL</span><input type="url" name="lily_footer[social_<?php echo esc_attr( $lily_social_key ); ?>][url]" value="<?php echo esc_attr( $lily_social['url'] ); ?>"></label>
				</fieldset>
			<?php endforeach; ?>

			<h2><?php esc_html_e( 'Bottom Bar', 'lily' ); ?></h2>
			<label class="lily-toggle"><input type="checkbox" name="lily_footer[bottom_enabled]" value="1" <?php checked( ! empty( $settings['bottom_enabled'] ) ); ?>> <span><?php esc_html_e( 'Enable Legal Links', 'lily' ); ?></span></label>
			<label><span><?php esc_html_e( 'Copyright Text', 'lily' ); ?></span><input type="text" name="lily_footer[copyright_text]" value="<?php echo esc_attr( $settings['copyright_text'] ); ?>" placeholder="© 2025 Lily. All rights reserved."></label>
			<label><span><?php esc_html_e( 'Copyright Text (Arabic)', 'lily' ); ?></span><input type="text" name="lily_footer[copyright_text_ar]" value="<?php echo esc_attr( $settings['copyright_text_ar'] ?? '' ); ?>" placeholder="<?php esc_attr_e( 'Arabic — blank reuses English', 'lily' ); ?>"></label>
			<label><span><?php esc_html_e( 'Bottom-right text', 'lily' ); ?></span><input type="text" name="lily_footer[bottom_note]" value="<?php echo esc_attr( $settings['bottom_note'] ); ?>"></label>
			<label><span><?php esc_html_e( 'Bottom-right text (Arabic)', 'lily' ); ?></span><input type="text" name="lily_footer[bottom_note_ar]" value="<?php echo esc_attr( $settings['bottom_note_ar'] ?? '' ); ?>" placeholder="<?php esc_attr_e( 'Arabic — blank reuses English', 'lily' ); ?>"></label>
			<div class="lily-footer-link-rows" data-lily-link-rows>
				<?php
				foreach ( $settings['bottom_links'] as $lily_row ) {
					lily_footer_link_row_fields( $lily_row, $destinations, 'bottom_links' );
				}
				?>
			</div>
			<button type="button" class="button button-secondary" data-lily-add-link-row data-group="bottom_links"><?php esc_html_e( '+ Add Link', 'lily' ); ?></button>
	<?php
}

/**
 * One link row (label + real page/category select + enable + order).
 *
 * @param array  $row          Row values.
 * @param array  $destinations Destination options.
 * @param string $group        Group key.
 */
function lily_footer_link_row_fields( $row, $destinations, $group ) {
	$row = wp_parse_args( is_array( $row ) ? $row : array(), array( 'enabled' => 0, 'label' => '', 'label_ar' => '', 'destination' => '', 'order' => 0 ) );
	echo '<fieldset class="lily-link-field lily-footer-link-row">';
	echo '<label class="lily-inline"><input type="checkbox" name="lily_footer[' . esc_attr( $group ) . '][' . esc_attr( $row['order'] ) . '][enabled]" value="1" ' . checked( ! empty( $row['enabled'] ), true, false ) . '> ' . esc_html__( 'Enabled', 'lily' ) . '</label>';
	printf( '<label><span>%1$s</span><input type="text" name="lily_footer[%2$s][%3$s][label]" value="%4$s"></label>', esc_html__( 'Label', 'lily' ), esc_attr( $group ), esc_attr( $row['order'] ), esc_attr( $row['label'] ) );
	printf( '<label><span>%1$s</span><input type="text" name="lily_footer[%2$s][%3$s][label_ar]" value="%4$s" placeholder="%5$s"></label>', esc_html__( 'Label (Arabic)', 'lily' ), esc_attr( $group ), esc_attr( $row['order'] ), esc_attr( $row['label_ar'] ), esc_attr__( 'Arabic — blank reuses English', 'lily' ) );
	echo '<label><span>' . esc_html__( 'Destination', 'lily' ) . '</span><select name="lily_footer[' . esc_attr( $group ) . '][' . esc_attr( $row['order'] ) . '][destination]">';
	foreach ( $destinations as $value => $label ) {
		echo '<option value="' . esc_attr( $value ) . '" ' . selected( $row['destination'], $value, false ) . '>' . esc_html( $label ) . '</option>';
	}
	echo '</select></label>';
	printf( '<label><span>%1$s</span><input type="number" name="lily_footer[%2$s][%3$s][order]" value="%4$s"></label>', esc_html__( 'Order', 'lily' ), esc_attr( $group ), esc_attr( $row['order'] ), esc_attr( $row['order'] ) );
	echo '</fieldset>';
}

/**
 * Save Footer Settings.
 */
function lily_save_footer_settings() {
	if ( empty( $_POST['lily_footer_settings_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['lily_footer_settings_nonce'] ) ), 'lily_save_footer_settings' ) ) {
		return;
	}

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have permission to edit Lily settings.', 'lily' ) );
	}

	$raw      = isset( $_POST['lily_footer'] ) ? (array) wp_unslash( $_POST['lily_footer'] ) : array();
	$defaults = lily_footer_settings_defaults();
	$data     = array();

	$data['brand_logo'] = absint( $raw['brand_logo'] ?? 0 );

	foreach ( array( 'brand_tagline_1', 'brand_tagline_1_ar', 'brand_tagline_2', 'brand_tagline_2_ar', 'brand_tagline_3', 'brand_tagline_3_ar', 'since_text', 'since_text_ar', 'cta_label', 'cta_label_ar', 'shop_heading', 'shop_heading_ar', 'help_heading', 'help_heading_ar', 'about_heading', 'about_heading_ar', 'contact_heading', 'contact_heading_ar', 'whatsapp_label', 'whatsapp_label_ar', 'service_label', 'service_label_ar', 'newsletter_heading', 'newsletter_heading_ar', 'newsletter_text', 'newsletter_text_ar', 'newsletter_placeholder', 'newsletter_placeholder_ar', 'copyright_text', 'copyright_text_ar', 'bottom_note', 'bottom_note_ar' ) as $field ) {
		$data[ $field ] = sanitize_text_field( $raw[ $field ] ?? '' );
	}

	foreach ( array( 'whatsapp_number', 'service_number' ) as $number_field ) {
		$data[ $number_field ] = sanitize_text_field( $raw[ $number_field ] ?? '' );
	}

	$data['newsletter_agreement']    = sanitize_textarea_field( $raw['newsletter_agreement'] ?? '' );
	$data['newsletter_agreement_ar'] = sanitize_textarea_field( $raw['newsletter_agreement_ar'] ?? '' );

	foreach ( array( 'newsletter_enabled', 'bottom_enabled' ) as $toggle ) {
		$data[ $toggle ] = ! empty( $raw[ $toggle ] ) ? 1 : 0;
	}

	foreach ( array( 'facebook', 'facebook_group', 'tiktok', 'youtube', 'instagram', 'pinterest' ) as $network ) {
		$data[ 'social_' . $network ] = array(
			'enabled' => ! empty( $raw[ 'social_' . $network ]['enabled'] ) ? 1 : 0,
			'label'   => sanitize_text_field( $raw[ 'social_' . $network ]['label'] ?? '' ),
			'label_ar' => sanitize_text_field( $raw[ 'social_' . $network ]['label_ar'] ?? '' ),
			'url'     => esc_url_raw( $raw[ 'social_' . $network ]['url'] ?? '' ),
		);
	}

	$data['features'] = array();
	foreach ( array_slice( (array) ( $raw['features'] ?? array() ), 0, 3 ) as $feature ) {
		$data['features'][] = array(
			'enabled'        => ! empty( $feature['enabled'] ) ? 1 : 0,
			'icon'           => in_array( $feature['icon'] ?? '', array( 'leaf', 'shield', 'box', 'eye' ), true ) ? $feature['icon'] : 'leaf',
			'title'          => sanitize_text_field( $feature['title'] ?? '' ),
			'title_ar'       => sanitize_text_field( $feature['title_ar'] ?? '' ),
			'description'    => sanitize_text_field( $feature['description'] ?? '' ),
			'description_ar' => sanitize_text_field( $feature['description_ar'] ?? '' ),
			'order'          => absint( $feature['order'] ?? 0 ),
		);
	}

	foreach ( array( 'shop_links', 'help_links', 'about_links', 'bottom_links' ) as $group ) {
		$rows = array();
		foreach ( (array) ( $raw[ $group ] ?? array() ) as $row ) {
			$rows[] = array(
				'enabled'     => ! empty( $row['enabled'] ) ? 1 : 0,
				'label'       => sanitize_text_field( $row['label'] ?? '' ),
				'label_ar'    => sanitize_text_field( $row['label_ar'] ?? '' ),
				'destination' => sanitize_text_field( $row['destination'] ?? '' ),
				'order'       => absint( $row['order'] ?? 0 ),
			);
		}
		$data[ $group ] = $rows;
	}

	update_option( 'lily_footer_settings', wp_parse_args( $data, $defaults ), false );

	/* Return to the calling screen (Pages hub embeds this field set too). */
	$return = isset( $_POST['lily_return'] ) ? sanitize_key( wp_unslash( $_POST['lily_return'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing -- nonce verified above.
	$target = '' !== $return ? lily_pages_screen_url( $return ) : menu_page_url( 'lily-footer', false );

	wp_safe_redirect( add_query_arg( 'updated', 'true', $target ) );
	exit;
}
add_action( 'admin_post_lily_save_footer_settings', 'lily_save_footer_settings' );
