<?php
/**
 * Lens Finder REST endpoint.
 *
 * @package Lily
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register Lens Finder REST route.
 */
function lily_register_lens_finder_rest_route() {
	register_rest_route(
		'lily/v1',
		'/lens-finder',
		array(
			'methods'             => WP_REST_Server::CREATABLE,
			'callback'            => 'lily_handle_lens_finder_request',
			'permission_callback' => '__return_true',
			'args'                => array(
				'nonce' => array(
					'type'     => 'string',
					'required' => true,
				),
			),
		)
	);
}
add_action( 'rest_api_init', 'lily_register_lens_finder_rest_route' );

/**
 * Handle Lens Finder submission.
 *
 * @param WP_REST_Request $request REST request.
 * @return WP_REST_Response|WP_Error
 */
function lily_handle_lens_finder_request( WP_REST_Request $request ) {
	$nonce = sanitize_text_field( (string) $request->get_param( 'nonce' ) );

	if ( ! wp_verify_nonce( $nonce, 'lily_lens_finder' ) ) {
		return new WP_Error(
			'lily_lens_finder_bad_nonce',
			esc_html__( 'The Lens Finder session expired. Please refresh the page and try again.', 'lily' ),
			array( 'status' => 403 )
		);
	}

	if ( ! class_exists( 'WooCommerce' ) ) {
		return new WP_Error(
			'lily_lens_finder_no_woocommerce',
			esc_html__( 'WooCommerce is required for Lens Finder recommendations.', 'lily' ),
			array( 'status' => 503 )
		);
	}

	$selections = lily_sanitize_lens_finder_selections( (array) $request->get_param( 'selections' ) );
	$products   = lily_get_lens_finder_products( $selections );

	return rest_ensure_response(
		array(
			'products' => $products,
		)
	);
}

/**
 * Sanitize Lens Finder selections.
 *
 * @param array $raw Raw selections.
 * @return array
 */
function lily_sanitize_lens_finder_selections( array $raw ) {
	$allowed = array(
		'lens_type',
		'prescription',
		'look',
		'eye_color',
		'skin_tone',
		'color',
		'duration',
	);

	$selections = array();

	foreach ( $allowed as $key ) {
		$value              = isset( $raw[ $key ] ) ? $raw[ $key ] : '';
		$selections[ $key ] = sanitize_title( wp_unslash( (string) $value ) );
	}

	return $selections;
}

/**
 * Query and score matching WooCommerce products.
 *
 * @param array $selections Sanitized selections.
 * @return array
 */
function lily_get_lens_finder_products( array $selections ) {
	$tax_query = array( 'relation' => 'AND' );

	$attribute_map = array(
		'lens_type'    => 'pa_lens_type',
		'prescription' => 'pa_prescription',
		'look'         => 'pa_look',
		'color'        => 'pa_color',
		'duration'     => 'pa_duration',
	);

	foreach ( $attribute_map as $selection_key => $taxonomy ) {
		if ( empty( $selections[ $selection_key ] ) || ! taxonomy_exists( $taxonomy ) ) {
			continue;
		}

		$tax_query[] = array(
			'taxonomy' => $taxonomy,
			'field'    => 'slug',
			'terms'    => array( $selections[ $selection_key ] ),
		);
	}

	$meta_query = array(
		'relation' => 'OR',
		array(
			'key'     => 'exclude_from_lens_finder',
			'compare' => 'NOT EXISTS',
		),
		array(
			'key'     => 'exclude_from_lens_finder',
			'value'   => '1',
			'compare' => '!=',
		),
	);

	$query = new WP_Query(
		array(
			'post_type'              => 'product',
			'post_status'            => 'publish',
			'posts_per_page'         => 8,
			'ignore_sticky_posts'    => true,
			'no_found_rows'          => true,
			'tax_query'              => count( $tax_query ) > 1 ? $tax_query : array(),
			'meta_query'             => $meta_query,
			'update_post_meta_cache' => true,
			'update_post_term_cache' => true,
		)
	);

	$matches = array();

	foreach ( $query->posts as $post ) {
		$product = wc_get_product( $post->ID );

		if ( ! $product ) {
			continue;
		}

		$matches[] = lily_format_lens_finder_product( $product, $selections );
	}

	wp_reset_postdata();

	usort(
		$matches,
		static function ( $a, $b ) {
			if ( $a['score'] === $b['score'] ) {
				return $b['priority'] <=> $a['priority'];
			}

			return $b['score'] <=> $a['score'];
		}
	);

	return array_slice( $matches, 0, 4 );
}

/**
 * Format a matched product for the frontend.
 *
 * @param WC_Product $product    Product object.
 * @param array      $selections Lens Finder selections.
 * @return array
 */
function lily_format_lens_finder_product( WC_Product $product, array $selections ) {
	$product_id = $product->get_id();
	$score      = 0;
	$priority   = (int) get_post_meta( $product_id, 'lens_finder_priority', true );

	$skin_tones = (array) get_post_meta( $product_id, 'best_skin_tones', true );
	$eye_colors = (array) get_post_meta( $product_id, 'best_eye_colors', true );

	if ( ! empty( $selections['skin_tone'] ) && in_array( $selections['skin_tone'], $skin_tones, true ) ) {
		$score += 2;
	}

	if ( ! empty( $selections['eye_color'] ) && in_array( $selections['eye_color'], $eye_colors, true ) ) {
		$score += 2;
	}

	$image_id = $product->get_image_id();

	return array(
		'id'          => $product_id,
		'title'       => $product->get_name(),
		'url'         => get_permalink( $product_id ),
		'image'       => $image_id ? wp_get_attachment_image_url( $image_id, 'woocommerce_thumbnail' ) : '',
		'price'       => wp_kses_post( $product->get_price_html() ),
		'buttonText'  => esc_html__( 'View Product', 'lily' ),
		'score'       => $score,
		'priority'    => $priority,
	);
}
