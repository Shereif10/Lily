<?php
/**
 * Lily â†’ Brands / Colors / Looks / Durations admin screens + the product
 * "Lily Product Data" metabox.
 *
 * ORGANISATION layer over the canonical WooCommerce attribute taxonomies
 * (pa_brand, pa_color, pa_look, pa_duration) â€” no new data sources. Term
 * metas managed here: lily_name_ar (Arabic name), brand_logo (brand image),
 * lily_swatch_color (parent color swatch), color_image (color marketing
 * image, existing homepage Shop by Colors data).
 *
 * @package Lily
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* â”€â”€ Registration â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */

/**
 * Register the four catalog entity screens under the Lily menu.
 */
function lily_register_catalog_menus() {
	add_submenu_page( 'lily', __( 'Brands', 'lily' ), __( 'Brands', 'lily' ), 'manage_options', 'lily-brands', 'lily_render_catalog_screen' );
	add_submenu_page( 'lily', __( 'Colors', 'lily' ), __( 'Colors', 'lily' ), 'manage_options', 'lily-colors', 'lily_render_catalog_screen' );
	add_submenu_page( 'lily', __( 'Looks', 'lily' ), __( 'Looks', 'lily' ), 'manage_options', 'lily-looks', 'lily_render_catalog_screen' );
	add_submenu_page( 'lily', __( 'Durations', 'lily' ), __( 'Durations', 'lily' ), 'manage_options', 'lily-durations', 'lily_render_catalog_screen' );
	add_submenu_page( 'lily', __( 'Occasions', 'lily' ), __( 'Occasions', 'lily' ), 'manage_options', 'lily-occasions', 'lily_render_catalog_screen' );
	add_submenu_page( 'lily', __( 'Skin Colors', 'lily' ), __( 'Skin Colors', 'lily' ), 'manage_options', 'lily-skin-colors', 'lily_render_catalog_screen' );
	add_submenu_page( 'lily', __( 'Eye Colors', 'lily' ), __( 'Eye Colors', 'lily' ), 'manage_options', 'lily-eye-colors', 'lily_render_catalog_screen' );
}
add_action( 'admin_menu', 'lily_register_catalog_menus', 20 );

/**
 * Order the Lily menu exactly as approved:
 * Pages â†’ Brands â†’ Colors â†’ Looks â†’ Durations â†’ Occasions â†’ Homepage Settings (Advanced).
 */
function lily_order_catalog_menus() {
	global $submenu;

	if ( empty( $submenu['lily'] ) ) {
		return;
	}

	$desired = array( 'lily-pages', 'lily-brands', 'lily-colors', 'lily-looks', 'lily-durations', 'lily-occasions', 'lily-skin-colors', 'lily-eye-colors', 'lily-homepage' );
	$ordered = array();
	$rest    = array();

	foreach ( $submenu['lily'] as $item ) {
		if ( in_array( $item[2], $desired, true ) ) {
			$ordered[] = $item;
		} else {
			$rest[] = $item;
		}
	}

	$sorted = array();

	foreach ( $desired as $slug ) {
		foreach ( $ordered as $item ) {
			if ( $item[2] === $slug ) {
				$sorted[] = $item;
			}
		}
	}

	$submenu['lily'] = array_values( array_merge( $sorted, $rest ) );
}
add_action( 'admin_menu', 'lily_order_catalog_menus', 99 );

/**
 * Enqueue the color picker on Lily admin pages.
 *
 * @param string $hook Current admin page hook.
 */
function lily_admin_catalog_assets( $hook ) {
	if ( 'toplevel_page_lily' !== $hook && 0 !== strpos( $hook, 'lily_page_' ) ) {
		return;
	}

	wp_enqueue_style( 'wp-color-picker' );
	wp_enqueue_script( 'wp-color-picker' );
}
add_action( 'admin_enqueue_scripts', 'lily_admin_catalog_assets' );

/* â”€â”€ Kind registry â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */

/**
 * Catalog entity configuration.
 *
 * @param string $kind Entity kind (also the admin page suffix).
 * @return array|null
 */
function lily_catalog_kind_config( $kind ) {
	$kinds = array(
		'brands'    => array(
			'kind'        => 'brand',
			'title'       => __( 'Brands', 'lily' ),
			'singular'    => __( 'Brand', 'lily' ),
			'description' => __( 'Each brand holds one logo and one English + Arabic name. It is the same canonical brand used by products, the navbar, filters, the homepage and Find My Lenses.', 'lily' ),
			'logo'        => true,
		),
		'colors'    => array(
			'kind'        => 'color',
			'title'       => __( 'Colors', 'lily' ),
			'singular'    => __( 'Color', 'lily' ),
			'description' => __( 'Parent colors carry the swatch and appear on the homepage, color filter, navbar and Find My Lenses. Child shades are the specific product colors shown on product cards and product pages.', 'lily' ),
			'hierarchy'   => true,
			'swatch'      => true,
			'image'       => true,
		),
		'looks'     => array(
			'kind'        => 'look',
			'title'       => __( 'Looks', 'lily' ),
			'singular'    => __( 'Look', 'lily' ),
			'description' => __( 'Each look holds one English + Arabic name and is used by products, the navbar, filters and Find My Lenses.', 'lily' ),
		),
		'durations' => array(
			'kind'        => 'duration',
			'title'       => __( 'Durations', 'lily' ),
			'singular'    => __( 'Duration', 'lily' ),
			'description' => __( 'Each duration holds one English + Arabic name and is used by products, the navbar, filters and Find My Lenses.', 'lily' ),
		),
		'occasions' => array(
			'kind'        => 'occasion',
			'title'       => __( 'Occasions', 'lily' ),
			'singular'    => __( 'Occasion', 'lily' ),
			'description' => __( 'Occasions describe where a product is perfect for (wedding, work, university…). They are selected per product under "Perfect For" and shown on the product page.', 'lily' ),
		),
		'skin-colors' => array(
			'kind'        => 'skin_color',
			'title'       => __( 'Skin Colors', 'lily' ),
			'singular'    => __( 'Skin Color', 'lily' ),
			'description' => __( 'Skin Colors feed "Best Suited For" on the product page and the Find My Lenses matching. New colors appear everywhere automatically.', 'lily' ),
		),
		'eye-colors'  => array(
			'kind'        => 'eye_color',
			'title'       => __( 'Eye Colors', 'lily' ),
			'singular'    => __( 'Eye Color', 'lily' ),
			'description' => __( 'Eye Colors are the original eye colors a product suits. They feed "Original Eye Color" on the product page and the Find My Lenses matching. New colors appear everywhere automatically.', 'lily' ),
		),
	);

	return isset( $kinds[ $kind ] ) ? $kinds[ $kind ] : null;
}

/**
 * Admin screen URL for one catalog entity kind.
 *
 * @param string $kind Page key (brands|colors|looks|durations).
 * @param array  $args Extra args.
 * @return string
 */
function lily_catalog_admin_url( $kind, $args = array() ) {
	return add_query_arg( $args, admin_url( 'admin.php?page=lily-' . $kind ) );
}

/**
 * Entity kind (brand) â†’ admin page key (brands).
 *
 * @param string $kind Entity kind.
 * @return string
 */
function lily_catalog_kind_page( $kind ) {
	$pages = array(
		'brand'      => 'brands',
		'color'      => 'colors',
		'look'       => 'looks',
		'duration'   => 'durations',
		'occasion'   => 'occasions',
		'skin_color' => 'skin-colors',
		'eye_color'  => 'eye-colors',
	);

	return isset( $pages[ $kind ] ) ? $pages[ $kind ] : '';
}

/* â”€â”€ Screens â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */

