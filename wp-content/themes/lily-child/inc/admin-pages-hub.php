<?php
/**
 * Lily Pages — the simplified dashboard CMS hub.
 *
 * A clean "Lily → Pages" area that lists every existing website page and
 * gives each one a focused management screen. It is a pure ORGANISATION
 * layer: every screen reads and writes the SAME canonical data sources the
 * frontend already uses (lily_homepage_settings, lily_navigation_settings,
 * lily_footer_settings, lily_shop_cover_image post meta, lily_cover_image
 * term meta, WooCommerce terms and pages). No new data sources, no
 * duplicated settings, no frontend changes.
 *
 * @package Lily
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* ── URLs ─────────────────────────────────────────────────────────────── */

/**
 * URL of one Pages screen.
 *
 * @param string $screen Screen key ('' = the overview).
 * @param array  $args   Extra query args.
 * @return string
 */
function lily_pages_screen_url( $screen = '', $args = array() ) {
	$url = admin_url( 'admin.php?page=lily-pages' );

	if ( '' !== $screen ) {
		$url = add_query_arg( 'screen', $screen, $url );
	}

	if ( ! empty( $args ) ) {
		$url = add_query_arg( $args, $url );
	}

	return $url;
}

/* ── Page registry ────────────────────────────────────────────────────── */

/**
 * Every page represented in the Pages area.
 *
 * Each entry maps an existing WordPress page / archive / template to its
 * canonical source. Nothing here creates or duplicates pages.
 *
 * @return array[] group_key => array of page entries.
 */
function lily_pages_registry() {
	$terms = array(
		'colored-lenses'       => __( 'Colored Lenses', 'lily' ),
		'clear-lenses'         => __( 'Clear Lenses', 'lily' ),
		'accessories-lens-care' => __( 'Accessories & Lens Care', 'lily' ),
	);

	$pages = array(
		'about'                   => __( 'About Lily', 'lily' ),
		'faqs'                    => __( 'FAQs', 'lily' ),
		'contact'                 => __( 'Contact Us', 'lily' ),
		'privacy-policy'          => __( 'Privacy Policy', 'lily' ),
		'terms-conditions'        => __( 'Terms & Conditions', 'lily' ),
		'shipping-delivery-policy' => __( 'Shipping & Delivery Policy', 'lily' ),
		'returns-exchange'        => __( 'Returns & Exchange Policy', 'lily' ),
	);

	$simple = array(
		'shipping-policy' => __( 'Shipping Policy', 'lily' ),
		'refund-policy'   => __( 'Refund Policy', 'lily' ),
		'cookie-policy'   => __( 'Cookie Policy', 'lily' ),
		'wishlist'        => __( 'Wishlist', 'lily' ),
		'checkout'        => __( 'Checkout', 'lily' ),
	);

	$registry = array(
		'storefront' => array(
			'home'           => array(
				'title'  => __( 'Home Page', 'lily' ),
				'desc'   => __( 'Global site elements and every homepage section.', 'lily' ),
				'icon'   => 'dashicons-admin-home',
				'screen' => 'home',
			),
			'shop'           => array(
				'title'  => __( 'Shop Page', 'lily' ),
				'desc'   => __( 'Cover image and archive header.', 'lily' ),
				'icon'   => 'dashicons-store',
				'screen' => 'shop',
			),
		),
		'content'    => array(),
		'other'      => array(),
	);

	foreach ( $terms as $slug => $title ) {
		$registry['storefront'][ 'term-' . $slug ] = array(
			'title'  => $title,
			'desc'   => __( 'Category archive — cover, title and description.', 'lily' ),
			'icon'   => 'dashicons-awards',
			'screen' => 'category',
			'term'   => $slug,
		);
	}

	$registry['storefront']['find-my-lenses'] = array(
		'title'  => __( 'Find My Lenses', 'lily' ),
		'desc'   => __( 'Lens Finder section content and product matching data.', 'lily' ),
		'icon'   => 'dashicons-search',
		'screen' => 'find-my-lenses',
	);

	foreach ( $pages as $slug => $title ) {
		$registry['content'][ 'page-' . $slug ] = array(
			'title'  => $title,
			'desc'   => __( 'Content page — sections, images and bilingual copy.', 'lily' ),
			'icon'   => 'dashicons-media-document',
			'screen' => 'page',
			'page'   => $slug,
		);
	}

	foreach ( $simple as $slug => $title ) {
		$registry['other'][ 'simple-' . $slug ] = array(
			'title'  => $title,
			'desc'   => __( 'Existing WordPress page — managed with the native editor.', 'lily' ),
			'icon'   => 'dashicons-admin-links',
			'editor' => $slug,
		);
	}

	$registry['other']['product-template'] = array(
		'title'   => __( 'Product Page Template', 'lily' ),
		'desc'    => __( 'How product pages are assembled; per-product data lives in Products.', 'lily' ),
		'icon'    => 'dashicons-media-spreadsheet',
		'screen'  => 'product-template',
	);

	return $registry;
}

/* ── Menus: Orders + Reviews (canonical targets only) ─────────────────── */

/**
 * Top-level Orders link (canonical WooCommerce order system) and the
 * Comments menu renamed to Reviews (same canonical screen).
 */
function lily_register_storefront_menus() {
	add_menu_page(
		esc_html__( 'Orders', 'lily' ),
		esc_html__( 'Orders', 'lily' ),
		'edit_others_shop_orders',
		'admin.php?page=wc-orders',
		'',
		'dashicons-cart',
		'58.3'
	);
}
add_action( 'admin_menu', 'lily_register_storefront_menus', 5 );

