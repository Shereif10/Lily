<?php
/**
 * Lily Product Reviews — native WooCommerce/WordPress review workflow.
 *
 * - Lists APPROVED reviews only (never pending/spam/trash).
 * - Stats (count + average) computed from approved reviews only.
 * - New frontend reviews forced to pending moderation.
 * - Up to 3 secure image uploads per review via the native media system.
 * - Reuses the native Comments dashboard for moderation (no parallel CMS).
 *
 * @package Lily
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'LILY_REVIEW_MAX_IMAGES' ) ) {
	define( 'LILY_REVIEW_MAX_IMAGES', 3 );
}

if ( ! defined( 'LILY_REVIEW_MAX_BYTES' ) ) {
	define( 'LILY_REVIEW_MAX_BYTES', 5 * 1024 * 1024 );
}

/* ── Product page body class ────────────────────────────────────────────
 * The single-product CSS scope expects body.lily-product-page (also carries
 * the existing Woo-blue neutralisation). Add it on real product views only.
 */

function lily_product_page_body_class( $classes ) {
	if ( function_exists( 'is_product' ) && is_product() ) {
		$classes[] = 'lily-product-page';
	}
	return $classes;
}
add_filter( 'body_class', 'lily_product_page_body_class' );

/* ── Helpers ──────────────────────────────────────────────────────────── */

/**
 * Resolve the review scope product ID (variations roll up to the parent).
 *
 * @param WC_Product|int $product Product object or ID.
 * @return int
 */
function lily_review_scope_product_id( $product ) {
	$product_id = $product instanceof WC_Product ? $product->get_id() : absint( $product );

	if ( function_exists( 'wc_get_product' ) ) {
		$scope = wc_get_product( $product_id );
		if ( $scope && $scope->is_type( 'variation' ) && $scope->get_parent_id() ) {
			return absint( $scope->get_parent_id() );
		}
	}

	return $product_id;
}

/**
 * Get APPROVED product reviews only.
 *
 * @param int $product_id Product ID.
 * @return WP_Comment[]
 */
function lily_get_approved_product_reviews( $product_id ) {
	$product_id = absint( $product_id );
	if ( $product_id < 1 ) {
		return array();
	}

	$comments = get_comments(
		array(
			'post_id' => $product_id,
			'status'  => 'approve',
			'type'    => 'review',
			'orderby' => 'comment_date_gmt',
			'order'   => 'DESC',
			'number'  => 200,
		)
	);

	return is_array( $comments ) ? $comments : array();
}

/**
 * Compute review stats from APPROVED reviews only.
 *
 * @param int $product_id Product ID.
 * @return array{count:int, average:float}
 */
function lily_get_product_review_stats( $product_id ) {
	$reviews = lily_get_approved_product_reviews( $product_id );
	$count   = count( $reviews );
	$sum     = 0;
	$rated   = 0;

	foreach ( $reviews as $review ) {
		$rating = absint( get_comment_meta( $review->comment_ID, 'rating', true ) );
		if ( $rating >= 1 && $rating <= 5 ) {
			$sum   += $rating;
			$rated += 1;
		}
	}

	return array(
		'count'   => $count,
		'average' => $rated > 0 ? ( (float) $sum / (float) $rated ) : 0.0,
	);
}

/**
 * Get validated review image attachments for a comment.
 *
 * @param int $comment_id Comment ID.
 * @return array[]
 */
function lily_get_review_images( $comment_id ) {
	$ids = get_comment_meta( absint( $comment_id ), 'lily_review_images', true );
	if ( ! is_array( $ids ) || empty( $ids ) ) {
		return array();
	}

	$allowed = array( 'image/jpeg', 'image/png', 'image/webp' );
	$images  = array();

	foreach ( array_slice( $ids, 0, LILY_REVIEW_MAX_IMAGES ) as $attachment_id ) {
		$attachment_id = absint( $attachment_id );
		if ( $attachment_id < 1 || ! wp_attachment_is_image( $attachment_id ) ) {
			continue;
		}

		$mime = get_post_mime_type( $attachment_id );
		if ( ! in_array( $mime, $allowed, true ) ) {
			continue;
		}

		$thumb = wp_get_attachment_image_url( $attachment_id, 'thumbnail' );
		$full  = wp_get_attachment_image_url( $attachment_id, 'large' );
		if ( ! $thumb || ! $full ) {
			continue;
		}

		$images[] = array(
			'id'    => $attachment_id,
			'thumb' => $thumb,
			'full'  => $full,
			'alt'   => get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ),
		);
	}

	return $images;
}

