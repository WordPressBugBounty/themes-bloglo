<?php

/**
 * Template parts.
 *
 * @package     Bloglo
 * @author      Peregrine Themes
 * @since       1.0.0
 */

/**
 * Do not allow direct script access.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Adds the meta tag to the site header.
 *
 * @since 1.0.0
 */
function bloglo_meta_viewport() {
	echo '<meta name="viewport" content="width=device-width, initial-scale=1">';
}
add_action( 'wp_head', 'bloglo_meta_viewport', 1 );

/**
 * Add a pingback url auto-discovery header for singularly identifiable articles.
 *
 * @since 1.0.0
 */
function bloglo_pingback_header() {
	if ( is_singular() && pings_open() ) {
		printf( '<link rel="pingback" href="%s">' . "\n", esc_url( get_bloginfo( 'pingback_url' ) ) );
	}
}
add_action( 'wp_head', 'bloglo_pingback_header' );

/**
 * Adds the meta tag for website accent color.
 *
 * @since 1.0.0
 */
function bloglo_meta_theme_color() {

	$color = bloglo_option( 'accent_color' );

	if ( $color ) {
		printf( '<meta name="theme-color" content="%s">', esc_attr( $color ) );
	}
}
add_action( 'wp_head', 'bloglo_meta_theme_color' );

/**
 * Outputs the theme top bar area.
 *
 * @since 1.0.0
 */
function bloglo_topbar_output() {

	if ( ! bloglo_is_top_bar_displayed() ) {
		return;
	}

	get_template_part( 'template-parts/topbar/topbar' );
}
add_action( 'bloglo_header', 'bloglo_topbar_output', 10 );

/**
 * Outputs the top bar widgets.
 *
 * @since 1.0.0
 * @param string $location Widget location in top bar.
 */
function bloglo_topbar_widgets_output( $location ) {

	do_action( 'bloglo_top_bar_widgets_before_' . $location );

	$bloglo_top_bar_widgets = bloglo_option( 'top_bar_widgets' );

	if ( is_array( $bloglo_top_bar_widgets ) && ! empty( $bloglo_top_bar_widgets ) ) {
		foreach ( $bloglo_top_bar_widgets as $widget ) {

			if ( ! isset( $widget['values'] ) ) {
				continue;
			}

			if ( $location !== $widget['values']['location'] ) {
				continue;
			}

			if ( function_exists( 'bloglo_top_bar_widget_' . $widget['type'] ) ) {

				$classes   = array();
				$classes[] = 'bloglo-topbar-widget__' . esc_attr( $widget['type'] );
				$classes[] = 'bloglo-topbar-widget';

				if ( isset( $widget['values']['visibility'] ) && $widget['values']['visibility'] ) {
					$classes[] = 'bloglo-' . esc_attr( $widget['values']['visibility'] );
				}

				$classes = apply_filters( 'bloglo_topbar_widget_classes', $classes, $widget );
				$classes = trim( implode( ' ', $classes ) );

				printf( '<div class="%s">', esc_attr( $classes ) );
				call_user_func( 'bloglo_top_bar_widget_' . $widget['type'], $widget['values'] );
				printf( '</div><!-- END .bloglo-topbar-widget -->' );
			}
		}
	}

	do_action( 'bloglo_top_bar_widgets_after_' . $location );
}
add_action( 'bloglo_topbar_widgets', 'bloglo_topbar_widgets_output' );

/**
 * Outputs the theme header area.
 *
 * @since 1.0.0
 */
function bloglo_header_output() {

	if ( ! bloglo_is_header_displayed() ) {
		return;
	}

	get_template_part( 'template-parts/header/base' );
}
add_action( 'bloglo_header', 'bloglo_header_output', 20 );

/**
 * Outputs the header widgets in Header Widget Locations.
 *
 * @since 1.0.0
 * @param string $locations Widget location.
 */
