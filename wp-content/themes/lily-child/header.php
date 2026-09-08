<?php
/**
 * Site header.
 *
 * @package Lily
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e( 'Skip to content', 'lily' ); ?></a>
<?php get_template_part( 'template-parts/homepage/announcement-bar' ); ?>
<header class="lily-site-header" role="banner">
	<?php lily_container_open( 'lily-site-header__inner' ); ?>
		<div class="lily-site-branding">
			<?php
			if ( has_custom_logo() ) {
				the_custom_logo();
			} else {
				printf(
					'<a class="lily-site-title" href="%1$s">%2$s</a>',
					esc_url( home_url( '/' ) ),
					esc_html( get_bloginfo( 'name' ) )
				);
			}
			?>
		</div>

		<nav class="lily-primary-nav" aria-label="<?php esc_attr_e( 'Primary navigation', 'lily' ); ?>">
			<?php lily_render_primary_nav(); ?>

			<?php if ( shortcode_exists( 'language-switcher' ) ) : ?>
				<div class="lily-language-switcher lily-language-switcher--drawer"><?php echo do_shortcode( '[language-switcher]' ); ?></div>
			<?php endif; ?>
		</nav>

		<div class="lily-header-actions">
			<button class="lily-icon-btn lily-search-toggle" type="button" aria-expanded="false" aria-controls="lily-search-bar">
				<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
					<circle cx="11" cy="11" r="7"></circle>
					<line x1="21" y1="21" x2="16.3" y2="16.3"></line>
				</svg>
				<span class="screen-reader-text"><?php esc_html_e( 'Search', 'lily' ); ?></span>
			</button>

			<?php if ( shortcode_exists( 'language-switcher' ) ) : ?>
				<div class="lily-language-switcher"><?php echo do_shortcode( '[language-switcher]' ); ?></div>
			<?php endif; ?>

			<?php if ( function_exists( 'wc_get_cart_url' ) ) : ?>
				<?php
				$lily_wishlist_page = get_page_by_path( 'wishlist' );
				$lily_wishlist_url  = $lily_wishlist_page instanceof WP_Post ? get_permalink( $lily_wishlist_page ) : home_url( '/wishlist/' );
				?>
				<a class="lily-wishlist-link" href="<?php echo esc_url( $lily_wishlist_url ); ?>" aria-label="<?php esc_attr_e( 'Wishlist', 'lily' ); ?>">
					<svg width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
						<path d="M12 20.7C6.4 17.3 3 14 3 10.2 3 7.6 5 5.5 7.6 5.5c1.7 0 3.3.9 4.4 2.4 1.1-1.5 2.7-2.4 4.4-2.4 2.6 0 4.6 2.1 4.6 4.7 0 3.8-3.4 7.1-9 10.5z"></path>
					</svg>
					<span class="screen-reader-text"><?php esc_html_e( 'Wishlist', 'lily' ); ?></span>
					<span class="lily-wishlist-count" data-lily-wishlist-nav-count hidden></span>
				</a>
				<a class="lily-cart-link" href="<?php echo esc_url( wc_get_cart_url() ); ?>">
					<svg class="lily-cart-icon" width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
						<path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
						<line x1="3" y1="6" x2="21" y2="6"></line>
						<path d="M16 10a4 4 0 0 1-8 0"></path>
					</svg>
					<span class="screen-reader-text"><?php esc_html_e( 'Cart', 'lily' ); ?></span>
					<?php if ( WC()->cart ) : ?>
						<span class="lily-cart-count"><?php echo esc_html( WC()->cart->get_cart_contents_count() ); ?></span>
					<?php endif; ?>
				</a>
			<?php endif; ?>

			<button class="lily-menu-toggle" type="button" aria-controls="lily-primary-menu" aria-expanded="false">
				<span class="lily-menu-toggle__line"></span>
				<span class="screen-reader-text"><?php esc_html_e( 'Open menu', 'lily' ); ?></span>
			</button>
		</div>
	<?php lily_container_close(); ?>

	<div class="lily-search-bar" id="lily-search-bar" hidden>
		<?php lily_container_open( 'lily-search-bar__inner' ); ?>
			<form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
				<input type="search" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="<?php esc_attr_e( 'Search lenses, brands...', 'lily' ); ?>" aria-label="<?php esc_attr_e( 'Search', 'lily' ); ?>">
				<button class="lily-icon-btn" type="submit">
					<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
						<circle cx="11" cy="11" r="7"></circle>
						<line x1="21" y1="21" x2="16.3" y2="16.3"></line>
					</svg>
					<span class="screen-reader-text"><?php esc_html_e( 'Search', 'lily' ); ?></span>
				</button>
			</form>
		<?php lily_container_close(); ?>
	</div>
</header>
<main id="primary" class="site-main">
