<?php
/**
 * Native Navigation Settings admin page (no ACF).
 *
 * @package Lily
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the Navigation submenu under the Lily menu.
 */
function lily_register_navigation_settings_page() {
	add_submenu_page(
		'lily',
		esc_html__( 'Navigation Settings', 'lily' ),
		esc_html__( 'Navigation Settings', 'lily' ),
		'manage_options',
		'lily-navigation',
		'lily_render_navigation_settings_page'
	);
}
add_action( 'admin_menu', 'lily_register_navigation_settings_page' );

/**
 * Save navigation settings.
 */
function lily_save_navigation_settings() {
	if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) {
		return;
	}

	if ( ! isset( $_POST['lily_navigation_settings_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['lily_navigation_settings_nonce'] ) ), 'lily_save_navigation_settings' ) ) {
		return;
	}

	if ( ! isset( $_POST['action'] ) || 'lily_save_navigation' !== $_POST['action'] ) {
		return;
	}

	$raw  = isset( $_POST['lily_nav'] ) ? (array) wp_unslash( $_POST['lily_nav'] ) : array();

	/*
	 * Field map shared by both save paths (same sanitization semantics).
	 * toggle = checkbox (full form: absent means unchecked); text = text/url
	 * input (always posted by the real form); id = absint.
	 */
	$lily_nav_fields = array(
		'logo'              => 'id',
		'show_shop'         => 'toggle',
		'shop_label'        => 'text',
		'shop_label_ar'     => 'text',
		'show_colored'      => 'toggle',
		'colored_label'     => 'text',
		'colored_label_ar'  => 'text',
		'show_clear'        => 'toggle',
		'clear_label'       => 'text',
		'clear_label_ar'    => 'text',
		'show_accessories'  => 'toggle',
		'accessories_label' => 'text',
		'accessories_label_ar' => 'text',
		'accessories_url'   => 'text',
		'show_find'         => 'toggle',
		'find_label'        => 'text',
		'find_label_ar'     => 'text',
		'find_url'          => 'text',
		'show_company'      => 'toggle',
		'company_label'     => 'text',
		'company_label_ar'  => 'text',
		'about_page'        => 'id',
		'faqs_page'         => 'id',
		'contact_page'      => 'id',
	);

	/*
	 * Full navigation form vs partial save: the real form always posts
	 * `shop_label` (text inputs submit even when empty), so its presence
	 * distinguishes a genuine navigation-form save from a partial one
	 * (e.g. a logo-only update). Partial saves merge over the currently
	 * saved settings and can never wipe navigation data.
	 */
	$is_full_form = array_key_exists( 'shop_label', $raw );
	$out          = $is_full_form ? lily_navigation_settings_defaults() : array();

	lily_save_navigation_settings_apply( $out, $raw, $lily_nav_fields, $is_full_form );

	update_option( 'lily_navigation_settings', $out );

	add_settings_error( 'lily_navigation', 'saved', esc_html__( 'Navigation settings saved.', 'lily' ), 'updated' );
}

/**
 * Apply navigation settings values with their sanitization semantics.
 *
 * Full-form save: every field is written exactly as before (checkboxes
 * absent from POST mean unchecked → off).
 *
 * Partial save (e.g. a logo-only update posted from outside the navigation
 * form): only the keys actually present in POST are written, merged over
 * the currently saved settings — navigation data can never be wiped.
 *
 * @param array  $out    Settings array to fill (by reference).
 * @param array  $raw    Posted values.
 * @param array  $fields Field map (key => type).
 * @param bool   $full   Whether this is a full navigation-form save.
 */
function lily_save_navigation_settings_apply( &$out, $raw, $fields, $full ) {
	if ( $full ) {
		// Full navigation form: existing canonical behavior.
		foreach ( $fields as $key => $type ) {
			$out[ $key ] = lily_save_navigation_settings_value( $key, $type, $raw );
		}

		return;
	}

	// Partial save: merge posted keys over the current saved settings.
	$current = get_option( 'lily_navigation_settings', array() );
	$out     = wp_parse_args( is_array( $current ) ? $current : array(), lily_navigation_settings_defaults() );

	foreach ( $fields as $key => $type ) {
		if ( 'toggle' === $type ) {
			continue; // Toggles can only be judged by the full form.
		}

		if ( array_key_exists( $key, $raw ) ) {
			$out[ $key ] = lily_save_navigation_settings_value( $key, $type, $raw );
		}
	}
}

/**
 * Sanitize one navigation settings value.
 *
 * @param string $key  Field key.
 * @param string $type Field type.
 * @param array  $raw  Posted values.
 * @return mixed
 */
function lily_save_navigation_settings_value( $key, $type, $raw ) {
	if ( 'toggle' === $type ) {
		return empty( $raw[ $key ] ) ? 0 : 1;
	}

	if ( 'id' === $type ) {
		return absint( isset( $raw[ $key ] ) ? $raw[ $key ] : 0 );
	}

	if ( 'text' === $type && in_array( $key, array( 'accessories_url', 'find_url' ), true ) ) {
		return esc_url_raw( isset( $raw[ $key ] ) ? $raw[ $key ] : '' );
	}

	return sanitize_text_field( isset( $raw[ $key ] ) ? $raw[ $key ] : '' );
}
add_action( 'admin_init', 'lily_save_navigation_settings' );

/**
 * Render a page selector.
 *
 * @param string $name         Field name.
 * @param int    $selected     Selected page ID.
 * @param string $placeholder  Empty option label.
 */
function lily_nav_page_dropdown( $name, $selected, $placeholder ) {
	wp_dropdown_pages(
		array(
			'name'              => 'lily_nav[' . $name . ']',
			'id'                => 'lily-nav-' . $name,
			'selected'          => (int) $selected,
			'show_option_none'  => $placeholder,
			'option_none_value' => '0',
			'post_status'       => array( 'publish' ),
			'echo'              => 1,
		)
	);
}

/**
 * Render the Navigation Settings page.
 */
function lily_render_navigation_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Lily — Navigation Settings', 'lily' ); ?></h1>

		<p class="description">
			<?php esc_html_e( 'Controls the built-in Lily navbar shown while no WordPress menu is assigned to the Primary Navigation location (Appearance → Menus). Assign a menu there to fully override this structure.', 'lily' ); ?>
		</p>

		<?php settings_errors( 'lily_navigation' ); ?>

		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<?php wp_nonce_field( 'lily_save_navigation_settings', 'lily_navigation_settings_nonce' ); ?>
			<input type="hidden" name="action" value="lily_save_navigation">

			<?php lily_render_navigation_fields(); ?>

			<?php submit_button( esc_html__( 'Save Navigation Settings', 'lily' ) ); ?>
		</form>
	</div>
	<?php
}

