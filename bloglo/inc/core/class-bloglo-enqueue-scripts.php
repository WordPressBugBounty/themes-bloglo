<?php
/**
 * Enqueue scripts & styles.
 *
 * @package     Bloglo
 * @author      Peregrine Themes
 * @since       1.0.0
 */

/**
 * Enqueue and register scripts and styles.
 *
 * @since 1.0.0
 */
class Bloglo_Enqueue_Scripts {

	/**
	 * Check if debug is on
	 *
	 * @var boolean
	 */
	private $is_debug;

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->is_debug = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG;
		add_action( 'wp_enqueue_scripts', array( $this, 'bloglo_enqueues' ) );
		add_action( 'wp_print_footer_scripts', array( $this, 'bloglo_skip_link_focus_fix' ) );
		add_action( 'enqueue_block_editor_assets', array( $this, 'bloglo_block_editor_assets' ) );
	}

	/**
	 * Enqueue styles and scripts.
	 *
	 * @since 1.0.0
	 */
	public function bloglo_enqueues() {
		// Script debug.
		$bloglo_dir    = $this->is_debug ? 'dev/' : '';
		$bloglo_suffix = $this->is_debug ? '' : '.min';

		// fontawesome enqueue.
		wp_enqueue_style(
			'FontAwesome',
			BLOGLO_THEME_URI . '/assets/css/all' . $bloglo_suffix . '.css',
			false,
			'5.15.4',
			'all'
		);
		// Enqueue theme stylesheet.
		wp_enqueue_style(
			'bloglo-styles',
			BLOGLO_THEME_URI . '/assets/css/style' . $bloglo_suffix . '.css',
			false,
			BLOGLO_THEME_VERSION,
			'all'
		);

		// Enqueue HTML5 shiv.
		wp_register_script(
			'html5shiv',
			BLOGLO_THEME_URI . '/assets/js/' . $bloglo_dir . 'vendors/html5' . $bloglo_suffix . '.js',
			array(),
			'3.7.3',
			true
		);

		// Load only on < IE9.
		wp_script_add_data(
			'html5shiv',
			'conditional',
			'lt IE 9'
		);

		// Flexibility.js for crossbrowser flex support.
		wp_enqueue_script(
			'bloglo-flexibility',
			BLOGLO_THEME_URI . '/assets/js/' . $bloglo_dir . 'vendors/flexibility' . $bloglo_suffix . '.js',
			array(),
			BLOGLO_THEME_VERSION,
			false
		);

		wp_add_inline_script(
			'bloglo-flexibility',
			'flexibility(document.documentElement);'
		);

		wp_script_add_data(
			'bloglo-flexibility',
			'conditional',
			'IE'
		);

		// Register Bloglo slider.
		wp_register_script(
			'bloglo-slider',
			BLOGLO_THEME_URI . '/assets/js/' . $bloglo_dir . 'bloglo-slider' . $bloglo_suffix . '.js',
			array( 'imagesloaded' ),
			BLOGLO_THEME_VERSION,
			true
		);

		wp_register_script(
			'bloglo-marquee',
			BLOGLO_THEME_URI . '/assets/js/' . $bloglo_dir . 'vendors/jquery.marquee' . $bloglo_suffix . '.js',
			array( 'imagesloaded' ),
			BLOGLO_THEME_VERSION,
			true
		);

		if ( bloglo()->options->get( 'bloglo_blog_layout' ) == 'blog-masonry' ) {
			wp_enqueue_script( 'masonry' );
		}

		// Load comment reply script if comments are open.
		if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply' );
		}

		// Enqueue main theme script.
		wp_enqueue_script(
			'bloglo',
			BLOGLO_THEME_URI . '/assets/js/' . $bloglo_dir . 'bloglo' . $bloglo_suffix . '.js',
			array( 'jquery', 'imagesloaded' ),
			BLOGLO_THEME_VERSION,
			true
		);

		// Comment count used in localized strings.
		$comment_count = get_comments_number();

		// Localized variables so they can be used for translatable strings.
		$localized = array(
			'ajaxurl'               => esc_url( admin_url( 'admin-ajax.php' ) ),
			'nonce'                 => wp_create_nonce( 'bloglo-nonce' ),
			'responsive-breakpoint' => intval( bloglo_option( 'main_nav_mobile_breakpoint' ) ),
			'sticky-header'         => array(
				'enabled' => bloglo_option( 'sticky_header' ),
				'hide_on' => bloglo_option( 'sticky_header_hide_on' ),
			),
			'dark_mode' 			=> (bool) bloglo_option( 'dark_mode' ),
			'strings'               => array(
				/* translators: %s Comment count */
				'comments_toggle_show' => $comment_count > 0 ? esc_html( sprintf( _n( 'Show %s Comment', 'Show %s Comments', $comment_count, 'bloglo' ), $comment_count ) ) : esc_html__( 'Leave a Comment', 'bloglo' ),
				'comments_toggle_hide' => esc_html__( 'Hide Comments', 'bloglo' ),
			),
		);

		wp_localize_script(
			'bloglo',
			'bloglo_vars',
			apply_filters( 'bloglo_localized', $localized )
		);

		// Enqueue google fonts.
		bloglo()->fonts->enqueue_google_fonts();

		// Add additional theme styles.
		do_action( 'bloglo_enqueue_scripts' );
	}

	/**
	 * Skip link focus fix for IE11.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function bloglo_skip_link_focus_fix() {
		?>
		<script>
			! function() {
				var e = -1 < navigator.userAgent.toLowerCase().indexOf("webkit"),
					t = -1 < navigator.userAgent.toLowerCase().indexOf("opera"),
					n = -1 < navigator.userAgent.toLowerCase().indexOf("msie");
				(e || t || n) && document.getElementById && window.addEventListener && window.addEventListener("hashchange", function() {
					var e, t = location.hash.substring(1);
					/^[A-z0-9_-]+$/.test(t) && (e = document.getElementById(t)) && (/^(?:a|select|input|button|textarea)$/i.test(e.tagName) || (e.tabIndex = -1), e.focus())
				}, !1)
			}();
		</script>
		<?php
	}

	/**
	 * Enqueue assets for the Block Editor.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function bloglo_block_editor_assets() {

		// RTL version.
		$rtl = is_rtl() ? '-rtl' : '';

		// Minified version.
		$min = $this->is_debug ? '' : '.min';
		// Enqueue block editor styles.
		wp_enqueue_style(
			'bloglo-block-editor-styles',
			BLOGLO_THEME_URI . '/inc/admin/assets/css/bloglo-block-editor-styles' . $rtl . $min . '.css',
			false,
			BLOGLO_THEME_VERSION,
			'all'
		);

		// Enqueue google fonts.
		bloglo()->fonts->enqueue_google_fonts();

		// Add dynamic CSS as inline style.
		wp_add_inline_style(
			'bloglo-block-editor-styles',
			apply_filters( 'bloglo_block_editor_dynamic_css', bloglo_dynamic_styles()->get_block_editor_css() )
		);
	}
}
