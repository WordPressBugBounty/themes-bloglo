<?php
/**
 * Template for Single post
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Bloglo
 * @author Peregrine Themes
 * @since   1.0.0
 */

?>

<?php do_action( 'bloglo_before_article' ); ?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'bloglo-article' ); ?><?php bloglo_schema_markup( 'article' ); ?>>

	<?php
	if ( 'quote' === get_post_format() ) {
		get_template_part( 'template-parts/entry/format/media', 'quote' );
	}

	$bloglo_single_post_elements = bloglo_get_single_post_elements();

	if ( ! empty( $bloglo_single_post_elements ) ) {
		foreach ( $bloglo_single_post_elements as $bloglo_element ) {

			if ( 'content' === $bloglo_element ) {
				do_action( 'bloglo_before_single_content' );
				get_template_part( 'template-parts/entry/entry', $bloglo_element );
				do_action( 'bloglo_after_single_content' );
			} else {
				get_template_part( 'template-parts/entry/entry', $bloglo_element );
			}
		}
	}
	?>

</article><!-- #post-<?php the_ID(); ?> -->

<?php do_action( 'bloglo_after_article' ); ?>
