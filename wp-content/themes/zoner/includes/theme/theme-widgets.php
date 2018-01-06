<?php 
/**
 * Search property
 * Zoner widget
 *
 */
class Zoner_Widget_Search_Property extends WP_Widget {

	function __construct() {
		$widget_ops = array('classname' => 'search-property', 'description' => __( "Search property.", 'zoner') );
		parent::__construct('zoner-wsp', __('Zoner Search Property', 'zoner'), $widget_ops);
		$this->alt_option_name = 'zoner-wsp';

		add_action( 'save_post',    array($this, 'flush_widget_cache') );
		add_action( 'deleted_post', array($this, 'flush_widget_cache') );
		add_action( 'switch_theme', array($this, 'flush_widget_cache') );
	}

	function widget($args, $instance) {
		global $zoner, $zoner_config, $prefix, $wp_query;
		
		$cache = wp_cache_get('widget_search_property', 'widget');

		if ( !is_array($cache) ) $cache = array();

		if ( ! isset( $args['widget_id'] ) )
			$args['widget_id'] = $this->id;

		if ( isset( $cache[ $args['widget_id'] ] ) ) {
			echo $cache[ $args['widget_id'] ];
			return;
		}

		ob_start();
		extract($args);

		$title = ( !empty( $instance['title'] ) ) ? $instance['title'] : __( 'Search Properties', 'zoner' );
		$advanced_search  	= !empty( $instance['advanced-search'] )   ? 1 : 0;		
		$specific_features  = !empty( $instance['specific-features'] ) ? 1 : 0;		
		
		$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );
		$args_tax = array(
						'orderby' 	=> 'name', 
						'order'		=> 'ASC',
						'hide_empty' => false, 
					); 
		
		$property_type   = $property_status = array();
		$property_type 	 = get_terms('property_type', $args_tax);
		$property_status = get_terms('property_status', $args_tax);
		$property_cat 	 = get_terms('property_cat', $args_tax);
		
		$min_price = $zoner->currency->zoner_get_price_("MIN", $prefix.'price');
		$max_price = $zoner->currency->zoner_get_price_("MAX", $prefix.'price');
		
		$all_city  	    = get_terms( 'property_city');;
		$all_districts  = $zoner->countries->zoner_get_all_metadata_($prefix.'district');
		
		$submit_text = trim($zoner_config['zoner-searchbox-submit']);
		$submit_text = (!empty($submit_text)) ? $submit_text : __('Search Now', 'zoner');
		$submit_text = esc_html($submit_text);
		
		if (!empty($zoner_config['zoner-searchbox']))
		$search_fields = $zoner_config['zoner-searchbox'];
	
		$property_archive = null;
		$archivePropertyPage = $zoner->zoner_get_page_id('page-property-archive');
		if (!empty($archivePropertyPage)) {
			$property_archive = $archivePropertyPage;
		}
		
		$filter_pid = $filter_zip = $filter_keyword = $filter_status = $filter_city = $filter_price = $filter_district = $filter_type = $filter_country = $filter_area = $filter_cat = '';
		$condition  = $payment = $rooms = $beds = $baths = $garages = $features =  $sorting  = $page_id = '';
		$min_price  = 0;

		$price_req = false;
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
			if (!empty($_GET['sb-price'])) 		$price 		= $_GET['sb-price'];
			if (!empty($_GET['sb-features'])) 	$features 	= $_GET['sb-features'];
			//if not empty all just redirect on this page
			if (!empty($_GET['sb-price-req']))
				$price_req 	= true;
			
