<?php
/**
 * Lily canonical catalog data — Brands, Colors (+ Child Shades), Looks,
 * Durations.
 *
 * Single source of truth = the existing WooCommerce attribute taxonomies
 * already used by products everywhere:
 *
 *   Brands    → pa_brand  (logo: brand_logo term meta)
 *   Colors    → pa_color  (real parent/child hierarchy inside pa_color;
 *                          swatch: lily_swatch_color term meta;
 *                          marketing image: color_image term meta)
 *   Looks     → pa_look
 *   Durations → pa_duration
 *
 * Every entity additionally stores an Arabic name in `lily_name_ar` term
 * meta. One term per entity — no duplicate Arabic terms, no separate lists
 * for dashboard / navbar / filters / Lens Finder / homepage.
 *
 * Color model: a product keeps its SPECIFIC color term (a Child Shade when
 * one is selected, otherwise the Parent Color). Whenever a Child Shade is
 * assigned, the Parent Color is assigned alongside it automatically
 * (lily_sync_color_parent_assignment), so every native WooCommerce surface
 * (layered-nav filter with OR multi-select, counts, price filter, search,
 * archives, Lens Finder REST) keeps matching the Parent Color with zero
 * custom query rewrites.
 *
 * @package Lily
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* ── Canonical taxonomies ─────────────────────────────────────────────── */

/**
 * Canonical taxonomy for one catalog entity kind.
 *
 * @param string $kind brand|color|look|duration.
 * @return string Taxonomy name, or '' when unavailable.
 */
function lily_catalog_taxonomy( $kind ) {
	$map = array(
		'brand'      => function_exists( 'lily_get_brand_taxonomy' ) ? lily_get_brand_taxonomy() : 'pa_brand',
		'color'      => 'pa_color',
		'look'       => 'pa_look',
		'duration'   => 'pa_duration',
		'occasion'   => 'pa_occasion',
		'skin_color' => 'pa_skin_color',
		'eye_color'  => 'pa_eye_color',
	);

	$taxonomy = isset( $map[ $kind ] ) ? $map[ $kind ] : '';

	return $taxonomy && taxonomy_exists( $taxonomy ) ? $taxonomy : '';
}

/**
 * Ensure the canonical Skin Color / Eye Color attributes exist
 * (pa_skin_color / pa_eye_color), created once through WooCommerce's own
 * attribute system and seeded with the historical Lens Finder options so
 * existing product data keeps matching (the old meta keys are these slugs).
 *
 * Idempotent: only creates what is missing.
 */
function lily_maybe_bootstrap_eye_skin_colors() {
	if ( ! function_exists( 'wc_create_attribute' ) || ! function_exists( 'wc_attribute_taxonomy_id_by_name' ) ) {
		return;
	}

	$definitions = array(
		'skin_color' => array(
			// English name, forced slug (matches legacy best_skin_tones keys), Arabic name.
			array( 'Fair', 'fair', 'بشرة فاتحة' ),
			array( 'Light / Medium', 'light-medium', 'بشرة فاتح لمتوسط' ),
			array( 'Medium', 'medium', 'متوسطة' ),
			array( 'Tan', 'tan', 'حنطية' ),
			array( 'Deep', 'deep', 'داكنة' ),
		),
		'eye_color'  => array(
			array( 'Brown', 'brown', 'بني' ),
			array( 'Hazel', 'hazel', 'عسلي' ),
			array( 'Green', 'green', 'أخضر' ),
			array( 'Blue', 'blue', 'أزرق' ),
			array( 'Gray', 'gray', 'رمادي' ),
			array( 'Other', 'other', 'أخرى' ),
		),
	);

	foreach ( $definitions as $slug_key => $terms ) {
		$taxonomy = 'pa_' . $slug_key; // WC preserves underscores in attribute slugs.

		if ( ! taxonomy_exists( $taxonomy ) ) {
			$attribute_id = (int) wc_attribute_taxonomy_id_by_name( $slug_key );

			if ( ! $attribute_id ) {
				$attribute_id = (int) wc_create_attribute(
					array(
						'name'         => 'skin_color' === $slug_key ? 'Skin Color' : 'Eye Color',
						'slug'         => $slug_key,
						'type'         => 'select',
						'order_by'     => 'menu_order',
						'has_archives' => false,
					)
				);

				if ( ! $attribute_id ) {
					continue;
				}

				// Make the taxonomy available in this same request.
				if ( class_exists( 'WC_Post_Types' ) && method_exists( 'WC_Post_Types', 'register_taxonomies' ) ) {
					WC_Post_Types::register_taxonomies();
				} else {
					delete_transient( 'wc_attribute_taxonomies' );
				}
			}

			if ( ! taxonomy_exists( $taxonomy ) ) {
				continue;
			}
		}

		$existing = get_terms(
			array(
				'taxonomy'   => $taxonomy,
				'hide_empty' => false,
				'fields'     => 'slugs',
			)
		);

		if ( is_wp_error( $existing ) ) {
			continue;
		}

		foreach ( $terms as $definition ) {
			list( $name, $slug, $name_ar ) = $definition;

			if ( in_array( $slug, (array) $existing, true ) ) {
				continue;
			}

			$created = wp_insert_term( $name, $taxonomy, array( 'slug' => $slug ) );

			if ( ! is_wp_error( $created ) ) {
				update_term_meta( (int) $created['term_id'], 'lily_name_ar', $name_ar );
			}
		}
	}
}
add_action( 'init', 'lily_maybe_bootstrap_eye_skin_colors', 30 );

