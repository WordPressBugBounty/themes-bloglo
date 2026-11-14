<?php
/**
 * Bloglo BuddyPress Compatibility.
 *
 * @package     Bloglo
 * @author      Peregrine Themes
 * @since       1.1.26
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Bloglo BuddyPress Compatibility.
 */
if ( ! class_exists( 'Bloglo_BuddyPress' ) ) :

	/**
	 * Bloglo BuddyPress Compatibility class.
	 */
	class Bloglo_BuddyPress {

		/**
		 * Primary class constructor.
		 *
		 * @since 1.1.26
		 */
		public function __construct() {
			add_action( 'bloglo_enqueue_scripts', array( $this, 'enqueue' ) );
		}

		/**
		 * Enqueue BuddyPress compatibility stylesheet.
		 *
		 * Only runs when the BuddyPress plugin is active.
		 *
		 * @since 1.1.26
		 */
		public function enqueue() {

			if ( ! function_exists( 'buddypress' ) ) {
				return;
			}

			$bloglo_suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
			$bloglo_file   = '/assets/css/compatibility/buddypress' . $bloglo_suffix . '.css';
			$bloglo_ver    = file_exists( BLOGLO_THEME_PATH . $bloglo_file ) ? filemtime( BLOGLO_THEME_PATH . $bloglo_file ) : BLOGLO_THEME_VERSION;

			wp_enqueue_style(
				'bloglo-buddypress',
				BLOGLO_THEME_URI . $bloglo_file,
				array( 'bloglo-styles' ),
				$bloglo_ver,
				'all'
			);

			// Better Messages (live chat for BuddyPress) uses RGB colour variables; map its accent to the theme accent.
			if ( class_exists( 'BP_Better_Messages' ) || function_exists( 'Better_Messages' ) ) {
				wp_add_inline_style( 'bloglo-buddypress', $this->better_messages_css() );

				// Keep the chat colour scheme in step with the theme's dark mode switch.
				wp_add_inline_script( 'bloglo', $this->better_messages_dark_mode_js() );
			}

			// Native message thread: align the member's own messages right and open the thread at the latest message.
			if ( is_user_logged_in() && function_exists( 'bp_is_messages_component' ) && bp_is_messages_component() ) {
				wp_add_inline_style( 'bloglo-buddypress', $this->conversation_css() );
				wp_add_inline_script( 'bloglo', $this->conversation_scroll_js() );
			}
		}

		/**
		 * CSS for the member's own messages in a conversation (the thread markup has no 'own message' class).
		 *
		 * @since 1.1.26
		 *
		 * @return string
		 */
		private function conversation_css() {

			// The URL ends up inside a quoted CSS attribute selector, so drop everything that could break out of it.
			$link = str_replace( array( '\\', '"', '<', '>', "\n", "\r" ), '', esc_url_raw( trailingslashit( bp_core_get_user_domain( get_current_user_id() ) ) ) );

			$own = '#buddypress ul#bp-message-thread-list>li:has(.message-metadata a.user-link[href^="' . $link . '"])';

			return $own . '{align-items:flex-end;padding:0 5rem 0 0}'
				. $own . ' .message-metadata{flex-direction:row-reverse}'
				. $own . ' .message-metadata a.user-link .avatar{left:auto;right:0}'
				. $own . ' .message-metadata strong{display:none}'
				. $own . ' .message-content{background:var(--bloglo-secondary,#232323);color:var(--bloglo-white,#fff);border-color:transparent;border-radius:.6rem .2rem .6rem .6rem}'
				. $own . ' .message-content a{color:inherit;text-decoration:underline}'
				. 'html[data-theme="dark"] ' . $own . ' .message-content{background:#475569;color:#fff}';
		}

		/**
		 * Scroll the conversation to the newest message once the thread has rendered.
		 *
		 * @since 1.1.26
		 *
		 * @return string
		 */
		private function conversation_scroll_js() {
			return '(function(){function s(){var l=document.getElementById("bp-message-thread-list");if(l&&l.children.length){l.scrollTop=l.scrollHeight;return true;}return false;}if(s()){return;}var c=document.getElementById("buddypress");if(!c){return;}var o=new MutationObserver(function(){if(s()){o.disconnect();}});o.observe(c,{childList:true,subtree:true});setTimeout(function(){o.disconnect();},10000);})();';
		}

		/**
		 * Toggle Better Messages' dark / light body classes together with the theme's dark mode.
		 *
		 * @since 1.1.26
		 *
		 * @return string
		 */
		private function better_messages_dark_mode_js() {
			return '(function(){var h=document.documentElement;function s(){var b=document.body;if(!b){return;}var d=h.getAttribute("data-theme")==="dark";b.classList.toggle("bm-messages-dark",d);b.classList.toggle("bm-messages-light",!d);}s();window.addEventListener("load",s);new MutationObserver(s).observe(h,{attributes:true,attributeFilter:["data-theme"]});})();';
		}

		/**
		 * CSS variables that align Better Messages with the theme accent colour.
		 *
		 * @since 1.1.26
		 *
		 * @return string
		 */
		private function better_messages_css() {

			$hex = sanitize_hex_color( bloglo_option( 'accent_color' ) );
			$hex = $hex ? $hex : '#0554f2';

			// Better Messages expects "r,g,b" values, so reuse the theme helpers and strip the rgb() wrapper.
			$to_rgb = function ( $color ) {
				if ( ! is_string( $color ) ) {
					return '';
				}

				return str_replace( array( 'rgb(', ')' ), '', bloglo_hex2rgba( $color ) );
			};

			$accent = $to_rgb( $hex );

			// Darker accent shades for hover, pressed and mention states.
			$shade = function ( $amount ) use ( $hex, $to_rgb ) {
				return $to_rgb( bloglo_luminance( $hex, -$amount ) );
			};

			$accent_vars = array(
				'accent',
				'inbox-accent',
				'list-accent',
				'reply-accent',
				'mc-reply-accent',
				'dock-accent',
				'wlist-accent',
				'mob-reply-accent',
				'mob-list-accent',
				'mob-tabs-accent',
				'bubble-button-bg',
				'bubble-button-border',
			);

			$css = 'html body,html body.bm-messages-dark,html body.bm-messages-light,body .bm-wrap-main,body .bm-wrap,:root{';

			foreach ( $accent_vars as $var ) {
				$css .= '--bm-color-' . $var . ':' . $accent . ';';
			}

			// Own messages stay neutral so the accent is kept for actions and unread markers.
			foreach ( array( 'mc-self-bg', 'mob-self-bg', 'bubble-self-bg', 'bubble-self-outline' ) as $var ) {
				$css .= '--bm-color-' . $var . ':35,35,35;';
			}

			$css .= '--bm-color-accent-hover:' . $shade( 0.12 ) . ';';
			$css .= '--bm-color-accent-pressed:' . $shade( 0.25 ) . ';';
			$css .= '--bm-color-mention-text:' . $shade( 0.3 ) . ';';
			$css .= '}';

			// Own messages need a lighter neutral on dark backgrounds.
			$css .= 'html body.bm-messages-dark{';

			foreach ( array( 'mc-self-bg', 'mob-self-bg', 'bubble-self-bg', 'bubble-self-outline' ) as $var ) {
				$css .= '--bm-color-' . $var . ':71,85,105;';
			}

			$css .= '}';

			return $css;
		}
	}

endif;

if ( function_exists( 'buddypress' ) ) {
	new Bloglo_BuddyPress();
}
