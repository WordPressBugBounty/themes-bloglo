<?php
/**
 * Template part for displaying entry thumbnail (featured image).
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

// Get default post media.
$bloglo_media = bloglo_get_post_media( '' );

if ( ! $bloglo_media || post_password_required() ) {
	return;
}

$bloglo_post_format = get_post_format();

// Wrap with link for non-singular pages.
if ( 'link' === $bloglo_post_format || ! is_single( get_the_ID() ) ) {

	$bloglo_icon = '';

	if ( is_sticky() ) {
		$bloglo_icon = sprintf(
			'<span class="entry-media-icon is_sticky" title="%1$s" aria-hidden="true"><span class="entry-media-icon-wrapper">%2$s%3$s</span></span>',
			esc_attr__( 'Featured', 'bloglo' ),
			bloglo()->icons->get_svg(
				'pin',
				array(
					'class'       => 'top-icon',
					'aria-hidden' => 'true',
				)
			),
			bloglo()->icons->get_svg( 'pin', array( 'aria-hidden' => 'true' ) )
		);
	} elseif ( 'video' === $bloglo_post_format ) {
		$bloglo_icon = sprintf(
			'<span class="entry-media-icon" aria-hidden="true"><span class="entry-media-icon-wrapper">%1$s%2$s</span></span>',
			bloglo()->icons->get_svg(
				'play',
				array(
					'class'       => 'top-icon',
					'aria-hidden' => 'true',
				)
			),
			bloglo()->icons->get_svg( 'play', array( 'aria-hidden' => 'true' ) )
		);
	} elseif ( 'link' === $bloglo_post_format ) {
		$bloglo_icon = sprintf(
			'<span class="entry-media-icon" title="%1$s" aria-hidden="true"><span class="entry-media-icon-wrapper">%2$s%3$s</span></span>',
			esc_url( bloglo_entry_get_permalink() ),
			bloglo()->icons->get_svg(
				'external-link',
				array(
					'class'       => 'top-icon',
					'aria-hidden' => 'true',
				)
			),
			bloglo()->icons->get_svg( 'external-link', array( 'aria-hidden' => 'true' ) )
		);
	}

	$bloglo_icon = apply_filters( 'bloglo_post_format_media_icon', $bloglo_icon, $bloglo_post_format );

	$bloglo_media = sprintf(
		'<a href="%1$s" class="entry-image-link">%2$s%3$s</a>',
		esc_url( bloglo_entry_get_permalink() ),
		$bloglo_media,
		$bloglo_icon
	);
}

$bloglo_media = apply_filters( 'bloglo_post_thumbnail', $bloglo_media );

// Print the post thumbnail.
echo wp_kses(
	sprintf(
		'<div class="post-thumb entry-media thumbnail">%1$s</div>',
		$bloglo_media
	),
	bloglo_get_allowed_html_tags()
);