/**
 * Whether the review form should render for this product.
 *
 * @param int $product_id Product ID.
 * @return bool
 */
function lily_product_reviews_open( $product_id ) {
	if ( function_exists( 'wc_get_product' ) ) {
		$product = wc_get_product( absint( $product_id ) );
		if ( $product && method_exists( $product, 'get_reviews_allowed' ) && ! $product->get_reviews_allowed() ) {
			return false;
		}
	}

	if ( 'yes' === get_option( 'woocommerce_enable_reviews', 'yes' ) ) {
		return comments_open( absint( $product_id ) ) || 'open' === get_post_field( 'comment_status', absint( $product_id ) );
	}

	return comments_open( absint( $product_id ) );
}

/**
 * Whether star ratings are enabled store-wide.
 *
 * @return bool
 */
function lily_reviews_rating_enabled() {
	return 'yes' === get_option( 'woocommerce_enable_review_rating', 'yes' );
}

/**
 * Default HOW TO USE copy — derived from the canonical default steps so the
 * step editor and this fallback share one source. Filterable per product.
 *
 * @param WC_Product|null $product Product or null.
 * @return string
 */
function lily_product_how_to_use_content( $product = null ) {
	$default = implode( ' ', lily_how_to_use_default_steps() );

	/**
	 * Filter the HOW TO USE accordion copy.
	 *
	 * @param string         $default Default copy.
	 * @param WC_Product|null $product Current product or null.
	 */
	return apply_filters( 'lily_product_how_to_use', $default, $product );
}

/* ── Stars ────────────────────────────────────────────────────────────── */

/**
 * Render an accessible star row.
 *
 * @param float $rating Rating 0-5.
 */
function lily_review_stars( $rating ) {
	$rating = max( 0, min( 5, (float) $rating ) );
	$full   = (int) floor( $rating + 0.25 );
	$out    = '';

	for ( $i = 1; $i <= 5; $i++ ) {
		$out .= $i <= $full ? '★' : '☆';
	}

	printf(
		'<span class="lily-reviews__stars" aria-hidden="true">%s</span><span class="screen-reader-text">%s</span>',
		esc_html( $out ),
		esc_html(
			sprintf(
				/* translators: %s: numeric rating, e.g. 4.8 out of 5. */
				__( 'Rated %s out of 5', 'lily' ),
				number_format_i18n( $rating, 1 )
			)
		)
	);
}

/* ── Moderation: every new frontend product review stays pending ──────── */

/**
 * Force pending moderation for EVERY new product review submitted from the
 * frontend — guests, customers and logged-in staff alike. Nothing publishes
 * itself automatically; approval happens only in the dashboard
 * (Comments → Pending → Approve).
 *
 * Runs on WordPress's authoritative comment-approval filter, so it wins over
 * Discussion Settings, plugin auto-approval and the "previously approved
 * author" shortcut. Dashboard comment screens keep the native behavior.
 *
 * @param string|int $approved    Approved status.
 * @param array      $commentdata Comment data.
 * @return string|int
 */
function lily_review_force_pending( $approved, $commentdata ) {
	/* Pingbacks/trackbacks are never product reviews. */
	if ( ! empty( $commentdata['comment_type'] ) && in_array( $commentdata['comment_type'], array( 'trackback', 'pingback' ), true ) ) {
		return $approved;
	}

	$post_id = isset( $commentdata['comment_post_ID'] ) ? absint( $commentdata['comment_post_ID'] ) : 0;
	if ( $post_id < 1 || 'product' !== get_post_type( $post_id ) ) {
		return $approved;
	}

	/* Dashboard comment screens keep core behavior (moderation lives there). */
	if ( is_admin() && ! wp_doing_ajax() ) {
		return $approved;
	}

	return '0';
}
add_filter( 'pre_comment_approved', 'lily_review_force_pending', 999, 2 );

