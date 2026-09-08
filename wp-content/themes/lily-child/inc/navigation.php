<?php
/**
 * Lily primary navigation: dynamic structure + native fallback renderer.
 *
 * When a WordPress menu is assigned to the "Primary Navigation" location it
 * is used as-is. Otherwise this file renders the built-in Lily navigation,
 * wired to WooCommerce attributes/categories and WordPress pages.
 *
 * @package Lily
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Default navigation settings.
 *
 * @return array
 */
function lily_navigation_settings_defaults() {
	static $defaults = null;

	if ( null !== $defaults ) {
		return $defaults;
	}

	$defaults = array(
		'logo'                 => 0,
		'show_shop'            => 1,
		'shop_label'          => 'Shop',
		'shop_label_ar'       => 'تسوقي',
		'show_colored'         => 1,
		'colored_label'        => 'Colored Lenses',
		'colored_label_ar'     => 'عدسات ملونة',
		'show_clear'           => 1,
		'clear_label'          => 'Clear Lenses',
		'clear_label_ar'       => 'عدسات شفافة',
		'show_accessories'     => 1,
		'accessories_label'    => 'Accessories & Lens Care',
		'accessories_label_ar' => 'إكسسوارات والعناية بالعدسات',
		'accessories_url'      => '',
		'show_find'            => 1,
		'find_label'           => 'Find My Lenses',
		'find_label_ar'        => 'اعثري على عدساتك',
		'find_url'             => '',
		'show_company'         => 1,
		'company_label'        => 'Company',
		'company_label_ar'     => 'الشركة',
		'about_page'           => 0,
		'faqs_page'            => 0,
		'contact_page'         => 0,
	);

	return $defaults;
}

/**
 * Read a navigation setting safely.
 *
 * @param string $name    Field name.
 * @param mixed  $default Default value.
 * @return mixed
 */
function lily_nav_get_option( $name, $default = null ) {
	$settings = get_option( 'lily_navigation_settings', array() );
	$settings = is_array( $settings ) && $settings
		? wp_parse_args( $settings, lily_navigation_settings_defaults() )
		: lily_navigation_settings_defaults();

	// Bilingual dashboard: on Arabic requests a non-empty "<name>_ar"
	// label wins; empty Arabic falls back to the shared Arabic dictionary
	// (real Arabic for known Lily labels), then to English. Scalar only.
	if ( function_exists( 'lily_is_arabic_request' ) && lily_is_arabic_request() && ! is_array( $default ) ) {
		$ar_key = $name . '_ar';

		if ( isset( $settings[ $ar_key ] ) && is_scalar( $settings[ $ar_key ] ) && '' !== trim( (string) $settings[ $ar_key ] ) ) {
			return $settings[ $ar_key ];
		}
	}

	$value = isset( $settings[ $name ] ) ? $settings[ $name ] : null;
	$value = null === $value ? $default : $value;

	if ( function_exists( 'lily_is_arabic_request' ) && lily_is_arabic_request() && ! is_array( $default ) && is_string( $value ) && '' !== trim( $value ) && function_exists( 'lily_ar_fallback' ) ) {
		return lily_ar_fallback( $value );
	}

	return $value;
}

/**
 * Get the first existing taxonomy from a list of candidates.
 *
 * @param string[] $candidates Taxonomy names.
 * @return string Empty string when none exist.
 */
function lily_nav_find_taxonomy( $candidates ) {
	foreach ( (array) $candidates as $taxonomy ) {
		if ( taxonomy_exists( $taxonomy ) ) {
			return $taxonomy;
		}
	}

	return '';
}

/**
 * Build filtered shop links for every term of an attribute taxonomy.
 *
 * Labels are the localized term names (stored Arabic name on Arabic
 * requests) so navbar entries, product data and filters always agree.
 * `$parents_only` keeps only top-level terms (Parent Colors in the navbar).
 *
 * @param string $taxonomy     Attribute taxonomy.
 * @param bool   $parents_only Restrict to top-level terms.
 * @return array[] Array of [ 'label' => string, 'url' => string ].
 */