function bloglo_header_widgets( $locations ) {

	$locations   = (array) $locations;
	$all_widgets = (array) bloglo_option( 'header_widgets' );

	$header_widgets = $all_widgets;
	$header_class   = '';

	if ( ! empty( $locations ) ) {

		$header_widgets = array();

		foreach ( $locations as $location ) {

			$header_class = ' bloglo-widget-location-' . $location;

			$header_widgets[ $location ] = array();

			if ( ! empty( $all_widgets ) ) {
				foreach ( $all_widgets as $i => $widget ) {
					if ( $location === $widget['values']['location'] ) {
						$header_widgets[ $location ][] = $widget;
					}
				}
			}
		}
	}

	echo '<div class="bloglo-header-widgets bloglo-header-element' . esc_attr( $header_class ) . '">';

	if ( ! empty( $header_widgets ) ) {
		foreach ( $header_widgets as $location => $widgets ) {

			do_action( 'bloglo_header_widgets_before_' . $location );

			if ( ! empty( $widgets ) ) {
				foreach ( $widgets as $widget ) {
					if ( function_exists( 'bloglo_header_widget_' . $widget['type'] ) ) {

						$classes   = array();
						$classes[] = 'bloglo-header-widget__' . esc_attr( $widget['type'] );
						$classes[] = 'bloglo-header-widget';

						if ( isset( $widget['values']['visibility'] ) && $widget['values']['visibility'] ) {
							$classes[] = 'bloglo-' . esc_attr( $widget['values']['visibility'] );
						}

						$classes = apply_filters( 'bloglo_header_widget_classes', $classes, $widget );
						$classes = trim( implode( ' ', $classes ) );

						printf( '<div class="%s"><div class="bloglo-widget-wrapper">', esc_attr( $classes ) );
						call_user_func( 'bloglo_header_widget_' . $widget['type'], $widget['values'] );
						printf( '</div></div><!-- END .bloglo-header-widget -->' );
					}
				}
			}

			do_action( 'bloglo_header_widgets_after_' . $location );
		}
	}

	echo '</div><!-- END .bloglo-header-widgets -->';
}
add_action( 'bloglo_header_widget_location', 'bloglo_header_widgets', 1 );

/**
 * Outputs the content of theme header.
 *
 * @since 1.0.0
 */
function bloglo_header_content_output() {

	// Get the selected header layout from Customizer.
	$header_layout = bloglo_option( 'header_layout' );

	?>
	<div id="bloglo-header-inner">
		<?php

		// Load header layout template.
		get_template_part( 'template-parts/header/header', $header_layout );

		?>
	</div><!-- END #bloglo-header-inner -->
	<?php
}
add_action( 'bloglo_header_content', 'bloglo_header_content_output' );

/**
 * Outputs the main footer area.
 *
 * @since 1.0.0
 */
function bloglo_footer_output() {

	if ( ! bloglo_is_footer_displayed() ) {
		return;
	}

	get_template_part( 'template-parts/footer/base' );
}
add_action( 'bloglo_footer', 'bloglo_footer_output', 20 );

/**
 * Outputs the copyright area.
 *
 * @since 1.0.0
 */
function bloglo_copyright_bar_output() {

	if ( ! bloglo_is_copyright_bar_displayed() ) {
		return;
	}

	get_template_part( 'template-parts/footer/copyright/copyright' );
}
add_action( 'bloglo_footer', 'bloglo_copyright_bar_output', 30 );

/**
 * Outputs the copyright widgets.
 *
 * @since 1.0.0
 * @param string $location Widget location in copyright.
 */
function bloglo_copyright_widgets_output( $location ) {

	do_action( 'bloglo_copyright_widgets_before_' . $location );

	$bloglo_widgets = bloglo_option( 'copyright_widgets' );

	if ( is_array( $bloglo_widgets ) && ! empty( $bloglo_widgets ) ) {
		foreach ( $bloglo_widgets as $widget ) {

			if ( ! isset( $widget['values'] ) ) {
				continue;
			}

			if ( isset( $widget['values'], $widget['values']['location'] ) && $location !== $widget['values']['location'] ) {
				continue;
			}

			if ( function_exists( 'bloglo_copyright_widget_' . $widget['type'] ) ) {

				$classes   = array();
				$classes[] = 'bloglo-copyright-widget__' . esc_attr( $widget['type'] );
				$classes[] = 'bloglo-copyright-widget';

				if ( isset( $widget['values']['visibility'] ) && $widget['values']['visibility'] ) {
					$classes[] = 'bloglo-' . esc_attr( $widget['values']['visibility'] );
				}

				$classes = apply_filters( 'bloglo_copyright_widget_classes', $classes, $widget );
				$classes = trim( implode( ' ', $classes ) );

				printf( '<div class="%s">', esc_attr( $classes ) );
				call_user_func( 'bloglo_copyright_widget_' . $widget['type'], $widget['values'] );
				printf( '</div><!-- END .bloglo-copyright-widget -->' );
			}
		}
	}

	do_action( 'bloglo_copyright_widgets_after_' . $location );
}
add_action( 'bloglo_copyright_widgets', 'bloglo_copyright_widgets_output' );

