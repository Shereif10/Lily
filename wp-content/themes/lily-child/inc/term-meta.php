<?php
/**
 * Native term meta fields.
 *
 * @package Lily
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'pa_color_add_form_fields', 'lily_color_add_form_fields' );
add_action( 'pa_color_edit_form_fields', 'lily_color_edit_form_fields' );
add_action( 'created_pa_color', 'lily_save_color_term_meta' );
add_action( 'edited_pa_color', 'lily_save_color_term_meta' );

add_action( 'pa_brand_add_form_fields', 'lily_brand_add_form_fields' );
add_action( 'pa_brand_edit_form_fields', 'lily_brand_edit_form_fields' );
add_action( 'created_pa_brand', 'lily_save_brand_term_logo' );
add_action( 'edited_pa_brand', 'lily_save_brand_term_logo' );

function lily_brand_add_form_fields() {
	?>
	<div class="form-field lily-image-field">
		<label><?php esc_html_e( 'Brand Logo', 'lily' ); ?></label>
		<input type="hidden" name="brand_logo" value="" data-lily-image-input>
		<div class="lily-image-preview" data-lily-image-preview></div>
		<button type="button" class="button" data-lily-image-select><?php esc_html_e( 'Choose Logo', 'lily' ); ?></button>
		<button type="button" class="button" data-lily-image-remove><?php esc_html_e( 'Remove', 'lily' ); ?></button>
		<?php lily_image_guidance( 480, 96 ); ?>
		<p><?php esc_html_e( 'Logo shown in the homepage Brands section.', 'lily' ); ?></p>
	</div>
	<?php
	wp_nonce_field( 'lily_save_brand_logo', 'lily_brand_logo_nonce' );
}

function lily_brand_edit_form_fields( $term ) {
	$logo_id = absint( get_term_meta( $term->term_id, 'brand_logo', true ) );
	?>
	<tr class="form-field lily-image-field">
		<th scope="row"><label><?php esc_html_e( 'Brand Logo', 'lily' ); ?></label></th>
		<td>
			<input type="hidden" name="brand_logo" value="<?php echo esc_attr( $logo_id ); ?>" data-lily-image-input>
			<div class="lily-image-preview" data-lily-image-preview>
				<?php echo $logo_id ? wp_get_attachment_image( $logo_id, 'thumbnail' ) : ''; ?>
			</div>
			<button type="button" class="button" data-lily-image-select><?php esc_html_e( 'Choose Logo', 'lily' ); ?></button>
			<button type="button" class="button" data-lily-image-remove><?php esc_html_e( 'Remove', 'lily' ); ?></button>
			<?php lily_image_guidance( 480, 96 ); ?>
			<p class="description"><?php esc_html_e( 'Logo shown in the homepage Brands section.', 'lily' ); ?></p>
			<?php wp_nonce_field( 'lily_save_brand_logo', 'lily_brand_logo_nonce' ); ?>
		</td>
	</tr>
	<?php
}

function lily_save_brand_term_logo( $term_id ) {
	if ( empty( $_POST['lily_brand_logo_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['lily_brand_logo_nonce'] ) ), 'lily_save_brand_logo' ) ) {
		return;
	}

	if ( ! current_user_can( 'manage_product_terms' ) && ! current_user_can( 'manage_categories' ) ) {
		return;
	}

	$logo_id = isset( $_POST['brand_logo'] ) ? absint( $_POST['brand_logo'] ) : 0;
	update_term_meta( $term_id, 'brand_logo', $logo_id );
}

function lily_color_add_form_fields() {
	?>
	<div class="form-field lily-image-field">
		<label><?php esc_html_e( 'Color Image', 'lily' ); ?></label>
		<input type="hidden" name="color_image" value="" data-lily-image-input>
		<div class="lily-image-preview" data-lily-image-preview></div>
		<button type="button" class="button" data-lily-image-select><?php esc_html_e( 'Choose Image', 'lily' ); ?></button>
		<button type="button" class="button" data-lily-image-remove><?php esc_html_e( 'Remove', 'lily' ); ?></button>
		<?php lily_image_guidance( 600, 600 ); ?>
		<p><?php esc_html_e( 'Marketing image used by the Shop by Colors homepage section.', 'lily' ); ?></p>
	</div>
	<?php
	wp_nonce_field( 'lily_save_color_image', 'lily_color_image_nonce' );
}

function lily_color_edit_form_fields( $term ) {
	$image_id = absint( get_term_meta( $term->term_id, 'color_image', true ) );
	?>
	<tr class="form-field lily-image-field">
		<th scope="row"><label><?php esc_html_e( 'Color Image', 'lily' ); ?></label></th>
		<td>
			<input type="hidden" name="color_image" value="<?php echo esc_attr( $image_id ); ?>" data-lily-image-input>
			<div class="lily-image-preview" data-lily-image-preview>
				<?php echo $image_id ? wp_get_attachment_image( $image_id, 'thumbnail' ) : ''; ?>
			</div>
			<button type="button" class="button" data-lily-image-select><?php esc_html_e( 'Choose Image', 'lily' ); ?></button>
			<button type="button" class="button" data-lily-image-remove><?php esc_html_e( 'Remove', 'lily' ); ?></button>
			<?php lily_image_guidance( 600, 600 ); ?>
			<p class="description"><?php esc_html_e( 'Marketing image used by the Shop by Colors homepage section.', 'lily' ); ?></p>
			<?php wp_nonce_field( 'lily_save_color_image', 'lily_color_image_nonce' ); ?>
		</td>
	</tr>
	<?php
}

function lily_save_color_term_meta( $term_id ) {
	if ( empty( $_POST['lily_color_image_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['lily_color_image_nonce'] ) ), 'lily_save_color_image' ) ) {
		return;
	}

	if ( ! current_user_can( 'manage_product_terms' ) && ! current_user_can( 'manage_categories' ) ) {
		return;
	}

	$image_id = isset( $_POST['color_image'] ) ? absint( $_POST['color_image'] ) : 0;
	update_term_meta( $term_id, 'color_image', $image_id );
}

