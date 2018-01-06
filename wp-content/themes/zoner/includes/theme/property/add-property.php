<?php
/**
 * The template for add property front-end
 * @package WordPress
 * @subpackage Zoner_Theme
 * @since Zoner Theme 1.0
 */
?>

<?php get_header(); ?>
<?php do_action('zoner_before_content') ?>	

<?php 
		global $prefix, $zoner, $zoner_config; 
			
		/*Check user limit properties*/
		$is_user_limit_properties = $zoner->membership->zoner_is_user_limit_properties();
		
		if ($is_user_limit_properties) {
			$currency_ = $zoner->currency->get_zoner_currencies();
			$currency_list = array();
			$default_country = null;
		
			foreach ($currency_ as $key => $value) {
				$currency_list[$key] = $zoner->currency->get_zoner_currency_symbol($key) . ' (' .  $key . ')';
			}	
		
			$price_format = $zoner->property->get_price_format_values();
		
			$default_currency = $zoner_config['currency'];
			$currency_symbol  = $zoner->currency->get_zoner_currency_symbol($default_currency);
		
			if (!empty($zoner_config['default-country']) && (isset($zoner_config['default-country'])))
			$default_country  = $zoner_config['default-country'];
		
			$allow_raiting 	  = false;
		
			$lat = $lng = '';
			if (!$lat && !empty($zoner_config['geo-center-lat']))  $lat = esc_attr($zoner_config['geo-center-lat']); 
			if (!$lng && !empty($zoner_config['geo-center-lng']))  $lng = esc_attr($zoner_config['geo-center-lng']); 
			$default_area_unit = esc_attr($zoner_config['area-unit']);

			// for enabling property fields
			if (isset($zoner_config['prop-enabling-fields']['enabled']) &&
				is_array($zoner_config['prop-enabling-fields']['enabled'])) {
				$prop_enabled_fields = $zoner_config['prop-enabling-fields']['enabled'];
			} else {
				$prop_enabled_fields = array();
			}
			$hidden_fields = array();

		}
        $not_saved = $_POST;
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
			<header><h1><?php _e('Create Property', 'zoner'); ?></h1></header>
			
			<?php if ($is_user_limit_properties) { ?>
			
			<form role="form" id="form-submit" class="form-submit add-property form-edit-property" method="post" action="" enctype="multipart/form-data" name="add-property">
				<input type="hidden" id="reference" name="reference" value="<?php echo 'PR'.mt_rand();?>">
				<?php wp_nonce_field( 'zoner_add_property', 'add_property', true, true ); ?>
				<div class="row">
					<div class="col-md-9">
						<section id="submit-form" class="submit-form">

							<section id="basic-information">
								<div class="row">
									<div class="col-md-12">
										<div class="form-group">
											<label for="submit-title"><?php _e('Title', 'zoner'); ?></label>
											<input type="text" class="form-control" id="submit-title" name="title" value="<?php if (!empty($not_saved['title'])) echo $not_saved['title'];?>" required>
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
											'selected' => $default_currency
										);
										zoner_generate_select_($args_select);
										?>
									</div>

									<div class="col-md-4">
										<div class="form-group">
											<label for="submit-price"><?php _e('Price', 'zoner'); ?></label>
											<div class="input-group">
												<span class="input-group-addon"><?php echo $currency_symbol; ?></span>
												<input type="text" class="form-control" id="submit-price" name="price" pattern="\d*" value="<?php  if (!empty($not_saved['price']))  echo $not_saved['price'];?>" required>
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
											'id'	=> 'submit-price-format',
											'class' => array('submit-price-format'),
											'items'	=> $price_format,
											'selected' => 0
										);
										zoner_generate_select_($args_select);
										?>
									</div>
								</div>

								<?php if ( isset($prop_enabled_fields['description']) ):  ?>
									<div class="form-group">
										<label for="submit-description"><?php _e('Description', 'zoner'); ?></label>
										<textarea class="form-control" id="submit-description" rows="8" name="description"><?php if (!empty($not_saved['description'])) echo $not_saved['description'];?></textarea>
									</div><!-- /.form-group -->
								<?php endif; ?>
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
													'id_container' => 'select-country',
													'for' => 'submit-country',
													'label' => __('Country', 'zoner'),
													'name' => 'country',
													'id' => 'submit-country',
													'class' => array('submit-country'),
													'items' => $zoner->countries->countries,
													'selected' => $default_country
												);
												zoner_generate_select_($args_select);
											} else {
												$hidden_fields[] = array(
													'id' => 'submit-country',
													'name' => 'country',
													'value' => $default_country
												);
											}
											?>


											<?php
											if ( isset($prop_enabled_fields['state']) ) {
												$args_select = array();
												$args_select = array(
													'id_container' => 'select-state',
													'for' => 'submit-state',
													'label' => __('State', 'zoner'),
													'name' => 'state',
													'id' => 'submit-state',
													'class' => array('submit-state'),
													'items' => null,
													'selected' => null
												);
												zoner_generate_select_($args_select);
											}
											?>


											<div class="form-group">
												<label for="submit-address"><?php _e('Address', 'zoner'); ?></label>
												<div class="input-group">
													<span class="input-group-addon"><i class="fa fa-map-marker"></i></span>
													<input type="text" class="form-control" id="submit-address" name="address" value="<?php if (!empty($not_saved['address'])) echo $not_saved['address'];?>" required>
												</div>
											</div><!-- /.form-group -->


											<div class="row">
												<?php if ( isset($prop_enabled_fields['city']) ) : ?>
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

														$in_city = null;
														if (isset($not_saved['city']))
															$in_city = $not_saved['city'];

														$args_select = array();
														$args_select = array(
															'id_container' => 'select-city',
															'for'      => 'submit-city',
															'label'    => __('Town / City', 'zoner'),
															'name'	   => 'city',
															'id'	   => 'submit-city',
															'class'    => array('submit-city'),
															'items'	   => $items_city,
															'selected' => null,
														);
														zoner_generate_select_($args_select);
														?>
													</div><!-- /.col-md-6 -->
												<?php endif; ?>
												<?php if ( isset($prop_enabled_fields['zip']) ) :  ?>
													<div class="col-md-6 col-sm-6">
														<div class="form-group">
															<label for="submit-zip"><?php _e('Postcode / Zip', 'zoner'); ?></label>
															<input type="text" class="form-control" id="submit-zip" name="zip" value="<?php if (!empty($not_saved['zip'])) echo $not_saved['zip'];?>">
														</div><!-- /.form-group -->
													</div><!-- /.col-md-6 -->
												<?php endif; ?>
												<?php if ( isset($prop_enabled_fields['district']) ) :  ?>
													<div class="col-md-6 col-sm-6">
														<div class="form-group">
															<label for="submit-district"><?php _e('District', 'zoner'); ?></label>
															<input type="text" class="form-control" id="submit-district" name="district" value="<?php if (!empty($not_saved['district'])) echo $not_saved['district'];?>">
														</div><!-- /.form-group -->
													</div><!-- /.col-md-6 -->
												<?php endif; ?>

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
														<input type="text" class="form-control" id="search-location" name="location" value="<?php if (!empty($not_saved['location'])) echo  $not_saved['location'];?>">
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
															<input name="show_on_map" value="on" type="checkbox" checked><?php _e('Show on map', 'zoner'); ?> <i class="fa fa-question-circle tool-tip"  data-toggle="tooltip" data-placement="right" title="<?php _e('Display position on maps.', 'zoner'); ?>"></i>
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
										<div class="<?php echo $summary_block_classes;  ?>">
											<header><h2><?php _e('Summary', 'zoner'); ?></h2></header>
											<div class="row">

												<?php if ( isset($prop_enabled_fields['condition']) ) :  ?>
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
															'selected' => 0
														);
														zoner_generate_select_($args_select);
														?>
													</div><!-- /.col-md-6 -->
												<?php endif; ?>
												<?php if ( isset($prop_enabled_fields['payment']) ) :  ?>
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
															'items'	   => $zoner->property->get_payment_rent_values(),
															'selected' => 0
														);
														zoner_generate_select_($args_select);
														?>
													</div><!-- /.col-md-6 -->
												<?php endif; ?>


												<?php if ( isset($prop_enabled_fields['type']) ) :  ?>
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
															'selected' => '',
														);
														zoner_generate_select_($args_select);
														?>
													</div><!-- /.col-md-6 -->
												<?php endif; ?>
												<?php if ( isset($prop_enabled_fields['status']) ) :  ?>
													<div class="col-md-6 col-sm-6">
														<?php
														$args_select = array();
														$args_select = array(
															'id_container' => 'submit-property_status',
															'for' 	   => 'submit-property_status',
															'label'    => __('Status', 'zoner'),
															'name'	   => 'status',
															'id'	   => 'submit-property_status',
															'class'    => array('submit-property_status'),
															'items'	   => zoner_get_property_status(),
															'selected' => ''
														);
														zoner_generate_select_($args_select);
														?>
													</div><!-- /.col-md-6 -->
												<?php endif; ?>


												<?php if ( isset($prop_enabled_fields['rooms']) ) :  ?>
													<div class="col-md-6 col-sm-6">
														<div class="form-group">
															<label for="submit-rooms"><?php _e('Rooms', 'zoner'); ?></label>
															<input type="text" class="form-control" id="submit-rooms" name="rooms" pattern="\d*" value="<?php  if (!empty($not_saved['rooms'])) echo $not_saved['rooms'];?>">
														</div><!-- /.form-group -->
													</div><!-- /.col-md-6 -->
												<?php endif; ?>
												<?php if ( isset($prop_enabled_fields['beds']) ) :  ?>
													<div class="col-md-6 col-sm-6">
														<div class="form-group">
															<label for="submit-beds"><?php _e('Beds', 'zoner'); ?></label>
															<input type="text" class="form-control" id="submit-beds" name="beds" pattern="\d*" value="<?php if (!empty($not_saved['beds'])) echo $not_saved['beds'];?>">
														</div><!-- /.form-group -->
													</div><!-- /.col-md-6 -->
												<?php endif; ?>


												<?php if ( isset($prop_enabled_fields['baths']) ) :  ?>
													<div class="col-md-6 col-sm-6">
														<div class="form-group">
															<label for="submit-baths"><?php _e('Baths', 'zoner'); ?> </label>
															<input type="text" class="form-control" id="submit-baths" name="baths" value="<?php if (!empty($not_saved['baths'])) echo $not_saved['baths'];?>" pattern="\d*">
														</div><!-- /.form-group -->
													</div><!-- /.col-md-6 -->
												<?php endif; ?>
												<?php if ( isset($prop_enabled_fields['garages']) ) :  ?>
													<div class="col-md-6 col-sm-6">
														<div class="form-group">
															<label for="submit-garages"><?php _e('Garages', 'zoner'); ?></label>
															<input type="text" class="form-control" id="submit-garages" name="garages" value="<?php if (!empty($not_saved['garages'])) echo $not_saved['garages'];?>" pattern="\d*">
														</div><!-- /.form-group -->
													</div><!-- /.col-md-6 -->
												<?php endif; ?>


												<div class="col-md-6 col-sm-6">
													<div class="form-group">
														<label for="submit-area"><?php _e('Area', 'zoner'); ?></label>
														<input type="text" class="form-control" id="submit-area" name="area" value="<?php if (!empty($not_saved['area'])) echo $not_saved['area'];?>" pattern="\d*" required>
													</div><!-- /.form-group -->
												</div><!-- /.col-md-6 -->

												<?php if ( isset($prop_enabled_fields['area_units']) ) :  ?>
													<div class="col-md-6 col-sm-6">
														<?php
														$args_select = array();
														$args_select = array(
															'id_container'  => 'submit-area-unit',
															'for' 	   		=> 'submit-area-unit',
															'label'    		=> __('Area Units', 'zoner'),
															'name'	   		=> 'area_unit',
															'id'	   		=> 'submit-area-unit',
															'class'    		=> array('submit-area-unit'),
															'items'	   		=> $zoner->property->get_area_units_values(),
															'selected' 		=> $default_area_unit
														);
														zoner_generate_select_($args_select);
														?>
													</div><!-- /.col-md-6 -->
												<?php endif; ?>
											</div><!-- /.row -->

											<!-- Custom Fileds Options -->
											<?php do_action('zoner_add_property_custom_fields_option'); ?>

											<?php if ( isset($prop_enabled_fields['rating']) ) :  ?>
												<div class="checkbox">
													<label>
														<input name="allow-user-rating" value="on" type="checkbox" <?php checked( 'on', $allow_raiting, true ); ?>><?php _e('Allow user rating', 'zoner'); ?> <i class="fa fa-question-circle tool-tip"  data-toggle="tooltip" data-placement="right" title="<?php _e('Users can give you a stars rating which is displayed in property detail', 'zoner'); ?>"></i>
													</label>
												</div>
											<?php endif; ?>


										</div><!-- / summary_block_classes -->

										<?php if ( isset($prop_enabled_fields['featured_image']) ) :  ?>
											<div class="col-md-6 col-sm-6">
												<section id="featured-image">
													<header class="section-title"><h2><?php _e('Set featured image', 'zoner'); ?></h2></header>
													<div class="property-featured-image-inner">
														<img width="100%"  id="prop-featured-image" class="img-responsive" data-src="holder.js/410x410?text=<?php _e('Featured', 'zoner'); ?>" />
													</div>
													<div class="form-group">
														<div class="col-md-offset-2 col-md-8">
															<input id="property-featured-image" name="prop-featured-image" class="file-inputs" type="file" title="<?php _e('Upload Image', 'zoner'); ?>" data-filename-placement="inside" value="">
														</div>
													</div>

												</section><!-- /#place-on-map -->
											</div><!-- / summary_block_classes -->
										<?php endif; ?>
									</div><!-- /.block -->
								</div><!-- /.row -->
							</section><!-- location section -->

							<?php if ( isset($prop_enabled_fields['files']) ) :  ?>
								<section class="block" id="files">
									<header><h2><?php _e('Files', 'zoner'); ?></h2></header>
									<section class="ready-img">
										<div class="row">
											<div id="sortable-image-files" class="sortable-gallery"></div>
										</div>

										<div class="center">
											<div class="form-group">
												<input id="file-upload-files" type="file" class="file-custom" multiple="multiple" name="files[]" data-show-upload="false" data-show-caption="false" data-show-remove="true" accept="application/pdf,application/zip,application/rar,application/tar,application/txt,text/plain,text/html,application/octet-stream,application/vnd.ms-excel,application/vnd.ms-word,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" data-browse-class="btn btn-default" data-browse-label="<?php _e('Browse Files', 'zoner'); ?>">
												<figure class="note"><strong><?php _e('Hint', 'zoner'); ?>:</strong> <?php _e('You can upload all files at once!', 'zoner'); ?></figure>
											</div>
										</div>
									</section>
								</section>
							<?php endif; ?>
							<?php if ( isset($prop_enabled_fields['gallery']) ) :  ?>
								<section class="block" id="gallery">
									<header><h2><?php _e('Gallery', 'zoner'); ?></h2></header>
									<section class="ready-img">
										<div class="row">
											<div id="sortable-image-gallery" class="sortable-gallery"></div>
										</div>

										<div class="center">
											<div class="form-group">
												<input id="file-upload-gallery" type="file" class="file-custom" multiple="multiple" name="gallery[]" data-show-upload="false" data-show-caption="false" data-show-remove="true" accept="image/jpeg,image/png" data-browse-class="btn btn-default" data-browse-label="<?php _e('Browse Images', 'zoner'); ?>">
												<figure class="note"><strong><?php _e('Hint', 'zoner'); ?>:</strong> <?php _e('You can upload all images at once!', 'zoner'); ?></figure>
											</div>
										</div>
									</section>
								</section>
							<?php endif; ?>
							<?php if ( isset($prop_enabled_fields['floor_plans']) ):  ?>
								<section id="flor-plans">
									<header><h2><?php _e('Floor Plans', 'zoner'); ?></h2></header>
									<section class="ready-img">
										<div class="row">
											<div id="sortable-image-plans" class="sortable-gallery"></div>
										</div>

										<div class="center">
											<div class="form-group">
												<input id="file-upload-plans" type="file" class="file-custom" multiple="multiple" name="floorplans[]" data-show-upload="false" data-show-caption="false" data-show-remove="true" accept="image/jpeg,image/png" data-browse-class="btn btn-default" data-browse-label="<?php _e('Browse Images', 'zoner'); ?>">
												<figure class="note"><strong><?php _e('Hint', 'zoner'); ?>:</strong> <?php _e('You can upload all images at once!', 'zoner'); ?></figure>
											</div>
										</div>
									</section>
								</section>
							<?php endif; ?>
							<?php if ( isset($prop_enabled_fields['video']) ) :  ?>
								<section id="property-video-presentation" class="block">
									<section>
										<header><h2><?php _e('Video Presentations', 'zoner'); ?></h2></header>
										<div class="row field-container">
											<?php zoner_get_input_videos(null, false, true); ?>
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
							<?php endif; ?>

							<section id="property_features" class="block">
								<section>
									<header><h2><?php _e('Property Features', 'zoner'); ?></h2></header>
									<?php zoner_get_property_edit_features(); ?>
								</section>
							</section>

							<hr>

						</section>
					</div>	
					<div class="col-md-3 col-sm-3">
						<aside class="submit-step">
								<figure class="step-number">1</figure>
                                <div class="description">
                                    <h4><?php _e($zoner_config['tips-header-fields-insert'], 'zoner'); ?></h4>
                                    <p><?php _e($zoner_config['tips-text-fields-insert'], 'zoner'); ?></p>
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
                                    <button type="submit" class="btn btn-default large"><?php _e('Create Property', 'zoner'); ?></button>
                                </div><!-- /.form-group -->
                            </div>
                        </div>
                        <div class="col-md-3">
                            <aside class="submit-step">
                                <figure class="step-number">2</figure>
                                <div class="description">
                                    <h4><?php _e($zoner_config['tips-header-submit-insert'], 'zoner'); ?></h4>
                                    <p><?php _e($zoner_config['tips-text-submit-insert'], 'zoner'); ?></p>
                                </div>
                            </aside><!-- /.submit-step -->
                        </div><!-- /.col-md-3 -->
                    </div>
                </div>
			</form>	
			<?php } else { ?>
			
				<div class="panel panel-info">
					<div class="panel-heading">
						<h3 class="panel-title"><?php _e("You've reached the limit of properties to add within your pricing plan!", 'zoner'); ?></h3>
					</div>
					<div class="panel-body">
						<?php _e('Please remove any existing Property or go to another package!', 'zoner'); ?>
					</div>
				</div>
			<?php } ?>
		</div>
	</section>

<?php do_action('zoner_after_content') ?>	

<?php get_footer(); ?>