<?php
/**
 * Bloglo Main Navigation Settings section in Customizer.
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

if ( ! class_exists( 'Bloglo_Customizer_Main_Navigation' ) ) :
	/**
	 * Bloglo Main Navigation Settings section in Customizer.
	 */
	class Bloglo_Customizer_Main_Navigation {

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
			$options['section']['bloglo_section_main_navigation'] = array(
				'title'    => esc_html__( 'Main Navigation', 'bloglo' ),
				'panel'    => 'bloglo_panel_header',
				'priority' => 30,
			);

			// Mobile Menu heading.
			$options['setting']['bloglo_main_nav_heading_mobile_menu'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'bloglo_sanitize_toggle',
				'control'           => array(
					'type'    => 'bloglo-heading',
					'label'   => esc_html__( 'Mobile Menu', 'bloglo' ),
					'section' => 'bloglo_section_main_navigation',
				),
			);

			// Mobile Menu Breakpoint.
			$options['setting']['bloglo_main_nav_mobile_breakpoint'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'bloglo_sanitize_range',
				'control'           => array(
					'type'        => 'bloglo-range',
					'label'       => esc_html__( 'Mobile Breakpoint', 'bloglo' ),
					'description' => esc_html__( 'Choose the breakpoint (in px) when to show the mobile navigation.', 'bloglo' ),
					'section'     => 'bloglo_section_main_navigation',
					'min'         => 0,
					'max'         => 1920,
					'step'        => 1,
					'unit'        => 'px',
					'required'    => array(
						array(
							'control'  => 'bloglo_main_nav_heading_mobile_menu',
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
new Bloglo_Customizer_Main_Navigation();