/**
 * Rename the native Comments menu to "Reviews" (same screen, same data —
 * product reviews live in the native comment system with pending moderation).
 */
function lily_rename_comments_to_reviews() {
	global $menu;

	foreach ( (array) $menu as $index => $item ) {
		if ( isset( $item[2] ) && 'edit-comments.php' === $item[2] ) {
			$menu[ $index ][0] = esc_html__( 'Reviews', 'lily' );
			$menu[ $index ][6] = 'dashicons-star-filled';
		}
	}
}
add_action( 'admin_menu', 'lily_rename_comments_to_reviews', 99 );

/* ── Overview ─────────────────────────────────────────────────────────── */

/**
 * Render the Pages overview (the Lily top-level screen).
 */
function lily_render_pages_overview() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$groups = array(
		'storefront' => __( 'Storefront', 'lily' ),
		'content'    => __( 'Company & Content Pages', 'lily' ),
		'other'      => __( 'Other Existing Pages', 'lily' ),
	);
	?>
	<div class="wrap lily-admin lily-pages-hub">
		<h1><?php esc_html_e( 'Pages', 'lily' ); ?></h1>
		<p class="lily-pages-hub__intro">
			<?php esc_html_e( 'Pick a page to manage its sections. Announcement bar, navigation and footer are controlled once — inside the Home Page screen — and apply to the whole website.', 'lily' ); ?>
		</p>

		<?php if ( isset( $_GET['updated'] ) ) : ?>
			<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Changes saved.', 'lily' ); ?></p></div>
		<?php endif; ?>

		<?php foreach ( $groups as $group_key => $group_label ) : ?>
			<?php $entries = lily_pages_registry()[ $group_key ] ?? array(); ?>
			<?php if ( empty( $entries ) ) : ?>
				<?php continue; ?>
			<?php endif; ?>
			<h2 class="lily-pages-hub__group"><?php echo esc_html( $group_label ); ?></h2>
			<div class="lily-pages-grid">
				<?php foreach ( $entries as $entry ) : ?>
					<?php
					$manage_url = isset( $entry['editor'] )
						? lily_page_simple_manage_url( $entry )
						: lily_page_manage_url( $entry );
					?>
					<a class="lily-page-card-tile" href="<?php echo esc_url( $manage_url ); ?>">
						<span class="dashicons <?php echo esc_attr( $entry['icon'] ?? 'dashicons-admin-page' ); ?>" aria-hidden="true"></span>
						<span class="lily-page-card-tile__title"><?php echo esc_html( $entry['title'] ); ?></span>
						<span class="lily-page-card-tile__desc"><?php echo esc_html( $entry['desc'] ); ?></span>
					</a>
				<?php endforeach; ?>
			</div>
		<?php endforeach; ?>

		<h2 class="lily-pages-hub__group"><?php esc_html_e( 'Quick Links', 'lily' ); ?></h2>
		<p class="lily-pages-hub__quick">
			<a class="button" href="<?php echo esc_url( admin_url( 'edit.php?post_type=product' ) ); ?>"><?php esc_html_e( 'Products', 'lily' ); ?></a>
			<a class="button" href="<?php echo esc_url( admin_url( 'edit-comments.php?comment_status=moderated' ) ); ?>"><?php esc_html_e( 'Reviews — pending approval', 'lily' ); ?></a>
			<a class="button" href="<?php echo esc_url( admin_url( 'admin.php?page=wc-orders' ) ); ?>"><?php esc_html_e( 'Orders', 'lily' ); ?></a>
			<a class="button" href="<?php echo esc_url( admin_url( 'admin.php?page=lily-homepage' ) ); ?>"><?php esc_html_e( 'All sections in one editor (Advanced)', 'lily' ); ?></a>
		</p>
	</div>
	<?php
}

/**
 * Manage URL for a registry entry.
 *
 * @param array $entry Registry entry.
 * @return string
 */
function lily_page_manage_url( $entry ) {
	$args = array();

	if ( isset( $entry['term'] ) ) {
		$args['term'] = $entry['term'];
	}

	if ( isset( $entry['page'] ) ) {
		$args['lily_page'] = $entry['page'];
	}

	return lily_pages_screen_url( $entry['screen'] ?? '', $args );
}

/**
 * Native editor URL for a registry entry managed by WordPress itself.
 *
 * @param array $entry Registry entry.
 * @return string
 */
function lily_page_simple_manage_url( $entry ) {
	$slug = (string) ( $entry['editor'] ?? '' );

	$page = '' !== $slug ? get_page_by_path( $slug ) : null;
	$page_id = $page instanceof WP_Post ? (int) $page->ID : 0;

	if ( function_exists( 'wc_get_page_id' ) ) {
		$wc_page_id = (int) wc_get_page_id( $slug );
		if ( $wc_page_id > 0 ) {
			$page_id = $wc_page_id;
		}
	}

	return $page_id ? (string) get_edit_post_link( $page_id ) : admin_url( 'edit.php?post_type=page' );
}

/**
 * First published product ID (used as a product-template reference link).
 *
 * @return int
 */
function lily_first_product_id() {
	$posts = get_posts(
		array(
			'post_type'      => 'product',
			'post_status'    => 'publish',
			'numberposts'    => 1,
			'fields'         => 'ids',
			'orderby'        => 'date',
			'order'          => 'ASC',
		)
	);

	return ! empty( $posts ) ? (int) $posts[0] : 0;
}