			if (!empty($price)) {
				$filter_price 	=  explode(';', $price);
				$min_price = (int)$filter_price[0];
				$max_price = (int)$filter_price[1];
			}			
			
		}
		
		if (is_tax( 'property_city')) {
			$filter_city = $wp_query->get_queried_object_id();
		}
		
		if (isset($_GET) && isset($_GET['sorting'])) $sorting = $_GET['sorting'];
		if (isset($_GET) && isset($_GET['page_id'])) $page_id = $_GET['page_id'];
		
		$perma_struct = get_option( 'permalink_structure' );
		$link 		  = get_permalink($zoner->zoner_get_page_id('page-property-archive'));
		if ($perma_struct == '' || empty($property_archive)) {
			$link = get_post_type_archive_link('property');
		}
		
		?>
		
		<?php echo $before_widget; ?>
		<?php if ( $title ) echo $before_title . $title . $after_title; ?>

		<form role="form" id="form-sidebar" class="form-search" action="<?php echo $link; ?>" method="GET">
			<?php 
				wp_nonce_field( 'zoner_filter_property', 'filter_property', false, true ); 
				
				if ($perma_struct == "") echo '<input type="hidden" name="post_type" value="property"/>';
				if ($sorting != '')  echo '<input type="hidden" name="sorting" value="'.$sorting.'" />';
				if ($page_id != '')  echo '<input type="hidden" name="page_id" value="'.$page_id.'" />';
				
				if (!empty($features) && !$advanced_search) { 
					foreach ($features as $f) {
						echo '<input type="hidden" name="sb-features[]" value="'.$f.'" />';
					} 
				}	
				
				/*If WPML Parametr exist*/
				if (isset($_GET['lang']) && !empty($_GET['lang'])) 
					echo '<input type="hidden" name="lang" value="'.esc_attr($_GET['lang']).'" />';
				
				if (!empty($search_fields['enabled'])) {
				  foreach ($search_fields['enabled'] as $key => $value) {
			?>
					
					<?php if ($key == 'zip') { ?>
						<div class="form-group">
							<input type="text" class="form-control" id="sb-zip" name='sb-zip' value="<?php echo $filter_zip; ?>" placeholder="<?php _e('Zip Code', 'zoner'); ?>">
						</div>
					<?php } ?>	
					
					<?php if ($key == 'keyword') { ?>
						<div class="form-group">
							<input type="text" class="form-control" id="sb-keyword" name='sb-keyword' value="<?php echo $filter_keyword; ?>" placeholder="<?php _e('Keyword', 'zoner'); ?>">
						</div>
					<?php } ?>	
					
					<?php if ($key == 'area') { ?>
						<div class="form-group">
							<input type="text" class="form-control" id="sb-area" name='sb-area' value="<?php echo $filter_area; ?>" placeholder="<?php _e('Min. area', 'zoner'); ?>">
						</div>
					<?php } ?>	
					
					<?php if ($key == 'category') { ?>
						<div class="form-group">
							<select id="property_cat" class="property_cat" name="sb-cat">
								<option value=""><?php _e('Category', 'zoner'); ?></option>
								<?php 
									if (!empty($property_cat))  { 
										foreach ($property_cat as $cat) {
											echo '<option value="'.$cat->term_id.'" '.selected( $filter_cat, $cat->term_id, false) .'>'.$cat->name .' (' . $cat->count .')'.'</option>';
										}
									}
								?>
							</select>
						</div><!-- /.form-group -->
					<?php } ?>	
					
					<?php if ($key == 'status') { ?>
						<div class="form-group">
							<select id="property_status" class="property_status" name="sb-status">
								<option value=""><?php _e('Status', 'zoner'); ?></option>
								<?php 
									if (!empty($property_status))  { 
										foreach ($property_status as $status) {
											echo '<option value="'.$status->term_id.'" '.selected( $filter_status, $status->term_id, false) .'>'.$status->name.' ('. $status->count .')'.'</option>';
										}
									}
								?>
							</select>
						</div><!-- /.form-group -->
					<?php } ?>	
					
					<?php if ($key == 'type') { ?>					
							<div class="form-group">
								<select id="property_type" class="property_type" name="sb-type">
									<option value=""><?php _e('Type', 'zoner'); ?></option>
									<?php 
										if (!empty($property_type))  { 
											foreach ($property_type as $type) {
											echo '<option value="'.$type->term_id.'" '.selected( $filter_type, $type->term_id, false) .'>'.$type->name.' ('. $type->count .')'.'</option>';
										}
									}
									?>
								</select>
							</div><!-- /.form-group -->
					<?php } ?>		
					
					<?php if ($key == 'country') { ?>					
						<div class="form-group">
							<select id="property-country" name="sb-country">
								<option value=""><?php _e('Country', 'zoner'); ?></option>
								<?php echo $zoner->countries->country_specific_dropdown_list($filter_country); ?>
							</select>
						</div><!-- /.form-group -->
					<?php } ?>	
					
					<?php if ($key == 'city') { ?>					
						<div class="form-group">
							<select id="property-city" name="sb-city">
								<option value=""><?php _e('City', 'zoner'); ?></option>
								<?php 
									if (!empty($all_city))  { 
										foreach ($all_city as $city) {
											echo '<option value="' . $city->term_id .'" '. selected( $filter_city, $city->term_id, false) .'>'.$city->name.' ('. $city->count .')'.'</option>';
										}
									}
								?>
							</select>
						</div><!-- /.form-group -->
					<?php } ?>	
					
					
					<?php if ($key == 'district') { ?>					
						<div class="form-group">
							<select id="property-district" name="sb-district">
								<option value=""><?php _e('District', 'zoner'); ?></option>
								<?php 
									if (!empty($all_districts))  { 
										foreach ($all_districts as $district) {
											echo '<option value="'.$district.'" '.selected( $filter_district, $district, false) .'>'.$district.'</option>';
										}
									}
								?>
							</select>
						</div><!-- /.form-group -->
					<?php } ?>	
					
					<?php if ($key == 'price') { ?>
						  <div class="form-group">
							  <div class="checkbox"><label><input type="checkbox"  name="sb-price-req" " value="yes" <?php checked( $price_req, true, true ); ?>><?php _e('Show price on request', 'zoner') ?></label></div>
						  </div>
						<div class="form-group">
							<div class="price-range">
								<input id="price-input" class="price-input" type="text" name="sb-price" value="<?php echo $min_price. ';' . $max_price; ?>">
							</div>
						</div>
					<?php } ?>	
					
					
					
					<?php 
						if ($key == 'condition') { 
							$options = array();
							$options = $zoner->property->get_condition_values();
					?>
						<div class="form-group">
							<select id="property_condition" name="sb-condition">
								<option value=""><?php _e('Condition', 'zoner'); ?></option>
								<?php 
									if (!empty($options))  { 
										foreach ($options as $key => $option) {
											echo '<option value="'.$key.'" '.selected( $condition, $key, false) .'>'.$option.'</option>';
										}
									}
								?>
							</select>
						</div><!-- /.form-group -->
					<?php } ?>
					
					<?php 
						if ($key == 'payment') { 
							$options = $zoner->property->get_payment_rent_values();
					?>
						<div class="form-group">
							<select id="property_payment" name="sb-payment">
								<option value=""><?php _e('Payment', 'zoner'); ?></option>
								<?php 
									if (!empty($options))  { 
										foreach ($options as $key => $option) {
											echo '<option value="'.$key.'" '.selected( $payment, $key, false) .'>'.$option.'</option>';
										}
									}
								?>
							</select>
						</div><!-- /.form-group -->
					<?php } ?>
					
					<?php 
						if ($key == 'rooms') { 
							$list_rooms    = $zoner->property->zoner_get_custom_meta($prefix . 'rooms');
							$options = array();
							foreach ($list_rooms as $room) {
								$options[$room->meta_value] = $room->meta_value;
							}
					?>
						<div class="form-group">
							<select id="property_rooms" name="sb-rooms">
								<option value=""><?php _e('Rooms', 'zoner'); ?></option>
								<?php 
									if (!empty($options))  { 
										foreach ($options as $key => $option) {
											echo '<option value="'.$key.'" '.selected( $rooms, $key, false) .'>'.$option.'</option>';
										}
									}
								?>
							</select>
						</div><!-- /.form-group -->
					<?php } ?>					
					
					<?php 
						if ($key == 'beds') { 
							$list_beds    = $zoner->property->zoner_get_custom_meta($prefix . 'beds');
							$options = array();
							foreach ($list_beds as $bed) {
								$options[$bed->meta_value] = $bed->meta_value;
							}
					?>
						<div class="form-group">
							<select id="property_beds" name="sb-beds">
								<option value=""><?php _e('Beds', 'zoner'); ?></option>
								<?php 
									if (!empty($options))  { 
										foreach ($options as $key => $option) {
											echo '<option value="'.$key.'" '.selected( $beds, $key, false) .'>'.$option.'</option>';
										}
									}
								?>
							</select>
						</div><!-- /.form-group -->
					<?php } ?>					
					
					
					<?php 
						if ($key == 'baths') { 
							$list_baths = $zoner->property->zoner_get_custom_meta($prefix . 'baths');
							$options = array();
							foreach ($list_baths as $bath) {
								$options[$bath->meta_value] = $bath->meta_value;
							}
					?>
						<div class="form-group">
							<select id="property_bath" name="sb-baths">
								<option value=""><?php _e('Baths', 'zoner'); ?></option>
								<?php 
									if (!empty($options))  { 
										foreach ($options as $key => $option) {
											echo '<option value="'.$key.'" '.selected( $baths, $key, false) .'>'.$option.'</option>';
										}
									}
								?>
							</select>
						</div><!-- /.form-group -->
					<?php } ?>					
					
					<?php 
						if ($key == 'garages') { 
							$list_garages = $zoner->property->zoner_get_custom_meta($prefix . 'garages');
							$options = array();
							foreach ($list_garages as $garage) {
								$options[$garage->meta_value] = $garage->meta_value;
							}
					?>
						<div class="form-group">
							<select id="property_garages" name="sb-garages">
								<option value=""><?php _e('Garages', 'zoner'); ?></option>
								<?php 
									if (!empty($options))  { 
										foreach ($options as $key => $option) {
											echo '<option value="'.$key.'" '.selected( $garages, $key, false) .'>'.$option.'</option>';
										}
									}
								?>
							</select>
						</div><!-- /.form-group -->
					<?php } ?>		
					
					<?php 
						/*Custom Search Fields*/
						do_action('zoner_add_custom_search_fields', $key, null);					
					?>
				<?php 
					}
				}
				
				/*Add advanced features items for  search widget*/
				
				$features_include = array();
					if ($specific_features && !empty($zoner_config['specific-features']))
						$features_include = $zoner_config['specific-features'];	
				
					$taxonomies = array( 'property_features' );
					$args_taxonomies = array( 
										'orderby' 	 => 'name', 
										'order' 	 => 'ASC',
										'hide_empty' => false,
										'include'    => $features_include
									); 
			
					$prop_features = array();
					$prop_features = get_terms($taxonomies,  $args_taxonomies);
					
				if (!empty($prop_features) && $advanced_search) { 
				$rand_id=rand();
					echo '<div class="form-group">';
						echo '<div class="search-box show-search-box">';
							echo '<a class="advanced-search-toggle" data-toggle="collapse" data-parent="#accordion" href="#advanced-search-sale-'.$rand_id.'">'.__('Advanced Search', 'zoner').'<i class="fa fa-plus"></i></a>';
							
							echo '<div id="advanced-search-sale-'.$rand_id.'" class="panel-collapse collapse">';
								echo '<div class="widget-advanced advanced-search">';
								echo '<hr />';
										
								echo '<ul class="submit-features">';
									foreach ($prop_features as $vals) {
										$is_cheked = false;
										if (!empty($features)) $is_cheked = in_array($vals->term_id, $features);
										
										echo '<li>';
											echo '<div class="checkbox">';
												echo '<label><input type="checkbox" name="sb-features[]" value="'.$vals->term_id.'" '.checked( $is_cheked, true, false ).'>'.$vals->name.'</label>';
											echo '</div>';
										echo '</li>';
									} 
								echo '</ul>';
							echo '</div>';
						echo '</div>';	
					echo '</div>';
				}	
					
				
				?>	
				
				<div class="form-group">
					<button type="submit" class="btn btn-default"><?php echo $submit_text; ?></button>
				</div><!-- /.form-group -->
		</form><!-- /#form-map -->
						
		
		<?php echo $after_widget; ?>
		
		<?php
		
		$cache[$args['widget_id']] = ob_get_flush();
		wp_cache_set('widget_search_property', $cache, 'widget');
	}

	function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['advanced-search']  = !empty($new_instance['advanced-search'])  ? 1 : 0;
		$instance['specific-features'] = !empty($new_instance['specific-features']) ? 1 : 0;
		
		$this->flush_widget_cache();

		$alloptions = wp_cache_get( 'alloptions', 'options' );
		if ( isset($alloptions['zoner-wsp']) )
			delete_option('zoner-wsp');

		return $instance;
	}

	function flush_widget_cache() {
		wp_cache_delete('widget_search_property', 'widget');
	}

	function form( $instance) {
		$title = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
		$advanced_search 	= isset( $instance['advanced-search'] ) ? (bool) $instance['advanced-search'] : false;
		$specific_features 	= isset( $instance['specific-features'] ) ? (bool) $instance['specific-features'] : false;
		
?>
		<p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'zoner' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" /></p>
		
		<p><input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('advanced-search'); ?>" name="<?php echo $this->get_field_name('advanced-search'); ?>"<?php checked( $advanced_search ); ?> />
		<label for="<?php echo $this->get_field_id('advanced-search'); ?>"><?php _e( 'Show advanced search items' , 'zoner'); ?></label></p>
		
		<p><input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('specific-features'); ?>" name="<?php echo $this->get_field_name('specific-features'); ?>"<?php checked( $specific_features ); ?> />
		<label for="<?php echo $this->get_field_id('specific-features'); ?>"><?php _e( 'Filtered items by "Specific Features"' , 'zoner'); ?></label></p>
		
<?php
	}
}


