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
	$out  = lily_navigation_settings_defaults();

	$out['show_shop']         = empty( $raw['show_shop'] ) ? 0 : 1;
	$out['shop_label']        = sanitize_text_field( isset( $raw['shop_label'] ) ? $raw['shop_label'] : '' );
	$out['shop_label_ar']     = sanitize_text_field( isset( $raw['shop_label_ar'] ) ? $raw['shop_label_ar'] : '' );
	$out['show_colored']      = empty( $raw['show_colored'] ) ? 0 : 1;
	$out['colored_label']     = sanitize_text_field( isset( $raw['colored_label'] ) ? $raw['colored_label'] : '' );
	$out['colored_label_ar']  = sanitize_text_field( isset( $raw['colored_label_ar'] ) ? $raw['colored_label_ar'] : '' );
	$out['show_clear']        = empty( $raw['show_clear'] ) ? 0 : 1;
	$out['clear_label']       = sanitize_text_field( isset( $raw['clear_label'] ) ? $raw['clear_label'] : '' );
	$out['clear_label_ar']    = sanitize_text_field( isset( $raw['clear_label_ar'] ) ? $raw['clear_label_ar'] : '' );
	$out['show_accessories']  = empty( $raw['show_accessories'] ) ? 0 : 1;
	$out['accessories_label'] = sanitize_text_field( isset( $raw['accessories_label'] ) ? $raw['accessories_label'] : '' );
	$out['accessories_label_ar'] = sanitize_text_field( isset( $raw['accessories_label_ar'] ) ? $raw['accessories_label_ar'] : '' );
	$out['accessories_url']   = esc_url_raw( isset( $raw['accessories_url'] ) ? $raw['accessories_url'] : '' );
	$out['show_find']         = empty( $raw['show_find'] ) ? 0 : 1;
	$out['find_label']        = sanitize_text_field( isset( $raw['find_label'] ) ? $raw['find_label'] : '' );
	$out['find_label_ar']     = sanitize_text_field( isset( $raw['find_label_ar'] ) ? $raw['find_label_ar'] : '' );
	$out['find_url']          = esc_url_raw( isset( $raw['find_url'] ) ? $raw['find_url'] : '' );
	$out['show_company']      = empty( $raw['show_company'] ) ? 0 : 1;
	$out['company_label']     = sanitize_text_field( isset( $raw['company_label'] ) ? $raw['company_label'] : '' );
	$out['company_label_ar']  = sanitize_text_field( isset( $raw['company_label_ar'] ) ? $raw['company_label_ar'] : '' );
	$out['about_page']        = absint( isset( $raw['about_page'] ) ? $raw['about_page'] : 0 );
	$out['faqs_page']         = absint( isset( $raw['faqs_page'] ) ? $raw['faqs_page'] : 0 );
	$out['contact_page']      = absint( isset( $raw['contact_page'] ) ? $raw['contact_page'] : 0 );

	update_option( 'lily_navigation_settings', $out );

	add_settings_error( 'lily_navigation', 'saved', esc_html__( 'Navigation settings saved.', 'lily' ), 'updated' );
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
	?>
	<table class="form-table" role="presentation">
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
