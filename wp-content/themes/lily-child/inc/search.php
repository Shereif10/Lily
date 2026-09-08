<?php
/**
 * Lily bilingual product search.
 *
 * Root cause this fixes:
 *
 * The header search form submits the WordPress native search (?s=).
 * On the default language (English) products are searchable through
 * post_title/post_content and work reasonably well.
 *
 * On Arabic pages TranslatePress rewrites the main search query
 * (TRP_Search::trp_search_filter, pre_get_posts @99999999): it destroys the
 * native search and replaces it with post IDs taken from the TRP dictionary
 * joined through the original_meta "post_parent_id" key. That meta records
 * only the page a string was first rendered/translated on — on this site the
 * product-title strings are linked to the Cart/Checkout pages or have no meta
 * at all, so searching the exact Arabic product name returned the Cart page
 * (or nothing) instead of the product.
 *
 * This layer runs AFTER TranslatePress's filter (both languages) and:
 *  - normalizes the query (case, extra spaces, Arabic alef/yeh/teh-marbuta
 *    variants, harakat/tatweel) — in memory only, stored data is untouched;
 *  - searches WooCommerce products directly (title, excerpt, content, slug,
 *    SKU) with forgiving matching;
 *  - for Arabic input, reverse-maps dictionary translations back to their
 *    ORIGINAL English strings and matches those against products — bypassing
 *    the unreliable post_parent_id meta entirely;
 *  - when products match, takes over the main query (products only, ordered
 *    best-match first). When nothing matches, the existing native/TRP
 *    behavior for pages/posts is preserved untouched.
 *
 * No TranslatePress data is written, no products are duplicated, and the
 * search results template/UI is unchanged.
 *
 * @package Lily
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Normalize a search string for forgiving matching.
 *
 * - strips tags, collapses leading/trailing/repeated whitespace
 * - removes Arabic tatweel + harakat (diacritics)
 * - unifies Arabic letter variants: أإآٱ→ا, ى→ي, ة→ه
 * - lowercases (EN case-insensitivity)
 *
 * @param string $string Raw input.
 * @return string Normalized string.
 */
function lily_search_normalize( $string ) {
	$string = (string) $string;
	$string = wp_strip_all_tags( $string );

	// Tatweel + harakat + superscript alef: pure decoration for matching.
	$string = str_replace( "\u{0640}", '', $string );
	$string = preg_replace( '/[\x{064B}-\x{0652}\x{0670}\x{0653}-\x{0655}]/u', '', $string );

	// Common Arabic letter variants users type interchangeably.
	$string = str_replace( array( "\u{0623}", "\u{0625}", "\u{0622}", "\u{0671}" ), "\u{0627}", $string ); // أإآٱ → ا
	$string = str_replace( "\u{0649}", "\u{064A}", $string ); // ى → ي
	$string = str_replace( "\u{0629}", "\u{0647}", $string ); // ة → ه

	// Collapse all whitespace runs to single spaces.
	$string = preg_replace( '/[\s\x{00A0}\x{2000}-\x{200B}]+/u', ' ', $string );

	$string = trim( $string );

	return function_exists( 'mb_strtolower' ) ? mb_strtolower( $string, 'UTF-8' ) : strtolower( $string );
}

/**
 * TRUE if the string contains Arabic script.
 *
 * @param string $string Input.
 * @return bool
 */
function lily_search_has_arabic( $string ) {
	return (bool) preg_match( '/\p{Arabic}/u', (string) $string );
}

/**
 * Escape a wildcard LIKE pattern for SQL: % and \ are escaped, while the
 * intentional single-character wildcards (_) stay active. Unlike
 * $wpdb->esc_like(), which would neutralize the wildcards this function's
 * callers deliberately inserted.
 *
 * @param string $pattern Pattern core with _ wildcards.
 * @return string Escaped pattern core.
 */
function lily_search_escape_pattern( $pattern ) {
	$pattern = str_replace( '\\', '\\\\', $pattern );
	return str_replace( '%', '\\%', $pattern );
}

/**
 * Build a forgiving SQL LIKE core for an Arabic term: every character that
 * belongs to an Arabic variant class becomes a single-character wildcard (_),
 * so the coarse DB pre-filter tolerates أ/ا, ي/ى, ه/ة mismatches. The final
 * verification re-normalizes candidate strings in PHP, so precision is kept.
 *
 * @param string $normalized_term Normalized term.
 * @return string LIKE pattern core (escaped, without %).
 */
