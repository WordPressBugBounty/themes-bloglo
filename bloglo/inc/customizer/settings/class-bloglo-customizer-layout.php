<?php
/**
 * Bloglo Layout section in Customizer.
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

if ( ! class_exists( 'Bloglo_Customizer_Layout' ) ) :
	/**
	 * Bloglo Layout section in Customizer.
	 */
	class Bloglo_Customizer_Layout {

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
			$options['section']['bloglo_layout_section'] = array(
				'title'    => esc_html__( 'Layout', 'bloglo' ),
				'panel'    => 'bloglo_panel_general',
				'priority' => 10,
			);

			// Site layout.
			$options['setting']['bloglo_site_layout'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'bloglo_sanitize_select',
				'control'           => array(
					'type'        => 'bloglo-select',
					'section'     => 'bloglo_layout_section',
					'label'       => esc_html__( 'Site Layout', 'bloglo' ),
					'description' => esc_html__( 'Choose your site&rsquo;s main layout.', 'bloglo' ),
					'choices'     => array(
						'fw-contained' => esc_html__( 'Full Width: Contained', 'bloglo' ),
						'fw-stretched' => esc_html__( 'Full Width: Stretched', 'bloglo' ),
					),
				),
			);

			return $options;
		}
	}
endif;
new Bloglo_Customizer_Layout();
