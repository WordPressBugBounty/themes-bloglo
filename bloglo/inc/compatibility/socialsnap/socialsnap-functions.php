<?php
/**
 * Bloglo Social Snap compatibility functions.
 *
 * @package     Bloglo
 * @author      Peregrine Themes
 * @since       1.0.0
 */

if ( ! function_exists( 'bloglo_entry_meta_shares' ) ) :
	/**
	 * Add share count information to entry meta.
	 *
	 * @since 1.0.0
	 */
	function bloglo_entry_meta_shares() {

		$share_count = socialsnap_get_total_share_count();

		// Icon.
		$icon = bloglo()->icons->get_meta_icon( 'share', bloglo()->icons->get_svg( 'share-2', array( 'aria-hidden' => 'true' ) ) );

		$output = sprintf(
			'<span class="share-count">%3$s%1$s %2$s</span>',
			socialsnap_format_number( $share_count ),
			esc_html( _n( 'Share', 'Shares', $share_count, 'bloglo' ) ),
			$icon
		);

		echo wp_kses( apply_filters( 'bloglo_entry_share_count', $output ), bloglo_get_allowed_html_tags() );
	}
endif;
