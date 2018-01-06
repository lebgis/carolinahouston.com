<?php
/**
 * Zoner currency
*/

class zoner_currency {
	public $currency;
	
	public function __construct() {
		$this->currency = $this->get_zoner_currencies();
		add_action('wp', array($this, 'zonerGetOnlineCurrencyInfo'));
		
		add_action( 'wp_ajax_nopriv_zoner_currency_calculate', array($this, 'zonerCurrencyCalculator') );  
		add_action( 'wp_ajax_zoner_currency_calculate', array($this, 'zonerCurrencyCalculator') );
		
	}	
	
	
	function get_zoner_currencies() {
		return array_unique(
			apply_filters( 'zoner_currencies',
				array(
					'AED' => __( 'United Arab Emirates Dirham', 'zoner' ),
					'AUD' => __( 'Australian Dollars', 'zoner' ),
					'BDT' => __( 'Bangladeshi Taka', 'zoner' ),
					'BRL' => __( 'Brazilian Real', 'zoner' ),
					'BGN' => __( 'Bulgarian Lev', 'zoner' ),
					'CAD' => __( 'Canadian Dollars', 'zoner' ),
					'CLP' => __( 'Chilean Peso', 'zoner' ),
					'CNY' => __( 'Chinese Yuan', 'zoner' ),
					'COP' => __( 'Colombian Peso', 'zoner' ),
					'CZK' => __( 'Czech Koruna', 'zoner' ),
					'DKK' => __( 'Danish Krone', 'zoner' ),
					'EUR' => __( 'Euros', 'zoner' ),
					'HKD' => __( 'Hong Kong Dollar', 'zoner' ),
					'HRK' => __( 'Croatia kuna', 'zoner' ),
					'HUF' => __( 'Hungarian Forint', 'zoner' ),
					'ISK' => __( 'Icelandic krona', 'zoner' ),
					'IDR' => __( 'Indonesia Rupiah', 'zoner' ),
					'INR' => __( 'Indian Rupee', 'zoner' ),
					'ILS' => __( 'Israeli Shekel', 'zoner' ),
					'JPY' => __( 'Japanese Yen', 'zoner' ),
					'KRW' => __( 'South Korean Won', 'zoner' ),
					'MYR' => __( 'Malaysian Ringgits', 'zoner' ),
					'MXN' => __( 'Mexican Peso', 'zoner' ),
					'NGN' => __( 'Nigerian Naira', 'zoner' ),
					'NOK' => __( 'Norwegian Krone', 'zoner' ),
					'NZD' => __( 'New Zealand Dollar', 'zoner' ),
					'PHP' => __( 'Philippine Pesos', 'zoner' ),
					'PLN' => __( 'Polish Zloty', 'zoner' ),
					'GBP' => __( 'Pounds Sterling', 'zoner' ),
					'RON' => __( 'Romanian Leu', 'zoner' ),
					'RUB' => __( 'Russian Ruble', 'zoner' ),
					'SGD' => __( 'Singapore Dollar', 'zoner' ),
					'ZAR' => __( 'South African rand', 'zoner' ),
					'SEK' => __( 'Swedish Krona', 'zoner' ),
					'CHF' => __( 'Swiss Franc', 'zoner' ),
					'TWD' => __( 'Taiwan New Dollars', 'zoner' ),
					'THB' => __( 'Thai Baht', 'zoner' ),
					'TRY' => __( 'Turkish Lira', 'zoner' ),
					'USD' => __( 'US Dollars', 'zoner' ),
					'UAH' => __( 'Ukrainian Hryvnia', 'zoner' ),
					'CLF' => __( 'Chilean Unit of Account', 'zoner'),
					'VEF' => __( 'Venezuelan Bolivar', 'zoner' ),
					'VND' => __( 'Vietnamese Dong', 'zoner' ),
				)
			)
		);
	}

