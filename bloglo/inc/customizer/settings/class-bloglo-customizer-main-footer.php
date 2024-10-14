<?php
/**
 * Bloglo Main Footer section in Customizer.
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

if ( ! class_exists( 'Bloglo_Customizer_Main_Footer' ) ) :
	/**
	 * Bloglo Main Footer section in Customizer.
	 */
	class Bloglo_Customizer_Main_Footer {

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
			$options['section']['bloglo_section_main_footer'] = array(
				'title'    => esc_html__( 'Main Footer', 'bloglo' ),
				'panel'    => 'bloglo_panel_footer',
				'priority' => 20,
			);

			// Enable Footer.
			$options['setting']['bloglo_enable_footer'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'bloglo_sanitize_toggle',
				'control'           => array(
					'type'    => 'bloglo-toggle',
					'label'   => esc_html__( 'Enable Main Footer', 'bloglo' ),
					'section' => 'bloglo_section_main_footer',
				),
			);

			// Footer Layout.
			$options['setting']['bloglo_footer_layout'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'bloglo_sanitize_select',
				'control'           => array(
					'type'        => 'bloglo-radio-image',
					'label'       => esc_html__( 'Column Layout', 'bloglo' ),
					'description' => esc_html__( 'Choose your site&rsquo;s footer column layout.', 'bloglo' ),
					'section'     => 'bloglo_section_main_footer',
					'choices'     => array(
						'layout-1' => array(
							'image' => BLOGLO_THEME_URI . '/inc/customizer/assets/images/footer-layout-1.svg',
							'title' => esc_html__( '1/4 + 1/4 + 1/4 + 1/4', 'bloglo' ),
						),
						'layout-2' => array(
							'image' => BLOGLO_THEME_URI . '/inc/customizer/assets/images/footer-layout-2.svg',
							'title' => esc_html__( '1/3 + 1/3 + 1/3', 'bloglo' ),
						),
						'layout-8' => array(
							'image' => BLOGLO_THEME_URI . '/inc/customizer/assets/images/footer-layout-8.svg',
							'title' => esc_html__( '1', 'bloglo' ),
						),
					),
					'required'    => array(
						array(
							'control'  => 'bloglo_enable_footer',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
				'partial'           => array(
					'selector'            => '#bloglo-footer-widgets',
					'render_callback'     => 'bloglo_footer_widgets',
					'container_inclusive' => false,
					'fallback_refresh'    => true,
				),
			);

			// Center footer widgets..
			$options['setting']['bloglo_footer_widgets_align_center'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'bloglo_sanitize_toggle',
				'control'           => array(
					'type'     => 'bloglo-toggle',
					'label'    => esc_html__( 'Center Widget Content', 'bloglo' ),
					'section'  => 'bloglo_section_main_footer',
					'required' => array(
						array(
							'control'  => 'bloglo_enable_footer',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
				'partial'           => array(
					'selector'            => '#bloglo-footer-widgets',
					'render_callback'     => 'bloglo_footer_widgets',
					'container_inclusive' => false,
					'fallback_refresh'    => true,
				),
			);

			// Footer Design Options heading.
			$options['setting']['bloglo_footer_heading_design_options'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'bloglo_sanitize_toggle',
				'control'           => array(
					'type'     => 'bloglo-heading',
					'label'    => esc_html__( 'Design Options', 'bloglo' ),
					'section'  => 'bloglo_section_main_footer',
					'required' => array(
						array(
							'control'  => 'bloglo_enable_footer',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Footer Background.
			$options['setting']['bloglo_footer_background'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'bloglo_sanitize_design_options',
				'control'           => array(
					'type'     => 'bloglo-design-options',
					'label'    => esc_html__( 'Background', 'bloglo' ),
					'section'  => 'bloglo_section_main_footer',
					'display'  => array(
						'background' => array(
							'color'    => esc_html__( 'Solid Color', 'bloglo' ),
							'gradient' => esc_html__( 'Gradient', 'bloglo' ),
							'image'    => esc_html__( 'Image', 'bloglo' ),
						),
					),
					'required' => array(
						array(
							'control'  => 'bloglo_enable_footer',
							'value'    => true,
							'operator' => '==',
						),
						array(
							'control'  => 'bloglo_footer_heading_design_options',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Footer Text Color.
			$options['setting']['bloglo_footer_text_color'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'bloglo_sanitize_design_options',
				'control'           => array(
					'type'     => 'bloglo-design-options',
					'label'    => esc_html__( 'Font Color', 'bloglo' ),
					'section'  => 'bloglo_section_main_footer',
					'display'  => array(
						'color' => array(
							'text-color'         => esc_html__( 'Text Color', 'bloglo' ),
							'link-color'         => esc_html__( 'Link Color', 'bloglo' ),
							'link-hover-color'   => esc_html__( 'Link Hover Color', 'bloglo' ),
							'widget-title-color' => esc_html__( 'Widget Title Color', 'bloglo' ),
						),
					),
					'required' => array(
						array(
							'control'  => 'bloglo_enable_footer',
							'value'    => true,
							'operator' => '==',
						),
						array(
							'control'  => 'bloglo_footer_heading_design_options',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Footer Border.
			$options['setting']['bloglo_footer_border'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'bloglo_sanitize_design_options',
				'control'           => array(
					'type'     => 'bloglo-design-options',
					'label'    => esc_html__( 'Border', 'bloglo' ),
					'section'  => 'bloglo_section_main_footer',
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
							'control'  => 'bloglo_enable_footer',
							'value'    => true,
							'operator' => '==',
						),
						array(
							'control'  => 'bloglo_footer_heading_design_options',
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
new Bloglo_Customizer_Main_Footer();