/**
 * Search Featured Property
 * Zoner widget
 *
 */
class Zoner_Widget_Featured_Property extends WP_Widget {

	function __construct() {
		$widget_ops = array('classname' => 'search-properties', 'description' => __( "Featured Properties", 'zoner') );
		parent::__construct('zoner-wfp', __('Zoner Featured Properties', 'zoner'), $widget_ops);
		$this->alt_option_name = 'zoner-wfp';

		add_action( 'save_post',    array($this, 'flush_widget_cache') );
		add_action( 'deleted_post', array($this, 'flush_widget_cache') );
		add_action( 'switch_theme', array($this, 'flush_widget_cache') );
	}

	function widget($args, $instance) {
		global $zoner, $zoner_config, $prefix;
		
		$cache = wp_cache_get('widget_featured_properties', 'widget');

		if ( !is_array($cache) ) $cache = array();

		if ( ! isset( $args['widget_id'] ) )
			$args['widget_id'] = $this->id;

		if ( isset( $cache[ $args['widget_id'] ] ) ) {
			echo $cache[ $args['widget_id'] ];
			return;
		}

		ob_start();
		extract($args);

		$title = ( ! empty( $instance['title'] ) ) ? $instance['title'] : __( 'Featured Properties', 'zoner' );
		$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );
		$number = ( ! empty( $instance['number'] ) ) ? absint( $instance['number'] ) : 10;
		
		
		
