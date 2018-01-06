<?php 
	/* Template Name: Recuring Payments Stripe*/	
	
	global $zoner_config, $zoner, $prefix;
	$stripeSK  = $zoner_config['stripe-secret-key'];
	$stripePK  = $zoner_config['stripe-publishable-key'];
	
	require_once ("includes/admin/classes/payments/stripe/stripe.php");
	Stripe::setApiKey($stripeSK);
	
	$input 		= @file_get_contents("php://input");
	$event_json = json_decode($input);
	
	if (!empty($event_json)) {
		$array_of_customers = get_object_vars($event_json->data);
		foreach($array_of_customers as $key => $value) {
			$stripe_customer_id = $value->customer;
		}
		
		$args =	array();
		$args =	array('meta_key'      =>  $prefix.'stripe_customer_id', 
					  'meta_value'    =>  $stripe_customer_id
					);
		$stripe_customers = get_users( $args ); 	
		
		if (!empty($stripe_customers)) {
			foreach ($stripe_customers as $stripe_customer) {
				$curr_user_id = $stripe_customer->ID;
			
				if ($event_json->type == 'invoice.payment_succeeded') {
					$packageID   		  = get_user_meta($curr_user_id, $prefix.'package_id', true);
					$stripe_customer_id   = get_user_meta($curr_user_id, $prefix.'stripe_customer_id', true);
					$recuring   		  = get_user_meta($curr_user_id, $prefix.'payment_recurring', true);
					$valid_thru     	  = get_user_meta($curr_user_id, $prefix.'valid_thru', true);
				
					if (($recuring  == 'on') && ($packageID)) {
						$package_info = $zoner->membership->zoner_get_package_info_by_id($packageID);
						
						if (!empty($package_info)) {
							$billing_period 	= $package_info->billing_period;
							$freq				= (int)$package_info->freq;
							$payment_code		= 4;
							$payment_recurring  = 'on';
							$property_id 		= 0;
							$pkg_price			= $pkg_info->price;
							$user_info	= null;
							$currency   = esc_attr($zoner_config['paid-currency']);
							$transactionId = $stripe_customer_id;
				
							$zoner->membership->zoner_create_invoice_row(
													$transactionId,
													'Stripe',
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
						}
					}
				}
			}	
				
		} elseif (($event_json->type == 'invoice.payment_failed') || ($event_json->type == 'charge.failed')) {
			$zoner->membership->zoner_cleare_customer_information($curr_user_id, 'Stripe');
		}
	}	
	
	status_header(200);
	exit;