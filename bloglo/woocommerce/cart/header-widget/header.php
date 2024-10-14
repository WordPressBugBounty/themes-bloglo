<?php
/**
 * Header Cart Widget dropdown header.
 *
 * @package Bloglo
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$bloglo_cart_count    = WC()->cart->get_cart_contents_count();
$bloglo_cart_subtotal = WC()->cart->get_cart_subtotal();

?>
<div class="wc-cart-widget-header">
	<span class="bloglo-cart-count">
		<?php
		/* translators: %s: the number of cart items; */
		echo wp_kses_post( sprintf( _n( '%s item', '%s items', $bloglo_cart_count, 'bloglo' ), $bloglo_cart_count ) );
		?>
	</span>

	<span class="bloglo-cart-subtotal">
		<?php
		/* translators: %s is the cart subtotal. */
		echo wp_kses_post( sprintf( __( 'Subtotal: %s', 'bloglo' ), '<span>' . $bloglo_cart_subtotal . '</span>' ) );
		?>
	</span>
</div>