		$r = new WP_Query( 
							apply_filters( 'widget_featured_property_args', 
							array( 
									'post_type' 		=> 'property',
									'posts_per_page' 	=> $number, 
									'no_found_rows' 	=> true, 
									'post_status' 		=> 'publish', 
									'orderby'			=> 'rand',
									'ignore_sticky_posts' => true, 
									'meta_query' => array(
															array(
																'key' 	=> $prefix . 'is_featured',
																'value' => 'on'
																)
														)
									) 
							) 
						);
		if ($r->have_posts()) :
		
		?>
		
		
		<?php echo $before_widget; ?>
		<?php if ( $title ) echo $before_title . $title . $after_title; ?>
						
		
		<?php 
		
			while ( $r->have_posts() ) : $r->the_post(); 
				$gproperty = array();
				$id_ = get_the_ID();
				
				$gproperty  = $zoner->property->get_property($id_);
				$price 		= $gproperty->price;
				$address 	= $gproperty->address;
				$full_address 	= $gproperty->full_address;
				$city		= $gproperty->city;
				$zip		= $gproperty->zip;
			
				$currency	= $gproperty->currency;
				$price_html = $gproperty->price_html;
		?>
		
			<div id="property-<?php echo $id_; ?>" class="property small">
				<a href="<?php the_permalink(); ?>">
					<div class="property-image">
						<?php 
							if (has_post_thumbnail()) { 
								$attachment_id 	  = get_post_thumbnail_id( $id_ );
								$image_attributes = wp_get_attachment_image_src( $attachment_id, 'zoner-footer-thumbnails');
						?>
							<img class="img-responsive" src="<?php echo $image_attributes[0]; ?>" alt="" />
						<?php } else { ?>	
							<img width="100%" class="img-responsive" data-src="holder.js/440x330?auto=yes&text=<?php _e('Property', 'zoner'); ?>" alt="" />
						<?php } ?>
                    </div>
                </a>
                <div class="info">
					<a href="<?php the_permalink(); ?>"><h4><?php the_title(); ?></h4></a>
                    <figure><?php echo $full_address; ?></figure>
                    <?php echo $price_html; ?>
                </div>
            </div><!-- /.property -->
			
		<?php endwhile; ?>
		
		<?php echo $after_widget; ?>
		
		<?php
		
		endif;
		
		wp_reset_postdata();
		
		$cache[$args['widget_id']] = ob_get_flush();
		wp_cache_set('widget_featured_properties', $cache, 'widget');
	}

	function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['number'] = (int) $new_instance['number'];
		
		$this->flush_widget_cache();

		$alloptions = wp_cache_get( 'alloptions', 'options' );
		if ( isset($alloptions['zoner-wfp']) )
			delete_option('zoner-wfp');

		return $instance;
	}

	function flush_widget_cache() {
		wp_cache_delete('widget_featured_properties', 'widget');
	}

	function form( $instance) {
		$title     = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
		$number    = isset( $instance['number'] ) ? absint( $instance['number'] ) : 5;
		
?>
		<p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'zoner' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" /></p>
		
		<p><label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php _e( 'Number of properties to show:', 'zoner' ); ?></label>
		<input id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="text" value="<?php echo $number; ?>" size="3" /></p>
		
<?php
	}
}


/**
 * Search Recent Property
 * Zoner widget
 *
 */
class Zoner_Widget_Recent_Property extends WP_Widget {

	function __construct() {
		$widget_ops = array('classname' => 'recent-properties', 'description' => __( "Recent Properties", 'zoner') );
		parent::__construct('zoner-wrp', __('Zoner Recent Properties', 'zoner'), $widget_ops);
		$this->alt_option_name = 'zoner-wrp';

		add_action( 'save_post',    array($this, 'flush_widget_cache') );
		add_action( 'deleted_post', array($this, 'flush_widget_cache') );
		add_action( 'switch_theme', array($this, 'flush_widget_cache') );
	}

	function widget($args, $instance) {
		global $zoner, $zoner_config, $prefix;
		
		$cache = wp_cache_get('widget_recent_properties', 'widget');

		if ( !is_array($cache) ) $cache = array();

		if ( ! isset( $args['widget_id'] ) )
			$args['widget_id'] = $this->id;

		if ( isset( $cache[ $args['widget_id'] ] ) ) {
			echo $cache[ $args['widget_id'] ];
			return;
		}

		ob_start();
		extract($args);
		

		$title = ( ! empty( $instance['title'] ) ) ? $instance['title'] : __( 'Recent Properties', 'zoner' );
		$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );
		$number = ( ! empty( $instance['number'] ) ) ? absint( $instance['number'] ) : 10;
		$order  = ( ! empty( $instance['order'] ) )  ? $instance['order'] : 'ASC';

		
		$r = new WP_Query( 
							apply_filters( 'widget_recent_property_args', 
							array( 
									'post_type' => 'property',
									'posts_per_page' => $number, 
									'no_found_rows'	=> true, 
									'post_status' => 'publish', 
									'order' => $order,
									'orderby' => 'DATE',
									'ignore_sticky_posts' => true, 
									) 
							) 
						);
		if ($r->have_posts()) :
		
		?>
		
		
		<?php echo $before_widget; ?>
		<?php if ( $title ) echo $before_title . $title . $after_title; ?>
						
		
		<?php 
		
			while ( $r->have_posts() ) : $r->the_post(); 
				$gproperty = array();
				$id_ = get_the_ID();
				$gproperty  = $zoner->property->get_property($id_);
				
				$price 		= $gproperty->price;
				$address 	= $gproperty->address;
				$full_address 	= $gproperty->full_address;
				$city		= $gproperty->city;
				$zip		= $gproperty->zip;
			
				$currency	= $gproperty->currency;
				$price_html = $gproperty->price_html;
		
		?>
		
			<div id="property-<?php echo $id_; ?>" class="property small">
				<a href="<?php the_permalink(); ?>">
					<div class="property-image">
						<?php 
							if (has_post_thumbnail()) { 
								$attachment_id 	  = get_post_thumbnail_id( $id_ );
								$image_attributes = wp_get_attachment_image_src( $attachment_id, 'zoner-footer-thumbnails');
						?>
							<img class="img-responsive" src="<?php echo $image_attributes[0]; ?>" alt="" />
						<?php } else { ?>	
							<img width="100%" class="img-responsive" data-src="holder.js/440x330?auto=yes&text=<?php _e('Property', 'zoner'); ?>" alt="" />
						<?php } ?>
                    </div>
                </a>
                <div class="info">
					<a href="<?php the_permalink(); ?>"><h4><?php the_title(); ?></h4></a>
                    <figure><?php echo $full_address; ?></figure>
                    <?php echo $price_html; ?>
                </div>
            </div><!-- /.property -->
			
