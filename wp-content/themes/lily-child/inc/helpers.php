<?php
/**
 * Shared Lily helpers.
 *
 * @package Lily
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Read a Lily homepage setting safely.
 *
 * Falls back to the schema defaults when no settings have been saved yet,
 * so a fresh install still renders the sections that are enabled by default.
 *
 * @param string $name    Field name.
 * @param mixed  $default Default value.
 * @return mixed
 */
function lily_get_option( $name, $default = null ) {
	$settings = get_option( 'lily_homepage_settings', array() );
	$settings = is_array( $settings ) && $settings
		? wp_parse_args( $settings, lily_homepage_settings_defaults() )
		: lily_homepage_settings_defaults();

	// Bilingual dashboard: on Arabic requests a non-empty "<name>_ar"
	// value wins so the brand owner controls Arabic copy directly.
	// Empty Arabic falls back to the shared Arabic dictionary (actual
	// Arabic for all known Lily strings), then to English (never empty).
	// Only scalar values participate; repeaters resolve per-row via
	// lily_ml_value() at their render sites.
	if ( lily_is_arabic_request() && ! is_array( $default ) ) {
		$ar_key = $name . '_ar';

		if ( array_key_exists( $ar_key, $settings ) && is_scalar( $settings[ $ar_key ] ) && '' !== trim( (string) $settings[ $ar_key ] ) ) {
			return $settings[ $ar_key ];
		}
	}

	$value = array_key_exists( $name, $settings ) ? $settings[ $name ] : null;
	$value = null !== $value && false !== $value && '' !== $value ? $value : $default;

	// Dictionary fallback for existing English content whose Arabic field
	// is still blank: show real Arabic instead of English on /ar/ pages.
	if ( lily_is_arabic_request() && ! is_array( $default ) && is_string( $value ) && '' !== trim( $value ) && function_exists( 'lily_ar_fallback' ) ) {
		return lily_ar_fallback( $value );
	}

	return $value;
}

/**
 * Current TranslatePress language code (for example "en_US" or "ar").
 *
 * Mirrors the existing lily_is_rtl_request() approach: the theme reads
 * the TranslatePress URL slugs from `trp_settings` and matches the
 * request path, so detection works even when the WP locale itself has
 * not been switched. Falls back to the TranslatePress default language.
 *
 * @return string
 */
function lily_get_current_language() {
	$settings = get_option( 'trp_settings', array() );
	$settings = is_array( $settings ) ? $settings : array();

	$default = isset( $settings['default-language'] ) && is_string( $settings['default-language'] ) && '' !== $settings['default-language']
		? $settings['default-language']
		: 'en_US';

	$url_slugs = ! empty( $settings['url-slugs'] ) && is_array( $settings['url-slugs'] ) ? $settings['url-slugs'] : array();

	if ( empty( $url_slugs ) ) {
		return $default;
	}

	$add_subdir_default = ! empty( $settings['add-subdirectory-to-default-language'] ) && 'yes' === $settings['add-subdirectory-to-default-language'];
	$request_uri        = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
	$path               = parse_url( $request_uri, PHP_URL_PATH );

	if ( ! is_string( $path ) || '' === $path ) {
		$path = '/';
	}

	foreach ( $url_slugs as $language_code => $slug ) {
		$slug = trim( (string) $slug, '/' );

		if ( '' === $slug ) {
			continue;
		}

		// The default language has no URL prefix unless TranslatePress is
		// explicitly configured to add a subdirectory for it.
		if ( (string) $language_code === (string) $default && ! $add_subdir_default ) {
			continue;
		}

		if ( preg_match( '#^/' . preg_quote( $slug, '#' ) . '(/|$)#', $path ) ) {
			return (string) $language_code;
		}
	}

	return $default;
}

/**
 * Whether the current request serves the Arabic version of the site.
 *
 * Result is memoized per request: the locale and the TranslatePress URL do
 * not change mid-request, and the check runs dozens of times per page.
 *
 * @return bool
 */
