<?php
/**
 * Bloglo Main Header Settings section in Customizer.
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

if ( ! class_exists( 'Bloglo_Customizer_Main_Header' ) ) :
	/**
	 * Bloglo Main Header section in Customizer.
	 */
	class Bloglo_Customizer_Main_Header {

		/**
		 * Primary class constructor.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {

			/**
			 * Registers our custom options in Customizer.
			 */
			add_filter( 'bloglo_customizer_options', array( $this, 'register_options' ) );
		}

		/**
		 * Registers our custom options in Customizer.
		 *
		 * @since 1.0.0
		 * @param array $options Array of customizer options.
		 */
		public function register_options( $options ) {

			// Main Header Section.
			$options['section']['bloglo_section_main_header'] = array(
				'title'    => esc_html__( 'Main Header', 'bloglo' ),
				'panel'    => 'bloglo_panel_header',
				'priority' => 20,
			);

			// Header Layout.
			$options['setting']['bloglo_header_layout'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'bloglo_sanitize_select',
				'control'           => array(
					'type'        => 'bloglo-radio-image',
					'label'       => esc_html__( 'Header Layout', 'bloglo' ),
					'description' => esc_html__( 'Pre-defined positions of header elements, such as logo and navigation.', 'bloglo' ),
					'section'     => 'bloglo_section_main_header',
					'priority'    => 5,
					'choices'     => array(
						'layout-4' => array(
							'image' => BLOGLO_THEME_URI . '/inc/customizer/assets/images/header-layout-4.svg',
							'title' => esc_html__( 'Header 1', 'bloglo' ),
						),
					),
				),
			);

			// Header Ads Banner
			$options['setting']['bloglo_header_ads_banner'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'esc_url',
				'control'           => array(
					'class'           => 'WP_Customize_Image_Control',
					'mime_type'       => 'image',
					'label'           => esc_html__( 'Ads image', 'bloglo' ),
					'description'     => esc_html__( 'Click “Add new image” to upload an image file from your computer. Your theme works best with an image with a banner size of 732 × 90 pixels. ', 'bloglo' ),
					'section'         => 'bloglo_section_main_header',
					'priority'        => 6,
					'active_callback' => function( $control ) {
						return 'layout-4' === $control->manager->get_setting( 'bloglo_header_layout' )->value();
					},
				),
			);

			// Header Ads Banner URL
			$options['setting']['bloglo_header_ads_banner_url'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'esc_url_raw',
				'control'           => array(
					'type'            => 'bloglo-text',
					'label'           => esc_html__( 'Ads url', 'bloglo' ),
					'section'         => 'bloglo_section_main_header',
					'priority'        => 7,
					'required'   => array(
						array(
							'control'  => 'bloglo_header_layout',
							'value'    => 'layout-4',
							'operator' => '==',
						),
					),
				),
			);

			// Header Ads Banner URL Target
			$options['setting']['bloglo_header_ads_banner_url_target'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'bloglo_sanitize_select',
				'control'           => array(
					'type'            => 'bloglo-select',
					'label'           => esc_html__( 'Open Ads link on', 'bloglo' ),
					'section'         => 'bloglo_section_main_header',
					'priority'        => 8,
					'choices'         => array(
						'_self'  => esc_html__( 'Open in same tab', 'bloglo' ),
						'_blank' => esc_html__( 'Open in new tab', 'bloglo' ),
					),
					'required'   => array(
						array(
							'control'  => 'bloglo_header_layout',
							'value'    => 'layout-4',
							'operator' => '==',
						),
					),
				),
			);

			// Header widgets heading.
			$options['setting']['bloglo_header_heading_widgets'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'bloglo_sanitize_toggle',
				'control'           => array(
					'type'        => 'bloglo-heading',
					'label'       => esc_html__( 'Header Widgets', 'bloglo' ),
					'description' => esc_html__( 'Click the Add Widget button to add available widgets to your Header. Click the down arrow icon to expand widget options.', 'bloglo' ),
					'section'     => 'bloglo_section_main_header',
					'space'       => true,
				),
			);

			// Header widgets.
			$options['setting']['bloglo_header_widgets'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'bloglo_sanitize_widget',
				'control'           => array(
					'type'       => 'bloglo-widget',
					'label'      => esc_html__( 'Header Widgets', 'bloglo' ),
					'section'    => 'bloglo_section_main_header',
					'widgets'    => apply_filters(
						'bloglo_main_header_widgets',
						array(
							'search'   => array(
								'max_uses' => 1,
							),
							'darkmode' => array(
								'max_uses' => 1,
							),
							'button'   => array(
								'max_uses' => 1,
							),
							'socials'  => array(
								'max_uses' => 1,
								'styles'   => array(
									'minimal'      => esc_html__( 'Minimal', 'bloglo' ),
									'rounded'      => esc_html__( 'Rounded', 'bloglo' ),
									'minimal-fill' => esc_html__( 'Minimal Fill', 'bloglo' ),
									'rounded-fill' => esc_html__( 'Rounded Fill', 'bloglo' ),
								),
								'sizes'    => array(
									'small'    => esc_html__( 'Small', 'bloglo' ),
									'standard' => esc_html__( 'Standard', 'bloglo' ),
									'large'    => esc_html__( 'Large', 'bloglo' ),
									'xlarge'   => esc_html__( 'Extra Large', 'bloglo' ),
								),
							),
						)
					),
					'locations'  => array(
						'left'  => esc_html__( 'Left', 'bloglo' ),
						'right' => esc_html__( 'Right', 'bloglo' ),
					),
					'visibility' => array(
						'all'                => esc_html__( 'Show on All Devices', 'bloglo' ),
						'hide-mobile'        => esc_html__( 'Hide on Mobile', 'bloglo' ),
						'hide-tablet'        => esc_html__( 'Hide on Tablet', 'bloglo' ),
						'hide-mobile-tablet' => esc_html__( 'Hide on Mobile and Tablet', 'bloglo' ),
					),
					'required'   => array(
						array(
							'control'  => 'bloglo_header_heading_widgets',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
				'partial'           => array(
					'selector'            => '#bloglo-header',
					'render_callback'     => 'bloglo_header_content_output',
					'container_inclusive' => false,
					'fallback_refresh'    => true,
				),
			);

			return $options;
		}
	}
endif;
new Bloglo_Customizer_Main_Header();
