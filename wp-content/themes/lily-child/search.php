<?php
/**
 * Lily product search results.
 *
 * When the Lily bilingual product search took over the main query
 * (inc/search.php — products only), results render through the ONE shared
 * product card system, exactly like the Shop grid. When no product matched,
 * the original Astra search template behavior is preserved untouched.
 *
 * @package Lily
 */

/* The Shop grid CSS scope applies to search results too. */
add_filter(
	'body_class',
	static function ( $classes ) {
		$classes[] = 'lily-shop-page';
		$classes[] = 'lily-search-page';
		return $classes;
	}
);

get_header();

$lily_search_products = is_search() && $GLOBALS['wp_query']->post_count > 0 && 'product' === (string) $GLOBALS['wp_query']->get( 'post_type' );

if ( $lily_search_products ) :
	$lily_search_query = trim( wp_strip_all_tags( (string) get_search_query() ) );
	?>
	<section class="lily-shop lily-search">
		<?php lily_container_open( 'lily-shop__inner' ); ?>

			<header class="lily-shop__header">
				<h1 class="lily-shop__title">
					<?php
					printf(
						/* translators: %s: search query. */
						esc_html__( 'Search results for: %s', 'lily' ),
						'<span class="lily-search__term">' . esc_html( $lily_search_query ) . '</span>'
					);
					?>
				</h1>
				<?php
				$lily_search_count = (int) $GLOBALS['wp_query']->found_posts;
				printf(
					'<p class="lily-shop__description">%s</p>',
					esc_html( sprintf( _n( '%s Product', '%s Products', $lily_search_count, 'lily' ), number_format_i18n( $lily_search_count ) ) )
				);
				?>
			</header>

			<?php if ( have_posts() ) : ?>
				<div class="lily-shop-grid lily-products-row--results lily-search__grid">
					<?php
					while ( have_posts() ) {
						the_post();
						global $product;

						if ( ! $product instanceof WC_Product ) {
							$product = wc_get_product( get_the_ID() );
						}

						if ( ! $product ) {
							continue;
						}

						get_template_part(
							'template-parts/components/product-card',
							null,
							array(
								'product' => $product,
							)
						);
					}
					?>
				</div>

				<?php
				the_posts_pagination(
					array(
						'mid_size'  => 2,
						'prev_text' => __( '&larr; Previous', 'lily' ),
						'next_text' => __( 'Next &rarr;', 'lily' ),
					)
				);
				?>
			<?php else : ?>
				<div class="lily-shop-empty">
					<p><?php esc_html_e( 'No products were found matching your selection.', 'lily' ); ?></p>
				</div>
			<?php endif; ?>

		<?php lily_container_close(); ?>
	</section>
	<?php
else :
	/* No product match — the existing page/post search behavior, untouched. */
	if ( astra_page_layout() === 'left-sidebar' ) {
		get_sidebar();
	}
	?>
	<div id="primary" <?php astra_primary_class(); ?>>
		<?php astra_primary_content_top(); ?>
		<?php astra_archive_header(); ?>
		<?php astra_content_loop(); ?>
		<?php astra_pagination(); ?>
		<?php astra_primary_content_bottom(); ?>
	</div>
	<?php
	if ( astra_page_layout() === 'right-sidebar' ) {
		get_sidebar();
	}
endif;

get_footer();
