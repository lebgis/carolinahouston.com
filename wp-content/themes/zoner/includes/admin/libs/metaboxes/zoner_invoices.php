<?php
/**
 * Include and setup custom metaboxes and fields.
 *
 * @category Zoner
 * @package  Metaboxes
 * @license  http://www.opensource.org/licenses/gpl-license.php GPL v2.0 (or later)
 * @link     https://github.com/webdevstudios/Custom-Metaboxes-and-Fields-for-WordPress
 */

add_filter( 'cmb_meta_boxes', 'zoner_invoices_mtb');
/**
 * Define the metabox and field configurations.
 *
 * @param  array $meta_boxes
 * @return array
 */
function zoner_invoices_mtb( array $meta_boxes ) {
	// Start with an underscore to hide fields from custom fields list
	global $zoner_config, $zoner;
	
	$prefix = '_zoner_';
	
	 $meta_boxes[] = array(
		 'id'         => 'invoice_detail',
		 'title'      => __( 'Invoice detail', 'zoner' ),
		 'pages'      => array( 'invoices'), 
		 'context'    => 'normal',
		 'priority'   => 'high',
		 'show_names' => true, 
		 'fields'     => array(
								
								array(
									'name'    => __( 'Paid System', 'zoner' ),
									'id'      => $prefix . 'invoice_paymnent_system',
									'type'    => 'text_medium',
									'default' => ''
								),
								
								array(
									'name'    => __( 'Transaction #', 'zoner' ),
									'id'      => $prefix . 'invoice_transaction_id',
									'type'    => 'text_medium',
									'default' => ''
								),
								
								array(
									'name'    => __( 'Details of payment', 'zoner' ),								
									'id'      => $prefix . 'invoice_detail_of_payment',
									'type'    => 'select',
									'options' => $zoner->membership->get_paid_details_of_payment_values(),
									'default' => -1
								),
											
								array(
									'name'  => __( 'Payment recurring', 'zoner' ),
									'id' 	=> $prefix . 'invoice_payment_recurring',
									'type' 	=> 'checkbox'
								),
								
								array(
									'name'    => __( 'Package ID', 'zoner' ),
									'id' 	=> $prefix . 'invoice_package_id',
									'type' => 'text_medium'
								),
								
								array(
									'name'    => __( 'Property ID', 'zoner' ),
									'id' 	=> $prefix . 'invoice_property_id',
									'type' => 'text_medium'
								),
								
								array(
									'name'    => __( 'Payment price', 'zoner' ),
									'id' 	=> $prefix . 'invoice_payment_price',
									'type' => 'text_medium'
								),
								
								array(
									'name'    => __( 'Payment currency', 'zoner' ),
									'id' 	=> $prefix . 'invoice_payment_currency',
									'type' => 'text_medium'
								),
								
								array(
									'name'    => __( 'Purchase date', 'zoner' ),
									'id' 	=> $prefix . 'invoice_purchase_date',
									'type' => 'text_medium'
								),
								
								array(
									'name'    => __( 'User ID', 'zoner' ),
									'id' 	=> $prefix . 'invoice_user_id',
									'type' => 'text_medium'
								),

								array(
									'name'    	=> __( 'User Information', 'zoner' ),
									'id' 		=> $prefix . 'invoice_user_info',
									'type' 		=> 'textarea_code',
									'default'	=> ''
								),
								
		 )
	);
	
	return $meta_boxes;
}	