/* ── Screen dispatcher ────────────────────────────────────────────────── */

/**
 * Render a Pages screen (registered as the Lily top-level + submenu callback).
 */
function lily_render_lily_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	if ( ! function_exists( 'submit_button' ) ) {
		require_once ABSPATH . 'wp-admin/includes/template.php';
	}

	$screen = isset( $_GET['screen'] ) ? sanitize_key( wp_unslash( $_GET['screen'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- display routing only.

	switch ( $screen ) {
		case 'home':
			lily_render_page_screen_home();
			break;
		case 'shop':
			lily_render_page_screen_shop();
			break;
		case 'category':
			lily_render_page_screen_category();
			break;
		case 'find-my-lenses':
			lily_render_page_screen_lens_finder();
			break;
		case 'page':
			lily_render_page_screen_content();
			break;
		case 'product-template':
			lily_render_page_screen_product_template();
			break;
		default:
			lily_render_pages_overview();
	}
}

/* ── Shared card + form helpers ───────────────────────────────────────── */

/**
 * Open one collapsible management card.
 *
 * @param string $id       Unique card id.
 * @param string $title    Card title.
 * @param string $subtitle Optional one-line hint.
 */
function lily_page_card_open( $id, $title, $subtitle = '' ) {
	printf( '<details class="lily-page-card" id="%s" open>', esc_attr( $id ) );
	echo '<summary class="lily-page-card__head"><span class="lily-page-card__title">';
	echo esc_html( $title );
	echo '</span>';
	if ( '' !== $subtitle ) {
		echo '<span class="lily-page-card__sub">' . esc_html( $subtitle ) . '</span>';
	}
	echo '<span class="lily-page-card__chevron" aria-hidden="true"></span></summary>';
	echo '<div class="lily-page-card__body">';
}

/**
 * Close one management card.
 */
function lily_page_card_close() {
	echo '</div></details>';
}

/**
 * Open the shared save form for lily_homepage_settings sections.
 *
 * @param string   $screen  Return screen key.
 * @param string[] $toggles Toggle keys owned by this section (unchecked = off).
 * @param array    $args    Extra return args.
 */
function lily_page_section_form_open( $screen, $toggles = array(), $args = array() ) {
	?>
	<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
		<input type="hidden" name="action" value="lily_save_lily_page_section">
		<input type="hidden" name="lily_screen" value="<?php echo esc_attr( $screen ); ?>">
		<input type="hidden" name="lily_section_toggles" value="<?php echo esc_attr( implode( ',', (array) $toggles ) ); ?>">
		<?php foreach ( (array) $args as $arg_key => $arg_value ) : ?>
			<input type="hidden" name="lily_return_args[<?php echo esc_attr( $arg_key ); ?>]" value="<?php echo esc_attr( $arg_value ); ?>">
		<?php endforeach; ?>
		<?php wp_nonce_field( 'lily_save_page_section', 'lily_page_section_nonce' ); ?>
	<?php
}

/**
 * Close a shared section form.
 *
 * @param string $label Submit label.
 */
function lily_page_section_form_close( $label = '' ) {
	echo '<div class="lily-page-card__actions">';
	submit_button( '' !== $label ? $label : __( 'Save Changes', 'lily' ), 'primary', 'submit', false );
	echo '</div>';
	echo '</form>';
}

/**
 * Saved notice for the hub screens.
 */
function lily_page_saved_notice() {
	if ( isset( $_GET['updated'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- display only.
		echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Changes saved.', 'lily' ) . '</p></div>';
	}
}

/**
 * Small inline "view on site" link.
 *
 * @param string $url Frontend URL.
 */
function lily_page_view_link( $url ) {
	if ( '' === $url ) {
		return;
	}
	printf( ' <a class="lily-page-view-link" href="%s" target="_blank" rel="noopener">%s <span aria-hidden="true">↗</span></a>', esc_url( $url ), esc_html__( 'View page', 'lily' ) );
}

/* ── Save: lily_homepage_settings sections ───────────────────────────── */

/**
 * Save one section of the lily_homepage_settings option.
 *
 * Posted fields are merged over the existing canonical option so every other
 * section keeps its values, then the existing sanitizer validates everything.
 */
function lily_save_lily_page_section() {
	if ( empty( $_POST['lily_page_section_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['lily_page_section_nonce'] ) ), 'lily_save_page_section' ) ) {
		wp_die( esc_html__( 'The form expired. Please try again.', 'lily' ) );
	}

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have permission to edit Lily settings.', 'lily' ) );
	}

	$screen = isset( $_POST['lily_screen'] ) ? sanitize_key( wp_unslash( $_POST['lily_screen'] ) ) : '';
	$raw    = isset( $_POST['lily_homepage'] ) ? (array) wp_unslash( $_POST['lily_homepage'] ) : array();

	$existing   = get_option( 'lily_homepage_settings', array() );
	$existing   = is_array( $existing ) ? $existing : array();
	$merged_raw = array_merge( $existing, $raw );

	/*
	 * Section-owned enable/disable toggles: an unchecked checkbox posts
	 * nothing, so force those keys to 0 here (the full legacy form relies on
	 * the whole option being posted at once; partial forms need this hint).
	 */
	$toggles = isset( $_POST['lily_section_toggles'] ) ? array_filter( array_map( 'sanitize_key', explode( ',', sanitize_text_field( wp_unslash( $_POST['lily_section_toggles'] ) ) ) ) ) : array();
	foreach ( $toggles as $toggle ) {
		$merged_raw[ $toggle ] = empty( $_POST['lily_homepage'][ $toggle ] ) ? 0 : 1;
	}

	$settings = lily_sanitize_homepage_settings( $merged_raw );
	update_option( 'lily_homepage_settings', $settings );

	$return_args = array();
	if ( ! empty( $_POST['lily_return_args'] ) && is_array( $_POST['lily_return_args'] ) ) {
		foreach ( $_POST['lily_return_args'] as $arg_key => $arg_value ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- sanitized per key/value below.
			$safe_key   = sanitize_key( wp_unslash( $arg_key ) );
			$safe_value = in_array( $safe_key, array( 'term', 'lily_page' ), true ) ? sanitize_title( wp_unslash( $arg_value ) ) : sanitize_key( wp_unslash( $arg_value ) );
			$return_args[ $safe_key ] = $safe_value;
		}
	}

	wp_safe_redirect( add_query_arg( 'updated', 'true', lily_pages_screen_url( $screen, $return_args ) ) );
	exit;
}
add_action( 'admin_post_lily_save_lily_page_section', 'lily_save_lily_page_section' );

/* ── Screen: Home Page ────────────────────────────────────────────────── */

/**
 * Home Page management screen.
 *
 * Global site elements (announcement bar, navbar, footer) live ONLY here and
 * apply site-wide. Homepage sections follow the frontend order.
 */
function lily_render_page_screen_home() {
	$settings     = wp_parse_args( get_option( 'lily_homepage_settings', array() ), lily_homepage_settings_defaults() );
	$footer       = lily_get_footer_settings();
	$destinations = lily_footer_destination_options();
	?>
	<div class="wrap lily-admin lily-pages-hub">
		<?php lily_page_screen_header( __( 'Home Page', 'lily' ), __( 'Global site elements and homepage sections — in the same order as the live site.', 'lily' ), home_url( '/' ) ); ?>
		<?php lily_page_saved_notice(); ?>

		<p class="lily-page-notice"><?php esc_html_e( 'Global Site Elements below apply to the ENTIRE website — they are managed only from this screen.', 'lily' ); ?></p>

		<?php /* ── 1. Global Site Elements ── */ ?>
		<div class="lily-page-group">
			<h2 class="lily-page-group__title"><?php esc_html_e( 'Global Site Elements', 'lily' ); ?></h2>

			<?php lily_page_card_open( 'lily-card-announcement', __( 'Announcement Bar', 'lily' ), __( 'Top benefit messages — shown on every page.', 'lily' ) ); ?>
				<?php lily_page_section_form_open( 'home', array( 'show_announcement_bar' ) ); ?>
					<?php lily_render_announcement_fields( $settings ); ?>
				<?php lily_page_section_form_close(); ?>
			<?php lily_page_card_close(); ?>

			<?php lily_page_card_open( 'lily-card-navbar', __( 'Navbar', 'lily' ), __( 'Main navigation, shown on every page.', 'lily' ) ); ?>
				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
					<input type="hidden" name="action" value="lily_save_navigation">
					<input type="hidden" name="lily_return" value="home">
					<?php wp_nonce_field( 'lily_save_navigation_settings', 'lily_navigation_settings_nonce' ); ?>
					<?php lily_render_navigation_fields(); ?>
					<?php lily_page_section_form_close( __( 'Save Navigation', 'lily' ) ); ?>
				</form>
			<?php lily_page_card_close(); ?>

			<?php lily_page_card_open( 'lily-card-footer', __( 'Footer', 'lily' ), __( 'One global footer — brand, columns, socials and bottom bar.', 'lily' ) ); ?>
				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
					<input type="hidden" name="action" value="lily_save_footer_settings">
					<input type="hidden" name="lily_return" value="home">
					<?php wp_nonce_field( 'lily_save_footer_settings', 'lily_footer_settings_nonce' ); ?>
					<?php lily_render_footer_fields( $footer, $destinations ); ?>
					<?php lily_page_section_form_close( __( 'Save Footer', 'lily' ) ); ?>
				</form>
				<script>
				(function () {
					var options = <?php echo wp_json_encode( $destinations ); ?>;
					document.querySelectorAll('[data-lily-add-link-row]').forEach(function (button) {
						if (button.dataset.lilyBound) { return; }
						button.dataset.lilyBound = '1';
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
								'<label><span>Label (Arabic)</span><input type="text" name="lily_footer[' + group + '][' + index + '][label_ar]"></label>' +
								'<label><span>Destination</span><select name="lily_footer[' + group + '][' + index + '][destination]">' + opts + '</select></label>' +
								'<label><span>Order</span><input type="number" name="lily_footer[' + group + '][' + index + '][order]" value="' + (index + 1) + '"></label>';
							wrap.appendChild(fs);
						});
					});
				})();
				</script>
			<?php lily_page_card_close(); ?>
		</div>

		<?php /* ── 2–7. Homepage sections ── */ ?>
		<div class="lily-page-group">
			<h2 class="lily-page-group__title"><?php esc_html_e( 'Homepage Sections', 'lily' ); ?></h2>

			<?php lily_page_card_open( 'lily-card-hero', __( 'Hero Section', 'lily' ), __( 'Slides, headings, CTAs and images.', 'lily' ) ); ?>
				<?php lily_page_section_form_open( 'home', array( 'show_hero' ) ); ?>
					<?php lily_render_hero_fields( $settings ); ?>
				<?php lily_page_section_form_close(); ?>
			<?php lily_page_card_close(); ?>

			<?php lily_page_card_open( 'lily-card-brands', __( 'Brands', 'lily' ), __( 'Brand order and heading.', 'lily' ) ); ?>
				<?php lily_page_section_form_open( 'home', array( 'show_brands' ) ); ?>
					<?php lily_render_brand_fields( $settings ); ?>
				<?php lily_page_section_form_close(); ?>
			<?php lily_page_card_close(); ?>

			<?php lily_page_card_open( 'lily-card-collections', __( 'Shop by Collections', 'lily' ), __( 'Heading, description and displayed categories.', 'lily' ) ); ?>
				<?php lily_page_section_form_open( 'home', array( 'show_shop_by_collections' ) ); ?>
					<?php lily_render_collection_fields( $settings ); ?>
				<?php lily_page_section_form_close(); ?>
			<?php lily_page_card_close(); ?>

			<?php lily_page_card_open( 'lily-card-colors', __( 'Shop by Colors', 'lily' ), __( 'Heading, description and displayed colors.', 'lily' ) ); ?>
				<?php lily_page_section_form_open( 'home', array( 'show_shop_by_colors' ) ); ?>
					<?php lily_render_color_fields( $settings ); ?>
				<?php lily_page_section_form_close(); ?>
			<?php lily_page_card_close(); ?>

			<?php lily_page_card_open( 'lily-card-best', __( 'Best Sellers', 'lily' ), __( 'Heading and description — products join automatically.', 'lily' ) ); ?>
				<?php lily_page_section_form_open( 'home', array( 'show_best_sellers' ) ); ?>
					<?php lily_render_best_seller_fields( $settings ); ?>
				<?php lily_page_section_form_close(); ?>
			<?php lily_page_card_close(); ?>

			<?php lily_page_card_open( 'lily-card-lens', __( 'Find Your Best Lenses', 'lily' ), __( 'Section content, steps and quiz labels.', 'lily' ) ); ?>
				<?php lily_page_section_form_open( 'home', array( 'show_lens_finder' ) ); ?>
					<?php lily_render_lens_finder_fields( $settings ); ?>
				<?php lily_page_section_form_close(); ?>
			<?php lily_page_card_close(); ?>
		</div>
	</div>
	<?php
}

/* ── Screen: Shop ─────────────────────────────────────────────────────── */

/**
 * Shop Page management screen (cover + archive header).
 */
function lily_render_page_screen_shop() {
	$shop_id = function_exists( 'wc_get_page_id' ) ? (int) wc_get_page_id( 'shop' ) : 0;
	if ( $shop_id <= 0 ) {
		echo '<div class="wrap"><h1>' . esc_html__( 'Shop Page', 'lily' ) . '</h1><p>' . esc_html__( 'No WooCommerce Shop page found.', 'lily' ) . '</p></div>';
		return;
	}

	$shop_page    = get_post( $shop_id );
	$cover_id     = absint( get_post_meta( $shop_id, 'lily_shop_cover_image', true ) );
	if ( $cover_id && 'attachment' !== get_post_type( $cover_id ) ) {
		$cover_id = 0;
	}
	$shop_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : '';
	?>
	<div class="wrap lily-admin lily-pages-hub">
		<?php lily_page_screen_header( __( 'Shop Page', 'lily' ), __( 'Cover image and archive header for the main Shop.', 'lily' ), $shop_url ); ?>
		<?php lily_page_saved_notice(); ?>

		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="lily_save_lily_shop_page">
			<?php wp_nonce_field( 'lily_save_lily_shop_page', 'lily_shop_page_nonce' ); ?>

			<?php lily_page_card_open( 'lily-card-shop-cover', __( 'Cover / Hero', 'lily' ), __( 'Optional wide image at the top of the Shop. Leave empty for no cover.', 'lily' ) ); ?>
				<div class="lily-image-field">
					<span><?php esc_html_e( 'Cover image', 'lily' ); ?></span>
					<input type="hidden" name="lily_shop_cover_image" value="<?php echo esc_attr( $cover_id ); ?>" data-lily-image-input>
					<div class="lily-image-preview" data-lily-image-preview>
						<?php echo $cover_id ? wp_get_attachment_image( $cover_id, 'medium' ) : ''; ?>
					</div>
					<button type="button" class="button" data-lily-image-select><?php esc_html_e( 'Choose Image', 'lily' ); ?></button>
					<button type="button" class="button" data-lily-image-remove><?php esc_html_e( 'Remove', 'lily' ); ?></button>
					<?php lily_image_guidance( 1920, 500 ); ?>
					<p class="description"><?php esc_html_e( 'When set, the Shop title and description appear centered over this image. When empty, no cover banner is rendered.', 'lily' ); ?></p>
				</div>
			<?php lily_page_card_close(); ?>

			<?php lily_page_card_open( 'lily-card-shop-header', __( 'Archive Header', 'lily' ), __( 'How the Shop title and description behave.', 'lily' ) ); ?>
				<label class="lily-page-field">
					<span><?php esc_html_e( 'Page title', 'lily' ); ?></span>
					<input type="text" value="<?php echo esc_attr( $shop_page ? $shop_page->post_title : '' ); ?>" readonly>
				</label>
				<p class="description">
					<?php esc_html_e( 'The Shop title comes from the WordPress page itself.' ); ?>
					<a href="<?php echo esc_url( get_edit_post_link( $shop_id ) ); ?>"><?php esc_html_e( 'Edit the page title', 'lily' ); ?></a>
				</p>
				<p class="description">
					<?php esc_html_e( 'The archive description is part of the WooCommerce storefront text and follows the site’s translation workflow; it renders over the cover when one is set, or in the standard header when not.', 'lily' ); ?>
				</p>
			<?php lily_page_card_close(); ?>

			<?php lily_page_card_open( 'lily-card-shop-display', __( 'Filters, Sorting & Product Grid', 'lily' ), __( 'Automatic — powered by WooCommerce.', 'lily' ) ); ?>
				<p class="description"><?php esc_html_e( 'Filters, sorting, pagination and the product grid run on the native WooCommerce archive. Product data, brands and colors are managed under Products.', 'lily' ); ?></p>
			<?php lily_page_card_close(); ?>

			<?php lily_page_section_form_close( __( 'Save Shop Page', 'lily' ) ); ?>
		</form>
	</div>
	<?php
}

/**
 * Save the Shop Page screen (canonical cover meta + shop page excerpt).
 */
function lily_save_lily_shop_page() {
	if ( empty( $_POST['lily_shop_page_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['lily_shop_page_nonce'] ) ), 'lily_save_lily_shop_page' ) ) {
		wp_die( esc_html__( 'The form expired. Please try again.', 'lily' ) );
	}

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have permission to edit Lily settings.', 'lily' ) );
	}

	$shop_id = function_exists( 'wc_get_page_id' ) ? (int) wc_get_page_id( 'shop' ) : 0;
	if ( $shop_id <= 0 || 'page' !== get_post_type( $shop_id ) ) {
		wp_die( esc_html__( 'No WooCommerce Shop page found.', 'lily' ) );
	}

	$cover_id = isset( $_POST['lily_shop_cover_image'] ) ? absint( $_POST['lily_shop_cover_image'] ) : 0;
	if ( $cover_id && 'attachment' !== get_post_type( $cover_id ) ) {
		$cover_id = 0;
	}
	update_post_meta( $shop_id, 'lily_shop_cover_image', $cover_id );

	wp_safe_redirect( add_query_arg( 'updated', 'true', lily_pages_screen_url( 'shop' ) ) );
	exit;
}
add_action( 'admin_post_lily_save_lily_shop_page', 'lily_save_lily_shop_page' );

/* ── Screen: Category archives ────────────────────────────────────────── */

/**
 * Category archive management screen (Colored / Clear / Accessories).
 */
function lily_render_page_screen_category() {
	$slug = isset( $_GET['term'] ) ? sanitize_title( wp_unslash( $_GET['term'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- display routing only.
	$term = '' !== $slug ? get_term_by( 'slug', $slug, 'product_cat' ) : null;

	if ( ! $term || is_wp_error( $term ) ) {
		echo '<div class="wrap"><h1>' . esc_html__( 'Category', 'lily' ) . '</h1><p>' . esc_html__( 'This category does not exist yet. Create it under Products → Categories.', 'lily' ) . '</p></div>';
		return;
	}

	$cover_id   = absint( get_term_meta( $term->term_id, 'lily_cover_image', true ) );
	$thumb_id   = absint( get_term_meta( $term->term_id, 'thumbnail_id', true ) );
	$term_link  = ( ! is_wp_error( $term ) ) ? get_term_link( $term ) : '';
	?>
	<div class="wrap lily-admin lily-pages-hub">
		<?php lily_page_screen_header( $term->name, __( 'Category archive — cover, title and description.', 'lily' ), $term_link ); ?>
		<?php lily_page_saved_notice(); ?>

		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="lily_save_lily_category">
			<input type="hidden" name="lily_term_id" value="<?php echo esc_attr( $term->term_id ); ?>">
			<?php wp_nonce_field( 'lily_save_lily_category', 'lily_category_nonce' ); ?>

			<?php lily_page_card_open( 'lily-card-term-basics', __( 'Title & Description', 'lily' ), __( 'The archive title and intro text.', 'lily' ) ); ?>
				<label class="lily-page-field">
					<span><?php esc_html_e( 'Title', 'lily' ); ?></span>
					<input type="text" name="lily_term_name" value="<?php echo esc_attr( $term->name ); ?>">
				</label>
				<label class="lily-page-field">
					<span><?php esc_html_e( 'Description', 'lily' ); ?></span>
					<textarea name="lily_term_description" rows="4"><?php echo esc_textarea( $term->description ); ?></textarea>
				</label>
				<p class="description"><?php esc_html_e( 'When a cover exists, the title and description appear centered over it; otherwise they sit in the standard archive header.', 'lily' ); ?></p>
			<?php lily_page_card_close(); ?>

			<?php lily_page_card_open( 'lily-card-term-cover', __( 'Cover Image', 'lily' ), __( 'Optional wide image at the top of this archive.', 'lily' ) ); ?>
				<div class="lily-image-field">
					<span><?php esc_html_e( 'Cover image', 'lily' ); ?></span>
					<input type="hidden" name="lily_cover_image" value="<?php echo esc_attr( $cover_id ); ?>" data-lily-image-input>
					<div class="lily-image-preview" data-lily-image-preview>
						<?php echo $cover_id ? wp_get_attachment_image( $cover_id, 'medium' ) : ''; ?>
					</div>
					<button type="button" class="button" data-lily-image-select><?php esc_html_e( 'Choose Image', 'lily' ); ?></button>
					<button type="button" class="button" data-lily-image-remove><?php esc_html_e( 'Remove', 'lily' ); ?></button>
					<?php lily_image_guidance( 1920, 500 ); ?>
				</div>
			<?php lily_page_card_close(); ?>

			<?php lily_page_card_open( 'lily-card-term-thumb', __( 'Collection Card Image', 'lily' ), __( 'Shown inside the homepage “Shop by Collections” card for this category.', 'lily' ) ); ?>
				<div class="lily-image-field">
					<span><?php esc_html_e( 'Card image', 'lily' ); ?></span>
					<input type="hidden" name="lily_term_thumbnail" value="<?php echo esc_attr( $thumb_id ); ?>" data-lily-image-input>
					<div class="lily-image-preview" data-lily-image-preview>
						<?php echo $thumb_id ? wp_get_attachment_image( $thumb_id, 'medium' ) : ''; ?>
					</div>
					<button type="button" class="button" data-lily-image-select><?php esc_html_e( 'Choose Image', 'lily' ); ?></button>
					<button type="button" class="button" data-lily-image-remove><?php esc_html_e( 'Remove', 'lily' ); ?></button>
					<?php lily_image_guidance( 800, 1000 ); ?>
					<p class="description"><?php esc_html_e( 'The standard WooCommerce category image — the same source the collections grid already uses.', 'lily' ); ?></p>
				</div>
			<?php lily_page_card_close(); ?>

			<?php lily_page_section_form_close( __( 'Save Category', 'lily' ) ); ?>
		</form>
	</div>
	<?php
}

/**
 * Save a category screen (canonical term name/description + cover metas).
 */
function lily_save_lily_category() {
	if ( empty( $_POST['lily_category_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['lily_category_nonce'] ) ), 'lily_save_lily_category' ) ) {
		wp_die( esc_html__( 'The form expired. Please try again.', 'lily' ) );
	}

	if ( ! current_user_can( 'manage_product_terms' ) && ! current_user_can( 'manage_categories' ) ) {
		wp_die( esc_html__( 'You do not have permission to edit product categories.', 'lily' ) );
	}

	$term_id = isset( $_POST['lily_term_id'] ) ? absint( $_POST['lily_term_id'] ) : 0;
	$term    = $term_id ? get_term( $term_id, 'product_cat' ) : null;

	if ( ! $term || is_wp_error( $term ) ) {
		wp_die( esc_html__( 'This category does not exist.', 'lily' ) );
	}

	$name = isset( $_POST['lily_term_name'] ) ? sanitize_text_field( wp_unslash( $_POST['lily_term_name'] ) ) : '';
	$desc = isset( $_POST['lily_term_description'] ) ? wp_kses_post( wp_unslash( $_POST['lily_term_description'] ) ) : '';

	if ( '' !== trim( $name ) && $name !== $term->name ) {
		wp_update_term( $term_id, 'product_cat', array( 'name' => $name ) );
	}
	if ( $desc !== $term->description ) {
		wp_update_term( $term_id, 'product_cat', array( 'description' => $desc ) );
	}

	$cover_id = isset( $_POST['lily_cover_image'] ) ? absint( $_POST['lily_cover_image'] ) : 0;
	if ( $cover_id && 'attachment' !== get_post_type( $cover_id ) ) {
		$cover_id = 0;
	}
	update_term_meta( $term_id, 'lily_cover_image', $cover_id );

	$thumb_id = isset( $_POST['lily_term_thumbnail'] ) ? absint( $_POST['lily_term_thumbnail'] ) : 0;
	if ( $thumb_id && 'attachment' !== get_post_type( $thumb_id ) ) {
		$thumb_id = 0;
	}
	update_term_meta( $term_id, 'thumbnail_id', $thumb_id );

	wp_safe_redirect( add_query_arg( 'updated', 'true', lily_pages_screen_url( 'category', array( 'term' => $term->slug ) ) ) );
	exit;
}
add_action( 'admin_post_lily_save_lily_category', 'lily_save_lily_category' );

/* ── Screen: Find My Lenses ───────────────────────────────────────────── */

/**
 * Find My Lenses screen (section content + product matching pointer).
 */
function lily_render_page_screen_lens_finder() {
	$settings = wp_parse_args( get_option( 'lily_homepage_settings', array() ), lily_homepage_settings_defaults() );
	?>
	<div class="wrap lily-admin lily-pages-hub">
		<?php lily_page_screen_header( __( 'Find My Lenses', 'lily' ), __( 'Section content and quiz presentation — the matching engine itself is automatic.', 'lily' ), home_url( '/#find-your-best-lenses' ) ); ?>
		<?php lily_page_saved_notice(); ?>

		<p class="lily-page-notice"><?php esc_html_e( 'Product matching is driven by each product’s own “Lens Finder Data” (best skin tones, best eye colors, priority, exclusion) under Products. Nothing here duplicates that data.', 'lily' ); ?></p>

		<?php lily_page_card_open( 'lily-card-lens-content', __( 'Section & Quiz Content', 'lily' ), __( 'Heading, description, CTA, steps and question labels.', 'lily' ) ); ?>
			<?php lily_page_section_form_open( 'find-my-lenses', array( 'show_lens_finder' ) ); ?>
				<?php lily_render_lens_finder_fields( $settings ); ?>
			<?php lily_page_section_form_close(); ?>
		<?php lily_page_card_close(); ?>
	</div>
	<?php
}

/* ── Screen: content pages ───────────────────────────────────────────── */

/**
 * Content page screens (About, FAQs, Contact, Privacy, Terms, Shipping &
 * Delivery, Returns) — each renders the existing canonical field set.
 */
function lily_render_page_screen_content() {
	$slug = isset( $_GET['lily_page'] ) ? sanitize_title( wp_unslash( $_GET['lily_page'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- display routing only.

	$screens = array(
		'about'                    => array(
			'title'   => __( 'About Lily', 'lily' ),
			'render'  => 'lily_render_about_fields',
			'toggles' => array(),
		),
		'faqs'                     => array(
			'title'   => __( 'FAQs', 'lily' ),
			'render'  => 'lily_render_faq_fields',
			'toggles' => array(),
		),
		'contact'                  => array(
			'title'   => __( 'Contact Us', 'lily' ),
			'render'  => 'lily_render_contact_fields',
			'toggles' => array(),
		),
		'privacy-policy'           => array(
			'title'   => __( 'Privacy Policy', 'lily' ),
			'render'  => 'lily_render_privacy_fields',
			'toggles' => array(),
		),
		'terms-conditions'         => array(
			'title'   => __( 'Terms & Conditions', 'lily' ),
			'render'  => 'lily_render_terms_fields',
			'toggles' => array(),
		),
		'shipping-delivery-policy' => array(
			'title'   => __( 'Shipping & Delivery Policy', 'lily' ),
			'render'  => 'lily_render_shipping_policy_fields',
			'toggles' => array(),
		),
		'returns-exchange'         => array(
			'title'   => __( 'Returns & Exchange Policy', 'lily' ),
			'render'  => 'lily_render_returns_fields',
			'toggles' => array(),
		),
	);

	if ( ! isset( $screens[ $slug ] ) || ! is_callable( $screens[ $slug ]['render'] ) ) {
		lily_render_pages_overview();
		return;
	}

	$page      = get_page_by_path( $slug );
	$page_link = $page && 'publish' === $page->post_status ? get_permalink( $page ) : '';
	$settings  = wp_parse_args( get_option( 'lily_homepage_settings', array() ), lily_homepage_settings_defaults() );

	?>
	<div class="wrap lily-admin lily-pages-hub">
		<?php lily_page_screen_header( $screens[ $slug ]['title'], __( 'Content page — sections, images and bilingual copy.', 'lily' ), $page_link ); ?>
		<?php lily_page_saved_notice(); ?>

		<?php lily_page_card_open( 'lily-card-page-content', $screens[ $slug ]['title'], __( 'Every field below is the same data the live page already uses.', 'lily' ) ); ?>
			<?php lily_page_section_form_open( 'page', $screens[ $slug ]['toggles'], array( 'lily_page' => $slug ) ); ?>
				<?php call_user_func( $screens[ $slug ]['render'], $settings ); ?>
			<?php lily_page_section_form_close(); ?>
		<?php lily_page_card_close(); ?>
	</div>
	<?php
}

/* ── Screen: Product Page Template (informational — no fake pages) ────── */

/**
 * Product Page Template screen.
 *
 * Product pages are assembled by the theme template — not by a WordPress
 * page — so no duplicate page is created here. This screen only explains
 * where each part is managed; per-product data stays in Products.
 */
function lily_render_page_screen_product_template() {
	$product_id = lily_first_product_id();
	$view_url   = $product_id ? get_permalink( $product_id ) : '';
	?>
	<div class="wrap lily-admin lily-pages-hub">
		<?php lily_page_screen_header( __( 'Product Page Template', 'lily' ), __( 'One automatic template for every product — no separate page per product.', 'lily' ), $view_url ); ?>
		<?php lily_page_saved_notice(); ?>

		<?php lily_page_card_open( 'lily-card-pt-layout', __( 'How Product Pages Work', 'lily' ), __( 'Automatic template — nothing to edit here.', 'lily' ) ); ?>
			<ul class="lily-page-template-notes">
				<li><?php esc_html_e( 'Product name, descriptions, prices, images, gallery, categories, brand and colors — managed under Products.', 'lily' ); ?></li>
				<li><?php esc_html_e( 'Prescription power requirement, per-product — the product edit screen (Prescription Power setting). Validation cannot be bypassed anywhere.', 'lily' ); ?></li>
				<li><?php esc_html_e( 'Lens Finder data (best skin tones, best eye colors, priority, exclusion) — the product edit screen “Lens Finder Data” box.', 'lily' ); ?></li>
				<li><?php esc_html_e( 'Best Seller flag — the product edit screen “Best Seller” checkbox; it drives the section membership and the badge everywhere.', 'lily' ); ?></li>
				<li><?php esc_html_e( 'Reviews — managed under Reviews; they only appear after approval.', 'lily' ); ?></li>
				<li><?php esc_html_e( 'Layout, cards and accordions are part of the theme template and are not edited per page.', 'lily' ); ?></li>
			</ul>
		<?php lily_page_card_close(); ?>
	</div>
	<?php
}

/* ── Screen chrome helpers ────────────────────────────────────────────── */

/**
 * Screen header with back link + view link.
 *
 * @param string $title   Screen title.
 * @param string $desc    Screen description.
 * @param string $view_url Frontend URL (optional).
 */
function lily_page_screen_header( $title, $desc = '', $view_url = '' ) {
	echo '<a class="lily-page-back" href="' . esc_url( lily_pages_screen_url() ) . '">&larr; ' . esc_html__( 'All Pages', 'lily' ) . '</a>';
	echo '<h1>';
	echo esc_html( $title );
	lily_page_view_link( $view_url );
	echo '</h1>';
	if ( '' !== $desc ) {
		echo '<p class="lily-page-screen-desc">' . esc_html( $desc ) . '</p>';
	}
}
