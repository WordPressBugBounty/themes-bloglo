<?php //phpcs:ignore
/**
 * Theme functions and definitions.
 *
 * @package Bloglo
 * @author Peregrine Themes
 * @since   1.0.0
 */

/**
 * Main Bloglo class.
 *
 * @since 1.0.0
 */
final class Bloglo {

	/**
	 * Theme options
	 *
	 * @since 1.0.0
	 * @var object
	 */
	public $options;

	/**
	 * Theme fonts
	 *
	 * @since 1.0.0
	 * @var object
	 */
	public $fonts;

	/**
	 * Theme icons
	 *
	 * @since 1.0.0
	 * @var object
	 */
	public $icons;

	/**
	 * Theme customizer
	 *
	 * @since 1.0.0
	 * @var object
	 */
	public $customizer;

	/**
	 * Theme admin
	 *
	 * @since 1.0.0
	 * @var object
	 */
	public $admin;

	/**
	 * Singleton instance of the class.
	 *
	 * @since 1.0.0
	 * @var object
	 */
	private static $instance;
	/**
	 * Theme version.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $version = '1.1.18';
	/**
	 * Main Bloglo Instance.
	 *
	 * Insures that only one instance of Bloglo exists in memory at any one
	 * time. Also prevents needing to define globals all over the place.
	 *
	 * @since 1.0.0
	 * @return Bloglo
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Bloglo ) ) {
			self::$instance = new Bloglo();
			self::$instance->constants();
			self::$instance->includes();
			self::$instance->objects();
			// Hook now that all of the Bloglo stuff is loaded.
			do_action( 'bloglo_loaded' );
		}
		return self::$instance;
	}
	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function __construct() {
		add_action( 'after_switch_theme', array( $this, 'bloglo_delete_theme_demos_transient' ) );
	}
	/**
	 * Setup constants.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	private function constants() {
		if ( ! defined( 'BLOGLO_THEME_VERSION' ) ) {
			define( 'BLOGLO_THEME_VERSION', $this->version );
		}
		if ( ! defined( 'BLOGLO_THEME_URI' ) ) {
			define( 'BLOGLO_THEME_URI', get_parent_theme_file_uri() );
		}
		if ( ! defined( 'BLOGLO_THEME_PATH' ) ) {
			define( 'BLOGLO_THEME_PATH', get_parent_theme_file_path() );
		}
	}
	/**
	 * Include files.
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function includes() {
		require_once BLOGLO_THEME_PATH . '/inc/common.php';
		require_once BLOGLO_THEME_PATH . '/inc/helpers.php';
		require_once BLOGLO_THEME_PATH . '/inc/widgets.php';
		require_once BLOGLO_THEME_PATH . '/inc/template-tags.php';
		require_once BLOGLO_THEME_PATH . '/inc/template-parts.php';
		require_once BLOGLO_THEME_PATH . '/inc/icon-functions.php';
		require_once BLOGLO_THEME_PATH . '/inc/breadcrumbs.php';
		require_once BLOGLO_THEME_PATH . '/inc/class-bloglo-dynamic-styles.php';
		// Core.
		require_once BLOGLO_THEME_PATH . '/inc/core/class-bloglo-options.php';
		require_once BLOGLO_THEME_PATH . '/inc/core/class-bloglo-enqueue-scripts.php';
		require_once BLOGLO_THEME_PATH . '/inc/core/class-bloglo-fonts.php';
		require_once BLOGLO_THEME_PATH . '/inc/core/class-bloglo-theme-setup.php';
		// Compatibility.
		require_once BLOGLO_THEME_PATH . '/inc/compatibility/woocommerce/class-bloglo-woocommerce.php';
		require_once BLOGLO_THEME_PATH . '/inc/compatibility/socialsnap/class-bloglo-socialsnap.php';
		require_once BLOGLO_THEME_PATH . '/inc/compatibility/class-bloglo-wpforms.php';
		require_once BLOGLO_THEME_PATH . '/inc/compatibility/class-bloglo-jetpack.php';
		require_once BLOGLO_THEME_PATH . '/inc/compatibility/class-bloglo-beaver-themer.php';
		require_once BLOGLO_THEME_PATH . '/inc/compatibility/class-bloglo-elementor.php';
		require_once BLOGLO_THEME_PATH . '/inc/compatibility/class-bloglo-elementor-pro.php';
		require_once BLOGLO_THEME_PATH . '/inc/compatibility/class-bloglo-hfe.php';

		if ( is_admin() ) {
			require_once BLOGLO_THEME_PATH . '/inc/utilities/class-bloglo-plugin-utilities.php';
			require_once BLOGLO_THEME_PATH . '/inc/admin/class-bloglo-admin.php';

		}
		new Bloglo_Enqueue_Scripts();
		// Customizer.
		require_once BLOGLO_THEME_PATH . '/inc/customizer/class-bloglo-customizer.php';
		require_once BLOGLO_THEME_PATH . '/inc/customizer/customizer-callbacks.php';
		require_once BLOGLO_THEME_PATH . '/inc/customizer/class-bloglo-section-ordering.php';
	}
	/**
	 * Setup objects to be used throughout the theme.
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function objects() {

		bloglo()->options    = new Bloglo_Options();
		bloglo()->fonts      = new Bloglo_Fonts();
		bloglo()->icons      = new Bloglo_Icons();
		bloglo()->customizer = new Bloglo_Customizer();
		if ( is_admin() ) {
			bloglo()->admin = new Bloglo_Admin();
		}
	}

	/**
	 * Delete theme demos transient when switching from other Peregrine's themes to Bloglo
	 *
	 * @since 1.0.0
	 */
	public function bloglo_delete_theme_demos_transient() {
		// Delete theme demos transient
		delete_transient( 'hester_core_demo_templates' );
	}
}

/**
 * The function which returns the one Bloglo instance.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Example: <?php $bloglo = bloglo(); ?>
 *
 * @since 1.0.0
 * @return object
 */
function bloglo() {
	return Bloglo::instance();
}

bloglo();

