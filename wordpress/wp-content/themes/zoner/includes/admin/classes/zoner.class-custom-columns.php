<?php
/**
 * Zoner Agent form Agnecies linked
*/
 
class zoner_custom_columns {
	
	public function __construct() {
		global $zoner;
		
		add_filter( 'manage_property_posts_columns', 			array( $this, 'zoner_property_columns'));
		add_action( 'manage_property_posts_custom_column', 		array( $this, 'zoner_custom_property_columns'), 2);
		add_filter( 'manage_edit-property_sortable_columns', 	array( $this, 'zoner_property_sortable_columns'));
				
		add_action( 'restrict_manage_posts', 					array( $this, 'zoner_add_property_filters' ) );
		add_filter( 'parse_query', 								array( $this, 'zoner_property_parse_query' ) );
		add_filter( 'request', 									array( $this, 'zoner_property_request_query' ) );
		
		add_action( 'bulk_edit_custom_box', 	array( $this, 'zoner_bulk_edit'  ), 10, 2 );
		add_action( 'quick_edit_custom_box',	array( $this, 'zoner_quick_edit' ), 10, 2);
		add_action( 'admin_footer', 		    array( $this, 'zoner_quick_edit_javascript' ));
		add_action( 'save_post', 				array( $this, 'zoner_baq_es_post' ), 10, 2 );
		
		add_filter( 'list_table_primary_column', array( $this, 'zoner_table_primary_column' ), 10, 2 );
		add_filter( 'post_row_actions', array( $this, 'zoner_row_actions' ), 2, 100 );
	}
	
	
	public function zoner_table_primary_column( $default, $screen_id ) {
		if ( 'edit-property' === $screen_id ) {
			return 'name';
		}
		return $default;
	}
	
	
	public function zoner_row_actions( $actions, $post ) {
		if ( 'property' === $post->post_type ) {
			return array_merge( array( 'id' => 'ID: ' . $post->ID ), $actions );
		}
		return $actions;
	}
	
	public function zoner_property_columns( $existing_columns ) {
		if ( empty( $existing_columns ) && ! is_array( $existing_columns ) ) {
					$existing_columns = array();
		}
		
		unset( $existing_columns['title'], $existing_columns['date'] );

		$columns = array();
		$columns['cb'] 	  = '<input type="checkbox" />';
		$columns['thumb'] = '<span class="fa fa-file-image-o" title="' . __( 'Image', 'zoner' ) . '" data-tip="' . esc_attr__( 'Image', 'zoner' ) . '"></span>';

		$columns['name']  			= __( 'Name', 'zoner' );
		$columns['property_cat'] 	= __( 'Categories', 'zoner' );
		$columns['property_tag'] 	= __( 'Tags', 'zoner' );
		$columns['property_type']   = __('Property type', 'zoner');
		$columns['property_status'] = __('Property status', 'zoner');
		$columns['price']			= __( 'Price', 'zoner' );
		$columns['paid'] 	 		= '<span class="fa fa-usd"  title="' . __( 'Paid',  'zoner' ) . '"></span>';
		$columns['featured'] 		= '<span class="fa fa-star" title="' . __( 'Featured', 'zoner' ) . '"></span>';
		$columns['property-pending']  = '<span class="fa fa-clock-o" title="' . __( 'Pending', 'zoner' ) . '"></span>';
		$columns['date']     		= __( 'Date', 'zoner' );
		
		return array_merge( $columns, $existing_columns );
	}
	
	
	private function zoner_render_property_row_actions( $post, $title ) {
		global $wp_version;

		if ( version_compare( $wp_version, '4.3-beta', '>=' ) ) {
			return;
		}

		$post_type_object = get_post_type_object( $post->post_type );
		$can_edit_post    = current_user_can( $post_type_object->cap->edit_post, $post->ID );

		// Get actions
		$actions = array();

		$actions['id'] = 'ID: ' . $post->ID;

		if ( $can_edit_post && 'trash' != $post->post_status ) {
			$actions['edit'] = '<a href="' . get_edit_post_link( $post->ID, true ) . '" title="' . esc_attr( __( 'Edit this item', 'zoner' ) ) . '">' . __( 'Edit', 'zoner' ) . '</a>';
			$actions['inline hide-if-no-js'] = '<a href="#" class="editinline" title="' . esc_attr( __( 'Edit this item inline', 'zoner' ) ) . '">' . __( 'Quick&nbsp;Edit', 'zoner' ) . '</a>';
		}
		if ( current_user_can( $post_type_object->cap->delete_post, $post->ID ) ) {
			if ( 'trash' == $post->post_status ) {
				$actions['untrash'] = '<a title="' . esc_attr( __( 'Restore this item from the Trash', 'zoner' ) ) . '" href="' . wp_nonce_url( admin_url( sprintf( $post_type_object->_edit_link . '&amp;action=untrash', $post->ID ) ), 'untrash-post_' . $post->ID ) . '">' . __( 'Restore', 'zoner' ) . '</a>';
			} elseif ( EMPTY_TRASH_DAYS ) {
				$actions['trash'] = '<a class="submitdelete" title="' . esc_attr( __( 'Move this item to the Trash', 'zoner' ) ) . '" href="' . get_delete_post_link( $post->ID ) . '">' . __( 'Trash', 'zoner' ) . '</a>';
			}

			if ( 'trash' == $post->post_status || ! EMPTY_TRASH_DAYS ) {
				$actions['delete'] = '<a class="submitdelete" title="' . esc_attr( __( 'Delete this item permanently', 'zoner' ) ) . '" href="' . get_delete_post_link( $post->ID, '', true ) . '">' . __( 'Delete Permanently', 'zoner' ) . '</a>';
			}
		}
		if ( $post_type_object->public ) {
			if ( in_array( $post->post_status, array( 'pending', 'draft', 'future' ) ) ) {
				if ( $can_edit_post )
					$actions['view'] = '<a href="' . esc_url( add_query_arg( 'preview', 'true', get_permalink( $post->ID ) ) ) . '" title="' . esc_attr( sprintf( __( 'Preview &#8220;%s&#8221;', 'zoner' ), $title ) ) . '" rel="permalink">' . __( 'Preview', 'zoner' ) . '</a>';
			} elseif ( 'trash' != $post->post_status ) {
				$actions['view'] = '<a href="' . get_permalink( $post->ID ) . '" title="' . esc_attr( sprintf( __( 'View &#8220;%s&#8221;', 'zoner' ), $title ) ) . '" rel="permalink">' . __( 'View', 'zoner' ) . '</a>';
			}
		}

		$actions = apply_filters( 'post_row_actions', $actions, $post );

		echo '<div class="row-actions">';

		$i = 0;
		$action_count = sizeof( $actions );

		foreach ( $actions as $action => $link ) {
			++$i;
			( $i == $action_count ) ? $sep = '' : $sep = ' | ';
			echo '<span class="' . $action . '">' . $link . $sep . '</span>';
		}
		echo '</div>';
	}
	
