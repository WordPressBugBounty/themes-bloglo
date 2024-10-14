<?php
/**
 * Template part for displaying media of the entry.
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

$bloglo_post_format = get_post_format();

if ( is_single() ) {
	$bloglo_post_format = '';
}

do_action( 'bloglo_before_entry_thumbnail' );

get_template_part( 'template-parts/entry/format/media', $bloglo_post_format );

do_action( 'bloglo_after_entry_thumbnail' );
