<?php
/**
 * Template Name: Bloglo Fullwidth
 *
 * 100% wide page template without vertical spacing.
 *
 * @package     Bloglo
 * @author      Peregrine Themes
 * @since       1.0.0
 */

get_header();
do_action( 'bloglo_before_singular_container' );
if ( have_posts() ) :
	while ( have_posts() ) :
		the_post();

		get_template_part( 'template-parts/content/content', 'bloglo-fullwidth' );
	endwhile;
endif;
do_action( 'bloglo_after_singular_container' );
get_footer();
