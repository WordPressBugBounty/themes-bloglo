<?php
/**
 * Bloglo compatibility class for Header Footer Elementor plugin.
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

// Return if Elementor not active.
if ( ! class_exists( '\Elementor\Plugin' ) ) {
	return;
}

// Return if HFE not active.
if ( ! class_exists( 'Header_Footer_Elementor' ) ) {
	return false;
}

if ( ! class_exists( 'Bloglo_HFE' ) ) :

	/**
	 * HFE compatibility.
	 */
	class Bloglo_HFE {

		/**
		 * Singleton instance of the class.
		 *
		 * @var object
		 */
		private static $instance;

		/**
		 * Instance.
		 *
		 * @since 1.0.0
		 * @return Bloglo_HFE
		 */
		public static function instance() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Bloglo_HFE ) ) {
				self::$instance = new Bloglo_HFE();
			}
			return self::$instance;
		}

		/**
		 * Primary class constructor.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function __construct() {
			add_action( 'after_setup_theme', array( $this, 'add_theme_support' ) );
			add_action( 'bloglo_header', array( $this, 'do_header' ), 0 );
			add_action( 'bloglo_footer', array( $this, 'do_footer' ), 0 );
		}

		/**
		 * Add theme support
		 *
		 * @since 1.0.0
		 */
		public function add_theme_support() {
			add_theme_support( 'header-footer-elementor' );
		}

		/**
		 * Override Header
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function do_header() {
			if ( ! hfe_header_enabled() ) {
				return;
			}

			hfe_render_header();

			remove_action( 'bloglo_header', 'bloglo_topbar_output', 10 );
			remove_action( 'bloglo_header', 'bloglo_header_output', 20 );
		}

		/**
		 * Override Footer
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function do_footer() {
			if ( ! hfe_footer_enabled() ) {
				return;
			}

			hfe_render_footer();

			remove_action( 'bloglo_footer', 'bloglo_footer_output', 20 );
			remove_action( 'bloglo_footer', 'bloglo_copyright_bar_output', 30 );
		}

	}

endif;

/**
 * Returns the one Bloglo_HFE instance.
 */
function bloglo_hfe() {
	return Bloglo_HFE::instance();
}

bloglo_hfe();
