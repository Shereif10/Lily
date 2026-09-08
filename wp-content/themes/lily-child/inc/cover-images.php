<?php
/**
 * Editable cover images for the Shop page and product category archives.
 *
 * - Shop:       post meta `lily_shop_cover_image` on the WooCommerce Shop page
 *               (Pages → Shop → Shop Cover Image).
 * - Categories: term meta `lily_cover_image` per product_cat
 *               (Products → Categories → Edit → Cover Image).
 *
 * Attachment IDs are stored (never raw URLs), the field is optional, and the
 * same image serves both languages (TranslatePress untouched).
 *
 * @package Lily
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* -------------------------------------------------------------------------
 * Frontend helper
 * ---------------------------------------------------------------------- */

/**
 * Resolve the cover image attachment ID for the current product archive.
 *
 * @return int Attachment ID, or 0 when no cover is set (block hidden).
 */
function lily_get_archive_cover_image_id() {
	$image_id = 0;

	if ( is_product_category() ) {
		$term = get_queried_object();
		if ( $term instanceof WP_Term ) {
			$image_id = absint( get_term_meta( $term->term_id, 'lily_cover_image', true ) );
		}
	} elseif ( function_exists( 'is_shop' ) && is_shop() && function_exists( 'wc_get_page_id' ) ) {
		$shop_id = (int) wc_get_page_id( 'shop' );
		if ( $shop_id > 0 ) {
			$image_id = absint( get_post_meta( $shop_id, 'lily_shop_cover_image', true ) );
		}
	}

	// Only trust real attachments so a deleted media entry never breaks a page.
	if ( $image_id > 0 && 'attachment' !== get_post_type( $image_id ) ) {
		return 0;
	}

	return $image_id;
}

/* -------------------------------------------------------------------------
 * Admin — product category "Cover Image" field
 * ---------------------------------------------------------------------- */

add_action( 'product_cat_edit_form_fields', 'lily_cover_image_term_field' );
add_action( 'edited_product_cat', 'lily_save_cover_image_term_meta' );

/**
 * Render the Cover Image field on the product category edit screen.
 *
 * @param WP_Term $term Current term.
 */
function lily_cover_image_term_field( $term ) {
	$image_id = absint( get_term_meta( $term->term_id, 'lily_cover_image', true ) );
	?>
	<tr class="form-field lily-image-field">
		<th scope="row"><label><?php esc_html_e( 'Cover Image', 'lily' ); ?></label></th>
		<td>
			<input type="hidden" name="lily_cover_image" value="<?php echo esc_attr( $image_id ); ?>" data-lily-image-input>
			<div class="lily-image-preview" data-lily-image-preview>
				<?php echo $image_id ? wp_get_attachment_image( $image_id, 'medium' ) : ''; ?>
			</div>
			<button type="button" class="button" data-lily-image-select><?php esc_html_e( 'Choose Image', 'lily' ); ?></button>
			<button type="button" class="button" data-lily-image-remove><?php esc_html_e( 'Remove', 'lily' ); ?></button>
			<?php lily_image_guidance( 1920, 500 ); ?>
			<p class="description"><?php esc_html_e( 'Optional wide cover shown at the top of this category archive. Leave empty to show no cover.', 'lily' ); ?></p>
			<?php wp_nonce_field( 'lily_save_cover_image', 'lily_cover_image_nonce' ); ?>
		</td>
	</tr>
	<?php
}

/**
 * Save the category cover image (attachment ID, sanitized).
 *
 * @param int $term_id Term ID.
 */
