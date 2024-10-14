<?php
/**
 * The template for displaying theme sidebar.
 *
 * @package     Bloglo
 * @author      Peregrine Themes
 * @since       1.0.0
 */

if ( ! bloglo_is_sidebar_displayed() ) {
	return;
}

$bloglo_sidebar = bloglo_get_sidebar();
?>

<aside id="secondary" class="widget-area bloglo-sidebar-container"<?php bloglo_schema_markup( 'sidebar' ); ?> role="complementary">

	<div class="bloglo-sidebar-inner">
		<?php do_action( 'bloglo_before_sidebar' ); ?>

		<?php
		if ( is_active_sidebar( $bloglo_sidebar ) ) {

			dynamic_sidebar( $bloglo_sidebar );

		} elseif ( current_user_can( 'edit_theme_options' ) ) {

			$bloglo_sidebar_name = bloglo_get_sidebar_name_by_id( $bloglo_sidebar );
			?>
			<div class="bloglo-sidebar-widget bloglo-widget bloglo-no-widget">

				<div class='h4 widget-title'><?php echo esc_html( $bloglo_sidebar_name ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div> 

				<p class='no-widget-text'>
					<?php if ( is_customize_preview() ) { ?>
						<a href='#' class="bloglo-set-widget" data-sidebar-id="<?php echo esc_attr( $bloglo_sidebar ); ?>">
					<?php } else { ?>
						<a href='<?php echo esc_url( admin_url( 'widgets.php' ) ); ?>'>
					<?php } ?>
						<?php esc_html_e( 'Click here to assign a widget.', 'bloglo' ); ?>
					</a>
				</p>
			</div>
			<?php
		}
		?>

		<?php do_action( 'bloglo_after_sidebar' ); ?>
	</div>

</aside><!--#secondary .widget-area -->

<?php
