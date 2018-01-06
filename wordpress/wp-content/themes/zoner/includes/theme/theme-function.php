<?php
global 	$inc_theme_url,
		$admin_theme_url,
		$prefix;

		$inc_theme_url   = get_template_directory_uri() . '/includes/theme/';
		$admin_theme_url = get_template_directory_uri() . '/includes/admin/';
		$prefix = '_zoner_';

add_theme_support( 'zoner' );
if ( ! isset( $content_width ) ) $content_width = 950;

if ( ! function_exists( 'zoner_setup' ) ) :
/**
 * Zoner Theme setup.
 * Set up theme defaults and registers support for various WordPress features.
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support post thumbnails.
 *
 * @since Zoner Theme 1.0
 */
function zoner_setup() {
	/*
	 * Make Zoner Theme available for translation.
	 *
	 * Translations can be added to the /languages/ directory.
	 * If you're building a theme based on Zoner Theme, use a find and
	 * replace to change 'zoner' to the name of your theme in all
	 * template files.
	 */

	load_theme_textdomain( 'zoner', get_template_directory() . '/languages' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'post-thumbnails' );
	set_post_thumbnail_size( 750, 750, true );

	add_image_size( 'zoner-floor-plans', 212, 155, true );
	add_image_size( 'zoner-gallery-property', 950, 480, true );
	add_image_size( 'zoner-footer-thumbnails', 440, 330, true );
	add_image_size( 'zoner-original-thumbnails', 555, 445, true );
	add_image_size( 'zoner-gallery-edit-property', 200, 200, true );
	add_image_size( 'zoner-map-property_type', 26, 26, true );
	add_image_size( 'zoner-avatar-ceo', 190, 190, true );
	add_image_size( 'zoner-home-slider', 1920, 780, true );

	register_nav_menus( array(
		'primary'   => __( 'Top primary menu', 'zoner' )
	) );

	add_theme_support( 'post-formats', array( 'aside', 'image', 'video', 'audio', 'quote', 'link', 'gallery', 'chat'));
}
endif; // zoner_setup


/*Walkers*/
class Zoner_Submenu_Class extends Walker_Nav_Menu {
	 function start_lvl(&$output, $depth = 0, $args = array()) {
		$classes 	 = array('sub-menu', 'list-unstyled', 'child-navigation');
		$class_names = implode( ' ', $classes );
		$output .= "\n" . '<ul class="' . $class_names . '">' . "\n";
	}

	function display_element( $element, &$children_elements, $max_depth, $depth=0, $args, &$output ) {
        $id_field = $this->db_fields['id'];
        if ( is_object( $args[0] ) )
        $args[0]->has_children = ! empty( $children_elements[$element->$id_field] );
        return parent::display_element( $element, $children_elements, $max_depth, $depth, $args, $output );
    }

	function start_el(&$output, $item, $depth = 0, $args = array(), $current_object_id = 0) {
		global $wp_query, $zoner_config;

		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

		$class_names_arr = array();
		$class_names = $value = '';


		$classes = empty( $item->classes ) ? array() : (array) $item->classes;

		$class_names =  join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item ) );
		$class_names_arr[] = esc_attr( $class_names );

		if ( $args->has_children )
		$class_names_arr[] = 'has-child';

		$class_names = ' class="'. implode(' ', $class_names_arr) . '"';

		$output .= $indent . '<li id="menu-item-'. $item->ID . '"' . $value . $class_names .'>';

		$attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
		$attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
		$attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
		$attributes .= ! empty( $item->url ) ? ' href="' . $item->url .'"' : '';

		$item_output = $args->before;
		$item_output .= '<a'. $attributes .'>';
		$item_output .= $args->link_before .apply_filters( 'the_title', $item->title, $item->ID );
		$item_output .= $args->link_after;
		$item_output .= '</a>';
		$item_output .= $args->after;

		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}
}

if ( ! function_exists( 'zoner_add_parent_url_menu_class' ) ) {
	function zoner_add_parent_url_menu_class( $classes = array(), $item = false ) {

		$curr_url = zoner_curPageURL();
		$home_url = trailingslashit( home_url() );

		if( is_404() or $item->url == $home_url ) return $classes;
		if ( get_post_type() == "property" ) {
			unset($classes[array_search('current_page_parent',$classes)]);

		if ( !empty($item->url) &&  !empty($curr_url))
			if ( strstr( $curr_url, $item->url) )
				$classes[] = 'current-menu-item';
		}
		return $classes;
	}
}


if ( ! function_exists( 'zoner_add_page_parent_class' ) ) {
	function zoner_add_page_parent_class( $css_class, $page, $depth, $args ) {
		if ( ! empty( $args['has_children'] ) )
		$css_class[] = 'parent';

		return $css_class;
	}
}

class Zoner_Page_Walker extends Walker_page {
	function start_lvl( &$output, $depth = 0, $args = array() ) {
		$indent = str_repeat("\t", $depth);
			$output .= "\n$indent<ul class='sub-menu list-unstyled child-navigation'>\n";
	}
}
/*End custom walkers*/

/*Customize*/
if ( ! function_exists( 'zoner_customize_register' ) ) :
	function zoner_customize_register( $wp_customize ) {
		class Zoner_Theme_Options_Button_Control extends WP_Customize_Control {
			public $type = 'button_link_control';

			public function render_content() {
				?>
					<label>
						<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
						<input class="button button-primary save link_to_options" type="button" value="<?php _e('Zoner Options', 'zoner'); ?>" onclick="javascript:location.href='<?php echo esc_url(admin_url('admin.php?page=zoner_options')); ?>'"/>
					</label>
				<?php
			}
		}

		$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
		$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
		$wp_customize->get_setting( 'header_textcolor' )->transport = 'postMessage';


		$wp_customize->remove_section( 'colors');
		$wp_customize->remove_section( 'header_image');
		$wp_customize->remove_section( 'background_image');
		$wp_customize->add_section('zoner_themeoptions_link', array(
								   'title' => __('Zoner Options', 'zoner'),
								   'priority' => 10,
								));


		$wp_customize->add_setting( 'themeoptions_button_control' );

		$wp_customize->add_control(
			new Zoner_Theme_Options_Button_Control (
				$wp_customize,
				'button_link_control',
				array(
					'label' 	=> __('Advanced theme settings', 'zoner'),
					'section' 	=> 'zoner_themeoptions_link',
					'settings' 	=> 'themeoptions_button_control'
					)
				)
			);
	}
endif; // zoner_customize_register

/**
 * Adjust content_width value for image attachment template.
 * @since Zoner Theme 1.0
 * @return void
 */
if ( ! function_exists( 'zoner_content_width' ) ) :
function zoner_content_width() {
	if ( is_attachment() && wp_attachment_is_image() ) {
		$GLOBALS['content_width'] = 950;
	}
}
endif; //zoner_content_width

/*Compress code*/
if ( ! function_exists( 'zoner_compress_code' ) ) {
	function zoner_compress_code($code) {
		$code = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $code);
		$code = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $code);

		return $code;
	}
}

/*Get Home Page Variations*/
if ( ! function_exists( 'zoner_get_header_variation_index' ) ) {
	function zoner_get_header_variation_index() {
		global $zoner_config, $zoner, $post, $prefix;
			   $header_variations = 0;

		$property_loop_page = 0;
		$property_loop_page = $zoner->zoner_get_page_id('page-property-archive');
		$page_header_variations = null;

		if ((is_page() && !is_front_page() && !is_home()) || (is_post_type_archive( 'property' ) || (is_page($property_loop_page) && $property_loop_page != 0))) {
			if (($property_loop_page != 0) && (is_post_type_archive( 'property' ))) {
				$page_header_variations = get_post_meta($property_loop_page, $prefix . 'page_header_variations', true);
			} else {
				$page_header_variations = get_post_meta(get_the_ID(), $prefix . 'page_header_variations', true);
			}

			if (!empty($page_header_variations)) {
				$header_variations = $page_header_variations;
			}
		} else {
			if (!empty($zoner_config['header-front-page-variations'])) {
				$header_variations = $zoner_config['header-front-page-variations'];
			}
		}
		return apply_filters( 'zoner_home_page_variations' , $header_variations);
	}
}

/**
 * Enqueue scripts and styles for the front end.
 * @since Zoner Theme 1.0
 * @return void
 */
if ( ! function_exists( 'zoner_scripts' ) ) {
	function zoner_scripts() {
		global 	$inc_theme_url, $zoner_config, $post, $prefix, $zoner;
		$posts_page = get_option( 'page_for_posts' );
		$zoom 		= 14;
		$cache_obj_id = $map_type = $is_rtl = 0;

		$is_mobile = 0;
		$is_marker_from_file = false;



		if ( wp_is_mobile()) $is_mobile = 1;
		$gg_marker = $lat = $lng = null;

		if (is_front_page())
		$cache_obj_id = get_option('page_on_front');
		if (is_home())
		$cache_obj_id = get_option('page_for_posts');
		if (is_rtl())
		$is_rtl = 1;

		$property_loop_page = 0;
		$property_loop_page = $zoner->zoner_get_page_id('page-property-archive');
		if ((is_page() && !is_front_page() && !is_home()) || (is_post_type_archive( 'property' ) || (is_page($property_loop_page) && $property_loop_page != 0))) {
			if (($property_loop_page != 0) && (is_post_type_archive( 'property' ))) {
				$page_lat  = get_post_meta($property_loop_page, $prefix. 'page_map_latitude', true);
				$page_lng  = get_post_meta($property_loop_page, $prefix. 'page_map_longitude', true);
				$page_zoom = get_post_meta($property_loop_page, $prefix. 'page_map_zoom', true);
				$cache_obj_id = $property_loop_page;
			} else {
				$page_lat  = get_post_meta(get_the_ID(), $prefix. 'page_map_latitude', true);
				$page_lng  = get_post_meta(get_the_ID(), $prefix. 'page_map_longitude', true);
				$page_zoom = get_post_meta(get_the_ID(), $prefix. 'page_map_zoom', true);
				$cache_obj_id = get_the_ID();
			}

			if ($page_lat) { $lat = $page_lat;
			} else {
				if (!empty($zoner_config['geo-center-lat']))  	$lat = $zoner_config['geo-center-lat'];
			}

			if ($page_lng) { $lng = $page_lng;
			} else {
				if (!empty($zoner_config['geo-center-lng']))  	$lng = $zoner_config['geo-center-lng'];
			}

			if ($page_zoom) { $zoom = $page_zoom;
			} else {
				if (!empty($zoner_config['maps-global-zoom']))  $zoom = $zoner_config['maps-global-zoom'];
			}

		} else {
			if (!empty($zoner_config['geo-center-lat']))  	$lat = $zoner_config['geo-center-lat'];
			if (!empty($zoner_config['geo-center-lng']))  	$lng = $zoner_config['geo-center-lng'];
			if (!empty($zoner_config['maps-global-zoom']))  $zoom = $zoner_config['maps-global-zoom'];
			if (!empty($zoner_config['map-markers-ff']))	$is_marker_from_file = $zoner_config['map-markers-ff'];
		}

		if (!empty($zoner_config['maps-global-type']))  $map_type 	= $zoner_config['maps-global-type'];

		if (!empty($zoner_config['prop-ggmaps-marker']))
		$gg_marker = $zoner_config['prop-ggmaps-marker'];

		$min_price = (INT)$zoner->currency->zoner_get_price_("MIN", $prefix.'price');
		$max_price = (INT)$zoner->currency->zoner_get_price_("MAX", $prefix.'price');
		$header_variations = zoner_get_header_variation_index();

		if ( is_singular()) wp_enqueue_script( 'comment-reply' );

		$path_to_markers = get_template_directory().'/markers/markers_'.$cache_obj_id.'.pin';
		$zoner_get_map_points_array = array();
		if ($header_variations > 0 && $header_variations <= 10) {
			if ($is_marker_from_file) {
				if (file_exists($path_to_markers)) {
					$zoner_get_map_points_array = @file_get_contents($path_to_markers);
				} else {
					$zoner_get_map_points_array = zoner_get_map_points_array();
				}
			} else {
				$zoner_get_map_points_array = zoner_get_map_points_array();
			}
		}
		
		do_action('zoner_before_enqueue_script');
		
		if (SCRIPT_DEBUG) {
			wp_register_script( 'zoner-mainJs',	 $inc_theme_url . 'assets/js/custom.js',	 array( 'jquery' ), '20142807', true );
		} else {
			wp_register_script( 'zoner-mainJs',	 $inc_theme_url . 'assets/js/custom.min.js',	 array( 'jquery' ), '20142807', true );
		}
		$currency = null;
		$zoner_config['is-gmap-api'] = 0;
		if ( (is_front_page() && in_array( $zoner_config['header-front-page-variations'], array(1, 2, 3, 4, 5) )) || $zoner_config['gm-or-osm'] == 0 ) {
			$zoner_config['is-gmap-api'] = 1;
		}
		if (isset($zoner_config['currency']))
		$currency = $zoner->currency->get_zoner_currency_symbol(esc_attr($zoner_config['currency']));
		wp_localize_script( 'zoner-mainJs', 'ZonerGlobal', 	array( 	'ajaxurl' 		=> admin_url( 'admin-ajax.php' ),
																	'domain'		=> esc_js(site_url()),
																	'is_mobile' 	=> $is_mobile,
																	'is_rtl'		=> $is_rtl,
																	'is_general_page' => esc_js(is_front_page()),
                                                                    'is_agency_page' => is_singular('agency'),
                                                                    'is_agent_page' => is_author(),
																	'source_path' 	=> $inc_theme_url . 'assets',
																	'start_lat'		=> $lat,
																	'start_lng'		=> $lng,
																	'locations'		=> $zoner_get_map_points_array,
																	'icon_marker' 	=> esc_js($gg_marker['url']),
																	'maps_zoom'		=> esc_js($zoom),
																	'map_type'		=> esc_js($map_type),
																	'min_price'		=> esc_js($min_price),
																	'max_price'		=> esc_js($max_price),
																	'default_currency'  => esc_js($currency),
																	'header_variations'	=> esc_js($header_variations),
																	'zoner_ajax_nonce'	=> wp_create_nonce('zoner_ajax_nonce'),
																	'zoner_message_send_text' => __('Thank you. Your message has been sent successfully.', 'zoner'),
																	'zoner_message_faq_text'  => __('Thank you for your vote.', 'zoner'),
																	'zoner_default_compare_text' => __('Compare Your Property', 'zoner'),
																	'zoner_pl_img_text_property' => __('Property', 'zoner'),
																	'zoner_pl_img_text_featured' => __('Featured', 'zoner'),
																	'zoner_pl_img_text_logo'	 => __('Logo', 'zoner'),
																	/*Stripe payment*/
																	'zoner_stripe_message_1' 	 => __('Stripe process payment.', 'zoner'),
																	'gm_or_osm' => esc_js((!empty($zoner_config['gm-or-osm']))?:0),
																	'global_fixed_header' => esc_js((!empty($zoner_config['global-fixed-header']))?:0),
																  )
							);
		wp_enqueue_script( 'zoner-mainJs' );
		/*Custom Css*/
		wp_enqueue_style( 'zoner-fontAwesom', 		$inc_theme_url . 'assets/fonts/font-awesome.min.css');
		wp_enqueue_style( 'zoner-fontElegantIcons', $inc_theme_url . 'assets/fonts/ElegantIcons.css');
		wp_enqueue_style( 'zoner-bootstrap', 	 	$inc_theme_url . 'assets/bootstrap/css/bootstrap.min.css');

		if (is_rtl())
		wp_enqueue_style( 'zoner-bootstrap-rtl', 	$inc_theme_url . 'assets/bootstrap/css/bootstrap-rtl.min.css');

		wp_enqueue_style( 'zoner-bootstrap-social', 	$inc_theme_url . 'assets/bootstrap/css/bootstrap-social-buttons.css');
		wp_enqueue_style( 'zoner-bootstrap-select', 	$inc_theme_url . 'assets/css/bootstrap-select.min.css');

		$is_edit_property 	= get_query_var('edit-property');
		$is_add_property  	= get_query_var('add-property');
		$is_edit_agency 	= get_query_var('edit-agency');
		$is_add_agency  	= get_query_var('add-agency');

		if ((isset($is_edit_property) || isset($is_add_property))  && is_user_logged_in()) {
			wp_enqueue_style( 'zoner-bootstrap-filea', 	$inc_theme_url . 'assets/css/fileinput.min.css');
		}

		wp_enqueue_style( 'zoner-magnific-css', 	$inc_theme_url . 'assets/css/magnific-popup.min.css');
		wp_enqueue_style( 'zoner-slider', 			$inc_theme_url . 'assets/css/jquery.slider.min.css');
		wp_enqueue_style( 'zoner-owl.carousel', 	$inc_theme_url . 'assets/css/owl.carousel.min.css');
		wp_enqueue_style( 'zoner-jgrowl', $inc_theme_url . 'assets/css/jquery.jgrowl.min.css');

		if ( ($header_variations > 5) && ($header_variations <= 10) or $zoner_config['gm-or-osm']) {
			wp_enqueue_style( 'zoner-osm', 		$inc_theme_url . 'assets/css/osm.min.css');
			wp_enqueue_style( 'zoner-leaflet', 	$inc_theme_url . 'assets/css/leaflet.min.css');
		}

		wp_enqueue_style( 'zoner-style', get_stylesheet_uri() );
		if (!empty($zoner_config['dynamic-css'])) wp_add_inline_style( 'zoner-style', $zoner_config['dynamic-css'] );
		/*Custom Js*/
		wp_enqueue_script( 'zoner-bootstrap', 		 $inc_theme_url . 'assets/bootstrap/js/bootstrap.min.js', array( 'jquery' ), '20142807', true );
		wp_enqueue_script( 'zoner-bootstrap-select', $inc_theme_url . 'assets/js/bootstrap-select.min.js',	  array( 'jquery' ), '20142807', true );
		wp_enqueue_script( 'zoner-bootstrap-holder',	 $inc_theme_url . 'assets/js/holder.js', array( 'jquery' ), '20142807', true );

		if ((!empty($is_edit_agency) || !empty($is_add_agency) || !empty($is_edit_property) || !empty($is_add_property) || is_author()) && is_user_logged_in()) {
			wp_enqueue_script( 'zoner-bootstrap-filei',	$inc_theme_url . 'assets/bootstrap/js/bootstrap.file-input.js', array( 'jquery' ), '20142807', true );
		}

		wp_enqueue_script( 'zoner-ichek', $inc_theme_url . 'assets/js/icheck.min.js',	 array( 'jquery' ), '20142807', true );
		if ((!empty($is_edit_property) || !empty($is_add_property))  && is_user_logged_in()) {
			wp_enqueue_script( 'zoner-bootstrap-filea',	$inc_theme_url . 'assets/js/fileinput.min.js', array( 'jquery' ), '20142807', true );
		}

		if (!empty($zoner_config['smoothscroll']))
	    wp_enqueue_script( 'zoner-smoothscroll', 	$inc_theme_url . 'assets/js/smoothscroll.js', array( 'jquery' ), '20142807', true );

		wp_register_script('zoner-waypoints',		$inc_theme_url . 'assets/js/waypoints.min.js', array( 'jquery' ), '20142807', true );
		wp_register_script('zoner-countTo', 		$inc_theme_url . 'assets/js/jquery.countTo.js', array( 'jquery' ), '20142807', true );

		wp_register_script( 'zoner-reveral',		$inc_theme_url . 'assets/js/scrollReveal.min.js',	 array( 'jquery' ), '20142807', true );
		wp_register_script( 'zoner-masonry',		$inc_theme_url . 'assets/js/masonry.pkgd.min.js',	 array( 'jquery' ), '20142807', true );
		wp_register_script( 'zoner-imagesloaded',	$inc_theme_url . 'assets/js/imagesloaded.pkgd.min.js',	 array( 'jquery' ), '20142807', true );

		if ((is_author() && is_user_logged_in()) || ($zoner_config['page-property-grid'] == '1' && (is_post_type_archive( 'property' ) || is_page( $zoner->zoner_get_page_id('page-property-archive'))))) {
			wp_enqueue_script( 'zoner-reveral');
			wp_enqueue_script( 'zoner-masonry' );
			wp_enqueue_script( 'zoner-imagesloaded');
		}
		
		wp_enqueue_script( 'zoner-owl', 		$inc_theme_url . 'assets/js/owl.carousel.min.js',	 array( 'jquery' ), '20142807', true );
        wp_enqueue_script( 'zoner-validate', 	$inc_theme_url . 'assets/js/jquery.validate.min.js',	 array( 'jquery' ), '20142807', true );

        //if we have multilanguages and not English on
        //need add files with new translation to folder
        
		$lang_get = get_locale();
		wp_register_script( 'zoner-validate-translate',	 $inc_theme_url . 'assets/js/jq-validation-translate/langs.js',	 array( 'jquery' ), '2015207', true );
        wp_localize_script( 'zoner-validate-translate', 'LangGlobal', 	array( 	'name'	=> $lang_get));
		wp_enqueue_script(  'zoner-validate-translate');

        wp_enqueue_script( 'zoner-placeholder',	$inc_theme_url . 'assets/js/jquery.placeholder.js',	 array( 'jquery' ), '20142807', true );
		wp_enqueue_script('zoner-jgrowl', 		$inc_theme_url . 'assets/js/jquery.jgrowl.min.js', array( 'jquery' ), '20142807', true );

		wp_enqueue_script( 'zoner-raty', $inc_theme_url . 'assets/js/jquery.raty.min.js', array( 'jquery' ), '20142807', true );
		if (is_singular('property') || is_single() || is_home() || is_author() || is_archive() || is_search())
			wp_enqueue_script( 'zoner-popup',$inc_theme_url	. 'assets/js/jquery.magnific-popup.min.js',	 array( 'jquery' ), '20142807', true );
		 wp_enqueue_script( 'zoner-price-slider',		$inc_theme_url . 'assets/js/jquery.slider.min.js',	 array( 'jquery' ), '20142807', true );

		if (is_author() && is_user_logged_in()) {
			wp_enqueue_style ( 'zoner-pscroll', $inc_theme_url . 'assets/css/perfect-scrollbar.min.css');
			wp_enqueue_script( 'zoner-pscroll', $inc_theme_url . 'assets/js/perfect-scrollbar.min.js',  array( 'jquery' ), '20142807', true );
			wp_enqueue_script('heartbeat');
		}
        zoner_scripts_map();
		
		do_action('zoner_after_enqueue_script');
	}
}

/*Return boolean base on have current page map or not*/
  if ( ! function_exists( 'zoner_map_location_pages' ) ) {
        function zoner_map_location_pages(){
        //1. Map in header
        $header_variation = zoner_get_header_variation_index();
        if (!empty($header_variation) && $header_variation<=10) return true;
        //2. Is property page
        if (is_singular('property')) return true;
        //3. Is agency page
        if (is_singular('agency')) return true;
	     //4. Is edit\add property page
	    $is_edit_property = get_query_var('edit-property');
		$is_add_property  = get_query_var('add-property');
		if (!empty($is_edit_property) || !empty($is_add_property)) return true;
		// 5. Is edit agency page
		$is_add_agency  	= get_query_var('add-agency');
		$is_edit_agency 	= get_query_var('edit-agency');
		if (!empty($is_add_agency) || !empty($is_edit_agency)) return true;
        // 6. Is author page
        if (is_author()) return true;
        return false;
       }
  }
   if ( ! function_exists( 'zoner_scripts_map' ) ) {
        function zoner_scripts_map($shortcode = false) {//TODO shortcode now only google map
        global $zoner_config, $inc_theme_url;
        if (!zoner_map_location_pages() && !$shortcode) return false;
        $is_google_map = empty($zoner_config['gm-or-osm']);
		if ($is_google_map || $shortcode) {
			    $currentLang = substr(get_bloginfo( 'language' ), 0, 2);
		        $lang_attr = (!empty($currentLang))?'&language='.$currentLang:'';
		        $api_key_attr     = (!empty($zoner_config['google-maps-api-key'] ))?'&key=' . $zoner_config['google-maps-api-key']:'';
		        if(!wp_script_is('googlemaps', 'enqueued'))
		            wp_enqueue_script ( 'googlemaps',   '//maps.googleapis.com/maps/api/js?v=3&libraries=places'.$lang_attr.$api_key_attr );
		        if(!wp_script_is('zoner-markerclusterer', 'enqueued'))
                    wp_enqueue_script ( 'zoner-markerclusterer',  $inc_theme_url . '/assets/js/markerclusterer.js',   array( 'jquery' ), '20151203', true );
                if(!wp_script_is('zoner-richmarker', 'enqueued'))
                    wp_enqueue_script ( 'zoner-richmarker', 	 $inc_theme_url . '/assets/js/richmarker-compiled.js',   array( 'jquery' ), '20151203', true );
                if(!wp_script_is('zoner-infobox', 'enqueued'))
                    wp_enqueue_script ( 'zoner-infobox',   $inc_theme_url . '/assets/js/infobox.js', array( 'jquery' ), '20151203', true );
                if(!wp_script_is('zoner-leaflet', 'enqueued'))
                    wp_enqueue_script ( 'zoner-leaflet', 	  		$inc_theme_url . '/assets/js/leaflet.js',   array( 'jquery' ), '20151203', true );
                if(!wp_script_is('zoner-markercluster-leaflet', 'enqueued'))
                    wp_enqueue_script( 'zoner-markercluster-leaflet', $inc_theme_url . 'assets/js/leaflet.markercluster.js', array( 'jquery' ), '20142807', true );
                if(!wp_script_is('zoner-markerwithlabel', 'enqueued'))
                    wp_enqueue_script( 'zoner-markerwithlabel', $inc_theme_url . 'assets/js/markerwithlabel_packed.js',	 array( 'jquery' ), '20142807', true );
		}else{
            if(!wp_script_is('zoner-leaflet', 'enqueued'))
                wp_enqueue_script ( 'zoner-leaflet', 	  		$inc_theme_url . '/assets/js/leaflet.js',   array( 'jquery' ), '20151203', true );
            if(!wp_script_is('zoner-markercluster-leaflet', 'enqueued'))
                wp_enqueue_script( 'zoner-markercluster-leaflet', $inc_theme_url . 'assets/js/leaflet.markercluster.js', array( 'jquery' ), '20142807', true );
		}
		if(!wp_script_is('zoner-customMap', 'enqueued'))
			wp_enqueue_script( 'zoner-customMap',		$inc_theme_url . 'assets/js/custom-map.min.js',	 array( 'jquery', 'zoner-mainJs' ), '20142807', true );
    }
}

/**
 * Extend the default WordPress body classes.
 *
 * Adds body classes to denote:
 * 1. Single or multiple authors.
 * 2. Presence of header image.
 * 3. Index views.
 * 4. Full-width content layout.
 * 5. Presence of footer widgets.
 * 6. Single views.
 * 7. Featured content layout.
 *
 * @since Zoner Theme 1.0
 *
 * @param array $classes A list of existing body class values.
 * @return array The filtered body class list.
 */
if ( ! function_exists( 'zoner_body_classes' ) ) :
function zoner_body_classes( $classes ) {
	global $prefix, $zoner, $zoner_config;
	$header_variations = zoner_get_header_variation_index();
	$posts_page = get_option( 'page_for_posts' );

	if ( is_multi_author() ) {
		$classes[] = 'group-blog';
	}

	if ( get_header_image() ) {
		$classes[] = 'header-image';
	} else {
		$classes[] = 'masthead-fixed';
	}

	if ( is_archive() || is_search() || is_home() || is_404() || is_tag() || is_category()) {
		$classes[] = 'list-view';
		$classes[] = 'page-sub-page';
		$classes[] = 'page-legal';
	}

	$is_edit_property = get_query_var('edit-property');
	$is_add_property  = get_query_var('add-property');
	if ($is_edit_property && is_user_logged_in() && is_singular('property')) {
		$classes[] = 'page-submit';
	}

	if (isset($is_add_property) && is_user_logged_in()) {
		$classes[] = 'page-submit';
	}

	$is_edit_agency = get_query_var('edit-agency');
	$is_add_agency  = get_query_var('add-agency');
	if ($is_edit_agency && is_user_logged_in() && is_singular('agency')) {
		$classes[] = 'page-submit';
	}

	if (isset($is_add_agency) && is_user_logged_in()) {
		$classes[] = 'page-submit';
	}

	if ( is_active_sidebar( 'sidebar-3' ) ) {
		$classes[] = 'footer-widgets';
	}

	if ( is_singular() && !is_front_page() && !is_page()) {
		$classes[] = 'singular';
		$classes[] = 'page-sub-page';
		$classes[] = 'page-legal';
	}


	if ( is_front_page() || (is_home() && (empty($posts_page) && ($posts_page > 0)))) {
		$classes[] = 'page-homepage';
		if($header_variations > 0) $classes = array_merge($classes, zoner_get_header_variations_class($header_variations));
	}

	$property_loop_page = 0;
	$property_loop_page = $zoner->zoner_get_page_id('page-property-archive');
	if ((is_page() && !is_front_page() && !is_home()) || (is_post_type_archive( 'property' ) || (is_page($property_loop_page) && $property_loop_page != 0))) {
		$classes[] = 'page-sub-page';
		$classes[] = 'page-legal';
		if($header_variations > 0) $classes = array_merge($classes, zoner_get_header_variations_class($header_variations));
	}


	return $classes;
}
endif; //zoner_body_classes



if ( ! function_exists( 'zoner_get_header_variations_class' ) ) :
function zoner_get_header_variations_class( $variation ) {
	$classes = array();
	/*Google Map*/
	if ($variation <= 5) {

		$classes[] = 'map-google';
		if ($variation == 1)  {
			$classes[] = 'navigation-fixed-bottom';
			$classes[] = 'has-fullscreen-map';
		}

		if (($variation == 3) || ($variation == 5))
		$classes[] = 'navigation-fixed-top';

		if ($variation == 4)
		$classes[] = 'horizontal-search-float';
		if ($variation == 5)
		$classes[] = 'horizontal-search';

	}

		/*OSM*/
	if (($variation > 5) && ($variation <= 10)) {
		$classes[] = 'map-osm';

		if ($variation == 6) {
			$classes[] = 'navigation-fixed-bottom';
			$classes[] = 'has-fullscreen-map';
		}

		if (($variation == 8) || ($variation == 10))
		$classes[] = 'navigation-fixed-top';
		if ($variation == 9)
		$classes[] = 'horizontal-search-float';
		if ($variation == 10)
		$classes[] = 'horizontal-search';

	}

		/*Slider*/
	if ($variation > 10) {
		$classes[] = 'page-slider';
		$classes[] = 'navigation-fixed-top';

		if (($variation == 11) || ($variation == 16))
		$classes[] = 'page-slider-search-box';
		if ($variation == 12)
		$classes[] = 'page-property-slider-search-box';
		if (($variation == 13) || ($variation == 17))
		$classes[] = 'horizontal-search-float';
		if (($variation == 14) || ($variation == 18))
		$classes[] = 'horizontal-search';
		if ($variation  > 14)
		$classes[] = 'rs-slider-header';
	}
	return $classes;
}
endif;

/**
 * Extend the default WordPress post classes.
 * Adds a post class to denote:
 * Non-password protected page with a post thumbnail.

 * @since Zoner Theme 1.0
 * @param array $classes A list of existing post class values.
 * @return array The filtered post class list.
 */
if ( ! function_exists( 'zoner_post_classes' ) ) :
function zoner_post_classes( $classes ) {
	if ( ! post_password_required() && has_post_thumbnail() ) {
		$classes[] = 'has-post-thumbnail';
	}
	return $classes;
}
endif; //zoner_post_classes


/**
 * Create a nicely formatted and more specific title element text for output
 * in head of document, based on current view.
 *
 * @since Zoner Theme 1.0
 *
 * @param string $title Default title text for current view.
 * @param string $sep Optional separator.
 * @return string The filtered title.
 */
if ( ! function_exists( 'zoner_wp_title' ) ) :
function zoner_wp_title( $title, $sep ) {
	global $paged, $page;
	if ( is_feed() ) {
		return $title;
	}
	$title .= get_bloginfo( 'name' );
	$site_description = get_bloginfo( 'description', 'display' );
	if ( $site_description && ( is_home() || is_front_page() ) ) {
		$title = "$title $sep $site_description";
	}

	if ( $paged >= 2 || $page >= 2 ) {
		$title = "$title $sep " . sprintf( __( 'Page %s', 'zoner' ), max( $paged, $page ) );
	}

	return $title;
}
endif; //zoner_wp_title


if ( ! function_exists( 'zoner_list_authors' ) ) :
/**
 * Print a list of all site contributors who published at least one post.
 * @since Zoner Theme 1.0
 * @return void
 */
