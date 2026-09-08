<?php
/**
 * Lily Wishlist — guest wishlist stored as product IDs in browser localStorage.
 *
 * One small integration: a heart-button helper rendered inside the existing
 * product-card component (and the Best Sellers card), a /wishlist/ page
 * created once by slug, and a tiny REST endpoint that renders wishlist
 * products through the shared product-card template so markup, pricing and
 * add-to-cart behavior always match the Shop.
 *
 * @package Lily
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Echo the wishlist heart button for a product.
 *
 * Real <button>, keyboard accessible. Saved state is synced client-side
 * from localStorage (see lily-wishlist.js), so the server always renders
 * the neutral not-saved state.
 *
 * @param int $product_id Product ID.
 */
function lily_wishlist_button( $product_id ) {
	$product_id = absint( $product_id );

	if ( ! $product_id ) {
		return;
	}

	printf(
		'<button type="button" class="lily-wishlist-btn" data-lily-wishlist-btn data-product-id="%1$d" aria-pressed="false" aria-label="%2$s">'
		. '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M12 20.7C6.4 17.3 3 14 3 10.2 3 7.6 5 5.5 7.6 5.5c1.7 0 3.3.9 4.4 2.4 1.1-1.5 2.7-2.4 4.4-2.4 2.6 0 4.6 2.1 4.6 4.7 0 3.8-3.4 7.1-9 10.5z"/></svg>'
		. '</button>',
		$product_id,
		esc_attr__( 'Add to wishlist', 'lily' )
	);
}

/**
 * Create the Wishlist page once (slug: wishlist). Reuses an existing page
 * with the same slug and never overwrites content.
 */
function lily_maybe_create_wishlist_page() {
	$existing = get_page_by_path( 'wishlist' );

	if ( $existing instanceof WP_Post ) {
		return;
	}

	wp_insert_post(
		array(
			'post_title'     => __( 'Wishlist', 'lily' ),
			'post_name'      => 'wishlist',
			'post_content'   => '',
			'post_status'    => 'publish',
			'post_type'      => 'page',
			'comment_status' => 'closed',
			'ping_status'    => 'closed',
		)
	);
}
add_action( 'init', 'lily_maybe_create_wishlist_page', 20 );

/**
 * Ensure the wishlist page has the same AJAX add-to-cart behavior as the Shop.
 */
function lily_wishlist_page_cart_assets() {
	if ( function_exists( 'is_page' ) && is_page( 'wishlist' ) && wp_script_is( 'wc-add-to-cart', 'registered' ) ) {
		wp_enqueue_script( 'wc-add-to-cart' );
	}
}
add_action( 'wp_enqueue_scripts', 'lily_wishlist_page_cart_assets', 30 );

/* ── REST: render wishlist products through the shared product card ─── */

add_action( 'rest_api_init', 'lily_register_wishlist_route' );

/**
 * GET /wp-json/lily/v1/wishlist?ids=1,2,3
 * Returns rendered product-card HTML for valid products only, so deleted
 * or unavailable products are skipped and pruned from storage client-side.
 */
function lily_register_wishlist_route() {
	register_rest_route(
		'lily/v1',
		'/wishlist',
		array(
			'methods'             => 'GET',
			'permission_callback' => '__return_true',
			'callback'            => 'lily_wishlist_rest',
			'args'                => array(
				'ids' => array(
					'type'              => 'string',
					'sanitize_callback' => 'sanitize_text_field',
				),
			),
		)
	);
}

/**
 * REST callback.
 *
 * @param WP_REST_Request $request Request.
 * @return WP_REST_Response
 */
function lily_wishlist_rest( WP_REST_Request $request ) {
	$raw = (string) $request->get_param( 'ids' );
	$ids = array_values(
		array_unique(
			array_filter(
				array_map( 'absint', explode( ',', $raw ) )
			)
		)
	);
	$ids = array_slice( $ids, 0, 100 );

	$products = array();
	$valid    = array();

	foreach ( $ids as $id ) {
		$product = function_exists( 'wc_get_product' ) ? wc_get_product( $id ) : null;

		if ( ! $product instanceof WC_Product || 'publish' !== $product->get_status() ) {
			continue; // Deleted, draft or otherwise unavailable — skip safely.
		}

		ob_start();
		get_template_part(
			'template-parts/components/product-card',
			null,
			array( 'product' => $product )
		);
		$html = trim( (string) ob_get_clean() );

		if ( '' === $html ) {
			continue;
		}

		$products[] = array(
			'id'   => $id,
			'html' => $html,
		);
		$valid[]    = $id;
	}

	return rest_ensure_response(
		array(
			'products' => $products,
			'valid'    => $valid,
		)
	);
}