function lily_search_like_pattern( $normalized_term ) {
	$classes = array(
		"\u{0627}" => array( "\u{0623}", "\u{0625}", "\u{0622}", "\u{0671}" ), // ا class
		"\u{064A}" => array( "\u{0649}" ),                                      // ي class
		"\u{0647}" => array( "\u{0629}" ),                                      // ه class
	);

	$pattern = '';
	$length  = mb_strlen( $normalized_term, 'UTF-8' );

	for ( $i = 0; $i < $length; $i++ ) {
		$char = mb_substr( $normalized_term, $i, 1, 'UTF-8' );
		$is_variant = false;
		foreach ( $classes as $canonical => $variants ) {
			if ( $char === $canonical || in_array( $char, $variants, true ) ) {
				$is_variant = true;
				break;
			}
		}
		if ( $is_variant ) {
			$pattern .= '_';
		} else {
			$pattern .= $char;
		}
	}

	return $pattern;
}

/**
 * Get the TranslatePress dictionary table names (original → translated) for
 * every published non-default language. Uses TRP's own component so the
 * naming always matches the active installation.
 *
 * @return string[] Table names (empty array when TRP is unavailable).
 */
function lily_search_dictionary_tables() {
	global $wpdb;

	$tables = array();

	if ( ! class_exists( 'TRP_Translate_Press' ) ) {
		return $tables;
	}

	$trp = TRP_Translate_Press::get_trp_instance();
	if ( ! $trp ) {
		return $tables;
	}

	$query_component = $trp->get_component( 'query' );
	if ( ! $query_component || ! method_exists( $query_component, 'get_table_name' ) ) {
		return $tables;
	}

	$settings = $trp->get_component( 'settings' );
	if ( ! $settings ) {
		return $tables;
	}

	$trp_settings = $settings->get_settings();
	$default      = isset( $trp_settings['default-language'] ) ? $trp_settings['default-language'] : 'en_US';
	$languages    = isset( $trp_settings['translation-languages'] ) ? (array) $trp_settings['translation-languages'] : array();
	$published    = isset( $trp_settings['publish-languages'] ) ? (array) $trp_settings['publish-languages'] : $languages;

	foreach ( $languages as $language ) {
		if ( $language === $default || ! in_array( $language, $published, true ) ) {
			continue;
		}

		$table = $query_component->get_table_name( $language );

		if ( $table && $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table ) ) === $table ) { // phpcs:ignore WordPress.DB.DirectDatabaseQuery
			$tables[] = $table;
		}
	}

	return $tables;
}

/**
 * Find WooCommerce products matching a search phrase (forgiving, bilingual).
 *
 * Two matching paths, merged and scored:
 *  1. Direct — every term must match title OR excerpt OR content OR slug OR
 *     SKU (covers English input on any language, and native Arabic stored
 *     anywhere in product fields).
 *  2. Dictionary reverse — for Arabic input, find TRP dictionary rows whose
 *     TRANSLATED text matches (with variant tolerance), take their ORIGINAL
 *     English strings, and match products against those originals. This makes
 *     Arabic product names findable without trusting TRP's post_parent_id
 *     meta and without touching TranslatePress data.
 *
 * @param string $raw_query Raw search phrase.
 * @return int[] Product IDs ordered best-match first.
 */
function lily_search_find_products( $raw_query ) {
	global $wpdb;

	$normalized = lily_search_normalize( $raw_query );

	if ( '' === $normalized ) {
		return array();
	}

	$terms = array_values( array_filter( array_map( 'lily_search_normalize', preg_split( '/\s+/u', $normalized ) ) ) );

	if ( empty( $terms ) ) {
		return array();
	}

	$direct_ids = lily_search_direct_product_ids( $terms );

	$dict_buckets = array(
		'title'   => array(),
		'content' => array(),
	);

	if ( lily_search_has_arabic( $normalized ) ) {
		$dict_buckets = lily_search_dictionary_product_ids( $terms );
	}

	$dict_ids = array_values( array_unique( array_merge( $dict_buckets['title'], $dict_buckets['content'] ) ) );

	$candidates = array_values( array_unique( array_merge( $direct_ids, $dict_ids ) ) );

	if ( empty( $candidates ) ) {
		return array();
	}

	return lily_search_score_products( $candidates, $normalized, $terms, $dict_buckets );
}