function zoner_list_authors() {
	$contributor_ids = get_users( array(
		'fields'  => 'ID',
		'orderby' => 'post_count',
		'order'   => 'DESC',
		'who'     => 'authors',
	) );

	foreach ( $contributor_ids as $contributor_id ) :
		$post_count = count_user_posts( $contributor_id );

		// Move on if user has not published a post (yet).
		if ( ! $post_count ) {
			continue;
		}
	?>

	<div class="contributor">
		<div class="contributor-info">
			<div class="contributor-avatar"><?php echo get_avatar( $contributor_id, 132 ); ?></div>
			<div class="contributor-summary">
				<h2 class="contributor-name"><?php echo get_the_author_meta( 'display_name', $contributor_id ); ?></h2>
				<p class="contributor-bio">
					<?php echo get_the_author_meta( 'description', $contributor_id ); ?>
				</p>
				<a class="contributor-posts-link" href="<?php echo esc_url( get_author_posts_url( $contributor_id ) ); ?>">
					<?php printf( _n( '%d Article', '%d Articles', $post_count, 'zoner' ), $post_count ); ?>
				</a>
			</div><!-- .contributor-summary -->
		</div><!-- .contributor-info -->
	</div><!-- .contributor -->

	<?php
	endforeach;
}
endif; //zoner_list_authors


/**
 * Getter function for Featured Content Plugin.
 * @since Zoner Theme 1.0
 * @return array An array of WP_Post objects.
 */
if ( ! function_exists( 'zoner_get_featured_posts' ) ) :
function zoner_get_featured_posts() {
	/**
	 * Filter the featured posts to return in Zoner Theme.
	 * @since Zoner Theme 1.0
	 * @param array|bool $posts Array of featured posts, otherwise false.
	 */
	return apply_filters( 'zoner_get_featured_posts', array() );
}
endif; //zoner_get_featured_posts

/**
 * A helper conditional function that returns a boolean value.
 * @since Zoner Theme 1.0
 * @return bool Whether there are featured posts.
 */
if ( ! function_exists( 'zoner_has_featured_posts' ) ) :
function zoner_has_featured_posts() {
	return ! is_paged() && (bool) zoner_get_featured_posts();
}
endif; //zoner_has_featured_posts

/*Custom functions*/
if ( ! function_exists( 'zoner_get_logo' ) ) {
	function zoner_get_logo() {
		global $zoner_config;

		$original_logo = $retina_logo = $width = $height = null;
		if ($zoner_config['logo-dimensions']['width'])
		$width 	= $zoner_config['logo-dimensions']['width'];
		if ($zoner_config['logo-dimensions']['height'])
		$height = $zoner_config['logo-dimensions']['height'];

		if (!empty($zoner_config['logo']['url'])) { $original_logo = esc_url($zoner_config['logo']['url']); } else { $original_logo = ''; }
		if (!empty($zoner_config['logo-retina']['url'])) { $retina_logo 	 = esc_url($zoner_config['logo-retina']['url']);  } else {  $retina_logo   = ''; }

		/*Full Backend Options*/
		$description  = $name = '';
		$description  = esc_attr(get_bloginfo('description'));
		$name  		  = esc_attr(get_bloginfo('name'));

		if (!empty($original_logo) || !empty($retina_logo)) {
			if ($original_logo) echo '<a class="navbar-brand nav logo" href="' 			. esc_url( home_url( '/' ) ) . '" title="' . $description .'" rel="home"><img style="width:'.$width.'; height:'.$height.';" width="'.(int)$width.'" height="'.(int)$height.'" src="'. $original_logo  .'" alt="' . $description . '"/></a>';
			if ($retina_logo) 	echo '<a class="navbar-brand nav logo retina" href="' 	. esc_url( home_url( '/' ) ) . '" title="' . $description .'" rel="home"><img style="width:'.$width.'; height:'.$height.';" width="'.(int)$width.'" height="'.(int)$height.'" src="'. $retina_logo    .'" alt="' . $description . '"/></a>';

		} else {
			echo  '<a class="navbar-brand nav" href="' . esc_url( home_url( '/' ) ) . '" title="' . $description .'" rel="home"><h1 class="site-title">'. $name .'</h1><h2 class="site-description">'. $description .'</h2></a>';
		}
	}
} //zoner_get_logo


if ( ! function_exists( 'zoner_get_main_nav' ) ) {
	function zoner_get_main_nav() {
		if ( has_nav_menu( 'primary' ) ) {
			 wp_nav_menu( array(
							'theme_location' 	=> 'primary',
							'menu_class' 	 	=> 'nav navbar-nav',
							'container'		 	=> 'nav',
							'container_class' 	=> 'collapse navbar-collapse bs-navbar-collapse navbar-right',
							'walker' 			=> new zoner_submenu_class()));
		} else {
			?>
				<nav class="collapse navbar-collapse bs-navbar-collapse navbar-right">
					<ul class="nav navbar-nav">
						<?php wp_list_pages(array('title_li' => '', 'sort_column' => 'ID', 'walker' => new Zoner_Page_Walker())); ?>
					</ul>
				</nav>
			<?php
		}
	}
}


/*User privilege*/
if ( ! function_exists( 'zoner_get_current_user_role' ) ) {
	function zoner_get_current_user_role() {
		global $wp_roles;
		$current_user = wp_get_current_user();
		$roles = $current_user->roles;
		$role  = array_shift($roles);
		return isset($wp_roles->role_names[$role]) ? translate_user_role($wp_roles->role_names[$role] ) : false;
	}
}

if ( ! function_exists( 'zoner_is_user_priv' ) ) {
	function zoner_is_user_priv() {
		return ( is_user_logged_in() && (is_admin() || is_super_admin() || (zoner_get_current_user_role() == 'Agent') || current_user_can( 'edit_propertys', get_current_user_id() )));
	}
}

if ( ! function_exists( 'zoner_add_property' ) ) {
	function zoner_add_property() {
        global $zoner_config;
        if (!zoner_is_user_priv()) return;

        $site_url = site_url('');
        if (function_exists('icl_get_home_url'))
            $site_url = icl_get_home_url();
        if (!empty($zoner_config['subheader-add-property'])) {
		?>
			<div class="add-your-property">
				<a href="<?php echo add_query_arg(array('add-property' => get_current_user_id()), esc_url($site_url)); ?>" class="btn btn-default"><i class="fa fa-plus"></i><span class="text"><?php _e('Add Your Property', 'zoner'); ?></span></a>
			</div>
		<?php
		}
	}
}


if ( ! function_exists( 'zoner_add_compare' ) ) {
	function zoner_add_compare() {
		global $zoner, $zoner_config;

		if (!is_user_logged_in()) return;

		$compare_page_link = '#';

		$page_compare = null;
		$page_compare = $zoner->zoner_get_page_id('page-compare');

		if (!empty($page_compare)) {
			$compare_page_link = esc_url(get_permalink($page_compare));
		}

		$arr_class = array();
		$arr_class[] = 'add-your-compare';
		$c_prop = $zoner->compare->zoner_get_all_count_compare();
		if ($c_prop > 0) $arr_class[] = 'active';
		if (!empty($zoner_config['subheader-compare'])) {
            ?>
			<div class="<?php echo implode(' ', $arr_class); ?>">
                <a href="<?php echo $compare_page_link; ?>" class="btn btn-default">
                    <i class="fa fa-building-o"></i>
                    <i class="fa fa-building"></i>
                    <?php if ($c_prop > 0) { ?>
                        <span class="text"><?php printf(__('%1s of 3 Property', 'zoner'), $c_prop); ?></span>
                    <?php } else { ?>
                        <span class="text"><?php _e('Compare Your Property', 'zoner'); ?></span>
                    <?php } ?>
                </a>
            </div>

        <?php
        }
	}
}



/*Search form*/
if ( ! function_exists( 'zoner_search_form' ) ) {
	function zoner_search_form( $form ) {
		$form = '';

		$form .= '<form role="search" method="get" id="searchform" class="searchform" action="' . home_url( '/' ) . '" >';
			$form .= '<div class="input-group">';
				$form .= '<input type="search" class="form-control" value="' . get_search_query() . '" name="s" id="s" placeholder="'.__('Enter Keyword', 'zoner').'"/>';
				$form .= '<span class="input-group-btn"><button class="btn btn-default search" type="button"><i class="fa fa-search"></i></button></span>';
			$form .= '</div><!-- /input-group -->';
		$form .= '</form>';
		return $form;
	}
} //zoner_search_form

if ( ! function_exists( 'zoner_ExludeSearchFilter' ) ) {
	function zoner_ExludeSearchFilter($query) {
		if ( !$query->is_admin && $query->is_search) {
			  $query->set('post_type', 'post');
		}
		return $query;
	}
}

if ( ! function_exists( 'zoner_kses_data' ) ) {
	function zoner_kses_data($text = null) {
		$allowed_tags = wp_kses_allowed_html( 'post' );
		return wp_kses($text, $allowed_tags);
	}
}

if ( ! function_exists( 'zoner_change_excerpt_more' ) ) {
	function zoner_change_excerpt_more( $more ) {
		return '&#8230;';
	}
}

if ( ! function_exists( 'zoner_modify_read_more_link' ) ) {
	function zoner_modify_read_more_link() {
		//return '<a class="link-arrow" href="' . get_permalink() . '">'.__('Read More', 'zoner').'</a>';
		return ''; //double more in blog
	}
}

if ( ! function_exists( 'zoner_get_footer_area_sidebars' ) ) {
	function zoner_get_footer_area_sidebars() {
		global $zoner_config;

		$footer_dynamic_sidebar = '';
		$zoner_sidebars_class = array();
		$total_sidebars_count = 0;
		$total_sidebars_count = $zoner_config['footer-widget-areas'];


		if ($total_sidebars_count != 0) {

			if ($total_sidebars_count == 1) {
				$zoner_sidebars_class[] = 'col-md-12';
				$zoner_sidebars_class[] = 'col-sm-12';
			} else if ($total_sidebars_count == 2) {
				$zoner_sidebars_class[] = 'col-md-6';
				$zoner_sidebars_class[] = 'col-sm-6';
			} else if ($total_sidebars_count == 3) {
				$zoner_sidebars_class[] = 'col-md-4';
				$zoner_sidebars_class[] = 'col-sm-4';
			} else if ($total_sidebars_count == 4) {
				$zoner_sidebars_class[] = 'col-md-3';
				$zoner_sidebars_class[] = 'col-sm-3';
			} else {
				$zoner_sidebars_class[] = 'col-md-3';
				$zoner_sidebars_class[] = 'col-sm-3';
			}


			ob_start();

			for ( $i = 1; $i <= intval( $total_sidebars_count ); $i++ ) {

				if (zoner_active_sidebar('footer-'.$i)) {
					echo '<div class="'.implode(' ', $zoner_sidebars_class).'">';
						zoner_sidebar('footer-'.$i);
					echo '</div>';
				}
			}

			$footer_dynamic_sidebar = ob_get_clean();
			if (!empty($footer_dynamic_sidebar)) {

			?>

				<section id="footer-main">
					<div class="container">
						<div class="row">
							<?php echo $footer_dynamic_sidebar; ?>
						</div>
					</div>
				</section>
			<?php

			}
		}
	}
}

if ( ! function_exists( 'zoner_get_footer_area_thumbnails' ) ) {
	function zoner_get_footer_area_thumbnails() {
		global $zoner_config, $zoner;

		$args = array( 'post_type' 		=> 'property',
					   'post_status'	=> 'publish',
					   'posts_per_page' => 20,
					   'orderby'	    => 'rand',
					   'showposts' 		=> 20  );

		$prop_posts = new WP_Query( $args );

		if ( $prop_posts->have_posts() && ($zoner_config['switch-footer-thumbnails'])) {

		?>
			<section id="footer-thumbnails" class="footer-thumbnails">
		<?php
			while ( $prop_posts->have_posts() ) : $prop_posts->the_post();
				$id_   		  = get_the_ID();
				$footer_thumb = wp_get_attachment_image_src( get_post_thumbnail_id($id_), 'thumbnail' );

				if (!empty($footer_thumb)) {
					echo '<div id="property-thumbnail-'.$id_.'" class="property-thumbnail"><a href="'. get_permalink( $id_ ) .'"><img src="'.$footer_thumb[0].'" alt="" /></a></div>';
				} else {
					echo '<div id="property-thumbnail-'.$id_.'" class="property-thumbnail"><a href="'. get_permalink( $id_ ) .'"><img width="100%" class="img-responsive" data-src="holder.js/440x330?auto=yes&text='.__('Property', 'zoner').'" alt="" /></a></div>';
				}
			endwhile;
		?>
			</section><!-- /#footer-thumbnails -->
		<?php
			wp_reset_postdata();
		}
	}
}

if ( ! function_exists( 'zoner_get_social' ) ) {
	function zoner_get_social() {
		global $zoner_config;
		$ftext = $fsocial = $out_ftext = '';
		$out_ = '';

		if (!empty($zoner_config['footer-text'])) {
			$ftext = zoner_kses_data(stripslashes($zoner_config['footer-text']));

			if (is_home() || is_front_page()) {
				$out_ftext .= $ftext;
			} else {
				$out_ftext .= '<nofollow>';
					$out_ftext .= $ftext;
				$out_ftext .= '</nofollow>';

			}
		}

		if (!empty($zoner_config['footer-issocial'])) {
			if ($zoner_config['footer-issocial']) {
				$fsocial .= '<div class="social pull-right">';
					$fsocial .= '<div class="icons">';
					if (!empty($zoner_config['facebook-url'])) 	{ $fsocial .= '<a title="Facebook" 	href="'.esc_url($zoner_config['facebook-url']).'"><i class="icon social_facebook"></i></a>'; }
					if (!empty($zoner_config['twitter-url'])) 	{ $fsocial .= '<a title="Twitter" 	href="'.esc_url($zoner_config['twitter-url']).'"><i class="icon social_twitter"></i></a>'; }
					if (!empty($zoner_config['linkedin-url'])) 	{ $fsocial .= '<a title="Linked In" href="'.esc_url($zoner_config['linkedin-url']).'"><i class="icon social_linkedin"></i></a>'; }
					if (!empty($zoner_config['myspace-url'])) 	{ $fsocial .= '<a title="My space" 	href="'.esc_url($zoner_config['myspace-url']).'"><i class="icon social_myspace"></i></a>'; }
					if (!empty($zoner_config['gplus-url'])) 	{ $fsocial .= '<a title="Google+"	href="'.esc_url($zoner_config['gplus-url']).'"><i class="icon social_googleplus"></i></a>'; }
					if (!empty($zoner_config['dribbble-url'])) 	{ $fsocial .= '<a title="Dribble" 	href="'.esc_url($zoner_config['dribbble-url']).'"><i class="icon social_dribbble"></i></a>';	}
					if (!empty($zoner_config['flickr-url'])) 	{ $fsocial .= '<a title="Flickr" 	href="'.esc_url($zoner_config['flickr-url']).'"><i class="icon social_flickr"></i></a>'; }
					if (!empty($zoner_config['youtube-url'])) 	{ $fsocial .= '<a title="YouTube" 	href="'.esc_url($zoner_config['youtube-url']).'"><i class="icon social_youtube"></i></a>'; }
					if (!empty($zoner_config['delicious-url'])) 	{ $fsocial .= '<a title="Delicious" href="'.esc_url($zoner_config['delicious-url']).'"><i class="icon social_delicious"></i></a>'; }
					if (!empty($zoner_config['deviantart-url']))	{ $fsocial .= '<a title="Deviantart" href="'.esc_url($zoner_config['deviantart-url']).'"><i class="icon social_deviantart"></i></a>'; }
					if (!empty($zoner_config['rss-url'])) 			{ $fsocial .= '<a title="RSS" 		href="'.esc_url($zoner_config['rss-url']).'"><i class="icon social_rss"></i></a>'; }
					if (!empty($zoner_config['instagram-url']))  { $fsocial .= '<a title="Instagram" href="'.esc_url($zoner_config['instagram-url']).'"><i class="icon social_instagram"></i></a>'; }
					if (!empty($zoner_config['pinterest-url']))  { $fsocial .= '<a title="Pinterset" href="'.esc_url($zoner_config['pinterest-url']).'"><i class="icon social_pinterest"></i></a>'; }
					if (!empty($zoner_config['vimeo-url'])) 		{ $fsocial .= '<a title="Vimeo" 	href="'.esc_url($zoner_config['vimeo-url']).'"><i class="icon social_vimeo"></i></a>'; }
					if (!empty($zoner_config['picassa-url'])) 		{ $fsocial .= '<a title="Picassa" 	href="'.esc_url($zoner_config['picassa-url']).'"><i class="icon social_picassa"></i></a>'; }
					if (!empty($zoner_config['social_tumblr']))		{ $fsocial .= '<a title="Tumblr" 	href="'.esc_url($zoner_config['social_tumblr']).'"><i class="icon social_tumblr"></i></a>'; }
					if (!empty($zoner_config['email-address']))  	{ $fsocial .= '<a title="Email" 	href="mailto:'.esc_attr($zoner_config['email-address']).'"><i class="icon icon_mail_alt"></i></a>'; }
					if (!empty($zoner_config['skype-username'])) 	{ $fsocial .= '<a title="Call to '.esc_attr($zoner_config['skype-username']).'" href="href="skype:'.esc_attr($zoner_config['skype-username']).'?call"><i class="icon social_skype"></i></a>'; }
					$fsocial .= '</div><!-- /.icons -->';
				$fsocial .= '</div><!-- /.social -->';
			}
		}


		$out_ = '<section id="footer-copyright">';
			$out_ .= '<div class="container">';
				if (!empty($out_ftext)) {
					$out_ .= '<div class="copyright pull-left">'.$out_ftext.'</div><!-- /.copyright -->';
				} else {
					$out_ .= '<div class="copyright pull-left"><a title="'.get_bloginfo('name').'" href="'.site_url().'">'.$out_ftext.'</a></div><!-- /.copyright -->';
				}

				if ($fsocial != '') $out_ .= $fsocial;
				$out_ .='<span class="go-to-top pull-right"><a href="#page-top" class="roll">' . __('Go to top', 'zoner') . '</a></span>';

			$out_ .= '</div><!-- /.container -->';
		$out_ .= '</section>';

		echo $out_;
	}
}


if ( ! function_exists( 'zoner_visibilty_comments' ) ) {
	function zoner_visibilty_comments() {
		global $zoner_config, $post;

		if (!empty($zoner_config['pp-comments'])) {
			$is_comment = $zoner_config['pp-comments'];
			$post_type = get_post_type();

			if ( ( $is_comment == $post_type || $is_comment == 'both' || is_singular('property') ) && is_page() ) {
				if ( ! post_password_required() && ( comments_open() || get_comments_number() ) ) { comments_template(); }
			}

			if ( ( $is_comment == $post_type || $is_comment == 'both' || is_singular('property') ) && is_single() ) {
				if ( ! post_password_required() && ( comments_open() || get_comments_number() ) ) { comments_template(); }
			}
		}
	}
}

if ( ! function_exists( 'zoner_breadcrumbs_generate' ) ) {
	function zoner_breadcrumbs_generate($args = array()) {
		global $wp_query, $prefix, $wp_rewrite;

		$breadcrumb = '';
		$trail = array();

		$path = '';
		$defaults = array(
			'separator' 	  => '',
			'before' 		  => false,
			'after'  		  => false,
			'front_page' 	  => true,
			'show_home' 	  => __( 'Home', 'zoner' ),
			'echo' 			  => true,
			'show_posts_page' => true
		);


		if ( is_singular() )$defaults["singular_{$wp_query->post->post_type}_taxonomy"] = false;

		extract( wp_parse_args( $args, $defaults ) );

		if ( !is_front_page() && $show_home )
			$trail[] = '<li><a href="' . esc_url( home_url() ) . '" title="' . esc_attr( get_bloginfo( 'name' ) ) . '" rel="home" class="trail-begin">' . esc_html( $show_home ) . '</a></li>';

		/* If viewing the front page of the site. */
		if ( is_front_page() ) {
			if ( !$front_page )
				$trail = false;
			elseif ( $show_home )
				$trail['trail_end'] = "{$show_home}";
		}

		/* If viewing the "home"/posts page. */
		elseif ( is_home() ) {
			$is_add_property = get_query_var('add-property');
			$is_add_agency   = get_query_var('add-agency');

			if (!empty($is_add_property) || !empty($is_add_agency)  ) {
				if(!empty($is_add_property))
				$trail['trail_end'] = __('Create Property', 'zoner');
				if(!empty($is_add_agency))
				$trail['trail_end'] = __('Create Agency', 'zoner');

			} else {
				$home_page = get_page( $wp_query->get_queried_object_id() );
				$trail 	   = array_merge( $trail, zoner_breadcrumbs_get_parents( $home_page->post_parent, '' ) );
				$trail['trail_end'] = get_the_title( $home_page->ID );
			}
		}

		/* If viewing a singular post (page, attachment, etc.). */
		elseif ( is_singular() ) {
			$post = $wp_query->get_queried_object();
			$post_id = absint( $wp_query->get_queried_object_id() );
			$post_type = $post->post_type;
			$parent = $post->post_parent;
			if ( 'page' !== $post_type && 'post' !== $post_type ) {

				$post_type_object = get_post_type_object( $post_type );
				if ( 'post' == $post_type || 'attachment' == $post_type || ( $post_type_object->rewrite['with_front'] && $wp_rewrite->front ) ) $path .= trailingslashit( $wp_rewrite->front );
				if ( !empty( $post_type_object->rewrite['slug'] ) ) $path .= $post_type_object->rewrite['slug'];
				if ( !empty( $path ) && '/' != $path ) $trail = array_merge( $trail, zoner_breadcrumbs_get_parents( '', $path ) );
				if ( !empty( $post_type_object->has_archive ) && function_exists( 'get_post_type_archive_link' ) ) $trail[] = '<li><a href="' . get_post_type_archive_link( $post_type ) . '" title="' . esc_attr( $post_type_object->labels->name ) . '">' . $post_type_object->labels->name . '</a></li>';
			}

			/* If the post type path returns nothing and there is a parent, get its parents. */
			if ( empty( $path ) && 0 !== $parent || 'attachment' == $post_type ) $trail = array_merge( $trail, zoner_breadcrumbs_get_parents( $parent, '' ) );

			/* Toggle the display of the posts page on single blog posts. */
			if ( 'post' == $post_type && $show_posts_page == true && 'page' == get_option( 'show_on_front' ) ) {
				$posts_page = get_option( 'page_for_posts' );
				if ( $posts_page != '' && is_numeric( $posts_page ) ) {
					 $trail = array_merge( $trail, zoner_breadcrumbs_get_parents( $posts_page, '' ) );
				}
			}

			/* Display terms for specific post type taxonomy if requested. */
			if ( isset( $args["singular_{$post_type}_taxonomy"] ) && $terms = get_the_term_list( $post_id, $args["singular_{$post_type}_taxonomy"], '', ', ', '' ) ) $trail[] = $terms;

			/* End with the post title. */
			$post_title = get_the_title( $post_id ); // Force the post_id to make sure we get the correct page title.
			if ( !empty( $post_title ) ) $trail['trail_end'] = $post_title;
		}

		/* If we're viewing any type of archive. */
		elseif ( is_archive() ) {

			/* If viewing a taxonomy term archive. */
			if ( is_tax() || is_category() || is_tag() ) {

				/* Get some taxonomy and term variables. */
				$term = $wp_query->get_queried_object();
				$taxonomy = get_taxonomy( $term->taxonomy );

				/* Get the path to the term archive. Use this to determine if a page is present with it. */
				if ( is_category() )
					$path = get_option( 'category_base' );
				elseif ( is_tag() )
					$path = get_option( 'tag_base' );
				else {
					if ( $taxonomy->rewrite['with_front'] && $wp_rewrite->front )
						$path = trailingslashit( $wp_rewrite->front );
					$path .= $taxonomy->rewrite['slug'];
				}

				/* Get parent pages by path if they exist. */
				if ( $path )
					$trail = array_merge( $trail, zoner_breadcrumbs_get_parents( '', $path ) );

				/* If the taxonomy is hierarchical, list its parent terms. */
				if ( is_taxonomy_hierarchical( $term->taxonomy ) && $term->parent )
					$trail = array_merge( $trail, zoner_breadcrumbs_get_term_parents( $term->parent, $term->taxonomy ) );

				/* Add the term name to the trail end. */
				$trail['trail_end'] = $term->name;
			}

			/* If viewing a post type archive. */
			elseif ( function_exists( 'is_post_type_archive' ) && is_post_type_archive() ) {

				/* Get the post type object. */
				$post_type_object = get_post_type_object( get_query_var( 'post_type' ) );

				/* If $front has been set, add it to the $path. */
				if ( $post_type_object->rewrite['with_front'] && $wp_rewrite->front )
					$path .= trailingslashit( $wp_rewrite->front );

				/* If there's a slug, add it to the $path. */
				if ( !empty( $post_type_object->rewrite['archive'] ) )
					$path .= $post_type_object->rewrite['archive'];

				/* If there's a path, check for parents. */
				if ( !empty( $path ) && '/' != $path )
					$trail = array_merge( $trail, zoner_breadcrumbs_get_parents( '', $path ) );

				/* Add the post type [plural] name to the trail end. */
				$trail['trail_end'] = $post_type_object->labels->name;
			}

			/* If viewing an author archive. */
			elseif ( is_author() ) {

				/* If $front has been set, add it to $path. */
				if ( !empty( $wp_rewrite->front ) )
					$path .= trailingslashit( $wp_rewrite->front );

				/* If an $author_base exists, add it to $path. */
				if ( !empty( $wp_rewrite->author_base ) )
					$path .= $wp_rewrite->author_base;

				/* If $path exists, check for parent pages. */
				if ( !empty( $path ) )
					$trail = array_merge( $trail, zoner_breadcrumbs_get_parents( '', $path ) );

				/* Add the author's display name to the trail end. */
				$trail['trail_end'] = get_the_author_meta( 'display_name', get_query_var( 'author' ) );
			}

			/* If viewing a time-based archive. */
			elseif ( is_time() ) {

				if ( get_query_var( 'minute' ) && get_query_var( 'hour' ) )
					$trail['trail_end'] = get_the_time( __( 'g:i a', 'zoner' ) );

				elseif ( get_query_var( 'minute' ) )
					$trail['trail_end'] = sprintf( __( 'Minute %1$s', 'zoner' ), get_the_time( __( 'i', 'zoner' ) ) );

				elseif ( get_query_var( 'hour' ) )
					$trail['trail_end'] = get_the_time( __( 'g a', 'zoner' ) );
			}

			/* If viewing a date-based archive. */
			elseif ( is_date() ) {

				/* If $front has been set, check for parent pages. */
				if ( $wp_rewrite->front )
					$trail = array_merge( $trail, zoner_breadcrumbs_get_parents( '', $wp_rewrite->front ) );

				if ( is_day() ) {
					$trail[] = '<li><a href="' . get_year_link( get_the_time( 'Y' ) ) . '" title="' . get_the_time( esc_attr__( 'Y', 'zoner' ) ) . '">' . get_the_time( __( 'Y', 'zoner' ) ) . '</a></li>';
					$trail[] = '<li><a href="' . get_month_link( get_the_time( 'Y' ), get_the_time( 'm' ) ) . '" title="' . get_the_time( esc_attr__( 'F', 'zoner' ) ) . '">' . get_the_time( __( 'F', 'zoner' ) ) . '</a></li>';
					$trail['trail_end'] = get_the_time( __( 'j', 'zoner' ) ) ;
				}

				elseif ( get_query_var( 'w' ) ) {
					$trail[] = '<li><a href="' . get_year_link( get_the_time( 'Y' ) ) . '" title="' . get_the_time( esc_attr__( 'Y', 'zoner' ) ) . '">' . get_the_time( __( 'Y', 'zoner' ) ) . '</a></li>';
					$trail['trail_end'] = sprintf( __( 'Week %1$s', 'zoner' ), get_the_time( esc_attr__( 'W', 'zoner' ) ) );
				}

				elseif ( is_month() ) {
					$trail[] = '<li><a href="' . get_year_link( get_the_time( 'Y' ) ) . '" title="' . get_the_time( esc_attr__( 'Y', 'zoner' ) ) . '">' . get_the_time( __( 'Y', 'zoner' ) ) . '</a></li>';
					$trail['trail_end'] = get_the_time( __( 'F', 'zoner' ) );
				}

				elseif ( is_year() ) {
					$trail['trail_end'] = get_the_time( __( 'Y', 'zoner' ) ) ;
				}
			}
		}

		/* If viewing search results. */
		elseif ( is_search() )
			$trail['trail_end'] = '<li>' . sprintf( __( 'Search results for &quot;%1$s&quot;', 'zoner' ), esc_attr( get_search_query() ) ) . '</li>';

		/* If viewing a 404 error page. */
		elseif ( is_404() )
			$trail['trail_end'] =  __( '404 Not Found', 'zoner' );

		/* Connect the breadcrumb trail if there are items in the trail. */
		if ( is_array( $trail ) ) {

			/* If $before was set, wrap it in a container. */
			if ( !empty( $before ) )
				$breadcrumb .= '<span class="trail-before">' . wp_kses_post( $before ) . '</span> ';

			/* Wrap the $trail['trail_end'] value in a container. */
			if ( !empty( $trail['trail_end'] ) && !is_search() )
				$trail['trail_end'] = '<li class="active"><span class="trail-end">' . wp_kses_post( $trail['trail_end'] ) . '</span></li>';

			/* Format the separator. */
			if ( !empty( $separator ) )
				$separator = '<li><span class="sep">' . wp_kses_post( $separator ) . '</span></li>';

			/* Join the individual trail items into a single string. */
			$breadcrumb .= join( " {$separator} ", $trail );

			/* If $after was set, wrap it in a container. */
			if ( !empty( $after ) )
				$breadcrumb .= '<li><span class="trail-after">' . wp_kses_post( $after ) . '</span></li>';

			/* Close the breadcrumb trail containers. */
		}


			$breadcrumb = '<!-- Breadcrumb --><div class="container"><ol class="breadcrumb">' . $breadcrumb . '</ol></div>';
		/* Output the breadcrumb. */
		if ( $echo ) echo $breadcrumb; else return $breadcrumb;
	}
}

if ( ! function_exists( 'zoner_breadcrumbs_get_parents' ) ) {
	function zoner_breadcrumbs_get_parents( $post_id = '', $path = '' ) {
		$trail = array();

		if ( empty( $post_id ) && empty( $path ) ) return $trail;
		if ( empty( $post_id ) ) {
			$parent_page = get_page_by_path( $path );
			if( empty( $parent_page ) ) $parent_page = get_page_by_title ( $path );
			if( empty( $parent_page ) ) $parent_page = get_page_by_title ( str_replace( array('-', '_'), ' ', $path ) );
			if ( !empty( $parent_page ) ) $post_id = $parent_page->ID;
		}

		if ( $post_id == 0 && !empty( $path ) ) {
			$path = trim( $path, '/' );
			preg_match_all( "/\/.*?\z/", $path, $matches );
			if ( isset( $matches ) ) {
				$matches = array_reverse( $matches );
				foreach ( $matches as $match ) {
					if ( isset( $match[0] ) ) {
						$path = str_replace( $match[0], '', $path );
						$parent_page = get_page_by_path( trim( $path, '/' ) );
						if ( !empty( $parent_page ) && $parent_page->ID > 0 ) {
							$post_id = $parent_page->ID;
							break;
						}
					}
				}
			}
		}

		while ( $post_id ) {
				$page = get_page( $post_id );
				$parents[]  = '<li><a href="' . get_permalink( $post_id ) . '" title="' . esc_attr( get_the_title( $post_id ) ) . '">' . esc_html( get_the_title( $post_id ) ) . '</a></li>';
				$post_id = $page->post_parent;
		}

		if ( isset( $parents ) ) $trail = array_reverse( $parents );
		return $trail;
	}
}

if ( ! function_exists( 'zoner_breadcrumbs_get_term_parents' ) ) {
	function zoner_breadcrumbs_get_term_parents( $parent_id = '', $taxonomy = '' ) {
		$trail = array();
		$parents = array();

		if ( empty( $parent_id ) || empty( $taxonomy ) ) return $trail;
		while ( $parent_id ) {
			$parent = get_term( $parent_id, $taxonomy );
			$parents[] = '<li><a href="' . get_term_link( $parent, $taxonomy ) . '" title="' . esc_attr( $parent->name ) . '">' . $parent->name . '</a></li>';
			$parent_id = $parent->parent;
		}

		if ( !empty( $parents ) ) $trail = array_reverse( $parents );
		return $trail;
	}
}

if ( ! function_exists( 'zoner_add_breadcrumbs' ) ) {
	function zoner_add_breadcrumbs() {
		global $zoner_config;
		if (!empty($zoner_config['pp-breadcrumbs'])) {
			if ($zoner_config['pp-breadcrumbs']) {
				if (!is_front_page()) zoner_breadcrumbs_generate();
			}
		}
	}
}

