<?php
/**
 * Theme setup and dependency notices.
 *
 * @package Lily
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register theme support.
 */
function lily_theme_setup() {
	load_child_theme_textdomain( 'lily', LILY_THEME_DIR . '/languages' );

	add_theme_support( 'woocommerce' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'align-wide' );

	register_nav_menus(
		array(
			'primary'          => esc_html__( 'Primary Navigation', 'lily' ),
			'footer_shop'      => esc_html__( 'Footer Shop Links', 'lily' ),
			'footer_service'   => esc_html__( 'Footer Customer Service Links', 'lily' ),
			'footer_secondary' => esc_html__( 'Footer Secondary Links', 'lily' ),
		)
	);
}
add_action( 'after_setup_theme', 'lily_theme_setup' );

/**
 * Show setup notices for required project dependencies.
 */
function lily_dependency_admin_notice() {
	if ( ! current_user_can( 'activate_plugins' ) ) {
		return;
	}

	$missing = array();

	if ( ! class_exists( 'WooCommerce' ) ) {
		$missing[] = esc_html__( 'WooCommerce', 'lily' );
	}

	if ( empty( $missing ) ) {
		return;
	}

	printf(
		'<div class="notice notice-warning"><p>%s</p></div>',
		esc_html(
			sprintf(
				/* translators: %s: comma-separated plugin names. */
				__( 'Lily is ready, but the following required plugins must be installed and activated to finish setup: %s.', 'lily' ),
				implode( ', ', $missing )
			)
		)
	);
}
add_action( 'admin_notices', 'lily_dependency_admin_notice' );

/**
 * Add RTL direction to TranslatePress RTL language URLs.
 *
 * @param string $output Language attributes.
 * @return string
 */
function lily_language_attributes( $output ) {
	if ( ! lily_is_rtl_request() || false !== strpos( $output, 'dir=' ) ) {
		return $output;
	}

	return trim( $output . ' dir="rtl"' );
}
add_filter( 'language_attributes', 'lily_language_attributes' );