		<?php endwhile; ?>
		
		<?php echo $after_widget; ?>
		
		<?php
		
		endif;
		
		wp_reset_postdata();
		
		$cache[$args['widget_id']] = ob_get_flush();
		wp_cache_set('widget_recent_properties', $cache, 'widget');
	}

	function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['number'] = (int) $new_instance['number'];
		$instance['order'] = esc_attr ($new_instance['order']);
		
		$this->flush_widget_cache();

		$alloptions = wp_cache_get( 'alloptions', 'options' );
		if ( isset($alloptions['zoner-wrp']) )
			delete_option('zoner-wrp');

		return $instance;
	}

	function flush_widget_cache() {
		wp_cache_delete('widget_recent_properties', 'widget');
	}

	function form( $instance) {
		$title     = isset( $instance['title'] )  ? esc_attr( $instance['title']) : '';
		$number    = isset( $instance['number'] ) ?  absint ( $instance['number']) : 5;
		$order	   = isset( $instance['order'] )  ? esc_attr( $instance['order']) : 'ASC';
		
?>
		<p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'zoner' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" /></p>
		
		<p><label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php _e( 'Number of properties to show:', 'zoner' ); ?></label>
		<input id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="text" value="<?php echo $number; ?>" size="3" /></p>
		
		<p><label for="<?php echo $this->get_field_id( 'order' ); ?>"><?php _e( 'Order:', 'zoner' ); ?></label>
		 <select id="<?php echo $this->get_field_id( 'order' ); ?>" name="<?php echo $this->get_field_name( 'order' ); ?>" class="widefat" style="width:100%;">
		    <option <?php if ( 'ASC'  == $order ) echo 'selected="selected"'; ?> value="ASC"><?php _e('ASC', 'zoner'); ?></option>
			<option <?php if ( 'DESC' == $order ) echo 'selected="selected"'; ?> value="DESC"><?php _e('DESC', 'zoner'); ?></option>
         </select>
		</p>
		
<?php
	}
}

/**
 * Zoner Categories
 * Zoner widget
 *
 */
class Zoner_Widget_Categories extends WP_Widget {

	function __construct() {
		$widget_ops = array('classname' => 'zoner-property_categories', 'description' => __( "A list or dropdown of product categories.", 'zoner') );
		parent::__construct('zoner-wrc', __('Zoner Property Categories', 'zoner'), $widget_ops);
		$this->alt_option_name = 'zoner-wrc';

		add_action( 'save_post',    array($this, 'flush_widget_cache') );
		add_action( 'deleted_post', array($this, 'flush_widget_cache') );
		add_action( 'switch_theme', array($this, 'flush_widget_cache') );
	}

	function widget($args, $instance) {
		global $zoner, $zoner_config, $prefix;
		
		$cache = wp_cache_get('widget_property_categories', 'widget');

		if ( !is_array($cache) ) $cache = array();

		if ( ! isset( $args['widget_id'] ) )
			$args['widget_id'] = $this->id;

		if ( isset( $cache[ $args['widget_id'] ] ) ) {
			echo $cache[ $args['widget_id'] ];
			return;
		}

		ob_start();
		extract($args);
		
		$title 	= (! empty( $instance['title'] ) ) ? $instance['title'] : __( 'Property Categories', 'zoner' );
		$tax   	= (! empty( $instance['property-tax'] ) ) ? $instance['property-tax'] : 'property_cat';
		$hierarchical	= ! empty( $instance['hierarchical'] ) ? '1' : '0';
		$hide_empty	= ! empty( $instance['hide_empty'] ) ? '1' : '0'; 
		$add_icons 	= ! empty( $instance['add_icons'] ) ? '1' : '0'; 
		echo $before_widget; 
		if ( $title ) echo $before_title . $title . $after_title; 	
		
		echo '<ul>';
		$this->hierarchical_category_list($tax, 0, $hierarchical, $hide_empty, $add_icons);
		echo '</ul>';
		
		echo $after_widget; 
		
		$cache[$args['widget_id']] = ob_get_flush();
		wp_cache_set('widget_property_categories', $cache, 'widget');
	}

	private function hierarchical_category_list($taxonomy_name, $root, $hierarchical, $hide_empty, $add_icons) {
		global $wp_query, $zoner;
  		 $next  = get_categories('taxonomy='.$taxonomy_name.'&hierarchical='.$hierarchical.'&hide_empty='.$hide_empty.'&parent='.$root);
		  if($next){   
  		  foreach( $next  as $cat ){
  		  	if (!empty($cat->term_id) && !empty($cat->slug) ){
  		  		$tax_attachment_id = $zoner->zoner_tax->get_zoner_term_meta($cat->term_id, 'thumbnail_id');
		 		$tax_image = wp_get_attachment_image_src($tax_attachment_id, 'full');
		 		$tax_image_html = '';
		 		if (!empty($tax_image) && !empty($add_icons)) $tax_image_html  = '<img width="26px" height="26px" src="'.$tax_image[0].'" alt="'.$cat->name.'" />';
	  		  	echo '<li><a href="'.get_category_link( $cat->term_id ).'">' . $tax_image_html . $cat->name .'</a></li>';
  		  		if (!empty($hierarchical)) echo '<ul>';
	  		  	$this->hierarchical_category_list($taxonomy_name, $cat->term_id, $hierarchical, $hide_empty, $add_icons);
	  		  	if (!empty($hierarchical)) echo '</ul>';
  		  	}
   		  }
   		}
	}

	function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['property-tax']  = isset( $new_instance['property-tax'] )  ? esc_attr( $new_instance['property-tax']) : 'property_cat';
		$instance['hierarchical'] = !empty($new_instance['hierarchical']) ? 1 : 0;
		$instance['hide_empty'] = !empty($new_instance['hide_empty']) ? 1 : 0;
		$instance['add_icons'] = !empty($new_instance['add_icons']) ? 1 : 0;
		
		$this->flush_widget_cache();

		$alloptions = wp_cache_get( 'alloptions', 'options' );
		if ( isset($alloptions['zoner-wrc']) )
			delete_option('zoner-wrc');