if ( ! function_exists( 'zoner_seconadry_navigation' ) ) {
	function zoner_seconadry_navigation() {
		global $zoner_config, $current_user, $wp_users, $zoner, $sitepress;

		if (isset($zoner_config['show-secondary-nav']) && empty($zoner_config['show-secondary-nav'])) return;

		wp_get_current_user();
		$user_id = $current_user->ID;
		$count_agencies = 0;
		$count_agencies = $zoner->invites->zoner_get_count_agencies_from_agent($user_id);
		$role = $zoner->zoner_get_current_user_role();

		$is_create_agency_account = false;
		$is_register_profile_link = false;

		if (!empty($zoner_config['register-agency-account'])) {
			$is_create_agency_account = !empty($zoner_config['register-agency-account']);
		}



		if (!empty($zoner_config['register-profile-link'])) {
			$is_register_profile_link = !empty($zoner_config['register-profile-link']);
		}

		if (!empty($zoner_config['paid-system'])) {
			$is_create_agency_account = $zoner->membership->zoner_is_available_agency_for_curr_user();
		}


		$site_url = site_url('');
		if (function_exists('icl_get_home_url'))
		$site_url = icl_get_home_url();
		?>
		<div class="secondary-navigation">
			<div class="container">
				<div class="contact">
					<?php if (!empty($zoner_config['header-phone'])) { ?>
						<figure><strong><?php _e('Phone', 'zoner'); ?>:</strong><?php echo $zoner_config['header-phone']; ?></figure>
					<?php } ?>
					<?php if (!empty($zoner_config['header-email'])) { ?>
						<figure><strong><?php _e('Email', 'zoner'); ?>:</strong><a href="mailto:<?php echo $zoner_config['header-email']; ?>"><?php echo $zoner_config['header-email']; ?></a></figure>
					<?php } ?>
				</div>
				<div class="user-area">
					<div class="actions">
						<?php if ( is_user_logged_in() ) { ?>

							<?php $zoner->conversation->zc_get_messages_notification(); ?>

							<a class="promoted" href="<?php echo add_query_arg(array('profile-page' => 'my_profile'), get_author_posts_url($current_user->ID)); ?>"><i class="fa fa-user"></i> <strong><?php echo zoner_get_user_name($current_user); ?></strong></a>
							<?php if  (($count_agencies == 0) && ($is_create_agency_account == true) && (($role == 'Agent') || ($role == 'Administrator'))) {  ?>
								<a class="promoted" href="<?php echo add_query_arg(array('add-agency' => get_current_user_id()) , esc_url($site_url)); ?>" title="<?php _e('Create Agency', 'zoner'); ?>" class="promoted add-agency"><?php _e('Create Agency', 'zoner'); ?></a>
							<?php } ?>
							<a class="promoted logout" href="<?php echo wp_logout_url(esc_url($site_url)); ?>" title="<?php _e('Sign Out', 'zoner'); ?>"><?php _e('Sign Out', 'zoner'); ?></a>
						<?php } else { ?>

							<?php
								if ($is_register_profile_link) {
									$page_register = null;
									$page_register = $zoner->zoner_get_page_id('page-register-account')
							?>
								<a class="promoted" href="<?php echo esc_url(get_permalink($page_register)); ?>" class="register"><strong><?php _e('Register', 'zoner'); ?></strong></a>
							<?php } ?>

							<?php
								$page_signin = null;
								$page_signin = $zoner->zoner_get_page_id('page-signin')
							?>

							<?php if (!empty($page_signin) && !empty($zoner_config['sign-in-link'])) { ?>
								<a href="<?php echo esc_url(get_permalink($page_signin)); ?>" class="sing-in"><?php _e('Sign In', 'zoner'); ?></a>
							<?php } ?>

						<?php } ?>
					</div>
					<?php
						if (isset($zoner_config['wmpl-flags-box'])) {
                            if (isset($sitepress))
                            $lang_lisy_type=$sitepress->get_settings();
							if ( function_exists( 'icl_get_languages' ) ) {
								$languages = icl_get_languages('skip_missing=0&orderby=code');
									if(!empty($languages) &&  $lang_lisy_type["icl_lang_sel_type"]!='dropdown') {
					?>
										<div class="language-bar">
											<?php

												foreach($languages as $l) {
													if($l['country_flag_url']){
														if(!$l['active']) { echo '<a href="'.$l['url'].'">'; } else { echo '<a class="active" href="'.$l['url'].'">'; };
															echo '<img src="'.$l['country_flag_url'].'" height="11" alt="'.$l['language_code'].'" width="16" />';
														if(!$l['active']) echo '</a>';
													}
												}
											?>
										</div>
					<?php
								}else{?>
                                        <div id="lang_sel" class="language-bar">
                                            <ul>
                                                <li>
											<?php
												foreach($languages as $l) {
                                                    if($l['country_flag_url']){
                                                        if($l['active']) { echo '<a class="lang_sel_sel active">';
                                                        echo '<img src="'.$l['country_flag_url'].'" class="iclflag" height="11" alt="'.$l['language_code'].'" width="16" />&nbsp;'.$l['native_name'].'</a>';
                                                        }
                                                    }

                                                }
											?>
                                                <ul>
                                                    <?php
                                                    foreach($languages as $l) {
                                                        if($l['country_flag_url']){
                                                            if(!$l['active']) { echo '<li><a href="'.$l['url'].'">';
                                                            echo '<img src="'.$l['country_flag_url'].'" class="iclflag" height="11" alt="'.$l['language_code'].'" width="16" />&nbsp;';
                                                            echo $l['native_name'].'</a><li>';}
                                                        }
                                                    }
                                                    ?>
                                                </ul>
                                                </li>
										</div>
                                 <?php }
							}
						}
					?>
				</div>


			</div>
		</div>

		<?php
	}
}


if ( ! function_exists( 'zoner_before_content' ) ) {
	function zoner_before_content() {
		$elem_class = array();
		$elem_class[] = 'wpb_row';
		$elem_class[] = 'vc_row-fluid';
		if (is_front_page()) $elem_class[] = 'block';
	?>
		<section class="<?php echo implode(' ', $elem_class); ?>">
			<div class="container">
				<div class="row">
	<?php

	}
}


if ( ! function_exists( 'zoner_after_content' ) ) {
	function zoner_after_content () {

	?>
				</div>
			</div>
		</section>
	<?php

	}
}


if ( ! function_exists( 'zoner_property_loop' ) ) {
	function zoner_property_loop () {
		global $zoner_config, $prefix;
		$layout = 3;
		if (!empty($zoner_config['prop-layout'])) $layout = (int)$zoner_config['prop-layout'];

		function zoner_get_property_loop_items() {
		?>
			<section id="results" class="results">
				<?php zoner_get_property_grid_header(); ?>
				<?php zoner_get_property_grid_items(); ?>
			</section>
		<?php
		}

		function zoner_get_property_sidebar() {
			?>
				<div id="sidebar" class="sidebar">
					<?php
						if (zoner_active_sidebar('property-archive')) {
							zoner_sidebar('property-archive');
						}
					?>
				</div>
			<?php
		}


			if ($layout == 1) {
		?>
			<div class="col-md-12 col-sm-12">
				<?php zoner_get_property_loop_items();?>
			</div>
		<?php
			} else if ($layout == 2) {
		?>
			<div class="col-md-3 col-sm-3">
				<?php zoner_get_property_sidebar(); ?>
			</div>
			<div class="col-md-9 col-sm-9">
				<?php zoner_get_property_loop_items(); ?>
			</div>

		<?php
			} else if ($layout == 3) {
		?>
			<div class="col-md-9 col-sm-9">
				<?php zoner_get_property_loop_items(); ?>
			</div>
			<div class="col-md-3 col-sm-3">
				<?php zoner_get_property_sidebar(); ?>
			</div>

		<?php
			}
	}
}

if ( ! function_exists( 'zoner_zoner_get_sidebar_part' ) ) {
	function zoner_get_sidebar_part($sidebar) {
		global $zoner_config, $zoner, $prefix;
	?>
		<div id="sidebar" class="sidebar">
			<?php if (zoner_active_sidebar($sidebar)) zoner_sidebar($sidebar); ?>
		</div>
	<?php
	}
}

if ( ! function_exists( 'zoner_get_content_part' ) ) {
	function zoner_get_content_part($type_in) {
		$title = '';
		if ( have_posts() ) {

			if (($type_in == 'agency') && is_post_type_archive('agency') && !is_single()) {
				echo '<section id="agencies-listing" class="agencies-listing">';
				echo '<header><h1>'. __('Agencies Listing', 'zoner').'</h1></header>';
			}

			$page_for_posts = get_option( 'page_for_posts' );
			$page_on_front  = get_option('page_on_front');

			if (is_home() && !empty($page_for_posts)) {
				echo '<header><h1>'.get_the_title($page_for_posts).'</h1></header>';
			}  elseif (is_front_page() && empty($page_for_posts) && empty($page_on_front)) {
				echo '<header><h1>'.__('Latest posts', 'zoner').'</h1></header>';
			}

			if (is_archive()) {
				if ( is_day() ) :
					$title = sprintf( __( 'Daily Archives: %s', 'zoner' ),   get_the_date() );
				elseif ( is_month() ) :
					$title = sprintf( __( 'Monthly Archives: %s', 'zoner' ), get_the_date( _x( 'F Y', 'monthly archives date format', 'zoner' ) ) );
				elseif ( is_year() ) :
					$title = sprintf( __( 'Yearly Archives: %s', 'zoner' ),  get_the_date( _x( 'Y', 'yearly archives date format', 'zoner' ) ) );
				else :
					if (($type_in != 'agency') && !is_post_type_archive('agency')) {
					 	 $title = __( 'Archives', 'zoner' );
					}
				endif;
			}



			if (is_category()) $title = sprintf( __( 'Category: %s', 'zoner' ), single_cat_title( '', false ) );
			if (is_search()) $title = sprintf( __( 'Search Results for: %s', 'zoner' ), get_search_query() );
			if (is_tag()) $title = sprintf( __( 'Tag Archives: %s', 'zoner' ), single_tag_title( '', false ) );

			if ($title != '') echo '<header><h1>'.$title.'</h1></header>';

				while ( have_posts() ) : the_post();
					if (($type_in == 'property') || is_post_type_archive('property')) {
						get_template_part( 'content', 'property' );
					} elseif ($type_in == 'agency') {
						get_template_part( 'content', 'agency' );
					} elseif ($type_in == 'page') {
						get_template_part( 'content', 'page' );
					} elseif (($type_in == 'agency') && is_post_type_archive('agency')) {
						get_template_part( 'content', 'agency' );
					} elseif ($type_in == 'front-page') {
						the_content();
					} else {
						get_template_part( 'content', get_post_format() );
					}
				endwhile;

			if ($type_in == 'post') zoner_paging_nav();
			if (($type_in == 'agency') && is_post_type_archive('agency') && !is_single()) {
				echo '</section>';
			}
		} else {
			echo '<header><h1>'. __('Nothing Found', 'zoner').'</h1></header>';
			get_template_part( 'content', 'none' );
		}
	}
}

if ( ! function_exists( 'zoner_the_main_content' ) ) {
	function zoner_the_main_content () {
		global $zoner_config, $prefix, $post;
		$layout = 3;
		$sidebar = 'secondary';
		$add_wrapper = true;

		$type   = get_post_type( $post );
		$postID = is_home() ? get_option( 'page_for_posts' ) : $post->ID;

		if ($type == 'property') {
			if (!empty($zoner_config['prop-layout'])) $layout = (int)$zoner_config['prop-layout'];
			$sidebar = 'property';
		} elseif (($type == 'agency')) {
			if (!empty($zoner_config['pp-agency-archive-layout'])) $layout = (int)$zoner_config['pp-agency-archive-layout'];
			$sidebar = 'property';
		} elseif ($type == 'page') {
			$page_layout = get_post_meta($postID, $prefix.'pages_layout', true);
			if ($page_layout) $layout = $page_layout;
			$sidebar = 'secondary';
		} else {
			$page_layout = get_post_meta($postID, $prefix.'pages_layout', true);
			if ($page_layout) $layout = $page_layout;
			elseif (!empty($zoner_config['pp-post'])) $layout = (int)$zoner_config['pp-post'];
			$sidebar = 'primary';
		}

		$page_on_front = get_option('page_on_front');
		if (is_front_page() && !empty($page_on_front)) {
			$page_layout = get_post_meta($postID, $prefix.'pages_layout', true);

			if ($page_layout) $layout  = $page_layout;

			$type 	 = 'front-page';
			$sidebar = 'primary';

			$front_page_content = $post->post_content;
			if (strpos($front_page_content, 'vc_row') !== false) $add_wrapper = false;

		}


		if ($layout == -1) {
			zoner_get_content_part($type);
		} elseif ($layout == 1) {
	?>
		<?php if ($add_wrapper) { ?>
			<div class="col-md-12 col-sm-12">
		<?php } ?>
			<?php zoner_get_content_part($type);?>
		<?php if ($add_wrapper) { ?>
			</div>
		<?php } ?>
	<?php
		} else if ($layout == 2) {
	?>
		<div class="col-md-3 col-sm-3">
			<?php zoner_get_sidebar_part($sidebar); ?>
		</div>
		<div class="col-md-9 col-sm-9">
			<?php zoner_get_content_part($type); ?>
		</div>

	<?php
		} else if ($layout == 3) {
	?>
		<div class="col-md-9 col-sm-9">
			<?php zoner_get_content_part($type); ?>
		</div>
		<div class="col-md-3 col-sm-3">
			<?php zoner_get_sidebar_part($sidebar); ?>
		</div>


	<?php
		}
	}
}


if ( ! function_exists( 'zoner_compare_content' ) ) {
	function zoner_compare_content() {
		global  $zoner_config, $zoner, $wp_query, $prefix;
		$out_html = '';

		$compare_args = $post_in = array();
		$post_in 	  = $zoner->compare->zoner_get_compare_property_id();

		if (empty($post_in))  $post_in = array(-1);

		$compare_args = array(
			'post_type' 		=> 'property',
			'posts_per_page'  	=> -1,
			'post_status' 		=> 'publish',
			'post__in'			=> $post_in
		);

		$args = array('orderby' => 'name', 'order' => 'ASC', 'hide_empty' => false);
		$property_features = get_terms('property_features', $args);

		$out_html  =  '<div class="col-md-12 col-sm-12">';
		$out_html .=  '<header><h1>'.__('Compare Properties', 'zoner').'</h1></header>';

		$thead_html  = '<th class="field-label col-md-3 col-xs-3">'. __('Property parameters', 'zoner'). '</th>';
		$countries = $states = $addresses = $cities = $districts = $zips = $gg_links = $list_rooms = $list_beds = $list_baths = $list_areas = $list_garages = $payments = $conditions = $featured_list = $list_plans = $list_videos =  $galleries  = '';

		$compare_prop = new wp_query($compare_args);
		$exists_posts = array();

		$b_val = '<td class="field-label col-md-3 col-xs-3">';
		$a_val = '</td>';

		if ( $compare_prop->have_posts() && $compare_prop->found_posts > 1) {

			$out_html .= '<div class="table-responsive compare-list">';
				$out_html .= '<table class="table">';
					while ( $compare_prop->have_posts() ) : $compare_prop->the_post();
						$out_thumbnal = '<img width="100%" class="img-responsive" data-src="holder.js/440x330?auto=yes&text='.__('Property', 'zoner') .'" alt="" />';

						$prop_id = get_the_ID();
						$exists_posts[] = $prop_id;

						if (has_post_thumbnail()) {
							$attachment_id 	  = get_post_thumbnail_id( $prop_id );
							$thumb 			  = wp_get_attachment_image_src( $attachment_id, 'zoner-footer-thumbnails');
								$out_thumbnal = '<img width="100%" class="img-responsive" src="'.$thumb[0].'" alt="" />';
						}

						$gproperty = array();
						$gproperty  = $zoner->property->get_property($prop_id);
						$currency	= $gproperty->currency;
						$price 		= $gproperty->price;
						$price_html = $gproperty->price_html;

						$rooms 		= $gproperty->rooms;
						$beds 		= $gproperty->beds;
						$baths 		= $gproperty->baths;
						$garages 	= $gproperty->garages;
						$condition	= $gproperty->condition;
						$condition_name = $gproperty->condition_name;

						$country 	= $gproperty->country;
						$state 		= $gproperty->state;
						$address 	= $gproperty->address;
						$city		= $gproperty->city;
						$district	= $gproperty->district;
						$zip		= $gproperty->zip;

						$area	= $gproperty->area;
						$area_unit  = esc_attr($zoner_config['area-unit']);
						if ($gproperty->area_unit) $area_unit	= $gproperty->area_unit;

						$payment_rent = $gproperty->payment_rent;
						$payment_rent_name = $gproperty->payment_rent_name;

						$is_featured = $gproperty->is_featured;

						$lat = $gproperty->lat;
						$lng = $gproperty->lng;

						$prop_gallery = $gproperty->prop_gallery;
						$prop_plans   = $gproperty->prop_plans;
						$prop_video   = $gproperty->prop_video;

						$prop_status_html = $prop_type_html = array();
						$prop_types  	= $gproperty->property_types;
						$prop_status 	= $gproperty->property_status;

						if (!empty($prop_types)) {
							foreach ($prop_types as $prop_type)  {
								$attachment_id = $zoner->zoner_tax->get_zoner_term_meta($prop_type->term_id, 'thumbnail_id');
								$img_tax = wp_get_attachment_image_src($attachment_id, array(26, 26));
								if (!empty($img_tax)) {
									$prop_type_html[]  = array('name' => $prop_type->name, 'icon' => '<img width="26" height="26" src="'.$img_tax[0].'" alt="" />');
								} else {
									$prop_type_html[]  = array('name' => $prop_type->name, 'icon' => '<img width="26" height="26" src="'. get_template_directory_uri() . '/includes/theme/assets/img/empty.png' .'" alt="" />');
								}
							}
						}

						if (!empty($prop_status)) {
							foreach ($prop_status as $status)  {
								$prop_status_html[] = $status->name;
							}
						}

						$thead_html  .= '<th class="field-label col-md-3 col-xs-3">';
							$thead_html  .= '<div id="property-'.$prop_id.'" class="property">';
								$thead_html  .= '<a href="'.get_the_permalink($prop_id).'">';
								$thead_html  .= '<div class="property-image">';
									if (!empty($prop_status_html))
									$thead_html .= '<figure class="tag status">'.implode(', ', $prop_status_html).'</figure>';

									if (!empty($prop_type_html)) {
										foreach ($prop_type_html as $type) {
											if (!empty($type) && isset($type['icon']) && isset($type['name']))
											$thead_html .= '<figure class="type" title="'.$type['name'].'">'.$type['icon'].'</figure>';
										}
									}
								$thead_html  .= $out_thumbnal;
								$thead_html  .= '</div>';
								$thead_html  .= '<div class="overlay">';
									$thead_html  .= '<div class="info">';
										$thead_html  .= $price_html;
										$thead_html  .= '<h3>'.get_the_title($prop_id).'</h3>';
									$thead_html  .= '</div>';
								$thead_html  .= '</div>';
							$thead_html  .= '</div>';
							$thead_html  .= '</a>';
						$thead_html  .= '</th>';



						if ($country) { $countries .=  $b_val.$zoner->countries->countries[$country].$a_val; } else {
										$countries .=  $b_val.'<i class="fa fa-times"></i>'.$a_val;
						}

						if ($state) { $country =  $zoner->countries->get_states($country);
									  $states .=  $b_val.$country[$state].$a_val; } else {
									  $states .=  $b_val.'<i class="fa fa-times"></i>'.$a_val;
						}

						if ($address) { $addresses .=  $b_val.$address.'</td>'; } else {
									    $addresses .=  $b_val.'<i class="fa fa-times"></i>'.$a_val;
						}

						if ($city) { $cities .=  $b_val.$city.'</td>'; } else {
									 $cities .=  $b_val.'<i class="fa fa-times"></i>'.$a_val;
						}

						if ($district) { $districts .=  $b_val.$district.'</td>'; } else {
										 $districts .=  $b_val.'<i class="fa fa-times"></i>'.$a_val;
						}

						if ($zip) { $zips .=  $b_val.$zip.'</td>'; } else {
									$zips .=  $b_val.'<i class="fa fa-times"></i>'.$a_val;
						}

						if (!empty($lat) && !empty($lng)) {
							$gg_links .=  $b_val.'<a href="https://www.google.com.au/maps/preview/@'.$lat.','.$lng.',14z" target="_blank"><i class="fa fa-map-marker"></i></a>'.$a_val; } else {
							$gg_links .=  $b_val.'<i class="fa fa-times"></i>'.$a_val;
						}


						if ($condition) { $conditions .=  $b_val.$condition_name .'</td>'; } else {
										  $conditions .=  $b_val.'<i class="fa fa-times"></i>'.$a_val;
						}

						if ($payment_rent) { $payments .=  $b_val.$payment_rent_name.'</td>'; } else {
											 $payments .=  $b_val.'<i class="fa fa-times"></i>'.$a_val;
						}

						if ($area) { $list_areas .=  $b_val.$area.' '. $zoner->property->ret_area_units_by_id($area_unit) .'</td>'; } else {
									 $list_areas .=  $b_val.'<i class="fa fa-times"></i>'.$a_val;
						}

						if ($rooms) { $list_rooms .=  $b_val.$rooms.'</td>'; } else {
									  $list_rooms .=  $b_val.'<i class="fa fa-times"></i>'.$a_val;
						}

						if ($beds) { $list_beds .=  $b_val.$beds.'</td>'; } else {
									 $list_beds .=  $b_val.'<i class="fa fa-times"></i>'.$a_val;
						}

						if ($baths) { $list_baths .=  $b_val.$baths.'</td>'; } else {
									  $list_baths .=  $b_val.'<i class="fa fa-times"></i>'.$a_val;
						}

						if ($garages) { $list_garages .=  $b_val.$garages.'</td>'; } else {
									    $list_garages .=  $b_val.'<i class="fa fa-times"></i>'.$a_val;
						}

						if ($is_featured == 'on') { $featured_list .=  '<td class="col-md-3 col-xs-3 is_exists"><i class="fa fa-check-square-o"></i>'.$a_val; } else {
													$featured_list .=  '<td class="col-md-3 col-xs-3"><i class="fa fa-times"></i>'.$a_val;
						}

						if (!empty($prop_gallery)) { $galleries .=  '<td class="col-md-3 col-xs-3 is_exists"><i class="fa fa-file-image-o"></i>'.$a_val; } else {
													 $galleries .=  '<td class="col-md-3 col-xs-3"><i class="fa fa-times"></i>'.$a_val;;
						}

						if (!empty($prop_plans)) { $list_plans .=  '<td class="col-md-3 col-xs-3 is_exists"><i class="fa fa-file-image-o"></i>'.$a_val; } else {
												   $list_plans .=  '<td class="col-md-3 col-xs-3"><i class="fa fa-times"></i>'.$a_val;
						}

						if (!empty($prop_video)) { $list_videos .=  '<td class="col-md-3 col-xs-3 is_exists"><i class="fa fa-video-camera"></i>'.$a_val; } else {
												   $list_videos .=  '<td class="col-md-3 col-xs-3"><i class="fa fa-times"></i>'.$a_val;
						}


					endwhile;

					/*HEAD*/
					$out_html .= '<thead>';
						$out_html .= '<tr>';
						$out_html .= $thead_html;
						$out_html .= '</tr>';
					$out_html .= '</thead>';

					/*BODY*/
					$out_html  .= '<tbody>';
						 $out_html  .= '<tr><td class="field-label col-md-3 col-xs-3">'.__('Country', 'zoner').'</td>';
							$out_html  .= $countries;
						$out_html  .= '</tr>';

						$out_html  .= '<tr><td class="field-label col-md-3 col-xs-3">'.__('State', 'zoner').'</td>';
							$out_html  .= $states;
						$out_html  .= '</tr>';

						$out_html  .= '<tr><td class="field-label col-md-3 col-xs-3">'.__('Address', 'zoner').'</td>';
							$out_html  .= $addresses;
						$out_html  .= '</tr>';

						$out_html  .= '<tr><td class="field-label col-md-3 col-xs-3">'.__('Town / City', 'zoner').'</td>';
							$out_html  .= $cities;
						$out_html  .= '</tr>';

						$out_html  .= '<tr><td class="field-label col-md-3 col-xs-3">'.__('District', 'zoner').'</td>';
							$out_html  .= $districts;
						$out_html  .= '</tr>';

						$out_html  .= '<tr><td class="field-label col-md-3 col-xs-3">'.__('Postcode / Zip', 'zoner').'</td>';
							$out_html  .= $zips;
						$out_html  .= '</tr>';

						$out_html  .= '<tr><td class="field-label col-md-3 col-xs-3">'.__('Google Map', 'zoner').'</td>';
							$out_html  .= $gg_links;
						$out_html  .= '</tr>';

						$out_html  .= '<tr><td class="field-label col-md-3 col-xs-3">'.__('Condition', 'zoner').'</td>';
							$out_html  .= $conditions;
						$out_html  .= '</tr>';

						$out_html  .= '<tr><td class="field-label col-md-3 col-xs-3">'.__('Payment', 'zoner').'</td>';
							$out_html  .= $payments;
						$out_html  .= '</tr>';

						$out_html  .= '<tr><td class="field-label col-md-3 col-xs-3">'.__('Area', 'zoner').'</td>';
							$out_html  .= $list_areas;
						$out_html  .= '</tr>';

						$out_html  .= '<tr><td class="field-label col-md-3 col-xs-3">'.__('Rooms', 'zoner').'</td>';
							$out_html  .= $list_rooms;
						$out_html  .= '</tr>';

						$out_html  .= '<tr><td class="field-label col-md-3 col-xs-3">'.__('Beds', 'zoner').'</td>';
							$out_html  .= $list_beds;
						$out_html  .= '</tr>';

						$out_html  .= '<tr><td class="field-label col-md-3 col-xs-3">'.__('Baths', 'zoner').'</td>';
							$out_html  .= $list_baths;
						$out_html  .= '</tr>';

						$out_html  .= '<tr><td class="field-label col-md-3 col-xs-3">'.__('Garages', 'zoner').'</td>';
							$out_html  .= $list_garages;
						$out_html  .= '</tr>';

						$out_html  .= '<tr><td class="field-label col-md-3 col-xs-3">'.__('Property Featured', 'zoner').'</td>';
							$out_html  .= $featured_list;
						$out_html  .= '</tr>';

						$out_html  .= '<tr><td class="field-label col-md-3 col-xs-3">'.__('Gallery', 'zoner').'</td>';
							$out_html  .= $galleries;
						$out_html  .= '</tr>';

						$out_html  .= '<tr><td class="field-label col-md-3 col-xs-3">'.__('Floor Plans', 'zoner').'</td>';
							$out_html  .= $list_plans;
						$out_html  .= '</tr>';

						$out_html  .= '<tr><td class="field-label col-md-3 col-xs-3">'.__('Video Presentation', 'zoner').'</td>';
							$out_html  .= $list_videos;
						$out_html  .= '</tr>';

						if (!empty($property_features)) {
							$out_html  .= '<tr><td colspan="4" class="row-headline">'.__('Property Features', 'zoner').'</td></tr>';

							foreach ($property_features as $features) {
								$out_html  .= '<tr><td class="field-label col-md-3 col-xs-3">'.$features->name.'</td>';

								if (!empty($exists_posts)) {
									foreach($exists_posts as $epost)
										if( has_term( $features->term_id, 'property_features', $epost) ) {
											$out_html  .= '<td class="is_exists"><i class="fa fa-check-circle"></i></td>';
										} else {
											$out_html  .= '<td><i class="fa fa-minus-circle"></i></td>';
										}
								}
								$out_html  .= '</tr>';
							}
						}

						if (!empty($exists_posts)) {
							$out_html  .= '<tr class="buttons"><td></td>';
								foreach($exists_posts as $epost) {
									$out_html  .= '<td align="center"><button data-propertyid="'.$epost.'" class="btn btn-default small remove-compare-property" type="button">'.__('Remove', 'zoner').'</button></td>';
								}
							$out_html  .= '</tr>';
						}

					$out_html  .= '</tbody>';


				$out_html .= '</table>';
			$out_html .= '</div>';
		} else {
			if ($compare_prop->found_posts == 1) {
				$out_html .= '<div class="alert alert-danger"><strong>'.__("Should be selected more than one property.", 'zoner').'</strong> '.__('Please add more property to compare list!', 'zoner').'</div>';
			} else {
				$out_html .= '<div class="alert alert-danger"><strong>'.__("Nothing was found on your request.", 'zoner').'</strong> '.__('Please add properties to compare list!', 'zoner').'</div>';
			}
		}
		$out_html .= '</div>';
			wp_reset_postdata();

   	   echo $out_html;

	}
}



if ( ! function_exists( 'zoner_get_property_grid_header' ) ) {
	function zoner_get_property_grid_header() {
		global $zoner_config, $zoner, $wp_query;

		$full_out = '';
		$sort = $zoner_config['property-default-orderby'];
		$search_count = 0;

		/*Get form parametr*/
		foreach ( $_GET as $key => $val ) {
			if ( 'sorting' === $key) {
				$sort = $val;
			}
		}
		$filter_pid  = $filter_zip = $filter_keyword = $filter_status = $filter_type = $filter_country = $filter_city = $price_value = '';
		$filter_city = $condition = $payment = $rooms = $beds = $baths = $garages = $filter_district = $filter_area = $filter_cat = '';

		$is_filtered = false;
		if (isset($_GET) && isset($_GET['filter_property']) && wp_verify_nonce($_GET['filter_property'], 'zoner_filter_property')) {
			$min_price = $max_price = 0;

			if (!empty($_GET['sb-zip'])) 		$filter_zip 	= $_GET['sb-zip'];
			if (!empty($_GET['sb-keyword'])) 	$filter_keyword = $_GET['sb-keyword'];
			if (!empty($_GET['sb-area'])) 		$filter_area 	= $_GET['sb-area'];
			if (!empty($_GET['sb-status'])) 	$filter_status 	= $_GET['sb-status'];
			if (!empty($_GET['sb-cat'])) 		$filter_cat 	= $_GET['sb-cat'];
			if (!empty($_GET['sb-type'])) 		$filter_type 	= $_GET['sb-type'];
			if (!empty($_GET['sb-country'])) 	$filter_country = $_GET['sb-country'];
			if (!empty($_GET['sb-city'])) 		$filter_city 	= $_GET['sb-city'];
			if (!empty($_GET['sb-price'])) 		$price 			= $_GET['sb-price'];

			/*Additional fileds*/
			if (!empty($_GET['sb-condition'])) 	$condition 	= $_GET['sb-condition'];
			if (!empty($_GET['sb-payment'])) 	$payment 	= $_GET['sb-payment'];
			if (!empty($_GET['sb-rooms'])) 		$rooms 		= $_GET['sb-rooms'];
			if (!empty($_GET['sb-beds'])) 		$beds 		= $_GET['sb-beds'];
			if (!empty($_GET['sb-baths'])) 		$baths		= $_GET['sb-baths'];
			if (!empty($_GET['sb-garages'])) 	$garages 	= $_GET['sb-garages'];
			if (!empty($_GET['sb-district']))	$filter_district  = $_GET['sb-district'];

			if (!empty($_GET['sb-features'])) 	$features 	= $_GET['sb-features'];

			$is_filtered = true;
		}

		$search_count = $wp_query->found_posts;

		$main_property_page_id = $zoner->zoner_get_page_id('page-property-archive');
		$perma_struct = get_option( 'permalink_structure' );
		$link = get_permalink($main_property_page_id);
		if ($perma_struct == '') {
			$link = get_post_type_archive_link('property');
		}

		if (is_tax('property_cat') || is_tax('property_tag') || is_tax('property_type') || is_tax('property_status') || is_tax('property_type') || is_tax('property_features')) {
			$value 		= get_query_var($wp_query->query_vars['taxonomy']);
			$curr_term 	= get_term_by('slug',$value,$wp_query->query_vars['taxonomy']);
			$main_property_page_title = '<header><h1>'.get_the_title($main_property_page_id) . ' - ' . $curr_term->name . '</h1></header>';
		} elseif(!empty($main_property_page_id)) {
			$main_property_page_title = '<header><h1>'.get_the_title($main_property_page_id).'</h1></header>';
		} else{
		    $main_property_page_title = '<header><h1>'.__('Properties', 'zoner').'</h1></header>';
		}

		$orderby = 'menu_order';


		$search_section = '<section id="search-filter" class="search-filter">';
			$search_section .= '<figure>';
				$search_section .= '<h3>';

					if (!empty($_GET['filter_property'])) {
						$search_section .= '<i class="fa fa-search"></i>';
						$search_section .= __('Search Results', 'zoner');
					} else {
						$search_section .= __('Results', 'zoner');
					}
				$search_section .= ':</h3>';
				$search_section .= '<span class="search-count">'.$search_count.'</span>';

				$search_section .= '<div class="sorting">';
					$search_section .= '<form id="form-sort" class="form-group form-sort" name="form-sort" action="" method="GET">';

					if ($is_filtered) {
						$search_section .= wp_nonce_field( 'zoner_filter_property', 'filter_property', false, false );
					}

					/*If WPML Parametr exist*/
					if (isset($_GET['lang']) && !empty($_GET['lang']))
						$search_section .= '<input type="hidden" name="lang" value="'.esc_attr($_GET['lang']).'" />';

					if (isset($_GET) && !empty($_GET['post_type'])) {
						$search_section .= '<input type="hidden" name="post_type" value="property"/>';
					}

					if (isset($_GET) && !empty($_GET['page_id'])) {
						$search_section .= '<input type="hidden" name="page_id" value="'. esc_attr($_GET['page_id']).'"/>';
					}

					if ($is_filtered) {

						if (!empty($filter_zip))		$search_section .= '<input type="hidden" name="sb-zip" value="'.$filter_zip.'"/>';
						if (!empty($filter_keyword))	$search_section .= '<input type="hidden" name="sb-keyword" value="'.$filter_keyword.'"/>';
						if (!empty($filter_status))		$search_section .= '<input type="hidden" name="sb-status" value="'.$filter_status.'"/>';
						if (!empty($filter_cat))		$search_section .= '<input type="hidden" name="sb-cat" value="'.$filter_cat.'"/>';
						if (!empty($filter_type))		$search_section .= '<input type="hidden" name="sb-type" value="'.$filter_type.'"/>';
						if (!empty($filter_country)) 	$search_section .= '<input type="hidden" name="sb-country" value="'.$filter_country.'"/>';
						if (!empty($filter_city))		$search_section .= '<input type="hidden" name="sb-city" value="'.$filter_city.'"/>';
						if (!empty($price))	{		    $search_section .= '<input type="hidden" name="sb-price" value="'.$price.'"/>';
									                    $search_section .= '<input type="hidden" name="sb-price-req" value="yes"/>';
						}

						/*Additional fileds*/
						if (!empty($condition))	$search_section .= '<input type="hidden" name="sb-condition" value="'.$condition.'"/>';
						if (!empty($payment))	$search_section .= '<input type="hidden" name="sb-payment" value="'.$payment.'"/>';
						if (!empty($rooms))		$search_section .= '<input type="hidden" name="sb-rooms" value="'.$rooms.'"/>';
						if (!empty($beds)) 		$search_section .= '<input type="hidden" name="sb-beds" value="'.$beds.'"/>';
						if (!empty($baths))		$search_section .= '<input type="hidden" name="sb-baths" value="'.$baths.'"/>';
						if (!empty($garages))	$search_section .= '<input type="hidden" name="sb-garages" value="'.$garages.'"/>';
						if (!empty($filter_district))	$search_section .= '<input type="hidden" name="sb-district" value="'.$filter_district.'"/>';
						if (!empty($filter_area))	$search_section .= '<input type="hidden" name="sb-area" value="'.$filter_area.'"/>';
						
						/*Custom Fields*/
						$search_section = apply_filters('zoner_add_hiddens_fields', $search_section);
						
						if (!empty($features)) {
							foreach ($features as $f) {
								$search_section .= '<input type="hidden" name="sb-features[]" value="'.$f.'" />';
							}
						}
					}



					$search_section .= '<select id="zoner-property-sort" name="sorting" class="zoner-property-sort">';
								$catalog_orderby = apply_filters( 'zoner_property_orderby',
									array(
										'menu_order' 	 => __( 'Sort By', 'zoner' ),
										'rating'     	 => __( 'Sort by Rating: low to high', 'zoner' ),
										'rating-desc'  	 => __( 'Sort by Rating: high to low', 'zoner' ),
									    //'featured'     	 => __( 'Sort by Featured: low to high', 'zoner' ),
									    //'featured-desc'  => __( 'Sort by Featured: high to low', 'zoner' ),
										'date'       	 => __( 'Sort by newness', 'zoner' ),
										'price'      	 => __( 'Sort by price: low to high', 'zoner' ),
										'price-desc' 	 => __( 'Sort by price: high to low', 'zoner' ),
										'rand' 		 	 => __( 'Sort by random', 'zoner' ),

									)
								);

								foreach ( $catalog_orderby as $id => $name )
								$search_section .= '<option data-value="'.esc_attr( $id ).'" value="' . esc_attr( $id ) . '" ' . selected( $sort, $id, false ) . '>' . esc_attr( $name ) . '</option>';

                        $search_section .= '</select>';

                    $search_section .= '</form><!-- /.form-group -->';
				$search_section .= '</div>';

			$search_section .= '</figure>';
		$search_section .= '</section>';

		$full_out  = $main_property_page_title;
		$full_out .= $search_section;

		echo $full_out;
	}
}

