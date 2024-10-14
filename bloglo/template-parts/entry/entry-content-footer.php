<?php
/**
 * Template part for displaying entry tags.
 *
 * @package     Bloglo
 * @author      Peregrine Themes
 * @since       1.0.0
 */

$bloglo_entry_elements    = bloglo_option( 'single_post_elements' );
$bloglo_entry_footer_tags = isset( $bloglo_entry_elements['tags'] ) && $bloglo_entry_elements['tags'] && has_tag();
$bloglo_entry_footer_date = isset( $bloglo_entry_elements['last-updated'] ) && $bloglo_entry_elements['last-updated'] && get_the_time( 'U' ) !== get_the_modified_time( 'U' );

$bloglo_entry_footer_tags = apply_filters( 'bloglo_display_entry_footer_tags', $bloglo_entry_footer_tags );
$bloglo_entry_footer_date = apply_filters( 'bloglo_display_entry_footer_date', $bloglo_entry_footer_date );

// Nothing is enabled, don't display the div.
if ( ! $bloglo_entry_footer_tags && ! $bloglo_entry_footer_date ) {
	return;
}
?>

<?php do_action( 'bloglo_before_entry_footer' ); ?>

<div class="entry-footer">

	<?php
	// Post Tags.
	if ( $bloglo_entry_footer_tags ) {
		bloglo_entry_meta_tag(
			'<div class="post-tags"><span class="cat-links">',
			'',
			'</span></div>',
			0,
			false
		);
	}

	// Last Updated Date.
	if ( $bloglo_entry_footer_date ) {

		$bloglo_before = '<span class="last-updated bloglo-iflex-center">';

		if ( true === bloglo_option( 'single_entry_meta_icons' ) ) {
			$bloglo_before .= bloglo()->icons->get_svg( 'edit-3' );
		}

		bloglo_entry_meta_date(
			array(
				'show_published' => false,
				'show_modified'  => true,
				'before'         => $bloglo_before,
				'after'          => '</span>',
			)
		);
	}
	?>

</div>

<?php do_action( 'bloglo_after_entry_footer' ); ?>
