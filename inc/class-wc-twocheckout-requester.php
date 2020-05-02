<?php

class WC_Twocheckout_Requester {

	public $api_url;
	private $private_key;

	function __construct() {
		$this->private_key = WC_Twocheckout_Api::$private_key;
		$this->api_url     = WC_Twocheckout_Api::$api_url;
	}

	function do_call( $data ) {
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
			throw new WC_Twocheckout_Error( 'cURL call failed', '403' );
		} else {
			return $resp;
		}
	}

}