	public function zoner_custom_property_columns( $column ) {
		global $post, $prefix, $zoner;

		switch ( $column ) {
			case 'thumb' :
				echo '<a href="' . get_edit_post_link( $post->ID ) . '">' . $this->zoner_get_property_thumbnail_image(array(40,40)) . '</a>';
				break;
			case 'name' :
				$edit_link        = get_edit_post_link( $post->ID );
				$title            = _draft_or_post_title();
				$post_type_object = get_post_type_object( $post->post_type );
				$can_edit_post    = current_user_can( $post_type_object->cap->edit_post, $post->ID );

				echo '<strong><a class="row-title" href="' . esc_url( $edit_link ) .'">' . $title.'</a>';

				_post_states( $post );

				echo '</strong>';

				if ( $post->post_parent > 0 ) {
					echo '&nbsp;&nbsp;&larr; <a href="'. get_edit_post_link( $post->post_parent ) .'">'. get_the_title( $post->post_parent ) .'</a>';
				}

				// Excerpt view
				if ( isset( $_GET['mode'] ) && 'excerpt' == $_GET['mode'] ) {
					echo apply_filters( 'the_excerpt', $post->post_excerpt );
				}

				$this->zoner_render_property_row_actions($post, $title);
				
				get_inline_data( $post );
				$this->get_zoner_inline_data( $post );
			break;
			
			case 'property_cat' :
			case 'property_tag' :
				if ( ! $terms = get_the_terms( $post->ID, $column ) ) {
					echo '<span class="na">&ndash;</span>';
				} else {
					foreach ( $terms as $term ) {
						$termlist[] = '<a href="' . admin_url( 'edit.php?' . $column . '=' . $term->slug . '&post_type=property' ) . ' ">' . $term->name . '</a>';
					}

				echo implode( ', ', $termlist );
				}
			break;
		
			case 'property_type' :
				$arr_links = array();	
				$prop_types = wp_get_post_terms($post->ID, 'property_type',   array('orderby' => 'name', 'hide_empty' => 0) );
				if (!empty($prop_types)) {
					foreach ($prop_types as $type) {
						$attachment_id = $zoner->zoner_tax->get_zoner_term_meta($type->term_id, 'thumbnail_id');
						$img_tax 	   = wp_get_attachment_image_src($attachment_id, array(26,26));
						$term_link 	   = admin_url( 'edit.php?' . $column . '=' . $type->slug . '&post_type=property' );
						
						if (!empty($img_tax)) {
							$arr_links[] = '<a class="property_type" href="'.$term_link.'" title="'.$type->name.'"><img width="26" height="26" src="'.$img_tax[0].'" alt="" /></a>';
						} else {
							$arr_links[] = '<a class="property_type" href="'.$term_link.'" title="'.$type->name.'">'.$type->name.'</a>';	
						}
					}	
					echo implode("", $arr_links);
				} else {
					echo '<span class="na">&ndash;</span>';	
				}
				
			break;
			case 'property_status' :
				$prop_status = wp_get_post_terms($post->ID, 'property_status', array('orderby' => 'name', 'hide_empty' => 0) );		

				if (!empty($prop_status)) {
					foreach ($prop_status as $status) {
						$term_link = admin_url( 'edit.php?' . $column . '=' . $status->slug . '&post_type=property' );
						
						echo '<a class="property_status" href="'.$term_link.'" title="'.$status->name.'">'.$status->name.'</a>';	
					}
				} else {
					echo '<span class="na">&ndash;</span>';	
				}
			break;
			case 'price' :
				
				$price_html = '';
				$price 		= get_post_meta( $post->ID, $prefix.'price', true );
				$currency 	= get_post_meta( $post->ID, $prefix.'currency', true );
				
				$price_html = $zoner->currency->get_zoner_property_price($price, $currency);
				
				echo $price_html ? $price_html : '<span class="na">&ndash;</span>';
				break;
			case 'paid' :
				$url = wp_nonce_url( admin_url( 'admin-ajax.php?action=zoner_paid_property&property_id=' . $post->ID ), 'zoner-paid-property' );
				
				$paid_html = '';
				$is_paid 		= get_post_meta( $post->ID, $prefix.'is_paid', true );
									
				if (!empty($is_paid) && ($is_paid == 'on')) {
					$paid_html = '<a href="' . esc_url( $url ) . '" title="'. __( 'Paid', 'zoner' ) . '">';
						$paid_html .= '<span class="fa fa-cc-mastercard"></span>';
					$paid_html .= '</a>';	
				} else {
					$paid_html = '<a href="' . esc_url( $url ) . '" title="'. __( 'Not Paid', 'zoner' ) . '">';
						$paid_html .= '<span class="fa fa-times"></span>';
					$paid_html .= '</a>';		
				}
				
				echo $paid_html;
				break;	
			case 'featured' :
				$url = wp_nonce_url( admin_url( 'admin-ajax.php?action=zoner_feature_property&property_id=' . $post->ID ), 'zoner-feature-property' );
				$out_featured = '';
				$featured 	= get_post_meta( $post->ID, $prefix . 'is_featured', true );
				
				$out_featured = '<a href="' . esc_url( $url ) . '" title="'. __( 'Toggle featured', 'zoner' ) . '">';
				
				if ( $featured  == 'on') { $out_featured .= '<span class="fa fa-star"></span>'; } 
									else { $out_featured .= '<span class="fa fa-star-o"></span>'; }
				$out_featured .= '</a>';
				echo $out_featured;
				break;
				
			case 'property-pending' :
				$url = wp_nonce_url( admin_url( 'admin-ajax.php?action=zoner_pending_property&property_id=' . $post->ID ), 'zoner-pending-property' );
				$out_pending = '';
				$pending = get_post_status( $post->ID);
				
				$out_pending = '<a href="' . esc_url( $url ) . '" title="'. __( 'Toggle pending', 'zoner' ) . '">';
				if ( $pending  == 'zoner-pending') { $out_pending .= '<span title="'.__('Pending', 'zoner').'" class="fa fa-clock-o"></span>'; } 
											  else { $out_pending .= '<span title="'.__('Approved', 'zoner').'" class="fa fa-check-circle-o"></span>'; }
				$out_pending .= '</a>';
				echo $out_pending;
				break;	
			default :
				break;
		}
	}
	
