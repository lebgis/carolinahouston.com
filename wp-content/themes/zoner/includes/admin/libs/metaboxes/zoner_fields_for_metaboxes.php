<?php

/*Adding cutom metabox filed*/
/*Made by zoner*/

add_action( 'cmb_render_custom_layout_sidebars', 'zoner_custom_layout_sidebars', 10, 2 );
function zoner_custom_layout_sidebars( $field, $meta ) {
	$layout = 0;
	$layout = $meta ? $meta : $field['default'];
    ?>
		<ul class="list-layouts">
			<li>
				<input type="radio" id="remove-all-wrappers" value="-1" name="<?php echo $field['id'];?>" <?php checked( $layout, '-1' ); ?>/>
				<img title="<?php _e('Without container (Only with Visual Composer)','zoner'); ?>" src="<?php echo CMB_META_BOX_URL . 'images/without-container.png'; ?>" alt="" />
			</li>
			<li>
				<input type="radio" id="full-width" value="1" name="<?php echo $field['id'];?>" <?php checked( $layout, '1' ); ?>/>
				<img title="<?php _e('Full width','zoner'); ?>" src="<?php echo CMB_META_BOX_URL . 'images/full.png'; ?>" alt="" />
			</li>
			<li>
				<input type="radio" id="right-sidebar" value="2" name="<?php echo $field['id'];?>" <?php checked( $layout, '2' ); ?>/>
				<img title="<?php _e('Content Right','zoner'); ?>" src="<?php echo CMB_META_BOX_URL . 'images/right.png'; ?>" alt="" />
			</li>
			<li>
				<input type="radio" id="left-sidebar" value="3" name="<?php echo $field['id'];?>" <?php checked( $layout, '3' ); ?>/>
				<img title="<?php _e('Content Left','zoner'); ?>" src="<?php echo CMB_META_BOX_URL . 'images/left.png'; ?>" alt="" />
			</li>
		</ul>
		<p class="cmb_metabox_description"><?php echo esc_attr($field['desc']); ?></p>
	<?php
}


add_action( 'admin_enqueue_scripts', 'zoner_custom_layout_sidebars_script' );
function zoner_custom_layout_sidebars_script($hook) {
	wp_register_script( 'cmb-layouts', CMB_META_BOX_URL . 'js/layout/layout.js'  );
	wp_register_style ( 'cmb-layouts', CMB_META_BOX_URL . 'js/layout/layout.css' );

	if ( $hook == 'post.php' || $hook == 'post-new.php' || $hook == 'page.php' ) {
		wp_enqueue_script( 'cmb-layouts' );
		wp_enqueue_style ( 'cmb-layouts' );
	}
}

function zoner_get_image($attachment_id, $id) {
	$out = "";
	$image_attributes = wp_get_attachment_image_src( $attachment_id, 'thumbnail');
	$image_full 	  = wp_get_attachment_image_src( $attachment_id, 'full');

	$out .= '<li class="img_status">';
		$out .= '<img id="image-'.$attachment_id.'" src="'. $image_attributes[0] .'" alt="" />';
		$out .= '<p class="cmb_remove_wrapper"><a href="#" class="cmb_remove_file_button">'. __( 'Remove Image', 'zoner' ) .'</a></p>';
		$out .= '<input type="hidden" value="'.$image_full[0].'" name="'.$id.'['.$attachment_id.']" />';
	$out .= '</li>';

	return $out;
}


function zoner_sort_glr_list($a, $b) {
    if ($a == $b) return 0;
    return ($a < $b) ? -1 : 1;
}

