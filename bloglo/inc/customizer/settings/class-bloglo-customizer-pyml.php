<?php
/**
 * Bloglo PYML section in Customizer.
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

if ( ! class_exists( 'Bloglo_Customizer_PYML' ) ) :
	/**
	 * Bloglo PYML section in Customizer.
	 */
	class Bloglo_Customizer_PYML {

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
			// Posts You Might Like Section.
			$options['section']['bloglo_section_pyml'] = array(
				'title'    => esc_html__( 'Posts You Might Like', 'bloglo' ),
				'priority' => 3,
			);

			// Posts You Might Like enable.
			$options['setting']['bloglo_enable_pyml'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'bloglo_sanitize_toggle',
				'control'           => array(
					'type'    => 'bloglo-toggle',
					'section' => 'bloglo_section_pyml',
					'label'   => esc_html__( 'Enable Posts You Might Like Section', 'bloglo' ),
				),
			);

			// Title.
			$options['setting']['bloglo_pyml_title'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sanitize_text_field',
				'control'           => array(
					'type'     => 'bloglo-text',
					'section'  => 'bloglo_section_pyml',
					'label'    => esc_html__( 'Title', 'bloglo' ),
					'required' => array(
						array(
							'control'  => 'bloglo_enable_pyml',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Posts You Might Like display on.
			$options['setting']['bloglo_pyml_enable_on'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'bloglo_no_sanitize',
				'control'           => array(
					'type'        => 'bloglo-checkbox-group',
					'label'       => esc_html__( 'Enable On: ', 'bloglo' ),
					'description' => esc_html__( 'Choose on which pages you want to enable Posts You Might Like. ', 'bloglo' ),
					'section'     => 'bloglo_section_pyml',
					'choices'     => array(
						'home'       => array(
							'title' => esc_html__( 'Home Page', 'bloglo' ),
						),
						'posts_page' => array(
							'title' => esc_html__( 'Blog / Posts Page', 'bloglo' ),
						),
						'archive'    => array(
							'title' => esc_html__( 'Archive Page', 'bloglo' ),
						),
						'search'     => array(
							'title' => esc_html__( 'Search Page', 'bloglo' ),
						),
						'post'       => array(
							'title' => esc_html__( 'Single Post', 'bloglo' ),
						),
					),
					'required'    => array(
						array(
							'control'  => 'bloglo_enable_pyml',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// PYML heading.
			$options['setting']['bloglo_pyml_style'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'bloglo_sanitize_toggle',
				'control'           => array(
					'type'     => 'bloglo-heading',
					'section'  => 'bloglo_section_pyml',
					'label'    => esc_html__( 'Style', 'bloglo' ),
					'required' => array(
						array(
							'control'  => 'bloglo_enable_pyml',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// PYML Elements.
			$options['setting']['bloglo_pyml_elements'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'bloglo_sanitize_sortable',
				'control'           => array(
					'type'        => 'bloglo-sortable',
					'section'     => 'bloglo_section_pyml',
					'label'       => esc_html__( 'Post Elements', 'bloglo' ),
					'description' => esc_html__( 'Set order and visibility for post elements.', 'bloglo' ),
					'sortable'    => false,
					'choices'     => array(
						'category' => esc_html__( 'Categories', 'bloglo' ),
						'meta'     => esc_html__( 'Post Details', 'bloglo' ),
					),
					'required'    => array(
						array(
							'control'  => 'bloglo_enable_pyml',
							'value'    => true,
							'operator' => '==',
						),
						array(
							'control'  => 'bloglo_pyml_style',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
				'partial'           => array(
					'selector'            => '#pyml',
					'render_callback'     => 'bloglo_blog_pyml',
					'container_inclusive' => true,
					'fallback_refresh'    => true,
				),
			);

			// Post Settings heading.
			$options['setting']['bloglo_pyml_posts'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'bloglo_sanitize_toggle',
				'control'           => array(
					'type'     => 'bloglo-heading',
					'section'  => 'bloglo_section_pyml',
					'label'    => esc_html__( 'Post Settings', 'bloglo' ),
					'required' => array(
						array(
							'control'  => 'bloglo_enable_pyml',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Post count.
			$options['setting']['bloglo_pyml_post_number'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'bloglo_sanitize_range',
				'control'           => array(
					'type'        => 'bloglo-range',
					'section'     => 'bloglo_section_pyml',
					'label'       => esc_html__( 'Post Number', 'bloglo' ),
					'description' => esc_html__( 'Set the number of visible posts.', 'bloglo' ),
					'min'         => 1,
					'max'         => 4,
					'step'        => 1,
					'unit'        => '',
					'required'    => array(
						array(
							'control'  => 'bloglo_enable_pyml',
							'value'    => true,
							'operator' => '==',
						),
						array(
							'control'  => 'bloglo_pyml_posts',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
				'partial'           => array(
					'selector'            => '#pyml',
					'render_callback'     => 'bloglo_blog_pyml',
					'container_inclusive' => true,
					'fallback_refresh'    => true,
				),
			);

			// Post category.
			$options['setting']['bloglo_pyml_category'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'bloglo_sanitize_select',
				'control'           => array(
					'type'        => 'bloglo-select',
					'section'     => 'bloglo_section_pyml',
					'label'       => esc_html__( 'Category', 'bloglo' ),
					'description' => esc_html__( 'Display posts from selected category only. Leave empty to include all.', 'bloglo' ),
					'is_select2'  => true,
					'data_source' => 'category',
					'multiple'    => true,
					'required'    => array(
						array(
							'control'  => 'bloglo_enable_pyml',
							'value'    => true,
							'operator' => '==',
						),
						array(
							'control'  => 'bloglo_pyml_posts',
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
new Bloglo_Customizer_PYML();
