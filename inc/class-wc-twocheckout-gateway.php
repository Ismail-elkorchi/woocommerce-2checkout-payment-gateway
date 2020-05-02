<?php
/**
 * WooCommerce 2Checkout Gateway class.
 *
 * @extends WC_Payment_Gateway
 * @package WC_Twocheckout
 */

/**
 * The main Gateway class.
 */
class WC_Twocheckout_Gateway extends WC_Payment_Gateway {

	// Logging.
	public static $log_enabled = false;
	public static $log         = false;

	public function __construct() {

		$plugin_dir = plugin_dir_url( __FILE__ );

		global $woocommerce;

		$this->id         = 'twocheckout';
		$this->icon       = apply_filters( 'wc_twocheckout_icon', '' . $plugin_dir . 'twocheckout.png' );
		$this->has_fields = true;

		// Load the settings.
		$this->init_form_fields();
		$this->init_settings();

		// Define user set variables.
		$this->title           = $this->get_option( 'title' );
		$this->seller_id       = $this->get_option( 'seller_id' );
		$this->publishable_key = $this->get_option( 'publishable_key' );
		$this->private_key     = $this->get_option( 'private_key' );
		$this->description     = $this->get_option( 'description' );
		$this->sandbox         = $this->get_option( 'sandbox' );
		$this->debug           = $this->get_option( 'debug' );

		self::$log_enabled = $this->debug;

		// Actions.
		add_action( 'woocommerce_receipt_' . $this->id, array( $this, 'receipt_page' ) );

		// Save options.
		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );

		// Payment listener/API hook.
		add_action( 'woocommerce_api_wc_' . $this->id, array( $this, 'check_ipn_response' ) );

