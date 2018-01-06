<?php
/**
 * Zoner compare
*/
 
class zoner_compare {
		
	public function __construct() {
		
		add_action( "wp_ajax_nopriv_add_user_compare", array ( $this, 'zoner_set_compare_ajax' ) );
        add_action( "wp_ajax_add_user_compare",        array ( $this, 'zoner_set_compare_ajax' ) );
		
		add_action( "wp_ajax_nopriv_remove_item_from_cl", array ( $this, 'zoner_set_compare_ajax' ) );
        add_action( "wp_ajax_remove_item_from_cl",		  array ( $this, 'zoner_set_compare_ajax' ) );
	}	
	
	public function zoner_set_compare_ajax() {
		if (($_POST['action'] == 'add_user_compare') || 
			($_POST['action'] == 'remove_item_from_cl')) {
		
			$property_id = $is_choose = $c_prop = 0;
		
			$property_id = $_POST['property_id'];
			$is_choose 	 = $_POST['is_choose'];
		
			if (($property_id != 0) || (!empty($property_id)))
				$this->zoner_set_compare_cookie($property_id, $is_choose);
		}
		die('');
	}

	public function zoner_set_compare_cookie($property_id, $is_choose) {
		
		$compareValues = $results = array();
		
	    if (!isset($_COOKIE['zonerCompare'])) {
			$compareValues[] = $property_id;
		} else {
			$existValues = explode('~', $_COOKIE['zonerCompare']);
			if ($is_choose == 0) {
				if(($key = array_search($property_id, $existValues)) !== false) {
					unset($existValues[$key]);
				}
			} else {
				$cnt = count($existValues);
				if ($cnt <= 2) {
					$existValues[] = $property_id;
				}	
			}
			$compareValues = $existValues;
		}
		
		$results = array_unique($compareValues);
		
		if (!empty($results)){
			setcookie('zonerCompare', implode('~', $results), strtotime('+14 days'), COOKIEPATH, COOKIE_DOMAIN, false);
			printf(__('%d of 3 Property', 'zoner'), count($results));
		} else {
			setcookie( 'zonerCompare', null, time() - 999999, COOKIEPATH, COOKIE_DOMAIN, false);
		}
		
		
    }
	
	public function zoner_get_compare($property_id) {
		$in_compare_list = false;
		$existValues = array();
		
		if (isset($_COOKIE['zonerCompare'])) {
			$existValues = explode('~', $_COOKIE['zonerCompare']);
			if(in_array($property_id, $existValues)) {
				$in_compare_list = true;
			}
		}
		
		return $in_compare_list;
	}		
	
	public function zoner_get_all_count_compare() {
		$count_properties = 0;
		
		if (isset($_COOKIE['zonerCompare'])) {
			$existValues = explode('~', $_COOKIE['zonerCompare']);
			$count_properties = count($existValues);
		}
		
		return $count_properties;
	}		
	
	public function zoner_get_compare_property_id() {
		$prop_ids = array();
		if (isset($_COOKIE['zonerCompare'])) {
			$prop_ids = explode('~', $_COOKIE['zonerCompare']);
		}
		return $prop_ids;
	}		
	
}