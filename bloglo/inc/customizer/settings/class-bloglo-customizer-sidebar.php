<?php
/**
 * Bloglo Sidebar section in Customizer.
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

if ( ! class_exists( 'Bloglo_Customizer_Sidebar' ) ) :

	/**
	 * Bloglo Sidebar section in Customizer.
	 */
	class Bloglo_Customizer_Sidebar {

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
			$options['section']['bloglo_section_sidebar'] = array(
				'title'    => esc_html__( 'Sidebar', 'bloglo' ),
				'priority' => 4,
			);

			// Default sidebar position.
			$options['setting']['bloglo_sidebar_position'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'bloglo_sanitize_select',
				'control'           => array(
					'type'        => 'bloglo-select',
					'section'     => 'bloglo_section_sidebar',
					'label'       => esc_html__( 'Default Position', 'bloglo' ),
					'description' => esc_html__( 'Choose default sidebar position layout. You can change this setting per page via metabox settings.', 'bloglo' ),
					'choices'     => array(
						'no-sidebar'    => esc_html__( 'No Sidebar', 'bloglo' ),
						'left-sidebar'  => esc_html__( 'Left Sidebar', 'bloglo' ),
						'right-sidebar' => esc_html__( 'Right Sidebar', 'bloglo' ),
					),
				),
			);

			// Single post sidebar position.
			$options['setting']['bloglo_single_post_sidebar_position'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'bloglo_sanitize_select',
				'control'           => array(
					'type'        => 'bloglo-select',
					'label'       => esc_html__( 'Single Post', 'bloglo' ),
					'description' => esc_html__( 'Choose default sidebar position layout for single posts. You can change this setting per post via metabox settings.', 'bloglo' ),
					'section'     => 'bloglo_section_sidebar',
					'choices'     => array(
						'default'       => esc_html__( 'Default', 'bloglo' ),
						'no-sidebar'    => esc_html__( 'No Sidebar', 'bloglo' ),
						'left-sidebar'  => esc_html__( 'Left Sidebar', 'bloglo' ),
						'right-sidebar' => esc_html__( 'Right Sidebar', 'bloglo' ),
					),
				),
			);

			// Single page sidebar position.
			$options['setting']['bloglo_single_page_sidebar_position'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'bloglo_sanitize_select',
				'control'           => array(
					'type'        => 'bloglo-select',
					'label'       => esc_html__( 'Page', 'bloglo' ),
					'description' => esc_html__( 'Choose default sidebar position layout for pages. You can change this setting per page via metabox settings.', 'bloglo' ),
					'section'     => 'bloglo_section_sidebar',
					'choices'     => array(
						'default'       => esc_html__( 'Default', 'bloglo' ),
						'no-sidebar'    => esc_html__( 'No Sidebar', 'bloglo' ),
						'left-sidebar'  => esc_html__( 'Left Sidebar', 'bloglo' ),
						'right-sidebar' => esc_html__( 'Right Sidebar', 'bloglo' ),
					),
				),
			);

			// Archive sidebar position.
			$options['setting']['bloglo_archive_sidebar_position'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'bloglo_sanitize_select',
				'control'           => array(
					'type'        => 'bloglo-select',
					'label'       => esc_html__( 'Archives & Search', 'bloglo' ),
					'description' => esc_html__( 'Choose default sidebar position layout for archives and search results.', 'bloglo' ),
					'section'     => 'bloglo_section_sidebar',
					'choices'     => array(
						'default'       => esc_html__( 'Default', 'bloglo' ),
						'no-sidebar'    => esc_html__( 'No Sidebar', 'bloglo' ),
						'left-sidebar'  => esc_html__( 'Left Sidebar', 'bloglo' ),
						'right-sidebar' => esc_html__( 'Right Sidebar', 'bloglo' ),
					),
				),
			);

			// Sidebar options heading.
			$options['setting']['bloglo_sidebar_options_heading'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'bloglo_sanitize_toggle',
				'control'           => array(
					'type'    => 'bloglo-heading',
					'label'   => esc_html__( 'Options', 'bloglo' ),
					'section' => 'bloglo_section_sidebar',
				),
			);

			// Sidebar width.
			$options['setting']['bloglo_sidebar_width'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'bloglo_sanitize_range',
				'control'           => array(
					'type'        => 'bloglo-range',
					'section'     => 'bloglo_section_sidebar',
					'label'       => esc_html__( 'Sidebar Width', 'bloglo' ),
					'description' => esc_html__( 'Change your sidebar width.', 'bloglo' ),
					'min'         => 15,
					'max'         => 50,
					'step'        => 1,
					'unit'        => '%',
					'required'    => array(
						array(
							'control'  => 'bloglo_sidebar_options_heading',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Sticky sidebar.
			$options['setting']['bloglo_sidebar_sticky'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'bloglo_sanitize_select',
				'control'           => array(
					'type'        => 'bloglo-select',
					'section'     => 'bloglo_section_sidebar',
					'label'       => esc_html__( 'Sticky Sidebar', 'bloglo' ),
					'description' => esc_html__( 'Stick sidebar when scrolling.', 'bloglo' ),
					'choices'     => array(
						''        => esc_html__( 'Disable', 'bloglo' ),
						'sidebar' => esc_html__( 'Stick first widget', 'bloglo' ),
					),
					'required'    => array(
						array(
							'control'  => 'bloglo_sidebar_options_heading',
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

new Bloglo_Customizer_Sidebar();
