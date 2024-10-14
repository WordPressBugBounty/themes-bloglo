<?php
/**
 * Template part for displaying page header.
 *
 * @package Bloglo
 * @author Peregrine Themes
 * @since   1.0.0
 */

?>

<div <?php bloglo_page_header_classes(); ?><?php bloglo_page_header_atts(); ?>>
	<div class="bloglo-container">

	<?php do_action( 'bloglo_page_header_start' ); ?>

	<?php if ( bloglo_page_header_has_title() ) { ?>

		<div class="bloglo-page-header-wrapper">

			<div class="bloglo-page-header-title">
				<?php bloglo_page_header_title(); ?>
			</div>

			<?php $bloglo_description = apply_filters( 'bloglo_page_header_description', bloglo_get_the_description() ); ?>

			<?php if ( $bloglo_description ) { ?>

				<div class="bloglo-page-header-description">
					<?php echo wp_kses( $bloglo_description, bloglo_get_allowed_html_tags() ); ?>
				</div>

			<?php } ?>
		</div>

	<?php } ?>

	<?php do_action( 'bloglo_page_header_end' ); ?>

	</div>
</div>
