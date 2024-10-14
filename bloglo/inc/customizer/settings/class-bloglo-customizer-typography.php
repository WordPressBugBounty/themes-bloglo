<?php
/**
 * Bloglo Base Typography section in Customizer.
 *
 * @package Bloglo
 * @author Peregrine Themes
 * @since   1.0.0
 */

/**
 * Do not allow direct script access.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Bloglo_Customizer_Typography' ) ) :
	/**
	 * Bloglo Typography section in Customizer.
	 */
	class Bloglo_Customizer_Typography {

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
			$options['section']['bloglo_section_typography'] = array(
				'title'    => esc_html__( 'Base Typography', 'bloglo' ),
				'panel'    => 'bloglo_panel_general',
				'priority' => 30,
			);

			// Headings typography heading.
			$options['setting']['bloglo_typography_body_heading'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'bloglo_sanitize_toggle',
				'control'           => array(
					'type'    => 'bloglo-heading',
					'label'   => esc_html__( 'Body & Content', 'bloglo' ),
					'section' => 'bloglo_section_typography',
				),
			);

			// Body Font.
			$options['setting']['bloglo_body_font'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'bloglo_sanitize_typography',
				'control'           => array(
					'type'     => 'bloglo-typography',
					'label'    => esc_html__( 'Body Typography', 'bloglo' ),
					'section'  => 'bloglo_section_typography',
					'display'  => array(
						'font-family'     => array(),
						'font-subsets'    => array(),
						'font-weight'     => array(),
						'font-style'      => array(),
						'text-transform'  => array(),
						'text-decoration' => array(),
						'letter-spacing'  => array(),
						'font-size'       => array(),
						'line-height'     => array(),
					),
					'required' => array(
						array(
							'control'  => 'bloglo_typography_body_heading',
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
new Bloglo_Customizer_Typography();
