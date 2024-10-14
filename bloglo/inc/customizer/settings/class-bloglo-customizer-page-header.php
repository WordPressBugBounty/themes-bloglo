<?php
/**
 * Bloglo Page Title Settings section in Customizer.
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

if ( ! class_exists( 'Bloglo_Customizer_Page_Header' ) ) :
	/**
	 * Bloglo Page Title Settings section in Customizer.
	 */
	class Bloglo_Customizer_Page_Header {

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

			// Page Title Section.
			$options['section']['bloglo_section_page_header'] = array(
				'title'    => esc_html__( 'Page Header', 'bloglo' ),
				'panel'    => 'bloglo_panel_header',
				'priority' => 60,
			);

			// Page Header enable.
			$options['setting']['bloglo_page_header_enable'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'bloglo_sanitize_toggle',
				'control'           => array(
					'type'    => 'bloglo-toggle',
					'label'   => esc_html__( 'Enable Page Header', 'bloglo' ),
					'section' => 'bloglo_section_page_header',
				),
			);

			// Spacing.
			$options['setting']['bloglo_page_header_spacing'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'bloglo_sanitize_responsive',
				'control'           => array(
					'type'        => 'bloglo-spacing',
					'label'       => esc_html__( 'Page Title Spacing', 'bloglo' ),
					'description' => esc_html__( 'Specify Page Title top and bottom padding.', 'bloglo' ),
					'section'     => 'bloglo_section_page_header',
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
							'control'  => 'bloglo_page_header_enable',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Page Header design options heading.
			$options['setting']['bloglo_page_header_heading_design'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'bloglo_sanitize_toggle',
				'control'           => array(
					'type'     => 'bloglo-heading',
					'label'    => esc_html__( 'Design Options', 'bloglo' ),
					'section'  => 'bloglo_section_page_header',
					'required' => array(
						array(
							'control'  => 'bloglo_page_header_enable',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Page Header background design.
			$options['setting']['bloglo_page_header_background'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'bloglo_sanitize_design_options',
				'control'           => array(
					'type'     => 'bloglo-design-options',
					'label'    => esc_html__( 'Background', 'bloglo' ),
					'section'  => 'bloglo_section_page_header',
					'display'  => array(
						'background' => array(
							'color'    => esc_html__( 'Solid Color', 'bloglo' ),
							'gradient' => esc_html__( 'Gradient', 'bloglo' ),
							'image'    => esc_html__( 'Image', 'bloglo' ),
						),
					),
					'required' => array(
						array(
							'control'  => 'bloglo_page_header_enable',
							'value'    => true,
							'operator' => '==',
						),
						array(
							'control'  => 'bloglo_page_header_heading_design',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Page Header Text Color.
			$options['setting']['bloglo_page_header_text_color'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'bloglo_sanitize_design_options',
				'control'           => array(
					'type'     => 'bloglo-design-options',
					'label'    => esc_html__( 'Font Color', 'bloglo' ),
					'section'  => 'bloglo_section_page_header',
					'display'  => array(
						'color' => array(
							'text-color'       => esc_html__( 'Text Color', 'bloglo' ),
							'link-color'       => esc_html__( 'Link Color', 'bloglo' ),
							'link-hover-color' => esc_html__( 'Link Hover Color', 'bloglo' ),
						),
					),
					'required' => array(
						array(
							'control'  => 'bloglo_page_header_enable',
							'value'    => true,
							'operator' => '==',
						),
						array(
							'control'  => 'bloglo_page_header_heading_design',
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
new Bloglo_Customizer_Page_Header();
