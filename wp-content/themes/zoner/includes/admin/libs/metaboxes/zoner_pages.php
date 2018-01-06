<?php
/**
 * Include and setup custom metaboxes and fields.
 *
 * @category Zoner
 * @package  Metaboxes
 * @license  http://www.opensource.org/licenses/gpl-license.php GPL v2.0 (or later)
 * @link     https://github.com/webdevstudios/Custom-Metaboxes-and-Fields-for-WordPress
 */

add_filter( 'cmb_meta_boxes', 'zoner_pages_mtb');
/**
 * Define the metabox and field configurations.
 *
 * @param  array $meta_boxes
 * @return array
 */
function zoner_pages_mtb( array $meta_boxes ) {
	// Start with an underscore to hide fields from custom fields list
	global $zoner_config, $zoner;

	$prefix = '_zoner_';

	$meta_boxes[] = array(
		'id'         => 'pages_layout',
		'title'      => __( 'Layout', 'zoner' ),
		'pages'      => array( 'page'),

		'context'    => 'side',
		'priority'   => 'low',
		'show_names' => true,
		'fields'     => array(
			array(
				'name'    	 => __( 'Page layout', 'zoner' ),
				'id' 		 => $prefix . 'pages_layout',
				'type' 		 => 'custom_layout_sidebars',
				'default'	 => 1
			),

		)
	);


	/*Pages Header variations*/
	$header_var = array();
	$header_var['0']   	= __('Default header', 'zoner');
	$header_var['1']	= __('Map Full Screen', 'zoner');
	$header_var['2']   	= __('Map Fixed Height', 'zoner');
	$header_var['3']   	= __('Map Fixed Navigation', 'zoner');
	$header_var['4']   	= __('Map with Horizontal Search Box', 'zoner');
	$header_var['5']   	= __('Map with Advanced Horizontal Search Box', 'zoner');
	$header_var['11']  	= __('Property Slider', 'zoner');
	$header_var['12']  	= __('Property Slider with Search box', 'zoner');
	$header_var['13']  	= __('Property Slider with Horizontal Search Box', 'zoner');
	$header_var['14']  	= __('Property Slider with Advanced Horizontal Search Box', 'zoner');

	$map_var[0] = __('Google map', 'zoner');
	$map_var[1] = __('Open Street map', 'zoner');
	$revsliders = array();
	$revsliders[0] = __( 'No slider', 'zoner' );

	/*Revolution slider integrate*/
	if ( ! function_exists( 'is_plugin_active' ) )
	require_once( ABSPATH . '/wp-admin/includes/plugin.php' );

	if (function_exists( 'is_plugin_active' )) {
		if (is_plugin_active( 'revslider/revslider.php' ) ) {
			global $wpdb;

			$header_var['15'] = __('Revolution Slider', 'zoner');
			$header_var['16'] = __('Revolution Slider with Search box', 'zoner');
			$header_var['17'] = __('Revolution Slider with Horizontal Search Box', 'zoner');
			$header_var['18'] = __('Revolution Slider with Advanced Horizontal Search Box', 'zoner');

			$rs = $wpdb->get_results("SELECT id, title, alias FROM " . $wpdb->prefix . "revslider_sliders ORDER BY id ASC LIMIT 999");
			if ($rs) {
				foreach ( $rs as $slider ) {
					$revsliders[$slider->alias] = $slider->title;
				}
			}
		}
	}

	/*Custom header variant for all page */

	$page_header_fields[] = array(
		'name'    		=> __('Show header', 'zoner'),
		//'subname'    	=> __('Choose header visibility.', 'zoner'),
		'id'      => $prefix . 'is_header_show',
		'type'    => 'checkbox',
		'default' => 'on',
	);

	$page_header_fields[] = array(
		'name'    		=> __('Show breadcrumb', 'zoner'),
		//'subname'    	=> __('Choose breadcrumb visibility.', 'zoner'),
		'id'      => $prefix . 'is_breadcrumb_show',
		'type'    => 'checkbox',
		'default' => 'on',
	);
	$page_header_fields[] = array(
				'name'    		=> __('Header variations', 'zoner'),
				'subname'    	=> __('Choose header variations.', 'zoner'),
				'id'      => $prefix . 'page_header_variations',
				'type'    => 'select',
				'options' => $header_var,
				'default' => 0,
			);

    $map_var_def = null;
	if (isset($zoner_config['gm-or-osm']) && !empty($zoner_config['gm-or-osm'])) {
	    $map_var_def = $zoner_config['gm-or-osm'];
    }

	if (!empty($_GET['post'])){
		$page_header_variations = get_post_meta($_GET['post'], $prefix . 'page_header_variations', true);
		if (!empty($page_header_variations) && $page_header_variations>=1 && $page_header_variations<=5){
			$map_var_def = 0;//it's Google in old Zoner
		}
		if (!empty($page_header_variations) && $page_header_variations>5 && $page_header_variations<=10){
			$map_var_def = 1;//it's OSM in old Zoner
		}
	}
	$page_header_fields[] = array(
		'name'    		=> __('Header map type', 'zoner'),
		'subname'    	=> __('GoogleMap or OSM.', 'zoner'),
		'id'      => $prefix . 'gm-or-osm',
		'type'    => 'select',
		'options' => $map_var,
		'default' => $map_var_def,
	);

	$page_header_fields[] = array(
				'name'    		=> __('Revolution slider', 'zoner'),
				'subname'   	=> __('Choose revolution slider.', 'zoner'),
				'id'      => $prefix . 'page_header_slider_revolution',
				'type'    => 'select',
				'options' => $revsliders,
	);

	$page_header_fields[] = array(
				'name'    		=> __('Maps Zoom', 'zoner'),
				'subname'   	=> __('Choose zoom on current page for maps.', 'zoner'),
				'id'      => $prefix . 'page_map_zoom',
				'type'    => 'select',
				'options' => array (
									'3'  => '3',
									'4'  => '4',
									'5'  => '5',
									'6'  => '6',
									'7'  => '7',
									'8'  => '8',
									'9'  => '9',
									'10' => '10',
									'11' => '11',
									'12' => '12',
									'13' => '13',
									'14' => '14 - Default',
									'15' => '15',
									'16' => '16',
									'17' => '17',
									'18' => '18',
									'19' => '19',
									'20' => '20',
									'21' => '21',
								  ),
				'default'	=> '14',
				'std'		=> '14',
	);

	$page_header_fields[] = array(
			'name' 		=> __('Use cache for map', 'zoner'),
			'subname' 	=> __('Enable cache for query property markers', 'zoner'),
			'id' 		=> $prefix . 'is_used_map_cache',
			'type' 		=> 'checkbox'
	);

	$geo_center_lat	= '40.7056308';
	$geo_center_lng	= '-73.9780035';
	if (!empty($zoner_config['geo-center-lat']))
	$geo_center_lat  = esc_attr($zoner_config['geo-center-lat']);

	if (!empty($zoner_config['geo-center-lng']))
	$geo_center_lng  = esc_attr($zoner_config['geo-center-lng']);

	$page_header_fields[] = array(
				'name'    		=> __('Latitude', 'zoner'),
				'subname'   	=> __('Set latitude coordinates for map on current page.', 'zoner'),
				'id'      		=> $prefix . 'page_map_latitude',
				'type' 	  		=> 'text_medium',
				'default'		=> $geo_center_lat
	);

	$page_header_fields[] = array(
				'name'    		=> __('Longitude', 'zoner'),
				'subname'   	=> __('Set longitude coordinates for map on current page.', 'zoner'),
				'id'      		=> $prefix . 'page_map_longitude',
				'type' 	  		=> 'text_medium',
				'default'		=> $geo_center_lng
	);

	$page_header_fields[] = 	array(
				'name' 		=> __('Property status', 'zoner'),
				'subname' 	=> __('Select a taxonomy to filter property on the map', 'zoner'),
				'id' 		=> $prefix . 'page_tax_status',
				'taxonomy' 	=> 'property_status',
				'type' 		=> 'taxonomy_multicheck',
				'default'	=> null,
	);

	$page_header_fields[] = 	array(
				'name' 		=> __('Property type', 'zoner'),
				'subname' 	=> __('Select a taxonomy to filter property on the map', 'zoner'),
				'id' 		=> $prefix . 'page_tax_type',
				'taxonomy' 	=> 'property_type',
				'type' 		=> 'taxonomy_multicheck',
				'default'	=> null,
	);

	$page_header_fields[] = 	array(
				'name' 		=> __('Property features', 'zoner'),
				'subname' 	=> __('Select a taxonomy to filter property on the map', 'zoner'),
				'id' 		=> $prefix . 'page_tax_features',
				'taxonomy' 	=> 'property_features',
				'type' 		=> 'taxonomy_multicheck',
				'default'	=> null,
	);

	$page_header_fields[] = 	array(
				'name' 		=> __('Property categories', 'zoner'),
				'subname' 	=> __('Select a category to filter property on the map', 'zoner'),
				'id' 		=> $prefix . 'page_tax_cat',
				'taxonomy' 	=> 'property_cat',
				'type' 		=> 'taxonomy_multicheck',
				'default'	=> null,
	);

	$page_header_fields[] = 	array(
				'name' 		=> __('Property cities', 'zoner'),
				'subname' 	=> __('Select a city to filter property on the map', 'zoner'),
				'id' 		=> $prefix . 'page_tax_city',
				'taxonomy' 	=> 'property_city',
				'type' 		=> 'taxonomy_multicheck',
				'default'	=> null,
	);

	$meta_boxes[] = array(
		'id'         => 'header_options',
		'title'      => __( 'Header options', 'zoner' ),
		'pages'      => array( 'page'),
		'show_on' 	 => array( 'key' => 'front-page', 'value' => '' ),
		'context'    => 'normal',
		'priority'   => 'low',
		'show_names' => true,
		'fields'     => $page_header_fields
	);


	return $meta_boxes;
}