	public function get_zoner_inline_data($post) {
			global $prefix;
			$property_id = $post->ID;
			
			$allow_raiting	= get_post_meta($property_id, $prefix .'allow_raiting', true);
			$is_featured    = get_post_meta($property_id, $prefix .'is_featured', true);
			$reference 		= get_post_meta($property_id, $prefix .'reference', true);
			$condition 		= get_post_meta($property_id, $prefix .'condition', true);
			$payment		= get_post_meta($property_id, $prefix .'payment', true);			
			$price			= get_post_meta($property_id, $prefix .'price', true);
			$price_format   = get_post_meta($property_id, $prefix .'price_format', true);
			$currency 		= get_post_meta($property_id, $prefix.'currency', true);
			$rooms	 		= get_post_meta($property_id, $prefix.'rooms', true);
			$beds	 		= get_post_meta($property_id, $prefix.'beds', true);
			$baths   		= get_post_meta($property_id, $prefix.'baths', true);
			$garages 		= get_post_meta($property_id, $prefix.'garages', true);
			$area  	  		= get_post_meta($property_id, $prefix.'area', true);
			$area_unit 		= get_post_meta($property_id, $prefix.'area_unit', true);
			
		?>
			<div class="hidden" id="zoner_inline_<?php echo $property_id; ?>">
				<div class="allow_raiting"><?php echo $allow_raiting; ?></div>
				<div class="is_featured"><?php echo $is_featured; ?></div>
				<div class="reference"><?php echo $reference; ?></div>
				<div class="condition"><?php echo $condition; ?></div>
				<div class="payment"><?php echo $payment; ?></div>
				<div class="price"><?php echo $price; ?></div>
				<div class="price_format"><?php echo $price_format; ?></div>
				<div class="currency"><?php echo $currency; ?></div>
				<div class="rooms"><?php echo $rooms; ?></div>
				<div class="beds"><?php echo $beds; ?></div>
				<div class="baths"><?php echo $baths; ?></div>
				<div class="garages"><?php echo $garages; ?></div>
				<div class="area"><?php echo $area; ?></div>
				<div class="area_unit"><?php echo $area_unit; ?></div>
			</div>	
		<?php 	
	}
	
