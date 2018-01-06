<?php
/**
 * The template for edit agency front-end
 * @package WordPress
 * @subpackage Zoner_Theme
 * @since Zoner Theme 1.0
 */
?>

<?php get_header(); ?>
<?php do_action('zoner_before_content') ?>	

<?php 
		global $post, $prefix, $zoner, $zoner_config;
        $agency_featured_url = $agency_featured_id = '';
        $agency_featured_id = get_post_thumbnail_id($post->ID);
        $featured_img = wp_get_attachment_image_src($agency_featured_id, 'full');
        if (!empty($featured_img[0])) $agency_featured_url = $featured_img[0];

        $logo_id = get_post_meta($post->ID, $prefix . 'agency_line_img_id', true);
        $logo 	 = get_post_meta($post->ID, $prefix . 'agency_line_img', true);

		$title = $content = $tel = $skype = $mob = $email = $gg_map_url = $facebook_url = $ggplus_url = $linkedin_url = $pinterest_url = $instagram_url = null;
        if (!isset($_POST['edit-agency'])) {
            $title = get_the_title( $post->ID );
            $content = $post->post_content;
            $address = get_post_meta($post->ID, $prefix . 'agency_address', true);

            $gg_map_url = get_post_meta($post->ID, $prefix . 'agency_googlemapurl', true);
            $email 		= get_post_meta($post->ID, $prefix . 'agency_email', true);
            $facebook_url 	= get_post_meta($post->ID, $prefix . 'agency_facebookurl', true);
            $twitter_url 	= get_post_meta($post->ID, $prefix . 'agency_twitterurl', true);
            $ggplus_url 	= get_post_meta($post->ID, $prefix . 'agency_googleplusurl', true);
            $linkedin_url 	= get_post_meta($post->ID, $prefix . 'agency_linkedinurl', true);
            $pinterest_url 	= get_post_meta($post->ID, $prefix . 'agency_pinteresturl', true);
            $instagram_url 	= get_post_meta($post->ID, $prefix . 'agency_instagramurl', true);
            $tel 	= get_post_meta($post->ID, $prefix . 'agency_tel', true);
            $mob 	= get_post_meta($post->ID, $prefix . 'agency_mob', true);
            $skype 	= get_post_meta($post->ID, $prefix . 'agency_skype', true);
        }else{
            if (isset($_POST['agency-title']))
			$title 	= $_POST['agency-title'];
		
			if (isset($_POST['agency-aboutus']))
            $content = $_POST['agency-aboutus'];
		
			if (isset($_POST['agency-address']))
			$address = $_POST['agency-address'];
		
			if (isset($_POST['agency-tel']))
            $tel 	 = $_POST['agency-tel'];
		
			if (isset($_POST['agency-skype']))
            $skype 	 = $_POST['agency-skype'];
			
			if (isset($_POST['agency-mobile']))
            $mob 	 = $_POST['agency-mobile'];
		
			if (isset($_POST['agency-email']))
            $email 	 = $_POST['agency-email'];
			
			if (isset($_POST['agency-ggmapurl']))
            $gg_map_url 	= $_POST['agency-ggmapurl'];
		
			if (isset($_POST['agency-facebook']))
            $facebook_url 	= $_POST['agency-facebook'];
		
			if (isset($_POST['agency-ggplus']))
            $ggplus_url 	= $_POST['agency-ggplus'];
			
			if (isset($_POST['agency-linkedin']))
            $linkedin_url 	= $_POST['agency-linkedin'];
		
			if (isset($_POST['agency-pinterset']))
            $pinterest_url 	= $_POST['agency-pinterset'];
			
			if (isset($_POST['agency-instagram']))
            $instagram_url 	= $_POST['agency-instagram'];
        }

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
			<header><h1><?php _e('Edit Agency', 'zoner'); ?></h1></header>
			<form role="form" id="form-submit" class="form-submit edit-agency form-edit-agency" method="post" action="" enctype="multipart/form-data" name="edit-agency">
				<?php wp_nonce_field( 'zoner_edit_agency', 'edit-agency', true, true ); ?>
				<div class="row">
					<div class="col-md-9">
						<section id="submit-form" class="submit-form">
							
							<section id="basic-information">
                                <div class="row">
									<div class="col-md-12">
										<div class="form-group">
											<label for="submit-title"><?php _e('Title', 'zoner'); ?></label>
                                            <input type="text" class="form-control" id="submit-title" name="agency-title" value="<?php echo $title; ?>" required>
                                        </div><!-- /.form-group -->
                                    </div>
                                </div>
                                <div class="form-group">
									<label for="submit-about-us"><?php _e('About US', 'zoner'); ?></label>
									<textarea class="form-control" id="submit-about-us" rows="8" name="agency-aboutus" required><?php echo $content ?></textarea>
								</div><!-- /.form-group -->
								
								<div class="form-group">
									<label for="submit-address"><?php _e('Address', 'zoner'); ?></label>
									<textarea class="form-control" id="submit-address" rows="5" name="agency-address" required><?php echo $address; ?></textarea>
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
													<input type="text" class="form-control" id="submit-phone" name="agency-tel" value="<?php echo $tel; ?>">
												</div>
											</div><!-- /.form-group -->
											
											<div class="form-group">
												<label for="submit-skype"><?php _e('Skype', 'zoner'); ?></label>
												<div class="input-group">
													<span class="input-group-addon"><i class="fa fa-skype"></i></span>
													<input type="text" class="form-control" id="submit-skype" name="agency-skype" value="<?php echo $skype; ?>">
												</div>
											</div><!-- /.form-group -->
										</div>
										
										<div class="col-md-6">
											<div class="form-group">
												<label for="submit-mobile"><?php _e('Mobile', 'zoner'); ?></label>
												 <div class="input-group">
													<span class="input-group-addon"><i class="fa fa-mobile"></i></span>
													<input type="text" class="form-control" id="submit-mobile" name="agency-mobile" value="<?php echo $mob; ?>">
												 </div>
											</div><!-- /.form-group -->	
											<div class="form-group">
												<label for="submit-email"><?php _e('Email', 'zoner'); ?></label>
												<div class="input-group">
													<span class="input-group-addon">@</span>
													<input type="email" class="form-control" id="submit-email" name="agency-email" value="<?php echo $email; ?>">
												</div>	
											</div><!-- /.form-group -->
										</div>
									</div>	
								</section>	
								
								<section id="social" class="social">	
									<header><h2><?php _e('Social Links', 'zoner'); ?></h2></header>
									<div class="row">
										<div class="col-md-6">
											<div class="form-group">
												<label for="submit-ggmap-url"><?php _e('Google Map URL', 'zoner'); ?></label>
												<div class="input-group">
													<span class="input-group-addon"><i class="fa fa-map-marker"></i></span>
													<input type="text" class="form-control" id="agency-ggmap-url" name="agency-ggmapurl" value="<?php echo $gg_map_url; ?>">
												</div>	
											</div><!-- /.form-group -->
											<div class="form-group">
												<label for="submit-facebook"><?php _e('Facebook', 'zoner'); ?></label>
												<div class="input-group">
													<span class="input-group-addon"><i class="fa fa-facebook"></i></span>
													<input type="text" class="form-control" id="submit-facebook" name="agency-facebook" value="<?php echo $facebook_url; ?>">
												</div>
											</div><!-- /.form-group -->
											<div class="form-group">
												<label for="submit-linkedin"><?php _e('Linkedin', 'zoner'); ?></label>
												<div class="input-group">
													<span class="input-group-addon"><i class="fa fa-linkedin"></i></span>
													<input type="text" class="form-control" id="submit-linkedin" name="agency-linkedin" value="<?php echo $linkedin_url; ?>">
												</div>
											</div><!-- /.form-group -->
											<div class="form-group">
												<label for="submit-instagram"><?php _e('Instagram', 'zoner'); ?></label>
												 <div class="input-group">
													<span class="input-group-addon"><i class="fa fa-instagram"></i></span>
													<input type="text" class="form-control" id="submit-instagram" name="agency-instagram" value="<?php echo $instagram_url; ?>">
												 </div>
											</div><!-- /.form-group -->
										</div>
										
										
										<div class="col-md-6">
											<div class="form-group">
												<label for="submit-google-plus"><?php _e('Google+', 'zoner'); ?></label>
												<div class="input-group">
													<span class="input-group-addon"><i class="fa fa-google-plus"></i></span>
													<input type="text" class="form-control" id="submit-google-plus" name="agency-ggplus" value="<?php echo $ggplus_url; ?>">
												</div>
											</div><!-- /.form-group -->
											<div class="form-group">
												<label for="submit-twitter"><?php _e('Twitter', 'zoner'); ?></label>
												<div class="input-group">
													<span class="input-group-addon"><i class="fa fa-twitter"></i></span>
													<input type="text" class="form-control" id="submit-twitter" name="agency-twitter" value="<?php echo $twitter_url; ?>">
												</div>
											</div><!-- /.form-group -->
											<div class="form-group">
												<label for="submit-pinterset"><?php _e('Pinterset', 'zoner'); ?></label>
												<div class="input-group">
													<span class="input-group-addon"><i class="fa fa-pinterest"></i></span>
													<input type="text" class="form-control" id="submit-pinterset" name="agency-pinterset" value="<?php echo $pinterest_url; ?>">
												</div>
											</div><!-- /.form-group -->
										</div>
								</div><!-- /.row -->
							</section><!-- contact-soical section -->
							
							
							<section id="media" class="media">
								<div class="row">
									<div class="block clearfix">
										<div class="col-md-6">
											<section id="featured-image">
												<header class="section-title"><h2><?php _e('Set featured image', 'zoner'); ?></h2></header>
												<div class="agency-featured-image-inner">
													<?php 
														$img_src = '';
														if (!empty($agency_featured_url)) { 
															$img_src = esc_url($agency_featured_url);
															echo '<span class="remove-agency-featured"><i class="fa fa-trash-o"></i></span>';
														} else {
															$img_src = 'holder.js/200x200?text='.__('Featured', 'zoner');
														}	
													?>
													<img id="agency-featured-image" class="img-responsive" data-src="<?php echo $img_src; ?>" src="<?php echo $img_src; ?>"/>
													<input type="hidden" id="agency-featured-image-exists" name="agency-featured-image-exists" value="<?php echo $agency_featured_id; ?>" />
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
													<?php 
														$img_src = '';
														if (!empty($logo)) { 
															$img_src = esc_url($logo);
															echo '<span class="remove-agency-logo"><i class="fa fa-trash-o"></i></span>';
														} else {
															$img_src = 'holder.js/200x200?text='.__('Logo', 'zoner');
														}	
													?>
													<img id="agency-logo-image" class="img-responsive" data-src="<?php echo $img_src; ?>" src="<?php echo $img_src; ?>"/>
													<input type="hidden" id="agency-logo-image-exists" name="agency-logo-image-exists" value="<?php echo $logo_id; ?>" />
												</div>
												<div class="form-group">
													<div class="col-md-offset-2  col-md-8">
														<input id="agency-logo-image-file" name="agency-logo-image-file" class="file-inputs" type="file" title="<?php _e('Upload Image', 'zoner'); ?>" data-filename-placement="inside" value="">
													</div>
												</div>
											
											</section><!-- /#featured-image -->
										</div>
									</div><!-- /.block -->
								</div><!-- /.row -->
							</section><!-- media section -->
							
						</section>	
					</div>	
					<div class="col-md-3 col-sm-3">
						<aside class="submit-step">
								<figure class="step-number">1</figure>
                                <div class="description">
                                    <h4><?php _e('Edit Agency fields', 'zoner'); ?></h4>
                                    <p><?php _e('Carefully check entered information and than click button to update them.', 'zoner'); ?></p>
                                </div>
                        </aside><!-- /.submit-step -->
					</div>
				</div><!-- row -->
				
				<div class="row">
                    <div class="block">
                        <div class="col-md-9">
                            <div class="center">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-default large"><?php _e('Update Information', 'zoner'); ?></button>
                                </div><!-- /.form-group -->
                            </div>
                        </div>
                        <div class="col-md-3">
                            <aside class="submit-step">
                                <figure class="step-number">2</figure>
                                <div class="description">
                                    <h4><?php _e('Update Agency', 'zoner'); ?></h4>
                                    <p><?php _e('Carefully check entered information and than click button to update them.', 'zoner'); ?></p>
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