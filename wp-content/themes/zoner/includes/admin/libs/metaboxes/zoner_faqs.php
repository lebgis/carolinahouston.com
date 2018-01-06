<?php
/**
 * Include and setup custom metaboxes and fields.
 *
 * @category Zoner
 * @package  Metaboxes
 * @license  http://www.opensource.org/licenses/gpl-license.php GPL v2.0 (or later)
 * @link     https://github.com/webdevstudios/Custom-Metaboxes-and-Fields-for-WordPress
 */

add_filter( 'cmb_meta_boxes', 'zoner_faqs_mtb');
/**
 * Define the metabox and field configurations.
 *
 * @param  array $meta_boxes
 * @return array
 */
function zoner_faqs_mtb( array $meta_boxes ) {
	// Start with an underscore to hide fields from custom fields list
	$prefix = '_zoner_';
	
	$meta_boxes[] = array(
		'id'         => 'faq_answer',
		'title'      => __( 'Was this answer helpful:', 'zoner' ),
		'pages'      => array( 'faq'), 
		'context'    => 'side',
		'priority'   => 'low',
		'show_names' => true, 
		'fields'     => array(
			array(
				'name' => __('Yes', 'zoner'),
				'default' => '0',
				'id' => $prefix . 'faq_helpful_yes',
				'type' => 'text_medium'
			),
			
			array(
				'name' => __('No', 'zoner'),
				'default' => '0',
				'id' => $prefix . 'faq_helpful_no',
				'type' => 'text_medium'
			),
			
		)
	);
	
	
	return $meta_boxes;
}	