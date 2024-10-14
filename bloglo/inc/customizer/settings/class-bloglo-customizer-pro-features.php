<?php
/**
 * Bloglo Pro Features section in Customizer.
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

if ( ! class_exists( 'Bloglo_Customizer_Pro_Features' ) ) :
	/**
	 * Bloglo PYML section in Customizer.
	 */
	class Bloglo_Customizer_Pro_Features {

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
			// Pro features section
			$options['section']['bloglo_section_bloglo_pro'] = array(
				'title'    => esc_html__( 'View pro features', 'bloglo' ),
				'priority' => 0,
			);

			$options['setting']['bloglo_section_bloglo_pro_features'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'sanitize_text_field',
				'control'           => array(
					'type'     => 'bloglo-pro',
					'section'  => 'bloglo_section_bloglo_pro',
					'screenshot' => apply_filters( 'bloglo_pro_theme_screenshot', esc_url( get_template_directory_uri() ) . '/assets/images/bloglo-lapi.png' ),
					'features' => apply_filters(
						'bloglo_pro_theme_features',
						array(
							esc_html__( 'All starter sites included', 'bloglo' ),
							esc_html__( 'Advance Header Options', 'bloglo' ),
							esc_html__( 'Advance FrontPage Slider Layouts', 'bloglo' ),
							esc_html__( 'Body and H1 to H6 typography options', 'bloglo' ),
							esc_html__( 'Primary, Seconday and text Buttons color and typography options', 'bloglo' ),
							esc_html__( 'Post Advance Featured', 'bloglo' ),
							esc_html__( 'Post Like ❤️ Feature', 'bloglo' ),
							esc_html__( 'Unlimited Featured links + some additional features', 'bloglo' ),
							esc_html__( 'Post Category Color Change Feature', 'bloglo' ),
							esc_html__( 'Meta Category Options', 'bloglo' ),
							esc_html__( 'Site Layouts Options e.g. Boxed, Framed etc', 'bloglo' ),
							esc_html__( 'Archive Layout Options', 'bloglo' ),
							esc_html__( 'Sticky Sidebar Options', 'bloglo' ),
							esc_html__( 'Advance Color Scheme', 'bloglo' ),
							esc_html__( 'Author Widgets', 'bloglo' ),
							esc_html__( 'Title Design Settings', 'bloglo' ),
							esc_html__( 'Masonry Grid & Multi Post Options', 'bloglo' ),
							esc_html__( 'Full Width Post/Page Options', 'bloglo' ),
							esc_html__( 'Single Post/Page Layout Options', 'bloglo' ),
							esc_html__( 'Single Post/Page Left/Right Sidebar', 'bloglo' ),
							esc_html__( 'Footer Advance Featured', 'bloglo' ),
							esc_html__( 'Footer Widgets Options', 'bloglo' ),
							esc_html__( 'Call to action / Pre-Footer', 'bloglo' ),
							esc_html__( 'Live Container Manage Options', 'bloglo' ),
							esc_html__( 'Parallax Footer', 'bloglo' ),
							esc_html__( 'Site pre-loader', 'bloglo' ),
							esc_html__( 'SEO Meta', 'bloglo' ),
							esc_html__( 'AMP Compatibility', 'bloglo' ),
							esc_html__( 'Coming Soon/Maintenance Mode Option', 'bloglo' ),
							esc_html__( 'Regular Premium Updates', 'bloglo' ),
							esc_html__( 'Quick Support', 'bloglo' ),
							esc_html__( 'And much more...', 'bloglo' ),
						)
					),
				),
			);

			return $options;
		}

	}
endif;
new Bloglo_Customizer_Pro_Features();