		return $instance;
	}

	function flush_widget_cache() {
		wp_cache_delete('widget_property_categories', 'widget');
	}

	function form( $instance) {
		$title     		= isset( $instance['title'] )  ? esc_attr( $instance['title']) : __('Property Categories', 'zoner');
		$tax	   		= isset( $instance['property-tax'] )  ? esc_attr( $instance['property-tax']) : 'property_cat';
		$hierarchical 	= isset( $instance['hierarchical'] ) ? (bool) $instance['hierarchical'] : false;
		$hide_empty 	= isset( $instance['hide_empty'] ) ? (bool) $instance['hide_empty'] : false;
		$add_icons 		= isset( $instance['add_icons'] ) ? (bool) $instance['add_icons'] : false;
		
?>
		<p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'zoner' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" /></p>
		
		<p><label  for="<?php echo $this->get_field_id( 'property-tax' ); ?>"><?php _e( 'Choose taxonomy:', 'zoner' ); ?></label>
			<select id="<?php echo $this->get_field_id( 'property-tax' ); ?>" name="<?php echo $this->get_field_name( 'property-tax' ); ?>" class="widefat" style="width:100%;">
				<option <?php if ( 'property_cat'  		== $tax ) echo 'selected="selected"'; ?> value="property_cat"><?php _e('Property Category', 'zoner'); ?></option>
				<option <?php if ( 'property_status'  	== $tax ) echo 'selected="selected"'; ?> value="property_status"><?php _e('Property Status', 'zoner'); ?></option>
				<option <?php if ( 'property_type'  	== $tax ) echo 'selected="selected"'; ?> value="property_type"><?php _e('Property Type', 'zoner'); ?></option>
				<option <?php if ( 'property_features'  == $tax ) echo 'selected="selected"'; ?> value="property_features"><?php _e('Property Features', 'zoner'); ?></option>
			</select>
		</p>

		<p><input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('hierarchical'); ?>" name="<?php echo $this->get_field_name('hierarchical'); ?>"<?php checked( $hierarchical ); ?> />
		<label for="<?php echo $this->get_field_id('hierarchical'); ?>"><?php _e( 'Show hierarchy' , 'zoner'); ?></label></p>
		
		<p><input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('hide_empty'); ?>" name="<?php echo $this->get_field_name('hide_empty'); ?>"<?php checked( $hide_empty ); ?> />
		<label for="<?php echo $this->get_field_id('hide_empty'); ?>"><?php _e( 'Hide empty', 'zoner' ); ?></label></p>
		
		<p><input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('add_icons'); ?>" name="<?php echo $this->get_field_name('add_icons'); ?>"<?php checked( $add_icons ); ?> />
		<label for="<?php echo $this->get_field_id('add_icons'); ?>"><?php _e( 'Add icon', 'zoner' ); ?></label></p>
		
<?php
	}
}

/**
 * Zoner Our Guides
 * Zoner widget
 *
 */
class Zoner_Widget_Our_Guides extends WP_Widget {

	function __construct() {
		$widget_ops = array('classname' => 'zoner-property_ourguides', 'description' => __( "Our Guides.", 'zoner') );
		parent::__construct('zoner-wog', __('Zoner Our Guides', 'zoner'), $widget_ops);
		$this->alt_option_name = 'zoner-wog';

		add_action( 'save_post',    array($this, 'flush_widget_cache') );
		add_action( 'deleted_post', array($this, 'flush_widget_cache') );
		add_action( 'switch_theme', array($this, 'flush_widget_cache') );
	}

	function widget($args, $instance) {
		global $zoner, $zoner_config, $prefix;
		
		$cache = wp_cache_get('widget_our_guides', 'widget');

		if ( !is_array($cache) ) $cache = array();

		if ( ! isset( $args['widget_id'] ) )
			$args['widget_id'] = $this->id;

		if ( isset( $cache[ $args['widget_id'] ] ) ) {
			echo $cache[ $args['widget_id'] ];
			return;
		}

		ob_start();
		extract($args);
		
		$title 		= (! empty( $instance['title'] ) ) ? $instance['title'] : '';
		$name_guide = (! empty( $instance['name_guide'] ) ) ? $instance['name_guide'] : __('Buying Guide', 'zoner');
		$fa_icon	= (! empty( $instance['fa_icon'] ) ) ? $instance['fa_icon'] : 'fa-home';
		$link		= (! empty( $instance['link'] ) ) 	 ? $instance['link'] : '#';
		$is_blank 	=  ! empty( $instance['is_blank'] )  ? '1' : '0';
		
		$icon_class = array();
		$icon_class[] = 'fa';
		$icon_class[] = $fa_icon;
		
		$a_atrget = '';
		if ($is_blank && (($link != '') && !empty($link))) {
			$a_atrget = 'target="_blank"';
		}
		
		echo $before_widget; 
		if ( $title ) echo $before_title . $title . $after_title; 
						
		/*each by taxonomy*/
		
		?>
			<a class="universal-button" href="<?php echo $link; ?>" <?php echo $a_atrget; ?>>
				<figure class="<?php echo implode(' ', $icon_class ); ?>"></figure>
				<span><?php echo $name_guide; ?></span>
				<span class="arrow fa fa-angle-right"></span>
			</a>

		<?php
		
		echo $after_widget; 
		
		$cache[$args['widget_id']] = ob_get_flush();
		wp_cache_set('widget_our_guides', $cache, 'widget');
	}

	function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['name_guide'] = strip_tags($new_instance['name_guide']);
		$instance['fa_icon'] 	= strip_tags($new_instance['fa_icon']);
		$instance['link'] 	 	= strip_tags($new_instance['link']);
		
		$instance['is_blank'] 	= !empty($new_instance['is_blank']) ? 1 : 0;

		
		$this->flush_widget_cache();

		$alloptions = wp_cache_get( 'alloptions', 'options' );
		if ( isset($alloptions['zoner-wog']) )
			delete_option('zoner-wog');

