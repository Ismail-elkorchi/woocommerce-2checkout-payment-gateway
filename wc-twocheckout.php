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

add_action( 'plugins_loaded', 'wc_twocheckout_init' );
/**
 * Init the plugin after plugins_loaded so environment variables are set.
 */
function wc_twocheckout_init() {
	if ( ! class_exists( 'WC_Payment_Gateway' ) ) {
		return; // if the WC payment gateway class is not available, do nothing.
	}
	if ( class_exists( 'WC_Twocheckout' ) ) {
		return;
	}

	include plugin_dir_path( __FILE__ ) . 'inc/class-wc-twocheckout-gateway.php';
	include plugin_dir_path( __FILE__ ) . 'inc/class-wc-twocheckout-api.php';

	add_filter( 'woocommerce_payment_gateways', 'wc_twocheckout_add_gateway' );
	/**
	 * Loads payment gateways via hooks for use in the store.
	 *
	 * @param array $gateways List of payment gateways to be loaded.
	 **/
	function wc_twocheckout_add_gateway( $gateways ) {
		$gateways[] = 'WC_Twocheckout_Gateway';
		return $gateways;
	}
}
