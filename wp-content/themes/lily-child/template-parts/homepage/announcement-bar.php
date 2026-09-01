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
							return trim( (string) $item['text'] );
						},
						array_values( $items )
					)
				)
			)
		);
		?>
	</p>
</div>
