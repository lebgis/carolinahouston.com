<?php

/**
 * Zoner membership
*/
 
class zoner_membership {
	
	public function __construct() {
		add_filter( 'manage_edit-packages_columns', 			array( $this, 'zoner_edit_packages_columns'));
		add_action( 'manage_packages_posts_custom_column', 	 	array( $this, 'zoner_custom_packages_columns'), 2);
		add_filter( 'manage_edit-packages_sortable_columns', 	array( $this, 'zoner_packages_sortable_columns'));
		
		
		/*Ajax calls*/
		
		add_action( 'wp_ajax_nopriv_get_total_price', array($this, 'zoner_get_total_price') );  
		add_action( 'wp_ajax_get_total_price', array($this, 'zoner_get_total_price') );
		
		/*Featured property toggle-d*/
		add_action( 'wp_ajax_nopriv_zoner_featured_toggle', array($this, 'zoner_featured_toggle') );  
		add_action( 'wp_ajax_zoner_featured_toggle', array($this, 'zoner_featured_toggle') );
		
		/*BACS paid by each property*/
		add_action( 'wp_ajax_nopriv_zoner_bacs_paid_per_package', array($this, 'zoner_bacs_paid_per_package') );  
		add_action( 'wp_ajax_zoner_bacs_paid_per_package', 		 array($this, 'zoner_bacs_paid_per_package') );
		add_action( 'wp_ajax_nopriv_zoner_bacs_paid_per_property', array($this, 'zoner_bacs_paid_per_property') );
		add_action( 'wp_ajax_zoner_bacs_paid_per_property', 		 array($this, 'zoner_bacs_paid_per_property') );
		//after Approved new invoice
		add_action( 'transition_post_status', array( $this,'zoner_new_bacs_invoices_approve'), 10, 3 );
		
		/*PayPal paid by each property*/
		add_action( 'wp_ajax_nopriv_zoner_paypal_paid_per_property', array($this, 'zoner_paypal_paid_per_property') );  
		add_action( 'wp_ajax_zoner_paypal_paid_per_property', 		 array($this, 'zoner_paypal_paid_per_property') );
		
		add_action( 'wp_ajax_nopriv_zoner_paypal_paid_per_package',  array($this, 'zoner_paypal_paid_per_package') );  
		add_action( 'wp_ajax_zoner_paypal_paid_per_package', 		 array($this, 'zoner_paypal_paid_per_package') );
		
		/*Stripe paid by each property*/
		add_action( 'wp_ajax_nopriv_zoner_get_stripe_payment_data', array($this, 'zoner_get_stripe_payment_data') );  
		add_action( 'wp_ajax_zoner_get_stripe_payment_data',  	    array($this, 'zoner_get_stripe_payment_data') );
		
		add_action( 'wp_ajax_nopriv_zoner_get_stripe_package_payment_data', array($this, 'zoner_get_stripe_package_payment_data') );  
		add_action( 'wp_ajax_zoner_get_stripe_package_payment_data',  	    array($this, 'zoner_get_stripe_package_payment_data') );
		
		
		add_action( 'wp_ajax_nopriv_zoner_complete_stripe_payment', array($this, 'zoner_stripe_paid_per_property_completed') );  
		add_action( 'wp_ajax_zoner_complete_stripe_payment',  	    array($this, 'zoner_stripe_paid_per_property_completed') );
		
		add_action( 'wp_ajax_nopriv_zoner_complete_stripe_package_payment', array($this, 'zoner_complete_stripe_package_payment') );  
		add_action( 'wp_ajax_zoner_complete_stripe_package_payment',  	    array($this, 'zoner_complete_stripe_package_payment') );
		
		
		add_action( 'wp', array($this, 'zoner_paypal_paid_completed'), 399 );
		
		add_action('init', array($this, 'zoner_register_session'));
		add_action('zoner_before_enqueue_script', array($this, 'zoner_get_stripe_js'));
	}	
	
	
	public function zoner_get_stripe_js() {
		global $zoner, $zoner_config;
		if (!empty($zoner_config['paid-system']) && ($zoner_config['paid-system'] == 1))
			if (!empty($zoner_config['membership-stripe']) && ($zoner_config['membership-stripe'] == 1))
				wp_enqueue_script( 'zoner-stripe-library', 'https://checkout.stripe.com/checkout.js', array( 'jquery' ), '', false );
	}
	
	public function zoner_register_session() {
		if( !session_id() ) session_start();
	}	
	
	public function get_membership_periods_name($in_) {
		$periods = array();
		$periods = $this->get_membership_period_values();
		if (empty($in_)) $in_ = 1;
		
		return $periods[$in_];
	}
	
	public function get_available_currency_paid_values() {
		$currencies = array();
		
		$currencies = apply_filters ('zoner_pay_currencies', array(
			'AUD' => __('Australian Dollar', 'zoner'),
			'BRL' => __('Brazilian Real', 'zoner'),
			'CAD' => __('Canadian Dollar', 'zoner'),
			'CZK' => __('Czech Koruna', 'zoner'),
			'DKK' => __('Danish Krone', 'zoner'),
			'EUR' => __('Euro', 'zoner'),
			'HKD' => __('Hong Kong Dollar', 'zoner'),
			'HUF' => __('Hungarian Forint', 'zoner'),
			'ILS' => __('Israeli New Sheqel', 'zoner'),
			'JPY' => __('Japanese Yen', 'zoner'),
			'MYR' => __('Malaysian Ringgit', 'zoner'),
			'MXN' => __('Mexican Peso', 'zoner'),
			'NOK' => __('Norwegian Krone', 'zoner'),
			'NZD' => __('New Zealand Dollar', 'zoner'),
			'PHP' => __('Philippine Peso', 'zoner'),
			'PLN' => __('Polish Zloty', 'zoner'),
			'GBP' => __('Pound Sterling', 'zoner'),
			'RUB' => __('Russian Ruble', 'zoner'),
			'SGD' => __('Singapore Dollar', 'zoner'),
			'SEK' => __('Swedish Krona', 'zoner'),
			'CHF' => __('Swiss Franc', 'zoner'),
			'TWD' => __('Taiwan New Dollar', 'zoner'),
			'THB' => __('Thai Baht', 'zoner'),
			'TRY' => __('Turkish Lira', 'zoner'),
			'USD' => __('U.S. Dollar', 'zoner'),
		));
		
		return $currencies;
			
	}
	
	public function get_paid_details_of_payment_values() {
		
		$periods = array();
		$periods = apply_filters ('zoner_membership_details_of_payment', array(
							1 => __('Publish Property', 'zoner'),
							2 => __('Publish Property with featured', 	'zoner'),
							3 => __('Upgrade property to featured', 'zoner'),
							4 => __('Purchase Package (tarrif)', 'zoner')
				  ) 
		);
		
		return $periods;
	}
	
	public function get_membership_period_values() {
		$periods = array();
		$periods = apply_filters ('zoner_membership_period', array(
							1 => __('Days', 	'zoner'),
							2 => __('Weeks', 	'zoner'),
							3 => __('Months', 	'zoner'),
						    4 => __('Years', 	'zoner'),
				  ) 
		);
		
		return $periods;
	}
	
	public function zoner_edit_packages_columns( $existing_columns ) {
		
		unset($existing_columns['author'], $existing_columns['date']);
		
		if ( empty( $existing_columns ) && ! is_array( $existing_columns ) ) {
			$existing_columns = array();
		}
		
		$columns['billing_period'] 			= __( 'Billing period', 'zoner' );
		$columns['pack_price'] 				= __( 'Price', 'zoner' );
		$columns['pack_available']			= '<span class="fa fa-check-circle-o" title="' . __( 'Available', 'zoner' ) . '"></span>'; 
		$columns['pack_unlim_properties']	= '<span class="fa fa-check-circle-o" title="' . __( 'Unlimited properties', 'zoner' ) . '"></span>'; 
		$columns['pack_unlim_featured']		= '<span class="fa fa-check-circle-o" title="' . __( 'Unlimited featured properties', 'zoner' ) . '"></span>'; 
		$columns['pack_agency_profile']		= '<span class="fa fa-check-circle-o" title="' . __( 'Agency profile', 'zoner' ) . '"></span>'; 
		$columns['author']					= __( 'Author', 'zoner' );
		$columns['date']					= __( 'Date', 'zoner' );
		
		return array_merge( $existing_columns, $columns);
	}
						
