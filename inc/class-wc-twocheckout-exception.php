<?php
/**
 * WooCommerce 2Checkout Exception Class
 *
 * @extends Exception
 * @package WC_Twocheckout
 */

/**
 * Extends Exception to provide additional data.
 */
class WC_Twocheckout_Exception extends Exception {

	public function __construct( $message, $code ) {
		parent::__construct( $message, $code );
	}

	public function __toString() {
		return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
	}
}
