<?php
/**
 * Bloglo Customizer widgets class.
 *
 * @package Bloglo
 * @author Peregrine Themes
 * @since   1.0.0
 */

/**
 * Do not allow direct script access.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Bloglo_Customizer_Widget_Cart' ) ) :

	/**
	 * Bloglo Customizer widget class
	 */
	class Bloglo_Customizer_Widget_Cart extends Bloglo_Customizer_Widget {

		/**
		 * Primary class constructor.
		 *
		 * @since 1.0.0
		 * @param array $args An array of the values for this widget.
		 */
		public function __construct( $args = array() ) {

			parent::__construct( $args );

			$this->name        = esc_html__( 'Cart', 'bloglo' );
			$this->description = esc_html__( 'Displays WooCommerce cart.', 'bloglo' );
			$this->icon        = 'dashicons dashicons-cart';
			$this->type        = 'cart';
		}

		/**
		 * Displays the form for this widget on the Widgets page of the WP Admin area.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function form() {}
	}
endif;