/**
 * Dispatch the catalog entity screen.
 */
function lily_render_catalog_screen() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	if ( ! function_exists( 'submit_button' ) ) {
		require_once ABSPATH . 'wp-admin/includes/template.php';
	}

	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- display routing only.
	$page  = isset( $_GET['page'] ) ? sanitize_key( wp_unslash( $_GET['page'] ) ) : '';
	$kind  = str_replace( 'lily-', '', $page );
	$config = lily_catalog_kind_config( $kind );

	if ( ! $config ) {
		return;
	}

	$taxonomy = lily_catalog_taxonomy( $config['kind'] );

	if ( ! $taxonomy ) {
		echo '<div class="wrap"><h1>' . esc_html( $config['title'] ) . '</h1><p>' . esc_html__( 'The matching WooCommerce attribute does not exist yet. Create it under Products â†’ Attributes.', 'lily' ) . '</p></div>';
		return;
	}

	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- display routing only.
	$edit_id = isset( $_GET['edit'] ) ? absint( $_GET['edit'] ) : 0;
	$edit    = $edit_id ? get_term( $edit_id, $taxonomy ) : null;

	if ( $edit && is_wp_error( $edit ) ) {
		$edit = null;
	}

	?>
	<div class="wrap lily-admin lily-catalog-screen">
		<h1><?php echo esc_html( $config['title'] ); ?></h1>
		<p class="lily-catalog-desc"><?php echo esc_html( $config['description'] ); ?></p>

		<?php if ( isset( $_GET['updated'] ) ) : // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- display only. ?>
			<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Saved.', 'lily' ); ?></p></div>
		<?php endif; ?>
		<?php if ( isset( $_GET['deleted'] ) ) : // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- display only. ?>
			<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Deleted.', 'lily' ); ?></p></div>
		<?php endif; ?>

		<div class="lily-catalog-columns">
			<div class="lily-catalog-list">
				<?php lily_render_catalog_list( $config, $taxonomy ); ?>
			</div>
			<div class="lily-catalog-form">
				<?php lily_render_catalog_form( $config, $taxonomy, $edit ); ?>
			</div>
		</div>
	</div>
	<?php
}

/**
 * Entity list (searchable; colors render as a real parent â†’ shade tree).
 *
 * @param array  $config   Kind config.
 * @param string $taxonomy Taxonomy name.
 */
function lily_render_catalog_list( $config, $taxonomy ) {
	$kind = $config['kind'];

	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- display routing only.
	$search = isset( $_GET['s'] ) ? sanitize_text_field( wp_unslash( $_GET['s'] ) ) : '';

	$args = array(
		'taxonomy'   => $taxonomy,
		'hide_empty' => false,
	);

	if ( '' !== $search ) {
		$args['search'] = $search;
	}

	if ( 'color' === $kind ) {
		$args['parent'] = 0;
	}

	$terms = get_terms( $args );

	if ( is_wp_error( $terms ) ) {
		$terms = array();
	}

	$delete_base = 'lily_delete_catalog_term';
	?>
	<form method="get" class="lily-catalog-search">
		<input type="hidden" name="page" value="<?php echo esc_attr( 'lily-' . lily_catalog_kind_page( $kind ) ); ?>">
		<input type="search" name="s" value="<?php echo esc_attr( $search ); ?>" placeholder="<?php esc_attr_e( 'Searchâ€¦', 'lily' ); ?>">
		<button type="submit" class="button"><?php esc_html_e( 'Search', 'lily' ); ?></button>
	</form>

	<table class="widefat striped lily-catalog-table">
		<thead>
			<tr>
				<th><?php echo esc_html( $config['singular'] ); ?></th>
				<th><?php esc_html_e( 'Arabic Name', 'lily' ); ?></th>
				<th><?php esc_html_e( 'Visual', 'lily' ); ?></th>
				<th><?php esc_html_e( 'Products', 'lily' ); ?></th>
				<th><?php esc_html_e( 'Actions', 'lily' ); ?></th>
			</tr>
		</thead>
		<tbody>
		<?php if ( empty( $terms ) ) : ?>
			<tr><td colspan="5"><?php esc_html_e( 'Nothing here yet â€” add the first one using the form on the right.', 'lily' ); ?></td></tr>
		<?php endif; ?>
		<?php
		foreach ( $terms as $term ) :
			lily_render_catalog_row( $config, $term, 0 );
			if ( 'color' === $kind ) {
				foreach ( lily_color_children( $term->term_id ) as $child ) {
					lily_render_catalog_row( $config, $child, 1 );
				}
			}
		endforeach;
		?>
		</tbody>
	</table>
	<?php
}

/**
 * One entity list row.
 *
 * @param array   $config Kind config.
 * @param WP_Term $term   Term.
 * @param int     $depth  Indent level (child shades).
 */
function lily_render_catalog_row( $config, $term, $depth = 0 ) {
	$page       = lily_catalog_kind_page( $config['kind'] );
	$edit_url   = lily_catalog_admin_url( $page, array( 'edit' => $term->term_id ) );
	$delete_url = wp_nonce_url(
		admin_url( 'admin-post.php?action=lily_delete_catalog_term' ),
		'lily_delete_catalog_term'
	);
	$delete_url = add_query_arg(
		array(
			'kind' => $page,
			'term' => $term->term_id,
		),
		$delete_url
	);

	$visual = '';
	if ( ! empty( $config['swatch'] ) ) {
		$swatch = lily_color_swatch( $term );
		$visual = $swatch
			? '<span class="lily-swatch-dot" style="background:' . esc_attr( $swatch ) . ';"></span>'
			: '<span class="lily-swatch-dot lily-swatch-dot--empty"></span>';
	} elseif ( ! empty( $config['logo'] ) ) {
		$logo_id = absint( get_term_meta( $term->term_id, 'brand_logo', true ) );
		$visual  = $logo_id ? wp_get_attachment_image( $logo_id, 'thumbnail', false, array( 'class' => 'lily-catalog-logo' ) ) : 'â€”';
	}

	$name_ar = lily_get_term_name_ar( $term );
	?>
	<tr class="<?php echo $depth ? 'lily-catalog-row--child' : ''; ?>">
		<td>
			<?php echo $depth ? '<span class="lily-catalog-branch" aria-hidden="true">â†³</span> ' : ''; ?>
			<strong><?php echo esc_html( $term->name ); ?></strong>
		</td>
		<td><?php echo esc_html( '' !== $name_ar ? $name_ar : 'â€”' ); ?></td>
		<td><?php echo $visual; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built safely above. ?></td>
		<td><?php echo esc_html( number_format_i18n( (int) $term->count ) ); ?></td>
		<td>
			<a href="<?php echo esc_url( $edit_url ); ?>"><?php esc_html_e( 'Edit', 'lily' ); ?></a> |
			<a class="lily-catalog-delete" href="<?php echo esc_url( $delete_url ); ?>" onclick="return confirm('<?php echo esc_js( __( 'Delete this entry? Products using it keep working; the entry is removed everywhere.', 'lily' ) ); ?>');"><?php esc_html_e( 'Delete', 'lily' ); ?></a>
		</td>
	</tr>
	<?php
}

/**
 * Add / edit form.
 *
 * @param array        $config   Kind config.
 * @param string       $taxonomy Taxonomy name.
 * @param WP_Term|null $edit     Term being edited (null = add new).
 */