	public function zoner_custom_packages_columns($columns) {
		global $post, $prefix, $zoner_config, $zoner;
		 
		 
		switch ( $columns ) {
			case 'billing_period' :
				
				$out_html = '';
				$period_freq = esc_attr(get_post_meta( $post->ID, $prefix . 'billing_period_freq', true ));
				$period      = esc_attr(get_post_meta( $post->ID, $prefix . 'billing_period', true ));
				if (!$period_freq) $period_freq = 30;
				
				if ($period)
				$out_html = '<strong>' . $period_freq . ' ' . $this->get_membership_periods_name($period) . '</strong>';
				
				echo $out_html ? $out_html : '<span class="na">&ndash;</span>';
				
				
				break;
			case 'pack_price' :
				
				$currency_symbol = '$'; 
				if (!empty($zoner_config['paid-currency'])) {
					$currency_symbol = $zoner->currency->get_zoner_currency_symbol(esc_attr($zoner_config['paid-currency']));
				}
				
				$price_html = '';
				
				$price 		= get_post_meta( $post->ID, $prefix.'pack_price', true );
				$price_html = $currency_symbol . ' ' . $price;
						
				echo $price_html ? $price_html : '<span class="na">&ndash;</span>';
						
				break;		
				
			case 'pack_unlim_properties' :
				
				$out_html = '';
				$unlim_properties 	= get_post_meta( $post->ID, $prefix . 'pack_unlim_properties', true );
				$out_html = '<span class="fa fa-times"></span>'; 
				if ( $unlim_properties  == 'on') 
				$out_html = '<span class="fa fa-check-circle-o"></span>'; 
				
				echo $out_html;
				
				break;			
			case 'pack_unlim_featured' :
			
				$out_html = '';
				$unlim_featured 	= get_post_meta( $post->ID, $prefix . 'pack_unlim_featured', true );
				$out_html = '<span class="fa fa-times"></span>'; 
				if ( $unlim_featured  == 'on') 
				$out_html = '<span class="fa fa-check-circle-o"></span>'; 
				
				echo $out_html;
				
				break;				
			case 'pack_agency_profile' :
			
				$out_html = '';
				$agency_profile 	= get_post_meta( $post->ID, $prefix . 'pack_agency_profile', true );
				$out_html = '<span class="fa fa-times"></span>'; 
				if ( $agency_profile  == 'on') 
				$out_html = '<span class="fa fa-check-circle-o"></span>'; 
				
				echo $out_html;
				break;				
			case 'pack_available' :
			
				$out_html = '';
				
				$visible 	= get_post_meta( $post->ID, $prefix . 'pack_visible', true );
				$out_html = '<span class="fa fa-times"></span>'; 
				if ( $visible  == 'on') 
				$out_html = '<span class="fa fa-check-circle-o"></span>'; 
				
				echo $out_html;
				
				break;			
				
			default :
				break;	
		}
	
	}
	
	public function zoner_packages_sortable_columns( $columns ) {
		$custom = array(
			'pack_price' => 'pack_price',
			'name'		 => 'title'
		);
		return wp_parse_args( $custom, $columns );
	}
	
	public function zoner_get_package_info_by_id($id_package = null) {
		global $prefix;
		$package_array_fields  = array();
		
		if (!empty($id_package) && ('publish' === get_post_status( $id_package ))) {
			$package_array_fields['id']		= $id_package;
			$package_array_fields['title']	= get_the_title($id_package);
			$package_array_fields['billing_period'] 	 = get_post_meta($id_package, $prefix . 'billing_period', true);
			$package_array_fields['billing_period_name'] = $this->get_membership_periods_name($package_array_fields['billing_period']);
			$package_array_fields['freq'] 	 			 = get_post_meta($id_package, $prefix . 'billing_period_freq', true);
			$package_array_fields['price'] 	 	 		 = get_post_meta($id_package, $prefix . 'pack_price', true);
			$package_array_fields['limit_properties'] 	 = (int) get_post_meta($id_package, $prefix . 'pack_limit_properties', true);
			$package_array_fields['limit_featured'] 	 = (int) get_post_meta($id_package, $prefix . 'pack_limit_featured', true);
			$package_array_fields['is_unlim_properties'] = get_post_meta($id_package, $prefix . 'pack_unlim_properties', true);
			$package_array_fields['is_unlim_featured'] 	 = get_post_meta($id_package, $prefix . 'pack_unlim_featured', true);
			$package_array_fields['is_available']   	 = get_post_meta($id_package, $prefix . 'pack_visible', true);
			$package_array_fields['is_create_agency'] 	 = get_post_meta($id_package, $prefix . 'pack_agency_profile', true);
			$package_array_fields['stripe_package_id'] 	 = get_post_meta($id_package, $prefix . 'stripe_pakcage_id', true);
			
			$package_array_fields = (object) $package_array_fields;	
		} 
		return $package_array_fields;
		
	}
	
	public function zoner_is_available_agency_for_curr_user() {
		global $zoner, $zoner_config, $prefix;
		$is_available = false;
		
		if (is_user_logged_in()) {
			
			$curr_user_id = get_current_user_id();
			$package_id   = get_user_meta($curr_user_id, $prefix.'package_id', true);
			
			if ($package_id) {
				$pkg_info = $this->zoner_get_package_info_by_id($package_id);
				if (!empty($pkg_info)) {
					if ($pkg_info->is_create_agency == 'on') {
						$is_available = true;
					}
				}
			} else {
				if (!empty($zoner_config['register-agency-account']) && ($zoner_config['register-agency-account'] == 1)) {
					$is_available = true;
				}
				
				if (!empty($zoner_config['paid-system']) && ($zoner_config['paid-system'] == 1)) {
					if (!empty($zoner_config['free-add-agency'])) {	
						$is_available = true;
					} else {
						$is_available = false;
					}
				}
			}
		}
		
		return $is_available;
	}
	
	public function zoner_get_invoice_info_by_id($id_invoice = null) {
		global $prefix;
		$invoice_array_fields  = '';
		
		if (!empty($id_invoice)) {
			
			$array_of_payment_details = $this->get_paid_details_of_payment_values();
			
			$invoice_array_fields['id']		= $id_invoice;
			$invoice_array_fields['invoice_id'] 	 	= get_post_meta($id_invoice, $prefix . 'invoice_transaction_id', true);
			$invoice_array_fields['title']				= get_the_title($id_invoice);
			$invoice_array_fields['payment_system'] 	= get_post_meta($id_invoice, $prefix . 'invoice_paymnent_system', true);
			$invoice_array_fields['detail_of_payment'] 	= get_post_meta($id_invoice, $prefix . 'invoice_detail_of_payment', true);
			$invoice_array_fields['detail_of_payment_name'] 	= $array_of_payment_details[$invoice_array_fields['detail_of_payment']];
			$invoice_array_fields['payment_recurring'] 	= get_post_meta($id_invoice, $prefix . 'invoice_payment_recurring', true);
			$invoice_array_fields['package_id'] 	 	= get_post_meta($id_invoice, $prefix . 'invoice_package_id', true);
			$invoice_array_fields['property_id'] 		= get_post_meta($id_invoice, $prefix . 'invoice_property_id', true);
			$invoice_array_fields['payment_price'] 	 	= get_post_meta($id_invoice, $prefix . 'invoice_payment_price', true);
			$invoice_array_fields['payment_currency'] 	= get_post_meta($id_invoice, $prefix . 'invoice_payment_currency', true);
			$invoice_array_fields['purchase_date'] 		= get_post_meta($id_invoice, $prefix . 'invoice_purchase_date', true);
			$invoice_array_fields['user_id'] 	 		= esc_attr(get_post_meta($id_invoice, $prefix . 'invoice_user_id', true));
			$invoice_array_fields['user_info'] 	 		= wp_kses_data(nl2br(get_post_meta($id_invoice, $prefix . 'invoice_user_info', true)));
		}
		
		return  (object) $invoice_array_fields;
		
	}
	
	public function zoner_get_all_packages($only_available = true) {
		global $zoner, $zoner_config, $wp_query, $prefix;
		
		$args_packages = $packages = array();
		if ($only_available) {
		
			$args_packages = array(
				'post_status' 		=> 'publish',
				'post_type'	  		=> array('packages'),
				'posts_per_pages'	=> -1,
				'meta_query'		=> array(
									array(
										'key'   	=> $prefix . 'pack_visible',
										'value' 	=> 'on',
										'compare'	=> '='
										)
									)	
				
			);
		} else {
			$args_packages = array(
				'post_status' 	=> 'publish',
				'post_type'	  	=> array('packages'),
				'posts_per_pages'	=> -1
			);
		}		
		
		
		$all_available_packages = get_posts($args_packages);
		if (!empty($all_available_packages)) {
			foreach($all_available_packages as $post) { 
				setup_postdata($post);
				$packages[$post->ID] = get_the_title($post->ID);
			}
		}
		wp_reset_postdata();
		return $packages;
	}
	
	public function zoner_is_user_limit_properties() {
		global $zoner, $zoner_config, $prefix, $wpdb;
		$is_user_property_not_limit = false;
		
		if ( is_user_logged_in() ) { 
			
			$curr_user_role = $zoner->zoner_get_current_user_role();
			$curr_user_id   = get_current_user_id();
			$package_id     = get_user_meta($curr_user_id, $prefix.'package_id', true);
			$valid_thru     = get_user_meta($curr_user_id, $prefix.'valid_thru', true);
			
			$pack_property_limit = -1;
			$user_property_limit = -1; 
			
			$full_list_user_property = array(
				'post_type' 	 => 'property',
				'post_status' 	 => 'any',
				'posts_per_page' => -1,
				'author' 		 => $curr_user_id
			);
			
			$property_found 	 = new WP_Query($full_list_user_property);
			$user_property_limit = (int) $property_found->found_posts;
			
			
			if ($package_id) {
				$package_info = $this->zoner_get_package_info_by_id($package_id);
				
				if ($package_info->is_unlim_properties == 'off') {
					$pack_property_limit = (int) $package_info->limit_properties;
				} else {
					$pack_property_limit = 0;
				}					
				
				if (!empty($valid_thru) && ($valid_thru >= current_time('mysql'))) {
					if ($pack_property_limit == 0) {
						$is_user_property_not_limit = true;	
					} else {
						if ($pack_property_limit > $user_property_limit)
							$is_user_property_not_limit = true;
					}
				} else {
					if ($pack_property_limit == 0) {
						$is_user_property_not_limit = true;	
					} else {
						if ($pack_property_limit > $user_property_limit)
							$is_user_property_not_limit = true;
					}
				}
			} else {
				if (isset($zoner_config['free-unlimited-properties']) && ($zoner_config['free-unlimited-properties'] == 0)) {
					$pack_property_limit = esc_attr((int) $zoner_config['free-limit-properties']);	
					
					if ($pack_property_limit > $user_property_limit)
						$is_user_property_not_limit = true;
				} else {
					$is_user_property_not_limit = true;
				}
			}
			
			if (esc_attr($zoner_config['paid-type-properties']) == 1) {
				$is_user_property_not_limit = true;
			}
		} 
		
		$is_paid_system = (!empty($zoner_config['paid-system']) && ($zoner_config['paid-system'] == 1));
		if (!$is_paid_system) $is_user_property_not_limit = true;
		
		wp_reset_query();
		return $is_user_property_not_limit;
	}	
	