function lily_is_arabic_request() {
	static $memoized = null;

	if ( null !== $memoized ) {
		return $memoized;
	}

	// WordPress locale switched to Arabic (covers admin-ajax/REST and any
	// request where TranslatePress already set the locale).
	if ( function_exists( 'get_locale' ) ) {
		$locale_base = strtolower( strtok( (string) get_locale(), '_-' ) );

		if ( 'ar' === $locale_base ) {
			$memoized = true;
			return true;
		}
	}

	$code = strtolower( str_replace( '_', '-', (string) lily_get_current_language() ) );
	$base = strtok( $code, '-' );

	$memoized = 'ar' === $base;

	return $memoized;
}

/**
 * Pick the Arabic variant of a dashboard/repeater string when appropriate.
 *
 * @param mixed $value_en English value.
 * @param mixed $value_ar Arabic value.
 * @return mixed
 */
function lily_ml_value( $value_en, $value_ar = '' ) {
	if ( lily_is_arabic_request() ) {
		if ( is_scalar( $value_ar ) && '' !== trim( (string) $value_ar ) ) {
			return $value_ar;
		}

		// Empty Arabic field: dictionary gives real Arabic for all known
		// Lily strings; unknown custom copy safely stays in English.
		if ( is_string( $value_en ) && '' !== trim( $value_en ) && function_exists( 'lily_ar_fallback' ) ) {
			return lily_ar_fallback( $value_en );
		}
	}

	return $value_en;
}

/**
 * Check whether a homepage section should render.
 *
 * @param string $field True/false option field name.
 * @return bool
 */
function lily_show_section( $field ) {
	return (bool) lily_get_option( $field, false );
}

/**
 * Print a shared homepage container opening tag.
 *
 * @param string $class Extra class names.
 */
function lily_container_open( $class = '' ) {
	$classes = trim( 'lily-container ' . sanitize_html_class( $class ) );
	printf( '<div class="%s">', esc_attr( $classes ) );
}

/**
 * Print a shared homepage container closing tag.
 */
function lily_container_close() {
	echo '</div>';
}

/**
 * Get product/category thumbnail URL from a term.
 *
 * @param WP_Term $term Product category term.
 * @param string  $size Image size.
 * @return string
 */
function lily_get_product_cat_image_url( $term, $size = 'large' ) {
	$thumbnail_id = get_term_meta( $term->term_id, 'thumbnail_id', true );

	if ( $thumbnail_id ) {
		return wp_get_attachment_image_url( (int) $thumbnail_id, $size );
	}

	return function_exists( 'wc_placeholder_img_src' ) ? wc_placeholder_img_src( $size ) : '';
}

/**
 * Get the marketing image URL attached to a color term.
 *
 * @param WP_Term $term Color term.
 * @param string  $size Image size.
 * @return string
 */
function lily_get_color_image_url( $term, $size = 'large' ) {
	if ( ! $term ) {
		return '';
	}

	$image = get_term_meta( $term->term_id, 'color_image', true );

	if ( is_numeric( $image ) ) {
		return wp_get_attachment_image_url( (int) $image, $size );
	}

	if ( is_array( $image ) && ! empty( $image['ID'] ) ) {
		return wp_get_attachment_image_url( (int) $image['ID'], $size );
	}

	if ( is_string( $image ) ) {
		return $image;
	}

	return '';
}

/**
 * Get a WooCommerce filtered shop URL for a product attribute term.
 *
 * @param string  $taxonomy Attribute taxonomy, for example pa_color.
 * @param WP_Term $term     Attribute term.
 * @return string
 */
function lily_get_attribute_filter_url( $taxonomy, $term ) {
	$shop_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' );
	$attr     = str_replace( 'pa_', '', $taxonomy );

	return add_query_arg(
		array(
			'filter_' . $attr => $term->slug,
		),
		$shop_url
	);
}

/* ── Shop filter sidebar: multi-select helpers ────────────────────────── */

