<?php
/**
 * Plugin Name: WooCommerce 2Checkout Payment Gateway
 * Plugin URI:
 * Description: Allows you to use 2Checkout payment gateway with the WooCommerce plugin.
 * Version: 0.0.1
 * Author: Ismail El Korchi
 * Author URI: https://www.ismailelkorchi.com
 * Text Domain: wc_twocheckout
 *
 * @package WC_Twocheckout
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/*
 Add a custom payment class to WC
  ------------------------------------------------------------ */
add_action( 'plugins_loaded', 'woocommerce_twocheckout', 0 );

function woocommerce_twocheckout() {
	if ( ! class_exists( 'WC_Payment_Gateway' ) ) {
		return; // if the WC payment gateway class is not available, do nothing.
	}
	if ( class_exists( 'WC_Twocheckout' ) ) {
		return;
	}

	include plugin_dir_path( __FILE__ ) . 'inc/class-wc-twocheckout-gateway.php';
	include plugin_dir_path( __FILE__ ) . 'inc/class-wc-twocheckout-api.php';

	/**
	 * Add the gateway to WooCommerce
	 **/
	function add_twocheckout_gateway( $methods ) {
		$methods[] = 'WC_Gateway_Twocheckout';
		return $methods;
	}

	add_filter( 'woocommerce_payment_gateways', 'add_twocheckout_gateway' );

}
