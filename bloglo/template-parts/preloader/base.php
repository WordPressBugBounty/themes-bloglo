<?php
/**
 * The template for displaying page preloader.
 *
 * @see https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Bloglo
 * @author Peregrine Themes
 * @since   1.0.0
 */

?>

<div id="bloglo-preloader"<?php bloglo_preloader_classes(); ?>>
	<?php get_template_part( 'template-parts/preloader/preloader', bloglo_option( 'preloader_style' ) ); ?>
</div><!-- END #bloglo-preloader -->
