<?php
/**
 * Bloglo Top Bar Settings section in Customizer.
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

if ( ! class_exists( 'Bloglo_Customizer_Top_Bar' ) ) :
	/**
	 * Bloglo Top Bar Settings section in Customizer.
	 */
	class Bloglo_Customizer_Top_Bar {

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

			// Section.
			$options['section']['bloglo_section_top_bar'] = array(
				'title'    => esc_html__( 'Top Bar', 'bloglo' ),
				'panel'    => 'bloglo_panel_header',
				'priority' => 10,
			);

			// Enable Top Bar.
			$options['setting']['bloglo_top_bar_enable'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'bloglo_sanitize_toggle',
				'control'           => array(
					'type'        => 'bloglo-toggle',
					'label'       => esc_html__( 'Enable Top Bar', 'bloglo' ),
					'description' => esc_html__( 'Top Bar is a section with widgets located above Main Header area.', 'bloglo' ),
					'section'     => 'bloglo_section_top_bar',
				),
			);

			// Top Bar widgets heading.
			$options['setting']['bloglo_top_bar_heading_widgets'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'bloglo_sanitize_toggle',
				'control'           => array(
					'type'        => 'bloglo-heading',
					'label'       => esc_html__( 'Top Bar Widgets', 'bloglo' ),
					'description' => esc_html__( 'Click the Add Widget button to add available widgets to your Top Bar.', 'bloglo' ),
					'section'     => 'bloglo_section_top_bar',
					'required'    => array(
						array(
							'control'  => 'bloglo_top_bar_enable',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Top Bar widgets.
			$options['setting']['bloglo_top_bar_widgets'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'bloglo_sanitize_widget',
				'control'           => array(
					'type'       => 'bloglo-widget',
					'label'      => esc_html__( 'Top Bar Widgets', 'bloglo' ),
					'section'    => 'bloglo_section_top_bar',
					'widgets'    => array(
						'text'    => array(
							'max_uses' => 2,
						),
						'nav'     => array(
							'max_uses' => 1,
						),
						'socials' => array(
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
							'control'  => 'bloglo_top_bar_heading_widgets',
							'value'    => true,
							'operator' => '==',
						),
						array(
							'control'  => 'bloglo_top_bar_enable',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
				'partial'           => array(
					'selector'            => '#bloglo-topbar',
					'render_callback'     => 'bloglo_topbar_output',
					'container_inclusive' => true,
					'fallback_refresh'    => true,
				),
			);

			// Top Bar design options heading.
			$options['setting']['bloglo_top_bar_heading_design_options'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'bloglo_sanitize_toggle',
				'control'           => array(
					'type'     => 'bloglo-heading',
					'label'    => esc_html__( 'Design Options', 'bloglo' ),
					'section'  => 'bloglo_section_top_bar',
					'required' => array(
						array(
							'control'  => 'bloglo_top_bar_enable',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Top Bar Background.
			$options['setting']['bloglo_top_bar_background'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'bloglo_sanitize_design_options',
				'control'           => array(
					'type'     => 'bloglo-design-options',
					'label'    => esc_html__( 'Background', 'bloglo' ),
					'section'  => 'bloglo_section_top_bar',
					'display'  => array(
						'background' => array(
							'color'    => esc_html__( 'Solid Color', 'bloglo' ),
							'gradient' => esc_html__( 'Gradient', 'bloglo' ),
						),
					),
					'required' => array(
						array(
							'control'  => 'bloglo_top_bar_enable',
							'value'    => true,
							'operator' => '==',
						),
						array(
							'control'  => 'bloglo_top_bar_heading_design_options',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Top Bar Text Color.
			$options['setting']['bloglo_top_bar_text_color'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'bloglo_sanitize_design_options',
				'control'           => array(
					'type'     => 'bloglo-design-options',
					'label'    => esc_html__( 'Font Color', 'bloglo' ),
					'section'  => 'bloglo_section_top_bar',
					'display'  => array(
						'color' => array(
							'text-color'       => esc_html__( 'Text Color', 'bloglo' ),
							'link-color'       => esc_html__( 'Link Color', 'bloglo' ),
							'link-hover-color' => esc_html__( 'Link Hover Color', 'bloglo' ),
						),
					),
					'required' => array(
						array(
							'control'  => 'bloglo_top_bar_enable',
							'value'    => true,
							'operator' => '==',
						),
						array(
							'control'  => 'bloglo_top_bar_heading_design_options',
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
new Bloglo_Customizer_Top_Bar();