		return $instance;
	}

	function flush_widget_cache() {
		wp_cache_delete('widget_our_guides', 'widget');
	}

	function form( $instance) {
		$title = isset( $instance['title'] )  ? esc_attr( $instance['title']) : __('Our Guides', 'zoner');
		$name_guide  = isset( $instance['name_guide'] )  ? esc_attr( $instance['name_guide']) : __('Buying Guide', 'zoner');
		$fa_icon  = isset( $instance['fa_icon'] )  ? esc_attr( $instance['fa_icon']) : 'fa-home';
		$link     = isset( $instance['link'] )  ? esc_attr( $instance['link']) : '#';
		$is_blank 		= isset( $instance['is_blank'] ) ? (bool) $instance['is_blank'] : false;
		
?>
		<p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'zoner' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" /></p>

		<p><label for="<?php echo $this->get_field_id( 'name_guide' ); ?>"><?php _e( 'Name guides:', 'zoner' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'name_guide' ); ?>" name="<?php echo $this->get_field_name( 'name_guide' ); ?>" type="text" value="<?php echo $name_guide; ?>" /></p>
		
		<p><label for="<?php echo $this->get_field_id( 'fa_icon' ); ?>"><?php _e( 'Icon prefix:', 'zoner' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'fa_icon' ); ?>" name="<?php echo $this->get_field_name( 'fa_icon' ); ?>" type="text" value="<?php echo $fa_icon; ?>" /></p>
		
		<p><label for="<?php echo $this->get_field_id( 'link' ); ?>"><?php _e( 'Link:', 'zoner' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'link' ); ?>" name="<?php echo $this->get_field_name( 'link' ); ?>" type="text" value="<?php echo $link; ?>" /></p>
		
		<p><input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('is_blank'); ?>" name="<?php echo $this->get_field_name('is_blank'); ?>"<?php checked( $is_blank ); ?> />
		<label for="<?php echo $this->get_field_id('is_blank'); ?>"><?php _e( 'Target is blank', 'zoner' ); ?></label></p>
		
		
<?php
	}
}

/* Zoner Currency Calculator
 * Zoner widget
 *
 */
class Zoner_Widget_Currency_Calculator extends WP_Widget {

	function __construct() {
		$widget_ops = array('classname'  => 'zoner-currency_calculator', 'description' => __( "Currency Calculator.", 'zoner') );
		parent::__construct('zoner-wcc', __('Zoner Currency Calculator', 'zoner'), $widget_ops);
		$this->alt_option_name = 'zoner-wcc';

		add_action( 'save_post',    array($this, 'flush_widget_cache') );
		add_action( 'deleted_post', array($this, 'flush_widget_cache') );
		add_action( 'switch_theme', array($this, 'flush_widget_cache') );
	}

	function widget($args, $instance) {
		global $zoner, $zoner_config, $prefix;
		
		$cache = wp_cache_get('widget_currency_calculator', 'widget');

		if ( !is_array($cache) ) $cache = array();

		if ( ! isset( $args['widget_id'] ) )
			$args['widget_id'] = $this->id;

		if ( isset( $cache[ $args['widget_id'] ] ) ) {
			echo $cache[ $args['widget_id'] ];
			return;
		}

		ob_start();
		extract($args);
		
		$title 		    = (! empty( $instance['title'] ) ) ? $instance['title'] : '';
		$currency_from 	= (! empty( $instance['currency_from'] ) )  ? $instance['currency_from'] : 'USD';
		$currency_to 	= (! empty( $instance['currency_to'] ) )  ? $instance['currency_to'] : 'EUR';
		
		echo $before_widget; 
		if ( $title ) echo $before_title . $title . $after_title; 
		
		
		$currencies_list = $zoner->currency->get_zoner_currency_dropdown_settings();
		
		?>
			<form role="form" id="form-currency-calculator-<?php echo rand(1, 255);?>" class="form-currency-calculator" method="post" action="">
				<?php 
					
					$id_from = 'currency_from_'.rand(1, 255);
					$id_to   = 'currency_from_'.rand(1, 255);
					$id_amount  = 'amount_'.rand(1, 255);
					
					$args_select = array();
					$args_select = array(
						'for' 	=> $id_from,
						'label' => __('Currency from:', 'zoner'),
						'name'	=> 'currency_from',
						'id'	=> $id_from,
						'class' => array('currency-from'),
						'items'		=> $currencies_list,
						'selected'  => $currency_from
					);
					zoner_generate_select_($args_select);
					
					
					$args_select = array();
					$args_select = array(
						'for' 	=> $id_to,
						'label' => __('Currency to:', 'zoner'),
						'name'	=> 'currency_to',
						'id'	=> $id_to,
						'class' => array('currency-to'),
						'items'		=> $currencies_list,
						'selected'  => $currency_to
					);
					zoner_generate_select_($args_select);
				?>
				
				<div class="form-group">
					<label for="currency-amount"><?php _e('Currency amount:', 'zoner'); ?></label><input type="text" class="form-control currency-amount" id="<?php echo $id_amount; ?>" class="currency-amount" name="currency_amount" value="0"/>
				</div><!-- /.form-group -->
				
				<div class="form-group">
					<button type="submit" class="btn btn-default"><?php _e('Calculate', 'zoner'); ?></button>
				</div><!-- /.form-group -->
				
				<div class="out-results">
					
				</div>
			</form>
		<?php
		
		echo $after_widget; 
		
		$cache[$args['widget_id']] = ob_get_flush();
		wp_cache_set('widget_currency_calculator', $cache, 'widget');
	}

	function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title']          = strip_tags($new_instance['title']);
		$instance['currency_from']  = esc_attr ($new_instance['currency_from']);
		$instance['currency_to']    = esc_attr ($new_instance['currency_to']);
		
		$this->flush_widget_cache();

		$alloptions = wp_cache_get( 'alloptions', 'options' );
		if ( isset($alloptions['zoner-wcc']) )
			delete_option('zoner-wcc');

		return $instance;
	}

	function flush_widget_cache() {
		wp_cache_delete('widget_currency_calculator', 'widget');
	}

	function form( $instance) {
		global $zoner;
		$currencies_list = $zoner->currency->get_zoner_currency_dropdown_settings();
		
		$title = isset( $instance['title'] )  ? esc_attr( $instance['title']) : __('Currency calculator', 'zoner');
		$currency_from = isset( $instance['currency_from'] )  ? esc_attr( $instance['currency_from']) : 'USD';
		$currency_to   = isset( $instance['currency_to'] )  ? esc_attr( $instance['currency_to']) : 'EUR';
		
		
		
?>
		<p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'zoner' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" /></p>
		
		<p><?php 
			  $args_select = array();
			  $args_select = array(
							'for' 	=> $this->get_field_id( 'currency_from' ),
							'label' => __('Currency from (default)', 'zoner'),
							'name'	=> $this->get_field_name('currency_from'),
							'id'	=> $this->get_field_id( 'currency_from' ),
							'class' => array('currency-from'),
							'items'		=> $currencies_list,
							'selected'  => $currency_from
						);
						zoner_generate_select_($args_select);
		?></p>
		
		<p><?php 
			  $args_select = array();
			  $args_select = array(
							'for' 	=> $this->get_field_id( 'currency_to' ),
							'label' => __('Currency to (default)', 'zoner'),
							'name'	=> $this->get_field_name('currency_to'),
							'id'	=> $this->get_field_id( 'currency_to' ),
							'class' => array('currency-to'),
							'items'		=> $currencies_list,
							'selected'  => $currency_to
						);
						zoner_generate_select_($args_select);
		?></p>
		
<?php
	}
}

