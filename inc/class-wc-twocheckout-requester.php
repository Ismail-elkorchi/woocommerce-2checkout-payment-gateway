<?php
/**
 * WooCommerce 2Checkout Requester class.
 *
 * @package WC_Twocheckout
 */

/**
 * Send the request to 2Checkout's API.
 */
class WC_Twocheckout_Requester {

	/**
	 * 2checkout API URL.
	 *
	 * @var string $api_url
	 */
	public $api_url;

	/**
	 * The user private key.
	 *
	 * @var string $private_key
	 */
	private $private_key;

	/**
	 * The constructor function.
	 */
	public function __construct() {
		$this->private_key = WC_Twocheckout_Api::$private_key;
		$this->api_url     = WC_Twocheckout_Api::$api_url;
	}

	/**
	 * Call 2checkout API.
	 *
	 * @param string $data json data.
	 * @throws WC_Twocheckout_Exception If the cURL call failed.
	 * @return string
	 */
	public function do_call( $data ) {
		$data['private_key'] = $this->private_key;
		$data                = wp_json_encode( $data );
		$header              = array( 'content-type:application/JSON', 'content-length:' . strlen( $data ) );
		$url                 = $this->api_url;
		$ch                  = curl_init( $url );
		curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'POST' );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $data );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_HTTPHEADER, $header );
		curl_setopt( $ch, CURLOPT_TIMEOUT, 120 );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
		$resp = curl_exec( $ch );
		curl_close( $ch );
		if ( false === $resp ) {
			throw new WC_Twocheckout_Exception( 'cURL call failed', '403' );
		} else {
			return $resp;
		}
	}

}
