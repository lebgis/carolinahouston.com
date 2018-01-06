<?php 

/**
 * Include and setup custom metaboxes and fields.
 *
 * @category Zoner
 * @package  Metaboxes
 * @license  http://www.opensource.org/licenses/gpl-license.php GPL v2.0 (or later)
 * @link     https://github.com/webdevstudios/Custom-Metaboxes-and-Fields-for-WordPress
 */

add_filter( 'cmb_meta_boxes', 'zoner_users_mtb');
/**
 * Define the metabox and field configurations.
 *
 * @param  array $meta_boxes
 * @return array
 */
function zoner_users_mtb( array $meta_boxes ) {
	// Start with an underscore to hide fields from custom fields list
	global $zoner_config, $current_user, $zoner;
	$prefix = '_zoner_';
	
	
	$currencies_list = $zoner->currency->get_zoner_currency_dropdown_settings();
	$currencies_list = array_merge($currencies_list, array('' => __('Not selected', 'zoner')));
	
	$meta_boxes[] = array(
		'id'            => 'user_edit',
		'title'         => __( 'User Profile', 'zoner' ),
		'pages'         => array( 'user' ), 
		'show_names'    => true,
		'zoner_styles' 	=> true, 
		'class'			=> 'user-profiles',
		'fields'        => array(
			array(
				'name'     => __( 'Extra User Info', 'zoner' ),
				'id'       => $prefix . 'extra_info',
				'type'     => 'title',
				'on_front' => false,
			),
			
			array(
				'name'    => __( 'Avatar', 'zoner' ),
				'id'      => $prefix . 'avatar',
				'type'    => 'file',
				'save_id' => true,
				'allow' => array( 'url', 'attachment' )
			),
			
			array(
				'name' => __( 'Facebook URL', 'zoner' ),
				'id'   => $prefix . 'facebookurl',
				'type' => 'text_url',
			),
			
			array(
				'name' => __( 'Twitter URL', 'zoner' ),
				'id'   => $prefix . 'twitterurl',
				'type' => 'text_url',
			),
			
			array(
				'name' => __( 'Google+ URL', 'zoner' ),
				'id'   => $prefix . 'googleplusurl',
				'type' => 'text_url',
			),
			
			array(
				'name' => __( 'Linkedin URL', 'zoner' ),
				'id'   => $prefix . 'linkedinurl',
				'type' => 'text_url',
			),
			
			array(
				'name' => __( 'Pinterest URL', 'zoner' ),
				'id'   => $prefix . 'pinteresturl',
				'type' => 'text_url',
			),
			
			array(
				'name' => __( 'Phone', 'zoner' ),
				'id'   => $prefix . 'tel',
				'type' => 'text_medium',
			),
			
			array(
				'name' => __( 'Mobile', 'zoner' ),
				'id'   => $prefix . 'mob',
				'type' => 'text_medium',
			),
			
			array(
				'name' => __( 'Skype', 'zoner' ),
				'id'   => $prefix . 'skype',
				'type' => 'text_medium',
			),
			
			array(
				'name'     => __( 'Localization', 'zoner' ),
				'id'       => $prefix . 'extra_info',
				'type'     => 'title',
				'on_front' => false,
			),
			
			array(
				'name'    => __('Localization Currency', 'zoner'),
				'subname'    => __('Select currency.', 'zoner'),
				'id'      => $prefix . 'user_currency',
				'type'    => 'select',
				'options' => $currencies_list,
				'default' => "",
			),
			
		)
	);
	
	if (is_user_logged_in()) {
		if ($current_user->roles[0] == 'administrator') {
			$curr_user_id 	 = $current_user->ID;
			$curr_package_id = get_user_meta($curr_user_id, $prefix.'package_id', true);
			
			$arr_packages = array();
			
			if (isset($zoner_config['free-available']) && $zoner_config['free-available']) {
				
				if (!empty($zoner_config['free-package-name'])) {
					$current_user_package = esc_attr($zoner_config['free-package-name']);	 
				} else {		
					$current_user_package = __('Without a name', 'zoner');	
				}
			
				$arr_packages[0] = $current_user_package;			
			}
			
			$all_custom_packages = $zoner->membership->zoner_get_all_packages();
			if (!empty($all_custom_packages)) {
				foreach($all_custom_packages as $key => $val) {
					$arr_packages[$key] = $val;
				}
			}
			
			$meta_boxes[] = array(
				'id'            => 'user_package_information',
				'title'         => __( 'User Package information', 'zoner' ),
				'pages'         => array( 'user' ), 
				'show_names'    => true,
				'zoner_styles' 	=> true, 
				'class'			=> 'user-profiles',
				'fields'        => array(
											array(
												'id'       => $prefix . 'user_membership',
												'name'     => __( 'User Package information', 'zoner' ),
												'type'     => 'title',
												'on_front' => false,
											),
											
											array(
												'id'      => $prefix . 'package_id',
												'name'    => __( 'Current package', 'zoner' ),
												'subname'	=> __('We do not recommend to change the package, it can cause conflict to members and membership system', 'zoner'),
												'type'    => 'select',
												'options' => $arr_packages,
											),
											
											array(
												'name' => __( 'Valid Thru', 'zoner' ),
												'id'   => $prefix . 'valid_thru',
												'type' => 'text_medium',
											),
											
											array(
												'name'  => __( 'Payment recurring', 'zoner' ),
												'id' 	=> $prefix . 'payment_recurring',
												'type' 	=> 'checkbox'
											),
										
									    )
				);	
		}		
	}	
	
	return $meta_boxes;
	
}	