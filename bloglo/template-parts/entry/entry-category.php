<?php
/**
 * Template part for displaying entry category.
 *
 * @package     Bloglo
 * @author      Peregrine Themes
 * @since       1.0.0
 */

?>

<div class="post-category">

	<?php
	do_action( 'bloglo_before_post_category' );

	if ( is_singular() ) {
		bloglo_entry_meta_category( ' ', false );
	} else {
		if ( 'blog-horizontal' === bloglo_get_article_feed_layout() ) {
			bloglo_entry_meta_category( ' ', false );
		} else {
			bloglo_entry_meta_category( ', ', false );
		}
	}

	do_action( 'bloglo_after_post_category' );
	?>

</div>
