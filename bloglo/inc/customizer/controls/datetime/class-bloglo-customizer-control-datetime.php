<?php
/**
 * Bloglo Customizer custom text control class.
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

if ( ! class_exists( 'Bloglo_Customizer_Control_Datetime' ) ) :
	/**
	 * Bloglo Customizer custom select control class.
	 */
	class Bloglo_Customizer_Control_Datetime extends Bloglo_Customizer_Control {

		/**
		 * The control type.
		 *
		 * @var string
		 */
		public $type = 'bloglo-datetime';

		/**
		 * Placeholder text.
		 *
		 * @since 1.0.0
		 * @var string|false
		 */

		public $info;
		/**
		 * Enqueue control related scripts/styles.
		 *
		 * @access public
		 */
		public function enqueue() {
		}

		/**
		 * Refresh the parameters passed to the JavaScript via JSON.
		 *
		 * @see WP_Customize_Control::to_json()
		 */
		public function to_json() {
			parent::to_json();
			$this->json['info'] = $this->info;
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
			<div class="bloglo-control-wrapper bloglo-text-wrapper">

				<label>
					<# if ( data.label ) { #>
						<div class="customize-control-title">
							<span>{{{ data.label }}}</span>

							<# if ( data.description ) { #>
								<i class="bloglo-info-icon">
									<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-help-circle">
										<circle cx="12" cy="12" r="10"></circle>
										<path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
										<line x1="12" y1="17" x2="12" y2="17"></line>
									</svg>
									<span class="bloglo-tooltip">{{{ data.description }}}</span>
								</i>
							<# } #>

						</div>
					<# } #>

					<input type="datetime-local" value="{{ data.value }}" placeholder="{{ data.placeholder }}" {{{ data.link }}} />
					<div class="bloglo-info-text">{{{ data.info }}}</div>
				</label>

			</div><!-- END .bloglo-control-wrapper -->
			<?php
		}
	}
endif;
