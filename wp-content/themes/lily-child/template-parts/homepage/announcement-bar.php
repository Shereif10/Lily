<?php
/**
 * Announcement bar — informational text only, no links, no carousel.
 *
 * @package Lily
 */

if ( ! defined( 'ABSPATH' ) || ! lily_show_section( 'show_announcement_bar' ) ) {
	return;
}

$items = (array) lily_get_option( 'announcement_items', array() );

$items = array_filter(
	$items,
	static function ( $item ) {
		return is_array( $item ) && '' !== trim( (string) ( $item['text'] ?? '' ) );
	}
);

if ( empty( $items ) ) {
	return;
}
?>
<div class="lily-announcement" role="region" aria-label="<?php esc_attr_e( 'Announcement', 'lily' ); ?>">
	<p class="lily-announcement__text">
		<?php
		echo esc_html(
			trim(
				implode(
					'  ·  ',
					array_map(
						static function ( $item ) {
							$text = trim( (string) $item['text'] );

							return function_exists( 'lily_ml_value' )
								? (string) lily_ml_value( $text, $item['text_ar'] ?? '' )
								: $text;
						},
						array_values( $items )
					)
				)
			)
		);
		?>
	</p>
</div>