/**
 * Outputs the theme sidebar area.
 *
 * @since 1.0.0
 */
function bloglo_sidebar_output() {

	if ( bloglo_is_sidebar_displayed() ) {
		get_sidebar();
	}
}
add_action( 'bloglo_sidebar', 'bloglo_sidebar_output' );

/**
 * Outputs the back to top button.
 *
 * @since 1.0.0
 */
function bloglo_back_to_top_output() {

	if ( ! bloglo_option( 'enable_scroll_top' ) ) {
		return;
	}

	get_template_part( 'template-parts/misc/back-to-top' );
}
add_action( 'bloglo_after_page_wrapper', 'bloglo_back_to_top_output' );

/**
 * Outputs the cursor dot.
 *
 * @since 1.0.0
 */
function bloglo_cursor_dot_output() {

	if ( ! bloglo_option( 'enable_cursor_dot' ) ) {
		return;
	}

	get_template_part( 'template-parts/misc/cursor-dot' );
}
add_action( 'bloglo_after_page_wrapper', 'bloglo_cursor_dot_output' );

/**
 * Outputs the theme page content.
 *
 * @since 1.0.0
 */
function bloglo_page_header_template() {

	do_action( 'bloglo_before_page_header' );

	if ( bloglo_is_page_header_displayed() ) {
		if ( is_singular( 'post' ) ) {
			get_template_part( 'template-parts/header-page-title-single' );
		} else {
			get_template_part( 'template-parts/header-page-title' );
		}
	}

	do_action( 'bloglo_after_page_header' );
}
add_action( 'bloglo_page_header', 'bloglo_page_header_template' );

/**
 * Outputs the theme hero content.
 *
 * @since 1.0.0
 */
function bloglo_blog_hero() {

	if ( ! bloglo_is_hero_displayed() ) {
		return;
	}

	// Hero type.
	$hero_type = bloglo_option( 'hero_type' );

	do_action( 'bloglo_before_hero' );

	// Enqueue Bloglo Slider script.
	wp_enqueue_script( 'bloglo-slider' );

	?>
	<div id="hero" <?php bloglo_hero_classes(); ?>>
		<?php get_template_part( 'template-parts/hero/hero', $hero_type ); ?>
	</div><!-- END #hero -->
	<?php

	do_action( 'bloglo_after_hero' );
}
add_action( 'bloglo_after_masthead', 'bloglo_blog_hero', 30 );


/**
 * Outputs the theme Blog Featured Links content.
 *
 * @since 1.1.0
 */
function bloglo_blog_featured_links() {

	if ( ! bloglo_is_featured_links_displayed() ) {
		return;
	}

	$bloglo_featured_links = bloglo_option('featured_links');

	// No items found.
	if ( !$bloglo_featured_links ) {
		return;
	}

	$features = array();
	$bloglo_featured_links = array_slice($bloglo_featured_links, 0, 3);
	foreach ($bloglo_featured_links as $bloglo_featured_link) {
		$features[] = array(
			'btn_text' 		=> $bloglo_featured_link['btn_text'],
			'btn_url' 		=> $bloglo_featured_link['btn_url'],
			'btn_target' 	=> $bloglo_featured_link['btn_target'],
			'image' 		=> $bloglo_featured_link['image'],
		);
	}

	do_action( 'bloglo_before_featured_links' );

	?>
	<div id="featured_links" <?php bloglo_featured_links_classes(); ?>>
		<?php get_template_part( 'template-parts/featured-links/featured', 'links', array('features' => $features) ); ?>
	</div><!-- END #featured_links -->
	<?php

	do_action( 'bloglo_after_featured_links' );
}
add_action( 'bloglo_after_masthead', 'bloglo_blog_featured_links', 31 );


