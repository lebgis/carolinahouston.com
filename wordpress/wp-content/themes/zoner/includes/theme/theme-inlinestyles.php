<?php
if ( ! function_exists( 'zoner_get_inline_styles' ) ) {				
	function zoner_get_inline_styles () {
		global $zoner_config;
		$style = '';

		$custom_colorscheme_suffix = '';
		if (!empty($zoner_config['advanced-color-scheme'])){
			$custom_colorscheme_suffix = '-cust';
		}

		if (!empty($zoner_config['content-link-color'])) {
			$regular = $hover = $active = '';
			
			$regular	= esc_attr($zoner_config['content-link-color'.$custom_colorscheme_suffix]['regular']);
			$hover 		= esc_attr($zoner_config['content-link-color'.$custom_colorscheme_suffix]['hover']);
			$active		= esc_attr($zoner_config['content-link-color'.$custom_colorscheme_suffix]['active']);
			
			$style .= '
					a { color:'.$regular.'; }
					a:hover { color:'.$hover.'; }
					a:active { color:'.$active.';}
				';
		}
		
		/*Logo*/
		if(!empty($zoner_config['logo-retina']['url'])) {
			$style .= '
				@media only screen and (-webkit-min-device-pixel-ratio: 2), 
					only screen and (min-device-pixel-ratio: 2),
					only screen and (min-resolution: 2dppx) {
						.navbar .navbar-header .navbar-brand.nav.logo { display: none; }
						.navbar .navbar-header .navbar-brand.nav.logo.retina 	{ display: inline-block; width:50%;}
					}'. "\n";
		} 
		
		
		/*Body*/
		
		if (!empty($zoner_config['body-background'])) {
			$body_styles = $zoner_config['body-background'];
			$bg_color	 = $body_styles['background-color'];
			$style .= '
				.page-sub-page #page-content::after {
					background: -moz-linear-gradient(top, #f1f1f1 0%, '.$bg_color.' 80%);
					background: -webkit-gradient(linear, left top, left bottom, color-stop(0%, #f1f1f1), color-stop(80%, '.$bg_color.'));
					background: -webkit-linear-gradient(top, #f1f1f1 0%, '.$bg_color.' 80%);
					background: -o-linear-gradient(top, #f1f1f1 0%, '.$bg_color.' 80%);
					background: -ms-linear-gradient(top, #f1f1f1 0%, '.$bg_color.' 80%);
					background: linear-gradient(to bottom, #f1f1f1 0%, '.$bg_color.' 80%);
					filter: progid:DXImageTransform.Microsoft.gradient(startColorstr="#f1f1f1", endColorstr="'.$bg_color.'",GradientType=0 );
				}	
			';	
				
			if (!empty($body_styles['media']['id']) && isset($body_styles['media']['id'])) {
				$style .= '
					.page-sub-page #page-content:after { 
						background:inherit; 
					}
				';	
				
			}
		}
		
		/*Header*/
		if (!empty($zoner_config['header-background-color'])) {
			 $style .= '
				 .navigation { 
					background-color:'.esc_attr($zoner_config['header-background-color']).'; 
				 }
			 ';	
		}
		
		if (!empty($zoner_config['prop-ggmaps-marker'])) {
			 $gg_marker = $zoner_config['prop-ggmaps-marker']; 
			 $style .= '
				 .leaflet-div-icon {
					 background-image:url('.esc_url($gg_marker['url']).'); 
				 }
			 ';	
		}
		
		/*Advanced search*/
		if (!empty($zoner_config['zoner-searchbox-advancedcolor'])) {
			$style .= '
				.horizontal-search .search-box-wrapper	{
					background-color:' .esc_attr($zoner_config['zoner-searchbox-advancedcolor'.$custom_colorscheme_suffix]).';
				}
			';
		}

		if (!empty($zoner_config['zoner-searchbox-advancedfcolor'])) {
			$color = $zoner_config['zoner-searchbox-advancedfcolor'];
			$style .= '
				.advanced-search,
				.advanced-search header h3,
				.horizontal-search .search-box-wrapper .search-box .advanced-search-toggle {
					color:' .esc_attr($color).';
				}
			';
		}	
		
		/*Menu First level*/
		if (!empty($zoner_config['menu-link-color'])) {
			$regular = $hover = $active = '';
			
			$regular	= esc_attr($zoner_config['menu-link-color']['regular']);
			$hover 		= esc_attr($zoner_config['menu-link-color']['hover']);
			$active		= esc_attr($zoner_config['menu-link-color']['active']);
			
			$style .= '
					.navigation .navbar .navbar-nav > li a { color:'.$regular.'; }
					.navigation .navbar .navbar-nav > li:hover > a { color:'.$hover.'; }
					
					.navigation .navbar .navbar-nav > li.current_page_item > a, 
					.navigation .navbar .navbar-nav > li.current-menu-item > a, 
					.navigation .navbar .navbar-nav > li.current-menu-parent > a, 
					.navigation .navbar .navbar-nav > li.current_page_parent > a, 
					.navigation .navbar .navbar-nav > li.current-menu-ancestor > a, 
					.navigation .navbar .navbar-nav > li.active a {
						color:'.$active.';
					}
					';
		}
		
		/*Submenu link color*/
		if (!empty($zoner_config['submenu-link-color'])) {
			$regular = $hover = $active = '';
			
			$regular	= esc_attr($zoner_config['submenu-link-color']['regular']);
			$hover 		= esc_attr($zoner_config['submenu-link-color']['hover']);
			$active		= esc_attr($zoner_config['submenu-link-color']['active']);
			
			$bg_regular	= esc_attr($zoner_config['submenu-itembg-color'.$custom_colorscheme_suffix]['regular']);
			$bg_hover 	= esc_attr($zoner_config['submenu-itembg-color'.$custom_colorscheme_suffix]['hover']);
			$bg_active	= esc_attr($zoner_config['submenu-itembg-color'.$custom_colorscheme_suffix]['active']);
			
			
			$style .= '
						.navigation .navbar .navbar-nav > li .child-navigation a {
							color:'.$regular.';
							background-color:'.$bg_regular.';
						}
						
						.navigation .navbar .navbar-nav > li .child-navigation li a:hover {
							background-color:'.$bg_hover.';
							color:'.$hover.';
						}
						
						.navigation .navbar .navbar-nav > li .child-navigation > li:hover > a, 
						.navigation .navbar .navbar-nav > li .child-navigation > li.current-menu-ancestor > a, 
						.navigation .navbar .navbar-nav > li .child-navigation > li .child-navigation > li.current-menu-item > a, 
						.navigation .navbar .navbar-nav > li.current-menu-ancestor > .child-navigation li.current-menu-item > a {
							background-color:'.$bg_active.';
							color:'.$active.';
						 }
					';
		}
		
		if (!empty($zoner_config['submenu-color'])) {
			 $submenu_color = esc_attr($zoner_config['submenu-color']);
			 $style .= '
				.navigation .navbar .navbar-nav > li .child-navigation {
					background-color:'.$submenu_color.'; 
				}
				
				.navigation .navbar .navbar-nav > li > .child-navigation > li:first-child a:after {
					border-color: transparent transparent '.$submenu_color.' transparent;
				}
					
				.navigation .navbar .navbar-nav > li > .child-navigation.position-bottom > li:last-child > a:after, 
						border-color: '.$submenu_color.' transparent transparent;
				}
			 ';	
		}
		
		if (!empty($zoner_config['submenu-itemborder-color'])) {
			$rgba = array();
			$rgba = $zoner_config['submenu-itemborder-color'];
			$rgb_color = zoner_hex2rgb($rgba['color']);
			$rgb_color = $rgb_color[0].','.$rgb_color[1].','.$rgb_color[2];
			$style .= '
				.navigation .navbar .navbar-nav > li .child-navigation li a {
					border-color:rgba(' . $rgba['color'] . ',' . $rgba['alpha'] . '); 
				}
			';	
		}
		
		if (!empty($zoner_config['underline-item-color'])) {
			$style .= '
				.navigation .navbar .navbar-nav > li a:after {
					background-color:'.esc_attr($zoner_config['underline-item-color'.$custom_colorscheme_suffix]).';
				}
			';	
		}
		
		
		if (!empty($zoner_config['p-opacity'])) {
			$opacity = $zoner_config['p-opacity'];
			$style .= '
					.blog-posts .blog-post .blog-post-content p, .container p {
						filter: progid:DXImageTransform.Microsoft.Alpha(Opacity='. $opacity*100 .');
						opacity: '.$opacity.';
					}
				
			';
		}
		
		
		/*Footer*/
		if (!empty($zoner_config['footer-copyright-color'])) {
			$color = $zoner_config['footer-copyright-color'];
			$style .= '
					#page-footer .inner #footer-copyright {
						color:'.$color.';
					}
			';
		}
		
		if (!empty($zoner_config['footer-copyright-bg-color'])) {
			$bg_color = $zoner_config['footer-copyright-bg-color'];
			$style .= '
					#page-footer .inner #footer-copyright {
						background-color:'.$zoner_config['footer-copyright-bg-color'].';
					}
			';
		}
		
		if (!empty($zoner_config['footer-thumbnails-mask-color'])) {
			$bg_color = $zoner_config['footer-thumbnails-mask-color'.$custom_colorscheme_suffix];
			$style .= '
					#page-footer .inner .property-thumbnail {
						background-color:'.$bg_color.';
					}
			';
		}
		
		if (!empty($zoner_config['custom-css'])) {
			$style .= wp_kses_stripslashes($zoner_config['custom-css']);
		}
			
		if (!empty($zoner_config['global-color-scheme']) || !empty($zoner_config['advanced-color-scheme'])) {
			$main_color = (!empty($zoner_config['global-color-scheme']))?$zoner_config['global-color-scheme']:0;
			
			$primary_color 		= '#1396e2';
			$secondary_color 	= '#128dd4';
			
			
			if ($main_color == 1) {
				$primary_color 		= '#998675';
				$secondary_color	= '#937E6C';
			} else if ($main_color == 2) {
				$primary_color 		= '#00c109';
				$secondary_color 	= '#00B309';
			} else if ($main_color == 3) {
				$primary_color 		= '#707070';
				$secondary_color 	= '#686868';
			} else if ($main_color == 4) {
				$primary_color 		= '#e83183';
				$secondary_color 	= '#E7237B';
			} else if ($main_color == 5) {
				$primary_color 		= '#f7941d';
				$secondary_color 	= '#F79113';
			} else if ($main_color == 6) {
				$primary_color 		= '#e2372f';
				$secondary_color 	= '#e02B21';
			} else if ($main_color == 7) {
				$primary_color 		= '#7c00c3';
				$secondary_color 	= '#7100B5';
			}

			if (!empty($zoner_config['advanced-color-scheme'])){
				$primary_color 		= $zoner_config['zoner-advanced-color-primary'];
				$secondary_color 	=  $zoner_config['zoner-advanced-color-secondary'];
			}
			
			$style .= '	
					.background-color-default,	
					.btn.btn-default, select.btn-default,
					.cluster > div:before,
					.checkbox.switch .icheckbox:before,
					.faq .icon,
					.feature-box .icon,
					.infobox-wrapper .infobox-inner .infobox-image .infobox-price,
					.jGrowl .jGrowl-notification,
					.jslider .jslider-bg .v,
					.jslider .jslider-pointer,
					.marker-cluster,
					.navigation .add-your-compare.active a,
					.property-carousel .owl-controls .owl-prev, .property-carousel .owl-controls .owl-next,
					.rating img,
					.rating .inner img,
					.ribbon,
					.submit-step .step-number,
					.search-box-wrapper .search-box .nav-pills li.active a,
					.timeline-item .circle .dot,
					.timeline-item .circle .date,
					#page .member .tag,
					#page .tag.price,
					.price-box header {
						background-color:'.$primary_color.';
					}
				';
			
			$style .= '	
					.btn.btn-default:hover, select.btn-default:hover {
						background-color: '.$secondary_color.';
					}
				';
			$style .= '	
					a:hover h1, a:hover h2, a:hover h3, a:hover h4, .avatar-wrapper .remove-btn i.fa, #agent-detail .agency-logo:after,
					.link-icon .fa, .link-arrow:after, .link-arrow.back:before,
					.universal-button figure, .universal-button .arrow, ul.list-links li a:hover, .widget ul li a:hover,
					.navigation .navbar .navbar-nav > li.has-child:after,
					.navigation .navbar .navbar-nav li .child-navigation li.has-child:after,
					.navigation .secondary-navigation a.promoted,
					.navigation .secondary-navigation a.sing-in,
					#sidebar ul li, #sidebar ul li, #sidebar .sidebar-navigation li i,
					.infobox-wrapper .infobox-inner .fa,
					.geo-location-wrapper .btn:hover,
					.property.small .info a:hover,
					.property_features-list li:before,
					.show-all:after,
					.banner .submit,
					.bookmark:before, .compare:before,
					.bookmark-added:after,  .compare-added:after,
					.comment-list .comment .reply .fa,
					.fun-facts .number-wrapper .number,
					.bootstrap-select .selectpicker .caret:after,
					.bootstrap-select .selectpicker,
					.bootstrap-select .selectpicker .filter-option:before,
					.error-page .title header, .infomessage .title header,
					.grid-data-table table tbody tr td .status i.fa.fa-check-circle-o,
					.grid-data-table table tbody tr td.actions .edit i,
					.pagination li a:hover, .pagination li a:active, .pagination li a:focus,
					.horizontal-search .search-box-wrapper .search-box .form-map .selectpicker .caret:after,
					#search-filter h3 i,
					.show-on-map .fa,
					.submit-pricing table thead tr th.title,
					.compare-list table thead tr th.title,
					.submit-pricing table tbody tr td.available,
					.compare-list table tbody tr td.is_exists,
					#page-footer .inner #footer-copyright a:hover,
					.navigation .navbar .navbar-nav li.mobile-submit i, 
					.navigation .add-your-compare a i.fa					{
						color:'.$primary_color.';
					}
				';
			$style .= '
					.search-box .form-map input[type="text"], 
					.search-box .form-map input[type="email"], 
					.search-box .form-map input[type="search"], 
					.search-box .form-map input[type="password"], 
					.search-box .form-map input[type="number"], 
					.search-box .form-map textarea, 
					.search-box .form-map select, 
					.search-box .form-map .selectpicker, 
					.search-box .form-map .price-range{
						background:'.$primary_color.';
						background-image: linear-gradient(to top, rgba(0,0,0,0.45), rgba(0,0,0,0.45));
					}
					.search-box .form-map ul.selectpicker,
					.search-box .form-map ul.selectpicker:hover{
						background: none;	
					}
    				
				';	
			$style .= '
					.search-box .form-map input[type="text"]:hover, 
					.search-box .form-map input[type="email"]:hover, 
					.search-box .form-map input[type="search"]:hover, 
					.search-box .form-map input[type="password"]:hover, 
					.search-box .form-map input[type="number"]:hover, 
					.search-box .form-map textarea:hover, 
					.search-box .form-map select:hover, 
					.search-box .form-map .selectpicker:hover, 
					.search-box .form-map .price-range:hover,
					.property .overlay .additional-info
					{
						background:'.$primary_color.';
						background-image: linear-gradient(to top, rgba(0,0,0,0.6), rgba(0,0,0,0.6));
					}
				';

					$style .= '
					hr.divider,
					#sidebar .sidebar-navigation li:hover,
					.checkbox.switch .icheckbox:hover,
					.checkbox.switch .icheckbox.checked,
					.pagination li a:hover, .pagination li a:active, .pagination li a:focus,
					.marker-style, 
					.leaflet-div-icon:after						{
						border-color:'.$secondary_color.';
					}
				';
					
			$style .= ' 
					.navigation .navbar .navbar-nav > li > .child-navigation.position-bottom > li:last-child:hover a:after {
						border-color: '.$primary_color.' transparent transparent;
					}
				';	

			
			$style .= ' 
					.feature-box .icon:after, 
					.faq .icon:after, 
					.timeline-item .circle .date:after, 
					.search-box-wrapper .search-box .nav-pills li a:after { 
						border-color: transparent '.$primary_color.' transparent transparent;
					}
				';	
			
			$style .= ' 
					.navigation .navbar .navbar-nav > li > .child-navigation > li:first-child:hover a:hover:after, 
					.navigation .navbar .navbar-nav > li.current-menu-ancestor > .child-navigation > li.current-menu-item a:after,
					.jslider .jslider-pointer:before {
						border-color: transparent transparent '.$primary_color.' transparent;
					}
				';		
			
			
			$style .= ' 
					#sidebar .sidebar-navigation li:hover:after, 
					.submit-step .step-number:after {
						border-color: transparent transparent transparent '.$primary_color.';
					}
				';
			
			$style .= '
				.search-box-wrapper {
					background-color: '.$primary_color.';
					background-image: linear-gradient(to top, rgba(0,0,0,0.45), rgba(0,0,0,0.45));
				}
				.search-box-wrapper div[class^="col-"] {
					min-height: 0;
				}
				@media (max-width: 767px) {
					.navigation .secondary-navigation {
						background-color: '.$primary_color.';
						background-image: linear-gradient(to top, rgba(0,0,0,0.65), rgba(0,0,0,0.65));
					}
				}
				#page-footer .inner #footer-copyright {
					background-color: '.$primary_color.';
					background-image: linear-gradient(to top, rgba(0,0,0,0.45), rgba(0,0,0,0.45));
				}
				.grid-data-table table thead tr th {
					background-color: '.$primary_color.';
					background-image: linear-gradient(to top, rgba(0,0,0,0.45), rgba(0,0,0,0.45));
				}
				#sidebar .widget>ul li {
					color: '.$primary_color.';
				}
			';
			
		}
		
		if ( !zoner_is_user_priv() ) {
			$style .= '
					@media (max-width: 767px) {
						.geo-location-wrapper .btn {
							top: 20px;
						}
					}
			';
		}
		
		if (!empty($style)) {
			wp_add_inline_style( 'zoner-style', zoner_compress_code($style)); 
		}	
		
	}
	add_action('wp_enqueue_scripts', 'zoner_get_inline_styles', 99);
}	
  
