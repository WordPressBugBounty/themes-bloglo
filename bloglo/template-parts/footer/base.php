<?php
/**
 * The template for displaying theme footer.
 *
 * @package     Bloglo
 * @author      Peregrine Themes
 * @since       1.0.0
 */

?>

<?php do_action( 'bloglo_before_footer' ); ?>
<div id="bloglo-footer" <?php bloglo_footer_classes(); ?>>
	<div class="bloglo-container">
		<div class="bloglo-flex-row" id="bloglo-footer-widgets">

			<?php bloglo_footer_widgets(); ?>

		</div><!-- END .bloglo-flex-row -->
	</div><!-- END .bloglo-container -->
</div><!-- END #bloglo-footer -->
<?php do_action( 'bloglo_after_footer' ); ?>
