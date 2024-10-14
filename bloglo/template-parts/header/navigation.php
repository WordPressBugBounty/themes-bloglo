<?php
/**
 * The template for displaying header navigation.
 *
 * @package     Bloglo
 * @author      Peregrine Themes
 * @since       1.0.0
 */

?>

<nav class="site-navigation main-navigation bloglo-primary-nav bloglo-nav bloglo-header-element" role="navigation"<?php bloglo_schema_markup( 'site_navigation' ); ?> aria-label="<?php esc_attr_e( 'Site Navigation', 'bloglo' ); ?>">

<?php

if ( has_nav_menu( 'bloglo-primary' ) ) {
	wp_nav_menu(
		array(
			'theme_location' => 'bloglo-primary',
			'menu_id'        => 'bloglo-primary-nav',
			'container'      => '',
			'link_before'    => '<span>',
			'link_after'     => '</span>',
		)
	);
} else {
	wp_page_menu(
		array(
			'menu_class'  => 'bloglo-primary-nav',
			'show_home'   => true,
			'container'   => 'ul',
			'before'      => '',
			'after'       => '',
			'link_before' => '<span>',
			'link_after'  => '</span>',
		)
	);
}

?>
</nav><!-- END .bloglo-nav -->
