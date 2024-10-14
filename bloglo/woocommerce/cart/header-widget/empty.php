<?php
/**
 * Header Cart Widget empty cart.
 *
 * @package Bloglo
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div class="bloglo-empty-cart">
	<?php echo bloglo()->icons->get_svg( 'shopping-empty', array( 'aria-hidden' => 'true' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	<p><?php esc_html_e( 'No products in the cart.', 'bloglo' ); ?></p>
</div>
