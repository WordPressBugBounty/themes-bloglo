<?php
/**
 * Template part for displaying content of Bloglo Canvas [Fullwidth] page template.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Bloglo
 * @author Peregrine Themes
 * @since   1.0.0
 */

?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?><?php bloglo_schema_markup( 'article' ); ?>>
	<div class="entry-content bloglo-entry bloglo-fullwidth-entry">
		<?php
		do_action( 'bloglo_before_page_content' );

		the_content();

		do_action( 'bloglo_after_page_content' );
		?>
	</div><!-- END .entry-content -->
</article><!-- #post-<?php the_ID(); ?> -->
