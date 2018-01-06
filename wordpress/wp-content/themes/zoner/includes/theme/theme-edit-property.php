<?php
if ( ! function_exists( 'zoner_property_scripts' ) ) {
	function zoner_property_scripts() {
		global 	$inc_theme_url, $zoner_config, $post, $prefix;

		$is_mobile = false;
		if (!empty($post)) $post_id = $post->ID;
		if ( wp_is_mobile()) $is_mobile = true;

		$is_edit_property = get_query_var('edit-property');
		$is_add_property  = get_query_var('add-property');

		if ((!empty($is_edit_property) || !empty($is_add_property)) && is_user_logged_in()) {
			$gg_marker 	= $zoner_config['prop-ggmaps-marker'];

			$lat = $lng = '';
			if (!empty($post)) {
				$lat = get_post_meta($post_id, $prefix.'lat', true);
				$lng = get_post_meta($post_id, $prefix.'lng', true);
			}

			if (!empty($zoner_config['single-prop-ggmaps-marker'])) $gg_marker 	= $zoner_config['single-prop-ggmaps-marker'];
			if (empty($lat) && !empty($zoner_config['single-geo-center-lat'])) $lat = $zoner_config['single-geo-center-lat'];
			if (empty($lng) && !empty($zoner_config['single-geo-center-lng'])) $lng = $zoner_config['single-geo-center-lng'];
			if (!empty($zoner_config['single-maps-global-zoom'])) $single_zoom	= esc_attr($zoner_config['single-maps-global-zoom']);

			wp_enqueue_script ( 'jquery-ui-sortable' );
			wp_enqueue_script ( 'zoner-edit-property', $inc_theme_url . 'assets/js/edit-property.min.js', array( 'jquery' ), '20142807', true );
			wp_localize_script ( 'zoner-edit-property', 'ZonerEproperty', array( 	
					'_latitude'			=> esc_js($lat),
					'_longitude'		=> esc_js($lng),
					'_icon_marker'	=> esc_js($gg_marker['url']),
					'_error_text'		=> esc_js(__('Geo Location is not supported', 'zoner')),
					'_single_zoom'	=> esc_js($single_zoom),
				)
		  );
		}
	}
}


	if ( ! function_exists( 'zoner_get_property_edit_features' ) ) {
		function zoner_get_property_edit_features($post_id = -1) {
			global $post, $prefix, $zoner;

			$taxonomies = array( 'property_features' );
			$args = array(
							'orderby' 	 => 'name',
							'order' 	 => 'ASC',
							'hide_empty' => false
			);

			$prop_features = array();
			$prop_features = get_terms($taxonomies,  $args);

			?>
				<ul class="submit-features">
					<?php
						 if (!empty($prop_features)) {
							 foreach ($prop_features as $vals) {
								$is_cheked = false;
								$is_cheked = has_term( $vals->term_id, 'property_features', $post_id);

					?>
							<li><div class="checkbox"><label><input type="checkbox" name="features[]" value="<?php echo $vals->term_id; ?>" <?php checked( $is_cheked, true, true ); ?>><?php echo $vals->name; ?></label></div></li>
					<?php
							 }
						 }
					?>
				</ul>
			<?php
		}
	}


	if ( ! function_exists( 'zoner_generate_select_' ) ) {
		function zoner_generate_select_($args = array()) {
			global $post, $prefix, $zoner;

				if (!empty($args)) {
					$id_container = '';
					if (!empty($args['id_container'])) $id_container = $args['id_container'];

			?>
					<div id="<?php echo $id_container; ?>" class="form-group">
						<label for="<?php echo $args['for']; ?>"><?php echo $args['label']; ?></label>
						<select name="<?php echo $args['name']; ?>" id="<?php echo $args['id']; ?>" class="<?php echo implode(' ',  $args['class']); ?>">
							<?php
								if (!empty($args['items'])) {
									foreach($args['items'] as $key => $val) {
										echo '<option value="'.$key.'" '.selected( $args['selected'], $key, false).'>'.$val.'</option>';
									}
								}

							?>
						</select>
					</div>
			<?php

				}
		}
	}

	if ( ! function_exists( 'zoner_get_property_types' ) ) {
		function zoner_get_property_types() {
			global $post, $prefix, $zoner;

			$taxonomies = array( 'property_type' );
			$args = array(
							'orderby' => 'name',
							'order' => 'ASC',
							'hide_empty' => false
			);

			$prop_type = array();
			$prop_type = get_terms($taxonomies,  $args);
			$out_arr = array();

			if (!empty($prop_type)) {
				$out_arr[-1] = __('No types', 'zoner');
				foreach ($prop_type as $type) {
					$out_arr[$type->term_id] = $type->name;
				}
			}

			return $out_arr;
		}
	}

	if ( ! function_exists( 'zoner_get_property_status' ) ) {
		function zoner_get_property_status() {
			global $post, $prefix, $zoner;

			$taxonomies = array( 'property_status' );
			$args = array(
							'orderby' => 'name',
							'order' => 'ASC',
							'hide_empty' => false
			);

			$prop_status = array();
			$prop_status = get_terms($taxonomies,  $args);
			$out_arr = array();

			if (!empty($prop_status)) {
				$out_arr[-1] = __('No status', 'zoner');
				foreach ($prop_status as $status) {
					$out_arr[$status->term_id] = $status->name;
				}
			}

			return $out_arr;
		}
	}


	if ( ! function_exists( 'zoner_get_curr_property_type' ) ) {
		function zoner_get_curr_property_type() {
			global $post, $prefix, $zoner;

			$curr_prop_type   = wp_get_post_terms($post->ID, 'property_type',   array('orderby' => 'name', 'hide_empty' => true) );
			$curr_val = null;
			if (!empty($curr_prop_type)) {
				foreach ($curr_prop_type as $type) {
					$curr_val = $type->term_id;
					break;
				}
			}

			return $curr_val;
		}

	}

	if ( ! function_exists( 'zoner_get_curr_property_status' ) ) {
		function zoner_get_curr_property_status() {
			global $post, $prefix, $zoner;

			$curr_prop_status = wp_get_post_terms($post->ID, 'property_status', array('orderby' => 'name', 'hide_empty' => true) );
			$curr_val = null;
			if (!empty($curr_prop_status)) {
				foreach ($curr_prop_status as $status) {
					$curr_val = $status->term_id;
					break;
				}
			}

			return $curr_val;
		}
	}

	if ( ! function_exists( 'zoner_change_code_currency' ) ) {
		function zoner_change_code_currency() {
			global $zoner;
			if (isset($_POST) && isset($_POST['curr_val']) && ($_POST['action'] == 'change_code_currency')) {
				$currency = $_POST['curr_val'];
				$currency = $zoner->currency->get_zoner_currency_symbol($currency);
				echo $currency;
			}
			die();
		}
	}

	if ( ! function_exists( 'zoner_edit_property' ) ) {
		function zoner_edit_property() {
			global $zoner_config, $prefix, $zoner, $post;
			$update_post = array();
            $no_errors=$zoner->validate->check('property');
			if (isset($_POST) && isset($_POST['edit-property']) && wp_verify_nonce($_POST['edit-property'], 'zoner_edit_property') && $no_errors) {
				$update_post = array(
						'ID' => $post->ID,
						'post_content' => wp_filter_post_kses($_POST['description']),
						'post_title'   => $_POST['title']
				);
				wp_update_post( $update_post );

				update_post_meta($post->ID, $prefix . 'currency', $_POST['currency']);
				update_post_meta($post->ID, $prefix . 'price_format', $_POST['price_format']);
				update_post_meta($post->ID, $prefix . 'price', 	 $_POST['price']);


				$country = $state = '';
				if (isset($_POST['country'])) $country = esc_attr($_POST['country']);
				if (isset($_POST['state'])) $state     = esc_attr($_POST['state']);

				update_post_meta($post->ID, $prefix . 'country', $country);
				update_post_meta($post->ID, $prefix . 'state', $state);

				update_post_meta($post->ID, $prefix . 'address', $_POST['address']);
				
				if (!empty($_POST['city'])) {
					$city = (int) $_POST['city'];
					wp_delete_term	 ( $post->ID, 'property_city');
					wp_set_post_terms( $post->ID, $city, 'property_city');
				}
				
				if (!empty($_POST['district']) && isset($_POST['district'])) {
					update_post_meta($post->ID, $prefix . 'district', 	esc_attr($_POST['district']));
				} else {
					update_post_meta($post->ID, $prefix . 'district', 	esc_attr($_POST['district']));
				}
				update_post_meta($post->ID, $prefix . 'zip',	  esc_attr($_POST['zip']));

				if (!empty($_POST['latitude']))  update_post_meta($post->ID, $prefix . 'lat',	 $_POST['latitude']);
				if (!empty($_POST['longitude'])) update_post_meta($post->ID, $prefix . 'lng', 	 $_POST['longitude']);
				if (!empty($_POST['latitude']) && !empty($_POST['longitude']))
				update_post_meta($post->ID, $prefix . 'geo_location',
						array('lat' => $_POST['latitude'],
							  'lng' => $_POST['longitude'])
							  );

				update_post_meta($post->ID, $prefix . 'location', 	$_POST['location']);
				update_post_meta($post->ID, $prefix . 'condition', 	$_POST['condition']);
				update_post_meta($post->ID, $prefix . 'payment',	$_POST['payment-rent']);
				update_post_meta($post->ID, $prefix . 'rooms',	 $_POST['rooms']);
				update_post_meta($post->ID, $prefix . 'beds',	 $_POST['beds']);
				update_post_meta($post->ID, $prefix . 'baths',	 $_POST['baths']);
				update_post_meta($post->ID, $prefix . 'area',	 $_POST['area']);
				update_post_meta($post->ID, $prefix . 'area_unit', esc_attr($_POST['area_unit']));
				update_post_meta($post->ID, $prefix . 'garages', $_POST['garages']);
				if (isset($_POST['allow-user-rating'])) {
					if ($_POST['allow-user-rating'] == 'on') {
						update_post_meta($post->ID, $prefix . 'allow_raiting', esc_attr($_POST['allow-user-rating']));
					}
				} else {
					update_post_meta($post->ID, $prefix . 'allow_raiting', null);
				}
				if (isset($_POST['show_on_map'])) {
					if ($_POST['show_on_map'] == 'on') {
						update_post_meta($post->ID, $prefix . 'show_on_map', esc_attr($_POST['show_on_map']));
					}
				} else {
					update_post_meta($post->ID, $prefix . 'show_on_map', 'off');
				}

				if (isset($_POST['type'])) {
					wp_set_post_terms($post->ID, array($_POST['type']),'property_type');
				} else {
					wp_set_post_terms($post->ID, null,'property_type');
				}

				if (isset($_POST['status'])) {
					wp_set_post_terms($post->ID, array($_POST['status']),'property_status');
				} else {
					wp_set_post_terms($post->ID, null,'property_status');
				}

				if (!empty($_POST['features'])) {
					$features_ids = array_map('intval', $_POST['features']);
					$features_ids = array_unique( $features_ids );
					wp_set_post_terms($post->ID,  $features_ids,'property_features');
				} else {
					wp_set_post_terms($post->ID, array(),'property_features');
				}

				if (!empty($_POST['videos'])) {
					$videos = $insert_array = array();
					$videos = $_POST['videos'];

					foreach ($videos as $vid)  {
						if (!empty($vid)) $insert_array[] = array($prefix.'link_video' => $vid);
					}

					if (!empty($insert_array))
						update_post_meta($post->ID, $prefix.'videos', $insert_array);
				} else {
					update_post_meta($post->ID, $prefix.'videos', null);
				}
				
				
				/*Save custom fields*/	
				$zoner_update_custom_fields = apply_filters('zoner_update_custom_fields', array());
				if (!empty($zoner_update_custom_fields)) {
					foreach($zoner_update_custom_fields as $field) {
						if (isset($_POST[$field]) && !empty($_POST[$field])) {
							update_post_meta($post->ID, $field, $_POST[$field]);
						} else {
							update_post_meta($post->ID, $field, null);
						}
					}
				}
				
				/*Featured image*/
				if (empty($_POST['prop-featured-image-exists']))
					delete_post_thumbnail($post->ID);

				if (!empty($_FILES['prop-featured-image']['name'])) {
					$attach_id = zoner_insert_attachment( 'prop-featured-image', $post->ID, true );
				}
				
				/*Gallery attachments*/
				$exists_files  = $post_files = array();
				$field_name    = $prefix.'gallery';
				$gallery_files = get_post_meta($post->ID, $field_name, true);

				if (!empty($_POST[$prefix. 'exist_gallery']))
					$exists_files = $_POST[$prefix. 'exist_gallery'];

				if (!empty($_FILES['gallery']['name']))
					$post_files   = $_FILES['gallery'];

				zoner_edit_attachments($field_name, $gallery_files, $exists_files, $post_files, 'gallery');
				
				/*Plans attachments*/
				$exists_files 	= $post_files = array();
				$field_name 	= $prefix.'plans';
				$gallery_files 	= get_post_meta($post->ID, $field_name, true);

				if (!empty($_POST[$prefix. 'exist_plans']))
					$exists_files = $_POST[$prefix. 'exist_plans'];

				if (!empty($_FILES['floorplans']['name']))
					$post_files   = $_FILES['floorplans'];

				zoner_edit_attachments($field_name, $gallery_files, $exists_files, $post_files, 'floorplans');


				/*Files attachments*/
				$exists_files 	= $post_files = array();
				$field_name 	= $prefix.'files';
				$gallery_files 	= get_post_meta($post->ID, $field_name, true);

				if (!empty($_POST[$prefix. 'exist_files']))
					$exists_files = $_POST[$prefix. 'exist_files'];

				if (!empty($_FILES['files']['name']))
					$post_files   = $_FILES['files'];

				zoner_edit_attachments($field_name, $gallery_files, $exists_files, $post_files, 'files', null, array("pdf", "txt", "zip", "rar", "tar", "xls", "xlsx", "doc", "docx"));

			}
			if (!empty($_POST) && isset($_POST['edit-property'])){
                if ($no_errors)
					wp_safe_redirect( zoner_curPageURL() );
            }
		}
	}

	if ( ! function_exists( 'zoner_insert_property' ) ) {
		function zoner_insert_property() {
			global $zoner_config, $prefix, $zoner;
			$update_post = array();
            $no_errors = $zoner->validate->check('property');
			if (isset($_POST) && isset($_POST['add_property']) && wp_verify_nonce($_POST['add_property'], 'zoner_add_property') && $no_errors) {
				$status_property = 'zoner-pending';
				if (is_admin() || is_super_admin() || !$zoner_config['property-admin-confirmation']) {
					$status_property = 'publish';
				}

				$insert_property = array();
				$insert_property = array(
						'post_title'   => $_POST['title'],
						'post_name'	   => sanitize_title_with_dashes($_POST['title'], '', 'save'),
						'post_content' => wp_filter_post_kses($_POST['description']),
						'post_status'  => $status_property,
						'post_author'  => get_current_user_id(),
						'post_type'	   => 'property'
				);

				$post_id = 0;
				$post_id = wp_insert_post( $insert_property );
				update_post_meta($post_id, $prefix . 'currency', $_POST['currency']);
				update_post_meta($post_id, $prefix . 'price_format', $_POST['price_format']);
				update_post_meta($post_id, $prefix . 'price', 	 $_POST['price']);

				$country = $state = '';
				if (!empty($_POST['country'])) $country = esc_attr($_POST['country']);
				if (!empty($_POST['state']))     $state = esc_attr($_POST['state']);

				update_post_meta($post_id, $prefix . 'country', 	$country);
				update_post_meta($post_id, $prefix . 'state', 		$state);
				update_post_meta($post_id, $prefix . 'address', 	$_POST['address']);
				
				if (!empty($_POST['city'])) {
					$city = (int) esc_attr($_POST['city']);
					wp_delete_term	 ( $post_id, 'property_city' );
					wp_set_post_terms( $post_id, $city, 'property_city');
				}
				
				if (!empty($_POST['district']) && isset($_POST['district'])) {
					update_post_meta($post_id, $prefix . 'district', 	esc_attr($_POST['district']));
				} else {
					update_post_meta($post_id, $prefix . 'district', 	esc_attr($_POST['district']));
				}
				update_post_meta($post_id, $prefix . 'zip',	 		esc_attr($_POST['zip']));
				update_post_meta($post_id, $prefix . 'is_featured', 	'off');

				if (!empty($zoner_config['paid-system']) && ($zoner_config['paid-system'] == 1)) {
					if (!empty($zoner_config['paid-type-properties']) && ($zoner_config['paid-type-properties'] == 0)) {
						update_post_meta($post_id, $prefix . 'is_paid', 'on');
					} else {
						update_post_meta($post_id, $prefix . 'is_paid', 'off');
					}
				} else {
					update_post_meta($post_id, $prefix . 'is_paid', 	'on');
				}

				if (!empty($_POST['latitude']))
				update_post_meta($post_id, $prefix . 'lat',	 $_POST['latitude']);
				if (!empty($_POST['longitude']))
				update_post_meta($post_id, $prefix . 'lng', 	 $_POST['longitude']);
				if (!empty($_POST['latitude']) && !empty($_POST['longitude']))
				update_post_meta($post_id, $prefix . 'geo_location',
						array('lat' => $_POST['latitude'],
							  'lng' => $_POST['longitude'])
							  );

				update_post_meta($post_id, $prefix . 'location', 	$_POST['location']);
				update_post_meta($post_id, $prefix . 'condition', 	$_POST['condition']);
				update_post_meta($post_id, $prefix . 'payment',		$_POST['payment-rent']);
				update_post_meta($post_id, $prefix . 'rooms',		$_POST['rooms']);
				update_post_meta($post_id, $prefix . 'beds',		$_POST['beds']);
				update_post_meta($post_id, $prefix . 'baths',		$_POST['baths']);
				update_post_meta($post_id, $prefix . 'area', 		$_POST['area']);
				update_post_meta($post_id, $prefix . 'area_unit', 		esc_attr($_POST['area_unit']));
				update_post_meta($post_id, $prefix . 'garages',		$_POST['garages']);
				update_post_meta($post_id, $prefix . 'avg_rating', -1);

				if (isset($_POST['allow-user-rating'])) {
					if ($_POST['allow-user-rating'] == 'on') {
						update_post_meta($post_id, $prefix . 'allow_raiting', esc_attr($_POST['allow-user-rating']));
					}
				} else {
					update_post_meta($post_id, $prefix . 'allow_raiting', null);
				}
				if (isset($_POST['show_on_map'])) {
					if ($_POST['show_on_map'] == 'on') {
						update_post_meta($post_id, $prefix . 'show_on_map', esc_attr($_POST['show_on_map']));
					}
				} else {
					update_post_meta($post_id, $prefix . 'allow_raiting', null);
				}
				if (isset($_POST['type'])) {
					wp_set_post_terms($post_id, array($_POST['type']),'property_type');
				} else {
					wp_set_post_terms($post_id, null,'property_type');
				}

				if (isset($_POST['status'])) {
					wp_set_post_terms($post_id, array($_POST['status']),'property_status');
				} else {
					wp_set_post_terms($post_id, null,'property_status');
				}

				if (!empty($_POST['features'])) {
					$features_ids = array_map('intval', $_POST['features']);
					$features_ids = array_unique( $features_ids );
					wp_set_post_terms($post_id, $features_ids,'property_features');
				} else {
					wp_set_post_terms($post_id, null,'property_features');
				}


				if (!empty($_POST['videos'])) {
					$videos = $insert_array = array();
					$videos = $_POST['videos'];

					foreach ($videos as $vid)  {
						if (!empty($vid)) $insert_array[] = array($prefix.'link_video' => $vid);
					}

					if (!empty($insert_array))
						update_post_meta($post_id, $prefix.'videos', $insert_array);
				} else {
					update_post_meta($post_id, $prefix.'videos', null);
				}


				/*Featured image*/
				if (empty($_POST['prop-featured-image-exists']))
					delete_post_thumbnail($post_id);

				if (!empty($_FILES['prop-featured-image']['name'])) {
					$attach_id = zoner_insert_attachment( 'prop-featured-image', $post_id, true );
				}

				/*Gallery attachments*/
				$exists_files  = $post_files = array();
				$field_name    = $prefix.'gallery';
				$gallery_files = get_post_meta($post_id, $field_name, true);

				if (!empty($_POST[$prefix. 'exist_gallery']))
					$exists_files = $_POST[$prefix. 'exist_gallery'];

				if (!empty($_FILES['gallery']['name']))
					$post_files   = $_FILES['gallery'];

				zoner_edit_attachments($field_name, $gallery_files, $exists_files, $post_files, 'gallery', $post_id);


				/*Plans attachments*/
				$exists_files 	= $post_files = array();
				$field_name 	= $prefix.'plans';
				$gallery_files 	= get_post_meta($post_id, $field_name, true);

				if (!empty($_POST[$prefix. 'exist_plans']))
					$exists_files = $_POST[$prefix. 'exist_plans'];

				if (!empty($_FILES['floorplans']['name']))
					$post_files   = $_FILES['floorplans'];

				zoner_edit_attachments($field_name, $gallery_files, $exists_files, $post_files, 'floorplans', $post_id);

				/*Files attachments*/
				$exists_files 	= $post_files = array();
				$field_name 	= $prefix.'files';
				$gallery_files 	= get_post_meta($post_id, $field_name, true);

				if (!empty($_POST[$prefix. 'exist_files']))
					$exists_files = $_POST[$prefix. 'exist_files'];

				if (!empty($_FILES['files']['name']))
					$post_files   = $_FILES['files'];

				zoner_edit_attachments($field_name, $gallery_files, $exists_files, $post_files, 'files', $post_id, array("pdf", "txt", "zip", "rar", "tar", "xls", "xlsx", "doc", "docx"));
				
				
				/*Save custom fields*/	
				$zoner_update_custom_fields = apply_filters('zoner_update_custom_fields', array());
				if (!empty($zoner_update_custom_fields)) {
					foreach($zoner_update_custom_fields as $field) {
						if (isset($_POST[$field]) && !empty($_POST[$field])) {
							update_post_meta($post_id, $field, $_POST[$field]);
						} else {
							update_post_meta($post_id, $field, null);
						}
					}
				}
			}

			$redirect_link = '';
			if (!empty($_POST) && isset($_POST['add_property']) && $no_errors) {
				$curr_user = get_current_user_id();
				$page_tasp = $zoner->zoner_get_page_id('page-tasp');
				if (!empty($page_tasp)) {
					$redirect_link = get_permalink($page_tasp);
				} else {
					$redirect_link = add_query_arg(array('profile-page' => 'my_properties'), get_author_posts_url($curr_user));
				}
                if ($no_errors)
				    wp_redirect($redirect_link);
				exit;
			}
		}
	}


	function zoner_edit_attachments($field_name = null, $gallery_files = array(), $exists_files = array(), $post_files = array(), $files_array_prefix = null, $post_id = null, $valid_formats = array("jpg", "png", "gif" ), $max_file_size = "4MB") {
		global $post;
		$max_file_size = wp_convert_hr_to_bytes('4MB');
		$attachmnets = $attach_id = $message = array();
		if (empty($post_id)) $post_id = $post->ID;

		if (!empty($exists_files)) {
			$results = array_diff($gallery_files, $exists_files);

			if (!empty($results)) {
				foreach ($results as $res => $val) {
					wp_delete_attachment( $res, true );
					unset($gallery_files[$res]);
				}
			}
		} else {
			if (!empty($gallery_files)) {
				foreach ($gallery_files as $res => $val) {
					wp_delete_attachment( $res, true );
					unset($gallery_files[$res]);
				}
			}
		}


		if (!empty($exists_files)) {
			foreach ($exists_files as $key => $val) {
				$attachmnets[$key] = esc_url($val);
			}
		}

		if (!empty($post_files['name'])) {
			$tmp_files = $_FILES;
			foreach ($post_files['name'] as $f => $name) {
				if ($post_files['error'][$f] == 4) continue;
				if ($post_files['error'][$f] == 0) {
					if ($post_files['size'][$f] > $max_file_size) {
						$message[] = sprintf(__(' is too large!', 'zoner'),  $name);
						continue;
					} elseif( ! in_array(pathinfo($name, PATHINFO_EXTENSION), $valid_formats) ){
						$message[] = sprintf(__(' is not a valid format!', 'zoner'),  $name);
						continue;
					} else {


						if ($post_files['name'][$f]) {
							$file = array();
							$file = array(
								'name' 		=> $post_files['name'][$f],
								'type' 		=> $post_files['type'][$f],
								'tmp_name' 	=> $post_files['tmp_name'][$f],
								'error' 	=> $post_files['error'][$f],
								'size' 		=> $post_files['size'][$f]

							);
						}
						
						$_FILES = array($files_array_prefix => $file);
						foreach ($_FILES as $file => $array) {
							$attach_id[] = zoner_insert_attachment($file,$post_id);
						}
						$_FILES = $tmp_files;
					}
				}
			}
		}


		if (!empty($attach_id)) {
			foreach ($attach_id as $val) {
				$file_url = wp_get_attachment_url($val);
				$attachmnets[$val] = $file_url;
			}
		}

		update_post_meta($post_id, $field_name, $attachmnets);

		return $message;
	}


	if ( ! function_exists( 'zoner_get_input_videos' ) ) {
		function zoner_get_input_videos($value = '', $is_die = true, $is_remove = false) {
		?>
			<div class="inner-fields">
				<div class="col-md-10">
					<div class="form-group">
						<input type="text" class="form-control" name="videos[]" value="<?php echo $value; ?>" />
					</div>	
				</div><!-- /.form-group -->
				<div class="col-md-2">
					<div class="form-group">
						<?php if (!$is_remove) { ?>
							<button type="button" class="btn btn-default medium remove-video">
								<i class="fa fa-trash-o"></i> <?php _e('Remove', 'zoner'); ?>
							</button>
						<?php } ?>
					</div><!-- /.form-group -->
				</div><!-- /.col-md-6 -->
			</div>

		<?php
			if ($is_die) die('');
		}
	}

	if ( ! function_exists( 'zoner_get_states_by_country' ) ) {
		function zoner_get_states_by_country() {
			global $zoner;

			if ($_POST['action'] == 'get_states_by_country') {
				$country = $_POST['country'];
				echo $zoner->countries->states_dropdown_list($country);
			}
			die('');
		}
	}
	
	if ( ! function_exists( 'zoner_get_print_property_html' ) ) {
		function zoner_get_print_property_html() {
			global $zoner, $zoner_config, $prefix;
	
			if (isset($_POST) && !empty($_POST) && $_POST['action'] == 'zoner_print_property') {
				
				$width = $height = $original_logo = $description  = $name = null;
				if ($zoner_config['logo-dimensions']['width']) {
					$width 	= $zoner_config['logo-dimensions']['width'];
				}
				if ($zoner_config['logo-dimensions']['height']) {
					$height = $zoner_config['logo-dimensions']['height'];
				}
				if (!empty($zoner_config['logo']['url'])) { 
					$original_logo = esc_url($zoner_config['logo']['url']); 
				}
				$api_key_attr = (!empty($zoner_config['google-maps-api-key'] ))?$zoner_config['google-maps-api-key']:null;
				
				$description  = esc_attr(get_bloginfo('description'));
				$name  		  = esc_attr(get_bloginfo('name'));
			
				$property_id  = (int) $_POST['property_id'];
				$gproperty    = $zoner->property->get_property($property_id);
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
				if ($gproperty->area_unit) {
					$area_unit	= $gproperty->area_unit;
				}

				$currency	= $gproperty->currency;

				$price_html 	= $gproperty->price_html;
				$prop_types  	= $gproperty->property_types;
				$prop_statuses 	= $gproperty->property_status;
		
				$payment_rent = $gproperty->payment_rent;
				$payment_rent_name = $gproperty->payment_rent_name;
				$prop_type_html = $prop_status_html = array();
				
				$content = null;
				$content = preg_replace('/\[.+\]/','',  get_post_field( 'post_content', $property_id, 'display' ) );
				$content = apply_filters('the_content', $content);
				$content = str_replace(']]>', ']]&gt;', $content);
					
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
		

										
				print  '<!DOCTYPE HTML>';
				print  '<html lang="en-US">';
					print  '<head>';
						print  '<meta charset="'.get_bloginfo( 'charset' ).'">';
						print  '<title>'.$gproperty->title.'</title>';
						print  '<script src="https://code.jquery.com/jquery-2.2.4.min.js" integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script>';
						print  '<link rel="stylesheet" id="font-print-css" href="http://fonts.googleapis.com/css?family=Roboto%3A400%2C300&#038;" type="text/css" media="all" />';
						print  '<style type="text/css">';
							print  '* {margin:0; padding:0; color:#5a5a5a; font-weight:normal;}';
							print  'body {font-family: "Helvetica Neue",Helvetica,Arial,sans-serif; font-size: 14px; line-height: 1.42857143; color: #333; background-color: #fff; padding:10px;}';
							print  '.print-logo {margin:20px 0;}';
							print  '.print-featured-image {margin:10px 0}';
							print  'img {width:100%; max-width:100%;}';
							print   'a {color: #428bca; text-decoration:none;}';
							print   '.elements-wrapper {width:100%; float:left; margin:10px 0; }';
							print   '.quick-summary {margin:10px 0; width:30%; float:left;}';
							print   '.description{margin:10px 0; width:65%; float:right;}';
							print   'dl dt {float: left;}';
							print   'dl dd {text-align: right; margin-bottom: 8px;}';
							print   'dt {font-weight: 700;}';
							print   'dt, dd {line-height: 1.42857143;}';
							print   'h1, h2, h3, h4, h5, h6 {line-height: 1.1;}';
							print   'h1 {font-family: Roboto; font-weight: 300; font-style: normal; color: #5a5a5a; font-size: 28px; border: none; margin-bottom: 5px; margin-top: 0; padding-bottom: 0;}';
							print 	'h2 {font-family: Roboto; font-weight: 300; font-style: normal; color: #5a5a5a; font-size: 24px; border-bottom: 1px solid rgba(0, 0, 0, 0.1); margin-top: 10px; padding-bottom: 15px; margin-bottom: 25px;}';
							print 	'h3 {font-family: Roboto; font-weight: 300; font-style: normal; color: #5a5a5a; font-size: 20px; margin-top: 10px; margin-bottom: 20px;}';
							print   'p {font-family: Arial, Helvetica, sans-serif; text-align: inherit; line-height: 20px; font-weight: 400; font-style: normal; color: #5a5a5a; font-size: 14px; opacity: 0.7; margin: 0 0 10px;}';
							print   '.features-list li {padding: 5px 0; width: 45%; float:left; margin:0 0 0 20px;}';
							print   '.property-glr-img {margin:0 0 10px 0; float:left;}';
							print   '.property-author {width:30%; float:left;}';
							print   '.tag.price {font-weight:700;}';
							print   'img.plan {width:48%;}';
							print   'img.plan.left {float:left}';
							print   'img.plan.right {float:right}';
							print   '.print-featured-image {text-align: center;}';
							print   '.print-featured-image img {height: 400px; width: auto;}';
							print   '@media print {
										.elements-wrapper {page-break-after: auto; page-break-before: auto; page-break-inside: avoid;}
									}
							';
							print  '</style>';
							print  '<script type="text/javascript">jQuery(window).load(function() {window.print();})</script>';
					print  '</head>';	
					print  '<body>';
						print  '<div class="print-logo">';
							print  '<img style="width:'.$width.'; height:'.$height.';" width="'.(int)$width.'" height="'.(int)$height.'" src="'. $original_logo  .'" alt="' . $description . '"/>';
						print  '</div>';
						print  '<header class="print-title">';
							print  '<h1>'.$gproperty->title.'</h1>';
							print  '<figure>'.$gproperty->full_address.'</figure>';
						print  '</header>';
						
						if (has_post_thumbnail($property_id)) {
							$image_ = wp_get_attachment_image_src(get_post_thumbnail_id($property_id), 'full');
							$image_ = esc_url($image_[0]);
							
							print '<div class="print-featured-image">';
								print '<img src="'.$image_.'" alt="" />';
							print '</div>';
						}
						
						print '<div class="elements-wrapper">';
							print '<div class="quick-summary">';
								print '<header><h2>'. esc_html__('Quick Summary', 'zoner').'</h2></header>';
								print '<dl>';
								if (!empty($reference)) { 
									print '<dt>'. esc_html__('Property ID', 'zoner').'</dt>';
									print '<dd>'.$reference.'</dd>';
								}
								if (!empty($full_adddres)) {
									print '<dt>'.esc_html__('Location', 'zoner').'</dt>';
									print '<dd>'.implode(', ', $full_address).'</dd>';
								}
								if ($price_html != '') {
									print '<dt>'. esc_html__('Price', 'zoner').'</dt>';
									print '<dd>'.$price_html.'</dd>';
								}
								if ($payment_rent) {
									print '<dt>'.esc_html__('Payment', 'zoner').':</dt>';
									print '<dd>'.esc_attr($payment_rent_name).'</dd>';
								}
								if (!empty($prop_type_html)) { 
									print '<dt>'.esc_html__('Type', 'zoner').':</dt>';
									print '<dd>'.implode(', ', $prop_type_html).'</dd>';
								} 
								if (!empty($prop_status_html)) { 
									print '<dt>'.esc_html__('Status', 'zoner').':</dt>';
									print '<dd>'.implode(', ', $prop_status_html).'</dd>';
								}
								if ($area) { 
									print '<dt>'.esc_html__('Area', 'zoner').':</dt>';
									print '<dd>'.esc_attr($area) . ' ' .$zoner->property->ret_area_units_by_id($area_unit).'</dd>';
								}
								if ($rooms) {
									print '<dt>'.esc_html__('Rooms', 'zoner').':</dt>';
									print '<dd>'.esc_attr($rooms).'</dd>';
								} 
								if ($beds) {
									print '<dt>'.esc_html__('Beds', 'zoner').':</dt>';
									print '<dd>'.esc_attr($beds).'</dd>';
								}
								if ($baths) {
									print'<dt>'.esc_html__('Baths', 'zoner').':</dt>';
									print'<dd>'.esc_attr($baths).'</dd>';
								}
								if ($garages) {
									print'<dt>'.esc_html__('Garages', 'zoner').':</dt>';
									print'<dd>'.esc_attr($garages).'</dd>';
								}
								print '</dl>';
							print '</div>';
							
							print '<div class="description">';
								print '<header><h2>'.esc_html__('Property Description','zoner').'</h2></header>';
								print $content;
							print '</div>';
						print '</div>';
						
						$prop_features = array();
						$prop_features = wp_get_post_terms($property_id, 'property_features', array('orderby' => 'name', 'hide_empty' => 0) );	
						print '<div class="elements-wrapper">';
							if (!empty($prop_features)) {	
								print '<div class="property-features">';
									print '<header><h2>'.esc_html__('Property Features', 'zoner').'</h2></header>';
									print '<ul class="features-list">';	
										foreach($prop_features as $feature) {
											echo '<li>'.$feature->name.'</li>';
										}
									print '</ul>';
								print '</div>';
							}
						print '</div>';
						
						
						$floor_plans = array();
						$floor_plans = $gproperty->prop_plans;
						$cnt = 1;
						if (!empty($floor_plans)) {
							print '<div class="elements-wrapper">';
								print '<div class="floor-plans">';
									print '<header><h2>'.esc_html__('Floor Plans', 'zoner').'</h2></header>';
									foreach($floor_plans as $key => $plan) {
										$img_plan = wp_get_attachment_image_src( $key, 'full' );
										if ($cnt%2 == 0) {
											print '<img class="property-glr-img plan right" alt="" src="'.$img_plan[0].'">';
										} else {
											print '<img class="property-glr-img plan left" alt="" src="'.$img_plan[0].'">';
										}
										$cnt++;
									}	
								print '</div>';		
							print '</div>';
						}
						
						
						$lat = $gproperty->lat;
						$lng = $gproperty->lng;
						if ($lat && $lng && !empty($api_key_attr)) {
							if ($zoner_config['gm-or-osm'] == 0) {
								$map_image_ = 'https://maps.googleapis.com/maps/api/staticmap?center='.$lat.','.$lng.'&zoom=15&size=800x300&maptype=roadmap&markers=color:green%7C'.$lat.','.$lng.'&key='.$api_key_attr;	
							}
							print '<div class="elements-wrapper">';
								print '<div class="property-map">';
									print '<header><h2>'.esc_html__('Map', 'zoner').'</h2></header>';
									print '<img src="'.$map_image_ .'" alt="'.$gproperty->title.'" />';
								print '</div>';
							print '</div>';	
						}
						
						$prop_gallery = $gproperty->prop_gallery;
						if ($prop_gallery) {
							print '<div class="elements-wrapper">';
								print '<div class="property-gallery">';
									print '<header><h2>'.esc_html__('Gallery', 'zoner').'</h2></header>';
							
									foreach ($prop_gallery as $attachment_id => $url) {
										$alt = null;
										$thumbnail_image = wp_get_attachment_image_src( $attachment_id, 'zoner-gallery-property');
										$alt = get_post_meta($attachment_id, '_wp_attachment_image_alt', true);
										if (!empty($thumbnail_image[0])) {
											print '<img class="property-glr-img" alt="'.$alt.'" src="'.$thumbnail_image[0].'">';
										}
									}
								
								print '</div>';
							print '</div>';		
						}
						
						$author 	 = get_user_by('id', $gproperty->author);
						$user_meta   = get_user_meta($author->ID );
						$avatar      = zoner_get_profile_avartar($author->ID);
						$description = $mob = $tel = $skype = null;
						
						if (isset($user_meta['description'])) {
							$description = current($user_meta['description']);
						}
						if (isset($user_meta[$prefix.'mob'])) {
							$mob = current($user_meta[$prefix.'mob']);
						}	
						if (isset($user_meta[$prefix.'tel'])) {
							$tel = current($user_meta[$prefix.'tel']);
						}		
						if (isset($user_meta[$prefix.'skype'])) {
							$skype = current($user_meta[$prefix.'skype']);
						}
						
						print '<div class="elements-wrapper">';
							print '<header><h2>'.esc_html__('Contact Agent', 'zoner').'</h2></header>';
							print '<div class="property-author">';
								print '<div class="print-autor-avatar">';
									print $avatar;
									print '<h3 class="agent-name">'.zoner_get_user_name($author).'</h3>';
									print '<dl>';
										if (!empty($tel)) { 
											print '<dt>'. esc_html__('Phone', 'zoner').':</dt>';
											print '<dd>'.$tel.'</dd>';
										}

										if (!empty($mob)) { 
											print '<dt>'.esc_html__('Mobile', 'zoner').':</dt>';
											print '<dd>'.$mob.'</dd>';
										}

										if (!empty($curr_user->user_email) && (is_user_logged_in())) {
											print '<dt>'.esc_html__('Email', 'zoner').':</dt>';
											print '<dd>'.$curr_user->user_email.'</dd>';
										}

										if (!empty($skype)) {
											print '<dt>'. esc_html__('Skype', 'zoner').':</dt>';
											print '<dd>'.$skype.'</dd>';
										}
									print '</dl>';
								print '</div>';
							print '</div>';
						print '</div>';
				
					print  '</body>';
				print '</html>';
					
			}
			die('');
		}
	}

if ( ! function_exists( 'zoner_gen_input_hidden' ) ) {
	/**
	 * Generate <input type='hidden'> fields by array of arrays with attributes and values:
	 * [ ['name' = > 'field_name, 'value' => 'field_value', ..] , [..], .. ]
	 * @param array $hidden_fields
	 * @return bool|null|string
	 */
	function zoner_gen_inputs_hidden($hidden_fields) {
		if ( ! is_array($hidden_fields) ) return false;
		$return_html = null;

		foreach ( $hidden_fields as $h_field ) {
			if ( ! is_array($h_field) || ! count($h_field) ) continue;
			$h_field_html = '<input type="hidden" ';
			foreach ( $h_field as $hf_attr_key => $hf_attr_value ) {
				$h_field_html .= $hf_attr_value ? "{$hf_attr_key}='{$hf_attr_value}' " : '';
			}
			$h_field_html .= '>';
			$return_html .= $h_field_html;
		}
		return $return_html;
	}
}