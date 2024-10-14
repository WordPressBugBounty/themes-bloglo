<?php
/**
 * Bloglo Customizer helper functions.
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
 * Returns array of available widgets.
 *
 * @since 1.0.0
 * @return array, $widgets array of available widgets.
 */
function bloglo_get_customizer_widgets() {

	$widgets = array(
		'text'     => 'Bloglo_Customizer_Widget_Text',
		'nav'      => 'Bloglo_Customizer_Widget_Nav',
		'socials'  => 'Bloglo_Customizer_Widget_Socials',
		'search'   => 'Bloglo_Customizer_Widget_Search',
		'darkmode' => 'Bloglo_Customizer_Widget_Darkmode',
		'button'   => 'Bloglo_Customizer_Widget_Button',
	);

	return apply_filters( 'bloglo_customizer_widgets', $widgets );
}

/**
 * Get choices for "Hide on" customizer options.
 *
 * @since  1.0.0
 * @return array
 */
function bloglo_get_display_choices() {

	// Default options.
	$return = array(
		'home'       => array(
			'title' => esc_html__( 'Home Page', 'bloglo' ),
		),
		'posts_page' => array(
			'title' => esc_html__( 'Blog / Posts Page', 'bloglo' ),
		),
		'search'     => array(
			'title' => esc_html__( 'Search', 'bloglo' ),
		),
		'archive'    => array(
			'title' => esc_html__( 'Archive', 'bloglo' ),
			'desc'  => esc_html__( 'Dynamic pages such as categories, tags, custom taxonomies...', 'bloglo' ),
		),
		'post'       => array(
			'title' => esc_html__( 'Single Post', 'bloglo' ),
		),
		'page'       => array(
			'title' => esc_html__( 'Single Page', 'bloglo' ),
		),
	);

	// Get additionally registered post types.
	$post_types = get_post_types(
		array(
			'public'   => true,
			'_builtin' => false,
		),
		'objects'
	);

	if ( is_array( $post_types ) && ! empty( $post_types ) ) {
		foreach ( $post_types as $slug => $post_type ) {
			$return[ $slug ] = array(
				'title' => $post_type->label,
			);
		}
	}

	return apply_filters( 'bloglo_display_choices', $return );
}

/**
 * Get device choices for "Display on" customizer options.
 *
 * @since  1.0.0
 * @return array
 */
function bloglo_get_device_choices() {

	// Default options.
	$return = array(
		'desktop' => array(
			'title' => esc_html__( 'Hide On Desktop', 'bloglo' ),
		),
		'tablet'  => array(
			'title' => esc_html__( 'Hide On Tablet', 'bloglo' ),
		),
		'mobile'  => array(
			'title' => esc_html__( 'Hide On Mobile', 'bloglo' ),
		),
	);

	return apply_filters( 'bloglo_device_choices', $return );
}