/* ── Lens specifications (numeric product meta) ───────────────────────── */

/**
 * Canonical spec meta keys + display units.
 *
 * @return array[] spec => [ meta key, unit, taxonomy legacy ]
 */
function lily_spec_definitions() {
	return array(
		'diameter'     => array(
			'meta' => '_lily_diameter',
			'unit' => 'mm',
			'legacy_taxonomy' => 'pa_diameter',
		),
		'water_content' => array(
			'meta' => '_lily_water_content',
			'unit' => '%',
			'legacy_taxonomy' => 'pa_water_content',
		),
		'base_curve'   => array(
			'meta' => '_lily_base_curve',
			'unit' => 'mm',
			'legacy_taxonomy' => 'pa_base_curve',
		),
	);
}

/**
 * Numeric value of one spec for a product (canonical meta), with a safe
 * read-only fallback to the legacy attribute-term name for products saved
 * before the numeric inputs existed.
 *
 * @param int    $product_id Product ID.
 * @param string $spec       diameter|water_content|base_curve.
 * @return string Raw numeric-ish value ('' when unknown).
 */
function lily_get_product_spec( $product_id, $spec ) {
	$definitions = lily_spec_definitions();

	if ( ! isset( $definitions[ $spec ] ) ) {
		return '';
	}

	$value = get_post_meta( (int) $product_id, $definitions[ $spec ]['meta'], true );

	if ( '' === $value || null === $value ) {
		$legacy = $definitions[ $spec ]['legacy_taxonomy'];

		if ( taxonomy_exists( $legacy ) ) {
			$terms = wp_get_post_terms( (int) $product_id, $legacy, array( 'fields' => 'names' ) );

			if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
				$value = lily_spec_numeric( $terms[0] );
			}
		}
	}

	return (string) $value;
}

/**
 * Extract the leading numeric portion of a value ("14.2 mm" → "14.2").
 *
 * @param string $value Raw value.
 * @return string
 */
function lily_spec_numeric( $value ) {
	$value = trim( (string) $value );

	if ( '' === $value ) {
		return '';
	}

	if ( preg_match( '/^[-+]?\d+(?:\.\d+)?/', $value, $m ) ) {
		return $m[0];
	}

	return '';
}

/**
 * Display value with its unit ("14.2" → "14.2 mm", "42" → "42%").
 * Values that already contain a unit are shown as entered.
 *
 * @param string $spec   diameter|water_content|base_curve.
 * @param string $value  Raw value.
 * @return string
 */
function lily_format_spec( $spec, $value ) {
	$value = trim( (string) $value );

	if ( '' === $value ) {
		return '';
	}

	$definitions = lily_spec_definitions();

	if ( ! isset( $definitions[ $spec ] ) ) {
		return $value;
	}

	if ( preg_match( '/[a-zA-Z%]/', $value ) ) {
		return $value; // Already carries a unit.
	}

	$unit = $definitions[ $spec ]['unit'];

	return '%' === $unit ? $value . $unit : $value . ' ' . $unit;
}