function lily_render_catalog_form( $config, $taxonomy, $edit = null ) {
	$page         = lily_catalog_kind_page( $config['kind'] );
	$is_child     = $edit && $edit->parent;
	$name         = $edit ? $edit->name : '';
	$name_ar      = $edit ? lily_get_term_name_ar( $edit ) : '';
	$swatch       = ( $edit && ! empty( $config['swatch'] ) ) ? lily_color_swatch( $edit ) : '';
	$logo_id      = ( $edit && ! empty( $config['logo'] ) ) ? absint( get_term_meta( $edit->term_id, 'brand_logo', true ) ) : 0;
	$image_id     = ( $edit && ! empty( $config['image'] ) ) ? absint( get_term_meta( $edit->term_id, 'color_image', true ) ) : 0;
	$allow_parent = ! empty( $config['hierarchy'] ) && ! $is_child;
	?>
	<div class="lily-page-card lily-catalog-form__card" id="lily-catalog-form">
		<h2><?php echo $edit ? esc_html( sprintf( __( 'Edit %s', 'lily' ), $config['singular'] ) ) : esc_html( sprintf( __( 'Add %s', 'lily' ), $config['singular'] ) ); ?></h2>
		<?php if ( $edit ) : ?>
			<p><a href="<?php echo esc_url( lily_catalog_admin_url( $page ) ); ?>"><?php esc_html_e( 'â† Cancel editing â€” add a new one instead', 'lily' ); ?></a></p>
		<?php endif; ?>

		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="lily_save_catalog_term">
			<input type="hidden" name="kind" value="<?php echo esc_attr( $page ); ?>">
			<input type="hidden" name="term_id" value="<?php echo esc_attr( $edit ? $edit->term_id : 0 ); ?>">
			<?php wp_nonce_field( 'lily_save_catalog_term', 'lily_catalog_nonce' ); ?>

			<label class="lily-page-field">
				<span><?php esc_html_e( 'Name (English)', 'lily' ); ?></span>
				<input type="text" name="name" value="<?php echo esc_attr( $name ); ?>" required>
			</label>

			<label class="lily-page-field">
				<span><?php esc_html_e( 'Name (Arabic)', 'lily' ); ?></span>
				<input type="text" name="name_ar" dir="rtl" value="<?php echo esc_attr( $name_ar ); ?>" placeholder="<?php esc_attr_e( 'Optional â€” shown on Arabic pages', 'lily' ); ?>">
			</label>

			<?php if ( $allow_parent ) : ?>
				<?php if ( $is_child ) : ?>
					<label class="lily-page-field">
						<span><?php esc_html_e( 'Parent Color', 'lily' ); ?></span>
						<?php
						$parent = lily_color_parent( $edit );
						printf( '<input type="text" value="%s" readonly>', esc_attr( $parent ? $parent->name : '' ) );
						?>
					</label>
				<?php else : ?>
					<label class="lily-page-field">
						<span><?php esc_html_e( 'Swatch Color', 'lily' ); ?></span>
						<input type="text" class="lily-color-picker" name="swatch" value="<?php echo esc_attr( $swatch ); ?>" data-default-color="<?php echo esc_attr( $swatch ? $swatch : '#AC7D61' ); ?>">
						<p class="description"><?php esc_html_e( 'The small color representation used across the site. Child shades inherit it automatically.', 'lily' ); ?></p>
					</label>
					<?php if ( ! empty( $config['image'] ) ) : ?>
						<div class="lily-image-field lily-page-field">
							<span><?php esc_html_e( 'Marketing Image (optional)', 'lily' ); ?></span>
							<input type="hidden" name="color_image" value="<?php echo esc_attr( $image_id ); ?>" data-lily-image-input>
							<div class="lily-image-preview" data-lily-image-preview>
								<?php echo $image_id ? wp_get_attachment_image( $image_id, 'thumbnail' ) : ''; ?>
							</div>
					<button type="button" class="button" data-lily-image-select><?php esc_html_e( 'Choose Image', 'lily' ); ?></button>
					<button type="button" class="button" data-lily-image-remove><?php esc_html_e( 'Remove', 'lily' ); ?></button>
					<?php lily_image_guidance( 600, 600 ); ?>
					<p class="description"><?php esc_html_e( 'Optional image shown by the homepage Shop by Colors section instead of the swatch.', 'lily' ); ?></p>
						</div>
					<?php endif; ?>
				<?php endif; ?>
			<?php endif; ?>

			<?php if ( ! empty( $config['logo'] ) ) : ?>
				<div class="lily-image-field lily-page-field">
					<span><?php esc_html_e( 'Brand Image / Logo', 'lily' ); ?></span>
					<input type="hidden" name="brand_logo" value="<?php echo esc_attr( $logo_id ); ?>" data-lily-image-input>
					<div class="lily-image-preview" data-lily-image-preview>
						<?php echo $logo_id ? wp_get_attachment_image( $logo_id, 'thumbnail' ) : ''; ?>
					</div>
					<button type="button" class="button" data-lily-image-select><?php esc_html_e( 'Choose Logo', 'lily' ); ?></button>
					<button type="button" class="button" data-lily-image-remove><?php esc_html_e( 'Remove', 'lily' ); ?></button>
					<?php lily_image_guidance( 480, 96 ); ?>
				</div>
			<?php endif; ?>

			<?php if ( ! empty( $config['hierarchy'] ) && ! $edit ) : ?>
				<label class="lily-page-field">
					<span><?php esc_html_e( 'Add as', 'lily' ); ?></span>
					<select name="parent">
						<option value="0"><?php esc_html_e( 'Parent Color', 'lily' ); ?></option>
						<?php foreach ( lily_color_parents() as $parent ) : ?>
							<option value="<?php echo esc_attr( $parent->term_id ); ?>"><?php echo esc_html( $parent->name ); ?></option>
						<?php endforeach; ?>
					</select>
					<p class="description"><?php esc_html_e( 'Choose â€œParent Colorâ€ for a main color, or pick a parent to add a child shade under it.', 'lily' ); ?></p>
				</label>
			<?php endif; ?>

			<?php submit_button( $edit ? __( 'Save Changes', 'lily' ) : __( 'Add New', 'lily' ), 'primary', 'submit', false ); ?>
		</form>
	</div>
	<script>
	jQuery( function ( $ ) {
		if ( $.fn.wpColorPicker ) {
			$( '.lily-color-picker' ).wpColorPicker();
		}
	} );
	</script>
	<?php
}

/* â”€â”€ Save / delete â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */

/**
 * Create or update one catalog entity.
 */
