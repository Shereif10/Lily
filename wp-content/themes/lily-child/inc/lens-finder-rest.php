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

		/*
		 * Colors: a Parent Color selection must match every product assigned
		 * to that color OR any of its Child Shades. Products normally carry
		 * the Parent Color assignment automatically (see
		 * lily_sync_color_parent_assignment()); expanding the slug list here
		 * also covers products whose shade was assigned directly.
		 */
		$term_slugs = array( $selections[ $selection_key ] );

		if ( 'pa_color' === $taxonomy && function_exists( 'lily_color_descendant_slugs' ) ) {
			$term_slugs = array_values( array_unique( array_merge( $term_slugs, lily_color_descendant_slugs( $selections[ $selection_key ] ) ) ) );
		}

		$tax_query[] = array(
			'taxonomy' => $taxonomy,
			'field'    => 'slug',
			'terms'    => $term_slugs,
		);
	}

	/*
	 * All products with valid Lens Finder data participate automatically —
	 * no exclusion flag or manual priority ranking. Products arrive in
	 * date-desc order, so a stable score sort keeps ties deterministic.
	 */
	$query = new WP_Query(
		array(
			'post_type'              => 'product',
			'post_status'            => 'publish',
			'posts_per_page'         => 8,
			'ignore_sticky_posts'    => true,
			'no_found_rows'          => true,
			'tax_query'              => count( $tax_query ) > 1 ? $tax_query : array(),
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
			return $b['score'] <=> $a['score'];
		}
	);

	return array_slice( $matches, 0, 4 );
}

/**
 * Format a matched product for the frontend.
 *
 * The result ships the RENDERED shared product card (same markup, badges,
 * metadata and add-to-cart behavior as the Shop), so the Lens Finder can
 * never drift from the one card system.
 *
 * @param WC_Product $product    Product object.
 * @param array      $selections Lens Finder selections.
 * @return array
 */
function lily_format_lens_finder_product( WC_Product $product, array $selections ) {
	$product_id = $product->get_id();
	$score      = 0;

	$skin_tones = (array) get_post_meta( $product_id, 'best_skin_tones', true );
	$eye_colors = (array) get_post_meta( $product_id, 'best_eye_colors', true );

	if ( ! empty( $selections['skin_tone'] ) && in_array( $selections['skin_tone'], $skin_tones, true ) ) {
		$score += 2;
	}

	if ( ! empty( $selections['eye_color'] ) && in_array( $selections['eye_color'], $eye_colors, true ) ) {
		$score += 2;
	}

	$image_id = $product->get_image_id();

	/*
	 * Render through the shared product card (drawer add-to-cart flow, like
	 * the homepage Best Sellers carousel — full validation chain preserved).
	 */
	$html = '';
	ob_start();
	get_template_part(
		'template-parts/components/product-card',
		null,
		array(
			'product'     => $product,
			'drawer_ajax' => true,
		)
	);
	$html = trim( (string) ob_get_clean() );

	return array(
		'id'          => $product_id,
		'title'       => $product->get_name(),
		'url'         => get_permalink( $product_id ),
		'image'       => $image_id ? wp_get_attachment_image_url( $image_id, 'woocommerce_thumbnail' ) : '',
		'price'       => wp_kses_post( $product->get_price_html() ),
		'buttonText'  => esc_html__( 'View Product', 'lily' ),
		'html'        => $html,
		'score'       => $score,
	);
}
