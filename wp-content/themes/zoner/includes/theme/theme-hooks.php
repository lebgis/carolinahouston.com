<?php
	//add_action( 'init', 'zoner_prevent_admin_access', 0 );
	
	add_action( 'after_setup_theme',  'zoner_setup' );
	add_filter( 'nav_menu_css_class', 'zoner_nav_parent_class', 10, 2 );
	add_filter( 'nav_menu_css_class', 'zoner_add_parent_url_menu_class', 10, 3 );
	
	/*Remove Admin Bar*/
	add_action('init', 'zoner_options_admin_bar');
	
	/*Main Content Part*/
	add_action('zoner_before_content', 'zoner_before_content');
	add_action('zoner_after_content',  'zoner_after_content');
	add_action('the_main_content', 'zoner_the_main_content');
	add_action('property_loop', 'zoner_property_loop');
	
	add_filter( 'page_css_class', 'zoner_add_page_parent_class', 10, 4 );
	add_action( 'customize_register', 'zoner_customize_register' );
	add_action( 'template_redirect', 'zoner_content_width' );
	add_action( 'wp_enqueue_scripts', 'zoner_scripts', 10 );
	add_action( 'wp_enqueue_scripts', 'zoner_property_scripts', 20 );
	
	add_filter( 'body_class', 'zoner_body_classes' );
	add_filter( 'post_class', 'zoner_post_classes' );
	add_filter( 'wp_title', 'zoner_wp_title', 10, 2 );
	add_filter( 'get_search_form', 'zoner_search_form' );
	add_filter( 'excerpt_more', 'zoner_change_excerpt_more');
	add_filter( 'excerpt_length', 'zoner_set_excerpt_length', 999 );	
	add_filter( 'the_content_more_link', 'zoner_modify_read_more_link' );
	add_filter( 'edit_post_link', 'zoner_edit_post_link');
	
	add_action( 'zoner_comments_template', 'zoner_visibilty_comments');
	add_filter( 'the_password_form', 'zoner_password_protect_form' );
	add_filter( 'the_content', 'zoner_post_chat', 99);
	add_action( 'wp_head', 'zoner_add_google_analytics', 99);
	add_action( 'wp_head', 'zoner_add_favicon', 100);
	add_filter( 'img_caption_shortcode', 'zoner_img_caption', 10, 3 );
	
	add_filter('pre_get_posts','zoner_ExludeSearchFilter');
	
	remove_shortcode('gallery');
	add_shortcode('gallery', 'zoner_gallery_shortcode');
	
	add_action( 'zoner_compare_content', 'zoner_compare_content', 99);
	
	
	/*Profile*/
	add_action('after_setup_theme', 'zoner_remove_admin_bar');
	add_action('wp', 'zoner_process_save_profile', 300);
	
	add_filter('query_vars', 'zoner_add_query_var');
	add_action('init', 'zoner_add_rewrite_rules', 10);
	
	add_filter('manage_users_columns' , 'zoner_add_extra_user_column');
	add_filter('manage_users_custom_column', 'zoner_add_extra_user_columns_values', 10, 3 );
	
	add_action( 'deleted_user', 'zoner_deleted_user_action' ); 
	
	/*Footer*/
	add_action('zoner_footer_elements', 'zoner_get_footer_area_sidebars', 1);
	add_action('zoner_footer_elements', 'zoner_get_footer_area_thumbnails', 2);
	add_action('zoner_footer_elements', 'zoner_get_social', 3);
	
	
	/*Property*/
	add_action ('wp',  'zoner_edit_property',   300);
	add_action ('wp',  'zoner_insert_property', 300);
	
	add_action ('wp',  'zoner_edit_agency',   300);
	add_action ('wp',  'zoner_insert_agency', 300);
	
	add_action( 'wp_ajax_change_code_currency', 'zoner_change_code_currency' );
	add_action( 'wp_ajax_nopriv_change_code_currency', 'zoner_change_code_currency' );
	
	/*Get input video html*/
	add_action( 'wp_ajax_get_input_video', 'zoner_get_input_videos' );
	add_action( 'wp_ajax_nopriv_get_input_video', 'zoner_get_input_videos' );
	
	/*Get Print Property Page*/
	add_action( 'wp_ajax_zoner_print_property', 'zoner_get_print_property_html' );
	add_action( 'wp_ajax_nopriv_zoner_print_property', 'zoner_get_print_property_html' );
	
	/*Delete Agency*/
	add_action( 'wp_ajax_delete_agency_act', 'zoner_delete_agency_act' );
	add_action( 'wp_ajax_nopriv_delete_agency_act', 'zoner_delete_agency_act' );
	
	/*Delete Property*/
	add_action( 'wp_ajax_delete_property_act', 'zoner_delete_property_act' );
	add_action( 'wp_ajax_nopriv_delete_property_act', 'zoner_delete_property_act' );
	
	add_action( 'wp_ajax_zoner_check_user_password', 'zoner_check_user_password_act' );
	add_action( 'wp_ajax_nopriv_zoner_check_user_password', 'zoner_check_user_password_act' );
	
	add_action( 'wp_ajax_delete_invite_agent', 'delete_invite_agent_act' );
	add_action( 'wp_ajax_nopriv_delete_invite_agent', 'delete_invite_agent_act' );
	
	add_action( 'wp', 'zoner_exist_user_invited', 575);
	
	add_action( 'wp_ajax_get_states_by_country', 'zoner_get_states_by_country' );
	add_action( 'wp_ajax_nopriv_get_states_by_country', 'zoner_get_states_by_country' );
	
	add_action( 'wp', 'set_property_comment_with_rating', 300);
	add_action( 'wp', 'zoner_change_user_pass_act', 300);
	
	/*Faq Vote*/
	add_action( 'wp_ajax_zoner_helpful_faq', 'zoner_helpful_faq_act' );
	add_action( 'wp_ajax_nopriv_zoner_helpful_faq', 'zoner_helpful_faq_act' );

	/*Map multichoise actions*/
	add_action( 'wp_ajax_zoner_get_multiitems', 'zoner_get_multiitems' );
	add_action( 'wp_ajax_nopriv_zoner_get_multiitems', 'zoner_get_multiitems' );

	/*Connection */
    add_action('wp', 'zoner_signin_facebook_connect', 315);
    add_action('wp', 'zoner_signin_google_connect', 315);