<?php
	
	if ( ! defined( 'ABSPATH' ) ) exit; 
	
	if ( ! class_exists( 'zoner' ) ) :
	
		final class zoner {
	
			public $countries 	= null;
			public $ratings 	= null;
			public $currency 	= null;
			public $zoner_tax 	= null;
			public $bookmark    = null;
			public $compare     = null;
			public $invites	    = null;
			public $conversation   = null;
			public $emails	    = null;
			public $property	= null;
			public $membership	= null;
			public $invoices	= null;
			public $validate    = null;
			
			public $customcolumns = null;
			
			protected static $_instance = null;
		
			public static function instance() {
				if ( is_null( 	self::$_instance ) ) {
								self::$_instance = new self();
				}
				
				return self::$_instance;
			}
			
			public function __construct() {
				
				$this->includes();
				$this->init();
					
			}
			
			public function init() {
				do_action( 'zoner_before_init' );

				$this->countries 	 = new zoner_сountries();
				$this->ratings 		 = new zoner_ratings();		
				$this->currency 	 = new zoner_currency();		
				$this->customcolumns = new zoner_custom_columns(); 				 
				$this->bookmark 	 = new zoner_bookmark();
				$this->compare 		 = new zoner_compare();
				$this->invites		 = new zoner_agent_agency_invites();
				$this->zoner_tax 	 = new zoner_taxonomies();
				$this->conversation	 = new zoner_conversation();
				$this->emails 		 = new zoner_emails();
				$this->property		 = new zoner_property();
				$this->membership	 = new zoner_membership();
				$this->invoices		 = new zoner_invoices();
                $this->validate		 = new form_validation();
				
				$this->zoner_add_custom_user_role();
				add_filter('wp_dropdown_users', array($this, 'zoner_addCustomRoleToDD'));
				
				$this->zoner_install_custom_pages();
				
				add_filter( 'template_include', array( $this, 'zoner_template_loader'));
				add_action( 'admin_enqueue_scripts', array( $this, 'zoner_enqueue_property_scripts'));
				add_action(	'init', array($this, 'zoner_create_custom_post_status'));
				/*Update taxonomy property city from meta_values*/
				add_action(	'init', array($this, 'zoner_update_tax_city_by_metavalues'), 777);
				
				add_action( 'admin_footer-post.php', array($this, 'zoner_append_post_status_list'));
				add_filter( 'display_post_states', array($this, 'zoner_display_custom_state') );
				
				add_action( 'wp_ajax_zoner_feature_property', array( $this,  'zoner_feature_property') );
				add_action( 'wp_ajax_zoner_paid_property', 	  array( $this,  'zoner_paid_property') );
				add_action( 'wp_ajax_zoner_pending_property', array( $this,  'zoner_pending_property') );
				add_filter( 'pre_get_posts', 			  	  array( $this,  'zoner_set_property_pre_get_posts'));
			  //add_action( 'template_redirect', array( $this, 'zoner_template_redirect') );
				
				add_filter('add_menu_classes', array($this, 'zoner_show_pending_properties_number'), 8);
				add_action('admin_head', array($this, 'zoner_remove_admin_menu') );
				add_action( 'wp', array( $this, 'zoner_remove_filter' ) );
				add_action( 'wp', array( $this, 'zoner_redirect_on_signin' ) );
				
				do_action( 'zoner_after_init' );
			}
			
			public static function zoner_update_tax_city_by_metavalues() {
				global $wpdb, $prefix;
				
				$is_update = get_option( $prefix.'is_update_city');
				
				if (!$is_update) {
					$sql_results = "select pm.meta_value tax_name, group_concat(pm.post_id) ids from {$wpdb->prefix}postmeta pm where pm.meta_key like '%city%' group by pm.meta_value ORDER BY tax_name";
					$results 	 = $wpdb->get_results($sql_results);
					if (!empty($results)) {
						foreach ($results as $res) {
							$tax_name      = $res->tax_name;
							$ids	       = explode(",", $res->ids);
							
						    $tax_inserting = wp_insert_term( $tax_name, 'property_city');
							if (!empty($ids) && !empty($tax_inserting) && isset($tax_inserting->term_id)) {
								$term_id = (int) $tax_inserting['term_id'];
								
								foreach ($ids as $id) {
									wp_set_post_terms( $id, $term_id, 'property_city', 0);
									delete_post_meta($id, $prefix. 'city');
								}
							}
						}
						update_option( $prefix.'is_update_city', 'on');
					}
				}
			}
			
			private function includes() {
				include_once('zoner.class-countries.php');	
				include_once('zoner.class-ratings.php');	
				include_once('zoner.class-currency.php');	
				include_once('zoner.class-taxonomies.php');
				include_once('zoner.class-custom-columns.php');	
				include_once('zoner.class-bookmark.php');
				include_once('zoner.class-compare.php');
				include_once('zoner.class-agency-agent.php');
				include_once('zoner.class-property.php');	
				include_once('zoner.class-membership.php');	
				include_once('zoner.class-invoices.php');	
				include_once('zoner.class-messenger.php');
				include_once('zoner.class-emails.php');
                include_once('zoner.class-validation.php');
			}

			public function zoner_get_page_id($page = null) {
				global $zoner_config, $sitepress;
				$properties_page = 0;
				if (function_exists('icl_object_id')) {
					$def_lang = $sitepress->get_default_language();
					if (!empty($zoner_config[$page])) {
						if ($def_lang == ICL_LANGUAGE_CODE) {
							$properties_page = $zoner_config[$page];	
						} else {
							$properties_page = $zoner_config[$page].ICL_LANGUAGE_CODE;	
						}
					}
				} else {
					if (!empty($zoner_config[$page])) {
						$properties_page = $zoner_config[$page];
					}
				}
				
				return (int) $properties_page;
			}
			
			public function zoner_search_keyword_ids($keyword = null) {
				global $wpdb, $prefix, $zoner_config;
				$keyword_ids = $standart_string = null;
				$keyword 	 = $wpdb->esc_like(strtolower($keyword));
				
				$q = explode(' ', $keyword);
				
				$cnt = 1;
				if (!empty($q)) {
					$cntq = count($q);
					foreach ($q as $word) {
						$standart_string .= "((lower(p.post_title) like '%{$word}%') OR (lower(p.post_content) LIKE '%{$word}%') OR (lower(p.post_excerpt) LIKE '%{$word}%'))";
						if ( $cntq > $cnt) {
							$standart_string .= "AND " ;
						}
						$cnt++;
					}	
				}				
				
				$sql_keyword = "SELECT DISTINCT keyword.post_id FROM
										  (SELECT p.ID post_id FROM {$wpdb->posts} p 
														  LEFT JOIN {$wpdb->postmeta} pm ON (p.ID = pm.post_id)  											
														  WHERE p.post_type    = 'property'  											
														  AND   p.post_status  = 'publish'  											
														  AND  (((pm.meta_key = '{$prefix}reference') AND LENGTH(pm.meta_value)>2 AND (lower(CAST(pm.meta_value AS CHAR)) like '%{$keyword}%')) OR
														  		((pm.meta_key = '{$prefix}address')   AND (lower(CAST(pm.meta_value AS CHAR)) like '%{$keyword}%')) OR
																((pm.meta_key = '{$prefix}district')  AND (lower(CAST(pm.meta_value AS CHAR)) like '%{$keyword}%')) OR
																((pm.meta_key = '{$prefix}beds')      AND (lower(CAST(pm.meta_value AS CHAR)) like '%{$keyword}%')) OR
																((pm.meta_key = '{$prefix}rooms')     AND (lower(CAST(pm.meta_value AS CHAR)) like '%{$keyword}%')) OR
																((pm.meta_key = '{$prefix}bath')      AND (lower(CAST(pm.meta_value AS CHAR)) like '%{$keyword}%')) OR
																((pm.meta_key = '{$prefix}garages')   AND (lower(CAST(pm.meta_value AS CHAR)) like '%{$keyword}%')))
											UNION 
										    SELECT p.ID FROM {$wpdb->posts} p 
											WHERE p.post_type    = 'property'  											
											AND   p.post_status  = 'publish' AND (" . $standart_string . " )
											UNION 
											SELECT wtr.object_id  FROM {$wpdb->terms} wt, {$wpdb->term_taxonomy} wtt, {$wpdb->term_relationships} wtr 
											WHERE  wtt.taxonomy = 'property_city' 
											AND    wt.term_id = wtt.term_id 
											AND   wtt.term_taxonomy_id = wtr.term_taxonomy_id 
											AND  (lower(CAST(wt.name AS CHAR)) like '%{$keyword}%')) keyword			
										GROUP BY keyword.post_id";
				
				$keyword_ids = $wpdb->get_results($sql_keyword);
				
				return $keyword_ids;
			}
			

			public function zoner_search_admin_reference($keyword = null) {
				global $wpdb, $prefix, $zoner_config;
				$keyword_ids = $standart_string = null;
				$keyword 	 = $wpdb->esc_like(strtolower($keyword));
				
				$q = explode(' ', $keyword);
				
				$cnt = 1;
				if (!empty($q)) {
					$cntq = count($q);
					foreach ($q as $word) {
						$standart_string .= "((lower(p.post_title) like '%{$word}%') OR (lower(p.post_content) LIKE '%{$word}%') OR (lower(p.post_excerpt) LIKE '%{$word}%'))";
						if ( $cntq > $cnt) {
							$standart_string .= "AND " ;
						}
						$cnt++;
					}	
				}				
				
				$sql_keyword = "SELECT keyword.post_id FROM
										  (SELECT p.ID post_id FROM {$wpdb->posts} p 
														  LEFT JOIN {$wpdb->postmeta} pm ON (p.ID = pm.post_id)  											
														  WHERE p.post_type    = 'property'  																						
														  AND  (((pm.meta_key = '{$prefix}reference') AND LENGTH(pm.meta_value)>2 AND (lower(CAST(pm.meta_value AS CHAR)) like '%{$keyword}%')) OR
														  		((pm.meta_key = '{$prefix}address')   AND (lower(CAST(pm.meta_value AS CHAR)) like '%{$keyword}%')) OR
																((pm.meta_key = '{$prefix}district')  AND (lower(CAST(pm.meta_value AS CHAR)) like '%{$keyword}%')))
											UNION 
										    SELECT p.ID FROM {$wpdb->posts} p 
											WHERE p.post_type    = 'property'  											
											AND  (" . $standart_string . " )) keyword											
										GROUP BY keyword.post_id";
				
				$keyword_ids = $wpdb->get_results($sql_keyword);
				
				return $keyword_ids;
			}
			
			public function zoner_template_loader( $template ) {
				$file = '';
						
				$is_edit_property = get_query_var('edit-property');
				$is_add_property  = get_query_var('add-property');
				$is_edit_agency   = get_query_var('edit-agency');
				$is_add_agency    = get_query_var('add-agency');
				
				$property_loop_page = $this->zoner_get_page_id('page-property-archive');
				
				if (!empty($is_edit_property) && is_user_logged_in() && is_singular('property')) {

					$file 	= 'includes/theme/property/edit-property.php';
					$find[] = $file;
					$find[] =  get_template_directory() .'/'. $file;
					
				} elseif (!empty($is_add_property) && is_user_logged_in() && (($this->zoner_get_current_user_role() == 'Agent') || is_admin() || is_super_admin() || current_user_can( 'edit_propertys', get_current_user_id() ))) {

					$file 	= 'includes/theme/property/add-property.php';
					$find[] = $file;
					$find[] =  get_template_directory() .'/'. $file;

				} elseif (!empty($is_edit_agency) && is_user_logged_in() && is_singular('agency')) {

					$file 	= 'includes/theme/agency/edit-agency.php';
					$find[] = $file;
					$find[] =  get_template_directory() .'/'. $file;

				} elseif (!empty($is_add_agency) && is_user_logged_in() && (($this->zoner_get_current_user_role() == 'Agent') || is_admin() || is_super_admin()) && (!is_singular('agency'))) {

					$file 	= 'includes/theme/agency/add-agency.php';
					$find[] = $file;
					$find[] =  get_template_directory() .'/'. $file;

				} elseif ( is_single() && get_post_type() == 'property' && (!isset($is_edit_property)) ) {
					
					$file 	= 'single-property.php';
					$find[] = $file;
					$find[] =  get_template_directory() .'/'. $file;

				} elseif ( is_post_type_archive( 'property' ) || (is_page($property_loop_page) && $property_loop_page != 0) ) {

					$file 	= 'archive-property.php';
					$find[] = $file;
					$find[] =  get_template_directory() .'/'. $file;
				}	

				if ( $file ) {
					$template = locate_template( $find );
					if ( ! $template )
						   $template = get_template_directory() . '/' . $file;
				}
				return $template;
			}


		public function zoner_create_custom_post_status() {
			register_post_status( 'zoner-pending', array(
				'label'                     => __( 'Pending property', 'zoner' ),
				'public'                    => false,
				'exclude_from_search'       => false,
				'show_in_admin_all_list'    => true,
				'show_in_admin_status_list' => true,
				'protected'   				=> true,
				'label_count'               => _n_noop( 'Pending property <span class="count">(%s)</span>', 'Pending property <span class="count">(%s)</span>', 'zoner' ),
			) );
			
			register_post_status( 'zoner-expired', array(
				'label' 					=> __( 'Expired property', 'zoner' ),
				'public' 					=> false,
				'exclude_from_search'       => false,
                'show_in_admin_all_list'    => true,
				'show_in_admin_status_list' => true,
				'protected'   				=> true,
				'label_count'               => _n_noop( 'Membership Expired <span class="count">(%s)</span>', 'Membership Expired <span class="count">(%s)</span>', 'zoner' ),
			) );
		}	
		
		
		public function zoner_append_post_status_list(){
			global $post;
			$complete = $label = '';
			if($post->post_type == 'property') {
				if( $post->post_status == 'zoner-pending') {
					 $complete = ' selected=\"selected\"';
						$label = '<span id=\"post-status-display\">'.__('Pending property', 'zoner').'</span>';
				}
				
				echo '
					<script type="text/javascript">
						jQuery(document).ready(function($){
							$("select#post_status").append("<option value=\"zoner-pending\" '.$complete.'>'.__('Pending property', 'zoner').'</option>");
							$(".misc-pub-section label").append("'.$label.'");
					});
					</script>
				';
			}
		}
		
		
		function zoner_display_custom_state( $states ) {
			global $post;
				$arg = get_query_var( 'post_status' );
				if($arg != 'zoner-pending') {
					if($post->post_status == 'zoner-pending') {
					return array(__('Pending property', 'zoner'));
				}
			}
			return $states;
		}

	
		public function zoner_get_custom_role_capabilities() {
			$capabilities 	  = array();
			$capability_types = array( 'property', 'agency' );
			foreach ( $capability_types as $capability_type ) {
	
					$capabilities[ $capability_type ] = array(
						// Post type
						"edit_{$capability_type}",
						"read_{$capability_type}",
						"delete_{$capability_type}",
						"edit_{$capability_type}s",
						"edit_others_{$capability_type}s",
						"publish_{$capability_type}s",
						"read_private_{$capability_type}s",
						"delete_{$capability_type}s",
						"delete_private_{$capability_type}s",
						"delete_published_{$capability_type}s",
						"delete_others_{$capability_type}s",
						"edit_private_{$capability_type}s",
						"edit_published_{$capability_type}s",

						// Terms
						"manage_{$capability_type}_terms",
						"edit_{$capability_type}_terms",
						"delete_{$capability_type}_terms",
						"assign_{$capability_type}_terms"
					);
				}

				return $capabilities;
			}
			
			function zoner_add_custom_user_role() {
				global $wp_roles;
				
				if ( class_exists( 'WP_Roles' ) ) {
					if ( ! isset( $wp_roles ) ) {
						$wp_roles = new WP_Roles();
					}
				}

				if ( is_object( $wp_roles ) ) {
				
					add_role(   'agent', __('Agent', 'zoner'), array( 
								'read' 			=> true,
								'edit_posts' 	=> false,
								'delete_posts' 	=> false
					));
				
				
					$capabilities = $this->zoner_get_custom_role_capabilities();
					
					foreach ( $capabilities as $cap_group ) {
						foreach ( $cap_group as $cap ) {
							$wp_roles->add_cap( 'administrator', $cap );
							$wp_roles->add_cap( 'editor', $cap );
							$wp_roles->add_cap( 'author', $cap );
						}
					}
				}
				
			}
			
			/*Add user role to dropdown list in custom zoner post type*/
			function zoner_addCustomRoleToDD($output) {
				global $post, $pagenow;
				if(!empty($post) && ($post->post_type == 'property' || $post->post_type == 'agency')) {
					$users = array();
					$name = $id = $class = $newDOM = null;
					
					$newDOM = new DOMDocument();
					$newDOM->loadHTML($output);
					$tags   = $newDOM->getElementsByTagName('select');
					
					foreach($tags as $tag) {
						$name 	= $tag->getAttribute('name');
						$id 	= $tag->getAttribute('id');
						$class 	= $tag->getAttribute('class');
						break;
					}
	
					$users[0] = get_users(array('role'=>'administrator'));
					$users[1] = get_users(array('role'=>'agent'));
					$users[2] = get_users(array('role'=>'editor'));
					
					$output = "<select id='".$id."' name='".$name."' class='".$class."'>";
					foreach($users as $userGroup) {
						foreach ($userGroup as $user) {
							$selected = ($user->ID == intval($post->post_author)) ? " selected='selected'" : "";
							$output .= "<option".$selected." value='".$user->ID."'>".$user->user_login."</option>";
						}
					}
					$output .= "</select>";
				}
				return $output;
			}

			public function zoner_get_current_user_role() {
				global $wp_roles;
				$current_user = wp_get_current_user();
				$roles = $current_user->roles;
				$role  = array_shift($roles);
				return isset($wp_roles->role_names[$role]) ? translate_user_role($wp_roles->role_names[$role] ) : false;
			}
			
			public function zoner_get_user_role_by_id($userID) {
				$user = new WP_User($userID);
				$role = array_shift($user->roles);
				return $role;
			}
	
			public function zoner_set_property_pre_get_posts($query) {
				global $zoner_config, $prefix, $wpdb;
				$meta_query = $query->get( 'meta_query' );
				$tax_query 	= $query->get( 'tax_query' );
				
				$post__in   = array();
				
				if (!$query->is_main_query()) return;
				$qoperator = isset($zoner_config['query-operator']) ? esc_attr($zoner_config['query-operator']) : "OR";
				if ($qoperator == 'OR') {
					$qoperator  = "IN";
				}
		
				$author_var = '';
				$author_var = get_query_var( 'author_name' );
				$paged = ( get_query_var( 'paged') ) ? get_query_var( 'paged' ) : 1;
				
				if (!empty($author_var)) {
					$author = get_user_by( 'slug', get_query_var( 'author_name' ) );
				} else {
					$author = get_user_by( 'id',   get_query_var( 'author' ) );
				}
				
				$curr_user_id 	 = get_current_user_id();
				$is_profile_page = get_query_var('profile-page');
				
				if ( $query->is_author() && $query->is_main_query() && is_user_logged_in() && ($curr_user_id == $author->ID)) {
					 if (($this->zoner_get_current_user_role() == 'Agent') || is_admin() || is_super_admin() || current_user_can('edit_propertys', get_current_user_id()) ) {
						if (!is_admin()) { 
							$query->set('post_type', array('property'));
							$query->set('post_status', 'any');
							$query->set('pagename', NULL );
							$query->set('paged', $paged );
							if (isset($zoner_config['page-property-num-of-posts']))
							$query->set('posts_per_page', $zoner_config['page-property-num-of-posts']);	 
						}	
					 }
				}
			  
				if ( $query->is_page() && $query->get('page_id') == $this->zoner_get_page_id('page-property-archive') && $this->zoner_get_page_id('page-property-archive') != 0) {
					global $wp_post_types;
					$query->set( 'post_type', 'property' );
					$query->set( 'post_status', 'publish');
				    $query->set( 'page_id', '' );
					if ( isset( $query->query['paged'] ) )
					$query->set( 'paged', $query->query['paged'] );

					$property_page 	= get_post( $this->zoner_get_page_id('page-property-archive') );

					$wp_post_types['property']->ID 			= $property_page->ID;
					$wp_post_types['property']->post_title 	= $property_page->post_title;
					$wp_post_types['property']->post_name 	= $property_page->post_name;
					$wp_post_types['property']->post_type   = $property_page->post_type;
					$wp_post_types['property']->ancestors   = get_ancestors( $property_page->ID, $property_page->post_type );			
					
					
										
					$query->is_singular = false;
					$query->is_post_type_archive = true;
					$query->is_archive  = true;
					$query->is_page     = true;
				}
									
				if ($query->is_post_type_archive( 'property' ) || $query->is_tax(get_object_taxonomies( 'property' ))) {
					$this->zoner_property_query_ordering($query);
				}	
						
				if ($query->is_post_type_archive( 'property' ) && !is_admin()) {
					$query->set('posts_per_page', apply_filters( 'zoner_loop_per_page', $zoner_config['page-property-num-of-posts']));
					$query->set( 'post_status', 'publish');
				}
				
					
				if (isset($_GET) && isset($_GET['filter_property']) && wp_verify_nonce($_GET['filter_property'], 'zoner_filter_property')) {
						$tax_query ['relation'] = 'AND';
						$meta_query['relation'] = 'AND';
					$meta_query_price = array();

					if (!empty($_GET['sb-price'])) {
						$meta_query_price['relation'] = 'OR';
						$sb_price = explode(';', $_GET['sb-price']);
						$meta_query_price[] = array(
												'key' 	=> $prefix .'price',
												'value' => array($sb_price[0], $sb_price[1]),
												'compare' => 'BETWEEN',
												'type' 	=> 'DECIMAL'
										);
						//on request
						if (isset($_GET['sb-price-req']))
							$meta_query_price[] = array(
								'key' 	=> $prefix .'price',
								'compare' => 'NOT EXISTS'
							);
						$meta_query[] = $meta_query_price;
					}


					if (!empty($_GET['sb-country'])) {
						$sb_country = esc_attr($_GET['sb-country']);
						$meta_query[] = array(
												'key' 	=> $prefix .'country',
												'value' => $sb_country,
												'compare' => '='
										);
					}					
					
					if (!empty($_GET['sb-district'])) {
						$sb_district = esc_attr($_GET['sb-district']);
						$meta_query[] = array(
												'key' 	  => $prefix .'district',
												'value'   => $sb_district,
												'compare' => '='
										);
					}					
					
					
					if (!empty($_GET['sb-zip'])) {
						$sb_zip = esc_attr($_GET['sb-zip']);
						$meta_query[] = array(
												'key' 	=> $prefix .'zip',
												'value' => $sb_zip,
												'compare' => 'like'
										);
					}					
					
					if (!empty($_GET['sb-keyword'])) {
						$sb_keyword  = esc_attr($_GET['sb-keyword']);
						$keyword_ids = $this->zoner_search_keyword_ids($sb_keyword);
						
						if (!empty($keyword_ids)) {
							foreach($keyword_ids as $val) {
								$post__in[] = $val->post_id;	
							}
						} else {
							$post__in[] = -9999999;
						}
					}					
					
					if (!empty($_GET['sb-area'])) {
						$sb_area = esc_attr($_GET['sb-area']);
						$meta_query[] = array(
												'key' 	=> $prefix .'area',
												'value' => $sb_area,
												'compare' => '>=',
												'type' => 'DECIMAL'
										);
					}					
					
					
					if (!empty($_GET['sb-features'])) {
						$sb_features = $_GET['sb-features'];
						$tax_query[] = array(
												'taxonomy' 	=> 'property_features',
												'field' 	=> 'id',
												'terms' 	=> $sb_features,
												'operator'  => $qoperator
										);
					
					}
					
					
					if (!empty($_GET['sb-city'])) {
						$sb_city = esc_attr($_GET['sb-city']);
						$tax_query[] = array(
												'taxonomy' => 'property_city',
												'field'    => 'id',
												'terms'    => $sb_city
										);
					}					
					
					if (!empty($_GET['sb-cat'])) {
						$sb_cat = (int)$_GET['sb-cat'];
						$tax_query[] = array(
												'taxonomy' => 'property_cat',
												'field' => 'id',
												'terms' => $sb_cat
										);
					}					
					
					if (!empty($_GET['sb-status'])) {
						$sb_status = (int)$_GET['sb-status'];
						$tax_query[] = array(
												'taxonomy' => 'property_status',
												'field' => 'id',
												'terms' => $sb_status
										);
					}					
					
					if (!empty($_GET['sb-type'])) {
						$sb_type = (int)$_GET['sb-type'];
						$tax_query[] = array(
												'taxonomy' => 'property_type',
												'field' => 'id',
												'terms' => $sb_type
										);
					}					
					
					if (!empty($_GET['sb-condition'])) {	
						$condition = (int)$_GET['sb-condition'];
						$meta_query[] = array(
												'key' 	=> $prefix .'condition',
												'value' => $condition,
												'compare' => '='
										);

					}					
					
					if (!empty($_GET['sb-payment'])) {
						$payment = (int)$_GET['sb-payment'];
						$meta_query[] = array(
												'key' 	=> $prefix .'payment',
												'value' => $payment,
												'compare' => '='
										);
					}					
					
					
					if (!empty($_GET['sb-rooms'])) {
						$rooms = (int)$_GET['sb-rooms'];
						$meta_query[] = array(
												'key' 	=> $prefix .'rooms',
												'value' => $rooms,
												'compare' => '='
										);
					}					
					
					if (!empty($_GET['sb-beds'])) {
						$beds = (int)$_GET['sb-beds'];
						$meta_query[] = array(
												'key' 	=> $prefix .'beds',
												'value' => $beds,
												'compare' => '='
										);
					}					
					
					if (!empty($_GET['sb-baths'])) {
						$baths = (int)$_GET['sb-baths'];
						$meta_query[] = array(
												'key' 	=> $prefix .'baths',
												'value' => $baths,
												'compare' => '='
										);
					}					
					
					if (!empty($_GET['sb-garages'])) {
						$garages = (int)$_GET['sb-garages'];
						$meta_query[] = array(
												'key' 	=> $prefix .'garages',
												'value' => $garages,
												'compare' => '='
										);
						
					}					
					
					
				}
				//search by id in admin
				if (is_admin() && $query->query['post_type']=='property' &&  !empty($query->query['s']) && empty($query->query['is_search'])){
					$post__in = array();
					$reference_id  = $query->query['s'];
					$keyword_ids = $this->zoner_search_admin_reference($reference_id);
					
					if (!empty($keyword_ids)) {
						foreach($keyword_ids as $val) {
							$post__in[] = $val->post_id;	
						}
					} else {
						$post__in[] = -9999999;
					}
					$query->set('s','');
				}


				if (!empty($post__in))
				$query->set( 'post__in', 	$post__in);

				$query->set( 'meta_query',  apply_filters('zoner_global_meta_query_fields', $meta_query));	
				$query->set( 'tax_query',   apply_filters('zoner_global_tax_query_fields', $tax_query ));	
				$this->zoner_remove_filter();
			}
			
			public function zoner_template_redirect() {
				global $wp_query, $wp;
				if ( ! empty( $_GET['page_id'] ) && get_option( 'permalink_structure' ) == "" && $_GET['page_id'] == $this->zoner_get_page_id('page-property-archive') ) {
					wp_safe_redirect( get_post_type_archive_link('property') );
					exit;
				}
			}
	
			public function zoner_remove_filter() {
				remove_filter( 'pre_get_posts', 	array( $this, 'zoner_set_property_pre_get_posts'));
			}	
			
			public function zoner_redirect_on_signin() {
				global $zoner_config;
				
				$is_add_property  = get_query_var('add-property');
				$is_add_agency    = get_query_var('add-agency');
				
				if ((!empty($is_add_property) || !empty($is_add_agency)) && !is_user_logged_in()) {
					$signin = $this->zoner_get_page_id('page-signin');
					wp_safe_redirect(esc_url(get_permalink($signin)));
				}
			}
			
			public function  zoner_property_query_ordering($query) {
				global $zoner_config;
				// Meta query
				$meta_query = $query->get( 'meta_query' );
				
				// Ordering
				$ordering   = $this->zoner_get_prop_ordering_args();
				
				// Ordering query vars
				$query->set( 'orderby', $ordering['orderby'] );
				$query->set( 'order',   $ordering['order'] );
				if ( isset( $ordering['meta_key'] ) ) $query->set( 'meta_key', $ordering['meta_key'] );

				$query->set( 'meta_query', $meta_query );
				$query->set( 'posts_per_page', $query->get( 'posts_per_page' ) ? $query->get( 'posts_per_page' ) : apply_filters( 'zoner_loop_per_page', $zoner_config['page-property-num-of-posts'] ) );
				
				do_action( 'zoner_property_query', $query, $this );
			}
	
			public function zoner_get_prop_ordering_args( $orderby = '', $order = '' ) {
				global $zoner_config;
				$prefix = '_zoner_';		
			
				if ( ! $orderby ) {
					$orderby_value = isset( $_GET['sorting'] ) ? $this->zoner_var_clean( $_GET['sorting'] ) : apply_filters( 'zoner_property_catalog_orderby_default', $zoner_config['property-default-orderby'] );
					
					$orderby_value = explode( '-', $orderby_value );
					$orderby       = esc_attr( $orderby_value[0] );
					$order         = ! empty( $orderby_value[1] ) ? $orderby_value[1] : $order;
				}
				
				$orderby = strtolower( $orderby );
				$order   = strtoupper( $order );

				$args = array();

					// default - menu_order
				$args['orderby']  = 'menu_order title';
				$args['order']    = $order == 'DESC' ? 'DESC' : 'ASC';
				$args['meta_key'] = '';

				switch ( $orderby ) {
					case 'rand' :
						$args['orderby']  = 'rand';
						$args['order']    = $order == 'ASC' ? 'ASC' : 'DESC';
					break;
					case 'id' :
						$args['orderby']  = 'ID';
						$args['order']    = $order == 'ASC' ? 'ASC' : 'DESC';
					break;
					case 'name' :
						$args['orderby']  = 'name';
						$args['order']    = $order == 'ASC' ? 'ASC' : 'DESC';
					break;
					case 'title' :
						$args['orderby']  = 'title';
						$args['order']    = $order == 'ASC' ? 'ASC' : 'DESC';
					break;
					case 'date' :
						$args['orderby']  = 'date';
						$args['order']    = $order == 'ASC' ? 'ASC' : 'DESC';
					break;
					case 'modified' :
						$args['orderby']  = 'modified';
						$args['order']    = $order == 'ASC' ? 'ASC' : 'DESC';
					break;
					case 'author' :
						$args['orderby']  = 'author';
						$args['order']    = $order == 'ASC' ? 'ASC' : 'DESC';
					break;
					case 'price' :
						$args['orderby']  = 'meta_value_num';
						$args['order']    = $order == 'DESC' ? 'DESC' : 'ASC';
						$args['meta_key'] = $prefix . 'price';
					break;
					case 'rating' :
						$args['orderby']  = 'meta_value_num';
						$args['order']    = $order == 'DESC' ? 'DESC' : 'ASC';
						$args['meta_key'] = $prefix . 'avg_rating';
					break;
					case 'featured' :
						$args['orderby']  = 'meta_value';
						$args['order']    = $order == 'DESC' ? 'DESC' : 'ASC';
						$args['meta_key'] = $prefix . 'is_featured';
					break;
					case 'title' :
						$args['orderby']  = 'title';
						$args['order']    = $order == 'DESC' ? 'DESC' : 'ASC';
					break;
				}

				return apply_filters( 'zoner_property_catalog_orderby', $args );
			}
	
			function zoner_var_clean( $var ) {
				return sanitize_text_field( $var );
			}
			
			public function zoner_insert_row_to_table($name_table, $arr_field, $array_of_type) {
				global $wpdb;
				$id = '';
				if (!empty($arr_field) || !empty($array_of_type)) {
					$wpdb->insert( 
						$wpdb->prefix . $name_table, 
						$arr_field, 
						$array_of_type
					);
				}
				
				$id = $wpdb->insert_id;
				$wpdb->flush();
				return $id;
			}
			
			public function zoner_show_pending_properties_number($menu) {
				$pending_count = 0;
				
				$type 	   = "property";
				$status    = "zoner-pending";
				$num_posts = wp_count_posts( $type, 'readable' );
				
				
				if ( !empty($num_posts->$status) )
				$pending_count = $num_posts->$status;
				
				if ($type == 'post') {
					$menu_str = 'edit.php';
				} else {
					$menu_str = 'edit.php?post_type=' . $type;
				}

    
				foreach( $menu as $menu_key => $menu_data ) {
					if( $menu_str != $menu_data[2] ) 
					continue;
					
					$menu[$menu_key][0] .= " <span class='awaiting-mod count-$pending_count'><span class='pending-count'>" . number_format_i18n($pending_count) . '</span></span>';
				}
				
				return $menu;
			}
			
			public function zoner_remove_admin_menu() {
				/*If not editor remove dashboard panel*/
				$current_user = wp_get_current_user();
				if ( !empty( $current_user ) ) {
					if (!empty($current_user->allcaps)) {
						 if ($current_user->allcaps['edit_posts'] === false)
						 remove_menu_page('index.php');    
					}
				}	
			}
		
			public function zoner_update_table($name_table, $arr_field = array(), $arr_where = array(), $array_of_type = array(), $array_of_type_where = array()) {	
				global $wpdb;
		
				if (!empty($arr_field) || !empty($array_of_type)) {
					$wpdb->update( 
						$wpdb->prefix . $name_table, 
						$arr_field, 
						$arr_where,
						$array_of_type,
						$array_of_type_where
					);
				}
				$wpdb->flush();
			}
	
			public function zoner_delete_from_table($name_table, $arr_where = array(), $array_of_type_where = array()) {
				global $wpdb;
				$wpdb->delete(
					$wpdb->prefix . $name_table, 
					$arr_where, 
					$array_of_type_where );
				$wpdb->flush();	
			}
			
			public function zoner_enqueue_property_scripts($hook) {
				global $inc_theme_url, $admin_theme_url;
				if( 'edit.php' != $hook ) return;
				
				wp_enqueue_style( 'zoner-fontAwesom', $inc_theme_url . 'assets/fonts/font-awesome.min.css');
				wp_enqueue_style( 'zoner-adminProperty', $admin_theme_url . 'classes/res/admin.property.css');
			}
		
			
			
			public function zoner_feature_property() {
				global $prefix; 
				
				if ( ! check_admin_referer( 'zoner-feature-property' ) )
					wp_die( __( 'You have taken too long. Please go back and retry.', 'zoner' ) );
				
				$post_id = ! empty( $_GET['property_id'] ) ? (int) $_GET['property_id'] : '';

				if ( ! $post_id || get_post_type( $post_id ) !== 'property' ) die;
				$featured = get_post_meta( $post_id, $prefix. 'is_featured', true );

				if ( 'on' === $featured ) {
					update_post_meta( $post_id, $prefix.'is_featured', 'off' );
				} else {
					update_post_meta( $post_id, $prefix.'is_featured', 'on' );
				}

				wp_safe_redirect( remove_query_arg( array( 'trashed', 'untrashed', 'deleted', 'ids' ), wp_get_referer() ) );
				die();
			}
			
			public function zoner_paid_property() {
				global $prefix; 
				
				if ( ! check_admin_referer( 'zoner-paid-property' ) )
					wp_die( __( 'You have taken too long. Please go back and retry.', 'zoner' ) );
				
				$post_id = ! empty( $_GET['property_id'] ) ? (int) $_GET['property_id'] : '';

				if ( ! $post_id || get_post_type( $post_id ) !== 'property' ) die;
				$is_paid = get_post_meta( $post_id, $prefix. 'is_paid', true );

				if ( 'on' === $is_paid ) {
					update_post_meta( $post_id, $prefix.'is_paid', 'off' );
				} else {
					update_post_meta( $post_id, $prefix.'is_paid', 'on' );
				}

				wp_safe_redirect( remove_query_arg( array( 'trashed', 'untrashed', 'deleted', 'ids' ), wp_get_referer() ) );
				die();
			}
			
			
			public function zoner_pending_property() {
				
				if ( ! check_admin_referer( 'zoner-pending-property' ) )
				wp_die( __( 'You have taken too long. Please go back and retry.', 'zoner' ) );
				
				$post_id = ! empty( $_GET['property_id'] ) ? (int) $_GET['property_id'] : '';

				if ( ! $post_id || get_post_type( $post_id ) !== 'property' ) die;
				
				$pending = get_post_status( $post_id);

				if ( 'zoner-pending' === $pending ) {
					  $post_args = array( 'ID' => $post_id, 'post_status' => 'publish' );
				} else {
					  $post_args = array( 'ID' => $post_id, 'post_status' => 'zoner-pending' );
				}
				
				wp_update_post( $post_args );
				wp_safe_redirect( remove_query_arg( array( 'trashed', 'untrashed', 'deleted', 'ids' ), wp_get_referer() ) );
				
				die();
			}
			
			
			public function zoner_install_custom_pages() {
				if (!get_option( '_zoner_is_added_paged' )) {
				
					$required_page = array();
					
					$required_page[] = array('slug' => 'properties','name' => __('Properties', 'zoner'), 'content' => '');
					$required_page[] = array('slug' => 'create-an-account', 'name' => __('Create an Account', 'zoner'), 'content' => '[vc_row][vc_column width="1/4"][/vc_column][vc_column width="1/2"][zoner_register_form sb_term_cond="66"][/vc_column][vc_column width="1/4"][/vc_column][/vc_row]');
					$required_page[] = array('slug' => 'sign-in', 'name' => __('Sign In', 'zoner'), 'content' => '[vc_row][vc_column width="1/4"][/vc_column][vc_column width="1/2"][zoner_signin][/vc_column][vc_column width="1/4"][/vc_column][/vc_row]');
					$required_page[] = array('slug' => 'compare', 'name' => __('Compare', 'zoner'), 'content' => '');
					
					$required_page[] = array('slug' 	=> 'thank-after-submit-property', 
										     'name' 	=> __('Thank you for submit property', 'zoner'), 
											 'content' 	=> sprintf('[vc_row][vc_column width="1/1"][zoner_info_message pb_title="%1s" pb_description="%2s" pb_custom_link="my_properties" pb_link_title="%3s"][/vc_column][/vc_row]', 
														   __('Thank You!', 'zoner'), 
														   __('Your property waiting for approval.', 'zoner'),
														   __('View your profile', 'zoner') 
													));
					$required_page[] = array('slug' 	=> 'thank-after-submit-agency',   
											 'name' 	=> __('Thank you for submit agency', 'zoner'),   
											 'content' 	=> sprintf('[vc_row][vc_column width="1/1"][zoner_info_message pb_title="%1s" pb_description="%2s" pb_custom_link="my_agencies" pb_link_title="%3s"][/vc_column][/vc_row]', 
														   __('Thank You!', 'zoner'), 
														   __('Your agency has been added.', 'zoner'), 
														   __('View your profile', 'zoner') 
													));
				
					foreach ($required_page as $page_) {
						if (!$this->zoner_is_page_exists($page_['slug'])) {
							 $this->zoner_insert_custom_page($page_);
						}
					}
				
				}
				
				update_option( '_zoner_is_added_paged', 1 );
			}
			
			public function zoner_is_page_exists($slug, $val_format = 0) {
				global $wpdb;
				$page_found = 0;
				$page_found = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM " . $wpdb->posts . " WHERE post_type='page' AND post_name = %s LIMIT 1;", $slug ) );
				if ($val_format == 0) {
					return ( !empty($page_found) && ($page_found != 0));
				} else {
					return $page_found;
				}				
			}

			public function zoner_get_all_lang_codes() {
				global $sitepress;
				$langs = $out = null;
				if ( function_exists( 'icl_get_languages' ) ) {
					$langs = icl_get_languages('skip_missing=0&orderby=name&order=ASC&link_empty_to=str');
					if (!empty($langs)) {
						foreach ($langs as $lang) {
							$out[] = $lang['language_code'];	
						}
					}
				}	
				return $out;
			}
			
			public function zoner_get_all_options_translated_pages($args = null) {
				global $zoner, $sitepress;
				$pagesOptions = null;
				
				$langs = $this->zoner_get_all_lang_codes();
				if (!empty($langs)) {
					foreach ($langs as $lang) {
						$prefix = $prefix_id = null;
						if (!empty($lang)) {
							$lang_prefix	   = ' ('. $lang . ')';
							$lang_prefix_id    = '_'. $lang;
						}
						
						$def_lang    = $sitepress->get_default_language();
						if ($def_lang == $lang) {
							$pagesOptions[] = array(
								'id'       => $args['id'],  
								'type'     => 'select',
								'title'    => $args['title'] . $lang_prefix,  
								'subtitle' => $args['subtitle'],
								'options'  =>  $this->zoner_generate_pages_by_lang($lang),
								'std'	   =>  $this->zoner_is_page_exists($args['exist_page'], 1),
								'default'  =>  $this->zoner_is_page_exists($args['exist_page'], 1)
							);		
						} else {
							$pagesOptions[] = array(
								'id'       => $args['id']. $lang_prefix_id,
								'type'     => 'select',
								'title'    => $args['title'] . $lang_prefix, 
								'subtitle' => $args['subtitle'],
								'options'  => $this->zoner_generate_pages_by_lang($lang),
							);		
						}
						
					}
				} else {
					$pagesOptions[] = array(
						'id'       => $args['id'],
						'type'     => 'select',
						'title'    => $args['title'], 
						'subtitle' => $args['subtitle'],
						'data'     =>  'page',
						'std'	   =>  $this->zoner_is_page_exists($args['exist_page'], 1),
						'default'  =>  $this->zoner_is_page_exists($args['exist_page'], 1)
					);		
				}					
					
				return  $pagesOptions;
			}
			
			public function zoner_generate_pages_by_lang($lng_code = null) {
				global $wpdb, $sitepress;
					$allLangPages = null;
					
					if (function_exists('icl_object_id')) {
					
						if (!empty($lng_code)) {
							$lng_code = trim(strtolower($lng_code));
							$sql_results = "SELECT DISTINCT p.ID id, p.post_title title, icl.language_code lc FROM {$wpdb->posts} p, {$wpdb->prefix}icl_translations icl WHERE p.ID = icl.element_id AND p.post_status = 'publish' AND p.post_type = 'page' AND icl.language_code = '{$lng_code}' ORDER BY p.post_title ASC ";
						} else {
							$sql_results = "SELECT DISTINCT p.ID id, p.post_title title, icl.language_code lc FROM {$wpdb->posts} p, {$wpdb->prefix}icl_translations icl WHERE p.ID = icl.element_id AND p.post_status = 'publish' AND p.post_type = 'page' ORDER BY p.post_title ASC ";	
						}	
				
						$pages = $wpdb->get_results($sql_results);
						if (!empty($pages)) {
							foreach($pages as $page) {
								$allLangPages[$page->id] = $page->title;
							}
						}
					}
				
				return $allLangPages;
			}
			
			public function zoner_insert_custom_page($args = array()) {
				global $prefix;
				$page_data = array(
					'post_status'       => 'publish',
					'post_type'         => 'page',
					'post_author'       => 1,
					'post_name'         => $args['slug'],
					'post_title'        => $args['name'],
					'post_content'      => $args['content'],
					'post_parent'       => 0,
					'comment_status'    => 'closed'
				);
				$page_id = wp_insert_post( $page_data );
				
				update_post_meta($page_id, '_wpb_vc_js_status', 'true');
				
				if (!($args['slug'] == 'properties')) {
					update_post_meta($page_id, '_wpb_vc_js_interface_version', '0');
					update_post_meta($page_id, '_zoner_pages_layout', '1');
				} 
				
				if ($args['slug'] == 'compare') {
					update_post_meta( $page_id, '_wp_page_template', 'compare.php' );
				}
				
				return $page_id;
			}
	
		}
	endif; 
	
	function ZONER_GO() {
		return zoner::instance();
	}
	
	$GLOBALS['zoner'] = ZONER_GO();