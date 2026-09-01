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

	$value = array_key_exists( $name, $settings ) ? $settings[ $name ] : null;

	return null !== $value && false !== $value && '' !== $value ? $value : $default;
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
 * Render a Lily link field as a button.
 *
 * Tolerates malformed or partially submitted link data: anything that does
 * not resolve to a usable URL is silently ignored instead of fatalling.
 *
 * @param array|string $link  Lily link field or URL.
 * @param string       $class Button class.
 */
function lily_render_link_button( $link, $class = 'lily-button' ) {
	if ( empty( $link ) ) {
		return;
	}

	$url    = '';
	$title  = '';
	$target = '_self';

	if ( is_array( $link ) ) {
		if ( isset( $link['url'] ) && is_string( $link['url'] ) ) {
			$url = trim( $link['url'] );
		}

		if ( isset( $link['title'] ) && is_string( $link['title'] ) ) {
			$title = trim( $link['title'] );
		}

		if ( isset( $link['target'] ) && '_blank' === $link['target'] ) {
			$target = '_blank';
		}
	} elseif ( is_scalar( $link ) ) {
		$url = trim( (string) $link );
	} else {
		return;
	}

	if ( '' === $url ) {
		return;
	}

	if ( '' === $title ) {
		$title = esc_html__( 'Learn more', 'lily' );
	}

	printf(
		'<a class="%1$s" href="%2$s" target="%3$s"%4$s>%5$s</a>',
		esc_attr( $class ),
		esc_url( $url ),
		esc_attr( $target ),
		'_blank' === $target ? ' rel="noopener noreferrer"' : '',
		esc_html( $title )
	);
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
 * @param string $taxonomy Attribute taxonomy.
 * @return array
 */
function lily_get_attribute_terms_for_js( $taxonomy ) {
	if ( ! taxonomy_exists( $taxonomy ) ) {
		return array();
	}

	$terms = get_terms(
		array(
			'taxonomy'   => $taxonomy,
			'hide_empty' => false,
		)
	);

	if ( is_wp_error( $terms ) ) {
		return array();
	}

	return array_map(
		static function ( $term ) {
			return array(
				'label' => $term->name,
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
				'lens_type'    => lily_get_option( 'lens_finder_questions', array() )['lens_type'] ?: esc_html__( 'Lens Type', 'lily' ),
				'prescription' => lily_get_option( 'lens_finder_questions', array() )['prescription'] ?: esc_html__( 'Prescription', 'lily' ),
				'look'         => lily_get_option( 'lens_finder_questions', array() )['look'] ?: esc_html__( 'Preferred Look', 'lily' ),
				'eye_color'    => lily_get_option( 'lens_finder_questions', array() )['eye_color'] ?: esc_html__( 'Natural Eye Color', 'lily' ),
				'skin_tone'    => lily_get_option( 'lens_finder_questions', array() )['skin_tone'] ?: esc_html__( 'Skin Tone', 'lily' ),
				'color'        => lily_get_option( 'lens_finder_questions', array() )['color'] ?: esc_html__( 'Preferred Color', 'lily' ),
				'duration'     => lily_get_option( 'lens_finder_questions', array() )['duration'] ?: esc_html__( 'Replacement Duration', 'lily' ),
			),
		),
		'terms'    => array(
			'lensType'     => lily_get_attribute_terms_for_js( 'pa_lens_type' ),
			'prescription' => lily_get_attribute_terms_for_js( 'pa_prescription' ),
			'duration'     => lily_get_attribute_terms_for_js( 'pa_duration' ),
			'look'         => lily_get_attribute_terms_for_js( 'pa_look' ),
			'color'        => lily_get_attribute_terms_for_js( 'pa_color' ),
		),
		'static'   => array(
			'skinTone' => array(
				array( 'label' => esc_html__( 'Fair', 'lily' ), 'value' => 'fair' ),
				array( 'label' => esc_html__( 'Light / Medium', 'lily' ), 'value' => 'light-medium' ),
				array( 'label' => esc_html__( 'Medium', 'lily' ), 'value' => 'medium' ),
				array( 'label' => esc_html__( 'Tan', 'lily' ), 'value' => 'tan' ),
				array( 'label' => esc_html__( 'Deep', 'lily' ), 'value' => 'deep' ),
			),
			'eyeColor' => array(
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