/* ── How To Use steps ─────────────────────────────────────────────────── */

/**
 * Standard fallback instructions (used when a product has no custom steps).
 *
 * @return string[]
 */
function lily_how_to_use_default_steps() {
	return array(
		__( 'Wash and dry your hands thoroughly before handling your lenses.', 'lily' ),
		__( 'Place the lens gently on your fingertip and check it forms a smooth cup shape.', 'lily' ),
		__( 'Check that the lens is clean, hydrated and correctly positioned.', 'lily' ),
		__( 'Insert the lens carefully and remove it before sleeping or swimming.', 'lily' ),
	);
}

/**
 * The product's HOW TO USE steps.
 *
 * Canonical source: the `_lily_how_to_use_steps` meta (dynamic step editor).
 * Falls back to the legacy single-text field split into lines, then to the
 * standard instructions — no existing instructions are ever lost.
 *
 * @param int $product_id Product ID.
 * @return string[]
 */
function lily_get_how_to_use_steps( $product_id ) {
	$steps = get_post_meta( (int) $product_id, '_lily_how_to_use_steps', true );

	if ( is_array( $steps ) ) {
		$steps = array_values(
			array_filter(
				array_map( 'trim', array_map( 'wp_strip_all_tags', (array) $steps ) ),
				static function ( $step ) {
					return '' !== $step;
				}
			)
		);

		if ( ! empty( $steps ) ) {
			return $steps;
		}
	}

	// Legacy single-text field (pre-step editor) — split non-empty lines.
	$legacy = get_post_meta( (int) $product_id, '_lily_how_to_use', true );

	if ( is_string( $legacy ) && '' !== trim( $legacy ) ) {
		$lines = array_values(
			array_filter(
				array_map( 'trim', preg_split( '/\r\n|\r|\n/', $legacy ) ),
				static function ( $line ) {
					return '' !== $line;
				}
			)
		);

		if ( ! empty( $lines ) ) {
			return $lines;
		}

		// A single paragraph without line breaks becomes one step.
		return array( trim( $legacy ) );
	}

	return lily_how_to_use_default_steps();
}

/* ── Localized option lists from slugs (skin/eye colors) ──────────────── */

/**
 * Localized labels for canonical term slugs of one entity kind.
 *
 * Used by the frontend to render stored Skin Color / Eye Color values
 * through the same canonical terms the dashboard manages.
 *
 * @param string   $kind  skin_color|eye_color.
 * @param string[] $slugs Term slugs.
 * @return string[] Localized names in stored order.
 */
function lily_catalog_names_by_slugs( $kind, $slugs ) {
	$taxonomy = lily_catalog_taxonomy( $kind );

	if ( ! $taxonomy || empty( $slugs ) ) {
		return array();
	}

	$terms = get_terms(
		array(
			'taxonomy'   => $taxonomy,
			'slug'       => array_values( (array) $slugs ),
			'hide_empty' => false,
		)
	);

	if ( is_wp_error( $terms ) || empty( $terms ) ) {
		return array();
	}

	$names = array();

	foreach ( $terms as $term ) {
		$names[ $term->slug ] = lily_term_name( $term );
	}

	$out = array();

	foreach ( (array) $slugs as $slug ) {
		if ( isset( $names[ $slug ] ) ) {
			$out[] = $names[ $slug ];
		}
	}

	return $out;
}

/* ── Occasions (canonical reusable source) ────────────────────────────── */

/**
 * Ensure the canonical Occasions attribute exists (pa_occasion), created once
 * through WooCommerce's own attribute system, with starter terms.
 *
 * Idempotent: safe to run on every request; it only acts when the attribute
 * (or a seeded term) is missing.
 */
