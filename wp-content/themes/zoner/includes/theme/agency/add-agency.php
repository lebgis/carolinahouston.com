<?php
/**
 * The template for add agency front-end
 * @package WordPress
 * @subpackage Zoner_Theme
 * @since Zoner Theme 1.0
 */
?>

<?php get_header(); ?>
<?php do_action('zoner_before_content') ?>	

<?php 

	global $prefix, $zoner, $zoner_config; 
	
	
	if (!empty($zoner_config['register-agency-account'])) {
		$is_create_agency_account = esc_attr($zoner_config['register-agency-account']);
			
		if (!empty($zoner_config['paid-system']))
		$is_create_agency_account = $zoner->membership->zoner_is_available_agency_for_curr_user();	
	}	
	
	$count_agencies = $zoner->invites->zoner_get_count_agencies_from_agent(get_current_user_id());
	$not_saved = $_POST;
	if  ((($count_agencies == 0) && ($is_create_agency_account != 0) &&
		  ($zoner->zoner_get_current_user_role() == 'Agent')) ||
		  ($zoner->zoner_get_current_user_role() == 'Administrator') ||
			(current_user_can('edit_agencys', get_current_user_id()))
		) {

?>

        <?php if (!$zoner->validate->check('agency')){?>
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
			<header><h1><?php _e('Create Agency', 'zoner'); ?></h1></header>
			<form role="form" id="form-submit" class="form-submit edit-agency form-edit-agency" method="post" action="" enctype="multipart/form-data" name="edit-agency">
				<?php wp_nonce_field( 'zoner_add_agency', 'add-agency', true, true ); ?>
				<div class="row">
					<div class="col-md-9">
						<section id="submit-form" class="submit-form">
							<section id="basic-information">
                                <div class="row">
									<div class="col-md-12">
										<div class="form-group">
											<label for="submit-title"><?php _e('Agency Title', 'zoner'); ?></label>
                                            <input type="text" class="form-control" id="submit-title" name="agency-title" value="<?php echo isset($not_saved['agency-title']) ? $not_saved['agency-title'] : null; ?>" required>
                                        </div><!-- /.form-group -->
                                    </div>
                                </div>
                                <div class="form-group">
									<label for="submit-about-us"><?php _e('Description', 'zoner'); ?></label>
									<textarea class="form-control" id="submit-about-us" rows="8" name="agency-aboutus" required><?php echo isset($not_saved['agency-aboutus']) ? $not_saved['agency-aboutus'] : null; ?></textarea>
								</div><!-- /.form-group -->
								
								<div class="form-group">
									<label for="submit-address"><?php _e('Address', 'zoner'); ?></label>
									<textarea class="form-control" id="submit-address" rows="5" name="agency-address" required><?php echo isset($not_saved['agency-address']) ? $not_saved['agency-address'] :null; ?></textarea>
								</div><!-- /.form-group -->
							</section><!-- /#basic-information -->
							
							
							<section id="contact" class="contact">
								<header><h2><?php _e('Contact', 'zoner'); ?></h2></header>
								<div class="row">
									<div class="col-md-6">
										<div class="form-group">
										<label for="submit-phone"><?php _e('Phone', 'zoner'); ?></label>
											<div class="input-group">
												<span class="input-group-addon"><i class="fa fa-phone"></i></span>
												<input type="text" class="form-control" id="submit-phone" name="agency-tel" value="<?php echo isset($not_saved['agency-tel']) ? $not_saved['agency-tel'] :null; ?>">
											</div>
										</div><!-- /.form-group -->
										
										<div class="form-group">
											<label for="submit-skype"><?php _e('Skype', 'zoner'); ?></label>
											<div class="input-group">
												<span class="input-group-addon"><i class="fa fa-skype"></i></span>
												<input type="text" class="form-control" id="submit-skype" name="agency-skype" value="<?php echo isset($not_saved['agency-skype']) ? $not_saved['agency-skype'] : null; ?>">
											</div>
										</div><!-- /.form-group -->
									</div>
									
									<div class="col-md-6">
										<div class="form-group">
											<label for="submit-mobile"><?php _e('Mobile', 'zoner'); ?></label>
											 <div class="input-group">
												<span class="input-group-addon"><i class="fa fa-mobile"></i></span>
												<input type="text" class="form-control" id="submit-mobile" name="agency-mobile" value="<?php echo isset($not_saved['agency-mobile']) ? $not_saved['agency-mobile'] : null;?>">
											 </div>
										</div><!-- /.form-group -->	
										<div class="form-group">
											<label for="submit-email"><?php _e('Email', 'zoner'); ?></label>
											<div class="input-group">
												<span class="input-group-addon">@</span>
												<input type="email" class="form-control" id="submit-email" name="agency-email" value="<?php echo isset($not_saved['agency-email']) ? $not_saved['agency-email'] : null; ?>" required>
											</div>	
										</div><!-- /.form-group -->
									</div>
								
								</div><!-- /.row -->
							</section><!-- contact-soical section -->
						
							<section id="social" class="social">
								<header><h2><?php _e('Social Links', 'zoner'); ?></h2></header>
								<div class="row">
									<div class="col-md-6">
										<div class="form-group">
											<label for="submit-ggmap-url"><?php _e('Google Map URL', 'zoner'); ?></label>
											<div class="input-group">
												<span class="input-group-addon"><i class="fa fa-map-marker"></i></span>
												<input type="text" class="form-control" id="agency-ggmap-url" name="agency-ggmapurl" value="<?php echo isset($not_saved['agency-ggmapurl']) ? $not_saved['agency-ggmapurl'] : null; ?>">
											</div>
										</div><!-- /.form-group -->
										<div class="form-group">
											<label for="submit-facebook"><?php _e('Facebook', 'zoner'); ?></label>
											<div class="input-group">
												<span class="input-group-addon"><i class="fa fa-facebook"></i></span>
												<input type="text" class="form-control" id="submit-facebook" name="agency-facebook" value="<?php echo isset($not_saved['agency-facebook']) ? $not_saved['agency-facebook'] : null; ?>">
											</div>
										</div><!-- /.form-group -->
										<div class="form-group">
											<label for="submit-linkedin"><?php _e('Linkedin', 'zoner'); ?></label>
											<div class="input-group">
												<span class="input-group-addon"><i class="fa fa-linkedin"></i></span>
												<input type="text" class="form-control" id="submit-linkedin" name="agency-linkedin" value="<?php echo isset($not_saved['agency-linkedin']) ? $not_saved['agency-linkedin'] : null; ?>">
											</div>
										</div><!-- /.form-group -->
										<div class="form-group">
											<label for="submit-instagram"><?php _e('Instagram', 'zoner'); ?></label>
											 <div class="input-group">
												<span class="input-group-addon"><i class="fa fa-instagram"></i></span>
												<input type="text" class="form-control" id="submit-instagram" name="agency-instagram" value="<?php echo isset($not_saved['agency-instagram']) ? $not_saved['agency-instagram'] : null; ?>">
											 </div>
										</div><!-- /.form-group -->
									</div>
									
									<div class="col-md-6">
										<div class="form-group">
											<label for="submit-google-plus"><?php _e('Google+', 'zoner'); ?></label>
											<div class="input-group">
												<span class="input-group-addon"><i class="fa fa-google-plus"></i></span>
												<input type="text" class="form-control" id="submit-google-plus" name="agency-ggplus" value="<?php echo isset($not_saved['agency-ggplus']) ? $not_saved['agency-ggplus'] : null; ?>">
											</div>
										</div><!-- /.form-group -->
										<div class="form-group">
											<label for="submit-twitter"><?php _e('Twitter', 'zoner'); ?></label>
											<div class="input-group">
												<span class="input-group-addon"><i class="fa fa-twitter"></i></span>
												<input type="text" class="form-control" id="submit-twitter" name="agency-twitter" value="<?php echo isset($not_saved['agency-twitter']) ? $not_saved['agency-twitter'] : null; ?>">
											</div>
										</div><!-- /.form-group -->
										<div class="form-group">
											<label for="submit-pinterset"><?php _e('Pinterset', 'zoner'); ?></label>
											<div class="input-group">
												<span class="input-group-addon"><i class="fa fa-pinterest"></i></span>
												<input type="text" class="form-control" id="submit-pinterset" name="agency-pinterset" value="<?php echo isset($not_saved['agency-pinterset']) ? $not_saved['agency-pinterset'] : null; ?>">
											</div>
										</div><!-- /.form-group -->
									</div>
								</div>	
							</section>	
								
								
							<section id="media" class="media">
								<div class="row">
									<div class="col-md-6">
										<section id="featured-image">
											<header class="section-title"><h2><?php _e('Set featured image', 'zoner'); ?></h2></header>
											<div class="agency-featured-image-inner">
												<img id="agency-featured-image" class="img-responsive" data-src="holder.js/200x200?text=<?php _e('Featured', 'zoner'); ?>"/>
											</div>
											<div class="form-group">
												<div class="col-md-offset-2  col-md-8">
													<input id="agency-featured-image-file" name="agency-featured-image-file" class="file-inputs" type="file" title="<?php _e('Upload Image', 'zoner'); ?>" data-filename-placement="inside" value="">
												</div>
											</div>
										
										</section><!-- /#featured-image -->
									</div>
											
											
									<div class="col-md-6">
										<section id="logo-image">
											<header class="section-title"><h2><?php _e('Set logo image', 'zoner'); ?></h2></header>
											<div class="agency-logo-image-inner">
												<img id="agency-logo-image" class="img-responsive" data-src="holder.js/200x200?text=<?php _e('Logo', 'zoner'); ?>"/>
											</div>
											<div class="form-group">
												<div class="col-md-offset-2  col-md-8">
													<input id="agency-logo-image-file" name="agency-logo-image-file" class="file-inputs" type="file" title="<?php _e('Upload Image', 'zoner'); ?>" data-filename-placement="inside" value="">
												</div>
											</div>
										
										</section><!-- /#featured-image -->
									</div>
								</div><!-- /.row -->
							</section><!-- media section -->
						</section>	
					</div>	
					<div class="col-md-3 col-sm-3">
						<aside class="submit-step">
								<figure class="step-number">1</figure>
                                <div class="description">
                                    <h4><?php _e('Edit Agency fields', 'zoner'); ?></h4>
                                    <p><?php _e('Carefully check entered information and than click button to submit them.', 'zoner'); ?></p>
                                </div>
                        </aside><!-- /.submit-step -->
					</div>
				</div><!-- row -->
				
				<div class="row">
                    <div class="block">
                        <div class="col-md-9">
                            <div class="center">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-default large"><?php _e('Create Agency', 'zoner'); ?></button>
                                </div><!-- /.form-group -->
                            </div>
                        </div>
                        <div class="col-md-3">
                            <aside class="submit-step">
                                <figure class="step-number">2</figure>
                                <div class="description">
                                    <h4><?php _e('Submit Agency', 'zoner'); ?></h4>
                                    <p><?php _e('Carefully check entered information and than click button to submit them.', 'zoner'); ?></p>
                                </div>
                            </aside><!-- /.submit-step -->
                        </div><!-- /.col-md-3 -->
                    </div>
                </div>
			</form>	
		</div>
	</section>
	
	<?php } else { ?>
		<section id="edit-property-" class="edit-property">
			<div class="container">
				<header><h1><?php _e('Create Agency', 'zoner'); ?></h1></header>
				<div class="panel panel-info">
					<div class="panel-heading">
						<h3 class="panel-title"><?php _e("You're unable to register an agency with your current service plan!", 'zoner'); ?></h3>
					</div>
					<div class="panel-body">
						<?php _e('Upgrade your service plan or contact site administrator.', 'zoner'); ?>
					</div>
				</div>
			</div>
		</section>	
	<?php } ?>
			
		

<?php do_action('zoner_after_content') ?>	

<?php get_footer(); ?>