if ( ! function_exists( 'zoner_get_property_grid_items' ) ) {
	function zoner_get_property_grid_items() {
		global $zoner_config, $prefix, $wp_query, $posts;
		$section_class = array();
		$cnt 		   = 1;
		$is_close = true;

		$section_class[] = 'properties';
		if (isset($zoner_config['page-property-grid'])) {
			if ($zoner_config['page-property-grid'] == 3) {
			    $section_class[] = 'display-lines';
			} elseif($zoner_config['page-property-grid'] == 1) {
				$section_class[] = 'masonry';
			}
		}

		if ( have_posts() ) {
			if($zoner_config['page-property-grid'] == 1)
			   $section_class[] = 'masonry-loaded';

		?>

		<section id="properties" class="<?php echo implode(' ', $section_class); ?>">
			<div class="grid">
			<?php

				if (!isset($_GET['sorting']))
				$posts = zoner_featured_first($posts);


				while ( have_posts() ) : the_post();
					if (($cnt%3 == 1) && ($zoner_config['page-property-grid'] != 3)) {
						echo '<div class="row">';
						$is_close = false;
					}

					get_template_part( 'content', 'property' );

					if (($cnt%3 == 0) && ($zoner_config['page-property-grid'] != 3)) {
						echo '</div>';
						$is_close = true;
					}

					$cnt++;
				endwhile; // end of the loop.

				if (!$is_close && ($zoner_config['page-property-grid'] != 3)) echo '</div>';
		?>

			</div>
			<?php zoner_paging_nav(); ?>
		</section>

		<?php

		} else {

		?>
			<section id="properties" class="<?php echo implode(' ', $section_class); ?>">
				<div class="alert alert-danger"><strong><?php _e("Nothing was found on your request.", 'zoner'); ?></strong><?php _e('Try to change search parameters!', 'zoner'); ?></div>
			<section>

		<?php
		}
	}
}

if ( !function_exists( 'zoner_featured_first' ) ) {
	function zoner_featured_first ($posts_list){
		global $prefix;
			$first_part = array();
			$second_part = array();

			foreach ($posts_list as $key => $post) {
				$feature_value = get_post_meta( $post->ID, $prefix.'is_featured');
				if (isset($feature_value[0])) {
					if ($feature_value[0] == 'on')
						$first_part[] = $post;
					else
						$second_part[] = $post;
				}
				else
				$second_part[] = $post;
			}

		return array_merge($first_part, $second_part);
	}
}

if ( ! function_exists( 'zoner_get_property_condition' ) ) {
	function zoner_get_property_condition() {
		global $post, $prefix, $zoner;

		$condition  = $condition_name = '';
		$condition	= get_post_meta($post->ID, $prefix.'condition', true);
		$condition_name = $zoner->property->get_condition_name($condition);

		if (!empty($condition_name) && ($condition > 0)) {
			return '<figure class="ribbon">'.$condition_name.'</figure>';
		}
	}
}

if ( ! function_exists( 'zoner_string_limit_words' ) ) {
	function zoner_string_limit_words($string, $word_limit) {
		$content = '';
		if (empty($string)) return '';
		$words = explode(' ', $string, ($word_limit + 1));
		if(count($words) > $word_limit) array_pop($words);
		$content = implode(' ', $words);
		$content = strip_tags($content);
		$content = strip_shortcodes($content) . '...';

		$content = preg_replace('/\[.+\]/','',  $content);
		$content = apply_filters('the_content', $content);
		$content = str_replace(']]>', ']]&gt;', $content);

		return $content;
	}
}

if ( ! function_exists( 'zoner_get_post_share' ) ) {
	function zoner_get_post_share() {
		global $zoner_config, $post;
		$src = '';
		$src = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), false, '' );
		if (!empty($src)) {
			$src = $src[0];
		}
		$out = '';
		$out .= '<div class="social-icons">';
			$out .= '<a title="twitter"	href="https://twitter.com/share?url=' . get_the_permalink() . '" target="_blank"><i class="icon social_twitter"></i></a>';
			$out .= '<a title="Facebook"	href="http://www.facebook.com/sharer.php?u' . get_the_permalink() . '" target="_blank"><i class="icon social_facebook"></i></a>';
			$out .= '<a title="Pinterest"	href="//pinterest.com/pin/create/button/?url=' . get_the_permalink() . '&media=' . $src . '&description=' . get_the_title() . '" target="_blank"><i class="icon social_pinterest"></i></a>';
			$out .= '<a title="Google +"	href="https://plus.google.com/share?url=' . get_the_permalink() . '" onclick="javascript:window.open(this.href, \'\', \'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600\');return false;"><i class="icon social_googleplus"></i></a>';
		$out .= '</div><!-- .social-icons -->';
		return $out;
	}
}

if ( ! function_exists( 'zoner_get_property_grid_items_masonry' ) ) {
	function zoner_get_property_grid_items_masonry($echo = true) {
		global $post, $prefix, $zoner, $zoner_config;
		$gproperty = $prop_type_arr = array();
		$out = '';

		$gproperty  = $zoner->property->get_property($post->ID);

		$price 		= $gproperty->price;
		$rooms 		= $gproperty->rooms;
		$beds 		= $gproperty->beds;
		$baths 		= $gproperty->baths;
		$garages 	= $gproperty->garages;

		$address 	= $gproperty->address;
		$full_address = $gproperty->full_address;
		$city		= $gproperty->city;
		$district	= $gproperty->district;
		$zip		= $gproperty->zip;
		$area	    = $gproperty->area;
		$area_unit  = esc_attr($zoner_config['area-unit']);
		if ($gproperty->area_unit)
		$area_unit	= $gproperty->area_unit;

		$currency	= $gproperty->currency;
		$price_html	= $gproperty->price_html;
		$prop_types  	= $gproperty->property_types;
		$prop_status 	= $gproperty->property_status;
		$payment_rent = $gproperty->payment_rent;
		$payment_rent_name = $gproperty->payment_rent_name;

		$prop_type_html = $prop_status_html = array();

		$is_compare    = $zoner->compare->zoner_get_compare($post->ID);
		$is_bookmarked = $zoner->bookmark->zoner_get_bookmark($post->ID);

		if (!empty($prop_types)) {
			foreach ($prop_types as $prop_type)  {
				$attachment_id = $zoner->zoner_tax->get_zoner_term_meta($prop_type->term_id, 'thumbnail_id');
				$img_tax = wp_get_attachment_image_src($attachment_id, array(26,26));
				if (!empty($img_tax)) {
					$prop_type_html[]  = array('name' => $prop_type->name, 'icon' => '<img width="26" height="26" src="'.$img_tax[0].'" alt="" />');
				} else {
					$prop_type_html[]  ="";
				}
			}
		}

		if (!empty($prop_status)) {
			foreach ($prop_status as $status)  {
				$prop_status_html[] = $status->name;

			}
		}
		// data-scroll-reveal in inner
		$out .= '<div id="property-'.get_the_ID().'" class="property masonry">';
			$out .= '<div class="inner" data-scroll-reveal>';
				$out .= '<a href="'.get_the_permalink(get_the_ID()).'">';
					$out .= '<div class="property-image">';

						if (!empty($prop_status_html))
						$out .= '<figure class="tag status">'.implode(', ', $prop_status_html).'</figure>';

						if (!empty($prop_type_html)) {
							foreach ($prop_type_html as $type) {
                                if (!empty($type) && isset($type['icon']) && isset($type['name']))
								$out .= '<figure class="type" title="'.$type['name'].'">'.$type['icon'].'</figure>';
							}
						}

						$out .= zoner_get_property_condition();
	            $out .= '<div class="overlay">';
								$out .= '<div class="info">';
									$out .= $price_html;
									$out .= '<div class="actions">';
										$bookmark_classes = array();
										$bookmark_classes[] = 'bookmark';
										if( $is_bookmarked ) $bookmark_classes[] = 'bookmark-added';
										$out .= '<a href="#" class="' . implode(' ', $bookmark_classes) . '" data-propertyid="' . $post->ID . '"></a>';
										$compare_classes = array();
										$compare_classes[] = 'compare';
										if( $is_compare ) $compare_classes[] = 'compare-added';
										$out .= '<a href="#" class="' . implode(' ', $compare_classes) . '" data-propertyid="' . $post->ID . '"></a>';
									$out .= '</div><!-- .actions -->';
								$out .= '</div>';
	            $out .= '</div>';

                        if (has_post_thumbnail()) {
							$attachment_id 	  = get_post_thumbnail_id( $post->ID );
							$image_attributes = wp_get_attachment_image_src( $attachment_id, 'large');

							$out .= '<img class="img-responsive" src="'.$image_attributes[0].'" alt="" />';
						} else {
							$out .= '<img width="100%" class="img-responsive" data-src="holder.js/100px100p?auto=yes&text='.__('Property', 'zoner') .'" alt="" />';
						}
					$out .= '</div>';
				$out .= '</a>';

				$out .= '<aside>';
					$out .= '<header>';
						$out .= '<a href="'.get_the_permalink().'"><h3>'.get_the_title().'</h3></a>';
                        $out .= '<figure>'.$full_address.'</figure>';
                    $out .= '</header>';

					$limit_words = $zoner_config['page-property-excerpt-limit'];
					$out .= zoner_string_limit_words(get_the_content(), $limit_words);

					$out .= '<dl>';

					if ($area) {
						$out .= '<dt>'.__('Area', 'zoner').':</dt>';
						$out .= '<dd>'.esc_attr($area) . ' ' . $zoner->property->ret_area_units_by_id($area_unit). '</dd>';
					}

					if ($rooms) {
						$out .= '<dt>'.__('Rooms', 'zoner').':</dt>';
						$out .= '<dd>'.esc_attr($rooms).'</dd>';
					}

					if ($beds) {
						$out .= '<dt>'.__('Beds', 'zoner').':</dt>';
						$out .= '<dd>'.esc_attr($beds).'</dd>';
					}

					if ($baths) {
						$out .= '<dt>'.__('Baths', 'zoner').':</dt>';
						$out .= '<dd>'.esc_attr($baths).'</dd>';
					}

					if ($garages) {
						$out .= '<dt>'.__('Garages', 'zoner').':</dt>';
						$out .= '<dd>'.esc_attr($garages).'</dd>';
					}

					if ($payment_rent) {
						$out .= '<dt>'.__('Payment', 'zoner').':</dt>';
						$out .= '<dd>'.esc_attr($payment_rent_name).'</dd>';
					}
					
					/*Show Custom Fields*/
					$out = apply_filters('zoner_masonry_summary_fields', $out); 
					
                    $out .= '</dl>';

					$out .= '<a href="'.get_the_permalink().'" class="link-arrow">'. __('Read More', 'zoner').'</a>';
               $out .= '</aside>';
			$out .= '</div>';
		$out .= '</div><!-- /.property -->';

		if ($echo) { echo $out; } else { return $out; }
	}
}


if ( ! function_exists( 'zoner_get_property_grid_items_original' ) ) {
	function zoner_get_property_grid_items_original($echo = true, $columns = array('col-md-4', 'col-sm-4')) {
		global $post, $prefix, $zoner, $zoner_config;

		$gproperty = $prop_type_arr = array();
 	    $out = '';

		$gproperty  = $zoner->property->get_property($post->ID);

		$price 		= $gproperty->price;
		$price_html	= $gproperty->price_html;

		$rooms 		= $gproperty->rooms;
		$beds 		= $gproperty->beds;
		$baths 		= $gproperty->baths;
		$garages 	= $gproperty->garages;

		$address 	= $gproperty->address;
		$full_address 	= $gproperty->full_address;
		$city		= $gproperty->city;
		$district	= $gproperty->district;
		$zip		= $gproperty->zip;

		$area = $gproperty->area;
		$area_unit  = esc_attr($zoner_config['area-unit']);
		if ($gproperty->area_unit)
		$area_unit	= $gproperty->area_unit;

		$currency = $gproperty->currency;

		$prop_types  	= $gproperty->property_types;
		$prop_status 	= $gproperty->property_status;
		$prop_type_html = $prop_status_html = array();

		if (!empty($prop_types)) {
			foreach ($prop_types as $prop_type)  {

				$attachment_id = $zoner->zoner_tax->get_zoner_term_meta($prop_type->term_id, 'thumbnail_id');
				$img_tax = wp_get_attachment_image_src($attachment_id, array(26,26));
				if (!empty($img_tax)) {
					$prop_type_html[]  = array('name' => $prop_type->name, 'icon' => '<img width="26" height="26" src="'.$img_tax[0].'" alt="" />');
				} else {
					$prop_type_html[]  = "";
				}

			}
		}

		if (!empty($prop_status)) {
			foreach ($prop_status as $status)  {
				$prop_status_html[] = $status->name;

			}
		}

		$out = '<div class="'.implode(' ',  $columns).'">';
			$out .= '<div id="property-'.get_the_ID().'" class="property">';
				if (!empty($prop_status_html)) {
					$out .= '<figure class="tag status">'.implode(', ', $prop_status_html).'</figure>';
				}

				if (!empty($prop_type_html)) {
					foreach ($prop_type_html as $type) {
						if (!empty($type) && isset($type['icon']) && isset($type['name']))
                        $out .= '<figure class="type" title="'.$type['name'].'">'.$type['icon'].'</figure>';
					}
				}
				$out .= zoner_get_property_condition();

				$out .= '<div class="property-image">';
					$out .= '<a href="'.get_the_permalink($post->ID).'" rel="nofollow">';
						if (has_post_thumbnail()) {
							$attachment_id 	  = get_post_thumbnail_id( $post->ID );
							$image_attributes = wp_get_attachment_image_src( $attachment_id, 'zoner-footer-thumbnails');

							$out .= '<img class="img-responsive" src="'.$image_attributes[0].'" alt="" />';
						} else {
							$out .= '<img class="img-responsive" width="100%" data-src="holder.js/440x330?auto=yes&text='.__('Property', 'zoner') .'" alt="" />';
						}
					$out .= '</a>';
				$out .= '</div>';

				$out .= '<div class="overlay">';
					$out .= '<div class="info">';
						$out .= $price_html;
						$out .= '<a href="'.get_the_permalink($post->ID).'" rel="nofollow"><h3>'.get_the_title().'</h3></a>';
						$out .= '<figure>'.$full_address.'</figure>';
					$out .= '</div>';

					$out .= '<ul class="additional-info">';
						if ($area) 	$out .= '<li><header>'.__('Area', 'zoner')	.':</header><figure>'.esc_attr($area). ' ' . $zoner->property->ret_area_units_by_id($area_unit) .'</figure></li>';
						if ($beds) 	$out .= '<li><header>'.__('Beds', 'zoner')	.':</header><figure>'.esc_attr($beds).'</figure></li>';
						if ($baths) 	$out .= '<li><header>'.__('Baths', 'zoner').':</header><figure>'. esc_attr($baths).'</figure></li>';
						if ($garages) 	$out .= '<li><header>'.__('Garages', 'zoner').':</header><figure>'.esc_attr($garages).'</figure></li>';
					$out .= '</ul>';
				$out .= '</div>';
			$out .= '</div><!-- /.property -->';
		$out .= '</div>';

		if ($echo) { echo $out; } else { return $out; }
	}
}


if ( ! function_exists( 'zoner_get_property_grid_items_lines' ) ) {
	function zoner_get_property_grid_items_lines($echo = true) {
		global $post, $prefix, $zoner, $zoner_config;

		$gproperty = $prop_type_arr = array();
		$out = '';

		$gproperty  = $zoner->property->get_property($post->ID);

		$price 		= $gproperty->price;
		$price_html	= $gproperty->price_html;

		$rooms 		= $gproperty->rooms;
		$beds 		= $gproperty->beds;
		$baths 		= $gproperty->baths;
		$garages 	= $gproperty->garages;

		$address 	= $gproperty->address;
		$full_address 	= $gproperty->full_address;
		$city		= $gproperty->city;
		$district	= $gproperty->district;
		$zip		= $gproperty->zip;

		$area = $gproperty->area;
		$area_unit  = esc_attr($zoner_config['area-unit']);
		if ($gproperty->area_unit)
		$area_unit	= $gproperty->area_unit;

		$currency = $gproperty->currency;

		$prop_types  	= $gproperty->property_types;
		$prop_status 	= $gproperty->property_status;

		$payment_rent   = $gproperty->payment_rent;
		$payment_rent_name = $gproperty->payment_rent_name;

		$prop_type_html = $prop_status_html = array();


		$is_compare    = $zoner->compare->zoner_get_compare($post->ID);
		$is_bookmarked = $zoner->bookmark->zoner_get_bookmark($post->ID);

		if (!empty($prop_types)) {
			foreach ($prop_types as $prop_type)  {

				$attachment_id = $zoner->zoner_tax->get_zoner_term_meta($prop_type->term_id, 'thumbnail_id');
				$img_tax = wp_get_attachment_image_src($attachment_id, array(26,26));
				if (!empty($img_tax)) {
					$prop_type_html[]  = array('name' => $prop_type->name, 'icon' => '<img width="26" height="26" src="'.$img_tax[0].'" alt="" />');
				} else {
					$prop_type_html[]  = "";
				}

			}
		}

		if (!empty($prop_status)) {
			foreach ($prop_status as $status)  {
				$prop_status_html[] = $status->name;
			}
		}

		$out = '<div id="property-'.$post->ID.'" class="property">';
			if (!empty($prop_status_html))
			$out .= '<figure class="tag status">'.implode(', ', $prop_status_html).'</figure>';
			if (!empty($prop_type_html)) {
				foreach ($prop_type_html as $type) {
						if (!empty($type) && isset($type['icon']) && isset($type['name']))
						$out .= '<figure class="type" title="'.$type['name'].'">'.$type['icon'].'</figure>';
					}
				}

			$out .= '<div class="property-image">';
				$out .= zoner_get_property_condition();
				$out .= '<a href="'.get_the_permalink().'">';
					if (has_post_thumbnail()) {
						$attachment_id 	  = get_post_thumbnail_id( $post->ID );
						$image_attributes = wp_get_attachment_image_src( $attachment_id, 'zoner-footer-thumbnails');
						$out .= '<img class="img-responsive" src="'.$image_attributes[0].'" alt="" />';
					} else {
						$out .= '<img width="100%" class="img-responsive" data-src="holder.js/550x440?auto=yes&text='.__('Property', 'zoner') .'" alt="" />';
					}
				$out .= '</a>';
				$out .= '<div class="actions">';
					$bookmark_classes = array();
					$bookmark_classes[] = 'bookmark';
					if( $is_bookmarked ) $bookmark_classes[] = 'bookmark-added';
					$out .= '<a href="#" class="' . implode(' ', $bookmark_classes) . '" data-propertyid="' . $post->ID . '"></a>';
					$compare_classes = array();
					$compare_classes[] = 'compare';
					if( $is_compare ) $compare_classes[] = 'compare-added';
					$out .= '<a href="#" class="' . implode(' ', $compare_classes) . '" data-propertyid="' . $post->ID . '"></a>';
				$out .= '</div><!-- .actions -->';
			$out .= '</div>';

			$out .= '<div class="info">';
				$out .= '<header>';
					$out .= '<a href="'.get_the_permalink().'"><h3>'.get_the_title().'</h3></a>';
					$out .= '<figure>'.$full_address.'</figure>';
				$out .= '</header>';

				$out .= $price_html;
				$out .= '<aside>';
					$limit_words = $zoner_config['page-property-excerpt-limit'];
					$out .= zoner_string_limit_words(get_the_content(), $limit_words);

					$out .= '<dl>';

					if ($area) {
						$out .= '<dt>'.__('Area', 'zoner').':</dt>';
						$out .= '<dd>'.esc_attr($area) . ' ' . $zoner->property->ret_area_units_by_id($area_unit) .'</dd>';
					}

					if ($rooms) {
						$out .= '<dt>'.__('Rooms', 'zoner').':</dt>';
						$out .= '<dd>'.esc_attr($rooms).'</dd>';
					}

					if ($beds) {
						$out .= '<dt>'.__('Beds', 'zoner').':</dt>';
						$out .= '<dd>'.esc_attr($beds).'</dd>';
					}

					if ($baths) {
						$out .= '<dt>'.__('Baths', 'zoner').':</dt>';
						$out .= '<dd>'.esc_attr($baths).'</dd>';
					}

					if ($garages) {
						$out .= '<dt>'.__('Garages', 'zoner').':</dt>';
						$out .= '<dd>'.esc_attr($garages).'</dd>';
					}

					if ($payment_rent) {
						$out .= '<dt>'.__('Payment', 'zoner').':</dt>';
						$out .= '<dd>'.esc_attr($payment_rent_name).'</dd>';
					}
					
					/*Show Custom Fields*/
					$out = apply_filters('zoner_lines_summary_fields', $out); 

                    $out .= '</dl>';
				$out .= '</aside>';
				$out .= '<a href="'.get_the_permalink().'" class="link-arrow">'.__('Read More', 'zoner').'</a>';
			$out .= '</div>';
		$out .= '</div><!-- /.property -->';

		if ($echo) { echo $out; } else { return $out; }
	}
}




if ( ! function_exists( 'zoner_get_property_header' ) ) {
	function zoner_get_property_header() {
		global $zoner_config, $prefix, $post, $zoner;
		$gproperty = array();

		$gproperty  = $zoner->property->get_property($post->ID);
		$full_address 	= $gproperty->full_address;

		$is_choose_bookmark = $is_choose_compare =  'empty';
		$is_compare    = $zoner->compare->zoner_get_compare($post->ID);
		$is_bookmarked = $zoner->bookmark->zoner_get_bookmark($post->ID);

		if ($is_bookmarked > 0) $is_choose_bookmark = 'added';
		if ($is_compare    > 0) $is_choose_compare  = 'added';

	?>
		<header class="property-title">
			<h1><?php the_title(); ?></h1>

			<?php if (!empty($full_address)) { ?>
				<figure><?php echo $full_address; ?></figure>
			<?php } ?>

			<span class="actions">
				<?php if ( $zoner_config['property-social-links'] ): ?>
					<div class="share">
						<?php echo zoner_get_post_share(); ?>
					</div><!-- .share -->
				<?php endif; ?>
				<?php if ( is_user_logged_in() ) { ?>
					<a href="#" class="bookmark" data-bookmark-state="<?php echo $is_choose_bookmark; ?>" data-propertyid="<?php echo $post->ID; ?>">
						<span class="title-add"><?php _e('Add to bookmark', 'zoner'); ?></span>
					</a>
					<a href="#" class="compare" data-compare-state="<?php echo $is_choose_compare; ?>" data-propertyid="<?php echo $post->ID; ?>">
						<span class="title-add"><?php _e('Add to compare', 'zoner'); ?></span>
					</a>
				<?php } ?>
				
				<a href="#" class="print-page" data-propertyid="<?php echo $post->ID; ?>"><i class="fa fa-print" aria-hidden="true"></i></a>
			</span>
    </header>
	<?php

	}
}



if ( ! function_exists( 'zoner_get_gallery_property' ) ) {
	function zoner_get_gallery_property() {
		global $zoner_config, $prefix, $post;
		$prop_gallery = '';
		$prop_gallery = get_post_meta($post->ID, $prefix.'gallery', true);
		$size_thumb   = 'zoner-gallery-property';

		if (!isset($zoner_config['prop-single-crop']) ||
			 empty($zoner_config['prop-single-crop'])) $size_thumb = 'full';

		if (!empty($prop_gallery)) {
		?>

		<section id="property-gallery-<?php echo $post->ID; ?>" class="property-gallery">
			<div id="owl-carousel-<?php echo rand(0,1000); ?>" class="owl-carousel property-carousel">
				<?php
					foreach ($prop_gallery as $attachment_id => $url) {
						$alt = '';
						$thumbnail_image = wp_get_attachment_image_src( $attachment_id, $size_thumb);
						$alt = get_post_meta($attachment_id, '_wp_attachment_image_alt', true);
						if (!empty($thumbnail_image[0])){
				?>

						<div class="property-slide">
							<a href="<?php echo esc_url($url); ?>" class="image-popup">
								<div class="overlay"><h3><?php echo $alt;?></h3></div>
								<img alt="<?php echo $alt; ?>" src="<?php echo $thumbnail_image[0]; ?>">
							</a>
						</div><!-- /.property-slide -->
				 <?php
					  }
					}
				 ?>
			</div>
		</section>

		<?php
		}
	}
}

if ( ! function_exists( 'zoner_get_quick_summary' ) ) {
	function zoner_get_quick_summary() {
		global $zoner_config, $prefix, $post, $zoner;

		$gproperty = $prop_type_arr = array();
		$gproperty = $zoner->property->get_property($post->ID);
		$reference	= $gproperty->reference;
		$price 		= $gproperty->price;
		$rooms 		= $gproperty->rooms;
		$beds 		= $gproperty->beds;
		$baths 		= $gproperty->baths;
		$garages 	= $gproperty->garages;
		$address 	= $gproperty->address;
		$full_address 	= $gproperty->full_address;
		$city		= $gproperty->city;
		$district	= $gproperty->district;
		$zip		= $gproperty->zip;
		$allow_rating = $gproperty->allow_raiting;

		$area	= $gproperty->area;
		$area_unit  = esc_attr($zoner_config['area-unit']);
		if ($gproperty->area_unit)
		$area_unit	= $gproperty->area_unit;

		$currency	= $gproperty->currency;

		$price_html 	= $gproperty->price_html;
		$prop_types  	= $gproperty->property_types;
		$prop_statuses 	= $gproperty->property_status;

		$payment_rent = $gproperty->payment_rent;
		$payment_rent_name = $gproperty->payment_rent_name;


		$prop_type_html = $prop_status_html = array();

		if (!empty($prop_types)) {
			foreach ($prop_types as $prop_type)  {
				$prop_type_html[] = $prop_type->name;
			}
		}

		if (!empty($prop_statuses)) {
			foreach ($prop_statuses as $prop_status)  {
				$prop_status_html[] = $prop_status->name;
			}
		}

		$rating = 0;
		$rating = $gproperty->avg_rating;
		if ($rating < 0 ) $rating = 0;

	?>
		<section id="quick-summary" class="clearfix">
			<header><h2><?php _e('Quick Summary', 'zoner'); ?></h2></header>
				<dl>
					<?php if (!empty($reference)) { ?>
						<dt><?php _e('Property ID', 'zoner'); ?></dt>
						<dd><?php echo $reference; ?></dd>
					<?php } ?>

					<?php if (!empty($full_adddres)) { ?>
						<dt><?php _e('Location', 'zoner'); ?></dt>
						<dd><?php echo implode(', ', $full_address); ?></dd>
					<?php } ?>

					<?php if ($price_html != '') { ?>
						<dt><?php _e('Price', 'zoner'); ?></dt>
						<dd><?php echo $price_html; ?></dd>
					<?php } ?>

					<?php if ($payment_rent) { ?>
						<dt><?php _e('Payment', 'zoner'); ?>:</dt>
						<dd><?php echo esc_attr($payment_rent_name); ?></dd>
					<?php } ?>

					<?php if (!empty($prop_type_html)) { ?>
						<dt><?php _e('Type', 'zoner'); ?>:</dt>
						<dd><?php echo implode(', ', $prop_type_html); ?></dd>
					<?php } ?>

					<?php if (!empty($prop_status_html)) { ?>
						<dt><?php _e('Status', 'zoner'); ?>:</dt>
						<dd><?php echo implode(', ', $prop_status_html); ?></dd>
					<?php } ?>

					<?php if ($area) { ?>
						<dt><?php _e('Area', 'zoner'); ?>:</dt>
						<dd><?php echo esc_attr($area) . ' ' .$zoner->property->ret_area_units_by_id($area_unit); ?></dd>
					<?php } ?>

					<?php if ($rooms) { ?>
						<dt><?php _e('Rooms', 'zoner'); ?>:</dt>
						<dd><?php echo esc_attr($rooms); ?></dd>
					<?php } ?>

					<?php if ($beds) { ?>
						<dt><?php _e('Beds', 'zoner'); ?>:</dt>
						<dd><?php echo esc_attr($beds); ?></dd>
					<?php } ?>

					<?php if ($baths) { ?>
						<dt><?php _e('Baths', 'zoner'); ?>:</dt>
						<dd><?php echo esc_attr($baths); ?></dd>
					<?php } ?>

					<?php if ($garages) { ?>
						<dt><?php _e('Garages', 'zoner'); ?>:</dt>
						<dd><?php echo esc_attr($garages); ?></dd>
					<?php } ?>

					<?php if($allow_rating == 'on') { ?>
						<dt><?php _e('Overall Rating', 'zoner'); ?>:</dt>
						<dd><div class="rating rating-overall" data-score="<?php echo esc_attr($rating); ?>"></div></dd>
					<?php } ?>
				</dl>
				<?php edit_post_link( '<i title="' . __("Edit", 'zoner') . '" class="fa fa-pencil-square-o"></i><span class="edit-link-text">'.__("Edit", 'zoner') .'</span>', '', '' ); ?>
		</section><!-- /#quick-summary -->
	<?php
	}
}

