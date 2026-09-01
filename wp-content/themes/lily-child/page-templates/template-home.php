<?php
/**
 * Template Name: Lily Homepage
 *
 * @package Lily
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

get_template_part( 'template-parts/homepage/hero' );
get_template_part( 'template-parts/homepage/brands' );
get_template_part( 'template-parts/homepage/shop-by-collections' );
get_template_part( 'template-parts/homepage/shop-by-colors' );
get_template_part( 'template-parts/homepage/best-sellers' );
get_template_part( 'template-parts/homepage/find-your-best-lenses' );

get_footer();