	public function zoner_is_user_limit_featured_properties() {
		global $zoner, $zoner_config, $prefix, $wpdb;
		$is_user_featured_property_not_limit = false;
		
		if ( is_user_logged_in() ) { 
			$curr_user_role = $zoner->zoner_get_current_user_role();
			$curr_user_id   = get_current_user_id();
			$package_id     = get_user_meta($curr_user_id, $prefix.'package_id', true);
			$valid_thru     = get_user_meta($curr_user_id, $prefix.'valid_thru', true);
			
			$pack_property_featured_limit = -1;
			$user_property_featured_limit = -1;
			
			$full_list_featured = array(
					'post_type' 	 => 'property',
					'post_status' 	 => 'any',
					'posts_per_page' => -1,
					'author' 		 => $curr_user_id,
					'meta_query' 	 => array(
											array(
												'key'     => $prefix . 'is_featured',
												'value'   => 'on',
												'compare' => '=',
											),
					)						
				);
			
			$featured_found = new WP_Query($full_list_featured);
			$user_property_featured_limit = (int) $featured_found->found_posts;
			
			
			if ($package_id) {
				$package_info = $this->zoner_get_package_info_by_id($package_id);
				
				if ($package_info->is_unlim_featured == 'off') {
					$pack_property_limit = (int) $package_info->limit_featured;
				} else {	
					$pack_property_limit = 0;
				}
				
				
				if (!empty($valid_thru) && ($valid_thru >= current_time('mysql'))) {
					if ($pack_property_limit == 0) {
						$is_user_featured_property_not_limit = true;	
					} else {
						if ($pack_property_limit > $user_property_featured_limit)  {
							$is_user_featured_property_not_limit = true;
						}	
					}
				} else {
					if ($pack_property_limit == 0) {
						$is_user_featured_property_not_limit = true;	
					} else {
						if ($pack_property_limit > $user_property_featured_limit)  {
							$is_user_featured_property_not_limit = true;
						}	
					}
				}
				
			} else {
				
				if (isset($zoner_config['free-unlimited-featured']) && ($zoner_config['free-unlimited-featured'] == 0)) {
					$pack_property_featured_limit = esc_attr((int) $zoner_config['free-limit-featured']);
					
					if ($pack_property_featured_limit > $user_property_featured_limit)
					$is_user_featured_property_not_limit = true;
				} else {
					$is_user_featured_property_not_limit = true;
				}
				
			}
			
			if ($zoner_config['paid-type-properties'] == 1) {
				$is_user_featured_property_not_limit = true;
			}
		}
		
		$is_paid_system = (!empty($zoner_config['paid-system']) && ($zoner_config['paid-system'] == 1));
		if (!$is_paid_system)
		$is_user_featured_property_not_limit = true;
		
		wp_reset_query();
		return $is_user_featured_property_not_limit;	
	}
	
	
	
	public function zoner_get_package_info_by_user() {
		global $zoner, $zoner_config, $prefix, $wpdb;
		$user_package_info = array();
		
		if ( is_user_logged_in() ) { 
			$curr_user_role = $zoner->zoner_get_current_user_role();
			$curr_user_id   = get_current_user_id();
			$curr_package_id = -1;
			$current_user_package 	 = '';
			$user_valid_thru_package = null;
			
			$is_agency_available = false;
			
			$unlimited 	   = __('Unlimited', 'zoner');
			$not_available = __('Not Available', 'zoner'); 
			
			
			if (($curr_user_role == 'Agent') || ($curr_user_role == 'Administrator') || (current_user_can('edit_propertys', get_current_user_id()))) {
				 $args_count_all_properties = array(
					'post_type' 	 => 'property',
					'post_status'	 => 'any',
					'posts_per_page' => -1,
					'author' 		 => $curr_user_id
				);
				
				$args_count_all_featured = array(
					'post_type' 	 => 'property',
					'post_status'	 => 'any',
					'posts_per_page' => -1,
					'author' 		 => $curr_user_id,
					'meta_query' 	 => array(
											array(
												'key'     => $prefix . 'is_featured',
												'value'   => 'on',
												'compare' => '=',
											),
					)						
				);
				
				$property_found = new WP_Query($args_count_all_properties);
				$featured_found = new WP_Query($args_count_all_featured);
				
				if (!empty($zoner_config['free-package-name'])) {
					$current_user_package = esc_attr($zoner_config['free-package-name']);	 
				} else {		
					$current_user_package = __('Without a name', 'zoner');	
				}
				
				$package_limit_properties = $package_limit_featured_properties = -1;
				
				
				if (isset($zoner_config['free-unlimited-properties']) && ($zoner_config['free-unlimited-properties'] == 0)) {
					if (isset($zoner_config['free-limit-properties']))
					$package_limit_properties = esc_attr((int) $zoner_config['free-limit-properties']);	
				} 				
				
				if (isset($zoner_config['free-unlimited-featured']) && ($zoner_config['free-unlimited-featured'] == 0)) {
					if (isset($zoner_config['free-limit-featured']))
						$package_limit_featured_properties = esc_attr((int) $zoner_config['free-limit-featured']);
				}
				
				if (isset($zoner_config['free-add-agency']) && ($zoner_config['free-add-agency']))
					$is_agency_available = esc_attr($zoner_config['free-add-agency']);
			
				$package_id = get_user_meta($curr_user_id, $prefix.'package_id', true);
				
				
				if ($package_id) {
					$current_user_package = get_the_title($package_id);
					$curr_package_id      = $package_id;
					
					$user_valid_thru_package  = get_user_meta($curr_user_id, $prefix.'valid_thru', true); 
					
					$pack_info = $this->zoner_get_package_info_by_id($package_id);
					
					if (!empty($pack_info)) {
						$is_agency_available  = $pack_info->is_create_agency;
						
						if ($pack_info->is_unlim_properties != 'on') {
							if ((int) $pack_info->limit_properties > 0) {
								$package_limit_properties = esc_attr((int) $pack_info->limit_properties);	
							}
						} else {
							$package_limit_properties = 0;
						}							
					
						if ($pack_info->is_unlim_featured != 'on') {
							if ((int) $pack_info->limit_featured > 0) {
								$package_limit_featured_properties = esc_attr((int) $pack_info->limit_featured);
							}
						} else {
							$package_limit_featured_properties = 0;
						}						
					}	
				}
				
				$user_package_info['user_curr_package_id']    = (int) $curr_package_id;
				$user_package_info['usre_curr_package_name']  = $current_user_package;
				$user_package_info['user_property_remaining'] = (int) $property_found->found_posts;
				$user_package_info['user_featured_remaining'] = (int) $featured_found->found_posts;
				$user_package_info['user_valid_thru'] 		  = $user_valid_thru_package;
				
				$user_package_info['package_property_limit'] 		  = $package_limit_properties;
				$user_package_info['package_featured_property_limit'] = $package_limit_featured_properties;
				$user_package_info['package_available_agency'] 		  = $is_agency_available;
					
			}
			wp_reset_query();
			return  (object) $user_package_info;					
		}
	}
		
