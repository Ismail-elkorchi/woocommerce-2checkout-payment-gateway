<?php
/**
 * WooCommerce 2Checkout Charge class
 *
 * @extends WC_Twocheckout_Api
 * @package WC_Twocheckout
 */

/**
 * Process the payment.
 */
class WC_Twocheckout_Charge extends WC_Twocheckout_Api {


	public static function auth( $params = array() ) {
		$request = new WC_Twocheckout_Requester();
		$result  = $request->do_call( $params );
		return WC_Twocheckout_Util::return_resp( $result );
	}

}