function lily_nav_term_links( $taxonomy, $parents_only = false ) {
	if ( ! $taxonomy || ! taxonomy_exists( $taxonomy ) ) {
		return array();
	}

	$args = array(
		'taxonomy'   => $taxonomy,
		'hide_empty' => false,
	);

	if ( $parents_only ) {
		$args['parent'] = 0;
	}

	$terms = get_terms( $args );

	if ( is_wp_error( $terms ) || empty( $terms ) ) {
		return array();
	}

	$links = array();

	foreach ( $terms as $term ) {
		$links[] = array(
			'label' => function_exists( 'lily_term_name' ) ? lily_term_name( $term ) : $term->name,
			'url'   => lily_get_attribute_filter_url( $taxonomy, $term ),
		);
	}

	return $links;
}

/**
 * Link for a product category slug, falling back to the shop page.
 *
 * @param string $slug Category slug.
 * @return string
 */
function lily_nav_product_cat_url( $slug ) {
	$term = get_term_by( 'slug', $slug, 'product_cat' );

	if ( $term && ! is_wp_error( $term ) ) {
		$link = get_term_link( $term );

		if ( ! is_wp_error( $link ) ) {
			return $link;
		}
	}

	return function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' );
}

/**
 * Resolve a Company page URL from a settings page ID, with slug auto-detect.
 *
 * @param string   $key        Settings field name.
 * @param string[] $slug_slugs Candidate slugs for auto-detection.
 * @return string Empty string when no page is available.
 */
function lily_nav_page_url( $key, $slug_slugs ) {
	$page_id = (int) lily_nav_get_option( $key, 0 );

	if ( $page_id && 'publish' === get_post_status( $page_id ) ) {
		return get_permalink( $page_id );
	}

	foreach ( (array) $slug_slugs as $slug ) {
		$page = get_page_by_path( $slug );

		if ( $page && 'publish' === $page->post_status ) {
			return get_permalink( $page );
		}
	}

	return '';
}

/**
 * Build the fallback navigation structure.
 *
 * @return array[] Top-level items: label, url (simple) or columns (dropdown).
 */