	public function zoner_get_user_info_panel() {
			$user_package_info = $this->zoner_get_package_info_by_user();
			$agency_icon = '<i class="fa fa-times-circle-o"></i>'; 
			
			if (!empty($user_package_info)) {
				
				
			$unlimited 	   = __('Unlimited', 'zoner');
			$not_available = __('Not Available', 'zoner'); 
			
			$featured_properties_limit =  $not_available;	
			$properties_limit 	= $not_available;	
			$valid_thru 		= $unlimited;
			
			if (($user_package_info->package_featured_property_limit >= 0) && isset($user_package_info->package_featured_property_limit)) {
				$featured_properties_limit =  sprintf(__('%1s of %2s', 'zoner'), $user_package_info->user_featured_remaining, $user_package_info->package_featured_property_limit);	
			} else {
				$featured_properties_limit =  sprintf(__('%1s of %2s', 'zoner'), $user_package_info->user_featured_remaining, $unlimited);	
			}				
			
			
			if (($user_package_info->package_property_limit >= 0) && isset($user_package_info->package_property_limit)) {
				$properties_limit = sprintf(__('%1s of %2s', 'zoner'), $user_package_info->user_property_remaining, $user_package_info->package_property_limit);
			} else {
				$properties_limit = sprintf(__('%1s of %2s', 'zoner'), $user_package_info->user_property_remaining, $unlimited);
			}			
			
			
			
			if (!empty($user_package_info->user_valid_thru)) {
				$valid_thru = $user_package_info->user_valid_thru;
			}
			
		?>
			
			<div class="panel panel-info">
				<div class="panel-heading">
					<h3 class="panel-title"><?php _e('Your Current Package', 'zoner');  ?> : <strong><?php echo esc_attr($user_package_info->usre_curr_package_name); ?></strong></h3>
				</div>
				<ul class="list-group">
					<li class="list-group-item"><?php _e('Property limit', 'zoner'); ?> : <strong><?php  echo $properties_limit; ?></strong></li>
					<li class="list-group-item"><?php _e('Featured property limit', 'zoner'); ?> : <strong><?php echo $featured_properties_limit; ?></strong></li>
					<li class="list-group-item"><?php _e('Valid thru', 'zoner'); ?> : <strong><?php echo $valid_thru; ?></strong></li>
					<?php 
						if ($user_package_info->package_available_agency == 'on') 
							$agency_icon = '<i class="fa fa-check-circle-o"></i>';
							
					 ?>
					
					<li class="list-group-item"><?php _e('Agency profile', 'zoner'); ?> : <?php echo $agency_icon; ?></li>
				</ul>
			</div>						
			
		<?php		
		
			}
	}
	
	
	
	/*Ajax calls*/
	public function zoner_get_total_price() {
		global $zoner_config, $zoner;
		
		$price_text = '';
		if (isset($_POST) && ($_POST['action'] == 'get_total_price')) {
			
			$price_per_property = (int) esc_attr($zoner_config['price-per-property']);
			$price_per_featured = (int) esc_attr($zoner_config['price-per-featured-property']);
			$currency = esc_attr($zoner_config['paid-currency']);
			
			if ($_POST['check'] == '1') {
				$price_text	= $zoner->currency->get_zoner_property_price(($price_per_property + $price_per_featured), $currency, 0, null, null, false);
			} else {
				$price_text	= $zoner->currency->get_zoner_property_price($price_per_property, $currency, 0, null, null, false);
			}
		}	
		
		echo esc_js($price_text); 
		die();
	}	
	
	/*Payments functions*/
	
	/*Per property*/
	public function zoner_paypal_payment_process($orderParams = array(), $item = array()) {
		global $zoner, $zoner_config, $prefix, $current_user;
		
		require_once ("payments/paypalfunctions.php");
		$resArray = $array_code_error = array();
		
		$paymentType   		= "Sale";
		$SandboxFlag 		= 'sandbox';
		$PaidTypeProperties = 0;
		$sBNCode 			= __('Paid system', 'zoner') . ' ' . get_bloginfo('name');
		
		$API_UserName  = $zoner_config['paypal-api-username'];
		$API_Password  = $zoner_config['paypal-api-password'];
		$API_Signature = $zoner_config['paypal-api-signature'];
		
		if (!empty($zoner_config['paid-api-method']))
		$SandboxFlag = esc_attr($zoner_config['paid-api-method']);
		
		if (!empty($zoner_config['paid-type-properties'])) 
			$PaidTypeProperties = esc_attr((int) $zoner_config['paid-type-properties']);
		
		if ($PaidTypeProperties == 0) {
			$returnURL = add_query_arg(array('profile-page' => 'my_package'),    get_author_posts_url($current_user->ID));
			$cancelURL = add_query_arg(array('profile-page' => 'my_package'),    get_author_posts_url($current_user->ID));
		} else {
			$returnURL = add_query_arg(array('profile-page' => 'my_properties'), get_author_posts_url($current_user->ID));
			$cancelURL = add_query_arg(array('profile-page' => 'my_properties'), get_author_posts_url($current_user->ID));
		}		
		
		$requestParams = array(
			'RETURNURL' => esc_url($returnURL),
			'CANCELURL' => esc_url($cancelURL)
		);
			

		$paypal_obj = new zoner_paypal($SandboxFlag, $sBNCode, $API_UserName, $API_Password, $API_Signature);
		$resArray 	= $paypal_obj->request('SetExpressCheckout', $requestParams + $orderParams + $item);
		
		$ack = strtoupper($resArray["ACK"]);
		if(($ack == "SUCCESS") || ($ack == "SUCCESSWITHWARNING")) {
			$redirectURL =  $paypal_obj->RedirectToPayPalUrl($resArray["TOKEN"]);
		} else {
			
		$ErrorCode 			= urldecode($resArray["L_ERRORCODE0"]);
		$ErrorShortMsg 		= urldecode($resArray["L_SHORTMESSAGE0"]);
		$ErrorLongMsg 		= urldecode($resArray["L_LONGMESSAGE0"]);
		$ErrorSeverityCode 	= urldecode($resArray["L_SEVERITYCODE0"]);
	
		$array_code_error[] = __("SetExpressCheckout API call failed. ", 'zoner');
		$array_code_error[] = sprintf(__("Detailed Error Message: %1s", 'zoner'), $ErrorLongMsg);
		$array_code_error[] = sprintf(__("Short Error Message: %1s", 'zoner'), $ErrorShortMsg);
		$array_code_error[] = sprintf(__("Error Code: %1s", 'zoner'), $ErrorCode);
		$array_code_error[] = sprintf(__("Error Severity Code: ", 'zoner'),  $ErrorSeverityCode);
		}
		
		if (!empty($array_code_error)) {
			return $array_code_error;
		} else {
			return $redirectURL;
		}
	}	
	
	public function zoner_paypal_paid_per_property() {
		global $zoner, $zoner_config, $prefix;
		if (isset($_POST) && ($_POST['action'] == 'zoner_paypal_paid_per_property')) {
	
			$property_id = $_POST['property_id'];
			$is_featured = $_POST['is_featured'];
			$is_upgrade  = $_POST['is_upgrade'];
			
			$price_per_property = 0;
			$price_per_featured_property = 0;
			$code_payment = 1;
			
			$currencyCodeType 	= 'USD';
			
			if (!empty($zoner_config['paid-currency']))
			$currencyCodeType = esc_attr($zoner_config['paid-currency']);
			
			if (!empty($zoner_config['price-per-property']))
			$price_per_property = esc_attr((int) $zoner_config['price-per-property']);
			
			if (!empty($zoner_config['price-per-featured-property']))
			$price_per_featured_property = esc_attr((int) $zoner_config['price-per-featured-property']);
			
			$total_price = $price_per_property;
			
			if ($is_featured) {
				$code_payment = 2;
				$total_price  = $price_per_property + $price_per_featured_property;
			} else {
				if ($is_upgrade) {
					$code_payment = 3;
					$total_price  = $price_per_featured_property;
				} 
			}
							
			$payment_name = $this->get_paid_details_of_payment_values();
			$payment_name = $payment_name[$code_payment];
			
			$orderParams = array(
				'PAYMENTREQUEST_0_AMT' 			=> $total_price,
				'PAYMENTREQUEST_0_CURRENCYCODE' => $currencyCodeType,
				'PAYMENTREQUEST_0_ITEMAMT' 		=> $total_price
			);
			
			$item = array(
				'L_PAYMENTREQUEST_0_NAME0' 	=> get_bloginfo('name'),
				'L_PAYMENTREQUEST_0_DESC0' 	=> $payment_name,
				'L_PAYMENTREQUEST_0_AMT0' 	=> $total_price,
				'L_PAYMENTREQUEST_0_QTY0'	=> 1
			);
		
			$res = $this->zoner_paypal_payment_process($orderParams, $item);
		
			if (is_array($res)) {
				echo json_encode($res);
			} else {
				
				$array_of_saved_data = array(
					'property_id'	=> $property_id,
					'is_featured' 	=> $is_featured,
					'is_upgrade'	=> $is_upgrade,
					'total_price' 	=> $total_price,
					'currency_code' => $currencyCodeType,
					'payment_code'  => $code_payment
				);
				
				 update_option("user_temporary_data_" . get_current_user_id(), $array_of_saved_data); 
			
				echo $res;
			}
		}
		
		die();
	}
	
