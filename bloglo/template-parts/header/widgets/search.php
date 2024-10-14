<?php
/**
 * The template for displaying theme header search widget.
 *
 * @package     Bloglo
 * @author      Peregrine Themes
 * @since       1.0.0
 */

?>

<div aria-haspopup="true">
	<a href="#" class="bloglo-search">
		<?php echo bloglo()->icons->get_svg( 'search', array( 'aria-label' => esc_html__( 'Search', 'bloglo' ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</a><!-- END .bloglo-search -->

	<div class="bloglo-search-simple bloglo-search-container dropdown-item">
		<form role="search" aria-label="<?php esc_attr_e( 'Site Search', 'bloglo' ); ?>" method="get" class="bloglo-search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">

			<label class="bloglo-form-label">
				<span class="screen-reader-text"><?php esc_html_e( 'Search for:', 'bloglo' ); ?></span>
				<input type="search" class="bloglo-input-search" placeholder="<?php esc_attr_e( 'Search', 'bloglo' ); ?>" value="<?php echo esc_attr( get_query_var( 's' ) ); ?>" name="s" autocomplete="off">
			</label><!-- END .bloglo-form-label -->

			<?php bloglo_animated_arrow( 'right', 'submit', true ); ?>
			<button type="button" class="bloglo-search-close" aria-hidden="true" role="button">
				<svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"><path d="M6.852 7.649L.399 1.195 1.445.149l6.454 6.453L14.352.149l1.047 1.046-6.454 6.454 6.454 6.453-1.047 1.047-6.453-6.454-6.454 6.454-1.046-1.047z" fill="currentColor" fill-rule="evenodd"></path></svg>
			</button>

		</form>
	</div><!-- END .bloglo-search-simple -->
</div>
