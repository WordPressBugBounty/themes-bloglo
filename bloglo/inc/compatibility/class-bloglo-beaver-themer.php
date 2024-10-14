<?php
/**
 * Bloglo compatibility class for Beaver Themer.
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

// Return if Beaver Themer not active.
if ( ! class_exists( 'FLThemeBuilderLoader' ) || ! class_exists( 'FLThemeBuilderLayoutData' ) ) {
	return;
}

// PHP 5.3+ is required.
if ( ! version_compare( PHP_VERSION, '5.3', '>=' ) ) {
	return;
}

if ( ! class_exists( 'Bloglo_Beaver_Themer' ) ) :

	/**
	 * Beaver Themer compatibility.
	 */
	class Bloglo_Beaver_Themer {

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
		 * @return Bloglo_Beaver_Themer
		 */
		public static function instance() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Bloglo_Beaver_Themer ) ) {
				self::$instance = new Bloglo_Beaver_Themer();
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
			add_action( 'wp', array( $this, 'header_footer_render' ) );
			add_action( 'wp', array( $this, 'page_header_render' ) );
			add_filter( 'fl_theme_builder_part_hooks', array( $this, 'register_part_hooks' ) );
		}

		/**
		 * Add theme support
		 *
		 * @since 1.0.0
		 */
		public function add_theme_support() {
			add_theme_support( 'fl-theme-builder-headers' );
			add_theme_support( 'fl-theme-builder-footers' );
			add_theme_support( 'fl-theme-builder-parts' );
		}

		/**
		 * Update header/footer with Beaver template
		 *
		 * @since 1.0.0
		 */
		public function header_footer_render() {

			// Get the header ID.
			$header_ids = FLThemeBuilderLayoutData::get_current_page_header_ids();

			// If we have a header, remove the theme header and hook in Theme Builder's.
			if ( ! empty( $header_ids ) ) {

				// Remove Top Bar.
				remove_action( 'bloglo_header', 'bloglo_topbar_output', 10 );

				// Remove Main Header.
				remove_action( 'bloglo_header', 'bloglo_header_output', 20 );

				// Replacement header.
				add_action( 'bloglo_header', 'FLThemeBuilderLayoutRenderer::render_header' );
			}

			// Get the footer ID.
			$footer_ids = FLThemeBuilderLayoutData::get_current_page_footer_ids();

			// If we have a footer, remove the theme footer and hook in Theme Builder's.
			if ( ! empty( $footer_ids ) ) {

				// Remove Main Footer.
				remove_action( 'bloglo_footer', 'bloglo_footer_output', 20 );

				// Remove Copyright Bar.
				remove_action( 'bloglo_footer', 'bloglo_copyright_bar_output', 30 );

				// Replacement footer.
				add_action( 'bloglo_footer', 'FLThemeBuilderLayoutRenderer::render_footer' );
			}
		}

		/**
		 * Remove page header if using Beaver Themer.
		 *
		 * @since 1.0.0
		 */
		public function page_header_render() {

			// Get the page ID.
			$page_ids = FLThemeBuilderLayoutData::get_current_page_content_ids();

			// If we have a content layout, remove the theme page header.
			if ( ! empty( $page_ids ) ) {
				remove_action( 'bloglo_page_header', 'bloglo_page_header_template' );
			}
		}

		/**
		 * Register hooks
		 *
		 * @since 1.0.0
		 */
		public function register_part_hooks() {
			return array(
				array(
					'label' => 'Header',
					'hooks' => array(
						'bloglo_before_masthead' => esc_html__( 'Before Header', 'bloglo' ),
						'bloglo_after_masthead'  => esc_html__( 'After Header', 'bloglo' ),
					),
				),
				array(
					'label' => 'Main',
					'hooks' => array(
						'bloglo_before_main' => esc_html__( 'Before Main', 'bloglo' ),
						'bloglo_after_main'  => esc_html__( 'After Main', 'bloglo' ),
					),
				),
				array(
					'label' => 'Content',
					'hooks' => array(
						'bloglo_before_page_content' => esc_html__( 'Before Content', 'bloglo' ),
						'bloglo_after_page_content'  => esc_html__( 'After Content', 'bloglo' ),
					),
				),
				array(
					'label' => 'Footer',
					'hooks' => array(
						'bloglo_before_colophon' => esc_html__( 'Before Footer', 'bloglo' ),
						'bloglo_after_colophon'  => esc_html__( 'After Footer', 'bloglo' ),
					),
				),
				array(
					'label' => 'Sidebar',
					'hooks' => array(
						'bloglo_before_sidebar' => esc_html__( 'Before Sidebar', 'bloglo' ),
						'bloglo_after_sidebar'  => esc_html__( 'After Sidebar', 'bloglo' ),
					),
				),
				array(
					'label' => 'Singular',
					'hooks' => array(
						'bloglo_before_singular'       => __( 'Before Singular', 'bloglo' ),
						'bloglo_after_singular'        => __( 'After Singular', 'bloglo' ),
						'bloglo_before_comments'       => __( 'Before Comments', 'bloglo' ),
						'bloglo_after_comments'        => __( 'After Comments', 'bloglo' ),
						'bloglo_before_single_content' => __( 'Before Single Content', 'bloglo' ),
						'bloglo_after_single_content'  => __( 'After Single Content', 'bloglo' ),
					),
				),
			);
		}

	}

endif;

/**
 * Returns the one Bloglo_Beaver_Themer instance.
 */
function bloglo_beaver_themer() {
	return Bloglo_Beaver_Themer::instance();
}

bloglo_beaver_themer();