/**
 * Outputs the theme PYML content.
 *
 * @since 1.0.0
 */
function bloglo_blog_pyml() {

	if ( ! bloglo_is_pyml_displayed() ) {
		return;
	}

	do_action( 'bloglo_before_pyml' );

	?>
	<div id="pyml" <?php bloglo_pyml_classes(); ?>>
		<?php get_template_part( 'template-parts/pyml/pyml' ); ?>
	</div><!-- END #pyml -->
	<?php

	do_action( 'bloglo_after_pyml' );
}
add_action( 'bloglo_after_container', 'bloglo_blog_pyml', 30 );


/**
 * Outputs the theme Ticker News content.
 *
 * @since 1.0.0
 */
function bloglo_blog_ticker() {

	if ( ! bloglo_is_ticker_displayed() ) {
		return;
	}

	do_action( 'bloglo_before_ticker' );

	// Enqueue Bloglo Marquee script.
	wp_enqueue_script( 'bloglo-marquee' );

	?>
	<div id="ticker" <?php bloglo_ticker_classes(); ?>>
		<?php get_template_part( 'template-parts/ticker/ticker' ); ?>
	</div><!-- END #ticker -->
	<?php

	do_action( 'bloglo_after_ticker' );
}
add_action( 'bloglo_after_masthead', 'bloglo_blog_ticker', 29 );


/**
 * Outputs the queried articles.
 *
 * @since 1.0.0
 */
function bloglo_content() {

	$bloglo_blog_layout        = bloglo()->options->get( 'bloglo_blog_layout' ) == 'blog-masonry' ? 'masonries' : '';
	$bloglo_blog_layout_column = 12;

	if ( bloglo()->options->get( 'bloglo_blog_layout' ) != 'blog-horizontal' ) :
		$bloglo_blog_layout_column = bloglo()->options->get( 'bloglo_blog_layout_column' );
	endif;

	if ( have_posts() ) :

		echo '<div class="bloglo-flex-row g-4 ' . $bloglo_blog_layout . '">';
		while ( have_posts() ) :
			the_post();

			echo '<div class="col-md-' . $bloglo_blog_layout_column . ' col-sm-' . $bloglo_blog_layout_column . ' col-xs-12">';
			get_template_part( 'template-parts/content/content', bloglo_get_article_feed_layout() );
			echo '</div>';
		endwhile;
		echo '</div>';
		bloglo_pagination();

	else :
		get_template_part( 'template-parts/content/content', 'none' );
	endif;
}
add_action( 'bloglo_content', 'bloglo_content' );
add_action( 'bloglo_content_archive', 'bloglo_content' );
add_action( 'bloglo_content_search', 'bloglo_content' );

/**
 * Outputs the theme single content.
 *
 * @since 1.0.0
 */
function bloglo_content_singular() {

	if ( have_posts() ) :
		while ( have_posts() ) :
			the_post();

			if ( is_singular( 'post' ) ) {
				do_action( 'bloglo_content_single' );
			} else {
				do_action( 'bloglo_content_page' );
			}

		endwhile;
	else :
		get_template_part( 'template-parts/content/content', 'none' );
	endif;
}
add_action( 'bloglo_content_singular', 'bloglo_content_singular' );


/**
 * Outputs the theme 404 page content.
 *
 * @since 1.0.0
 */
function bloglo_404_page_content() {

	get_template_part( 'template-parts/content/content', '404' );
}
add_action( 'bloglo_content_404', 'bloglo_404_page_content' );

/**
 * Outputs the theme page content.
 *
 * @since 1.0.0
 */
function bloglo_content_page() {

	get_template_part( 'template-parts/content/content', 'page' );
}
add_action( 'bloglo_content_page', 'bloglo_content_page' );

/**
 * Outputs the theme single post content.
 *
 * @since 1.0.0
 */
function bloglo_content_single() {

	get_template_part( 'template-parts/content/content', 'single' );
}
add_action( 'bloglo_content_single', 'bloglo_content_single' );

/**
 * Outputs the comments template.
 *
 * @since 1.0.0
 */