/**
 * Current multi-select values for one filter param (comma-separated slugs).
 *
 * @param string $param GET param name, for example filter_color.
 * @return string[]
 */
function lily_filter_current_values( $param ) {
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- public catalog filters.
	$raw = isset( $_GET[ $param ] ) ? sanitize_text_field( wp_unslash( $_GET[ $param ] ) ) : '';

	$values = array_filter( array_map( 'sanitize_title', explode( ',', $raw ) ) );

	return array_values( array_unique( $values ) );
}

/**
 * Build the URL that toggles one option inside a multi-select filter group.
 *
 * The whole filter state is preserved: every other GET param (other filter
 * groups, price bounds, orderby, TranslatePress params) carries over, only
 * pagination resets. Clicking a selected option removes it; clicking a new
 * option adds it to the comma list WooCommerce parses natively.
 *
 * @param string $param    GET param to toggle, for example filter_color.
 * @param string $slug     Option slug.
 * @param string $base_url Optional base URL; defaults to the current page.
 * @return string
 */
function lily_filter_toggle_url( $param, $slug, $base_url = '' ) {
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- public catalog filters.
	$params = (array) $_GET;

	// Rebuild the query copy: keep filter state, drop pagination + cart noise.
	unset( $params['paged'], $params['add-to-cart'] );

	$values = lily_filter_current_values( $param );

	if ( '__clear__' === $slug ) {
		$values = array();
	} else {
		$index = array_search( $slug, $values, true );

		if ( false !== $index ) {
			unset( $values[ $index ] );
			$values = array_values( $values );
		} else {
			$values[] = $slug;
		}
	}

	if ( ! empty( $values ) ) {
		$params[ $param ] = implode( ',', $values );
	} else {
		unset( $params[ $param ] );
	}

	if ( '' === $base_url ) {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- public catalog filters.
		$base_url = home_url( strtok( isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '/', '?' ) );
	}

	return add_query_arg( array_map( 'rawurlencode_deep', $params ), $base_url );
}

/**
 * URL that clears every active shop filter (all groups + price + category
 * multi-select) while keeping the current page (category archives keep their
 * own path) and the chosen sort order.
 *
 * @return string
 */
function lily_filter_clear_all_url() {
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- public catalog filters.
	$params = (array) $_GET;

	foreach ( array_keys( $params ) as $key ) {
		if ( 0 === strpos( $key, 'filter_' ) || 0 === strpos( $key, 'query_type_' ) ) {
			unset( $params[ $key ] );
		}
	}

	unset( $params['min_price'], $params['max_price'], $params['paged'], $params['add-to-cart'] );

	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- public catalog filters.
	$base_url = home_url( strtok( isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '/', '?' ) );

	if ( empty( $params ) ) {
		return $base_url;
	}

	return add_query_arg( array_map( 'rawurlencode_deep', $params ), $base_url );
}

/**
 * Real swatch tone for a color term (product-data color, matching the tone
 * map used by the homepage color cards — exempt from the UI palette like
 * product photography).
 *
 * The canonical swatch lives on the term itself (lily_swatch_color meta,
 * set in Lily → Colors) and child shades inherit their parent's swatch.
 * The static tone map below remains the development fallback.
 *
 * @param string $slug Term slug.
 * @return string Hex color.
 */
function lily_get_color_swatch_color( $slug ) {
	// Canonical swatch from the pa_color term (inherited from the parent shade).
	if ( taxonomy_exists( 'pa_color' ) && '' !== (string) $slug ) {
		$term = get_term_by( 'slug', (string) $slug, 'pa_color' );

		if ( $term && ! is_wp_error( $term ) && function_exists( 'lily_color_swatch' ) ) {
			$swatch = lily_color_swatch( $term );

			if ( '' !== $swatch ) {
				return $swatch;
			}
		}
	}

	$tones = array(
		'blue'  => '#7d94a8',
		'gray'  => '#aaa49a',
		'green' => '#6f7d5f',
		'hazel' => '#9c7b4f',
		'brown' => '#885337',
		'other' => '#d8d2bd',
	);

	return isset( $tones[ $slug ] ) ? $tones[ $slug ] : '#AC7D61';
}

