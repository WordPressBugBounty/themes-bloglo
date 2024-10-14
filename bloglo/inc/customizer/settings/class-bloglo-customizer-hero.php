<?php
/**
 * Bloglo Hero Section Settings section in Customizer.
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

if ( ! class_exists( 'Bloglo_Customizer_Hero' ) ) :
	/**
	 * Bloglo Page Title Settings section in Customizer.
	 */
	class Bloglo_Customizer_Hero {

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

			// Hero Section.
			$options['section']['bloglo_section_hero'] = array(
				'title'    => esc_html__( 'Hero', 'bloglo' ),
				'priority' => 3,
			);

			// Hero enable.
			$options['setting']['bloglo_enable_hero'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'bloglo_sanitize_toggle',
				'control'           => array(
					'type'    => 'bloglo-toggle',
					'section' => 'bloglo_section_hero',
					'label'   => esc_html__( 'Enable Hero Section', 'bloglo' ),
				),
			);

			// Hero display on.
			$options['setting']['bloglo_hero_enable_on'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'bloglo_no_sanitize',
				'control'           => array(
					'type'        => 'bloglo-checkbox-group',
					'label'       => esc_html__( 'Enable On: ', 'bloglo' ),
					'description' => esc_html__( 'Choose on which pages you want to enable Hero. ', 'bloglo' ),
					'section'     => 'bloglo_section_hero',
					'choices'     => array(
						'home'       => array(
							'title' => esc_html__( 'Home Page', 'bloglo' ),
						),
						'posts_page' => array(
							'title' => esc_html__( 'Blog / Posts Page', 'bloglo' ),
						),
					),
					'required'    => array(
						array(
							'control'  => 'bloglo_enable_hero',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Post Settings heading.
			$options['setting']['bloglo_hero_hover_slider_posts'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'bloglo_sanitize_toggle',
				'control'           => array(
					'type'     => 'bloglo-heading',
					'section'  => 'bloglo_section_hero',
					'label'    => esc_html__( 'Post Settings', 'bloglo' ),
					'required' => array(
						array(
							'control'  => 'bloglo_enable_hero',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Post count.
			$options['setting']['bloglo_hero_hover_slider_post_number'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'bloglo_sanitize_range',
				'control'           => array(
					'type'        => 'bloglo-range',
					'section'     => 'bloglo_section_hero',
					'label'       => esc_html__( 'Post Number', 'bloglo' ),
					'description' => esc_html__( 'Set the number of visible posts.', 'bloglo' ),
					'min'         => 1,
					'max'         => 2,
					'step'        => 1,
					'unit'        => '',
					'required'    => array(
						array(
							'control'  => 'bloglo_enable_hero',
							'value'    => true,
							'operator' => '==',
						),
						array(
							'control'  => 'bloglo_hero_hover_slider_posts',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
				'partial'           => array(
					'selector'            => '#hero',
					'render_callback'     => 'bloglo_blog_hero',
					'container_inclusive' => true,
					'fallback_refresh'    => true,
				),
			);

			// Post category.
			$options['setting']['bloglo_hero_hover_slider_category'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'bloglo_sanitize_select',
				'control'           => array(
					'type'        => 'bloglo-select',
					'section'     => 'bloglo_section_hero',
					'label'       => esc_html__( 'Category', 'bloglo' ),
					'description' => esc_html__( 'Display posts from selected category only. Leave empty to include all.', 'bloglo' ),
					'is_select2'  => true,
					'data_source' => 'category',
					'multiple'    => true,
					'required'    => array(
						array(
							'control'  => 'bloglo_enable_hero',
							'value'    => true,
							'operator' => '==',
						),
						array(
							'control'  => 'bloglo_hero_hover_slider_posts',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Hover Slider heading.
			$options['setting']['bloglo_hero_hover_slider'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'bloglo_sanitize_toggle',
				'control'           => array(
					'type'     => 'bloglo-heading',
					'section'  => 'bloglo_section_hero',
					'label'    => esc_html__( 'Style', 'bloglo' ),
					'required' => array(
						array(
							'control'  => 'bloglo_enable_hero',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Hover Slider height.
			$options['setting']['bloglo_hero_hover_slider_height'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'bloglo_sanitize_range',
				'control'           => array(
					'type'        => 'bloglo-range',
					'section'     => 'bloglo_section_hero',
					'label'       => esc_html__( 'Height', 'bloglo' ),
					'description' => esc_html__( 'Set the height of the container.', 'bloglo' ),
					'min'         => 350,
					'max'         => 1000,
					'step'        => 1,
					'unit'        => 'px',
					'required'    => array(
						array(
							'control'  => 'bloglo_enable_hero',
							'value'    => true,
							'operator' => '==',
						),
						array(
							'control'  => 'bloglo_hero_hover_slider',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Hover Slider Elements.
			$options['setting']['bloglo_hero_hover_slider_elements'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'bloglo_sanitize_sortable',
				'control'           => array(
					'type'        => 'bloglo-sortable',
					'section'     => 'bloglo_section_hero',
					'label'       => esc_html__( 'Post Elements', 'bloglo' ),
					'description' => esc_html__( 'Set order and visibility for post elements.', 'bloglo' ),
					'sortable'    => false,
					'choices'     => array(
						'category'  => esc_html__( 'Categories', 'bloglo' ),
						'meta'      => esc_html__( 'Post Details', 'bloglo' ),
						'read_more' => esc_html__( 'Continue Reading', 'bloglo' ),
					),
					'required'    => array(
						array(
							'control'  => 'bloglo_enable_hero',
							'value'    => true,
							'operator' => '==',
						),
						array(
							'control'  => 'bloglo_hero_hover_slider',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
				'partial'           => array(
					'selector'            => '#hero',
					'render_callback'     => 'bloglo_blog_hero',
					'container_inclusive' => true,
					'fallback_refresh'    => true,
				),
			);

			return $options;
		}
	}
endif;
new Bloglo_Customizer_Hero();
