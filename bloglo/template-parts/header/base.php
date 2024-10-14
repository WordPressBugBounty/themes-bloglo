<?php
/**
 * The base template for displaying theme header area.
 *
 * @see https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package     Bloglo
 * @author      Peregrine Themes
 * @since       1.0.0
 */

?>
<?php do_action( 'bloglo_before_header' ); ?>
<div id="bloglo-header" <?php bloglo_header_classes(); ?>>
	<?php do_action( 'bloglo_header_content' ); ?>
</div><!-- END #bloglo-header -->
<?php do_action( 'bloglo_after_header' ); ?>
