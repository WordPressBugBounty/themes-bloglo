<?php
/**
 * The template for displaying Hero Hover Slider.
 *
 * @package     Bloglo
 * @author      Peregrine Themes
 * @since       1.0.0
 */

$bloglo_ticker_categories = ! empty( $bloglo_ticker_categories ) ? implode( ', ', $bloglo_ticker_categories ) : '';

// Setup Hero posts.
$bloglo_args = array(
	'post_type'           => 'post',
	'post_status'         => 'publish',
	'posts_per_page'      => bloglo_option( 'ticker_post_number' ), // phpcs:ignore WordPress.WP.PostsPerPage.posts_per_page_posts_per_page
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

$bloglo_ticker_categories = bloglo_option( 'ticker_category' );

if ( ! empty( $bloglo_ticker_categories ) ) {
	$bloglo_args['category_name'] = implode( ', ', $bloglo_ticker_categories );
}

$bloglo_args = apply_filters( 'bloglo_ticker_query_args', $bloglo_args );

$bloglo_posts = new WP_Query( $bloglo_args );

// No posts found.
if ( ! $bloglo_posts->have_posts() ) {
	return;
}

$bloglo_ticker_items_html = '';

$bloglo_ticker_elements = (array) bloglo_option( 'ticker_elements' );

while ( $bloglo_posts->have_posts() ) :
	$bloglo_posts->the_post();

	// Post items HTML markup.
	ob_start();
	?>
	<div class="ticker-slide-item">

		<?php if ( get_the_post_thumbnail_url( get_the_ID(), 'thumbnail' ) ) { ?>
		<div class="ticker-slider-backgrounds">
			<a href="<?php echo esc_url( bloglo_entry_get_permalink() ); ?>">
				<?php echo get_the_post_thumbnail( get_the_ID(), 'thumbnail' ); ?>
			</a>
		</div><!-- END .ticker-slider-items -->
		<?php } ?>

		<div class="slide-inner">				

			<?php if ( get_the_title() ) { ?>
				<h6><a href="<?php echo esc_url( bloglo_entry_get_permalink() ); ?>"><?php the_title(); ?></a></h6>
			<?php } ?>

			<?php if ( isset( $bloglo_ticker_elements['meta'] ) && $bloglo_ticker_elements['meta'] ) { ?>
				<div class="entry-meta">
					<div class="entry-meta-elements">
						<?php
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
	</div><!-- END .ticker-slide-item -->
	<?php
	$bloglo_ticker_items_html .= ob_get_clean();
endwhile;

// Restore original Post Data.
wp_reset_postdata();

// Hero container.
$bloglo_ticker_container = bloglo_option( 'ticker_container' );
$bloglo_ticker_container = 'full-width' === $bloglo_ticker_container ? 'bloglo-container bloglo-container__wide' : 'bloglo-container';

$bloglo_ticker_title = bloglo()->options->get( 'bloglo_ticker_title' );

?>

<div class="bloglo-ticker-slider">

	<div class="bloglo-ticker-container <?php echo esc_attr( $bloglo_ticker_container ); ?>">
		<div class="bloglo-flex-row">
			<div class="col-xs-12">
				<div class="ticker-slider-items">
					<?php if ( $bloglo_ticker_title ) : ?>
					<div class="ticker-title">
						<span class="icon">
							<i class="fas fa-bolt"></i>
						</span>
						<span class="title"><?php echo esc_html( $bloglo_ticker_title ); ?></span>
					</div>
					<?php endif; ?>
					<?php
						$ticker_direction = 'left';
						$ticker_dir       = 'ltr';
					if ( is_rtl() ) {
						$ticker_direction = 'right';
						$ticker_dir       = 'ltr';
					}
					?>
					<div class="ticker-slider-box">
						<div class="ticker-slider-wrap" direction="<?php echo esc_attr( $ticker_direction ); ?>" dir="<?php echo esc_attr( $ticker_dir ); ?>">
							<?php echo wp_kses_post( $bloglo_ticker_items_html ); ?>
						</div>
					</div>
					<div class="ticker-slider-controls">
						<button class="ticker-slider-pause"><i class="fas fa-pause"></i></button>
					</div>
				</div>
			</div>
		</div><!-- END .ticker-slider-items -->
	</div>
</div><!-- END .bloglo-ticker-slider -->