	public function zoner_property_sortable_columns( $columns ) {
		$custom = array(
			'price'		=> 'price',
			'name'		=> 'title'
		);
		return wp_parse_args( $custom, $columns );
	}
	
	
	public function zoner_add_property_filters() {
		global $typenow, $wp_query;
		if ( 'property' != $typenow ) return;
		
		
		$property_type   = get_terms( 'property_type' );
		$property_status = get_terms( 'property_status' );
		$property_cat 	 = get_terms( 'property_cat' );
		
		$output = '';
		
		if (!empty($property_cat)) {
			$output .= '<select name="property_cat" id="filter-property_cat" class="filter-property_cat">';
				$output .= '<option value="">' . __( 'Show all categories', 'zoner' ) . '</option>';
				
				foreach ($property_cat as $cat) {
					$output .= '<option value="' . $cat->slug . '" ';						
						
					if ( isset( $wp_query->query['property_cat'] ) )
						$output .= selected( $cat->slug, $wp_query->query['property_cat'], false );
					$output .= '>' . $cat->name;
					$output .= " ($cat->count)</option>";							
			
			}
			
			$output .= '</select>';
		}
		
		if (!empty($property_type)) {
			$output .= '<select name="property_type" id="filter-property_type" class="filter-property_type">';
				$output .= '<option value="">' . __( 'Show all types', 'zoner' ) . '</option>';
				
				foreach ($property_type as $type) {
					$output .= '<option value="' . $type->slug . '" ';						
						
					if ( isset( $wp_query->query['property_type'] ) )
						$output .= selected( $type->slug, $wp_query->query['property_type'], false );
					$output .= '>' . $type->name;
					$output .= " ($type->count)</option>";							
				
				}
			$output .= '</select>';
		}
		
		
		if (!empty($property_status)) {
			$output .= '<select name="property_status" id="filter-property_status" class="filter-property_status">';
				$output .= '<option value="">' . __( 'Show all statuses', 'zoner' ) . '</option>';
				
				foreach ($property_status as $status) {
					$output .= '<option value="' . $status->slug . '" ';						
						
					if ( isset( $wp_query->query['property_status'] ) )
						$output .= selected( $status->slug, $wp_query->query['property_status'], false );
					$output .= '>' . $status->name;
					$output .= " ($status->count)</option>";							
			
			}
			
			$output .= '</select>';
		}
		
		echo apply_filters( 'zoner_property_filters', $output );
	}	
	
	public function zoner_property_parse_query( $query ) {
		global $typenow, $wp_query;
		if ( 'property' == $typenow ) {
	
			if ( isset( $_GET['product_type'] ) && '0' == $_GET['product_type'] ) {
				$query->query_vars['tax_query'][] = array(
					'taxonomy' => 'product_type',
					'field'    => 'id',
					'terms'    => get_terms( 'product_type', array( 'fields' => 'ids' ) ),
					'operator' => 'NOT IN'
				);
			}
			
			if ( isset( $_GET['product_status'] ) && '0' == $_GET['product_status'] ) {
				$query->query_vars['tax_query'][] = array(
					'taxonomy' => 'product_status',
					'field'    => 'id',
					'terms'    => get_terms( 'product_status', array( 'fields' => 'ids' ) ),
					'operator' => 'NOT IN'
				);
			}
			
			if ( isset( $_GET['product_cat'] ) && '0' == $_GET['product_cat'] ) {
				$query->query_vars['tax_query'][] = array(
					'taxonomy' => 'product_cat',
					'field'    => 'id',
					'terms'    => get_terms( 'product_cat', array( 'fields' => 'ids' ) ),
					'operator' => 'NOT IN'
				);
			}
		}
	}
	
	
	public function zoner_property_request_query( $vars ) {
		global $typenow, $wp_query, $prefix, $zoner;
		
		
		if ( 'property' === $typenow ) {
			if ( isset( $vars['orderby'] ) ) {
				$orderby = $vars['orderby'];
				
				/*Remove pre get post params*/
				$zoner->zoner_remove_filter();
				
				if ( 'price' ==  $orderby ) {
					$vars = array_merge( $vars, array(
						'meta_key' 	=> $prefix.'price',
						'orderby' 	=> 'meta_value_num title'
					) );
				}
			
				if ( 'featured' == $orderby ) {
					$vars = array_merge( $vars, array(
							'meta_key' 	=> $prefix.'is_featured',
							'orderby' 	=> 'meta_value'
					) );
				}
			}
		}	
		return $vars;
	}
	
	public function zoner_get_property_thumbnail_image($size = 'thumbnail', $attr = array() ) {
		global $post;
		if ( has_post_thumbnail( $post->ID ) ) {
			$image = get_the_post_thumbnail( $post->ID, $size, $attr );
		} elseif ( ( $parent_id = wp_get_post_parent_id( $post->ID ) ) && has_post_thumbnail( $parent_id ) ) {
			$image = get_the_post_thumbnail( $parent_id, $size, $attr );
		} else {
			$image = $this->zoner_property_placeholder_img( $size );
		}
		return $image;
	}
	