if ( ! function_exists( 'zoner_get_property_description' ) ) {
	function zoner_get_property_description	() {
		global $zoner_config, $prefix, $post;
		?>
			<section id="description">
				<header><h2><?php _e('Property Description','zoner'); ?></h2></header>
				<?php the_content(); ?>
			</section><!-- /#description -->
		<?php
	}
}

if ( ! function_exists( 'zoner_get_icon_for_attachment' ) ) {
	function zoner_get_icon_for_attachment ($attachment_id) {
		$file_icon  = '<i class="fa fa-file-o"></i>';

		$attachment = get_post( $attachment_id );
		$file_ext   = wp_check_filetype(wp_get_attachment_url($attachment_id));
		$ext 		= strtolower($file_ext['ext']);

		if ($ext) {
			if (($ext == 'doc') || ($ext == 'docx')) {
				$file_icon  = '<i class="fa fa-file-word-o"></i>';
			} elseif (($ext == 'xls') || ($ext == 'xlsx')) {
				$file_icon  = '<i class="fa fa-file-excel-o"></i>';
			} elseif (($ext == 'ppt') || ($ext == 'pptx')) {
				$file_icon  = '<i class="fa fa-file-powerpoint-o"></i>';
			} elseif (($ext == 'zip') || ($ext == 'rar') || ($ext == 'tar')) {
				$file_icon  = '<i class="fa fa-file-archive-o"></i>';
			} elseif ($ext == 'txt') {
				$file_icon  = '<i class="fa fa-file-text"></i>';
			} elseif ($ext == 'pdf') {
				$file_icon  = '<i class="fa fa-file-pdf-o"></i>';
			}
		}

		return $file_icon;
	}
}


if ( ! function_exists( 'zoner_get_property_files' ) ) {
	function zoner_get_property_files() {
		global $zoner_config, $prefix, $post;

		$property_files = get_post_meta($post->ID, $prefix.'files', true);

		if (!empty($property_files)) {

		?>
			<section id="property_files">
				<header><h2><?php _e('Property Files', 'zoner'); ?></h2></header>
					<ul class="list-unstyled property_files-list">
						<?php
							foreach ($property_files as $key => $file) {
								$file_icon  = '<i class="fa fa-file-o"></i>';

								$attachment = get_post( $key );
								$alt 		= get_post_meta( $key, '_wp_attachment_image_alt', true );
								$caption 	= $attachment->post_excerpt;
								$description = $attachment->post_content;
								$title 		= $attachment->post_title;
								$file_icon 	= zoner_get_icon_for_attachment($key);

								echo '<li><a title="'.$title.'" href="'.$file.'" target="_blank">'.$file_icon.'</a></li>';
							}
						?>
					</ul>
			</section><!-- /#property_features -->
		<?php
		}
	}
}

if ( ! function_exists( 'zoner_get_property_features' ) ) {
	function zoner_get_property_features() {
		global $zoner_config, $prefix, $post;
			$prop_features = array();
			$prop_features = wp_get_post_terms($post->ID, 'property_features', array('orderby' => 'name', 'hide_empty' => 0) );
			if (!empty($prop_features)) {

		?>
			<section id="property_features">
				<header><h2><?php _e('Property Features', 'zoner'); ?></h2></header>
					<ul class="list-unstyled property_features-list">
						<?php
							foreach($prop_features as $feature) {
							echo '<li>'.$feature->name.'</li>';
							}
						?>
					</ul>
			</section><!-- /#property_features -->
		<?php
			}
	}
}

if ( ! function_exists( 'zoner_get_property_floor_plans' ) ) {
	function zoner_get_property_floor_plans() {
		global $zoner_config, $prefix, $post;
		$floor_plans = array();
		$floor_plans = get_post_meta($post->ID, $prefix.'plans', true);

		if (!empty($floor_plans)) {

		?>
			<section id="floor-plans">
				<div class="floor-plans">
					<header><h2><?php _e('Floor Plans', 'zoner'); ?></h2></header>
						<?php
							foreach($floor_plans as $key => $plan) {
								$thumb_img = wp_get_attachment_image_src( $key, 'zoner-floor-plans' );
								echo '<a href="'.esc_url($plan).'" class="image-popup"><img alt="" src="'.$thumb_img[0].'"></a>';
							}

						?>


				</div>
            </section><!-- /#floor-plans -->
		<?php

		}
	}
}


if ( ! function_exists( 'zoner_get_property_map' ) ) {
    function zoner_get_property_map() {
        global $zoner_config, $prefix, $post, $zoner;
        $lat = get_post_meta($post->ID, $prefix.'lat', true);
        $lng = get_post_meta($post->ID, $prefix.'lng', true);
        if (!empty($lat) && !empty($lng)) {
            $gg_marker 		= $zoner_config['prop-ggmaps-marker'];
            $property_type  = wp_get_post_terms( $post->ID, 'property_type', array("fields" => "all") );
            $term_id 		= $label_type_icon = '';
            if (!empty($property_type)) {
                foreach ($property_type as $type) {
                    $term_id = $type->term_id;
                }
                $attachment_label_id =  $zoner->zoner_tax->get_zoner_term_meta($term_id, 'thumbnail_id');
                if (!empty($attachment_label_id)) {
                    $label_type_icon = wp_get_attachment_image_src( $attachment_label_id, 'full');
                    $label_type_icon = esc_url($label_type_icon[0]);
                } else {
                    $label_type_icon = get_template_directory_uri() . '/includes/theme/assets/img/empty.png';
                }
            }
            ?>

            <section id="property-map">
                <header><h2><?php _e('Map', 'zoner'); ?></h2></header>
                <div class="property-detail-map-wrapper">
                    <div id="property-detail-map" class="property-detail-map" ></div>
                </div>
            </section><!-- /#property-map -->

            <script type="text/javascript">
                jQuery(document).ready(function() {
                    initMap(
                        "<?php echo esc_js($lat); ?>",
                        "<?php echo esc_js($lng); ?>",
                        "<?php echo esc_js($label_type_icon); ?>",
                        "<?php echo esc_js($gg_marker['url']); ?>");
                });
            </script>

        <?php
        }
    }
}

if ( ! function_exists( 'zoner_get_multiitems' ) ) {
	function zoner_get_multiitems() {
		global $prefix, $zoner, $zoner_config, $inc_theme_url, $post;
		$out_ = $outproperties = null;
		
		if ( isset($_REQUEST) && isset($_REQUEST['action']) && ($_REQUEST['action'] == 'zoner_get_multiitems'))  {

			$same_lat          = esc_attr($_REQUEST['sameLatitude']);
			$same_lng     	   = esc_attr($_REQUEST['sameLongitude']);
			$isAgentAgencyPage = filter_var($_REQUEST['isAgentAgencyPage'], FILTER_VALIDATE_BOOLEAN);
			
			$args = array();
			$args = array(
				'post_type' 		=> 'property',
				'post_status' 		=> 'publish',
				'posts_per_page'	=> -1,
				'meta_query' 		=> array(
				'relation' 			=> 'OR',
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
					if ($isAgentAgencyPage) {
						$outproperties .= zoner_get_property_grid_items_original(false, array('col-md-6', 'col-sm-12'));	
					} else {
						$outproperties .= zoner_get_property_grid_items_original(false);	
					}
				}
			}

			$out_ .= '<div class="modal-wrapper">';
				$out_ .= '<h2>'.__('Multiple properties in one location', 'zoner').'</h2>';
				$out_ .= '<div class="modal-body row"><ul class="items list-unstyled">'.$outproperties.'</ul></div>';
				$out_ .= '<div class="modal-close"><img src="'.$inc_theme_url.'/assets/img/close-btn.png"></div>';
			$out_ .= '</div>';
			$out_ .= '<div class="modal-background fade_in"></div>';

			wp_reset_query();
			echo $out_;
		}

		die('');
	}
}

if ( ! function_exists( 'zoner_get_agency_map' ) ) {
    function zoner_get_agency_map($cache_obj_id) {
        global $inc_theme_url, $zoner_config, $prefix, $post, $zoner;
        $authors = $all_users_from_agency = $authors_uniq = array();
        $authors[] = $post->post_author;
        $all_users_from_agency = $zoner->invites->zoner_get_all_agents_from_agency($post->ID);
        if (!empty($all_users_from_agency)) {
            foreach ($all_users_from_agency as $author) {
                $authors[] = $author->user_id;
            }
        }
        $authors_uniq = array_unique($authors);
        $args = array(
            'fields'   => 'ids',
            'post_type' 	=> 'property',
            'post_status'	=> 'publish',
            'posts_per_page' => -1,
            'author'			=> implode(',', $authors_uniq)
        );
        $prop_from_agency = new WP_Query( $args );
		$lat = $lng  = null;
        foreach((array)$prop_from_agency->posts as $key=>$id_prop){
            $gproperty  = $zoner->property->get_property($id_prop);
            $full_address 	= $gproperty->full_address;
            $price 	= $gproperty->price;
            $currency 	= $gproperty->currency;
            $area_unit 	= $gproperty->area_unit;
        	$price_format 	= $gproperty->price_format;
			$payment_rent 	= $gproperty->payment_rent;
            $price_html 	= $zoner->currency->get_zoner_property_price($price, $currency, $price_format, $payment_rent, $area_unit, false);
            $lat 		= $gproperty->lat;
            $lng 		= $gproperty->lng;
            $prop_types = $gproperty->property_types;
            $prop_type_out = array();
            if (!empty($prop_types)) {
                foreach ($prop_types as $prop_type)  {
                    $attachment_id = $zoner->zoner_tax->get_zoner_term_meta($prop_type->term_id, 'thumbnail_id');
                    $img_tax 	   = wp_get_attachment_image_src($attachment_id, 'full');
                    $prop_type_out = $img_tax[0];
                    break;
                }
            }
            if (empty($prop_type_out)) {
                $prop_type_out = get_template_directory_uri() . '/includes/theme/assets/img/empty.png';
            }
            $img_url = $img_holder = '';
            if (has_post_thumbnail($id_prop)) {
                $attachment_id 	  = get_post_thumbnail_id( $id_prop );
                $image_attributes = wp_get_attachment_image_src( $attachment_id, 'zoner-footer-thumbnails');
                $img_url = $image_attributes[0];
            } else {
                $img_holder = 'holder.js/555x445?auto=yes&text='.__('Property', 'zoner');
            }
            $array_of_locations[] = array (
                'title' 	=> esc_js(get_the_title($id_prop)),
                'address' 	=> wp_kses_data($full_address),
				'price' 	=> esc_js(wp_kses_data($price_html)),
                'lat' 		=> esc_js($lat),
                'lng' 		=> esc_js($lng),
                'link' 		=> esc_js(get_permalink($id_prop)),
                'featured-image' => esc_js($img_url),
                'holder-image'   => esc_js($img_holder),
                'type' 		=> esc_url($prop_type_out)
            );
        }
        ?>
        <?php if ($lat) { ?>
		<hr class="thick">
		<div id="map" class="agency-map"></div>

        <script>
            jQuery(document).ready(function(){
                createHomepageGoogleMap(<?php echo $lat;?>,<?php echo $lng;?>,<?php echo json_encode($array_of_locations); ?>, ZonerGlobal.source_path);
            });
        </script>
		<?php }
    }
}

if ( ! function_exists( 'zoner_get_agent_map' ) ) {
    function zoner_get_agent_map($cache_obj_id) {
        global $inc_theme_url, $zoner_config, $prefix, $post, $zoner;
        $args = array(
            'fields'   => 'ids',
            'post_type' 	=> 'property',
            'post_status'	=> 'publish',
            'posts_per_page' => -1,
            'author'			=> $cache_obj_id
        );
        $prop_from_agency = new WP_Query( $args );
        foreach((array)$prop_from_agency->posts as $key=>$id_prop){
            $gproperty  = $zoner->property->get_property($id_prop);
            $full_address 	= $gproperty->full_address;
            $price 	= $gproperty->price;
            $currency 	= $gproperty->currency;
            $area_unit 	= $gproperty->area_unit;
        	$price_format 	= $gproperty->price_format;
			$payment_rent 	= $gproperty->payment_rent;
            $price_html 	= $zoner->currency->get_zoner_property_price($price, $currency, $price_format, $payment_rent, $area_unit, false);
            $lat 		= $gproperty->lat;
            $lng 		= $gproperty->lng;
            $prop_types = $gproperty->property_types;

            $prop_type_out = array();
            if (!empty($prop_types)) {
                foreach ($prop_types as $prop_type)  {
                    $attachment_id = $zoner->zoner_tax->get_zoner_term_meta($prop_type->term_id, 'thumbnail_id');
                    $img_tax 	   = wp_get_attachment_image_src($attachment_id, 'full');
                    $prop_type_out = $img_tax[0];
                    break;
                }
            }
            if (empty($prop_type_out)) {
                $prop_type_out = get_template_directory_uri() . '/includes/theme/assets/img/empty.png';
            }
            $img_url = $img_holder = '';
            if (has_post_thumbnail($id_prop)) {
                $attachment_id 	  = get_post_thumbnail_id( $id_prop );
                $image_attributes = wp_get_attachment_image_src( $attachment_id, 'zoner-footer-thumbnails');
                $img_url = $image_attributes[0];
            } else {
                $img_holder = 'holder.js/555x445?auto=yes&text='.__('Property', 'zoner');
            }
            $array_of_locations[] = array (
                'title' 	=> esc_js(get_the_title($id_prop)),
                'address' 	=> wp_kses_data($full_address),
				'price' 	=> esc_js(wp_kses_data($price_html)),
                'lat' 		=> esc_js($lat),
                'lng' 		=> esc_js($lng),
                'link' 		=> esc_js(get_permalink($id_prop)),
                'featured-image' => esc_js($img_url),
                'holder-image'   => esc_js($img_holder),
                'type' 		=> esc_url($prop_type_out)
            );
        }
        ?>
        <?php if (!empty($lat)){?>
		
		<hr class="thick">
		<div id="map" class="agency-map"></div>

        <script>
            jQuery(document).ready(function(){
                createHomepageGoogleMap(<?php echo $lat;?>,<?php echo $lng;?>,<?php echo json_encode($array_of_locations); ?>, ZonerGlobal.source_path);
            });
        </script>
            <hr class="thick">
    <?php }?>

        <?php
        ?>



    <?php
    }
}
if ( ! function_exists( 'zoner_get_video_presents' ) ) {
	function zoner_get_video_presents() {
		global $zoner_config, $prefix, $post, $wp_embed;
		$links_video = get_post_meta($post->ID, $prefix.'videos', true);

		$out_videos = '';
		if (!empty($links_video)) {
			foreach($links_video as $link) {
				$val = $embed = '';
				if (is_array($link)) $val = $link[$prefix.'link_video'];
					$embed = $wp_embed->run_shortcode('[embed width="554"]'.$val.'[/embed]');
					if (!empty($embed)) $out_videos .= '<li>'.$embed.'</li>';
				}
		}
		if (!empty($out_videos))  {
			$out_videos = '<section id="video-presentation" class="video-presentation"><header><h2>'.__('Video Presentation', 'zoner').'</h2></header><ul class="list-unstyled">' . $out_videos . '</ul></section><!-- /#video-presentation -->';
			echo $out_videos;
		}
	}
}


if ( ! function_exists( 'zoner_get_rating_form' ) ) {
	function zoner_get_rating_form() {
		global $zoner_config, $prefix, $post, $zoner;
		$rating = 0;
		$rating = get_post_meta($post->ID, $prefix.'avg_rating', true);
		if ($rating < 0) $rating = 0;

		?>
			<section id="property-rating">
				<header><h2><?php _e('Rating', 'zoner'); ?></h2></header>
				<div class="clearfix">
					<aside>
						<header><?php _e('Your Rating', 'zoner'); ?></header>
						<div class="rating rating-user">
							<div class="inner"></div>
						</div>
					</aside>
					<figure>
						<header><?php _e('Overall Rating', 'zoner'); ?></header>
						<div class="rating rating-overall" data-score="<?php echo $rating; ?>"></div>
					</figure>
				</div>
				<div class="rating-form">
					<header><?php _e('Thank you! Please describe your rating', 'zoner'); ?></header>
					<form role="form" id="form-rating" name="form-rating" method="post" action="" class="clearfix">
						<?php wp_nonce_field( 'zoner_add_property_rating', 'add_property_rating', false, true ); ?>
						<input type="hidden" id="form-rating-score" name="form-rating-score" value="0"/>
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label for="form-rating-name"><?php _e('Your Name', 'zoner');?><em>*</em></label>
									<input type="text" class="form-control" id="form-rating-name" name="form-rating-name" required>
								 </div><!-- /.form-group -->
							 </div><!-- /.col-md-6 -->
							<div class="col-md-6">
								<div class="form-group">
									<label for="form-rating-email"><?php _e('Your Email', 'zoner'); ?><em>*</em></label>
									<input type="email" class="form-control" id="form-rating-email" name="form-rating-email" required>
								</div><!-- /.form-group -->
							</div><!-- /.col-md-6 -->
						</div><!-- /.row -->
						<div class="row">
							<div class="col-md-12">
								<div class="form-group">
									<label for="form-rating-message"><?php _e('Your Message', 'zoner'); ?><em>*</em></label>
									<textarea class="form-control" id="form-rating-message" rows="6" name="form-rating-message" required></textarea>
								</div><!-- /.form-group -->
							</div><!-- /.col-md-12 -->
						</div><!-- /.row -->
						<div class="form-group">
							<button type="submit" class="btn pull-right btn-default" id="form-rating-submit"><?php _e('Submit', 'zoner'); ?></button>
						</div><!-- /.form-group -->
						<div id="form-rating-status"></div>
					</form><!-- /#form-contact -->
				</div><!-- /.rating-form -->
			</section><!-- /#property-rating -->
		<?php

	}
}


if ( ! function_exists( 'set_property_comment_with_rating' ) ) {
	function set_property_comment_with_rating() {
		global $zoner_config, $prefix, $wp_query, $zoner, $wpdb;
		if (isset($_POST['add_property_rating']) && wp_verify_nonce($_POST['add_property_rating'], 'zoner_add_property_rating')) {
			$time    = current_time('mysql');
			$post_id = $wp_query->post->ID;
			$user_id = $comment_author = $name = null;

			$comment_approved = 0;

			$comment_author       = ( isset($_POST['form-rating-name']) )    ? trim(strip_tags($_POST['form-rating-name'])) : null;
			$comment_author_email = ( isset($_POST['form-rating-email']) )   ? trim($_POST['form-rating-email']) : null;
			$comment_author_url   = ( isset($_POST['url']) )     ? trim($_POST['url']) : null;
			$comment_content      = ( isset($_POST['form-rating-message']) ) ? trim($_POST['form-rating-message']) : null;
			$score = (int) $_POST['form-rating-score'];

			$user = null;
			$user = wp_get_current_user();
			if ( $user->exists() ) {
				$user->display_name	  = zoner_get_user_name($user);
				$user_id 			  = $user->ID;

				$comment_author       = esc_sql(zoner_get_user_name($user));
				$comment_author_email = esc_sql($user->user_email);
				$comment_author_url   = esc_sql($user->user_url);
				$comment_approved = 1;
			}

			$data = array(
				'comment_post_ID' 		=> $post_id,
				'comment_author'  		=> $comment_author,
				'comment_author_email' 	=> $comment_author_email,
				'comment_content' 		=> $comment_content,
				'comment_author_url' 	=> $comment_author_url,
				'comment_parent' 		=> 0,
				'user_id' 				=> $user_id,
				'comment_author_IP' 	=> isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : null,
				'comment_date' 			=> $time,
				'comment_approved' 		=> $comment_approved,
				'comment_type'          => (( isset($_POST['type']) ) ? trim($_POST['type']) : null)
			);

			$comment_id = wp_new_comment($data);
			$comment    = get_comment($comment_id);
			do_action('set_comment_cookies', $comment, $user);

			add_comment_meta($comment_id, $prefix.'rating',  $score );
			update_post_meta($post_id, $prefix.'avg_rating', $zoner->ratings->zoner_calculate_rating_by_property());


			$location = empty($_POST['redirect_to']) ? get_comment_link($comment_id) : $_POST['redirect_to'] . '#comment-' . $comment_id;
			$location = apply_filters('comment_post_redirect', $location, $comment);

			wp_safe_redirect( $location );
		}
	}
}


if ( ! function_exists( 'zoner_get_contact_agent' ) ) {
	function zoner_get_contact_agent() {
		global $zoner_config, $prefix, $post, $zoner;

		$curr_user 			= get_user_by('id', $post->post_author);
		$all_meta_for_user  = get_user_meta( $curr_user->ID );
		$avatar 	 		= zoner_get_profile_avartar($curr_user->ID);
		$author_link 		= get_author_posts_url( $curr_user->ID);

		if (isset($all_meta_for_user['description']))
		$description = current($all_meta_for_user['description']);

		$is_form 		= isset($zoner_config['property-agent-form']) && !empty($zoner_config['property-agent-form']) ? $zoner_config['property-agent-form'] : false;
		$is_converstion = isset($zoner_config['property-agent-conversation']) && !empty($zoner_config['property-agent-conversation']) ? $zoner_config['property-agent-conversation'] : false;

		$mob = $tel = $skype = '';

		if (isset($all_meta_for_user[$prefix.'mob']))
			$mob = current($all_meta_for_user[$prefix.'mob']);
		if (isset($all_meta_for_user[$prefix.'tel']))
			$tel = current($all_meta_for_user[$prefix.'tel']);
		if (isset($all_meta_for_user[$prefix.'skype']))
			$skype = current($all_meta_for_user[$prefix.'skype']);
		?>
        <section id="contact-agent">
			<header><h2><?php _e('Contact Agent', 'zoner'); ?></h2></header>
			<div class="row">
				<section class="agent-form">
					<div class="col-md-7 col-sm-12">
						<aside id="<?php echo $curr_user->ID; ?>" class="agent-info clearfix">
							<figure><a href="<?php echo $author_link; ?>"><?php echo $avatar; ?></a></figure>
							<div class="agent-contact-info">
								<h3><?php echo zoner_get_user_name($curr_user); ?></h3>
								<?php if (!empty($description)) { ?>
									<p><?php echo $description; ?></p>
								<?php } ?>
								<dl>
									<?php if (!empty($tel)) { ?>
										<dt><?php _e('Phone', 'zoner'); ?>:</dt>
										<dd><?php echo $tel; ?></dd>
									<?php } ?>

									<?php if (!empty($mob)) { ?>
										<dt><?php _e('Mobile', 'zoner'); ?>:</dt>
										<dd><?php echo $mob; ?></dd>
									<?php } ?>

									<?php if (!empty($curr_user->user_email) && (is_user_logged_in())) { ?>
										<dt><?php _e('Email', 'zoner'); ?>:</dt>
										<dd><a href="mailto:<?php echo $curr_user->user_email; ?>"><?php echo $curr_user->user_email; ?></a></dd>
									<?php } ?>

									<?php if (!empty($skype)) { ?>
										<dt><?php _e('Skype', 'zoner'); ?>:</dt>
										<dd><a href="skype:<?php echo $skype; ?>?call"><?php echo $skype; ?></a></dd>
									<?php } ?>
								</dl>
								<hr>
								<a href="<?php echo $author_link; ?>" class="link-arrow"><?php _e('Full Profile', 'zoner'); ?></a>
							</div>
						</aside><!-- /.agent-info -->
					</div><!-- /.col-md-7 -->
					<div class="col-md-5 col-sm-12">
						<?php if ($is_form) { ?>
						<?php
							$args = array();
							$args = array (
										'title' => '',
										'id'	=> 'form-contact-agent',
										'name'	=> 'form-contact-agent',
										'sumbit_button_title' => __('Send a Message', 'zoner'),
										'email'	=> $curr_user->user_email,
										'agent_name' 	=> zoner_get_user_name($curr_user),
										'class_columns' => array('col-md-12'),
										'send_from_page' => get_the_ID()
								);
								zoner_get_form_send_email($args);
						?>
						<?php } ?>

						<?php if (is_user_logged_in() && $is_converstion) { ?>
							<div class="chat-btn">
								<button id="chat-wnd" data-label="<?php _e('Start conversation', 'zoner');?>" class="btn pull-right btn-default" type="submit"><?php _e('Start conversation', 'zoner');?></button>
								<?php zoner_get_property_chat_wnd($post->post_author); ?>
							</div>
						<?php } ?>
					</div>
				</section>
			</div>
		</section>
		<hr class="thick">
		<?php
	}
}
if ( ! function_exists( 'zoner_get_user_name' ) ) {
	function zoner_get_user_name($user) {
        if (!empty($user)) {
 	        if (!empty($user->first_name) || !empty($user->last_name)) {
 	            $display_name = sprintf('%1s %2s', $user->first_name, $user->last_name);
 	        } else {
 	            $display_name = $user->display_name;
 	        }
 	        return $display_name;
        }
	}
}
if ( ! function_exists( 'zoner_get_single_similar_properties' ) ) {
	function zoner_get_single_similar_properties() {
		global $zoner_config, $prefix, $post, $zoner, $wp_query;
		$per_page = apply_filters('zoner_similar_properties_count', 3);

		$show_by_type = $show_by_status = $show_by_city = $show_by_cat = $show_by_country = $show_by_state = false;

		$tax_query = $meta_query = $type = $status = $cat = $city = array();
		$allow_rating = get_post_meta($post->ID, $prefix.'allow_raiting', true);

		$show_by_type 		= $zoner_config['show-by-type'];
		$show_by_status 	= $zoner_config['show-by-status'];
		$show_by_city 		= $zoner_config['show-by-city'];
		$show_by_cat 		= $zoner_config['show-by-cat'];
		$show_by_country	= $zoner_config['show-by-country'];
		$show_by_state		= $zoner_config['show-by-state'];

		if ($show_by_type || $show_by_status || $show_by_city || $show_by_cat || $show_by_country || $show_by_state) {

			$tax_query ['relation'] = 'AND';
			$meta_query['relation'] = 'AND';

			if ($show_by_country) {
				$country = null;
				$country = get_post_meta($post->ID, $prefix.'country', true);

				if ($country)
					$meta_query[] = array(
								'key' 	  => $prefix .'country',
								'value'   => $country,
								'compare' => '='
							);
			}

			if ($show_by_state) {
				$state = null;
				$state = get_post_meta($post->ID, $prefix.'state', true);

				if ($state)
					$meta_query[] = array(
								'key' 	  => $prefix .'state',
								'value'	  => $state,
								'compare' => '='
							);
			}


			if ($show_by_type) {
				$prop_types = wp_get_post_terms($post->ID, 'property_type',   array("fields" => "ids") );
				if (!empty($prop_types))
					foreach ($prop_types as $val)
						$type[] = $val;

				if (!empty($type))
					$tax_query[] = array(
								'taxonomy' => 'property_type',
								'field' => 'id',
								'terms' => $type
							);
			}

			if ($show_by_status) {
				$prop_status = wp_get_post_terms($post->ID, 'property_status', array("fields" => "ids") );
				if (!empty($prop_status))
					foreach ($prop_status as $val)
						$status[] = $val;

				if (!empty($status))
					$tax_query[] = array(
							'taxonomy' => 'property_status',
							'field' => 'id',
							'terms' => $status
						);
			}

			if ($show_by_city) {
				$prop_city  = wp_get_post_terms($post->ID, 'property_city',   array("fields" => "ids") );

				if (!empty($prop_city))
				foreach ($prop_city as $val)
					$city[] = $val;

				if (!empty($city))
					$tax_query[] = array(
							'taxonomy' => 'property_city',
							'field' => 'id',
							'terms' => $city
						);
			}

			if ($show_by_cat) {
				$prop_cat = wp_get_post_terms($post->ID, 'property_cat',    array("fields" => "ids") );
				if (!empty($prop_cat))
					foreach ($prop_cat as $val)
						$cat[] = $val;

				if (!empty($cat))
					$tax_query[] = array(
							'taxonomy' => 'property_cat',
							'field' => 'id',
							'terms' => $cat
						);
			}

			$args = array( 	'post_type' 		=> 'property',
							'post_status' 		=> 'publish',
							'posts_per_page' 	=> $per_page,
							'orderby'	    	=> 'rand',
							'tax_query'			=> $tax_query,
							'meta_query'		=> $meta_query,
							'post__not_in' 		=> array($post->ID)
						);

			$similar_post = new WP_Query( $args );


			if ($similar_post->have_posts() ) { ?>
				<section id="similar-properties">
					<header><h2 class="no-border"><?php _e('Similar Properties', 'zoner'); ?></h2></header>
					<div class="row">
						<?php
							while ( $similar_post->have_posts() ) : $similar_post->the_post();
								zoner_get_property_grid_items_original(true, array('col-md-4', 'col-sm-6'));
							endwhile;
						?>

					</div>
				</section>

				<?php if ($allow_rating == 'on') { ?>
					<hr class="thick">
				<?php }
			}

			wp_reset_postdata();
		}
	}
}

if ( ! function_exists( 'zoner_get_agency_line_info' ) ) {
	function zoner_get_agency_line_info($is_full = false) {
		global $zoner_config, $prefix, $post;

		$address = get_post_meta($post->ID, $prefix . 'agency_address', true);
		$email 	 = get_post_meta($post->ID, $prefix . 'agency_email', true);

		$facebook 	 = esc_url(get_post_meta($post->ID, $prefix . 'agency_facebookurl', true));
		$twitter 	 = esc_url(get_post_meta($post->ID, $prefix . 'agency_twitterurl', true));
		$goole_plus  = esc_url(get_post_meta($post->ID, $prefix . 'agency_googleplusurl', true));
		$linkedin 	 = esc_url(get_post_meta($post->ID, $prefix . 'agency_linkedinurl', true));
		$pinterest   = esc_url(get_post_meta($post->ID, $prefix . 'agency_pinteresturl', true));
		$instagramm  = esc_url(get_post_meta($post->ID, $prefix . 'agency_instagramurl', true));
		$ggmap_url   = esc_url(get_post_meta($post->ID, $prefix . 'agency_googlemapurl', true));

		$tel		 = esc_attr(get_post_meta($post->ID, $prefix . 'agency_tel', true));
		$mobile 	 = esc_attr(get_post_meta($post->ID, $prefix . 'agency_mob', true));
		$skype 		 = esc_attr(get_post_meta($post->ID, $prefix . 'agency_skype', true));

		$sfi 		 = esc_url(get_post_meta($post->ID, $prefix  . 'agency_line_img', true));


		?>
		<div id="agency-<?php the_id();?>" class="agency">


			<?php if (!empty($sfi) || !is_single()) { ?>
				<a href="<?php the_permalink();?>" class="agency-image"><img alt="" src="<?php echo esc_url($sfi); ?>"></a>
			<?php } else { ?>

				<?php if (has_post_thumbnail()) { ?>
						<?php
							$attachment_id 	  = get_post_thumbnail_id( $post->ID );
							$image_attributes = wp_get_attachment_image_src( $attachment_id, 'full');
						?>
						<a href="<?php the_permalink();?>" class="agency-image"><img alt="" src="<?php echo $image_attributes[0]; ?>"></a>

				<?php } ?>
			<?php } ?>


            <div class="wrapper">
				<header><a href="<?php the_permalink();?>"><h2><?php the_title();?></h2></a></header>
					<dl>
					<?php if (!empty($tel)) { ?>
						<dt><?php _e('Phone', 'zoner'); ?>:</dt>
						<dd><?php echo $tel; ?></dd>
					<?php } ?>

					<?php if (!empty($mobile)) { ?>
						<dt><?php _e('Mobile', 'zoner'); ?>:</dt>
						<dd><?php echo $mobile; ?></dd>
					<?php } ?>

					<?php if (!empty($email) && (is_user_logged_in())) { ?>
						<dt><?php _e('Email', 'zoner'); ?>:</dt>
						<dd><a href="mailto:<?php echo $email; ?>"><?php echo $email; ?></a></dd>
					<?php }	 ?>

					<?php if (!empty($skype)) { ?>
						<dt><?php _e('Skype', 'zoner'); ?>:</dt>
						<dd><a href="skype:<?php echo $skype; ?>?call"><?php echo $skype; ?></a></dd>
					<?php } ?>
				</dl>
				<address>
					<h3><?php _e('Address', 'zoner'); ?></h3>
					<strong><?php the_title(); ?></strong>
					<?php echo wpautop($address); ?>
				</address>
			</div>
		</div><!-- /.agency -->
		<?php
	}
}

if ( ! function_exists( 'zoner_get_agency_header' ) ) {
	function zoner_get_agency_header() {
		global $zoner_config, $prefix, $post;

	?>
		<header><h1><?php the_title(); ?></h1></header>
	<?php
	}
}

