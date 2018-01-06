<?php
	/*Add custom post type*/
	DEFINE('POST_TYPES_ICONS_URL', get_template_directory_uri() .'/includes/images/');
  
	add_action('init', 'zoner_register_cp',2);
	function zoner_register_cp() {
		global $zoner;
		
		/*Properties*/
		$property_loop_page = $zoner->zoner_get_page_id('page-property-archive');
		register_post_type( 'property',
			apply_filters( 'zoner_register_post_type_property',
				array(
					'labels'              => array(
							'name'               => __( 'Properties', 'zoner' ),
							'singular_name'      => __( 'Property', 'zoner' ),
							'menu_name'          => _x( 'Properties', 'Admin menu name', 'zoner' ),
							'add_new'            => __( 'Add Property', 'zoner' ),
							'add_new_item'       => __( 'Add New Property', 'zoner' ),
							'edit'               => __( 'Edit', 'zoner' ),
							'edit_item'          => __( 'Edit Property', 'zoner' ),
							'new_item'           => __( 'New Property', 'zoner' ),
							'view'               => __( 'View Property', 'zoner' ),
							'view_item'          => __( 'View Property', 'zoner' ),
							'search_items'       => __( 'Search Property', 'zoner' ),
							'not_found'          => __( 'No Property found', 'zoner' ),
							'not_found_in_trash' => __( 'No Property found in trash', 'zoner' ),
							'parent'             => __( 'Parent Property', 'zoner' )
						),
					'description'         => __( 'This is where you can add new property to your site.', 'zoner' ),
					'public'              => true,
					'show_ui'             => true,
					'capability_type'     => 'property',
					'map_meta_cap'        => true,
					'publicly_queryable'  => true,
					'menu_icon'			  => POST_TYPES_ICONS_URL . 'property.png',
					'exclude_from_search' => false,
					'hierarchical'        => false, // Hierarchical causes memory issues - WP loads all records!
					'rewrite' 	  => true,
					'query_var'   => true,
					'supports' 	  => array('title', 'editor', 'thumbnail', 'comments', 'author'),
					'has_archive' => ( $property_loop_page ) && get_post( $property_loop_page ) ? get_page_uri( $property_loop_page ) : __('properties', 'zoner'),
					'show_in_nav_menus'   => true
				)
			)
		);
		
		/*Invoice*/
		register_post_type('invoices', array(
				'label' 		    => __('Invoices', 'zoner'),
				'description' 		=> __('Add invoice', 'zoner'),
				'publicly_queryable' 	=> false,
				'public'  			 	=> true,
				'show_ui' 			 	=> true,
				'show_in_menu' 		 	=> true,
				'show_in_nav_menus'	 	=> false,
				'hierarchical' 		 	=> false,
				'exclude_from_search' 	=> true,
				'can_export' 		=> true,
				'menu_position' 	=> null,
				'menu_icon'			=> POST_TYPES_ICONS_URL . 'invoice.png',
				'query_var'   => true,
				'supports' 	  => array('title', 'author'),
				'rewrite' 	  => array('slug' => 'invoices'),
				'has_archive' => false,
					'labels' => array (
										'name' 			=> __('Invoices',  'zoner'),
										'singular_name' => __('Invoice',  'zoner'),
										'menu_name' 	=> __('Invoice',  'zoner'),
										'add_new' 		=> __('Add New Invoice',  'zoner'),
										'add_new_item' 	=> __('Add New Invoice',  'zoner'),
										'edit' 			=> __('Edit',  'zoner'),
										'edit_item' 	=> __('Edit invoice',  'zoner'),
										'new_item' 		=> __('New invoice',   'zoner'),
										'view' 			=> __('View invoice',  'zoner'),
										'view_item' 	=> __('View invoice',  'zoner'),
										'search_items' 	=> __('Search invoice',  'zoner'),
										'not_found' 	=> __('No invoice Found',  'zoner'),
										'not_found_in_trash' => __('No invoice Found in Trash',  'zoner'),
										'parent' 		=> __('Parent invoice',  'zoner')
					)
					
			) 
		); 
		
		/*Membership*/
		register_post_type('packages', array(
				'label' 		    => __('Membership', 'zoner'),
				'description' 		=> __('Add membership package', 'zoner'),
				'public'  			=> true,
				'show_ui' 			=> true,
				'show_in_menu' 		=> true,
				'map_meta_cap' 		=> true,
				'hierarchical' 		=> false,
				'exclude_from_search' => true,
				'menu_position' 	=> null,
				'query_var'   		=> true,
				'has_archive' 		=> false,
				'menu_icon'			=> POST_TYPES_ICONS_URL . 'membership.png',
				'supports' 	  => array('title', 'author'),
				'rewrite' 	  => array('slug' => 'packages'),
				'labels' 	  => array (
										'name' 			=> __('Membership packages',  'zoner'),
										'singular_name' => __('Membership package',  'zoner'),
										'menu_name' 	=> __('Membership',  'zoner'),
										'add_new' 		=> __('Add New package',  'zoner'),
										'add_new_item' 	=> __('Add New package',  'zoner'),
										'edit' 			=> __('Edit',  'zoner'),
										'edit_item' 	=> __('Edit package',  'zoner'),
										'new_item' 		=> __('New package',   'zoner'),
										'view' 			=> __('View package',  'zoner'),
										'view_item' 	=> __('View package',  'zoner'),
										'search_items' 	=> __('Search package',  'zoner'),
										'not_found' 	=> __('No membership package Found',  'zoner'),
										'not_found_in_trash' => __('No membership package Found in Trash',  'zoner'),
										'parent' 		=> __('Parent package',  'zoner')
					)
					
			) 
		); 
		
		
		/*Agencies*/
		register_post_type('agency', array(
				'label' 		    => __('Agency', 'zoner'),
				'description' 		=> __('Add agency items', 'zoner'),
				'publicly_queryable' => true,
				'public'  			 => true,
				'_builtin' 			 => false,
				'show_ui' 			 => true,
				'show_in_menu' 		 => true,
				'show_in_nav_menus'	 => true,
				'capability_type'    => 'post',
				'map_meta_cap' 		 => true,
				'hierarchical' 		 => false,
				'exclude_from_search' => true,
				'menu_position' 	=> null,
				'menu_icon'			=> POST_TYPES_ICONS_URL . 'agency.png',
				'rewrite' 	  => array('slug' => 'agency', 'with_front' => false, 'feeds' => true),
				'query_var'   => true,
				'supports' 	  => array('title', 'editor','thumbnail', 'author'),
				'has_archive' => true,
					'labels' => array (
										'name' 			=> __('Agencies',  'zoner'),
										'singular_name' => __('Agency',  'zoner'),
										'menu_name' 	=> __('Agencies',  'zoner'),
										'add_new' 		=> __('Add New Agency',  'zoner'),
										'add_new_item' 	=> __('Add New Agency',  'zoner'),
										'edit' 			=> __('Edit',  'zoner'),
										'edit_item' 	=> __('Edit Agency',  'zoner'),
										'new_item' 		=> __('New Agency',   'zoner'),
										'view' 			=> __('View Agency',  'zoner'),
										'view_item' 	=> __('View Agency',  'zoner'),
										'search_items' 	=> __('Search Agency',  'zoner'),
										'not_found' 	=> __('No Agency Found',  'zoner'),
										'not_found_in_trash' => __('No Agency Found in Trash',  'zoner'),
										'parent' 		=> __('Parent Agency',  'zoner')
					)
					
			) 
		); 
	
		/*Faq's*/
		register_post_type('faq', array(
				'label' 		    => __('FAQ',  'zoner'),
				'description' 		=> __('Add Question and Answers',  'zoner'),
				'public'  			=> true,
				'_builtin' 			=> false,
				'show_ui' 			=> true,
				'show_in_menu' 		=> true,
				'map_meta_cap' 		=> true,
				'hierarchical' 		=> false,
				'exclude_from_search' => true,
				'menu_position' 	=> null,
				'menu_icon'			=> POST_TYPES_ICONS_URL . 'faq.png',
				'rewrite' 	 => array('slug' => 'faq', 'with_front' => false, 'feeds' => true),
				'query_var'  => true,
				'supports' 	 => array('title','editor'),
					'labels' => array (
										'name' 			=> __("FAQ's",  'zoner'),
										'singular_name' => __('FAQ',  'zoner'),
										'menu_name' 	=> __("FAQ's",  'zoner'),
										'add_new' 		=> __('Add New Question',  'zoner'),
										'add_new_item' 	=> __('Add New Question',  'zoner'),
										'edit' 			=> __('Edit',  'zoner'),
										'edit_item' 	=> __('Edit Question',  'zoner'),
										'new_item' 		=> __('New Question',   'zoner'),
										'view' 			=> __('View Question',  'zoner'),
										'view_item' 	=> __('View Question',  'zoner'),
										'search_items' 	=> __('Search Question',  'zoner'),
										'not_found' 	=> __('No Question Found',  'zoner'),
										'not_found_in_trash' => __('No Question Found in Trash',  'zoner'),
										'parent' 		=> __('Parent Question',  'zoner')
					)
					
			) 
		); 
	
		/*Timeline*/
		register_post_type('timeline', array(
				'label' 		    => __("Timeline",  'zoner'),
				'description' 		=> __("Add Timeline",  'zoner'),
				'public'  			=> true,
				'_builtin' 			=> false,
				'show_ui' 			=> true,
				'show_in_menu' 		=> true,
				'capability_type' 	=> 'post',
				'map_meta_cap' 		=> true,
				'hierarchical' 		=> false,
				'exclude_from_search' => true,
				'menu_position' 	=> null,
				'menu_icon'			=> POST_TYPES_ICONS_URL . 'timeline.png',
				'rewrite' 	 => array('slug' => 'timeline', 'with_front' => false, 'feeds' => true),
				'query_var'  => true,
				'supports' 	 => array('title','editor','thumbnail'),
					'labels' => array (
										'name' 			=> __("Timeline",  'zoner'),
										'singular_name' => __('Timeline',  'zoner'),
										'menu_name' 	=> __("Timeline",  'zoner'),
										'add_new' 		=> __('Add New Timeline',  'zoner'),
										'add_new_item' 	=> __('Add New Timeline',  'zoner'),
										'edit' 			=> __('Edit',  'zoner'),
										'edit_item' 	=> __('Edit Timeline',  'zoner'),
										'new_item' 		=> __('New Timeline',   'zoner'),
										'view' 			=> __('View Timeline',  'zoner'),
										'view_item' 	=> __('View Timeline',  'zoner'),
										'search_items' 	=> __('Search Timeline',  'zoner'),
										'not_found' 	=> __('No Timeline Found',  'zoner'),
										'not_found_in_trash' => __('No Timeline Found in Trash',  'zoner'),
										'parent' 		=> __('Parent Timeline',  'zoner')
					)
					
			) 
		); 
		
	}