/**
 * Direct product matching: every term must be found in at least one of
 * title/excerpt/content/slug/SKU. Case and extra spaces are handled by
 * normalization + case-insensitive collation.
 *
 * @param string[] $terms Normalized terms.
 * @return int[] Product IDs.
 */
function lily_search_direct_product_ids( $terms ) {
	global $wpdb;

	$where = array();
	$args  = array();

	foreach ( $terms as $term ) {
		$like    = '%' . $wpdb->esc_like( $term ) . '%';
		$escaped = $wpdb->esc_like( $term );
		$words   = preg_split( '/\s+/u', $term );
		$words   = array_filter( $words );

		// Match either the whole term, or all of its individual words.
		$conds   = array( '( p.post_title LIKE %s OR p.post_excerpt LIKE %s OR p.post_content LIKE %s OR p.post_name LIKE %s OR sku.meta_value LIKE %s )' );
		$args[]  = $like;
		$args[]  = $like;
		$args[]  = $like;
		$args[]  = $like;
		$args[]  = $like;

		if ( count( $words ) > 1 ) {
			$word_conds = array();
			foreach ( $words as $word ) {
				$word_like   = '%' . $wpdb->esc_like( $word ) . '%';
				$word_conds[] = '( p.post_title LIKE %s OR p.post_excerpt LIKE %s OR p.post_content LIKE %s OR p.post_name LIKE %s OR sku.meta_value LIKE %s )';
				array_push( $args, $word_like, $word_like, $word_like, $word_like, $word_like );
			}
			$conds[] = '( ' . implode( ' AND ', $word_conds ) . ' )';
		}

		$where[] = '( ' . implode( ' OR ', $conds ) . ' )';
	}

	$sql  = "SELECT DISTINCT p.ID FROM {$wpdb->posts} p";
	$sql .= " LEFT JOIN {$wpdb->postmeta} sku ON sku.post_id = p.ID AND sku.meta_key = '_sku'";
	$sql .= " WHERE p.post_type = 'product' AND p.post_status = 'publish'";
	$sql .= " AND NOT EXISTS (
		SELECT 1 FROM {$wpdb->term_relationships} tr
		INNER JOIN {$wpdb->term_taxonomy} tt ON tt.term_taxonomy_id = tr.term_taxonomy_id
		INNER JOIN {$wpdb->terms} t ON t.term_id = tt.term_id
		WHERE tr.object_id = p.ID AND tt.taxonomy = 'product_visibility'
		AND t.name IN ('exclude-from-search','exclude-from-catalog')
	)";
	$sql .= ' AND ' . implode( ' AND ', $where );
	$sql .= ' LIMIT 200';

	// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery -- built with esc_like + prepare below.
	$sql = $wpdb->prepare( $sql, $args ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery

	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared
	$ids = array_map( 'absint', (array) $wpdb->get_col( $sql ) );

	return $ids;
}

/**
 * Dictionary reverse-lookup matching (Arabic input).
 *
 * 1. Coarse LIKE (variant-wildcard pattern) over dictionary `translated`.
 * 2. PHP verification with full normalization (precision).
 * 3. Products must match originals covering ALL terms — title-only matches
 *    score higher than content matches.
 *
 * @param string[] $terms Normalized terms.
 * @return array{title:int[],content:int[]} Product IDs per bucket.
 */
function lily_search_dictionary_product_ids( $terms ) {
	global $wpdb;

	$tables = lily_search_dictionary_tables();

	if ( empty( $tables ) ) {
		return array(
			'title'   => array(),
			'content' => array(),
		);
	}

	// original (English) string => set of term indexes it covers.
	$original_coverage = array();

	foreach ( $tables as $table ) {
		foreach ( $terms as $index => $term ) {
			$pattern = '%' . lily_search_escape_pattern( lily_search_like_pattern( $term ) ) . '%';

			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery -- table name from TRP component, value prepared.
			$rows = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT d.original AS original, d.translated AS translated FROM {$table} d WHERE d.translated LIKE %s LIMIT 400", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
					$pattern
				)
			);

			if ( ! is_array( $rows ) ) {
				continue;
			}

			foreach ( $rows as $row ) {
				$original   = trim( (string) $row->original );
				$translated = lily_search_normalize( (string) $row->translated );

				if ( '' === $original || '' === $translated ) {
					continue;
				}

				// Precise verification with full normalization.
				if ( false === strpos( $translated, $term ) ) {
					continue;
				}

				if ( ! isset( $original_coverage[ $original ] ) ) {
					$original_coverage[ $original ] = array();
				}
				$original_coverage[ $original ][ $index ] = true;
			}
		}
	}

	// Keep only originals that cover every term of the query.
	$full_originals = array();
	foreach ( $original_coverage as $original => $covered ) {
		if ( count( $covered ) === count( $terms ) ) {
			$full_originals[] = $original;
		}
	}

	if ( empty( $full_originals ) ) {
		return array(
			'title'   => array(),
			'content' => array(),
		);
	}

	return array(
		'title'   => lily_search_products_by_originals( $full_originals, true ),
		'content' => lily_search_products_by_originals( $full_originals, false ),
	);
}

