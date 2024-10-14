<?php
/**
 * Bloglo Logo section in Customizer.
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

if ( ! class_exists( 'Bloglo_Customizer_Logo' ) ) :
	/**
	 * Bloglo Logo section in Customizer.
	 */
	class Bloglo_Customizer_Logo {

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

			// Logo Retina.
			$options['setting']['bloglo_logo_default_retina'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'bloglo_sanitize_background',
				'control'           => array(
					'type'        => 'bloglo-background',
					'section'     => 'title_tagline',
					'label'       => esc_html__( 'Retina Logo', 'bloglo' ),
					'description' => esc_html__( 'Upload exactly 2x the size of your default logo to make your logo crisp on HiDPI screens. This options is not required if logo above is in SVG format.', 'bloglo' ),
					'priority'    => 20,
					'advanced'    => false,
					'strings'     => array(
						'select_image' => __( 'Select logo', 'bloglo' ),
						'use_image'    => __( 'Select', 'bloglo' ),
					),
					'required'    => array(
						array(
							'control'  => 'custom_logo',
							'value'    => false,
							'operator' => '!=',
						),
					),
				),
				'partial'           => array(
					'selector'            => '.bloglo-logo',
					'render_callback'     => 'bloglo_logo',
					'container_inclusive' => false,
					'fallback_refresh'    => true,
				),
			);

			// Logo Max Height.
			$options['setting']['bloglo_logo_max_height'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'bloglo_sanitize_responsive',
				'control'           => array(
					'type'        => 'bloglo-range',
					'label'       => esc_html__( 'Logo Height', 'bloglo' ),
					'description' => esc_html__( 'Maximum logo image height.', 'bloglo' ),
					'section'     => 'title_tagline',
					'priority'    => 30,
					'min'         => 0,
					'max'         => 1000,
					'step'        => 10,
					'unit'        => 'px',
					'responsive'  => true,
					'required'    => array(
						array(
							'control'  => 'custom_logo',
							'value'    => false,
							'operator' => '!=',
						),
					),
				),
			);

			// Logo margin.
			$options['setting']['bloglo_logo_margin'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'bloglo_sanitize_responsive',
				'control'           => array(
					'type'        => 'bloglo-spacing',
					'label'       => esc_html__( 'Logo Margin', 'bloglo' ),
					'description' => esc_html__( 'Specify spacing around logo. Negative values are allowed.', 'bloglo' ),
					'section'     => 'title_tagline',
					'settings'    => 'bloglo_logo_margin',
					'priority'    => 40,
					'choices'     => array(
						'top'    => esc_html__( 'Top', 'bloglo' ),
						'right'  => esc_html__( 'Right', 'bloglo' ),
						'bottom' => esc_html__( 'Bottom', 'bloglo' ),
						'left'   => esc_html__( 'Left', 'bloglo' ),
					),
					'responsive'  => true,
					'unit'        => array(
						'px',
					),
				),
			);

			// Show tagline.
			$options['setting']['bloglo_display_tagline'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'bloglo_sanitize_toggle',
				'control'           => array(
					'type'     => 'bloglo-toggle',
					'label'    => esc_html__( 'Display Tagline', 'bloglo' ),
					'section'  => 'title_tagline',
					'settings' => 'bloglo_display_tagline',
					'priority' => 80,
				),
				'partial'           => array(
					'selector'            => '.bloglo-logo',
					'render_callback'     => 'bloglo_logo',
					'container_inclusive' => false,
					'fallback_refresh'    => true,
				),
			);

			// Site Identity heading.
			$options['setting']['bloglo_logo_heading_site_identity'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'bloglo_sanitize_toggle',
				'control'           => array(
					'type'     => 'bloglo-heading',
					'label'    => esc_html__( 'Site Identity', 'bloglo' ),
					'section'  => 'title_tagline',
					'settings' => 'bloglo_logo_heading_site_identity',
					'priority' => 50,
					'toggle'   => false,
				),
			);

			// Logo typography heading.
			$options['setting']['bloglo_typography_logo_heading'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'bloglo_sanitize_toggle',
				'control'           => array(
					'type'     => 'bloglo-heading',
					'label'    => esc_html__( 'Typography', 'bloglo' ),
					'section'  => 'title_tagline',
					'priority' => 100,
					'required' => array(
						array(
							'control'  => 'custom_logo',
							'value'    => false,
							'operator' => '==',
						),
					),
				),
			);

			// Site title font size.
			$options['setting']['bloglo_logo_text_font_size'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'bloglo_sanitize_responsive',
				'control'           => array(
					'type'       => 'bloglo-range',
					'label'      => esc_html__( 'Site Title Font Size', 'bloglo' ),
					'section'    => 'title_tagline',
					'priority'   => 100,
					'min'        => 8,
					'max'        => 30,
					'step'       => 1,
					'responsive' => true,
					'unit'       => array(
						array(
							'id'   => 'px',
							'name' => 'px',
							'min'  => 8,
							'max'  => 90,
							'step' => 1,
						),
						array(
							'id'   => 'em',
							'name' => 'em',
							'min'  => 0.5,
							'max'  => 5,
							'step' => 0.01,
						),
						array(
							'id'   => 'rem',
							'name' => 'rem',
							'min'  => 0.5,
							'max'  => 5,
							'step' => 0.01,
						),
					),
					'required'   => array(
						array(
							'control'  => 'custom_logo',
							'value'    => false,
							'operator' => '==',
						),
						array(
							'control'  => 'bloglo_typography_logo_heading',
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
new Bloglo_Customizer_Logo();
