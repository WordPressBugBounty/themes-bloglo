<?php
/**
 * Bloglo Copyright Bar section in Customizer.
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

if ( ! class_exists( 'Bloglo_Customizer_Copyright_Settings' ) ) :
	/**
	 * Bloglo Copyright Bar section in Customizer.
	 */
	class Bloglo_Customizer_Copyright_Settings {

		/**
		 * Primary class constructor.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {

			// Registers our custom options in Customizer.
			add_filter( 'bloglo_customizer_options', array( $this, 'register_options' ) );
		}

		/**
		 * Registers our custom options in Customizer.
		 *
		 * @since 1.0.0
		 * @param array $options Array of customizer options.
		 */
		public function register_options( $options ) {

			// Section.
			$options['section']['bloglo_section_copyright_bar'] = array(
				'title'    => esc_html__( 'Copyright Bar', 'bloglo' ),
				'priority' => 30,
				'panel'    => 'bloglo_panel_footer',
			);

			// Enable Copyright Bar.
			$options['setting']['bloglo_enable_copyright'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'bloglo_sanitize_toggle',
				'control'           => array(
					'type'    => 'bloglo-toggle',
					'label'   => esc_html__( 'Enable Copyright Bar', 'bloglo' ),
					'section' => 'bloglo_section_copyright_bar',
				),
			);

			// Copyright Layout.
			$options['setting']['bloglo_copyright_layout'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'bloglo_sanitize_select',
				'control'           => array(
					'type'        => 'bloglo-radio-image',
					'section'     => 'bloglo_section_copyright_bar',
					'label'       => esc_html__( 'Copyright Layout', 'bloglo' ),
					'description' => esc_html__( 'Choose your site&rsquo;s copyright widgets layout.', 'bloglo' ),
					'choices'     => array(
						'layout-1' => array(
							'image' => BLOGLO_THEME_URI . '/inc/customizer/assets/images/copyright-layout-1.svg',
							'title' => esc_html__( 'Centered', 'bloglo' ),
						),
					),
					'required'    => array(
						array(
							'control'  => 'bloglo_enable_copyright',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Copyright widgets heading.
			$options['setting']['bloglo_copyright_heading_widgets'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'bloglo_sanitize_toggle',
				'control'           => array(
					'type'        => 'bloglo-heading',
					'section'     => 'bloglo_section_copyright_bar',
					'label'       => esc_html__( 'Copyright Bar Widgets', 'bloglo' ),
					'description' => esc_html__( 'Click the Add Widget button to add available widgets to your Copyright Bar.', 'bloglo' ),
					'required'    => array(
						array(
							'control'  => 'bloglo_enable_copyright',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Copyright widgets.
			$options['setting']['bloglo_copyright_widgets'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'bloglo_sanitize_widget',
				'control'           => array(
					'type'       => 'bloglo-widget',
					'section'    => 'bloglo_section_copyright_bar',
					'label'      => esc_html__( 'Copyright Bar Widgets', 'bloglo' ),
					'widgets'    => array(
						'text' => array(
							'max_uses' => 1,
						),
						'nav'  => array(
							'menu_location' => apply_filters( 'bloglo_footer_menu_location', 'bloglo-footer' ),
							'max_uses'      => 1,
						),
					),
					'locations'  => array(
						'start' => esc_html__( 'Start', 'bloglo' ),
						'end'   => esc_html__( 'End', 'bloglo' ),
					),
					'visibility' => array(
						'all'                => esc_html__( 'Show on All Devices', 'bloglo' ),
						'hide-mobile'        => esc_html__( 'Hide on Mobile', 'bloglo' ),
						'hide-tablet'        => esc_html__( 'Hide on Tablet', 'bloglo' ),
						'hide-mobile-tablet' => esc_html__( 'Hide on Mobile and Tablet', 'bloglo' ),
					),
					'required'   => array(
						array(
							'control'  => 'bloglo_copyright_heading_widgets',
							'value'    => true,
							'operator' => '==',
						),
						array(
							'control'  => 'bloglo_enable_copyright',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
				'partial'           => array(
					'selector'            => '#bloglo-copyright',
					'render_callback'     => 'bloglo_copyright_bar_output',
					'container_inclusive' => true,
					'fallback_refresh'    => true,
				),
			);

			// Copyright design options heading.
			$options['setting']['bloglo_copyright_heading_design_options'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'bloglo_sanitize_toggle',
				'control'           => array(
					'type'     => 'bloglo-heading',
					'section'  => 'bloglo_section_copyright_bar',
					'label'    => esc_html__( 'Design Options', 'bloglo' ),
					'required' => array(
						array(
							'control'  => 'bloglo_enable_copyright',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Copyright Background.
			$options['setting']['bloglo_copyright_background'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'bloglo_sanitize_design_options',
				'control'           => array(
					'type'     => 'bloglo-design-options',
					'section'  => 'bloglo_section_copyright_bar',
					'label'    => esc_html__( 'Background', 'bloglo' ),
					'space'    => true,
					'display'  => array(
						'background' => array(
							'color'    => esc_html__( 'Solid Color', 'bloglo' ),
							'gradient' => esc_html__( 'Gradient', 'bloglo' ),
						),
					),
					'required' => array(
						array(
							'control'  => 'bloglo_copyright_heading_design_options',
							'value'    => true,
							'operator' => '==',
						),
						array(
							'control'  => 'bloglo_enable_copyright',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Copyright Text Color.
			$options['setting']['bloglo_copyright_text_color'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'bloglo_sanitize_design_options',
				'control'           => array(
					'type'        => 'bloglo-design-options',
					'section'     => 'bloglo_section_copyright_bar',
					'label'       => esc_html__( 'Font Color', 'bloglo' ),
					'description' => '',
					'space'       => true,
					'display'     => array(
						'color' => array(
							'text-color'       => esc_html__( 'Text Color', 'bloglo' ),
							'link-color'       => esc_html__( 'Link Color', 'bloglo' ),
							'link-hover-color' => esc_html__( 'Link Hover Color', 'bloglo' ),
						),
					),
					'required'    => array(
						array(
							'control'  => 'bloglo_copyright_heading_design_options',
							'value'    => true,
							'operator' => '==',
						),
						array(
							'control'  => 'bloglo_enable_copyright',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			return $options;
		}

	}
endif;
new Bloglo_Customizer_Copyright_Settings();
