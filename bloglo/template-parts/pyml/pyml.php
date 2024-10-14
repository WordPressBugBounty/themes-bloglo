<?php
/**
 * The template for displaying Hero Hover Slider.
 *
 * @package     Bloglo
 * @author      Peregrine Themes
 * @since       1.0.0
 */

$bloglo_pyml_categories = ! empty( $bloglo_pyml_categories ) ? implode( ', ', $bloglo_pyml_categories ) : '';

// Setup Hero posts.
$bloglo_args = array(
	'post_type'           => 'post',
	'post_status'         => 'publish',
	'posts_per_page'      => bloglo_option( 'pyml_post_number' ), // phpcs:ignore WordPress.WP.PostsPerPage.posts_per_page_posts_per_page
	'ignore_sticky_posts' => true,
	'tax_query'           => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
		array(
			'taxonomy' => 'post_format',
			'field'    => 'slug',
			'terms'    => array( 'post-format-quote' ),
			'operator' => 'NOT IN',
		),
	),
);

$bloglo_pyml_categories = bloglo_option( 'pyml_category' );

if ( ! empty( $bloglo_pyml_categories ) ) {
	$bloglo_args['category_name'] = implode( ', ', $bloglo_pyml_categories );
}

$bloglo_args = apply_filters( 'bloglo_pyml_query_args', $bloglo_args );

$bloglo_posts = new WP_Query( $bloglo_args );

// No posts found.
if ( ! $bloglo_posts->have_posts() ) {
	return;
}

// $bloglo_pyml_bgs_html   = '';
$bloglo_pyml_items_html = '';

$bloglo_pyml_elements = (array) bloglo_option( 'pyml_elements' );

$bloglo_pyml_posts_carousel = bloglo_option( 'pyml_posts_carousel' );

$bloglo_posts_per_page = 'col-md-' . ceil( esc_attr( 12 / $bloglo_args['posts_per_page'] ) ) . ' col-sm-6 col-xs-12';

while ( $bloglo_posts->have_posts() ) :
	$bloglo_posts->the_post();

	// Post items HTML markup.
	ob_start();
	?>
	<div class="<?php echo $bloglo_posts_per_page; ?>">
		<div class="pyml-slide-item">

			<div class="pyml-slider-backgrounds">
				<a href="<?php echo esc_url( bloglo_entry_get_permalink() ); ?>">
					<div class="pyml-slide-bg"> <?php echo get_the_post_thumbnail( get_the_ID(), 'large' ); ?> </div>
				</a>
				<?php if ( isset( $bloglo_pyml_elements['category'] ) && $bloglo_pyml_elements['category'] ) { ?>
					<div class="post-category">
						<?php bloglo_entry_meta_category( ' ', false, apply_filters('bloglo_pyml_category_limit', 3) ); ?>
					</div>
				<?php } ?>
			</div><!-- END .pyml-slider-items -->

			<div class="slide-inner">				

				<?php if ( get_the_title() ) { ?>
					<h6><a href="<?php echo esc_url( bloglo_entry_get_permalink() ); ?>"><?php the_title(); ?></a></h6>
				<?php } ?>

				<?php if ( isset( $bloglo_pyml_elements['meta'] ) && $bloglo_pyml_elements['meta'] ) { ?>
					<div class="entry-meta">
						<div class="entry-meta-elements">
							<?php
							bloglo_entry_meta_author();

							bloglo_entry_meta_date(
								array(
									'show_modified'   => false,
									'published_label' => '',
								)
							);
							?>
						</div>
					</div><!-- END .entry-meta -->
				<?php } ?>

			</div><!-- END .slide-inner -->
		</div><!-- END .pyml-slide-item -->
	</div><!-- END .pyml-slider-item-wrapper -->
	<?php
	$bloglo_pyml_items_html .= ob_get_clean();
endwhile;

// Restore original Post Data.
wp_reset_postdata();

// Hero container.
$bloglo_pyml_container = bloglo_option( 'pyml_container' );
$bloglo_pyml_container = 'full-width' === $bloglo_pyml_container ? 'bloglo-container bloglo-container__wide' : 'bloglo-container';

$bloglo_pyml_title = bloglo()->options->get( 'bloglo_pyml_title' );

?>

<div class="bloglo-pyml-slider">

	<div class="bloglo-pyml-container <?php echo esc_attr( $bloglo_pyml_container ); ?>">
		<div class="bloglo-flex-row">
			<div class="col-xs-12">
				<div class="pyml-slider-items">
					<div class="h4 widget-title">
						<?php if ( $bloglo_pyml_title ) : ?>
						<span><?php echo esc_html( $bloglo_pyml_title ); ?></span>
						<?php endif; ?>
					</div>

					<div class="bloglo-flex-row gy-4">
						<?php echo wp_kses_post( $bloglo_pyml_items_html ); ?>
					</div>
				</div>
			</div>
		</div><!-- END .pyml-slider-items -->
	</div>
</div><!-- END .bloglo-pyml-slider -->
