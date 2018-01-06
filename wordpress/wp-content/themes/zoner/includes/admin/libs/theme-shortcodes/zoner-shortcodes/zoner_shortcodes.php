<?php
	global $prefix;
    $prefix = '_zoner_';

	define('ZONER_SHORTCODE_DIR', 	   dirname(__FILE__));
	define('ZONER_SHORTCODE_PATTERNS', get_template_directory_uri() . '/includes/admin/libs/theme-shortcodes/zoner-shortcodes/patterns/');
	define('ZONER_SHORTCODE_JS', 	   get_template_directory_uri() . '/includes/admin/libs/theme-shortcodes/zoner-shortcodes/patternsJs/');
	define('ZONER_SHORTCODE_CSS', 	   get_template_directory_uri() . '/includes/admin/libs/theme-shortcodes/zoner-shortcodes/patternsCss/');

	/*Headline Text*/
	function zoner_headlinetext_func($atts = array(), $content = null) {
		$headline_title = $headline_additional_title =  $pb_title_tag  = $pb_additional_tag = $headline_text_color = $pb_margin = $pb_dsep = $el_class = '';
			
		$atts = vc_map_get_attributes( 'zoner_headlinetext', $atts );
		extract( $atts );
		
		$out_ = $class = $reset_margin = $dsep = '';
		if ($pb_margin) { $reset_margin = ' reset-margin'; }
		if ($pb_dsep) 	{ $pb_dsep = ' dsep'; }
		
		$out_ .= '<div class="center">';
			$out_ .= '<div class="section-title '.$el_class.$pb_dsep.'">';
				if (!empty($headline_additional_title)) {
					$class = 'has-subtitle';
				}
				
				$out_ .= '<'.$pb_title_tag.' style="color:'.$headline_text_color.';" class="'.$class.'">'.$headline_title.'</'.$pb_title_tag.'>';
				if (!empty($headline_additional_title)) {
					$out_ .= '<'.$pb_additional_tag.' style="color:'.$headline_text_color.';" class="has-opacity additional'. $reset_margin .'">'.$headline_additional_title.'</'.$pb_additional_tag.'>';
				}	
			$out_ .=	'</div>';
		$out_ .= '</div>';
		return $out_;
		
	}
	add_shortcode('zoner_headlinetext', 'zoner_headlinetext_func');
	
	/*Services*/
	function zoner_infobox_func($atts = array(), $content = null) {
		$sb_title = $sb_text_icons = $sb_title_color = $sb_content_color = $sb_icon_color = $el_class = '';
			
		$atts = vc_map_get_attributes( 'zoner_infobox', $atts );
		extract( $atts );
		
		$out_ = $style = $figure = '';
		$sb_content = do_shortcode(wpb_js_remove_wpautop($content, true));

		if (!empty($sb_text_icons) && $sb_text_icons != -1) {
			$figure .= '<figure class="icon">';
				$figure .= '<i class="fa '.$sb_text_icons.'"></i>';
			$figure .= '</figure>';		
		}	

		
		$link = ($link=='||') ? '' : $link;
		$link = vc_build_link($link);
		
		$a_href 	= $link['url'];
		$a_title 	= $link['title'];
		$a_target 	= $link['target'];
			
		
		$elem_class  = array();
		$elem_class[] = 'feature-box';
		$elem_class[] = 'equal-height';
		$elem_class[] = $el_class;
		
		$out_ .= '<div class="'.implode(' ', $elem_class).'">';
			$out_ .= $figure;
			$out_ .= '<aside class="description">';
				$out_ .= '<header><h3>'.$sb_title.'</h3></header>';
				$out_ .= $sb_content;
                   if (!empty($a_title)) $out_ .= '<a href="'.esc_url($a_href).'" target="'.esc_attr($a_target).'" title="'.$a_title.'" class="link-arrow">'.$a_title.'</a>';
            $out_ .= '</aside>';
        $out_ .= '</div><!-- /.feature-box -->';
		
		return $out_;
	}
	add_shortcode('zoner_infobox', 'zoner_infobox_func');
	
	function zoner_pricebox_func($atts = array(), $content = null) {
		$out_= '';
		$pb_package = $pb_price_package = $pb_period = $pb_featured = $pb_show_button = $pb_link_title = $pb_link_target = $promoted = '';
		
		$atts = vc_map_get_attributes( 'zoner_pricebox', $atts );
		extract( $atts );
		
		
		$a_href = $a_title = $a_target = '';
		
		
		$a_href 	= esc_url($pb_link_url);
		$a_title 	= esc_attr($pb_link_title);
		$a_target 	= esc_attr($pb_link_target);
		
		$btn_class = array();
		$btn_class[] = 'btn';
		$btn_class[] = 'btn-default';
		
		$elem_class = array();
		$elem_class[] = 'price-box';
		
		if ($pb_featured) { 
			$elem_class[]  = 'promoted';
			$btn_class[] = 'btn';
			$btn_class[] = 'btn-default';
		}	
		
		$content = do_shortcode(wpb_js_remove_wpautop($content, true));
		
						
		$out_ .= '<div class="'.implode(' ', $elem_class).'">';
			$out_ .= '<header>';
				$out_ .= '<h2>'.esc_html($pb_package).'</h2>';
			$out_ .= '</header>';
			
			$out_ .= '<div class="price">';
				$out_ .= '<figure>'.esc_html($pb_price_package).'</figure>';
				$out_ .= '<small>'.esc_html($pb_period).'</small>';
			$out_ .= '</div>';

			$out_ .= $content; 

			if (!empty($a_title) && ($pb_show_button == 'true')) {
				$out_ .= '<a class="'.implode(' ' ,  $btn_class).'" href="'.esc_url($a_href).'" title="'.esc_attr($a_title).'" target="'.$a_target.'">';
					$out_ .= $a_title; 
				$out_ .= '</a>';
			}
					
		$out_ .= '</div><!-- /.price-box -->';
		
		
		if ($out_ != '') {
			return $out_;
		} 
	}
	add_shortcode('zoner_pricebox', 'zoner_pricebox_func');
	
	
	/*Faq's*/
	function zoner_faqs_func($atts = array(), $content = null) {
		$out = $el_class = $pz_count_items = $pz_items_id = $pz_order_by = $pz_order = $pz_tax_slug = $pz_add_helpfull = '';

		$atts = vc_map_get_attributes( 'zoner_faq', $atts );
		extract( $atts );
		
		$rand_id = 'faqs-' . rand(0, 250);
		
		$tax_include = $faq_include = $qargs = $classes = array();
		
		$classes[] = 'faqs';
		if (!empty($el_class)) $classes[] = $el_class;

		if (!empty($pz_items_id)) $faq_include = explode(",", $pz_items_id);
		
		if (empty($pz_count_items)) $pz_count_items = -1;
		
		$qargs = array(
					'post_type' 	 => 'faq',
					'post_status' 	 => 'publish',
					'posts_per_page' => esc_attr($pz_count_items),
					'ignore_sticky_posts' => 1,
					'orderby' 		 => esc_attr($pz_order_by), 
					'order' 		 => esc_attr($pz_order),
		);
		if (!empty($pz_tax_slug)) {
			$qargs['tax_query'] = array(
										array(
											'taxonomy' => 'faq_tax',
											'field' => 'slug',
											'terms' => $pz_tax_slug,
											)
								  );
		}


		if (!empty($faq_include)) {
			$qargs['post__in'] = $faq_include;
		}							
		
		
		$qfaq = new WP_Query($qargs);
		if ( $qfaq->have_posts() ) {
			$out = '<section id="'.$rand_id.'" class="'.implode(' ', $classes).'">';
				while ( $qfaq->have_posts() ) {
					$qfaq->the_post();
					$out .= '<article id="faq-item'.get_the_ID().'" class="faq">';	
						$out .= '<figure class="icon">'.__('Q', 'zoner') .'</figure>';
						$out .= '<div class="wrapper">';
							$out .= '<header>'.get_the_title().'</header>';
						$out .= '</div>';
						
						$full_content = '';
						$full_content = apply_filters( 'the_content', get_the_content() );
						$full_content = str_replace( ']]>', ']]&gt;', $full_content );
						
						$out .= $full_content;
						if ($pz_add_helpfull) {
							$out .= '<aside class="answer-votes">';
								$out .= __('Was this answer helpful?', 'zoner');
								$out .= '<a data-faqid="'.get_the_ID().'" class="faq-help-yes" href="#">'. __('Yes', 'zoner').'</a>';
								$out .= '<a data-faqid="'.get_the_ID().'" class="faq-help-no" href="#">'. __('No', 'zoner').'</a>';
							$out .= '</aside>';
						}	
						
					$out .= '</article>';
		
				}
			$out .= '</section>';
		} else {
			return '<div class="alert alert-danger"><strong>'.__("FAQ's list is empty.", 'zoner').'</strong> '. __('Verify that the data set!', 'zoner') .'</div>';
		}

		
		wp_reset_postdata();	
		
		if (!empty($out)) return $out;
	}
	add_shortcode('zoner_faq', 'zoner_faqs_func');
	
	
	/*Timeline's*/
	function zoner_timeline_func($atts = array(), $content = null) {
		$out = $el_class = $pz_count_items = $pz_items_id = $pz_tax_slug = $pz_add_featured_image = $pz_order_by = $pz_order = '';

		$atts = vc_map_get_attributes( 'zoner_timeline', $atts );
		extract( $atts );
		
		$rand_id = 'timeline-' . rand(0, 250);
		
		$tax_include = $faq_include = $qargs = $classes = array();
		
		$classes[] = 'timeline';
		if (!empty($el_class)) $classes[] = $el_class;
		

		if (!empty($pz_items_id)) $timeline_include = explode(",", $pz_items_id);
		
		if (empty($pz_count_items)) $pz_count_items = -1;
		
		$qargs = array(
					'post_type' 	 => 'timeline',
					'post_status' 	 => 'publish',
					'posts_per_page' => esc_attr($pz_count_items),
					'ignore_sticky_posts' => 1,
					'orderby' 		 => $pz_order_by, 
					'order' 		 => $pz_order,
		);
						
		if (!empty($pz_tax_slug)) {
			$qargs['tax_query'] = array(
										array(
											'taxonomy' => 'timeline_tax',
											'field' => 'slug',
											'terms' => $pz_tax_slug,
											)
								  );
		}			
		
		
		if (!empty($timeline_include)) {
			$qargs['post__in'] = $timeline_include;
		}							
		
		
		$qfaq = new WP_Query($qargs);
		if ( $qfaq->have_posts() ) {
			$out = '<section id="'.$rand_id.'" class="'.implode(' ', $classes).'">';
				while ( $qfaq->have_posts() ) {
					$qfaq->the_post();
					$out .= '<article id="timeline-item-'.get_the_ID().'" class="timeline-item">';	
						$out .= '<div class="row">';
							$out .= '<div class="col-md-1">';
								$out .= '<div class="circle">';
									$out .= '<figure class="dot"></figure>';
									$out .= '<div class="date">'.get_the_date('d/m/Y').'</div>';
								$out .= '</div>';
							$out .= '</div>';
								
							$full_content = '';
							$full_content = apply_filters( 'the_content', get_the_content() );
							$full_content = str_replace( ']]>', ']]&gt;', $full_content );	
							
							$out .= '<div class="col-md-11">';
								$out .= '<div class="wrapper">';
									$out .= '<header><h3>'.get_the_title().'</h3></header>';
									
									
									if ($pz_add_featured_image) {
										if (has_post_thumbnail(get_the_ID())) {
											
											$image_ = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), 'full');
											$image_ = esc_url($image_[0]);
											
											$out .= '<a class="image-popup timeline-image" href="'.$image_.'">';
												$out .= '<img src="'.$image_.'">';
											$out .= '</a>';
										}
									}	

									$out .= $full_content;
								$out .= '</div>';
							$out .= '</div>';
						$out .= '</div>';
					$out .= '</article>';
				}
			$out .= '</section>';
		} else {
			return '<div class="alert alert-danger"><strong>'.__('Faq list is empty.', 'zoner').'</strong> '. __('Verify that the data set!', 'zoner') .'</div>';
		}

		
		wp_reset_postdata();	
		
		if (!empty($out)) return $out;
	}
	add_shortcode('zoner_timeline', 'zoner_timeline_func');
	
	
	/*Google Maps*/
	class zoner_ggmaps_shortcode {
		static function init() {
			add_action   ('wp_footer', array(__CLASS__, 'zoner_gmap_jscript'));
			add_shortcode('zoner_gmaps', 	array(__CLASS__, 'zoner_gmaps_func'));
			
		}

		static function zoner_gmap_jscript() {
			if (!empty(self::$shortcode_id)){
				zoner_scripts_map(true);//parse google maps
			}
		}
		static $shortcode_id = 0;
		static function zoner_gmaps_func($atts = array()) {
			global $zoner_config;
			$out_ = $pb_title = $pb_image = $pb_label_content = $pb_latlng = $pb_size = $pb_type = $pb_zoom = $pb_scroll = $pb_fullwidth = $el_class = '';	
			$zoner_config['is-gmap-api'] = 1;
			
			$atts = vc_map_get_attributes( 'zoner_gmaps', $atts );
			extract( $atts );
			
			$id_map = 'ggmaps-' . self::$shortcode_id;
			self::$shortcode_id++;
			$class_fullwidth = '';
			
			if ($pb_fullwidth) {
				$class_fullwidth = ' fullwidth';
			}
			
			$out_ = '<div id="'.$id_map.'" class="map '.$class_fullwidth.'" style="height:'.$pb_size.'px; width:100%;"></div>';
			
			$icon_map  = '';
			if (!empty($pb_latlng )) {
				$position = $pb_latlng;
			}
			
			if (!empty($pb_image)) {
				$icon_map = wp_get_attachment_image_src( $pb_image, 'full');
				$icon_map = $icon_map[0];
			}	

			$map_type = "google.maps.MapTypeId.ROADMAP";
			$custom_types = "styles: [{featureType:'water',elementType:'all',stylers:[{hue:'#d7ebef'},{saturation:-5},{lightness:54},{visibility:'on'}]},{featureType:'landscape',elementType:'all',stylers:[{hue:'#eceae6'},{saturation:-49},{lightness:22},{visibility:'on'}]},{featureType:'poi.park',elementType:'all',stylers:[{hue:'#dddbd7'},{saturation:-81},{lightness:34},{visibility:'on'}]},{featureType:'poi.medical',elementType:'all',stylers:[{hue:'#dddbd7'},{saturation:-80},{lightness:-2},{visibility:'on'}]},{featureType:'poi.school',elementType:'all',stylers:[{hue:'#c8c6c3'},{saturation:-91},{lightness:-7},{visibility:'on'}]},{featureType:'landscape.natural',elementType:'all',stylers:[{hue:'#c8c6c3'},{saturation:-71},{lightness:-18},{visibility:'on'}]},{featureType:'road.highway',elementType:'all',stylers:[{hue:'#dddbd7'},{saturation:-92},{lightness:60},{visibility:'on'}]},{featureType:'poi',elementType:'all',stylers:[{hue:'#dddbd7'},{saturation:-81},{lightness:34},{visibility:'on'}]},{featureType:'road.arterial',elementType:'all',stylers:[{hue:'#dddbd7'},{saturation:-92},{lightness:37},{visibility:'on'}]},{featureType:'transit',elementType:'geometry',stylers:[{hue:'#c8c6c3'},{saturation:4},{lightness:10},{visibility:'on'}]}]";
	
			switch ($pb_type) {
				case "r":
					$map_type = "google.maps.MapTypeId.ROADMAP";
					$custom_types = '';
				break;
				case "s":
					$map_type = "google.maps.MapTypeId.SATELLITE";
					$custom_types = '';
				break;
				case "h":
					$map_type = "google.maps.MapTypeId.HYBRID";
					$custom_types = '';
				break;
				case "t":
					$map_type = "google.maps.MapTypeId.TERRAIN";
					$custom_types = '';
				break;
			}
			
			if (!empty($pb_scroll)) {$pb_scroll = 'true'; } else {$pb_scroll = 'false'; }
			
			$out_ .= '
			<!-- Google Maps -->
			<script type="text/javascript">
			jQuery(document).ready( function(){
				if (typeof(google) != \'undefined\') {
				google.maps.event.addDomListener(window, "load", init);
				function init() {
					var center = new google.maps.LatLng('.$position.');
					var mapOptions = {
						zoom: '.$pb_zoom.',
						center: center, 
						disableDefaultUI: false,
						scrollwheel: '.$pb_scroll.',
						mapTypeId:   '.$map_type .',
						'.$custom_types.'
					};
	
					var mapElement = document.getElementById("'.$id_map.'");
					var map = new google.maps.Map(mapElement, mapOptions);
					var marker = new google.maps.Marker({
							position: center,
							map: map,
							title:"'.$pb_title.'",
							icon: "'.$icon_map.'",
							animation:   google.maps.Animation.DROP
						});
						
						google.maps.event.addListener(marker, "click", toggleBounce);
						google.maps.event.addDomListener(window, "resize", function() {
							map.setCenter(center);
						});
						
						function toggleBounce() {
							if (marker.getAnimation() != null) {
								marker.setAnimation(null);
							} else {
								marker.setAnimation(google.maps.Animation.BOUNCE);
							}
						}
					}
				
					jQuery(window).resize(function() {
					
					});
					}
				});
			</script>
			<!-- Google Maps -->';
			
			return $out_;
		}
		
	}		
	
	/*Initialize map*/
	zoner_ggmaps_shortcode::init();
	
	
	/*Button shortcode*/
	function zoner_btn_func($atts = array()) {
		$out_  = $pb_text_button = $pb_textcolor = $pb_textcolorhover = $pb_bgcolor = $pb_bgcolorhover = $pb_bordercolor = $pb_bordercolorhover = $pb_position = $link = $pb_target  = $el_class = '';
		
		$atts = vc_map_get_attributes( 'zoner_button', $atts );
		extract( $atts );
	
	
		$a_href = $a_title = $a_target = $style = '';
	
		$link = ($link=='||') ? '' : $link;
		$link = vc_build_link($link);
	
		$a_href 	= $link['url'];
		$a_title 	= $link['title'];
		$a_target 	= $link['target'];
	
		$id_button = 'zoner-btn-' . rand(1, 250);
		
		$style .= '#' . $id_button . ' { color:'.$pb_textcolor.'; background:'.$pb_bgcolor.'; border-color:'.$pb_bordercolor.'; } ';
		$style .= '#' . $id_button . ':hover { color:'.$pb_textcolorhover.'; background:'.$pb_bgcolorhover.'; border-color:'.$pb_bordercolorhover.'; } ';
		
		if (!empty($link)) {
			$link = '#';
		} else {
			$link = esc_url($link);
		}
		
		$array_of_class[] = 'btn';
		
		if ($el_class)
		$array_of_class[] = $el_class;
	
		$array_of_wrapper[] = 'zoner-btn-wrapper';
		if ($pb_position)
		$array_of_wrapper[] = $pb_position;
	
		if ($pb_fullwidth)
		$array_of_wrapper[] = 'fullwidth';
		
		$out_ .= '<style type="text/css">'.$style.'</style>';
		$out_ .= '<div class="'.implode(" ", $array_of_wrapper).'">';
			if (!empty($a_title) && !empty($a_href)) {
				$out_ .= '<a id="'.$id_button .'" title="'.esc_html($a_title).'" href="'.esc_url($a_href).'" target="'.$a_target.'" class="'.implode(' ', $array_of_class).'">'.esc_html($pb_text_button).'</a>';
			} else {
				$out_ .= '<button id="'.$id_button.'" class="'.implode(' ', $array_of_class).'">'.esc_html($pb_text_button).'</button>';
			}
		$out_ .= '</div>';
	
		return $out_;
	}
	
	add_shortcode('zoner_button', 'zoner_btn_func');
	
	
	function zoner_separator_func($atts = array(), $content = null) {
		$out_  = $pb_bordercolor = $pb_style = $pb_width = $pb_size = $pb_margin = '';
		$atts = vc_map_get_attributes( 'zoner_separator', $atts );
		extract( $atts );
		
		if (empty($pb_style)) { $pb_style = "solid"; }
		$out_ .= '<div class="separator" style="display:block; border-bottom-color:'.$pb_bordercolor.'; border-bottom-style:'.$pb_style.'; border-width:'.$pb_size.'; width:'.$pb_width.'%; margin:'.$pb_margin.' auto '.$pb_margin.';"></div>';
		return $out_;
	}	
	add_shortcode('zoner_separator', 'zoner_separator_func');
	
	
	function zoner_icon_func($atts = array(), $content = null) {
		$out_  = $sb_icon = $sb_icon_size = $sb_icon_padding = $sb_icon_color = $sb_icon_position = $el_class = '';
		$atts  = vc_map_get_attributes( 'zoner_icon', $atts );
		extract( $atts );
		
		$arr_of_class = array();
		$arr_of_class[] = 'icon-box';
		if (!empty($sb_icon_position))
		$arr_of_class[] = $sb_icon_position;
		if (!empty($sb_icon_position))
		$arr_of_class[] = $el_class;
		
		$out_ .= '<div class="'.implode(" ", $arr_of_class).'">';
			$out_ .= '<i style="font-size:'.esc_attr($sb_icon_size).'; color:'.esc_attr($sb_icon_color).'; padding:'.$sb_icon_padding.'; " class="fa '.$sb_icon.'"></i>';
		$out_ .= '</div>';
		return $out_;
	}	
	add_shortcode('zoner_icon', 'zoner_icon_func');
	
	
	/*Register shortcode*/
	class zoner_register_user_form_shortcode {
		static function init() {	
			add_action   ('wp_enqueue_scripts',  array(__CLASS__, 'zoner_registerForm_jscript'));
			add_shortcode('zoner_register_form', array(__CLASS__, 'zoner_custom_register_user'));
			
			add_action( 'wp_ajax_nopriv_zoner_reg_user_account_is_email_exists', array(__CLASS__, 'zoner_is_user_email_exists') );
			add_action( 'wp_ajax_nopriv_zoner_reg_user_account_is_login_exists',  array(__CLASS__, 'zoner_is_login_name_exists') );
		}
		
		static function zoner_is_user_email_exists() {
			if (isset($_POST) && ($_POST['action'] == 'zoner_reg_user_account_is_email_exists')) {
				$email = $_POST['ca-email'];
				if (email_exists( $email )) { echo 'false'; } else { echo 'true'; }
			}	
			die('');
		}
		
		static function zoner_is_login_name_exists() {
			if (isset($_POST) && ($_POST['action'] == 'zoner_reg_user_account_is_login_exists')) {
				$login_name = $_POST['ca-login-name'];
				if (username_exists( $login_name )) { echo 'false'; } else { echo 'true'; }
				
			}	
			die('');
		}
				
		static function zoner_registerForm_jscript() {
			wp_enqueue_script( 'zoner-registerForm',  ZONER_SHORTCODE_JS . 'registerForm.js', array( 'jquery' ), '20140808', true );
			
			wp_localize_script( 'zoner-registerForm', 'zonerRegisterUserForm', 	array( 	
																			'valid_email_mess' 	=> __('Please enter your unique email', 'zoner'),	
																			'valid_login_mess' 	=> __('Please enter your unique login', 'zoner')
																		 )
															);  
		}
		
		
		static function zoner_custom_register_user($atts = array()) {
			global $zoner;
			$out_html  = $el_class = $sb_term_cond = $sb_user_type = '';
		
			$atts  = vc_map_get_attributes( 'zoner_register_form', $atts );
			extract( $atts );
			
			$r_user = $a_user = '';		
			
			$invite_user_hash = $login_name = $f_name = $l_name = $email = '';
			$invite_user_hash = get_query_var( 'invitehash' );
			$agency_id = $invite_id = -1;
			
			if (!empty($invite_user_hash)) {
				$a_user = 'checked=checked';			
				$invite_user_info = $zoner->invites->zoner_get_invite_user_info($invite_user_hash);

				if (!empty($invite_user_info)) {
					$invite_user_info = current($invite_user_info);
					$agency_id  = $invite_user_info->agency_id;
					$login_name = sanitize_title($invite_user_info->user_temporary_name);
					$fl_name = explode(' ',  $invite_user_info->user_temporary_name);
					if (!empty($fl_name[0])) $f_name = $fl_name[0];
					if (!empty($fl_name[1])) $l_name = $fl_name[1];
					$email = $invite_user_info->user_email;
					
					$invite_id = $invite_user_info->invite_id;
				}
			}
			
			
			$form_class = array();
			$form_class[] = $el_class;
			$form_class[] = 'form-create-account';
			
			if (empty($sb_user_type)) $sb_user_type = 1;
			
			if ( !is_user_logged_in() ) {
			
				$out_html .= '<h3>'.__('Account Type', 'zoner') .'</h3>';
				$out_html .= '<form role="form" id="form-create-account" class="'. implode(' ', $form_class ) .'" name="form-create-account" method="post" action="">';
					
					$out_html .= wp_nonce_field( 'zoner_create_profile', 'create_profile', true, true ); 

					if (empty($invite_user_hash) && ($sb_user_type != 3)) {
						$r_user = 'checked=checked';
						$out_html .= '<div class="radio" id="create-account-user">';
							$out_html .= '<label><input type="radio" id="account-type-user" value="1" name="account-type" required '.$r_user.'>'. __('Regular User', 'zoner') .'</label>';
						$out_html .= '</div>';
					}	

					if ($sb_user_type == 3) {
						$a_user = 'checked=checked';
					}

					if ($sb_user_type != 2) {
						$out_html .= '<div class="radio" id="agent-switch" data-agent-state="">';
							$out_html .= '<label><input type="radio" id="account-type-agent" value="2"  name="account-type" required '.$a_user.'>'. __('Agent', 'zoner').'</label>';
						$out_html .= '</div>';
					}					

					if (!empty($invite_user_hash)) {
						$out_html .= '<div class="form-group">';
							$out_html .= '<label for="ca-agencyname">'. __('Agency name', 'zoner').':</label>';
							$out_html .= '<input type="text" class="form-control" id="ca-agencyname" name="ca-agencyname" value="'. get_the_title($agency_id).'" disabled="disabled">';
							$out_html .= '<input type="hidden" name="ca-invite-user" value="'.$invite_id.'" />';
						$out_html .= '</div><!-- /.form-group -->';
					} 
					
					$out_html .= '<div class="form-group">';
						$out_html .= '<label for="ca-login-name">'. __('Login name', 'zoner').':</label>';
						$out_html .= '<input type="text" class="form-control" id="ca-login-name" name="ca-login-name" value="'.$login_name.'" required>';
					$out_html .= '</div><!-- /.form-group -->';
					
					$out_html .= '<div class="form-group">';
						$out_html .= '<label for="ca-first-name">'. __('First Name', 'zoner').':</label>';
						$out_html .= '<input type="text" class="form-control" id="ca-first-name" name="ca-first-name" value="'.$f_name.'" required>';
					$out_html .= '</div><!-- /.form-group -->';

					$out_html .= '<div class="form-group">';
						$out_html .= '<label for="ca-last-name">'. __('Last Name', 'zoner').':</label>';
						$out_html .= '<input type="text" class="form-control" id="ca-full-name" name="ca-last-name" value="'.$l_name.'" required>';
					$out_html .= '</div><!-- /.form-group -->';


					$out_html .= '<div class="form-group">';
						$out_html .= '<label for="ca-email">'. __('Email', 'zoner').':</label>';
						$out_html .= '<input type="email" class="form-control" id="ca-email" name="ca-email" value="'.$email.'" required>';
					$out_html .= '</div><!-- /.form-group -->';
					
					$out_html .= '<div class="form-group clearfix">';
						$out_html .= '<button type="submit" class="btn pull-right btn-default" id="account-submit">'. __('Create an Account', 'zoner').'</button>';
					$out_html .= '</div><!-- /.form-group -->';
				$out_html .= '</form>';
				$out_html .= '<hr>';

				if (isset($sb_term_cond) && ($sb_term_cond != -1))	 {
					$out_html .= '<div class="center">';
						$out_html .= '<figure class="note">'.sprintf(__('By clicking the “Create an Account” button you agree with our <a href="%1$s" target="_blank">Terms and conditions</a>', 'zoner'), get_permalink($sb_term_cond)) .'</figure>';
					$out_html .= '</div>';
				}	

			
			} else {
				$out_html = '<div class="alert alert-info">';
					$out_html .= '<a href="#" class="close" data-dismiss="alert">&times;</a>';
					$out_html .= __('For register a new user, please logout.', 'zoner');
					$out_html .= '<strong><a href="'.wp_logout_url(home_url()).'"> '.__('Click to Logout', 'zoner') .'</a></strong>';
				$out_html .= '</div>';
			}
			
			
			if (!empty($invite_user_hash) && ($invite_id == -1)) {
				$out_html = '<div class="alert alert-info">';
					$out_html .= '<a href="#" class="close" data-dismiss="alert">&times;</a>';
					$out_html .= __('Your hash code is not valid!', 'zoner');
				$out_html .= '</div>';
			}
			
			
			return $out_html;
		}
		
	}		
	
	/*Initialize register form*/
	zoner_register_user_form_shortcode::init();
	
	if ( ! function_exists( 'zoner_create_user_account_func' ) ) {
		function zoner_create_user_account_func() {
			global $zoner_config, $prefix, $zoner;
			
			$userdata = array();
			$role = 'subscriber';
			$invite_user = -1;
			
			if ( isset($_POST['create_profile']) && wp_verify_nonce($_POST['create_profile'], 'zoner_create_profile')) {
				$user_password = wp_generate_password(8, true);				
				
				if ($_POST['account-type'] == 2) $role = 'agent';
					$userdata['user_login'] = sanitize_user($_POST['ca-login-name']);
					$userdata['first_name'] = $_POST['ca-first-name'];
					$userdata['last_name']	= $_POST['ca-last-name'];
					$userdata['user_email']	= sanitize_email($_POST['ca-email']);
					$userdata['user_pass']	= $user_password;
					$userdata['role']		= $role;
				
					$user_id = wp_insert_user( $userdata );
				
				if(!is_wp_error($user_id)) {
					if (isset($_POST['ca-invite-user'])) { 
						$invite_id 	= $_POST['ca-invite-user'];
						$table_name = 'zoner_agent_from_agencies';
						$arr_field 	= array (
							'invite_hash' 	=> null,
							'user_id' 		=> $user_id,
							'status'  		=> 1
						);
						
						$arr_where = array(
							'invite_id' => $invite_id
						);
						
						$array_of_type = array('%s', '%d', '%d');
						$array_of_type_where = array ('%s');
						$zoner->zoner_update_table($table_name, $arr_field, $arr_where, $array_of_type, $array_of_type_where);
					}
					
					do_action('zoner_user_register', $user_id, $user_password);
					wp_redirect( add_query_arg(array('created_user' => 'true'), esc_url(get_permalink($zoner->zoner_get_page_id('page-signin')))));
					
					exit;
				}
			}
		}
	}	
	add_action('wp', 'zoner_create_user_account_func', 298);
	
		
	class zoner_signin_form_shortcode {
		
		static function init() {
			add_shortcode('zoner_signin', array(__CLASS__, 'zoner_custom_signin_form'));
			add_action   ('wp_enqueue_scripts',  array(__CLASS__, 'zoner_signInForm_jscript'), 700);
			
			add_action( 'wp_ajax_nopriv_zoner_signin_user_email_exists', array(__CLASS__, 'zoner_is_user_email_exists') );
			add_action( 'wp_ajax_nopriv_zoner_signin_user_pass_exists',  array(__CLASS__, 'zoner_is_pass_exists') );
			
			add_action( 'wp_ajax_nopriv_zoner_ajax_social_login_facebook', array(__CLASS__, 'zoner_connect_facebook') );
			add_action( 'wp_ajax_zoner_ajax_social_login_facebook', array(__CLASS__, 'zoner_connect_facebook') );
			
			add_action( 'wp_ajax_nopriv_zoner_ajax_social_login_twitter',  array(__CLASS__, 'zoner_connect_twitter') );
			add_action( 'wp_ajax_zoner_ajax_social_login_twitter',  array(__CLASS__, 'zoner_connect_twitter') );
			
			add_action( 'wp_ajax_nopriv_zoner_ajax_social_login_google',   array(__CLASS__, 'zoner_connect_google') );
			add_action( 'wp_ajax_zoner_ajax_social_login_google',   array(__CLASS__, 'zoner_connect_google') );
		}

		static function zoner_connect_facebook() {
			global $zoner, $zoner_config;
				   $fapi = $fsecret = $loginUrl = '';
				   $array_out = array();

			$errMsg =  __('Please check the Facebook Api Key and Secret Code.', 'zoner');
			if (empty($_POST['action']) && ($_POST['action'] != 'zoner_ajax_social_login_facebook')) return;

            require_once  ZONER_SHORTCODE_DIR.'/vc_social_connect/Facebook/autoload.php';

			if (!empty($zoner_config['facebook-api-key']))
			$fapi 	 = esc_html ($zoner_config['facebook-api-key']);
			if (!empty($zoner_config['facebook-secret-code']))
			$fsecret = esc_html ($zoner_config['facebook-secret-code']);
			
			if (!empty($fapi) && !empty($fsecret)) {

				$fClass = new Facebook\Facebook([
                    'app_id'        => $fapi, // Replace {app-id} with your app id
                    'app_secret'    => $fsecret,
                    'default_graph_version' => 'v2.8',
                ]);

                $helper = $fClass->getRedirectLoginHelper();
                $permissions = ['email'];
                $loginUrl = $helper->getLoginUrl(htmlspecialchars(home_url('/')), $permissions);

				if (!empty($loginUrl)) {
					$array_out = array("link" => $loginUrl, 'errorMessage' => '');
				} else {
					$array_out = array("link" => '', 'errorMessage' => $errMsg);
				}				
			} else {
					$array_out = array("link" => '', 'errorMessage' => $errMsg);
			}
			
			echo json_encode($array_out);
			die();
		}
		
		static function zoner_connect_google() {
			global $zoner, $zoner_config;
			$gclient_id = $gclient_secret = $gdev_key = '';
			$array_out = array();
			
			$errMsg =  __('Please check the Google Client ID, Secret, Api Key.', 'zoner');
			if (empty($_POST['action']) && ($_POST['action'] != 'zoner_ajax_social_login_google')) return;
		
		
			if(!empty($zoner_config['google-oauth-client-id']))
			$gclient_id = esc_html ( $zoner_config['google-oauth-client-id']);
			if(!empty($zoner_config['google-client-secret']))
			$gclient_secret = esc_html ( $zoner_config['google-client-secret']);
			if(!empty($zoner_config['google-api-key']))
			$gdev_key = esc_html ( $zoner_config['google-api-key']);
			
			if (!empty($gclient_id) && !empty($gclient_secret) && !empty($gdev_key)) {
				$gUser = $loginUrl = '';
				require_once  ZONER_SHORTCODE_DIR.'/vc_social_connect/googleoauth/autoload.php';
				$client = new Google_Client();
				$client->setApplicationName(sprintf(__('Login to 1%s', 'zoner'), get_bloginfo('name')));
				$client->setClientId($gclient_id);
				$client->setClientSecret($gclient_secret);
				$client->setRedirectUri(home_url(''));
				$client->setDeveloperKey($gdev_key);
				$client->addScope("https://www.googleapis.com/auth/urlshortener");
				$client->setScopes('email');
				$client->setApprovalPrompt('force');
				 
				$service = new Google_Service_Urlshortener($client);
				$loginUrl = $client->createAuthUrl();
				
				if (!empty($loginUrl)) {
					$array_out = array("link" => $loginUrl, 'errorMessage' => '');
				} else {
					$array_out = array("link" => '', 'errorMessage' => $errMsg);
				}
			} else {
					$array_out = array("link" => '', 'errorMessage' => $errMsg);
			}
			
			echo json_encode($array_out);
			die();
		
		}
		
		static function zoner_signInForm_jscript() {
			
			$created_user = false;
			$created_user = get_query_var('created_user');
			
			wp_enqueue_script( 'zoner-signInForm',  ZONER_SHORTCODE_JS . 'signIn.js', array( 'jquery' ), '20140808', true );
			wp_localize_script( 'zoner-signInForm', 'zonerSignIn', 	array( 	
																			'valid_email_mess' 		=> __('Please enter valid email address', 'zoner'),	
																			'valid_pass_mess' 		=> __('Please enter valid password', 'zoner'),
																			'frg_pass_button_text'	=> __('Send Me Password', 'zoner'),
																			'zoner_created_user' 	=> esc_js($created_user),
																			'zoner_message_created_user' => __('Thank you for registering. Please check your mail.', 'zoner'),
																		 )
															);  
		}
		
		static function zoner_is_user_email_exists() {
			if (isset($_POST) && ($_POST['action'] == 'zoner_signin_user_email_exists')) {
				$email = $_POST['si-email'];
				if (email_exists( $email )) { echo 'true'; } else { echo 'false'; }
			}	
			die();
		}
		
		static function zoner_is_pass_exists() {
			$user = '';
			if (isset($_POST) && ($_POST['action'] == 'zoner_signin_user_pass_exists')) {
				$user = get_user_by( 'email',$_POST['si_email']);
				$pass  = $_POST['si-password'];
				if ($user && wp_check_password( $pass, $user->data->user_pass, $user->ID)) { 
					echo 'true'; 
				} else { 
					echo 'false'; 
				}
			}	
			die();
		}
		
		static function zoner_custom_signin_form($atts = array()) {
			$out_form = '';
			$pb_show_facebook = $pb_show_google = false;
			
			$atts  = vc_map_get_attributes( 'zoner_signin', $atts );
			extract( $atts );
			
			$arr_classes[] = 'form-signin';
			$arr_classes[] = $el_class;
		
			$out_form = '<div class="wrapper-sigin-form">';
				if (!is_user_logged_in()) {
					$out_form .= '<form role="form" id="form-signin" class="'.implode(' ', $arr_classes).'" name="form-signin" method="post" action="">';
						$out_form .= wp_nonce_field( 'zoner_signin', 'signin', true, true ); 
						$out_form .= '<input type="hidden" id="type-form" name="type-form" value="1" />';
						
						$out_form .= '<div class="form-group is-reset-password">';
							$out_form .= '<h3>'.__('Please enter your email and we will send you your password', 'zoner').'</h3>'; 
						$out_form .= '</div><!-- /.form-group -->';
						
						$out_form .= '<div class="form-group">';
							$out_form .= '<label for="si-email">'.__('Email', 'zoner').':</label>';
							$out_form .= '<input type="email" class="form-control" id="si-email" name="si-email" required>';
						$out_form .= '</div><!-- /.form-group -->';
						$out_form .= '<div class="form-group">';
							$out_form .= '<label for="si-password">'.__('Password', 'zoner') .':</label>';
								$out_form .= '<input type="password" class="form-control" id="si-password" name="si-password" required>';
						$out_form .= '</div><!-- /.form-group -->';
						$out_form .= '<div class="form-group clearfix">';
							if ($pb_show_facebook)
							$out_form .= '<a data-socialact="facebook" class="social btn btn-facebook right-space"><i class="fa fa-facebook"></i> | '.__('Facebook', 'zoner').'</a>';
							if ($pb_show_google)
							$out_form .= '<a data-socialact="google"   class="social btn btn-google-plus right-space"><i class="fa fa-google-plus"></i> | '.__('Google', 'zoner').'</a>';
							
							$out_form .= '<button type="submit" class="btn pull-right btn-default" id="account-submit">'.__('Sign to My Account', 'zoner').'</button>';
						$out_form .= '</div><!-- /.form-group -->';
					$out_form .= '</form>';
					$out_form .= '<hr>';
					$out_form .= '<div class="center"><a href="#" id="frg-password" class="frg-password">'.__("I don't remember my password", 'zoner').'</a></div>';
					
					
				} else {
					
					$out_form .= '<div class="alert alert-info">';
						$out_form .= '<a href="#" class="close" data-dismiss="alert">&times;</a>';
						$out_form .= __('Your account is active!', 'zoner');
						$out_form .= '<strong><a href="'. add_query_arg(array('profile-page' => 'my_profile'), get_author_posts_url(get_current_user_id())).'"> '.__('Click to view profile') .'</a></strong>';
					$out_form .= '</div>';
					$out_form .= '<hr>';
				}
				
			$out_form .= '</div>';
			
			
			return $out_form;
		}
		
		
	}
	/*Initialize signin form*/
	zoner_signin_form_shortcode::init();
	
	if ( ! function_exists( 'zoner_user_signin_process' ) ) {
		function zoner_user_signin_process() {
			global $zoner, $zoner_config;	
			if ( isset($_POST['signin']) && wp_verify_nonce($_POST['signin'], 'zoner_signin')) {
				$user = get_user_by( 'email',$_POST['si-email']);
				$user_id = $user->ID;
				
				if ( $_POST['type-form'] == 1) {
					if(!is_wp_error($user_id)) {
						wp_set_auth_cookie( $user_id, false, is_ssl() );
						wp_redirect( add_query_arg(array('profile-page' => 'my_profile'), get_author_posts_url($user_id)));
						exit;
					}
				} 
				
				if ( $_POST['type-form'] == 2) {
					$new_pass = wp_generate_password(8, true);
					wp_set_password($new_pass, $user_id);
					$zoner->emails->zoner_mail_reset_password($user->display_name, $user->user_email, $new_pass);
					wp_safe_redirect( zoner_curPageURL());
				}
			}	
		}
	}	
	add_action('wp', 'zoner_user_signin_process', 299); 
	
	
	if ( ! function_exists( 'zoner_user_query_count_post_type' ) ) {				
		function zoner_user_query_count_post_type($args) {
			$args->query_from = str_replace("post_type = 'post' AND", "post_type IN ('property') AND ", $args->query_from);
		}
	}	
	
	if ( ! function_exists( 'zoner_count_user_posts_by_type' ) ) {				
		function zoner_count_user_posts_by_type( $userid, $post_type = 'post' ) {
			global $wpdb;
			$where = get_posts_by_author_sql( $post_type, true, $userid, true );
			$count = $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->posts $where" );

			return apply_filters( 'get_usernumposts', $count, $userid );
		}
	}

	/*Agents Listing*/
	function zoner_agents_listing_func($atts = array(), $content = null) {
		global $prefix, $zoner;
		$out_agents  = $pb_show_agents_with_property = $number_agents = $pb_agency = $pb_orderby = $pb_order = $pb_showtop = $pb_position = $number_agents_per_page = '';
		
		$atts  = vc_map_get_attributes( 'zoner_agents_listing', $atts );
		extract( $atts );

		$number_agents = $display_name = $skype = $tel = $mob = null;
		$elem_class = $user_filters = array();
		
		if (!empty($pb_agent_id)) {
			$elem_class[] = 'member';
			if (!empty($el_class)) $elem_class[] = $el_class;
			
			$agent = array();
			$agent = get_user_by( 'id', $pb_agent_id );
            $email = null;

			$all_meta_for_user = get_user_meta( $pb_agent_id );
			
			if (isset($all_meta_for_user[$prefix.'tel']))
			$tel   = current($all_meta_for_user[$prefix.'tel']);
			
			if (isset($all_meta_for_user[$prefix.'mob']))
			$mob = current($all_meta_for_user[$prefix.'mob']);
			
			if (isset($all_meta_for_user[$prefix.'skype']))
			$skype = current($all_meta_for_user[$prefix.'skype']);
			
			$all_meta_for_user = get_user_meta( $pb_agent_id );
			$avatar = '';
		
			$img_url = get_template_directory_uri() . '/includes/theme/profile/res/avatar.jpg';
		
			if (!empty($all_meta_for_user[$prefix.'avatar']))
				$avatar = $all_meta_for_user[$prefix.'avatar'];
			if (!empty($all_meta_for_user[$prefix.'avatar_id']))
				$avatar_id = $all_meta_for_user[$prefix.'avatar_id'];
		
		
			if (is_array($avatar)) { 
				$avatar = array_filter($avatar);
				if (!empty($avatar)) $img_url = current($avatar);
			}	
			
            $display_name = zoner_get_user_name($agent);
            if (!empty($agent)) {
			    $email = $agent->user_email;
            }
			
			$out_agents = '<div id="member-'.$pb_agent_id.'" class="'.implode(' ', $elem_class ).'">';
				$out_agents .= '<a href="'.get_author_posts_url( $pb_agent_id ).'" class="image"><img src="'.esc_url($img_url).'" alt="" /></a>';
					if ($pb_showtop)
					$out_agents .= '<div class="tag">'.__('Top Agent', 'zoner').'</div>';
					
					$out_agents .= '<div class="wrapper">';
						$out_agents .= '<a href="'.get_author_posts_url( $pb_agent_id ).'"><h3>'.$display_name.'</h3></a>';
						$out_agents .= '<figure class="subtitle">'.$pb_position.'</figure>';
						
						$out_agents .= '<dl>';
							if (!empty($tel)) {
								$out_agents .= '<dt>'. __('Phone', 'zoner') .':</dt>';
								$out_agents .= '<dd>'. $tel .'</dd>';
							}
									
							if (!empty($mob)) {
								$out_agents .= '<dt>'. __('Mobile', 'zoner') .':</dt>';
								$out_agents .= '<dd>'. $mob .'</dd>';
							}
									
							if (!empty($email) && (is_user_logged_in())) { 
								$out_agents .= '<dt>'.__('Email', 'zoner').':</dt>';
								$out_agents .= '<dd><a href="mailto:'. $email .'">'. $email .'</a></dd>';
							}
									
							if (!empty($skype)) { 
								$out_agents .= '<dt>'. __('Skype', 'zoner') .':</dt>';
								$out_agents .= '<dd><a href="skype:'.$skype.'?call">'.$skype.'</a></dd>';
							} 
						$out_agents .= '</dl>';	
					$out_agents .= '</div>';
				$out_agents .= '</div><!-- /.member -->';
		
		} else {
			$elem_class[] = 'agents-listing';
			if (!empty($el_class)) $elem_class[] = $el_class;
			
			
			if ($number_agents != -1) $number = $number_agents;
			if ($pb_agency != -1) {
				$all_agents_by_agency = $zoner->invites->zoner_get_all_agents_from_agency($pb_agency);
				
				$post_agency = get_post($pb_agency); 
				if (!empty($post_agency)) $user_filters[] = $post_agency->post_author;
				
				if (!empty($all_agents_by_agency)) {
					foreach ($all_agents_by_agency as $agency) {
						$user_filters[] = $agency->user_id;
					}
				}
			}	

			$number_per_page = $number_agents_per_page;
			$total_agents = $total_query = $total_pages = 0;
			
			if (($number_per_page > 0) && (!empty($number_per_page))) {
				
				$paged       = (get_query_var('paged')) ? get_query_var('paged') : 1;  
				$offset      = ($paged - 1) * $number_per_page;  
				$args = array();
				$args = array(
					'role'      => 'Agent',
					'orderby'   => $pb_orderby,
					'order'     => $pb_order,
					'number'	=> $number_agents,
					'include'	=> $user_filters
				);
				
				add_action('pre_user_query','zoner_user_query_count_post_type');
				
				$full_list_agents = get_users($args);  
				
				if (!empty($full_list_agents) && ($pb_show_agents_with_property)) {
					foreach($full_list_agents as $key => $agent) {
						$agent_id = $agent->ID;
						$count_properties = zoner_count_user_posts_by_type($agent_id, 'property');
						if ($count_properties > 0) $total_agents++;
					}
				} else {
					$total_agents = count($full_list_agents);
				}
				
				$args = array();
				$args = array(
					'role'      => 'Agent',
					'orderby'   => $pb_orderby,
					'order'     => $pb_order,
					'number'	=> $number_per_page,
					'include'	=> $user_filters,
					'offset'	=> $offset
				);
			
				$all_agents   = get_users( $args );
				$total_query  = count($all_agents);  
				$total_pages  = intval($total_agents / $number_per_page);  
				
			} else {
				
				$args = array(
					'role'      => 'Agent',
					'orderby'   => $pb_orderby,
					'order'     => $pb_order,
					'number'	=> $number_agents,
					'include'	=> $user_filters
				);
			
				add_action('pre_user_query','zoner_user_query_count_post_type');
				$all_agents = get_users( $args );
			
			}			
			
				remove_action('pre_user_query','zoner_user_query_count_post_type');
				
			$count = 1;
			$is_close_row = true;
			
			if (!empty($all_agents)) {
			
				$out_agents = '<section id="agents-listing" class="'.implode(' ', $elem_class).'">';	
				foreach ($all_agents as $agent) {
					
					$userID = $agent->ID;
					$display_name = zoner_get_user_name($agent);
					$email = $agent->user_email;
					$all_meta_for_user = get_user_meta( $userID );

					if ($count%2 == 1) {
						$out_agents .= '<div class="row">';
						$is_close_row = false;
					}	
					
					
					$tel = $mob = $skype = '';
				
					if (isset($all_meta_for_user[$prefix.'tel']))
					$tel = current($all_meta_for_user[$prefix.'tel']);
					if (isset($all_meta_for_user[$prefix.'mob']))
					$mob = current($all_meta_for_user[$prefix.'mob']);
					if (isset($all_meta_for_user[$prefix.'skype']))
					$skype = current($all_meta_for_user[$prefix.'skype']);
				
					$count_property_args = array( 
						   'post_type' 		=> 'property', 
						   'post_status'	=> 'publish',
						   'posts_per_page' => -1, 
						   'orderby'	    => 'DATE', 
						   'order'			=> 'ASC',
						   'author'			=> $userID
						   
					);
							   
					$prop_from_agent = new WP_Query( $count_property_args );
					$count_property = $prop_from_agent->found_posts;
					
					if ($pb_show_agents_with_property && ($count_property == 0)) continue;
					
					
					$out_agents .= '<div class="col-md-12 col-lg-6">';
						$out_agents .= '<div id="agent-'.$userID.'" class="agent">';
							$out_agents .= '<a href="'.get_author_posts_url( $userID ).'" class="agent-image">'.zoner_get_profile_avartar($userID).'</a>';
								$out_agents .= '<div class="wrapper">';
									$out_agents .= '<header><a href="'.get_author_posts_url( $userID ).'"><h2>'.$display_name.'</h2></a></header>';
									$out_agents .= '<aside>'.sprintf( '%s %s', $count_property, __('Properties', 'zoner') ) .'</aside>';
									$out_agents .= '<dl>';
										
										if (!empty($tel)) {
											$out_agents .= '<dt>'. __('Phone', 'zoner') .':</dt>';
											$out_agents .= '<dd>'. $tel .'</dd>';
										}
									
										if (!empty($mob)) {
											$out_agents .= '<dt>'. __('Mobile', 'zoner') .':</dt>';
											$out_agents .= '<dd>'.$mob.'</dd>';
										}
									
										if (!empty($email) && (is_user_logged_in())) { 
											$out_agents .= '<dt>'.__('Email', 'zoner').':</dt>';
											$out_agents .= '<dd><a href="mailto:'.$email.'">'. $email .'</a></dd>';
										}
									
										if (!empty($skype)) { 
											$out_agents .= '<dt>'. __('Skype', 'zoner') .':</dt>';
											$out_agents .= '<dd><a href="skype:'.$skype.'?call">'.$skype.'</a></dd>';
										} 
										
								$out_agents .= '</dl>';
							$out_agents .= '</div>';
						$out_agents .= '</div><!-- /.agent -->';
					$out_agents .= '</div><!-- /.col-md-12 -->';
					
					if ($count%2 == 0) {
						$out_agents .= '</div>';
						$is_close_row = true;
					}
					
					$count++;
				}
				
				if (!$is_close_row) $out_agents .= '</div>';
				
				$out_agents .= '</section>';
				
				if (($total_agents > $total_query) && ($number_per_page != -1)) {  
					$out_agents .=  '<div class="center" role="navigation">';
						$out_agents .=  '<ul class="pagination loop-pagination">';
						$current_page = max(1, get_query_var('paged'));  
						for ($i = 1; $i <= $total_pages; $i++) {
							$class = '';
							if ($current_page == $i) $class = ' class="active"';
							$out_agents .= sprintf( '<li%s><a href="%s">%s</a></li>' . "\n", $class, esc_url( get_pagenum_link( $i ) ), $i );
						}
						$out_agents .= '</ul>';
					$out_agents .=  '</div>';
				}
			} else {
				$out_agents = '<div class="alert alert-danger"><strong>'.__('Agents list is empty.', 'zoner').'</strong> '. __('Verify that the data set!', 'zoner') .'</div>';
			}
  
		}
		return $out_agents;
	}	
	add_shortcode('zoner_agents_listing', 'zoner_agents_listing_func');
	
	
	
	/*Agencies Listing*/
	function zoner_agencies_listing_func($atts = array(), $content = null) {
		global $prefix, $zoner, $zoner_config;
		$out_agencies = $number_agencies_per_page = $pb_orderby = $pb_order = $pb_show_agencies = '';
		
		$atts  = vc_map_get_attributes( 'zoner_agencies_listing', $atts );
		extract( $atts );
		
		
		$args = $elem_class = array();
		
		$elem_class[] = 'agencies-listing';
		if (!empty($el_class)) $elem_class[] = $el_class;
		
		$total_agencies  = $total_query = $total_pages = 0;
		$args = array();
		
		if (($number_agencies_per_page > 0) && (!empty($number_agencies_per_page))) {
			$paged       = (get_query_var('paged')) ? get_query_var('paged') : 1;  
			$args = array(
				'post_type' 	=> 'agency',
				'post_status' 	=> 'publish',
				'order'			=> $pb_order,
				'orderby'		=> $pb_orderby
			);
		
			$q_agencies_all = new WP_Query( $args );
			
			$args = array();
			$args = array(
				'post_type' 	=> 'agency',
				'post_status' 	=> 'publish',
				'posts_per_page' => $number_agencies_per_page,
				'order'			=> $pb_order,
				'orderby'		=> $pb_orderby,
				'paged'         => $paged,
			);
			
			$q_agencies = new WP_Query( $args );
			
			$total_agencies = $q_agencies_all->found_posts;  
			$total_query  	= count($q_agencies);  
			if ($total_agencies > 0 ) {
				$total_pages =  ceil($total_agencies / $number_agencies_per_page);  
			}	
		} else {
			$args = array();
			$args = array(
				'post_type' 	=> 'agency',
				'post_status' 	=> 'publish',
				'posts_per_page' => -1,
				'order'			=> $pb_order,
				'orderby'		=> $pb_orderby
			);
		
			$q_agencies = new WP_Query( $args );
		}

		
		if ( $q_agencies->have_posts() ) {
			 $out_agencies .= '<section id="agencies-listing" class="'.implode(' ', $elem_class).'">';	
			
			while ( $q_agencies->have_posts() ) {
					$q_agencies->the_post();
					$id_ = get_the_ID();
					
					$user_post_count = 0;
					
					$all_users = $zoner->invites->zoner_get_all_agents_from_agency($id_);
					$admin_agency_id = $q_agencies->post->post_author;
					$user_post_count = count_user_posts( $admin_agency_id );
					
					
					if (!empty($all_users)) {
						foreach($all_users as $user) {
							$user_post_count = $user_post_count + zoner_count_user_posts_by_type( $user->user_id, 'property' );
						}
					}
					if (($user_post_count == 0) && ($pb_show_agencies)) continue;

					$address = nl2br(get_post_meta($id_, $prefix . 'agency_address', true));
					$email 	 = sanitize_email(get_post_meta($id_, $prefix . 'agency_email', true));
					$tel 	 = esc_attr(get_post_meta($id_, $prefix . 'agency_tel', true)); 
					$mob 	 = esc_attr(get_post_meta($id_, $prefix . 'agency_mob', true)); 
					$skype   = get_post_meta($id_, $prefix . 'agency_skype', true); 
					$sfi 	 = esc_url(get_post_meta($id_, $prefix . 'agency_line_img', true));
					$sfi_id  = get_post_meta($id_, $prefix . 'agency_line_img_id', true);
				
				
					$out_image = '<img data-src="holder.js/200x200?auto=yes&text='.__('No Image', 'zoner') .'" alt="" />';
					if ($sfi) {
						$agency_logo = wp_get_attachment_image_src($sfi_id, array(200,200));
						$out_image = '<img class="" src="'.$agency_logo[0].'" alt="" />';
					} 				
					
					
					$out_agencies .= '<div id="agency-'.$id_.'" class="agency">';
						$out_agencies .= '<a href="'.get_permalink().'" class="agency-image">'.$out_image.'</a>';
						$out_agencies .= '<div class="wrapper">';
							$out_agencies .= '<header><a href="'.get_permalink().'"><h2>'.get_the_title().'</h2></a></header>';
							$out_agencies .= '<dl>';
								
								if ($tel) {
									$out_agencies .= '<dt>'.__('Phone', 'zoner') .':</dt>';
									$out_agencies .= '<dd>'.$tel.'</dd>';
								}	
								
								if ($mob) {
									$out_agencies .= '<dt>'.__('Mobile', 'zoner') .':</dt>';
									$out_agencies .= '<dd>'.$mob.'</dd>';
								}	
								
								if (!empty($email) && (is_user_logged_in())) {
									$out_agencies .= '<dt>'.__('Email', 'zoner') .':</dt>';
									$out_agencies .= '<dd><a href="mailto:'.$email.'">'.$email.'</a></dd>';
								}	
								
								if ($skype) {
									$out_agencies .= '<dt>'.__('Skype', 'zoner').':</dt>';
									$out_agencies .= '<dd><a href="skype:'.$skype.'?call">'.$skype.'</a></dd>';
								}	
							
							$out_agencies .= '</dl>';
								
							$out_agencies .= '<address>';
								$out_agencies .= '<h3>'. __('Address', 'zoner') .'</h3>';
								$out_agencies .= '<strong>' . get_the_title() . '</strong><br />';
								$out_agencies .= $address;
							$out_agencies .= '</address>';
						$out_agencies .= '</div>';
					$out_agencies .= '</div><!-- /.agency -->';
				
				}
					
				$out_agencies .= '</section>';	
				
				if (($total_pages > 1) && ($number_agencies_per_page != -1)) {  
						
						$out_agencies .= '<div class="center" role="navigation">';
							$out_agencies .= '<ul class="pagination loop-pagination">';
		
							$current_page = max(1, get_query_var('paged'));  
				
							for ($i = 1; $i <= $total_pages; $i++) {
								$class = '';
								if ($current_page == $i) $class = ' class="active"';
								$out_agencies .= sprintf( '<li%s><a href="%s">%s</a></li>' . "\n", $class, esc_url( get_pagenum_link( $i ) ), $i );
							}

							$out_agencies .= '</ul>';
						$out_agencies .= '</div>';
				}
				
			} else {
				$out_agencies = '<div class="alert alert-danger"><strong>'.__('Agencies list is empty.', 'zoner').'</strong> '. __('Verify that the data set!', 'zoner') .'</div>';
			}

			wp_reset_postdata();
			
			return $out_agencies;
		
	}	
	add_shortcode('zoner_agencies_listing', 'zoner_agencies_listing_func');
	
	
	
	/*Get custom query properties*/
	class zoner_properties_listing_shortcode {

	static function init() {
		add_action   ('zoner_before_enqueue_script', array(__CLASS__, 'zoner_property_listing_script'), 5);
		add_shortcode('zoner_properties_listing', array(__CLASS__, 'zoner_properties_listing_func'));
	}
	static function zoner_property_listing_script() {
		global  $inc_theme_url;
		if( !wp_script_is('zoner-reveral', 'enqueued')) {
			wp_register_script( 'zoner-reveral',		$inc_theme_url . 'assets/js/scrollReveal.min.js',	 array( 'jquery' ), '20142807', true );
			wp_enqueue_script( 'zoner-reveral');
		}
		if( !wp_script_is('zoner-masonry', 'enqueued')) {
			wp_enqueue_script( 'zoner-masonry');
			wp_register_script( 'zoner-masonry',		$inc_theme_url . 'assets/js/masonry.pkgd.min.js',	 array( 'jquery' ), '20142807', true );
		}
		if( !wp_script_is('zoner-imagesloaded', 'enqueued')) {
			wp_register_script( 'zoner-imagesloaded',	$inc_theme_url . 'assets/js/imagesloaded.pkgd.min.js',	 array( 'jquery' ), '20142807', true );
			wp_enqueue_script( 'zoner-imagesloaded');
		}
	}
    static $shortcode_id = 0;
	static function zoner_properties_listing_func($atts = array(), $content = null) {
		global $prefix, $zoner, $zoner_config, $wp_query;

		$out_props = $pb_grid_type = $pb_type = $pb_title = $pb_title_link = $pb_url_link = $pb_status = $pb_property_ids = $number_properties = $paged = $pb_columns = $pb_carousel = $pb_carousel_full_width = $el_class = '';
		$pb_tax_status = $pb_tax_type = $pb_tax_features = $pb_tax_categories = $pb_tax_cities = $pb_show_all_prop = $pb_show_sorting_section = $pb_show_paging_section = $is_autoplay = '';
		if (!empty($atts))
			extract( $atts );
		$atts  = vc_map_get_attributes( 'zoner_properties_listing', $atts );
		extract( $atts );

		$count = 1;
		$args = $elem_class = $tax_query = array();
		$is_closed = false;
		$out_properties = $error = '';


		if (empty($pb_grid_type)) $pb_grid_type = 1;


		if (!empty($pb_tax_status) 	 ||
			!empty($pb_tax_type) 	 ||
			!empty($pb_tax_features) ||
			!empty($pb_tax_categories)||
			!empty($pb_tax_cities))
			$tax_query['relation'] = 'AND';


		if (!empty($pb_tax_status)) {
			$tax_query[] = array(
				array(
					'taxonomy' 	=> 'property_status',
					'field' 	=> 'slug',
					'operator'  => 'IN',
					'terms' 	=> explode(',', $pb_tax_status)
				)
			);
		}

		if (!empty($pb_tax_type)) {
			$tax_query[] = array(
				array(
					'taxonomy' 	=> 'property_type',
					'field' 	=> 'slug',
					'operator' 	=> 'IN',
					'terms' 	=> explode(',', $pb_tax_type)
				)
			);
		}

		if (!empty($pb_tax_features)) {
			$tax_query[] = array(
				array(
					'taxonomy'  => 'property_features',
					'field' 	=> 'slug',
					'operator'  => 'IN',
					'terms' => explode(',', $pb_tax_features)
				)
			);
		}

		if (!empty($pb_tax_categories)) {
			$tax_query[] = array(
				array(
					'taxonomy' 		=> 'property_cat',
					'field' 		=> 'slug',
					'operator' 		=> 'IN',
					'terms' 		=> explode(',', $pb_tax_categories)
				)
			);
		}
		if (!empty($pb_tax_cities)) {
			$tax_query[] = array(
				array(
					'taxonomy' 		=> 'property_city',
					'field' 		=> 'slug',
					'operator' 		=> 'IN',
					'terms' 		=> explode(',', $pb_tax_cities)
				)
			);
		}

		$args['post_type'] 		= 'property';
		$args['post_status'] 	= 'publish';
		$args['posts_per_page'] = $number_properties;
		if (!empty($paged)){
			$args['paged'] = $paged;
		}

		if (!empty($_GET['paged-'.self::$shortcode_id] ) ) {
			$current_page = $_GET['paged-'.self::$shortcode_id];
		} else {
			$current_page = 1;
		}
		$args['offset'] = ($current_page-1) * $args['posts_per_page'];
		
		//Set Default value from vc params
		$ordering  = $zoner->zoner_get_prop_ordering_args($pb_order_by, $pb_order);
		
		//If set show sorting section
		if ($pb_show_sorting_section) {
			if (!empty($_GET['sorting-'.self::$shortcode_id])) {
				$_GET['sorting'] = $_GET['sorting-'.self::$shortcode_id];
				$ordering   = $zoner->zoner_get_prop_ordering_args();
				//clear for next shortcode
				unset($_GET['sorting']);
			}
		}
		
		$args['orderby'] = $ordering['orderby'];
		$args['order']	 = $ordering['order'];
		if ( isset( $ordering['meta_key'] ) ) 
			$args['meta_key'] = $ordering['meta_key'];

		if ($pb_type == 1) {
			if (!empty($tax_query)) $args['tax_query'] 	= $tax_query;
			$error = '<div class="alert alert-danger"><strong>'.__('Property list is empty.', 'zoner').'</strong> '. __('Verify that the data set!', 'zoner') .'</div>';
		} elseif ($pb_type == 2) {
			$args['meta_query']	= array(
				array(
					'key' 	=> $prefix . 'is_featured',
					'value' => 'on'
				)
			);
			if (!empty($tax_query)) $args['tax_query'] 	= $tax_query;

			$error = '<div class="alert alert-danger"><strong>'.__('Featured property list is empty.', 'zoner').'</strong> '. __('Maybe not selected "featured" any property!', 'zoner') .'</div>';
		}

		if ($pb_property_ids) {
			$args['posts_per_page'] = -1;
			$args['post__in']		= explode(',', $pb_property_ids);
			$error = '<div class="alert alert-danger"><strong>'.__('Property list is empty.', 'zoner').'</strong> '. __('Check that you entered a unique identifier!', 'zoner') .'</div>';
		}

		$archivePropertyPage = $zoner->zoner_get_page_id('page-property-archive');
		if ($archivePropertyPage != 0) {
			$all_property_link = get_permalink($archivePropertyPage);
		} else {
			$all_property_link = site_url();
		}

		$column_class = array('col-md-3', 'col-sm-6');
		if ($pb_columns == 2) {
			$column_class = array('col-md-6', 'col-sm-6');
		} elseif ($pb_columns == 3) {
			$column_class = array('col-md-4', 'col-sm-6');
		} elseif ($pb_columns == 4) {
			$column_class = array('col-md-3', 'col-sm-6');
		}
		$is_closed = true;
		$properties = new WP_Query( $args );
		if ($properties->have_posts()) {
			$found_posts = get_object_vars ($properties);
			$found_posts = $found_posts['found_posts'];
			if ($pb_carousel && ($pb_grid_type == 1)) {
				$carousel_classes = array();
				$carousel_classes[] = 'featured-properties';
				$carousel_classes[] = 'block';
				if ($pb_carousel_full_width) $carousel_classes[] = 'carousel-full-width';

				$out_properties .= '<section id="featured-properties-'.self::$shortcode_id.'" class="'.	implode(' ',  $carousel_classes).'">';
				if (!empty($pb_title)) {
					if ($pb_carousel_full_width) $out_properties .= '<div class="container">';
					$out_properties .= '<header class="section-title">';
					$out_properties .= '<h2>'.esc_attr($pb_title).'</h2>';
					$out_properties .= '</header>';
					if ($pb_carousel_full_width) $out_properties .= '</div>';
				}

				$out_properties .= '<div id="owl-carousel-'.self::$shortcode_id.'" is_autoplay="' . $is_autoplay . '" class="owl-carousel featured-properties-carousel">';
			} else {
				$out_properties .= '<section class="property-list">';
				$out_properties .= '<header class="section-title">';
				$trimed_title = trim($pb_title);
				$trimed_link = trim($pb_title_link);
				if (!empty($trimed_title))
					$out_properties .= '<h2>'.esc_attr($pb_title).'</h2>';

				if ( empty($trimed_link)) $pb_title_link = __('All Properties', 'zoner');
				if (!empty($pb_url_link))
					$all_property_link = esc_url($pb_url_link);
				// -------------------------------sorting section-------------------------------
				if ($pb_show_sorting_section == 1) {
					$sorting_section = '<section id="search-filter" class="search-filter">';
					$sorting_section .= '<figure>';
					$sorting_section .= '<h3>';
					if (!empty($_GET['filter_property'])) {
						$sorting_section .= '<i class="fa fa-search"></i>';
						$sorting_section .= __('Search Results', 'zoner');
					} else {
						$sorting_section .= __('Results', 'zoner');
					}
					$sorting_section .= ':</h3>';
					$sorting_section .= '<span class="search-count">' . $found_posts . '</span>';
					$sorting_section .= '<div class="sorting">';
					$sorting_section .= '<form id="form-sort" class="form-group form-sort" name="form-sort" action="" method="GET">';
					$sorting_section .= '<select name="sorting-'.self::$shortcode_id.'" class="zoner-property-sort">';
					$catalog_orderby = apply_filters( 'zoner_property_orderby',
						array(
							'menu_order' 	 => __( 'Sort By', 'zoner' ),
							'rating'     	 => __( 'Sort by Rating: low to high', 'zoner' ),
							'rating-desc'  	 => __( 'Sort by Rating: high to low', 'zoner' ),
							'date'       	 => __( 'Sort by newness', 'zoner' ),
							'price'      	 => __( 'Sort by price: low to high', 'zoner' ),
							'price-desc' 	 => __( 'Sort by price: high to low', 'zoner' ),
							'rand' 		 	 => __( 'Sort by random', 'zoner' ),

						)
					);

					$pb_order_by = '';
					if (!empty( $_GET['sorting-'.self::$shortcode_id]))
						$pb_order_by = $_GET['sorting-'.self::$shortcode_id];
					foreach ( $catalog_orderby as $id => $name )
						$sorting_section .= '<option data-value="'.esc_attr( $id ).'" value="'. esc_attr( $id ) . '" ' . selected( $pb_order_by, $id, false ) . '>' . esc_attr( $name ) . '</option>';
					$sorting_section .= '</select>';
					$sorting_section .= '</form><!-- /.form-group -->';
					$sorting_section .= '</div>';
					$sorting_section .= '</figure>';
					$sorting_section .= '</section>';
					$out_properties .= $sorting_section;
				}
				// -----------------------------end sorting section-----------------------------
				if ($pb_show_all_prop)
					$out_properties .= '<a target="_blank" href="'.$all_property_link.'" class="link-arrow">'.$pb_title_link.'</a>';
				$out_properties .= '</header>';


				$section_class_inner   = array();
				$section_class_inner[] = 'properties';
				if ($pb_grid_type == 2) {
					$section_class_inner[] = 'masonry';
					$section_class_inner[] = 'masonry-loaded';
					$pb_columns = 3;
				}
				if ($pb_grid_type == 3) $section_class_inner[] = 'display-lines';

				$out_properties .= '<section id="properties" class="'.implode(' ', $section_class_inner).'">';
				$out_properties .= '<div class="grid">';


			}
			while ($properties->have_posts() ) : $properties->the_post();
				if ($pb_carousel && ($pb_grid_type == 1)) {
					$out_properties .= zoner_get_property_grid_items_original(false, array('property', 'big'));
				} else {

					if ($pb_grid_type != 3) {
						if ($count%$pb_columns == 1) {
							$out_properties .= '<div class="row">';
							$is_closed = false;
						}
					}

					if ($pb_grid_type == 1) {
						$out_properties .= zoner_get_property_grid_items_original(false, $column_class);
					} elseif($pb_grid_type == 2) {
						$out_properties .= zoner_get_property_grid_items_masonry(false);
					} elseif($pb_grid_type == 3) {
						$out_properties .= zoner_get_property_grid_items_lines(false);
					}

					if ($pb_grid_type != 3) {
						if ($count%$pb_columns == 0) {
							$out_properties .= '</div>';
							$is_closed = true;
						}
					}
				}

				$count++;
			endwhile;

			if (!$is_closed)  $out_properties .= '</div>';
			if ( $pb_carousel && ($pb_grid_type == 1)) {
				$out_properties .= '</div>';
			} else {
				$out_properties .= '</div><!-- end grid inner -->';
				$out_properties .= '</section><!-- end properties section -->';
			}
			// ---------------------------------pagination---------------------------------
			if ($pb_show_paging_section == 1) {
				if ($found_posts > $args['posts_per_page'] && $args['posts_per_page']!=-1) {
					$out_properties .=  '<div class="center" role="navigation">';
					$out_properties .=  '<ul class="pagination loop-pagination">';
					$total_pages = get_object_vars($properties);
					$total_pages = $total_pages['max_num_pages'];
					for ($i = 1; $i <= $total_pages; $i++) {
						$class = '';
						if ($current_page == $i) $class = ' class="active"';
						$link = strtok($_SERVER["REQUEST_URI"],'?');
						$params = $_GET;
						$params['paged-'.self::$shortcode_id] = $i;
						$paramString = http_build_query($params);
						$link = $link.'?'.$paramString;
						$out_properties .= sprintf( '<li%s><a href="%s">%s</a></li>' . "\n", $class, $link, $i );
					}
					$out_properties .= '</ul>';
					$out_properties .=  '</div>';
				}
			}
			// -----------------------------end pagination---------------------------------
			$out_properties .= '</section>';
		} else {
			$out_properties = $error;
		}
		self::$shortcode_id++;
		wp_reset_postdata();
		return $out_properties;
	}
}

	/*Initialize properties listing shortcode*/
	zoner_properties_listing_shortcode::init();
	
	
	/*Get custom partners*/
	function zoner_partners_list_func($atts = array(), $content = null) {
		$out_partners = $el_class = $pb_title = '';
		$atts  = vc_map_get_attributes( 'zoner_partners', $atts );
		extract( $atts );
		
		$rand_id = rand(0, 250);
		
		$elem_class = array();
		$elem_class[] = 'block';
		$elem_class[] = 'partners';
		if (!empty($el_class))
		$elem_class[] = $el_class;

		$out_partners .= '<section id="partners-'.$rand_id.'" class="'.implode(' ', $elem_class).'">';
			$out_partners .= '<header class="section-title"><h2>'.esc_attr($pb_title).'</h2></header>';
			$out_partners .= '<div class="logos">';
				$out_partners .= ($content == '' || $content == ' ') ? __("Empty partners. Edit page to add content here.", "zoner") : wpb_js_remove_wpautop($content);			
			$out_partners .= '</div>';
		$out_partners .= '</section>';
		
		return $out_partners;
	
	}
	add_shortcode('zoner_partners', 'zoner_partners_list_func');
	
	function zoner_partners_item_func($atts = array(), $content = null) {
		$out_partners_item = $out_logo = $pb_logo = $el_class = '';
		
		$atts  = vc_map_get_attributes( 'zoner_partners_item', $atts );
		extract( $atts );
		
		$elem_class = array();
		$elem_class[] = 'logo';
		if (!empty($el_class))
		$elem_class[] = $el_class;
		
		$link = ($link=='||') ? '' : $link;
		$link = vc_build_link($link);
		
		$a_href 	= $link['url'];
		$a_title 	= $link['title'];
		$a_target 	= $link['target'];
		
		
		if (!empty($pb_logo)) {
			$logo = wp_get_attachment_image_src( $pb_logo, 'full');
			$logo_url = $logo[0];
			$out_logo = '<img src="'.$logo_url.'" alt="" />';
		} else {
			$out_logo = '<img data-src="holder.js/122x32?auto=yes&text='.__('logo', 'zoner') .'" alt="" />';
		}
		
		
		
		$out_partners_item .= '<div class="'.implode(' ', $elem_class).'"><a target="'.$a_target.'" href="'.esc_url($a_href).'" title="'.$a_title.'">'.$out_logo.'</a></div> ';
		return $out_partners_item;
	}
	add_shortcode('zoner_partners_item', 'zoner_partners_item_func');
							
	/*Get testimonials*/
	function zoner_testimonials_list_func($atts = array(), $content = null) {
		$out_testimonials = $el_class = $pb_title = '';
		
		$atts  = vc_map_get_attributes( 'zoner_testimonials', $atts );
		extract( $atts );
		
		$rand_id = rand(0, 250);
		
		$elem_class = array();
		$elem_class[] = 'block';
		$elem_class[] = 'testimonials';
		if (!empty($el_class))
		$elem_class[] = $el_class;

		$out_testimonials .= '<section id="testimonials-'.$rand_id.'" class="'.implode(' ', $elem_class).'">';
			if (!empty($pb_title)) 
			$out_testimonials .= '<header class="center"><h2 class="no-border">'.esc_attr($pb_title).'</h2></header>';
			$out_testimonials .= '<div id="testimonials-carousel-'.$rand_id.'" class="owl-carousel testimonials-carousel">';
				$out_testimonials .= ($content == '' || $content == ' ') ? __("Empty testimonials list. Edit page to add content here.", "zoner") : wpb_js_remove_wpautop($content);			
			$out_testimonials .= '</div>';
		$out_testimonials .= '</section>';
		
		return $out_testimonials;
	
	}
	add_shortcode('zoner_testimonials', 'zoner_testimonials_list_func');
	
	
	function zoner_testimonial_item_func($atts = array(), $content = null) {
		$out_testimonial_item = $out_image = $pb_image = $pb_author = $el_class = '';
		
		$atts  = vc_map_get_attributes( 'zoner_testimonial_item', $atts );
		extract( $atts );
		
		$elem_class = array();
		if (!empty($el_class))
		$elem_class[] = $el_class;
		
		if (!empty($pb_image)) {
			$image = wp_get_attachment_image_src( $pb_image, 'full');
			$image_url = $image[0];
			$out_image = '<img src="'.$image_url.'" alt="" />';
		} else {
			$out_image = '<img data-src="holder.js/188x188?auto=yes&text='.__('Author', 'zoner') .'" alt="" />';
		}
		
		$out_testimonial_item .= '<blockquote class="testimonial">';
			$out_testimonial_item .= '<figure>';
				$out_testimonial_item .= '<div class="image">';
					$out_testimonial_item .= $out_image;
                $out_testimonial_item .= '</div>';
			$out_testimonial_item .= '</figure>';
				$out_testimonial_item .= '<aside class="cite">';
					$out_testimonial_item .= '<p>'.$pb_cite.'</p>';
					$out_testimonial_item .= '<footer>'.$pb_author.'</footer>';
			$out_testimonial_item .= '</aside>';
		$out_testimonial_item .= '</blockquote>';
								
		return $out_testimonial_item;
	}
	add_shortcode('zoner_testimonial_item', 'zoner_testimonial_item_func');
	
	
	function zoner_funnumber_func($atts = array(), $content = null) {
		$out_number = $el_class = $pb_label = '';
		if (!wp_script_is('zoner-countTo', 'enqueued')) wp_enqueue_script('zoner-countTo');
		if (!wp_script_is('zoner-waypoints', 'enqueued')) wp_enqueue_script('zoner-waypoints');
		
		$atts  = vc_map_get_attributes( 'zoner_funnumber', $atts );
		extract( $atts );
		
		
		$id 		= rand(0,250);
		$elem_class = array();
		$elem_class[] = 'fun-facts';
		if (!empty($el_class))
		$elem_class[] = $el_class;
		
		$out_number .= '<div id="number-'.$id.'" class="'.implode(' ', $elem_class ).'">';
			$out_number .= '<div class="number-wrapper">';
				$out_number .= '<div class="number" data-from="'.$pb_from.'" data-to="'.$pb_to.'">'.$pb_to.'</div>';
				$out_number .= '<figure>'.$pb_label.'</figure>';
			$out_number .= '</div><!-- /.number-wrapper -->';
		$out_number .= '</div>';	
		
		return $out_number;
		
                 
	}
	add_shortcode('zoner_funnumber', 'zoner_funnumber_func');
	
	
	
	function zoner_ceo_user_func($atts = array(), $content = null) {
		$out_ceo = $el_class = $el_title = $el_ceo_name = $el_ceo_post = $el_background = $el_content = $link = '';
		
		$atts  = vc_map_get_attributes( 'zoner_ceo', $atts );
		extract( $atts );
		
		$elem_class = array();
		$elem_class[] = 'ceo-section';
		$elem_class[] = 'center';
		if (!empty($el_class)) $elem_class[] = $el_class;
		$id = 'ceo-section-' . rand(0, 250);
		
		$a_href = '#';
		$a_title = $el_ceo_name;
		$a_target = '_self';
		
		$link = ($link=='||') ? '' : $link;
		$link = vc_build_link($link);
		if (!empty($link['url'])) $a_href = $link['url'];
		if (!empty($link['title'])) $a_title = $link['title'];
		if (!empty($link['target'])) $a_target 	= $link['target'];
		
		$content = do_shortcode($el_content);
		
		$avatar_img_html = $bg_image_html = $avatar_img = $bg_img =  '';
		$avatar_img = wp_get_attachment_image_src( $el_avatar, 'zoner-avatar-ceo' );
		$bg_img	= wp_get_attachment_image_src( $el_background, 'full' );
		
		if (!empty($avatar_img))
			$avatar_img_html = '<img src="'.$avatar_img[0].'" alt="" />';
		if (!empty($bg_img))
			$bg_image_html = '<img src="'.$bg_img[0].'" alt="" />';

		
		$out_ceo = '<section id="'.$id.'" class="'.implode(' ',  $elem_class).'">';
			$out_ceo .= '<header class="center"><div class="cite-title">'.$el_title.'</div></header>';
			$out_ceo .= '<div class="cite no-bottom-margin">'.$content.'</div>';
			$out_ceo .= '<hr class="divider">';
			if ($avatar_img_html) 
				$out_ceo .= '<a title="'.$a_title.'" href="'.$a_href.'" class="image" target="'.$a_target.'">'.$avatar_img_html.'</a>';
				
			$out_ceo .= '<a title="'.$a_title.'" href="'.$a_href.'" target="'.$a_target.'"><h3>'.$el_ceo_name.'</h3></a>';
			$out_ceo .= '<figure class="subtitle">'.$el_ceo_post.'</figure>';
			$out_ceo .= '<div class="background-image">'.$bg_image_html.'</div>';
		$out_ceo .= '</section><!-- /#ceo-section -->';
						
	return $out_ceo;
	
	}
	add_shortcode('zoner_ceo', 'zoner_ceo_user_func');
	
	
	function zoner_advertising_user_func($atts = array(), $content = null) {
		$out_ads = $el_class = $pb_title = $pb_icon = $pb_submit_title = $link = '';
		
		$atts  = vc_map_get_attributes( 'zoner_advertising', $atts );
		extract( $atts );
		
		$elem_class = array();
		$elem_class[] = 'advertising';
		if (!empty($el_class)) $elem_class[] = $el_class;
		$id_ = 'advertising-' . rand(0, 250);
		
		
		$a_href   = '#';
		$a_title  = $pb_title;
		$a_target = '_self';
		
		$link = ($link=='||') ? '' : $link;
		$link = vc_build_link($link);
		if (!empty($link['url'])) $a_href = $link['url'];
		if (!empty($link['title'])) $a_title = $link['title'];
		if (!empty($link['target'])) $a_target 	= $link['target'];
		
		
		
		$out_ads = '<section id="'.$id_.'" class="'.implode(' ', $elem_class).'">';
			$out_ads .= '<a title="'.$a_title.'" href="'.$a_href.'" target="'.$a_target.'">';
				$out_ads .= '<div class="banner">';
					$out_ads .= '<div class="wrapper">';
						$out_ads .= '<span class="title">'.$pb_title.'</span>';
						$out_ads .= '<span class="submit">'.$pb_submit_title.' <i class="fa '.$pb_icon.'"></i></span>';
					$out_ads .= '</div>';
				$out_ads .= '</div><!-- /.banner-->';
            $out_ads .= '</a>';
		$out_ads .= '</section>';
						
		return $out_ads;
	
	}
	add_shortcode('zoner_advertising', 'zoner_advertising_user_func');
	
	
	function zoner_information_message_func($atts = array(), $content = null) {
		global $current_user, $wp_users;
		$pb_title =  $pb_description = $pb_link_title = $pb_image = $pb_custom_link = $link = $el_class = $out_info = '';
		
		$atts  = vc_map_get_attributes( 'zoner_info_message', $atts );
		extract( $atts );
		
		
		$id_ 	= 'infomessage-' . rand(0, 250);
		$a_href = $a_title = $a_target = '';
		
		wp_get_current_user();
		$user_id    = $current_user->ID;
		$elem_class = array();
		
		if (!empty($el_class)) 
		$elem_class[] = $el_class;
		$elem_class[] = 'infomessage';
		
		$link = ($link=='||') ? '' : $link;
		$link = vc_build_link($link);
		if (!empty($link['url'])) 		$a_href   = $link['url'];
		if (!empty($link['title'])) 	$a_title  = $link['title'];
		if (!empty($link['target'])) 	$a_target = $link['target'];
		
		if (!empty($a_target)) { 
			$a_target = $a_target;
		} else {
			$a_target = '_self';
		}
		
		
		if ($pb_custom_link != 'custom_link') {
			$link = '<a class="link-arrow back" target="'.$a_target.'" href="'.add_query_arg(array('profile-page' => $pb_custom_link), get_author_posts_url($current_user->ID)).'">'.esc_attr($pb_link_title).'</a>';
		} else {
			$link = '<a class="link-arrow back" target="'.$a_target.'" title="'.$a_title.'" href="'.$a_href.'">'.esc_attr($a_title).'</a>';
		}		
		
		$out_info = '<section id="'.$id_.'" class="'.implode(' ', $elem_class).'">';
			$out_info .= '<div class="info-page">';
				$out_info .= '<div class="title"><header>'.esc_attr($pb_title).'</header></div>';
				$out_info .= '<h2 class="no-border">'.esc_attr($pb_description).'</h2>';
				$out_info .= $link;
			$out_info .= '</div>';
			
			if(!empty($pb_image)) {
				$image = wp_get_attachment_image_src($pb_image, 'full');
				
				$out_info .= '<div class="background-image">';
					$out_info .= '<img src="'.$image[0].'" alt="" />';
				$out_info .= '</div>';
			}
		$out_info .= '</section>';
		
		return $out_info;
	}
	add_shortcode('zoner_info_message', 'zoner_information_message_func');

	/*Google map with items and search*/
	 require_once( 'googlemaps.shortcode.php' );
	 zoner_maps_shortcode::init();

	
	
	
	/*init visible classes*/
	class WPBakeryShortCode_zoner_headlinetext 	extends WPBakeryShortCode {}
	class WPBakeryShortCode_zoner_infobox   	extends WPBakeryShortCode {}
	class WPBakeryShortCode_zoner_blogbox 		extends WPBakeryShortCode {}
	class WPBakeryShortCode_zoner_gmaps 		extends WPBakeryShortCode {}
	class WPBakeryShortCode_zoner_separator		extends WPBakeryShortCode {}
	class WPBakeryShortCode_zoner_icon			extends WPBakeryShortCode {}
	class WPBakeryShortCode_zoner_faq			extends WPBakeryShortCode {}
	class WPBakeryShortCode_zoner_agent_listing extends WPBakeryShortCode {}
	class WPBakeryShortCode_zoner_ceo			extends WPBakeryShortCode {}
	class WPBakeryShortCode_zoner_agencies_listing 	 extends WPBakeryShortCode {}
	class WPBakeryShortCode_zoner_properties_listing extends WPBakeryShortCode {}
	class WPBakeryShortCode_zoner_partners 		extends WPBakeryShortCodesContainer {}
	class WPBakeryShortCode_zoner_partners_item extends WPBakeryShortCode {}
	class WPBakeryShortCode_zoner_testimonials 	extends WPBakeryShortCodesContainer {}
	class WPBakeryShortCode_zoner_testimonial_item extends WPBakeryShortCode {}
	class WPBakeryShortCode_zoner_funnumber 	extends WPBakeryShortCode {}
	class WPBakeryShortCode_zoner_info_message 	extends WPBakeryShortCode {}
	class WPBakeryShortCode_zoner_button 		extends WPBakeryShortCode {}