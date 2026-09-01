<?php
/**
 * Native product meta fields for Lens Finder.
 *
 * @package Lily
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'add_meta_boxes_product', 'lily_register_product_lens_finder_metabox' );
add_action( 'save_post_product', 'lily_save_product_lens_finder_meta' );

function lily_register_product_lens_finder_metabox() {
	add_meta_box(
		'lily_lens_finder_data',
		esc_html__( 'Lens Finder Data', 'lily' ),
		'lily_render_product_lens_finder_metabox',
		'product',
		'normal',
		'default'
	);
}

function lily_render_product_lens_finder_metabox( $post ) {
	wp_nonce_field( 'lily_save_product_lens_finder', 'lily_product_lens_finder_nonce' );

	$skin_tones = (array) get_post_meta( $post->ID, 'best_skin_tones', true );
	$eye_colors = (array) get_post_meta( $post->ID, 'best_eye_colors', true );
	$priority   = (int) get_post_meta( $post->ID, 'lens_finder_priority', true );
	$exclude    = (bool) get_post_meta( $post->ID, 'exclude_from_lens_finder', true );

	lily_product_checkbox_group(
		'best_skin_tones',
		esc_html__( 'Best Skin Tones', 'lily' ),
		array(
			'fair'         => esc_html__( 'Fair', 'lily' ),
			'light-medium' => esc_html__( 'Light / Medium', 'lily' ),
			'medium'       => esc_html__( 'Medium', 'lily' ),
			'tan'          => esc_html__( 'Tan', 'lily' ),
			'deep'         => esc_html__( 'Deep', 'lily' ),
		),
		$skin_tones
	);

	lily_product_checkbox_group(
		'best_eye_colors',
		esc_html__( 'Best Eye Colors', 'lily' ),
		array(
			'brown' => esc_html__( 'Brown', 'lily' ),
			'hazel' => esc_html__( 'Hazel', 'lily' ),
			'green' => esc_html__( 'Green', 'lily' ),
			'blue'  => esc_html__( 'Blue', 'lily' ),
			'gray'  => esc_html__( 'Gray', 'lily' ),
			'other' => esc_html__( 'Other', 'lily' ),
		),
		$eye_colors
	);

	printf( '<p><label><strong>%1$s</strong><br><input type="number" min="0" name="lens_finder_priority" value="%2$d"></label></p>', esc_html__( 'Lens Finder Priority', 'lily' ), $priority );
	printf( '<p><label><input type="checkbox" name="exclude_from_lens_finder" value="1" %1$s> %2$s</label></p>', checked( $exclude, true, false ), esc_html__( 'Exclude from Lens Finder', 'lily' ) );
}

function lily_product_checkbox_group( $name, $label, $choices, $selected ) {
	echo '<fieldset class="lily-product-meta-group"><legend><strong>' . esc_html( $label ) . '</strong></legend>';
	foreach ( $choices as $value => $choice_label ) {
		printf( '<label><input type="checkbox" name="%1$s[]" value="%2$s" %3$s> %4$s</label><br>', esc_attr( $name ), esc_attr( $value ), checked( in_array( $value, $selected, true ), true, false ), esc_html( $choice_label ) );
	}
	echo '</fieldset>';
}

function lily_save_product_lens_finder_meta( $post_id ) {
	if ( empty( $_POST['lily_product_lens_finder_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['lily_product_lens_finder_nonce'] ) ), 'lily_save_product_lens_finder' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$allowed_skin = array( 'fair', 'light-medium', 'medium', 'tan', 'deep' );
	$allowed_eye  = array( 'brown', 'hazel', 'green', 'blue', 'gray', 'other' );
	$skin_tones   = isset( $_POST['best_skin_tones'] ) ? array_intersect( array_map( 'sanitize_key', (array) wp_unslash( $_POST['best_skin_tones'] ) ), $allowed_skin ) : array();
	$eye_colors   = isset( $_POST['best_eye_colors'] ) ? array_intersect( array_map( 'sanitize_key', (array) wp_unslash( $_POST['best_eye_colors'] ) ), $allowed_eye ) : array();

	update_post_meta( $post_id, 'best_skin_tones', array_values( $skin_tones ) );
	update_post_meta( $post_id, 'best_eye_colors', array_values( $eye_colors ) );
	update_post_meta( $post_id, 'lens_finder_priority', isset( $_POST['lens_finder_priority'] ) ? absint( $_POST['lens_finder_priority'] ) : 0 );
	update_post_meta( $post_id, 'exclude_from_lens_finder', ! empty( $_POST['exclude_from_lens_finder'] ) ? 1 : 0 );
}