function lily_maybe_bootstrap_occasions() {
	if ( ! function_exists( 'wc_create_attribute' ) || ! function_exists( 'wc_attribute_taxonomy_id_by_name' ) ) {
		return;
	}

	$attribute_id = (int) wc_attribute_taxonomy_id_by_name( 'occasion' );

	if ( ! $attribute_id ) {
		$attribute_id = (int) wc_create_attribute(
			array(
				'name'         => 'Occasion',
				'slug'         => 'occasion',
				'type'         => 'select',
				'order_by'     => 'menu_order',
				'has_archives' => false,
			)
		);

		if ( ! $attribute_id ) {
			return;
		}

		// Make the taxonomy available in this same request (WC registers
		// attribute taxonomies during its init bootstrap, which already ran).
		if ( class_exists( 'WC_Post_Types' ) && method_exists( 'WC_Post_Types', 'register_taxonomies' ) ) {
			WC_Post_Types::register_taxonomies();
		} else {
			delete_transient( 'wc_attribute_taxonomies' );
		}
	}

	if ( ! taxonomy_exists( 'pa_occasion' ) ) {
		return; // Taxonomy registers on the next request (WC bootstrap order).
	}

	$defaults = array(
		'Everyday'         => 'يومي',
		'Work'             => 'العمل',
		'University'       => 'الجامعة',
		'Party'            => 'حفلات',
		'Wedding'          => 'زفاف',
		'Special Occasion' => 'مناسبة خاصة',
		'Photoshoot'       => 'تصوير',
	);

	$existing = get_terms(
		array(
			'taxonomy'   => 'pa_occasion',
			'hide_empty' => false,
			'fields'     => 'names',
		)
	);

	if ( is_wp_error( $existing ) ) {
		return;
	}

	foreach ( $defaults as $name => $name_ar ) {
		if ( in_array( $name, (array) $existing, true ) ) {
			continue;
		}

		$created = wp_insert_term( $name, 'pa_occasion' );

		if ( ! is_wp_error( $created ) ) {
			update_term_meta( (int) $created['term_id'], 'lily_name_ar', $name_ar );
		}
	}
}
add_action( 'init', 'lily_maybe_bootstrap_occasions', 30 );

/* ── Localized names (one term holds both languages) ──────────────────── */

/**
 * Arabic name stored on a term.
 *
 * @param WP_Term|int $term Term object or ID.
 * @return string
 */
function lily_get_term_name_ar( $term ) {
	$term_id = is_object( $term ) ? (int) $term->term_id : absint( $term );

	if ( ! $term_id ) {
		return '';
	}

	$name = get_term_meta( $term_id, 'lily_name_ar', true );

	return is_string( $name ) ? trim( $name ) : '';
}

/**
 * Localized term name for the current language (English name on English
 * requests, stored Arabic name on Arabic requests — never a new term).
 *
 * @param WP_Term $term Term object.
 * @return string
 */
function lily_term_name( $term ) {
	if ( ! $term instanceof WP_Term ) {
		return '';
	}

	if ( function_exists( 'lily_ml_value' ) ) {
		return lily_ml_value( $term->name, lily_get_term_name_ar( $term ) );
	}

	return $term->name;
}

/**
 * Localized brand display name for a product (canonical pa_brand / product_brand).
 *
 * @param int $product_id Product ID.
 * @return string
 */
function lily_product_brand_name( $product_id ) {
	$taxonomy = lily_catalog_taxonomy( 'brand' );

	if ( ! $taxonomy ) {
		return '';
	}

	$terms = wp_get_post_terms( (int) $product_id, $taxonomy );

	if ( is_wp_error( $terms ) || empty( $terms ) ) {
		return '';
	}

	return lily_term_name( $terms[0] );
}

/* ── Color hierarchy (real data: parent/child inside pa_color) ────────── */

/**
 * Parent (main) color of a shade term.
 *
 * @param WP_Term $term Color term.
 * @return WP_Term|null
 */
function lily_color_parent( $term ) {
	if ( ! $term instanceof WP_Term || 'pa_color' !== $term->taxonomy || ! $term->parent ) {
		return null;
	}

	$parent = get_term( (int) $term->parent, 'pa_color' );

	return ( $parent && ! is_wp_error( $parent ) ) ? $parent : null;
}

/**
 * Direct child shade term IDs of a parent color.
 *
 * Uses a direct term_taxonomy lookup: WordPress skips `get_terms` parent /
 * child_of queries on non-hierarchical taxonomies (pa_color is registered
 * flat by WooCommerce), while the underlying hierarchy data is real.
 *
 * @param int $term_id Parent color term ID.
 * @return int[]
 */
