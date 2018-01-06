<?php
/**
Zoner custom taxonomies field
 */
class zoner_taxonomies {

	public function __construct() {
		global $wpdb;	
		$type = 'zoner_term';
		$table_name = $wpdb->prefix . $type . 'meta';

		$this->create_metadata_table($table_name, $type);
		
		$variable_name = $type . 'meta';
		$wpdb->$variable_name = $table_name;
		
		
		add_action( "delete_term", array( $this, 'zoner_delete_term' ), 99 );
		
		add_action( 'property_type_add_form_fields', 		array( $this, 'zoner_add_category_fields' ) );
		add_action( 'property_type_edit_form_fields', 		array( $this, 'zoner_edit_category_fields' ), 10, 2 );
		
		add_filter( 'manage_edit-property_type_columns', 	array( $this, 'prop_type_columns' ) );
		add_filter( 'manage_property_type_custom_column', 	array( $this, 'prop_type_column' ), 10, 3 );
		
		add_action( 'created_term', array( $this, 'zoner_save_category_fields' ), 10, 5 );
		add_action( 'edit_term', 	array( $this, 'zoner_save_category_fields' ), 10, 5 );
		
	}
	
	public function zoner_delete_term( $term_id ) {
		$term_id = (int) $term_id;
		if ( ! $term_id ) return;
		global $wpdb;
		$wpdb->query( "DELETE FROM {$wpdb->zoner_termmeta} WHERE `zoner_term_id` = " . $term_id );
	}
	
	public function zoner_save_category_fields($term_id) {
		if ( isset( $_POST['property_type_thumbnail_id'] ) ) {
			$this->update_zoner_term_meta( $term_id, 'thumbnail_id', absint( $_POST['property_type_thumbnail_id'] ) );
		}	
	}
	
