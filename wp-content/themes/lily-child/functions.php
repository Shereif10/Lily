<?php
/**
 * Lily child theme bootstrap.
 *
 * @package Lily
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'LILY_THEME_VERSION', '0.4.0' );
define( 'LILY_THEME_DIR', get_stylesheet_directory() );
define( 'LILY_THEME_URI', get_stylesheet_directory_uri() );

$lily_includes = array(
	'inc/setup.php',
	'inc/helpers.php',
	'inc/navigation.php',
	'inc/enqueue.php',
	'inc/admin-homepage.php',
	'inc/admin-navigation.php',
	'inc/term-meta.php',
	'inc/product-meta.php',
	'inc/prescription.php',
	'inc/shipping.php',
	'inc/woocommerce.php',
	'inc/lens-finder-rest.php',
	'inc/template-tags.php',
	'inc/content-pages.php',
);

foreach ( $lily_includes as $lily_file ) {
	$lily_path = LILY_THEME_DIR . '/' . $lily_file;

	if ( file_exists( $lily_path ) ) {
		require_once $lily_path;
	}
}