	/*Per Package*/
	public function zoner_paypal_pkg_payment_process($orderParams = array(), $item = array()) {
		global $zoner, $zoner_config, $prefix, $current_user;
		
		require_once ("payments/paypalfunctions.php");
		$resArray = $array_code_error = array();
		
		$paymentType   		= "Sale";
		$SandboxFlag 		= 'sandbox';
		$PaidTypeProperties = 0;
		$sBNCode 			= __('Paid system', 'zoner') . ' ' . get_bloginfo('name');
		
		$API_UserName  = $zoner_config['paypal-api-username'];
		$API_Password  = $zoner_config['paypal-api-password'];
		$API_Signature = $zoner_config['paypal-api-signature'];
		
		if (!empty($zoner_config['paid-api-method']))
		$SandboxFlag = esc_attr($zoner_config['paid-api-method']);
		
		if (!empty($zoner_config['paid-type-properties'])) 
			$PaidTypeProperties = esc_attr((int) $zoner_config['paid-type-properties']);
		
		if ($PaidTypeProperties == 0) {
			$returnURL = add_query_arg(array('profile-page' => 'my_package'),    get_author_posts_url($current_user->ID));
			$cancelURL = add_query_arg(array('profile-page' => 'my_package'),    get_author_posts_url($current_user->ID));
		} else {
			$returnURL = add_query_arg(array('profile-page' => 'my_properties'), get_author_posts_url($current_user->ID));
			$cancelURL = add_query_arg(array('profile-page' => 'my_properties'), get_author_posts_url($current_user->ID));
		}		
		
		$requestParams = array(
			'RETURNURL' => esc_url($returnURL),
			'CANCELURL' => esc_url($cancelURL)
		);
			

		$paypal_obj = new zoner_paypal($SandboxFlag, $sBNCode, $API_UserName, $API_Password, $API_Signature);
		$resArray 	= $paypal_obj->request('SetExpressCheckout', $requestParams + $orderParams + $item);
		
		$ack = strtoupper($resArray["ACK"]);
		if(($ack == "SUCCESS") || ($ack == "SUCCESSWITHWARNING")) {
			$redirectURL =  $paypal_obj->RedirectToPayPalUrl($resArray["TOKEN"]);
		} else {
			
		$ErrorCode 			= urldecode($resArray["L_ERRORCODE0"]);
		$ErrorShortMsg 		= urldecode($resArray["L_SHORTMESSAGE0"]);
		$ErrorLongMsg 		= urldecode($resArray["L_LONGMESSAGE0"]);
		$ErrorSeverityCode 	= urldecode($resArray["L_SEVERITYCODE0"]);
	
		$array_code_error[] = __("SetExpressCheckout API call failed. ", 'zoner');
		$array_code_error[] = sprintf(__("Detailed Error Message: %1s", 'zoner'), $ErrorLongMsg);
		$array_code_error[] = sprintf(__("Short Error Message: %1s", 'zoner'), $ErrorShortMsg);
		$array_code_error[] = sprintf(__("Error Code: %1s", 'zoner'), $ErrorCode);
		$array_code_error[] = sprintf(__("Error Severity Code: ", 'zoner'),  $ErrorSeverityCode);
		}
		
		if (!empty($array_code_error)) {
			return $array_code_error;
		} else {
			return $redirectURL;
		}
	}
	public function zoner_bacs_payment($packageID = null, $recurring = null, $property_id = null, $is_featured= null, $is_upgrade= null){
		global $zoner , $zoner_config;
		$current_user = wp_get_current_user();
		if (!empty($packageID)){ //package style
			$zoner_info_pkg = $this->zoner_get_package_info_by_id($packageID);
			$code_payment = 4;
			$pkg_name = $zoner_info_pkg->title;
			$price	  = $zoner_info_pkg->price;
		}
		if (!empty($property_id)){//by one property style
			$pkg_name = __('Property','zoner');

			$price_per_property = 0;
			$price_per_featured_property = 0;
			$code_payment = 1;

			$currencyCodeType 	= 'USD';

			if (!empty($zoner_config['paid-currency']))
				$currencyCodeType = esc_attr($zoner_config['paid-currency']);

			if (!empty($zoner_config['price-per-property']))
				$price_per_property = esc_attr((int) $zoner_config['price-per-property']);

			if (!empty($zoner_config['price-per-featured-property']))
				$price_per_featured_property = esc_attr((int) $zoner_config['price-per-featured-property']);

			$price = $price_per_property;

			if ($is_featured) {
				$code_payment = 2;
				$price  = $price_per_property + $price_per_featured_property;
			} else {
				if ($is_upgrade) {
					$code_payment = 3;
					$price  = $price_per_featured_property;
				}
			}
		}
		$payment_name = $this->get_paid_details_of_payment_values();
		$payment_name = $payment_name[$code_payment];
		$currency = 'USD';
		if (!empty($zoner_config['paid-currency']))
			$currency = esc_attr($zoner_config['paid-currency']);
		$user_info = null;
		$wp_invoice_id = $this->zoner_create_invoice_row(
							time(),
							'BACS',
							$code_payment,
							$recurring,
							$property_id,
							$packageID,
							$price,
							$currency,
							$user_info
						);
		//to custumer
		$zoner->emails->zoner_mail_to_user_bacs_payment_data($pkg_name, $price.$currency, $current_user->user_email,zoner_get_user_name($current_user));

	}

	public function zoner_new_bacs_invoices_approve($new_status, $old_status, $post){
		global $prefix, $spotter;
		$is_BACS = get_post_meta( $post->ID, $prefix . 'invoice_paymnent_system', true ) == 'BACS';
		$property_id = get_post_meta( $post->ID, $prefix . 'invoice_property_id', true );
		$packageID = get_post_meta( $post->ID, $prefix . 'invoice_package_id', true );
		if ($is_BACS && $new_status == 'publish' && $old_status != 'publish' && !empty($packageID)) {
			//change user package
			$package_info = $this->zoner_get_package_info_by_id($packageID);
			if (!empty($package_info)) {
				$pkg_billing_period = $package_info->billing_period;
				$pkg_period_freq = (int)$package_info->freq;
				$this->zoner_update_user_plans_info(
					$packageID,
					$this->zoner_get_expiry_date($pkg_billing_period, $pkg_period_freq),
					'on'
				);
			}
		}
			if ($is_BACS && $new_status == 'publish' && $old_status != 'publish' && !empty($property_id)){
				$payment_code = get_post_meta( $post->ID, $prefix . 'invoice_detail_of_payment', true );
				$this->zoner_update_property_payment_status($property_id, $payment_code);

		}
		
	}
	public function zoner_bacs_paid_per_package() {
		global $zoner, $zoner_config, $prefix;
		if (isset($_POST) && ($_POST['action'] == 'zoner_bacs_paid_per_package') && !empty($_POST['package_id'])) {
			
			$currencyCodeType 	= 'USD';
			$packageID 			= esc_attr((int) $_POST['package_id']);
			$recurring 			= (bool) $_POST['recurring'];
			
		
			$this->zoner_bacs_payment($packageID, $recurring);
			$res = array(0, __('Check your mail', 'zoner'));
			echo json_encode($res);
		} else {
			if (!isset($_POST['package_id'])) {
				$out_array_payment_data = array (0, __('Please choose a package', 'zoner'));
			} else {
				$out_array_payment_data = array (0, __('Undefined action', 'zoner'));
			}			
			
			echo json_encode($out_array_payment_data);
		}	
		die();
	}

	public function zoner_bacs_paid_per_property() {
		global $zoner, $zoner_config, $prefix;
		if (isset($_POST) && ($_POST['action'] == 'zoner_bacs_paid_per_property') && !empty($_POST['property_id'])) {
			$res = array();
			$property_id 		= $_POST['property_id'];
			$is_featured 		= (bool) $_POST['is_featured'];
			$is_upgrade		= (bool) $_POST['is_upgrade'];

			$this->zoner_bacs_payment(null, null, $property_id, $is_featured, $is_upgrade);
			$res = array(0, __('Check your mail', 'zoner'));
			echo json_encode($res);
		} else {
				$out_array_payment_data = array (0, __('Undefined action', 'zoner'));
				echo json_encode($out_array_payment_data);
			}
		die();
	}

	public function zoner_paypal_paid_per_package() {
		global $zoner, $zoner_config, $prefix;
		if (isset($_POST) && ($_POST['action'] == 'zoner_paypal_paid_per_package') && !empty($_POST['package_id'])) {
			
			$code_payment = 4;
			$currencyCodeType 	= 'USD';
			$packageID 			= esc_attr((int) $_POST['package_id']);
			$recurring 			= (bool) $_POST['recurring'];
			
			if (!empty($zoner_config['paid-currency']))
			$currencyCodeType = esc_attr($zoner_config['paid-currency']);
		
			$zoner_info_pkg = $this->zoner_get_package_info_by_id($packageID);
			
			if (!empty($zoner_info_pkg)) {
				$pkg_name = $zoner_info_pkg->title;	
				$pkg_billing_period 	 = $zoner_info_pkg->billing_period;
				$pkg_billing_period_name = $zoner_info_pkg->billing_period_name;
				$pkg_period_freq		 = $zoner_info_pkg->freq;
				$pkg_price				 = $zoner_info_pkg->price;
				
				$payment_name = $this->get_paid_details_of_payment_values();
				$payment_name = $payment_name[$code_payment];
				
				if ($recurring) {
					$orderParams = array(
						'PAYMENTREQUEST_0_AMT' 			=> $pkg_price,
						'PAYMENTREQUEST_0_CURRENCYCODE' => $currencyCodeType
					);
			
					$item = array(
						'L_BILLINGTYPE0' 					=> 'RecurringPayments',
						'L_BILLINGAGREEMENTDESCRIPTION0' 	=> sprintf(__('%1s package purchase on %2s','zoner'), $pkg_name, get_bloginfo('name')),
						'L_PAYMENTTYPE0' 					=> 'Any',
						'L_CUSTOM0'							=> '',
					);
				} else {
					
					$orderParams = array(
						'PAYMENTREQUEST_0_AMT' 			=> $pkg_price,
						'PAYMENTREQUEST_0_CURRENCYCODE' => $currencyCodeType,
						'PAYMENTREQUEST_0_ITEMAMT' 		=> $pkg_price
					);
			
					$item = array(
						'L_PAYMENTREQUEST_0_NAME0' 	=> get_bloginfo('name'),
						'L_PAYMENTREQUEST_0_DESC0' 	=> sprintf(__('%1s package purchase on %2s','zoner'), $pkg_name, get_bloginfo('name')),
						'L_PAYMENTREQUEST_0_AMT0' 	=> $pkg_price,
						'L_PAYMENTREQUEST_0_QTY0'	=> 1
					);	

				}				
			
				$res = $this->zoner_paypal_pkg_payment_process($orderParams, $item);
				
				if (is_array($res)) {
					echo json_encode($res);
				} else {
				
					$array_of_saved_data = array(
						'package_id' 	=> $packageID,
						'recurring'	 	=> $recurring,
						'currency_code' => $currencyCodeType
					);
				
					update_option("user_temporary_data_" . get_current_user_id(), $array_of_saved_data); 
			
					echo $res;
				}
			}
		} else {
			if (!isset($_POST['package_id'])) {
				$out_array_payment_data = array (0, __('Please choose a package', 'zoner'));
			} else {
				$out_array_payment_data = array (0, __('Undefined action', 'zoner'));
			}			
			
			echo json_encode($out_array_payment_data);
		}	
		die();
	}
	
