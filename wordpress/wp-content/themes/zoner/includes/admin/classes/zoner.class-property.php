<?php

/**
 * Zoner property
*/
 
class zoner_property {
	
	public function __construct() {
		
	}	
	
	public function get_default_area_unit() {
		global $zoner, $zoner_config;
		
		$unit = 'm';
		if (!empty($zoner_config['area-unit']))
		$unit = esc_attr($zoner_config['area-unit']);
		
		return $unit;
	}
	
	public function get_condition_values() {
		$conditions = array();
		$conditions = apply_filters( 'zoner_condition', array( 
							 0 => __('No condition', 'zoner'),
							 1 => __('Sold', 'zoner'),
							 2 => __('In Hold', 'zoner'))
		);
		
		return $conditions;
	}
	
	public function get_condition_name($in_) {
		$conditions = array();
		$conditions = $this->get_condition_values();
		if (empty($in_)) $in_ = 0;
		if (!empty($conditions[$in_]))
		return $conditions[$in_];
	}

	public function get_payment_rent_values() {
		$payments = array();
		$payments = apply_filters ('zoner_payments', array(
						   0 => __('No Payment', 'zoner'),
						   1 => __('Monthly', 'zoner'),
						   2 => __('Quarter', 'zoner'),
						   3 => __('Yearly', 'zoner'),
						   4 => __('Daily', 'zoner'),
						   5 => __('Weekly', 'zoner'),
				  ) 
		);
		
		return $payments;
	}
	
	public function get_area_units_values() {
		$area_units = array();
		$area_units = apply_filters( 'zoner_area_units', array(
							 0  => __('m2', 	'zoner'),
							 1  => __('km2', 	'zoner'),
							 2  => __('dam2', 	'zoner'),
						     3  => __('dm2', 	'zoner'),
						     4  => __('ha', 	'zoner'),
						     5  => __('sqmi', 	'zoner'),
						     6  => __('acres', 	'zoner'),
							 7  => __('sq yd', 	'zoner'),
							 8  => __('sq ft',  'zoner'),
							 9  => __('sq nmi', 'zoner'),
							 10 => __('dunam', 	'zoner'),
							 11 => __('tsubo', 	'zoner'),
							 12 => __('pyeong',	'zoner'),
							 13 => __('cda',	'zoner'),
				  ) 
		);
		
		return $area_units;
	}
	
	public function ret_area_units_by_id($id = 0) {
		$html_text  = '';
		$area_units = $this->get_area_units_values();
		
		if (array_key_exists($id, $area_units)) {
			if (strpos($area_units[$id], '2') === false) {
				$html_text = $area_units[$id];
			} else {
				$html_text = str_replace("2", "<sup>2</sup>", $area_units[$id]);
			}
		} else {	
			$html_text = str_replace("2", "<sup>2</sup>", $area_units[0]);
		}
		
		return $html_text;
	}
	
	
	
	public function get_price_format_values() {
		$payments = array();
		$payments = apply_filters( 'zoner_price_format_values', array(
						   0 => __('Default format', 'zoner'),
						   1 => __('+ Payment',      'zoner'),
						   2 => __('+ Area units',   'zoner'),
				  ) 
		);
		
		return $payments;
	}

	public function get_payment_rent_name($in_) {
		$payments = array();
		$payments = $this->get_payment_rent_values();
		if (empty($in_)) $in_ = 0;
		return $payments[$in_];
	}
	
	
	public function zoner_get_custom_meta($meta) {
		global $wpdb;
		$query = $wpdb->prepare( "SELECT DISTINCT pm.meta_value FROM {$wpdb->posts} p, {$wpdb->postmeta} pm WHERE pm.meta_key='%s' AND  ((pm.meta_value != '') OR (pm.meta_value != 0)) AND p.ID = pm.post_id AND p.post_status = 'publish' ORDER BY cast(meta_value as unsigned) ASC", $meta );
		return $wpdb->get_results( $query );
	}