if ( ! function_exists( 'zoner_get_agency_info' ) ) {
	function zoner_get_agency_info() {
		global $zoner_config, $prefix, $post;

		$address = get_post_meta($post->ID, $prefix . 'agency_address', true);
		$email 	 = get_post_meta($post->ID, $prefix . 'agency_email', true);

		$facebook 	 = esc_url(get_post_meta($post->ID, $prefix . 'agency_facebookurl', true));
		$twitter 	 = esc_url(get_post_meta($post->ID, $prefix . 'agency_twitterurl', true));
		$goole_plus  = esc_url(get_post_meta($post->ID, $prefix . 'agency_googleplusurl', true));
		$linkedin 	 = esc_url(get_post_meta($post->ID, $prefix . 'agency_linkedinurl', true));
		$pinterest   = esc_url(get_post_meta($post->ID, $prefix  . 'agency_pinteresturl', true));
		$instagramm  = esc_url(get_post_meta($post->ID, $prefix . 'agency_instagramurl', true));
		$ggmap_url   = esc_url(get_post_meta($post->ID, $prefix . 'agency_googlemapurl', true));

		$tel		 = esc_attr(get_post_meta($post->ID, $prefix . 'agency_tel', true));
		$mobile 	 = esc_attr(get_post_meta($post->ID, $prefix . 'agency_mob', true));
		$skype 		 = esc_attr(get_post_meta($post->ID, $prefix . 'agency_skype', true));

		$sfi 		 = esc_url(get_post_meta($post->ID, $prefix . 'agency_line_img', true));

	?>
		<section id="agent-detail" class="agency-detail">
			<div class="row">
				<div class="col-md-3 col-sm-3">
					<?php if (has_post_thumbnail()) { ?>
						<?php
							$attachment_id 	  = get_post_thumbnail_id( $post->ID );
							$image_attributes = wp_get_attachment_image_src( $attachment_id, 'full');
						?>
						<figure><img width="100%" src="<?php echo esc_url($image_attributes[0]); ?>" alt="" class="agency-image" /></figure>
					<?php } else { ?>
						<figure><img width="100%" data-src="holder.js/220x220?auto=yes&text=<?php _e('Agency', 'zoner'); ?>" alt="" class="agency-image" /></figure>
					<?php } ?>
				</div>

				<div class="col-md-5 col-sm-5">
					<h3><?php _e('Contact Info', 'zoner'); ?></h3>
					<address>

						<a href="<?php echo $ggmap_url; ?>" class="show-on-map" target="_blank">
							<i class="fa fa-map-marker"></i>
							<figure><?php _e('Map','zoner'); ?></figure>
						</a>

						<strong><?php the_title(); ?></strong>
						<br />
						<?php echo nl2br($address); ?>
					</address>

					<dl>
						<?php if (!empty($tel)) { ?>
							<dt><?php _e('Phone', 'zoner'); ?>:</dt>
							<dd><?php echo $tel; ?></dd>
						<?php } ?>

						<?php if (!empty($mobile)) { ?>
							<dt><?php _e('Mobile', 'zoner'); ?>:</dt>
							<dd><?php echo $mobile; ?></dd>
						<?php } ?>

						<?php if (!empty($email) && (is_user_logged_in())) { ?>
							<dt><?php _e('Email', 'zoner'); ?>:</dt>
							<dd><a href="mailto:<?php echo $email; ?>"><?php echo $email; ?></a></dd>
						<?php }	 ?>

						<?php if (!empty($skype)) { ?>
							<dt><?php _e('Skype', 'zoner'); ?>:</dt>
							<dd><a href="skype:<?php echo $skype; ?>?call"><?php echo $skype; ?></a></dd>
						<?php } ?>
					</dl>
				</div>
                <div class="col-md-4 col-sm-4">
					<h3><?php _e('Shortly About Us', 'zoner'); ?></h3>
					<?php the_content(); ?>

					<?php
						if (!empty($facebook) ||
							!empty($twitter)  ||
							!empty($goole_plus) ||
							!empty($linkedin) ||
							!empty($pinterest) ||
							!empty($instagramm)  ) {
					?>
					<div class="social">
						<h3><?php _e('Social Profiles', 'zoner'); ?></h3>
						<div class="agent-social">
							<?php if (!empty($facebook)) 	{ ?>  <a href="<?php echo $facebook; ?>" target="_blank"	class="fa fa-facebook btn btn-grey-dark"></a> <?php } ?>
							<?php if (!empty($twitter)) 	{ ?> 	<a href="<?php echo $twitter; ?>" target="_blank"	class="fa fa-twitter btn btn-grey-dark"></a> <?php } ?>
							<?php if (!empty($goole_plus))	{ ?> 	<a href="<?php echo $goole_plus; ?>" target="_blank" class="fa fa-google-plus btn btn-grey-dark"></a> <?php } ?>
							<?php if (!empty($linkedin)) 	{ ?>	<a href="<?php echo $linkedin; ?>" target="_blank" 	class="fa fa-linkedin btn btn-grey-dark"></a> <?php } ?>
							<?php if (!empty($pinterest)) 	{ ?>	<a href="<?php echo $pinterest; ?>" target="_blank" class="fa fa-pinterest btn btn-grey-dark"></a> <?php } ?>
							<?php if (!empty($instagramm)) 	{ ?> 	<a href="<?php echo $instagramm; ?>" target="_blank" class="fa fa-instagram btn btn-grey-dark"></a> <?php } ?>
						</div>
					</div>
					<?php } ?>
				</div>
                <div class="col-md-12 col-sm-12">
                        <?php zoner_get_agency_map($post->ID); ?>
                </div>
			</div>
		</section>
		<hr class="thick"/>
	<?php
	}
}


if ( ! function_exists( 'zoner_get_agency_properties' ) ) {
	function zoner_get_agency_properties() {
		global $zoner_config, $prefix, $post, $zoner;
			   $prop_id = $count_property = 0;
			   $is_closed = true;

		$authors = $all_users_from_agency = $authors_uniq = array();
		$authors[] = $post->post_author;

		$all_users_from_agency = $zoner->invites->zoner_get_all_agents_from_agency($post->ID);


		if (!empty($all_users_from_agency)) {
			foreach ($all_users_from_agency as $author) {
				$authors[] = $author->user_id;
			}
		}
		$authors_uniq = array_unique($authors);


			$args = array(
					   'post_type' 		=> 'property',
					   'post_status'	=> 'publish',
					   'posts_per_page' => -1,
					   'orderby'	    => 'DATE',
					   'order'			=> 'ASC',
					   'author'			=> implode(',', $authors_uniq)

					  );

			$prop_from_agency = new WP_Query( $args );
			$count_property = $prop_from_agency->found_posts;

			if ( $prop_from_agency->have_posts() ) {
				 $cnt = 1;

		?>
			<section id="agent-properties" class="agent-properties">
				<header><h3><?php printf( '%s (%s)', __('Our Properties', 'zoner'), $count_property ); ?></h3></header>
				<div class="layout-expandable" data-layoutrow="<?php echo $count_property; ?>">
					<?php
						while ( $prop_from_agency->have_posts() ) : $prop_from_agency->the_post();

							if ($cnt%3 == 1) {
								echo '<div class="row">';
								$is_closed = false;
							}
								zoner_get_property_grid_items_original();

							if ($cnt%3 == 0) {
								echo '</div>';
								$is_closed = true;
							}

							$cnt++;
						endwhile;

						if (!$is_closed) echo '</div>';
					?>
				</div>
				<?php if ($count_property >= 7) { ?>
					<div class="center">
						<span class="show-all"><?php _e('Show All Properties', 'zoner'); ?></span>
					</div>
				<?php } ?>
			</section>
			<hr class="thick"/>
		<?php

			}
			wp_reset_postdata();

		?>

	<?php
	}
}


if ( ! function_exists( 'zoner_get_agency_agents' ) ) {
	function zoner_get_agency_agents() {
		global $zoner_config, $prefix, $post, $zoner;

		$authors_uniq = $all_users_from_agency = $authors = array();
		$all_users_from_agency = $zoner->invites->zoner_get_all_agents_from_agency($post->ID);

		$authors[] = $post->post_author;

		if (!empty($all_users_from_agency)) {
			foreach ($all_users_from_agency as $author) {
				$authors[] = $author->user_id;
			}
		}
		$authors_uniq = array_filter(array_unique($authors));


		if (!empty($authors_uniq)) {
	?>
		<section id="agents-listing" class="agents-listing">
			<header><h3><?php _e('Our Agents', 'zoner'); ?></h3></header>

	<?php
		$is_closed = true;
		$cnt = 1;
		foreach ($authors_uniq as $author) {
			$curr_user 		   = get_userdata($author);

			$all_meta_for_user = get_user_meta ($author);
			$avatar = zoner_get_profile_avartar($author);

			$count_property = 0;
			$mob = $phone = $skype = '';

			if (isset($all_meta_for_user[$prefix.'tel']))
			$mob   = current($all_meta_for_user[$prefix.'tel']);

			if (isset($all_meta_for_user[$prefix.'mob']))
			$phone = current($all_meta_for_user[$prefix.'mob']);

			if (isset($all_meta_for_user[$prefix.'skype']))
			$skype = current($all_meta_for_user[$prefix.'skype']);

			$args = array();
			$args = array(
					   'post_type' 		=> 'property',
					   'post_status'	=> 'publish',
					   'posts_per_page' => -1,
					   'orderby'	    => 'DATE',
					   'order'			=> 'ASC',
					   'author'			=> $author,
					  );

			$prop_from_agent= new WP_Query( $args );
			$count_property = $prop_from_agent->found_posts;


	?>
			<?php
				if  ($cnt%2 == 1) {
					echo '<div class="row">';
					$is_closed = false;
				}
			?>

				<div class="col-md-12 col-lg-6">
					<div id="agent-<?php echo $author; ?>" class="agent">
						<a href="<?php echo get_author_posts_url( $author ); ?>" class="agent-image"><?php echo $avatar; ?></a>
						<div class="wrapper">
							<header><a href="<?php echo get_author_posts_url($author); ?>"><h2><?php echo zoner_get_user_name($curr_user); ?></h2></a></header>
							<aside><?php printf( '%s %s', $count_property, __('Properties', 'zoner') ); ?></aside>
							<dl>
								<?php if (!empty($phone)) { ?>
									<dt><?php _e('Phone', 'zoner'); ?>:</dt>
									<dd><?php echo $phone; ?></dd>
								<?php } ?>

								<?php if (!empty($mob)) { ?>
									<dt><?php _e('Mobile', 'zoner'); ?>:</dt>
									<dd><?php echo $mob; ?></dd>
								<?php } ?>

								<?php if (!empty($curr_user->user_email) && (is_user_logged_in())) { ?>
									<dt><?php _e('Email', 'zoner'); ?>:</dt>
									<dd><a href="mailto:<?php echo $curr_user->user_email; ?>"><?php echo $curr_user->user_email; ?></a></dd>
								<?php } ?>

								<?php if (!empty($skype)) { ?>
									<dt><?php _e('Skype', 'zoner'); ?>:</dt>
									<dd><a href="skype:<?php echo $skype; ?>?call"><?php echo $skype; ?></a></dd>
								<?php } ?>
							</dl>
						</div>
					</div><!-- /.agent -->
				 </div><!-- /.col-md-12 -->
	<?php
			if ($cnt % 2 == 0) {
				echo '</div>';
				$is_closed = true;
			}

			$cnt++;
		}

		if (!$is_closed) echo '</div>';

	?>

		</section>
		<hr class="thick"/>
	<?php
		}

	}
}

if ( ! function_exists( 'zoner_get_agency_additional_fields' ) ) {
	function zoner_get_agency_additional_fields() {
		global $zoner_config, $prefix, $post;
			   $author_id = $post->post_author;
			   $user = get_user_by( 'id', $author_id);

			   $agency_email = get_post_meta($post->ID, $prefix . 'agency_email', true);;
			   $display_name = zoner_get_user_name($user);


		?>

		<section id="additional-agency-information" class="additional-agency-information">
			<div class="row">
				<div class="col-md-12">
					<?php

						$args = array();
						$args = array(
							'title' => __('Send Us a Message', 'zoner'),
							'id'	=> 'form-contact-agency',
							'name'	=> 'form-contact-agency',
							'email'	=> $agency_email,
							'agent_name' => $display_name
						);
						zoner_get_form_send_email($args);
					?>
				</div>
			</div>
		</section>


		<?php
	}
}

if ( ! function_exists( 'zoner_nav_parent_class' ) ) {
	function zoner_nav_parent_class( $classes, $item ) {
		global $wpdb, $zoner, $zoner_config, $prefix;
		$cpt_name = array('');
		if ( in_array(get_post_type(), $cpt_name) && ! is_admin() ) {

			$classes = str_replace( 'current_page_parent', '', $classes );
			$page    = get_page_by_title( $item->title, OBJECT, 'page' );
			if (!empty($page->post_name)) {
				if($page->post_name === get_post_type())  $classes[] = 'current_page_parent';
			}
		}

		$property_loop_page = 0;
		$property_loop_page = $zoner->zoner_get_page_id('page-property-archive');
		if(is_post_type_archive( 'property' ) || (is_page($property_loop_page) && $property_loop_page != 0)) {
			if ($property_loop_page != 0) {
				if ($item->title == get_the_title($property_loop_page)) {
					$classes[] = "current-menu-item";
				}
			}
		}

		return $classes;
	}
}

if ( ! function_exists( 'zoner_set_excerpt_length' ) ) {
	function zoner_set_excerpt_length( $length ) {
		return 75;
	}
}

/*Gallery shortcode customization*/
if ( ! function_exists( 'zoner_gallery_shortcode' ) ) {
	function zoner_gallery_shortcode($atts) {
		$out_gallery = '';
		global $post;
		if ( ! empty( $atts['ids'] ) ) {
			if ( empty( $atts['orderby'] ) )
				$atts['orderby'] = 'post__in';
				$atts['include'] = $atts['ids'];
		}

		extract(shortcode_atts(array(
			'orderby' 		=> 'menu_order ASC, ID ASC',
			'include' 		=> '',
			'id'			=> $post->ID,
			'itemtag' 		=> 'dl',
			'icontag' 		=> 'dt',
			'captiontag' 	=> 'dd',
			'columns' 		=> 3,
			'size' 			=> '',
			'link' 			=> 'file'
		), $atts));


		$args = array(
			'post_type' => 'attachment',
			'post_status' => 'inherit',
			'post_mime_type' => 'image',
			'orderby' => $orderby
		);

		if ( !empty($include) )
			$args['include'] = $include;
		else {
			$args['post_parent'] = $id;
			$args['numberposts'] = -1;
		}


		if ($columns == 1) {
			$col = 6;
			$col_divider = 2;
		} else if ($columns == 2) {
			$col = 6;
			$col_divider = 2;
		} else if ($columns == 3) {
			$col = 4;
			$col_divider = 3;
		} else if ($columns == 4) {
			$col = 3;
			$col_divider = 4;
		} else if ($columns == 5) {
			$col = 3;
			$col_divider = 4;
		} else if ($columns == 6) {
			$col = 2;
			$col_divider = 6;
		} else if ($columns == 7) {
			$col = 2;
			$col_divider = 6;
		} else if ($columns == 8) {
			$col = 2;
			$col_divider = 6;
		} else if ($columns == 9) {
			$col = 1;
			$col_divider = 12;
		} else {
			$col = 4;
			$col_divider = 3;
		}

		$cnt = 1;
		$is_close = false;

		$images = get_posts($args);
		if (!empty($images)) {
		$elem_class = array();
		$elem_class[]  = 'zoner-gallery-shortcode';

		$out_gallery = '<div id="post-gallery-'.$id.'" class="'.implode(' ', $elem_class).'">';

			foreach ( $images as $image ) {

				if ($cnt%$col_divider == 1) {
					$out_gallery .= '<div class="row">';
					$is_close = false;
				}


				$caption = $image->post_excerpt;
				$description = $image->post_content;

				if($description == '') $description = $image->post_title;

				$image_alt = get_post_meta($image->ID,'_wp_attachment_image_alt', true);
				$image_url = wp_get_attachment_image_src($image->ID, 'zoner-footer-thumbnails');

				$image_url_full = wp_get_attachment_image_src($image->ID, 'full');

				$out_gallery .= '<div class="col-md-'.$col.'">';
					$out_gallery .= '<a href="'.esc_url($image_url_full[0]).'" class="thumbnail">';
						$out_gallery .= '<img class="img-responsive" src="'.esc_url($image_url[0]).'" alt="'.$image_alt.'">';
					$out_gallery .= '</a>';
				$out_gallery .= '</div>';



				if ($cnt%$col_divider == 0) {
					$out_gallery .= '</div>';
					$is_close = true;
					$cnt = 0;
				}

				$cnt++;

			}

			if (!$is_close) $out_gallery .= '</div>';

		$out_gallery .= '</div>';
		return $out_gallery;
		}
	}
}


/*Post password protected*/
if ( ! function_exists( 'zoner_password_protect_form' ) ) {
	function zoner_password_protect_form() {
		global $post;
		$out = '';
		$label = 'pwbox-'.( empty( $post->ID ) ? rand() : $post->ID );

		$out .= '<form role="form" class="protected-form" action="' . esc_url( site_url( 'wp-login.php?action=postpass', 'login_post' ) ) . '" method="post">';

			$out .= '<div class="panel panel-default">';
				$out .= '<div class="panel-heading">' . __('This is password protected post', 'zoner') . '</div>';
				$out .= '<div class="panel-body">'  . __("This content is password protected. To view it please enter your password below:", 'zoner' ) . '</div>';
				$out .= '<div class="panel-body">';
					$out .= '<input name="post_password" id="'. $label .'" type="password" size="20" maxlength="20" placeholder="'.__('Password', 'zoner').'"/>';
				$out .= '</div>';

				$out .= '<div class="form-group clearfix">';
					$out .= '<div class="col-md-12">';
						$out .= '<input type="submit" name="Submit" class="btn pull-right btn-default" value="' . esc_attr__( "Submit", 'zoner' ) . '" />';
					$out .= '</div>';
				$out .= '</div>';
			$out .= '</div>';

		$out .= '</form>';

		return $out;
	}
}


if ( ! function_exists( 'zoner_post_chat' ) ) {
	function zoner_post_chat($content = null) {
		global $post;
		$format = null;
		if (isset($post)) $format = get_post_format( $post->ID );
		$cnt = 0;

		if ($format == 'chat') {
			if (($post->post_type == 'post') && ($format == 'chat')) {
					remove_filter ('the_content',  'wpautop');
					$chatoutput = "<dl class=\"chat\">\n";
					$split = preg_split("/(\r?\n)+|(<br\s*\/?>\s*)+/", $content);
						foreach($split as $haystack) {
							if (strpos($haystack, ":")) {
								$string 	= explode(":", trim($haystack), 2);
								$who 		= strip_tags(trim($string[0]));
								$what 		= strip_tags(trim($string[1]));
								$chatoutput = $chatoutput . "<dt><i class='fa fa-weixin'></i><span class='chat-author'><strong>$who:</strong></span></dt><dd>$what</dd>\n";
							}
							else {
								$chatoutput = $chatoutput . $haystack . "\n";
							}
							$cnt++;

							if (!is_single()) {
								if ($cnt > 2) break;
							}
						}
						$content = $chatoutput . "</dl>\n";
						return $content;
			}
		} else {
			return $content;
		}
	}
}

if ( ! function_exists( 'zoner_add_google_analytics' ) ) {
	function zoner_add_google_analytics() {
		global $zoner_config, $prefix;

		if (!empty($zoner_config['tracking-code'])) {
		?>

		<script type="text/javascript">
			var _gaq = _gaq || [];
			_gaq.push(['_setAccount', '<?php echo esc_js($zoner_config['tracking-code']); ?>']);
			_gaq.push(['_trackPageview']);
			(function() {
				var ga = document.createElement('script');
					ga.type = 'text/javascript';
					ga.async = true;
					ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
				var s = document.getElementsByTagName('script')[0];
					s.parentNode.insertBefore(ga, s);
			})();
		</script>

		<?php
		}
	}
}

if ( ! function_exists( 'zoner_get_delay_interval' ) ) {
	function zoner_get_delay_interval($interval = 0) {
		$time_class = '';
		if ($interval > 0) {
			$time_class = 'after '.$interval.'s';
		}
		return $time_class;
	}
}

if ( ! function_exists( 'zoner_add_favicon' ) ) {
	function zoner_add_favicon() {
		global $zoner_config, $prefix;

		if( !empty($zoner_config['favicon'])) 				echo '<link rel="shortcut icon" href="' .  	esc_url($zoner_config['favicon']['url'])  . '"/>' . "\n";
		if( !empty($zoner_config['favicon-iphone'])) 		echo '<link rel="apple-touch-icon" href="'. esc_url($zoner_config['favicon-iphone']['url']) .'"> '. "\n";
		if( !empty($zoner_config['favicon-iphone-retina'])) 	echo '<link rel="apple-touch-icon" sizes="114x114" 	href="'.  esc_url($zoner_config['favicon-iphone-retina']['url']) .' "> '. "\n";
		if( !empty($zoner_config['favicon-ipad'])) 			echo '<link rel="apple-touch-icon" sizes="72x72" 	href="'. esc_url($zoner_config['favicon-ipad']['url']) .'"> '. "\n";
		if( !empty($zoner_config['favicon-ipad-retina']))	echo '<link rel="apple-touch-icon" sizes="144x144" 	href="'. esc_url($zoner_config['favicon-ipad-retina']['url'])  .'"> '. "\n";

	}
}

if ( ! function_exists( 'zoner_img_caption' ) ) {
	function zoner_img_caption( $empty_string, $attributes, $content ){
		extract(shortcode_atts(array(
			'id' 		=> '',
			'align' 	=> 'alignnone',
			'width' 	=> '',
			'caption' 	=> ''
		), $attributes));

		if ( empty($caption) ) return $content;
		if ($id ) $id = 'id="' . esc_attr($id) . '" ';
		return '<div ' . $id . 'class="wp-caption ' . esc_attr($align) . '" style="width:'.$width.'px;">' . do_shortcode( $content ) . '<p class="wp-caption-text">' . $caption . '</p></div>';
	}
}



if ( ! function_exists( 'zoner_curPageURL' ) ) {
	function zoner_curPageURL() {
		$pageURL = 'http';
		if (isset($_SERVER["HTTPS"])) {$pageURL .= "s";}
			$pageURL .= "://";
		if ($_SERVER["SERVER_PORT"] != "80") {
			$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
		} else {
			$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
		}
	 return $pageURL;
	}
}

if ( ! function_exists( 'zoner_setPropViews' ) ) {
	function zoner_setPropViews() {
		global $post, $prefix;
		$count = 0;
		$count = get_post_meta($post->ID, $prefix . 'views', true);

		$count++;
		update_post_meta($post->ID, $prefix.'views', $count);
	}
}

if ( ! function_exists( 'zoner_add_query_var' ) ) {
	function zoner_add_query_var($public_query_vars) {
		$public_query_vars[] = 'profile-page';
		$public_query_vars[] = 'edit-property';
		$public_query_vars[] = 'add-property';
		$public_query_vars[] = 'edit-agency';
		$public_query_vars[] = 'add-agency';
		$public_query_vars[] = 'invitehash';

		$public_query_vars[] = 'created_user';

		return apply_filters('zoner_query_vars', $public_query_vars);
	}
}

if ( ! function_exists( 'zoner_add_rewrite_rules' ) ) {
	function zoner_add_rewrite_rules() {
		 add_rewrite_tag ('%add-property%', '([^/]*)/?');
		 add_rewrite_rule('^add-property/([^/]*)/?', 'index.php?add-property=$matches[1]', 'top' );

		 add_rewrite_tag ('%add-agency%', '([^/]*)/?');
		 add_rewrite_rule('^add-agency/([^/]*)/?', 'index.php?add-agency=$matches[1]', 'top' );

		 add_rewrite_tag ('%edit-property%', '([^/]*)/?');
		 add_rewrite_rule('^edit-property/([^/]*)/?', 'index.php?edit-property=$matches[1]', 'top' );

		 add_rewrite_tag ('%edit-agency%', '([^/]*)/?');
		 add_rewrite_rule('^edit-agency/([^/]*)/?', 'index.php?edit-agency=$matches[1]', 'top' );

		 add_rewrite_tag ('%invitehash%', '([^/]*)/?');
		 add_rewrite_rule('^invitehash/([^/]*)/?', 'index.php?invitehash=$matches[1]', 'top' );

		 add_rewrite_tag ('%created_user%', '([^/]*)/?');
		 add_rewrite_rule('^created_user/([^/]*)/?', 'index.php?created_user=$matches[1]', 'top' );

		 flush_rewrite_rules();
	}
}

if ( ! function_exists( 'zoner_insert_attachment' ) ) {
	function zoner_insert_attachment($file_handler, $post_id, $setthumb = false) {
		if ($_FILES[$file_handler]['error'] !== UPLOAD_ERR_OK) __return_false();

		require_once(ABSPATH . "wp-admin" . '/includes/image.php');
		require_once(ABSPATH . "wp-admin" . '/includes/file.php');
		require_once(ABSPATH . "wp-admin" . '/includes/media.php');

		$attach_id = media_handle_upload( $file_handler, $post_id );
		if ($setthumb) set_post_thumbnail($post_id, $attach_id);
		return $attach_id;
	}

}

//

if ( ! function_exists( 'zoner_get_form_send_email' ) ) {
	function zoner_get_form_send_email($args = array()) {

		$defaults = array (
			'title' => __('Send Me a Message', 'zoner'),
			'id'	=> 'form-contact-agent',
			'name'	=> 'form-contact-agent',
			'sumbit_button_title' => __('Send a Message', 'zoner'),
			'email'	=> get_option('admin_email'),
			'agent_name' 		=> '',
			'class_columns'		=> array('col-md-6'),
			'send_from_page' 	=> ''
		);

		$args = wp_parse_args( $args, $defaults );
		extract( $args, EXTR_SKIP );

		if (!is_array($class_columns))
			$class_columns = array($class_columns);
		if (!empty($title))
			echo '<h3>'.$title.'</h3>';


	?>
		<div class="agent-form">
			<form role="form" id="<?php echo $id; ?>" name="<?php echo $name; ?>" method="post" action="" class="clearfix mail-form-sending">
				<input type="hidden" name="wsend_email"  value="<?php echo $email; ?>"/>
				<?php if (!empty($agent_name)) { ?>
					<input type="hidden" name="wsend_agent"  value="<?php echo $agent_name; ?>"/>
				<?php } ?>

				<?php if (!empty($send_from_page)) { ?>
					<input type="hidden" name="send_from_page"  value="<?php echo $send_from_page; ?>"/>
				<?php } ?>

				<div class="row">
					<div class="<?php echo implode(' ', $class_columns); ?>">
						<div class="form-group">
							<label for="mfs-name"><?php _e('Your Name', 'zoner'); ?><em>*</em></label>
							<input type="text" class="form-control" id="mfs-name" name="mfs-name" required>
						</div><!-- /.form-group -->
					</div><!-- /.col-md-6 -->
					<div class="<?php echo implode(' ', $class_columns); ?>">
						<div class="form-group">
							<label for="mfs-email"><?php _e('Your Email', 'zoner'); ?><em>*</em></label>
							<input type="email" class="form-control" id="mfs-email" name="mfs-email" required>
						</div><!-- /.form-group -->
					</div><!-- /.col-md-6 -->
				</div><!-- /.row -->
				<div class="row">
					<div class="col-md-12">
						<div class="form-group">
							<label for="mfs-message"><?php _e('Your Message', 'zoner'); ?><em>*</em></label>
							<textarea class="form-control" id="mfs-message" rows="5" name="mfs-message" required></textarea>
						</div><!-- /.form-group -->
					</div><!-- /.col-md-12 -->
				</div><!-- /.row -->
				<div class="form-group clearfix">
					<button type="submit" class="btn pull-right btn-default" id="form-contact-agent-submit"><?php echo $sumbit_button_title; ?></button>
				</div><!-- /.form-group -->
			</form><!-- /#form-contact -->
		</div>
	<?php
	}
}


/*Agents page*/
if ( ! function_exists( 'zoner_get_agent_information' ) ) {
	function zoner_get_agent_information() {
		global $prefix, $zoner, $zoner_config;
		$q_author 	  = get_queried_object();
		$userID 	  = $q_author->ID;
		$cwa = 	isset($zoner_config['contact-agent']) && !empty($zoner_config['contact-agent']) ? (int) $zoner_config['contact-agent'] : 2;

		$avatar 		= get_the_author_meta( $prefix.'avatar', $userID );
		$facebook 		= get_the_author_meta( $prefix.'facebookurl', $userID );
		$twitter 		= get_the_author_meta( $prefix.'twitterurl', $userID );
		$google_plus 	= get_the_author_meta( $prefix.'googleplusurl', $userID );
		$linkedin 		= get_the_author_meta( $prefix.'linkedinurl', $userID );
		$pinterest 		= get_the_author_meta( $prefix.'pinteresturl', $userID );
		$tel 			= get_the_author_meta( $prefix.'tel', $userID );
		$mob 			= get_the_author_meta( $prefix.'mob', $userID );
		$skype 			= get_the_author_meta( $prefix.'skype', $userID );
		$email 			= get_the_author_meta( 'user_email', $userID );
		$display_name 	= get_the_author_meta( 'display_name', $userID );
		$description  	= get_the_author_meta( 'description', $userID );

		$is_form 		= isset($zoner_config['property-agent-form']) && !empty($zoner_config['property-agent-form']) ? $zoner_config['property-agent-form'] : false;
		$is_converstion = isset($zoner_config['property-agent-conversation']) && !empty($zoner_config['property-agent-conversation']) ? $zoner_config['property-agent-conversation'] : false;

		$args = array();
		$args = array(
			'post_type'		=> 'agency',
			'post_status'	=> 'publish',
			'author'		=>  $userID,
			'posts_per_page' => 1
		);

		$agency_link = $agency_title = $agency_logo = '';
		$count_property = 0;

		$arr_agencies = array();
		$curr_agency  = new WP_Query($args);

		if ($curr_agency->have_posts() ) {
			while ( $curr_agency->have_posts() ) : $curr_agency->the_post();

				$agency_id = get_the_ID();
				$sfi = esc_url(get_post_meta($agency_id, $prefix . 'agency_line_img', true));
				$agency_logo = '<img data-src="holder.js/200x200?text='.__('No Image', 'zoner') .'" alt="" />';
				if ($sfi) {
					$agency_logo = '<img class="" src="'.$sfi.'" alt="" />';
				}

				$arr_agencies[] = array('title' => get_the_title($agency_id),
										'link'	=> get_permalink($agency_id),
										'logo'	=> $agency_logo
										);
			endwhile;
		}


		$all_invites_to_agency = $zoner->invites->zoner_get_all_agencies_from_agent($userID);
		$agency_post_in = array();
		if (!empty($all_invites_to_agency)) {

			foreach ($all_invites_to_agency as $agency) {
				$agency_post_in[] = $agency->agency_id;
			}

			$args = array();
			$args = array(
				'post_type'		 => 'agency',
				'post_status'	 => 'publish',
				'posts_per_page' => -1,
				'post__in'		 => $agency_post_in
			);
			$curr_agency = null;
			$curr_agency = new WP_Query($args);

			if ($curr_agency->have_posts() ) {
				while ( $curr_agency->have_posts() ) : $curr_agency->the_post();

					$agency_id = get_the_ID();

					$sfi = esc_url(get_post_meta($agency_id, $prefix . 'agency_line_img', true));
					$agency_logo = '<img data-src="holder.js/200x200?auto=yes&text='.__('No Image', 'zoner').'" alt="" />';
					if ($sfi) {
						$agency_logo = '<img class="" src="'.$sfi.'" alt="" />';
					}

					$arr_agencies[] = array('title' => get_the_title($agency_id),
											'link'	=> get_permalink($agency_id),
											'logo'	=> $agency_logo
										);
				endwhile;
			}

		}



		$args = array();
		$args = array(
				'post_type'		=> 'property',
				'post_status' 	=> 'publish',
				'posts_per_page' => -1,
				'orderby'	=> 'DATE',
				'order'		=> 'ASC',
				'author'	=> $userID

		);

		$prop_from_agent = new WP_Query( $args );
		$count_property = $prop_from_agent->found_posts;

	?>
		<section id="agent-detail" class="agent-detail">
			<header><h1><?php echo $display_name; ?></h1></header>
            <section id="agent-info" class="agent-info">
				<div class="row">
					<div class="col-md-3 col-sm-3">
						<figure class="agent-image"><?php echo zoner_get_profile_avartar($userID); ?></figure>
						<?php if (is_user_logged_in() && $is_converstion) { ?>
					<div class="chat-btn">
						<button id="chat-wnd" data-label="<?php _e('Start conversation', 'zoner');?>" class="btn pull-right btn-default" type="submit"><?php _e('Start conversation', 'zoner');?></button>
						<?php zoner_get_property_chat_wnd($userID); ?>
					</div>
				<?php } ?>
                     </div><!-- /.col-md-3 -->
					<div class="col-md-5 col-sm-5">
						<h3><?php _e('Contact Info', 'zoner'); ?></h3>
                        <dl>
							<?php if (!empty($tel)) { ?>
								<dt><?php _e('Phone','zoner'); ?>:</dt>
								<dd><?php echo $tel; ?></dd>
							<?php } ?>

							<?php if (!empty($mob)) { ?>
								<dt><?php _e('Mobile','zoner'); ?>:</dt>
								<dd><?php echo $mob;  ?></dd>
							<?php } ?>

							<?php if (!empty($email) && (is_user_logged_in())) { ?>
								<dt><?php _e('Email','zoner'); ?>:</dt>
								<dd><a href="mailto:<?php echo $email; ?>"><?php echo $email; ?></a></dd>
							<?php } ?>

							<?php if (!empty($skype)) { ?>
								<dt><?php _e('Skype','zoner'); ?>:</dt>
								<dd><a href="skype:<?php echo $skype; ?>?call"><?php echo $skype; ?></a></dd>
							<?php } ?>
						</dl>
					</div><!-- /.col-md-5 -->

					<div class="col-md-4 col-sm-4">
						<h3><?php _e('Shortly About Me', 'zoner'); ?></h3>
                        <p><?php echo $description; ?></p>
                    </div><!-- /.col-md-4 -->
                </div><!-- /.row -->
                <div class="row">
					<div class="col-md-offset-3 col-md-5 col-sm-offset-3 col-sm-5">
						<?php
							if ($arr_agencies) {
						?>
							<h3><?php _e('Agency', 'zoner'); ?></h3>

							<?php foreach ($arr_agencies as $list_agency) { ?>
								<a href="<?php echo esc_url($list_agency['link']); ?>" class="agency-logo" title="<?php echo $list_agency['title'];?>"><?php echo $list_agency['logo']; ?></a>
							<?php } ?>

						<?php
							}
						?>
					</div><!-- /.col-md-5 -->
                    <div class="col-md-4 col-sm-4">
						<?php
							if (!empty($facebook) || !empty($twitter) || !empty($google_plus) || !empty($pinterest) || !empty($linkedin)) { ?>
						<h3><?php _e('My Social Profiles', 'zoner'); ?></h3>
						<div class="agent-social">
							<?php if (!empty($facebook)) 	{ ?> <a href="<?php echo $facebook; ?>" class="fa fa-facebook btn btn-grey-dark"></a> <?php } ?>
							<?php if (!empty($twitter))		{ ?> <a href="<?php echo $twitter; ?>" class="fa fa-twitter btn btn-grey-dark"></a>	<?php } ?>
							<?php if (!empty($google_plus)) { ?> <a href="<?php echo $google_plus; ?>" class="fa fa-google-plus btn btn-grey-dark"></a> <?php } ?>
							<?php if (!empty($pinterest)) 	{ ?> <a href="<?php echo $pinterest; ?>" class="fa fa-pinterest btn btn-grey-dark"></a> <?php } ?>
							<?php if (!empty($linkedin)) 	{ ?> <a href="<?php echo $linkedin; ?>" class="fa fa-linkedin btn btn-grey-dark"></a> <?php } ?>
                        </div>
						<?php } ?>
                   </div><!-- /.col-md-4 -->
				</div><!-- /.row -->
			</section><!-- /#agent-info -->
            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <?php zoner_get_agent_map($userID); ?>
                </div>
            </div>
			<section id="agent-properties" class="agent-properties">
				<header><h3><?php printf( '%s (%s)', __('My Properties', 'zoner'), $count_property ); ?></h3></header>
				<div class="layout-expandable" data-layoutrow="<?php echo $count_property; ?>">
					<?php
						if ($prop_from_agent->have_posts() ) {
							$cnt = 1;
							while ( $prop_from_agent->have_posts() ) : $prop_from_agent->the_post();

								if ($cnt%3 == 1) {
									echo '<div class="row">';
									$is_closed = false;
								}
									zoner_get_property_grid_items_original();

								if ($cnt%3 == 0) {
									echo '</div>';
									$is_closed = true;
								}

								$cnt++;
							endwhile;

							if (!$is_closed) echo '</div>';
						?>
					</div>


			<?php
					}
					wp_reset_postdata();
			?>

			<?php if ($count_property >= 7) { ?>
				<div class="center">
					<span class="show-all"><?php _e('Show All Properties', 'zoner'); ?></span>
				</div>
			<?php } ?>


		</section>
		<hr class="thick"/>

		<div class="row">
			<div class="col-md-12">
				<?php if ($is_form) { ?>
				<?php
					$args = array();
					$args = array (
								'title' => __('Send Me a Message', 'zoner'),
								'id'	=> 'form-contact-agent',
								'name'	=> 'form-contact-agent',
								'sumbit_button_title' => __('Send a Message', 'zoner'),
								'email'	=> $email,
								'class_columns' => array('col-md-12')
					);
					zoner_get_form_send_email($args);
				?>
				<?php } ?>


			</div>
		</div><!-- /.row -->
	</section><!-- /.agent-detail -->

	<?php
	}
}