/**
 * Safety net: re-pend any product review that reached the database as
 * approved from a non-moderator context (insertion paths that skip the
 * native wp_allow_comment flow). Dashboard/moderator submissions keep the
 * core behavior; existing reviews are never touched.
 *
 * @param int        $comment_id New comment ID.
 * @param WP_Comment $comment    Comment object.
 */
function lily_review_repend_on_insert( $comment_id, $comment ) {
	if ( ! $comment instanceof WP_Comment ) {
		return;
	}

	/* Never interfere with dashboard-created comments. */
	if ( is_admin() && ! wp_doing_ajax() ) {
		return;
	}

	/* Moderators may create approved reviews anywhere (trusted dashboard flow). */
	if ( current_user_can( 'moderate_comments' ) ) {
		return;
	}

	/* Pingbacks/trackbacks are never product reviews. */
	if ( in_array( $comment->comment_type, array( 'trackback', 'pingback' ), true ) ) {
		return;
	}

	$post_id = absint( $comment->comment_post_ID );
	if ( $post_id < 1 || 'product' !== get_post_type( $post_id ) ) {
		return;
	}

	/* Only already-approved insertions need correcting. */
	if ( 1 !== (int) $comment->comment_approved ) {
		return;
	}

	wp_update_comment(
		array(
			'comment_ID'       => $comment_id,
			'comment_approved' => 0,
		)
	);
}
add_action( 'wp_insert_comment', 'lily_review_repend_on_insert', 10, 2 );

/**
 * Surface product reviews inside the native Comments dashboard screens
 * (Comments → Pending list and its counters).
 *
 * Recent WooCommerce versions hide product reviews from the Comments screens
 * (they host moderation on their own Products → Reviews page). Lily moderates
 * product reviews through the native Comments → Pending flow, so generic
 * admin listing/count queries — the ones that do not target a specific
 * comment type, post type or post — are given the full content scope. That
 * makes WooCommerce's review-exclusion filter stand down for those queries
 * only; every explicitly scoped query (frontend reviews, Products → Reviews,
 * product lookups) keeps its own behavior untouched.
 *
 * @param WP_Comment_Query $query Current comment query.
 */
function lily_reviews_in_comments_screens( $query ) {
	if ( ! is_admin() || wp_doing_ajax() ) {
		return;
	}

	$vars = $query->query_vars;

	/*
	 * Explicitly scoped queries manage their own scope. Note: the native
	 * Comments list table always passes type__not_in = ['note'], which is a
	 * block-comment exclusion, not a scope signal — so type__not_in is NOT
	 * treated as explicit scoping here.
	 */
	if ( ! empty( $vars['type'] ) || ! empty( $vars['type__in'] )
		|| ! empty( $vars['post_type'] ) || ! empty( $vars['post_id'] ) || ! empty( $vars['post__in'] )
		|| ! empty( $vars['parent'] ) || ! empty( $vars['parent__in'] ) || ! empty( $vars['parent__not_in'] )
		|| ! empty( $vars['comment__in'] ) || ! empty( $vars['comment__not_in'] ) || ! empty( $vars['ID'] ) ) {
		return;
	}

	/* WP_Comment_Query has no set() — assign the public property directly. */
	$query->query_vars['post_type'] = array( 'product', 'post', 'page' );
}
add_action( 'pre_get_comments', 'lily_reviews_in_comments_screens', 10 );

/**
 * Re-include product reviews in the native Comments list table.
 *
 * WooCommerce's ReviewsCommentsOverrides hooks this same filter (priority 10)
 * on the edit-comments screen and strips 'product' from the post-type scope.
 * Running later restores it, so the Comments → Pending / Approved / Spam /
 * Trash views list product reviews again. Every other admin screen is
 * untouched, and WooCommerce's own Products → Reviews page keeps working
 * (it queries reviews directly with its own post-type scope).
 *
 * @param array $args get_comments() arguments for the list table.
 * @return array
 */