	function get_zoner_currency_symbol( $currency = '' ) {
		if ( !$currency ) $currency = $this->get_zoner_currencies();

		switch ( $currency ) {
			case 'AED' :
				$currency_symbol = 'د.إ';
				break;
			case 'BDT':
				$currency_symbol = '&#2547;&nbsp;';
				break;
			case 'BRL' :
				$currency_symbol = '&#82;&#36;';
				break;
			case 'BGN' :
				$currency_symbol = '&#1083;&#1074;.';
				break;
			case 'AUD' :
			case 'CAD' :
			case 'CLP' :
			case 'MXN' :
			case 'NZD' :
			case 'HKD' :
			case 'SGD' :
			case 'USD' :
				$currency_symbol = '&#36;';
				break;
			case 'EUR' :
				$currency_symbol = '&euro;';
				break;
			case 'CNY' :
			case 'RMB' :
			case 'JPY' :
				$currency_symbol = '&yen;';
				break;
			case 'RUB' :
				$currency_symbol = '&#1088;&#1091;&#1073;.';
				break;
			case 'KRW' : $currency_symbol = '&#8361;'; break;
			case 'TRY' : $currency_symbol = '&#8378;'; break;
			case 'NOK' : $currency_symbol = '&#107;&#114;'; break;
			case 'ZAR' : $currency_symbol = '&#82;'; break;
			case 'CZK' : $currency_symbol = '&#75;&#269;'; break;
			case 'MYR' : $currency_symbol = '&#82;&#77;'; break;
			case 'DKK' : $currency_symbol = 'kr.'; break;
			case 'HUF' : $currency_symbol = '&#70;&#116;'; break;
			case 'IDR' : $currency_symbol = 'Rp'; break;
			case 'INR' : $currency_symbol = 'Rs.'; break;
			case 'ISK' : $currency_symbol = 'Kr.'; break;
			case 'ILS' : $currency_symbol = '&#8362;'; break;
			case 'PHP' : $currency_symbol = '&#8369;'; break;
			case 'PLN' : $currency_symbol = '&#122;&#322;'; break;
			case 'SEK' : $currency_symbol = '&#107;&#114;'; break;
			case 'CHF' : $currency_symbol = '&#67;&#72;&#70;'; break;
			case 'TWD' : $currency_symbol = '&#78;&#84;&#36;'; break;
			case 'THB' : $currency_symbol = '&#3647;'; break;
			case 'GBP' : $currency_symbol = '&pound;'; break;
			case 'RON' : $currency_symbol = 'lei'; break;
			case 'VND' : $currency_symbol = '&#8363;'; break;
			case 'VEF' : $currency_symbol = 'VEB'; break;
			case 'NGN' : $currency_symbol = '&#8358;'; break;
			case 'HRK' : $currency_symbol = 'Kn'; break;
			case 'CLF' : $currency_symbol = 'UF'; break;
			case 'UAH' : $currency_symbol = '&#8372;'; break;
			case 'COP' : $currency_symbol = 'COP'; break;
			default    : $currency_symbol = ''; break;
		}

		return apply_filters( 'zoner_currency_symbol', $currency_symbol, $currency );
	}
	
	public function get_zoner_currency_dropdown_settings() {
		$currency_code_options = array();
		$currency_code_options = $this->get_zoner_currencies();
		
		foreach ( $currency_code_options as $code => $name ) {
			$currency_code_options[ $code ] = $name . ' (' . $this->get_zoner_currency_symbol( $code ) . ')';
		}
		
		
		return $currency_code_options;
	}
	