	public function zoner_quick_edit_javascript() {
		global $current_screen, $prefix;
		 
		if (($current_screen->id != 'edit-property') || ($current_screen->post_type != 'property')) return; 
		?>
		<script type="text/javascript">
			jQuery(document).ready(function() {
				jQuery( '#the-list' ).on( 'click', '.editinline', function() {
					inlineEditPost.revert();
					var post_id = jQuery(this).closest( 'tr' ).attr( 'id' );
						post_id = post_id.replace( 'post-', '' );

					var zoner_inline_data = jQuery( '#zoner_inline_' + post_id );
				
					var reference 	= zoner_inline_data.find( '.reference' ).text(),
						condition 	= zoner_inline_data.find( '.condition' ).text(),
						payment   	= zoner_inline_data.find( '.payment' ).text(),
						price   	= zoner_inline_data.find( '.price' ).text(),
						price_format   = zoner_inline_data.find( '.price_format' ).text(),
						currency   	= zoner_inline_data.find( '.currency' ).text(),
						rooms   	= zoner_inline_data.find( '.rooms' ).text(),
						beds   		= zoner_inline_data.find( '.beds' ).text(),
						baths   	= zoner_inline_data.find( '.baths' ).text(),
						garages   	= zoner_inline_data.find( '.garages' ).text(),
						area   		= zoner_inline_data.find( '.area' ).text(),
						area_unit   = zoner_inline_data.find( '.area_unit' ).text(),
						is_featured    = zoner_inline_data.find( '.is_featured' ).text(),
						allow_raiting  = zoner_inline_data.find( '.allow_raiting' ).text();
						
						
					jQuery( 'input[name="<?php echo $prefix.'reference'; ?>"]', '.inline-edit-row' ).val( reference );
					jQuery( 'input[name="<?php echo $prefix.'price'; ?>"]', '.inline-edit-row' ).val( price );
					jQuery( 'input[name="<?php echo $prefix.'rooms'; ?>"]', '.inline-edit-row' ).val( rooms );
					jQuery( 'input[name="<?php echo $prefix.'beds'; ?>"]', '.inline-edit-row' ).val( beds );
					jQuery( 'input[name="<?php echo $prefix.'baths'; ?>"]', '.inline-edit-row' ).val( baths );
					jQuery( 'input[name="<?php echo $prefix.'garages'; ?>"]', '.inline-edit-row' ).val( garages );
					jQuery( 'input[name="<?php echo $prefix.'area'; ?>"]', '.inline-edit-row' ).val( area );
					
					jQuery( 'select[name="<?php echo $prefix.'condition'; ?>"] option[value="' + condition + '"]', '.inline-edit-row' ).attr( 'selected', 'selected' );
					jQuery( 'select[name="<?php echo $prefix.'payment'; ?>"] option[value="' + payment + '"]', '.inline-edit-row' ).attr( 'selected', 'selected' );
					jQuery( 'select[name="<?php echo $prefix.'price_format'; ?>"] option[value="' + price_format + '"]', '.inline-edit-row' ).attr( 'selected', 'selected' );
					jQuery( 'select[name="<?php echo $prefix.'currency'; ?>"] option[value="' + currency + '"]', '.inline-edit-row' ).attr( 'selected', 'selected' );
					jQuery( 'select[name="<?php echo $prefix.'area_unit'; ?>"] option[value="' + area_unit + '"]', '.inline-edit-row' ).attr( 'selected', 'selected' );
					
					
					if ( 'on' === is_featured ) { 
						jQuery( 'input[name="<?php echo $prefix.'is_featured'; ?>"]', '.inline-edit-row' ).attr( 'checked', 'checked' );
					} else {
						jQuery( 'input[name="<?php echo $prefix.'is_featured'; ?>"]', '.inline-edit-row' ).removeAttr( 'checked' );
					}
					
					if ( 'on' === allow_raiting ) { 
						jQuery( 'input[name="<?php echo $prefix.'allow_raiting'; ?>"]', '.inline-edit-row' ).attr( 'checked', 'checked' );
					} else {
						jQuery( 'input[name="<?php echo $prefix.'allow_raiting'; ?>"]', '.inline-edit-row' ).removeAttr( 'checked' );
					}
					
				});
			});
		</script>
    <?php
}

	public function zoner_property_placeholder_img($size) {
		global $admin_theme_url;
		return '<img width="'.$size[0].'" height="'.$size[1].'" src="'.$admin_theme_url.'/classes/res/placeholder.jpg" alt="" />';
	}			
	
	public function zoner_bulk_edit( $column_name, $post_type ) {
		if ( 'name' != $column_name || 'property' != $post_type ) {
			return;
		}
		
		$this->zoner_get_bulk_edit_form();
	}
	
	public function zoner_quick_edit($column_name, $post_type) {
		if ( 'name' != $column_name || 'property' != $post_type ) {
			return;
		}
		
		$this->zoner_get_quick_edit_form();
	}
	