if ( ! function_exists( 'zoner_get_author_information' ) ) {
	function zoner_get_author_information() {
		global $zoner_config, $prefix, $zoner;
		?>
		<?php if ( have_posts() ) : ?>
			<?php the_post(); ?>
			<section id="author-detail" class="author-detail">
				<header><h1><?php printf( __( 'All posts by "%s"', 'zoner' ), get_the_author()); ?></h1></header>
			</section>

			<?php if ( get_the_author_meta( 'description' ) ) : ?>
				<div class="author-description"><?php the_author_meta( 'description' ); ?></div>
			<?php endif; ?>

			<?php
				rewind_posts();

				while ( have_posts() ) : the_post();
					get_template_part( 'content', get_post_format() );
				endwhile;
				zoner_paging_nav();
				else :
					get_template_part( 'content', 'none' );
				endif;
	}
}



if ( ! function_exists( 'zoner_get_map_points_array' ) ) {
	function zoner_get_map_points_array() {
		global $zoner_config, $prefix, $zoner;

		$array_of_locations = array();

		$cache_obj_id 		= $property_loop_page = 0;
		$property_loop_page = $zoner->zoner_get_page_id('page-property-archive');
		$page_status = $page_type = $page_features = $page_tax_cat = $page_tax_city = false;
		$is_used_map_cache  = false;

		$qoperator = isset($zoner_config['query-operator']) ? esc_attr($zoner_config['query-operator']) : "OR";
		if ($qoperator == 'OR') {
			$qoperator  = "IN";
		}

		if (is_front_page() || is_home()) {
			if (!empty($zoner_config['map-use-cache']) && ($zoner_config['map-use-cache']))
			$is_used_map_cache = true;

			if (is_front_page())
				$cache_obj_id = get_option('page_on_front');
			if (is_home())
				$cache_obj_id = get_option('page_for_posts');
		}

		if (($property_loop_page != 0) && (is_post_type_archive( 'property' ))) {
			$page_status  	= get_post_meta($property_loop_page, $prefix. 'page_tax_status', true);
			$page_type  	= get_post_meta($property_loop_page, $prefix. 'page_tax_type', true);
			$page_features  = get_post_meta($property_loop_page, $prefix. 'page_tax_features', true);
			$page_tax_cat 	= get_post_meta($property_loop_page, $prefix. 'page_tax_cat', true);
			$page_tax_city 	= get_post_meta($property_loop_page, $prefix. 'page_tax_city', true);
			$is_used_map_cache 	= get_post_meta($property_loop_page, $prefix. 'is_used_map_cache', true);
			$is_used_map_cache  = ($is_used_map_cache == 'on');
			$cache_obj_id 	= $property_loop_page;
		} else {
			if (is_page() && !is_front_page() && !is_home()) {
				$page_status  	= get_post_meta(get_the_ID(), $prefix. 'page_tax_status', true);
				$page_type  	= get_post_meta(get_the_ID(), $prefix. 'page_tax_type', true);
				$page_features  = get_post_meta(get_the_ID(), $prefix. 'page_tax_features', true);
				$page_tax_cat 	= get_post_meta(get_the_ID(), $prefix. 'page_tax_cat', true);
				$page_tax_city 	= get_post_meta(get_the_ID(), $prefix. 'page_tax_city', true);
				$is_used_map_cache 	= get_post_meta(get_the_ID(), $prefix. 'is_used_map_cache', true);
				$is_used_map_cache  = ($is_used_map_cache == 'on');
				$cache_obj_id = get_the_ID();
			}
		}

		/*Filters maps parametrs*/
		$tax_query = $meta_query = $post__in = array();
		$meta_query['relation'] = 'AND';

		if (isset($_GET) && isset($_GET['filter_property']) && wp_verify_nonce($_GET['filter_property'], 'zoner_filter_property')) {
			/*Zip*/
			if (!empty($_GET['sb-zip'])) {
				$sb_zip = esc_attr($_GET['sb-zip']);
				$meta_query[] = array('key' => $prefix .'zip', 'value' => $sb_zip, 'compare' => 'like');
			}

			/*Keyword*/
			if (!empty($_GET['sb-keyword'])) {
				$sb_keyword  = esc_attr($_GET['sb-keyword']);
				$keyword_ids = $zoner->zoner_search_keyword_ids($sb_keyword);

				if (!empty($keyword_ids)) {
					foreach($keyword_ids as $val) {
						$post__in[] = $val->post_id;
					}
				} else {
					$post__in[] = -9999999;
				}
			}

			/*Between Price*/
			if (!empty($_GET['sb-price'])) {
				$sb_price = explode(';', $_GET['sb-price']);
				$meta_query[] = array('key'	=> $prefix .'price', 'value' => array($sb_price[0], $sb_price[1]), 'compare' => 'BETWEEN','type' => 'DECIMAL');
			}

			/*Country*/
			if (!empty($_GET['sb-country'])) {
				$sb_country = $_GET['sb-country'];
				$meta_query[] = array('key' => $prefix .'country','value' => $sb_country, 'compare' => '=' );
			}

			/*District*/
			if (!empty($_GET['sb-district'])) {
				$sb_district = $_GET['sb-district'];
				$meta_query[] = array('key' => $prefix .'district', 'value' => $sb_district, 'compare' => '=');
			}

			/*Area*/
			if (!empty($_GET['sb-area'])) {
				$sb_area = $_GET['sb-area'];
				$meta_query[] = array('key' => $prefix .'area', 'value' => $sb_area, 'compare' => '>', 'type' => 'DECIMAL');
			}

			/*Condition*/
			if (!empty($_GET['sb-condition'])) {
				$condition = (int)$_GET['sb-condition'];
				$meta_query[] = array('key' => $prefix .'condition', 'value' => $condition, 'compare' => '=');
			}

			/*Payment*/
			if (!empty($_GET['sb-payment'])) {
				$payment = (int)$_GET['sb-payment'];
				$meta_query[] = array('key' => $prefix .'payment', 'value' => $payment, 'compare' => '=');
			}

			/*Rooms*/
			if (!empty($_GET['sb-rooms'])) {
				$rooms = (int)$_GET['sb-rooms'];
				$meta_query[] = array( 'key' => $prefix .'rooms', 'value' => $rooms, 'compare' => '=');
			}

			/*Beds*/
			if (!empty($_GET['sb-beds'])) {
				$beds = (int)$_GET['sb-beds'];
				$meta_query[] = array('key' => $prefix .'beds', 'value' => $beds, 'compare' => '=');
			}

			/*Baths*/
			if (!empty($_GET['sb-baths'])) {
				$baths = (int)$_GET['sb-baths'];
				$meta_query[] = array( 'key' => $prefix .'baths', 'value' => $baths, 'compare' => '=' );
			}

			if (!empty($_GET['sb-garages'])) {
				$garages = (int)$_GET['sb-garages'];
				$meta_query[] = array( 'key' => $prefix .'garages', 'value' => $garages, 'compare' => '=');
			}

			/*Tax*/
			$tax_query['relation'] = 'AND';
			/*Features*/
			if (!empty($_GET['sb-features'])) {
				$sb_features = $_GET['sb-features'];
				$tax_query[] = array( 'taxonomy' => 'property_features', 'field' => 'id', 'terms' => $sb_features, 'operator' => $qoperator);
			}

			/*Categories*/
			if (!empty($_GET['sb-cat'])) {
				$sb_cat = (int)$_GET['sb-cat'];
				$tax_query[] = array( 'taxonomy' => 'property_cat', 'field' => 'id', 'terms' => $sb_cat );
			}

			/*Status*/
			if (!empty($_GET['sb-status'])) {
				$sb_status = (int)$_GET['sb-status'];
				$tax_query[] = array( 'taxonomy' => 'property_status', 'field' => 'id', 'terms' => $sb_status );
			}

			/*Type*/
			if (!empty($_GET['sb-type'])) {
				$sb_type = (int)$_GET['sb-type'];
				$tax_query[] = array( 'taxonomy' => 'property_type', 'field' => 'id', 'terms' => $sb_type );
			}

			/*City*/
			if (!empty($_GET['sb-city'])) {
				$sb_city = $_GET['sb-city'];
				$sb_city = explode(',', $sb_city);
				$tax_query[] = array( 'taxonomy' => 'property_city', 'field' => 'term_id', 'terms' => $sb_city);
			}

		} else {
			/*Tax*/
			/*Features*/
			$tax_query['relation'] = 'AND';
			if ($page_features)
			$tax_query[] = array( 'taxonomy' => 'property_features', 'field' => 'id', 'terms' => $page_features,   'operator' => 'IN');

			/*Status*/
			if ($page_status)
			$tax_query[] = array( 'taxonomy' => 'property_status', 'field' => 'id', 'terms' => $page_status, 'operator' => 'IN' );

			/*Type*/
			if ($page_type)
			$tax_query[] = array( 'taxonomy' => 'property_type', 'field' => 'id', 'terms' => $page_type, 'operator' => 'IN' );

			/*Categories only for Pages*/
			if ($page_tax_cat)
			$tax_query[] = array( 'taxonomy' => 'property_cat', 'field' => 'id', 'terms' => $page_tax_cat, 'operator' => 'IN' );
		}


		$args = array(
				'post_type'			=> 'property',
				'post_status' 		=> 'publish',
				'posts_per_page' 	=> -1,
				'orderby'			=> 'DATE',
				'order'				=> 'DESC',
				'meta_query' 		=> $meta_query,
				'tax_query'			=> $tax_query,
				'fields' 			=> 'ids',
				'no_found_rows' 	=> true,
				'post__in'			=> $post__in,
				'nopaging'			=> true
		);


		if ( false === ( $cache_all_property = get_transient( 'cache_all_property'.$cache_obj_id))) {
			$cache_all_property = new WP_Query( $args );
			set_transient( 'cache_all_property'.$cache_obj_id, $cache_all_property, 1*60*60 );
		} else {
			if ($is_used_map_cache) {
				$all_property = $cache_all_property;
			} else {
				$cache_all_property = new WP_Query( $args );
				delete_transient('cache_all_property'.$cache_obj_id);
			}
		}

		$all_property = $cache_all_property;

		if ( $all_property->have_posts() ) {
			   foreach( $all_property->posts as $id_prop ) {
				$gproperty 	= $prop_type_arr = array();

				$gproperty  = $zoner->property->get_property($id_prop);

				$show_on_map = $gproperty->show_on_map;
			   	if (!empty($show_on_map) && $show_on_map  == 'off') continue;

				$address 		= $gproperty->address;
				$full_address 	= $gproperty->full_address;
				if (!empty($gproperty->city))
				$city			= $gproperty->city;
				$district		= $gproperty->district;
				$zip			= $gproperty->zip;
				$price_format 	= $gproperty->price_format;
				$payment_rent 	= $gproperty->payment_rent;
				$area_unit 		= $gproperty->area_unit;

				$price 			= $gproperty->price;
				$currency		= $gproperty->currency;
				$price_html 	= $zoner->currency->get_zoner_property_price($price, $currency, $price_format, $payment_rent, $area_unit, false);

				$lat 		= $gproperty->lat;
				$lng 		= $gproperty->lng;

				$img_url = $img_holder = '';
				if (has_post_thumbnail($id_prop)) {
					$attachment_id 	  = get_post_thumbnail_id( $id_prop );
					$image_attributes = wp_get_attachment_image_src( $attachment_id, 'zoner-footer-thumbnails');
					$img_url = $image_attributes[0];
				} else {
					$img_holder = 'holder.js/555x445?auto=yes&text='.__('Property', 'zoner');
				}

				$prop_types = $gproperty->property_types;
				$prop_type_out = array();

				if (!empty($prop_types)) {
					foreach ($prop_types as $prop_type)  {
						$attachment_id = $zoner->zoner_tax->get_zoner_term_meta($prop_type->term_id, 'thumbnail_id');
						$img_tax 	   = wp_get_attachment_image_src($attachment_id, 'full');
						$prop_type_out = $img_tax[0];
						break;
					}
				}

				if (empty($prop_type_out)) {
					$prop_type_out = get_template_directory_uri() . '/includes/theme/assets/img/empty.png';
				}

				if (!empty($lat) && !empty($lng)) {
					if (!empty($_REQUEST['advanced_map'])){ //using for gmap shortcode - need more options
						$condition  = $condition_name = $status ='';
				        $condition	= get_post_meta($id_prop, $prefix.'condition', true);
					    $condition_name = $zoner->property->get_condition_name($condition);
 						if (!empty($condition_name) && ($condition > 0)) {
							$condition = '<figure class="ribbon">'.$condition_name.'</figure>';
						} else $condition = '';

					    $prop_status 	= $gproperty->property_status;
					    if (!empty($prop_status)) {
							foreach ($prop_status as $status)  {
								$prop_status_html[] = $status->name;
							}
							$status = '<figure class="tag status">'.implode(', ', $prop_status_html).'</figure>';
							unset($prop_status_html);
						}



						$array_of_locations[] = array (
							'post_id'   => $id_prop,
							'title' 	=> esc_js(get_the_title($id_prop)),
							'address' 	=> wp_kses_data($full_address),
							'price' 	=> esc_js(wp_kses_data($price_html)),
							'lat' 		=> esc_js($lat),
							'lng' 		=> esc_js($lng),
							'link' 		=> esc_js(get_permalink($id_prop)),
							'featured-image' => esc_js($img_url),
							'holder-image'   => esc_js($img_holder),
							'type' 			=> esc_url($prop_type_out),
							//'rooms' 		=> $gproperty->rooms,
							'condition'		=> $condition,
							'status'		=> $status,
            				'beds' 			=> (!empty($gproperty->beds)?'<li><header>'.__('Beds', 'zoner')	.':</header><figure>'.esc_attr($gproperty->beds).'</figure></li>':''),
            				'baths' 		=> (!empty($gproperty->baths)?'<li><header>'.__('Baths', 'zoner')	.':</header><figure>'.esc_attr($gproperty->baths).'</figure></li>':''),
            				'garages' 		=> (!empty($gproperty->garages)?'<li><header>'.__('Garages', 'zoner')	.':</header><figure>'.esc_attr($gproperty->garages).'</figure></li>':''),
            				'area' 			=> (!empty($gproperty->area)?'<li><header>'.__('Area', 'zoner')	.':</header><figure>'.esc_attr($gproperty->area). ' ' . $zoner->property->ret_area_units_by_id($gproperty->area_unit) .'</figure></li>':'')
						);
						if (!empty($_REQUEST['items_number_max'])){
							if (!isset($number_property)) $number_property = 0;
							$number_property++;
							if ($number_property==$_REQUEST['items_number_max']) break;
						}

					}else{
						$array_of_locations[] = array (
							'title' 	=> esc_js(get_the_title($id_prop)),
							'address' 	=> wp_kses_data($full_address),
							'price' 	=> esc_js(wp_kses_data($price_html)),
							'lat' 		=> esc_js($lat),
							'lng' 		=> esc_js($lng),
							'link' 		=> esc_js(get_permalink($id_prop)),
							'featured-image' => esc_js($img_url),
							'holder-image'   => esc_js($img_holder),
							'type' 		=> esc_url($prop_type_out)
						);
					}
				}

			}
		}

		wp_reset_postdata();

		if (!empty($array_of_locations)) {
			$is_marker_from_file = false;
			if (!empty($zoner_config['map-markers-ff'])) $is_marker_from_file = esc_attr($zoner_config['map-markers-ff']);

			/*Only to front page template*/
			if ($is_marker_from_file && (is_front_page() || is_home())) {
				$path =	get_template_directory().'/markers/markers_'.$cache_obj_id.'.pin';

				if (!is_dir($path))
				mkdir(get_template_directory() . '/markers/', 0755);

				@file_put_contents($path, json_encode($array_of_locations));

			}

			return json_encode($array_of_locations);
		} else {
			return -1;
		}
	}
}


if ( ! function_exists( 'zoner_generate_input_field' ) ) {
	function zoner_generate_input_field($id = '', $placeholder = '', $name = '', $additional_wrapper = array('col-md-2', 'col-sm-4'), $class = 'form-control', $type = 'text', $value = null) {

		if (!empty($additional_wrapper)) { ?>
			<div class="<?php echo implode(' ', $additional_wrapper); ?>">
		<?php } ?>
			<div class="form-group">
				<input type="<?php echo $type; ?>" class="<?php echo $class; ?>" id="<?php echo $id; ?>" name="<?php echo $name; ?>" value="<?php echo $value;  ?>" placeholder="<?php echo $placeholder; ?>" />
			</div>
		<?php if (!empty($additional_wrapper)) { ?>
			</div>
		<?php }
	}
}

if ( ! function_exists( 'zoner_generate_select_field' ) ) {
	function zoner_generate_select_field($id = '', $placeholder = '', $name = '', $options = array(), $additional_wrapper = array('col-md-2', 'col-sm-4'), $class = 'form-control', $default_value = '', $choose_value = null) {

		if (!empty($additional_wrapper)) { ?>
			<div class="<?php echo implode(' ', $additional_wrapper); ?>">
		<?php } ?>

			<div class="form-group">
				<select id="<?php echo $id; ?>" class="<?php echo $class; ?>" name="<?php echo $name; ?>">
					<option value="<?php echo $default_value; ?>"><?php echo $placeholder; ?></option>
					<?php
						if (!empty($options))  {
							foreach ($options as $key => $option) {

								echo '<option value="'.$key.'" '.selected( $choose_value, $key, false).'>'.$option.'</option>';
							}
						}
					?>
				</select>
			</div><!-- /.form-group -->
		<?php if (!empty($additional_wrapper)) { ?>
			</div>
		<?php }
	}
}

if ( ! function_exists( 'zoner_generate_price_field')) {
	function zoner_generate_price_field($id = '',  $class = '',  $name = '',  $min_value = '', $max_value = '') {
		?>
		<input type="hidden" value="yes" name="sb-price-req" />
			<div class="form-group">
				<div class="price-range">
					<input id="<?php echo $id; ?>" class="<?php echo $class; ?>" type="text" name="<?php echo $name; ?>" value="<?php echo $min_value . ';' . $max_value; ?>">
				</div>
			</div>
		<?php
	}
}

if ( ! function_exists( 'zoner_generate_submit_btn')) {
	function zoner_generate_submit_btn($submit_text = '', $additional_wrapper = array('col-md-2', 'col-sm-4')) {

		if (!empty($additional_wrapper)) { ?>
			<div class="<?php echo implode(' ', $additional_wrapper); ?>">
		<?php } ?>

			<div class="form-group">
				<button type="submit" class="btn btn-default"><?php echo $submit_text; ?></button>
            </div><!-- /.form-group -->

		<?php if (!empty($additional_wrapper)) { ?>
			</div>
		<?php }
	}
}

if ( ! function_exists( 'zoner_generate_search_box' ) ) {
	function zoner_generate_search_box($class = '', $display = 0, $is_shortcode = 0) {
		global $zoner_config, $prefix, $zoner, $wp;

		if (!empty($zoner_config['zoner-searchbox'])) {
			$search_fields = $zoner_config['zoner-searchbox'];
		}

		if (count($search_fields['enabled']) <= 1)
		return false;

		$perma_struct = get_option( 'permalink_structure' );
		$link 		  = get_permalink($zoner->zoner_get_page_id('page-property-archive'));

		$property_archive = null;
		$archivePropertyPage = $zoner->zoner_get_page_id('page-property-archive');
		if (!empty($archivePropertyPage)) {
			$property_archive = $archivePropertyPage;
		}

		if ($perma_struct == '' || empty($property_archive)) {
			$link = get_post_type_archive_link('property');
		}

		$submit_text = trim($zoner_config['zoner-searchbox-submit']);
		$submit_text = (!empty($submit_text)) ? $submit_text : __('Search Now', 'zoner');
		$submit_text = esc_html($submit_text);

		$add_wrapper = array('col-md-2', 'col-sm-4');
		$header_variations = zoner_get_header_variation_index();

		$bg_advanced_search = '';
		if (!empty($zoner_config['zoner-searchbox']))
			$bg_advanced_search = $zoner_config['zoner-searchbox-advancedimg']['url'];

		$features_include = array();
		if (!empty($zoner_config['specific-features'])) {
			$features_include = $zoner_config['specific-features'];
		}

		$args = array('orderby' => 'name', 'order' => 'ASC', 'hide_empty' => false);

		$filter_pid = $filter_zip = $filter_keyword = $filter_status = $filter_city = $filter_price = $filter_district = $filter_type = $filter_country = $filter_area = $filter_cat = '';
		$condition  = $payment = $rooms = $beds = $baths = $garages = $features =  $sorting  = $page_id = '';
		$min_price  = 0;
		if (isset($_GET) && isset($_GET['filter_property']) && wp_verify_nonce($_GET['filter_property'], 'zoner_filter_property')) {
			$min_price  = $max_price = 0;
			if (!empty($_GET['sb-zip'])) 		$filter_zip 	= $_GET['sb-zip'];
			if (!empty($_GET['sb-keyword'])) 	$filter_keyword	= $_GET['sb-keyword'];
			if (!empty($_GET['sb-area'])) 		$filter_area 	= $_GET['sb-area'];
			if (!empty($_GET['sb-status'])) 	$filter_status 	= $_GET['sb-status'];
			if (!empty($_GET['sb-cat'])) 		$filter_cat 	= $_GET['sb-cat'];
			if (!empty($_GET['sb-type'])) 		$filter_type 	= $_GET['sb-type'];
			if (!empty($_GET['sb-country'])) 	$filter_country = $_GET['sb-country'];
			if (!empty($_GET['sb-city'])) 		$filter_city 	= $_GET['sb-city'];
			if (!empty($_GET['sb-district'])) 	$filter_district = $_GET['sb-district'];



			/*Additional fileds*/
			if (!empty($_GET['sb-condition'])) 	$condition 	= $_GET['sb-condition'];
			if (!empty($_GET['sb-payment'])) 	$payment 	= $_GET['sb-payment'];
			if (!empty($_GET['sb-rooms'])) 		$rooms 		= $_GET['sb-rooms'];
			if (!empty($_GET['sb-beds'])) 		$beds 		= $_GET['sb-beds'];
			if (!empty($_GET['sb-baths'])) 		$baths		= $_GET['sb-baths'];
			if (!empty($_GET['sb-garages'])) 	$garages 	= $_GET['sb-garages'];

			if (!empty($_GET['sb-features'])) 	$features 	= $_GET['sb-features'];
		}

		if (isset($_GET) && isset($_GET['sorting'])) $sorting = $_GET['sorting'];
		if (isset($_GET) && isset($_GET['page_id'])) $page_id = $_GET['page_id'];

		if ($is_shortcode) {
			$search_box_wrapper = 'horizontal-search-shortcode';
		} else {
			$search_box_wrapper = 'search-box-wrapper';
		}
	?>


	<!-- Search Box -->

	<?php if ($display == 1) {
		?>
		<div class="<?php echo $search_box_wrapper ?> <?php echo (empty($class))?'':implode(' ', $class); ?>">
			<div class="search-box-inner">
				<div class="container">
					<div class="search-box map">

						<?php if ((($header_variations == 5) || ($header_variations == 10) || ($header_variations == 14) || ($header_variations == 18)) && !$is_shortcode ) { ?>
							<a class="advanced-search-toggle" data-toggle="collapse" data-parent="#accordion" href="#advanced-search-sale"><?php _e('Advanced Search', 'zoner'); ?><i class="fa fa-plus"></i></a>
							<hr />
						<?php } ?>

						<form role="form" id="form-map-<?php echo mt_rand(0, 1000);?>" class="form-map form-search" action="<?php echo $link; ?>" method="GET">
							<?php
								if ((($header_variations == 5) || ($header_variations == 10) || ($header_variations == 14) || ($header_variations == 18)) && !$is_shortcode) {

									$property_features = array();
									$args_features = array();
									$args_features = array('orderby' => 'name', 'order' => 'ASC', 'hide_empty' => false, 'include' => $features_include);
									$property_features = get_terms('property_features', $args_features);
									if (!empty($property_features)) {
							?>
									<div id="advanced-search-sale" class="panel-collapse collapse">
										<div class="advanced-search">
											<header><h3><?php _e('Property Features', 'zoner'); ?></h3></header>
											<ul class="submit-features">
												<?php
													foreach ($property_features as $term) {
														echo '<li><div class="checkbox"><label><input type="checkbox" value="'.$term->term_id.'" name="sb-features[]">'.$term->name.'</label></div></li>';
													}
												?>
											</ul>
										</div>
									</div>
							<?php
									}
								}
							?>

		<div class="row">
	<?php

		} else {
		if (empty($class)) $class =  array('col-md-3', 'col-sm-4');

		$add_wrapper = array();
		$search_wrap = 'search-box-wrapper';
		if ( $header_variations == 12 || $header_variations == 16 || $header_variations == 2 || $header_variations == 3 || $header_variations == 7 || $header_variations == 8 )
			$search_wrap = 'search-box-wrapper advanced_search_box_right';
	?>
		<div class="<?php echo $search_wrap; ?>">
			<div class="search-box-inner">
				<div class="container">
					<div class="row">
						<div class="<?php echo implode(' ', $class); ?>">
							<div class="search-box map">
								<form role="form" id="form-map" class="form-map form-search" action="<?php echo $link; ?>" method="GET">
									<h2><?php _e('Search Your Property', 'zoner'); ?></h2>

	<?php } ?>


									<?php wp_nonce_field( 'zoner_filter_property', 'filter_property', false, true ); ?>
									<?php
										if ($perma_struct == "") echo '<input type="hidden" name="post_type" value="property"/>';
										if ($sorting != '') 	 echo '<input type="hidden" name="sorting" value="'.$sorting.'" />';
										if ($page_id != '' && $perma_struct != "") 	 echo '<input type="hidden" name="page_id" value="'.$page_id.'" />';

										if (!empty($features)) {
											foreach ($features as $f) {
												echo '<input type="hidden" name="sb-features[]" value="'.$f.'" />';
											}
										}

										/*If WPML Parametr exist*/
										if (isset($_GET['lang']) && !empty($_GET['lang']))
											echo '<input type="hidden" name="lang" value="'.esc_attr($_GET['lang']).'" />';

									?>

									<?php
										if (!empty($search_fields['enabled'])) {
											foreach ($search_fields['enabled'] as $key => $value) {

											/*Zip*/
											if ($key == 'zip')
											zoner_generate_input_field('sb-zip', __('Zip Code', 'zoner'), 'sb-zip', $add_wrapper, 'form-control', 'text', $filter_zip);

											/*Keyword*/
											if ($key == 'keyword')
											zoner_generate_input_field('sb-keyword', __('Keyword', 'zoner'), 'sb-keyword', $add_wrapper, 'form-control', 'text', $filter_keyword);

											if ($key == 'area')
											zoner_generate_input_field('sb-area', __('Min. area', 'zoner'), 'sb-area',   $add_wrapper, 'form-control', 'text', $filter_area);


											/*Categories*/
											if ($key == 'category') {
												$property_cat = array();
												$property_cat = get_terms('property_cat', $args);

												$options = array();
												if (!empty($property_cat))  {
													foreach ($property_cat as $cat) {
														$options[$cat->term_id] = $cat->name .' ('.$cat->count.')';
													}
												}
												zoner_generate_select_field('property_cat', __('Categories', 'zoner'), 'sb-cat', $options, $add_wrapper, 'property_cat', null, $filter_cat);
											}

											/*Status*/
											if ($key == 'status') {
												$property_status = array();
												$property_status = get_terms('property_status', $args);

												$options = array();
												if (!empty($property_status))  {
													foreach ($property_status as $status) {
														$options[$status->term_id] = $status->name .' ('.$status->count.')';
													}
												}
												zoner_generate_select_field('property_status', __('Status', 'zoner'), 'sb-status', $options, $add_wrapper, 'property_status', null, $filter_status);
											}

											/*Type*/
											if ($key == 'type') {
												$property_type = array();
												$property_type = get_terms('property_type', $args);
												$options = array();
												if (!empty($property_type))  {
													foreach ($property_type as $type) {
														$options[$type->term_id] = $type->name .' ('.$type->count.')';
													}
												}

												zoner_generate_select_field('property_type', __('Type', 'zoner'), 'sb-type', $options, $add_wrapper, 'property_type', null, $filter_type);
											}

											/*Country*/
											if ($key == 'country')
											zoner_generate_select_field('property_country', __('Country', 'zoner'), 'sb-country', $zoner->countries->get_specific_countries(), $add_wrapper, 'property_country', null, $filter_country);

											/*City*/
											if ($key == 'city') {
												$all_city = get_terms( 'property_city');
												$options = array();

												if (!empty($all_city))  {
													foreach ($all_city as $city) {
														$options[$city->term_id] = $city->name .' ('.$city->count.')';
													}
												}
												zoner_generate_select_field('property_city', __('City', 'zoner'), 'sb-city', $options, $add_wrapper, 'property_city', null, $filter_city);
											}

											if ($key == 'district') {
												$all_districts = $zoner->countries->zoner_get_all_metadata_($prefix.'district');
												$options = array();

												if (!empty($all_districts))  {
													foreach ($all_districts as $district) {
														$options[$district] = $district;
													}
												}

												zoner_generate_select_field('property_district', __('District', 'zoner'), 'sb-district', $options, $add_wrapper, 'property_district', null, $filter_district);
											}

											/*Price*/
											if ($key == 'price') {
													$min_price = $zoner->currency->zoner_get_price_("MIN", $prefix.'price');
													$max_price = $zoner->currency->zoner_get_price_("MAX", $prefix.'price');

													$min_price = 0;
													$price = null;

													if (!empty($_GET['sb-price'])) $price = $_GET['sb-price'];
													if (!empty($price)) {
														$filter_price 	=  explode(';', $price);
														$min_price 		= (int)$filter_price[0];
														$max_price 		= (int)$filter_price[1];
													}
												?><input type="hidden" value="yes" name="sb-price-req" /><?php
												if ($display == 0) {
													zoner_generate_price_field('price-input','price-input', 'sb-price', $min_price, $max_price);
												} else {
													$options = array();

													$options['1000;'  .$max_price] = '1000 +';
													$options['10000;' .$max_price] = '10000 +';
													$options['25000;' .$max_price] = '25000 +';
													$options['50000;' .$max_price] = '50000 +';
													$options['75000;' .$max_price] = '75000 +';
													$options['100000;'.$max_price] = '100000 +';
													$options['250000;'.$max_price] = '250000 +';
													$options['500000;'.$max_price] = '500000 +';
													zoner_generate_select_field('property_price', __('Price', 'zoner'), 'sb-price', $options, $add_wrapper, 'property_price', $min_price .';'.$max_price, $min_price .';'.$max_price);
												}
											}

											/*Сondition*/
											if ($key == 'condition') {
												$options = array();
												$options = $zoner->property->get_condition_values();
												zoner_generate_select_field('property_condition', __('Condition', 'zoner'), 'sb-condition', $options, $add_wrapper, 'property_condition', null, $condition);
											}

											/*Payment*/
											if ($key == 'payment') {
												$options = array();
												$options[1] = __('Monthly', 'zoner');
												$options[2] = __('Quarter', 'zoner');
												$options[3] = __('Yearly', 'zoner');
												zoner_generate_select_field('property_payment', __('Payment', 'zoner'), 'sb-payment', $options, $add_wrapper, 'property_payment', null, $payment);
											}


											/*Rooms*/
											if ($key == 'rooms') {
												$rooms_options = $zoner->property->zoner_get_custom_meta($prefix . 'rooms');
												$options = array();
												foreach ($rooms_options as $room) {
													$options[$room->meta_value] = $room->meta_value;
												}
												zoner_generate_select_field('property_rooms', __('Rooms', 'zoner'), 'sb-rooms', $options, $add_wrapper, 'property_rooms', null, $rooms);
											}

											/*Beds*/
											if ($key == 'beds') {
												$beds_options = $zoner->property->zoner_get_custom_meta($prefix . 'beds');
												$options = array();
												foreach ($beds_options as $bed) {
													$options[$bed->meta_value] = $bed->meta_value;
												}
												zoner_generate_select_field('property_beds', __('Beds', 'zoner'), 'sb-beds', $options, $add_wrapper, 'property_beds', null, $beds);
											}

											/*Baths*/
											if ($key == 'baths') {
												$baths_options = $zoner->property->zoner_get_custom_meta($prefix . 'baths');
												$options = array();
												foreach ($baths_options as $bath) {
													$options[$bath->meta_value] = $bath->meta_value;
												}
												zoner_generate_select_field('property_bath', __('Baths', 'zoner'), 'sb-baths', $options, $add_wrapper, 'property_baths', null, $baths);
											}

											/*Garages*/
											if ($key == 'garages') {
												$garages = $zoner->property->zoner_get_custom_meta($prefix . 'garages');
												$options = array();
												foreach ($garages as $garage) {
													$options[$garage->meta_value] = $garage->meta_value;
												}
												zoner_generate_select_field('property_garages', __('Garages', 'zoner'), 'sb-garages', $options, $add_wrapper, 'property_garages', null, $garages);
											}
											
											/*Custom Search Fields*/
											do_action('zoner_add_custom_search_fields', $key, $add_wrapper);

										}
									}


									zoner_generate_submit_btn($submit_text, $add_wrapper);
								?>


			<?php if ($display == 1) { ?>
							</div>
						</form>
					</div>
				</div>
			</div>
			<?php
				if ((($header_variations == 5) || ($header_variations == 10) || ($header_variations == 14) || ($header_variations == 18)) && !$is_shortcode)  {
					if ($bg_advanced_search) {
			?>
					<div class="background-image">
						<img class="opacity-20" src="<?php echo esc_url($bg_advanced_search); ?>">
					</div>
				<?php
					}
				}
				?>
		</div>
	<?php  } else {  ?>
							</form><!-- /#form-map -->
                        </div><!-- /.search-box.map -->
                    </div><!-- /.col-md-3 -->
                </div><!-- /.row -->
            </div><!-- /.container -->
        </div><!-- /.search-box-inner -->
    </div>
    <!-- end Search Box -->

	<?php
		}

	}
}

