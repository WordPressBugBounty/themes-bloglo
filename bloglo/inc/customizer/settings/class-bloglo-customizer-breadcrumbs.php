<?php
/**
 * Bloglo Breadcrumbs Settings section in Customizer.
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

if ( ! class_exists( 'Bloglo_Customizer_Breadcrumbs' ) ) :
	/**
	 * Bloglo Breadcrumbs Settings section in Customizer.
	 */
	class Bloglo_Customizer_Breadcrumbs {

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

			// Main Navigation Section.
			$options['section']['bloglo_section_breadcrumbs'] = array(
				'title'    => esc_html__( 'Breadcrumbs', 'bloglo' ),
				'panel'    => 'bloglo_panel_header',
				'priority' => 70,
			);

			// Breadcrumbs.
			$options['setting']['bloglo_breadcrumbs_enable'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'bloglo_sanitize_toggle',
				'control'           => array(
					'type'    => 'bloglo-toggle',
					'label'   => esc_html__( 'Enable Breadcrumbs', 'bloglo' ),
					'section' => 'bloglo_section_breadcrumbs',
				),
			);

			// Hide breadcrumbs on.
			$options['setting']['bloglo_breadcrumbs_hide_on'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'bloglo_no_sanitize',
				'control'           => array(
					'type'        => 'bloglo-checkbox-group',
					'label'       => esc_html__( 'Disable On: ', 'bloglo' ),
					'description' => esc_html__( 'Choose on which pages you want to disable breadcrumbs. ', 'bloglo' ),
					'section'     => 'bloglo_section_breadcrumbs',
					'choices'     => bloglo_get_display_choices(),
					'required'    => array(
						array(
							'control'  => 'bloglo_breadcrumbs_enable',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Position.
			$options['setting']['bloglo_breadcrumbs_position'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'bloglo_sanitize_select',
				'control'           => array(
					'type'     => 'bloglo-select',
					'label'    => esc_html__( 'Position', 'bloglo' ),
					'section'  => 'bloglo_section_breadcrumbs',
					'choices'  => array(
						'in-page-header' => esc_html__( 'In Page Header', 'bloglo' ),
						'below-header'   => esc_html__( 'Below Header (Separate Container)', 'bloglo' ),
					),
					'required' => array(
						array(
							'control'  => 'bloglo_breadcrumbs_enable',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Alignment.
			$options['setting']['bloglo_breadcrumbs_alignment'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'bloglo_sanitize_select',
				'control'           => array(
					'type'     => 'bloglo-alignment',
					'label'    => esc_html__( 'Alignment', 'bloglo' ),
					'section'  => 'bloglo_section_breadcrumbs',
					'choices'  => 'horizontal',
					'icons'    => array(
						'left'   => 'dashicons dashicons-editor-alignleft',
						'center' => 'dashicons dashicons-editor-aligncenter',
						'right'  => 'dashicons dashicons-editor-alignright',
					),
					'required' => array(
						array(
							'control'  => 'bloglo_breadcrumbs_enable',
							'value'    => true,
							'operator' => '==',
						),
						array(
							'control'  => 'bloglo_breadcrumbs_position',
							'value'    => 'below-header',
							'operator' => '==',
						),
					),
				),
			);

			// Spacing.
			$options['setting']['bloglo_breadcrumbs_spacing'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'bloglo_sanitize_responsive',
				'control'           => array(
					'type'        => 'bloglo-spacing',
					'label'       => esc_html__( 'Spacing', 'bloglo' ),
					'description' => esc_html__( 'Specify top and bottom padding.', 'bloglo' ),
					'section'     => 'bloglo_section_breadcrumbs',
					'choices'     => array(
						'top'    => esc_html__( 'Top', 'bloglo' ),
						'bottom' => esc_html__( 'Bottom', 'bloglo' ),
					),
					'responsive'  => true,
					'unit'        => array(
						'px',
					),
					'required'    => array(
						array(
							'control'  => 'bloglo_breadcrumbs_enable',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Design options heading.
			$options['setting']['bloglo_breadcrumbs_heading_design'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'bloglo_sanitize_toggle',
				'control'           => array(
					'type'     => 'bloglo-heading',
					'label'    => esc_html__( 'Design Options', 'bloglo' ),
					'section'  => 'bloglo_section_breadcrumbs',
					'required' => array(
						array(
							'control'  => 'bloglo_breadcrumbs_enable',
							'value'    => true,
							'operator' => '==',
						),
						array(
							'control'  => 'bloglo_breadcrumbs_position',
							'value'    => 'below-header',
							'operator' => '==',
						),
					),
				),
			);

			// Background design.
			$options['setting']['bloglo_breadcrumbs_background'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'bloglo_sanitize_design_options',
				'control'           => array(
					'type'     => 'bloglo-design-options',
					'label'    => esc_html__( 'Background', 'bloglo' ),
					'section'  => 'bloglo_section_breadcrumbs',
					'display'  => array(
						'background' => array(
							'color'    => esc_html__( 'Solid Color', 'bloglo' ),
							'gradient' => esc_html__( 'Gradient', 'bloglo' ),
							'image'    => esc_html__( 'Image', 'bloglo' ),
						),
					),
					'required' => array(
						array(
							'control'  => 'bloglo_breadcrumbs_enable',
							'value'    => true,
							'operator' => '==',
						),
						array(
							'control'  => 'bloglo_breadcrumbs_heading_design',
							'value'    => true,
							'operator' => '==',
						),
						array(
							'control'  => 'bloglo_breadcrumbs_position',
							'value'    => 'below-header',
							'operator' => '==',
						),
					),
				),
			);

			// Text Color.
			$options['setting']['bloglo_breadcrumbs_text_color'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'bloglo_sanitize_design_options',
				'control'           => array(
					'type'     => 'bloglo-design-options',
					'label'    => esc_html__( 'Font Color', 'bloglo' ),
					'section'  => 'bloglo_section_breadcrumbs',
					'display'  => array(
						'color' => array(
							'text-color'       => esc_html__( 'Text Color', 'bloglo' ),
							'link-color'       => esc_html__( 'Link Color', 'bloglo' ),
							'link-hover-color' => esc_html__( 'Link Hover Color', 'bloglo' ),
						),
					),
					'required' => array(
						array(
							'control'  => 'bloglo_breadcrumbs_enable',
							'value'    => true,
							'operator' => '==',
						),
						array(
							'control'  => 'bloglo_breadcrumbs_heading_design',
							'value'    => true,
							'operator' => '==',
						),
						array(
							'control'  => 'bloglo_breadcrumbs_position',
							'value'    => 'below-header',
							'operator' => '==',
						),
					),
				),
			);

			// Border.
			$options['setting']['bloglo_breadcrumbs_border'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'bloglo_sanitize_design_options',
				'control'           => array(
					'type'     => 'bloglo-design-options',
					'label'    => esc_html__( 'Border', 'bloglo' ),
					'section'  => 'bloglo_section_breadcrumbs',
					'display'  => array(
						'border' => array(
							'style'     => esc_html__( 'Style', 'bloglo' ),
							'color'     => esc_html__( 'Color', 'bloglo' ),
							'width'     => esc_html__( 'Width (px)', 'bloglo' ),
							'positions' => array(
								'top'    => esc_html__( 'Top', 'bloglo' ),
								'bottom' => esc_html__( 'Bottom', 'bloglo' ),
							),
						),
					),
					'required' => array(
						array(
							'control'  => 'bloglo_breadcrumbs_enable',
							'value'    => true,
							'operator' => '==',
						),
						array(
							'control'  => 'bloglo_breadcrumbs_heading_design',
							'value'    => true,
							'operator' => '==',
						),
						array(
							'control'  => 'bloglo_breadcrumbs_position',
							'value'    => 'below-header',
							'operator' => '==',
						),
					),
				),
			);

			return $options;
		}
	}
endif;
new Bloglo_Customizer_Breadcrumbs();