	public function zoner_get_bulk_edit_form() {
		global $prefix, $zoner;
		?>
		<fieldset class="inline-edit-col-left">
			<div id="zoner-fields-bulk" class="inline-edit-col">
				<h4><?php _e( 'Property Settings', 'zoner' ); ?></h4>

				<?php do_action( 'zoner_property_bulk_edit_start' ); ?>

				<label>
					<span class="title"><?php _e( 'Condition', 'zoner' ); ?></span>
					<span class="input-text-wrap">
						<select class="condition_change" name="condition_change">
						<?php
							$options[''] = __( '— No Change —', 'zoner' );
							$options = array_merge($options, $zoner->property->get_condition_values());
							
							foreach ( $options as $key => $value ) {
								echo '<option value="' . esc_attr( $key ) . '">' . $value . '</option>';
							}
						?>
						</select>
					</span>
				</label>	
				<label>
					<span class="title"><?php _e( 'Payment', 'zoner' ); ?></span>
					<span class="input-text-wrap">
						<select class="payment_change" name="payment_change">
						<?php
							$options[''] = __( '— No Change —', 'zoner' );
							$options = array_merge($options, $zoner->property->get_payment_rent_values());
							
							foreach ( $options as $key => $value ) {
								echo '<option value="' . esc_attr( $key ) . '">' . $value . '</option>';
							}
						?>
						</select>
					</span>
				</label>	
				<label>
					<span class="title"><?php _e( 'Price', 'zoner' ); ?></span>
					<span class="input-text-wrap">
						<input type="text" name="price_change" class="text price_change" value="">
					</span>
				</label>
				<label>
					<span class="title"><?php _e( 'Price format', 'zoner' ); ?></span>
					<span class="input-text-wrap">
						<select class="price_format_change" name="price_format_change">
						<?php
							$options[''] = __( '— No Change —', 'zoner' );
							$options = array_merge($options, $zoner->property->get_price_format_values());
							
							foreach ( $options as $key => $value ) {
								echo '<option value="' . esc_attr( $key ) . '">' . $value . '</option>';
							}
						?>
						</select>
					</span>
				</label>	
				<label>
					<span class="title"><?php _e( 'Currency', 'zoner' ); ?></span>
					<span class="input-text-wrap">
						<select class="currency_change" name="currency_change">
						<?php
							$options[''] = __( '— No Change —', 'zoner' );
							$options = array_merge($options, $zoner->currency->get_zoner_currency_dropdown_settings());
							
							foreach ( $options as $key => $value ) {
								echo '<option value="' . esc_attr( $key ) . '">' . $value . '</option>';
							}
						?>
						</select>
					</span>
				</label>	
				<label>
					<span class="title"><?php _e( 'Rooms', 'zoner' ); ?></span>
					<span class="input-text-wrap">
						<input type="text" name="rooms_change" class="text rooms_change" value="">
					</span>
				</label>
				<label>
					<span class="title"><?php _e( 'Beds', 'zoner' ); ?></span>
					<span class="input-text-wrap">
						<input type="text" name="beds_change" class="text beds_change" value="">
					</span>
				</label>
				<label>
					<span class="title"><?php _e( 'Baths', 'zoner' ); ?></span>
					<span class="input-text-wrap">
						<input type="text" name="baths_change" class="text baths_channge" value="">
					</span>
				</label>
				<label>
					<span class="title"><?php _e( 'Garages', 'zoner' ); ?></span>
					<span class="input-text-wrap">
						<input type="text" name="garages_change" class="text garages_change" value="">
					</span>
				</label>
				<label>
					<span class="title"><?php _e( 'Area', 'zoner' ); ?></span>
					<span class="input-text-wrap">
						<input type="text" name="area_change" class="text area_change" value="">
					</span>
				</label>
				<label>
					<span class="title"><?php _e( 'Area units', 'zoner' ); ?></span>
					<span class="input-text-wrap">
						<select class="area_unit_change" name="area_unit_change">
						<?php
							$options[''] = __( '— No Change —', 'zoner' );
							$options = array_merge($options, $zoner->property->get_area_units_values());
							
							foreach ( $options as $key => $value ) {
								echo '<option value="' . esc_attr( $key ) . '">' . $value . '</option>';
							}
						?>
						</select>
					</span>
				</label>	
				
				
				<?php do_action( 'zoner_property_bulk_edit_end' ); ?>

				<input type="hidden" name="zoner_bulk_edit" value="1" />
				<input type="hidden" name="zoner_bulk_edit_nonce" value="<?php echo wp_create_nonce( 'zoner_bulk_edit_nonce' ); ?>" />
			</div>
		</fieldset>
	<?php 
	}
	
