<?php
/**
 * The header for our theme.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package     Bloglo
 * @author      Peregrine Themes
 * @since       1.0.0
 */

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?><?php bloglo_schema_markup( 'html' ); ?> <?php echo bloglo_option( 'dark_mode' ) ? 'data-theme="dark"' : ''; ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

<?php wp_body_open(); ?>

<?php do_action( 'bloglo_before_page_wrapper' ); ?>
<div id="page" class="hfeed site">
	<a class="skip-link screen-reader-text" href="#main"><?php esc_html_e( 'Skip to content', 'bloglo' ); ?></a>

	<?php
	if ( get_header_image() ) {
		the_custom_header_markup();
	}
	?>

	<?php do_action( 'bloglo_before_masthead' ); ?>

	<header id="masthead" class="site-header" role="banner"<?php bloglo_masthead_atts(); ?><?php bloglo_schema_markup( 'header' ); ?>>
		<?php do_action( 'bloglo_header' ); ?>
		<?php do_action( 'bloglo_page_header' ); ?>
	</header><!-- #masthead .site-header -->

	<?php do_action( 'bloglo_after_masthead' ); ?>

	<?php do_action( 'bloglo_before_main' ); ?>
		<div id="main" class="site-main">

			<?php do_action( 'bloglo_main_start' ); ?>