/**
 * Best Seller state for a product — set per product in the dashboard
 * (Products → edit product → "Best Seller" checkbox).
 *
 * This post meta is the single source of truth: it drives the BEST SELLER
 * badge on the shared product card and the Best Sellers homepage section.
 * Checking/unchecking the box propagates everywhere automatically.
 *
 * @param int|WC_Product $product Product ID or object.
 * @return bool
 */
function lily_is_best_seller( $product ) {
	$product_id = is_object( $product ) && method_exists( $product, 'get_id' ) ? $product->get_id() : absint( $product );

	if ( ! $product_id ) {
		return false;
	}

	return '1' === (string) get_post_meta( $product_id, '_lily_best_seller', true );
}

/**
 * Build the URL that toggles one collection inside the multi-select
 * Collections group (filter_cat). Category archives seed their own term so
 * selecting another collection there combines both; the Shop page toggles
 * freely within the group. All other filter state is preserved.
 *
 * @param string $slug Collection slug.
 * @return string
 */
function lily_filter_cat_toggle_url( $slug ) {
	$shop_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' );

	$values = lily_filter_current_values( 'filter_cat' );

	if ( is_product_category() ) {
		$queried = get_queried_object();

		if ( $queried instanceof WP_Term && ! in_array( $queried->slug, $values, true ) ) {
			$values[] = $queried->slug;
		}
	}

	$index = array_search( $slug, $values, true );

	if ( false !== $index ) {
		unset( $values[ $index ] );
		$values = array_values( $values );
	} else {
		$values[] = $slug;
	}

	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- public catalog filters.
	$params = (array) $_GET;
	unset( $params['paged'], $params['add-to-cart'] );

	if ( ! empty( $values ) ) {
		$params['filter_cat'] = implode( ',', array_values( array_unique( $values ) ) );
	} else {
		unset( $params['filter_cat'] );
	}

	if ( empty( $params ) ) {
		return $shop_url;
	}

	return add_query_arg( array_map( 'rawurlencode_deep', $params ), $shop_url );
}

/**
 * Multi-select collections: honor the filter_cat OR selection on the Shop
 * page. Mirrors WooCommerce's own layered-nav pattern (comma-separated slug
 * list; OR within the group; other filters keep combining with AND). On
 * single category archives the existing archive behavior is untouched.
 *
 * @param WP_Query $query Main query.
 */
function lily_filter_cat_query( $query ) {
	if ( is_admin() || ! $query->is_main_query() ) {
		return;
	}

	if ( ! $query->is_post_type_archive( 'product' ) ) {
		return;
	}

	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- public catalog filters.
	$raw = isset( $_GET['filter_cat'] ) ? sanitize_text_field( wp_unslash( $_GET['filter_cat'] ) ) : '';
	$slugs = array_filter( array_map( 'sanitize_title', explode( ',', $raw ) ) );

	if ( empty( $slugs ) ) {
		return;
	}

	$tax_query   = (array) $query->get( 'tax_query' );
	$tax_query[] = array(
		'taxonomy' => 'product_cat',
		'field'    => 'slug',
		'terms'    => array_values( $slugs ),
		'operator' => 'IN',
	);

	$query->set( 'tax_query', $tax_query );
}
add_action( 'pre_get_posts', 'lily_filter_cat_query', 30 );

/**
 * WooCommerce parses comma-separated layered-nav values per attribute group;
 * Lily's sidebar treats those options as independent checkbox selections, so
 * the group operator is OR (the official layered-nav multi-select mode).
 */
add_filter(
	'woocommerce_layered_nav_default_query_type',
	static function () {
		return 'or';
	}
);

/**
 * Get selected taxonomy terms from a Lily option.
 *
 * @param string $field    Option field.
 * @param string $taxonomy Taxonomy name.
 * @return WP_Term[]
 */