	public function zoner_get_quick_edit_form() {
		global $prefix, $zoner;
		?>
		<fieldset class="inline-edit-col-left">
			<div id="zoner-fields" class="inline-edit-col">
				<h4><?php _e( 'Property Settings', 'zoner' ); ?></h4>

				<?php do_action( 'zoner_property_quick_edit_start' ); ?>

				<label>
					<span class="title"><?php _e( 'Property ID', 'zoner' ); ?></span>
					<span class="input-text-wrap">
						<input type="text" name="<?php echo $prefix.'reference'; ?>" class="text <?php echo $prefix.'reference'; ?>" value="">
					</span>
				</label>
				<label>
					<span class="title"><?php _e( 'Condition', 'zoner' ); ?></span>
					<span class="input-text-wrap">
						<select class="<?php echo $prefix . 'condition'; ?>" name="<?php echo $prefix . 'condition'; ?>">
						<?php
							$options = $zoner->property->get_condition_values();
							foreach ( $options as $key => $value ) {
								echo '<option value="' . esc_attr( $key ) . '">' . $value . '</option>';
							}
						?>
						</select>
					</span>
				</label>	
				<label>
					<span class="title"><?php _e( 'Payment', 'zoner' ); ?></span>
					<span class="input-text-wrap">
						<select class="<?php echo $prefix . 'payment'; ?>" name="<?php echo $prefix . 'payment'; ?>">
						<?php
							$options = $zoner->property->get_payment_rent_values();
							foreach ( $options as $key => $value ) {
								echo '<option value="' . esc_attr( $key ) . '">' . $value . '</option>';
							}
						?>
						</select>
					</span>
				</label>	
				<label>
					<span class="title"><?php _e( 'Price', 'zoner' ); ?></span>
					<span class="input-text-wrap">
						<input type="text" name="<?php echo $prefix.'price'; ?>" class="text <?php echo $prefix.'price'; ?>" value="">
					</span>
				</label>
				<label>
					<span class="title"><?php _e( 'Price format', 'zoner' ); ?></span>
					<span class="input-text-wrap">
						<select class="<?php echo $prefix . 'price_format'; ?>" name="<?php echo $prefix . 'price_format'; ?>">
						<?php
							$options = $zoner->property->get_price_format_values();
							foreach ( $options as $key => $value ) {
								echo '<option value="' . esc_attr( $key ) . '">' . $value . '</option>';
							}
						?>
						</select>
					</span>
				</label>	
				<label>
					<span class="title"><?php _e( 'Currency', 'zoner' ); ?></span>
					<span class="input-text-wrap">
						<select class="<?php echo $prefix . 'currency'; ?>" name="<?php echo $prefix . 'currency'; ?>">
						<?php
							$options = $zoner->currency->get_zoner_currency_dropdown_settings();
							foreach ( $options as $key => $value ) {
								echo '<option value="' . esc_attr( $key ) . '">' . $value . '</option>';
							}
						?>
						</select>
					</span>
				</label>	
				<label>
					<span class="title"><?php _e( 'Rooms', 'zoner' ); ?></span>
					<span class="input-text-wrap">
						<input type="text" name="<?php echo $prefix.'rooms'; ?>" class="text <?php echo $prefix.'rooms'; ?>" value="">
					</span>
				</label>
				<label>
					<span class="title"><?php _e( 'Beds', 'zoner' ); ?></span>
					<span class="input-text-wrap">
						<input type="text" name="<?php echo $prefix.'beds'; ?>" class="text <?php echo $prefix.'beds'; ?>" value="">
					</span>
				</label>
				<label>
					<span class="title"><?php _e( 'Baths', 'zoner' ); ?></span>
					<span class="input-text-wrap">
						<input type="text" name="<?php echo $prefix.'baths'; ?>" class="text <?php echo $prefix.'baths'; ?>" value="">
					</span>
				</label>
				<label>
					<span class="title"><?php _e( 'Garages', 'zoner' ); ?></span>
					<span class="input-text-wrap">
						<input type="text" name="<?php echo $prefix.'garages'; ?>" class="text <?php echo $prefix.'garages'; ?>" value="">
					</span>
				</label>
				<label>
					<span class="title"><?php _e( 'Area', 'zoner' ); ?></span>
					<span class="input-text-wrap">
						<input type="text" name="<?php echo $prefix.'area'; ?>" class="text <?php echo $prefix.'area'; ?>" value="">
					</span>
				</label>
				<label>
					<span class="title"><?php _e( 'Area units', 'zoner' ); ?></span>
					<span class="input-text-wrap">
						<select class="<?php echo $prefix . 'area_unit'; ?>" name="<?php echo $prefix . 'area_unit'; ?>">
						<?php
							$options = $zoner->property->get_area_units_values();
							foreach ( $options as $key => $value ) {
								echo '<option value="' . esc_attr( $key ) . '">' . $value . '</option>';
							}
						?>
						</select>
					</span>
				</label>	
				
				<br class="clear" />
				
				<label class="featured">
					<input type="checkbox" name="<?php echo $prefix.'is_featured'; ?>" value="1">
					<span class="checkbox-title"><?php _e( 'Property featured', 'zoner' ); ?></span>
				</label>
				<label class="raiting">
					<input type="checkbox" name="<?php echo $prefix.'allow_raiting'; ?>" value="1">
					<span class="checkbox-title"><?php _e( 'Allow user rating', 'zoner' ); ?></span>
				</label>
				
				<?php do_action( 'zoner_property_quick_edit_end' ); ?>

				<input type="hidden" name="zoner_quick_edit" value="1" />
				<input type="hidden" name="zoner_quick_edit_nonce" value="<?php echo wp_create_nonce( 'zoner_quick_edit_nonce' ); ?>" />
			</div>
		</fieldset>
	<?php 
	}
	
	
	public function zoner_baq_es_post( $post_id, $post ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}
		
