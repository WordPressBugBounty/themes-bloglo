<?php
/**
 * Template part for displaying entry content.
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

<?php do_action( 'bloglo_before_entry_content' ); ?>
<div class="entry-content bloglo-entry"<?php bloglo_schema_markup( 'text' ); ?>>
	<?php the_content(); ?>
</div>

<?php bloglo_link_pages(); ?>

<?php do_action( 'bloglo_after_entry_content' ); ?>
