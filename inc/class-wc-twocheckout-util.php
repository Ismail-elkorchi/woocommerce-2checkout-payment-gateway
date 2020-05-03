<?php
/**
 * WooCommerce 2Checkout Util Class
 *
 * @package WC_Twocheckout
 */

/**
 * Provides static methods that perform small and repetitive operations.
 */
class WC_Twocheckout_Util {

	/**
	 * Get the response from 2Checkout's API.
	 *
	 * @param string $contents json data.
	 * @return array
	 */
	static function return_resp( $contents ) {
		$array_object = self::objectToArray( $contents );
		self::checkError( $array_object );
		return $array_object;
	}

	/**
	 * Convert a json object to an array.
	 *
	 * @param string $object json data.
	 * @return array
	 */
	public static function objectToArray( $object ) {
		$object = json_decode( $object, true );
		$array  = array();
		foreach ( $object as $member => $data ) {
			$array[ $member ] = $data;
		}
		return $array;
	}

	/**
	 * Throw an exception if it exists.
	 *
	 * @param string $contents json data.
	 * @throws WC_Twocheckout_Exception If it exists.
	 */
	public static function checkError( $contents ) {
		if ( isset( $contents['exception'] ) ) {
			throw new WC_Twocheckout_Exception( $contents['exception']['errorMsg'], $contents['exception']['errorCode'] );
		}
	}

}
