<?php
/**
 * Bloglo Misc section in Customizer.
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

if ( ! class_exists( 'Bloglo_Customizer_Misc' ) ) :
	/**
	 * Bloglo Misc section in Customizer.
	 */
	class Bloglo_Customizer_Misc {

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
			$options['section']['bloglo_section_misc'] = array(
				'title'    => esc_html__( 'Misc Settings', 'bloglo' ),
				'panel'    => 'bloglo_panel_general',
				'priority' => 60,
			);

			// Schema toggle.
			$options['setting']['bloglo_enable_schema'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'bloglo_sanitize_toggle',
				'control'           => array(
					'type'        => 'bloglo-toggle',
					'label'       => esc_html__( 'Schema Markup', 'bloglo' ),
					'description' => esc_html__( 'Add structured data to your content.', 'bloglo' ),
					'section'     => 'bloglo_section_misc',
				),
			);

			// Custom form styles.
			$options['setting']['bloglo_custom_input_style'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'bloglo_sanitize_toggle',
				'control'           => array(
					'type'        => 'bloglo-toggle',
					'label'       => esc_html__( 'Custom Form Styles', 'bloglo' ),
					'description' => esc_html__( 'Custom design for checkboxes and radio buttons.', 'bloglo' ),
					'section'     => 'bloglo_section_misc',
				),
			);

			// Scroll Top heading.
			$options['setting']['bloglo_scroll_top_heading'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'bloglo_sanitize_toggle',
				'control'           => array(
					'type'    => 'bloglo-heading',
					'label'   => esc_html__( 'Scroll Top Button', 'bloglo' ),
					'section' => 'bloglo_section_misc',
				),
			);

			// Enable/Disable Scroll Top.
			$options['setting']['bloglo_enable_scroll_top'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'bloglo_sanitize_toggle',
				'control'           => array(
					'type'        => 'bloglo-toggle',
					'label'       => esc_html__( 'Enable Scroll Top Button', 'bloglo' ),
					'description' => esc_html__( 'A sticky button that allows users to easily return to the top of a page.', 'bloglo' ),
					'section'     => 'bloglo_section_misc',
					'required'    => array(
						array(
							'control'  => 'bloglo_scroll_top_heading',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Cursor Dot heading.
			$options['setting']['bloglo_cursor_dot_heading'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'bloglo_sanitize_toggle',
				'control'           => array(
					'type'    => 'bloglo-heading',
					'label'   => esc_html__( 'Cursor Dot Effect', 'bloglo' ),
					'section' => 'bloglo_section_misc',
				),
			);

			// Enable/Disable Cursor Dot.
			$options['setting']['bloglo_enable_cursor_dot'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'bloglo_sanitize_toggle',
				'control'           => array(
					'type'        => 'bloglo-toggle',
					'label'       => esc_html__( 'Enable Cursor Dot', 'bloglo' ),
					'description' => esc_html__( 'A cursor dot effect show on desktop size mode only with work on mouse.', 'bloglo' ),
					'section'     => 'bloglo_section_misc',
					'required'    => array(
						array(
							'control'  => 'bloglo_cursor_dot_heading',
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
new Bloglo_Customizer_Misc();
