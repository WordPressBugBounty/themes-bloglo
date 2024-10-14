<?php
/**
 * Bloglo Blog » Blog Page / Archive section in Customizer.
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

if ( ! class_exists( 'Bloglo_Customizer_Blog_Page' ) ) :
	/**
	 * Bloglo Blog » Blog Page / Archive section in Customizer.
	 */
	class Bloglo_Customizer_Blog_Page {

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
			$options['section']['bloglo_section_blog_page'] = array(
				'title' => esc_html__( 'Blog Page / Archive', 'bloglo' ),
				'panel' => 'bloglo_panel_blog',
			);

			// Layout.
			$options['setting']['bloglo_blog_layout'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'bloglo_sanitize_select',
				'control'           => array(
					'type'        => 'bloglo-select',
					'label'       => esc_html__( 'Layout', 'bloglo' ),
					'description' => esc_html__( 'Choose blog layout. This will affect blog layout on archives, search results and posts page.', 'bloglo' ),
					'section'     => 'bloglo_section_blog_page',
					'choices'     => array(
						'blog-masonry' => esc_html__( 'Masonry', 'bloglo' ),
					),
				),
			);

			// Blog Layout Column.
			$options['setting']['bloglo_blog_layout_column'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'bloglo_sanitize_select',
				'control'           => array(
					'type'        => 'bloglo-select',
					'label'       => esc_html__( 'Layout Column', 'bloglo' ),
					'description' => esc_html__( 'Choose blog layout Column. This will affect blog layout on archives, search results and posts page.', 'bloglo' ),
					'section'     => 'bloglo_section_blog_page',
					'choices'     => array(
						12 => esc_html__( 'Layout 1 Column', 'bloglo' ),
						6  => esc_html__( 'Layout 1/2 Column', 'bloglo' ),
					),
					'required'    => array(
						array(
							'control'  => 'bloglo_blog_layout',
							'value'    => 'blog-horizontal',
							'operator' => '!=',
						),
					),
				),
			);

			$_image_sizes = bloglo_get_image_sizes();
			$size_choices = array();

			if ( ! empty( $_image_sizes ) ) {
				foreach ( $_image_sizes as $key => $value ) {
					$name = ucwords( str_replace( array( '-', '_' ), ' ', $key ) );

					$size_choices[ $key ] = $name;

					if ( $value['width'] || $value['height'] ) {
						$size_choices[ $key ] .= ' (' . $value['width'] . 'x' . $value['height'] . ')';
					}
				}
			}

			// Featured Image Size.
			$options['setting']['bloglo_blog_image_size'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'bloglo_sanitize_select',
				'control'           => array(
					'type'    => 'bloglo-select',
					'label'   => esc_html__( 'Featured Image Size', 'bloglo' ),
					'section' => 'bloglo_section_blog_page',
					'choices' => $size_choices,
				),
			);

			// Post Elements.
			$options['setting']['bloglo_blog_entry_elements'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'bloglo_sanitize_sortable',
				'control'           => array(
					'type'        => 'bloglo-sortable',
					'section'     => 'bloglo_section_blog_page',
					'label'       => esc_html__( 'Post Elements', 'bloglo' ),
					'description' => esc_html__( 'Set order and visibility for post elements.', 'bloglo' ),
					'choices'     => array(
						'summary'        => esc_html__( 'Summary', 'bloglo' ),
						'header'         => esc_html__( 'Title', 'bloglo' ),
						'meta'           => esc_html__( 'Post Meta', 'bloglo' ),
						'thumbnail'      => esc_html__( 'Featured Image', 'bloglo' ),
						'summary-footer' => esc_html__( 'Read More', 'bloglo' ),
					),
					'required'    => array(
						array(
							'control'  => 'bloglo_blog_layout',
							'value'    => 'blog-horizontal',
							'operator' => '!=',
						),
					),
				),
			);

			// Meta/Post Details Layout.
			$options['setting']['bloglo_blog_entry_meta_elements'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'bloglo_sanitize_sortable',
				'control'           => array(
					'type'        => 'bloglo-sortable',
					'section'     => 'bloglo_section_blog_page',
					'label'       => esc_html__( 'Post Meta', 'bloglo' ),
					'description' => esc_html__( 'Set order and visibility for post meta details.', 'bloglo' ),
					'choices'     => array(
						'author'   => esc_html__( 'Author', 'bloglo' ),
						'date'     => esc_html__( 'Publish Date', 'bloglo' ),
						'comments' => esc_html__( 'Comments', 'bloglo' ),
						'category' => esc_html__( 'Categories', 'bloglo' ),
						'tag'      => esc_html__( 'Tags', 'bloglo' ),
					),
				),
			);

			// Post Categories.
			$options['setting']['bloglo_blog_horizontal_post_categories'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'bloglo_sanitize_toggle',
				'control'           => array(
					'type'        => 'bloglo-toggle',
					'label'       => esc_html__( 'Show Post Categories', 'bloglo' ),
					'description' => esc_html__( 'A list of categories the post belongs to. Displayed above post title.', 'bloglo' ),
					'section'     => 'bloglo_section_blog_page',
					'required'    => array(
						array(
							'control'  => 'bloglo_blog_layout',
							'value'    => 'blog-horizontal',
							'operator' => '==',
						),
					),
				),
			);

			// Read More Button.
			$options['setting']['bloglo_blog_horizontal_read_more'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'bloglo_sanitize_toggle',
				'control'           => array(
					'type'     => 'bloglo-toggle',
					'label'    => esc_html__( 'Show Read More Button', 'bloglo' ),
					'section'  => 'bloglo_section_blog_page',
					'required' => array(
						array(
							'control'  => 'bloglo_blog_layout',
							'value'    => 'blog-horizontal',
							'operator' => '==',
						),
					),
				),
			);

			// Meta Author image.
			$options['setting']['bloglo_entry_meta_icons'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'bloglo_sanitize_toggle',
				'control'           => array(
					'type'    => 'bloglo-toggle',
					'section' => 'bloglo_section_blog_page',
					'label'   => esc_html__( 'Show avatar and icons in post meta', 'bloglo' ),
				),
			);

			// Featured Image Position.
			$options['setting']['bloglo_blog_image_position'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'bloglo_sanitize_select',
				'control'           => array(
					'type'     => 'bloglo-select',
					'label'    => esc_html__( 'Featured Image Position', 'bloglo' ),
					'section'  => 'bloglo_section_blog_page',
					'choices'  => array(
						'left'  => esc_html__( 'Left', 'bloglo' ),
						'right' => esc_html__( 'Right', 'bloglo' ),
					),
					'required' => array(
						array(
							'control'  => 'bloglo_blog_layout',
							'value'    => 'blog-horizontal',
							'operator' => '==',
						),
					),
				),
			);

			// Excerpt Length.
			$options['setting']['bloglo_excerpt_length'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'bloglo_sanitize_range',
				'control'           => array(
					'type'        => 'bloglo-range',
					'section'     => 'bloglo_section_blog_page',
					'label'       => esc_html__( 'Excerpt Length', 'bloglo' ),
					'description' => esc_html__( 'Number of words displayed in the excerpt.', 'bloglo' ),
					'min'         => 0,
					'max'         => 100,
					'step'        => 1,
					'unit'        => '',
					'responsive'  => false,
				),
			);

			// Excerpt more.
			$options['setting']['bloglo_excerpt_more'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'sanitize_text_field',
				'control'           => array(
					'type'        => 'bloglo-text',
					'section'     => 'bloglo_section_blog_page',
					'label'       => esc_html__( 'Excerpt More', 'bloglo' ),
					'description' => esc_html__( 'What to append to excerpt if the text is cut.', 'bloglo' ),
				),
			);

			return $options;
		}
	}
endif;

new Bloglo_Customizer_Blog_Page();
