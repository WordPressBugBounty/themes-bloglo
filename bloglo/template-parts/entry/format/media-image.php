<?php
/**
 * Template part for displaying post format image entry.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
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

$bloglo_media = bloglo_get_post_media( 'image' );

if ( ! $bloglo_media || post_password_required() ) {
	return;
}

?>

<div class="post-thumb entry-media thumbnail">

	<?php
	if ( ! is_single( get_the_ID() ) ) {
		$bloglo_media = sprintf(
			'<a href="%1$s" class="entry-image-link">%2$s</a>',
			esc_url( bloglo_entry_get_permalink() ),
			$bloglo_media
		);
	}

	echo $bloglo_media; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	?>
</div>
