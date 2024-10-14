<?php
/**
 * The template for displaying Featured Links.
 *
 * @package     Bloglo
 * @author      Peregrine Themes
 * @since       1.0.0
 */


$bloglo_featured_links_items_html = '';

foreach ( $args['features'] as $key => $feature ) :

	// Post items HTML markup.
	ob_start();
	?>
	
	<div id="bloghsah-featured-item-<?php echo esc_attr($key); ?>" class="col-md-4 col-sm-12 col-xs-12">
		<div class="bloglo-post-item style-1 center">
			<div class="bloglo-post-thumb">
				<span class="bloglo-post-animation"><span></span></span>
				<div class="inner bloghsah-featured-item-image">
					<?php if ( ! empty($feature['image']['id']) ) : echo wp_get_attachment_image($feature['image']['id'], 'large'); endif; ?>
				</div>
			</div><!-- END .bloglo-post-thumb-->
			<div class="bloglo-post-content">

				<?php if ( ! empty($feature['btn_text']) ) : 
					printf( '<a href="%1$s" class="bloglo-btn btn-small btn-white" title="%2$s" target="%3$s">%4$s</a>', esc_url_raw( $feature['btn_url'] ), esc_attr( $feature['btn_text'] ), esc_attr( $feature['btn_target'] ), esc_html( $feature['btn_text'] ) );
				endif; ?>
			</div><!-- END .bloglo-post-content -->
		</div><!-- END .bloglo-post-item -->
	</div>
	<?php
	$bloglo_featured_links_items_html .= ob_get_clean();
endforeach;

// Restore original Post Data.
wp_reset_postdata();

// Container.
$bloglo_featured_links_container = bloglo_option( 'featured_links_container' );
$bloglo_featured_links_container = 'full-width' === $bloglo_featured_links_container ? 'bloglo-container bloglo-container__wide' : 'bloglo-container';

// Title.
$bloglo_featured_links_title     = bloglo_option( 'featured_links_title' );

?>

<div class="bloglo-featured-slider featured-one slider-overlay-1 mt-5">
	<div class="bloglo-featured-container <?php echo esc_attr( $bloglo_featured_links_container ); ?>">
		<div class="bloglo-flex-row g-0">
			<div class="col-xs-12">
				<div class="bloglo-featured-items">
					<?php if ( $bloglo_featured_links_title ) : ?>
					<div class="h4 widget-title">
						<span><?php echo esc_html( $bloglo_featured_links_title ); ?></span>
					</div>
					<?php endif; ?>
					<div class="bloglo-flex-row gy-4">
						<?php echo wp_kses_post( $bloglo_featured_links_items_html ); ?>
					</div>
				</div>
			</div>
		</div><!-- END .bloglo-card-items -->
	</div>
</div><!-- END .bloglo-featured-slider -->