/**
 * Match products against a list of original (English) strings.
 *
 * @param string[] $originals Original strings.
 * @param bool     $title_only Restrict to post_title (strong signal).
 * @return int[] Product IDs.
 */
function lily_search_products_by_originals( $originals, $title_only ) {
	global $wpdb;

	$originals = array_slice( array_unique( $originals ), 0, 300 );
	$where     = array();
	$args      = array();

	foreach ( $originals as $original ) {
		$like = '%' . $wpdb->esc_like( $original ) . '%';

		if ( $title_only ) {
			$where[] = '( p.post_title LIKE %s )';
			$args[]  = $like;
		} else {
			$where[] = '( p.post_title LIKE %s OR p.post_excerpt LIKE %s OR p.post_content LIKE %s OR sku.meta_value LIKE %s )';
			array_push( $args, $like, $like, $like, $like );
		}
	}

	$sql  = "SELECT DISTINCT p.ID FROM {$wpdb->posts} p";
	$sql .= " LEFT JOIN {$wpdb->postmeta} sku ON sku.post_id = p.ID AND sku.meta_key = '_sku'";
	$sql .= " WHERE p.post_type = 'product' AND p.post_status = 'publish'";
	$sql .= " AND NOT EXISTS (
		SELECT 1 FROM {$wpdb->term_relationships} tr
		INNER JOIN {$wpdb->term_taxonomy} tt ON tt.term_taxonomy_id = tr.term_taxonomy_id
		INNER JOIN {$wpdb->terms} t ON t.term_id = tt.term_id
		WHERE tr.object_id = p.ID AND tt.taxonomy = 'product_visibility'
		AND t.name IN ('exclude-from-search','exclude-from-catalog')
	)";
	$sql .= ' AND ( ' . implode( ' OR ', $where ) . ' )';
	$sql .= ' LIMIT 200';

	// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery -- built with esc_like, prepared here.
	$sql = $wpdb->prepare( $sql, $args ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery

	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared
	$ids = array_map( 'absint', (array) $wpdb->get_col( $sql ) );

	return $ids;
}

/**
 * Score candidates so exact title matches come first, then partial title
 * matches, then broader field matches, then dictionary-content matches.
 *
 * @param int[]   $candidates   Candidate product IDs.
 * @param string  $normalized   Normalized full phrase.
 * @param string[] $terms       Normalized terms.
 * @param array    $dictionary_ids Title/content dictionary buckets.
 * @return int[] Scored, ordered IDs (best first).
 */
