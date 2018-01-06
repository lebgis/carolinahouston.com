<?php 

	if ( ! function_exists( 'zoner_edit_agency' ) ) {
		function zoner_edit_agency() {
			global $zoner_config, $prefix, $zoner, $post;
			$update_post = array();

            $no_errors=$zoner->validate->check('agency');
			if (isset($_POST) && isset($_POST['edit-agency']) && wp_verify_nonce($_POST['edit-agency'], 'zoner_edit_agency') && $no_errors) {
				
				$update_post = array(
						'ID' 		   => $post->ID,
						'post_content' => wp_filter_post_kses($_POST['agency-aboutus']),
						'post_title'   => $_POST['agency-title']
				);
				
				wp_update_post( $update_post );
				
				update_post_meta($post->ID, $prefix . 'agency_address', wp_filter_post_kses($_POST['agency-address']));
				
				update_post_meta($post->ID, $prefix . 'agency_googlemapurl', 	esc_url($_POST['agency-ggmapurl']));
				update_post_meta($post->ID, $prefix . 'agency_email', 			$_POST['agency-email']);
				update_post_meta($post->ID, $prefix . 'agency_facebookurl', 	esc_url($_POST['agency-facebook']));
				update_post_meta($post->ID, $prefix . 'agency_twitterurl', 		esc_url($_POST['agency-twitter']));
				update_post_meta($post->ID, $prefix . 'agency_googleplusurl', 	esc_url($_POST['agency-ggplus']));
				update_post_meta($post->ID, $prefix . 'agency_linkedinurl', 	esc_url($_POST['agency-linkedin']));
				update_post_meta($post->ID, $prefix . 'agency_pinteresturl', 	esc_url($_POST['agency-pinterset']));
				update_post_meta($post->ID, $prefix . 'agency_instagramurl', 	esc_url($_POST['agency-instagram']));
				update_post_meta($post->ID, $prefix . 'agency_tel',				esc_attr($_POST['agency-tel']));
				update_post_meta($post->ID, $prefix . 'agency_mob',				esc_attr($_POST['agency-mobile']));
				update_post_meta($post->ID, $prefix . 'agency_skype',			esc_attr($_POST['agency-skype']));
				
				
				/*Featured image*/
				if (empty($_POST['agency-featured-image-exists']))
					delete_post_thumbnail($post->ID); 
			
				if (!empty($_FILES['agency-featured-image-file']['name'])) {
					$attach_id = zoner_insert_attachment( 'agency-featured-image-file', $post->ID, true );
				}
				
				if (empty($_POST['agency-logo-image-exists'])) {
					update_post_meta($post->ID, $prefix.'agency_line_img_id', null); 
				    update_post_meta($post->ID, $prefix.'agency_line_img', null);
				}
					
					
				if (!empty($_FILES['agency-logo-image-file']['name'])) {
					$attach_id = zoner_insert_attachment( 'agency-logo-image-file', $post->ID);
					$img_logo  = wp_get_attachment_image_src($attach_id, 'full');
					
					update_post_meta($post->ID, $prefix.'agency_line_img_id', $attach_id); 
				    update_post_meta($post->ID, $prefix.'agency_line_img', $img_logo[0]);
					
				}
				
			}
			
			
			if (!empty($_POST) && isset($_POST['edit-agency'])){
                if ($no_errors)
                    wp_safe_redirect( zoner_curPageURL() );
            }
		}
	}	 
	
	
	
	if ( ! function_exists( 'zoner_insert_agency' ) ) {
		function zoner_insert_agency() {
			global $zoner_config, $prefix, $zoner, $post;
			$update_post = array();
            $no_errors=$zoner->validate->check('agency');
			if (isset($_POST) && isset($_POST['add-agency']) && wp_verify_nonce($_POST['add-agency'], 'zoner_add_agency') && $no_errors) {
				
				$insert_agency = array();
				$insert_agency = array(
						'post_title'   => $_POST['agency-title'],
						'post_name'	   => sanitize_title_with_dashes($_POST['agency-title'], '', 'save'),
						'post_content' => wp_filter_post_kses($_POST['agency-aboutus']),
						'post_status'  => 'publish',
						'post_author'  => get_current_user_id(),
						'post_type'	   => 'agency'	
				);

				
				$post_id = wp_insert_post( $insert_agency );
				
				
				update_post_meta($post_id, $prefix . 'agency_address', wp_filter_post_kses($_POST['agency-address']));
				
				if (isset($_POST['agency-ggmapurl']))
				update_post_meta($post_id, $prefix . 'agency_googlemapurl', 	esc_url($_POST['agency-ggmapurl']));
				update_post_meta($post_id, $prefix . 'agency_email', 			$_POST['agency-email']);
				if (isset($_POST['agency-facebook']))
				update_post_meta($post_id, $prefix . 'agency_facebookurl', 	esc_url($_POST['agency-facebook']));
				if (isset($_POST['agency-twitter']))
				update_post_meta($post_id, $prefix . 'agency_twitterurl', 		esc_url($_POST['agency-twitter']));
				if (isset($_POST['agency-ggplus']))
				update_post_meta($post_id, $prefix . 'agency_googleplusurl', 	esc_url($_POST['agency-ggplus']));
				if (isset($_POST['agency-linkedin']))
				update_post_meta($post_id, $prefix . 'agency_linkedinurl', 	esc_url($_POST['agency-linkedin']));
				if (isset($_POST['agency-pinterset']))
				update_post_meta($post_id, $prefix . 'agency_pinteresturl', 	esc_url($_POST['agency-pinterset']));
				if (isset($_POST['agency-instagram']))
				update_post_meta($post_id, $prefix . 'agency_instagramurl', 	esc_url($_POST['agency-instagram']));
				if (isset($_POST['agency-tel']))
				update_post_meta($post_id, $prefix . 'agency_tel',				esc_attr($_POST['agency-tel']));
				if (isset($_POST['agency-mobile']))
				update_post_meta($post_id, $prefix . 'agency_mob',				esc_attr($_POST['agency-mobile']));
				if (isset($_POST['agency-skype']))
				update_post_meta($post_id, $prefix . 'agency_skype',			esc_attr($_POST['agency-skype']));
				
				
				/*Featured image*/
				if (!empty($_FILES['agency-featured-image-file']['name'])) {
					$attach_id = zoner_insert_attachment( 'agency-featured-image-file', $post_id, true );
				}
					
				if (!empty($_FILES['agency-logo-image-file']['name'])) {
					$attach_id = zoner_insert_attachment( 'agency-logo-image-file', $post_id);
					$img_logo  = wp_get_attachment_image_src($attach_id, 'full');
					
					update_post_meta($post_id, $prefix.'agency_line_img_id', $attach_id); 
				    update_post_meta($post_id, $prefix.'agency_line_img', $img_logo[0]);
				}
			}
			
			$redirect_link  = '';
			if (!empty($_POST) && isset($_POST['add-agency']) && $no_errors) {
				$curr_user = get_current_user_id();
				$page_tasp = $zoner->zoner_get_page_id('page-tasa');
				
				if (!empty($page_tasp)) {
					$redirect_link = get_permalink($page_tasp);
				} else {
					$redirect_link = add_query_arg(array('profile-page' => 'my_agencies'), get_author_posts_url($curr_user));
				}
				if ($no_errors)
				    wp_redirect($redirect_link);
				exit;
			}	
		}
	}