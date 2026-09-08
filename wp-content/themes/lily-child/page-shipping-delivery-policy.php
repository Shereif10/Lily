<?php
/**
 * Lily Shipping & Delivery Policy page (slug: shipping-delivery-policy).
 *
 * Reuses the Terms & Conditions visual system exactly: the same hero
 * composition (breadcrumb, eyebrow, dominant title, intro), the same
 * numbered section rows, the same hairline dividers and the same CTA
 * band — all through the shared lily-terms-* markup and CSS. Every
 * visible string reads from the Lily dashboard (Shipping Page tab, same
 * settings system as the other Lily pages) with the complete approved
 * bilingual Shipping content as fallback. The delivery-fee table is the
 * only page-specific block, styled in the same legal-page language.
 *
 * @package Lily
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* Same Terms page scope (reuses all Terms CSS) plus a shipping hook for the table. */
add_filter(
	'body_class',
	static function ( $classes ) {
		$classes[] = 'lily-terms-page';
		$classes[] = 'lily-shipping-delivery-page';
		return $classes;
	}
);

get_header();

$lily_contact_page = get_page_by_path( 'contact' );
$lily_contact_url  = $lily_contact_page instanceof WP_Post ? get_permalink( $lily_contact_page ) : home_url( '/contact/' );

$lily_ship_approved = function_exists( 'lily_shipping_approved_content' )
	? lily_shipping_approved_content()
	: array(
		'hero_eyebrow_en' => 'SHIPPING & DELIVERY',
		'hero_title_en'   => 'Shipping & Delivery Policy',
		'hero_intro_en'   => '',
		'sections'        => array(),
		'table'           => array(),
	);

$lily_hero_title   = lily_get_option( 'ship_hero_title', isset( $lily_ship_approved['hero_title_en'] ) ? $lily_ship_approved['hero_title_en'] : __( 'Shipping & Delivery Policy', 'lily' ) );
$lily_hero_intro   = lily_get_option( 'ship_hero_intro', isset( $lily_ship_approved['hero_intro_en'] ) ? $lily_ship_approved['hero_intro_en'] : '' );

$lily_hero_intro_paragraphs = array_values(
	array_filter(
		array_map( 'trim', preg_split( "/\n\s*\n/", str_replace( array( "\r\n", "\r" ), "\n", (string) $lily_hero_intro ) ) ),
		static function ( $paragraph ) {
			return '' !== $paragraph;
		}
	)
);

$lily_sections = array();
for ( $lily_i = 1; $lily_i <= 22; $lily_i++ ) {
	$lily_fallback = isset( $lily_ship_approved['sections'][ $lily_i - 1 ] ) && is_array( $lily_ship_approved['sections'][ $lily_i - 1 ] )
		? $lily_ship_approved['sections'][ $lily_i - 1 ]
		: array();

	$lily_sections[] = array(
		'num'   => sprintf( '%02d', $lily_i ),
		'title' => lily_get_option( "ship_{$lily_i}_title", isset( $lily_fallback['title_en'] ) ? $lily_fallback['title_en'] : '' ),
		'body'  => lily_get_option( "ship_{$lily_i}_body", isset( $lily_fallback['body_en'] ) ? $lily_fallback['body_en'] : '' ),
	);
}

$lily_ship_after_3 = lily_get_option(
	'ship_3_after',
	isset( $lily_ship_approved['sections'][2]['body_after_en'] ) ? $lily_ship_approved['sections'][2]['body_after_en'] : ''
);

$lily_th_range = lily_get_option( 'ship_th_range', isset( $lily_ship_approved['th_range_en'] ) ? $lily_ship_approved['th_range_en'] : __( 'RANGE', 'lily' ) );
$lily_th_areas = lily_get_option( 'ship_th_areas', isset( $lily_ship_approved['th_areas_en'] ) ? $lily_ship_approved['th_areas_en'] : __( 'GOVERNORATES / AREAS', 'lily' ) );
$lily_th_fee   = lily_get_option( 'ship_th_fee', isset( $lily_ship_approved['th_fee_en'] ) ? $lily_ship_approved['th_fee_en'] : __( 'DELIVERY FEE', 'lily' ) );

$lily_table_rows = array();
for ( $lily_r = 1; $lily_r <= 11; $lily_r++ ) {
	$lily_row_fallback = isset( $lily_ship_approved['table'][ $lily_r - 1 ] ) && is_array( $lily_ship_approved['table'][ $lily_r - 1 ] )
		? $lily_ship_approved['table'][ $lily_r - 1 ]
		: array();

	$lily_table_rows[] = array(
		'range' => lily_get_option( "ship_row_{$lily_r}_range", isset( $lily_row_fallback['range_en'] ) ? $lily_row_fallback['range_en'] : '' ),
		'areas' => lily_get_option( "ship_row_{$lily_r}_areas", isset( $lily_row_fallback['areas_en'] ) ? $lily_row_fallback['areas_en'] : '' ),
		'fee'   => lily_get_option( "ship_row_{$lily_r}_fee", isset( $lily_row_fallback['fee_en'] ) ? $lily_row_fallback['fee_en'] : '' ),
	);
}
?>