function lily_save_cover_image_term_meta( $term_id ) {
	if ( empty( $_POST['lily_cover_image_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['lily_cover_image_nonce'] ) ), 'lily_save_cover_image' ) ) {
		return;
	}

	if ( ! current_user_can( 'manage_product_terms' ) && ! current_user_can( 'manage_categories' ) ) {
		return;
	}

	$image_id = isset( $_POST['lily_cover_image'] ) ? absint( $_POST['lily_cover_image'] ) : 0;
	update_term_meta( $term_id, 'lily_cover_image', $image_id );
}

/* -------------------------------------------------------------------------
 * Admin — Shop page "Shop Cover Image" metabox
 * ---------------------------------------------------------------------- */

add_action( 'add_meta_boxes', 'lily_shop_cover_register_metabox' );
add_action( 'save_post_page', 'lily_shop_cover_save_meta', 10, 2 );

/**
 * Register the metabox on the WooCommerce Shop page only.
 */
function lily_shop_cover_register_metabox() {
	if ( ! function_exists( 'wc_get_page_id' ) ) {
		return;
	}

	$shop_id = (int) wc_get_page_id( 'shop' );
	if ( $shop_id <= 0 || 'page' !== get_post_type( $shop_id ) ) {
		return;
	}

	add_meta_box(
		'lily-shop-cover',
		__( 'Shop Cover Image', 'lily' ),
		'lily_shop_cover_render_metabox',
		'page',
		'side',
		'default'
	);
}

/**
 * Render the Shop Cover Image metabox.
 *
 * @param WP_Post $post Current post.
 */
function lily_shop_cover_render_metabox( $post ) {
	$image_id = absint( get_post_meta( $post->ID, 'lily_shop_cover_image', true ) );
	wp_nonce_field( 'lily_save_shop_cover', 'lily_shop_cover_nonce' );
	?>
	<div class="lily-image-field">
		<input type="hidden" name="lily_shop_cover_image" value="<?php echo esc_attr( $image_id ); ?>" data-lily-image-input>
		<div class="lily-image-preview" data-lily-image-preview>
			<?php echo $image_id ? wp_get_attachment_image( $image_id, 'medium' ) : ''; ?>
		</div>
		<button type="button" class="button" data-lily-image-select><?php esc_html_e( 'Choose Image', 'lily' ); ?></button>
		<button type="button" class="button" data-lily-image-remove><?php esc_html_e( 'Remove', 'lily' ); ?></button>
		<?php lily_image_guidance( 1920, 500 ); ?>
		<p class="description"><?php esc_html_e( 'Optional wide cover shown at the top of the Shop page.', 'lily' ); ?></p>
	</div>
	<?php
}

/**
 * Save the Shop cover image.
 *
 * @param int     $post_id Post ID.
 * @param WP_Post $post    Post object.
 */
function lily_shop_cover_save_meta( $post_id, $post ) {
	if ( empty( $_POST['lily_shop_cover_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['lily_shop_cover_nonce'] ) ), 'lily_save_shop_cover' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	if ( function_exists( 'wc_get_page_id' ) && (int) $post_id !== (int) wc_get_page_id( 'shop' ) ) {
		return;
	}

	$image_id = isset( $_POST['lily_shop_cover_image'] ) ? absint( $_POST['lily_shop_cover_image'] ) : 0;
	update_post_meta( $post_id, 'lily_shop_cover_image', $image_id );
}

/* -------------------------------------------------------------------------
 * Admin assets — media picker on the Shop page edit screen
 * (term edit screens are already covered by lily_admin_assets()).
 * ---------------------------------------------------------------------- */

add_action( 'admin_enqueue_scripts', 'lily_shop_cover_admin_assets' );

/**
 * Load the shared Lily media-picker assets when editing the Shop page.
 *
 * @param string $hook Current admin page hook.
 */
function lily_shop_cover_admin_assets( $hook ) {
	if ( 'post.php' !== $hook && 'post-new.php' !== $hook ) {
		return;
	}

	$post_id = isset( $_GET['post'] ) ? absint( $_GET['post'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only screen check.
	if ( $post_id <= 0 || ! function_exists( 'wc_get_page_id' ) || (int) $post_id !== (int) wc_get_page_id( 'shop' ) ) {
		return;
	}

	wp_enqueue_media();
	wp_enqueue_script(
		'lily-admin',
		LILY_THEME_URI . '/assets/js/admin.js',
		array( 'jquery' ),
		LILY_THEME_VERSION,
		true
	);
	wp_localize_script(
		'lily-admin',
		'lilyAdminI18n',
		array(
			'chooseImage' => esc_html__( 'Choose Image', 'lily' ),
			'useImage'    => esc_html__( 'Use Image', 'lily' ),
		)
	);
	wp_enqueue_style( 'lily-admin', LILY_THEME_URI . '/assets/css/admin.css', array(), LILY_THEME_VERSION );
}
