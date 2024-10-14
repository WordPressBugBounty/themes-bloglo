<?php
/**
 * Bloglo Blog - Single Post section in Customizer.
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

if ( ! class_exists( 'Bloglo_Customizer_Single_Post' ) ) :
	/**
	 * Bloglo Blog - Single Post section in Customizer.
	 */
	class Bloglo_Customizer_Single_Post {

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
			$options['section']['bloglo_section_blog_single_post'] = array(
				'title'    => esc_html__( 'Single Post', 'bloglo' ),
				'panel'    => 'bloglo_panel_blog',
				'priority' => 20,
			);

			// Single post layout.
			$options['setting']['bloglo_single_post_layout_heading'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'bloglo_sanitize_toggle',
				'control'           => array(
					'type'    => 'bloglo-heading',
					'label'   => esc_html__( 'Layout', 'bloglo' ),
					'section' => 'bloglo_section_blog_single_post',
				),
			);

			// Content Layout.
			$options['setting']['bloglo_single_title_position'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'bloglo_sanitize_select',
				'control'           => array(
					'type'        => 'bloglo-select',
					'label'       => esc_html__( 'Title Position', 'bloglo' ),
					'description' => esc_html__( 'Select title position for single post pages.', 'bloglo' ),
					'section'     => 'bloglo_section_blog_single_post',
					'choices'     => array(
						'in-content'     => esc_html__( 'In Content', 'bloglo' ),
						'in-page-header' => esc_html__( 'In Page Header', 'bloglo' ),
					),
					'required'    => array(
						array(
							'control'  => 'bloglo_single_post_layout_heading',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Spacing.
			$options['setting']['bloglo_single_title_spacing'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'bloglo_sanitize_responsive',
				'control'           => array(
					'type'        => 'bloglo-spacing',
					'label'       => esc_html__( 'Title Spacing', 'bloglo' ),
					'description' => esc_html__( 'Specify title top and bottom padding.', 'bloglo' ),
					'section'     => 'bloglo_section_blog_single_post',
					'choices'     => array(
						'top'    => esc_html__( 'Top', 'bloglo' ),
						'bottom' => esc_html__( 'Bottom', 'bloglo' ),
					),
					'responsive'  => true,
					'unit'        => array(
						'px',
					),
					'required'    => array(
						array(
							'control'  => 'bloglo_single_post_layout_heading',
							'value'    => true,
							'operator' => '==',
						),
						array(
							'control'  => 'bloglo_single_title_position',
							'value'    => 'in-page-header',
							'operator' => '==',
						),
					),
				),
			);

			// Single post elements.
			$options['setting']['bloglo_single_post_elements_heading'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'bloglo_sanitize_toggle',
				'control'           => array(
					'type'    => 'bloglo-heading',
					'label'   => esc_html__( 'Post Elements', 'bloglo' ),
					'section' => 'bloglo_section_blog_single_post',
				),
			);

			$options['setting']['bloglo_single_post_elements'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'bloglo_sanitize_sortable',
				'control'           => array(
					'type'        => 'bloglo-sortable',
					'section'     => 'bloglo_section_blog_single_post',
					'label'       => esc_html__( 'Post Elements', 'bloglo' ),
					'description' => esc_html__( 'Set visibility of post elements.', 'bloglo' ),
					'sortable'    => false,
					'choices'     => array(
						'thumb'          => esc_html__( 'Featured Image', 'bloglo' ),
						'category'       => esc_html__( 'Post Categories', 'bloglo' ),
						'tags'           => esc_html__( 'Post Tags', 'bloglo' ),
						'last-updated'   => esc_html__( 'Last Updated Date', 'bloglo' ),
						'about-author'   => esc_html__( 'About Author Box', 'bloglo' ),
						'prev-next-post' => esc_html__( 'Next/Prev Post Links', 'bloglo' ),
					),
					'required'    => array(
						array(
							'control'  => 'bloglo_single_post_elements_heading',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Meta/Post Details Layout.
			$options['setting']['bloglo_single_post_meta_elements'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'bloglo_sanitize_sortable',
				'control'           => array(
					'type'        => 'bloglo-sortable',
					'label'       => esc_html__( 'Post Meta', 'bloglo' ),
					'description' => esc_html__( 'Set order and visibility for post meta details.', 'bloglo' ),
					'section'     => 'bloglo_section_blog_single_post',
					'choices'     => array(
						'author'   => esc_html__( 'Author', 'bloglo' ),
						'date'     => esc_html__( 'Publish Date', 'bloglo' ),
						'comments' => esc_html__( 'Comments', 'bloglo' ),
						'category' => esc_html__( 'Categories', 'bloglo' ),
					),
					'required'    => array(
						array(
							'control'  => 'bloglo_single_post_elements_heading',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Meta icons.
			$options['setting']['bloglo_single_entry_meta_icons'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'bloglo_sanitize_toggle',
				'control'           => array(
					'type'     => 'bloglo-toggle',
					'section'  => 'bloglo_section_blog_single_post',
					'label'    => esc_html__( 'Show avatar and icons in post meta', 'bloglo' ),
					'required' => array(
						array(
							'control'  => 'bloglo_single_post_elements_heading',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Toggle Comments.
			$options['setting']['bloglo_single_toggle_comments'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'bloglo_sanitize_toggle',
				'control'           => array(
					'type'        => 'bloglo-toggle',
					'label'       => esc_html__( 'Show Toggle Comments', 'bloglo' ),
					'description' => esc_html__( 'Hide comments and comment form behind a toggle button. ', 'bloglo' ),
					'section'     => 'bloglo_section_blog_single_post',
					'required'    => array(
						array(
							'control'  => 'bloglo_single_post_elements_heading',
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
new Bloglo_Customizer_Single_Post();