function lily_reviews_in_comments_list_args( $args ) {
	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;

	if ( ! $screen instanceof WP_Screen || 'edit-comments' !== $screen->id ) {
		return $args;
	}

	if ( ! is_array( $args ) ) {
		$args = array();
	}

	$post_types = ! empty( $args['post_type'] ) && 'any' !== $args['post_type'] ? (array) $args['post_type'] : array();

	if ( ! in_array( 'product', $post_types, true ) ) {
		$post_types[] = 'product';
	}

	$args['post_type'] = array_values( $post_types );

	return $args;
}
add_filter( 'comments_list_table_query_args', 'lily_reviews_in_comments_list_args', 20 );

/**
 * After a native review POST, land back on the product with the
 * reviews accordion addressable and a quiet thank-you flag.
 *
 * @param string         $location Redirect URL.
 * @param WP_Comment|int|array $comment Comment.
 * @return string
 */
function lily_review_post_redirect( $location, $comment ) {
	$post_id = 0;

	if ( $comment instanceof WP_Comment ) {
		$post_id = absint( $comment->comment_post_ID );
	} elseif ( is_array( $comment ) && isset( $comment['comment_post_ID'] ) ) {
		$post_id = absint( $comment['comment_post_ID'] );
	} elseif ( is_numeric( $comment ) ) {
		$found = get_comment( absint( $comment ) );
		if ( $found instanceof WP_Comment ) {
			$post_id = absint( $found->comment_post_ID );
		}
	}

	if ( $post_id < 1 || 'product' !== get_post_type( $post_id ) ) {
		return $location;
	}

	$permalink = get_permalink( $post_id );
	if ( ! $permalink ) {
		return $location;
	}

	return add_query_arg( 'lily_review', 'submitted', $permalink ) . '#lily-reviews';
}
add_filter( 'comment_post_redirect', 'lily_review_post_redirect', 10, 2 );

/* ── Secure image uploads (server-side, native media library) ─────────── */

/**
 * Allowlist for review uploads. SVG and everything executable excluded.
 *
 * @return array Extension => mime.
 */
function lily_review_allowed_mimes() {
	return array(
		'jpg'  => 'image/jpeg',
		'jpeg' => 'image/jpeg',
		'png'  => 'image/png',
		'webp' => 'image/webp',
	);
}

/**
 * Validate one uploaded file against actual content (never extension alone).
 *
 * @param string $tmp_name      Temporary path.
 * @param string $original_name Original filename.
 * @param int    $size          File size in bytes.
 * @param int    $error         PHP upload error code.
 * @return string|false Validated mime or false.
 */
function lily_validate_review_image( $tmp_name, $original_name, $size, $error ) {
	if ( UPLOAD_ERR_OK !== absint( $error ) ) {
		return false;
	}

	if ( ! is_string( $tmp_name ) || '' === $tmp_name || ! is_uploaded_file( $tmp_name ) ) {
		return false;
	}

	$size = absint( $size );
	if ( $size < 1 || $size > LILY_REVIEW_MAX_BYTES ) {
		return false;
	}

	$check = wp_check_filetype_and_ext( $tmp_name, (string) $original_name, lily_review_allowed_mimes() );
	if ( empty( $check['ext'] ) || empty( $check['type'] ) ) {
		return false;
	}

	if ( ! in_array( $check['type'], array_values( lily_review_allowed_mimes() ), true ) ) {
		return false;
	}

	$real_mime = wp_get_image_mime( $tmp_name );
	if ( ! $real_mime || $real_mime !== $check['type'] ) {
		return false;
	}

	return $check['type'];
}

/**
 * Normalise the multi-file $_FILES entry into a flat list.
 *
 * @param array $files $_FILES entry.
 * @return array[]
 */