		if ( ! $this->is_valid_for_use() ) {
			$this->enabled = false;
		}
	}

	/**
	 * Logging method
	 *
	 * @param string $message the log message.
	 */
	public static function log( $message ) {
		if ( self::$log_enabled ) {
			if ( empty( self::$log ) ) {
				self::$log = new WC_Logger();
			}
			self::$log->add( 'twocheckout', $message );
		}
	}

	/**
	 * Check if this gateway is enabled and available in the user's country
	 *
	 * @access public
	 * @return bool
	 */
	public function is_valid_for_use() {
		$supported_currencies = array(
			'AFN',
			'ALL',
			'DZD',
			'ARS',
			'AUD',
			'AZN',
			'BSD',
			'BDT',
			'BBD',
			'BZD',
			'BMD',
			'BOB',
			'BWP',
			'BRL',
			'GBP',
			'BND',
			'BGN',
			'CAD',
			'CLP',
			'CNY',
			'COP',
			'CRC',
			'HRK',
			'CZK',
			'DKK',
			'DOP',
			'XCD',
			'EGP',
			'EUR',
			'FJD',
			'GTQ',
			'HKD',
			'HNL',
			'HUF',
			'INR',
			'IDR',
			'ILS',
			'JMD',
			'JPY',
			'KZT',
			'KES',
			'LAK',
			'MMK',
			'LBP',
			'LRD',
			'MOP',
			'MYR',
			'MVR',
			'MRO',
			'MUR',
			'MXN',
			'MAD',
			'NPR',
			'TWD',
			'NZD',
			'NIO',
			'NOK',
			'PKR',
			'PGK',
			'PEN',
			'PHP',
			'PLN',
			'QAR',
			'RON',
			'RUB',
			'WST',
			'SAR',
			'SCR',
			'SGF',
			'SBD',
			'ZAR',
			'KRW',
			'LKR',
			'SEK',
			'CHF',
			'SYP',
			'THB',
			'TOP',
			'TTD',
			'TRY',
			'UAH',
			'AED',
			'USD',
			'VUV',
			'VND',
			'XOF',
			'YER',
		);

		if ( ! in_array( get_woocommerce_currency(), apply_filters( 'wc_twocheckout_supported_currencies', $supported_currencies ), true ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Admin Panel Options
	 * - Options for bits like 'title' and availability on a country-by-country basis
	 *
	 * @since 1.0.0
	 */
	public function admin_options() {

		?>
			<h3><?php esc_html_e( '2Checkout', 'wc_twocheckout' ); ?></h3>
			<p><?php esc_html_e( '2Checkout - Credit Card/Paypal', 'wc_twocheckout' ); ?></p>

			<?php if ( $this->is_valid_for_use() ) : ?>

			<table class="form-table">
				<?php
				// Generate the HTML For the settings form.
				$this->generate_settings_html();
				?>
			</table><!--/.form-table-->

		<?php else : ?>
			<div class="inline error">
				<p>
					<strong><?php esc_html_e( 'Gateway Disabled', 'wc_twocheckout' ); ?></strong>: <?php esc_html_e( '2Checkout does not support your store currency.', 'wc_twocheckout' ); ?>
				</p>
			</div>
			<?php
		endif;
	}

	/**
	 * Initialise Gateway Settings Form Fields
	 *
	 * @access public
	 * @return void
	 */
	public function init_form_fields() {

		$this->form_fields = array(
			'enabled'         => array(
				'title'   => __( 'Enable/Disable', 'wc_twocheckout' ),
				'type'    => 'checkbox',
				'label'   => __( 'Enable 2Checkout', 'wc_twocheckout' ),
				'default' => 'yes',
			),
			'title'           => array(
				'title'       => __( 'Title', 'wc_twocheckout' ),
				'type'        => 'text',
				'description' => __( 'This controls the title which the user sees during checkout.', 'wc_twocheckout' ),
				'default'     => __( 'Credit Card/PayPal', 'wc_twocheckout' ),
				'desc_tip'    => true,
			),
			'description'     => array(
				'title'       => __( 'Description', 'wc_twocheckout' ),
				'type'        => 'textarea',
				'description' => __( 'This controls the description which the user sees during checkout.', 'wc_twocheckout' ),
				'default'     => __( 'Pay with Credit Card/PayPal', 'wc_twocheckout' ),
			),
			'seller_id'       => array(
				'title'       => __( 'Seller ID', 'wc_twocheckout' ),
				'type'        => 'text',
				'description' => __( 'Please enter your 2Checkout account number; this is needed in order to take payment.', 'wc_twocheckout' ),
				'default'     => '',
				'desc_tip'    => true,
				'placeholder' => '',
			),
			'publishable_key' => array(
				'title'       => __( 'Publishable Key', 'wc_twocheckout' ),
				'type'        => 'text',
				'description' => __( 'Please enter your 2Checkout Publishable Key; this is needed in order to take payment.', 'wc_twocheckout' ),
				'default'     => '',
				'desc_tip'    => true,
				'placeholder' => '',
			),
			'private_key'     => array(
				'title'       => __( 'Private Key', 'wc_twocheckout' ),
				'type'        => 'text',
				'description' => __( 'Please enter your 2Checkout Private Key; this is needed in order to take payment.', 'wc_twocheckout' ),
				'default'     => '',
				'desc_tip'    => true,
				'placeholder' => '',
			),
			'sandbox'         => array(
				'title'   => __( 'Sandbox/Production', 'wc_twocheckout' ),
				'type'    => 'checkbox',
				'label'   => __( 'Use 2Checkout Sandbox', 'wc_twocheckout' ),
				'default' => 'no',
			),
			'debug'           => array(
				'title'       => __( 'Debug Log', 'wc_twocheckout' ),
				'type'        => 'checkbox',
				'label'       => __( 'Enable logging', 'wc_twocheckout' ),
				'default'     => 'no',
				'description' => sprintf( __( 'Log 2Checkout events', 'wc_twocheckout' ), wc_get_log_file_path( 'twocheckout' ) ),
			),
		);

	}

	/**
	 * Generate the credit card payment form
	 *
	 * @access public
	 */
	public function payment_fields() {
		$plugin_dir = plugin_dir_url( __FILE__ );
		// Description of payment method from settings.
		if ( $this->description ) {
			?>
			<p>
				<?php echo $this->description; ?>
			</p>
			<?php
		}
		?>

		<ul class="woocommerce-error" style="display:none" id="twocheckout_error_creditcard">
			<li>Credit Card details are incorrect, please try again.</li>
		</ul>

		<fieldset>

			<input id="sellerId" type="hidden" maxlength="16" width="20" value="<?php echo $this->seller_id; ?>">
			<input id="publishableKey" type="hidden" width="20" value="<?php echo $this->publishable_key; ?>">
			<input id="token" name="token" type="hidden" value="">

			<!-- Credit card number -->
			<p class="form-row form-row-first">
				<label for="ccNo"><?php echo __( 'Credit Card number', 'wc_twocheckout' ); ?> <span class="required">*</span></label>
				<input type="text" class="input-text" id="ccNo" autocomplete="off" value="" />

			</p>

			<div class="clear"></div>

			<!-- Credit card expiration -->
			<p class="form-row form-row-first">
				<label for="cc-expire-month"><?php echo __( 'Expiration date', 'wc_twocheckout' ); ?> <span class="required">*</span></label>
				<select id="expMonth" class="woocommerce-select woocommerce-cc-month">
					<option value=""><?php esc_html_e( 'Month', 'wc_twocheckout' ); ?></option>
					<?php
					$months = array();
					for ( $i = 1; $i <= 12; $i ++ ) {
						$timestamp                         = mktime( 0, 0, 0, $i, 1 );
						$months[ date( 'n', $timestamp ) ] = date( 'F', $timestamp );
					}
					foreach ( $months as $num => $name ) {
						printf( '<option value="%02d">%s</option>', $num, $name );
					}
					?>
				</select>
				<select id="expYear" class="woocommerce-select woocommerce-cc-year">
					<option value=""><?php esc_html_e( 'Year', 'wc_twocheckout' ); ?></option>
						<?php
						$years = array();
						for ( $i = date( 'y' ); $i <= date( 'y' ) + 15; $i ++ ) {
							printf( '<option value="20%u">20%u</option>', $i, $i );
						}
						?>
				</select>
			</p>
			<div class="clear"></div>

			<!-- Credit card security code -->
			<p class="form-row">
			<label for="cvv"><?php esc_html_e( 'Card security code', 'wc_twocheckout' ); ?> <span class="required">*</span></label>
			<input type="text" class="input-text" id="cvv" autocomplete="off" maxlength="4" style="width:55px" />
			<span class="help"><?php esc_html_e( '3 or 4 digits usually found on the signature strip.', 'wc_twocheckout' ); ?></span>
			</p>

			<div class="clear"></div>

		</fieldset>

		<script type="text/javascript">
			var formName = "order_review";
			var myForm = document.getElementsByName('checkout')[0];
			if(myForm) {
				myForm.id = "tcoCCForm";
				formName = "tcoCCForm";
			}
			jQuery('#' + formName).on("click", function(){
				jQuery('#place_order').unbind('click');
				jQuery('#place_order').click(function(e) {
					e.preventDefault();
					retrieveToken();
				});
			});

			function successCallback(data) {
				clearPaymentFields();
				jQuery('#token').val(data.response.token.token);
				jQuery('#place_order').unbind('click');
				jQuery('#place_order').click(function(e) {
					return true;
				});
				jQuery('#place_order').click();
			}

			function errorCallback(data) {
				if (data.errorCode === 200) {
					TCO.requestToken(successCallback, errorCallback, formName);
				} else if(data.errorCode == 401) {
					clearPaymentFields();
					jQuery('#place_order').click(function(e) {
						e.preventDefault();
						retrieveToken();
					});
					jQuery("#twocheckout_error_creditcard").show();

				} else{
					clearPaymentFields();
					jQuery('#place_order').click(function(e) {
						e.preventDefault();
						retrieveToken();
					});
					alert(data.errorMsg);
				}
			}

			var retrieveToken = function () {
				jQuery("#twocheckout_error_creditcard").hide();
				if (jQuery('div.payment_method_twocheckout:first').css('display') === 'block') {
					jQuery('#ccNo').val(jQuery('#ccNo').val().replace(/[^0-9\.]+/g,''));
					TCO.requestToken(successCallback, errorCallback, formName);
				} else {
					jQuery('#place_order').unbind('click');
					jQuery('#place_order').click(function(e) {
						return true;
					});
					jQuery('#place_order').click();
				}
			}

			function clearPaymentFields() {
				jQuery('#ccNo').val('');
				jQuery('#cvv').val('');
				jQuery('#expMonth').val('');
				jQuery('#expYear').val('');
			}

		</script>

		<?php if ( $this->sandbox === 'yes' ) : ?>
		<script type="text/javascript" src="https://sandbox.2checkout.com/checkout/api/script/publickey/<?php echo $this->seller_id; ?>"></script>
		<script type="text/javascript" src="https://sandbox.2checkout.com/checkout/api/2co.js"></script>
		<?php else : ?>
		<script type="text/javascript" src="https://www.2checkout.com/checkout/api/script/publickey/<?php echo $this->seller_id; ?>"></script>
		<script type="text/javascript" src="https://www.2checkout.com/checkout/api/2co.js"></script>
			<?php
		endif;
	}

	/**
	 * Process the payment and return the result
	 *
	 * @access public
	 * @param int $order_id the order id of the payment.
	 * @return array
	 */
	public function process_payment( $order_id ) {
		global $woocommerce;
		$order = new WC_Order( $order_id );

		if ( 'yes' === $this->debug && $this->notify_url !== '' ) {
			$this->log( 'Generating payment form for order ' . $order->get_order_number() . '. Notify URL: ' . $this->notify_url );
		}

		// 2Checkout Args.
		$twocheckout_args = array(
			'token'           => $_POST['token'],
			'sellerId'        => $this->seller_id,
			'currency'        => get_woocommerce_currency(),
			'total'           => $order->get_total(),

			// Order key.
			'merchantOrderId' => $order->get_order_number(),

			// Billing Address info.
			'billingAddr'     => array(
				'name'        => $order->get_billing_first_name() . ' ' . $order->get_billing_last_name(),
				'addrLine1'   => $order->get_billing_address_1(),
				'addrLine2'   => $order->get_billing_address_2(),
				'city'        => $order->get_billing_city(),
				'state'       => $order->get_billing_state(),
				'zipCode'     => $order->get_billing_postcode(),
				'country'     => $order->get_billing_country(),
				'email'       => $order->get_billing_email(),
				'phoneNumber' => $order->get_billing_phone(),
			),
		);

		try {
			if ( $this->sandbox === 'yes' ) {
				WC_Twocheckout_Api::setCredentials( $this->seller_id, $this->private_key, 'sandbox' );
			} else {
				WC_Twocheckout_Api::setCredentials( $this->seller_id, $this->private_key );
			}
			$charge = WC_Twocheckout_Charge::auth( $twocheckout_args );
			if ( $charge['response']['responseCode'] === 'APPROVED' ) {
				$order->payment_complete();
				return array(
					'result'   => 'success',
					'redirect' => $this->get_return_url( $order ),
				);
			}
		} catch ( WC_Twocheckout_Exception $e ) {
			wc_add_notice( $e->getMessage(), $notice_type = 'error' );
			return;
		}
	}

}