function lily_get_selected_terms( $field, $taxonomy ) {
	$selected = lily_get_option( $field, array() );

	if ( empty( $selected ) ) {
		return array();
	}

	$ids = array_map(
		static function ( $term ) {
			return is_object( $term ) ? (int) $term->term_id : (int) $term;
		},
		(array) $selected
	);

	$terms = get_terms(
		array(
			'taxonomy'   => $taxonomy,
			'include'    => $ids,
			'hide_empty' => false,
			'orderby'    => 'include',
		)
	);

	return is_wp_error( $terms ) ? array() : $terms;
}

/**
 * Return terms for an attribute taxonomy as frontend-friendly arrays.
 *
 * Labels are the localized term names (Arabic name meta wins on Arabic
 * requests), so every consumer of this helper — Lens Finder included —
 * reads the one canonical list. `$parents_only` restricts the list to
 * top-level terms (Parent Colors only).
 *
 * @param string $taxonomy     Attribute taxonomy.
 * @param bool   $parents_only Restrict to top-level terms.
 * @return array
 */
function lily_get_attribute_terms_for_js( $taxonomy, $parents_only = false ) {
	if ( ! taxonomy_exists( $taxonomy ) ) {
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

	if ( is_wp_error( $terms ) ) {
		return array();
	}

	return array_map(
		static function ( $term ) {
			return array(
				'label' => function_exists( 'lily_term_name' ) ? lily_term_name( $term ) : $term->name,
				'value' => $term->slug,
			);
		},
		$terms
	);
}

/**
 * Detect RTL requests, including TranslatePress language URLs.
 *
 * @return bool
 */
function lily_is_rtl_request() {
	if ( is_rtl() ) {
		return true;
	}

	$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
	$settings    = get_option( 'trp_settings', array() );
	$url_slugs   = ! empty( $settings['url-slugs'] ) && is_array( $settings['url-slugs'] ) ? $settings['url-slugs'] : array();
	$rtl_codes   = array( 'ar', 'fa', 'he', 'ur' );

	foreach ( $url_slugs as $language_code => $slug ) {
		$base_code = strtolower( strtok( (string) $language_code, '_' ) );
		$slug      = trim( (string) $slug, '/' );

		if ( ! in_array( $base_code, $rtl_codes, true ) || '' === $slug ) {
			continue;
		}

		if ( preg_match( '#^/' . preg_quote( $slug, '#' ) . '(/|$)#', $request_uri ) ) {
			return true;
		}
	}

	return false;
}

/**
 * Build Lens Finder frontend configuration.
 *
 * @return array
 */
function lily_get_lens_finder_frontend_config() {
	// Quiz question labels: dashboard Arabic variants win per-question on
	// Arabic requests; blanks fall back to English, then to the gettext
	// default (which TranslatePress owns).
	$lily_questions    = (array) lily_get_option( 'lens_finder_questions', array() );
	$lily_questions_ar = (array) lily_get_option( 'lens_finder_questions_ar', array() );

	$lily_question_text = static function ( $key, $fallback ) use ( $lily_questions, $lily_questions_ar ) {
		$ar = isset( $lily_questions_ar[ $key ] ) ? trim( (string) $lily_questions_ar[ $key ] ) : '';

		if ( lily_is_arabic_request() && '' !== $ar ) {
			return $ar;
		}

		$en = isset( $lily_questions[ $key ] ) ? trim( (string) $lily_questions[ $key ] ) : '';

		return '' !== $en ? $en : $fallback;
	};

	return array(
		'endpoint' => esc_url_raw( rest_url( 'lily/v1/lens-finder' ) ),
		'nonce'    => wp_create_nonce( 'lily_lens_finder' ),
		'i18n'     => array(
			'back'          => esc_html__( 'Back', 'lily' ),
			'next'          => esc_html__( 'Next', 'lily' ),
			'review'        => esc_html__( 'Review your selections', 'lily' ),
			'find'          => esc_html__( 'Find My Lenses', 'lily' ),
			'loading'       => esc_html__( 'Finding lenses...', 'lily' ),
			'acknowledge'   => lily_get_option( 'lens_finder_responsibility_text', esc_html__( 'I understand these selections are my responsibility and do not replace professional eye care advice.', 'lily' ) ),
			'noResults'     => lily_get_option( 'lens_finder_no_results_message', esc_html__( 'No matching lenses were found. Try changing one or two selections.', 'lily' ) ),
			'resultsTitle'  => esc_html__( 'Recommended lenses', 'lily' ),
			'requiredCheck' => esc_html__( 'Please confirm your selections before continuing.', 'lily' ),
			'chooseOne'     => esc_html__( 'Choose one option to continue.', 'lily' ),
			'questions'     => array(
				'lens_type'    => $lily_question_text( 'lens_type', esc_html__( 'Lens Type', 'lily' ) ),
				'prescription' => $lily_question_text( 'prescription', esc_html__( 'Prescription', 'lily' ) ),
				'look'         => $lily_question_text( 'look', esc_html__( 'Preferred Look', 'lily' ) ),
				'eye_color'    => $lily_question_text( 'eye_color', esc_html__( 'Natural Eye Color', 'lily' ) ),
				'skin_tone'    => $lily_question_text( 'skin_tone', esc_html__( 'Skin Tone', 'lily' ) ),
				'color'        => $lily_question_text( 'color', esc_html__( 'Preferred Color', 'lily' ) ),
				'duration'     => $lily_question_text( 'duration', esc_html__( 'Replacement Duration', 'lily' ) ),
			),
		),
		'terms'    => array(
			'lensType'     => lily_get_attribute_terms_for_js( 'pa_lens_type' ),
			'prescription' => lily_get_attribute_terms_for_js( 'pa_prescription' ),
			'duration'     => lily_get_attribute_terms_for_js( 'pa_duration' ),
			'look'         => lily_get_attribute_terms_for_js( 'pa_look' ),
			// Parent Colors only — child shades never surface as independent
			// quiz answers; matching still reaches every shade (see the
			// automatic Parent Color assignment on products).
			'color'        => lily_get_attribute_terms_for_js( 'pa_color', true ),
		),
		'static'   => array(
			/* Skin Colors + Eye Colors are canonical catalog data (Lily →
			 * Skin Colors / Eye Colors). The options below are the live
			 * localized terms — new entries appear here automatically. */
			'skinTone' => lily_catalog_taxonomy( 'skin_color' )
				? lily_catalog_terms_for_js( 'skin_color' )
				: array(
					array( 'label' => esc_html__( 'Fair', 'lily' ), 'value' => 'fair' ),
					array( 'label' => esc_html__( 'Light / Medium', 'lily' ), 'value' => 'light-medium' ),
					array( 'label' => esc_html__( 'Medium', 'lily' ), 'value' => 'medium' ),
					array( 'label' => esc_html__( 'Tan', 'lily' ), 'value' => 'tan' ),
					array( 'label' => esc_html__( 'Deep', 'lily' ), 'value' => 'deep' ),
				),
			'eyeColor' => lily_catalog_taxonomy( 'eye_color' )
				? lily_catalog_terms_for_js( 'eye_color' )
				: array(
					array( 'label' => esc_html__( 'Brown', 'lily' ), 'value' => 'brown' ),
					array( 'label' => esc_html__( 'Hazel', 'lily' ), 'value' => 'hazel' ),
					array( 'label' => esc_html__( 'Green', 'lily' ), 'value' => 'green' ),
					array( 'label' => esc_html__( 'Blue', 'lily' ), 'value' => 'blue' ),
					array( 'label' => esc_html__( 'Gray', 'lily' ), 'value' => 'gray' ),
					array( 'label' => esc_html__( 'Other', 'lily' ), 'value' => 'other' ),
				),
		),
	);
}
