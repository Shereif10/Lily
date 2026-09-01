<?php
/**
 * Lily template helpers.
 *
 * @package Lily
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render a section heading block.
 *
 * @param string $heading     Heading text.
 * @param string $description Optional description.
 */
function lily_section_heading( $heading, $description = '' ) {
	if ( empty( $heading ) && empty( $description ) ) {
		return;
	}

	echo '<div class="lily-section-heading">';

	if ( ! empty( $heading ) ) {
		printf( '<h2>%s</h2>', esc_html( $heading ) );
	}

	if ( ! empty( $description ) ) {
		printf( '<p>%s</p>', esc_html( $description ) );
	}

	echo '</div>';
}

