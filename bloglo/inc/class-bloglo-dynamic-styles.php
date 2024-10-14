<?php

/**
 * Dynamically generate CSS code.
 * The code depends on options set in the Highend Options and Post/Page metaboxes.
 *
 * If possible, write the dynamically generated code into a .css file, otherwise return the code. The file is refreshed on each modification of metaboxes & theme options.
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

if ( ! class_exists( 'Bloglo_Dynamic_Styles' ) ) :
	/**
	 * Dynamically generate CSS code.
	 */
	class Bloglo_Dynamic_Styles {

		/**
		 * Singleton instance of the class.
		 *
		 * @since 1.0.0
		 * @var object
		 */
		private static $instance;

		/**
		 * URI for Dynamic CSS file.
		 *
		 * @since 1.0.0
		 * @var object
		 */
		private $dynamic_css_uri;

		/**
		 * Path for Dynamic CSS file.
		 *
		 * @since 1.0.0
		 * @var object
		 */
		private $dynamic_css_path;

		/**
		 * Main Bloglo_Dynamic_Styles Instance.
		 *
		 * @since 1.0.0
		 * @return Bloglo_Dynamic_Styles
		 */
		public static function instance() {

			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Bloglo_Dynamic_Styles ) ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * Primary class constructor.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {

			$upload_dir = wp_upload_dir();

			$this->dynamic_css_uri  = trailingslashit( set_url_scheme( $upload_dir['baseurl'] ) ) . 'bloglo/';
			$this->dynamic_css_path = trailingslashit( set_url_scheme( $upload_dir['basedir'] ) ) . 'bloglo/';

			if ( ! is_customize_preview() && wp_is_writable( trailingslashit( $upload_dir['basedir'] ) ) ) {
				add_action( 'bloglo_enqueue_scripts', array( $this, 'enqueue_dynamic_style' ), 20 );
			} else {
				add_action( 'bloglo_enqueue_scripts', array( $this, 'print_dynamic_style' ), 99 );
			}

			// Include button styles.
			add_filter( 'bloglo_dynamic_styles', array( $this, 'get_button_styles' ), 6 );

			// Remove Customizer Custom CSS from wp_head, we will include it in our dynamic file.
			if ( ! is_customize_preview() ) {
				remove_action( 'wp_head', 'wp_custom_css_cb', 101 );
			}

			// Generate new styles on Customizer Save action.
			add_action( 'customize_save_after', array( $this, 'update_dynamic_file' ) );

			// Generate new styles on theme activation.
			add_action( 'after_switch_theme', array( $this, 'update_dynamic_file' ) );

			// Delete the css stye on theme deactivation.
			add_action( 'switch_theme', array( $this, 'delete_dynamic_file' ) );

			// Generate initial dynamic css.
			add_action( 'init', array( $this, 'init' ) );
		}

		/**
		 * Init.
		 *
		 * @since 1.0.0
		 */
		public function init() {

			// Ensure we have dynamic stylesheet generated.
			if ( false === get_transient( 'bloglo_has_dynamic_css' ) ) {
				$this->update_dynamic_file();
			}
		}

		/**
		 * Enqueues dynamic styles file.
		 *
		 * @since 1.0.0
		 */
		public function enqueue_dynamic_style() {

			$exists = file_exists( $this->dynamic_css_path . 'dynamic-styles.css' );
			// Generate the file if it's missing.
			if ( ! $exists ) {
				$exists = $this->update_dynamic_file();
			}

			// Enqueue the file if available.
			if ( $exists ) {
				wp_enqueue_style(
					'bloglo-dynamic-styles',
					$this->dynamic_css_uri . 'dynamic-styles.css',
					false,
					filemtime( $this->dynamic_css_path . 'dynamic-styles.css' ),
					'all'
				);
			}
		}

		/**
		 * Prints inline dynamic styles if writing to file is not possible.
		 *
		 * @since 1.0.0
		 */
		public function print_dynamic_style() {
			$dynamic_css = $this->get_css();
			wp_add_inline_style( 'bloglo-styles', $dynamic_css );
		}

		/**
		 * Generates dynamic CSS code, minifies it and cleans cache.
		 *
		 * @param  boolean $custom_css - should we include the wp_get_custom_css.
		 * @return string, minifed code
		 * @since  1.0.0
		 */
		public function get_css( $custom_css = false ) {

			// Refresh options.
			bloglo()->options->refresh();

			// Delete google fonts enqueue transients.
			delete_transient( 'bloglo_google_fonts_enqueue' );

			// Add our theme custom CSS.
			$css = '';

			// Dark Mode.
			if ( is_array( $header_active_widgets = bloglo_option( 'header_widgets' ) ) ) {
				array_walk(
					$header_active_widgets,
					function ( $value, $key ) use ( &$css ) {
						$darkMode = bloglo_option( 'dark_mode' );
						if ( $value['type'] == 'darkmode' || $darkMode ) {
							$css = '
							[data-theme=dark] {
								--bloglo-white: ' . bloglo_sanitize_color( bloglo_option( 'headings_color' ) ) . ';
								--bloglo-secondary: #ffffff !important;
							}
							[data-theme=dark] select option {
								background: rgba(0, 0, 0, 0.3);
  								color: #fff;
							}
							[data-theme=dark] .entry-media > a .entry-media-icon .bloglo-icon,
							[data-theme=dark] .entry-media > a .entry-media-icon svg,
							[data-theme=dark] #bloglo-scroll-top svg,
							[data-theme=dark] .navigation .nav-links .page-numbers svg,
							[data-theme=dark] .navigation .nav-links .page-numbers:hover svg,
							[data-theme=dark] .using-keyboard .navigation .nav-links .page-numbers:focus svg {
								fill: #fff;
							}
							[data-theme=dark] .wp-block-search .wp-block-search__button {
								--bloglo-white: #ffffff;
							}
							[data-theme=dark] #comments a,
							[data-theme=dark] #comments .comment-meta,
							[data-theme=dark] #colophon .search-form .search-submit,
							[data-theme=dark] #main .search-form .search-submit,
							[data-theme=dark] .content-area a:not(.bloglo-btn,.showcoupon,.wp-block-button__link):hover,
							[data-theme=dark] #secondary .hester-core-custom-list-widget .bloglo-entry a:not(.bloglo-btn):hover,
							[data-theme=dark] .bloglo-breadcrumbs a:hover,
							[data-theme=dark] #add_payment_method table.cart td.actions .coupon .input-text:focus,
							[data-theme=dark] .woocommerce-cart table.cart td.actions .coupon .input-text:focus,
							[data-theme=dark] .woocommerce-checkout table.cart td.actions .coupon .input-text:focus,
							[data-theme=dark] input[type="date"]:focus,
							[data-theme=dark] input[type="email"]:focus,
							[data-theme=dark] input[type="password"]:focus,
							[data-theme=dark] input[type="search"]:focus,
							[data-theme=dark] input[type="tel"]:focus,
							[data-theme=dark] input[type="text"]:focus,
							[data-theme=dark] input[type="url"]:focus,
							[data-theme=dark] textarea:focus,
							[data-theme=dark] .entry-media > a .entry-media-icon .bloglo-icon,
							[data-theme=dark] .entry-media > a .entry-media-icon svg,
							[data-theme=dark] .navigation .nav-links .page-numbers:hover button,
							[data-theme=dark] .using-keyboard .navigation .nav-links .page-numbers:focus button,
							[data-theme=dark] .navigation .nav-links .page-numbers:not(.prev, .next).current,
							[data-theme=dark] .navigation .nav-links .page-numbers:not(.prev, .next):hover,
							[data-theme=dark] .using-keyboard .navigation .nav-links .page-numbers:not(.prev, .next):focus,
							[data-theme=dark] .page-links a:hover span,
							[data-theme=dark] .using-keyboard .page-links a:focus span, .page-links > span,
							[data-theme=dark] .bloglo-btn.btn-text-1:hover,
							[data-theme=dark] .bloglo-btn.btn-text-1:focus,
							[data-theme=dark] .btn-text-1:hover, .btn-text-1:focus,
							[data-theme=dark] .bloglo-header-widgets .bloglo-search-simple .bloglo-search-form button:not(.bloglo-search-close),
							[data-theme=dark] #bloglo-header,
							[data-theme=dark] .bloglo-header-widgets a:not(.bloglo-btn),
							[data-theme=dark] .bloglo-logo a,
							[data-theme=dark] .bloglo-hamburger,
							[data-theme=dark] h1,
							[data-theme=dark] h2,
							[data-theme=dark] h3,
							[data-theme=dark] h4,
							[data-theme=dark] h5,
							[data-theme=dark] h6,
							[data-theme=dark] .h1,
							[data-theme=dark] .h2,
							[data-theme=dark] .h3,
							[data-theme=dark] .h4,
							[data-theme=dark] .bloglo-logo .site-title,
							[data-theme=dark] .error-404 .page-header h1,
							[data-theme=dark] body,
							[data-theme=dark] #bloglo-header .bloglo-nav li > a {
								color: #ffffff;
							}
							[data-theme=dark] .woocommerce table.my_account_orders thead th,
							[data-theme=dark] .woocommerce table.woocommerce-table--order-downloads thead th,
							[data-theme=dark] .woocommerce table.woocommerce-table--order-details thead th,
							[data-theme=dark] .bloglo-cart-item .bloglo-x,
							[data-theme=dark] .woocommerce form.login .lost_password a,
							[data-theme=dark] .woocommerce form.register .lost_password a,
							[data-theme=dark] .woocommerce a.remove,
							[data-theme=dark] #add_payment_method .cart-collaterals .cart_totals .woocommerce-shipping-destination,
							[data-theme=dark] .woocommerce-cart .cart-collaterals .cart_totals .woocommerce-shipping-destination,
							[data-theme=dark] .woocommerce-checkout .cart-collaterals .cart_totals .woocommerce-shipping-destination,
							[data-theme=dark] .woocommerce ul.products li.product .bloglo-loop-product__category-wrap a,
							[data-theme=dark] .woocommerce ul.products li.product .bloglo-loop-product__category-wrap,
							[data-theme=dark] .woocommerce .woocommerce-checkout-review-order table.shop_table thead th,
							[data-theme=dark] #add_payment_method #payment div.payment_box, .woocommerce-cart #payment div.payment_box,
							[data-theme=dark] .woocommerce-checkout #payment div.payment_box,
							[data-theme=dark] #add_payment_method #payment ul.payment_methods .about_paypal,
							[data-theme=dark] .woocommerce-cart #payment ul.payment_methods .about_paypal,
							[data-theme=dark] .woocommerce-checkout #payment ul.payment_methods .about_paypal,
							[data-theme=dark] .woocommerce table dl, .woocommerce table .wc-item-meta,
							[data-theme=dark] .widget.woocommerce .reviewer, .woocommerce.widget_shopping_cart .cart_list li a.remove::before,
							[data-theme=dark] .woocommerce .widget_shopping_cart .cart_list li a.remove::before,
							[data-theme=dark] .woocommerce .widget_shopping_cart .cart_list li .quantity,
							[data-theme=dark] .woocommerce.widget_shopping_cart .cart_list li .quantity,
							[data-theme=dark] .woocommerce div.product .woocommerce-product-rating .woocommerce-review-link,
							[data-theme=dark] .woocommerce div.product .woocommerce-tabs table.shop_attributes td,
							[data-theme=dark] .woocommerce div.product .product_meta > span span:not(.bloglo-woo-meta-title),
							[data-theme=dark] .woocommerce div.product .product_meta > span a,
							[data-theme=dark] .woocommerce .star-rating::before,
							[data-theme=dark] .woocommerce div.product #reviews #comments ol.commentlist li .comment-text p.meta,
							[data-theme=dark] .ywar_review_count, .woocommerce .add_to_cart_inline del,
							[data-theme=dark] .woocommerce div.product p.price del,
							[data-theme=dark] .woocommerce div.product span.price del,
							[data-theme=dark] .woocommerce #yith-wcwl-form table.shop_table thead,
							[data-theme=dark] .woocommerce .woocommerce-cart-form table.shop_table thead,
							[data-theme=dark] .woocommerce .woocommerce-checkout-review-order table.shop_table thead,
							[data-theme=dark] .woocommerce div.product .woocommerce-tabs ul.tabs li a,
							[data-theme=dark] .woocommerce #yith-wcwl-form table.shop_table,
							[data-theme=dark] .woocommerce .woocommerce-cart-form table.shop_table,
							[data-theme=dark] .woocommerce .woocommerce-checkout-review-order table.shop_table,
							[data-theme=dark] .bloglo-btn.btn-text-1,
							[data-theme=dark] .btn-text-1,
							[data-theme=dark] .comment-form .comment-notes,
							[data-theme=dark] #comments .no-comments,
							[data-theme=dark] #page .wp-caption .wp-caption-text, #comments .comment-meta,
							[data-theme=dark] .comments-closed,
							[data-theme=dark] .entry-meta,
							[data-theme=dark] .bloglo-entry cite,
							[data-theme=dark] legend,
							[data-theme=dark] .bloglo-page-header-description,
							[data-theme=dark] .page-links em,
							[data-theme=dark] .site-content .page-links em,
							[data-theme=dark] .single .entry-footer .last-updated,
							[data-theme=dark] .single .post-nav .post-nav-title,
							[data-theme=dark] #main .widget_recent_comments span,
							[data-theme=dark] #main .widget_recent_entries span,
							[data-theme=dark] #main .widget_calendar table > caption,
							[data-theme=dark] .post-thumb-caption,
							[data-theme=dark] .wp-block-image figcaption,
							[data-theme=dark] .wp-block-embed figcaption {
								color: rgba(255,255,255,0.7);
							}
							[data-theme=dark] #bloglo-header .bloglo-nav .children li.current_page_ancestor > a,
							[data-theme=dark] #bloglo-header .bloglo-nav .children li.current_page_item > a,
							[data-theme=dark] #bloglo-header .bloglo-nav .children li:hover > a,
							[data-theme=dark] #bloglo-header .bloglo-nav .sub-menu li.current-menu-ancestor > a,
							[data-theme=dark] #bloglo-header .bloglo-nav .sub-menu li.current-menu-item > a,
							[data-theme=dark] #bloglo-header .bloglo-nav .sub-menu li:hover > a {
								color: rgba(255,255,255,0.7) !important;
							}
							[data-theme=dark] .entry-meta .entry-meta-elements > span::before {
								background-color: rgba(255, 255, 255, 0.25);
							}

							[data-theme=dark] .bloglo-post-gallery .swiper-button-prev,
							[data-theme=dark] .bloglo-post-gallery .swiper-button-next,
							[data-theme=dark] .bloglo-vertical-slider .swiper-button-prev,
							[data-theme=dark] .bloglo-vertical-slider .swiper-button-next,
							[data-theme=dark] .bloglo-horizontal-slider .swiper-button-prev,
							[data-theme=dark] .bloglo-horizontal-slider .swiper-button-next,
							[data-theme=dark] .woocommerce #yith-wcwl-form table.shop_table th:first-child,
							[data-theme=dark] .woocommerce #yith-wcwl-form table.shop_table td:first-child,
							[data-theme=dark] .woocommerce .woocommerce-cart-form table.shop_table th:first-child,
							[data-theme=dark] .woocommerce .woocommerce-cart-form table.shop_table td:first-child,
							[data-theme=dark] .woocommerce .woocommerce-checkout-review-order table.shop_table th:first-child,
							[data-theme=dark] .woocommerce .woocommerce-checkout-review-order table.shop_table td:first-child,
							[data-theme=dark] .woocommerce #yith-wcwl-form table.shop_table td,
							[data-theme=dark] .woocommerce .woocommerce-cart-form table.shop_table td,
							[data-theme=dark] .woocommerce .woocommerce-checkout-review-order table.shop_table td,
							[data-theme=dark] .woocommerce #yith-wcwl-form table.shop_table tr:nth-last-child(2) td,
							[data-theme=dark] .woocommerce .woocommerce-cart-form table.shop_table tr:nth-last-child(2) td,
							[data-theme=dark] .woocommerce .cart_totals table.shop_table,
							[data-theme=dark] .woocommerce .cart_totals table.shop_table th,
							[data-theme=dark] .woocommerce .cart_totals table.shop_table td,
							[data-theme=dark] .bloglo-header-layout-5 #masthead+#main .bloglo-breadcrumbs,
							[data-theme=dark] #bloglo-topbar,
							[data-theme=dark] #bloglo-header-inner,
							[data-theme=dark] .page-header,
							[data-theme=dark] .bloglo-header-layout-3 .bloglo-nav-container,
							[data-theme=dark] .bloglo-header-layout-4 .bloglo-nav-container {
								border-color: rgba(255,255,255,0.08);
							}
							html[data-theme=dark] body,
							[data-theme=dark] .select2-dropdown,
							[data-theme=dark] .bloglo-header-layout-5 #masthead+#main .bloglo-breadcrumbs,
							[data-theme=dark] #add_payment_method #payment ul.payment_methods li:not(.woocommerce-notice),
							[data-theme=dark] .woocommerce-cart #payment ul.payment_methods li:not(.woocommerce-notice),
							[data-theme=dark] .woocommerce-checkout #payment ul.payment_methods li:not(.woocommerce-notice),
							html[data-theme=dark] .woocommerce div.product .woocommerce-tabs table.shop_attributes,
							[data-theme=dark] .bloglo-header-layout-4 .bloglo-nav-container,
							[data-theme=dark] .bloglo-header-layout-3 .bloglo-nav-container,
							[data-theme=dark] #bloglo-header-inner {
								background: ' . bloglo_sanitize_color( bloglo_option( 'headings_color' ) ) . ';
							}
							[data-theme=dark] .bloglo-hover-slider,
							[data-theme=dark] .select2-container--default .select2-selection--single,
							[data-theme=dark] .woocommerce .woocommerce-checkout-review-order table.shop_table,
							[data-theme=dark] .woocommerce #yith-wcwl-form table.shop_table thead th,
							[data-theme=dark] .woocommerce .woocommerce-cart-form table.shop_table thead th,
							[data-theme=dark] .woocommerce .woocommerce-checkout-review-order table.shop_table thead th,
							[data-theme=dark] .woocommerce .cart_totals table.shop_table .order-total th,
							[data-theme=dark] .woocommerce .cart_totals table.shop_table .order-total td,
							[data-theme=dark] .woocommerce div.product .woocommerce-tabs .wc-tab,
							[data-theme=dark] #page .woocommerce-error, #page .woocommerce-info,
							[data-theme=dark] #page .woocommerce-message,
							[data-theme=dark] .woocommerce div.product .woocommerce-tabs ul.tabs::before,
							[data-theme=dark] .woocommerce div.product .woocommerce-tabs ul.tabs::after,
							[data-theme=dark] .bloglo-layout__boxed-separated .ticker-slider-items,
							[data-theme=dark] .bloglo-layout__boxed-separated .pyml-slider-items,
							[data-theme=dark] .bloglo-layout__framed #page,
							[data-theme=dark] .bloglo-layout__boxed #page,
							[data-theme=dark] .bloglo-layout__boxed-separated:not(.blog, .archive, .category, .search-results) #content,
							[data-theme=dark] .bloglo-layout__boxed-separated.author .author-box,
							[data-theme=dark] .bloglo-layout__boxed-separated.single #content,
							[data-theme=dark] .bloglo-layout__boxed-separated.bloglo-sidebar-style-3 #secondary .bloglo-widget,
							[data-theme=dark] .bloglo-layout__boxed-separated.bloglo-sidebar-style-3 .elementor-widget-sidebar .bloglo-widget,
							[data-theme=dark] .bloglo-layout__boxed-separated.archive .bloglo-article,
							[data-theme=dark] .bloglo-layout__boxed-separated.blog .bloglo-article,
							[data-theme=dark] .bloglo-layout__boxed-separated.search-results .bloglo-article,
							[data-theme=dark] .bloglo-layout__boxed-separated.category .bloglo-article {
								background-color: rgba(0,0,0,0.3);
							}
							[data-theme=dark] .woocommerce ul.products li.product:hover,
							[data-theme=dark] .woocommerce ul.products li.product:focus-within,
							[data-theme=dark] .bloglo-layout__framed #page,
							[data-theme=dark] .bloglo-layout__boxed #page {
								-webkit-box-shadow: 0 0 3.5rem rgba(0, 0, 0, 0.4);
								box-shadow: 0 0 3.5rem rgba(0, 0, 0, 0.4);
							}
							[data-theme=dark] .bloglo-btn.btn-text-1 > span::before {
								background-color: #fff;
							}
							[data-theme=dark] .woocommerce .quantity .bloglo-woo-minus:not(:hover, :focus),
							[data-theme=dark] .woocommerce .quantity .bloglo-woo-plus:not(:hover, :focus) {
								color: ' . bloglo_sanitize_color( bloglo_option( 'headings_color' ) ) . ' !important;
							}
							[data-theme=dark] .bloglo-layout__boxed-separated .ticker-slider-items,
							[data-theme=dark] .bloglo-layout__boxed-separated .pyml-slider-items,
							[data-theme=dark] .bloglo-layout__boxed-separated.bloglo-sidebar-style-3 #secondary .bloglo-widget,
							[data-theme=dark] .bloglo-layout__boxed-separated.archive article.bloglo-article,
							[data-theme=dark] .bloglo-layout__boxed-separated.blog article.bloglo-article,
							[data-theme=dark] .bloglo-layout__boxed-separated.category article.bloglo-article,
							[data-theme=dark] .bloglo-layout__boxed-separated.search-results article.bloglo-article {
								border-color: rgba(190,190,190,0.30);
							}
							[data-theme=dark] .bloglo-social-nav.rounded > ul > li > a > span:not(.screen-reader-text) {
								background-color: rgba(190,190,190,0.30);
							}
							[data-theme=dark] .bloglo-blog-layout-1 .bloglo-article .entry-thumb-image,
							[data-theme=dark] .pyml-slide-item .pyml-slider-backgrounds .pyml-slide-bg {
								background-color: rgba(39,39,39,.75);
							}
							@media screen and (max-width: ' . intval( bloglo_option( 'main_nav_mobile_breakpoint' ) ) . 'px) {
								[data-theme=dark] .bloglo-layout__boxed-separated #page {
									background-color: rgba(0,0,0,0.3);
								}
								[data-theme=dark] #bloglo-header-inner .site-navigation > ul li {
									border-bottom-color: rgba(255,255,255,0.08);
								}
								[data-theme=dark] #bloglo-header-inner .site-navigation {
									background: ' . bloglo_sanitize_color( bloglo_option( 'headings_color' ) ) . ';
								}
								[data-theme=dark] .bloglo-mobile-toggen,
								[data-theme=dark] #bloglo-header-inner .bloglo-nav {
									color: rgba(255,255,255,0.7);
								}
								[data-theme=dark] #bloglo-header-inner .bloglo-nav .menu-item-has-children > a > span,
								[data-theme=dark] #bloglo-header-inner .bloglo-nav .page_item_has_children > a > span {
									border-right-color: rgba(255,255,255,0.08);
								}
								[data-theme=dark] #bloglo-header-inner .site-navigation > ul .sub-menu {
									background: rgba(0,0,0,0.3);
								}
							}
							';
						}
					}
				);
			}

			// Accent color.
			$accent_color = bloglo_option( 'accent_color' );

			$css .= '
				:root {
					--bloglo-primary: ' . bloglo_sanitize_color( $accent_color ) . ';
					--bloglo-primary_15: ' . bloglo_sanitize_color( bloglo_luminance( $accent_color, .15 ) ) . ';
					--bloglo-primary_27: ' . bloglo_sanitize_color( bloglo_hex2rgba( $accent_color, .27 ) ) . ';
					--bloglo-primary_09: ' . bloglo_sanitize_color( bloglo_hex2rgba( $accent_color, .09 ) ) . ';
					--bloglo-primary_04: ' . bloglo_sanitize_color( bloglo_hex2rgba( $accent_color, .04 ) ) . ';
				}
			';

			$header_layout_3_additional_css = '';

			if ( 'layout-3' === bloglo_option( 'header_layout' ) || is_customize_preview() ) {
				$header_layout_3_additional_css = '

					.bloglo-header-layout-3 .bloglo-logo-container > .bloglo-container {
						flex-wrap: wrap;
					}

					.bloglo-header-layout-3 .bloglo-logo-container .bloglo-logo > .logo-inner {
						align-items: flex-start;
					}
					
					.bloglo-header-layout-3 .bloglo-logo-container .bloglo-logo {
						order: 0;
						align-items: flex-start;
						flex-basis: auto;
						margin-left: 0;
					}

					.bloglo-header-layout-3 .bloglo-logo-container .bloglo-header-element {
						flex-basis: auto;
					}

					.bloglo-header-layout-3 .bloglo-logo-container .bloglo-mobile-nav {
						order: 5;
					}

				';
			}

			$css .= '
						@media screen and (min-width: ' . intval( bloglo_option( 'main_nav_mobile_breakpoint' ) + 1 ) . 'px) {
							
							.blogtick.bloglo-header-layout-3 #bloglo-topbar+#bloglo-header .bloglo-logo-container {
								padding-top: 0;
							}
							
							.blogtick.bloglo-header-layout-3 .bloglo-logo-container {
								padding-top: 4rem;
								padding-bottom: 1rem;
								margin-bottom: 3.5rem;
							}
							
							.blogtick.bloglo-header-layout-3 .bloglo-nav-container {
								min-width: 80rem;
								width: auto;
								max-width: max-content;
								margin: 0 auto;
								border-radius: 4rem;
							}
							
							.blogtick.bloglo-header-layout-3 .bloglo-nav-container:after {
								content: "";
								position: absolute;
								top: 0;
								left: -0.4rem;
								right: -0.4rem;
								bottom: -1rem;
								z-index: -1;
								border-radius: 0 0 4rem 4rem;
								border-bottom-width: 1rem;
								border-bottom-style: solid;
								border-bottom-color: inherit;
							}
							
							.blogtick.bloglo-header-layout-3 .bloglo-nav-container>.bloglo-container:before,
							.blogtick.bloglo-header-layout-3 .bloglo-nav-container>.bloglo-container:after {
								content: "";
								position: absolute;
								width: 2rem;
								height: 0.4rem;
								background-color: #000;
								top: 50%;
								margin-top: -0.2rem;
							}
							
							.blogtick.bloglo-header-layout-3 .bloglo-nav-container>.bloglo-container:before {
								left: 0;
							}
							
							.blogtick.bloglo-header-layout-3 .bloglo-nav-container>.bloglo-container:after {
								right: 0;
							}
							
							.blogtick.bloglo-header-layout-3 #bloglo-header-inner .bloglo-nav>ul {
								min-height: 6.2rem;
							}
							
							.blogtick.bloglo-header-layout-3 #bloglo-header-inner .bloglo-nav>ul>li>a {
								font-weight: 600;
							}
							
							.blogtick.bloglo-header-layout-3 .bloglo-logo-container .bloglo-logo:after {
								content: "";
								position: absolute;
								bottom: -1rem;
								width: 32rem;
								height: 9px;
								background: #000;
								--mask: url(\'data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" width="350" height="9" viewBox="0 0 350 9"><path fill="currentColor" d="M350 6.923 347.688 9l-5.397-4.847-3.084 2.77L336.895 9l-2.313-2.077-3.084-2.77-3.084 2.77L326.102 9l-2.313-2.077-3.084-2.77-3.084 2.77L315.309 9l-2.313-2.077-3.084-2.77-3.084 2.77L304.516 9l-2.313-2.077-3.084-2.77-3.084 2.77L293.723 9l-2.313-2.077-3.084-2.77-3.084 2.77L282.93 9l-2.312-2.077-3.085-2.77-3.084 2.77L272.137 9l-2.312-2.077-3.085-2.77-3.084 2.77L261.344 9l-2.312-2.077-3.085-2.77-3.084 2.77L250.551 9l-2.312-2.077-3.085-2.77-3.084 2.77L239.758 9l-2.312-2.077-3.085-2.77-3.084 2.77L228.965 9l-2.312-2.077-3.085-2.77-3.084 2.77L218.172 9l-2.312-2.077-3.085-2.77-3.084 2.77L207.379 9l-2.312-2.077-3.085-2.77-3.084 2.77L196.586 9l-2.312-2.077-3.085-2.77-3.084 2.77L185.793 9l-2.312-2.077-3.085-2.77-3.084 2.77L175 9l-2.312-2.077-3.084-2.77-3.085 2.77L164.207 9l-2.312-2.077-3.084-2.77-3.085 2.77L153.414 9l-2.312-2.077-3.084-2.77-3.085 2.77L142.621 9l-2.312-2.077-3.084-2.77-3.085 2.77L131.828 9l-2.312-2.077-3.084-2.77-3.085 2.77L121.035 9l-2.312-2.077-3.084-2.77-3.085 2.77L110.242 9l-2.312-2.077-3.084-2.77-3.085 2.77L99.449 9l-2.312-2.077-3.084-2.77-3.084 2.77L88.656 9l-2.312-2.077-3.084-2.77-3.085 2.77L77.864 9l-2.312-2.077-3.084-2.77-3.085 2.77L67.07 9l-2.312-2.077-3.084-2.77-3.084 2.77L56.276 9l-2.312-2.077-3.084-2.77-3.084 2.77L45.484 9l-2.312-2.077-3.084-2.77-3.084 2.77L34.69 9 32.38 6.923l-3.084-2.77-3.084 2.77L23.898 9l-2.312-2.077-3.084-2.77-3.084 2.77L13.105 9l-2.312-2.077-3.084-2.77L2.312 9 0 6.923 7.709 0l5.396 4.847L18.502 0l5.396 4.847L29.295 0l5.396 4.847L40.088 0l5.396 4.847L50.881 0l5.396 4.847L61.674 0l5.396 4.847L72.467 0l5.396 4.847L83.26 0l5.396 4.847L94.053 0l5.396 4.847L104.846 0l5.396 4.847L115.639 0l5.396 4.847L126.432 0l5.396 4.847L137.225 0l5.396 4.847L148.018 0l5.396 4.847L158.811 0l5.396 4.847L169.604 0 175 4.847 180.396 0l5.397 4.847L191.189 0l5.397 4.847L201.982 0l5.397 4.847L212.775 0l5.397 4.847L223.568 0l5.397 4.847L234.361 0l5.397 4.847L245.154 0l5.397 4.847L255.947 0l5.397 4.847L266.74 0l5.397 4.847L277.533 0l5.397 4.847L288.326 0l5.397 4.847L299.119 0l5.397 4.847L309.912 0l5.397 4.847L320.705 0l5.397 4.847L331.498 0l5.397 4.847L342.291 0 350 6.923Z"/></svg>\');
								-webkit-mask: var(--mask);
								mask: var(--mask);
							}
						}';

			$header_layout_4_additional_css = '';

			if ( 'layout-4' === bloglo_option( 'header_layout' ) || is_customize_preview() ) {
				$header_layout_4_additional_css = '

					.bloglo-header-layout-4 .bloglo-logo-container > .bloglo-container {
						flex-wrap: wrap;
					}

					.bloglo-header-layout-4 .bloglo-logo-container .bloglo-logo > .logo-inner {
						align-items: flex-start;
					}
					
					.bloglo-header-layout-4 .bloglo-logo-container .bloglo-logo {
						order: 0;
						align-items: flex-start;
						flex-basis: auto;
						margin-left: 0;
					}

					.bloglo-header-layout-4 .bloglo-logo-container .bloglo-header-element {
						flex-basis: auto;
					}

					.bloglo-header-layout-4 .bloglo-logo-container .bloglo-mobile-nav {
						order: 5;
					}

					.bloglo-header-layout-4 .bloglo-widget-location-left .dropdown-item {
						left: auto;
						right: -7px;
					}

					.bloglo-header-layout-4 .bloglo-widget-location-left .dropdown-item::after {
						left: auto;
						right: 8px;
					}

					.bloglo-header-layout-4 .bloglo-logo-container .bloglo-widget-location-right:not(.bloglo-header-widgets-two) {
						-js-display: flex;
						display: -webkit-box;
						display: -ms-flexbox;
						display: flex;
					}

					.bloglo-header-layout-4 .bloglo-nav-container .bloglo-header-element,
					.bloglo-header-layout-4 .bloglo-header-widgets.bloglo-header-widgets-two {
						display: none;
					}

				';
			}

			$css .= '
						@media screen and (max-width: ' . intval( bloglo_option( 'main_nav_mobile_breakpoint' ) ) . 'px) {
							
							' . $header_layout_4_additional_css . '
						}';

			if ( 'layout-4' === bloglo_option( 'header_layout' ) ) {

				// Background.
				$css .= $this->get_design_options_field_css( '.bloglo-header-layout-4 .bloglo-nav-container', 'main_nav_background', 'background' );

				// Border.
				$css .= $this->get_design_options_field_css( '.bloglo-header-layout-4 .bloglo-nav-container', 'main_nav_border', 'border' );
			}

			/**
			 * Top Bar.
			 */

			// Background.
			$css .= $this->get_design_options_field_css( '#bloglo-topbar', 'top_bar_background', 'background' );

			// Border.
			$css .= $this->get_design_options_field_css( '#bloglo-topbar', 'top_bar_border', 'border' );
			$css .= $this->get_design_options_field_css( '.bloglo-topbar-widget', 'top_bar_border', 'separator_color' );

			// Top Bar colors.
			$topbar_color = bloglo_option( 'top_bar_text_color' );

			// Top Bar text color.
			if ( isset( $topbar_color['text-color'] ) && $topbar_color['text-color'] ) {
				$css .= '#bloglo-topbar { color: ' . bloglo_sanitize_color( $topbar_color['text-color'] ) . '; }';
			}

			// Top Bar link color.
			if ( isset( $topbar_color['link-color'] ) && $topbar_color['link-color'] ) {
				$css .= '
					.bloglo-topbar-widget__text a,
					.bloglo-topbar-widget .bloglo-nav > ul > li > a,
					.bloglo-topbar-widget__socials .bloglo-social-nav > ul > li > a,
					#bloglo-topbar .bloglo-topbar-widget__text .bloglo-icon { 
						color: ' . bloglo_sanitize_color( $topbar_color['link-color'] ) . '; }
				';
			}

			// Top Bar link hover color.
			if ( isset( $topbar_color['link-hover-color'] ) && $topbar_color['link-hover-color'] ) {
				$css .= '
					#bloglo-topbar .bloglo-nav > ul > li > a:hover,
					#bloglo-topbar .bloglo-nav > ul > li.menu-item-has-children:hover > a,
					#bloglo-topbar .bloglo-nav > ul > li.current-menu-item > a,
					#bloglo-topbar .bloglo-nav > ul > li.current-menu-ancestor > a,
					#bloglo-topbar .bloglo-topbar-widget__text a:hover,
					.using-keyboard #bloglo-topbar .bloglo-topbar-widget__text a:focus,
					#bloglo-topbar .bloglo-social-nav > ul > li > a .bloglo-icon.bottom-icon { 
						color: ' . bloglo_sanitize_color( $topbar_color['link-hover-color'] ) . '; }
				';
			}

			/**
			 * Header.
			 */

			// Background.
			$css .= $this->get_design_options_field_css( '#bloglo-header-inner', 'header_background', 'background' );

			// Font colors.
			$header_color = bloglo_option( 'header_text_color' );

			// Header text color.
			if ( isset( $header_color['text-color'] ) && $header_color['text-color'] ) {
				$css .= '.bloglo-logo .site-description { color: ' . bloglo_sanitize_color( $header_color['text-color'] ) . '; }';
			}

			// Header link color.
			if ( isset( $header_color['link-color'] ) && $header_color['link-color'] ) {
				$css .= '
					#bloglo-header,
					.bloglo-header-widgets a:not(.bloglo-btn),
					.bloglo-logo a,
					.bloglo-hamburger { 
						color: ' . bloglo_sanitize_color( $header_color['link-color'] ) . '; }
				';
			}

			// Header link hover color.
			if ( isset( $header_color['link-hover-color'] ) && $header_color['link-hover-color'] ) {
				$css .= '
					.bloglo-header-widgets a:not(.bloglo-btn):hover, 
					#bloglo-header-inner .bloglo-header-widgets .bloglo-active,
					.bloglo-logo .site-title a:hover, 
					.bloglo-hamburger:hover, 
					.is-mobile-menu-active .bloglo-hamburger,
					#bloglo-header-inner .bloglo-nav > ul > li > a:hover,
					#bloglo-header-inner .bloglo-nav > ul > li.menu-item-has-children:hover > a,
					#bloglo-header-inner .bloglo-nav > ul > li.current-menu-item > a,
					#bloglo-header-inner .bloglo-nav > ul > li.current-menu-ancestor > a,
					#bloglo-header-inner .bloglo-nav > ul > li.page_item_has_children:hover > a,
					#bloglo-header-inner .bloglo-nav > ul > li.current_page_item > a,
					#bloglo-header-inner .bloglo-nav > ul > li.current_page_ancestor > a {
						color: ' . bloglo_sanitize_color( $header_color['link-hover-color'] ) . ';
					}
					.bloglo-menu-animation-squareborder:not(.bloglo-is-mobile) #bloglo-header-inner .bloglo-nav > ul > li > a:hover,
					.bloglo-menu-animation-squareborder:not(.bloglo-is-mobile) #bloglo-header-inner .bloglo-nav > ul > li.menu-item-has-children:hover > a,
					.bloglo-menu-animation-squareborder:not(.bloglo-is-mobile) #bloglo-header-inner .bloglo-nav > ul > li.current-menu-item > a,
					.bloglo-menu-animation-squareborder:not(.bloglo-is-mobile) #bloglo-header-inner .bloglo-nav > ul > li.current-menu-ancestor > a,
					.bloglo-menu-animation-squareborder:not(.bloglo-is-mobile) #bloglo-header-inner .bloglo-nav > ul > li.page_item_has_children:hover > a,
					.bloglo-menu-animation-squareborder:not(.bloglo-is-mobile) #bloglo-header-inner .bloglo-nav > ul > li.current_page_item > a,
					.bloglo-menu-animation-squareborder:not(.bloglo-is-mobile) #bloglo-header-inner .bloglo-nav > ul > li.current_page_ancestor > a {
						color: ' . bloglo_sanitize_color( $header_color['link-hover-color'] ) . ';
						border-color: ' . bloglo_sanitize_color( $header_color['link-hover-color'] ) . ';
					}
				';
			}

			// Header border.
			$css .= $this->get_design_options_field_css( '#bloglo-header-inner', 'header_border', 'border' );

			// Header separator color.
			$css .= $this->get_design_options_field_css( '.bloglo-header-widget', 'header_border', 'separator_color' );

			// Main navigation breakpoint.
			$css .= '
				@media screen and (max-width: ' . intval( bloglo_option( 'main_nav_mobile_breakpoint' ) ) . 'px) {

					#bloglo-header-inner .bloglo-nav {
						display: none;
						color: #000;
					}
					.bloglo-mobile-toggen,
					.bloglo-mobile-nav {
						display: inline-flex;
					}

					#bloglo-header-inner {
						position: relative;
					}

					#bloglo-header-inner .bloglo-nav > ul > li > a {
						color: inherit;
					}

					#bloglo-header-inner .bloglo-nav-container {
						position: static;
						border: none;
					}

					#bloglo-header-inner .site-navigation {
						display: none;
						position: absolute;
						top: 100%;
						width: 100%;
						height: 100%;
						min-height: 100vh;
						left: 0;
						right: 0;
						margin: -1px 0 0;
						background: #FFF;
						border-top: 1px solid #eaeaea;
						box-shadow: 0 15px 25px -10px  rgba(50, 52, 54, 0.125);
						z-index: 999;
						font-size: 1.7rem;
						padding: 0;
					}

					.bloglo-header-layout-5 #bloglo-header-inner .site-navigation {
						min-height: unset;
						border-radius: 15px;
						height: unset;
					}

					#bloglo-header-inner .site-navigation > ul {
						overflow-y: auto;
						max-height: 68vh;
						display: block;
					}

					#bloglo-header-inner .site-navigation > ul > li > a {
						padding: 0 !important;
					}

					#bloglo-header-inner .site-navigation > ul li {
						display: block;
						width: 100%;
						padding: 0;
						margin: 0;
						margin-left: 0 !important;
					}

					#bloglo-header-inner .site-navigation > ul .sub-menu {
						position: static;
						display: none;
						border: none;
						box-shadow: none;
						border: 0;
						opacity: 1;
						visibility: visible;
						font-size: 1.7rem;
						transform: none;
						background: #f8f8f8;
						pointer-events: all;
						min-width: initial;
						left: 0;
						padding: 0;
						margin: 0;
						border-radius: 0;
						line-height: inherit;
					}

					#bloglo-header-inner .site-navigation > ul .sub-menu > li > a > span {
						padding-left: 50px !important;
					}

					#bloglo-header-inner .site-navigation > ul .sub-menu .sub-menu > li > a > span {
						padding-left: 70px !important;
					}

					#bloglo-header-inner .site-navigation > ul .sub-menu a > span {
						padding: 10px 30px 10px 50px;
					}

					#bloglo-header-inner .site-navigation > ul a {
						padding: 0;
						position: relative;
						background: none;
					}

					#bloglo-header-inner .site-navigation > ul li {
						border-bottom: 1px solid #eaeaea;
					}

					#bloglo-header-inner .site-navigation > ul > li:last-child {
						border-bottom: 0;
					}

					#bloglo-header-inner .site-navigation > ul a > span {
						padding: 10px 30px !important;
						width: 100%;
						display: block;
					}

					#bloglo-header-inner .site-navigation > ul a > span::after,
					#bloglo-header-inner .site-navigation > ul a > span::before {
						display: none !important;
					}

					#bloglo-header-inner .site-navigation > ul a > span.description {
						display: none;
					}

					#bloglo-header-inner .site-navigation > ul .menu-item-has-children > a {
						display: inline-flex;
    					width: 100%;
						max-width: calc(100% - 50px);
					}

					#bloglo-header-inner .bloglo-nav .menu-item-has-children>a > span, 
					#bloglo-header-inner .bloglo-nav .page_item_has_children>a > span {
					    border-right: 1px solid rgba(0,0,0,.09);
					}

					#bloglo-header-inner .bloglo-nav .menu-item-has-children>a > .bloglo-icon, 
					#bloglo-header-inner .bloglo-nav .page_item_has_children>a > .bloglo-icon {
						transform: none;
						width: 50px;
					    margin: 0;
					    position: absolute;
					    right: 0;
					    pointer-events: none;
					    height: 1em;
						display: none;
					}

					.bloglo-header-layout-3 .bloglo-widget-location-left .dropdown-item {
						left: auto;
						right: -7px;
					}

					.bloglo-header-layout-3 .bloglo-widget-location-left .dropdown-item::after {
						left: auto;
						right: 8px;
					}

					.bloglo-nav .sub-menu li.current-menu-item > a {
						font-weight: 500;
					}

					.bloglo-mobile-toggen {
						width: 50px;
						height: 1em;
						background: none;
						border: none;
						cursor: pointer;
					}

					.bloglo-mobile-toggen .bloglo-icon {
						transform: none;
						width: 50px;
						margin: 0;
						position: absolute;
						right: 0;
						pointer-events: none;
						height: 1em;
					}

					#bloglo-header-inner .site-navigation > ul .menu-item-has-children.bloglo-open > .bloglo-mobile-toggen > .bloglo-icon {
						transform: rotate(180deg);
					}

					' . $header_layout_3_additional_css . '
				}
			';

			/**
			 * Main Navigation.
			 */

			// Font Color.
			$main_nav_font_color = bloglo_option( 'main_nav_font_color' );

			if ( $main_nav_font_color['link-color'] ) {
				$css .= '#bloglo-header-inner .bloglo-nav > ul > li > a { color: ' . $main_nav_font_color['link-color'] . '; }';
			}

			if ( $main_nav_font_color['link-hover-color'] ) {
				$css .= '
					#bloglo-header-inner .bloglo-nav > ul > li > a:hover,
					#bloglo-header-inner .bloglo-nav > ul > li.menu-item-has-children:hover > a,
					#bloglo-header-inner .bloglo-nav > ul > li.current-menu-item > a,
					#bloglo-header-inner .bloglo-nav > ul > li.current-menu-ancestor > a,
					#bloglo-header-inner .bloglo-nav > ul > li.page_item_has_children:hover > a,
					#bloglo-header-inner .bloglo-nav > ul > li.current_page_item > a,
					#bloglo-header-inner .bloglo-nav > ul > li.current_page_ancestor > a {
						color: ' . bloglo_sanitize_color( $main_nav_font_color['link-hover-color'] ) . ';
					}
					.bloglo-menu-animation-squareborder:not(.bloglo-is-mobile) #bloglo-header-inner .bloglo-nav > ul > li > a:hover,
					.bloglo-menu-animation-squareborder:not(.bloglo-is-mobile) #bloglo-header-inner .bloglo-nav > ul > li.menu-item-has-children:hover > a,
					.bloglo-menu-animation-squareborder:not(.bloglo-is-mobile) #bloglo-header-inner .bloglo-nav > ul > li.current-menu-item > a,
					.bloglo-menu-animation-squareborder:not(.bloglo-is-mobile) #bloglo-header-inner .bloglo-nav > ul > li.current-menu-ancestor > a,
					.bloglo-menu-animation-squareborder:not(.bloglo-is-mobile) #bloglo-header-inner .bloglo-nav > ul > li.page_item_has_children:hover > a,
					.bloglo-menu-animation-squareborder:not(.bloglo-is-mobile) #bloglo-header-inner .bloglo-nav > ul > li.current_page_item > a,
					.bloglo-menu-animation-squareborder:not(.bloglo-is-mobile) #bloglo-header-inner .bloglo-nav > ul > li.current_page_ancestor > a {
						color: ' . bloglo_sanitize_color( $main_nav_font_color['link-hover-color'] ) . ';
						border-color: ' . bloglo_sanitize_color( $main_nav_font_color['link-hover-color'] ) . ';
					}
				';
			}

			if ( 'layout-3' === bloglo_option( 'header_layout' ) ) {

				// Background.
				$css .= $this->get_design_options_field_css( '.bloglo-header-layout-3 .bloglo-nav-container', 'main_nav_background', 'background' );

				// Border.
				$css .= $this->get_design_options_field_css( '.bloglo-header-layout-3 .bloglo-nav-container', 'main_nav_border', 'border' );
			}

			// Font size.
			$css .= $this->get_range_field_css( '.bloglo-nav.bloglo-header-element, .bloglo-header-layout-1 .bloglo-header-widgets, .bloglo-header-layout-2 .bloglo-header-widgets', 'font-size', 'main_nav_font_size', false );

			/**
			 * Hero Section.
			 */
			if ( bloglo_option( 'enable_hero' ) ) {
				// Hero height.
				$css .= '#hero .bloglo-hover-slider .hover-slide-item { height: ' . intval( bloglo_option( 'hero_hover_slider_height' ) ) . 'px; }';
			}

			/**
			 * Pre Footer.
			 */
			if ( bloglo_option( 'enable_pre_footer_cta' ) ) {

				// Call to Action.
				if ( bloglo_option( 'enable_pre_footer_cta' ) ) {

					$cta_style = absint( bloglo_option( 'pre_footer_cta_style' ) );

					// Background.
					$cta_background = bloglo_option( 'pre_footer_cta_background' );

					if ( 1 === $cta_style || is_customize_preview() ) {
						$css .= $this->get_design_options_field_css( '.bloglo-pre-footer-cta-style-1 #bloglo-pre-footer .bloglo-flex-row::after', 'pre_footer_cta_background', 'background' );
					}

					if ( 2 === $cta_style || is_customize_preview() ) {
						$css .= $this->get_design_options_field_css( '.bloglo-pre-footer-cta-style-2 #bloglo-pre-footer::after', 'pre_footer_cta_background', 'background' );
					}

					if ( 'image' === $cta_background['background-type'] && isset( $cta_background['background-color-overlay'] ) && $cta_background['background-color-overlay'] ) {
						$css .= '
							.bloglo-pre-footer-cta-style-1 #bloglo-pre-footer .bloglo-flex-row::before,
			 				.bloglo-pre-footer-cta-style-2 #bloglo-pre-footer::before {
			 					background-color: ' . bloglo_sanitize_color( $cta_background['background-color-overlay'] ) . ';
			 				}
			 				';
					}

					// Text color.
					$css .= $this->get_design_options_field_css( '#bloglo-pre-footer .h2, #bloglo-pre-footer .h3, #bloglo-pre-footer .h4', 'pre_footer_cta_text_color', 'color' );

					// Border.
					if ( 1 === $cta_style || is_customize_preview() ) {
						$css .= $this->get_design_options_field_css( '.bloglo-pre-footer-cta-style-1 #bloglo-pre-footer .bloglo-flex-row::before', 'pre_footer_cta_border', 'border' );
					}

					if ( 2 === $cta_style || is_customize_preview() ) {
						$css .= $this->get_design_options_field_css( '.bloglo-pre-footer-cta-style-2 #bloglo-pre-footer::before', 'pre_footer_cta_border', 'border' );
					}

					// Font size.
					$css .= $this->get_range_field_css( '#bloglo-pre-footer .h3', 'font-size', 'pre_footer_cta_font_size', true );
				}
			}

			// Footer Background.
			if ( bloglo_option( 'enable_footer' ) || bloglo_option( 'enable_copyright' ) ) {

				// Background.
				$css .= $this->get_design_options_field_css( '#colophon', 'footer_background', 'background' );

				// Footer font color.
				$footer_font_color = bloglo_option( 'footer_text_color' );

				// Footer text color.
				if ( isset( $footer_font_color['text-color'] ) && $footer_font_color['text-color'] ) {
					$css .= '
						#colophon { 
							color: ' . bloglo_sanitize_color( $footer_font_color['text-color'] ) . ';
						}
					';
				}

				// Footer link color.
				if ( isset( $footer_font_color['link-color'] ) && $footer_font_color['link-color'] ) {
					$css .= '
						#colophon a { 
							color: ' . bloglo_sanitize_color( $footer_font_color['link-color'] ) . '; 
						}
					';
				}

				// Footer link hover color.
				if ( isset( $footer_font_color['link-hover-color'] ) && $footer_font_color['link-hover-color'] ) {
					$css .= '
						#colophon a:not(.bloglo-btn):hover,
						.using-keyboard #colophon a:not(.bloglo-btn):focus,
						#colophon li.current_page_item > a,
						#colophon .bloglo-social-nav > ul > li > a .bloglo-icon.bottom-icon { 
							color: ' . bloglo_sanitize_color( $footer_font_color['link-hover-color'] ) . ';
						}
					';
				}

				// Footer widget title.
				if ( isset( $footer_font_color['widget-title-color'] ) && $footer_font_color['widget-title-color'] ) {
					$css .= '
						#colophon .widget-title, #colophon .wp-block-heading { 
							color: ' . bloglo_sanitize_color( $footer_font_color['widget-title-color'] ) . ';
						}
					';
				}
			}

			// Main Footer border.
			if ( bloglo_option( 'enable_footer' ) ) {

				// Border.
				$footer_border = bloglo_option( 'footer_border' );

				if ( $footer_border['border-top-width'] ) {
					$css .= '
						#colophon {
							border-top-width: ' . intval( $footer_border['border-top-width'] ) . 'px;
							border-top-style: ' . sanitize_text_field( $footer_border['border-style'] ) . ';
							border-top-color: ' . bloglo_sanitize_color( $footer_border['border-color'] ) . ';
						}
					';
				}

				if ( $footer_border['border-bottom-width'] ) {
					$css .= '
						#colophon {
							border-bottom-width: ' . intval( $footer_border['border-bottom-width'] ) . 'px;
							border-bottom-style: ' . sanitize_text_field( $footer_border['border-style'] ) . ';
							border-bottom-color: ' . bloglo_sanitize_color( $footer_border['border-color'] ) . ';
						}
					';
				}
			}

			// Sidebar.
			$css .= '
				#secondary {
					width: ' . intval( bloglo_option( 'sidebar_width' ) ) . '%;
				}

				body:not(.bloglo-no-sidebar) #primary {
					max-width: ' . intval( 100 - intval( bloglo_option( 'sidebar_width' ) ) ) . '%;
				}
			';

			// Content background.
			$boxed_content_background_color = bloglo_sanitize_color( bloglo_option( 'boxed_content_background_color' ) );

			// Boxed Separated Layout specific CSS.
			$css .= '
				.bloglo-layout__boxed-separated .ticker-slider-items,
				.bloglo-layout__boxed-separated .pyml-slider-items,
				.bloglo-layout__boxed-separated.author .author-box,
				.bloglo-layout__boxed-separated #content, 
				.bloglo-layout__boxed-separated.bloglo-sidebar-style-3 #secondary .bloglo-widget, 
				.bloglo-layout__boxed-separated.bloglo-sidebar-style-3 .elementor-widget-sidebar .bloglo-widget, 
				.bloglo-layout__boxed-separated.archive .bloglo-article,
				.bloglo-layout__boxed-separated.blog .bloglo-article, 
				.bloglo-layout__boxed-separated.search-results .bloglo-article, 
				.bloglo-layout__boxed-separated.category .bloglo-article {
					background-color: ' . $boxed_content_background_color . ';
				}				
			';

			$css .= '
				.bloglo-layout__boxed #page {
					background-color: ' . $boxed_content_background_color . ';
				}
			';

			// Content text color.
			$content_text_color = bloglo_sanitize_color( bloglo_option( 'content_text_color' ) );

			$css .= '
				body {
					color: ' . $content_text_color . ';
				}

				:root {
					--bloglo-secondary_38: ' . $content_text_color . ';
				}

				.comment-form .comment-notes,
				#comments .no-comments,
				#page .wp-caption .wp-caption-text,
				#comments .comment-meta,
				.comments-closed,
				.entry-meta,
				.bloglo-entry cite,
				legend,
				.bloglo-page-header-description,
				.page-links em,
				.site-content .page-links em,
				.single .entry-footer .last-updated,
				.single .post-nav .post-nav-title,
				#main .widget_recent_comments span,
				#main .widget_recent_entries span,
				#main .widget_calendar table > caption,
				.post-thumb-caption,
				.wp-block-image figcaption,
				.wp-block-embed figcaption {
					color: ' . $content_text_color . ';
				}
			';

			// bloglo_hex2rgba( $content_text_color, 0.73 )
			// Lightened or darkened background color for backgrounds, borders & inputs.
			$background_color = bloglo_sanitize_color( bloglo_get_background_color() );

			$content_text_color_offset = bloglo_sanitize_color( bloglo_light_or_dark( $background_color, bloglo_luminance( $background_color, -0.045 ), bloglo_luminance( $background_color, 0.2 ) ) );
			// Only add for dark background color.
			if ( ! bloglo_is_light_color( $background_color ) ) {
				$css .= '
					#content textarea,
					#content input[type="text"],
					#content input[type="number"],
					#content input[type="email"],
					#content input[type=password],
					#content input[type=tel],
					#content input[type=url],
					#content input[type=search],
					#content input[type=date] {
						background-color: ' . $background_color . ';
					}
				';

				// Offset border color.
				$css .= '
					.bloglo-sidebar-style-3 #secondary .bloglo-widget {
						border-color: ' . $content_text_color_offset . ';
					}
				';

				// Offset background color.
				$css .= '
					.entry-meta .entry-meta-elements > span:before {
						background-color: ' . $content_text_color_offset . ';
					}
				';
			}

			// Content link hover color.
			$css .= '
				.content-area a:not(.bloglo-btn, .wp-block-button__link, .page-numbers, [rel^=category]):hover,
				#secondary .hester-core-custom-list-widget .bloglo-entry a:not(.bloglo-btn):hover,
				.bloglo-breadcrumbs a:hover {
					color: ' . bloglo_sanitize_color( bloglo_option( 'content_link_hover_color' ) ) . ';
				}
			';

			// Headings Color.
			$css .= '
				h1, h2, h3, h4, h5, h6,
				.h1, .h2, .h3, .h4,
				.bloglo-logo .site-title,
				.error-404 .page-header h1 {
					color: ' . bloglo_sanitize_color( bloglo_option( 'headings_color' ) ) . ';
				}
				:root {
					--bloglo-secondary: ' . bloglo_sanitize_color( bloglo_option( 'headings_color' ) ) . ';
				}
			';

			// Container width.
			$css .= '
				.bloglo-container,
				.alignfull.bloglo-wrap-content > div {
					max-width: ' . intval( bloglo_option( 'container_width' ) ) . 'px;
				}

				.bloglo-layout__boxed #page,
				.bloglo-layout__boxed.bloglo-sticky-header.bloglo-is-mobile #bloglo-header-inner,
				.bloglo-layout__boxed.bloglo-sticky-header:not(.bloglo-header-layout-3, .bloglo-header-layout-4) #bloglo-header-inner,
				.bloglo-layout__boxed.bloglo-sticky-header:not(.bloglo-is-mobile).bloglo-header-layout-4 #bloglo-header-inner .bloglo-nav-container > .bloglo-container,
				.bloglo-layout__boxed.bloglo-sticky-header:not(.bloglo-is-mobile).bloglo-header-layout-3 #bloglo-header-inner .bloglo-nav-container > .bloglo-container {
					max-width: ' . ( intval( bloglo_option( 'container_width' ) ) + 100 ) . 'px;
				}
			';

			// Adjust fullwidth sections for boxed layouts.
			if ( 'boxed' === bloglo_option( 'site_layout' ) || is_customize_preview() ) {
				$css .= '
					@media screen and (max-width: ' . intval( bloglo_option( 'container_width' ) ) . 'px) {
						body.bloglo-layout__boxed.bloglo-no-sidebar .elementor-section.elementor-section-stretched,
						body.bloglo-layout__boxed.bloglo-no-sidebar .bloglo-fw-section,
						body.bloglo-layout__boxed.bloglo-no-sidebar .entry-content .alignfull {
							margin-left: -5rem !important;
							margin-right: -5rem !important;
						}
					}
				';
			}

			// Logo max height.
			$css .= $this->get_range_field_css( '.bloglo-logo img', 'max-height', 'logo_max_height' );
			$css .= $this->get_range_field_css( '.bloglo-logo img.bloglo-svg-logo', 'height', 'logo_max_height' );

			// Logo margin.
			$css .= $this->get_spacing_field_css( '.bloglo-logo .logo-inner', 'margin', 'logo_margin' );

			/**
			 * Transparent header.
			 */

			// Logo max height.
			$css .= $this->get_range_field_css( '.bloglo-tsp-header .bloglo-logo img', 'max-height', 'tsp_logo_max_height' );
			$css .= $this->get_range_field_css( '.bloglo-tsp-header .bloglo-logo img.bloglo-svg-logo', 'height', 'tsp_logo_max_height' );

			// Logo margin.
			$css .= $this->get_spacing_field_css( '.bloglo-tsp-header .bloglo-logo .logo-inner', 'margin', 'tsp_logo_margin' );

			// Main Header custom background.
			$css .= $this->get_design_options_field_css( '.bloglo-tsp-header #bloglo-header-inner', 'tsp_header_background', 'background' );

			/** Font Colors */

			$tsp_font_color = bloglo_option( 'tsp_header_font_color' );

			// Header text color.
			if ( isset( $tsp_font_color['text-color'] ) && $tsp_font_color['text-color'] ) {
				$css .= '
					.bloglo-tsp-header .bloglo-logo .site-description {
						color: ' . bloglo_sanitize_color( $tsp_font_color['text-color'] ) . ';
					}
				';
			}

			// Header link color.
			if ( isset( $tsp_font_color['link-color'] ) && $tsp_font_color['link-color'] ) {
				$css .= '
					.bloglo-tsp-header #bloglo-header,
					.bloglo-tsp-header .bloglo-header-widgets a:not(.bloglo-btn),
					.bloglo-tsp-header .bloglo-logo a,
					.bloglo-tsp-header .bloglo-hamburger,
					.bloglo-tsp-header #bloglo-header-inner .bloglo-nav > ul > li > a { 
						color: ' . bloglo_sanitize_color( $tsp_font_color['link-color'] ) . ';
					}
				';
			}

			// Header link hover color.
			if ( isset( $tsp_font_color['link-hover-color'] ) && $tsp_font_color['link-hover-color'] ) {
				$css .= '
					.bloglo-tsp-header .bloglo-header-widgets a:not(.bloglo-btn):hover, 
					.bloglo-tsp-header #bloglo-header-inner .bloglo-header-widgets .bloglo-active,
					.bloglo-tsp-header .bloglo-logo .site-title a:hover, 
					.bloglo-tsp-header .bloglo-hamburger:hover, 
					.is-mobile-menu-active .bloglo-tsp-header .bloglo-hamburger,
					.bloglo-tsp-header.using-keyboard .site-title a:focus,
					.bloglo-tsp-header.using-keyboard .bloglo-header-widgets a:not(.bloglo-btn):focus,
					.bloglo-tsp-header #bloglo-header-inner .bloglo-nav > ul > li.hovered > a,
					.bloglo-tsp-header #bloglo-header-inner .bloglo-nav > ul > li > a:hover,
					.bloglo-tsp-header #bloglo-header-inner .bloglo-nav > ul > li.menu-item-has-children:hover > a,
					.bloglo-tsp-header #bloglo-header-inner .bloglo-nav > ul > li.current-menu-item > a,
					.bloglo-tsp-header #bloglo-header-inner .bloglo-nav > ul > li.current-menu-ancestor > a,
					.bloglo-tsp-header #bloglo-header-inner .bloglo-nav > ul > li.page_item_has_children:hover > a,
					.bloglo-tsp-header #bloglo-header-inner .bloglo-nav > ul > li.current_page_item > a,
					.bloglo-tsp-header #bloglo-header-inner .bloglo-nav > ul > li.current_page_ancestor > a {
						color: ' . bloglo_sanitize_color( $tsp_font_color['link-hover-color'] ) . ';
					}
					.bloglo-menu-animation-squareborder.bloglo-tsp-header:not(.bloglo-is-mobile) #bloglo-header-inner .bloglo-nav > ul > li > a:hover,
					.bloglo-menu-animation-squareborder.bloglo-tsp-header:not(.bloglo-is-mobile) #bloglo-header-inner .bloglo-nav > ul > li.menu-item-has-children:hover > a,
					.bloglo-menu-animation-squareborder.bloglo-tsp-header:not(.bloglo-is-mobile) #bloglo-header-inner .bloglo-nav > ul > li.current-menu-item > a,
					.bloglo-menu-animation-squareborder.bloglo-tsp-header:not(.bloglo-is-mobile) #bloglo-header-inner .bloglo-nav > ul > li.current-menu-ancestor > a,
					.bloglo-menu-animation-squareborder.bloglo-tsp-header:not(.bloglo-is-mobile) #bloglo-header-inner .bloglo-nav > ul > li.page_item_has_children:hover > a,
					.bloglo-menu-animation-squareborder.bloglo-tsp-header:not(.bloglo-is-mobile) #bloglo-header-inner .bloglo-nav > ul > li.current_page_item > a,
					.bloglo-menu-animation-squareborder.bloglo-tsp-header:not(.bloglo-is-mobile) #bloglo-header-inner .bloglo-nav > ul > li.current_page_ancestor > a {
						color: ' . bloglo_sanitize_color( $tsp_font_color['link-hover-color'] ) . ';
						border-color: ' . bloglo_sanitize_color( $tsp_font_color['link-hover-color'] ) . ';
					}
				';
			}

			/** Border Color */
			$css .= $this->get_design_options_field_css( '.bloglo-tsp-header #bloglo-header-inner', 'tsp_header_border', 'border' );

			/** Separator Color */
			$css .= $this->get_design_options_field_css( '.bloglo-tsp-header .bloglo-header-widget', 'tsp_header_border', 'separator_color' );

			/**
			 * Page Header.
			 */
			if ( bloglo_option( 'page_header_enable' ) ) {

				// Font size.
				$css .= $this->get_range_field_css( '#page .page-header .page-title', 'font-size', 'page_header_font_size', true );

				// Page Title spacing.
				$css .= $this->get_spacing_field_css( '.bloglo-page-title-align-left .page-header.bloglo-has-page-title, .bloglo-page-title-align-right .page-header.bloglo-has-page-title, .bloglo-page-title-align-center .page-header .bloglo-page-header-wrapper', 'padding', 'page_header_spacing' );

				// Page Header background.
				$css .= $this->get_design_options_field_css( '.bloglo-tsp-header:not(.bloglo-tsp-absolute) #masthead', 'page_header_background', 'background' );
				$css .= $this->get_design_options_field_css( '.page-header', 'page_header_background', 'background' );

				// Page Header font color.
				$page_header_color = bloglo_option( 'page_header_text_color' );

				// Page Header text color.
				if ( isset( $page_header_color['text-color'] ) && $page_header_color['text-color'] ) {
					$css .= '
						.page-header .page-title { 
							color: ' . bloglo_sanitize_color( $page_header_color['text-color'] ) . '; }

						.page-header .bloglo-page-header-description {
							color: ' . bloglo_sanitize_color( bloglo_hex2rgba( $page_header_color['text-color'], 0.75 ) ) . '; 
						}
					';
				}

				// Page Header link color.
				if ( isset( $page_header_color['link-color'] ) && $page_header_color['link-color'] ) {
					$css .= '
						.page-header .bloglo-breadcrumbs a { 
							color: ' . bloglo_sanitize_color( $page_header_color['link-color'] ) . '; }

						.page-header .bloglo-breadcrumbs span,
						.page-header .breadcrumb-trail .trail-items li::after, .page-header .bloglo-breadcrumbs .separator {
							color: ' . bloglo_sanitize_color( bloglo_hex2rgba( $page_header_color['link-color'], 0.75 ) ) . '; 
						}
					';
				}

				// Page Header link hover color.
				if ( isset( $page_header_color['link-hover-color'] ) && $page_header_color['link-hover-color'] ) {
					$css .= '
						.page-header .bloglo-breadcrumbs a:hover { 
							color: ' . bloglo_sanitize_color( $page_header_color['link-hover-color'] ) . '; }
					';
				}

				// Page Header border color.
				$page_header_border = bloglo_option( 'page_header_border' );

				$css .= $this->get_design_options_field_css( '.page-header', 'page_header_border', 'border' );
			}

			/**
			 * Breadcrumbs.
			 */
			if ( bloglo_option( 'breadcrumbs_enable' ) ) {

				// Spacing.
				$css .= $this->get_spacing_field_css( '.bloglo-breadcrumbs', 'padding', 'breadcrumbs_spacing' );

				if ( 'below-header' === bloglo_option( 'breadcrumbs_position' ) ) {

					// Background.
					$css .= $this->get_design_options_field_css( '.bloglo-breadcrumbs', 'breadcrumbs_background', 'background' );

					// Border.
					$css .= $this->get_design_options_field_css( '.bloglo-breadcrumbs', 'breadcrumbs_border', 'border' );

					// Text Color.
					$css .= $this->get_design_options_field_css( '.bloglo-breadcrumbs', 'breadcrumbs_text_color', 'color' );
				}
			}

			/**
			 * Copyright Bar.
			 */
			if ( bloglo_option( 'enable_copyright' ) ) {
				$css .= $this->get_design_options_field_css( '#bloglo-copyright', 'copyright_background', 'background' );

				// Copyright font color.
				$copyright_color = bloglo_option( 'copyright_text_color' );

				// Copyright text color.
				if ( isset( $copyright_color['text-color'] ) && $copyright_color['text-color'] ) {
					$css .= '
						#bloglo-copyright { 
							color: ' . bloglo_sanitize_color( $copyright_color['text-color'] ) . '; }
					';
				}

				// Copyright link color.
				if ( isset( $copyright_color['link-color'] ) && $copyright_color['link-color'] ) {
					$css .= '
						#bloglo-copyright a { 
							color: ' . bloglo_sanitize_color( $copyright_color['link-color'] ) . '; }
					';
				}

				// Copyright link hover color.
				if ( isset( $copyright_color['link-hover-color'] ) && $copyright_color['link-hover-color'] ) {
					$css .= '
						#bloglo-copyright a:hover,
						.using-keyboard #bloglo-copyright a:focus,
						#bloglo-copyright .bloglo-social-nav > ul > li > a .bloglo-icon.bottom-icon,
						#bloglo-copyright .bloglo-nav > ul > li.current-menu-item > a,
						#bloglo-copyright .bloglo-nav > ul > li.current-menu-ancestor > a,
						#bloglo-copyright .bloglo-nav > ul > li:hover > a { 
							color: ' . bloglo_sanitize_color( $copyright_color['link-hover-color'] ) . '; }
					';
				}

				// Copyright separator color.
				$footer_text_color = bloglo_option( 'footer_text_color' );
				$footer_text_color = $footer_text_color['text-color'];

				$copyright_separator_color = bloglo_light_or_dark( $footer_text_color, 'rgba(255,255,255,0.1)', 'rgba(0,0,0,0.1)' );

				$css .= '
					#bloglo-copyright.contained-separator > .bloglo-container::before {
						background-color: ' . bloglo_sanitize_color( $copyright_separator_color ) . ';
					}

					#bloglo-copyright.fw-separator {
						border-top-color: ' . bloglo_sanitize_color( $copyright_separator_color ) . ';
					}
				';
			}

			/**
			 * Typography.
			 */

			// Base HTML font size.
			$css .= $this->get_range_field_css( 'html', 'font-size', 'html_base_font_size', true, '%' );

			// Font smoothing.
			if ( bloglo_option( 'font_smoothing' ) ) {
				$css .= '
					* {
						-moz-osx-font-smoothing: grayscale;
						-webkit-font-smoothing: antialiased;
					}
				';
			}

			// Body.
			$css .= $this->get_typography_field_css( 'body', 'body_font' );

			// Headings.
			$css .= $this->get_typography_field_css( 'h1, .h1, .bloglo-logo .site-title, .page-header .page-title, h2, .h2, h3, .h3, h4, .h4, h5, .h5, h6, .h6', 'headings_font' );

			$css .= $this->get_typography_field_css( 'h1, .h1, .bloglo-logo .site-title, .page-header .page-title', 'h1_font' );
			$css .= $this->get_typography_field_css( 'h2, .h2', 'h2_font' );
			$css .= $this->get_typography_field_css( 'h3, .h3', 'h3_font' );
			$css .= $this->get_typography_field_css( 'h4, .h4', 'h4_font' );
			$css .= $this->get_typography_field_css( 'h5, .h5', 'h5_font' );
			$css .= $this->get_typography_field_css( 'h6, .h6', 'h6_font' );
			$css .= $this->get_typography_field_css( 'h1 em, h2 em, h3 em, h4 em, h5 em, h6 em, .h1 em, .h2 em, .h3 em, .h4 em, .h5 em, .h6 em, .bloglo-logo .site-title em, .error-404 .page-header h1 em', 'heading_em_font' );

			// Emphasized Heading.
			$css .= $this->get_typography_field_css( 'h1 em, h2 em, h3 em, h4 em, h5 em, h6 em, .h1 em, .h2 em, .h3 em, .h4 em, .h5 em, .h6 em, .bloglo-logo .site-title em, .error-404 .page-header h1 em', 'heading_em_font' );

			// Site Title font size.
			$css .= $this->get_range_field_css( '#bloglo-header .bloglo-logo .site-title', 'font-size', 'logo_text_font_size', true );

			// Sidebar widget title.
			$css .= $this->get_range_field_css( '#main .widget-title, .widget-area .wp-block-heading', 'font-size', 'sidebar_widget_title_font_size', true );

			// Footer widget title.
			$css .= $this->get_range_field_css( '#colophon .widget-title, #colophon .wp-block-heading', 'font-size', 'footer_widget_title_font_size', true );

			// Blog Single Post - Title Spacing.
			$css .= $this->get_spacing_field_css( '.bloglo-single-title-in-page-header #page .page-header .bloglo-page-header-wrapper', 'padding', 'single_title_spacing', true );
			
			// Blog Single Post - narrow container.
			if ( 'narrow' === bloglo_option( 'single_content_width' ) ) {
				$css .= '
					.single-post.narrow-content .entry-content > :not([class*="align"]):not([class*="gallery"]):not(.wp-block-image):not(.quote-inner):not(.quote-post-bg), 
					.single-post.narrow-content .mce-content-body:not([class*="page-template-full-width"]) > :not([class*="align"]):not([data-wpview-type*="gallery"]):not(blockquote):not(.mceTemp), 
					.single-post.narrow-content .entry-footer, 
					.single-post.narrow-content .entry-content > .alignwide,
					.single-post.narrow-content p.has-background:not(.alignfull):not(.alignwide),
					.single-post.narrow-content .post-nav, 
					.single-post.narrow-content #bloglo-comments-toggle, 
					.single-post.narrow-content #comments, 
					.single-post.narrow-content .entry-content .aligncenter, .single-post.narrow-content .bloglo-narrow-element, 
					.single-post.narrow-content.bloglo-single-title-in-content .entry-header, 
					.single-post.narrow-content.bloglo-single-title-in-content .entry-meta, 
					.single-post.narrow-content.bloglo-single-title-in-content .post-category,
					.single-post.narrow-content.bloglo-no-sidebar .bloglo-page-header-wrapper,
					.single-post.narrow-content.bloglo-no-sidebar .bloglo-breadcrumbs nav {
						max-width: ' . intval( bloglo_option( 'single_narrow_container_width' ) ) . 'px;
						margin-left: auto;
						margin-right: auto;
					}

					.single-post.narrow-content .author-box,
					.single-post.narrow-content .entry-content > .alignwide,
					.single.bloglo-single-title-in-page-header .page-header.bloglo-align-center .bloglo-page-header-wrapper {
						max-width: ' . ( intval( bloglo_option( 'single_narrow_container_width' ) ) + 70 ) . 'px;
					}
				';
			}

			// Allow CSS to be filtered.
			$css = apply_filters( 'bloglo_dynamic_styles', $css );

			// Add user custom CSS.
			if ( $custom_css || ! is_customize_preview() ) {
				$css .= wp_get_custom_css();
			}

			// Minify the CSS code.
			$css = $this->minify( $css );

			return $css;
		}

		/**
		 * Update dynamic css file with new CSS. Cleans caches after that.
		 *
		 * @return [Boolean] returns true if successfully updated the dynamic file.
		 */
		public function update_dynamic_file() {

			$css = $this->get_css( true );

			if ( empty( $css ) || '' === trim( $css ) ) {
				return;
			}

			// Load file.php file.
			require_once ABSPATH . 'wp-admin' . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'file.php'; // phpcs:ignore

			global $wp_filesystem;

			// Check if the the global filesystem isn't setup yet.
			if ( is_null( $wp_filesystem ) ) {
				WP_Filesystem();
			}

			$wp_filesystem->mkdir( $this->dynamic_css_path );

			if ( $wp_filesystem->put_contents( $this->dynamic_css_path . 'dynamic-styles.css', $css ) ) {
				$this->clean_cache();
				set_transient( 'bloglo_has_dynamic_css', true, 0 );
				return true;
			}

			return false;
		}

		/**
		 * Delete dynamic css file.
		 *
		 * @return void
		 */
		public function delete_dynamic_file() {

			// Load file.php file.
			require_once ABSPATH . 'wp-admin' . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'file.php'; // phpcs:ignore

			global $wp_filesystem;

			// Check if the the global filesystem isn't setup yet.
			if ( is_null( $wp_filesystem ) ) {
				WP_Filesystem();
			}

			$wp_filesystem->delete( $this->dynamic_css_path . 'dynamic-styles.css' );

			delete_transient( 'bloglo_has_dynamic_css' );
		}

		/**
		 * Simple CSS code minification.
		 *
		 * @param  string $css code to be minified.
		 * @return string, minifed code
		 * @since  1.0.0
		 */
		private function minify( $css ) {
			$css = preg_replace( '/\s+/', ' ', $css );
			$css = preg_replace( '/\/\*[^\!](.*?)\*\//', '', $css );
			$css = preg_replace( '/(,|:|;|\{|}) /', '$1', $css );
			$css = preg_replace( '/ (,|;|\{|})/', '$1', $css );
			$css = preg_replace( '/(:| )0\.([0-9]+)(%|em|ex|px|in|cm|mm|pt|pc)/i', '${1}.${2}${3}', $css );
			$css = preg_replace( '/(:| )(\.?)0(%|em|ex|px|in|cm|mm|pt|pc)/i', '${1}0', $css );

			return trim( $css );
		}

		/**
		 * Cleans various caches. Compatible with cache plugins.
		 *
		 * @since 1.0.0
		 */
		private function clean_cache() {

			// If W3 Total Cache is being used, clear the cache.
			if ( function_exists( 'w3tc_pgcache_flush' ) ) {
				w3tc_pgcache_flush();
			}

			// if WP Super Cache is being used, clear the cache.
			if ( function_exists( 'wp_cache_clean_cache' ) ) {
				global $file_prefix;
				wp_cache_clean_cache( $file_prefix );
			}

			// If SG CachePress is installed, reset its caches.
			if ( class_exists( 'SG_CachePress_Supercacher' ) ) {
				if ( method_exists( 'SG_CachePress_Supercacher', 'purge_cache' ) ) {
					SG_CachePress_Supercacher::purge_cache();
				}
			}

			// Clear caches on WPEngine-hosted sites.
			if ( class_exists( 'WpeCommon' ) ) {

				if ( method_exists( 'WpeCommon', 'purge_memcached' ) ) {
					WpeCommon::purge_memcached();
				}

				if ( method_exists( 'WpeCommon', 'clear_maxcdn_cache' ) ) {
					WpeCommon::clear_maxcdn_cache();
				}

				if ( method_exists( 'WpeCommon', 'purge_varnish_cache' ) ) {
					WpeCommon::purge_varnish_cache();
				}
			}

			// Clean OpCache.
			if ( function_exists( 'opcache_reset' ) ) {
				opcache_reset(); // phpcs:ignore PHPCompatibility.FunctionUse.NewFunctions.opcache_resetFound
			}

			// Clean WordPress cache.
			if ( function_exists( 'wp_cache_flush' ) ) {
				wp_cache_flush();
			}
		}

		/**
		 * Prints spacing field CSS based on passed params.
		 *
		 * @since  1.0.0
		 *
		 * @param  string $css_selector CSS selector.
		 * @param  string $css_property CSS property, such as 'margin', 'padding' or 'border'.
		 * @param  string $setting_id The ID of the customizer setting containing all information about the setting.
		 * @param  bool   $responsive Has responsive values.
		 * @return string  Generated CSS.
		 */
		public function get_spacing_field_css( $css_selector, $css_property, $setting_id, $responsive = true ) {

			// Get the saved setting.
			$setting = bloglo_option( $setting_id );

			// If setting doesn't exist, return.
			if ( ! is_array( $setting ) ) {
				return;
			}

			// Get the unit. Defaults to px.
			$unit = 'px';

			if ( isset( $setting['unit'] ) ) {
				if ( $setting['unit'] ) {
					$unit = $setting['unit'];
				}

				unset( $setting['unit'] );
			}

			// CSS buffer.
			$css_buffer = '';

			// Loop through options.
			foreach ( $setting as $key => $value ) {

				// Check if responsive options are available.
				if ( is_array( $value ) ) {

					if ( 'desktop' === $key ) {
						$mq_open  = '';
						$mq_close = '';
					} elseif ( 'tablet' === $key ) {
						$mq_open  = '@media only screen and (max-width: 768px) {';
						$mq_close = '}';
					} elseif ( 'mobile' === $key ) {
						$mq_open  = '@media only screen and (max-width: 480px) {';
						$mq_close = '}';
					} else {
						$mq_open  = '';
						$mq_close = '';
					}

					// Add media query prefix.
					$css_buffer .= $mq_open . $css_selector . '{';

					// Loop through all choices.
					foreach ( $value as $pos => $val ) {

						if ( empty( $val ) ) {
							continue;
						}

						if ( 'border' === $css_property ) {
							$pos .= '-width';
						}

						$css_buffer .= $css_property . '-' . $pos . ': ' . intval( $val ) . $unit . ';';
					}

					$css_buffer .= '}' . $mq_close;
				} else {

					if ( 'border' === $css_property ) {
						$key .= '-width';
					}

					$css_buffer .= $css_property . '-' . $key . ': ' . intval( $value ) . $unit . ';';
				}
			}

			// Check if field is has responsive values.
			if ( ! $responsive ) {
				$css_buffer = $css_selector . '{' . $css_buffer . '}';
			}

			// Finally, return the generated CSS code.
			return $css_buffer;
		}

		/**
		 * Prints range field CSS based on passed params.
		 *
		 * @since  1.0.0
		 *
		 * @param  string $css_selector CSS selector.
		 * @param  string $css_property CSS property, such as 'margin', 'padding' or 'border'.
		 * @param  string $setting_id The ID of the customizer setting containing all information about the setting.
		 * @param  bool   $responsive Has responsive values.
		 * @param  string $unit Unit.
		 * @return string  Generated CSS.
		 */
		public function get_range_field_css( $css_selector, $css_property, $setting_id, $responsive = true, $unit = 'px' ) {

			// Get the saved setting.
			$setting = bloglo_option( $setting_id );

			// If just a single value option.
			if ( ! is_array( $setting ) ) {
				return $css_selector . ' { ' . $css_property . ': ' . $setting . $unit . '; }';
			}

			// Resolve units.
			if ( isset( $setting['unit'] ) ) {
				if ( $setting['unit'] ) {
					$unit = $setting['unit'];
				}

				unset( $setting['unit'] );
			}

			// CSS buffer.
			$css_buffer = '';

			if ( is_array( $setting ) && ! empty( $setting ) ) {

				// Media query syntax wrap.
				$mq_open  = '';
				$mq_close = '';

				// Loop through options.
				foreach ( $setting as $key => $value ) {

					if ( empty( $value ) ) {
						continue;
					}

					if ( 'desktop' === $key ) {
						$mq_open  = '';
						$mq_close = '';
					} elseif ( 'tablet' === $key ) {
						$mq_open  = '@media only screen and (max-width: 768px) {';
						$mq_close = '}';
					} elseif ( 'mobile' === $key ) {
						$mq_open  = '@media only screen and (max-width: 480px) {';
						$mq_close = '}';
					} else {
						$mq_open  = '';
						$mq_close = '';
					}

					// Add media query prefix.
					$css_buffer .= $mq_open . $css_selector . '{';
					$css_buffer .= $css_property . ': ' . floatval( $value ) . $unit . ';';
					$css_buffer .= '}' . $mq_close;
				}
			}

			// Finally, return the generated CSS code.
			return $css_buffer;
		}

		/**
		 * Prints design options field CSS based on passed params.
		 *
		 * @since 1.0.0
		 * @param string       $css_selector CSS selector.
		 * @param string|mixed $setting The ID of the customizer setting containing all information about the setting.
		 * @param string       $type Design options field type.
		 * @return string      Generated CSS.
		 */
		public function get_design_options_field_css( $css_selector, $setting, $type ) {

			if ( is_string( $setting ) ) {
				// Get the saved setting.
				$setting = bloglo_option( $setting );
			}

			// Setting has to be array.
			if ( ! is_array( $setting ) || empty( $setting ) ) {
				return;
			}

			// CSS buffer.
			$css_buffer = '';

			// Background.
			if ( 'background' === $type ) {

				// Background type.
				$background_type = $setting['background-type'];

				if ( 'color' === $background_type ) {
					if ( isset( $setting['background-color'] ) && ! empty( $setting['background-color'] ) ) {
						$css_buffer .= 'background: ' . bloglo_sanitize_color( $setting['background-color'] ) . ';';
					}
				} elseif ( 'gradient' === $background_type ) {

					$css_buffer .= 'background: ' . bloglo_sanitize_color( $setting['gradient-color-1'] ) . ';';

					if ( 'linear' === $setting['gradient-type'] ) {
						$css_buffer .= '
							background: -webkit-linear-gradient(' . intval( $setting['gradient-linear-angle'] ) . 'deg, ' . bloglo_sanitize_color( $setting['gradient-color-1'] ) . ' ' . intval( $setting['gradient-color-1-location'] ) . '%, ' . bloglo_sanitize_color( $setting['gradient-color-2'] ) . ' ' . intval( $setting['gradient-color-2-location'] ) . '%);
							background: -o-linear-gradient(' . intval( $setting['gradient-linear-angle'] ) . 'deg, ' . bloglo_sanitize_color( $setting['gradient-color-1'] ) . ' ' . intval( $setting['gradient-color-1-location'] ) . '%, ' . bloglo_sanitize_color( $setting['gradient-color-2'] ) . ' ' . intval( $setting['gradient-color-2-location'] ) . '%);
							background: linear-gradient(' . intval( $setting['gradient-linear-angle'] ) . 'deg, ' . bloglo_sanitize_color( $setting['gradient-color-1'] ) . ' ' . intval( $setting['gradient-color-1-location'] ) . '%, ' . bloglo_sanitize_color( $setting['gradient-color-2'] ) . ' ' . intval( $setting['gradient-color-2-location'] ) . '%);

						';
					} elseif ( 'radial' === $setting['gradient-type'] ) {
						$css_buffer .= '
							background: -webkit-radial-gradient(' . sanitize_text_field( $setting['gradient-position'] ) . ', circle, ' . bloglo_sanitize_color( $setting['gradient-color-1'] ) . ' ' . intval( $setting['gradient-color-1-location'] ) . '%, ' . bloglo_sanitize_color( $setting['gradient-color-2'] ) . ' ' . intval( $setting['gradient-color-2-location'] ) . '%);
							background: -o-radial-gradient(' . sanitize_text_field( $setting['gradient-position'] ) . ', circle, ' . bloglo_sanitize_color( $setting['gradient-color-1'] ) . ' ' . intval( $setting['gradient-color-1-location'] ) . '%, ' . bloglo_sanitize_color( $setting['gradient-color-2'] ) . ' ' . intval( $setting['gradient-color-2-location'] ) . '%);
							background: radial-gradient(circle at ' . sanitize_text_field( $setting['gradient-position'] ) . ', ' . bloglo_sanitize_color( $setting['gradient-color-1'] ) . ' ' . intval( $setting['gradient-color-1-location'] ) . '%, ' . bloglo_sanitize_color( $setting['gradient-color-2'] ) . ' ' . intval( $setting['gradient-color-2-location'] ) . '%);
						';
					}
				} elseif ( 'image' === $background_type ) {
					$css_buffer .= '
						background-image: url(' . esc_url( $setting['background-image'] ) . ');
						background-size: ' . sanitize_text_field( $setting['background-size'] ) . ';
						background-attachment: ' . sanitize_text_field( $setting['background-attachment'] ) . ';
						background-position: ' . intval( $setting['background-position-x'] ) . '% ' . intval( $setting['background-position-y'] ) . '%;
						background-repeat: ' . sanitize_text_field( $setting['background-repeat'] ) . ';
					';
				}

				$css_buffer = ! empty( $css_buffer ) ? $css_selector . '{' . $css_buffer . '}' : '';

				if ( 'image' === $background_type && isset( $setting['background-color-overlay'] ) && $setting['background-color-overlay'] && isset( $setting['background-image'] ) && $setting['background-image'] ) {
					$css_buffer .= $css_selector . '::after { background-color: ' . bloglo_sanitize_color( $setting['background-color-overlay'] ) . '; }';
				}
			} elseif ( 'color' === $type ) {

				// Text color.
				if ( isset( $setting['text-color'] ) && ! empty( $setting['text-color'] ) ) {
					$css_buffer .= $css_selector . ' { color: ' . bloglo_sanitize_color( $setting['text-color'] ) . '; }';
				}

				// Link Color.
				if ( isset( $setting['link-color'] ) && ! empty( $setting['link-color'] ) ) {
					$css_buffer .= $css_selector . ' a { color: ' . bloglo_sanitize_color( $setting['link-color'] ) . '; }';
				}

				// Link Hover Color.
				if ( isset( $setting['link-hover-color'] ) && ! empty( $setting['link-hover-color'] ) ) {
					$css_buffer .= $css_selector . ' a:hover { color: ' . bloglo_sanitize_color( $setting['link-hover-color'] ) . ' !important; }';
				}
			} elseif ( 'border' === $type ) {

				// Color.
				if ( isset( $setting['border-color'] ) && ! empty( $setting['border-color'] ) ) {
					$css_buffer .= 'border-color:' . bloglo_sanitize_color( $setting['border-color'] ) . ';';
				}

				// Style.
				if ( isset( $setting['border-style'] ) && ! empty( $setting['border-style'] ) ) {
					$css_buffer .= 'border-style: ' . sanitize_text_field( $setting['border-style'] ) . ';';
				}

				// Width.
				$positions = array( 'top', 'right', 'bottom', 'left' );

				foreach ( $positions as $position ) {
					if ( isset( $setting[ 'border-' . $position . '-width' ] ) && ! empty( $setting[ 'border-' . $position . '-width' ] ) ) {
						$css_buffer .= 'border-' . sanitize_text_field( $position ) . '-width: ' . $setting[ 'border-' . sanitize_text_field( $position ) . '-width' ] . 'px;';
					}
				}

				$css_buffer = ! empty( $css_buffer ) ? $css_selector . '{' . $css_buffer . '}' : '';
			} elseif ( 'separator_color' === $type && isset( $setting['separator-color'] ) && ! empty( $setting['separator-color'] ) ) {

				// Separator Color.
				$css_buffer .= $css_selector . '::after { background-color:' . bloglo_sanitize_color( $setting['separator-color'] ) . '; }';
			}

			// Finally, return the generated CSS code.
			return $css_buffer;
		}

		/**
		 * Prints typography field CSS based on passed params.
		 *
		 * @since  1.0.0
		 * @param  string       $css_selector CSS selector.
		 * @param  string|mixed $setting The ID of the customizer setting containing all information about the setting.
		 * @return string       Generated CSS.
		 */
		public function get_typography_field_css( $css_selector, $setting ) {

			if ( is_string( $setting ) ) {
				// Get the saved setting.
				$setting = bloglo_option( $setting );
			}

			// Setting has to be array.
			if ( ! is_array( $setting ) || empty( $setting ) ) {
				return;
			}

			// CSS buffer.
			$css_buffer = '';

			// Properties.
			$properties = array(
				'font-weight',
				'font-style',
				'text-transform',
				'text-decoration',
			);

			foreach ( $properties as $property ) {

				if ( 'inherit' !== $setting[ $property ] ) {
					$css_buffer .= $property . ':' . $setting[ $property ] . ';';
				}
			}

			// Font family.
			if ( 'inherit' !== $setting['font-family'] ) {
				$font_family = bloglo()->fonts->get_font_family( $setting['font-family'] );

				$css_buffer .= 'font-family: ' . sanitize_text_field( $font_family ) . ';';
			}

			// Letter spacing.
			if ( ! empty( $setting['letter-spacing'] ) ) {
				$css_buffer .= 'letter-spacing:' . floatval( $setting['letter-spacing'] ) . sanitize_text_field( $setting['letter-spacing-unit'] ) . ';';
			}

			// Font size.
			if ( ! empty( $setting['font-size-desktop'] ) ) {
				$css_buffer .= 'font-size:' . floatval( $setting['font-size-desktop'] ) . sanitize_text_field( $setting['font-size-unit'] ) . ';';
			}

			// Line Height.
			if ( ! empty( $setting['line-height-desktop'] ) ) {
				$css_buffer .= 'line-height:' . floatval( $setting['line-height-desktop'] ) . ';';
			}

			$css_buffer = $css_buffer ? $css_selector . '{' . $css_buffer . '}' : '';

			// Responsive options - tablet.
			$tablet = '';

			if ( ! empty( $setting['font-size-tablet'] ) ) {
				$tablet .= 'font-size:' . floatval( $setting['font-size-tablet'] ) . sanitize_text_field( $setting['font-size-unit'] ) . ';';
			}

			if ( ! empty( $setting['line-height-tablet'] ) ) {
				$tablet .= 'line-height:' . floatval( $setting['line-height-tablet'] ) . ';';
			}

			$tablet = ! empty( $tablet ) ? '@media only screen and (max-width: 768px) {' . $css_selector . '{' . $tablet . '} }' : '';

			$css_buffer .= $tablet;

			// Responsive options - mobile.
			$mobile = '';

			if ( ! empty( $setting['font-size-mobile'] ) ) {
				$mobile .= 'font-size:' . floatval( $setting['font-size-mobile'] ) . sanitize_text_field( $setting['font-size-unit'] ) . ';';
			}

			if ( ! empty( $setting['line-height-mobile'] ) ) {
				$mobile .= 'line-height:' . floatval( $setting['line-height-mobile'] ) . ';';
			}

			$mobile = ! empty( $mobile ) ? '@media only screen and (max-width: 480px) {' . $css_selector . '{' . $mobile . '} }' : '';

			$css_buffer .= $mobile;

			// Equeue google fonts.
			if ( bloglo()->fonts->is_google_font( $setting['font-family'] ) ) {

				$params = array();

				if ( 'inherit' !== $setting['font-weight'] ) {
					$params['weight'] = $setting['font-weight'];
				}

				if ( 'inherit' !== $setting['font-style'] ) {
					$params['style'] = $setting['font-style'];
				}

				if ( $setting['font-subsets'] && ! empty( $setting['font-subsets'] ) ) {
					$params['subsets'] = $setting['font-subsets'];
				}

				bloglo()->fonts->enqueue_google_font(
					$setting['font-family'],
					$params
				);
			}

			// Finally, return the generated CSS code.
			return $css_buffer;
		}

		/**
		 * Filters the dynamic styles to include button styles and makes sure it has the highest priority.
		 *
		 * @since  1.0.0
		 * @param  string $css The dynamic CSS.
		 * @return string Filtered dynamic CSS.
		 */
		public function get_button_styles( $css ) {

			/**
			 * Primary Button.
			 */

			$primary_button_selector = '
				.bloglo-btn,
				body:not(.wp-customizer) input[type=submit], 
				.site-main .woocommerce #respond input#submit, 
				.site-main .woocommerce a.button, 
				.site-main .woocommerce button.button, 
				.site-main .woocommerce input.button, 
				.woocommerce ul.products li.product .added_to_cart, 
				.woocommerce ul.products li.product .button, 
				.woocommerce div.product form.cart .button, 
				.woocommerce #review_form #respond .form-submit input, 
				#infinite-handle span';

			/*
			$primary_button_bg_color      = bloglo_option( 'primary_button_bg_color' );
			$primary_button_border_radius = bloglo_option( 'primary_button_border_radius' );

			if ( '' !== $primary_button_bg_color ) {
				$css .= $primary_button_selector . ' {
					background-color: ' . $primary_button_bg_color . ';
				}';
			}
			*/
			// Primary button text color, border color & border width.
			/*
			$css .= $primary_button_selector . ' {
				color: ' . bloglo_option( 'primary_button_text_color' ) . ';
				border-color: ' . bloglo_option( 'primary_button_border_color' ) . ';
				border-width: ' . bloglo_option( 'primary_button_border_width' ) . 'rem;
				border-top-left-radius: ' . $primary_button_border_radius['top-left'] . 'rem;
				border-top-right-radius: ' . $primary_button_border_radius['top-right'] . 'rem;
				border-bottom-right-radius: ' . $primary_button_border_radius['bottom-right'] . 'rem;
				border-bottom-left-radius: ' . $primary_button_border_radius['bottom-left'] . 'rem;
			}';
			*/
			$css .= $primary_button_selector . ' {
				color: ' . bloglo_sanitize_color( bloglo_option( 'primary_button_text_color' ) ) . ';
				border-color: var(--bloglo-primary);
				border-width: ' . floatval( bloglo_option( 'primary_button_border_width' ) ) . 'rem;
			}';
			// Primary button hover.
			$primary_button_hover_selector = '';
			/*
			$primary_button_hover_selector = '
				.bloglo-btn:hover,
				.bloglo-btn:focus,
				body:not(.wp-customizer) input[type=submit]:hover,
				body:not(.wp-customizer) input[type=submit]:focus,
				.site-main .woocommerce #respond input#submit:hover,
				.site-main .woocommerce #respond input#submit:focus,
				.site-main .woocommerce a.button:hover,
				.site-main .woocommerce a.button:focus,
				.site-main .woocommerce button.button:hover,
				.site-main .woocommerce button.button:focus,
				.site-main .woocommerce input.button:hover,
				.site-main .woocommerce input.button:focus,
				.woocommerce ul.products li.product .added_to_cart:hover,
				.woocommerce ul.products li.product .added_to_cart:focus,
				.woocommerce ul.products li.product .button:hover,
				.woocommerce ul.products li.product .button:focus,
				.woocommerce div.product form.cart .button:hover,
				.woocommerce div.product form.cart .button:focus,
				.woocommerce #review_form #respond .form-submit input:hover,
				.woocommerce #review_form #respond .form-submit input:focus,
				#infinite-handle span:hover';

			$primary_button_hover_bg_color = bloglo_option( 'primary_button_hover_bg_color' );
			*/
			// Primary button hover bg color.
			/*
			if ( '' !== $primary_button_hover_bg_color ) {
				$css .= $primary_button_hover_selector . ' {
					background-color: ' . $primary_button_hover_bg_color . ';
				}';
			}
			*/
			// Primary button hover color & border.
			$css .= $primary_button_hover_selector . '{
				color: ' . bloglo_sanitize_color( bloglo_option( 'primary_button_hover_text_color' ) ) . ';
				border-color: ' . bloglo_sanitize_color( bloglo_option( 'primary_button_hover_border_color' ) ) . ';
			}';

			// Primary button typography.
			$css .= $this->get_typography_field_css( $primary_button_selector, 'primary_button_typography' );

			/**
			 * Secondary Button.
			 */

			$secondary_button_selector = '
				input[type="reset"],
				.btn-secondary,
				.bloglo-btn.btn-secondary';

			$secondary_button_bg_color      = bloglo_option( 'secondary_button_bg_color' );
			$secondary_button_border_radius = bloglo_option( 'secondary_button_border_radius' );

			// Secondary button text color, border color & border width.
			$css .= $secondary_button_selector . ' {
				color: ' . bloglo_sanitize_color( bloglo_option( 'secondary_button_text_color' ) ) . ';
				border-color: ' . bloglo_sanitize_color( bloglo_option( 'secondary_button_border_color' ) ) . ';
				border-width: ' . floatval( bloglo_option( 'secondary_button_border_width' ) ) . 'rem;
				background-color: ' . bloglo_sanitize_color( $secondary_button_bg_color ) . ';
				border-top-left-radius: ' . floatval( $secondary_button_border_radius['top-left'] ) . 'rem;
				border-top-right-radius: ' . floatval( $secondary_button_border_radius['top-right'] ) . 'rem;
				border-bottom-right-radius: ' . floatval( $secondary_button_border_radius['bottom-right'] ) . 'rem;
				border-bottom-left-radius: ' . floatval( $secondary_button_border_radius['bottom-left'] ) . 'rem;
			}';

			// Secondary button hover.
			$secondary_button_hover_selector = '
				.btn-secondary:hover, 
				.btn-secondary:focus, 
				.bloglo-btn.btn-secondary:hover, 
				.bloglo-btn.btn-secondary:focus';

			$secondary_button_hover_bg_color = bloglo_option( 'secondary_button_hover_bg_color' );

			// Secondary button hover color & border.
			$css .= $secondary_button_hover_selector . '{
				color: ' . bloglo_sanitize_color( bloglo_option( 'secondary_button_hover_text_color' ) ) . ';
				border-color: ' . bloglo_sanitize_color( bloglo_option( 'secondary_button_hover_border_color' ) ) . ';
				background-color: ' . bloglo_sanitize_color( $secondary_button_hover_bg_color ) . ';
			}';

			// Secondary button typography.
			$css .= $this->get_typography_field_css( $secondary_button_selector, 'secondary_button_typography' );

			// Text Button.
			$css .= '
				.bloglo-btn.btn-text-1, .btn-text-1 {
					color: ' . bloglo_sanitize_color( bloglo_option( 'text_button_text_color' ) ) . ';
				}
			';

			$css .= '
				.bloglo-btn.btn-text-1:hover, .bloglo-btn.btn-text-1:focus, .btn-text-1:hover, .btn-text-1:focus {
					color: ' . bloglo_sanitize_color( bloglo_option( 'accent_color' ) ) . ';
				}
			';

			$css .= '
				.bloglo-btn.btn-text-1 > span::before {
					background-color: ' . bloglo_sanitize_color( bloglo_option( 'accent_color' ) ) . ';
				}
			';

			if ( bloglo_option( 'text_button_hover_text_color' ) ) {
				$css .= '
					.bloglo-btn.btn-text-1:hover, .bloglo-btn.btn-text-1:focus, .btn-text-1:hover, .btn-text-1:focus {
						color: ' . bloglo_sanitize_color( bloglo_option( 'text_button_hover_text_color' ) ) . ';
					}

					.bloglo-btn.btn-text-1 > span::before {
						background-color: ' . bloglo_sanitize_color( bloglo_option( 'text_button_hover_text_color' ) ) . ';
					}
				';
			}

			// Secondary button typography.
			$css .= $this->get_typography_field_css( '.bloglo-btn.btn-text-1, .btn-text-1', 'text_button_typography' );

			// Return the filtered CSS.
			return $css;
		}

		/**
		 * Generate dynamic Block Editor styles.
		 *
		 * @since  1.0.0
		 * @return string
		 */
		public function get_block_editor_css() {

			// Current post.
			$post_id   = get_the_ID();
			$post_type = get_post_type( $post_id );

			// Layout.
			$site_layout          = bloglo_get_site_layout( $post_id );
			$sidebar_position     = bloglo_get_sidebar_position( $post_id );
			$container_width      = bloglo_option( 'container_width' );
			$single_content_width = bloglo_option( 'single_content_width' );

			$container_width = $container_width - 100;

			if ( bloglo_is_sidebar_displayed( $post_id ) ) {

				$sidebar_width   = bloglo_option( 'sidebar_width' );
				$container_width = $container_width * ( 100 - intval( $sidebar_width ) ) / 100;
				$container_width = $container_width - 50;

				if ( 'boxed-separated' === $site_layout ) {
					if ( 3 === intval( bloglo_option( 'sidebar_style' ) ) ) {
						$container_width += 15;
					}
				}
			}

			if ( 'boxed-separated' === $site_layout ) {
				$container_width += 16;
			}

			if ( 'boxed' === $site_layout ) {
				$container_width = $container_width + 200;
			}

			$background_color = bloglo_sanitize_color( get_background_color() );
			$accent_color     = bloglo_sanitize_color( bloglo_option( 'accent_color' ) );
			$content_color    = bloglo_sanitize_color( bloglo_option( 'boxed_content_background_color' ) );
			$text_color       = bloglo_sanitize_color( bloglo_option( 'content_text_color' ) );
			$link_hover_color = bloglo_sanitize_color( bloglo_option( 'content_link_hover_color' ) );
			$headings_color   = bloglo_sanitize_color( bloglo_option( 'headings_color' ) );
			$font_smoothing   = sanitize_text_field( bloglo_option( 'font_smoothing' ) );

			$css = '';

			// Base HTML font size.
			$css .= $this->get_range_field_css( 'html', 'font-size', 'html_base_font_size', true, '%' );

			// Accent color.
			$css .= '
				.editor-styles-wrapper .block-editor-rich-text__editable mark,
				.editor-styles-wrapper .block-editor-rich-text__editable span.highlight,
				.editor-styles-wrapper .block-editor-rich-text__editable code,
				.editor-styles-wrapper .block-editor-rich-text__editable kbd,
				.editor-styles-wrapper .block-editor-rich-text__editable var,
				.editor-styles-wrapper .block-editor-rich-text__editable samp,
				.editor-styles-wrapper .block-editor-rich-text__editable tt {
					background-color: ' . bloglo_sanitize_color( bloglo_hex2rgba( $accent_color, .09 ) ) . ';
				}

				.editor-styles-wrapper .wp-block code.block,
				.editor-styles-wrapper .block code {
					background-color: ' . bloglo_sanitize_color( bloglo_hex2rgba( $accent_color, .075 ) ) . ';
				}

				.editor-styles-wrapper .wp-block .block-editor-rich-text__editable a,
				.editor-styles-wrapper .block-editor-rich-text__editable code,
				.editor-styles-wrapper .block-editor-rich-text__editable kbd,
				.editor-styles-wrapper .block-editor-rich-text__editable var,
				.editor-styles-wrapper .block-editor-rich-text__editable samp,
				.editor-styles-wrapper .block-editor-rich-text__editable tt {
					color: ' . $accent_color . ';
				}

				#editor .editor-styles-wrapper ::-moz-selection { background-color: ' . $accent_color . '; color: #FFF; }
				#editor .editor-styles-wrapper ::selection { background-color: ' . $accent_color . '; color: #FFF; }

				
				.editor-styles-wrapper blockquote,
				.editor-styles-wrapper .wp-block-quote {
					border-color: ' . $accent_color . ';
				}
			';

			// Container width.
			/*
			if ( 'fw-stretched' === $site_layout ) {
				$css .= '
					.editor-styles-wrapper .wp-block {
						max-width: none;
					}
				';
			} elseif ( 'boxed-separated' === $site_layout || 'boxed' === $site_layout ) {

				$css .= '
					.editor-styles-wrapper {
						max-width: ' . $container_width . 'px;
						margin: 0 auto;
					}

					.editor-styles-wrapper .wp-block {
						max-width: none;
					}
				';

				if ( 'boxed' === $site_layout ) {
					$css .= '
						.editor-styles-wrapper {
							-webkit-box-shadow: 0 0 30px rgba(50, 52, 54, 0.06);
							box-shadow: 0 0 30px rgba(50, 52, 54, 0.06);
							padding-left: 42px;
							padding-right: 42px;
						}
					';
				} else {
					$css .= '
						.editor-styles-wrapper {
							border-radius: 3px;
							border: 1px solid rgba(190, 190, 190, 0.30);
						}
					';
				}
			}
			else {
				$css .= '
					.editor-styles-wrapper .wp-block {
						max-width: ' . $container_width . 'px;
					}
				';
			} */

			if ( 'boxed-separated' === $site_layout || 'boxed' === $site_layout ) {

				if ( 'boxed' === $site_layout ) {
					$css .= '
						.editor-styles-wrapper {
							-webkit-box-shadow: 0 0 30px rgba(50, 52, 54, 0.06);
							box-shadow: 0 0 30px rgba(50, 52, 54, 0.06);
							padding-left: 42px;
							padding-right: 42px;
						}
					';
				} else {
					$css .= '
						.editor-styles-wrapper {
							border-radius: 0;
							border: 1px solid rgba(190, 190, 190, 0.30);
						}
					';
				}
			}

			if ( 'post' === $post_type && 'narrow' === $single_content_width ) {

				$narrow_container_width = intval( bloglo_option( 'single_narrow_container_width' ) );

				$css .= '
					.editor-styles-wrapper .wp-block:not([data-size="full"]) {
						max-width: ' . $narrow_container_width . 'px;
					}
				';
			}

			// Background color.
			if ( 'boxed-separated' === $site_layout || 'boxed' === $site_layout ) {
				$css .= '
					:root .edit-post-layout .interface-interface-skeleton__content {
						background-color: #' . trim( $background_color, '#' ) . ';
					}

					:root .editor-styles-wrapper {
						background-color: ' . $content_color . ';
					}
				';
			} else {
				$css .= '
					:root .editor-styles-wrapper {
						background-color: #' . trim( $background_color, '#' ) . ';
					}
				';
			}

			// Body.
			$css .= $this->get_typography_field_css( ':root .editor-styles-wrapper, .editor-styles-wrapper .wp-block, .block-editor-default-block-appender textarea.block-editor-default-block-appender__content', 'body_font' );
			$css .= '
				:root .editor-styles-wrapper {
					color: ' . $text_color . ';
				}
			';

			// Headings typography.
			$css .= $this->get_typography_field_css( ':root .editor-styles-wrapper h1.wp-block, :root .editor-styles-wrapper h2.wp-block, :root .editor-styles-wrapper h3.wp-block, :root .editor-styles-wrapper h4.wp-block, :root .editor-styles-wrapper h5.wp-block, :root .editor-styles-wrapper h6.wp-block, :root .editor-styles-wrapper .editor-post-title__block .editor-post-title__input', 'headings_font' );

			// Heading em.
			$css .= $this->get_typography_field_css( '.editor-styles-wrapper h1.wp-block em, .editor-styles-wrapper h2.wp-block em, .editor-styles-wrapper h3.wp-block em, .editor-styles-wrapper h4.wp-block em, .editor-styles-wrapper h5.wp-block em, .editor-styles-wrapper h6.wp-block em', 'heading_em_font' );

			// Headings (H1-H6).
			$css .= $this->get_typography_field_css( ':root .editor-styles-wrapper h1.wp-block, :root .editor-styles-wrapper .h1, :root .editor-styles-wrapper .editor-post-title__block .editor-post-title__input', 'h1_font' );
			$css .= $this->get_typography_field_css( ':root .editor-styles-wrapper h2.wp-block, :root .editor-styles-wrapper .h2', 'h2_font' );
			$css .= $this->get_typography_field_css( ':root .editor-styles-wrapper h3.wp-block, :root .editor-styles-wrapper .h3', 'h3_font' );
			$css .= $this->get_typography_field_css( ':root .editor-styles-wrapper h4.wp-block', 'h4_font' );
			$css .= $this->get_typography_field_css( ':root .editor-styles-wrapper h5.wp-block', 'h5_font' );
			$css .= $this->get_typography_field_css( ':root .editor-styles-wrapper h6.wp-block', 'h6_font' );

			$css .= '
				:root .editor-styles-wrapper h1,
				:root .editor-styles-wrapper h2,
				:root .editor-styles-wrapper h3,
				:root .editor-styles-wrapper h4,
				:root .editor-styles-wrapper .h4,
				:root .editor-styles-wrapper h5,
				:root .editor-styles-wrapper h6,
				:root .editor-post-title__block .editor-post-title__input {
					color: ' . $headings_color . ';
				}
			';

			// Page header font size.
			$css .= $this->get_range_field_css( ':root .editor-styles-wrapper .editor-post-title__block .editor-post-title__input', 'font-size', 'page_header_font_size', true );

			// Link hover color.
			$css .= '
				.editor-styles-wrapper .wp-block .block-editor-rich-text__editable a:hover { 
					color: ' . $link_hover_color . '; 
				}
			';

			// Font smoothing.
			if ( $font_smoothing ) {
				$css .= '
					.editor-styles-wrapper {
						-moz-osx-font-smoothing: grayscale;
						-webkit-font-smoothing: antialiased;
					}
				';
			}

			return $css;
		}
	}
endif;

/**
 * The function which returns the one Bloglo_Dynamic_Styles instance.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Example: <?php $dynamic_styles = bloglo_dynamic_styles(); ?>
 *
 * @since 1.0.0
 * @return object
 */
function bloglo_dynamic_styles() {
	return Bloglo_Dynamic_Styles::instance();
}

bloglo_dynamic_styles();