		if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) {
			return $post_id;
		}

		if ( 'property' != $post->post_type ) {
			return $post_id;
		}
		
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
		}

		if ( ! isset( $_REQUEST['zoner_quick_edit_nonce'] ) && ! isset( $_REQUEST['zoner_bulk_edit_nonce'] ) ) {
			return $post_id;
		}
		if ( isset( $_REQUEST['zoner_quick_edit_nonce'] ) && ! wp_verify_nonce( $_REQUEST['zoner_quick_edit_nonce'], 'zoner_quick_edit_nonce' ) ) {
			return $post_id;
		}
		if ( isset( $_REQUEST['zoner_bulk_edit_nonce'] )  && ! wp_verify_nonce( $_REQUEST['zoner_bulk_edit_nonce'], 'zoner_bulk_edit_nonce' ) ) {
			return $post_id;
		}

		if ( !empty( $_REQUEST['zoner_quick_edit'] ) ) {
			 $this->zoner_quick_edit_save( $post_id);
		} else {
			 $this->zoner_bulk_edit_save( $post_id );
		}
		
		do_action( 'zoner_bulk_and_quick_edit_save', $post_id );

		return $post_id;
	}
	
	function zoner_quick_edit_save($post_id) {
		global $wpdb, $prefix;
		
		if (isset( $_REQUEST[$prefix.'reference']))
		update_post_meta( $post_id, $prefix.'reference', esc_attr($_REQUEST[$prefix.'reference']));
		if (isset( $_REQUEST[$prefix.'condition']))
		update_post_meta( $post_id, $prefix.'condition', esc_attr($_REQUEST[$prefix.'condition']));
		if (isset( $_REQUEST[$prefix.'payment']))
		update_post_meta( $post_id, $prefix.'payment', esc_attr($_REQUEST[$prefix.'payment']));
		if (isset( $_REQUEST[$prefix.'price'])) 
		update_post_meta( $post_id, $prefix.'price', esc_attr($_REQUEST[$prefix.'price']));
		if (isset( $_REQUEST[$prefix.'price_format']))
		update_post_meta( $post_id, $prefix.'price_format', esc_attr($_REQUEST[$prefix.'price_format']));
		if (isset( $_REQUEST[$prefix.'currency']))
		update_post_meta( $post_id, $prefix.'currency', esc_attr($_REQUEST[$prefix.'currency']));
		if (isset( $_REQUEST[$prefix.'rooms']))
		update_post_meta( $post_id, $prefix.'rooms', esc_attr($_REQUEST[$prefix.'rooms']));
		if (isset( $_REQUEST[$prefix.'beds']))
		update_post_meta( $post_id, $prefix.'beds', esc_attr($_REQUEST[$prefix.'beds']));
		if (isset( $_REQUEST[$prefix.'baths']))
		update_post_meta( $post_id, $prefix.'baths', esc_attr($_REQUEST[$prefix.'baths']));
		if (isset( $_REQUEST[$prefix.'garages']))
		update_post_meta( $post_id, $prefix.'garages', esc_attr($_REQUEST[$prefix.'garages']));
		if (isset( $_REQUEST[$prefix.'area']))
		update_post_meta( $post_id, $prefix.'area', esc_attr($_REQUEST[$prefix.'area']));
		if (isset( $_REQUEST[$prefix.'area_unit']))
		update_post_meta( $post_id, $prefix.'area_unit', esc_attr($_REQUEST[$prefix.'area_unit']));
		
		if (isset( $_REQUEST[$prefix.'is_featured'])) {
			update_post_meta( $post_id, $prefix.'is_featured', 'on');
		} else {
			delete_post_meta( $post_id, $prefix.'is_featured');
		}
		
		if (isset( $_REQUEST[$prefix.'allow_raiting'])) {
			update_post_meta( $post_id, $prefix.'allow_raiting', 'on');
		} else {
			delete_post_meta( $post_id, $prefix.'allow_raiting');
		}
		
		do_action( 'zoner_property_quick_edit_save', $post_id );		
	}
	
	function zoner_bulk_edit_save($post_id) {
		global $wpdb, $prefix;
		
		if (!empty( $_REQUEST['reference_change']))
		update_post_meta( $post_id, $prefix.'reference', esc_attr($_REQUEST['reference_change']));
		if (!empty( $_REQUEST['condition_change']))
		update_post_meta( $post_id, $prefix.'condition', esc_attr($_REQUEST['condition_change']));
		if (!empty( $_REQUEST['payment_change']))
		update_post_meta( $post_id, $prefix.'payment', esc_attr($_REQUEST['payment_change']));
		if (!empty( $_REQUEST['price_change'])) 
		update_post_meta( $post_id, $prefix.'price', esc_attr($_REQUEST['price_change']));
		if (!empty( $_REQUEST['price_format_change']))
		update_post_meta( $post_id, $prefix.'price_format', esc_attr($_REQUEST['price_format_change']));
		if (!empty( $_REQUEST['currency_change']))
		update_post_meta( $post_id, $prefix.'currency', esc_attr($_REQUEST['currency_change']));
		if (!empty( $_REQUEST['rooms_change']))
		update_post_meta( $post_id, $prefix.'rooms', esc_attr($_REQUEST['rooms_change']));
		if (!empty( $_REQUEST['beds_change']))
		update_post_meta( $post_id, $prefix.'beds', esc_attr($_REQUEST['beds_change']));
		if (!empty( $_REQUEST['baths_change']))
		update_post_meta( $post_id, $prefix.'baths', esc_attr($_REQUEST['baths_change']));
		if (!empty( $_REQUEST['garages_change']))
		update_post_meta( $post_id, $prefix.'garages', esc_attr($_REQUEST['garages_change']));
		if (!empty( $_REQUEST['area_change']))
		update_post_meta( $post_id, $prefix.'area', esc_attr($_REQUEST['area_change']));
		if (!empty( $_REQUEST['area_unit_change']))
		update_post_meta( $post_id, $prefix.'area_unit', esc_attr($_REQUEST['area_unit_change']));
		
		
		if ( ! empty( $_REQUEST['tax_input']['property_status'] ) ) {
			$property_status = $_REQUEST['tax_input']['property_status'];
			if (is_array($property_status)) {
				foreach ($property_status as $status) {
					if (!empty($status) && $status > 0) {
						
						wp_delete_object_term_relationships( $post_id,  'property_status' ); 
						wp_set_object_terms( $post_id, (int) $status, 'property_status'); 
					}
				}
			}
		}
		
		do_action( 'zoner_property_bulk_edit_save', $_REQUEST);	
	}
}