function bloglo_output_comments() {
	comments_template();
}
add_action( 'bloglo_after_singular', 'bloglo_output_comments' );

/**
 * Outputs the theme archive page info.
 *
 * @since 1.0.0
 */
function bloglo_archive_info() {

	// Author info.
	if ( is_author() ) {
		get_template_part( 'template-parts/entry/entry', 'about-author' );
	}
}
add_action( 'bloglo_before_content', 'bloglo_archive_info' );

/**
 * Outputs more posts button to author description box.
 *
 * @since 1.0.0
 */
function bloglo_add_author_posts_button() {
	if ( ! is_author() ) {
		get_template_part( 'template-parts/entry/entry', 'author-posts-button' );
	}
}
add_action( 'bloglo_entry_after_author_description', 'bloglo_add_author_posts_button' );

/**
 * Outputs Comments Toggle button.
 *
 * @since 1.0.0
 */
function bloglo_comments_toggle() {

	if ( bloglo_comments_toggle_displayed() ) {
		get_template_part( 'template-parts/entry/entry-show-comments' );
	}
}
add_action( 'bloglo_before_comments', 'bloglo_comments_toggle' );

/**
 * Outputs Pre-Footer area.
 *
 * @since 1.0.0
 */
function bloglo_pre_footer() {

	if ( ! bloglo_is_pre_footer_displayed() ) {
		return;
	}

	get_template_part( 'template-parts/pre-footer/base' );
}
add_action( 'bloglo_before_colophon', 'bloglo_pre_footer' );

/**
 * Outputs Page Preloader.
 *
 * @since 1.0.0
 */
function bloglo_preloader() {

	if ( ! bloglo_is_preloader_displayed() ) {
		return;
	}

	get_template_part( 'template-parts/preloader/base' );
}
add_action( 'bloglo_before_page_wrapper', 'bloglo_preloader' );

/**
 * Outputs breadcrumbs after header.
 *
 * @since  1.0.0
 * @return void
 */
function bloglo_breadcrumb_after_header_output() {

	if ( 'below-header' === bloglo_option( 'breadcrumbs_position' ) && bloglo_has_breadcrumbs() ) {

		$alignment = 'bloglo-text-align-' . bloglo_option( 'breadcrumbs_alignment' );

		$args = array(
			'container_before' => '<div class="bloglo-breadcrumbs"><div class="bloglo-container ' . $alignment . '">',
			'container_after'  => '</div></div>',
		);

		bloglo_breadcrumb( $args );
	}
}
add_action( 'bloglo_main_start', 'bloglo_breadcrumb_after_header_output' );

/**
 * Outputs breadcumbs in page header.
 *
 * @since  1.0.0
 * @return void
 */
function bloglo_breadcrumb_page_header_output() {

	if ( bloglo_page_header_has_breadcrumbs() ) {

		if ( is_singular( 'post' ) ) {
			$args = array(
				'container_before' => '<div class="bloglo-container bloglo-breadcrumbs">',
				'container_after'  => '</div>',
			);
		} else {
			$args = array(
				'container_before' => '<div class="bloglo-breadcrumbs">',
				'container_after'  => '</div>',
			);
		}

		bloglo_breadcrumb( $args );
	}
}
add_action( 'bloglo_page_header_end', 'bloglo_breadcrumb_page_header_output' );

/**
 * Replace tranparent header logo.
 *
 * @since  1.0.0
 * @param  string $output Current logo markup.
 * @return string         Update logo markup.
 */
function bloglo_transparent_header_logo( $output ) {

	// Check if transparent header is displayed.
	if ( bloglo_is_header_transparent() ) {

		// Check if transparent logo is set.
		$logo = bloglo_option( 'tsp_logo' );
		$logo = isset( $logo['background-image-id'] ) ? $logo['background-image-id'] : false;

		$retina = bloglo_option( 'tsp_logo_retina' );
		$retina = isset( $retina['background-image-id'] ) ? $retina['background-image-id'] : false;

		if ( $logo ) {
			$output = bloglo_get_logo_img_output( $logo, $retina, 'bloglo-tsp-logo' );
		}
	}

	return $output;
}
add_filter( 'bloglo_logo_img_output', 'bloglo_transparent_header_logo' );
add_filter( 'bloglo_site_title_markup', 'bloglo_transparent_header_logo' );

