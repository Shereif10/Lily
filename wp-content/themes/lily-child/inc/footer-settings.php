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
		'brand_tagline_1' => 'Timeless lenses.',
		'brand_tagline_2' => 'Designed for you.',
		'brand_tagline_3' => 'Made to be seen.',
		'shop_heading'    => 'Shop',
		'help_heading'    => 'Help',
		'about_heading'   => 'About',
		'shop_links'      => array(
			array( 'enabled' => 1, 'label' => 'Colored Lenses', 'destination' => 'term:colored-lenses', 'order' => 1 ),
			array( 'enabled' => 1, 'label' => 'Clear Lenses', 'destination' => 'term:clear-lenses', 'order' => 2 ),
			array( 'enabled' => 1, 'label' => 'Best Sellers', 'destination' => 'route:shop', 'order' => 3 ),
			array( 'enabled' => 0, 'label' => 'Sale', 'destination' => 'route:shop', 'order' => 4 ),
		),
		'help_links'      => array(
			array( 'enabled' => 1, 'label' => 'Lens Finder', 'destination' => 'route:find-your-best-lenses', 'order' => 1 ),
			array( 'enabled' => 1, 'label' => 'Shipping & Delivery', 'destination' => 'page:shipping-policy', 'order' => 2 ),
			array( 'enabled' => 1, 'label' => 'Returns & Exchanges', 'destination' => 'page:returns-exchange', 'order' => 3 ),
			array( 'enabled' => 1, 'label' => 'FAQ', 'destination' => 'page:faqs', 'order' => 4 ),
		),
		'about_links'     => array(
			array( 'enabled' => 1, 'label' => 'Contact Us', 'destination' => 'page:contact', 'order' => 1 ),
		),
		'features'        => array(
			array( 'enabled' => 1, 'icon' => 'leaf', 'title' => 'Premium Quality', 'description' => 'High quality lenses with exceptional comfort.', 'order' => 1 ),
			array( 'enabled' => 1, 'icon' => 'shield', 'title' => 'Safe & Secure', 'description' => 'Your data is safe with us. Always.', 'order' => 2 ),
			array( 'enabled' => 1, 'icon' => 'box', 'title' => 'Fast Delivery', 'description' => 'Quick and reliable shipping to your doorstep.', 'order' => 3 ),
		),
		'newsletter_enabled' => 1,
		'newsletter_heading' => 'Stay in the know',
		'newsletter_text'    => 'Join our community and get 10% off your first order.',
		'newsletter_placeholder' => 'Your email address',
		'newsletter_agreement'   => 'By subscribing, you agree to our Privacy Policy and consent to receive updates from Lily.',
		'social_instagram'   => array( 'enabled' => 0, 'url' => '' ),
		'social_tiktok'      => array( 'enabled' => 0, 'url' => '' ),
		'social_pinterest'   => array( 'enabled' => 0, 'url' => '' ),
		'social_youtube'     => array( 'enabled' => 0, 'url' => '' ),
		'bottom_enabled'     => 1,
		'copyright_text'     => '',
		'bottom_links'       => array(
			array( 'enabled' => 1, 'label' => 'Terms & Conditions', 'destination' => 'page:terms-conditions', 'order' => 1 ),
			array( 'enabled' => 1, 'label' => 'Privacy Policy', 'destination' => 'page:privacy', 'order' => 2 ),
			array( 'enabled' => 1, 'label' => 'Accessibility', 'destination' => 'page:accessibility', 'order' => 3 ),
			array( 'enabled' => 1, 'label' => 'Contact Us', 'destination' => 'page:contact', 'order' => 4 ),
		),
	);
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
 * Resolve one saved destination to a real URL.
 *
 * @param string $destination Saved destination token.
 * @return string Real permalink, or '' when the target does not exist.
 */
