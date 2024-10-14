<?php
/**
 * The template for displaying Hero Hover Slider.
 *
 * @package     Bloglo
 * @author      Peregrine Themes
 * @since       1.0.0
 */

$hero_hover_slider_post_number_count = bloglo_option( 'hero_hover_slider_post_number' ) >= 2 ? 2 : 1;

// Setup Hero posts.
$bloglo_args = array(
	'post_type'           => 'post',
	'post_status'         => 'publish',
	'posts_per_page'      => $hero_hover_slider_post_number_count, // phpcs:ignore WordPress.WP.PostsPerPage.posts_per_page_posts_per_page
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

$bloglo_hero_categories = bloglo_option( 'hero_hover_slider_category' );

if ( ! empty( $bloglo_hero_categories ) ) {
	$bloglo_args['category_name'] = implode( ', ', $bloglo_hero_categories );
}

$bloglo_args = apply_filters( 'bloglo_hero_hover_slider_query_args', $bloglo_args );

$bloglo_posts = new WP_Query( $bloglo_args );

// No posts found.
if ( ! $bloglo_posts->have_posts() ) {
	return;
}

$bloglo_hero_bgs_html   = '';
$bloglo_hero_items_html = '';

$bloglo_hero_elements = (array) bloglo_option( 'hero_hover_slider_elements' );
$bloglo_hero_readmore = isset( $bloglo_hero_elements['read_more'] ) && $bloglo_hero_elements['read_more'] ? ' bloglo-hero-readmore' : '';

while ( $bloglo_posts->have_posts() ) :
	$bloglo_posts->the_post();

	// Background images HTML markup.
	$bloglo_hero_bgs_html .= '<div class="hover-slide-bg" data-background="' . get_the_post_thumbnail_url( get_the_ID(), 'full' ) . '"></div>';

	// Post items HTML markup.
	ob_start();
	?>
	<div class="col-xs-<?php echo esc_attr( 12 / $bloglo_args['posts_per_page'] ); ?> hover-slider-item-wrapper<?php echo esc_attr( $bloglo_hero_readmore ); ?>">
		<div class="hover-slide-item">
			<div class="slide-inner">

				<?php if ( isset( $bloglo_hero_elements['category'] ) && $bloglo_hero_elements['category'] ) { ?>
					<div class="post-category">
						<?php bloglo_entry_meta_category( ' ', false ); ?>
					</div>
				<?php } ?>

				<?php if ( get_the_title() ) { ?>
					<h3><a href="<?php echo esc_url( bloglo_entry_get_permalink() ); ?>"><?php the_title(); ?></a></h3>
				<?php } ?>

				<?php if ( isset( $bloglo_hero_elements['meta'] ) && $bloglo_hero_elements['meta'] ) { ?>
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

				<?php if ( $bloglo_hero_readmore ) { ?>
					<a href="<?php echo esc_url( bloglo_entry_get_permalink() ); ?>" class="read-more bloglo-btn btn-small btn-outline btn-uppercase" role="button"><span><?php esc_html_e( 'Continue Reading', 'bloglo' ); ?></span></a>
				<?php } ?>

			</div><!-- END .slide-inner -->
		</div><!-- END .hover-slide-item -->
	</div><!-- END .hover-slider-item-wrapper -->
	<?php
	$bloglo_hero_items_html .= ob_get_clean();
endwhile;

// Restore original Post Data.
wp_reset_postdata();

// Hero container.
$bloglo_hero_container = bloglo_option( 'hero_hover_slider_container' );
$bloglo_hero_container = 'full-width' === $bloglo_hero_container ? 'bloglo-container bloglo-container__wide' : 'bloglo-container';

// Hero overlay.
$bloglo_hero_overlay = absint( bloglo_option( 'hero_hover_slider_overlay' ) );
?>

<div class="bloglo-hover-slider slider-overlay-<?php echo esc_attr( $bloglo_hero_overlay ); ?>">
	<div class="hover-slider-backgrounds">

		<?php echo wp_kses_post( $bloglo_hero_bgs_html ); ?>

	</div><!-- END .hover-slider-items -->

	<div class="bloglo-hero-container <?php echo esc_attr( $bloglo_hero_container ); ?>">
		<div class="bloglo-flex-row hover-slider-items">

			<?php echo wp_kses_post( $bloglo_hero_items_html ); ?>

		</div><!-- END .hover-slider-items -->
	</div>

	<div class="bloglo-spinner visible">
		<div></div>
		<div></div>
	</div>
</div><!-- END .bloglo-hover-slider -->
