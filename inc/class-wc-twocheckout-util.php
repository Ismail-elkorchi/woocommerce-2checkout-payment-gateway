<?php

class WC_Twocheckout_Util {


	static function return_resp( $contents ) {
		$array_object = self::objectToArray( $contents );
		self::checkError( $array_object );
		return $array_object;
	}

	public static function objectToArray( $object ) {
		$object = json_decode( $object, true );
		$array  = array();
		foreach ( $object as $member => $data ) {
			$array[ $member ] = $data;
		}
		return $array;
	}

	public static function checkError( $contents ) {
		if ( isset( $contents['exception'] ) ) {
			throw new WC_Twocheckout_Error( $contents['exception']['errorMsg'], $contents['exception']['errorCode'] );
		}
	}

}
