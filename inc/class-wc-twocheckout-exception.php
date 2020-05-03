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

	/**
	 * The constructor function.
	 *
	 * @param string $message The exception message.
	 * @param string $code The exception code.
	 */
	public function __construct( $message, $code ) {
		parent::__construct( $message, $code );
	}

	/**
	 * Custom string representation of the exception.
	 */
	public function __toString() {
		return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
	}
}
