<?php

/**
 * Bloglo Options Class.
 *
 * @package  Bloglo
 * @author   Peregrine Themes
 * @since    1.0.0
 */

/**
 * Do not allow direct script access.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Bloglo_Options' ) ) :

	/**
	 * Bloglo Options Class.
	 */
	class Bloglo_Options {

		/**
		 * Singleton instance of the class.
		 *
		 * @since 1.0.0
		 * @var object
		 */
		private static $instance;

		/**
		 * Options variable.
		 *
		 * @since 1.0.0
		 * @var mixed $options
		 */
		private static $options;

		/**
		 * Main Bloglo_Options Instance.
		 *
		 * @since 1.0.0
		 * @return Bloglo_Options
		 */
		public static function instance() {

			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Bloglo_Options ) ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * Primary class constructor.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {

			// Refresh options.
			add_action( 'after_setup_theme', array( $this, 'refresh' ) );
		}

		/**
		 * Set default option values.
		 *
		 * @since  1.0.0
		 * @return array Default values.
		 */
		public function get_defaults() {

			$defaults = array(

				/**
				 * General Settings.
				 */

				// Layout.
				'bloglo_site_layout'                       => 'fw-contained',
				'bloglo_container_width'                   => 1420,

				// Base Colors.
				'bloglo_accent_color'                      => '#004bed',
				'bloglo_dark_mode'                         => false,
				'bloglo_content_text_color'                => '#212121',
				'bloglo_headings_color'                    => '#212121',
				'bloglo_content_link_hover_color'          => '#94979e',
				'bloglo_body_background_heading'           => true,
				'bloglo_content_background_heading'        => true,
				'bloglo_boxed_content_background_color'    => '#FFFFFF',
				'bloglo_scroll_top_visibility'             => 'all',

				// Base Typography.
				'bloglo_html_base_font_size'               => array(
					'desktop' => 62.5,
					'tablet'  => 53,
					'mobile'  => 50,
				),
				'bloglo_font_smoothing'                    => true,
				'bloglo_typography_body_heading'           => false,
				'bloglo_typography_headings_heading'       => false,
				'bloglo_body_font'                         => bloglo_typography_defaults(
					array(
						'font-family'         => 'Josefin Sans',
						'font-weight'         => 400,
						'font-size-desktop'   => '1.7',
						'font-size-unit'      => 'rem',
						'line-height-desktop' => '1.75',
					)
				),
				'bloglo_headings_font'                     => bloglo_typography_defaults(
					array(
						'font-weight'     => 700,
						'font-style'      => 'normal',
						'text-transform'  => 'none',
						'text-decoration' => 'none',
					)
				),
				'bloglo_h1_font'                           => bloglo_typography_defaults(
					array(
						'font-weight'         => 700,
						'font-size-desktop'   => '4',
						'font-size-unit'      => 'rem',
						'line-height-desktop' => '1.1',
					)
				),
				'bloglo_h2_font'                           => bloglo_typography_defaults(
					array(
						'font-weight'         => 700,
						'font-size-desktop'   => '3.6',
						'font-size-unit'      => 'rem',
						'line-height-desktop' => '1.2',
					)
				),
				'bloglo_h3_font'                           => bloglo_typography_defaults(
					array(
						'font-weight'         => 700,
						'font-size-desktop'   => '2.8',
						'font-size-unit'      => 'rem',
						'line-height-desktop' => '1.2',
					)
				),
				'bloglo_h4_font'                           => bloglo_typography_defaults(
					array(
						'font-weight'         => 700,
						'font-size-desktop'   => '2.4',
						'font-size-unit'      => 'rem',
						'line-height-desktop' => '1.2',
					)
				),
				'bloglo_h5_font'                           => bloglo_typography_defaults(
					array(
						'font-weight'         => 700,
						'font-size-desktop'   => '2',
						'font-size-unit'      => 'rem',
						'line-height-desktop' => '1.2',
					)
				),
				'bloglo_h6_font'                           => bloglo_typography_defaults(
					array(
						'font-weight'         => 600,
						'font-size-desktop'   => '1.8',
						'font-size-unit'      => 'rem',
						'line-height-desktop' => '1.72',
					)
				),
				'bloglo_heading_em_font'                   => bloglo_typography_defaults(
					array(
						'font-family' => 'Playfair Display',
						'font-weight' => 'inherit',
						'font-style'  => 'italic',
					)
				),
				'bloglo_section_heading_style'             => '1',
				'bloglo_footer_widget_title_font_size'     => array(
					'desktop' => 2,
					'unit'    => 'rem',
				),

				// Primary Button.
				'bloglo_primary_button_heading'            => false,
				'bloglo_primary_button_bg_color'           => '#004bed',
				'bloglo_primary_button_hover_bg_color'     => '#2766f0',
				'bloglo_primary_button_text_color'         => '#fff',
				'bloglo_primary_button_hover_text_color'   => '#fff',
				'bloglo_primary_button_border_radius'      => array(
					'top-left'     => '',
					'top-right'    => '',
					'bottom-right' => '',
					'bottom-left'  => '',
					'unit'         => 'rem',
				),
				'bloglo_primary_button_border_width'       => .1,
				'bloglo_primary_button_border_color'       => '#004bed',
				'bloglo_primary_button_hover_border_color' => '#2766f0',
				'bloglo_primary_button_typography'         => bloglo_typography_defaults(
					array(
						'font-family'         => 'Plus Jakarta Sans',
						'font-weight'         => 500,
						'font-size-desktop'   => '1.8',
						'font-size-unit'      => 'rem',
						'line-height-desktop' => '1.6',
					)
				),

				// Secondary Button.
				'bloglo_secondary_button_heading'          => false,
				'bloglo_secondary_button_bg_color'         => '#212121',
				'bloglo_secondary_button_hover_bg_color'   => '#3e4750',
				'bloglo_secondary_button_text_color'       => '#FFFFFF',
				'bloglo_secondary_button_hover_text_color' => '#FFFFFF',
				'bloglo_secondary_button_border_radius'    => array(
					'top-left'     => '',
					'top-right'    => '',
					'bottom-right' => '',
					'bottom-left'  => '',
					'unit'         => 'rem',
				),
				'bloglo_secondary_button_border_width'     => .1,
				'bloglo_secondary_button_border_color'     => 'rgba(0, 0, 0, 0.12)',
				'bloglo_secondary_button_hover_border_color' => 'rgba(0, 0, 0, 0.12)',
				'bloglo_secondary_button_typography'       => bloglo_typography_defaults(
					array(
						'font-family'         => 'Plus Jakarta Sans',
						'font-weight'         => 500,
						'font-size-desktop'   => '1.8',
						'font-size-unit'      => 'rem',
						'line-height-desktop' => '1.6',
					)
				),

				// Text button.
				'bloglo_text_button_heading'               => false,
				'bloglo_text_button_text_color'            => '#212121',
				'bloglo_text_button_hover_text_color'      => '',
				'bloglo_text_button_typography'            => bloglo_typography_defaults(
					array(
						'font-family'         => 'Plus Jakarta Sans',
						'font-weight'         => 500,
						'font-size-desktop'   => '1.6',
						'font-size-unit'      => 'rem',
						'line-height-desktop' => '1.5',
					)
				),

				// Misc Settings.
				'bloglo_enable_schema'                     => true,
				'bloglo_custom_input_style'                => true,
				'bloglo_preloader_heading'                 => false,
				'bloglo_preloader'                         => false,
				'bloglo_preloader_style'                   => '1',
				'bloglo_preloader_visibility'              => 'all',
				'bloglo_scroll_top_heading'                => false,
				'bloglo_enable_scroll_top'                 => true,
				'bloglo_enable_cursor_dot'                 => true,
				'bloglo_parallax_footer'                   => false,

				/**
				 * Logos & Site Title.
				 */
				'bloglo_logo_default_retina'               => '',
				'bloglo_logo_max_height'                   => array(
					'desktop' => 45,
				),
				'bloglo_logo_margin'                       => array(
					'desktop' => array(
						'top'    => 25,
						'right'  => 40,
						'bottom' => 25,
						'left'   => 0,
					),
					'tablet'  => array(
						'top'    => 25,
						'right'  => 1,
						'bottom' => 25,
						'left'   => 0,
					),
					'mobile'  => array(
						'top'    => '',
						'right'  => '',
						'bottom' => '',
						'left'   => '',
					),
					'unit'    => 'px',
				),
				'bloglo_display_tagline'                   => false,
				'bloglo_logo_heading_site_identity'        => true,
				'bloglo_typography_logo_heading'           => false,
				'bloglo_logo_text_font_size'               => array(
					'desktop' => 4,
					'unit'    => 'rem',
				),

				/**
				 * Header.
				 */

				// Top Bar.
				'bloglo_top_bar_enable'                    => true,
				'bloglo_top_bar_container_width'           => 'content-width',
				'bloglo_top_bar_visibility'                => 'all',
				'bloglo_top_bar_heading_widgets'           => true,
				'bloglo_top_bar_widgets'                   => array(
					array(
						'classname' => 'bloglo_customizer_widget_text',
						'type'      => 'text',
						'values'    => array(
							'content'    => wp_kses( '<i class="far fa-calendar-alt fa-lg bloglo-icon"></i><strong><span id="bloglo-date"></span> - <span id="bloglo-time"></span></strong>', bloglo_get_allowed_html_tags() ),
							'location'   => 'left',
							'visibility' => 'all',
						),
					),
					array(
						'classname' => 'bloglo_customizer_widget_text',
						'type'      => 'text',
						'values'    => array(
							'content'    => wp_kses( '<i class="far fa-location-arrow fa-lg bloglo-icon"></i> Subscribe to our newsletter & never miss our best posts. <a href="#"><strong>Subscribe Now!</strong></a>', bloglo_get_allowed_html_tags() ),
							'location'   => 'right',
							'visibility' => 'all',
						),
					),
				),
				'bloglo_top_bar_heading_design_options'    => false,
				'bloglo_top_bar_background'                => bloglo_design_options_defaults(
					array(
						'background' => array(
							'color'    => array(
								'background-color' => '#30373e',
							),
							'gradient' => array(),
						),
					)
				),
				'bloglo_top_bar_text_color'                => bloglo_design_options_defaults(
					array(
						'color' => array(
							'text-color'       => '#ffffff',
							'link-color'       => '#ffffff',
							'link-hover-color' => '#ffffff',
						),
					)
				),
				'bloglo_top_bar_border'                    => bloglo_design_options_defaults(
					array(
						'border' => array(
							'border-bottom-width' => '1',
							'border-style'        => 'solid',
							'border-color'        => 'rgba(0,0,0, .085)',
							'separator-color'     => '#cccccc',
						),
					)
				),

				// Main Header.
				'bloglo_header_layout'                     => 'layout-4',

				'bloglo_header_ads_banner'                 => get_template_directory_uri() . '/assets/images/header-promo.png',
				'bloglo_header_ads_banner_url'             => '',
				'bloglo_header_ads_banner_url_target'      => '_self',

				'bloglo_header_container_width'            => 'content-width',
				'bloglo_header_heading_widgets'            => true,
				'bloglo_header_widgets'                    => array(
					array(
						'classname' => 'bloglo_customizer_widget_socials',
						'type'      => 'socials',
						'values'    => array(
							'style'      => 'rounded-fill',
							'size'       => 'standard',
							'location'   => 'left',
							'visibility' => 'hide-mobile-tablet',
						),
					),
					array(
						'classname' => 'bloglo_customizer_widget_darkmode',
						'type'      => 'darkmode',
						'values'    => array(
							'location'   => 'right',
							'visibility' => 'hide-mobile-tablet',
						),
					),
					array(
						'classname' => 'bloglo_customizer_widget_search',
						'type'      => 'search',
						'values'    => array(
							'location'   => 'right',
							'visibility' => 'hide-mobile-tablet',
						),
					),
					array(
						'classname' => 'bloglo_customizer_widget_button',
						'type'      => 'button',
						'values'    => array(
							'text'       => 'Subscribe',
							'url'        => '#',
							'class'      => 'btn-small',
							'target'     => true,
							'location'   => 'right',
							'visibility' => 'hide-mobile-tablet',
						),
					),
				),
				'bloglo_header_widgets_separator'          => 'none',
				'bloglo_header_heading_design_options'     => false,
				'bloglo_header_background'                 => bloglo_design_options_defaults(
					array(
						'background' => array(
							'color'    => array(
								'background-color' => '#FFFFFF',
							),
							'gradient' => array(),
							'image'    => array(),
						),
					)
				),
				'bloglo_header_border'                     => bloglo_design_options_defaults(
					array(
						'border' => array(
							'border-bottom-width' => 1,
							'border-color'        => 'rgba(0,0,0, .085)',
							'separator-color'     => '#cccccc',
						),
					)
				),
				'bloglo_header_text_color'                 => bloglo_design_options_defaults(
					array(
						'color' => array(
							'text-color' => '#66717f',
							'link-color' => '#30373e',
						),
					)
				),

				// Transparent Header.
				'bloglo_tsp_header'                        => false,
				'bloglo_tsp_header_disable_on'             => array(
					'404',
					'posts_page',
					'archive',
					'search',
				),
				'bloglo_tsp_logo_heading'                  => false,
				'bloglo_tsp_logo'                          => '',
				'bloglo_tsp_logo_retina'                   => '',
				'bloglo_tsp_logo_max_height'               => array(
					'desktop' => 45,
				),
				'bloglo_tsp_logo_margin'                   => array(
					'desktop' => array(
						'top'    => '',
						'right'  => '',
						'bottom' => '',
						'left'   => '',
					),
					'tablet'  => array(
						'top'    => '',
						'right'  => '',
						'bottom' => '',
						'left'   => '',
					),
					'mobile'  => array(
						'top'    => '',
						'right'  => '',
						'bottom' => '',
						'left'   => '',
					),
					'unit'    => 'px',
				),
				'bloglo_tsp_colors_heading'                => false,
				'bloglo_tsp_header_background'             => bloglo_design_options_defaults(
					array(
						'background' => array(
							'color' => array(),
						),
					)
				),
				'bloglo_tsp_header_font_color'             => bloglo_design_options_defaults(
					array(
						'color' => array(),
					)
				),
				'bloglo_tsp_header_border'                 => bloglo_design_options_defaults(
					array(
						'border' => array(),
					)
				),

				// Sticky Header.
				'bloglo_sticky_header'                     => false,
				'bloglo_sticky_header_hide_on'             => array( '' ),

				// Main Navigation.
				'bloglo_main_nav_heading_animation'        => false,
				'bloglo_main_nav_hover_animation'          => 'underline',
				'bloglo_main_nav_heading_sub_menus'        => true,
				'bloglo_main_nav_sub_indicators'           => true,
				'bloglo_main_nav_heading_mobile_menu'      => false,
				'bloglo_main_nav_mobile_breakpoint'        => 1024,
				'bloglo_main_nav_mobile_label'             => '',
				'bloglo_nav_design_options'                => false,
				'bloglo_main_nav_background'               => bloglo_design_options_defaults(
					array(
						'background' => array(
							'color'    => array(
								'background-color' => '#FFFFFF',
							),
							'gradient' => array(),
						),
					)
				),
				'bloglo_main_nav_border'                   => bloglo_design_options_defaults(
					array(
						'border' => array(
							'border-top-width'    => 1,
							'border-bottom-width' => 1,
							'border-style'        => 'solid',
							'border-color'        => 'rgba(0,0,0, .085)',
						),
					)
				),
				'bloglo_main_nav_font_color'               => bloglo_design_options_defaults(
					array(
						'color' => array(),
					)
				),
				'bloglo_typography_main_nav_heading'       => false,
				'bloglo_main_nav_font_size'                => array(
					'value' => 1.7,
					'unit'  => 'rem',
				),

				// Page Header.
				'bloglo_page_header_enable'                => true,
				'bloglo_page_header_alignment'             => 'left',
				'bloglo_page_header_spacing'               => array(
					'desktop' => array(
						'top'    => 30,
						'bottom' => 30,
					),
					'tablet'  => array(
						'top'    => '',
						'bottom' => '',
					),
					'mobile'  => array(
						'top'    => '',
						'bottom' => '',
					),
					'unit'    => 'px',
				),
				'bloglo_page_header_background'            => bloglo_design_options_defaults(
					array(
						'background' => array(
							'color'    => array( 'background-color' => 'rgba(255,76,96,0.1)' ),
							'gradient' => array(),
							'image'    => array(),
						),
					)
				),
				'bloglo_page_header_text_color'            => bloglo_design_options_defaults(
					array(
						'color' => array(),
					)
				),
				'bloglo_page_header_border'                => bloglo_design_options_defaults(
					array(
						'border' => array(
							'border-bottom-width' => 1,
							'border-style'        => 'solid',
							'border-color'        => 'rgba(0,0,0,.062)',
						),
					)
				),
				'bloglo_typography_page_header'            => false,
				'bloglo_page_header_font_size'             => array(
					'desktop' => 2.6,
					'unit'    => 'rem',
				),

				// Breadcrumbs.
				'bloglo_breadcrumbs_enable'                => true,
				'bloglo_breadcrumbs_hide_on'               => array( 'home' ),
				'bloglo_breadcrumbs_position'              => 'in-page-header',
				'bloglo_breadcrumbs_alignment'             => 'left',
				'bloglo_breadcrumbs_spacing'               => array(
					'desktop' => array(
						'top'    => 15,
						'bottom' => 15,
					),
					'tablet'  => array(
						'top'    => '',
						'bottom' => '',
					),
					'mobile'  => array(
						'top'    => '',
						'bottom' => '',
					),
					'unit'    => 'px',
				),
				'bloglo_breadcrumbs_heading_design'        => false,
				'bloglo_breadcrumbs_background'            => bloglo_design_options_defaults(
					array(
						'background' => array(
							'color'    => array(),
							'gradient' => array(),
							'image'    => array(),
						),
					)
				),
				'bloglo_breadcrumbs_text_color'            => bloglo_design_options_defaults(
					array(
						'color' => array(),
					)
				),
				'bloglo_breadcrumbs_border'                => bloglo_design_options_defaults(
					array(
						'border' => array(
							'border-top-width'    => 0,
							'border-bottom-width' => 0,
							'border-color'        => '',
							'border-style'        => 'solid',
						),
					)
				),

				/**
				 * Hero.
				 */
				'bloglo_enable_hero'                       => true,
				'bloglo_hero_type'                         => 'hover-slider',
				'bloglo_hero_visibility'                   => 'all',
				'bloglo_hero_enable_on'                    => array( 'home' ),
				'bloglo_hero_hover_slider'                 => false,
				'bloglo_hero_hover_slider_container'       => 'content-width',
				'bloglo_hero_hover_slider_height'          => 500,
				'bloglo_hero_hover_slider_overlay'         => '2',
				'bloglo_hero_hover_slider_elements'        => array(
					'category'  => true,
					'meta'      => true,
					'read_more' => true,
				),
				'bloglo_hero_hover_slider_posts'           => false,
				'bloglo_hero_hover_slider_post_number'     => 2,
				'bloglo_hero_hover_slider_category'        => array(),

				/**
				 * Featured Links
				 */
				'bloglo_enable_featured_links'        	=> false,
				'bloglo_featured_links_title'           => '',
				'bloglo_featured_links_visibility'    	=> 'all',
				'bloglo_featured_links_enable_on'     	=> array( 'home' ),
				'bloglo_featured_links_style'         	=> false,
				'bloglo_featured_links_container'     	=> 'content-width',
				'bloglo_featured_links'          		=> apply_filters(
					'bloglo_featured_links_default',
					array(
						array(
							'btn_text'        	=> '',
							'btn_url'       	=> '',
							'btn_target' 		=> '',
							'image' 			=> array(),
						),
						array(
							'btn_text'        	=> '',
							'btn_url'       	=> '',
							'btn_target' 		=> '',
							'image' 			=> array(),
						),
						array(
							'btn_text'        	=> '',
							'btn_url'       	=> '',
							'btn_target' 		=> '',
							'image' 			=> array(),
						),
					),
				),

				/**
				 * PYML
				 */
				'bloglo_enable_pyml'                       => true,
				'bloglo_pyml_title'                        => esc_html__( 'Post You Might Like', 'bloglo' ),
				'bloglo_pyml_visibility'                   => 'all',
				'bloglo_pyml_enable_on'                    => array( 'home' ),
				'bloglo_pyml_style'                        => false,
				'bloglo_pyml_container'                    => 'content-width',
				'bloglo_pyml_elements'                     => array(
					'category' => true,
					'meta'     => true,
				),
				'bloglo_pyml_posts'                        => true,
				'bloglo_pyml_post_number'                  => 4,
				'bloglo_pyml_category'                     => array(),

				/**
				 * Ticker Slider
				 */
				'bloglo_enable_ticker'                     => true,
				'bloglo_ticker_title'                      => esc_html__( 'Breakings', 'bloglo' ),
				'bloglo_ticker_visibility'                 => 'all',
				'bloglo_ticker_enable_on'                  => array( 'home' ),
				'bloglo_ticker_style'                      => false,
				'bloglo_ticker_container'                  => 'content-width',
				'bloglo_ticker_elements'                   => array(
					'meta' => true,
				),
				'bloglo_ticker_posts'                      => false,
				'bloglo_ticker_post_number'                => 10,
				'bloglo_ticker_category'                   => array(),

				/**
				 * Maintenance mode
				 */

				'bloglo_enable_maintenance'                => false,
				'bloglo_maintenance_text'                  => sprintf( '<h2 class="title">%1$s</h2><p class="description">%2$s</p>', esc_html__( 'Site Under construction.', 'bloglo' ), esc_html__( 'Subscribe today and we will inform you once we go online.', 'bloglo' ) ),
				'bloglo_maintenance_background'            => bloglo_design_options_defaults(
					array(
						'background' => array(
							'color'    => array( 'background-color' => 'rgba(0,0,0,.025)' ),
							'gradient' => array(),
							'image'    => array(),
						),
					)
				),
				'bloglo_maintenance_text_color'            => bloglo_design_options_defaults(
					array(
						'color' => array(
							'text-color' => '#fff',
						),
					)
				),
				'bloglo_maintenance_make_live'             => false,
				'bloglo_maintenance_timezone'              => '',
				'bloglo_enable_maintenance_form'           => false,
				'bloglo_maintenance_form_shortcode'        => '',
				'bloglo_enable_maintenance_social_media'   => false,
				'bloglo_maintenance_style'                 => 1,

				/**
				 * Blog.
				 */

				// Blog Page / Archive.
				'bloglo_blog_entry_elements'               => array(
					'thumbnail'      => true,
					'header'         => true,
					'meta'           => true,
					'summary'        => true,
					'summary-footer' => true,
				),
				'bloglo_blog_entry_meta_elements'          => array(
					'author'   => true,
					'date'     => true,
					'category' => false,
					'tag'      => false,
					'comments' => false,
				),
				'bloglo_entry_meta_icons'                  => true,
				'bloglo_excerpt_length'                    => 30,
				'bloglo_excerpt_more'                      => '&hellip;',
				'bloglo_blog_layout'                       => 'blog-masonry',
				'bloglo_blog_layout_column'                => 6,
				'bloglo_blog_image_position'               => 'left',
				'bloglo_blog_image_size'                   => 'large',
				'bloglo_blog_card_border'                  => true,
				'bloglo_blog_card_shadow'                  => true,
				'bloglo_blog_horizontal_post_categories'   => true,
				'bloglo_blog_horizontal_read_more'         => false,

				// Single Post.
				'bloglo_single_post_layout_heading'        => false,
				'bloglo_single_title_position'             => 'in-content',
				'bloglo_single_title_alignment'            => 'left',
				'bloglo_single_title_spacing'              => array(
					'desktop' => array(
						'top'    => 152,
						'bottom' => 100,
					),
					'tablet'  => array(
						'top'    => 90,
						'bottom' => 55,
					),
					'mobile'  => array(
						'top'    => '',
						'bottom' => '',
					),
					'unit'    => 'px',
				),
				'bloglo_single_content_width'              => 'wide',
				'bloglo_single_narrow_container_width'     => 700,
				'bloglo_single_post_elements_heading'      => false,
				'bloglo_single_post_meta_elements'         => array(
					'author'   => true,
					'date'     => true,
					'comments' => true,
					'category' => false,
				),
				'bloglo_single_post_thumb'                 => true,
				'bloglo_single_post_categories'            => true,
				'bloglo_single_post_tags'                  => true,
				'bloglo_single_last_updated'               => true,
				'bloglo_single_about_author'               => true,
				'bloglo_single_post_next_prev'             => true,
				'bloglo_single_post_elements'              => array(
					'thumb'          => true,
					'category'       => true,
					'tags'           => true,
					'last-updated'   => true,
					'about-author'   => true,
					'prev-next-post' => true,
				),
				'bloglo_single_toggle_comments'            => false,
				'bloglo_single_entry_meta_icons'           => true,
				'bloglo_typography_single_post_heading'    => false,

				/**
				 * Sidebar.
				 */

				'bloglo_sidebar_position'                  => 'right-sidebar',
				'bloglo_single_post_sidebar_position'      => 'no-sidebar',
				'bloglo_single_page_sidebar_position'      => 'default',
				'bloglo_archive_sidebar_position'          => 'default',
				'bloglo_sidebar_options_heading'           => false,
				'bloglo_sidebar_style'                     => '3',
				'bloglo_sidebar_width'                     => 30,
				'bloglo_sidebar_sticky'                    => 'sidebar',
				'bloglo_sidebar_responsive_position'       => 'after-content',
				'bloglo_typography_sidebar_heading'        => false,
				'bloglo_sidebar_widget_title_font_size'    => array(
					'desktop' => 2,
					'unit'    => 'rem',
				),

				/**
				 * Footer.
				 */

				// Pre Footer.
				'bloglo_pre_footer_cta'                    => true,
				'bloglo_enable_pre_footer_cta'             => false,
				'bloglo_pre_footer_cta_visibility'         => 'all',
				'bloglo_pre_footer_cta_hide_on'            => array(),
				'bloglo_pre_footer_cta_style'              => '1',
				'bloglo_pre_footer_cta_text'               => wp_kses_post( __( 'This is an example of <em>Pre Footer</em> section in Bloglo.', 'bloglo' ) ),
				'bloglo_pre_footer_cta_btn_text'           => wp_kses_post( __( 'Example Button', 'bloglo' ) ),
				'bloglo_pre_footer_cta_btn_url'            => '#',
				'bloglo_pre_footer_cta_btn_new_tab'        => false,
				'bloglo_pre_footer_cta_design_options'     => false,
				'bloglo_pre_footer_cta_background'         => bloglo_design_options_defaults(
					array(
						'background' => array(
							'color'    => array(),
							'gradient' => array(),
							'image'    => array(),
						),
					)
				),
				'bloglo_pre_footer_cta_border'             => bloglo_design_options_defaults(
					array(
						'border' => array(),
					)
				),
				'bloglo_pre_footer_cta_text_color'         => bloglo_design_options_defaults(
					array(
						'color' => array(
							'text-color' => '#FFFFFF',
						),
					)
				),
				'bloglo_pre_footer_cta_typography'         => false,
				'bloglo_pre_footer_cta_font_size'          => array(
					'desktop' => 2.8,
					'unit'    => 'rem',
				),

				// Copyright.
				'bloglo_enable_copyright'                  => true,
				'bloglo_copyright_layout'                  => 'layout-1',
				'bloglo_copyright_separator'               => 'contained-separator',
				'bloglo_copyright_visibility'              => 'all',
				'bloglo_copyright_heading_widgets'         => true,
				'bloglo_copyright_widgets'                 => array(
					array(
						'classname' => 'bloglo_customizer_widget_text',
						'type'      => 'text',
						'values'    => array(
							'content'    => esc_html__( 'Copyright {{the_year}} &mdash; {{site_title}}. All rights reserved. {{theme_link}}', 'bloglo' ),
							'location'   => 'start',
							'visibility' => 'all',
						),
					),
				),
				'bloglo_copyright_heading_design_options'  => false,
				'bloglo_copyright_background'              => bloglo_design_options_defaults(
					array(
						'background' => array(
							'color'    => array(
								'background-color' => '#000000',
							),
							'gradient' => array(),
						),
					)
				),
				'bloglo_copyright_text_color'              => bloglo_design_options_defaults(
					array(
						'color' => array(
							'text-color'       => '#94979e',
							'link-color'       => '#ffffff',
							'link-hover-color' => '#004bed',
						),
					)
				),

				// Main Footer.
				'bloglo_enable_footer'                     => true,
				'bloglo_footer_layout'                     => 'layout-2',
				'bloglo_footer_widgets_align_center'       => false,
				'bloglo_footer_visibility'                 => 'all',
				'bloglo_footer_widget_heading_style'       => '0',
				'bloglo_footer_heading_design_options'     => false,
				'bloglo_footer_background'                 => bloglo_design_options_defaults(
					array(
						'background' => array(
							'color'    => array(
								'background-color' => '#30373e',
							),
							'gradient' => array(),
							'image'    => array(),
						),
					)
				),
				'bloglo_footer_text_color'                 => bloglo_design_options_defaults(
					array(
						'color' => array(
							'text-color'         => '#94979e',
							'link-color'         => '#ffffff',
							'link-hover-color'   => '#004bed',
							'widget-title-color' => '#ffffff',
						),
					)
				),
				'bloglo_footer_border'                     => bloglo_design_options_defaults(
					array(
						'border' => array(
							'border-top-width'    => 5,
							'border-bottom-width' => 0,
							'border-color'        => '',
							'border-style'        => 'solid',
						),
					)
				),
				'bloglo_typography_main_footer_heading'    => false,
			);

			$defaults = apply_filters( 'bloglo_default_option_values', $defaults );
			return $defaults;
		}

		/**
		 * Get the options from static array()
		 *
		 * @since  1.0.0
		 * @return array    Return array of theme options.
		 */
		public function get_options() {
			return self::$options;
		}

		/**
		 * Get the options from static array().
		 *
		 * @since  1.0.0
		 * @param string $id Options jet to get.
		 * @return array Return array of theme options.
		 */
		public function get( $id ) {
			$value = isset( self::$options[ $id ] ) ? self::$options[ $id ] : self::get_default( $id );
			$value = apply_filters("theme_mod_{$id}", $value); // phpcs:ignore
			return $value;
		}

		/**
		 * Set option.
		 *
		 * @since  1.0.0
		 * @param string $id Option key.
		 * @param any    $value Option value.
		 * @return void
		 */
		public function set( $id, $value ) {
			set_theme_mod( $id, $value );
			self::$options[ $id ] = $value;
		}

		/**
		 * Refresh options.
		 *
		 * @since  1.0.0
		 * @return void
		 */
		public function refresh() {
			self::$options = wp_parse_args(
				get_theme_mods(),
				self::get_defaults()
			);
		}

		/**
		 * Returns the default value for option.
		 *
		 * @since  1.0.0
		 * @param  string $id Option ID.
		 * @return mixed      Default option value.
		 */
		public function get_default( $id ) {
			$defaults = self::get_defaults();
			return isset( $defaults[ $id ] ) ? $defaults[ $id ] : false;
		}
	}

endif;
