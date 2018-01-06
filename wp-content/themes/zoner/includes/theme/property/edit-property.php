<?php
/**
 * The template for edit property front-end
 * @package WordPress
 * @subpackage Zoner_Theme
 * @since Zoner Theme 1.0
 */
?>

<?php get_header(); ?>
<?php do_action('zoner_before_content') ?>	

<?php 
		global $post, $prefix, $zoner, $zoner_config;
        $gproperty = array();
        $gproperty = $zoner->property->get_property($post->ID);
        $files 	   = $gproperty->prop_files;
        $gallery   = $gproperty->prop_gallery;
        $plans     = $gproperty->prop_plans;

        $property_featured_url = $property_featured_id = '';
        $property_featured_id = get_post_thumbnail_id($post->ID);
        $featured_img = wp_get_attachment_image_src($property_featured_id, 'full');
        if (!empty($featured_img[0]))
            $property_featured_url = $featured_img[0];
        $currency_ = $zoner->currency->get_zoner_currencies();

        $currency_list = $price_format_list = array();
        foreach ($currency_ as $key => $value) {
            $currency_list[$key] = $zoner->currency->get_zoner_currency_symbol($key) . ' (' . $key . ')';
        }
		
        $price_format_list = $zoner->property->get_price_format_values();
		$reference = $currency_symbol = $payment_rent = $allow_raiting = $lat = $lng = $location = null; 
		
        if (!isset($_POST['edit-property'])) {
            $title = get_the_title( $post->ID );
            $reference = $gproperty->reference;
            $content   = $post->post_content;
            $address   = $gproperty->address;
            $city 	   = $gproperty->city_tax_id;
			$district  = $gproperty->district;
            $zip 	   = $gproperty->zip;

            $price 	   	= $gproperty->price;
            $price_format = $gproperty->price_format;
            $rooms 		= $gproperty->rooms;
            $beds 		= $gproperty->beds;
            $baths 		= $gproperty->baths;
            $garages 	= $gproperty->garages;
            $area 		= $gproperty->area;
            $area_unit 	= $gproperty->area_unit;
            $location 	= $gproperty->location;
            $allow_raiting = $gproperty->allow_raiting;

            $currency 	= $gproperty->currency;
            $currency_symbol = $gproperty->currency_symbol;

            $country 	= $gproperty->country;
            $state 		= $gproperty->state;
            $condition 	= $gproperty->condition;
            $payment_rent = $gproperty->payment_rent;
            $lat = $gproperty->lat;
            $lng = $gproperty->lng;

            $show_on_map = $gproperty->show_on_map;
            $links_video = $gproperty->prop_video;

        } else {
            if (isset($_POST['title'])) 		$title 		= $_POST['title'];
            if (isset($_POST['description'])) 	$content 	= $_POST['description'];
            if (isset($_POST['address'])) 		$address 	= $_POST['address'];
            if (isset($_POST['city'])) 			$city 		= $_POST['city'];
            if (isset($_POST['district'])) 		$district	= $_POST['district'];
            if (isset($_POST['zip'])) 			$zip 		= $_POST['zip'];
            if (isset($_POST['price'])) 		$price 		= $_POST['price'];
            if (isset($_POST['price_format'])) 	$price_format = $_POST['price_format'];
            if (isset($_POST['rooms'])) 		$rooms 		= $_POST['rooms'];
            if (isset($_POST['beds'])) 			$beds 		= $_POST['beds'];
            if (isset($_POST['baths'])) 		$baths 		= $_POST['baths'];
            if (isset($_POST['garages'])) 		$garages 	= $_POST['garages'];
			if (isset($_POST['location']))   	$location 	= $_POST['location'];
            if (isset($_POST['area'])) 			$area 		= $_POST['area'];
            if (isset($_POST['area_unit'])) 	$area_unit 	= $_POST['area_unit'];
			if (isset($_POST['allow_raiting']))	$allow_raiting = $_POST['allow_raiting'];
			if (isset($_POST['currency']))		$currency 	= $_POST['currency'];
			if (isset($_POST['currency_symbol']))	$currency_symbol = $_POST['currency_symbol'];
			if (isset($_POST['state']))			$state 		= $_POST['state'];
			if (isset($_POST['country']))		$country 	= $_POST['country'];
			if (isset($_POST['condition'])) 	$condition 	= $_POST['condition'];
			if (isset($_POST['lat'])) 			$lat = $_POST['lat'];
            if (isset($_POST['lng'])) 			$lng = $_POST['lng'];
			if (isset($_POST['links_video'])) 	$links_video = $_POST['links_video'];
			if (isset($_POST['show_on_map'])) 	$show_on_map = $_POST['show_on_map'];
        }

		// property enabling fields
		if (isset($zoner_config['prop-enabling-fields']['enabled']) &&
			is_array($zoner_config['prop-enabling-fields']['enabled'])) {
			$prop_enabled_fields = $zoner_config['prop-enabling-fields']['enabled'];
		} else {
			$prop_enabled_fields = array();
		}
		$hidden_fields = array();
