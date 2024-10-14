<?php
/**
 * Bloglo Sticky Header Settings section in Customizer.
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

if ( ! class_exists( 'Bloglo_Customizer_Sticky_Header' ) ) :
	/**
	 * Bloglo Sticky Header section in Customizer.
	 */
	class Bloglo_Customizer_Sticky_Header {

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

			// Sticky Header Section.
			$options['section']['bloglo_section_sticky_header'] = array(
				'title'    => esc_html__( 'Sticky Header', 'bloglo' ),
				'panel'    => 'bloglo_panel_header',
				'priority' => 80,
			);

			// Enable Transparent Header.
			$options['setting']['bloglo_sticky_header'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'bloglo_sanitize_toggle',
				'control'           => array(
					'type'    => 'bloglo-toggle',
					'label'   => esc_html__( 'Enable Sticky Header', 'bloglo' ),
					'section' => 'bloglo_section_sticky_header',
				),
			);

			return $options;
		}
	}
endif;
new Bloglo_Customizer_Sticky_Header();
