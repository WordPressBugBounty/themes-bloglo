<?php
/**
 * Template part for displaying entry meta info.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package     Bloglo
 * @author      Peregrine Themes
 * @since       1.0.0
 */

/**
 * Do not allow direct script access.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Only show meta tags for posts.
 */
if ( ! in_array( get_post_type(), (array) apply_filters( 'bloglo_entry_meta_post_type', array( 'post' ) ), true ) ) {
	return;
}

do_action( 'bloglo_before_entry_meta' );

// Get meta items to be displayed.
$bloglo_meta_elements = bloglo_get_entry_meta_elements();

if ( ! empty( $bloglo_meta_elements ) ) {

	echo '<div class="entry-meta"><div class="entry-meta-elements">';

	do_action( 'bloglo_before_entry_meta_elements' );

	// Loop through meta items.
	foreach ( $bloglo_meta_elements as $bloglo_meta_item ) {

		// Call a template tag function.
		if ( function_exists( 'bloglo_entry_meta_' . $bloglo_meta_item ) ) {
			call_user_func( 'bloglo_entry_meta_' . $bloglo_meta_item );
		}
	}

	// Add edit post link.
	$bloglo_edit_icon = bloglo()->icons->get_meta_icon( 'edit', bloglo()->icons->get_svg( 'edit-3', array( 'aria-hidden' => 'true' ) ) );

	bloglo_edit_post_link(
		sprintf(
			wp_kses(
				/* translators: %s: Name of current post. Only visible to screen readers */
				$bloglo_edit_icon . __( 'Edit <span class="screen-reader-text">%s</span>', 'bloglo' ),
				bloglo_get_allowed_html_tags()
			),
			get_the_title()
		),
		'<span class="edit-link">',
		'</span>'
	);

	do_action( 'bloglo_after_entry_meta_elements' );

	echo '</div></div>';
}

do_action( 'bloglo_after_entry_meta' );
