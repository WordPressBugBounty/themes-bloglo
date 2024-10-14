<?php
/**
 * Template part for displaying post in post listing.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package     Bloglo
 * @author      Peregrine Themes
 * @since       1.0.0
 */

?>

<?php do_action( 'bloglo_before_article' ); ?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'bloglo-article' ); ?><?php bloglo_schema_markup( 'article' ); ?>>

	<?php
	$bloglo_blog_entry_format = get_post_format();

	if ( 'quote' === $bloglo_blog_entry_format ) {
		get_template_part( 'template-parts/entry/format/media', $bloglo_blog_entry_format );
	} else {

		$bloglo_blog_entry_elements = bloglo_get_blog_entry_elements();

		if ( ! empty( $bloglo_blog_entry_elements ) ) {
			foreach ( $bloglo_blog_entry_elements as $bloglo_element ) {
				get_template_part( 'template-parts/entry/entry', $bloglo_element );
			}
		}
	}
	?>

</article><!-- #post-<?php the_ID(); ?> -->

<?php do_action( 'bloglo_after_article' ); ?>