function lily_save_catalog_term() {
	if ( empty( $_POST['lily_catalog_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['lily_catalog_nonce'] ) ), 'lily_save_catalog_term' ) ) {
		wp_die( esc_html__( 'The form expired. Please try again.', 'lily' ) );
	}

	if ( ! current_user_can( 'manage_options' ) && ! current_user_can( 'manage_product_terms' ) ) {
		wp_die( esc_html__( 'You do not have permission to manage this data.', 'lily' ) );
	}

	$page   = isset( $_POST['kind'] ) ? sanitize_key( wp_unslash( $_POST['kind'] ) ) : '';
	$config = lily_catalog_kind_config( $page );
	$tax    = $config ? lily_catalog_taxonomy( $config['kind'] ) : '';

	if ( ! $config || ! $tax ) {
		wp_die( esc_html__( 'Unknown entity type.', 'lily' ) );
	}

	$term_id = isset( $_POST['term_id'] ) ? absint( $_POST['term_id'] ) : 0;
	$name    = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
	$name_ar = isset( $_POST['name_ar'] ) ? sanitize_text_field( wp_unslash( $_POST['name_ar'] ) ) : '';

	if ( '' === $name ) {
		wp_die( esc_html__( 'A name is required.', 'lily' ) );
	}

	$redirect = lily_catalog_admin_url( $page );

	if ( $term_id ) {
		$result = wp_update_term( $term_id, $tax, array( 'name' => $name ) );
		if ( is_wp_error( $result ) ) {
			wp_die( esc_html( $result->get_error_message() ) );
		}
		$redirect = add_query_arg( 'updated', 'true', $redirect );
	} else {
		$args = array();

		if ( 'color' === $config['kind'] ) {
			$parent = isset( $_POST['parent'] ) ? absint( $_POST['parent'] ) : 0;
			if ( $parent ) {
				$parent_term = get_term( $parent, $tax );
				if ( ! $parent_term || is_wp_error( $parent_term ) || $parent_term->parent ) {
					$parent = 0; // One hierarchy level only â€” parents must be top-level.
				}
			}
			if ( $parent ) {
				$args['parent'] = $parent;
			}
		}

		$result = wp_insert_term( $name, $tax, $args );

		if ( is_wp_error( $result ) ) {
			if ( 'term_exists' === $result->get_error_code() ) {
				$existing = get_term( (int) $result->get_error_data( 'term_exists' ), $tax );
				wp_safe_redirect( add_query_arg( array( 'edit' => $existing ? $existing->term_id : 0, 'updated' => 'true' ), $redirect ) );
				exit;
			}
			wp_die( esc_html( $result->get_error_message() ) );
		}

		$term_id  = (int) $result['term_id'];
		$redirect = add_query_arg( 'updated', 'true', $redirect );
	}

	// Arabic name â€” one term, both languages.
	update_term_meta( $term_id, 'lily_name_ar', $name_ar );

	// Brand image.
	if ( isset( $_POST['brand_logo'] ) ) {
		$logo_id = absint( $_POST['brand_logo'] );
		if ( $logo_id && 'attachment' !== get_post_type( $logo_id ) ) {
			$logo_id = 0;
		}
		update_term_meta( $term_id, 'brand_logo', $logo_id );
	}

	// Parent color swatch.
	if ( 'color' === $config['kind'] && isset( $_POST['swatch'] ) ) {
		$term    = get_term( $term_id, $tax );
		$is_top  = $term && ! is_wp_error( $term ) && ! $term->parent;
		$swatch  = sanitize_hex_color( wp_unslash( $_POST['swatch'] ) );

		if ( $is_top ) {
			update_term_meta( $term_id, 'lily_swatch_color', $swatch ? $swatch : '' );
		}
	}

	// Color marketing image.
	if ( isset( $_POST['color_image'] ) ) {
		$image_id = absint( $_POST['color_image'] );
		if ( $image_id && 'attachment' !== get_post_type( $image_id ) ) {
			$image_id = 0;
		}
		update_term_meta( $term_id, 'color_image', $image_id );
	}

	wp_safe_redirect( $redirect );
	exit;
}
add_action( 'admin_post_lily_save_catalog_term', 'lily_save_catalog_term' );

/**
 * Delete one catalog entity (products keep their other data; WordPress cleans
 * the relationship automatically).
 */
function lily_delete_catalog_term() {
	if ( empty( $_GET['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'lily_delete_catalog_term' ) ) {
		wp_die( esc_html__( 'The link expired. Please go back and try again.', 'lily' ) );
	}

	if ( ! current_user_can( 'manage_options' ) && ! current_user_can( 'manage_product_terms' ) ) {
		wp_die( esc_html__( 'You do not have permission to manage this data.', 'lily' ) );
	}

	$page   = isset( $_GET['kind'] ) ? sanitize_key( wp_unslash( $_GET['kind'] ) ) : '';
	$config = lily_catalog_kind_config( $page );
	$tax    = $config ? lily_catalog_taxonomy( $config['kind'] ) : '';
	$term_id = isset( $_GET['term'] ) ? absint( $_GET['term'] ) : 0;

	if ( ! $tax || ! $term_id ) {
		wp_die( esc_html__( 'Unknown entity.', 'lily' ) );
	}

	$result = wp_delete_term( $term_id, $tax );

	if ( is_wp_error( $result ) ) {
		wp_die( esc_html( $result->get_error_message() ) );
	}

	wp_safe_redirect( add_query_arg( 'deleted', 'true', lily_catalog_admin_url( $page ) ) );
	exit;
}
add_action( 'admin_post_lily_delete_catalog_term', 'lily_delete_catalog_term' );

/* ── Lily Product Editor — the simple product workflow ───────────────── */

/**
 * Canonical Lily collections (product categories) for the editor.
 *
 * @return WP_Term[]
 */
function lily_catalog_collection_terms() {
	$terms = array();

	foreach (
		array(
			array( 'colored-lenses' ),
			array( 'clear-lenses' ),
			array( 'accessories-lens-care', 'accessories', 'lens-care' ),
		) as $slugs
	) {
		foreach ( $slugs as $slug ) {
			$term = get_term_by( 'slug', $slug, 'product_cat' );

			if ( $term && ! is_wp_error( $term ) ) {
				$terms[] = $term;
				break;
			}
		}
	}

	return $terms;
}

/**
 * Remove the legacy metaboxes now covered by the Lily Product Editor so each
 * product field has exactly one dashboard location.
 *
 * Hooked to BOTH add_meta_boxes and add_meta_boxes_product (late) because the
 * legacy boxes register on the product-specific hook, which fires after the
 * generic one.
 */
function lily_remove_legacy_product_metaboxes() {
	remove_meta_box( 'lily_best_seller', 'product', 'side' );
	remove_meta_box( 'lily_lens_finder_data', 'product', 'normal' );
	remove_meta_box( 'lily_product_data', 'product', 'side' );
	/* Category selection lives in the editor (Section 5); the native box
	 * would duplicate it. Brand box targets WC's native product_brand
	 * taxonomy, which nothing on this site reads — removing it prevents
	 * data landing in a dead taxonomy. Tags are not part of the flow. */
	remove_meta_box( 'product_catdiv', 'product', 'side' );
	remove_meta_box( 'product_branddiv', 'product', 'side' );
	remove_meta_box( 'tagsdiv-product_tag', 'product', 'side' );
}
add_action( 'add_meta_boxes', 'lily_remove_legacy_product_metaboxes', 99 );
add_action( 'add_meta_boxes_product', 'lily_remove_legacy_product_metaboxes', 99 );

/**
 * Render the unified Lily Product Editor directly under the product title.
 */
add_action( 'edit_form_after_title', 'lily_render_product_editor' );

/**
 * Hide the WooCommerce data tabs whose fields the Lily Product Editor owns
 * (identity/price/stock/attributes). The hidden tabs never post, and
 * WC_Data::set_props() skips null props, so nothing is wiped. Shipping,
 * Linked Products, Variations and Advanced stay native.
 *
 * @param array $tabs Product data tabs.
 * @return array
 */
function lily_hide_product_data_tabs( $tabs ) {
	foreach ( array( 'general', 'inventory', 'attribute', 'variations' ) as $tab ) {
		unset( $tabs[ $tab ] );
	}

	return $tabs;
}
add_filter( 'woocommerce_product_data_tabs', 'lily_hide_product_data_tabs' );

/**
 * Render the Lily Product Editor directly under the product title.
 *
 * Every control writes the SAME canonical source it always did — WooCommerce
 * fields (_regular_price, _sale_price, _stock_status, _lily_prescription_power),
 * product_cat, and the pa_* attribute taxonomies and Lens Finder metas managed
 * by the Lily systems. Nothing is stored twice.
 *
 * @param WP_Post $post Product post.
 */
function lily_render_product_editor( $post ) {
	if ( 'product' !== $post->post_type || ! current_user_can( 'edit_post', $post->ID ) ) {
		return;
	}

	if ( ! function_exists( 'wc_get_product' ) ) {
		return;
	}

	wp_nonce_field( 'lily_save_product_editor', 'lily_product_editor_nonce' );

	$product   = wc_get_product( $post->ID );
	$taxonomies = array(
		'brand'    => lily_catalog_taxonomy( 'brand' ),
		'color'    => lily_catalog_taxonomy( 'color' ),
		'look'     => lily_catalog_taxonomy( 'look' ),
		'duration' => lily_catalog_taxonomy( 'duration' ),
		'occasion' => lily_catalog_taxonomy( 'occasion' ),
		'skin_color' => lily_catalog_taxonomy( 'skin_color' ),
		'eye_color'  => lily_catalog_taxonomy( 'eye_color' ),
	);

	/**
	 * First assigned term id in one taxonomy (single-value selects).
	 *
	 * @param int    $post_id Product id.
	 * @param string $taxonomy Taxonomy.
	 * @return int
	 */
	$current_term = static function ( $post_id, $taxonomy ) {
		if ( ! $taxonomy ) {
			return 0;
		}

		$terms = wp_get_post_terms( $post_id, $taxonomy, array( 'fields' => 'ids' ) );

		return ( ! is_wp_error( $terms ) && ! empty( $terms ) ) ? (int) $terms[0] : 0;
	};

	$color_term = lily_product_color_term( $post->ID );
	$main_color = $color_term ? ( $color_term->parent ? (int) $color_term->parent : (int) $color_term->term_id ) : 0;
	$specific   = ( $color_term && $color_term->parent ) ? (int) $color_term->term_id : 0;

	$skin_tones = (array) get_post_meta( $post->ID, 'best_skin_tones', true );
	$eye_colors = (array) get_post_meta( $post->ID, 'best_eye_colors', true );
	$best       = lily_is_best_seller( $post->ID );
	$rx         = 'yes' === get_post_meta( $post->ID, '_lily_prescription_power', true ) ? 'yes' : 'no';

	/* How To Use steps — falls back to the standard instructions in the UI. */
	$how_to_use_steps = lily_get_how_to_use_steps( $post->ID );

	if (
		empty( get_post_meta( $post->ID, '_lily_how_to_use_steps', true ) )
		&& empty( get_post_meta( $post->ID, '_lily_how_to_use', true ) )
	) {
		$how_to_use_steps = array( '' ); // One empty row; standard steps apply until the owner types their own.
	}

	$stock    = $product ? $product->get_stock_status() : 'instock';
	$regular  = $product ? $product->get_regular_price( 'edit' ) : '';
	$sale     = $product ? $product->get_sale_price( 'edit' ) : '';

	$occasion_ids = $taxonomies['occasion'] ? wp_get_post_terms( $post->ID, $taxonomies['occasion'], array( 'fields' => 'ids' ) ) : array();
	$occasion_ids = is_wp_error( $occasion_ids ) ? array() : array_map( 'intval', (array) $occasion_ids );

	$category_ids = wp_get_post_terms( $post->ID, 'product_cat', array( 'fields' => 'ids' ) );
	$category_ids = is_wp_error( $category_ids ) ? array() : array_map( 'intval', (array) $category_ids );

	$parents = lily_color_parents();
	$tree    = array();
	foreach ( $parents as $parent ) {
		$tree[] = array(
			'id'       => (int) $parent->term_id,
			'name'     => $parent->name,
			'children' => array_map(
				static function ( $child ) {
					return array(
						'id'   => (int) $child->term_id,
						'name' => $child->name,
					);
				},
				lily_color_children( $parent->term_id )
			),
		);
	}

	$lily_keep_option = __( '— Keep current —', 'lily' );
	?>
	<div class="lily-editor">
		<p class="lily-editor__intro">
			<?php esc_html_e( 'Lily Product Editor — everything a normal product needs, in order. Product Name (above) is the English name; the Arabic name is translated with TranslatePress. Short Description is the “Excerpt” box, Full Description is the main editor, and images are set in the Product Image / Gallery panel below.', 'lily' ); ?>
		</p>

		<?php /* SECTION 1 — Product identity */ ?>
		<section class="lily-editor-card">
			<h3><?php esc_html_e( '1 · Product Identity', 'lily' ); ?></h3>
			<div class="lily-editor-grid">
				<p class="lily-editor-field">
					<label><strong><?php esc_html_e( 'Brand', 'lily' ); ?></strong></label>
					<?php if ( $taxonomies['brand'] ) : ?>
						<select name="lily_editor[brand]">
							<option value=""><?php esc_html_e( '— No brand —', 'lily' ); ?></option>
							<?php
							foreach ( get_terms( array( 'taxonomy' => $taxonomies['brand'], 'hide_empty' => false ) ) as $term ) {
								if ( is_wp_error( $term ) ) { continue; }
								printf( '<option value="%1$d"%2$s>%3$s</option>', (int) $term->term_id, selected( $current_term( $post->ID, $taxonomies['brand'] ), (int) $term->term_id, false ), esc_html( $term->name ) );
							}
							?>
						</select>
						<p class="description"><?php esc_html_e( 'Existing brands only. New brands are created under Lily → Brands.', 'lily' ); ?></p>
					<?php endif; ?>
				</p>
			</div>
		</section>

		<?php /* SECTION 2 — Product color */ ?>
		<section class="lily-editor-card">
			<h3><?php esc_html_e( '2 · Product Color', 'lily' ); ?></h3>
			<div class="lily-editor-grid">
				<p class="lily-editor-field">
					<label><strong><?php esc_html_e( 'Main Color', 'lily' ); ?></strong></label>
					<select name="lily_editor[main_color]" id="lily-editor-main-color">
						<option value=""><?php esc_html_e( '— None —', 'lily' ); ?></option>
						<?php foreach ( $parents as $parent ) : ?>
							<option value="<?php echo esc_attr( $parent->term_id ); ?>"<?php selected( $main_color, (int) $parent->term_id ); ?>><?php echo esc_html( $parent->name ); ?></option>
						<?php endforeach; ?>
					</select>
				</p>
				<p class="lily-editor-field">
					<label><strong><?php esc_html_e( 'Shade (Specific Color)', 'lily' ); ?></strong></label>
					<select name="lily_editor[specific_color]" id="lily-editor-specific-color">
						<option value=""><?php esc_html_e( '— Same as Main Color —', 'lily' ); ?></option>
						<?php
						if ( $main_color ) {
							foreach ( lily_color_children( $main_color ) as $child ) {
								printf( '<option value="%1$d"%2$s>%3$s</option>', (int) $child->term_id, selected( $specific, (int) $child->term_id, false ), esc_html( $child->name ) );
							}
						}
						?>
					</select>
					<p class="description"><?php esc_html_e( 'The shade shows on the product card and product page; the main color drives Shop by Colors, filters and Find My Lenses. Shades are managed under Lily → Colors.', 'lily' ); ?></p>
				</p>
			</div>
		</section>

		<?php /* SECTION 3 — Pricing (saved through WC's canonical setters) */ ?>
		<section class="lily-editor-card">
			<h3><?php esc_html_e( '3 · Pricing', 'lily' ); ?></h3>
			<div class="lily-editor-grid">
				<p class="lily-editor-field">
					<label><strong><?php esc_html_e( 'Regular Price', 'lily' ); ?></strong></label>
					<input type="number" step="any" min="0" name="lily_editor[regular_price]" value="<?php echo esc_attr( $regular ); ?>" placeholder="<?php esc_attr_e( 'Normal price', 'lily' ); ?>">
				</p>
				<p class="lily-editor-field">
					<label><strong><?php esc_html_e( 'Sale Price', 'lily' ); ?></strong></label>
					<input type="number" step="any" min="0" name="lily_editor[sale_price]" value="<?php echo esc_attr( $sale ); ?>" placeholder="<?php esc_attr_e( 'Leave empty for no sale', 'lily' ); ?>">
					<p class="description"><?php esc_html_e( 'A sale price turns on the SALE badge and sale pricing everywhere automatically.', 'lily' ); ?></p>
				</p>
			</div>
		</section>

		<?php /* SECTION 4 — Stock status (saved through WC's canonical setter) */ ?>
		<section class="lily-editor-card">
			<h3><?php esc_html_e( '4 · Stock Status', 'lily' ); ?></h3>
			<p class="lily-editor-radios">
				<label><input type="radio" name="lily_editor[stock_status]" value="instock"<?php checked( $stock, 'instock' ); ?>> <?php esc_html_e( 'In Stock', 'lily' ); ?></label>
				<label><input type="radio" name="lily_editor[stock_status]" value="outofstock"<?php checked( $stock, 'outofstock' ); ?>> <?php esc_html_e( 'Out of Stock', 'lily' ); ?></label>
			</p>
			<p class="description"><?php esc_html_e( 'Out of Stock products stay visible everywhere but cannot be added to the cart.', 'lily' ); ?></p>
		</section>

		<?php /* SECTION 5 — Product type / category */ ?>
		<section class="lily-editor-card">
			<h3><?php esc_html_e( '5 · Product Type', 'lily' ); ?></h3>
			<p class="lily-editor-checks">
				<?php foreach ( lily_catalog_collection_terms() as $term ) : ?>
					<label><input type="checkbox" name="lily_editor[categories][]" value="<?php echo esc_attr( $term->term_id ); ?>"<?php checked( in_array( (int) $term->term_id, $category_ids, true ) ); ?>> <?php echo esc_html( $term->name ); ?></label>
				<?php endforeach; ?>
			</p>
			<p class="description"><?php esc_html_e( 'Decides where the product appears: Colored Lenses, Clear Lenses or Accessories & Lens Care.', 'lily' ); ?></p>
		</section>

		<?php /* SECTION 6 — Prescription power */ ?>
		<section class="lily-editor-card">
			<h3><?php esc_html_e( '6 · Prescription Power', 'lily' ); ?></h3>
			<p class="lily-editor-radios">
				<label><input type="radio" name="_lily_prescription_power" value="no"<?php checked( $rx, 'no' ); ?>> <?php esc_html_e( 'Without Power', 'lily' ); ?></label>
				<label><input type="radio" name="_lily_prescription_power" value="yes"<?php checked( $rx, 'yes' ); ?>> <?php esc_html_e( 'With Power', 'lily' ); ?></label>
			</p>
			<p class="description"><?php esc_html_e( 'With Power products ask for Right Eye (OD) and Left Eye (OS) on the product page — both are required before the product can be added to the cart.', 'lily' ); ?></p>
		</section>

		<?php /* SECTION 7 — Lens specifications (manual numeric inputs) */ ?>
		<section class="lily-editor-card">
			<h3><?php esc_html_e( '7 · Lens Specifications', 'lily' ); ?></h3>
			<div class="lily-editor-grid lily-editor-grid--3">
				<?php
				$lily_specs = array(
					'diameter'  => array( __( 'Diameter', 'lily' ), 'mm', '14.2' ),
					'water'     => array( __( 'Water Content', 'lily' ), '%', '42' ),
					'curve'     => array( __( 'Base Curve', 'lily' ), 'mm', '8.6' ),
				);
				$lily_spec_keys = array( 'diameter' => 'diameter', 'water' => 'water_content', 'curve' => 'base_curve' );
				foreach ( $lily_specs as $key => $spec ) :
					$current = lily_get_product_spec( $post->ID, $lily_spec_keys[ $key ] );
					?>
					<p class="lily-editor-field">
						<label><strong><?php echo esc_html( $spec[0] ); ?></strong></label>
						<span class="lily-editor-unit-field">
							<input type="number" step="any" min="0" name="lily_editor[spec_<?php echo esc_attr( $key ); ?>]" value="<?php echo esc_attr( $current ); ?>" placeholder="<?php echo esc_attr( $spec[2] ); ?>">
							<span class="lily-editor-unit"><?php echo esc_html( $spec[1] ); ?></span>
						</span>
					</p>
				<?php endforeach; ?>
			</div>
			<p class="description"><?php esc_html_e( 'Optional — shown on the product page only when set.', 'lily' ); ?></p>
		</section>

		<?php /* SECTION 8 — Look */ ?>
		<section class="lily-editor-card">
			<h3><?php esc_html_e( '8 · Look', 'lily' ); ?></h3>
			<p class="lily-editor-field">
				<?php if ( $taxonomies['look'] ) : ?>
					<select name="lily_editor[look]">
						<option value=""><?php esc_html_e( '— None —', 'lily' ); ?></option>
						<?php
						foreach ( get_terms( array( 'taxonomy' => $taxonomies['look'], 'hide_empty' => false ) ) as $term ) {
							if ( is_wp_error( $term ) ) { continue; }
							printf( '<option value="%1$d"%2$s>%3$s</option>', (int) $term->term_id, selected( $current_term( $post->ID, $taxonomies['look'] ), (int) $term->term_id, false ), esc_html( $term->name ) );
						}
						?>
					</select>
					<p class="description"><?php esc_html_e( 'Existing looks only — new looks are created under Lily → Looks.', 'lily' ); ?></p>
				<?php endif; ?>
			</p>
		</section>

		<?php /* SECTION 9 — Duration */ ?>
		<section class="lily-editor-card">
			<h3><?php esc_html_e( '9 · Duration', 'lily' ); ?></h3>
			<p class="lily-editor-field">
				<?php if ( $taxonomies['duration'] ) : ?>
					<select name="lily_editor[duration]">
						<option value=""><?php esc_html_e( '— None —', 'lily' ); ?></option>
						<?php
						foreach ( get_terms( array( 'taxonomy' => $taxonomies['duration'], 'hide_empty' => false ) ) as $term ) {
							if ( is_wp_error( $term ) ) { continue; }
							printf( '<option value="%1$d"%2$s>%3$s</option>', (int) $term->term_id, selected( $current_term( $post->ID, $taxonomies['duration'] ), (int) $term->term_id, false ), esc_html( $term->name ) );
						}
						?>
					</select>
					<p class="description"><?php esc_html_e( 'Existing durations only — new durations are created under Lily → Durations.', 'lily' ); ?></p>
				<?php endif; ?>
			</p>
		</section>

		<?php /* SECTION 10 — Best Suited For (skin colors, canonical pa_skin_color) */ ?>
		<section class="lily-editor-card">
			<h3><?php esc_html_e( '10 · Best Suited For', 'lily' ); ?></h3>
			<?php if ( $taxonomies['skin_color'] ) : ?>
				<p class="lily-editor-checks">
					<label class="lily-editor-group-label"><?php esc_html_e( 'Skin Colors', 'lily' ); ?></label>
					<?php
					foreach ( get_terms( array( 'taxonomy' => $taxonomies['skin_color'], 'hide_empty' => false ) ) as $term ) {
						if ( is_wp_error( $term ) ) { continue; }
						printf( '<label><input type="checkbox" name="lily_editor[skin_tones][]" value="%1$s"%2$s> %3$s</label>', esc_attr( $term->slug ), checked( in_array( $term->slug, $skin_tones, true ), true, false ), esc_html( $term->name ) );
					}
					?>
				</p>
				<p class="description"><?php esc_html_e( 'Feeds “Best Suited For” on the product page and the Find My Lenses matching. Managed under Lily → Skin Colors.', 'lily' ); ?></p>
			<?php endif; ?>
		</section>

		<?php /* SECTION 11 — Original eye color (canonical pa_eye_color) */ ?>
		<section class="lily-editor-card">
			<h3><?php esc_html_e( '11 · Original Eye Color', 'lily' ); ?></h3>
			<?php if ( $taxonomies['eye_color'] ) : ?>
				<p class="lily-editor-checks">
					<?php
					foreach ( get_terms( array( 'taxonomy' => $taxonomies['eye_color'], 'hide_empty' => false ) ) as $term ) {
						if ( is_wp_error( $term ) ) { continue; }
						printf( '<label><input type="checkbox" name="lily_editor[eye_colors][]" value="%1$s"%2$s> %3$s</label>', esc_attr( $term->slug ), checked( in_array( $term->slug, $eye_colors, true ), true, false ), esc_html( $term->name ) );
					}
					?>
				</p>
				<p class="description"><?php esc_html_e( 'Feeds the Find My Lenses “Natural Eye Color” matching. Managed under Lily → Eye Colors.', 'lily' ); ?></p>
			<?php endif; ?>
		</section>

		<?php /* SECTION 12 — Perfect For (occasions) */ ?>
		<section class="lily-editor-card">
			<h3><?php esc_html_e( '12 · Perfect For', 'lily' ); ?></h3>
			<?php if ( $taxonomies['occasion'] ) : ?>
				<p class="lily-editor-checks">
					<label class="lily-editor-group-label"><?php esc_html_e( 'Occasions', 'lily' ); ?></label>
					<?php
					foreach ( get_terms( array( 'taxonomy' => $taxonomies['occasion'], 'hide_empty' => false ) ) as $term ) {
						if ( is_wp_error( $term ) ) { continue; }
						printf( '<label><input type="checkbox" name="lily_editor[occasions][]" value="%1$d"%2$s> %3$s</label>', (int) $term->term_id, checked( in_array( (int) $term->term_id, $occasion_ids, true ), true, false ), esc_html( $term->name ) );
					}
					?>
				</p>
				<p class="description"><?php esc_html_e( 'Shown on the product page under “Perfect For”. Occasions are managed under Lily → Occasions.', 'lily' ); ?></p>
			<?php endif; ?>
		</section>

		<?php /* SECTION 13 — How to use (dynamic steps) */ ?>
		<section class="lily-editor-card">
			<h3><?php esc_html_e( '13 · How to Use', 'lily' ); ?></h3>
			<p class="lily-editor-checks">
				<label class="lily-editor-group-label"><?php esc_html_e( 'HOW TO USE', 'lily' ); ?></label>
			</p>
			<div class="lily-steps" data-lily-steps>
				<?php foreach ( $how_to_use_steps as $step_index => $step_text ) : ?>
					<div class="lily-step-row">
						<span class="lily-step-num"><?php echo esc_html( $step_index + 1 ); ?></span>
						<input type="text" name="lily_editor[how_to_use_steps][]" value="<?php echo esc_attr( $step_text ); ?>" placeholder="<?php esc_attr_e( 'Enter instruction', 'lily' ); ?>">
						<button type="button" class="button button-small" data-lily-step-up aria-label="<?php esc_attr_e( 'Move up', 'lily' ); ?>">↑</button>
						<button type="button" class="button button-small" data-lily-step-down aria-label="<?php esc_attr_e( 'Move down', 'lily' ); ?>">↓</button>
						<button type="button" class="button button-small button-link-delete" data-lily-step-remove aria-label="<?php esc_attr_e( 'Remove step', 'lily' ); ?>">×</button>
					</div>
				<?php endforeach; ?>
			</div>
			<p>
				<button type="button" class="button button-secondary" data-lily-add-step><?php esc_html_e( '+ Add Step', 'lily' ); ?></button>
			</p>
			<p class="description"><?php esc_html_e( 'Shown as a numbered list in the HOW TO USE accordion. Leave empty to use the standard instructions. Steps can be added, removed and reordered.', 'lily' ); ?></p>
		</section>

		<?php /* SECTION 14 — Best Seller */ ?>
		<section class="lily-editor-card">
			<h3><?php esc_html_e( '14 · Best Seller', 'lily' ); ?></h3>
			<p class="lily-editor-checks">
				<label><input type="checkbox" name="lily_editor[best_seller]" value="1"<?php checked( $best ); ?>> <?php esc_html_e( 'Mark this product as Best Seller', 'lily' ); ?></label>
			</p>
			<p class="description"><?php esc_html_e( 'Best Seller flags the product in the homepage section and adds the badge everywhere.', 'lily' ); ?></p>
		</section>
	</div>
	<script>
	(function () {
		var tree = <?php echo wp_json_encode( $tree ); ?>;
		var main = document.getElementById( 'lily-editor-main-color' );
		var spec = document.getElementById( 'lily-editor-specific-color' );
		if ( main && spec ) {
			var initial = spec.value;

			function rebuild() {
				var value = main.value;
				while ( spec.options.length > 1 ) { spec.remove( 1 ); }
				var children = [];
				for ( var i = 0; i < tree.length; i++ ) {
					if ( String( tree[ i ].id ) === String( value ) ) { children = tree[ i ].children; break; }
				}
				children.forEach( function ( child ) {
					var option = document.createElement( 'option' );
					option.value = child.id;
					option.textContent = child.name;
					spec.appendChild( option );
				} );
				if ( initial && children.some( function ( child ) { return String( child.id ) === String( initial ); } ) ) {
					spec.value = initial;
				} else {
					spec.value = '';
				}
			}

			main.addEventListener( 'change', rebuild );
			rebuild();
		}

		/* How To Use steps: add / remove / reorder / renumber. */
		var stepsWrap = document.querySelector( '[data-lily-steps]' );
		if ( stepsWrap ) {
			function renumber() {
				var rows = stepsWrap.querySelectorAll( '.lily-step-row' );
				rows.forEach( function ( row, index ) {
					row.querySelector( '.lily-step-num' ).textContent = index + 1;
				} );
			}

			document.querySelectorAll( '[data-lily-add-step]' ).forEach( function ( button ) {
				button.addEventListener( 'click', function () {
					var row = document.createElement( 'div' );
					row.className = 'lily-step-row';
					row.innerHTML =
						'<span class="lily-step-num"></span>' +
						'<input type="text" name="lily_editor[how_to_use_steps][]" value="" placeholder="Enter instruction">' +
						'<button type="button" class="button button-small" data-lily-step-up aria-label="Move up">↑</button>' +
						'<button type="button" class="button button-small" data-lily-step-down aria-label="Move down">↓</button>' +
						'<button type="button" class="button button-small button-link-delete" data-lily-step-remove aria-label="Remove step">×</button>';
					stepsWrap.appendChild( row );
					renumber();
					row.querySelector( 'input' ).focus();
				} );
			} );

			stepsWrap.addEventListener( 'click', function ( event ) {
				var button = event.target.closest( 'button' );
				if ( ! button ) { return; }
				var row = button.closest( '.lily-step-row' );
				if ( button.hasAttribute( 'data-lily-step-remove' ) ) {
					row.remove();
					renumber();
				} else if ( button.hasAttribute( 'data-lily-step-up' ) && row.previousElementSibling ) {
					stepsWrap.insertBefore( row, row.previousElementSibling );
					renumber();
				} else if ( button.hasAttribute( 'data-lily-step-down' ) && row.nextElementSibling ) {
					stepsWrap.insertBefore( row.nextElementSibling, row );
					renumber();
				}
			} );
		}
	})();
	</script>
	<?php
}

/**
 * Save the Lily Product Editor.
 *
 * Writes exactly the canonical fields: product_cat, pa_* attribute terms,
 * Lens Finder metas, the Best Seller flag and the How to Use steps meta.
 *
 * Pricing and stock are re-asserted on `woocommerce_process_product_meta`
 * (see lily_save_product_editor_pricing_stock) — that hook runs AFTER
 * WooCommerce has finished its own product-data save, whose hidden panels
 * post stale rendered values.
 *
 * @param int $post_id Product ID.
 */
function lily_save_product_editor( $post_id ) {
	if ( empty( $_POST['lily_product_editor_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['lily_product_editor_nonce'] ) ), 'lily_save_product_editor' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$data = isset( $_POST['lily_editor'] ) && is_array( $_POST['lily_editor'] ) ? wp_unslash( $_POST['lily_editor'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- sanitized per field below.

	/* Single-value attribute selects (empty = keep current). */
	$single_terms = array(
		'brand'    => lily_catalog_taxonomy( 'brand' ),
		'look'     => lily_catalog_taxonomy( 'look' ),
		'duration' => lily_catalog_taxonomy( 'duration' ),
	);

	foreach ( $single_terms as $key => $taxonomy ) {
		$term_id = isset( $data[ $key ] ) ? absint( $data[ $key ] ) : 0;

		if ( ! $taxonomy || ! $term_id ) {
			continue;
		}

		$term = get_term( $term_id, $taxonomy );

		if ( $term && ! is_wp_error( $term ) ) {
			wp_set_object_terms( $post_id, array( $term_id ), $taxonomy, false );
		}
	}

	/* Color: product keeps its SPECIFIC color; the parent is synced automatically. */
	$color_tax = lily_catalog_taxonomy( 'color' );
	$main      = isset( $data['main_color'] ) ? absint( $data['main_color'] ) : 0;
	$specific  = isset( $data['specific_color'] ) ? absint( $data['specific_color'] ) : 0;

	if ( $color_tax && ( $specific || $main ) ) {
		$target = 0;

		if ( $specific ) {
			$term = get_term( $specific, $color_tax );

			if ( $term && ! is_wp_error( $term ) ) {
				$target = (int) $term->term_id;
			}
		}

		if ( ! $target && $main ) {
			$term = get_term( $main, $color_tax );

			if ( $term && ! is_wp_error( $term ) ) {
				$target = (int) $term->term_id;
			}
		}

		if ( $target ) {
			wp_set_object_terms( $post_id, array( $target ), $color_tax, false );
		}
	}

	/* Category (canonical product_cat relationship). */
	if ( isset( $data['categories'] ) ) {
		$category_ids = array_filter( array_map( 'absint', (array) $data['categories'] ) );

		if ( empty( $category_ids ) ) {
			wp_set_object_terms( $post_id, array(), 'product_cat', false );
		} else {
			wp_set_object_terms( $post_id, array_values( $category_ids ), 'product_cat', false );
		}
	}

	/* Occasions (canonical pa_occasion relationship). */
	$occasion_tax = lily_catalog_taxonomy( 'occasion' );

	if ( $occasion_tax && isset( $data['occasions'] ) ) {
		$occasion_ids = array_filter( array_map( 'absint', (array) $data['occasions'] ) );
		wp_set_object_terms( $post_id, array_values( $occasion_ids ), $occasion_tax, false );
	}

	/* Lens Finder matching data — Skin/Eye Color values are canonical term
	 * slugs from pa_skin_color / pa_eye_color (dashboard-managed lists). */
	$skin_slugs = isset( $data['skin_tones'] ) ? array_values( array_filter( array_map( 'sanitize_title', (array) $data['skin_tones'] ) ) ) : array();
	$eye_slugs  = isset( $data['eye_colors'] ) ? array_values( array_filter( array_map( 'sanitize_title', (array) $data['eye_colors'] ) ) ) : array();

	update_post_meta( $post_id, 'best_skin_tones', $skin_slugs );
	update_post_meta( $post_id, 'best_eye_colors', $eye_slugs );

	/* Best Seller flag (single source of truth for section + badge). */
	update_post_meta( $post_id, '_lily_best_seller', empty( $data['best_seller'] ) ? '' : '1' );

	/* Lens specifications — manual numeric values (canonical metas). */
	$spec_keys = array(
		'spec_diameter' => '_lily_diameter',
		'spec_water'    => '_lily_water_content',
		'spec_curve'    => '_lily_base_curve',
	);

	foreach ( $spec_keys as $key => $meta_key ) {
		if ( ! isset( $data[ $key ] ) ) {
			continue;
		}

		$numeric = lily_spec_numeric( wp_unslash( $data[ $key ] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing -- nonce verified above.

		update_post_meta( $post_id, $meta_key, $numeric );
	}

	/* How to Use steps (empty list → standard instructions apply). */
	if ( isset( $data['how_to_use_steps'] ) ) {
		$steps = array_values(
			array_filter(
				array_map(
					static function ( $step ) {
						return trim( sanitize_text_field( (string) $step ) );
					},
					(array) $data['how_to_use_steps']
				),
				static function ( $step ) {
					return '' !== $step;
				}
			)
		);

		update_post_meta( $post_id, '_lily_how_to_use_steps', $steps );
	}
}
add_action( 'save_post_product', 'lily_save_product_editor', 20 );

/**
 * Re-assert Pricing and Stock Status from the Lily Product Editor.
 *
 * Runs on `woocommerce_process_product_meta` at priority 99 — AFTER
 * WooCommerce's own meta-box save (priority 10) has applied the stale
 * values its hidden panels post (they render before the owner's edits).
 * Applying the editor's values through WooCommerce's canonical setters
 * here guarantees `$product->get_price()`, `is_on_sale()` and
 * `get_stock_status()` reflect exactly what the owner entered.
 */
function lily_save_product_editor_pricing_stock( $post_id ) {
	if ( empty( $_POST['lily_product_editor_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['lily_product_editor_nonce'] ) ), 'lily_save_product_editor' ) ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$data = isset( $_POST['lily_editor'] ) && is_array( $_POST['lily_editor'] ) ? wp_unslash( $_POST['lily_editor'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- sanitized per field below.

	$product = function_exists( 'wc_get_product' ) ? wc_get_product( $post_id ) : null;

	if ( ! $product ) {
		return;
	}

	$props = array();

	if ( isset( $data['regular_price'] ) ) {
		$props['regular_price'] = '' === trim( (string) $data['regular_price'] ) ? '' : wc_format_decimal( $data['regular_price'] );
	}

	if ( isset( $data['sale_price'] ) ) {
		$props['sale_price'] = '' === trim( (string) $data['sale_price'] ) ? '' : wc_format_decimal( $data['sale_price'] );
	}

	if ( isset( $data['stock_status'] ) ) {
		$props['stock_status'] = 'outofstock' === $data['stock_status'] ? 'outofstock' : 'instock';
	}

	if ( empty( $props ) ) {
		return;
	}

	$product->set_props( $props );
	$product->save();
	wc_delete_product_transients( $post_id );
}
add_action( 'woocommerce_process_product_meta', 'lily_save_product_editor_pricing_stock', 99 );

/**
 * Per-product HOW TO USE content: the editor meta wins, otherwise the
 * standard filterable default.
 *
 * @param string         $content Default content (from the filter chain).
 * @param WC_Product|null $product Current product.
 * @return string
 */
function lily_product_editor_how_to_use( $content, $product ) {
	$product_id = ( $product instanceof WC_Product ) ? $product->get_id() : 0;

	if ( ! $product_id ) {
		return $content;
	}

	$custom = get_post_meta( $product_id, '_lily_how_to_use', true );
	$custom = is_string( $custom ) ? trim( wp_strip_all_tags( $custom ) ) : '';

	return '' !== $custom ? $custom : $content;
}
add_filter( 'lily_product_how_to_use', 'lily_product_editor_how_to_use', 10, 2 );
