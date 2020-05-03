/* global TCO, jQuery */

var formName = 'order_review';
var myForm = document.getElementsByName( 'checkout' )[0];
if ( myForm ) {
	myForm.id = 'tcoCCForm';
	formName = 'tcoCCForm';
}
jQuery( '#' + formName ).on( 'click', function() {
	jQuery( '#place_order' ).unbind( 'click' );
	jQuery( '#place_order' ).click( function( e ) {
		e.preventDefault();
		retrieveToken();
	} );
} );

function successCallback( data ) {
	clearPaymentFields();
	jQuery( '#token' ).val( data.response.token.token );
	jQuery( '#place_order' ).unbind( 'click' );
	jQuery( '#place_order' ).click( function( e ) { // eslint-disable-line
		return true;
	} );
	jQuery( '#place_order' ).click();
}

function errorCallback( data ) {
	if ( data.errorCode === 200 ) {
		TCO.requestToken( successCallback, errorCallback, formName );
	} else if ( data.errorCode === 401 ) {
		clearPaymentFields();
		jQuery( '#place_order' ).click( function( e ) {
			e.preventDefault();
			retrieveToken();
		} );
		jQuery( '#twocheckout_error_creditcard' ).show();
	} else {
		clearPaymentFields();
		jQuery( '#place_order' ).click( function( e ) {
			e.preventDefault();
			retrieveToken();
		} );
		alert( data.errorMsg ); // eslint-disable-line
	}
}

function retrieveToken() {
	jQuery( '#twocheckout_error_creditcard' ).hide();
	if ( jQuery( 'div.payment_method_twocheckout:first' ).css( 'display' ) === 'block' ) {
		jQuery( '#ccNo' ).val( jQuery( '#ccNo' ).val().replace( /[^0-9\.]+/g, '' ) );
		TCO.requestToken( successCallback, errorCallback, formName );
	} else {
		jQuery( '#place_order' ).unbind( 'click' );
		jQuery( '#place_order' ).click( function( e ) { // eslint-disable-line
			return true;
		} );
		jQuery( '#place_order' ).click();
	}
}

function clearPaymentFields() {
	jQuery( '#ccNo' ).val( '' );
	jQuery( '#cvv' ).val( '' );
	jQuery( '#expMonth' ).val( '' );
	jQuery( '#expYear' ).val( '' );
}
