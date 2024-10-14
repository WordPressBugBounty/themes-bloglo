<?php
/**
 * Template part for displaying entry footer.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package     Bloglo
 * @author      Peregrine Themes
 * @since       1.0.0
 */

/**
 * Do not allow direct script access.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<?php do_action( 'bloglo_before_entry_footer' ); ?>
<footer class="entry-footer">
	<?php

	// Allow text to be filtered.
	$bloglo_read_more_text = apply_filters( 'bloglo_entry_read_more_text', __( 'Read More', 'bloglo' ) );

	?>
	<a href="<?php echo esc_url( bloglo_entry_get_permalink() ); ?>" class="bloglo-btn btn-text-1"><span><?php echo esc_html( $bloglo_read_more_text ); ?></span></a>
</footer>
<?php do_action( 'bloglo_after_entry_footer' ); ?>