	public function zoner_paypal_paid_completed() {
		global $zoner, $zoner_config, $prefix, $current_user;
		
		if( isset($_GET['token']) && !empty($_GET['token']) && !empty($_GET['PayerID']) && is_user_logged_in()) { 
			
			require_once ("payments/paypalfunctions.php");
		
			$paymentType   		= "Sale";
			$SandboxFlag 		= 'sandbox';
			$PaidTypeProperties = 0;
			$sBNCode 			= __('Paid system', 'zoner') . ' ' . get_bloginfo('name');
		
			$API_UserName  = $zoner_config['paypal-api-username'];
			$API_Password  = $zoner_config['paypal-api-password'];
			$API_Signature = $zoner_config['paypal-api-signature'];
		
			if (!empty($zoner_config['paid-api-method']))
			$SandboxFlag = esc_attr($zoner_config['paid-api-method']);
		
			if (!empty($zoner_config['paid-type-properties'])) 
			$PaidTypeProperties = esc_attr((int) $zoner_config['paid-type-properties']);
			
			if ($PaidTypeProperties == 0) {
				$returnURL = add_query_arg(array('profile-page' => 'my_package'),    get_author_posts_url($current_user->ID));
				$cancelURL = add_query_arg(array('profile-page' => 'my_package'),    get_author_posts_url($current_user->ID));
			} else {
				$returnURL = add_query_arg(array('profile-page' => 'my_properties'), get_author_posts_url($current_user->ID));
				$cancelURL = add_query_arg(array('profile-page' => 'my_properties'), get_author_posts_url($current_user->ID));
			}		
		
			$paypal_obj = new zoner_paypal($SandboxFlag, $sBNCode, $API_UserName, $API_Password, $API_Signature);
			$checkoutDetails = $paypal_obj->request('GetExpressCheckoutDetails', array('TOKEN' => $_GET['token']));
			
			if (is_array($checkoutDetails) && ($checkoutDetails['PAYMENTREQUESTINFO_0_ERRORCODE'] == 0)) {
				$requestParams = array (
					'PAYMENTACTION' => $paymentType,
					'PAYERID' 		=> $checkoutDetails['PAYERID'], 
					'TOKEN'	  		=> $checkoutDetails['TOKEN'],
					'CURRENCYCODE'	=> $checkoutDetails['CURRENCYCODE'],
					'AMT'			=> $checkoutDetails['AMT'],
				);
				
				$response = $paypal_obj->request('DoExpressCheckoutPayment', $requestParams);
				
				if( is_array($response) && $response['ACK'] == 'Success') { 
					$transactionId = $response['PAYMENTINFO_0_TRANSACTIONID'];
			   	    $save_payment_user_data = get_option('user_temporary_data_' . get_current_user_id());
					
					if (!empty($save_payment_user_data)) {
						if (array_key_exists('property_id', $save_payment_user_data))  {
							$payment_code  		= (int) $save_payment_user_data['payment_code'];
							$payment_recurring 	= 0;
							$package_id			= 0;
							$property_id 		= (int) $save_payment_user_data['property_id'];
							$price				= $checkoutDetails['AMT'];
							$currency			= $checkoutDetails['CURRENCYCODE'];
							$user_info          = null;
					
							$this->zoner_create_invoice_row(
								$transactionId,
								'PayPal',
								$payment_code, 
								$payment_recurring, 
								$property_id, 
								$package_id, 
								$price, 
								$currency, 
								$user_info
							);
						 
							if ($property_id != 0)
							$this->zoner_update_property_payment_status($property_id, $payment_code);
					
							delete_option('user_temporary_data_' . get_current_user_id());
						
							wp_safe_redirect($returnURL);
							exit;
							
						} else {
							
							/*Package Payments*/
							$package_id = (int) $save_payment_user_data['package_id'];
							$recuring   = $save_payment_user_data['recurring'];
							$package_info = $this->zoner_get_package_info_by_id($package_id);
							
							if (!empty($package_info)) {
								$pkg_name 				 = $package_info->title;	
								$pkg_billing_period 	 = $package_info->billing_period;
								$pkg_period_freq		 = (int) $package_info->freq;
								$pkg_price				 = $package_info->price;
								
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
								$packageID			= $package_id;
								$property_id 		= 0;
								$price				= $checkoutDetails['AMT'];
								$currency			= $checkoutDetails['CURRENCYCODE'];
								$user_info          = null;
							
								if ($recuring) {
									$payment_recurring 	= 'on';	
									$start_date =  strtotime(date('Y-m-d H:i:s', current_time( 'timestamp', 0 )));
									$start_date =  date('Y-m-d H:i:s', $start_date); 
									
									
									$recuringAccountArgs = array (
											'TOKEN'	  			=> $checkoutDetails['TOKEN'],
											'CURRENCYCODE'		=> $checkoutDetails['CURRENCYCODE'],
											'AMT'				=> $checkoutDetails['AMT'],
											'PROFILESTARTDATE' 	=> $start_date,
											'BILLINGPERIOD'	   	=> $pp_period,
											'BILLINGFREQUENCY' 	=> $pkg_period_freq,
											'DESC'				=> sprintf(__('%1s package purchase on %2s','zoner'), get_the_title($packageID), get_bloginfo('name')),
											'MAXFAILEDPAYMENTS' => 1,
											'AUTOBILLAMT'		=> 'AddToNextBilling',
											'SUBSCRIBERNAME'	=> get_bloginfo('name')
									);
				
									$recuringAccount = $paypal_obj->request('CreateRecurringPaymentsProfile', $recuringAccountArgs);
									
									if("SUCCESS" == strtoupper($recuringAccount["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($recuringAccount["ACK"])) {
										
										$profile_user_id = $recuringAccount["PROFILEID"];	
										$curr_user_id = get_current_user_id();
										
										$profile_id	=  str_replace('-',   'xxx', $profile_user_id);
										$profile_id	=  str_replace('%2d', 'xxx', $profile_id);
  
										update_user_meta($curr_user_id, $prefix. 'paypal_customer_id', $profile_id);  
	  
									}
									
								} else {
									$payment_recurring 	= 'off';	
								}
								
								$this->zoner_create_invoice_row(
										$transactionId,
										'PayPal',
										$payment_code, 
										$payment_recurring, 
										$property_id, 
										$packageID, 
										$price, 
										$currency, 
										$user_info
									);
								$this->zoner_update_user_plans_info	(
									$packageID,  
									$this->zoner_get_expiry_date($pkg_billing_period, $pkg_period_freq), 
									'on'
								);
								
								delete_option('user_temporary_data_' . get_current_user_id());
								wp_safe_redirect($returnURL);
								exit;
							}
							
						} /*package payments*/
					}	
				}
			}
		}
	}	
	
	/*Stripe payment process*/
	public function zoner_get_stripe_btn_per_property ($property_id, $class, $btnTitle = null) {
		global $zoner, $zoner_config, $prefix;
			$out_form = '';
			
			$sBNCode  = __('Paid system', 'zoner') . ' ' . get_bloginfo('name');
			$code_payment = 1;
			$logo_url 	  = '';
			$currencyCodeType 	= 'USD';
			
			if (!empty($zoner_config['logo']))
			$logo_url  = $zoner_config['logo'];
			
			$stripeSK  = $zoner_config['stripe-secret-key'];
			$stripePK  = $zoner_config['stripe-publishable-key'];
			
		
			if (!empty($zoner_config['paid-currency']))
			$currencyCodeType = esc_attr($zoner_config['paid-currency']);
			if (!empty($zoner_config['price-per-property']))
			$price_per_property = esc_attr((int) $zoner_config['price-per-property']) * 100;
		
			if (!empty($zoner_config['price-per-featured-property']))
			$price_per_featured_property = esc_attr((int) $zoner_config['price-per-featured-property'])*100;
		
			
			$payment_name = $this->get_paid_details_of_payment_values();
			$payment_name = $payment_name[$code_payment];
			
			$current_user = wp_get_current_user();
			$out_button = '<button type="button" 
								data-propertyid="'.$property_id.'" 
								data-key="'.esc_js($stripePK).'"
								data-propertyprice="' .esc_js($price_per_property).'"
								data-featuredprice="' .esc_js($price_per_featured_property).'"
								data-name="'	.esc_js($sBNCode).'"
								data-email="' 	.$current_user->user_email.'"
								data-description="'.esc_js($payment_name).'"
								data-currency="'   .esc_js($currencyCodeType).'"
								data-image="'	   .esc_url($logo_url['url']).'"
								class="'.$class.'">'.$btnTitle.'</button>';
			
			return $out_button;
	}
	
	
	public function zoner_get_stripe_payment_data() {
		global $zoner, $zoner_config, $prefix, $current_user;
		
		
		if (isset($_POST) && ($_POST['action'] == 'zoner_get_stripe_payment_data'))	{
			
			$out_array = array();
			$property_id = $_POST['property_id'];
			$is_featured = $_POST['is_featured'];
			$is_upgrade  = $_POST['is_upgrade'];
			
			
			$price_per_property = 0;
			$price_per_featured_property = 0;
			$code_payment = 1;
			
			$currencyCodeType 	= 'USD';
			if (!empty($zoner_config['paid-currency']))
			$currencyCodeType = esc_attr($zoner_config['paid-currency']);
			
			if (!empty($zoner_config['price-per-property']))
			$price_per_property = esc_attr((int) $zoner_config['price-per-property']);
			
			if (!empty($zoner_config['price-per-featured-property']))
			$price_per_featured_property = esc_attr((int) $zoner_config['price-per-featured-property']);
			
			$total_price = $price_per_property;
			
			if ($is_featured) {
				$code_payment = 2;
				$total_price  = $price_per_property + $price_per_featured_property;
			} else {
				if ($is_upgrade) {
					$code_payment = 3;
					$total_price  = $price_per_featured_property;
				} 
			}
							
			$total_price = $total_price*100;
			
			$payment_name = $this->get_paid_details_of_payment_values();
			$payment_name = $payment_name[$code_payment];
			
			
			$out_array  = array( $payment_name, $total_price );
			
			echo json_encode($out_array);
		}
		
		die();
	}
	
	public function zoner_stripe_paid_per_property_completed() {
		global $zoner, $zoner_config, $prefix, $current_user;
		 
		 if (isset($_POST) && !empty($_POST['tokenID']) && ($_POST['action'] == 'zoner_complete_stripe_payment')) {
			
			$stripeSK  = $zoner_config['stripe-secret-key'];
			$stripePK  = $zoner_config['stripe-publishable-key'];
			
			require_once ("payments/stripe/stripe.php");
			
			Stripe::setApiKey($stripeSK);
			$code_payment = 1;
			$currencyCodeType 	= 'USD';
			$total_price		= 0;
			
			$tokenID	  = $_POST['tokenID'];
			$property_id  = $_POST['property_id'];
			$is_featured  = $_POST['is_featured'];
			$is_upgrade   = $_POST['is_upgrade'];
			
			if (!empty($zoner_config['paid-currency']))
			$currencyCodeType = esc_attr($zoner_config['paid-currency']);
			
			
			if (!empty($zoner_config['price-per-property']))
			$price_per_property = esc_attr((int) $zoner_config['price-per-property']);
			
			if (!empty($zoner_config['price-per-featured-property']))
			$price_per_featured_property = esc_attr((int) $zoner_config['price-per-featured-property']);
			
			$total_price = $price_per_property;
			
			if ($is_featured) {
				$code_payment = 2;
				$total_price  = $price_per_property + $price_per_featured_property;
			} else {
				if ($is_upgrade) {
					$code_payment = 3;
					$total_price  = $price_per_featured_property;
				} 
			}
							
			$total_price = $total_price*100;
			
			$payment_name = $this->get_paid_details_of_payment_values();
			$payment_name = $payment_name[$code_payment];
			
			
			$current_user = wp_get_current_user();
			$customer = Stripe_Customer::create(array(
				'email' => $current_user->user_email,
				'card'  => $tokenID
			));
			
			
			try {
				$charge = Stripe_Charge::create(array(
					'customer' => $customer->id,
					'amount'   => $total_price,
					'currency' => $currencyCodeType
				));
				
				if ($charge->paid == true) {
					$payment_code  		= (int) $code_payment;
					$payment_recurring 	= 0;
					$package_id			= 0;
					$property_id 		= (int) $property_id;
					$price				= $total_price/100;
					$currency			= $currencyCodeType;
					$user_info          = null;
						
					$this->zoner_create_invoice_row(
								$charge->id,
								'Stripe',
								$payment_code, 
								$payment_recurring, 
								$property_id, 
								$package_id, 
								$price, 
								$currency, 
								$user_info
					);
					$this->zoner_update_property_payment_status($property_id, $payment_code);
					
					echo json_encode(array('1', __('Payment success', 'zoner')));
				} else {
					echo json_encode(array('0', __('Payment Error', 'zoner')));
				}
			
			} catch (Stripe_CardError $e) {
				echo json_encode(array('0', $e));
			}
		 }
		 die();
	}
	
	
	
	/*Package Payments*/
	public function zoner_get_stripe_package_payment_data() {
		global $zoner, $zoner_config, $prefix, $current_user;
			   $out_array_payment_data = array();
		
		if (isset($_POST) && ($_POST['action'] == 'zoner_get_stripe_package_payment_data') && (isset($_POST['package_id']))) {
			$stripeSK  = $zoner_config['stripe-secret-key'];
			$stripePK  = $zoner_config['stripe-publishable-key'];
			
			$packageID = esc_attr((int) $_POST['package_id']);
			$recurring  = esc_attr($_POST['recurring']);
			
			$currencyCodeType 	= 'USD';
			if (!empty($zoner_config['paid-currency']))
			$currencyCodeType = esc_attr($zoner_config['paid-currency']);
			
			$package_info = $this->zoner_get_package_info_by_id($packageID);
			$logo_url	  = '';
								
			if (!empty($package_info)) {
				if (!empty($zoner_config['logo']))
				$logo_url  = $zoner_config['logo'];
		
				$current_user = wp_get_current_user();
				$sBNCode      = sprintf(__('%1s Package Payment on %2s', 'zoner'), $package_info->title, get_bloginfo('name'));
				
				$billing_period 	= $package_info->billing_period;
				$freq				= $package_info->freq;
				$price				= $package_info->price * 100;
				$stripe_package_id 	= $package_info->stripe_package_id;

				$out_array_payment_data = array (	1,
													$stripePK,
													$packageID,
													$stripe_package_id,
													esc_js($currencyCodeType),
													esc_js($sBNCode),
													$price,
													$current_user->user_email,
													esc_js(sprintf(__('Buy %1s package', 'zoner'), $package_info->title)),
													esc_url($logo_url['url']),
													$recurring
												);
				if (empty($stripe_package_id))								
				$out_array_payment_data = array (0, __('Stripe package undefined', 'zoner'));					
													
			} else {
				$out_array_payment_data = array (0, __('Information on the package is incomplete', 'zoner'));
			}
		} else {
			if (!isset($_POST['package_id'])) {
				$out_array_payment_data = array (0, __('Please choose a package', 'zoner'));
			} else {
				$out_array_payment_data = array (0, __('Undefined action', 'zoner'));
			}			
		}		
		
		echo json_encode($out_array_payment_data);
		die();
	}

	
	public function zoner_complete_stripe_package_payment() {
		global $zoner, $zoner_config, $prefix, $current_user;
		 
		 if (isset($_POST) && !empty($_POST['tokenID']) && ($_POST['action'] == 'zoner_complete_stripe_package_payment')) {
			
			$stripeSK  = $zoner_config['stripe-secret-key'];
			$stripePK  = $zoner_config['stripe-publishable-key'];
			
			require_once ("payments/stripe/stripe.php");
			Stripe::setApiKey($stripeSK);
			
			$code_payment = 1;
			$currencyCodeType 	= 'USD';
			$total_price		= 0;
			
			$tokenID	  = $_POST['tokenID'];
			$packageID    = (int) $_POST['packageID'];
			$isrecurring   = $_POST['recurring'];
			
			
			if (!empty($zoner_config['paid-currency']))
			$currencyCodeType = esc_attr($zoner_config['paid-currency']);
			
			$current_user = wp_get_current_user();
			$package_info = $this->zoner_get_package_info_by_id($packageID);
			
			if (!empty($package_info)) {
				$sBNCode      = sprintf(__('%1s Package Payment on %2s', 'zoner'), $package_info->title, get_bloginfo('name'));
				
				$billing_period 	= $package_info->billing_period;
				$freq				= (int)$package_info->freq;
				$price				= $package_info->price * 100;
				$stripe_package_id 	= $package_info->stripe_package_id;
				$description 		= sprintf(__('Buy %1s package', 'zoner'), $package_info->title);
				
				if ($isrecurring) {
					/*For recurring Payment*/
					try {
						
						$customerID = '';
						$customer = Stripe_Customer::create(array(
							"card"  => $tokenID,
							"plan"  => $stripe_package_id,
							"email" => $current_user->user_email
						));
						
					
						$customerID = $customer->id;
						
						if (!empty($customerID)) {
							
							$this->zoner_create_invoice_row( $customerID, 'Stripe', 4,  0,  null,  $packageID,  $price/100,  $currencyCodeType,  null );
							$this->zoner_update_user_plans_info(
									$packageID,  
									$this->zoner_get_expiry_date($billing_period, $freq), 
									'on',
									$customerID
							);			
							
							echo json_encode(array('1', __('Payment success', 'zoner')));
						} else {
							echo json_encode(array('0', __('Error complete stripe payment', 'zoner')));
						}
							
					
					} catch (Stripe_CardError $e) {
						echo json_encode(array('0', $e));
					}
					
				} else {
					
					try {
						
						$customer = Stripe_Customer::create(array(
							'email' => $current_user->user_email,
							'card'  => $tokenID
						));
						
						$customerID = $customer->id;
						$charge     = Stripe_Charge::create(array(
							'customer' => $customer->id,
							'amount'   => $price,
							'currency' => $currencyCodeType
						));
						
						if ($charge->paid == true) {
							$this->zoner_create_invoice_row( $charge->id, 'Stripe', 4,  0,  null,  $packageID,  $price/100,  $currencyCodeType,  null );
							$this->zoner_update_user_plans_info(
									$packageID,  
									$this->zoner_get_expiry_date($billing_period, $freq), 
									'off',
									$customerID
							);
					
							echo json_encode(array('1', __('Payment success', 'zoner')));
						} else {
							echo json_encode(array('0', __('Payment Error', 'zoner')));
						}
			
					} catch (Stripe_CardError $e) {
						echo json_encode(array('0', $e));
					}
				}
			}
		 }	

		 die();
	}
	
	public function zoner_create_invoice_row($transactionId, $paymentSystem, $payment_code, $payment_recurring, $property_id, $package_id, $price, $currency, $user_info, $isAuto = false) {
		global $zoner, $prefix, $zoner_config; 
		
		$invoice_name = null;
		$invoice_name = sprintf(__('Invoice #%1s', 'zoner'), $transactionId);
		$post_status = 'publish';
		if ($isAuto){
			$invoice_name = sprintf(__('Invoice recurring #%1s', 'zoner'), $transactionId);	
		}

		if($paymentSystem == 'BACS'){
			$post_status = 'draft';
		}
		
		
		$create_invocie_args = array(
			'post_title' 	=> $invoice_name,
			'post_content' 	=> '',
			'post_status' 	=> $post_status,
			'post_type'		=> 'invoices',
		);

		$wp_invoice_id = wp_insert_post( $create_invocie_args );
								
		update_post_meta($wp_invoice_id, $prefix . 'invoice_paymnent_system',   $paymentSystem);
		update_post_meta($wp_invoice_id, $prefix . 'invoice_transaction_id',    $transactionId);
		update_post_meta($wp_invoice_id, $prefix . 'invoice_detail_of_payment', $payment_code);
	    update_post_meta($wp_invoice_id, $prefix . 'invoice_payment_recurring', $payment_recurring); 
		if ($property_id != 0)
		update_post_meta($wp_invoice_id, $prefix . 'invoice_property_id', 		$property_id);
		if ($package_id != 0)
		update_post_meta($wp_invoice_id, $prefix . 'invoice_package_id', 		$package_id);
		
		update_post_meta($wp_invoice_id, $prefix . 'invoice_payment_price', 	$price);
		update_post_meta($wp_invoice_id, $prefix . 'invoice_payment_currency', 	$currency);
		update_post_meta($wp_invoice_id, $prefix . 'invoice_purchase_date',     current_time('mysql')); 	
		update_post_meta($wp_invoice_id, $prefix . 'invoice_user_id',			get_current_user_id());
		if (!empty($user_info))
		update_post_meta($wp_invoice_id, $prefix . 'invoice_user_info', 		$user_info);
		
		$zoner->emails->zoner_mail_to_invoice_act($wp_invoice_id);
		
		return $wp_invoice_id;
	}	
	
	public function zoner_update_property_payment_status($property_id, $payment_code) {
		global $zoner, $zoner_config, $prefix;
		
		if ($payment_code == 1) {
			update_post_meta($property_id, $prefix . 'is_paid',     'on');		
		} elseif ($payment_code == 2) {
			update_post_meta($property_id, $prefix . 'is_paid',     'on');		
			update_post_meta($property_id, $prefix . 'is_featured', 'on');
		} elseif ($payment_code == 3) {
			update_post_meta($property_id, $prefix . 'is_featured', 'on');
		}
	}
	
	
	public function  zoner_get_expiry_date($period = 1, $freq = 0) {
		$seconds = 0;
		
		switch ($period) {
			/*Days*/
			case 1:
				$seconds=60*60*24;
				break;
			/*Weeks*/	
			case 2:
				$seconds=60*60*24*7;
				break;
			/*Months*/	
			case 3:
				$seconds=60*60*24*30;
				break;    
			/*Years*/	
			case 4:
				$seconds=60*60*24*365;
				break;    
		}
               
		$expired_date =  strtotime(date('Y-m-d H:i:s', current_time( 'timestamp', 0 ))) + ($seconds*$freq);
		$expired_date =  date('Y-m-d H:i:s', $expired_date); 
		
		return $expired_date;
	}		   
			   
	public function zoner_update_user_plans_info($packageID, $validThru, $recurring, $stripeCustomerID = null) {
		global $zoner, $zoner_config, $prefix;
		
		$current_user = wp_get_current_user();		
		$user_id 	  = $current_user->ID;
		if (!is_super_admin( $user_id )) {
			update_user_meta( $user_id, $prefix . 'package_id', 		$packageID);
			update_user_meta( $user_id, $prefix . 'valid_thru', 		$validThru);
		    update_user_meta( $user_id, $prefix . 'payment_recurring', 	$recurring);
			if (!empty($stripeCustomerID))
			update_user_meta( $user_id, $prefix . 'stripe_customer_id', 		$stripeCustomerID);
			$zoner->emails->zoner_mail_to_agent_payment_package(get_the_title($packageID), $current_user->user_email, zoner_get_user_name($current_user));
		}
	}
	
	public function zoner_update_user_exp_date($userID, $packageID, $validThru) {
		global $zoner, $zoner_config, $prefix;
		if (!is_super_admin( $userID )) {
			$current_user = get_user_by( 'id', $userID );
			update_user_meta( $userID, $prefix . 'valid_thru', 			$validThru);
		    $zoner->emails->zoner_mail_to_agent_payment_package(get_the_title($packageID), $current_user->user_email, zoner_get_user_name($current_user));
		}
	}
	
	public function zoner_cleare_customer_information($user_id = null, $is_paid_system = null) {
		global $zoner, $zoner_config, $prefix;
		
		if ($is_paid_system == 0) {
			delete_user_meta( $user_id, $prefix. 'paypal_customer_id');
		} else {	
			delete_user_meta( $user_id, $prefix. 'stripe_customer_id');
		}
		
		delete_user_meta( $user_id, $prefix. 'package_id');
		delete_user_meta( $user_id, $prefix. 'valid_thru');
		delete_user_meta( $user_id, $prefix. 'package_id');
		delete_user_meta( $user_id, $prefix. 'payment_recurring');
		
		$args = array();
		$args = array(
               'post_type' 		=> 'property',
               'author'    		=> $user_id,
               'post_status'   	=> 'any',
			   'posts_per_page'	=> -1
        );
    
		$allProperty = new WP_Query( $args );    
		while( 	$query->have_posts()){
				$query->the_post();
        
				$property = array(
						'ID'            => $post->ID,
						'post_type'     => 'property',
						'post_status'   => 'zoner-expired'
				);
           
				wp_update_post($property); 
		}
		wp_reset_query();
		
		$curr_user 	= get_user_by('id', $user_id); 
		$user_email	= $curr_user->user_email;
		$display_name = zoner_get_user_name($curr_user);
		
		$zoner->emails->zoner_mail_to_agent_nopayment_package($user_email, $display_name);
	}
	
	public function zoner_featured_toggle() {
		global $zoner, $zoner_config, $prefix;
		
		$array_out = array();
		
		if (isset($_POST) && ($_POST['action'] = 'zoner_featured_toggle') && ($_POST['propertyID']) && is_user_logged_in()) {
			$is_featured_limit = $this->zoner_is_user_limit_featured_properties();
			
			$propertyID 	= 	(int)$_POST['propertyID'];
			$featured 		= 	(int)$_POST['featured'];
			
			if ($is_featured_limit) {
				if ($featured) {
					$array_out = array(1,sprintf(__('%1s is featured ON','zoner'),  get_the_title($propertyID)), __('Featured', 'zoner'));
					update_post_meta($propertyID, $prefix . 'is_featured', 'on');
				} else {
					$array_out = array(1,sprintf(__('%1s is featured OFF','zoner'), get_the_title($propertyID)), __('Not Featured', 'zoner'));
					update_post_meta($propertyID, $prefix . 'is_featured', 'off');
				}	
			} else {
				if ($featured) {
					$array_out = array(0,__('You have reached the limit of adding featured.','zoner'));
				} else {
					$array_out = array(1,sprintf(__('%1s is featured OFF','zoner'), get_the_title($propertyID)), __('Not Featured', 'zoner'));
					update_post_meta($propertyID, $prefix . 'is_featured', 'off');
				}	
			}
			
			
			echo json_encode($array_out);
		}
		die();
	}
	
}