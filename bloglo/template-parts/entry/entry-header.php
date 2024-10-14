<?php
/**
 * Template part for displaying entry header.
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

?>

<?php do_action( 'bloglo_before_entry_header' ); ?>
<header class="entry-header">

	<?php
	$bloglo_tag = is_single( get_the_ID() ) && ! bloglo_page_header_has_title() ? 'h1' : 'h4';
	$bloglo_tag = apply_filters( 'bloglo_entry_header_tag', $bloglo_tag );

	$bloglo_title_string = '%2$s%1$s';

	if ( 'link' === get_post_format() ) {
		$bloglo_title_string = '<a href="%3$s" title="%3$s" rel="bookmark">%2$s%1$s</a>';
	} elseif ( ! is_single( get_the_ID() ) ) {
		$bloglo_title_string = '<a href="%3$s" title="%4$s" rel="bookmark">%2$s%1$s</a>';
	}

	$bloglo_title_icon = apply_filters( 'bloglo_post_title_icon', '' );
	$bloglo_title_icon = bloglo()->icons->get_svg( $bloglo_title_icon );
	?>

	<<?php echo tag_escape( $bloglo_tag ); ?> class="entry-title"<?php bloglo_schema_markup( 'headline' ); ?>>
		<?php
		echo sprintf(
			wp_kses_post( $bloglo_title_string ),
			wp_kses_post( get_the_title() ),
			wp_kses_post( (string) $bloglo_title_icon ),
			esc_url( bloglo_entry_get_permalink() ),
			the_title_attribute( array( 'echo' => false ) )
		);
		?>
	</<?php echo tag_escape( $bloglo_tag ); ?>>

</header>
<?php do_action( 'bloglo_after_entry_header' ); ?>
