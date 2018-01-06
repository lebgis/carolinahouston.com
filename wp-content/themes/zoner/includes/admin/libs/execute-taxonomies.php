<?php
	/*Add custom taxonomies*/
	add_action('init', 'zoner_register_ct', 0);
	function zoner_register_ct() {
		register_taxonomy( 'property_status',array ( 0 => 'property', ),
					   array( 	'hierarchical' => true,
								'label' => __('Property Status', 'zoner'),
								'show_ui' 	=> true,
								'query_var' => true,
								'show_admin_column' => false,
								'rewrite' => array(
										'slug' => 'status', 
										'with_front' => false 
								),
								'labels' => array (
									'search_items' 	=> __('Property Status', 'zoner'),
									'popular_items' => __('Popular Property Status', 'zoner'),
									'all_items' 	=> __('All Status', 'zoner'),
									'parent_item' 	=> __('Parent Status', 'zoner'),
									'parent_item_colon' => '',
									'edit_item' 	=> __('Edit Status', 'zoner'),
									'update_item' 	=> __('Update Status', 'zoner'),
									'add_new_item' 	=> __('Add Status', 'zoner'),
									'new_item_name' => '',
									'separate_items_with_commas' => '',
									'add_or_remove_items' => '',
									'choose_from_most_used' => ''
								)
							) 	
		); 
	
		register_taxonomy( 'property_type',array ( 0 => 'property', ),
					   array( 	'hierarchical' => true,
								'label' => __('Property Type', 'zoner'),
								'show_ui' 	=> true,
								'query_var' => true,
								'show_admin_column' => false,
								'rewrite' => array(
										'slug' => 'type', 
										'with_front' => false 
								),
								'labels' => array (
									'search_items'  => __('Property Type', 'zoner'),
									'popular_items' => __('Popular Type', 'zoner'),
									'all_items' 	=> __('All Types', 'zoner'),
									'parent_item' 	=> __('Parent Type', 'zoner'),
									'parent_item_colon' => '',
									'edit_item' 	=> __('Edit Type', 'zoner'),
									'update_item' 	=> __('Update Type', 'zoner'),
									'add_new_item' 	=> __('Add Type', 'zoner'),
									'new_item_name' => '',
									'separate_items_with_commas' => '',
									'add_or_remove_items' 		 => '',
									'choose_from_most_used' 	 => ''
								)
							) 	
		); 
	
		register_taxonomy( 'property_features',array ( 0 => 'property', ),
					   array( 	'hierarchical' => true,
								'label' => __('Property Features', 'zoner'),
								'show_ui' 	=> true,
								'query_var' => true,
								'show_admin_column' => false,
								'rewrite' => array(
										'slug' => 'features', 
										'with_front' => false 
								),
								'labels' => array (
									'search_items' 	=> __('Property Features', 'zoner'),
									'popular_items' => __('Popular Features', 'zoner'),
									'all_items' 	=> __('All Features', 'zoner'),
									'parent_item' 	=> __('Parent Features', 'zoner'),
									'parent_item_colon' => '',
									'edit_item' 	=> __('Edit Feature', 'zoner'),
									'update_item' 	=> __('Update Feature', 'zoner'),
									'add_new_item' 	=> __('Add Feature', 'zoner'),
									'new_item_name' => '',
									'separate_items_with_commas' => '',
									'add_or_remove_items' => '',
									'choose_from_most_used' => ''
								)
							) 	
		); 
		
		register_taxonomy( 'property_city',array ( 0 => 'property', ),
					   array( 	'hierarchical' => true,
								'label' => __('Town / City', 'zoner'),
								'show_ui' 	=> true,
								'query_var' => true,
								'rewrite' => array(
										'slug' => 'city', 
										'with_front' => false 
								),
								'show_admin_column' => false,
								'labels' => array (
									'name' 				=> __( 'Town / City', 'zoner' ),
									'singular_name' 	=> __( 'Town / City', 'zoner' ),
									'menu_name'			=> _x( 'Town / City', 'Town / City', 'zoner' ),
									'search_items' 		=> __( 'Search City', 'zoner' ),
									'all_items' 		=> __( 'All Cities', 'zoner' ),
									'parent_item' 		=> __( 'Parent City', 'zoner' ),
									'parent_item_colon' => __( 'Parent City:', 'zoner' ),
									'edit_item' 		=> __( 'Edit City', 'zoner' ),
									'update_item' 		=> __( 'Update City', 'zoner' ),
									'add_new_item' 		=> __( 'Add New City', 'zoner' ),
									'new_item_name' 	=> __( 'New City Name', 'zoner' )
								)
							) 	
		); 
		
		register_taxonomy( 'property_cat',array ( 0 => 'property', ),
					   array( 	'hierarchical' => true,
								'label' => __('Categories', 'zoner'),
								'show_ui' 	=> true,
								'query_var' => true,
								'rewrite' => array(
										'slug' => 'propertycat', 
										'with_front' => false 
								),
								'show_admin_column' => false,
								'labels' => array (
									'name' 				=> __( 'Property Categories', 'zoner' ),
									'singular_name' 	=> __( 'Property Category', 'zoner' ),
									'menu_name'			=> _x( 'Categories', 'Categories', 'zoner' ),
									'search_items' 		=> __( 'Search Property Categories', 'zoner' ),
									'all_items' 		=> __( 'All Property Categories', 'zoner' ),
									'parent_item' 		=> __( 'Parent Property Category', 'zoner' ),
									'parent_item_colon' => __( 'Parent Property Category:', 'zoner' ),
									'edit_item' 		=> __( 'Edit Property Category', 'zoner' ),
									'update_item' 		=> __( 'Update Property Category', 'zoner' ),
									'add_new_item' 		=> __( 'Add New Property Category', 'zoner' ),
									'new_item_name' 	=> __( 'New Property Category Name', 'zoner' )
								)
							) 	
		); 
	
		register_taxonomy( 'property_tag', array ( 0 => 'property', ),
							array( 	'hierarchical' => false,
									'label' => __('Tags', 'zoner'),
									'show_ui' 	=> true,
									'query_var' => true,
									'rewrite' => array(
											'slug' => 'propertytag',
											'with_front' => false 
									),
									'show_admin_column' => false,
									'labels' => array (
										'name' 				=> __( 'Property Tags', 'zoner' ),
										'singular_name' 	=> __( 'Property Tag', 'zoner' ),
										'menu_name'			=> _x( 'Tags', 'Admin menu name', 'zoner' ),
										'search_items' 		=> __( 'Search Property Tags', 'zoner' ),
										'all_items' 		=> __( 'All Property Tags', 'zoner' ),
										'parent_item' 		=> __( 'Parent Property Tag', 'zoner' ),
										'parent_item_colon' => __( 'Parent Property Tag:', 'zoner' ),
										'edit_item' 		=> __( 'Edit Property Tag', 'zoner' ),
										'update_item' 		=> __( 'Update Property Tag', 'zoner' ),
										'add_new_item' 		=> __( 'Add New Property Tag', 'zoner' ),
										'new_item_name' 	=> __( 'New Property Tag Name', 'zoner' )
									)
								) 	
		); 
		
		register_taxonomy( 'faq_tax', array ( 0 => 'faq' ),
						   array( 	'hierarchical' => true,
									'label' => __("FAQ's taxonomy", 'zoner'),
									'show_ui' 	=> true,
									'query_var' => true,
									'show_admin_column' => false,
									'labels' => array (
										'search_items' 	=> __("Faq's", 'zoner'),
										'popular_items' => __("Popular faq's taxonomy", 'zoner'),
										'all_items' 	=> __("All faq's taxonomy", 'zoner'),
										'parent_item' 	=> __("Parent faq's taxonomy", 'zoner'),
										'parent_item_colon' => '',
										'edit_item' 	=> __("Edit faq's taxonomy", 'zoner'),
										'update_item' 	=> __("Update faq's taxonomy", 'zoner'),
										'add_new_item' 	=> __("Add faq's taxonomy", 'zoner'),
										'new_item_name' => '',
										'separate_items_with_commas' => '',
										'add_or_remove_items' 		 => '',
										'choose_from_most_used' 	 => '',
									)
								) 	
		); 
		
		register_taxonomy( 'timeline_tax',array ( 0 => 'timeline' ),
						   array( 	'hierarchical' => true,
									'label' => __("Timeline taxonomy", 'zoner'),
									'show_ui' 	=> true,
									'query_var' => true,
									'show_admin_column' => false,
									'labels' => array (
										'search_items' 	=> __("Timeline", 'zoner'),
										'popular_items' => __("Popular timeline taxonomy", 'zoner'),
										'all_items' 	=> __("All timeline taxonomy", 'zoner'),
										'parent_item' 	=> __("Parent timeline taxonomy", 'zoner'),
										'parent_item_colon' => '',
										'edit_item' 	=> __("Edit timeline taxonomy", 'zoner'),
										'update_item' 	=> __("Update timeline taxonomy", 'zoner'),
										'add_new_item' 	=> __("Add timeline taxonomy", 'zoner'),
										'new_item_name' => '',
										'separate_items_with_commas' => '',
										'add_or_remove_items' 		 => '',
										'choose_from_most_used' 	 => '',
									)
								) 	
		); 
		
	}