/*Sorting Gallery image listing*/
add_action( 'save_post',  'zoner_save_postdata', 10, 5);
function zoner_save_postdata($post_id) {
	if(defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE) return;
	if(!isset ($_POST['zoner_gallery_nonce'])) 	  return;

	if(!is_admin() || !wp_verify_nonce( $_POST['zoner_gallery_nonce'], 'zoner_gallery' ) ) return;

	if ( 'page' == $_POST['post_type'] ) {
		if ( !current_user_can( 'edit_page', $post_id ) )
			return;
	} else {
		if ( !current_user_can( 'edit_post', $post_id ) )
			return;
	}
	$gallery_keys = array();

	if (!empty($_POST['field_name_sorting'])) {
		$field_name = $_POST['field_name_sorting'];

		foreach ($field_name as $field) {
			$old_gallery_data = get_post_meta($post_id, $field, true);

			if(isset($_POST[$field]) && !empty($_POST[$field])) {
				$new_data = $_POST[$field];

				if (is_array($new_data)) {
					$gallery_keys = array_keys($new_data);
					usort($gallery_keys, 'zoner_sort_glr_list');
				}
				zoner_save_meta_data($post_id, $gallery_keys, $old_gallery_data, $field);
			}

		}
	}
}

function zoner_save_meta_data($post_id, $new_data, $old_data, $name){
	if ($new_data == $old_data){
		add_post_meta($post_id, $name, $new_data, true);
	} else if(!$new_data){
		delete_post_meta($post_id, $name, $old_data);
	} else if($new_data != $old_data){
		update_post_meta($post_id, $name, $new_data, $old_data);
	}
	return;
}

add_action( 'wp_ajax_zoner_add_new_element_action', 'zoner_add_new_element');
function zoner_add_new_element() {
	$out = "";
	if(!is_admin() || !wp_verify_nonce( $_POST['zoner_ajax_nonce'], 'zoner_add_img_ajax_nonce' ) ) {
		return;
	}

	$image_url  = $_POST['image_url'];
	$image_id   = $_POST['image_id'];
	$field_name = $_POST['field_name'];

	$image_attributes = wp_get_attachment_image_src( $image_id, 'thumbnail');
	$image_full 	  = wp_get_attachment_image_src( $image_id, 'full');

	$out .= '<li class="img_status">';
		$out .= '<img id="image-'.$image_id.'" src="'. $image_attributes[0] .'" alt="" />';
		$out .= '<p class="cmb_remove_wrapper"><a href="#" class="cmb_remove_file_button">'. __( 'Remove Image', 'zoner' ) .'</a></p>';
		$out .= '<input type="hidden" value="'.$image_full[0].'" name="'.$field_name.'['.$image_id.']" />';
	$out .= '</li>';

	echo $out;
	die();
}


add_action( 'cmb_render_custom_gallery_list', 'zoner_custom_gallery_list', 10, 2 );
function zoner_custom_gallery_list( $field, $meta) {
	$out = $gallery_items = '';
	$gallery_data = array();
	$id_field = $field['id'];

	if (!empty($meta) && is_array($meta)) {
		$gallery_data = $meta;
	}
	$j = 0;

	if (!empty($gallery_data)) {
		foreach($gallery_data as $key => $value) {
			$gallery_items .= zoner_get_image($key, $id_field);
			$j++;
		}
	}

	wp_nonce_field('zoner_gallery', 'zoner_gallery_nonce' );
	$out .= '<input type="hidden" value="'.$id_field.'" name="field_name_sorting[]" />';
	$out .= '<input type="button" class="button add_gallery_items_button" value="'. __('Add Images', 'zoner') .'"/>';
	$out .= '<div class="soratble-inner">';
		$out .= '<ul id="'.$id_field.'" class="sortable-admin-gallery cmb_media_status attach_list">';
			$out .= $gallery_items;
		$out .= '</ul>';
	$out .= '</div>';

	echo $out;

}

