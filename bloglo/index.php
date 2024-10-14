<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme and one of the
 * two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * For example, it puts together the home page when no home.php file exists.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package     Bloglo
 * @author      Peregrine Themes
 * @since       1.0.0
 */

?>

<?php get_header(); ?>

<?php do_action( 'bloglo_before_container' ); ?>

<div class="bloglo-container">

	<div id="primary" class="content-area">

		<?php do_action( 'bloglo_before_content' ); ?>

		<main id="content" class="site-content" role="main"<?php bloglo_schema_markup( 'main' ); ?>>

			<?php do_action( 'bloglo_content' ); ?>

		</main><!-- #content .site-content -->

		<?php do_action( 'bloglo_after_content' ); ?>

	</div><!-- #primary .content-area -->

	<?php do_action( 'bloglo_sidebar' ); ?>
	
</div><!-- END .bloglo-container -->

<?php do_action( 'bloglo_after_container' ); ?>

<?php
get_footer();