function lily_search_score_products( $candidates, $normalized, $terms, $dictionary_ids ) {
	global $wpdb;

	$dict_title_ids   = isset( $dictionary_ids['title'] ) ? $dictionary_ids['title'] : array();
	$dict_content_ids = isset( $dictionary_ids['content'] ) ? $dictionary_ids['content'] : array();

	$fields = array();

	// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
	$rows = $wpdb->get_results(
		$wpdb->prepare(
			"SELECT p.ID, p.post_title, p.post_excerpt, p.post_content, p.post_name, sku.meta_value AS sku
			 FROM {$wpdb->posts} p
			 LEFT JOIN {$wpdb->postmeta} sku ON sku.post_id = p.ID AND sku.meta_key = '_sku'
			 WHERE p.ID IN ( " . implode( ',', array_fill( 0, count( $candidates ), '%d' ) ) . ' )',
			$candidates
		)
	);

	foreach ( (array) $rows as $row ) {
		$fields[ (int) $row->ID ] = array(
			'title'   => lily_search_normalize( $row->post_title ),
			'excerpt' => lily_search_normalize( $row->post_excerpt ),
			'content' => lily_search_normalize( wp_strip_all_tags( $row->post_content ) ),
			'slug'    => lily_search_normalize( str_replace( array( '-', '_' ), ' ', $row->post_name ) ),
			'sku'     => lily_search_normalize( (string) $row->sku ),
		);
	}

	$scored = array();

	foreach ( $candidates as $id ) {
		$score = 0;
		$data  = isset( $fields[ $id ] ) ? $fields[ $id ] : array(
			'title'   => '',
			'excerpt' => '',
			'content' => '',
			'slug'    => '',
			'sku'     => '',
		);

		if ( $data['title'] === $normalized ) {
			$score = 100;
		} elseif ( '' !== $normalized && false !== strpos( $data['title'], $normalized ) ) {
			$score = 90;
		} elseif ( lily_search_all_terms_in( $terms, $data['title'] ) ) {
			$score = 80;
		} elseif ( in_array( $id, $dict_title_ids, true ) ) {
			$score = 85;
		}

		if ( $score < 40 ) {
			$haystack = $data['title'] . ' ' . $data['excerpt'] . ' ' . $data['content'] . ' ' . $data['slug'] . ' ' . $data['sku'];
			if ( lily_search_all_terms_in( $terms, $haystack ) ) {
				$score = max( $score, 40 );
			}
		}

		if ( $score < 30 && in_array( $id, $dict_content_ids, true ) ) {
			$score = 30;
		}

		if ( 0 === $score ) {
			continue;
		}

		$scored[] = array( 'id' => $id, 'score' => $score );
	}

	usort(
		$scored,
		static function ( $a, $b ) {
			if ( $a['score'] === $b['score'] ) {
				return $a['id'] <=> $b['id'];
			}
			return $b['score'] <=> $a['score'];
		}
	);

	return array_slice( wp_list_pluck( $scored, 'id' ), 0, 60 );
}

/**
 * TRUE when every term appears in the haystack.
 *
 * @param string[] $terms Normalized terms.
 * @param string   $haystack Normalized haystack.
 * @return bool
 */
function lily_search_all_terms_in( $terms, $haystack ) {
	foreach ( $terms as $term ) {
		if ( false === strpos( (string) $haystack, $term ) ) {
			return false;
		}
	}
	return true;
}

/**
 * Take over the main search query when products match.
 *
 * Runs AFTER TranslatePress's trp_search_filter (99999999) so our results
 * replace its post__in output. When no product matches, the existing native /
 * TranslatePress behavior is left completely untouched.
 *
 * @param WP_Query $query Main query.
 */
function lily_search_products_first( $query ) {
	if ( is_admin() || ! $query->is_main_query() || ! $query->is_search() ) {
		return;
	}

	if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
		return;
	}

	if ( wp_doing_ajax() ) {
		return;
	}

	$raw = '';

	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- public catalog search.
	if ( isset( $_GET['s'] ) ) {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$raw = sanitize_text_field( wp_unslash( $_GET['s'] ) );
	}

	if ( '' === $raw ) {
		$raw = (string) $query->get( 's' );
	}

	$raw = trim( $raw );

	if ( '' === $raw ) {
		return;
	}

	$product_ids = lily_search_find_products( $raw );

	if ( empty( $product_ids ) ) {
		return;
	}

	lily_search_set_marker( true );

	$query->set( 'post_type', 'product' );
	$query->set( 'post__in', $product_ids );
	$query->set( 'orderby', 'post__in' );
	$query->set( 's', '' );
}
add_action( 'pre_get_posts', 'lily_search_products_first', 100000010 );

/**
 * Marker: the main search query was taken over by the Lily product search.
 *
 * @param bool|null $set True to set, null to read.
 * @return bool
 */
function lily_search_set_marker( $set = null ) {
	static $marker = false;

	if ( null !== $set ) {
		$marker = (bool) $set;
	}

	return $marker;
}

/**
 * Keep the typed phrase visible in the results header / search forms after
 * the query 's' var was blanked during takeover. Mirrors the approach
 * TranslatePress itself uses for the same purpose.
 *
 * @param string $search Current value.
 * @return string
 */
function lily_search_restore_display_query( $search ) {
	if ( is_admin() ) {
		return $search;
	}

	if ( '' === trim( (string) $search ) ) {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- public catalog search.
		if ( isset( $_GET['s'] ) ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return sanitize_text_field( wp_unslash( $_GET['s'] ) );
		}
	}

	return $search;
}
add_filter( 'get_search_query', 'lily_search_restore_display_query', 20 );
