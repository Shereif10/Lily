<?php
/**
 * Lily Coupons — native WooCommerce coupons, exactly one per order.
 *
 * All coupon settings (code, type, amount, expiry, usage limits, spend
 * limits, product/category/email restrictions, free shipping, sale
 * exclusion) stay 100% native in the WooCommerce dashboard. This file adds
 * only the single-coupon invariant, enforced server-side so no entry point
 * (cart, checkout, AJAX, refresh) can stack two codes.
 *
 * @package Lily
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Replace behavior: when a coupon passes native validation and is applied,
 * silently drop any other active coupon. remove_coupon() adds no notices
 * and triggers no recalculation loop, so the UX stays a single success.
 *
 * @param string $coupon_code Newly applied coupon code.
 */
function lily_keep_single_coupon( $coupon_code ) {
	if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
		return;
	}

	$new = wc_format_coupon_code( $coupon_code );

	foreach ( WC()->cart->get_applied_coupons() as $applied ) {
		if ( ! wc_is_same_coupon( $applied, $new ) ) {
			WC()->cart->remove_coupon( $applied );
		}
	}
}
add_action( 'woocommerce_applied_coupon', 'lily_keep_single_coupon', 10, 1 );

/**
 * Sessions created before this rule (or via any direct path) could hold
 * two codes — trim back to the newest on every session load.
 */
function lily_trim_session_coupons() {
	if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
		return;
	}

	$applied = WC()->cart->get_applied_coupons();

	if ( count( $applied ) > 1 ) {
		foreach ( array_slice( $applied, 0, -1 ) as $old ) {
			WC()->cart->remove_coupon( $old );
		}
	}
}
add_action( 'woocommerce_cart_loaded_from_session', 'lily_trim_session_coupons', 20 );