function lily_footer_resolve_destination( $destination ) {
	$destination = trim( (string) $destination );

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

			<h2><?php esc_html_e( 'Brand', 'lily' ); ?></h2>
			<?php lily_admin_image_field( $settings, 'brand_logo', esc_html__( 'Logo', 'lily' ) ); ?>
			<label><span><?php esc_html_e( 'Tagline Line 1', 'lily' ); ?></span><input type="text" name="lily_footer[brand_tagline_1]" value="<?php echo esc_attr( $settings['brand_tagline_1'] ); ?>"></label>
			<label><span><?php esc_html_e( 'Tagline Line 2', 'lily' ); ?></span><input type="text" name="lily_footer[brand_tagline_2]" value="<?php echo esc_attr( $settings['brand_tagline_2'] ); ?>"></label>
			<label><span><?php esc_html_e( 'Tagline Line 3', 'lily' ); ?></span><input type="text" name="lily_footer[brand_tagline_3]" value="<?php echo esc_attr( $settings['brand_tagline_3'] ); ?>"></label>

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
					<label><span><?php esc_html_e( 'Description', 'lily' ); ?></span><input type="text" name="lily_footer[features][<?php echo esc_attr( $lily_index ); ?>][description]" value="<?php echo esc_attr( $lily_feature['description'] ?? '' ); ?>"></label>
					<label><span><?php esc_html_e( 'Order', 'lily' ); ?></span><input type="number" name="lily_footer[features][<?php echo esc_attr( $lily_index ); ?>][order]" value="<?php echo esc_attr( $lily_feature['order'] ?? $lily_index + 1 ); ?>"></label>
				</fieldset>
			<?php endforeach; ?>

			<h2><?php esc_html_e( 'Newsletter', 'lily' ); ?></h2>
			<label class="lily-toggle"><input type="checkbox" name="lily_footer[newsletter_enabled]" value="1" <?php checked( ! empty( $settings['newsletter_enabled'] ) ); ?>> <span><?php esc_html_e( 'Enable Newsletter', 'lily' ); ?></span></label>
			<label><span><?php esc_html_e( 'Newsletter Heading', 'lily' ); ?></span><input type="text" name="lily_footer[newsletter_heading]" value="<?php echo esc_attr( $settings['newsletter_heading'] ); ?>"></label>
			<label><span><?php esc_html_e( 'Newsletter Description', 'lily' ); ?></span><input type="text" name="lily_footer[newsletter_text]" value="<?php echo esc_attr( $settings['newsletter_text'] ); ?>"></label>
			<label><span><?php esc_html_e( 'Email Placeholder', 'lily' ); ?></span><input type="text" name="lily_footer[newsletter_placeholder]" value="<?php echo esc_attr( $settings['newsletter_placeholder'] ); ?>"></label>
			<label><span><?php esc_html_e( 'Agreement Text', 'lily' ); ?></span><textarea name="lily_footer[newsletter_agreement]" rows="2"><?php echo esc_textarea( $settings['newsletter_agreement'] ); ?></textarea></label>

			<h2><?php esc_html_e( 'Social', 'lily' ); ?></h2>
			<?php
			$lily_socials = array( 'instagram' => 'Instagram', 'tiktok' => 'TikTok', 'pinterest' => 'Pinterest', 'youtube' => 'YouTube' );
			foreach ( $lily_socials as $lily_social_key => $lily_social_label ) :
				$lily_social = $settings[ 'social_' . $lily_social_key ];
				?>
				<fieldset class="lily-link-field">
					<legend><?php echo esc_html( $lily_social_label ); ?></legend>
					<label class="lily-inline"><input type="checkbox" name="lily_footer[social_<?php echo esc_attr( $lily_social_key ); ?>][enabled]" value="1" <?php checked( ! empty( $lily_social['enabled'] ) ); ?>> <?php esc_html_e( 'Enabled', 'lily' ); ?></label>
					<label><span>URL</span><input type="url" name="lily_footer[social_<?php echo esc_attr( $lily_social_key ); ?>][url]" value="<?php echo esc_attr( $lily_social['url'] ); ?>"></label>
				</fieldset>
			<?php endforeach; ?>

			<h2><?php esc_html_e( 'Bottom Bar', 'lily' ); ?></h2>
			<label class="lily-toggle"><input type="checkbox" name="lily_footer[bottom_enabled]" value="1" <?php checked( ! empty( $settings['bottom_enabled'] ) ); ?>> <span><?php esc_html_e( 'Enable Legal Links', 'lily' ); ?></span></label>
			<label><span><?php esc_html_e( 'Copyright Text', 'lily' ); ?></span><input type="text" name="lily_footer[copyright_text]" value="<?php echo esc_attr( $settings['copyright_text'] ); ?>" placeholder="© 2025 Lily. All rights reserved."></label>
			<div class="lily-footer-link-rows" data-lily-link-rows>
				<?php
				foreach ( $settings['bottom_links'] as $lily_row ) {
					lily_footer_link_row_fields( $lily_row, $destinations, 'bottom_links' );
				}
				?>
			</div>
			<button type="button" class="button button-secondary" data-lily-add-link-row data-group="bottom_links"><?php esc_html_e( '+ Add Link', 'lily' ); ?></button>

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
	$row = wp_parse_args( is_array( $row ) ? $row : array(), array( 'enabled' => 0, 'label' => '', 'destination' => '', 'order' => 0 ) );
	echo '<fieldset class="lily-link-field lily-footer-link-row">';
	echo '<label class="lily-inline"><input type="checkbox" name="lily_footer[' . esc_attr( $group ) . '][' . esc_attr( $row['order'] ) . '][enabled]" value="1" ' . checked( ! empty( $row['enabled'] ), true, false ) . '> ' . esc_html__( 'Enabled', 'lily' ) . '</label>';
	printf( '<label><span>%1$s</span><input type="text" name="lily_footer[%2$s][%3$s][label]" value="%4$s"></label>', esc_html__( 'Label', 'lily' ), esc_attr( $group ), esc_attr( $row['order'] ), esc_attr( $row['label'] ) );
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

	foreach ( array( 'brand_tagline_1', 'brand_tagline_2', 'brand_tagline_3', 'shop_heading', 'help_heading', 'about_heading', 'newsletter_heading', 'newsletter_text', 'newsletter_placeholder', 'copyright_text' ) as $field ) {
		$data[ $field ] = sanitize_text_field( $raw[ $field ] ?? '' );
	}

	$data['newsletter_agreement'] = sanitize_textarea_field( $raw['newsletter_agreement'] ?? '' );

	foreach ( array( 'newsletter_enabled', 'bottom_enabled' ) as $toggle ) {
		$data[ $toggle ] = ! empty( $raw[ $toggle ] ) ? 1 : 0;
	}

	foreach ( array( 'instagram', 'tiktok', 'pinterest', 'youtube' ) as $network ) {
		$data[ 'social_' . $network ] = array(
			'enabled' => ! empty( $raw[ 'social_' . $network ]['enabled'] ) ? 1 : 0,
			'url'     => esc_url_raw( $raw[ 'social_' . $network ]['url'] ?? '' ),
		);
	}

	$data['features'] = array();
	foreach ( array_slice( (array) ( $raw['features'] ?? array() ), 0, 3 ) as $feature ) {
		$data['features'][] = array(
			'enabled'     => ! empty( $feature['enabled'] ) ? 1 : 0,
			'icon'        => in_array( $feature['icon'] ?? '', array( 'leaf', 'shield', 'box', 'eye' ), true ) ? $feature['icon'] : 'leaf',
			'title'       => sanitize_text_field( $feature['title'] ?? '' ),
			'description' => sanitize_text_field( $feature['description'] ?? '' ),
			'order'       => absint( $feature['order'] ?? 0 ),
		);
	}

	foreach ( array( 'shop_links', 'help_links', 'about_links', 'bottom_links' ) as $group ) {
		$rows = array();
		foreach ( (array) ( $raw[ $group ] ?? array() ) as $row ) {
			$rows[] = array(
				'enabled'     => ! empty( $row['enabled'] ) ? 1 : 0,
				'label'       => sanitize_text_field( $row['label'] ?? '' ),
				'destination' => sanitize_text_field( $row['destination'] ?? '' ),
				'order'       => absint( $row['order'] ?? 0 ),
			);
		}
		$data[ $group ] = $rows;
	}

	update_option( 'lily_footer_settings', wp_parse_args( $data, $defaults ), false );

	wp_safe_redirect( add_query_arg( 'updated', 'true', menu_page_url( 'lily-footer', false ) ) );
	exit;
}
add_action( 'admin_post_lily_save_footer_settings', 'lily_save_footer_settings' );
