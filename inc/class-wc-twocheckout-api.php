<?php
/**
 * WooCommerce 2Checkout Api class
 *
 * @package WC_Twocheckout
 */

/**
 * Abstract class that handle Api Credentials.
 */
abstract class WC_Twocheckout_Api {

	public static $sid;
	public static $private_key;
	public static $api_url;
	public static $error;
	const VERSION = '0.0.1';

	/**
	 * Set 2Checkout API credentials.
	 *
	 * @param string $sid The seller id.
	 * @param string $private_key The private API key.
	 * @param string $mode The mode of connection : sandbox or live.
	 */
	static function set_credentials( $sid, $private_key, $mode = '' ) {
		self::$sid         = $sid;
		self::$private_key = $private_key;
		if ( 'sandbox' === $mode ) {
			self::$api_url = 'https://sandbox.2checkout.com/checkout/api/1/' . $sid . '/rs/authService';
		} else {
			self::$api_url = 'https://www.2checkout.com/checkout/api/1/' . $sid . '/rs/authService';
		}
	}
}

require dirname( __FILE__ ) . '/class-wc-twocheckout-requester.php';
require dirname( __FILE__ ) . '/class-wc-twocheckout-charge.php';
require dirname( __FILE__ ) . '/class-wc-twocheckout-util.php';
require dirname( __FILE__ ) . '/class-wc-twocheckout-exception.php';
