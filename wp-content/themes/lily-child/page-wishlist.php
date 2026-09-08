<?php
/**
 * Lily Wishlist page (slug: wishlist).
 *
 * Wishlist-only template: centered editorial masthead, a quiet item count,
 * the shared Shop product grid (populated client-side from the guest
 * wishlist in localStorage via the lily/v1/wishlist endpoint) and a calm
 * empty state. Uses the existing global header/footer and the shared
 * lily-container helper. Add-to-cart markup is identical to the Shop, so
 * cart behavior matches the rest of the site.
 *
 * @package Lily
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* Wishlist-only body class so the Wishlist CSS scope applies without touching globals. */
add_filter(
	'body_class',
	static function ( $classes ) {
		$classes[] = 'lily-wishlist-page';
		return $classes;
	}
);

get_header();

$lily_shop_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );
?>

<main id="primary" class="site-main lily-wishlist">
	<?php lily_container_open( 'lily-wishlist__crumbs-wrap' ); ?>
		<nav class="lily-wishlist__crumbs" aria-label="<?php esc_attr_e( 'Breadcrumb', 'lily' ); ?>">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'lily' ); ?></a>
			<span class="lily-wishlist__crumbs-sep" aria-hidden="true">/</span>
			<span aria-current="page"><?php esc_html_e( 'Wishlist', 'lily' ); ?></span>
		</nav>
	<?php lily_container_close(); ?>
	<section class="lily-wishlist__masthead" aria-labelledby="lily-wishlist-title">
		<?php lily_container_open( 'lily-wishlist__masthead-inner' ); ?>
			<h1 class="lily-wishlist__title" id="lily-wishlist-title"><?php esc_html_e( 'My Wishlist', 'lily' ); ?></h1>
			<p class="lily-wishlist__subtitle"><?php esc_html_e( 'Your favorite Lily lenses, saved for later.', 'lily' ); ?></p>
		<?php lily_container_close(); ?>
	</section>

	<section class="lily-wishlist__collection" aria-label="<?php esc_attr_e( 'Saved products', 'lily' ); ?>">
		<?php lily_container_open( 'lily-wishlist__collection-inner' ); ?>
			<p class="lily-wishlist__count" data-lily-wishlist-count hidden></p>
			<div class="lily-shop-grid lily-wishlist__grid" data-lily-wishlist-grid hidden></div>
			<div class="lily-wishlist__empty" data-lily-wishlist-empty>
				<p class="lily-wishlist__eyebrow"><?php esc_html_e( 'Wishlist', 'lily' ); ?></p>
				<h2 class="lily-wishlist__empty-title"><?php esc_html_e( 'Your Wishlist is Empty', 'lily' ); ?></h2>
				<p class="lily-wishlist__empty-text"><?php esc_html_e( 'Save your favorite lenses and find them here anytime.', 'lily' ); ?></p>
				<a class="lily-wishlist__cta" href="<?php echo esc_url( $lily_shop_url ); ?>"><?php esc_html_e( 'Shop Lenses', 'lily' ); ?> <span class="lily-wishlist__cta-arrow" aria-hidden="true">&rarr;</span></a>
			</div>
		<?php lily_container_close(); ?>
	</section>
</main>

<?php
get_footer();