function lily_color_child_ids( $term_id ) {
	global $wpdb;

	$term_id = absint( $term_id );

	if ( ! $term_id || ! taxonomy_exists( 'pa_color' ) ) {
		return array();
	}

	$ids = $wpdb->get_col( // phpcs:ignore WordPress.DB.DirectDatabaseQuery -- hierarchy lookup on a flat taxonomy; small, indexed, uncached by core.
		$wpdb->prepare(
			"SELECT term_id FROM {$wpdb->term_taxonomy} WHERE taxonomy = 'pa_color' AND parent = %d",
			$term_id
		)
	);

	return array_values( array_map( 'absint', (array) $ids ) );
}

/**
 * Direct child shades of a parent color.
 *
 * @param int $term_id Parent color term ID.
 * @return WP_Term[]
 */
function lily_color_children( $term_id ) {
	$child_ids = lily_color_child_ids( $term_id );

	if ( empty( $child_ids ) ) {
		return array();
	}

	$terms = get_terms(
		array(
			'taxonomy'   => 'pa_color',
			'include'    => $child_ids,
			'hide_empty' => false,
			'orderby'    => 'name',
		)
	);

	return ( is_wp_error( $terms ) || ! is_array( $terms ) ) ? array() : array_values( $terms );
}

/**
 * Top-level parent colors (the ones surfaced in homepage / filters / navbar /
 * Lens Finder).
 *
 * `parent => 0` is safe here (only positive parents are short-circuited by
 * core), but the result is still filtered defensively.
 *
 * @param array $args Extra get_terms args.
 * @return WP_Term[]
 */
function lily_color_parents( $args = array() ) {
	if ( ! taxonomy_exists( 'pa_color' ) ) {
		return array();
	}

	$terms = get_terms(
		wp_parse_args(
			$args,
			array(
				'taxonomy'   => 'pa_color',
				'parent'     => 0,
				'hide_empty' => false,
			)
		)
	);

	if ( is_wp_error( $terms ) || ! is_array( $terms ) ) {
		return array();
	}

	return array_values(
		array_filter(
			$terms,
			static function ( $term ) {
				return $term instanceof WP_Term && 0 === (int) $term->parent;
			}
		)
	);
}

/**
 * Descendant shade term IDs of a color term (walks the real hierarchy data,
 * any depth — the admin enforces one level).
 *
 * @param int $term_id Color term ID.
 * @return int[]
 */
function lily_color_descendant_ids( $term_id ) {
	$term_id     = absint( $term_id );
	$descendants = array();
	$queue       = array( $term_id );
	$guard       = 0;

	if ( ! $term_id || ! taxonomy_exists( 'pa_color' ) ) {
		return array();
	}

	while ( $queue && $guard < 10 ) {
		$guard++;

		$level = lily_color_child_ids( array_shift( $queue ) );

		foreach ( $level as $child_id ) {
			if ( ! in_array( $child_id, $descendants, true ) ) {
				$descendants[] = $child_id;
				$queue[]       = $child_id;
			}
		}
	}

	return $descendants;
}

/**
 * Descendant shade slugs of a color term.
 *
 * @param int|string $term_id Color term ID or slug.
 * @return string[]
 */
function lily_color_descendant_slugs( $term_id ) {
	if ( is_string( $term_id ) && ! ctype_digit( $term_id ) && taxonomy_exists( 'pa_color' ) ) {
		$term = get_term_by( 'slug', $term_id, 'pa_color' );
		$term = ( $term && ! is_wp_error( $term ) ) ? (int) $term->term_id : 0;
	} else {
		$term = absint( $term_id );
	}

	$slugs = array();

	foreach ( lily_color_descendant_ids( $term ) as $child_id ) {
		$child = get_term( $child_id, 'pa_color' );

		if ( $child && ! is_wp_error( $child ) ) {
			$slugs[] = $child->slug;
		}
	}

	return $slugs;
}

/**
 * The product's SPECIFIC color term — the Child Shade when one is assigned,
 * otherwise the directly assigned Parent Color.
 *
 * @param int $product_id Product ID.
 * @return WP_Term|null
 */