function lily_normalise_review_files( $files ) {
	$flat = array();

	if ( ! is_array( $files ) || ! isset( $files['name'] ) || ! is_array( $files['name'] ) ) {
		return $flat;
	}

	$count = count( $files['name'] );
	for ( $i = 0; $i < $count; $i++ ) {
		$flat[] = array(
			'name'     => isset( $files['name'][ $i ] ) ? $files['name'][ $i ] : '',
			'type'     => isset( $files['type'][ $i ] ) ? $files['type'][ $i ] : '',
			'tmp_name' => isset( $files['tmp_name'][ $i ] ) ? $files['tmp_name'][ $i ] : '',
			'error'    => isset( $files['error'][ $i ] ) ? $files['error'][ $i ] : UPLOAD_ERR_NO_FILE,
			'size'     => isset( $files['size'][ $i ] ) ? $files['size'][ $i ] : 0,
		);
	}

	return $flat;
}

/**
 * Process review images on native comment submission.
 *
 * Runs inside the core wp-comments-post.php flow, so moderation,
 * capabilities, notifications and the dashboard stay 100% native.
 * Invalid files are skipped quietly — the review itself still saves.
 *
 * @param int   $comment_id       New comment ID.
 * @param mixed $comment_approved Approval status.
 * @param array $commentdata      Comment data.
 */
