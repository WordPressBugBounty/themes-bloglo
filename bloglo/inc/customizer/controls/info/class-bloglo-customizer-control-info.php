<?php
/**
 * Bloglo Customizer info control class.
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

if ( ! class_exists( 'Bloglo_Customizer_Control_Info' ) ) :
	/**
	 * Bloglo Customizer info control class.
	 */
	class Bloglo_Customizer_Control_Info extends Bloglo_Customizer_Control {

		/**
		 * The control type.
		 *
		 * @var string
		 */
		public $type = 'bloglo-info';

		/**
		 * Custom URL.
		 *
		 * @since  1.0.0
		 * @var    string
		 */
		public $url = '';

		/**
		 * Link target.
		 *
		 * @since  1.0.0
		 * @var    string
		 */
		public $target = '_blank';

		/**
		 * Enqueue control related scripts/styles.
		 *
		 * @access public
		 */
		public function enqueue() {

			// Script debug.
			$bloglo_suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

			// Control type.
			$bloglo_type = str_replace( 'bloglo-', '', $this->type );

			/**
			 * Enqueue control stylesheet
			 */
			wp_enqueue_style(
				'bloglo-' . $bloglo_type . '-control-style',
				BLOGLO_THEME_URI . '/inc/customizer/controls/' . $bloglo_type . '/' . $bloglo_type . $bloglo_suffix . '.css',
				false,
				BLOGLO_THEME_VERSION,
				'all'
			);
		}

		/**
		 * Refresh the parameters passed to the JavaScript via JSON.
		 *
		 * @see WP_Customize_Control::to_json()
		 */
		public function to_json() {
			parent::to_json();

			$this->json['url']    = $this->url;
			$this->json['target'] = $this->target;
		}

		/**
		 * An Underscore (JS) template for this control's content (but not its container).
		 *
		 * Class variables for this control class are available in the `data` JS object;
		 * export custom variables by overriding {@see WP_Customize_Control::to_json()}.
		 *
		 * @see WP_Customize_Control::print_template()
		 */
		protected function content_template() {
			?>
			<div class="bloglo-info-wrapper bloglo-control-wrapper">

				<# if ( data.label ) { #>
					<span class="bloglo-control-heading customize-control-title bloglo-field">{{{ data.label }}}</span>
				<# } #>

				<# if ( data.description ) { #>
					<div class="description customize-control-description bloglo-field bloglo-info-description">{{{ data.description }}}</div>
				<# } #>

				<a href="{{ data.url }}" class="button button-primary" target="{{ data.target }}" rel="noopener noreferrer"><?php esc_html_e( 'Learn More', 'bloglo' ); ?></a>

			</div><!-- END .bloglo-control-wrapper -->
			<?php
		}

	}
endif;
