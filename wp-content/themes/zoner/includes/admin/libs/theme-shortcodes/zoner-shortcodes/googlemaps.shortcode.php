<?php
/*Google Maps*/
class zoner_maps_shortcode {
	static function init() {
		add_shortcode('zoner_gmaps_ajax', 		array(__CLASS__, 'zoner_gmaps_ajax'));
		//listing
		add_action   ('zoner_before_enqueue_script', array(__CLASS__, 'zoner_property_listing_script'), 5);
		//map
		add_action   ('wp_footer', array(__CLASS__, 'zoner_gmap_jscript'), 10);
		add_action( 'wp_ajax__zoner_get_multiitems_', array(__CLASS__, 'zoner_gmaps_multichoise' ));
		add_action( 'wp_ajax_nopriv__zoner_get_multiitems_', array(__CLASS__, 'zoner_gmaps_multichoise'));
		add_action( 'wp_ajax__zoner_get_items_', array(__CLASS__, 'zoner_gmap_ajax_items' ));
		add_action( 'wp_ajax_nopriv__zoner_get_items_', array(__CLASS__, 'zoner_gmap_ajax_items'));
	}
	static function zoner_gmap_jscript() {
		global $inc_theme_url;
		if (!empty(self::$shortcode_id)) {
			// if(!wp_script_is('googlemaps', 'enqueued'))
			// 	wp_enqueue_script ( 'googlemaps',     '//maps.googleapis.com/maps/api/js?v=3&libraries=places', array(), null, false);
			wp_enqueue_style('zoner-gmap-custom-scroll', $inc_theme_url . '/assets/css/jquery.mCustomScrollbar.css');
			wp_enqueue_style('zoner-gmap-style', ZONER_SHORTCODE_CSS . 'gmap.css');
			zoner_scripts_map(true);//parse google maps
			if (!wp_script_is('zoner-gmaps', 'enqueued')) {
				wp_register_script('zoner-gmaps', get_template_directory_uri() . '/includes/admin/libs/theme-shortcodes/zoner-shortcodes/patternsJs/gmap.js', array('jquery'), '20151203', true);
				wp_localize_script('zoner-gmaps', 'globalGmap', array('zoner_ajax_maps_nonce' => wp_create_nonce('zoner_maps_ajax_nonce'),
						'source_path' => $inc_theme_url . 'assets',
						'detatail_text' => __('Go to Detail', 'zoner')
					)
				);
				wp_enqueue_script('zoner-gmaps');
			}
			if (!wp_script_is('zoner-custom-scrollbar', 'enqueued'))
				wp_enqueue_script('zoner-custom-scrollbar', $inc_theme_url . '/assets/js/jquery.mCustomScrollbar.concat.min.js', array('jquery'), '20152812', true);
			if (!wp_script_is('zoner-cookie', 'enqueued'))
				wp_enqueue_script('zoner-cookie', $inc_theme_url . '/assets/js/jquery.cookie.js', array('jquery'), '20151203', true);
			//if(!wp_script_is('zoner-list', 'enqueued'))
			//wp_enqueue_script( 'zoner-list',  ZONER_SHORTCODE_JS . 'mapList.js', array( 'jquery' ), '20140808', true );
		}

	}
			