	/*All Variables from Property Types*/
	public function get_property($property_id) {
		global $zoner, $prefix, $zoner_config;
		$property_array_fields = $property = array();
		$property 			   = get_post( $property_id);
		
		if (!empty($property)) {
			$property_array_fields['id']	= $property->ID;
			$property_array_fields['reference'] = get_post_meta($property_id, $prefix .'reference', true);
			$property_array_fields['title']	= $property->post_title;
			$property_array_fields['link']	= get_permalink($property_id);
			$property_array_fields['author'] = $property->post_author;
						
			$property_array_fields['allow_raiting'] = get_post_meta($property_id, $prefix .'allow_raiting', true);
			$property_array_fields['avg_rating']   	= get_post_meta($property_id, $prefix .'avg_rating', true);
			$property_array_fields['views'] 	 	= get_post_meta($property_id, $prefix .'views', true);
			$property_array_fields['currency']	 	= get_post_meta($property_id, $prefix.'currency', true);
			$property_array_fields['currency_symbol'] = $zoner->currency->get_zoner_currency_symbol($property_array_fields['currency']);
			$property_array_fields['price']			= get_post_meta($property_id, $prefix.'price', true);
			$property_array_fields['price_format'] 	= get_post_meta($property_id, $prefix.'price_format', true);
			$property_array_fields['payment_rent'] 	= get_post_meta($property_id, $prefix.'payment', true);			
			$property_array_fields['payment_rent_name'] = $this->get_payment_rent_name($property_array_fields['payment_rent']);
			$property_array_fields['area']			= get_post_meta($property_id, $prefix.'area', true);
			$property_array_fields['location']		= get_post_meta($property_id, $prefix.'location', true);
			$property_array_fields['show_on_map']		= get_post_meta($property_id, $prefix.'show_on_map', true);
			$area_unit = get_post_meta($property_id, $prefix.'area_unit', true);
			if ($area_unit) { 
				$property_array_fields['area_unit'] = $area_unit;
			} else {
				$property_array_fields['area_unit'] = 0;
			}			
			if (empty($zoner_config['area-unit'])){
				$all_ares_units = $this->get_area_units_values();
				$zoner_config['area-unit'] = $all_ares_units[0];	
			} 
			$property_array_fields['price_html'] = $zoner->currency->get_zoner_property_price(	$property_array_fields['price'], 
																								$property_array_fields['currency'], 
																								esc_attr($property_array_fields['price_format']),
																								esc_attr($property_array_fields['payment_rent']),
																								esc_attr($property_array_fields['area_unit']),
																								true
																							 );
			$property_array_fields['rooms'] 	 = get_post_meta($property_id, $prefix.'rooms', true);
			$property_array_fields['beds'] 		 = get_post_meta($property_id, $prefix.'beds', true);
			$property_array_fields['baths'] 	 = get_post_meta($property_id, $prefix.'baths', true);
			$property_array_fields['garages'] 	 = get_post_meta($property_id, $prefix.'garages', true);
			$property_array_fields['condition']	 = get_post_meta($property_id, $prefix.'condition', true);
			$property_array_fields['condition_name'] = $this->get_condition_name($property_array_fields['condition']);

			$property_array_fields['country'] 	= get_post_meta($property_id, $prefix.'country', true);
			$property_array_fields['state'] 	= get_post_meta($property_id, $prefix.'state', true);
			
			$name_state = null;
			if (!empty($property_array_fields['state']) && ($property_array_fields['country'])) {
				$name_state =  $zoner->countries->get_name_state($property_array_fields['country'], $property_array_fields['state']);
				$name_state = __($name_state, 'zoner');	/// quick fix. Can't get the right translation without this line		
			}
			
			$address_arr = array();
			$city        = wp_get_post_terms($property_id, 'property_city',   array('orderby' => 'name', 'hide_empty' => 0) );
			if (!empty($city)) {
				foreach($city as $item) {
					$city = $item;
					break;
				}
			}
			
			$district 	= get_post_meta($property_id, $prefix.'district', true);
			$zip 		= get_post_meta($property_id, $prefix.'zip', true);
			$address	= get_post_meta($property_id, $prefix.'address', true);
			
			if (!empty($city)){
				$property_array_fields['city'] = $city->name;
				$property_array_fields['city_tax_id'] = $city->term_id;	
			} else {
				$property_array_fields['city'] = null;
				$property_array_fields['city_tax_id'] = -1;	
			}

			$property_array_fields['district']	= $district;
			$property_array_fields['zip']		= $zip;
			$property_array_fields['address']   = $address;
			
			if (!empty($name_state))    $address_arr[] = $name_state;
			if (!empty($city)) 	    	$address_arr[] = '<a href="'.get_term_link( $city, 'property_city' ).'" title="'.$city->name.'" rel="nofollow">'.$city->name.'</a>';
			if (!empty($district))		$address_arr[] = $district;
			if (!empty($address))   	$address_arr[] = $address;
			if (!empty($zip))			$address_arr[] = $zip;
			
			$property_array_fields['full_address'] = implode(', ', apply_filters('zoner_full_address', $address_arr));
			$property_array_fields['is_featured']  = get_post_meta($property_id, $prefix . 'is_featured', true);			
			$property_array_fields['is_paid'] 	   = get_post_meta($property_id, $prefix . 'is_paid', true);			
						
			$property_array_fields['lat'] = get_post_meta($property_id, $prefix.'lat', true);
			$property_array_fields['lng'] = get_post_meta($property_id, $prefix.'lng', true);
		
			$property_array_fields['prop_files']   = get_post_meta($property_id, $prefix.'files', true);
			$property_array_fields['prop_gallery'] = get_post_meta($property_id, $prefix.'gallery', true);
			$property_array_fields['prop_plans']   = get_post_meta($property_id, $prefix.'plans', true);
			$property_array_fields['prop_video']   = get_post_meta($property_id, $prefix.'videos', true);
			
			$property_array_fields['property_types']  = wp_get_post_terms($property_id, 'property_type',   array('orderby' => 'name', 'hide_empty' => 0) );
			$property_array_fields['property_status'] = wp_get_post_terms($property_id, 'property_status', array('orderby' => 'name', 'hide_empty' => 0) );
		}
		
		return  (object) $property_array_fields;
	}
}	