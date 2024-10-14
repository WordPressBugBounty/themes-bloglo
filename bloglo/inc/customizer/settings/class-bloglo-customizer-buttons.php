<?php
/**
 * Buttons section in Customizer » General Settings.
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

if ( ! class_exists( 'Bloglo_Customizer_Buttons' ) ) :
	/**
	 * Buttons section in Customizer » General Settings.
	 */
	class Bloglo_Customizer_Buttons {

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

			$theme = wp_get_theme();
			// Upsell section
			$options['section']['bloglo_section_upsell_button'] = array(
				'class'    => 'Bloglo_Customizer_Control_Section_Pro',
				'title'    => esc_html__( 'Need more features?', 'bloglo' ),
				'pro_url'  => sprintf( esc_url_raw( 'https://peregrine-themes.com/%s' ), strtolower( $theme->name ) ),
				'pro_text' => esc_html__( 'Upgrade to pro', 'bloglo' ),
				'priority' => 60,
			);

			$options['setting']['bloglo_section_upsell_heading'] = array(
				'control' => array(
					'type'    => 'hidden',
					'section' => 'bloglo_section_upsell_button',
				),
			);
			// Docs link
			$options['section']['bloglo_section_docs_button'] = array(
				'class'    => 'Bloglo_Customizer_Control_Section_Pro',
				'title'    => esc_html__( 'Need Help?', 'bloglo' ),
				'pro_url'  => esc_url_raw( 'http://docs.peregrine-themes.com/' ),
				'pro_text' => esc_html__( 'See the Articles', 'bloglo' ),
				'priority' => 60,
			);

			$options['setting']['bloglo_section_docs_heading'] = array(
				'control' => array(
					'type'    => 'hidden',
					'section' => 'bloglo_section_docs_button',
				),
			);

			return $options;
		}
	}
endif;
new Bloglo_Customizer_Buttons();