function lily_nav_get_structure() {
	$brand_taxonomy = lily_nav_find_taxonomy( array( 'pa_brand', 'product_brand' ) );

	$items = array();

	// Shop — the main catalog entry point.
	if ( lily_nav_get_option( 'show_shop', 1 ) ) {
		$items[] = array(
			'label' => lily_nav_get_option( 'shop_label', __( 'Shop', 'lily' ) ),
			'url'   => function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' ),
		);
	}

	// Colored Lenses.
	if ( lily_nav_get_option( 'show_colored', 1 ) ) {
		$columns = array();

		$brands = lily_nav_term_links( $brand_taxonomy );
		if ( $brands ) {
			$columns[] = array(
				'title' => __( 'By Brand', 'lily' ),
				'links' => $brands,
			);
		}

		$looks = lily_nav_term_links( lily_nav_find_taxonomy( array( 'pa_look' ) ) );
		if ( $looks ) {
			$columns[] = array(
				'title' => __( 'By Look / Effect', 'lily' ),
				'links' => $looks,
			);
		}

		// Parent Colors only in the navigation — shades are product data.
		$lily_color_taxonomy = lily_nav_find_taxonomy( array( 'pa_color' ) );
		$colors = lily_nav_term_links( $lily_color_taxonomy, true );
		if ( $colors ) {
			$columns[] = array(
				'title' => __( 'By Color', 'lily' ),
				'links' => $colors,
			);
		}

		$prescription = lily_nav_term_links( lily_nav_find_taxonomy( array( 'pa_prescription' ) ) );
		if ( $prescription ) {
			$columns[] = array(
				'title' => __( 'By Prescription', 'lily' ),
				'links' => $prescription,
			);
		}

		$durations = lily_nav_term_links( lily_nav_find_taxonomy( array( 'pa_duration' ) ) );
		if ( $durations ) {
			$columns[] = array(
				'title' => __( 'By Duration', 'lily' ),
				'links' => $durations,
			);
		}

		$items[] = array(
			'label'   => lily_nav_get_option( 'colored_label', __( 'Colored Lenses', 'lily' ) ),
			'columns' => array_merge(
				$columns,
				array(
					array(
						'title' => __( 'Shop All', 'lily' ),
						'links' => array(
							array(
								'label' => __( 'Shop All', 'lily' ),
								'url'   => lily_nav_product_cat_url( 'colored-lenses' ),
							),
						),
					),
				)
			),
		);
	}

	// Clear Lenses.
	if ( lily_nav_get_option( 'show_clear', 1 ) ) {
		$columns = array();

		if ( $brand_taxonomy ) {
			$brands = lily_nav_term_links( $brand_taxonomy );
			if ( $brands ) {
				$columns[] = array(
					'title' => __( 'By Brand', 'lily' ),
					'links' => $brands,
				);
			}
		}

		$durations = lily_nav_term_links( lily_nav_find_taxonomy( array( 'pa_duration' ) ) );
		if ( $durations ) {
			$columns[] = array(
				'title' => __( 'By Duration', 'lily' ),
				'links' => $durations,
			);
		}

		$items[] = array(
			'label'   => lily_nav_get_option( 'clear_label', __( 'Clear Lenses', 'lily' ) ),
			'columns' => array_merge(
				$columns,
				array(
					array(
						'title' => __( 'Shop All', 'lily' ),
						'links' => array(
							array(
								'label' => __( 'Shop All', 'lily' ),
								'url'   => lily_nav_product_cat_url( 'clear-lenses' ),
							),
						),
					),
				)
			),
		);
	}

	// Accessories & Lens Care.
	if ( lily_nav_get_option( 'show_accessories', 1 ) ) {
		$url = trim( (string) lily_nav_get_option( 'accessories_url', '' ) );

		if ( '' === $url ) {
			$accessories_term = get_term_by( 'slug', 'accessories', 'product_cat' );
			$lens_care_term   = get_term_by( 'slug', 'lens-care', 'product_cat' );
			$term             = $accessories_term ? $accessories_term : $lens_care_term;

			if ( $term && ! is_wp_error( $term ) ) {
				$link = get_term_link( $term );
				$url  = is_wp_error( $link ) ? '' : $link;
			}
		}

		if ( '' === $url ) {
			$url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' );
		}

		$items[] = array(
			'label' => lily_nav_get_option( 'accessories_label', __( 'Accessories & Lens Care', 'lily' ) ),
			'url'   => $url,
		);
	}

	// Find My Lenses.
	if ( lily_nav_get_option( 'show_find', 1 ) ) {
		$url = trim( (string) lily_nav_get_option( 'find_url', '' ) );

		if ( '' === $url ) {
			$url = home_url( '/#find-your-best-lenses' );
		}

		$items[] = array(
			'label' => lily_nav_get_option( 'find_label', __( 'Find My Lenses', 'lily' ) ),
			'url'   => $url,
			'badge' => __( 'New', 'lily' ),
		);
	}

	// Company.
	if ( lily_nav_get_option( 'show_company', 1 ) ) {
		$company_links = array();

		$company_pages = array(
			'about_page'   => array( __( 'About Us', 'lily' ), array( 'about', 'about-us' ) ),
			'faqs_page'    => array( __( 'FAQs', 'lily' ), array( 'faqs', 'faq' ) ),
			'contact_page' => array( __( 'Contact Us', 'lily' ), array( 'contact', 'contact-us' ) ),
		);

		foreach ( $company_pages as $key => $data ) {
			$company_links[] = array(
				'label' => $data[0],
				'url'   => lily_nav_page_url( $key, $data[1] ),
			);
		}

			$items[] = array(
				'label'   => lily_nav_get_option( 'company_label', __( 'Company', 'lily' ) ),
				'hover'   => true,
				'columns' => array(
					array(
						'title' => '',
						'links' => $company_links,
					),
				),
			);
	}

	return $items;
}