if ( ! function_exists( 'zoner_get_inline_scripts' ) ) {				
	function zoner_get_inline_scripts () {
		global $zoner_config;
		if (!empty($zoner_config['custom-js'])) {
			if ( wp_script_is( 'jquery', 'done' ) ) { 
				if (trim($zoner_config['custom-js']) != null) {
				?>
					<script type="text/javascript">
						<?php echo wp_kses_stripslashes($zoner_config['custom-js']); ?>
					</script>
				<?php
				}
			}
		}	
	}
	add_action( 'wp_footer', 'zoner_get_inline_scripts', 99 );
}	
if ( ! function_exists( 'zoner_hex2rgb' ) ) {	
	function zoner_hex2rgb($hex) {
	   $hex = str_replace("#", "", $hex);

	   if(strlen($hex) == 3) {
	      $r = hexdec(substr($hex,0,1).substr($hex,0,1));
	      $g = hexdec(substr($hex,1,1).substr($hex,1,1));
	      $b = hexdec(substr($hex,2,1).substr($hex,2,1));
	   } else {
	      $r = hexdec(substr($hex,0,2));
	      $g = hexdec(substr($hex,2,2));
	      $b = hexdec(substr($hex,4,2));
	   }
	   $rgb = array($r, $g, $b);

	   return $rgb; 
	}
}