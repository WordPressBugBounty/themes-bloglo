<?php
/**
 * Bloglo Featured Links Section Settings section in Customizer.
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

if ( ! class_exists( 'Bloglo_Customizer_Featured_Links' ) ) :
	/**
	 * Bloglo Page Title Settings section in Customizer.
	 */
	class Bloglo_Customizer_Featured_Links {

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

			// Featured links Section.
			$options['section']['bloglo_section_featured_links'] = array(
				'title'    => esc_html__( 'Featured Items', 'bloglo' ),
				'priority' => 3,
			);

			// Featured links enable.
			$options['setting']['bloglo_enable_featured_links'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'bloglo_sanitize_toggle',
				'control'           => array(
					'type'    => 'bloglo-toggle',
					'section' => 'bloglo_section_featured_links',
					'label'   => esc_html__( 'Enable featured items section', 'bloglo' ),
				),
			);

			// Title.
			$options['setting']['bloglo_featured_links_title'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sanitize_text_field',
				'control'           => array(
					'type'     => 'bloglo-text',
					'section'  => 'bloglo_section_featured_links',
					'label'    => esc_html__( 'Title', 'bloglo' ),
					'required' => array(
						array(
							'control'  => 'bloglo_enable_featured_links',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
				'partial'           => array(
					'selector'            => '#featured_links .widget-title',
					'render_callback'     => function() {
						return get_theme_mod('bloglo_featured_links_title');
					},
					'fallback_refresh'    => true,
				)
			);

			$options['setting']['bloglo_featured_links'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'bloglo_repeater_sanitize',
				'control'           => array(
					'type'          => 'bloglo-repeater',
					'label'         => esc_html__( 'Featured Items', 'bloglo' ),
					'section'       => 'bloglo_section_featured_links',
					'item_name'     => esc_html__( 'Featured Link', 'bloglo' ),
					'live_title_id' => 'btn_text', // apply for input text and textarea only
					'title_format'  => esc_html__( '[live_title]', 'bloglo' ), // [live_title]
					'add_text'      => esc_html__( 'Add new Feature', 'bloglo' ),
					'max_item'      => 3,
					'limited_msg'   => wp_kses_post( __( 'Upgrade to <a target="_blank" href="https://peregrine-themes.com/bloglo/">Bloglo Pro</a> to be able to add more items and unlock other premium features!', 'bloglo' ) ),
					'fields'        => array(
						'btn_text' => array(
							'title' => esc_html__( 'Button Text', 'bloglo' ),
							'type'  => 'text',
						),
						'btn_url' => array(
							'title' => esc_html__( 'Button URL', 'bloglo' ),
							'type'  => 'url',
						),
						'btn_target' => array(
							'title' => esc_html__( 'Open link in new tab ', 'bloglo' ),
							'type'  => 'checkbox',
						),
						'image' => array(
							'title' => esc_html__( 'Image', 'bloglo' ),
							'type'  => 'media',
						),
					),
					'required'    => array(
						array(
							'control'  => 'bloglo_enable_featured_links',
							'value'    => true,
							'operator' => '==',
						)
					),
				),
				'partial'           => array(
					'selector'            => '#featured_links',
					'render_callback'     => 'bloglo_blog_featured_links',
					'container_inclusive' => true,
					'fallback_refresh'    => true,
				),
			);
			
			// Visibility.
			$options['setting']['bloglo_featured_links_visibility'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'bloglo_sanitize_select',
				'control'           => array(
					'type'        => 'bloglo-select',
					'section'     => 'bloglo_section_featured_links',
					'label'       => esc_html__( 'Device Visibility', 'bloglo' ),
					'description' => esc_html__( 'Devices where the Posts You Might Like is displayed.', 'bloglo' ),
					'choices'     => array(
						'all'                => esc_html__( 'Show on All Devices', 'bloglo' ),
						'hide-mobile'        => esc_html__( 'Hide on Mobile', 'bloglo' ),
						'hide-tablet'        => esc_html__( 'Hide on Tablet', 'bloglo' ),
						'hide-mobile-tablet' => esc_html__( 'Hide on Mobile and Tablet', 'bloglo' ),
					),
					'required'    => array(
						array(
							'control'  => 'bloglo_enable_featured_links',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Style
			$options['setting']['bloglo_featured_links_style'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'bloglo_sanitize_toggle',
				'control'           => array(
					'type'     => 'bloglo-heading',
					'section'  => 'bloglo_section_featured_links',
					'label'    => esc_html__( 'Style', 'bloglo' ),
					'required' => array(
						array(
							'control'  => 'bloglo_enable_featured_links',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Featured links container width.
			$options['setting']['bloglo_featured_links_container'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'bloglo_sanitize_select',
				'control'           => array(
					'type'        => 'bloglo-select',
					'section'     => 'bloglo_section_featured_links',
					'label'       => esc_html__( 'Width', 'bloglo' ),
					'description' => esc_html__( 'Stretch the container to full width, or match your site&rsquo;s content width.', 'bloglo' ),
					'choices'     => array(
						'content-width' => esc_html__( 'Content Width', 'bloglo' ),
						'full-width'    => esc_html__( 'Full Width', 'bloglo' ),
					),
					'required'    => array(
						array(
							'control'  => 'bloglo_enable_featured_links',
							'value'    => true,
							'operator' => '==',
						),
						array(
							'control'  => 'bloglo_featured_links_style',
							'value'    => true,
							'operator' => '==',
						),
					)
				),
			);

			return $options;
		}
	}
endif;
new Bloglo_Customizer_Featured_Links();
