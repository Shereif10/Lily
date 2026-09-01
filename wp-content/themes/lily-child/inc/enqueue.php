<?php
/**
 * Frontend assets.
 *
 * @package Lily
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueue Lily styles and scripts.
 */
function lily_enqueue_assets() {
	$style_dependencies = wp_style_is( 'astra-theme-css', 'registered' ) ? array( 'astra-theme-css' ) : array();

	wp_enqueue_style(
		'lily',
		LILY_THEME_URI . '/assets/css/lily.css',
		$style_dependencies,
		LILY_THEME_VERSION
	);

	wp_enqueue_script(
		'lily',
		LILY_THEME_URI . '/assets/js/lily.js',
		array(),
		LILY_THEME_VERSION,
		true
	);

	if ( function_exists( 'WC' ) && function_exists( 'wc_get_checkout_url' ) ) {
		wp_localize_script(
			'lily',
			'lilyCartConfig',
			array(
				'ajaxUrl'     => esc_url_raw( admin_url( 'admin-ajax.php' ) ),
				'checkoutUrl' => esc_url_raw( wc_get_checkout_url() ),
				'shopUrl'     => esc_url_raw( wc_get_page_permalink( 'shop' ) ),
			)
		);
	}

	wp_localize_script(
		'lily',
		'lilyI18n',
		array(
			'openMenu'  => esc_html__( 'Open menu', 'lily' ),
			'closeMenu' => esc_html__( 'Close menu', 'lily' ),
			'prevImage' => esc_html__( 'Previous image', 'lily' ),
			'nextImage' => esc_html__( 'Next image', 'lily' ),
			'increaseQty' => esc_html__( 'Increase quantity', 'lily' ),
			'decreaseQty' => esc_html__( 'Decrease quantity', 'lily' ),
		)
	);

	if ( lily_is_rtl_request() ) {
		wp_enqueue_style(
			'lily-rtl',
			LILY_THEME_URI . '/assets/css/rtl.css',
			array( 'lily' ),
			LILY_THEME_VERSION
		);
	}

	if ( is_page_template( 'page-templates/template-home.php' ) || is_front_page() ) {
		wp_enqueue_script(
			'lily-lens-finder',
			LILY_THEME_URI . '/assets/js/lens-finder.js',
			array(),
			LILY_THEME_VERSION,
			true
		);

		wp_localize_script(
			'lily-lens-finder',
			'lilyLensFinder',
			lily_get_lens_finder_frontend_config()
		);
	}
}
add_action( 'wp_enqueue_scripts', 'lily_enqueue_assets', 20 );
