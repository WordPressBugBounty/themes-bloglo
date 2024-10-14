<?php
/**
 * The template for displaying search form.
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

// Support for custom search post type.
$bloglo_post_type = apply_filters( 'bloglo_search_post_type', 'all' );
$bloglo_post_type = 'all' !== $bloglo_post_type ? '<input type="hidden" name="post_type" value="' . esc_attr( $bloglo_post_type ) . '" />' : '';
?>

<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<div>
		<input type="search" class="search-field" aria-label="<?php esc_attr_e( 'Enter search keywords', 'bloglo' ); ?>" placeholder="<?php esc_attr_e( 'Search', 'bloglo' ); ?>" value="<?php echo get_search_query(); ?>" name="s" />
		<?php echo $bloglo_post_type; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

		<button role="button" type="submit" class="search-submit" aria-label="<?php esc_attr_e( 'Search', 'bloglo' ); ?>">
			<?php echo bloglo()->icons->get_svg( 'search', array( 'aria-hidden' => 'true' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</button>
	</div>
</form>