/**
 * Output the main navigation template.
 */
function bloglo_main_navigation_template() {
	get_template_part( 'template-parts/header/navigation' );
}

/**
 * Output the Header logo template.
 */
function bloglo_header_logo_template() {
	get_template_part( 'template-parts/header/logo' );
}


if ( ! function_exists( 'bloglo_display_customizer_shortcut' ) ) {
	/**
	 * This function display a shortcut to a customizer control.
	 *
	 * @param string $class_name The name of control we want to link this shortcut with.
	 * @param bool   $is_section_toggle Tells function to display eye icon if it's true.
	 */
	function bloglo_display_customizer_shortcut( $class_name, $is_section_toggle = false, $should_return = false ) {
		if ( ! is_customize_preview() ) {
			return;
		}
		$icon = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
				<path d="M13.89 3.39l2.71 2.72c.46.46.42 1.24.03 1.64l-8.01 8.02-5.56 1.16 1.16-5.58s7.6-7.63 7.99-8.03c.39-.39 1.22-.39 1.68.07zm-2.73 2.79l-5.59 5.61 1.11 1.11 5.54-5.65zm-2.97 8.23l5.58-5.6-1.07-1.08-5.59 5.6z"></path>
			</svg>';
		if ( $is_section_toggle ) {
			$icon = '<i class="far fa-eye"></i>';
		}

		$data = '<span class="bloglo-hide-section-shortcut customize-partial-edit-shortcut customize-partial-edit-shortcut-' . esc_attr( $class_name ) . '">
		<button class="customize-partial-edit-shortcut-button">
			' . $icon . '
		</button>
	</span>';
		if ( $should_return === true ) {
			return $data;
		}
		echo $data;
	}
}

function bloglo_about_button() {
	$button_widgets = bloglo_option( 'about_widgets' );

	if ( empty( $button_widgets ) ) {
		return;
	}
	foreach ( $button_widgets as $widget ) {
		call_user_func( 'bloglo_about_widget_' . $widget['type'], $widget['values'] );
	}
}

function bloglo_cta_widgets() {
	$widgets = bloglo_option( 'cta_widgets' );

	if ( empty( $widgets ) ) {
		return;
	}
	foreach ( $widgets as $widget ) {
		call_user_func( 'bloglo_cta_widget_' . $widget['type'], $widget['values'] );
	}
}

/**
 * Outputs the content of theme Service.
 *
 * @since 1.0.0
 */
function bloglo_service_content_output( $args ) {
	$args = (object) $args;
	// Get the selected service layout from Customizer.
	$services_style = bloglo_option( 'services_style' );

	// Load service layout template.
	get_template_part( 'template-parts/components/service/service-layout', $services_style, $args );

}
add_action( 'bloglo_service_content', 'bloglo_service_content_output', 10, 1 );

/**
 * Outputs the content of theme Info.
 *
 * @since 1.0.0
 */
function bloglo_info_content_output( $args ) {
	$args = (object) $args;
	// Get the selected info layout from Customizer.
	$info_style = bloglo_option( 'info_style' );

	// Load info layout template.
	get_template_part( 'template-parts/components/info/info-layout', $info_style, $args );

}
add_action( 'bloglo_info_content', 'bloglo_info_content_output', 10, 1 );

/**
 * Outputs the content of theme Team.
 *
 * @since 1.0.0
 */
function bloglo_team_content_output( $args ) {
	$args = (object) $args;
	// Get the selected team layout from Customizer.
	$team_style = bloglo_option( 'team_style' );

	// Load team layout template.
	get_template_part( 'template-parts/components/team/team-layout', $team_style, $args );

}
add_action( 'bloglo_team_content', 'bloglo_team_content_output', 10, 1 );

/**
 * Outputs the content of theme Features.
 *
 * @since 1.0.0
 */
function bloglo_features_content_output( $args ) {
	$args = (object) $args;
	// Get the selected features layout from Customizer.
	$features_style = bloglo_option( 'features_style' );

	// Load features layout template.
	get_template_part( 'template-parts/components/features/features-layout', $features_style, $args );

}
add_action( 'bloglo_features_content', 'bloglo_features_content_output', 10, 1 );
