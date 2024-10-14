<?php
/**
 * Bloglo Base Colors section in Customizer.
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

if ( ! class_exists( 'Bloglo_Customizer_Colors' ) ) :
	/**
	 * Bloglo Colors section in Customizer.
	 */
	class Bloglo_Customizer_Colors {

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
			$options['section']['bloglo_section_colors'] = array(
				'title'    => esc_html__( 'Base Colors', 'bloglo' ),
				'panel'    => 'bloglo_panel_general',
				'priority' => 20,
			);

			// Accent color.
			$options['setting']['bloglo_accent_color'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'bloglo_sanitize_color',
				'control'           => array(
					'type'        => 'bloglo-color',
					'label'       => esc_html__( 'Accent Color', 'bloglo' ),
					'description' => esc_html__( 'The accent color is used subtly throughout your site, to call attention to key elements.', 'bloglo' ),
					'section'     => 'bloglo_section_colors',
					'priority'    => 10,
					'opacity'     => false,
				),
			);

			// Dark mode
			$options['setting']['bloglo_dark_mode'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'bloglo_sanitize_toggle',
				'control'           => array(
					'type'        => 'bloglo-toggle',
					'label'       => esc_html__( 'Dark mode', 'bloglo' ),
					'description' => esc_html__( 'Enable dark mode.', 'bloglo' ),
					'section'     => 'bloglo_section_colors',
					'priority'    => 11,
				),
			);

			// Body background heading.
			$options['setting']['bloglo_body_background_heading'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'bloglo_sanitize_toggle',
				'control'           => array(
					'type'     => 'bloglo-heading',
					'priority' => 40,
					'label'    => esc_html__( 'Body Background', 'bloglo' ),
					'section'  => 'bloglo_section_colors',
					'toggle'   => false,
				),
			);

			return $options;
		}

	}
endif;
new Bloglo_Customizer_Colors();
