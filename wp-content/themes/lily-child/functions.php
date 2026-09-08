<?php
/**
 * Lily child theme bootstrap.
 *
 * @package Lily
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'LILY_THEME_VERSION', '0.5.0' );
define( 'LILY_THEME_DIR', get_stylesheet_directory() );
define( 'LILY_THEME_URI', get_stylesheet_directory_uri() );

$lily_includes = array(
	'inc/i18n-ar.php',
	'inc/setup.php',
	'inc/helpers.php',
	'inc/navigation.php',
	'inc/enqueue.php',
	'inc/admin-homepage.php',
	'inc/admin-about.php',
	'inc/admin-faq.php',
	'inc/admin-contact.php',
	'inc/admin-terms.php',
	'inc/admin-shipping-delivery.php',
	'inc/admin-returns-exchange.php',
	'inc/admin-privacy.php',
	'inc/admin-navigation.php',
	'inc/footer-settings.php',
	'inc/admin-pages-hub.php',
	'inc/term-meta.php',
	'inc/cover-images.php',
	'inc/product-meta.php',
	'inc/prescription.php',
	'inc/shipping.php',
	'inc/woocommerce.php',
	'inc/catalog-data.php',
	'inc/admin-catalog.php',
	'inc/reviews.php',
	'inc/lens-finder-rest.php',
	'inc/search.php',
	'inc/template-tags.php',
	'inc/content-pages.php',
	'inc/wishlist.php',
	'inc/coupons.php',
);

foreach ( $lily_includes as $lily_file ) {
	$lily_path = LILY_THEME_DIR . '/' . $lily_file;

	if ( file_exists( $lily_path ) ) {
		require_once $lily_path;
	}
}