function lily_product_color_term( $product_id ) {
	if ( ! taxonomy_exists( 'pa_color' ) ) {
		return null;
	}

	$terms = wp_get_post_terms( (int) $product_id, 'pa_color' );

	if ( is_wp_error( $terms ) || empty( $terms ) ) {
		return null;
	}

	// Prefer the deepest assigned term: a Child Shade wins over its Parent.
	$specific = null;

	foreach ( $terms as $term ) {
		if ( $term->parent ) {
			$specific = $term;
			break;
		}
	}

	return $specific ? $specific : $terms[0];
}

/* ── Color swatches ───────────────────────────────────────────────────── */

/**
 * Canonical swatch hex for a color term — own `lily_swatch_color` meta,
 * inherited from the Parent Color for shades, else '' (caller falls back).
 *
 * @param WP_Term $term Color term.
 * @return string Hex color or ''.
 */
function lily_color_swatch( $term ) {
	if ( ! $term instanceof WP_Term || 'pa_color' !== $term->taxonomy ) {
		return '';
	}

	$swatch = get_term_meta( $term->term_id, 'lily_swatch_color', true );

	if ( is_string( $swatch ) && '' !== $swatch ) {
		return $swatch;
	}

	$parent = lily_color_parent( $term );

	if ( $parent ) {
		$swatch = get_term_meta( $parent->term_id, 'lily_swatch_color', true );

		if ( is_string( $swatch ) && '' !== $swatch ) {
			return $swatch;
		}
	}

	return '';
}

/**
 * Keep Parent Color assignment in sync whenever a Child Shade is assigned to
 * a product — regardless of which dashboard UI set the terms (Lily product
 * metabox, native Attributes box, import, REST...).
 *
 * Selecting "Aqua Blue" always yields the product both "Aqua Blue" and its
 * parent "Blue", so Parent Color filters/matching work natively everywhere.
 *
 * @param int    $object_id Object ID.
 * @param array  $terms     Term IDs assigned.
 * @param array  $tt_ids    Term taxonomy IDs assigned.
 * @param string $taxonomy  Taxonomy name.
 * @param bool   $append    Whether appending.
 * @param array  $old_tt_ids Previous term taxonomy IDs.
 */
function lily_sync_color_parent_assignment( $object_id, $terms, $tt_ids, $taxonomy, $append, $old_tt_ids ) {
	if ( 'pa_color' !== $taxonomy || empty( $terms ) ) {
		return;
	}

	static $recursing = false;

	if ( $recursing ) {
		return;
	}

	$term_ids = array_values( array_map( 'absint', (array) $terms ) );
	$missing  = array();

	foreach ( $term_ids as $term_id ) {
		$term = get_term( $term_id, 'pa_color' );

		if ( ! $term || is_wp_error( $term ) || ! $term->parent ) {
			continue;
		}

		if ( ! in_array( (int) $term->parent, $term_ids, true ) ) {
			$missing[] = (int) $term->parent;
		}
	}

	if ( empty( $missing ) ) {
		return;
	}

	$recursing = true;
	wp_set_object_terms( (int) $object_id, array_values( array_unique( array_merge( $term_ids, $missing ) ) ), 'pa_color', false );
	$recursing = false;
}
add_action( 'set_object_terms', 'lily_sync_color_parent_assignment', 10, 6 );

/* ── Dynamic term lists for JS (Lens Finder etc.) ─────────────────────── */

/**
 * Terms of a canonical taxonomy as frontend-friendly option arrays with
 * localized labels.
 *
 * @param string $kind         brand|color|look|duration.
 * @param bool   $parents_only Restrict to top-level terms (Parent Colors only).
 * @return array[] Array of [ 'label' => localized name, 'value' => slug ].
 */
function lily_catalog_terms_for_js( $kind, $parents_only = false ) {
	$taxonomy = lily_catalog_taxonomy( $kind );

	if ( ! $taxonomy ) {
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

	return array_map(
		static function ( $term ) {
			return array(
				'label' => lily_term_name( $term ),
				'value' => $term->slug,
			);
		},
		$terms
	);
}