?>

<?php if (!$zoner->validate->check('property')){?>
    <div class="alert alert-danger fade in">
        <a href="#" class="close" data-dismiss="alert">&times;</a>
        <strong><?php _e('Required fields', 'zoner'); ?></strong>
        <?php
        echo $zoner->validate->listErrors();
        ?>
    </div>
<?php }?>

	<section id="edit-property-" class="edit-property">
		<div class="container">
			<header><h1><?php _e('Edit Property', 'zoner'); ?></h1></header>
			<form role="form" id="form-submit" class="form-submit edit-property form-edit-property" method="post" action="" enctype="multipart/form-data" name="edit-property">
				<?php if (!$reference){ ?>
					<input type="hidden" class="form-control" id="reference" name="reference" value="<?php echo 'PR'.mt_rand();?>">
				<?php } ?>
				<?php wp_nonce_field( 'zoner_edit_property', 'edit-property', true, true ); ?>
				<div class="row">
					<div class="col-md-9">
						<section id="submit-form" class="submit-form">
							
							<section id="basic-information">
                                <div class="row">
									<div class="col-md-12">
										<div class="form-group">
											<label for="submit-title"><?php _e('Title', 'zoner'); ?></label>
                                            <input type="text" class="form-control" id="submit-title" name="title" value="<?php echo $title; ?>" required>
                                        </div><!-- /.form-group -->
                                    </div>
								</div>	
								
								<div class="row">
									<div class="col-md-2">
										<?php 
											$args_select = array();
											$args_select = array(
												'for' 	=> 'submit-currency',
												'label' => __('Currency', 'zoner'),
												'name'	=> 'currency',
												'id'	=> 'submit-currency',
												'class' => array('submit-currency'),
												'items'	=> $currency_list,
												'selected' => $currency
											);
											zoner_generate_select_($args_select);
										?>	
                                    </div>
									
                                    <div class="col-md-4">
										<div class="form-group">
											<label for="submit-price"><?php _e('Price', 'zoner'); ?></label>
                                            <div class="input-group">
												<span class="input-group-addon"><?php echo $currency_symbol; ?></span>
												<input type="text" class="form-control" id="submit-price" name="price" pattern="\d*" value="<?php echo $price; ?>" required>
											</div>
										</div><!-- /.form-group -->
                                    </div>
									
									<div class="col-md-6">
										<?php 
											$args_select = array();
											$args_select = array(
												'for' 	=> 'submit-price-format',
												'label' => __('Price format', 'zoner'),
												'name'	=> 'price_format',
												'id'	=> 'submit-price-formats',
												'class' => array('submit-price-format'),
												'items'		=> $price_format_list,
												'selected'  => $price_format
											);
											zoner_generate_select_($args_select);
										?>	
                                    </div>
									
                                </div>
								<?php if ( isset($prop_enabled_fields['description']) ) {  ?>
									<div class="form-group">
										<label for="submit-description"><?php _e('Description', 'zoner'); ?></label>
										<textarea class="form-control" id="submit-description" rows="8" name="description"><?php echo esc_html($content); ?></textarea>
									</div><!-- /.form-group -->
								<?php } else {
									$hidden_fields[] = array(
										'id'    => 'submit-description',
										'name'  => 'description',
										'value' => esc_html($content)
									);
								}
								?>
							</section><!-- /#basic-information -->
							
							
							<section id="location" class="location">
								<div class="row">
									<div class="block clearfix">
										<div class="col-md-6 col-sm-6">
											<header><h2><?php _e('Local information', 'zoner'); ?></h2></header>
											<?php
											if ( isset($prop_enabled_fields['country']) ) {
												$args_select = array();
												$args_select = array(
													'for' => 'submit-country',
													'label' => __('Country', 'zoner'),
													'name' => 'country',
													'id' => 'submit-country',
													'class' => array('submit-country'),
													'items' => $zoner->countries->countries,
													'selected' => $country
												);
												zoner_generate_select_($args_select);
											} else {
												$hidden_fields[] = array(
													'id'    => 'submit-country',
													'name'  => 'country',
													'value' => esc_html($country)
												);
											}
											?>
												
											<?php
											if ( isset($prop_enabled_fields['state']) ) {
												$args_select = array();
												$args_select = array(
													'id_container' => 'select-state',
													'for' 	=> 'submit-state',
													'label' => __('State', 'zoner'),
													'name'	=> 'state',
													'id'	=> 'submit-state',
													'class' => array('submit-state'),
													'items'	=> $zoner->countries->get_states($country),
													'selected' => $state
												);
												zoner_generate_select_($args_select);
											} else {
												$hidden_fields[] = array(
													'id'    => 'submit-state',
													'name'  => 'state',
													'value' => esc_html($state)
												);
											}
											?>
											
											<div class="form-group">
												<label for="submit-address"><?php _e('Address', 'zoner'); ?></label>
													<div class="input-group">
														<span class="input-group-addon"><i class="fa fa-map-marker"></i></span>
														<input type="text" class="form-control" id="submit-address" name="address" value="<?php echo $address; ?>" required>
													</div>
											</div><!-- /.form-group -->
											
											
											<div class="row">
												<?php if ( isset($prop_enabled_fields['city']) ) { ?>
													<div class="col-md-6 col-sm-6">
														<?php
															$items_city = array();
															$tax_cities = get_terms('property_city', array(
																				'orderby' => 'name',
																				'order'	  => 'ASC',
																				'hide_empty' => false));

															if (!empty($tax_cities)) {
																foreach ($tax_cities as $tax_city) {
																	$items_city[$tax_city->term_id] = $tax_city->name;
																}
															}

															$args_select = array();
															$args_select = array(
																'id_container' => 'select-city',
																'for'      => 'submit-city',
																'label'    => __('Town / City', 'zoner'),
																'name'	   => 'city',
																'id'	   => 'submit-city',
																'class'    => array('submit-city'),
																'items'	   => $items_city,
																'selected' => $city,
															);
															zoner_generate_select_($args_select);
														?>
													</div><!-- /.col-md-6 -->
												<?php } else {
													$hidden_fields[] = array(
														'id'    => 'submit-city',
														'name'  => 'city',
														'value' => esc_html($city)
													);
												}
												?>

												<?php if ( isset($prop_enabled_fields['zip']) ) { ?>
													<div class="col-md-6 col-sm-6">
														<div class="form-group">
															<label for="submit-zip"><?php _e('Postcode / Zip', 'zoner'); ?></label>
															<input type="text" class="form-control" id="submit-zip" name="zip" value="<?php echo $zip; ?>">
														</div><!-- /.form-group -->
													</div><!-- /.col-md-6 -->
												<?php } else {
													$hidden_fields[] = array(
														'id'    => 'submit-zip',
														'name'  => 'zip',
														'value' => esc_html($zip)
													);
												}
												?>

												<?php if ( isset($prop_enabled_fields['district']) ) { ?>
													<div class="col-md-6 col-sm-6">
														<div class="form-group">
															<label for="submit-district"><?php _e('District', 'zoner'); ?></label>
															<input type="text" class="form-control" id="submit-district" name="district" value="<?php echo $district; ?>">
														</div><!-- /.form-group -->
													</div><!-- /.col-md-6 -->
												<?php } else {
													$hidden_fields[] = array(
														'id'    => 'submit-district',
														'name'  => 'district',
														'value' => esc_html($district)
													);
												}
												?>
											</div><!-- /.row -->
										</div><!-- /.col-md-6 -->
										<div class="col-md-6 col-sm-6">
											<section id="place-on-map">
												<header class="section-title">
													<h2><?php _e('Place on Map', 'zoner'); ?></h2>
													<span class="link-arrow geo-location"><?php _e('Get My Position', 'zoner'); ?></span>
												</header>
												<div class="form-group">
													<label for="search-location"><?php _e('Search Location', 'zoner'); ?></label>
													<div class="input-group">
														<span class="input-group-addon"><i class="fa fa-map-marker"></i></span>
														<input type="text" class="form-control" id="search-location" name="location" value="<?php echo $location; ?>">
													</div>	
												</div><!-- /.form-group -->
												<div id="submit-map"></div>
												<div class="row">
													<div class="col-md-6">
														<div class="form-group">
															<input type="text" class="form-control" id="latitude" name="latitude" value="<?php echo $lat; ?>" readonly>
														</div><!-- /.form-group -->
													</div>
													<div class="col-md-6">
														<div class="form-group">
															<input type="text" class="form-control" id="longitude" name="longitude" value="<?php echo $lng; ?>" readonly>
														</div><!-- /.form-group -->
													</div>
												<div class="checkbox">
													<label class="col-md-12">
														<input name="show_on_map" value="on" type="checkbox" <?php checked( 'on', $show_on_map, true ); ?>><?php _e('Show on map', 'zoner'); ?> <i class="fa fa-question-circle tool-tip"  data-toggle="tooltip" data-placement="right" title="<?php _e('Display position on maps.', 'zoner'); ?>"></i>
													</label>
												</div>
												</div>
											</section><!-- /#place-on-map -->
										</div><!-- /.col-md-6 -->
									</div><!-- /.block -->
								</div><!-- /.row -->
							</section><!-- location section -->
							
							<section id="summary" class="summary">
								<div class="row">
									<div class="block clearfix">
										<?php
										$summary_block_classes = 'col-md-6 col-sm-6';
										if ( ! isset($prop_enabled_fields['featured_image'])) {
											$summary_block_classes = 'col-sm-12';
										}
										?>
										<div class="<?php echo $summary_block_classes; ?>">
											<header><h2><?php _e('Summary', 'zoner'); ?></h2></header>
											<div class="row">
												<?php if ( isset($prop_enabled_fields['condition']) ) {  ?>
													<div class="col-md-6 col-sm-6">
														<?php
															$args_select = array();
															$args_select = array(
																	'id_container' => 'submit-condition',
																	'for' 	=> 'submit-condition',
																	'label' => __('Condition', 'zoner'),
																	'name'	=> 'condition',
																	'id'	=> 'submit-condition',
																	'class' => array('submit-condition'),
																	'items'	   => $zoner->property->get_condition_values(),
																	'selected' => $condition
															);
															zoner_generate_select_($args_select);
														?>
													</div><!-- /.col-md-6 -->
												<?php } else {
													$hidden_fields[] = array(
														'id'    => 'submit-condition',
														'name'  => 'condition',
														'value' => esc_html($condition)
													);
												}
												?>

												<?php if ( isset($prop_enabled_fields['payment']) ) {  ?>
													<div class="col-md-6 col-sm-6">
														<?php
															$args_select = array();
															$args_select = array(
																'id_container' => 'submit-payment-rent',
																'for' 	=> 'submit-payment-rent',
																'label' => __('Interval payment for rent', 'zoner'),
																'name'	=> 'payment-rent',
																'id'	=> 'submit-payment-rent',
																'class' => array('submit-payment-rent'),
																'items'	=> $zoner->property->get_payment_rent_values(),
																'selected' => $payment_rent
															);
															zoner_generate_select_($args_select);
														?>
													</div><!-- /.col-md-6 -->
												<?php } else {
													$hidden_fields[] = array(
														'id'    => 'submit-payment-rent',
														'name'  => 'payment-rent',
														'value' => esc_html($payment_rent)
													);
												}
												?>

												<?php if ( isset($prop_enabled_fields['type']) ) {  ?>
													<div class="col-md-6 col-sm-6">
														<?php
															$args_select = array();
															$args_select = array(
																'id_container' => 'submit-property_type',
																'for' 	=> 'submit-property_type',
																'label' => __('Property Type', 'zoner'),
																'name'	=> 'type',
																'id'	=> 'submit-property_type',
																'class' => array('submit-property_type'),
																'items'	   => zoner_get_property_types(),
																'selected' => zoner_get_curr_property_type(),
															);
															zoner_generate_select_($args_select);
														?>
													</div><!-- /.col-md-6 -->
												<?php } else {
													$hidden_fields[] = array(
														'id'    => 'submit-property_type',
														'name'  => 'type',
														'value' => zoner_get_curr_property_type()
													);
												}
												?>

												<?php if ( isset($prop_enabled_fields['status']) ) {  ?>
													<div class="col-md-6 col-sm-6">
														<?php
															$args_select = array();
															$args_select = array(
																'id_container' => 'submit-property_status',
																'for' 	=> 'submit-property_status',
																'label' => __('Status', 'zoner'),
																'name'	=> 'status',
																'id'	=> 'submit-property_status',
																'class'   => array('submit-property_status'),
																'items'	   => zoner_get_property_status(),
																'selected' => zoner_get_curr_property_status()
															);
															zoner_generate_select_($args_select);
														?>
													</div><!-- /.col-md-6 -->
												<?php } else {
													$hidden_fields[] = array(
														'id'    => 'submit-property_status',
														'name'  => 'status',
														'value' => zoner_get_curr_property_status()
													);
												}
												?>

												<?php if ( isset($prop_enabled_fields['rooms']) ) {  ?>
													<div class="col-md-6 col-sm-6">
														<div class="form-group">
															<label for="submit-rooms"><?php _e('Rooms', 'zoner'); ?></label>
															<input type="text" class="form-control" id="submit-rooms" name="rooms" pattern="\d*" value="<?php echo $rooms; ?>">
														</div><!-- /.form-group -->
													</div><!-- /.col-md-6 -->
												<?php } else {
													$hidden_fields[] = array(
														'id'    => 'submit-rooms',
														'name'  => 'rooms',
														'value' => esc_html($rooms)
													);
												}
												?>

												<?php if ( isset($prop_enabled_fields['beds']) ) {  ?>
													<div class="col-md-6 col-sm-6">
														<div class="form-group">
															<label for="submit-beds"><?php _e('Beds', 'zoner'); ?></label>
															<input type="text" class="form-control" id="submit-beds" name="beds" pattern="\d*" value="<?php echo $beds; ?>">
														</div><!-- /.form-group -->
													</div><!-- /.col-md-6 -->
												<?php } else {
													$hidden_fields[] = array(
														'id'    => 'submit-beds',
														'name'  => 'beds',
														'value' => esc_html($beds)
													);
												}
												?>

												<?php if ( isset($prop_enabled_fields['baths']) ) {  ?>
												<div class="col-md-6 col-sm-6">
													<div class="form-group">
														<label for="submit-baths"><?php _e('Baths', 'zoner'); ?> </label>
														<input type="text" class="form-control" id="submit-baths" name="baths" value="<?php echo $baths; ?>" pattern="\d*">
													</div><!-- /.form-group -->
												</div><!-- /.col-md-6 -->
												<?php } else {
													$hidden_fields[] = array(
														'id'    => 'submit-baths',
														'name'  => 'baths',
														'value' => esc_html($baths)
													);
												}
												?>

												<?php if ( isset($prop_enabled_fields['garages']) ) {  ?>
													<div class="col-md-6 col-sm-6">
														<div class="form-group">
															<label for="submit-garages"><?php _e('Garages', 'zoner'); ?></label>
															<input type="text" class="form-control" id="submit-garages" name="garages" value="<?php echo $garages; ?>" pattern="\d*">
														</div><!-- /.form-group -->
													</div><!-- /.col-md-6 -->
												<?php } else {
													$hidden_fields[] = array(
														'id'    => 'submit-garages',
														'name'  => 'garages',
														'value' => esc_html($garages)
													);
												}
												?>

												<div class="col-md-6 col-sm-6">
													<div class="form-group">
														<label for="submit-area"><?php _e('Area', 'zoner'); ?></label>
														<input type="text" class="form-control" id="submit-area" name="area" value="<?php echo $area; ?>" pattern="\d*" required>
													</div><!-- /.form-group -->
												</div><!-- /.col-md-6 -->

												<?php if ( isset($prop_enabled_fields['area_units']) ) {  ?>
													<div class="col-md-6 col-sm-6">
														<?php
															$args_select = array();
															$args_select = array(
																'id_container' => 'submit-area-unit',
																'for' 	   => 'submit-area-unit',
																'label'    => __('Area Units', 'zoner'),
																'name'	   => 'area_unit',
																'id'	   => 'submit-area-unit',
																'class'    => array('submit-area-unit'),
																'items'	   => $zoner->property->get_area_units_values(),
																'selected' => $area_unit
															);
															zoner_generate_select_($args_select);
														?>
													</div><!-- /.col-md-6 -->
												<?php } else {
													$hidden_fields[] = array(
														'id'    => 'submit-area-unit',
														'name'  => 'area_unit',
														'value' => esc_html($area_unit)
													);
												}
												?>

											</div><!-- /.row -->
											
											<!-- Custom Fileds Options -->
											<?php do_action('zoner_edit_property_custom_fields_option', $post->ID); ?>

											<?php if ( isset($prop_enabled_fields['rating']) ) {  ?>
												<div class="checkbox">
													<label>
														<input name="allow-user-rating" value="on" type="checkbox" <?php checked( 'on', $allow_raiting, true ); ?>><?php _e('Allow user rating', 'zoner'); ?> <i class="fa fa-question-circle tool-tip"  data-toggle="tooltip" data-placement="right" title="<?php _e('Users can give you a stars rating which is displayed in property detail', 'zoner'); ?>"></i>
													</label>
												</div>
											<?php } else {
												$hidden_fields[] = array(
													'name'  => 'allow-user-rating',
													'value' => $allow_raiting
												);
											}
											?>

										</div><!-- /.col-md-6 --><!-- / summary_block_classes -->

										<?php if ( isset($prop_enabled_fields['featured_image']) ) {  ?>
											<div class="col-md-6 col-sm-6">
												<section id="featured-image">
													<header class="section-title"><h2><?php _e('Set featured image', 'zoner'); ?></h2></header>
													<div class="property-featured-image-inner">
														<?php
															$img_src = '';
															if (!empty($property_featured_url)) {
																$img_src = esc_url($property_featured_url);
																echo '<span class="remove-prop-featured"><i class="fa fa-trash-o"></i></span>';
															} else {
																$img_src = 'holder.js/410x410?text='.__('Featured', 'zoner');
															}
														?>
														<img width="100%" id="prop-featured-image" class="img-responsive" src="<?php echo $img_src; ?>"/>
														<input type="hidden" id="prop-featured-image-exists" name="prop-featured-image-exists" value="<?php echo $property_featured_id; ?>" />
													</div>
													<div class="form-group">
														<div class="col-md-offset-2  col-md-8">
															<input id="property-featured-image" name="prop-featured-image" class="file-inputs" type="file" title="<?php _e('Upload Image', 'zoner'); ?>" data-filename-placement="inside" value="">
														</div>
													</div>

												</section><!-- /#featured-image -->
											</div><!-- /.col-md-6 -->
										<?php } else {
											$hidden_fields[] = array(
												'id'    => 'prop-featured-image-exists',
												'name'  => 'prop-featured-image-exists',
												'value' => esc_url($property_featured_url)
											);
										}
										?>

									</div><!-- /.block -->
								</div><!-- /.row -->
							</section><!-- location section -->

							<?php if ( isset($prop_enabled_fields['files']) ) {  ?>
							<section class="block" id="files">
								<header><h2><?php _e('Files', 'zoner'); ?></h2></header>
								<section class="ready-img">
									<div class="row">
										<div id="sortable-image-files" class="sortable-gallery">
											<?php
												if (!empty($files)) {
													foreach ($files as $key => $value) {
															$attachment_url = wp_get_attachment_url( $key );
															$file_icon = zoner_get_icon_for_attachment( $key );
														?>
															<div class="col-md-3 sortable">
																<div class="thumbnail">
																	<span class="remove-prop"><i class="fa fa-trash-o"></i></span>
																	<div title="<?php echo get_the_title($key); ?>" class="file-type-icon">
																		<?php echo $file_icon; ?>
																	</div>
																	<input type="hidden" value="<?php echo $attachment_url; ?>" name="<?php echo $prefix. 'exist_files['. $key .']'; ?>"/>
																</div>
															</div>
														<?php
													}
												}
											?>
										</div>
									</div>

									<div class="center">
										<div class="form-group">
											<input id="file-upload-files" type="file" class="file-custom" multiple="multiple" name="files[]" data-show-upload="false" data-show-caption="false" data-show-remove="true" accept="application/pdf,application/zip,application/rar,application/tar,application/txt,text/plain,text/html,application/octet-stream,application/vnd.ms-excel,application/vnd.ms-word,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" data-browse-class="btn btn-default" data-browse-label="<?php _e('Browse Files', 'zoner'); ?>">
										<figure class="note"><strong><?php _e('Hint', 'zoner'); ?>:</strong> <?php _e('You can upload all files at once!', 'zoner'); ?></figure>
										</div>
									</div>
								</section>

							</section>
							<?php } else {
										if (!empty($files)) {
											foreach ($files as $key => $value) {
												$hidden_fields[] = array(
													'name'  => $prefix . 'exist_files['. $key .']',
													'value' => wp_get_attachment_url( $key )
												);
											}
										}
							}
							?>


							<?php if ( isset($prop_enabled_fields['gallery']) ) {  ?>
							<section class="block" id="gallery">
								<header><h2><?php _e('Gallery', 'zoner'); ?></h2></header>
								<section class="ready-img">
									<div class="row">
										<div id="sortable-image-gallery" class="sortable-gallery">
											<?php 
												if (!empty($gallery)) {
													foreach ($gallery as $key => $value) {
															$image 		= wp_get_attachment_image_src($key, 'zoner-gallery-edit-property');
															$image_full = wp_get_attachment_image_src($key, 'full');
														?>

															<div class="col-md-3 sortable">
																<div class="thumbnail">
																	<span class="remove-prop"><i class="fa fa-trash-o"></i></span>
																	<img width="100%" class="img-responsive" src="<?php echo esc_url($image[0]); ?>" />
																	<input type="hidden" value="<?php echo esc_url($image_full[0]); ?>" name="<?php echo $prefix. 'exist_gallery['. $key .']'; ?>"/>
																</div>
															</div>	
														<?php
													}	
												}
											?>	
										</div>	
									</div>
									
									<div class="center">
										<div class="form-group">
											<input id="file-upload-gallery" type="file" class="file-custom" multiple="multiple" name="gallery[]" data-show-upload="false" data-show-caption="false" data-show-remove="true" accept="image/jpeg,image/png" data-browse-class="btn btn-default" data-browse-label="<?php _e('Browse Images', 'zoner'); ?>">
										<figure class="note"><strong><?php _e('Hint', 'zoner'); ?>:</strong> <?php _e('You can upload all images at once!', 'zoner'); ?></figure>
										</div>
									</div>
								</section>
								
							</section>
							<?php } else {
								if (!empty($gallery)) {
									foreach ($gallery as $key => $value) {
										$image_full = wp_get_attachment_image_src($key, 'full');
										$hidden_fields[] = array(
											'name'  => $prefix . 'exist_gallery['. $key .']',
											'value' => esc_url($image_full[0])
										);
									}
								}
							}
							?>


							<?php if ( isset($prop_enabled_fields['floor_plans']) ) {  ?>
							<section id="flor-plans">
								<header><h2><?php _e('Floor Plans', 'zoner'); ?></h2></header>
								<section class="ready-img">
									<div class="row">
										<div id="sortable-image-plans" class="sortable-gallery">
											<?php 
												if (!empty($plans)) {
													foreach ($plans as $key => $value) {
															$image 		= wp_get_attachment_image_src($key, 'zoner-gallery-edit-property');
															$image_full = wp_get_attachment_image_src($key, 'full');
														?>

															<div class="col-md-3 sortable">
																<div class="thumbnail">
																	<span class="remove-prop"><i class="fa fa-trash-o"></i></span>
																	<img width="100%" class="img-responsive" src="<?php echo esc_url($image[0]); ?>" />
																	<input type="hidden" value="<?php echo esc_url($image_full[0]); ?>" name="<?php echo $prefix. 'exist_plans['. $key .']'; ?>"/>
																</div>
															</div>	
														<?php
													}	
												}
											?>	
										</div>	
									</div>
									
									<div class="center">
										<div class="form-group">
											<input id="file-upload-plans" type="file" class="file-custom" multiple="multiple" name="floorplans[]" data-show-upload="false" data-show-caption="false" data-show-remove="true" accept="image/jpeg,image/png" data-browse-class="btn btn-default" data-browse-label="<?php _e('Browse Images', 'zoner'); ?>">
										<figure class="note"><strong><?php _e('Hint', 'zoner'); ?>:</strong> <?php _e('You can upload all images at once!', 'zoner'); ?></figure>
										</div>
									</div>
								</section>
							</section>
							<?php } else {
								if (!empty($plans)) {
									foreach ($plans as $key => $value) {
										$image_full = wp_get_attachment_image_src($key, 'full');
										$hidden_fields[] = array(
											'name'  => $prefix . 'exist_plans['. $key .']',
											'value' => esc_url($image_full[0])
										);
									}
								}
							}
							?>


							<?php if ( isset($prop_enabled_fields['video']) ) {  ?>
							<section id="property-video-presentation" class="block">
								<section>
									<header><h2><?php _e('Video Presentations', 'zoner'); ?></h2></header>
										<div class="row field-container">
											<?php 
												if (!empty($links_video)) {
													$cnt=1; 
													
													foreach ($links_video as $link)	 {
														
														$is_remove = ($cnt<2);
														
														$url_video = esc_url($link[$prefix.'link_video']);
														zoner_get_input_videos($url_video, false, $is_remove);
														
														$cnt++;
													}
												
												} else {
														zoner_get_input_videos(null, false, true);
												} 
											
											?>
											
											
											
										</div><!-- /.row -->
										
										<div class="row">
											<div class="col-md-4">
												<div class="form-group">
													<button type="button" class="btn btn-default medium add-video">
														<i class="fa fa-plus-circle"></i> <?php _e('Add video', 'zoner'); ?>
													</button>	
												</div><!-- /.form-group -->
											</div>
										</div>
								</section>
							</section>
							<?php } else {
								if (!empty($links_video)) {
									foreach ($links_video as $link) {
										$url_video = esc_url($link[$prefix.'link_video']);
										$hidden_fields[] = array(
											'name'  => 'videos[]',
											'value' => $url_video
										);
									}
								}
							}
							?>
							
							
							<section id="property_features" class="block">
								<section>
									<header><h2><?php _e('Property Features', 'zoner'); ?></h2></header>
									<?php zoner_get_property_edit_features($post->ID); ?>
								</section>
							</section>
							
							<hr>
							
						</section>	
					</div>	
					<div class="col-md-3 col-sm-3">
						<aside class="submit-step">
								<figure class="step-number">1</figure>
                                <div class="description">
                                    <h4><?php _e($zoner_config['tips-header-fields-update'], 'zoner'); ?></h4>
                                    <p><?php _e($zoner_config['tips-text-fields-update'], 'zoner'); ?></p>
                                </div>
                        </aside><!-- /.submit-step -->
					</div>
				</div><!-- row -->
				
				<div class="row">
                    <div class="block">
                        <div class="col-md-9">
                            <div class="center">
                                <div class="form-group">
									<?php // for enabling property fields
									echo zoner_gen_inputs_hidden($hidden_fields);
									?>
                                    <button type="submit" class="btn btn-default large"><?php _e('Update Information', 'zoner'); ?></button>
                                </div><!-- /.form-group -->
                            </div>
                        </div>
                        <div class="col-md-3">
                            <aside class="submit-step">
                                <figure class="step-number">2</figure>
                                <div class="description">
                                    <h4><?php _e($zoner_config['tips-header-submit-update'], 'zoner'); ?></h4>
                                    <p><?php _e($zoner_config['tips-text-submit-update'], 'zoner'); ?></p>
                                </div>
                            </aside><!-- /.submit-step -->
                        </div><!-- /.col-md-3 -->
                    </div>
                </div>
				
			</form>	
		</div>
	</section>

<?php do_action('zoner_after_content') ?>	

<?php get_footer(); ?>