	public function create_metadata_table($table_name, $type) {
		global $wpdb;
 
		if ( ! empty( $wpdb->charset ) ) { $charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset}"; }
		if ( ! empty( $wpdb->collate ) ) { $charset_collate .= " COLLATE {$wpdb->collate}"; }
		
		if( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) != $table_name ) {
		
			$sql = '';     
			$sql = "CREATE TABLE $table_name (
					meta_id bigint(20) NOT NULL AUTO_INCREMENT,
					{$type}_id bigint(20) NOT NULL default 0,
					meta_key varchar(255) DEFAULT NULL,
					meta_value longtext DEFAULT NULL,
					UNIQUE KEY meta_id (meta_id)
			) $charset_collate;";
     
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($sql);
		}
	}
	
	
	function zoner_placeholder_img_src() {
		return get_template_directory_uri() . '/includes/admin/classes/res/placeholder.jpg';
	}


	public function zoner_add_category_fields() {
		?>
		<div class="form-field">
			<label><?php _e( 'Thumbnail', 'zoner' ); ?></label>
			<div id="property_type_thumbnail" style="float:left;margin-right:10px;"><img src="<?php echo $this->zoner_placeholder_img_src(); ?>" width="100%"/></div>
			<div style="line-height:60px;">
				<input  type="hidden" id="property_type_thumbnail_id" name="property_type_thumbnail_id" />
				<button type="button" class="upload_image_button button"><?php _e( 'Upload/Add image', 'zoner' ); ?></button>
				<button type="button" class="remove_image_button button"><?php _e( 'Remove image', 'zoner' ); ?></button>
			</div>
			<script type="text/javascript">

				 if ( ! jQuery('#property_type_thumbnail_id').val() )
						jQuery('.remove_image_button').hide();


				var file_frame;

				jQuery(document).on( 'click', '.upload_image_button', function( event ){
					event.preventDefault();
					if ( file_frame ) {
						 file_frame.open();
						return;
					}

					// Create the media frame.
					file_frame = wp.media.frames.downloadable_file = wp.media({
						title: '<?php _e( 'Choose an image', 'zoner' ); ?>',
						button: {
							text: '<?php _e( 'Use image', 'zoner' ); ?>',
						},
						multiple: false
					});

					file_frame.on( 'select', function() {
						attachment = file_frame.state().get('selection').first().toJSON();

						jQuery('#property_type_thumbnail_id').val( attachment.id );
						jQuery('#property_type_thumbnail img').attr('src', attachment.url );
						jQuery('.remove_image_button').show();
					});

					file_frame.open();
				});

				jQuery(document).on( 'click', '.remove_image_button', function( event ){
					jQuery('#property_type_thumbnail img').attr('src', '<?php echo $this->zoner_placeholder_img_src(); ?>');
					jQuery('#property_type_thumbnail_id').val('');
					jQuery('.remove_image_button').hide();
					return false;
				});

			</script>
			<div class="clear"></div>
		</div>
		<?php
	}
	
	public function get_zoner_term_meta( $term_id, $key, $single = true ) {
		return get_metadata( 'zoner_term', $term_id, $key, $single );
	}
	
	public function update_zoner_term_meta( $term_id, $meta_key, $meta_value, $prev_value = '' ) {
		return update_metadata( 'zoner_term', $term_id, $meta_key, $meta_value, $prev_value );
	}

	public function zoner_edit_category_fields( $term, $taxonomy ) {
		$image 			= '';
		$thumbnail_id 	= absint( $this->get_zoner_term_meta( $term->term_id, 'thumbnail_id', true ) );
		if ( $thumbnail_id )
			$image = wp_get_attachment_thumb_url( $thumbnail_id );
		else
			$image = $this->zoner_placeholder_img_src();
		?>
		
		<tr class="form-field">
			<th scope="row" valign="top"><label><?php _e( 'Thumbnail', 'zoner' ); ?></label></th>
			<td>
				<div id="property_type_thumbnail" style="float:left; margin-right:10px;"><img src="<?php echo $image; ?>" width="100%"/></div>
				<div style="line-height:60px;">
					<input type="hidden" id="property_type_thumbnail_id" name="property_type_thumbnail_id" value="<?php echo $thumbnail_id; ?>" />
					<button type="submit" class="upload_image_button button"><?php _e( 'Upload/Add image', 'zoner' ); ?></button>
					<button type="submit" class="remove_image_button button"><?php _e( 'Remove image', 'zoner' ); ?></button>
				</div>
				<script type="text/javascript">
					var file_frame;

					jQuery(document).on( 'click', '.upload_image_button', function( event ){

						event.preventDefault();

						// If the media frame already exists, reopen it.
						if ( file_frame ) {
							 file_frame.open();
							return;
						}

						// Create the media frame.
						file_frame = wp.media.frames.downloadable_file = wp.media({
							title: '<?php _e( 'Choose an image', 'zoner' ); ?>',
							button: {
								text: '<?php _e( 'Use image', 'zoner' ); ?>',
							},
							multiple: false
						});

						// When an image is selected, run a callback.
						file_frame.on( 'select', function() {
							attachment = file_frame.state().get('selection').first().toJSON();

							jQuery('#property_type_thumbnail_id').val( attachment.id );
							jQuery('#property_type_thumbnail img').attr('src', attachment.url );
							jQuery('.remove_image_button').show();
						});

						// Finally, open the modal.
						file_frame.open();
					});

					jQuery(document).on( 'click', '.remove_image_button', function( event ){
						jQuery('#property_type_thumbnail img').attr('src', '<?php echo $this->zoner_placeholder_img_src(); ?>');
						jQuery('#property_type_thumbnail_id').val('');
						jQuery('.remove_image_button').hide();
						return false;
					});

				</script>
				<div class="clear"></div>
			</td>
		</tr>
		<?php
	}
	
	public function prop_type_columns( $columns ) {
		$new_columns          = array();
		$new_columns['cb']    = $columns['cb'];
		$new_columns['thumb'] = __( 'Image', 'zoner' );

		unset( $columns['cb'] );

		return array_merge( $new_columns, $columns );
	}

	/**
	 * Thumbnail column value added to category admin.
	 */
	 
	public function prop_type_column( $columns, $column, $id ) {
		if ( $column == 'thumb' ) {
				$image = '';
				$thumbnail_id 	= $this->get_zoner_term_meta( $id, 'thumbnail_id', true );

				if ($thumbnail_id)
					$image = wp_get_attachment_thumb_url( $thumbnail_id );
				else
					$image = $this->zoner_placeholder_img_src();
				$image = str_replace( ' ', '%20', $image );
				$columns .= '<img src="' . esc_url( $image ) . '" alt="Thumbnail" class="wp-post-image" height="48" width="48" />';
		}
		return $columns;
	}
	
}	