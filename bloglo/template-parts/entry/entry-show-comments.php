<?php
/**
 * Template part for displaying ”Show Comments” button.
 *
 * @package     Bloglo
 * @author      Peregrine Themes
 * @since       1.0.0
 */

// Do not show if the post is password protected.
if ( post_password_required() ) {
	return;
}

$bloglo_comment_count = get_comments_number();
$bloglo_comment_title = esc_html__( 'Leave a Comment', 'bloglo' );

if ( $bloglo_comment_count > 0 ) {
	/* translators: %s is comment count */
	$bloglo_comment_title = esc_html( sprintf( _n( 'Show %s Comment', 'Show %s Comments', $bloglo_comment_count, 'bloglo' ), $bloglo_comment_count ) );
}

?>
<a href="#" id="bloglo-comments-toggle" class="bloglo-btn btn-large btn-fw btn-left-icon">
	<?php echo bloglo()->icons->get_svg( 'chat' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	<span><?php echo $bloglo_comment_title; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
</a>