if ( ! function_exists( 'zoner_map_shortcode_vartical_form' ) ) {
	function zoner_map_shortcode_vartical_form($class = '', $display = 0, $is_shortcode = 0){
		global $zoner_config, $prefix, $zoner, $wp;

		if (!empty($zoner_config['zoner-searchbox']))
		$search_fields = $zoner_config['zoner-searchbox'];

		if (count($search_fields['enabled']) <= 1)
		return false;

		$perma_struct = get_option( 'permalink_structure' );
		$link 		  = get_permalink($zoner->zoner_get_page_id('page-property-archive'));
		
		$property_archive = null;
		$archivePropertyPage = $zoner->zoner_get_page_id('page-property-archive');
		if (!empty($archivePropertyPage)) {
			$property_archive = $archivePropertyPage;
		}

		if ($perma_struct == '' || empty($property_archive)) {
			$link = get_post_type_archive_link('property');
		}

		$submit_text = trim($zoner_config['zoner-searchbox-submit']);
		$submit_text = (!empty($submit_text)) ? $submit_text : __('Search Now', 'zoner');
		$submit_text = esc_html($submit_text);

		$add_wrapper = array('col-md-12', 'col-sm-12');
		$header_variations = zoner_get_header_variation_index();

		$bg_advanced_search = '';
		if (!empty($zoner_config['zoner-searchbox']))
			$bg_advanced_search = $zoner_config['zoner-searchbox-advancedimg']['url'];

		$features_include = array();
		if (!empty($zoner_config['specific-features'])) {
			$features_include = $zoner_config['specific-features'];
		}

		$args = array('orderby' => 'name', 'order' => 'ASC', 'hide_empty' => false);

		$filter_pid = $filter_zip = $filter_keyword = $filter_status = $filter_city = $filter_price = $filter_district = $filter_type = $filter_country = $filter_area = $filter_cat = '';
		$condition  = $payment = $rooms = $beds = $baths = $garages = $features =  $sorting  = $page_id = '';
		$min_price  = 0;
		if (isset($_GET) && isset($_GET['filter_property']) && wp_verify_nonce($_GET['filter_property'], 'zoner_filter_property')) {
			$min_price  = $max_price = 0;
			if (!empty($_GET['sb-zip'])) 		$filter_zip 	= $_GET['sb-zip'];
			if (!empty($_GET['sb-keyword']))	$filter_keyword	= $_GET['sb-keyword'];
			if (!empty($_GET['sb-area'])) 		$filter_area 	= $_GET['sb-area'];
			if (!empty($_GET['sb-status'])) 	$filter_status 	= $_GET['sb-status'];
			if (!empty($_GET['sb-cat'])) 		$filter_cat 	= $_GET['sb-cat'];
			if (!empty($_GET['sb-type'])) 		$filter_type 	= $_GET['sb-type'];
			if (!empty($_GET['sb-country'])) 	$filter_country	= $_GET['sb-country'];
			if (!empty($_GET['sb-city'])) 		$filter_city 	= $_GET['sb-city'];
			if (!empty($_GET['sb-district']))   $filter_district = $_GET['sb-district'];



			/*Additional fileds*/
			if (!empty($_GET['sb-condition']))	$condition 	= $_GET['sb-condition'];
			if (!empty($_GET['sb-payment'])) 	$payment 	= $_GET['sb-payment'];
			if (!empty($_GET['sb-rooms'])) 		$rooms 		= $_GET['sb-rooms'];
			if (!empty($_GET['sb-beds'])) 		$beds 		= $_GET['sb-beds'];
			if (!empty($_GET['sb-baths'])) 		$baths		= $_GET['sb-baths'];
			if (!empty($_GET['sb-garages'])) 	$garages 	= $_GET['sb-garages'];

			if (!empty($_GET['sb-features'])) 	$features 	= $_GET['sb-features'];
		}

		if (isset($_GET) && isset($_GET['sorting'])) $sorting = $_GET['sorting'];
		if (isset($_GET) && isset($_GET['page_id'])) $page_id = $_GET['page_id'];

		$search_wrap = 'search-box-wrapper zoner-dinamic-search';
		$class = array('col-md-3', 'col-sm-4');
		?>
		<div class="<?php echo $search_wrap; ?>">
			<div class="search-box-inner">
				<div class="container">
					<div class="row">
						<div class="<?php echo implode(' ', $class); ?>">
							<div class="search-box map form-vertical">
								<form role="form" id="form-map" class="form-map form-search" action="<?php echo $link; ?>" method="GET">
									<h2><?php _e('Search Your Property', 'zoner'); ?></h2>
									<?php wp_nonce_field( 'zoner_filter_property', 'filter_property', false, true ); ?>
									<?php
										if ($perma_struct == "") echo '<input type="hidden" name="post_type" value="property"/>';
										if ($sorting != '')		 echo '<input type="hidden" name="sorting" value="'.$sorting.'" />';
										if ($page_id != '' && $perma_struct != "")	echo '<input type="hidden" name="page_id" value="'.$page_id.'" />';
										if (!empty($features)) {
											foreach ($features as $f) {
												echo '<input type="hidden" name="sb-features[]" value="'.$f.'" />';
											}
										}
										/*If WPML Parametr exist*/
										if (isset($_GET['lang']) && !empty($_GET['lang']))
											echo '<input type="hidden" name="lang" value="'.esc_attr($_GET['lang']).'" />';
										if (!empty($search_fields['enabled'])) {
											foreach ($search_fields['enabled'] as $key => $value) {
												/*Zip*/
												if ($key == 'zip')
												zoner_generate_input_field('sb-zip', __('Zip Code', 'zoner'), 'sb-zip', '', 'form-control', 'text', $filter_zip);
												/*Keyword*/
												if ($key == 'keyword')
												zoner_generate_input_field('sb-keyword', __('Keyword', 'zoner'), 'sb-keyword', '', 'form-control', 'text', $filter_keyword);
												if ($key == 'area')
												zoner_generate_input_field('sb-area', __('Min. area', 'zoner'), 'sb-area',   '', 'form-control', 'text', $filter_area);
												/*Categories*/
												if ($key == 'category') {
													$property_cat = array();
													$property_cat = get_terms('property_cat', $args);
													$options = array();
													if (!empty($property_cat))  {
														foreach ($property_cat as $cat) {
															$options[$cat->term_id] = $cat->name .' ('.$cat->count.')';
														}
													}
													zoner_generate_select_field('property_cat', __('Categories', 'zoner'), 'sb-cat', $options, '', 'property_cat', null, $filter_cat);
												}
												/*Status*/
												if ($key == 'status') {
													$property_status = array();
													$property_status = get_terms('property_status', $args);
													$options = array();
													if (!empty($property_status))  {
														foreach ($property_status as $status) {
															$options[$status->term_id] = $status->name .' ('.$status->count.')';
														}
													}
													zoner_generate_select_field('property_status', __('Status', 'zoner'), 'sb-status', $options, '', 'property_status', null, $filter_status);
												}
												/*Type*/
												if ($key == 'type') {
													$property_type = array();
													$property_type = get_terms('property_type', $args);
													$options = array();
													if (!empty($property_type))  {
														foreach ($property_type as $type) {
															$options[$type->term_id] = $type->name .' ('.$type->count.')';
														}
													}
												}
												/*Country*/
												if ($key == 'country')
												zoner_generate_select_field('property_country', __('Country', 'zoner'), 'sb-country', $zoner->countries->get_specific_countries(), '', 'property_country', null, $filter_country);
												/*City*/
												if ($key == 'city') {
													$all_city = get_terms( 'property_city');
													$options = array();
													if (!empty($all_city))  {
														foreach ($all_city as $city) {
															$options[$city->term_id] = $city->name .' ('.$city->count.')';
														}
													}
													zoner_generate_select_field('property_city', __('City', 'zoner'), 'sb-city', $options, '', 'property_city', null, $filter_city);
												}
												if ($key == 'district') {
													$all_districts = $zoner->countries->zoner_get_all_metadata_($prefix.'district');
													$options = array();
													if (!empty($all_districts))  {
														foreach ($all_districts as $district) {
															$options[$district] = $district;
														}
													}
													zoner_generate_select_field('property_district', __('District', 'zoner'), 'sb-district', $options, '', 'property_district', null, $filter_district);
												}
												/*Сondition*/
												if ($key == 'condition') {
													$options = array();
													$options = $zoner->property->get_condition_values();
													zoner_generate_select_field('property_condition', __('Condition', 'zoner'), 'sb-condition', $options, '', 'property_condition', null, $condition);
												}
												/*Payment*/
												if ($key == 'payment') {
													$options = array();
													$options[1] = __('Monthly', 'zoner');
													$options[2] = __('Quarter', 'zoner');
													$options[3] = __('Yearly', 'zoner');
													zoner_generate_select_field('property_payment', __('Payment', 'zoner'), 'sb-payment', $options, '', 'property_payment', null, $payment);
												}
												/*Rooms*/
												if ($key == 'rooms') {
													$rooms_options = $zoner->property->zoner_get_custom_meta($prefix . 'rooms');
													$options = array();
													foreach ($rooms_options as $room) {
														$options[$room->meta_value] = $room->meta_value;
													}
													zoner_generate_select_field('property_rooms', __('Rooms', 'zoner'), 'sb-rooms', $options, '', 'property_rooms', null, $rooms);
												}
												/*Beds*/
												if ($key == 'beds') {
													$beds_options = $zoner->property->zoner_get_custom_meta($prefix . 'beds');
													$options = array();
													foreach ($beds_options as $bed) {
														$options[$bed->meta_value] = $bed->meta_value;
													}
													zoner_generate_select_field('property_beds', __('Beds', 'zoner'), 'sb-beds', $options, '', 'property_beds', null, $beds);
												}
												/*Baths*/
												if ($key == 'baths') {
													$baths_options = $zoner->property->zoner_get_custom_meta($prefix . 'baths');
													$options = array();
													foreach ($baths_options as $bath) {
														$options[$bath->meta_value] = $bath->meta_value;
													}
													zoner_generate_select_field('property_bath', __('Baths', 'zoner'), 'sb-baths', $options, '', 'property_baths', null, $baths);
												}
												/*Garages*/
												if ($key == 'garages') {
													$garages = $zoner->property->zoner_get_custom_meta($prefix . 'garages');
													$options = array();
													foreach ($garages as $garage) {
														$options[$garage->meta_value] = $garage->meta_value;
													}
													zoner_generate_select_field('property_garages', __('Garages', 'zoner'), 'sb-garages', $options, '', 'property_garages', null, $garages);
												}
												/*Price*/
												if ($key == 'price') {
														$min_price = $zoner->currency->zoner_get_price_("MIN", $prefix.'price');
														$max_price = $zoner->currency->zoner_get_price_("MAX", $prefix.'price');
														$min_price = 0;
														$price = null;
														if (!empty($_GET['sb-price'])) $price = $_GET['sb-price'];
														if (!empty($price)) {
															$filter_price 	=  explode(';', $price);
															$min_price 		= (int)$filter_price[0];
															$max_price 		= (int)$filter_price[1];
														}
													?><input type="hidden" value="yes" name="sb-price-req" /><?php
													if ($display == 0) {
														zoner_generate_price_field('price-input','price-input', 'sb-price', $min_price, $max_price);
													} else {
														$options = array();
														$options['1000;'  .$max_price] = '1000 +';
														$options['10000;' .$max_price] = '10000 +';
														$options['25000;' .$max_price] = '25000 +';
														$options['50000;' .$max_price] = '50000 +';
														$options['75000;' .$max_price] = '75000 +';
														$options['100000;'.$max_price] = '100000 +';
														$options['250000;'.$max_price] = '250000 +';
														$options['500000;'.$max_price] = '500000 +';
														zoner_generate_select_field('property_price', __('Price', 'zoner'), 'sb-price', $options, '', 'property_price', $min_price .';'.$max_price, $min_price .';'.$max_price);
													}
												}
											}
										}
										zoner_generate_submit_btn($submit_text, '');
									?>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
	}
}

if ( ! function_exists( 'zoner_get_home_slider' ) ) {
	function zoner_get_home_slider() {
		global $zoner_config, $zoner, $prefix;
		$out_slider = '';

		$is_only_gallery_images = false;

		$scount  = 5;
		$order   = 'DESC';
		$orderby = 'DATE';

		$specific_properties = array();

		if (!empty($zoner_config['slider-scount']) )
		$scount  = esc_attr($zoner_config['slider-scount']);
		if (!empty($zoner_config['slider-sorders']) )
		$order   = esc_attr($zoner_config['slider-sorder']);
		if (!empty($zoner_config['slider-sorderby']) )
		$orderby = esc_attr($zoner_config['slider-sorderby']);
		if (!empty($zoner_config['slider-specific-properties']) )
		$specific_properties = $zoner_config['slider-specific-properties'];
		if (!empty($zoner_config['slider-only-gallery-images']) )
		$is_only_gallery_images = $zoner_config['slider-only-gallery-images'];


		$args = array(
					'post_type' => 'property',
					'post_status' => 'publish',
					'posts_per_page' => $scount,
					'orderby' 	=> $orderby,
					'order' 	=> $order,
					'post__in'	=> $specific_properties
				  );

		$query_slides = new WP_Query($args);

		if ( $query_slides->have_posts() ) {

			$out_slider .= '<div id="slider" class="loading has-parallax">';
				$out_slider .= '<div id="loading-icon"><i class="fa fa-cog fa-spin"></i></div>';
				$out_slider .= '<div class="owl-carousel homepage-slider carousel-full-width">';

					while ( $query_slides->have_posts() ) : $query_slides->the_post();
						$id_ = get_the_ID();
						$gproperty = array();
						$gproperty = $zoner->property->get_property($id_);
						$address 	= $gproperty->address;
						$full_address = $gproperty->full_address;
						$city		= $gproperty->city;
						$zip		= $gproperty->zip;
						$price 		= $gproperty->price;
						$currency	= $gproperty->currency;
						$price_html = $gproperty->price_html;
						$gallery_images = $gproperty->prop_gallery;


						$out_image = get_template_directory_uri() . '/includes/theme/assets/img/slide.jpg';
						$image_attributes = array();

						if (!empty($gallery_images) && ($is_only_gallery_images)) {
							foreach ($gallery_images as $key => $image) {
								$image_attributes = wp_get_attachment_image_src( $key, 'zoner-home-slider');

								$out_slider .= '<div id="slide-'.$id_.$key.'" class="slide" style="background-image:url('.$image_attributes[0].');">';
									$out_slider .= '<div class="container">';
										$out_slider .= '<div class="overlay">';
											$out_slider .= '<div class="info">';
												$out_slider .= $price_html;
												$out_slider .= '<h3>'.get_the_title($id_).'</h3>';
												$out_slider .= '<figure>'.$full_address.'</figure>';
											$out_slider .= '</div>';
											$out_slider .= '<hr />';
											$out_slider .= '<a href="'.get_permalink($id_).'" class="link-arrow">'. __('Read More', 'zoner').'</a>';
										$out_slider .= '</div>';
									$out_slider .= '</div>';

   							  //$out_slider .= $out_image;
								$out_slider .= '</div>';
							}

						} else {
							if (has_post_thumbnail()) {
								$attachment_id 	  = get_post_thumbnail_id( $id_ );
								$image_attributes = wp_get_attachment_image_src( $attachment_id, 'zoner-home-slider');
								$out_image = $image_attributes[0];
							}

							$out_slider .= '<div id="slide-'.$id_.'" class="slide" style="background-image:url('.$out_image.');">';
								$out_slider .= '<div class="container">';
									$out_slider .= '<div class="overlay">';
										$out_slider .= '<div class="info">';
											$out_slider .= $price_html;
											$out_slider .= '<h3>'.get_the_title($id_).'</h3>';
											$out_slider .= '<figure>'.$full_address.'</figure>';
										$out_slider .= '</div>';
										$out_slider .= '<hr />';
										$out_slider .= '<a href="'.get_permalink($id_).'" class="link-arrow">'. __('Read More', 'zoner').'</a>';
									$out_slider .= '</div>';
								$out_slider .= '</div>';

							//$out_slider .= $out_image;
							$out_slider .= '</div>';
						}

					endwhile;
			$out_slider .= '</div>';
		$out_slider .= '</div>';
		}

		wp_reset_query();
		echo $out_slider;
 	}
}


if ( ! function_exists( 'zoner_get_rev_slider' ) ) {
	function zoner_get_rev_slider($indSlider = null) {
		global $zoner_config, $zoner, $prefix, $post;
		$out_rs = '';

		if (empty($indSlider)) {
			$rs_alias = get_post_meta(get_the_ID() , $prefix . 'page_header_slider_revolution', true);
		} else {
			$rs_alias = $indSlider;
		}

		if (!empty($rs_alias)) {
			$out_rs .= '<div id="wrapper-rs" class="wrapper-rs">';
				$out_rs .= do_shortcode ('[rev_slider "'.$rs_alias.'"]');
			$out_rs .= '</div>';
			echo $out_rs;
		}
	}
}


if ( ! function_exists( 'zoner_get_header_variations' ) ) {
	function zoner_get_header_variations() {
		global $zoner_config, $zoner, $prefix;

		$is_add_property  = get_query_var('add-property');
		if (!empty($is_add_property)) return;

		$header_variations = null;
		$header_variations = zoner_get_header_variation_index();

		$property_loop_page = 0;
		$property_loop_page = $zoner->zoner_get_page_id('page-property-archive');

		$sliderRevInd = 0;
		if (isset($zoner_config['slider-rev-index']) && is_front_page()) {
			$sliderRevInd = esc_attr($zoner_config['slider-rev-index']);
		}

		$map_class = array();
		if (($header_variations != 2) && ($header_variations != 7)) {
			$map_class[] = 'has-parallax';
		}	

		if ((is_front_page() || is_page()) || (is_post_type_archive( 'property' ) || (is_page($property_loop_page) && $property_loop_page != 0))) {
			if (($header_variations <= 10) && ($header_variations > 0)) {

		?>
				<div class="container">
					<div class="geo-location-wrapper">
						<span class="btn geo-location"><i class="fa fa-map-marker"></i><span class="text"><?php _e('Find My Position', 'zoner'); ?></span></span>
					</div>
				</div>

				<!-- Map -->
				<div id="map" class="<?php echo implode(' ', $map_class); ?>"></div>
				<!-- end Map -->

		<?php
				if (($header_variations == 4) || ($header_variations == 5) || ($header_variations == 9) || ($header_variations == 10)) {
					zoner_generate_search_box(array(), 1);
				} else {
					zoner_generate_search_box();
				}

			} elseif (($header_variations >= 11 && $header_variations <= 14) && ($header_variations > 0)) {
				if ($header_variations <= 14) {
				zoner_get_home_slider();

					if (($header_variations == 13) || ($header_variations == 14)) {
						zoner_generate_search_box(array(), 1);
					} else {
						if (($header_variations != 11) && ($header_variations != 12)) {
							zoner_generate_search_box();
						} elseif ($header_variations == 12) {
							zoner_generate_search_box(array('col-md-3', 'col-md-offset-9', 'col-sm-4', 'col-sm-offset-8'));
						}
					}
				}
			} elseif (($header_variations >= 15 && $header_variations <= 18) && ($header_variations > 0)) {
				if ($header_variations <= 18) {
					zoner_get_rev_slider($sliderRevInd);

					if (($header_variations == 17) || ($header_variations == 18)) {
						zoner_generate_search_box(array(), 1);
					} else {
						if (($header_variations != 15) && ($header_variations != 16)) {
							zoner_generate_search_box();
						} elseif ($header_variations == 16) {
							zoner_generate_search_box(array('col-md-3', 'col-md-offset-9', 'col-sm-4', 'col-sm-offset-8'));
						}
					}
				}
			}
		}
	}
}



/*Blog*/

/*Get Post Thumbnail*/
if ( ! function_exists( 'zoner_get_post_thumbnail' ) ) {
	function zoner_get_post_thumbnail() {
		global $zoner_config, $prefix, $zoner, $post;

		if ( has_post_thumbnail() && ($zoner_config['pp-thumbnail'])) {
			$attachment_id = get_post_thumbnail_id( $post->ID );
			$post_thumbnail = wp_get_attachment_image_src( $attachment_id, 'full');
		?>
			<?php if (!is_single()) { ?>
				<a href="<?php the_permalink();?>">
			<?php } ?>
				<img src="<?php echo $post_thumbnail[0]; ?>" alt="" />
			<?php if (!is_single()) { ?>
				</a>
			<?php } ?>
		<?php
		}
	}
}

/*Get title*/
if ( ! function_exists( 'zoner_get_post_title' ) ) {
	function zoner_get_post_title() {
		global $zoner_config, $prefix, $zoner;

		$sticky_icon = '';

		if (is_sticky()) $sticky_icon = '<span class="sticky-wrapper"><i class="fa fa-paperclip"></i></span>';

		if ( is_single() ) :
			the_title( '<header><h1 class="entry-title">' . $sticky_icon, '</h1></header>' );
		else :
			the_title( '<header><a href="' . esc_url( get_permalink() ) . '"><h2>'. $sticky_icon, '</h2></a></header>' );
		endif;
	}
}

/*Meta*/
if ( ! function_exists( 'zoner_get_post_meta' ) ) {
	function zoner_get_post_meta() {
		global $zoner_config, $prefix, $zoner, $post;

			$archive_year  = get_the_time('Y');
			$archive_month = get_the_time('m');

		?>
			<figure class="meta">
				<?php if ($zoner_config['pp-authors']) { ?>
					<a class="link-icon" href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' )); ?>">
						<i class="fa fa-user"></i>
						<?php the_author(); ?>
					</a>
				<?php } ?>

				<?php if ($zoner_config['pp-date']) { ?>
					<a class="link-icon" href="<?php echo get_month_link( $archive_year, $archive_month ); ?>">
						<i class="fa fa-calendar"></i>
						<?php the_time('d/m/Y'); ?>
					</a>
				<?php } ?>
				<?php edit_post_link( '<i title="' . __("Edit", 'zoner') . '" class="fa fa-pencil-square-o"></i>'.__("Edit", 'zoner'), '', '' ); ?>
				<?php
					 $tags = wp_get_post_tags( $post->ID);
					 if (!empty($tags) && ($zoner_config['pp-tags'])) {
				?>
					<div class="tags">
						<?php foreach($tags as $tag) {  ?>
							<a class="tag article" href="<?php echo get_tag_link($tag->term_id)?>"><?php echo $tag->name; ?></a>
						<?php } ?>
					</div>

				<?php } ?>

			</figure>
		<?php
	}
}

/*Content none*/
if ( ! function_exists( 'zoner_get_post_none_content' ) ) {
	function zoner_get_post_none_content() {
	?>
		<?php if ( is_home() && current_user_can( 'publish_posts' ) ) : ?>
				<p><?php printf( __( 'Ready to publish your first post? <a href="%1$s">Get started here</a>.', 'zoner' ), admin_url( 'post-new.php' ) ); ?></p>
			<?php elseif ( is_search() ) : ?>
				<p><?php _e( 'Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'zoner' ); ?></p>
			<?php get_search_form(); ?>
			<?php else : ?>
				<p><?php _e( 'It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching can help.', 'zoner' ); ?></p>
			<?php get_search_form(); ?>
		<?php endif; ?>
	<?php
	}
}

/*Single About The Author*/
if ( ! function_exists( 'zoner_get_post_about_author' ) ) {
	function zoner_get_post_about_author() {
		global $zoner_config, $prefix, $zoner, $post;

		if ($zoner_config['pp-about-author']) {
		?>

			<section id="about-author">
				<header><h3><?php _e('About the Author', 'zoner'); ?></h3></header>
				<div class="post-author">
					<?php echo zoner_get_profile_avartar(get_the_author_meta( 'ID')); ?>
					<div class="wrapper">
						<header><?php the_author(); ?></header>
						<?php the_author_meta( 'description'); ?>
					</div>
				</div>
			</section>

		<?php
		}
	}
}

/*Read More*/
if ( ! function_exists( 'zoner_get_readmore_link' ) ) {
	function zoner_get_readmore_link() {
		global $zoner_config, $prefix, $zoner, $post;
		?>
			<a class="link-arrow" href="<?php the_permalink();?>"><?php _e('Read More', 'zoner'); ?></a>
		<?php
	}
}

if ( ! function_exists( 'zoner_edit_post_link' ) ) {
	function zoner_edit_post_link($output) {
		$output = str_replace('class="post-edit-link"', 'class="link-icon"', $output);
		return $output;
	}
}


/*Add vote FAQ's*/
if ( ! function_exists( 'zoner_helpful_faq_act' ) ) {
	function zoner_helpful_faq_act() {
		global $zoner_config, $prefix, $zoner;
		 $faq_id = -1;
		 $choose = '';

		 if (isset($_POST) && ($_POST['action'] == 'zoner_helpful_faq')) {
			$faq_id = $_POST['faq_id'];
			$choose = $_POST['choose'];

			$count_yes = get_post_meta( $faq_id, $prefix .'faq_helpful_yes', true );
			$count_no  = get_post_meta( $faq_id, $prefix .'faq_helpful_no', true );

			if ($choose == 'yes') {
				if ($count_yes) {
					$count_yes++;
				} else {
					$count_yes= 1;
				}
				update_post_meta($faq_id, $prefix .'faq_helpful_yes', $count_yes);
			} else {
				if ($count_no) {
					$count_no++;
				} else {
					$count_no= 1;
				}
				update_post_meta($faq_id, $prefix .'faq_helpful_no', $count_no);
			}
		}

		die();
	}
}

/*Remove admin bar*/
if ( ! function_exists( 'zoner_options_admin_bar' ) ) {
	function zoner_options_admin_bar() {
		global $zoner_config, $prefix, $zoner;
		$vBarOptions = 1;

		if (!empty($zoner_config['adminbar-displayed'])) {
			$vBarOptions = (int) esc_attr($zoner_config['adminbar-displayed']);
		}

		if ($vBarOptions == 2) {
			if (!is_admin() && !is_super_admin())
			add_filter('show_admin_bar', '__return_false');

		} elseif ($vBarOptions == 3) {
			add_filter('show_admin_bar', '__return_false');
		}
	}
}

if ( ! function_exists( 'zoner_words_limit' ) ) {
	function zoner_words_limit($string, $word_limit) {
		$content = '';
		if (empty($string)) return '';
		$words = explode(' ', $string, ($word_limit + 1));
		if(count($words) > $word_limit) array_pop($words);
		$content = implode(' ', $words);
		$content = strip_tags($content);
		$content = strip_shortcodes($content) . '...';

		$content = preg_replace('/\[.+\]/','',  $content);
		$content = apply_filters('the_content', $content);
		$content = str_replace(']]>', ']]&gt;', $content);

		return $content;
	}
}

if ( ! function_exists( 'zoner_blog_post_preview()' ) ) {
	function zoner_blog_post_preview() {
		global $zoner_config;

		if (!empty($zoner_config['excerpt'])) {
			$num = $zoner_config['excerpt-numwords'];
			if ($zoner_config['excerpt'] == 1 ) {
				the_content();
			}
			if ($zoner_config['excerpt'] == 2 ) {
				echo zoner_words_limit(get_the_content(), $num);
			}
		}else{
			the_content();
		}
	}
}