	public function get_zoner_property_price($price = '', $currency = '', $format = 0, $payment = '', $area_unit = '', $is_tagged = true) {
		global $zoner_config, $post, $zoner, $prefix;
		$out_html = $out_additional_price_info = '';
		 
		$decimal_sep  = '.';
		$thousands_sep = ',';
		$number_decimals = 2;
		
		$currency_symbol = $this->get_zoner_currency_symbol($currency);
		if (!empty($zoner_config['decimal-sep'])){
			$decimal_sep     = wp_specialchars_decode( stripslashes( $zoner_config['decimal-sep'] ),  ENT_QUOTES );
		} 
		if (!empty($zoner_config['thousand-sep'])){
			$thousands_sep   = wp_specialchars_decode( stripslashes( $zoner_config['thousand-sep'] ), ENT_QUOTES );
		}
		if (!empty($zoner_config['number-decimal'])){
			$number_decimals = absint( $zoner_config['number-decimal'] );
		}
		
		$is_bookmark = get_query_var('profile-page');
		
		
		if ((is_user_logged_in() && !is_author()) || (is_user_logged_in() && is_author() && $is_bookmark == 'my_bookmarks')) {
			if (isset($_SESSION['zonerUserCurrencyRate'])) {
				$rate = $_SESSION['zonerUserCurrencyRate'];
				
				if (is_array($rate)) {
					if (!empty($rate[$currency])) {
						$currency_rate   = 1;
						$currency_symbol = $this->get_zoner_currency_symbol($rate[$currency]['tCurrency']);
						
						if (!empty($rate[$currency]['rate']))
						$currency_rate = $rate[$currency]['rate'];
							
						$price = round((float) $currency_rate * $price);
					}
				}	
			}
		}
		
		$price = apply_filters( 'raw_zoner_price', floatval( $price ) );
		$price = apply_filters( 'formatted_zoner_price', number_format( $price, $number_decimals, $decimal_sep, $thousands_sep ), $price, $number_decimals, $decimal_sep, $thousands_sep );
		
		if ($format > 0) {
			$payment_name = $area_html = '';
			
			if (!empty($payment))   $payment_name  = ' / ' . $zoner->property->get_payment_rent_name($payment);
			if (!empty($area_unit))	$area_html     = ' / ' . $zoner->property->ret_area_units_by_id($area_unit);
			
			if ($format == 1) {
				$out_additional_price_info =  $payment_name;
			} else {
				$out_additional_price_info =  $area_html;
			}
		}

		if (empty($price) || $price == 0) {
			if ($is_tagged) {
				$out_html = '<span class="tag price">'. __('Price on request', 'zoner') .'</span>';
			} else {
				$out_html = __('Price on request', 'zoner');
			}
		} else {
			if ( $number_decimals > 0 ) $price = $this->zoner_trim_zeros( $price );
			if ($is_tagged) {
				$out_html = '<span class="tag price">'. sprintf( $this->zoner_price_format(), $currency_symbol, $price ) .$out_additional_price_info.'</span>';
			} else 	{
				$out_html = sprintf( $this->zoner_price_format(), $currency_symbol, $price ) . $out_additional_price_info;
			}
		}	
		
		
		
		return $out_html;
	}
	
	
	public function zoner_trim_zeros( $price ) {
		global $zoner_config;
		$decimal_sep = '.';
		if (!empty($zoner_config['decimal-sep'])){
			$decimal_sep = $zoner_config['decimal-sep'];
		}
		return preg_replace( '/' . preg_quote( $decimal_sep, '/' ) . '0++$/', '', $price );
	}
	
	
	function zoner_price_format() {
		global $zoner_config;
		$format = '';
		$currency_pos = 'right';
		if (!empty($zoner_config[ 'currency-position' ]))
			$currency_pos = $zoner_config[ 'currency-position' ];
		
		
		switch ( $currency_pos ) {
			case 'left' :
				$format = '%1$s%2$s';
			break;
			case 'right' :
				$format = '%2$s%1$s';
			break;
			case 'left_space' :
				$format = '%1$s&nbsp;%2$s';
			break;
			case 'right_space' :
				$format = '%2$s&nbsp;%1$s';
			break;
		}

		return apply_filters( 'zoner_price_format', $format, $currency_pos );
	}
	
	public function zoner_get_price_($operator, $meta) {
		global $wpdb;
		$query = $wpdb->prepare( "SELECT ".$operator."( cast( pm.meta_value as UNSIGNED ) ) FROM {$wpdb->posts} p , {$wpdb->postmeta} pm WHERE pm.meta_key='%s' AND p.ID = pm.post_id AND p.post_status = 'publish'", $meta );
		return $wpdb->get_var( $query );
	}
	
