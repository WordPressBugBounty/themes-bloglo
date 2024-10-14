<?php
/**
 * Header Cart Widget icon.
 *
 * @package Bloglo
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$bloglo_cart_count = WC()->cart->get_cart_contents_count();
$bloglo_cart_icon  = apply_filters( 'bloglo_wc_cart_widget_icon', 'shopping-bag' );

?>
<a href="<?php echo esc_url( wc_get_cart_url() ); ?>" class="bloglo-cart">
	<?php echo bloglo()->icons->get_svg( $bloglo_cart_icon ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	<?php if ( $bloglo_cart_count > 0 ) { ?>
		<span class="bloglo-cart-count"><?php echo esc_html( $bloglo_cart_count ); ?></span>
	<?php } ?>
</a>
