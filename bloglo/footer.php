<?php
/**
 * The template for displaying the footer in our theme.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package     Bloglo
 * @author      Peregrine Themes
 * @since       1.0.0
 */

?>
		<?php do_action( 'bloglo_main_end' ); ?>

	</div><!-- #main .site-main -->
	<?php do_action( 'bloglo_after_main' ); ?>

	<?php do_action( 'bloglo_before_colophon' ); ?>

	<?php if ( bloglo_is_colophon_displayed() ) { ?>
		<footer id="colophon" class="site-footer" role="contentinfo"<?php bloglo_schema_markup( 'footer' ); ?>>

			<?php do_action( 'bloglo_footer' ); ?>

		</footer><!-- #colophon .site-footer -->
	<?php } ?>

	<?php do_action( 'bloglo_after_colophon' ); ?>

</div><!-- END #page -->
<?php do_action( 'bloglo_after_page_wrapper' ); ?>
<?php wp_footer(); ?>

</body>
</html>