/**
 * Render the navigation field set (no page chrome, no form).
 *
 * Reused by the legacy Navigation Settings page and the simplified
 * Pages → Home Page → Global Site Elements → Navbar card. Field names
 * (lily_nav[...]) and the save flow stay canonical.
 */
function lily_render_navigation_fields() {
	$lily_nav_logo_id = absint( lily_nav_get_option( 'logo', 0 ) );
	?>
	<table class="form-table" role="presentation">
		<tr>
			<th scope="row"><?php esc_html_e( 'Navbar Logo', 'lily' ); ?></th>
			<td>
				<div class="lily-image-field">
					<input type="hidden" name="lily_nav[logo]" value="<?php echo esc_attr( $lily_nav_logo_id ); ?>" data-lily-image-input>
					<div class="lily-image-preview" data-lily-image-preview>
						<?php echo $lily_nav_logo_id && 'attachment' === get_post_type( $lily_nav_logo_id ) ? wp_get_attachment_image( $lily_nav_logo_id, 'thumbnail' ) : ''; ?>
					</div>
					<button type="button" class="button" data-lily-image-select><?php esc_html_e( 'Choose Image', 'lily' ); ?></button>
					<button type="button" class="button" data-lily-image-remove><?php esc_html_e( 'Remove', 'lily' ); ?></button>
					<?php lily_image_guidance( 240, 96 ); ?>
					<p class="description"><?php esc_html_e( 'Shown in the website navbar. Leave empty to keep the current WordPress Site Identity logo (or the site title when no logo exists).', 'lily' ); ?></p>
				</div>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php esc_html_e( 'Shop', 'lily' ); ?></th>
			<td>
				<label style="display:block;margin-bottom:8px;">
					<input type="checkbox" name="lily_nav[show_shop]" value="1" <?php checked( lily_nav_get_option( 'show_shop', 1 ) ); ?>>
					<?php esc_html_e( 'Show in navigation', 'lily' ); ?>
				</label>
				<input type="text" class="regular-text" name="lily_nav[shop_label]" value="<?php echo esc_attr( lily_nav_get_option( 'shop_label', 'Shop' ) ); ?>" placeholder="<?php esc_attr_e( 'Label', 'lily' ); ?>">
				<input type="text" class="regular-text" name="lily_nav[shop_label_ar]" value="<?php echo esc_attr( lily_nav_get_option( 'shop_label_ar', '' ) ); ?>" placeholder="<?php esc_attr_e( 'Label (Arabic) — blank reuses English', 'lily' ); ?>">
				<p class="description"><?php esc_html_e( 'Links to the main WooCommerce Shop page (all products).', 'lily' ); ?></p>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php esc_html_e( 'Colored Lenses', 'lily' ); ?></th>
			<td>
				<label style="display:block;margin-bottom:8px;">
					<input type="checkbox" name="lily_nav[show_colored]" value="1" <?php checked( lily_nav_get_option( 'show_colored', 1 ) ); ?>>
					<?php esc_html_e( 'Show in navigation', 'lily' ); ?>
				</label>
				<input type="text" class="regular-text" name="lily_nav[colored_label]" value="<?php echo esc_attr( lily_nav_get_option( 'colored_label', 'Colored Lenses' ) ); ?>" placeholder="<?php esc_attr_e( 'Label', 'lily' ); ?>">
				<input type="text" class="regular-text" name="lily_nav[colored_label_ar]" value="<?php echo esc_attr( lily_nav_get_option( 'colored_label_ar', '' ) ); ?>" placeholder="<?php esc_attr_e( 'Label (Arabic) — blank reuses English', 'lily' ); ?>">
				<p class="description"><?php esc_html_e( 'Dropdown content (Shop All, Prescription, Color, Look, Duration) comes from WooCommerce attributes and categories.', 'lily' ); ?></p>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php esc_html_e( 'Clear Lenses', 'lily' ); ?></th>
			<td>
				<label style="display:block;margin-bottom:8px;">
					<input type="checkbox" name="lily_nav[show_clear]" value="1" <?php checked( lily_nav_get_option( 'show_clear', 1 ) ); ?>>
					<?php esc_html_e( 'Show in navigation', 'lily' ); ?>
				</label>
				<input type="text" class="regular-text" name="lily_nav[clear_label]" value="<?php echo esc_attr( lily_nav_get_option( 'clear_label', 'Clear Lenses' ) ); ?>" placeholder="<?php esc_attr_e( 'Label', 'lily' ); ?>">
				<input type="text" class="regular-text" name="lily_nav[clear_label_ar]" value="<?php echo esc_attr( lily_nav_get_option( 'clear_label_ar', '' ) ); ?>" placeholder="<?php esc_attr_e( 'Label (Arabic) — blank reuses English', 'lily' ); ?>">
			</td>
		</tr>
		<tr>
			<th scope="row"><?php esc_html_e( 'Accessories & Lens Care', 'lily' ); ?></th>
			<td>
				<label style="display:block;margin-bottom:8px;">
					<input type="checkbox" name="lily_nav[show_accessories]" value="1" <?php checked( lily_nav_get_option( 'show_accessories', 1 ) ); ?>>
					<?php esc_html_e( 'Show in navigation', 'lily' ); ?>
				</label>
				<input type="text" class="regular-text" name="lily_nav[accessories_label]" value="<?php echo esc_attr( lily_nav_get_option( 'accessories_label', 'Accessories & Lens Care' ) ); ?>" placeholder="<?php esc_attr_e( 'Label', 'lily' ); ?>">
				<input type="text" class="regular-text" name="lily_nav[accessories_label_ar]" value="<?php echo esc_attr( lily_nav_get_option( 'accessories_label_ar', '' ) ); ?>" placeholder="<?php esc_attr_e( 'Label (Arabic) — blank reuses English', 'lily' ); ?>">
				<input type="url" class="regular-text code" name="lily_nav[accessories_url]" value="<?php echo esc_attr( lily_nav_get_option( 'accessories_url', '' ) ); ?>" placeholder="<?php esc_attr_e( 'Custom URL (optional)', 'lily' ); ?>">
				<p class="description"><?php esc_html_e( 'Leave the URL empty to auto-link an existing “Accessories” or “Lens Care” category, otherwise the Shop page.', 'lily' ); ?></p>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php esc_html_e( 'Find My Lenses', 'lily' ); ?></th>
			<td>
				<label style="display:block;margin-bottom:8px;">
					<input type="checkbox" name="lily_nav[show_find]" value="1" <?php checked( lily_nav_get_option( 'show_find', 1 ) ); ?>>
					<?php esc_html_e( 'Show in navigation', 'lily' ); ?>
				</label>
				<input type="text" class="regular-text" name="lily_nav[find_label]" value="<?php echo esc_attr( lily_nav_get_option( 'find_label', 'Find My Lenses' ) ); ?>" placeholder="<?php esc_attr_e( 'Label', 'lily' ); ?>">
				<input type="text" class="regular-text" name="lily_nav[find_label_ar]" value="<?php echo esc_attr( lily_nav_get_option( 'find_label_ar', '' ) ); ?>" placeholder="<?php esc_attr_e( 'Label (Arabic) — blank reuses English', 'lily' ); ?>">
				<input type="url" class="regular-text code" name="lily_nav[find_url]" value="<?php echo esc_attr( lily_nav_get_option( 'find_url', '' ) ); ?>" placeholder="<?php esc_attr_e( 'Custom URL (optional)', 'lily' ); ?>">
				<p class="description"><?php esc_html_e( 'Defaults to the Lens Finder section on the homepage.', 'lily' ); ?></p>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php esc_html_e( 'Company', 'lily' ); ?></th>
			<td>
				<label style="display:block;margin-bottom:8px;">
					<input type="checkbox" name="lily_nav[show_company]" value="1" <?php checked( lily_nav_get_option( 'show_company', 1 ) ); ?>>
					<?php esc_html_e( 'Show in navigation', 'lily' ); ?>
				</label>
				<input type="text" class="regular-text" name="lily_nav[company_label]" value="<?php echo esc_attr( lily_nav_get_option( 'company_label', 'Company' ) ); ?>" placeholder="<?php esc_attr_e( 'Label', 'lily' ); ?>">
				<input type="text" class="regular-text" name="lily_nav[company_label_ar]" value="<?php echo esc_attr( lily_nav_get_option( 'company_label_ar', '' ) ); ?>" placeholder="<?php esc_attr_e( 'Label (Arabic) — blank reuses English', 'lily' ); ?>">
				<p class="description"><?php esc_html_e( 'Pick existing pages for each item. Items without a page stay visible as inactive placeholders until a page is selected.', 'lily' ); ?></p>
				<div style="display:grid;grid-template-columns:180px 1fr;gap:8px;align-items:center;margin-top:8px;">
					<label for="lily-nav-about_page"><?php esc_html_e( 'About Us page', 'lily' ); ?></label>
					<?php lily_nav_page_dropdown( 'about_page', lily_nav_get_option( 'about_page', 0 ), esc_html__( '— Select —', 'lily' ) ); ?>
					<label for="lily-nav-faqs_page"><?php esc_html_e( 'FAQs page', 'lily' ); ?></label>
					<?php lily_nav_page_dropdown( 'faqs_page', lily_nav_get_option( 'faqs_page', 0 ), esc_html__( '— Select —', 'lily' ) ); ?>
					<label for="lily-nav-contact_page"><?php esc_html_e( 'Contact Us page', 'lily' ); ?></label>
					<?php lily_nav_page_dropdown( 'contact_page', lily_nav_get_option( 'contact_page', 0 ), esc_html__( '— Select —', 'lily' ) ); ?>
				</div>
			</td>
		</tr>
	</table>
	<?php
}

/**
 * admin_post landing for navigation saves posted from any Lily screen.
 *
 * The canonical save itself runs on admin_init (lily_save_navigation_settings);
 * this handler only redirects the user back to the screen they came from.
 */
function lily_navigation_admin_post_redirect() {
	if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$return = isset( $_POST['lily_return'] ) ? sanitize_key( wp_unslash( $_POST['lily_return'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing -- nonce verified by the save handler.
	$url    = lily_pages_screen_url( '' !== $return ? $return : 'navigation' );

	wp_safe_redirect( add_query_arg( 'updated', 'true', $url ) );
	exit;
}
add_action( 'admin_post_lily_save_navigation', 'lily_navigation_admin_post_redirect' );
