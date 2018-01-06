<?php 
	/* Template Name: Recuring Payments PayPal*/	
	global $zoner, $zoner_config, $prefix;

	function getUserIdByTxnID($txnID) {
		global $prefix, $zoner, $zoner_config;
		$paypal_customer_id =  str_replace('-', 'xxx', $txnID);
		
		$arg = array(
						'role'         => 'agent',
						'meta_key'     => $prefix . 'paypal_customer_id',
						'meta_value'   => $paypal_customer_id,
						'meta_compare' => '='
				);
    
		
		$userid = 0;
		$blogusers = get_users($arg);
		foreach ($blogusers as $user) {
			$userid=$user->ID;
		}
		
		return $userid;	
	}
	
	
	/*Connect varibale*/
	$API_UserName  = $zoner_config['paypal-api-username'];
	$API_Password  = $zoner_config['paypal-api-password'];
	$API_Signature = $zoner_config['paypal-api-signature'];
	$SandboxFlag = 'sandbox';
	$isDebug 	 = 0;
	$isSandBox   = 0;  
	
	if (!empty($zoner_config['paid-api-method']))
		$SandboxFlag = esc_attr($zoner_config['paid-api-method']);
	
	if ($SandboxFlag  == 'sandbox') {
		$isDebug 	= 1;
		$isSandBox  = 1;
		$paypal_url = "https://www.sandbox.paypal.com/cgi-bin/webscr";
	} else {
		$paypal_url = "https://www.paypal.com/cgi-bin/webscr";
	}
	
	define("DEBUG", $isDebug);
    define("LOG_FILE", "ipn.log");
	
	$raw_post_data 	= file_get_contents('php://input');
	$raw_post_array = explode('&', $raw_post_data);
	$myPost = array();
	foreach ($raw_post_array as $keyval) {
		$keyval = explode ('=', $keyval);
		if (count($keyval) == 2)
			$myPost[$keyval[0]] = urldecode($keyval[1]);
	}
	
	// read the post from PayPal system and add 'cmd'
	$req = 'cmd=_notify-validate';
	if(function_exists('get_magic_quotes_gpc')) {
		$get_magic_quotes_exists = true;
	}
	foreach ($myPost as $key => $value) {
		if($get_magic_quotes_exists == true && get_magic_quotes_gpc() == 1) {
			$value = urlencode(stripslashes($value));
		} else {
			$value = urlencode($value);
		}
		$req .= "&$key=$value";
	}
	// Post IPN data back to PayPal to validate the IPN data is genuine
	// Without this step anyone can fake IPN data
	
	$ch = curl_init($paypal_url);
	if ($ch == FALSE) {
		return FALSE;
	}
	curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
	curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
	if(DEBUG == true) {
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLINFO_HEADER_OUT, 1);
	}
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));
	$res = curl_exec($ch);
	if (curl_errno($ch) != 0) { // cURL error
		if(DEBUG == true)
			error_log(date('[Y-m-d H:i e] '). "Can't connect to PayPal to validate IPN message: " . curl_error($ch) . PHP_EOL, 3, LOG_FILE);
		curl_close($ch);
		exit;
	} else {
			// Log the entire HTTP response if debug is switched on.
		if(DEBUG == true) {
			error_log(date('[Y-m-d H:i e] '). "HTTP request of validation request:". curl_getinfo($ch, CURLINFO_HEADER_OUT) ." for IPN payload: $req" . PHP_EOL, 3, LOG_FILE);
			error_log(date('[Y-m-d H:i e] '). "HTTP response of validation request: $res" . PHP_EOL, 3, LOG_FILE);
		}
		curl_close($ch);
	}
	
	// Inspect IPN validation result and act accordingly
	// Split response headers and payload, a better way for strcmp
	$tokens = explode("\r\n\r\n", trim($res));
	$res = trim(end($tokens));
	if (strcmp ($res, "VERIFIED") == 0) {
		
		$payer_email            =   sanitize_email ($_POST['payer_email']);
		$receiver_email         =   sanitize_email ($_POST['receiver_email']);
		
		$payment_status         =   esc_attr($_POST['payment_status']);
        $txn_id                 =   esc_attr($_POST['txn_id']);
        $txn_type               =   esc_attr($_POST['txn_type']);   
        $payer_id               =   esc_attr($_POST['payer_id']);

        $curr_user_id           =   getUserIdByTxnID($txn_id) ; 
        $pkg_id                 =   get_user_meta($curr_user_id, $prefix. 'package_id',true);
        $pkg_info 				=   $zoner->membership->zoner_get_package_info_by_id($pkg_id);
		$transactionId			= 	esc_attr ($_POST['invoice']);
		
		if (!empty($pkg_info)) {
			if( $payment_status=='Completed' ){
				
				$pkg_name 				 = $pkg_info->title;	
				$pkg_billing_period 	 = $pkg_info->billing_period;
				$pkg_period_freq		 = (int) $pkg_info->freq;
				$pkg_price				 = $pkg_info->price;
								
				if ($pkg_billing_period == 1)	{ 
					$pp_period =  'Day';  	
				} elseif ($pkg_billing_period == 2)	{ 
					$pp_period =  'Week'; 	
				} elseif ($pkg_billing_period == 3)	{ 
					$pp_period =  'Month'; 	
				} else { 
					$pp_period =  'Year'; 
				}
				
				$payment_code  		= 4;
				$packageID			= $pkg_id;
				$property_id 		= 0;
				
				$payment_recurring 	= 'on';	
				
				$user_info	= null;
				$currency   = esc_attr($zoner_config['paid-currency']);
								
				$zoner->membership->zoner_create_invoice_row(
													$transactionId,
													'PayPal',
													$payment_code, 
													$payment_recurring, 
													$property_id, 
													$packageID, 
													$pkg_price, 
													$currency, 
													$user_info
												);
				
				$zoner->membership->zoner_update_user_exp_date (
										$curr_user_id,
										$packageID,  
										$zoner->membership->zoner_get_expiry_date($pkg_billing_period, $pkg_period_freq)
								);
								
			} else {
				$zoner->membership->zoner_cleare_customer_information($curr_user_id, 'PayPal');	
			}
		}
		
		
		if(DEBUG == true) {
			error_log(date('[Y-m-d H:i e] '). "Verified IPN: $req ". PHP_EOL, 3, LOG_FILE);
		}
	} else if (strcmp ($res, "INVALID") == 0) {
		if(DEBUG == true) {
			error_log(date('[Y-m-d H:i e] '). "Invalid IPN: $req" . PHP_EOL, 3, LOG_FILE);
		}
	}