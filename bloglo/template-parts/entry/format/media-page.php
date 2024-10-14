<?php
/**
 * Template part for displaying page featured image.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Bloglo
 * @author Peregrine Themes
 * @since   1.0.0
 */

/**
 * Do not allow direct script access.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Get default post media.
$bloglo_media = bloglo_get_post_media( '' );

if ( ! $bloglo_media || post_password_required() ) {
	return;
}

$bloglo_media = apply_filters( 'bloglo_post_thumbnail', $bloglo_media, get_the_ID() );

$bloglo_classes = array( 'post-thumb', 'entry-media', 'thumbnail' );

$bloglo_classes = apply_filters( 'bloglo_post_thumbnail_wrapper_classes', $bloglo_classes, get_the_ID() );
$bloglo_classes = trim( implode( ' ', array_unique( $bloglo_classes ) ) );

// Print the post thumbnail.
echo wp_kses_post(
	sprintf(
		'<div class="%2$s">%1$s</div>',
		$bloglo_media,
		esc_attr( $bloglo_classes )
	)
);