add_action( 'admin_enqueue_scripts', 'zoner_custom_gallery_list_script' );
function zoner_custom_gallery_list_script($hook) {
	if ( $hook == 'post.php' || $hook == 'post-new.php' || $hook == 'page-new.php' || $hook == 'page.php' ) {
		if(function_exists( 'wp_enqueue_media' )) {
			wp_enqueue_media();
		} else {
			wp_enqueue_style ('thickbox');
			wp_enqueue_script('media-upload');
			wp_enqueue_script('thickbox');
		}

		wp_enqueue_script	( 'zoner-gallery-js',  CMB_META_BOX_URL  . 'js/gallery/gallery-init.js',  array('jquery'));
		wp_enqueue_style	( 'zoner-gallery-css', CMB_META_BOX_URL  . 'js/gallery/gallery-admin.css' );
		wp_localize_script	( 'zoner-gallery-js',  'zoner_vars_ajax', array(
															'ajaxurl' 	=> admin_url( 'admin-ajax.php' ),
															'ajax_nonce' 	=> wp_create_nonce( 'zoner_add_img_ajax_nonce' ),
												));
	}
}


/*Locations*/

add_action( 'cmb_render_country', 'cmb_render_country_field', 10, 5 );
/**
 * Render Address Field
 */
function cmb_render_country_field( $field_args, $value, $object_id, $object_type, $field_type_object ) {
	global $post, $prefix;
	$country_options = '';
	$country_options = ZONER_GO()->countries->country_dropdown_list($value);

	echo $field_type_object->select( array(
			'class'   => 'countries_select',
			'options' => $country_options,
		 ));

    echo $field_type_object->_desc( true );
}
add_action( 'cmb_render_state', 'cmb_render_state_field', 10, 5 );

/**
 * Render Address Field
 */
function cmb_render_state_field( $field_args, $value, $object_id, $object_type, $field_type_object ) {
	global $post, $prefix;
	$states_options  = '';

	$country_value   = get_post_meta($post->ID, $prefix . 'country', true);
	if (isset($country_value)) $states_options  = ZONER_GO()->countries->states_dropdown_list ($country_value, $value);

    echo $field_type_object->select( array(
		'class'   => 'states_select',
		'options' => $states_options,
	) );

    echo $field_type_object->_desc( true );
}



add_action( 'wp_ajax_zoner_change_countries', 'zoner_change_countries');
function zoner_change_countries() {
	$out = "";
	$country  = $_POST['country'];
	echo json_encode(ZONER_GO()->countries->states_array_list ($country, ''));

	die();
}


/*Map Field*/

add_filter( 'cmb_render_zoner_custom_map', 'zoner_custom_map', 10, 2 );

function zoner_custom_map( $field, $meta) {
	global  $zoner_config, $prefix;
	$default_lat = $default_lng =  $desc = $meta_lat = $meta_lng = '';
	$zoom = 5;

	$out_html_field = '';

	$lat = esc_js($zoner_config['geo-center-lat']);
	$lng = esc_js($zoner_config['geo-center-lng']);

	if (!empty($field['default_zoom']))
		$zoom = (int) $field['default_zoom'];

	if (!empty($field['desc']))
		$desc = $field['desc'];

	$currentLang = substr(get_bloginfo( 'language' ), 0, 2);
	if ( isset($zoner_config['google-maps-api-key']) && !empty($zoner_config['google-maps-api-key']) ) {
		wp_enqueue_script(  'zoner_google_maps_api', '//maps.googleapis.com/maps/api/js?v=3&libraries=places&language=' . $currentLang . '&key=' . $zoner_config['google-maps-api-key'], array(),    null );
	} else {
		wp_enqueue_script(  'zoner_google_maps_api', '//maps.googleapis.com/maps/api/js?v=3&libraries=places&language=' . $currentLang, array(),    null );
	}
	wp_enqueue_script(  'zoner_google_maps_init', CMB_META_BOX_URL . 'js/map/map-script.js',  array( 'zoner_google_maps_api' ), null );
	wp_localize_script( 'zoner_google_maps_init',  'zoner_maps_metabox_vars', array(
														'ajaxurl' 	=> admin_url( 'admin-ajax.php' ),
														'default_lat'  => $lat,
														'default_lng'  => $lng,
														'default_zoom' => $zoom,
														'map_title'	   => esc_js($desc)
													));

	wp_enqueue_style ( 'zoner_google_maps_css',  CMB_META_BOX_URL . 'js/map/map-style.css',  array(), null );

	$out_html_field .= '<input type="text" class="large-text map-search" id="' . $field['id'] . '" />';
	$out_html_field .= '<div id="admin-map" class="admin-map"></div>';
	if ( ! empty( $desc ) ) echo '<p class="cmb_metabox_description">' . $desc . '</p>';

	if (!empty( $meta['lat'] )) {
		$meta_lat = $meta['lat'];
	} else {
		if (get_post_meta( get_the_ID(), $prefix . 'lat', true )) {
			$meta_lat = get_post_meta( get_the_ID(), $prefix . 'lat', true );
		}
    }

	if (!empty( $meta['lng'] )) {
		$meta_lng = $meta['lng'];
	} else {
		if (get_post_meta( get_the_ID(), $prefix . 'lng', true )) {
			$meta_lng = get_post_meta( get_the_ID(), $prefix . 'lng', true );
		}
    }

	$out_html_field .= '<input type="text" class="lat cmb_text_medium"   name="' . $field['id'] . '[lat]"  value="' . $meta_lat . '" placeholder="'.__(' Latitude', 'zoner').'" />';
	$out_html_field .= '<input type="text" class="lng cmb_text_medium"   name="' . $field['id'] . '[lng]"  value="' . $meta_lng . '" placeholder="'.__('Longitude', 'zoner').'" />';
	echo $out_html_field;
}

