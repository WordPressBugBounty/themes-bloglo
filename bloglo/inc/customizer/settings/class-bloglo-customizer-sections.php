<?php
/**
 * Bloglo Customizer sections and panels.
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

if ( ! class_exists( 'Bloglo_Customizer_Sections' ) ) :
	/**
	 * Bloglo Customizer sections and panels.
	 */
	class Bloglo_Customizer_Sections {

		/**
		 * Primary class constructor.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {

			/**
			 * Registers our custom panels in Customizer.
			 */
			add_filter( 'bloglo_customizer_options', array( $this, 'register_panel' ) );
		}

		/**
		 * Registers our custom options in Customizer.
		 *
		 * @since 1.0.0
		 * @param array $options Array of customizer options.
		 */
		public function register_panel( $options ) {

			// General panel.
			$options['panel']['bloglo_panel_general'] = array(
				'title'    => esc_html__( 'General Settings', 'bloglo' ),
				'priority' => 1,
			);

			// Homapepage panel.
			$options['panel']['bloglo_panel_homepage'] = array(
				'title'    => esc_html__( 'Homapage sections', 'bloglo' ),
				'priority' => 3,
			);

			// Header panel.
			$options['panel']['bloglo_panel_header'] = array(
				'title'    => esc_html__( 'Header', 'bloglo' ),
				'priority' => 3,
			);

			// Footer panel.
			$options['panel']['bloglo_panel_footer'] = array(
				'title'    => esc_html__( 'Footer', 'bloglo' ),
				'priority' => 5,
			);

			// Blog settings.
			$options['panel']['bloglo_panel_blog'] = array(
				'title'    => esc_html__( 'Blog', 'bloglo' ),
				'priority' => 6,
			);

			return $options;
		}
	}
endif;
new Bloglo_Customizer_Sections();
