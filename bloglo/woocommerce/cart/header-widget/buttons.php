<?php
/**
 * Header Cart Widget cart & checkout buttons.
 *
 * @package Bloglo
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div class="bloglo-cart-buttons">
	<a href="<?php echo esc_url( wc_get_cart_url() ); ?>" class="bloglo-btn btn-text-1" role="button">
		<span><?php esc_html_e( 'View Cart', 'bloglo' ); ?></span>
	</a>

	<a href="<?php echo esc_url( wc_get_checkout_url() ); ?>" class="bloglo-btn btn-fw" role="button">
		<span><?php esc_html_e( 'Checkout', 'bloglo' ); ?></span>
	</a>
</div>
