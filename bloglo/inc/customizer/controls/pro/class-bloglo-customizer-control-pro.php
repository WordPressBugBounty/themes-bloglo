<?php
if ( ! class_exists( 'Bloglo_Customizer_Control_Pro' ) ) :
	class Bloglo_Customizer_Control_Pro extends Bloglo_Customizer_Control {

		/**
		 * The control type.
		 *
		 * @var string
		 */
		public $type = 'bloglo-pro';

		/**
		 * Pro features
		 *
		 * @since 1.1.1
		 */
		public $features = array();

		/**
		 * Pro theme screenshot
		 *
		 * @since 1.1.1
		 */

		 public $screenshot;

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

		public function to_json() {
			parent::to_json();
			$this->json['features']   = $this->features;
			$this->json['screenshot'] = $this->screenshot;
		}

		/**
		 * Render the content on the theme customizer page
		 */
		public function content_template() {?>

			<div class="upsell-btn" style="text-align: center;">                 
				<a style="margin: 0 auto 5px;display: inline-block;" href="https://www.peregrine-themes.com/bloglo/?utm_medium=customizer&utm_source=button&utm_campaign=profeatures" target="blank" class="btn btn-success btn"><?php esc_html_e( 'Upgrade to Bloglo Pro', 'bloglo' ); ?></a>
			</div>
			<# if ( data.screenshot ) {   #>
			<div class="">
				<img class="bloglo_img_responsive " src="{{{ data.screenshot }}}" alt="<?php esc_attr_e( 'Bloglo Pro', 'bloglo' ); ?>">
			</div>  
			<# }  #>       
			<div class="">
				<h3 style="margin-top:10px;margin-left:20px;text-decoration:underline;color:#111;font-size:16px;"><?php esc_html_e( 'Bloglo Pro Features', 'bloglo' ); ?></h3>
				<ul style="padding-top:10px">
					<# _.each(data.features, function(feature){ #>
						<li class="upsell-bloglo"> <div class="dashicons dashicons-yes"></div> {{{ feature }}} </li>
					<# }); #>
				</ul>
			</div>
			<div class="upsell-btn upsell-btn-bottom" style="text-align: center;">                 
				<a style="margin: 0 auto 5px;display: inline-block;" href="https://www.peregrine-themes.com/bloglo/?utm_medium=customizer&utm_source=button&utm_campaign=profeatures" target="blank" class="btn btn-success btn"><?php esc_html_e( 'Upgrade to Bloglo Pro', 'bloglo' ); ?></a>
			</div>
		   
			<p>
				<?php
					printf( __( 'If you Like our Products , Please Rate us 5 star on %1$sWordPress.org%2$s.  We\'d really appreciate it! </br></br>  Thank You', 'bloglo' ), '<a target="" href="https://wordpress.org/support/view/theme-reviews/bloglo?filter=5">', '</a>' );
				?>
			</p>
			<?php
		}
	}
endif;
