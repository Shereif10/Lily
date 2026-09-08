<?php
/**
 * Lily Returns & Exchange Policy page (slug: returns-exchange).
 *
 * Reuses the Terms & Conditions visual system exactly: the same hero
 * composition (breadcrumb, dominant title, intro — no eyebrow, so the
 * title appears once), the same numbered section rows, the same hairline
 * dividers and the same CTA band — all through the shared lily-terms-*
 * markup and CSS. Every visible string reads from the Lily dashboard
 * (Returns Page tab, same settings system as the other Lily pages) with
 * the complete approved bilingual Returns content as fallback.
 *
 * @package Lily
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* Same Terms page scope (reuses all Terms CSS). */
add_filter(
	'body_class',
	static function ( $classes ) {
		$classes[] = 'lily-terms-page';
		$classes[] = 'lily-returns-exchange-page';
		return $classes;
	}
);

get_header();

$lily_contact_page = get_page_by_path( 'contact' );
$lily_contact_url  = $lily_contact_page instanceof WP_Post ? get_permalink( $lily_contact_page ) : home_url( '/contact/' );

$lily_returns_approved = function_exists( 'lily_returns_approved_content' )
	? lily_returns_approved_content()
	: array(
		'hero_title_en' => 'Returns & Exchange Policy',
		'hero_intro_en' => '',
		'sections'      => array(),
	);

$lily_hero_title = lily_get_option( 'returns_hero_title', isset( $lily_returns_approved['hero_title_en'] ) ? $lily_returns_approved['hero_title_en'] : __( 'Returns & Exchange Policy', 'lily' ) );
$lily_hero_intro = lily_get_option( 'returns_hero_intro', isset( $lily_returns_approved['hero_intro_en'] ) ? $lily_returns_approved['hero_intro_en'] : '' );

$lily_hero_intro_paragraphs = array_values(
	array_filter(
		array_map( 'trim', preg_split( "/\n\s*\n/", str_replace( array( "\r\n", "\r" ), "\n", (string) $lily_hero_intro ) ) ),
		static function ( $paragraph ) {
			return '' !== $paragraph;
		}
	)
);

$lily_sections = array();
for ( $lily_i = 1; $lily_i <= 20; $lily_i++ ) {
	$lily_fallback = isset( $lily_returns_approved['sections'][ $lily_i - 1 ] ) && is_array( $lily_returns_approved['sections'][ $lily_i - 1 ] )
		? $lily_returns_approved['sections'][ $lily_i - 1 ]
		: array();

	$lily_sections[] = array(
		'num'   => sprintf( '%02d', $lily_i ),
		'title' => lily_get_option( "returns_{$lily_i}_title", isset( $lily_fallback['title_en'] ) ? $lily_fallback['title_en'] : '' ),
		'body'  => lily_get_option( "returns_{$lily_i}_body", isset( $lily_fallback['body_en'] ) ? $lily_fallback['body_en'] : '' ),
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

	<section class="lily-terms-band lily-terms-policy" aria-label="<?php esc_attr_e( 'Returns and exchange policy details', 'lily' ); ?>">
		<?php lily_container_open( 'lily-terms-policy__inner' ); ?>
			<ol class="lily-terms-list">
				<?php foreach ( $lily_sections as $lily_section ) : ?>
					<li class="lily-terms-row">
						<span class="lily-terms-row__num" aria-hidden="true"><?php echo esc_html( function_exists( 'lily_terms_num' ) ? lily_terms_num( $lily_section['num'] ) : $lily_section['num'] ); ?></span>
						<div class="lily-terms-row__content">
							<h2 class="lily-terms-row__title"><?php echo esc_html( $lily_section['title'] ); ?></h2>
							<div class="lily-terms-row__body"><?php lily_terms_body_html( $lily_section['body'] ); ?></div>
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
