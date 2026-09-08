<?php
/**
 * Brands section — full-width olive logo marquee.
 *
 * @package Lily
 */

if ( ! defined( 'ABSPATH' ) || ! lily_show_section( 'show_brands' ) ) {
	return;
}

$brands = lily_get_option( 'brands', array() );

$brands = array_values(
	array_filter(
		(array) $brands,
		static function ( $brand ) {
			return is_array( $brand );
		}
	)
);

if ( empty( $brands ) ) {
	$brands = array();
}

/*
 * Resolve each brand row from the REAL Brand taxonomy.
 * Identity = the selected term. Logo = term meta (placeholder when missing).
 * Destination = optional custom URL override, otherwise the automatic
 * brand-filtered shop URL for that exact term.
 */
$lily_brand_taxonomy = lily_get_brand_taxonomy();
$lily_placeholder    = LILY_THEME_URI . '/assets/images/brand-placeholder.svg';

/**
 * Build one homepage brand item from a taxonomy term + optional override URL.
 *
 * @param WP_Term $term      Brand term.
 * @param string  $custom_url Optional custom destination URL.
 * @return array|null
 */
function lily_resolve_brand_item( $term, $custom_url = '' ) {
	if ( ! $term || is_wp_error( $term ) ) {
		return null;
	}

	$logo_id = (int) get_term_meta( $term->term_id, 'brand_logo', true );
	$url     = '' !== trim( (string) $custom_url ) ? $custom_url : lily_get_attribute_filter_url( lily_get_brand_taxonomy(), $term );

	return array(
		'logo'   => $logo_id,
		'url'    => $url,
		'name'   => function_exists( 'lily_term_name' ) ? lily_term_name( $term ) : $term->name,
	);
}

$lily_rows        = array_values( (array) $brands );
$lily_brand_items = array();

foreach ( $lily_rows as $lily_row ) {
	if ( ! is_array( $lily_row ) || empty( $lily_row['brand_term'] ) ) {
		continue;
	}

	$lily_term = get_term( absint( $lily_row['brand_term'] ), $lily_brand_taxonomy );
	$lily_item = null;

	if ( $lily_term && ! is_wp_error( $lily_term ) ) {
		$lily_custom = isset( $lily_row['url'] ) ? trim( (string) $lily_row['url'] ) : '';
		$lily_item   = lily_resolve_brand_item( $lily_term, $lily_custom );
	}

	if ( $lily_item ) {
		$lily_item['target'] = ! empty( $lily_row['target'] ) && '_blank' === $lily_row['target'] ? '_blank' : '_self';
		$lily_brand_items[]  = $lily_item;
	}
}

// No rows configured: every real brand becomes available automatically.
if ( empty( $lily_brand_items ) && $lily_brand_taxonomy ) {
	foreach ( get_terms( array( 'taxonomy' => $lily_brand_taxonomy, 'hide_empty' => false ) ) as $lily_term ) {
		if ( is_wp_error( $lily_term ) ) {
			continue;
		}

		$lily_item = lily_resolve_brand_item( $lily_term );

		if ( $lily_item ) {
			$lily_item['target'] = '_self';
			$lily_brand_items[]  = $lily_item;
		}
	}
}

// Always a continuous seamless marquee (~40px/sec at the default logo scale).
$lily_brand_duration = max( 20, (int) round( count( $lily_brand_items ) * 3.2 ) );

/**
 * Render one set of brand logos.
 *
 * @param array $items Brand items.
 */
function lily_render_brand_set( array $items, $placeholder ) {
	foreach ( $items as $lily_item ) {
		// Fall back to the placeholder when the logo attachment no longer exists.
		if ( $lily_item['logo'] && get_post( $lily_item['logo'] ) ) {
			$lily_img = wp_get_attachment_image(
				$lily_item['logo'],
				'medium',
				false,
				array(
					'class'   => 'lily-brand__logo',
					'loading' => 'lazy',
					'alt'     => $lily_item['name'],
				)
			);
		} else {
			$lily_img = sprintf(
				'<img class="lily-brand__logo lily-brand__logo--placeholder" src="%1$s" alt="%2$s" loading="lazy" width="220" height="64">',
				esc_url( $placeholder ),
				esc_attr( $lily_item['name'] )
			);
		}
		?>
		<li class="lily-brand">
			<?php if ( '' !== $lily_item['url'] ) : ?>
				<a href="<?php echo esc_url( $lily_item['url'] ); ?>" target="<?php echo esc_attr( $lily_item['target'] ); ?>"<?php echo '_blank' === $lily_item['target'] ? ' rel="noopener noreferrer"' : ''; ?>>
					<?php echo $lily_img; ?>
				</a>
			<?php else : ?>
				<span class="lily-brand__static">
					<?php echo $lily_img; ?>
				</span>
			<?php endif; ?>
		</li>
		<?php
	}
}
?>
<section class="lily-brands" aria-label="<?php esc_attr_e( 'Our brands', 'lily' ); ?>">
	<div class="lily-brands__strip" role="region" aria-label="<?php esc_attr_e( 'Our brands', 'lily' ); ?>">
		<div class="lily-brands__track" style="--lily-marquee-duration:<?php echo esc_attr( $lily_brand_duration ); ?>s;">
			<ul class="lily-brands__set">
				<?php lily_render_brand_set( $lily_brand_items, $lily_placeholder ); ?>
			</ul>
			<ul class="lily-brands__set" aria-hidden="true">
				<?php lily_render_brand_set( $lily_brand_items, $lily_placeholder ); ?>
			</ul>
		</div>
	</div>
</section>
