<?php
/**
 * Template part for displaying page layout in page.php
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package     Bloglo
 * @author      Peregrine Themes
 * @since       1.0.0
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?><?php bloglo_schema_markup( 'article' ); ?>>

<?php
if ( bloglo_show_post_thumbnail() ) {
	get_template_part( 'template-parts/entry/format/media', 'page' );
}
?>

<div class="entry-content bloglo-entry">
	<?php
	do_action( 'bloglo_before_page_content' );

	the_content();

	do_action( 'bloglo_after_page_content' );
	?>
</div><!-- END .entry-content -->

<?php bloglo_link_pages(); ?>

</article><!-- #post-<?php the_ID(); ?> -->
