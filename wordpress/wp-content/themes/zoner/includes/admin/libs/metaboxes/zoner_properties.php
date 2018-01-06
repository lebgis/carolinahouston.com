<?php
/**
 * Include and setup custom metaboxes and fields.
 *
 * @category Zoner
 * @package  Metaboxes
 * @license  http://www.opensource.org/licenses/gpl-license.php GPL v2.0 (or later)
 * @link     https://github.com/webdevstudios/Custom-Metaboxes-and-Fields-for-WordPress
 */

add_filter( 'cmb_meta_boxes', 'zoner_properties_mtb');
/**
 * Define the metabox and field configurations.
 *
 * @param  array $meta_boxes
 * @return array
 */
function zoner_properties_mtb( array $meta_boxes ) {
	// Start with an underscore to hide fields from custom fields list
	$prefix = '_zoner_';
	global $zoner_config, $zoner;
	
	$currency = $country_value = $area_unit = '';
	if (isset($zoner_config['currency'])) 
	$currency = $zoner_config['currency'];
	if (isset($zoner_config['default-country'])) 
	$country_value = $zoner_config['default-country'];
	if (isset($zoner_config['area-unit']))  
	$area_unit = $zoner_config['area-unit'];
	
	$meta_boxes[] = array(
		'id'         => 'prop_location',
		'title'      => __( 'Location', 'zoner' ),
		'pages'      => array( 'property'), 
		'context'    => 'normal',
		'priority'   => 'high',
		'show_names' => true, 
		'fields'     => array(
			
			array(
				'name'    => __( 'Country', 'zoner' ),
				'subname'    => __( 'Edit country.', 'zoner' ),
				'id' 	=> $prefix . 'country',
				'type' 	=> 'country',
				'default'	=> $country_value
			),
			
			array(
				'name'    => __( 'State', 'zoner' ),
				'subname' => __( 'Edit state.', 'zoner' ),
				'id' 	  => $prefix . 'state',
				'type' 	  => 'state'
			),
			
			array(
				'name'    => __( 'Address', 'zoner' ),
				'subname' => __( 'Edit address.', 'zoner' ),
				'id'      => $prefix . 'address',
				'type'    => 'text'
			),
			
			array(
				'name'    => __( 'District', 'zoner' ),
				'subname'    => __( 'Edit district.', 'zoner' ),
				'id' 	=> $prefix . 'district',
				'type' => 'text_medium'
			),
			
			array(
				'name'    => __( 'Postcode / Zip', 'zoner' ),
				'subname'    => __( 'Edit postcode / zip.', 'zoner' ),
				'id' 	=> $prefix . 'zip',
				'type' => 'text_small'
			),
			
			array(
				'name' 	=> __('Map Location', 'zoner'),
				'desc' 	=> __('Drag the marker to set the exact location', 'zoner'),
				'id' 	=> $prefix . 'geo_location',
				'type' 			  => 'zoner_custom_map',
				'sanitization_cb' => 'zoner_custom_map_sanitise',
				'default_zoom'	  => 5
			),
			array(
				'name'    => __( 'Show on map', 'zoner' ),
				'id' 	  => $prefix . 'show_on_map',
				'type' 	  => 'checkbox',
				'default' => 'on'
			)
		)
	);
	
	$property_fields_option = apply_filters('zoner_admin_property_fields', array (
			array(
                'name'    => __( 'Property ID', 'zoner' ),
                'subname'    => __( 'Edit property ID.', 'zoner' ),
                'id' 	=> $prefix . 'reference',
                'type' => 'text_medium',
               	'default' => 'PR'.mt_rand()
            ),
			array(
				'name'    => __('Condition', 'zoner'),
				'subname'    => __('Choose condition state.', 'zoner'),
				'id'      => $prefix . 'condition',
				'type'    => 'select',
				'options' => $zoner->property->get_condition_values(),
				'default' => 0,
			),
			
			array(
				'name'    => __('Payment', 'zoner'),
				'subname'    => __('Choose payment interval (only for rent).', 'zoner'),
				'id'      => $prefix . 'payment',
				'type'    => 'select',
				'options' => $zoner->property->get_payment_rent_values(),
				'default' => 0,
			),
			
			array(
				'name'    => __( 'Price', 'zoner' ),
				'subname'    => __( 'Edit price for property.', 'zoner' ),
				'id' 	=> $prefix . 'price',
				'type' => 'text_small'
			),
			
			array(
				'name'    => __('Price format', 'zoner'),
				'subname'    => __('Choose price format.', 'zoner'),
				'id'      => $prefix . 'price_format',
				'type'    => 'select',
				'options' => $zoner->property->get_price_format_values(),
				'default' => 0,
			),
			
			array(
				'name'    => __('Currency', 'zoner'),
				'subname'    => __('Select currency.', 'zoner'),
				'id'      => $prefix . 'currency',
				'type'    => 'select',
				'options' => $zoner->currency->get_zoner_currency_dropdown_settings(),
				'default' => $currency,
			),

			array(
				'name'    => __( 'Rooms', 'zoner' ),
				'subname'    => __( 'Edit count rooms for property.', 'zoner' ),
				'id' 	=> $prefix . 'rooms',
				'type' => 'text_small'
			),
			
			array(
				'name'    => __( 'Beds', 'zoner' ),
				'subname'    => __( 'Edit count beds for property.', 'zoner' ),
				'id' 	=> $prefix . 'beds',
				'type' => 'text_small'
			),
			
			array(
				'name'    => __( 'Baths', 'zoner' ),
				'subname'    => __( 'Edit count baths for property.', 'zoner' ),
				'id' 	=> $prefix . 'baths',
				'type' => 'text_small'
			),
			
			array(
				'name'    => __( 'Area', 'zoner' ),
				'subname'    => __( 'Edit count Area for property.', 'zoner' ),
				'id' 	=> $prefix . 'area',
				'type' => 'text_small'
			),
			
			array(
				'name'    	=> __( 'Area units', 'zoner' ),
				'subname'   => __( 'Edit property area units.', 'zoner' ),
				'id' 		=> $prefix . 'area_unit',
				'type' 		=> 'select',
				'default' 	=> $area_unit,
				'options'	=> $zoner->property->get_area_units_values(),
			),
			
			array(
				'name'    => __( 'Garages', 'zoner' ),
				'subname'    => __( 'Edit count garages for property.', 'zoner' ),
				'id' 	=> $prefix . 'garages',
				'type' => 'text_small'
			),
			
			array(
				'name'    => __( 'Allow user rating', 'zoner' ),
				'id' 	=> $prefix . 'allow_raiting',
				'type' => 'checkbox'
			),
			
			array(
				'name'    => __( 'Property featured', 'zoner' ),
				'id' 	  => $prefix . 'is_featured',
				'type' 	  => 'checkbox'
			),
		)
	);
	
	$meta_boxes[] = array(
		'id'         => 'prop_options',
		'title'      => __( 'Property options', 'zoner' ),
		'pages'      => array( 'property'), 
		'context'    => 'normal',
		'priority'   => 'high',
		'show_names' => true, 
		'fields'     => $property_fields_option
	);
	
	
	$meta_boxes[] = array(
		'id'         => 'prop_files',
		'title'      => __( 'Property Files', 'zoner' ),
		'pages'      => array( 'property'), 
		'context'    => 'normal',
		'priority'   => 'high',
		'show_names' => true, 
		'fields'     => array(
			array(
				'name'         		=> __( 'Files', 'zoner' ),
				'subname'      		=> __( 'Upload or add multiple files.', 'zoner' ),
				'id'            	=> $prefix . 'files',
				'type'         	 	=> 'file_list',
				'save_id' 			=> true
			),
		)
	);
	
	$meta_boxes[] = array(
		'id'         => 'prop_gallery',
		'title'      => __( 'Property images', 'zoner' ),
		'pages'      => array( 'property'), 
		'context'    => 'normal',
		'priority'   => 'high',
		'show_names' => true, 
		'fields'     => array(
			array(
				'name'         		=> __( 'Gallery', 'zoner' ),
				'subname'      		=> __( 'Upload or add multiple images.', 'zoner' ),
				'id'            	=> $prefix . 'gallery',
				'type'         	 	=> 'custom_gallery_list',
				'zoner_show_on' 	=> false, 
				'save_id' => true
			),
		)
	);
	
	$meta_boxes[] = array(
		'id'         => 'prop_plan',
		'title'      => __( 'Floor Plans', 'zoner' ),
		'pages'      => array( 'property'), 
		'context'    => 'normal',
		'priority'   => 'high',
		'show_names' => true, 
		'fields'     => array(
			array(
				'name'         		=> __( 'Gallery', 'zoner' ),
				'subname'      		=> __( 'Upload or add multiple images.', 'zoner' ),
				'id'            	=> $prefix . 'plans',
				'type'         	 	=> 'custom_gallery_list',
				'zoner_show_on' 	=> false, 
				'save_id' 			=> true
			),
		)
	);
	
	$meta_boxes[] = array(
		'id'         => 'prop_video',
		'title'      => __( 'Video Presentation', 'zoner' ),
		'pages'      => array( 'property'), 
		'context'    => 'normal',
		'priority'   => 'high',
		'show_names' => true, 
		'fields'     => array(
			
			array(
				'id'          => $prefix . 'videos',
				'type'        => 'group',
				'description' => __( 'Video Presentations', 'zoner' ),
				'options'     => array(
					'add_button'    => __( 'Add link to video', 'zoner' ),
					'remove_button' => __( 'Remove video', 'zoner' ),
					'sortable'      => true, 
				),
				
				'fields'      => array(
					array(
						'name' => __( 'Video URL', 'zoner' ),
						'id'   => $prefix . 'link_video',
						'type' => 'text_url',
    
					),
				),
			),
		)
	);
	
	
	$meta_boxes[] = array(
		'id'         => 'prop_views',
		'title'      => __( 'Views', 'zoner' ),
		'pages'      => array( 'property'), 
		'context'    => 'side',
		'priority'   => 'low',
		'fields'     => array(
			array(
				'name'    		=> __( 'Count views', 'zoner' ),
				'description'  	=> __( "Please don't edit this field manual.", 'zoner' ),
				'id' 			=> $prefix . 'views',
				'type' 			=> 'text_small',
				'default'		=> 0,
				'value'			=> 0
			),
			
			array(
				'name'    		=> __( 'AVG rating', 'zoner' ),
				'description'  	=> __( "Please don't edit this field manual.", 'zoner' ),
				'id' 			=> $prefix . 'avg_rating',
				'type' 			=> 'text_small',
				'default'		=> -1,
				'value'			=> -1
			),
			
		)
	);
	
	
	$meta_boxes[] = array(
		'id'         => 'prop_is_paid',
		'title'      => __( 'Property payment status', 'zoner' ),
		'pages'      => array( 'property'), 
		'context'    => 'side',
		'priority'   => 'high',
		'fields'     => array(
			array(
				'name'    => __( 'Paid', 'zoner' ),
				'id' 	  => $prefix . 'is_paid',
				'type' 	  => 'checkbox'
			),
			
		)
	);
			
	return apply_filters('zoner_property_metabox_fields', $meta_boxes);
}