/**
 * Update latitude/longitude values into meta fields
 */
function zoner_custom_map_sanitise( $meta_value, $field ) {
	global $prefix;
	$latitude  = $meta_value['lat'];
	$longitude = $meta_value['lng'];

	if (!empty($latitude))  update_post_meta( get_the_ID(), $prefix . 'lat', $latitude);
	if (!empty($longitude)) update_post_meta( get_the_ID(), $prefix . 'lng', $longitude);
	return $meta_value;
}


add_filter( 'cmb_render_zoner_multiselect', 'zoner_multiselect', 10, 2 );

function zoner_multiselect( $field, $meta ) {
	$options = array();
	$out_multiselect = '';

	if ( isset(   $field['options'] ) && ! empty( $field['options'] ) ) {
		foreach ( $field['options'] as $option_key => $option ) {
			$opt_label = is_array( $option ) && array_key_exists( 'name',  $option  )  ? $option['name'] : $option;
			$opt_value = is_array( $option ) && array_key_exists( 'value', $option ) ? $option['value'] : $option_key;

			$options[] = array(
				'id' 	=> $opt_value,
				'text' 	=> $opt_label
			);
		}
	}

	$out_multiselect .= '<select  multiple="multiple" name="' . $field['id'] . '[]" id="' . $field['id'] . '" class="zoner_multiselect select" >';
		if (!empty($options)) {
			foreach ($options as $opt) {
				$selected = false;

				if (!empty($meta)) {
					if ($selected = in_array($opt['id'], $meta)) {
						if ($selected) $selected = 'selected="selected"';
					}
				}

				$out_multiselect .= '<option value="'.$opt['id'].'" '.$selected.'>'.$opt['text'].'</option>';
			}
		}
	$out_multiselect .= '</select>';
	echo $out_multiselect;
}

/*Exclude from front page metaboxes filter*/
function zoner_metabox_exclude_front_page( $display, $meta_box ) {
    if ( isset( $_GET['post'] ) ) {
        $post_id = $_GET['post'];
    } elseif ( isset( $_POST['post_ID'] ) ) {
        $post_id = $_POST['post_ID'];
    }

    if( !isset( $post_id ) ) return true;

    $front_page = get_option('page_on_front');
	if ( ($post_id == $front_page) && ('front-page' == $meta_box['show_on']['key']) ) {
        return false;
    } else {
		return $display;
	}
}

add_filter( 'cmb_show_on', 'zoner_metabox_exclude_front_page', 10, 2 );