/**
 * Render the primary navigation.
 *
 * Uses the assigned WordPress menu when available; otherwise renders the
 * built-in dynamic Lily structure with dropdown panels.
 */
function lily_render_primary_nav() {
	if ( has_nav_menu( 'primary' ) ) {
		wp_nav_menu(
			array(
				'theme_location' => 'primary',
				'menu_class'     => 'lily-nav-menu',
				'container'      => false,
				'depth'          => 2,
			)
		);

		return;
	}

	$items = lily_nav_get_structure();

	if ( empty( $items ) ) {
		return;
	}

	$caret = '<svg class="lily-nav-caret" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><polyline points="6 9 12 15 18 9"></polyline></svg>';
	?>
	<ul class="lily-nav-menu">
		<?php foreach ( $items as $item ) : ?>
			<?php if ( ! empty( $item['columns'] ) ) : ?>
				<li class="lily-nav-item lily-nav-item--dropdown<?php echo ! empty( $item['hover'] ) ? ' lily-nav-item--hover' : ''; ?>">
					<button class="lily-nav-link" type="button" aria-expanded="false" aria-haspopup="true">
						<span><?php echo esc_html( $item['label'] ); ?></span>
						<?php echo $caret; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
					</button>
					<div class="lily-dropdown">
						<?php $lily_multi = count( $item['columns'] ) > 1; ?>
						<div class="lily-mega">
							<?php if ( $lily_multi ) : ?>
								<ul class="lily-mega__cats">
									<?php foreach ( $item['columns'] as $lily_i => $lily_column ) : ?>
										<li>
											<button type="button" class="lily-mega__cat<?php echo 0 === $lily_i ? ' is-active' : ''; ?>" data-lily-mega-cat="<?php echo esc_attr( $lily_i ); ?>" aria-expanded="<?php echo 0 === $lily_i ? 'true' : 'false'; ?>">
												<span><?php echo esc_html( $lily_column['title'] ); ?></span>
												<?php echo $caret; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
											</button>
										</li>
									<?php endforeach; ?>
								</ul>
							<?php endif; ?>
							<div class="lily-mega__panels<?php echo $lily_multi ? '' : ' lily-mega__panels--single'; ?>">
								<?php foreach ( $item['columns'] as $lily_i => $lily_column ) : ?>
									<div class="lily-mega__panel<?php echo $lily_multi && 0 === $lily_i ? ' is-active' : ''; ?>" data-lily-mega-panel="<?php echo esc_attr( $lily_i ); ?>">
										<?php if ( ! $lily_multi && ! empty( $lily_column['title'] ) ) : ?>
											<p class="lily-mega__panel-title"><?php echo esc_html( $lily_column['title'] ); ?></p>
										<?php endif; ?>
										<ul class="lily-mega__links">
											<?php foreach ( $lily_column['links'] as $link ) : ?>
												<li>
													<?php if ( ! empty( $link['url'] ) ) : ?>
														<a href="<?php echo esc_url( $link['url'] ); ?>"><?php echo esc_html( $link['label'] ); ?></a>
													<?php else : ?>
														<span class="lily-dropdown__pending"><?php echo esc_html( $link['label'] ); ?></span>
													<?php endif; ?>
												</li>
											<?php endforeach; ?>
										</ul>
									</div>
								<?php endforeach; ?>
							</div>
						</div>
					</div>
				</li>
			<?php else : ?>
				<li class="lily-nav-item">
					<a class="lily-nav-link" href="<?php echo esc_url( $item['url'] ); ?>"><span><?php echo esc_html( $item['label'] ); ?></span><?php echo ! empty( $item['badge'] ) ? '<span class="lily-nav-badge">' . esc_html( $item['badge'] ) . '</span>' : ''; ?></a>
				</li>
			<?php endif; ?>
		<?php endforeach; ?>
	</ul>
	<?php
}
