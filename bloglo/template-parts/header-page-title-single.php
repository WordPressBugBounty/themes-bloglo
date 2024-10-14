<?php
/**
 * Template part for displaying page header for single post.
 *
 * @package Bloglo
 * @author Peregrine Themes
 * @since   1.0.0
 */

?>

<div <?php bloglo_page_header_classes(); ?><?php bloglo_page_header_atts(); ?>>

	<?php do_action( 'bloglo_page_header_start' ); ?>

	<?php if ( 'in-page-header' === bloglo_option( 'single_title_position' ) ) { ?>

		<div class="bloglo-container">
			<div class="bloglo-page-header-wrapper">

				<?php
				if ( bloglo_single_post_displays( 'category' ) ) {
					get_template_part( 'template-parts/entry/entry', 'category' );
				}

				if ( bloglo_page_header_has_title() ) {
					echo '<div class="bloglo-page-header-title">';
					bloglo_page_header_title();
					echo '</div>';
				}

				if ( bloglo_has_entry_meta_elements() ) {
					get_template_part( 'template-parts/entry/entry', 'meta' );
				}
				?>

			</div>
		</div>

	<?php } ?>

	<?php do_action( 'bloglo_page_header_end' ); ?>

</div>