		static function zoner_property_listing_script() {
			if( !wp_script_is('zoner-reveral', 'enqueued')) wp_enqueue_script( 'zoner-reveral');
			if( !wp_script_is('zoner-masonry', 'enqueued')) wp_enqueue_script( 'zoner-masonry');
			if( !wp_script_is('zoner-imagesloaded', 'enqueued')) wp_enqueue_script( 'zoner-imagesloaded');
		}
		static $shortcode_id = 0;
		static function zoner_gmaps_ajax($atts = array()) {
			global $prefix, $zoner, $zoner_config, $inc_theme_url;
			$zoner_config['is-gmap-api'] = 1;
			$out = $el_class = $pb_list_type = $pb_is_auto = $pb_type = $pb_is_items = $pb_max_items = 
			$pb_zoom = $pb_latlng = $pb_is_search = '';	
			$atts = vc_map_get_attributes( 'zoner_gmaps_ajax', $atts );
			extract( $atts );
			$id_ = self::$shortcode_id;
			self::$shortcode_id++;
			if (!empty($pb_latlng)) {
				$pb_latlng = str_replace(' ', '', $pb_latlng);//delete whitespaces
				$pb_latlng = explode(',', $pb_latlng);
				$lat = $pb_latlng[0];
				$lng = $pb_latlng[1];	
			}
			$out .= '<div class="gmap-shortcode">';
			$out .= '<div class="info-alert ajax-loading-start"><i class="fa fa-cog fa-spin"></i> '.__("Results updating...", 'zoner').'</div>';
			$out .= '<div class="info-alert ajax-loading-end">'.__("Done.", 'zoner').'</div>';
			$out .= '<div class="info-alert nothing-found">'.__("Nothing was found on your request.", 'zoner').'</div>';
			$out .= '<div id="map-canvas-'.$id_.'" class="map-canvas list-solid '.(empty($pb_is_items)?"full-width":"").'"><!-- Map Canvas-->';
			
			$out .= '<div class="map gm-style " ><!-- Map -->';
			$out .= '<div class="toggle-navigation">';
			$out .= '<div class="icon">';
			$out .= '<div class="line"></div>';
			$out .= '<div class="line"></div>';
			$out .= '<div class="line"></div>';
			$out .= '</div>';
			$out .= '</div><!--/.toggle-navigation-->';
			$out .= '<div id="map-'.$id_.'" class="has-parallax map-wrapper shortcode-map-wrapper" data-start_lat="'.esc_js($lat).'" data-start_lng="'.esc_js($lng).'" data-default_zoom="'.esc_js($pb_zoom).'" data-auto_zoom="'.$pb_is_auto.'" 
			data-items_number_max="'.esc_js($pb_max_items).'" data-tax_city ="'.esc_js($pb_tax_cities).'"></div>';
			 if ($pb_is_search) {
			 	ob_start();
				 if($pb_search_layout != 'Vertical') {
					zoner_generate_search_box(array('col-md-3', 'col-sm-4', 'zoner-dinamic-search', 'horizontal-search-shortcode', 'horizontal-search-float', 'shortcode-searchform'),1, 1);
				} else {
					zoner_map_shortcode_vartical_form();	
				}
			 	$out .= ob_get_contents();
			 	ob_end_clean();   
			} 

			$out .= '</div><!-- end Map -->';
			$out .= '</div><!-- end Map Canvas-->';
			if (!empty($pb_is_items)){
					$out .= '<div id="items-list-'.$id_.'" class="items-list">';
					$out .= '<div class="inner">';
					$out .='<ul class="results">';
					$out .= '</ul>';
					$out .= '</div><!--end Results-->';
					$out .= '</div><!--end Items List-->';
					
				}
			$out .= '</div>';
			return $out;
			
		}

		static function array_of_loc_push(){
			global $zoner, $prefix;
			$result = array();
			$args = array(
				'post_type' 		=> 'property',
				'post_status' 		=> 'publish',
				'posts_per_page'	=> -1
			);
			$query_items = new WP_Query($args);
			
			if ( $query_items->have_posts() ) {
				while ( $query_items->have_posts() ) {
						$query_items->the_post();
						$item = $zoner->property->get_property(get_the_ID());
					if (!empty($item->lat) && !empty($item->lat)) {
						$result[] = $item;
					}		
				}
			}
			return $result;
		}
		static function zoner_gmap_ajax_items(){
			$_REQUEST['advanced_map'] = 1;
			if (!empty($_REQUEST['serialize_form'])){
				$_GET['filter_property'] = wp_create_nonce( 'zoner_filter_property' );
				foreach ($_REQUEST['serialize_form'] as $get) {
					$_GET[$get['name']] = $get['value'];
				}
			}
			echo zoner_get_map_points_array();
			unset($_REQUEST['advanced_map']);
			die();
		}

	static function zoner_gmaps_multichoise() {
		global $prefix, $zoner, $zoner_config,$inc_theme_url, $post;
		$out = $outproperties = null;
		
		if ( isset($_REQUEST) && isset($_REQUEST['action']) && ($_REQUEST['action'] == '_zoner_get_multiitems_'))  {
			
			$same_lat = esc_attr($_REQUEST['sameLatitude']);
			$same_lng = esc_attr($_REQUEST['sameLongitude']);
			$args = array(
				'post_type' 		=> 'property',
				'post_status' 		=> 'publish',
				'posts_per_page'	=> -1,
				'meta_query' 		=> array(
				 		'relation' => 'OR',
				 		array(
				 			'key'     => $prefix.'lat',
				 			'value'   => array($same_lat-0.0001,$same_lat+0.0001),
				 			'compare' => 'BETWEEN',
							'type'    => 'DEMICAL'
				 		),
				 		array(
				 			'key'     => $prefix.'lng',
				 			'value'   => array($same_lng-0.0001,$same_lng+0.0001),
				 			'compare' => 'BETWEEN',
							'type'    => 'DEMICAL'
				 		)
				 )
			);
			
			$query_property = new WP_Query($args);
				if ( $query_property->have_posts() ) {
				foreach( $query_property->posts as $post ) {
					$outproperties .= zoner_get_property_grid_items_original(false);
				}
			}	
		
			$out .= '<div class="modal-wrapper">';
				$out .= '<h2>'.__('Multiple properties in one location', 'zoner').'</h2>';
				$out .= '<div class="modal-body"><ul class="items list-unstyled">'.$outproperties.'</ul></div>';
				$out .= '<div class="modal-close"><img src="'.$inc_theme_url.'/assets/img/close-btn.png"></div>';
			$out .= '</div>';
			$out .= '<div class="modal-background fade_in"></div>';
		
		
			echo $out;
 
		}
		
		die('');
	}
}	