<main id="primary" class="site-main lily-terms">
	<section class="lily-terms-band lily-terms-hero">
		<?php lily_container_open( 'lily-terms-hero__inner' ); ?>
			<nav class="lily-terms-crumbs" aria-label="<?php esc_attr_e( 'Breadcrumb', 'lily' ); ?>">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php echo esc_html_x( 'Home', 'breadcrumb', 'woocommerce' ); ?></a>
				<span class="lily-terms-crumbs__sep" aria-hidden="true">/</span>
				<span aria-current="page"><?php echo esc_html( $lily_hero_title ); ?></span>
			</nav>
			<h1 class="lily-terms-hero__title"><?php echo esc_html( $lily_hero_title ); ?></h1>
			<span class="lily-terms-hero__rule" aria-hidden="true"></span>
			<?php foreach ( $lily_hero_intro_paragraphs as $lily_intro_paragraph ) : ?>
				<p class="lily-terms-hero__sub"><?php echo esc_html( $lily_intro_paragraph ); ?></p>
			<?php endforeach; ?>
		<?php lily_container_close(); ?>
	</section>

	<section class="lily-terms-band lily-terms-policy" aria-label="<?php esc_attr_e( 'Shipping and delivery policy details', 'lily' ); ?>">
		<?php lily_container_open( 'lily-terms-policy__inner' ); ?>
			<ol class="lily-terms-list">
				<?php foreach ( $lily_sections as $lily_index => $lily_section ) : ?>
					<li class="lily-terms-row">
						<span class="lily-terms-row__num" aria-hidden="true"><?php echo esc_html( function_exists( 'lily_terms_num' ) ? lily_terms_num( $lily_section['num'] ) : $lily_section['num'] ); ?></span>
						<div class="lily-terms-row__content">
							<h2 class="lily-terms-row__title"><?php echo esc_html( $lily_section['title'] ); ?></h2>
							<div class="lily-terms-row__body"><?php lily_terms_body_html( $lily_section['body'] ); ?></div>
							<?php if ( 2 === $lily_index ) : ?>
								<div class="lily-ship-table__wrap">
									<table class="lily-ship-table">
										<thead>
											<tr>
												<th scope="col"><?php echo esc_html( $lily_th_range ); ?></th>
												<th scope="col"><?php echo esc_html( $lily_th_areas ); ?></th>
												<th scope="col"><?php echo esc_html( $lily_th_fee ); ?></th>
											</tr>
										</thead>
										<tbody>
											<?php foreach ( $lily_table_rows as $lily_table_row ) : ?>
												<tr>
													<td><span class="lily-ship-table__m-label" aria-hidden="true"><?php echo esc_html( $lily_th_range ); ?></span><?php echo esc_html( $lily_table_row['range'] ); ?></td>
													<td><span class="lily-ship-table__m-label" aria-hidden="true"><?php echo esc_html( $lily_th_areas ); ?></span><?php echo esc_html( $lily_table_row['areas'] ); ?></td>
													<td><span class="lily-ship-table__m-label" aria-hidden="true"><?php echo esc_html( $lily_th_fee ); ?></span><?php echo esc_html( $lily_table_row['fee'] ); ?></td>
												</tr>
											<?php endforeach; ?>
										</tbody>
									</table>
								</div>
								<?php if ( '' !== trim( $lily_ship_after_3 ) ) : ?>
									<div class="lily-terms-row__body"><?php lily_terms_body_html( $lily_ship_after_3 ); ?></div>
								<?php endif; ?>
							<?php endif; ?>
						</div>
					</li>
				<?php endforeach; ?>
			</ol>
		<?php lily_container_close(); ?>
	</section>

	<section class="lily-terms-band lily-terms-cta" aria-labelledby="lily-terms-cta-heading">
		<?php lily_container_open( 'lily-terms-cta__inner' ); ?>
			<p class="lily-terms-eyebrow lily-terms-eyebrow--center"><?php esc_html_e( 'Need more help?', 'lily' ); ?></p>
			<p class="lily-terms-cta__lead" id="lily-terms-cta-heading"><?php esc_html_e( 'If you have any questions about these terms, we are here to help.', 'lily' ); ?></p>
			<a class="lily-button lily-terms-cta__btn" href="<?php echo esc_url( $lily_contact_url ); ?>"><?php esc_html_e( 'Contact Us', 'lily' ); ?> <span aria-hidden="true">&rarr;</span></a>
		<?php lily_container_close(); ?>
	</section>
</main>

<?php
get_footer();
