<?php
/**
 * The template for displaying header layout 4.
 *
 * @package     Bloglo
 * @author      Peregrine Themes
 * @since       1.0.0
 */

?>

<div class="bloglo-header-container">
	<div class="bloglo-logo-container">
		<div class="bloglo-container">

			<?php
			bloglo_header_logo_template();
			?>

			<?php
			$header_ads_banner_custom_image  = bloglo()->options->get( 'bloglo_header_ads_banner' );
			$header_ads_banner_custom_url    = bloglo()->options->get( 'bloglo_header_ads_banner_url' );
			$header_ads_banner_custom_target = bloglo()->options->get( 'bloglo_header_ads_banner_url_target' );

			$adsbanner = $header_ads_banner_custom_url ? sprintf( '<a href="%1$s" target="' . esc_attr( $header_ads_banner_custom_target ) . '"><img src="%2$s"/></a>', esc_url_raw( $header_ads_banner_custom_url ), esc_url_raw( $header_ads_banner_custom_image ) ) : sprintf( '<img src="%1$s"/>', esc_url_raw( $header_ads_banner_custom_image ) );

			if ( $header_ads_banner_custom_image ) {
				?>

			<!-- Header Image -->
			<div class="bloglo-header-widgets bloglo-header-widgets-two bloglo-header-element">
				<div class="bloglo-header-widget__image bloglo-header-widget bloglo-all">
					<div class="bloglo-widget-wrapper">
						<div class="bloglo-banner-imgads">
							<?php echo wp_kses_post( $adsbanner ); ?>
						</div><!-- .bloglo-banner-imgads -->
					</div>
				</div>
			</div>
			<?php } ?>

			<?php
			do_action( 'bloglo_header_widget_location', array( 'left', 'right' ) );
			?>

			<span class="bloglo-header-element bloglo-mobile-nav">
				<?php bloglo_hamburger( bloglo_option( 'main_nav_mobile_label' ), 'bloglo-primary-nav' ); ?>
				<?php bloglo_main_navigation_template(); ?>
			</span>

		</div><!-- END .bloglo-container -->
	</div><!-- END .bloglo-logo-container -->

	<div class="bloglo-nav-container">
		<div class="bloglo-container">

			<?php
			do_action( 'bloglo_header_widget_location', 'left' );
			bloglo_main_navigation_template();
			do_action( 'bloglo_header_widget_location', 'right' );
			?>

		</div><!-- END .bloglo-container -->
	</div><!-- END .bloglo-nav-container -->
</div><!-- END .bloglo-header-container -->
