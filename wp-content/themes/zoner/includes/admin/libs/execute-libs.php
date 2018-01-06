<?php

/*VC active*/
if (class_exists('WPBakeryVisualComposerAbstract')) {
 // if(function_exists('vc_set_as_theme')) vc_set_as_theme(true);
	if(function_exists('vc_disable_frontend')) vc_disable_frontend(true);
	
	$vc_templates_dir = get_template_directory() . '/includes/admin/libs/theme-shortcodes/zoner-shortcodes/vc_templates/';
	vc_set_shortcodes_templates_dir($vc_templates_dir);
	
	/*Add Custom Shortcodes*/
	include_once('theme-shortcodes/zoner-shortcodes/zoner_shortcodes.php');
	include_once('theme-shortcodes/execute-shortcodes/execute-shortcodes.php');
	
	if (is_admin()) :
		function remove_vc_teaser() {
			remove_meta_box('vc_teaser', '' , 'side');
		}
		add_action( 'admin_head', 'remove_vc_teaser' );
	endif;
}


//Admin area shortcodes
function zoner_add_admin_styles() {
    if ( is_admin() ) {
		if (class_exists('WPBakeryVisualComposerAbstract')) { 
			wp_enqueue_style('zoner-visual-composer', get_template_directory_uri() .'/includes/admin/libs/theme-shortcodes/execute-shortcodes/execute-shortcodes.css', false, null, 'all');
		}
    }
}
add_action( 'admin_enqueue_scripts', 'zoner_add_admin_styles' );


/*Metaboxes activation*/
add_action( 'init', 'zoner_initialize_cmb_meta_boxes', 9999  );
function zoner_initialize_cmb_meta_boxes() {
	if ( ! class_exists( 'cmb_Meta_Box' ) ) require_once dirname(__FILE__) . '/metaboxes/init.php';
}

require dirname(__FILE__) . '/metaboxes/zoner_fields_for_metaboxes.php';
require dirname(__FILE__) . '/metaboxes/zoner_invoices.php';
require dirname(__FILE__) . '/metaboxes/zoner_membership.php';
require dirname(__FILE__) . '/metaboxes/zoner_properties.php';
require dirname(__FILE__) . '/metaboxes/zoner_agencies.php';
require dirname(__FILE__) . '/metaboxes/zoner_pages.php';
require dirname(__FILE__) . '/metaboxes/zoner_faqs.php';
require dirname(__FILE__) . '/metaboxes/zoner_users.php';

/*Tgm activation*/
require_once dirname(__FILE__) . '/tgm/class-tgm-init.php';
