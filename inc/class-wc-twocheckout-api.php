<?php

abstract class WC_Twocheckout_Api {

	public static $sid;
	public static $private_key;
	public static $api_url;
	public static $error;
	const VERSION = '0.0.1';

	static function setCredentials( $sid, $private_key, $mode = '' ) {
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
require dirname( __FILE__ ) . '/class-wc-twocheckout-error.php';
