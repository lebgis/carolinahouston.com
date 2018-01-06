<?php
/**
 * Include and setup custom metaboxes and fields.
 *
 * @category Zoner
 * @package  Metaboxes
 * @license  http://www.opensource.org/licenses/gpl-license.php GPL v2.0 (or later)
 * @link     https://github.com/webdevstudios/Custom-Metaboxes-and-Fields-for-WordPress
 */


 add_filter( 'cmb_meta_boxes', 'zoner_membership_mtb');
/**
 * Define the metabox and field configurations.
 *
 * @param  array $meta_boxes
 * @return array
 */
function zoner_membership_mtb( array $meta_boxes ) {

	// Start with an underscore to hide fields from custom fields list
	global $zoner_config, $zoner;
	
	$prefix = '_zoner_';
	
	$meta_boxes[] = array(
		'id'         => 'package_options',
		'title'      => __( 'Package options', 'zoner' ),
		'pages'      => array( 'packages'), 
		'context'    => 'normal',
		'priority'   => 'high',
		'show_names' => true, 
		'fields'     => array(
			
				array(
					'name'    => __('Billing Period', 'zoner'),
					'id'      => $prefix . 'billing_period',
					'type'    => 'select',
					'options' => $zoner->membership->get_membership_period_values(),
					'default' => '1',
				),
				
				array(
					'name'    => __('Billing every "Billing Period"', 'zoner'),
					'default' => '10',
					'id'      => $prefix . 'billing_period_freq',
					'type'    => 'text_small'
				),
				
				array(
					'name'    => __('How many properties are included?', 'zoner'),
					'default' => '20',
					'id'      => $prefix . 'pack_limit_properties',
					'type'    => 'text_small'
				),

				array(
					'name'    => __('How many featured properties are included?', 'zoner'),
					'id'      => $prefix . 'pack_limit_featured',
					'type'    => 'text_small'
				),
				
				array(
					'name' 		=> __('Price', 'zoner'),
					'subname' 	=> __('Edit price for current package.', 'zoner'),
					'id'   		=> $prefix . 'pack_price',
					'type' 	    => 'text_small',
					'default'   => '20',
				),
				
				array(
					'id' 	=> $prefix . 'pack_unlim_properties',
					'label'	=> __('Yes', 'zoner'),
					'name' 	=> __('Unlimited properties', 'zoner'),
					'type' 	=> 'checkbox',
				),
				
				array(
					'id' 	=> $prefix . 'pack_unlim_featured',
					'label'	=> __('Yes', 'zoner'),
					'name' 	=> __('Unlimited featured properties', 'zoner'),
					'type' 	=> 'checkbox',
				),
				array(
					'name' 	=> __('Create agency', 'zoner'),
					'label'	=> __('Yes', 'zoner'),
					'id' 	=> $prefix . 'pack_agency_profile',
					'type' 	=> 'checkbox',
				),
				array(
					'name' 	=> __('Available package', 'zoner'),
					'label'	=> __('Yes', 'zoner'),
					'id' 	=> $prefix . 'pack_visible',
					'type' 	=> 'checkbox',
					'default' => 'on'
				),
				array(
					'name' 		=> __('Stripe Package ID', 'zoner'),
					'subname' 	=> __('Edit stripe package ID.', 'zoner'),
					'id'   		=> $prefix . 'stripe_pakcage_id',
					'type' 	    => 'text_medium'
				),
				
				// New Features
				// array(
					// 'name' 			=> __('Package color', 'zoner'),
					// 'id'   			=> $prefix . 'pack_color',
					// 'type' 			=> 'colorpicker',
					// 'default'  		=> '#00B200',
					// 'repeatable' 	=> false,
				// ),
				
	));
	
	return $meta_boxes;
}	