	public function zonerGetOnlineCurrencyInfo() {
		global $zoner, $prefix, $zoner_config, $wpdb;
			   $currency_rate_array = array();
		$is_update = false;
		$userCurrency = null;
		
		if (session_id() == '') session_start(); 
		
		if (is_user_logged_in()) {
			$userID = get_current_user_id();
			$userCurrency = esc_attr(get_the_author_meta( $prefix.'user_currency', $userID ));
			
			if (!empty($userCurrency)) {
				
				if (isset($_SESSION['zonerUserCurrencyUpdate']) && (int)$_SESSION['zonerUserCurrencyUpdate'] <= time()) {
					$is_update = true;
				} 
				
				if (!isset($_SESSION['zonerUserCurrencyUpdate'])) {
					$is_update = true;
				}
				if (isset($_SESSION['zonerUserCurrentCurrency']) && ($userCurrency != $_SESSION['zonerUserCurrentCurrency'])) {
					$is_update = true;
				}
				
				
				$query_currency = $wpdb->prepare( "SELECT DISTINCT pm.meta_value currency FROM {$wpdb->posts} p , {$wpdb->postmeta} pm WHERE pm.meta_key='%s' AND p.ID = pm.post_id AND p.post_status = 'publish' AND p.post_type='property' AND pm.meta_value is not null", array($prefix."currency") );	
				$query_currency = $wpdb->get_results( $query_currency );
				
				if (is_array($query_currency) && !empty($query_currency) && $is_update && !empty($userCurrency)) {
					foreach ($query_currency as $currency) {
						$fCurrency 	 = urlencode($currency->currency);
						$tCurrency   = urlencode($userCurrency);
                        $acces_key   = $zoner_config['currencylayer-key'];
                        if ($acces_key == '')
                            $acces_key = 'f2c55afee27f032c0cd38e997e547d16'; //default free account
						$getCurrency = @file_get_contents("http://apilayer.net/api/live?access_key=$acces_key&source=$fCurrency&currencies=$tCurrency");
						$getCurrency = json_decode($getCurrency);
						
						
						if(!empty($getCurrency)) {
							$rate = 0;
                            if (!empty($getCurrency->quotes))
							$rate = (float) current($getCurrency->quotes);
							
							$currency_rate_array[$fCurrency] = array('tCurrency' => $userCurrency, 'rate' => $rate);
						}
					}
					
					if (!empty($currency_rate_array)) {
						$_SESSION['zonerUserCurrencyRate']    = $currency_rate_array;
						$_SESSION['zonerUserCurrencyUpdate']  = strtotime("+5 minutes");
						$_SESSION['zonerUserCurrentCurrency'] = $userCurrency;
					}	
				}
			} else {
				if (isset($_SESSION['zonerUserCurrencyRate'])) unset($_SESSION['zonerUserCurrencyRate']);
				if (isset($_SESSION['zonerUserCurrencyUpdate'])) unset($_SESSION['zonerUserCurrencyUpdate']);
				if (isset($_SESSION['zonerUserCurrentCurrency'])) unset($_SESSION['zonerUserCurrentCurrency']);
			}
		}
	}
	
	public function zonerCurrencyCalculator() {
		global $zoner_config;

		if (isset($_POST) && ($_POST['action'] == 'zoner_currency_calculate'))	{
			/*Thanks for http://jsonrates.com/*/
			
			$decimal_sep     = wp_specialchars_decode( stripslashes( $zoner_config['decimal-sep'] ),  ENT_QUOTES );
			$thousands_sep   = wp_specialchars_decode( stripslashes( $zoner_config['thousand-sep'] ), ENT_QUOTES );
			$number_decimals = absint( $zoner_config['number-decimal'] );
			
			$out_arr 	 = array();
			$amount 	 = (float) $_POST['amount'];
			$fCurrency 	 = urlencode($_POST['fCurrency']);
			$tCurrency   = urlencode($_POST['tCurrency']);
			$getRate 	 = 0;
            $get = file_get_contents("https://www.google.com/finance/converter?a=$amount&from=$fCurrency&to=$tCurrency");
            $get = explode("<span class=bld>",$get);
            $get = explode("</span>",$get[1]);  
            $getRate = preg_replace("/[^0-9\.]/", null, $get[0]);
            if (!$getRate) $getRate=0;
			$out_arr 	 = array ('to' => $tCurrency, 'from' => $fCurrency, 'v' => $getRate, 'amount' => $amount);
			
			if (!empty($out_arr)) echo json_encode($out_arr);
		}
		die('');
	}
	
}