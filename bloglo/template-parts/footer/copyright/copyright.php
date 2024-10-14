<?php
/**
 * The template for displaying theme copyright bar.
 *
 * @package     Bloglo
 * @author      Peregrine Themes
 * @since       1.0.0
 */

?>

<?php do_action( 'bloglo_before_copyright' ); ?>
<div id="bloglo-copyright" <?php bloglo_copyright_classes(); ?>>
	<div class="bloglo-container">
		<div class="bloglo-flex-row">

			<div class="col-xs-12 center-xs col-md flex-basis-auto start-md"><?php do_action( 'bloglo_copyright_widgets', 'start' ); ?></div>
			<div class="col-xs-12 center-xs col-md flex-basis-auto end-md"><?php do_action( 'bloglo_copyright_widgets', 'end' ); ?></div>

		</div><!-- END .bloglo-flex-row -->
	</div>
</div><!-- END #bloglo-copyright -->
<?php do_action( 'bloglo_after_copyright' ); ?>