function lily_review_handle_images( $comment_id, $comment_approved, $commentdata ) {
	$comment_id = absint( $comment_id );
	if ( $comment_id < 1 ) {
		return;
	}

	$comment = get_comment( $comment_id );
	if ( ! $comment instanceof WP_Comment ) {
		return;
	}

	$product_id = absint( $comment->comment_post_ID );
	if ( $product_id < 1 || 'product' !== get_post_type( $product_id ) ) {
		return;
	}

	/* Nonce + context validation: only our own product form may attach files. */
	if ( empty( $_POST['lily_review_images_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['lily_review_images_nonce'] ) ), 'lily_review_images' ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing -- verified here.
		return;
	}

	if ( empty( $_FILES['lily_review_images'] ) || ! is_array( $_FILES['lily_review_images'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing -- nonce checked above.
		return;
	}

	$files = lily_normalise_review_files( $_FILES['lily_review_images'] ); // phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- validated per-file below.
	if ( empty( $files ) ) {
		return;
	}

	$files = array_slice( $files, 0, LILY_REVIEW_MAX_IMAGES );

	if ( ! function_exists( 'media_handle_sideload' ) ) {
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';
	}

	$attachment_ids = array();

	foreach ( $files as $file ) {
		if ( UPLOAD_ERR_NO_FILE === absint( $file['error'] ) ) {
			continue;
		}

		$mime = lily_validate_review_image( $file['tmp_name'], $file['name'], $file['size'], $file['error'] );
		if ( ! $mime ) {
			continue;
		}

		$sideload = array(
			'name'     => sanitize_file_name( wp_unslash( (string) $file['name'] ) ),
			'type'     => $mime,
			'tmp_name' => $file['tmp_name'],
			'error'    => 0,
			'size'     => absint( $file['size'] ),
		);

		$attachment_id = media_handle_sideload( $sideload, $product_id );

		if ( is_wp_error( $attachment_id ) || ! wp_attachment_is_image( absint( $attachment_id ) ) ) {
			continue;
		}

		$attachment_id = absint( $attachment_id );
		update_post_meta( $attachment_id, '_lily_review_comment', $comment_id );
		$attachment_ids[] = $attachment_id;

		if ( count( $attachment_ids ) >= LILY_REVIEW_MAX_IMAGES ) {
			break;
		}
	}

	if ( ! empty( $attachment_ids ) ) {
		update_comment_meta( $comment_id, 'lily_review_images', array_values( array_unique( array_map( 'absint', $attachment_ids ) ) ) );
	}
}
add_action( 'comment_post', 'lily_review_handle_images', 20, 3 );

/**
 * Remove review attachments when the review itself is permanently deleted.
 *
 * @param int $comment_id Comment ID.
 */
function lily_review_images_cleanup( $comment_id ) {
	$ids = get_comment_meta( absint( $comment_id ), 'lily_review_images', true );
	if ( ! is_array( $ids ) || empty( $ids ) ) {
		return;
	}

	foreach ( $ids as $attachment_id ) {
		$attachment_id = absint( $attachment_id );
		if ( $attachment_id > 0 ) {
			wp_delete_attachment( $attachment_id, true );
		}
	}
}
add_action( 'delete_comment', 'lily_review_images_cleanup' );

/* ── Dashboard: surface review photos inside the native comment screen ── */

function lily_review_images_comment_metabox() {
	add_meta_box(
		'lily-review-images',
		__( 'Review Photos', 'lily' ),
		'lily_render_review_images_metabox',
		'comment',
		'normal',
		'default'
	);
}
add_action( 'add_meta_boxes_comment', 'lily_review_images_comment_metabox' );

/**
 * Render the review-photos meta box on the native Edit Comment screen.
 *
 * @param WP_Comment $comment Comment object.
 */
function lily_render_review_images_metabox( $comment ) {
	if ( ! $comment instanceof WP_Comment ) {
		return;
	}

	if ( 'product' !== get_post_type( $comment->comment_post_ID ) ) {
		echo '<p>' . esc_html__( 'This comment is not a product review.', 'lily' ) . '</p>';
		return;
	}

	$images = lily_get_review_images( $comment->comment_ID );

	if ( empty( $images ) ) {
		echo '<p>' . esc_html__( 'No photos attached to this review.', 'lily' ) . '</p>';
		return;
	}

	echo '<div style="display:flex;gap:10px;flex-wrap:wrap;">';
	foreach ( $images as $image ) {
		$edit_link = get_edit_post_link( $image['id'] );
		echo '<a href="' . esc_url( $edit_link ? $edit_link : $image['full'] ) . '" target="_blank" rel="noopener" style="display:inline-block;">';
		echo wp_get_attachment_image( $image['id'], 'thumbnail', false, array( 'style' => 'width:72px;height:72px;object-fit:cover;border-radius:8px;' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core image HTML.
		echo '</a>';
	}
	echo '</div>';
	echo '<p class="description">' . esc_html__( 'Photos uploaded by the customer with this review (maximum 3).', 'lily' ) . '</p>';
}

/* ── Frontend renderer ────────────────────────────────────────────────── */

/**
 * Render the complete editorial reviews section (called inside the
 * REVIEWS accordion panel). Uses approved reviews only.
 *
 * @param WC_Product $product Product object.
 */
function lily_render_product_reviews( $product ) {
	if ( ! $product instanceof WC_Product ) {
		return;
	}

	$product_id = lily_review_scope_product_id( $product );
	$stats      = lily_get_product_review_stats( $product_id );
	$count      = absint( $stats['count'] );
	$average    = (float) $stats['average'];
	$reviews    = $count > 0 ? lily_get_approved_product_reviews( $product_id ) : array();
	$form_open  = lily_product_reviews_open( $product_id );
	$submitted  = isset( $_GET['lily_review'] ) && 'submitted' === sanitize_key( wp_unslash( $_GET['lily_review'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- display-only flag.
	$commenter  = wp_get_current_commenter();

	?>
	<div class="lily-reviews" id="lily-reviews">
		<div class="lily-reviews__head">
			<div class="lily-reviews__titles">
				<h3 class="lily-reviews__title"><?php esc_html_e( 'Reviews', 'lily' ); ?></h3>
				<p class="lily-reviews__sub"><?php esc_html_e( 'What our customers are saying', 'lily' ); ?></p>
			</div>
			<?php if ( $form_open ) : ?>
				<button type="button" class="lily-reviews__write" data-lily-review-form-toggle aria-expanded="false" aria-controls="lily-review-form">
					<?php esc_html_e( 'Write a review', 'lily' ); ?>
				</button>
			<?php endif; ?>
		</div>

		<?php if ( $submitted ) : ?>
			<p class="lily-reviews__notice" role="status">
				<?php esc_html_e( 'Thank you! Your review is awaiting moderation and will appear once approved.', 'lily' ); ?>
			</p>
		<?php endif; ?>

		<?php if ( $count > 0 ) : ?>
			<div class="lily-reviews__summary">
				<span class="lily-reviews__avg"><?php echo esc_html( number_format_i18n( $average, 1 ) ); ?></span>
				<span class="lily-reviews__summary-stars">
					<?php lily_review_stars( $average ); ?>
					<span class="lily-reviews__count">
						<?php
						printf(
							/* translators: %d: number of approved reviews. */
							esc_html( _n( 'Based on %d review', 'Based on %d reviews', $count, 'lily' ) ),
							esc_html( number_format_i18n( $count ) )
						);
						?>
					</span>
				</span>
			</div>

			<ol class="lily-reviews__list">
				<?php foreach ( $reviews as $review ) : ?>
					<?php
					$rating      = absint( get_comment_meta( $review->comment_ID, 'rating', true ) );
					$images      = lily_get_review_images( $review->comment_ID );
					$author_name = get_comment_author( $review );
					$author_mail = get_comment_author_email( $review );
					$verified    = false;

					if ( function_exists( 'wc_customer_bought_product' ) && '' !== $author_mail ) {
						$verified = (bool) wc_customer_bought_product( $author_mail, (int) $review->user_id, $product_id );
					}
					?>
					<li class="lily-reviews__item">
						<div class="lily-reviews__meta">
							<span class="lily-reviews__author"><?php echo esc_html( $author_name ? $author_name : __( 'Anonymous', 'lily' ) ); ?></span>
							<time class="lily-reviews__date" datetime="<?php echo esc_attr( mysql2date( 'c', $review->comment_date, false ) ); ?>">
								<?php echo esc_html( mysql2date( get_option( 'date_format' ), $review->comment_date, true ) ); ?>
							</time>
						</div>

						<?php if ( $verified ) : ?>
							<p class="lily-reviews__verified">
								<span aria-hidden="true">✓</span>
								<?php esc_html_e( 'Verified Purchase', 'lily' ); ?>
							</p>
						<?php endif; ?>

						<?php if ( $rating >= 1 && $rating <= 5 ) : ?>
							<div class="lily-reviews__rating"><?php lily_review_stars( $rating ); ?></div>
						<?php endif; ?>

						<div class="lily-reviews__text">
							<?php echo wpautop( wp_kses_post( get_comment_text( $review->comment_ID ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- filtered comment HTML. ?>
						</div>

						<?php if ( ! empty( $images ) ) : ?>
							<ul class="lily-reviews__photos">
								<?php foreach ( $images as $index => $image ) : ?>
									<li>
										<button
											type="button"
											class="lily-reviews__photo"
											data-lily-review-zoom="<?php echo esc_url( $image['full'] ); ?>"
											aria-label="<?php echo esc_attr( sprintf( __( 'Open review photo %d', 'lily' ), $index + 1 ) ); ?>"
										>
											<img
												src="<?php echo esc_url( $image['thumb'] ); ?>"
												alt="<?php echo esc_attr( $image['alt'] ? $image['alt'] : sprintf( __( 'Customer review photo %d', 'lily' ), $index + 1 ) ); ?>"
												loading="lazy"
												width="80"
												height="80"
											>
										</button>
									</li>
								<?php endforeach; ?>
							</ul>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ol>
		<?php else : ?>
			<div class="lily-reviews__empty">
				<p class="lily-reviews__empty-title"><?php esc_html_e( 'No reviews yet', 'lily' ); ?></p>
				<p class="lily-reviews__empty-sub"><?php esc_html_e( 'Be the first to share your experience with this lens.', 'lily' ); ?></p>
				<?php if ( $form_open ) : ?>
					<button type="button" class="lily-reviews__write" data-lily-review-form-toggle aria-expanded="false" aria-controls="lily-review-form">
						<?php esc_html_e( 'Write a review', 'lily' ); ?>
					</button>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<?php if ( $form_open ) : ?>
			<div class="lily-review-formwrap" id="lily-review-form" hidden>
				<form
					action="<?php echo esc_url( site_url( '/wp-comments-post.php' ) ); ?>"
					method="post"
					class="lily-review-form"
					enctype="multipart/form-data"
				>
					<h4 class="lily-review-form__title"><?php esc_html_e( 'Share your experience', 'lily' ); ?></h4>

					<?php if ( lily_reviews_rating_enabled() ) : ?>
						<fieldset class="lily-review-form__row lily-review-form__rating">
							<legend><?php esc_html_e( 'Your rating', 'lily' ); ?></legend>
							<div class="lily-review-form__stars" role="radiogroup" aria-label="<?php esc_attr_e( 'Your rating', 'lily' ); ?>">
								<?php for ( $star = 5; $star >= 1; $star-- ) : ?>
									<label>
										<input type="radio" name="rating" value="<?php echo esc_attr( $star ); ?>" required>
										<span aria-hidden="true"><?php echo esc_html( str_repeat( '★', $star ) . str_repeat( '☆', 5 - $star ) ); ?></span>
										<span class="screen-reader-text">
											<?php
											printf(
												/* translators: %d: star count 1-5. */
												esc_html( _n( '%d star', '%d stars', $star, 'lily' ) ),
												esc_html( $star )
											);
											?>
										</span>
									</label>
								<?php endfor; ?>
							</div>
						</fieldset>
					<?php endif; ?>

					<p class="lily-review-form__row">
						<label for="lily-review-comment"><?php esc_html_e( 'Your review', 'lily' ); ?></label>
						<textarea id="lily-review-comment" name="comment" rows="5" maxlength="2000" required></textarea>
					</p>

					<?php if ( is_user_logged_in() ) : ?>
						<p class="lily-review-form__note">
							<?php
							printf(
								/* translators: %s: display name. */
								esc_html__( 'Posting as %s.', 'lily' ),
								esc_html( wp_get_current_user()->display_name )
							);
							?>
						</p>
					<?php else : ?>
						<p class="lily-review-form__row">
							<label for="lily-review-author"><?php esc_html_e( 'Name', 'lily' ); ?></label>
							<input type="text" id="lily-review-author" name="author" value="<?php echo esc_attr( $commenter['comment_author'] ); ?>" maxlength="60" autocomplete="name" <?php echo get_option( 'require_name_email' ) ? 'required' : ''; ?>>
						</p>
						<p class="lily-review-form__row">
							<label for="lily-review-email"><?php esc_html_e( 'Email', 'lily' ); ?></label>
							<input type="email" id="lily-review-email" name="email" value="<?php echo esc_attr( $commenter['comment_author_email'] ); ?>" maxlength="100" autocomplete="email" <?php echo get_option( 'require_name_email' ) ? 'required' : ''; ?>>
						</p>
					<?php endif; ?>

					<p class="lily-review-form__row">
						<label for="lily-review-images"><?php esc_html_e( 'Photos (optional)', 'lily' ); ?></label>
						<input
							type="file"
							id="lily-review-images"
							name="lily_review_images[]"
							accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
							multiple
						>
						<span class="lily-review-form__hint">
							<?php esc_html_e( 'Up to 3 images — JPG, PNG or WebP, max 5 MB each.', 'lily' ); ?>
						</span>
					</p>

					<?php comment_id_fields( $product_id ); ?>
					<?php wp_nonce_field( 'lily_review_images', 'lily_review_images_nonce' ); ?>

					<p class="lily-review-form__row lily-review-form__row--submit">
						<button type="submit" class="lily-reviews__submit"><?php esc_html_e( 'Submit review', 'lily' ); ?></button>
					</p>
					<p class="lily-review-form__note"><?php esc_html_e( 'Reviews are moderated and appear once approved.', 'lily' ); ?></p>
				</form>
			</div>
		<?php endif; ?>
	</div>

	<div class="lily-review-lightbox" data-lily-review-lightbox hidden>
		<div class="lily-review-lightbox__backdrop" data-lily-review-lightbox-close></div>
		<figure class="lily-review-lightbox__figure" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Review photo', 'lily' ); ?>">
			<button type="button" class="lily-review-lightbox__close" data-lily-review-lightbox-close aria-label="<?php esc_attr_e( 'Close photo', 'lily' ); ?>">×</button>
			<img src="" alt="<?php esc_attr_e( 'Review photo enlarged', 'lily' ); ?>" data-lily-review-lightbox-img>
		</figure>
	</div>
	<?php
}