/* Zoner Quick Summary
 * Zoner widget
 *
 */
class Zoner_Widget_Quick_Summary extends WP_Widget {

	function __construct() {
		$widget_ops = array('classname' => 'zoner-quick_summary', 'description' => esc_html__( "Property Quick Summary (only for Property sidebar and Property Information sidebar).", 'zoner') );
		parent::__construct('zoner-wqs', esc_html__('Zoner Quick Summary', 'zoner'), $widget_ops);
		$this->alt_option_name = 'zoner-wqs';

		add_action( 'save_post',    array($this, 'flush_widget_cache') );
		add_action( 'deleted_post', array($this, 'flush_widget_cache') );
		add_action( 'switch_theme', array($this, 'flush_widget_cache') );
	}

	function widget($args, $instance) {
		global $zoner, $zoner_config, $prefix, $post;
		
		$cache = wp_cache_get('widget_quick_summary', 'widget');

		if ( !is_array($cache) ) $cache = array();

		if ( ! isset( $args['widget_id'] ) )
			$args['widget_id'] = $this->id;

		if ( isset( $cache[ $args['widget_id'] ] ) ) {
			echo $cache[ $args['widget_id'] ];
			return;
		}

		ob_start();
		extract($args);
		
		$title = (! empty( $instance['title'] ) ) ? $instance['title'] : esc_html__('Quick Summary', 'zoner');
		
		echo $before_widget; 
		
		$gproperty  = $prop_type_arr = array();
		$gproperty  = $zoner->property->get_property($post->ID);
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
			<header><h2><?php echo $title; ?></h2></header>
				<dl>
					<?php if (!empty($reference)) { ?>
						<dt><?php esc_html_e('Property ID', 'zoner'); ?></dt>
						<dd><?php echo $reference; ?></dd>
					<?php } ?>

					<?php if (!empty($full_adddres)) { ?>
						<dt><?php esc_html_e('Location', 'zoner'); ?></dt>
						<dd><?php echo implode(', ', $full_address); ?></dd>
					<?php } ?>

					<?php if ($price_html != '') { ?>
						<dt><?php esc_html_e('Price', 'zoner'); ?></dt>
						<dd><?php echo $price_html; ?></dd>
					<?php } ?>

					<?php if ($payment_rent) { ?>
						<dt><?php esc_html_e('Payment', 'zoner'); ?>:</dt>
						<dd><?php echo esc_attr($payment_rent_name); ?></dd>
					<?php } ?>

					<?php if (!empty($prop_type_html)) { ?>
						<dt><?php esc_html_e('Type', 'zoner'); ?>:</dt>
						<dd><?php echo implode(', ', $prop_type_html); ?></dd>
					<?php } ?>

					<?php if (!empty($prop_status_html)) { ?>
						<dt><?php esc_html_e('Status', 'zoner'); ?>:</dt>
						<dd><?php echo implode(', ', $prop_status_html); ?></dd>
					<?php } ?>

					<?php if ($area) { ?>
						<dt><?php esc_html_e('Area', 'zoner'); ?>:</dt>
						<dd><?php echo esc_attr($area) . ' ' .$zoner->property->ret_area_units_by_id($area_unit); ?></dd>
					<?php } ?>

					<?php if ($rooms) { ?>
						<dt><?php esc_html_e('Rooms', 'zoner'); ?>:</dt>
						<dd><?php echo esc_attr($rooms); ?></dd>
					<?php } ?>

					<?php if ($beds) { ?>
						<dt><?php esc_html_e('Beds', 'zoner'); ?>:</dt>
						<dd><?php echo esc_attr($beds); ?></dd>
					<?php } ?>

					<?php if ($baths) { ?>
						<dt><?php esc_html_e('Baths', 'zoner'); ?>:</dt>
						<dd><?php echo esc_attr($baths); ?></dd>
					<?php } ?>

					<?php if ($garages) { ?>
						<dt><?php esc_html_e('Garages', 'zoner'); ?>:</dt>
						<dd><?php echo esc_attr($garages); ?></dd>
					<?php } ?>

					<?php if($allow_rating == 'on') { ?>
						<dt><?php esc_html_e('Overall Rating', 'zoner'); ?>:</dt>
						<dd><div class="rating rating-overall" data-score="<?php echo esc_attr($rating); ?>"></div></dd>
					<?php } ?>
					
					
					<?php 
						/*Show Custom Fields*/
						do_action('zoner_quick_summary_fields'); 
					?>
				</dl>
			</section><!-- /#quick-summary -->
		<?php
		
		echo $after_widget; 
		
		$cache[$args['widget_id']] = ob_get_flush();
		wp_cache_set('widget_quick_summary', $cache, 'widget');
	}

	function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = strip_tags($new_instance['title']);
		
		
		$this->flush_widget_cache();

		$alloptions = wp_cache_get( 'alloptions', 'options' );
		if ( isset($alloptions['zoner-wqs']) )
			delete_option('zoner-wqs');

		return $instance;
	}

	function flush_widget_cache() {
		wp_cache_delete('widget_quick_summary', 'widget');
	}

	function form( $instance) {
		global $zoner;
		$title = isset( $instance['title'] )  ? esc_attr( $instance['title']) : esc_html__('Quick Summary', 'zoner');
		
?>
		<p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'zoner' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" /></p>
		
<?php
	}
}

function zoner_register_widgets() { 
	register_widget( 'Zoner_Widget_Search_Property' );
	register_widget( 'Zoner_Widget_Featured_Property' );
	register_widget( 'Zoner_Widget_Recent_Property' );
	register_widget( 'Zoner_Widget_Categories' );
	register_widget( 'Zoner_Widget_Our_Guides' );
	register_widget( 'Zoner_Widget_Currency_Calculator' );
	register_widget( 'Zoner_Widget_Quick_Summary' );
}

add_action( 'widgets_init', 'zoner_register_widgets' );