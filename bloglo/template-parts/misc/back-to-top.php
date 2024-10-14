<?php
/**
 * The template for displaying scroll to top button.
 *
 * @package     Bloglo
 * @author      Peregrine Themes
 * @since       1.0.0
 */

?>

<a href="#" id="bloglo-scroll-top" class="bloglo-smooth-scroll" title="<?php esc_attr_e( 'Scroll to Top', 'bloglo' ); ?>" <?php bloglo_scroll_top_classes(); ?>>
	<span class="bloglo-scroll-icon" aria-hidden="true">
		<?php echo bloglo()->icons->get_svg( 'chevron-up', array( 'class' => 'top-icon' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<?php echo bloglo()->icons->get_svg( 'chevron-up' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</span>
	<span class="screen-reader-text"><?php esc_html_e( 'Scroll to Top', 'bloglo' ); ?></span>
</a><!-- END #bloglo-scroll-to-top -->
