<?php
/**
 * The template for displaying theme top bar.
 *
 * @see https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Bloglo
 * @author Peregrine Themes
 * @since   1.0.0
 */

?>

<?php do_action( 'bloglo_before_topbar' ); ?>
<div id="bloglo-topbar" <?php bloglo_top_bar_classes(); ?>>
	<div class="bloglo-container">
		<div class="bloglo-flex-row">
			<div class="col-md flex-basis-auto start-sm"><?php do_action( 'bloglo_topbar_widgets', 'left' ); ?></div>
			<div class="col-md flex-basis-auto end-sm"><?php do_action( 'bloglo_topbar_widgets', 'right' ); ?></div>
		</div>
	</div>
</div><!-- END #bloglo-topbar -->
<?php do_action( 'bloglo_after_topbar' ); ?>
