<?php
/**
 * The template for displaying 404 pages (not found).
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package     Bloglo
 * @author      Peregrine Themes
 * @since       1.0.0
 */

?>

<?php get_header(); ?>

<div class="bloglo-container">

	<div id="primary" class="content-area">

		<?php do_action( 'bloglo_before_content' ); ?>

		<main id="content" class="site-content" role="main"<?php bloglo_schema_markup( 'main' ); ?>>

			<?php do_action( 'bloglo_content_404' ); ?>

		</main><!-- #content .site-content -->

		<?php do_action( 'bloglo_after_content' ); ?>

	</div><!-- #primary .content-area -->

</div><!-- END .bloglo-container -->

<?php
get_footer();
