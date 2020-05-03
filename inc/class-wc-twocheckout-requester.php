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
		$options             = array(
			'method'  => 'POST',
			'headers' => $header,
			'body'    => $data,
			'timeout' => 120,
		);

		$response = wp_safe_remote_post( $url, $options );

		if ( is_wp_error( $response ) || empty( $response['body'] ) ) {
			throw new WC_Twocheckout_Exception( 'There was a problem connecting to 2checkout API endpoint.', '403' );
		}

		return json_decode( $response['body'] );
	}

}
