<?php 

/**
 * Include and setup custom metaboxes and fields.
 *
 * @category Zoner
 * @package  Metaboxes
 * @license  http://www.opensource.org/licenses/gpl-license.php GPL v2.0 (or later)
 * @link     https://github.com/webdevstudios/Custom-Metaboxes-and-Fields-for-WordPress
 */

add_filter( 'cmb_meta_boxes', 'zoner_agency_mtb');
/**
 * Define the metabox and field configurations.
 *
 * @param  array $meta_boxes
 * @return array
 */
function zoner_agency_mtb( array $meta_boxes ) {
	// Start with an underscore to hide fields from custom fields list
	$prefix = '_zoner_';
	global $zoner_config;
	
	$meta_boxes[] = array(
		'id'            => 'agency_edit',
		'title'         => __( 'Agency information', 'zoner' ),
		'pages'         => array( 'agency' ), 
		'show_names'    => true,
		'zoner_styles' 	=> true, 
		'class'			=> 'agencies',
		'fields'        => array(
		
		array(
			'name' => __('Address', 'zoner'),
			'subname' => __('Edit address for current agency.', 'zoner'),
			'id' => $prefix . 'agency_address',
			'type' => 'textarea'
		),
		
		array(
			'name' => __( 'Google Map URL', 'zoner' ),
			'id'   => $prefix . 'agency_googlemapurl',
			'type' => 'text_url',
		),
		
		array(
			'name' => __( 'Email', 'zoner' ),
			'id'   => $prefix . 'agency_email',
			'type' => 'text_email',
		),
		
		array(
			'name' => __( 'Facebook URL', 'zoner' ),
			'id'   => $prefix . 'agency_facebookurl',
			'type' => 'text_url',
		),
			
		array(
			'name' => __( 'Twitter URL', 'zoner' ),
			'id'   => $prefix . 'agency_twitterurl',
			'type' => 'text_url',
		),
			
		array(
			'name' => __( 'Google+ URL', 'zoner' ),
			'id'   => $prefix . 'agency_googleplusurl',
			'type' => 'text_url',
		),
			
		array(
			'name' => __( 'Linkedin URL', 'zoner' ),
			'id'   => $prefix . 'agency_linkedinurl',
			'type' => 'text_url',
		),
			
		array(
			'name' => __( 'Pinterest URL', 'zoner' ),
			'id'   => $prefix . 'agency_pinteresturl',
			'type' => 'text_url',
		),
			
		array(
			'name' => __( 'Instagram URL', 'zoner' ),
			'id'   => $prefix . 'agency_instagramurl',
			'type' => 'text_url',
		),
		
		array(
			'name' => __( 'Phone', 'zoner' ),
			'id'   => $prefix . 'agency_tel',
			'type' => 'text_medium',
		),
			
		array(
			'name' => __( 'Mobile', 'zoner' ),
			'id'   => $prefix . 'agency_mob',
			'type' => 'text_medium',
		),
			
		array(
			'name' => __( 'Skype', 'zoner' ),
			'id'   => $prefix . 'agency_skype',
			'type' => 'text_medium',
		),
			
		)
	);
	
	$meta_boxes[] = array(
		'id'            => 'agency_additional_fields',
		'title'         => __( 'Small Fetaured Image', 'zoner' ),
		'pages'         => array( 'agency' ), 
		'show_names'    => true,
		'zoner_styles' 	=> true, 
		'class'			=> 'agencies',
		'context'		=> 'side',
		'priority'		=> 'low',
		'fields'        => array(
			array(
				'id'      => $prefix . 'agency_line_img',
				'type'    => 'file',
				'save_id' => true,
				'allow'   => array( 'url', 'attachment' )
			),
		)
	);	
	
		
	return $meta_boxes;
}	