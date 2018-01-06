<?php 

if ( ! function_exists( 'zoner_remove_admin_bar' ) ) {				
	function zoner_remove_admin_bar() {
		show_admin_bar((current_user_can('administrator') || current_user_can('editor')));	
	}
}	

if ( ! function_exists( 'zoner_generate_profile_tabs' ) ) {				
	function zoner_generate_profile_tabs() {
		global $zoner_config, $zoner;

		$role = $zoner->zoner_get_current_user_role();
		if (!empty($zoner_config['property-agent-conversation'])){
		$array_profile_tabs = apply_filters('zoner_profile_tabs', array(
									'my_profile' 	 => array('name' => __( 'Profile', 		'zoner' ), 'icon' => 'fa-user'),
									'my_package'     => array('name' => __( 'My Package', 	'zoner' ), 'icon' => 'fa-credit-card'),
									'my_properties'  => array('name' => __( 'My Properties','zoner' ), 'icon' => 'fa-home'),
									'my_bookmarks'   => array('name' => __( 'Bookmarked Properties', 'zoner' ), 'icon' => 'fa-heart'),
									'my_agencies'    => array('name' => __( 'My Agencies',	'zoner' ), 'icon' => 'fa-building'),
									'my_invites'     => array('name' => __( 'My Invites', 	'zoner' ), 'icon' => 'fa-users'),
									'my_messages'    => array('name' => __( 'My Messages', 	'zoner' ), 'icon' => 'fa-weixin'),
                                    'delete_account' => array('name' => __( 'Delete account', 	'zoner' ), 'icon' => 'fa-trash-o'),
		));
		}else{
		$array_profile_tabs = apply_filters('zoner_profile_tabs', array(
									'my_profile' 	 => array('name' => __( 'Profile', 		'zoner' ), 'icon' => 'fa-user'),
									'my_package'     => array('name' => __( 'My Package', 	'zoner' ), 'icon' => 'fa-credit-card'),
									'my_properties'  => array('name' => __( 'My Properties','zoner' ), 'icon' => 'fa-home'),
									'my_bookmarks'   => array('name' => __( 'Bookmarked Properties', 'zoner' ), 'icon' => 'fa-heart'),
									'my_agencies'    => array('name' => __( 'My Agencies',	'zoner' ), 'icon' => 'fa-building'),
									'my_invites'     => array('name' => __( 'My Invites', 	'zoner' ), 'icon' => 'fa-users'),
                                    'delete_account' => array('name' => __( 'Delete account', 	'zoner' ), 'icon' => 'fa-trash-o'),
		));
		}
		
		if (($role != 'Agent') && ($role != 'Administrator')) {
			if (  !current_user_can( 'edit_propertys', get_current_user_id() ) ) {
				unset($array_profile_tabs['my_properties']);
			}
			unset($array_profile_tabs['my_agencies']);
			unset($array_profile_tabs['my_invites']);
			unset($array_profile_tabs['my_package']);
		} 
		
		
		if ((empty($zoner_config['paid-system']) && ($zoner_config['paid-system'] != 1)) || (esc_attr($zoner_config['paid-type-properties']) == 1)) {
			unset($array_profile_tabs['my_package']);
		}	
		
		if (!is_user_logged_in()) {
			unset($array_profile_tabs['my_messages']);
		}
		
		$profile_page = 'my_profile';
		
		foreach ( $_GET as $key => $val ) {
			if ( 'profile-page' == $key) $profile_page = $val;
		}
		
		
	?>
		<div class="col-md-3 col-sm-2">
			<section id="sidebar" class="sidebar">
				<header><h3 class="widget-title"><?php _e('Account', 'zoner'); ?></h3></header>
					<aside>
						<ul class="sidebar-navigation">
							<?php 
							
								foreach ($array_profile_tabs as $key => $val) { 
										 $arr_params = array( 'profile-page' => $key);
								
							?>
								<li class="<?php if ($key == $profile_page) echo 'active'; ?>">
									<a href="<?php echo esc_url(add_query_arg($arr_params)); ?>">
										<i class="fa <?php echo $val['icon']; ?>"></i>
										<span><?php echo $val['name']; ?></span>
									</a>
								</li>		
							<?php 
								
								} 
							
							?>
						</ul>
					</aside>
			</section>
		</div>
	<?php 	
	}
}	

if ( ! function_exists( 'zoner_get_profile_avartar' ) ) {				
	function zoner_get_profile_avartar($userID = '') {
		global $zoner_config, $prefix, $zoner;
		if ($userID == '') $userID = get_current_user_id();
		
		$all_meta_for_user = get_user_meta( $userID );
		$avatar = '';
		
		$img_url = get_template_directory_uri() . '/includes/theme/profile/res/avatar.jpg';

		if (!empty($all_meta_for_user[$prefix.'avatar']))
			$avatar = $all_meta_for_user[$prefix.'avatar'];
		if (!empty($all_meta_for_user[$prefix.'avatar_id']))
			$avatar_id = $all_meta_for_user[$prefix.'avatar_id'];
			
		if (is_array($avatar)) { 
				$avatar = array_filter($avatar);
				if (!empty($avatar_id)) {
					$avatar_icon = wp_get_attachment_image_src( current($avatar_id), 'zoner-avatar-ceo' );
					if (!empty($avatar_icon)) $img_url = $avatar_icon[0];
				} else {
					if (!empty($avatar)) $img_url = current($avatar);
				}			
		}	
		
		return '<img alt="" id="avatar-image" class="image" src="'.esc_url($img_url).'">';
	}
}	


if ( ! function_exists( 'zoner_get_user_id' ) ) {				
	function zoner_get_user_id() {
		global $zoner_config, $prefix, $wp_query, $zoner;
				
		$user_id = get_current_user_id();
		if( array_key_exists('author_name', $wp_query->query_vars) && !empty($wp_query->query_vars['author_name'])) {
			$user_data = $wp_query->get_queried_object('WP_User');
			if ($user_id != $user_data->ID) $user_id = $user_data->ID;
		}
		
		return $user_id;
	}
}	

if ( ! function_exists( 'zoner_get_author_content' ) ) {				
	function zoner_get_author_content() {
		global $zoner_config, $zoner, $prefix;
		
		$query_author  = get_queried_object();
		$query_user_id = $query_author->ID;
		$curr_user_id  = get_current_user_id();
		
		if (is_author() && is_user_logged_in() && ($curr_user_id == $query_user_id)) {	
			zoner_generate_profile_tabs();
			zoner_get_profile_page_data();
		} elseif (is_author() && (($zoner->zoner_get_user_role_by_id($query_user_id) == 'agent') || ($zoner->zoner_get_user_role_by_id($query_user_id) == 'administrator'))) {
			zoner_get_agent_information();
		} elseif (is_author() || ($curr_user_id != $query_user_id)) {
			zoner_get_author_information();
		}
	}		
}

if ( ! function_exists( 'zoner_generate_profile_info' ) ) {				
	function zoner_generate_profile_info() {
		global $zoner_config, $prefix, $zoner;
		
		$avatar = '';
		$avatar_id = -1;
		$userID = zoner_get_user_id();
		$all_meta_for_user = get_user_meta( $userID );
		
		if (!empty($all_meta_for_user[$prefix.'avatar']))
		$avatar    = current($all_meta_for_user[$prefix.'avatar']);
		
		if (!empty($all_meta_for_user[$prefix.'avatar_id']))
		$avatar_id = current($all_meta_for_user[$prefix.'avatar_id']);
		
		
		$currencies_list = $zoner->currency->get_zoner_currency_dropdown_settings();
		$currencies_list = array_merge($currencies_list, array('' => __('Not selected', 'zoner')));
		
		
		$social_arr	 = apply_filters('zoner_profile_social', array(
									 'facebookurl'   => array('icon' => 'fa-facebook', 		'class'=>'facebook'),
									 'twitterurl'    => array('icon' => 'fa-twitter',   	'class'=>'twitter'),
									 'googleplusurl'  => array('icon' => 'fa-google-plus',  'class'=>'googleplus'),
									 'linkedinurl'   => array('icon' => 'fa-linkedin',   	'class'=>'linkedin'),
									 'pinteresturl'	 => array('icon' => 'fa-pinterest', 	'class'=>'pinterest'),
										
												
						));
		
	?>
		<div class="col-md-9 col-sm-10">
			<section id="profile">
				<header><h1><?php _e('Profile', 'zoner'); ?></h1></header>
					<div class="account-profile">
						<div class="row">
							<form role="form" id="form-account-profile" class="form-account-profile" method="post" action="" enctype="multipart/form-data">
								<?php wp_nonce_field( 'zoner_save_profile', 'save_profile', true, true ); ?>
								<input type="hidden" id="form-account-avatar" 	 name="form-account-avatar"    value="<?php echo $avatar;   ?>" />
								<input type="hidden" id="form-account-avatar-id" name="form-account-avatar-id" value="<?php echo $avatar_id; ?>" />
									
								<div class="col-md-3 col-sm-3">
									<div class="avatar-wrapper">
										<?php if ($avatar_id != -1) { ?>
												<span class="remove-btn"><i class="fa fa-trash-o"></i></span>
										<?php } ?>
										<?php echo zoner_get_profile_avartar($userID); ?>
									</div>
									<div class="form-group tool-tip-info"  data-original-title="<?php _e('image size has to be less than 1 MB', 'zoner'); ?>">
										<input id="form-account-avatar-file" name="form-account-avatar-file" class="file-inputs" type="file" title="<?php _e('Upload Avatar', 'zoner'); ?>" data-filename-placement="inside" value="">
									</div>
								</div>
								
								<div class="col-md-9 col-sm-9">
									<section id="contact">
										<h3><?php _e('Contact', 'zoner'); ?></h3>
										<dl class="contact-fields">
											<dt><label for="form-account-fname"><?php _e('First Name', 'zoner'); ?>:</label></dt>
											<dd><div class="form-group">
												<input type="text" class="form-control" id="form-account-fname" name="form-account-fname" required value="<?php the_author_meta( 'first_name', $userID ); ?>">
											</div><!-- /.form-group --></dd>
											
											<dt><label for="form-account-lname"><?php _e('Last Name', 'zoner'); ?>:</label></dt>
											<dd><div class="form-group">
												<input type="text" class="form-control" id="form-account-lname" name="form-account-lname" required value="<?php the_author_meta( 'last_name', $userID ); ?>">
											</div><!-- /.form-group --></dd>
											
											<dt><label for="form-account-phone"><?php _e('Phone', 'zoner'); ?>:</label></dt>
											<dd><div class="form-group">
												<input type="text" class="form-control" id="form-account-phone" name="form-account-phone" value="<?php the_author_meta( $prefix.'tel', $userID ); ?>">
											</div><!-- /.form-group --></dd>
											
											<dt><label for="form-account-mobile"><?php _e('Mobile', 'zoner'); ?>:</label></dt>
											<dd><div class="form-group">
												<input type="text" class="form-control" id="form-account-mobile" name="form-account-mobile" value="<?php the_author_meta( $prefix.'mob', $userID ); ?>">
											</div><!-- /.form-group --></dd>
											
											<dt><label for="form-account-email"><?php _e('Email', 'zoner');?>:</label></dt>
											<dd><div class="form-group">
												<input type="text" class="form-control" id="form-account-email" name="form-account-email" value="<?php the_author_meta( 'user_email', $userID ); ?>" disabled="disabled">
											</div><!-- /.form-group --></dd>
											<dt><label for="form-account-skype"><?php _e('Skype', 'zoner');?>:</label></dt>
											<dd><div class="form-group">
												<input type="text" class="form-control" id="form-account-skype" name="form-account-skype" value="<?php the_author_meta( $prefix.'skype', $userID ); ?>">
											</div><!-- /.form-group --></dd>
										</dl>
									</section>
									<section id="about-me">
										<h3><?php _e('About Me', 'zoner'); ?></h3>
										<div class="form-group">
											<textarea class="form-control" id="form-contact-agent-message" rows="5" name="form-contact-agent-message"><?php the_author_meta( 'description', $userID ); ?></textarea>
										</div><!-- /.form-group -->
									</section>
									
									<?php if (is_user_logged_in() && !is_super_admin() && (isset($zoner_config['profile-localization-currency']) && ($zoner_config['profile-localization-currency'] == 1))) { ?>
									<section id="localize-currency">
										<h3><?php _e('Localizations', 'zoner'); ?></h3>
										<div class="form-group">
											<?php 
												$args_select = array();
												$args_select = array(
													'for' 	=> 'submit-user-currency',
													'label' => __('Choose your currency to display all prices in your currency', 'zoner'),
													'name'	=> 'user_currency',
													'id'	=> 'submit-user-currency',
													'class' => array('submit-user-currency'),
													'items'		=> $currencies_list,
													'selected'  => esc_attr(get_the_author_meta( $prefix.'user_currency', $userID )) 
												);
												zoner_generate_select_($args_select);
											?>
										</div><!-- /.form-group -->
									</section>
									<?php } ?>
									<section id="social">
										<h3><?php _e('My Social Network', 'zoner'); ?></h3>
										<?php 
											foreach ($social_arr as $key => $values) {
										?>
										
											<div class="form-group">
												<div class="input-group">
													<span class="input-group-addon"><i class="fa <?php echo $values['icon']; ?>"></i></span>
													<input type="text" class="form-control" id="account-social-<?php echo $values['class']; ?>" name="account-social-<?php echo $values['class']; ?>" value="<?php the_author_meta( $prefix.$key, $userID ); ?>"> 
												</div>
											</div><!-- /.form-group -->
										<?php 
											}
										?>
										
										<div class="form-group clearfix">
											<button type="submit" class="btn pull-right btn-default" id="account-submit"><?php _e('Save Changes', 'zoner'); ?></button>
										</div><!-- /.form-group -->
									</section>
								</div>
							</form><!-- /#form-contact -->
						
						
						<div class="col-md-offset-3 col-md-9 col-sm-10">			
							<section id="change-password">
								<header><h2><?php _e('Change Your Password', 'zoner'); ?></h2></header>
								<div class="row">
									<div class="col-md-6 col-sm-6">
										<form role="form" id="form-account-password" class="form-account-password" method="post" action="">
											<?php wp_nonce_field( 'zoner_change_password', 'change_password', true, true ); ?>
											<div class="form-group">
												<label for="form-account-password-current"><?php _e('Current Password', 'zoner'); ?></label>
												<input type="password" class="form-control" id="form-account-password-current" name="form-account-password-current" required>
											</div><!-- /.form-group -->
											<div class="form-group">
												<label for="form-account-password-new"><?php _e('New Password','zoner'); ?> </label>
												<input type="password" class="form-control" id="form-account-password-new" name="form-account-password-new" required>
											</div><!-- /.form-group -->
											<div class="form-group">
												<label for="form-account-password-confirm-new"><?php _e('Confirm New Password', 'zoner'); ?></label>
												<input type="password" class="form-control" id="form-account-password-confirm-new" name="form-account-password-confirm-new" required>
											</div><!-- /.form-group -->
											<div class="form-group clearfix">
												<button type="submit" class="btn btn-default" id="form-account-password-submit"><?php _e('Change Password', 'zoner');?></button>
											</div><!-- /.form-group -->
										</form><!-- /#form-account-password -->
									</div>
									
									<div class="col-md-6 col-sm-6">
										<strong><?php _e('Hint', 'zoner'); ?>:</strong>
										<p><?php _e('Be careful. After you change the password, the password is automatically applied.', 'zoner'); ?></p>
										<p><?php _e("If you don't have a current password you can sign out and reset your password at sign in page.", 'zoner'); ?></p>
									</div>
								</div>
							</section>
						</div><!-- /.col-md-9 -->
					</div><!-- /.row -->
				</div><!-- /.account-profile -->
			</section><!-- /#profile -->
		</div><!-- /.col-md-9 -->
	<?php 			
	}
}	

if ( ! function_exists( 'zoner_process_save_profile' ) ) {				
	function zoner_process_save_profile() {
		global $zoner_config, $prefix, $zoner;
			   $userID = zoner_get_user_id();
			   $attach_id = -1;

		if ( is_author()  && isset($_POST['save_profile']) && wp_verify_nonce($_POST['save_profile'], 'zoner_save_profile')) {
			update_user_meta( $userID, 'first_name', 			 $_POST['form-account-fname']);
			update_user_meta( $userID, 'last_name',  			 $_POST['form-account-lname']);
			update_user_meta( $userID, $prefix.'tel', 			 esc_attr($_POST['form-account-phone']));
			update_user_meta( $userID, $prefix.'mob', 			 esc_attr($_POST['form-account-mobile']));
			update_user_meta( $userID, 'user_email', 			 sanitize_email($_POST['form-account-email']));
			update_user_meta( $userID, 'description', 			 $_POST['form-contact-agent-message']);
			update_user_meta( $userID,  $prefix.'skype', 		 esc_attr($_POST['form-account-skype']));
			update_user_meta( $userID,  $prefix.'facebookurl', 	 esc_url($_POST['account-social-facebook']));
			update_user_meta( $userID,  $prefix.'twitterurl', 	 esc_url($_POST['account-social-twitter']));
			update_user_meta( $userID,  $prefix.'googleplusurl', esc_url($_POST['account-social-googleplus']));
			update_user_meta( $userID,  $prefix.'linkedinurl', 	 esc_url($_POST['account-social-linkedin']));
			update_user_meta( $userID,  $prefix.'pinteresturl',  esc_url($_POST['account-social-pinterest']));
			
			update_user_meta( $userID,  $prefix.'user_currency',  esc_attr($_POST['user_currency']));
			
			if (empty($_POST['form-account-avatar-id']) && empty($_POST['form-account-avatar'])) {
				delete_user_meta( $userID,  $prefix.'avatar' );
				delete_user_meta( $userID,  $prefix.'avatar_id');
			}
		}
		
		if (!empty($_FILES['form-account-avatar-file']['name'])) {
			foreach ($_FILES as $file => $array) {
				$attach_id = zoner_insert_attachment( $file, 0 );
			}   
			
			if ($attach_id != -1) {
				update_user_meta( $userID,  $prefix.'avatar', 	 wp_get_attachment_url( $attach_id ));
				update_user_meta( $userID,  $prefix.'avatar_id', $attach_id);
			}	
		} 
		
		if (!empty($_POST) && isset($_POST['save_profile'])) wp_safe_redirect( zoner_curPageURL() );
	}
}

if ( ! function_exists( 'zoner_generate_profile_my_package' ) ) {				
	function zoner_generate_profile_my_package() {
		global $zoner_config, $prefix, $zoner;	
		
		$titles = $prices = $submit_limit = $featured_limit = $agency_profile = $agent_profile = $time_periods = $package_selected = '';
		$args_pack = array();
		$args_pack = array (	
							'post_type' 		=> 'packages',
							'post_status' 		=> 'publish',
							'orderby'			=> 'meta_value_num ' . $prefix. 'pack_price',
							'order'				=> 'ASC',
							'posts_per_page' 	=> -1,
							'meta_query'	=> array(
									array(
										'key'   	=> $prefix . 'pack_visible',
										'value' 	=> 'on',
										'compare'	=> '='
										)
									)	
						  );
						  
		$packages = new WP_Query($args_pack);
		$currency = 'USD';
		if (!empty($zoner_config['paid-currency']))
			$currency = esc_attr($zoner_config['paid-currency']);
		$currency_symbol = $zoner->currency->get_zoner_currency_symbol($currency);
		$unlimited  = __('Unlimited', 'zoner');
		$user_package_info = $zoner->membership->zoner_get_package_info_by_user();
		$is_available_free = esc_attr($zoner_config['free-available']); 
		
		$is_packages_available  = false;
		
		
		/*Free package on packages table*/
		// if ($is_available_free) {
				
			// $free_package_name    = esc_attr($zoner_config['free-package-name']);
			// $limit_properties     = esc_attr((int)  $zoner_config['free-limit-properties']);
			// $limit_featured       = esc_attr((int)  $zoner_config['free-limit-featured']);
			// $is_unlim_properties  = esc_attr((bool) $zoner_config['free-unlimited-properties']);
			// $is_unlim_featured    = esc_attr((bool) $zoner_config['free-unlimited-featured']);
			// $is_agency_profile	  = esc_attr((bool) $zoner_config['free-add-agency']);
			
			
			// $titles .= '<th class="title">'.$free_package_name.'</th>';
			// $prices .= '<td>'.sprintf( $zoner->currency->zoner_price_format(), $currency_symbol, 0 ).'</td>';
			
			
			// if ($is_unlim_properties) {
				// $submit_limit 	.= '<td>'.$unlimited.'</td>';	
			// } else {
				// $submit_limit 	.= '<td>'.$limit_properties.'</td>';
			// }			
			
			// if ($is_unlim_featured) {
				// $featured_limit .= '<td>'.$unlimited.'</td>';
			// } else {
				// $featured_limit .= '<td>'.$limit_featured.'</td>';
			// }			
			
			// if ($is_agency_profile) {
				// $agency_profile .= '<td class="available"><i class="fa fa-check"></i></td>';
			// } else {
				// $agency_profile .= '<td class="not-available"><i class="fa fa-times"></i></td>';
			// }
			
			// $agent_profile .= '<td class="available"><i class="fa fa-check"></i></td>';
			// $time_periods  .= '<td class="not-available"><i class="fa fa-times"></i></td>';
			

			// $btn_select_class = array();
			// $btn_select_class[] = 'select-package';
			// if ($user_package_info->user_curr_package_id == -1)
			// $btn_select_class[] = 'package-selected';
			
			// $package_selected = '<td data-packagename="'.$free_package_name.'" data-packageid="-1" class="'.implode(' ', $btn_select_class).'">';
				// $package_selected .= '<button type="button" class="btn btn-default small">'.__('Select','zoner').'</button>';
			// $package_selected .= '</td>';
				
		// }	
		
		if ($packages->have_posts()) {
			$is_packages_available = true;

			while ( $packages->have_posts() ) {
				$packages->the_post();
				
				$package_info = array();
				$package_info = $zoner->membership->zoner_get_package_info_by_id(get_the_ID());
				$titles .= '<th class="title">'.$package_info->title.'</th>';
				$prices .= '<td>'.$zoner->currency->get_zoner_property_price($package_info->price, $currency, 0, null, null, false).'</td>';
				
				if ($package_info->is_unlim_properties == 'off') {
					$submit_limit 	.= '<td>'.$package_info->limit_properties.'</td>';
				} else {
					$submit_limit 	.= '<td>'.$unlimited.'</td>';
				}			
			
				if ($package_info->is_unlim_featured == 'off') {
					$featured_limit .= '<td>'.$package_info->limit_featured.'</td>';
				} else {
					$featured_limit .= '<td>'.$unlimited.'</td>';
				}			
			
				if ($package_info->is_create_agency == 'on') {
					$agency_profile .= '<td class="available"><i class="fa fa-check"></i></td>';
				} else {
					$agency_profile .= '<td class="not-available"><i class="fa fa-times"></i></td>';
				}
		
				$agent_profile .=  '<td class="available"><i class="fa fa-check"></i></td>';
				$time_periods  .=  '<td><strong>'.$package_info->freq . ' ' . $package_info->billing_period_name.'</strong></td>';
				
				
				$btn_select_class = array();
				$btn_select_class[] = 'select-package';
				if ($user_package_info->user_curr_package_id == $package_info->id)
				$btn_select_class[] = 'package-selected';
		
				$package_selected .= '<td data-packagename="'.$package_info->title.'" data-packageid="'.$package_info->id.'" class="'.implode(' ', $btn_select_class).'">';	
					$package_selected .= '<button type="button" class="btn btn-default small">'.__('Select','zoner').'</button>';
				$package_selected .= '</td>';
					
			}	
		}	
		wp_reset_query();
		wp_reset_postdata();
		
		$zoner->membership->zoner_get_package_info_by_user();
		
		?>
			<div class="col-md-9 col-sm-10">
				<section id="my-package">
					<header><h1><?php _e('My Package', 'zoner');?></h1></header>
					<?php 
						/*User current inforamtion panel*/
						$zoner->membership->zoner_get_user_info_panel(); 
					?>
					
					<?php 
						/*Available package*/
						if ($is_packages_available) {
					?>
					<div class="table-responsive submit-pricing packages">
						<table class="table">
							<thead>
								<tr>
									<th><?php _e('Your Package', 'zoner'); ?>:</th>
									<?php  if (!empty($titles)) echo $titles; ?>
								</tr>
							</thead>
							<tbody>
								<tr class="prices">
									<td></td>
									<?php if (!empty($prices)) echo $prices; ?>
								</tr>
								
								<tr>
									<td><?php _e('Time period', 'zoner'); ?></td>
									<?php if (!empty($time_periods)) echo $time_periods; ?>
								</tr>
								
								<tr>
									<td><?php _e('Property Submit Limit', 'zoner'); ?></td>
									<?php if(!empty($submit_limit)) echo $submit_limit; ?>
									
								</tr>
								<tr>
									<td><?php _e('Agent Profiles', 'zoner'); ?></td>
									<?php if (!empty($agent_profile)) echo $agent_profile; ?>
								</tr>
								
								<tr>
									<td><?php _e('Agency Profiles', 'zoner'); ?></td>
									<?php if (!empty($agency_profile)) echo $agency_profile; ?>
								</tr>
								<tr>
									<td><?php _e('Featured Properties', 'zoner'); ?></td>
									<?php if (!empty($featured_limit)) echo $featured_limit; ?>
								</tr>
								<tr class="buttons">
									<td></td>					
									<?php echo $package_selected; ?>
								</tr>
							</tbody>
						</table>
					</div><!-- /.submit-pricing -->
					
					
					<?php 
						if ((isset($zoner_config['bacs-system']) && ($zoner_config['bacs-system'] == 1)) ||
							(isset($zoner_config['membership-paypal']) && ($zoner_config['membership-paypal'] == 1)) || 
							(isset($zoner_config['membership-stripe']) && ($zoner_config['membership-stripe'] == 1))) {
					?>			
					
					<div class="pull-right">
						<div class="checkbox text-right">
							<label for="recurring_payments">
								<input type="checkbox" id="recurring_payments" name="recurring_payments" value="1"><?php _e('Recurring payment', 'zoner'); ?>
							</label>
						</div>
						<?php if (isset($zoner_config['bacs-system']) && ($zoner_config['bacs-system'] == 1)) { ?>
							<button type="button" id="payment-bacs-pack" class="btn btn-default small bacs"><?php _e('Pay with BACS','zoner'); ?></button>
						<?php } ?>

						<?php if (isset($zoner_config['membership-paypal']) && ($zoner_config['membership-paypal'] == 1)) { ?>
							<button type="button" id="payment-paypal-pack" class="btn btn-default small paypal"><?php _e('Pay with PayPal','zoner'); ?></button>
						<?php } ?>
						
						<?php if (isset($zoner_config['membership-stripe']) && ($zoner_config['membership-stripe'] == 1)) { ?>
							<button type="button" id="payment-stripe-pack" class="btn btn-default small stripe"><?php _e('Pay with Stripe','zoner'); ?></button>
						<?php } ?>
					</div>
					<?php 
						}
					?>
					
					<?php 
						/*Available package*/
						} 
					?>
				</section>
			</div>	
		<?php 
	}
}

if ( ! function_exists( 'zoner_generate_profile_my_properties' ) ) {				
	function zoner_generate_profile_my_properties() {
		global $zoner_config, $prefix, $wp_query, $zoner;	
		$userID = zoner_get_user_id();
			
		$is_paid_per_property = (!empty($zoner_config['paid-type-properties']) && ($zoner_config['paid-type-properties'] == 1));
		$is_paid_system = (!empty($zoner_config['paid-system']) && ($zoner_config['paid-system'] == 1));
		$paid_currency = esc_attr($zoner_config['paid-currency']);
		
		$bacs_btn_title = __('Pay with BACS','zoner');
		$paypal_btn_title = __('Pay with PayPal','zoner');
		$stripe_btn_title = __('Pay with Stripe','zoner');
		
		$price_per_property 		 = esc_attr((int) $zoner_config['price-per-property']);
		$price_per_featured_property = esc_attr((int) $zoner_config['price-per-featured-property']);
		
		$property_price = $zoner->currency->get_zoner_property_price($price_per_property, $paid_currency, 0, null, null, false);
		$featured_price = $zoner->currency->get_zoner_property_price($price_per_featured_property, $paid_currency, 0, null, null, false);
		
	?>
		<div class="col-md-9 col-sm-10">
			<section id="my-properties">
			<header><h1><?php _e('My Properties', 'zoner');?></h1></header>
				<?php if( have_posts() ) { ?>
									
					<section id="properties" class="properties display-lines profile">
						<div class="grid">
							<?php  while (have_posts()) : the_post(); ?>
						
							<?php 
									$gproperty = $prop_type_arr = array();
									$views = 0;
									$id_ = get_the_ID();
										
									$post_status = get_post_status($id_);
										
									$gproperty  	= $zoner->property->get_property($id_);
									$price 			= $gproperty->price;
									$address 		= $gproperty->address;
									$full_address 	= $gproperty->full_address;
									$city			= $gproperty->city;
									$zip			= $gproperty->zip;
									$views      	= $gproperty->views;
									if  (empty($views)) $views = 0;
									
									$currency			= $gproperty->currency;
									$price_html			= $gproperty->price_html;
									$prop_types  		= $gproperty->property_types;
									$prop_status 		= $gproperty->property_status;
									$payment_rent   	= $gproperty->payment_rent;			
									$payment_rent_name 	= $gproperty->payment_rent_name;
									$is_featured 		= $gproperty->is_featured;
									$is_paid 			= $gproperty->is_paid;
									
									$prop_type_html = $prop_status_html = array();
		
									if (!empty($prop_types)) {
										foreach ($prop_types as $prop_type)  {
											$attachment_id = $zoner->zoner_tax->get_zoner_term_meta($prop_type->term_id, 'thumbnail_id');
											$img_tax = wp_get_attachment_image_src($attachment_id, 'full');
											if (!empty($img_tax)) {
												$prop_type_html[]  = array('name' => $prop_type->name, 'icon' => '<img width="26px" height="26px" src="'.$img_tax[0].'" alt="" />');
											} else {
												$prop_type_html[]  = array('name' => $prop_type->name, 'icon' => '<img width="26px" height="26px" src="'. get_template_directory_uri() . '/includes/theme/assets/img/empty.png' .'" alt="" />');
											}		
				
										}
									}		
		
									if (!empty($prop_status)) {
										foreach ($prop_status as $status)  {
											$prop_status_html[] = $status->name;
										}
									}
		
									if ($post_status == 'zoner-pending') {
										$approved_status = '<span class="complete no">'. __('Pending', 'zoner') . '</span>';	
									} elseif ($post_status == 'zoner-expired') {
										$approved_status = '<span class="complete no">'. __('Expired', 'zoner') . '</span>';	
									} elseif ($post_status == 'publish') {
										$approved_status = '<span class="complete yes">'.__('Published', 'zoner') . '</span>';
									} else {
										$approved_status = '<span class="complete no">'. $post_status . '</span>';
									}
									
									$featured_status = '<span class="featured-status complete no">' . __('Not Featured', 'zoner') . '</span>';
									$paid			 = '<span class="paid-status complete no">' . __('Not Paid', 'zoner') . '</span>';
									
									if ($is_paid == 'on' ) {
										$paid = '<span class="complete yes">' . __('Paid', 'zoner') .'</span>';	
									}	
									
									if ( $is_paid_per_property ) {
										if ($is_paid == 'on' ) {
											if ($is_featured == 'off') {
												$bacs_btn_title = sprintf(__('Update to featured with BACS %1s', 'zoner'), $featured_price);
												$paypal_btn_title = sprintf(__('Update to featured with PayPal %1s', 'zoner'), $featured_price);
												$stripe_btn_title = sprintf(__('Update to featured with Stripe %1s', 'zoner'), $featured_price);
											} else {
												$featured_status = '<span class="featured-status complete yes">' . __('Featured', 'zoner') . '</span>';
											}
										}
									} else {
										if ($is_featured === 'on') {
											$featured_status = '<span class="featured-status complete yes">' . __('Featured', 'zoner') . '</span>';
										}
									}
									
									
							?>	
									
							<div id="property-<?php echo esc_attr($id_); ?>" class="property">
								<?php if (!empty($prop_status_html)) { ?>
									<figure class="tag status"><?php echo implode(', ', $prop_status_html); ?></figure>
								<?php } ?>	
								
								<?php 
									if (!empty($prop_type_html)) { 
										foreach ($prop_type_html as $type) { 
											if (!empty($type) && isset($type['icon']) && isset($type['name'])) {
								?>
											<figure class="type" title="<?php echo esc_attr($type['name']); ?>"><?php echo $type['icon']; ?></figure>
									<?php 
											}
										} 
									} 
								?>
				
								<div class="property-image">
									<?php echo zoner_get_property_condition(); ?>
									<a href="<?php the_permalink(); ?>">
										<?php if (has_post_thumbnail()) { ?>
										<?php 
												$attachment_id 	  = get_post_thumbnail_id( $id_ );
												$image_attributes = wp_get_attachment_image_src( $attachment_id, 'zoner-footer-thumbnails');
										?>
												<img src="<?php echo $image_attributes[0]; ?>" alt="" />
										<?php } else { ?>	
												<img width="100%" data-src="holder.js/440x195?text=<?php _e('Property', 'zoner'); ?>" alt="" />
										<?php } ?>
									</a>
								</div>	
									
								<div class="info">
									<header>
										<a href="<?php the_permalink(); ?>"><h3><?php the_title(''); ?></h3></a>
										<figure><?php echo $full_address; ?></figure>
									</header>
									<?php echo $price_html; ?>
									<aside>
										<dl>
											<dt><?php _e('Date added', 'zoner'); ?>:</dt>
											<dd><?php echo get_the_date(); ?></dd>
											
											<dt><?php _e('Access status', 'zoner'); ?>:</dt>
											<dd><?php echo $approved_status; ?></dd>
											
											<dt><?php _e('Featured', 'zoner'); ?>:</dt>
											<dd><?php echo $featured_status; ?></dd>
											
											<dt><?php _e('Payment', 'zoner'); ?>:</dt>
											<dd><?php echo $paid; ?></dd>
											
											<dt><?php _e('Views', 'zoner'); ?>:</dt>
											<dd><?php echo $views; ?></dd>
										</dl>
										
										
										<?php if ($is_paid_per_property && ($is_paid == 'off')) { ?>
											<div class="price-info">
												<div class="price-text text-right">
													<?php _e('Submission fee', 'zoner'); ?>: <?php echo $property_price; ?>
												</div>
												
												<div class="checkbox text-right">
													<label for="is_featured_property_submit">
														<input type="checkbox" class="is_featured_submit" name="is_featured_submit" value="1">
														<?php _e('Featured fee', 'zoner'); ?>: <?php echo $featured_price; ?>
													</label>
												</div>
												
												<div class="price-text text-right">
													<?php _e('Total price', 'zoner'); ?>: <span class="total-price"><?php echo $property_price; ?></span>
												</div>	
											</div>	
										<?php } ?>
									</aside>	
									
									<div class="actions">
										<?php
										if ($post_status !== 'publish' && !(current_user_can('administrator') || current_user_can('editor')))  {
											?>
											<span title="<?php _e('Edit Property', 'zoner');  ?>" class="action-disabled"><i class="fa fa-pencil"></i></span>
											<?php
										} else {
											?>
											<a title="<?php _e('Edit Property', 'zoner');  ?>" 	href="<?php echo add_query_arg( array('edit-property' => $id_), get_permalink($id_)); ?>" class="edit"><i class="fa fa-pencil"></i></a>
											<?php
										}
										?>
										
										<a title="<?php _e('Delete Property', 'zoner'); ?>" href="#" data-toggle="modal" class="delete-property" data-propertyid="<?php echo $id_; ?>"><i class="delete fa fa-trash-o"></i></a>
										
										<?php 
										
											/*Update to featured is not paid per property*/
											if (!$is_paid_per_property && $is_paid_system) { 
												if ($is_featured != 'off') { 
													echo '<a title="'.__('Remove featured status', 'zoner').'" href="#" data-toggle="modal" class="featured-property is-featured" data-propertyid="'.$id_.'"><i class="fa fa-star"></i></a>';
												} else { 
													echo '<a title="'.__('Add featured status', 'zoner').'" href="#" data-toggle="modal" class="featured-property" data-propertyid="'.$id_.'"><i class="fa fa-star-o"></i></a>';
												}
											} 
											
											/*Buy*/
											if ($is_paid_per_property && ($is_paid == 'off' || $is_featured == 'off')) { 
												
												$BACSBtnClass = array();
												$PayPalBtnClass = array();
												$StripeBtnClass = array();
												
												$BACSBtnClass[] = 'btn';
												$BACSBtnClass[] = 'btn-default';
												$BACSBtnClass[] = 'small';
												$BACSBtnClass[] = 'pay-bacs';

												$PayPalBtnClass[] = 'btn';
												$PayPalBtnClass[] = 'btn-default';
												$PayPalBtnClass[] = 'small';
												$PayPalBtnClass[] = 'pay-paypal';
												
												$StripeBtnClass[] = 'btn';
												$StripeBtnClass[] = 'btn-default';
												$StripeBtnClass[] = 'small';
												$StripeBtnClass[] = 'pay-stripe';
												
												if ($is_paid == 'on' && $is_featured == 'off') {
													/*PayPal & Stripe & BACS*/
													$BACSBtnClass[] = 'is-upgrade';
													$PayPalBtnClass[] = 'is-upgrade';
													$StripeBtnClass[] = 'is-upgrade';
												}
												
												echo '<div class="pull-right">';
													if (!empty($zoner_config['bacs-system']) && ($zoner_config['bacs-system'] == 1))
														echo '<button type="button" data-propertyid="'.$id_.'" class="'.implode(' ' , $BACSBtnClass).'">'.$bacs_btn_title.'</button>';
													

													if (!empty($zoner_config['membership-paypal']) && ($zoner_config['membership-paypal'] == 1))
														echo '<button type="button" data-propertyid="'.$id_.'" class="'.implode(' ' , $PayPalBtnClass).'">'.$paypal_btn_title.'</button>';
													
													if (!empty($zoner_config['membership-stripe']) && ($zoner_config['membership-stripe'] == 1))
														echo $zoner->membership->zoner_get_stripe_btn_per_property($id_, implode(' ' , $StripeBtnClass), $stripe_btn_title);
												echo '</div>';
										
											} 
										 ?>
										<?php zoner_get_delete_property_wnd(); ?>
									</div>
								</div>
							</div>	
							
							<?php endwhile; ?>				
							
						</div>
					</section>
									
					<?php zoner_paging_nav(); ?>		
				</div><!-- my-properties -->
								
				<?php } else { ?>
					<div class="alert alert-info">
						<a href="#" class="close" data-dismiss="alert">&times;</a>
						<?php _e('You have not added properties.', 'zoner'); ?>
					</div>
				<?php } ?>
			</section>	
		</div> <!-- close grid div -->	
    <?php 
	
	}
}	

if ( ! function_exists( 'zoner_generate_profile_my_bookmarks' ) ) {				
	function zoner_generate_profile_my_bookmarks() {
		global $zoner_config, $prefix, $wp_query, $zoner;
			   $all_bookmarks = $zoner->bookmark->zoner_get_all_bookmark_by_user();
			   $prop_post_in  = array();
			   $grid_type 	  = 1;
			   
		foreach ($all_bookmarks as $prop) {
			$prop_post_in[] = $prop->property_id;
		}
		
		$args = array();
		$args = array(
				'post_type' 	=> 'property',
				'post_status' 	=> 'publish',
				'post__in'		=> $prop_post_in,
				'order by'		=> 'DATE',
				'order'			=> 'DESC',
				'posts_per_page' => -1
		);

		$bookmark = new WP_Query($args);

		if ( $bookmark->have_posts() && !empty($prop_post_in)) {
		
		?>
			<div class="col-md-9 col-sm-10">
				<section id="bookmarked-properties" class="properties masonry">
					<header><h1><?php _e('Bookmarked Properties', 'zoner'); ?></h1></header>
					<div class="grid">
						
		<?php 
			while ( $bookmark->have_posts() ) {
					$bookmark->the_post();
					zoner_get_property_grid_items_masonry();
			}
		?>	
					</div>
				</section>	
			</div>	
					
		<?php 	
		} else {
		
		?>
			<div class="col-md-9 col-sm-10">
				<section id="bookmarked-properties">
					<header><h1><?php _e('Bookmarked Properties', 'zoner'); ?></h1></header>
					<div class="alert alert-info">
						<a href="#" class="close" data-dismiss="alert">&times;</a>
						<?php _e('You have not added bookmarked property.', 'zoner'); ?>
					</div>
				</section>	
			</div>	
		<?php 
		
		}

		wp_reset_postdata();
	}
}	

if ( ! function_exists( 'zoner_generate_profile_my_agencies' ) ) {				
	function zoner_generate_profile_my_agencies() {
		global $zoner_config, $prefix, $wp_query, $zoner;
		$userID = zoner_get_user_id();
		
		$args = array();
		$args = array(
				'post_type' 	=> 'agency',
				'post_status' 	=> 'publish',
				'order by'		=> 'DATE',
				'order'			=> 'DESC',
				'posts_per_page' => -1,
				'author' 		=> $userID,
		);
		$array_of_agnecies = array();
		
		$agency = new WP_Query($args);
		if ( $agency->have_posts()) {
			while ( $agency->have_posts() ) {
				$agency->the_post();
				$array_of_agnecies[] = array('admin' => 1, 'ID' => get_the_ID());
			}
		}
		
		wp_reset_postdata();
		
		
		$all_agencies = $zoner->invites->zoner_get_all_agencies_from_agent($userID);
		if (!empty($all_agencies)) {
			foreach($all_agencies as $agency) {
				$array_of_agnecies[] = array('admin' => 0, 'ID' => $agency->agency_id);
			}
		}
		
		
		
		if ( !empty($array_of_agnecies) ) {
		
		?>
			<div class="col-md-9 col-sm-10">
				<section id="agencies-listing">
					<header><h1><?php _e('Your Agencies Listing', 'zoner'); ?></h1></header>
					<div id="my-agencies" class="my-agencies grid-data-table">
						<div class="table-responsive">
							<table class="table">
								<thead>
									<tr>
										<th colspan="2"><?php _e('Agency', 'zoner'); ?></th>
										<th><?php _e('Date Added', 'zoner'); ?></th>
										<th><?php _e('Actions', 'zoner'); ?></th>
									</tr>
								</thead>
								<tbody>	
									<?php 
										foreach ($array_of_agnecies as $value) {
											$id_ = $value['ID'];
											$address = get_post_meta($id_, $prefix . 'agency_address', true);
									?>

									<tr>
										<td class="image">
											<a href="<?php echo get_permalink($id_); ?>">
												<?php if (has_post_thumbnail($id_)) { ?>
													<?php 
														$attachment_id 	  = get_post_thumbnail_id( $id_ );
														$image_attributes = wp_get_attachment_image_src( $attachment_id, 'zoner-footer-thumbnails');
													?>
													<img src="<?php echo $image_attributes[0]; ?>" alt="" />
												<?php } else { ?>	
													<img width="100%" class="img-responsive" data-src="holder.js/200x200?text=<?php _e('Agency', 'zoner'); ?>" alt="" />
												<?php } ?>
											</a>
										</td>
										<td>
											<div class="inner">
												<a href="<?php echo get_permalink($id_); ?>"><h2><?php echo get_the_title($id_); ?></h2></a>
												<figure>
													<?php echo nl2br($address); ?>
												</figure>
											</div>
										</td>
										
										<td><?php echo get_the_date('', $id_); ?></td>
										
										<?php if (!empty($value['admin']) && ($value['admin'] > 0) ) { ?>
											<td class="actions">
												<a href="<?php echo add_query_arg( array('edit-agency' => $id_), get_permalink($id_)); ?>" class="edit"><i class="fa fa-pencil"></i><?php _e('Edit', 'zoner'); ?></a>
												<a href="#" data-toggle="modal" class="delete-agency" data-agencyid="<?php echo $id_; ?>"><i class="delete fa fa-trash-o"></i></a>
												<?php zoner_get_delete_agency_wnd(); ?>
										<?php } else { ?>
											<td class="actions invite">
												<span class="left"><strong><?php _e('Invited', 'zoner'); ?></strong></span>
										<?php } ?>
										</td>
									</tr>
									
								<?php			
								
									}
								?>						
		
								</tbody>
							</table>
						</div>	
					</div>
				</section>	
			</div>	
		<?php 	
		} else {
		
		?>
			<div class="col-md-9 col-sm-10">
				<section id="agencies-listing">
					<header><h1><?php _e('Your Agencies Listing', 'zoner'); ?></h1></header>
					<div class="grid">
						<div class="alert alert-info">
							<a href="#" class="close" data-dismiss="alert">&times;</a>
							<?php _e('You have not added agencies.', 'zoner'); ?>
						</div>
					</div>	
				</section>	
			</div>	
		<?php 
		
		}

		wp_reset_postdata();
	}
}	

if ( ! function_exists( 'zoner_generate_profile_my_invites' ) ) {				
	function zoner_generate_profile_my_invites() {
	global $zoner_config, $prefix, $wp_query, $zoner;
		$userID = zoner_get_user_id();
		$count_agencies = 0;
		
		$count_agencies = $zoner->invites->zoner_get_count_agencies_from_agent ($userID);
		$agency_id 		= $zoner->invites->zoner_get_current_agency_id_by_agent($userID);
		$all_agents  =    $zoner->invites->zoner_get_all_agents_from_agency($agency_id, -1);
		
		if (count($all_agents) > 0) {
		
		?>
			<div class="col-md-9 col-sm-10">
				<section id="agents-listing">
					<header><h1><?php _e('Your Invites Listing', 'zoner'); ?></h1></header>
					
					<div class="agents-listing grid-data-table">
						<div class="table-responsive">
							<table class="table">
								<thead>
									<tr>
										<th><?php _e('User Name', 'zoner'); ?></th>
										<th><?php _e('Email', 'zoner'); ?></th>
										<th><?php _e('Status', 'zoner'); ?></th>
										<th><?php _e('Date', 'zoner'); ?></th>
										<th><?php _e('Actions', 'zoner'); ?></th>
									</tr>
								</thead>
								<tbody>	
									
									<?php 
										foreach($all_agents as $agent) {
										
										$user_info = array();
										$user_info = get_userdata($agent->user_id);
										$status = $zoner->invites->zoner_get_agent_status((int) $agent->status);
										$invite_date  = $agent->invite_date;
										
										if (!empty($user_info)) {
											$email = $user_info->user_email;
											$display_name = zoner_get_user_name($user_info);
										} else {
											$email = $agent->user_email;
											$display_name = $agent->user_temporary_name;
										}
									?>
									
									<tr>
										<td><?php echo $display_name; ?></td>
										<td><?php echo $email; ?></td>
										<td><?php echo $status; ?></td>
										<td><?php echo $invite_date; ?></td>
										
										<td class="actions">
											<a href="#" class="delete-invite-agent" data-inviteid="<?php echo $agent->invite_id; ?>"><i class="delete fa fa-trash-o"></i></a>
											<?php zoner_get_invite_agent_wnd(); ?>
										</td>
									</tr>	
									<?php 
										}
									?>
		
								</tbody>
							</table>
						</div>	
					</div>
				</section>	
				
				<?php if ($count_agencies > 0 ) zoner_get_form_invites($agency_id); ?>
			</div>	
		<?php 	
		
		} else {
		
		?>
			<div class="col-md-9 col-sm-10">
				<section id="agencies-listing">
					<header><h1><?php _e('Your Invites Listing', 'zoner'); ?></h1></header>
					<div class="grid">
						<div class="alert alert-info">
							<a href="#" class="close" data-dismiss="alert">&times;</a>
							
							<?php 
								if ($count_agencies == 0) {
									_e('Please create agency before will add agents.', 'zoner'); 
								} else {
									_e('You have not added agents to your agency.', 'zoner'); 
								}								
								
							?>
						</div>
					</div>	
				</section>	
				
				<?php if ($count_agencies > 0 ) zoner_get_form_invites($agency_id); ?>
			</div>	
		<?php 
		
		}
		
		wp_reset_postdata();
	}
}

if ( ! function_exists( 'zoner_get_form_invites' ) ) {				
	function zoner_get_form_invites($agency_id = -1) {
	?>	
		<section id="zoner-invites" class="block">
			<header><h1><?php _e('Add agent to your agency', 'zoner'); ?></h1></header>
			<form role="form" id="form-invites" class="form-invites" method="POST" action="">
				<?php wp_nonce_field( 'zoner_send_agent_invite', 'send_agent_invite', false, true ); ?>
				<input type="hidden" name="finv-agencyid" value="<?php echo $agency_id; ?>" />
				
				<div class="row">
					<div class="col-md-12">
						<div class="form-group">
							<label for="finv-agency"><?php _e('Current agency', 'zoner');?></label>
							<input type="text" class="form-control" id="finv-agency" name="finv-agentname" value="<?php echo get_the_title($agency_id); ?>" disabled="disabled">
						</div>
					</div>
					
					<div class="col-md-6">
						<div class="form-group">
							<label for="finv-agentname"><?php _e('Agent Name', 'zoner');?> <em>*</em></label>
							<input type="text" class="form-control" id="finv-agentname" name="finv-agentname" required>
						 </div><!-- /.form-group -->
					 </div><!-- /.col-md-6 -->
					<div class="col-md-6">
						<div class="form-group">
							<label for="finv-agentemail"><?php _e('Agent Email', 'zoner'); ?> <em>*</em></label>
							<input type="email" class="form-control" id="finv-agentemail" name="finv-agentemail" required>
						</div><!-- /.form-group -->
					</div><!-- /.col-md-6 -->
					
					<div class="col-md-12">
						<div class="form-group">
							<input type="submit" class="btn pull-right btn-default" id="finv-invite" value="<?php _e('Send invite', 'zoner'); ?>">
						</div>	
					</div><!-- /.form-group -->
				</div><!-- /.row -->
			</form>
		</section>
		
	<?php 	
	}
}

if ( ! function_exists( 'zoner_generate_profile_my_messages' ) ) {				
	function zoner_generate_profile_my_messages() {
		global $zoner_config, $prefix, $wp_query, $zoner;
		
		$currUserID    = get_current_user_id();
		$all_convs = $zoner->conversation->zc_get_conversations();
		
		?>
		<div class="col-md-9 col-sm-10">
			<section id="zoner-messages" class="block">
				<header><h1><?php _e('My Message Listing', 'zoner'); ?></h1></header>
				<div class="list-messages-wrapper">
				
					<?php  if ($all_convs != -1) { ?>
						<?php zoner_delete_conversation_wnd(); ?>
						<ul id="list-messages" class="list-messages list-unstyled">
							<?php foreach ($all_convs as $conv ) { ?>
							<?php 
								
								$userID = null;
								
								if ($currUserID == $conv->recipient_id) {
									$userID = $conv->sender_id;	
								} else {
									$userID = $conv->recipient_id;	
								}
								
								$user_meta = get_user_meta( $userID );
								$user_data = get_userdata ( $userID ); 
								
		
								if (!empty($user_meta[$prefix.'avatar'])) {
									$avatar_url = $user_meta[$prefix.'avatar'][0];
								} else {
									$avatar_url = get_template_directory_uri() . '/includes/theme/profile/res/avatar.jpg';	
								}
								
								$display_name = null;
								$display_name = zoner_get_user_name ($user_data);
								$author_url   = get_author_posts_url($conv->recipient_id);
							
								$last_msg  = $zoner->conversation->zc_get_last_message($conv->conversation_id);
							
							?>
								<li id="message-<?php echo $conv->conversation_id; ?>" class="message" data-convid="<?php echo $conv->conversation_id; ?>">
									<div class="avatar-box col-md-1 col-sm-1 col-xs-2"><img src="<?php echo esc_url($avatar_url); ?>" alt="" /></div>
									<div class="author-bio col-md-3 col-sm-4 col-xs-10"><a target="_blank" href="<?php echo $author_url;?>"><?php echo $display_name; ?></a></div>
									<?php if (!empty($last_msg)) { ?>
										<div class="excerpt-message col-md-6 col-sm-6 col-xs-9"><?php echo $last_msg; ?></div>
									<?php } ?>	
									<div class="col-md-1 col-sm-1 col-xs-1"><div class="roll-spin"><i class="fa fa-cog"></i></div></div>
									<div class="col-md-1 col-sm-1 col-xs-2"><a href="#" class="del-conv"><i class="fa fa-trash"></i></a></div>
								</li>
							<?php } ?>
						</ul>
						
						<div class="chat-message"></div>
						
					<?php  } else { ?>
						<div class="alert alert-info">
							<a href="#" class="close" data-dismiss="alert">&times;</a>
							<?php _e('You have not conversations.', 'zoner'); ?>
						</div>
					<?php } ?>
				</div>
			</section>	
		</div>	
		
		<?php	
	}
}

//For redirect without error
function app_output_buffer() {
	ob_start();
} // soi_output_buffer
add_action('init', 'app_output_buffer');
if ( ! function_exists( 'zoner_delete_acccount' ) ) {
    function zoner_delete_acccount()
    {
        global $zoner_config, $prefix, $wp_query, $zoner;
        $user = get_user_by( 'id', get_current_user_id() );
        //save all data to admin account
        $admins = get_users( array( 'role' => 'administrator' ));
        if (!empty( $_POST['password_del']) && $user && wp_check_password( $_POST['password_del'], $user->data->user_pass, $user->ID)){
            require_once(ABSPATH.'wp-admin/includes/user.php' );
            wp_delete_user($user->ID, $admins[0]->ID);
            wp_redirect( site_url() );
            exit;
        }else{
            //do nothing yet
        }

        ?>
           <div class="col-md-9 col-sm-10">
               <header><h1><?php _e('Delete account', 'zoner'); ?></h1></header>
              <form role="form" method="post" action="">
                  <div class="form-group">

                      <div class="alert alert-danger" role="alert">
                          <?php _e('Enter your password to confirm delete account', 'zoner'); ?>
                       </div>
                      <div class="col-md-4 col-sm-5" style="padding: 0;">
                          <input name="password_del" type="password"/>
                      </div>
                      <div class="col-md-5 col-sm-5">
                          <button type="submit" class="btn btn-default">
                              <?php _e('Delete', 'zoner'); ?>
                          </button>
                      </div>
                   </div>
              </form>
           </div>
<?php
    }
}

/*Deleted user additional inforamtion */
if ( ! function_exists( 'zoner_deleted_user_action' ) ) {				
	function zoner_deleted_user_action($user_id = null) {
		global $zoner, $zoner_config, $prefix;
		
		/*Delete user from invited agecnies*/
		$zoner->zoner_delete_from_table('zoner_agent_from_agencies', array('user_id' => $user_id), array('%d')); 
	}
}


/*If user exist and inveted to agency*/
if ( ! function_exists( 'zoner_exist_user_invited' ) ) {				
	function zoner_exist_user_invited() {
		global $zoner, $zoner_config, $prefix, $wpdb;
		$invite_info = null;
		
		if (isset($_REQUEST['invitehash']) && !empty($_REQUEST['invitehash']) &&
			isset($_REQUEST['is_exist'])   && !empty($_REQUEST['is_exist'])) {
			
			$invite_hash = esc_attr($_REQUEST['invitehash']);
			$invite_info = $zoner->invites->zoner_get_invite_user_info($invite_hash);
			
			
			if (!empty($invite_info)) {
				$invite_info = current($invite_info);
				$agency_id  	  = $invite_info->agency_id;
				$invite_id 		  = $invite_info->invite_id;
				$user_id		  = $invite_info->user_id;	 
				
				$role = $zoner->zoner_get_user_role_by_id($user_id);
				
				/*Update user role if not agent*/
				if ($role != 'agent') {
					wp_update_user(array(
						'ID' 	=> $user_id,
						'role' 	=> 'agent'
					));	
				}
				
				$table_name = 'zoner_agent_from_agencies';
				$arr_field 	= array (
					'invite_hash' 	=> null,
					'user_id' 		=> $user_id,
					'status'  		=> 1
				);
						
				$arr_where = array(
					'invite_id' => $invite_id
				);
						
				$array_of_type = array('%s', '%d', '%d');
				$array_of_type_where = array ('%s');
				$zoner->zoner_update_table($table_name, $arr_field, $arr_where, $array_of_type, $array_of_type_where);
						
				/*Redirect to current agency*/		
				wp_safe_redirect(get_the_permalink($agency_id));
			}
		}
	}
}
		

if ( ! function_exists( 'zoner_form_invites_process' ) ) {				
	function zoner_form_invites_process($agency_id = -1) {
		global $zoner, $wp_query, $zoner_config, $prefix, $wpdb;
		$table_name = 'zoner_agent_from_agencies';
		
		$arr_fields = $arr_types = array();
		$time = current_time('mysql');		
		
		if ( isset($_POST['send_agent_invite']) && wp_verify_nonce($_POST['send_agent_invite'], 'zoner_send_agent_invite')) {		
			
			$agency_id 		= esc_attr($_POST['finv-agencyid']);
			$agent_name 	= esc_html($_POST['finv-agentname']);
			$agent_email 	= $_POST['finv-agentemail'];
			$hash_user 		= md5($agent_email.time());
			
			$user_id = -1;
			if( email_exists( trim($agent_email) ) ) {
				$invited_user = get_user_by( 'email', trim($agent_email) );
				$user_id = $invited_user->ID;
			}
			
			$arr_field = array(	'agency_id' 	=> $agency_id, 
								'user_email'	=> $agent_email,
								'user_temporary_name' => esc_attr($agent_name),
								'user_id'       => $user_id,
								'user_id_owner' => get_current_user_id(),
								'status' => 0,
								'invite_hash' => $hash_user,
								'invite_date' => $time 
							);
									
			$arr_types = array( '%d', '%s', '%s', '%d', '%d', '%d', '%s',  '%s');	
			$zoner->zoner_insert_row_to_table($table_name, $arr_field, $arr_types);
			
			$page_register = null;
			$page_register = $zoner->zoner_get_page_id('page-register-account');
			
			if( email_exists( trim($agent_email) ) ) {
				$invite_link = add_query_arg(array('invitehash' => $hash_user, 'is_exist' => 1), site_url(''));
			} else {
				$invite_link = add_query_arg(array('invitehash' => $hash_user), get_permalink($page_register));
			}
			
			$agencyName  = get_the_title($agency_id);
			$zoner->emails->zoner_mail_invite_agent(esc_attr($agent_name), $agent_email, $agencyName, $invite_link);
		}
		
		if (!empty($_POST) && isset($_POST['send_agent_invite'])) wp_safe_redirect( zoner_curPageURL());
	}	
}
add_action('wp', 'zoner_form_invites_process');

if ( ! function_exists( 'zoner_get_profile_page_data' ) ) {				
	function zoner_get_profile_page_data() {
		$sort = 'my_profile';
		if (!empty($_GET)) {
			foreach ( $_GET as $key => $val ) {
				if ( 'profile-page' === $key) {
					  $sort = $val;
				}
			}	
		}
		
		if ($sort == 'my_profile') 	  zoner_generate_profile_info();
		if ($sort == 'my_package')    zoner_generate_profile_my_package();
		if ($sort == 'my_properties') zoner_generate_profile_my_properties();
		if ($sort == 'my_bookmarks')  zoner_generate_profile_my_bookmarks();
		if ($sort == 'my_agencies')   zoner_generate_profile_my_agencies();
		if ($sort == 'my_invites')    zoner_generate_profile_my_invites();
		if ($sort == 'my_messages')   zoner_generate_profile_my_messages();
        if ($sort == 'delete_account')zoner_delete_acccount();
	}
}	


/*Actions*/
if ( ! function_exists( 'zoner_delete_agency_act' ) ) {
	function zoner_delete_agency_act() {
		global $wpdb, $zoner;
		check_ajax_referer( 'zoner_ajax_nonce','security');
		$is_delete = false;

		if (isset($_POST) && ($_POST['action'] == 'delete_agency_act')) {
			$agency_id = esc_attr($_POST['agencyID']);
			$is_delete = wp_delete_post( $agency_id, true );
			
			if ($is_delete) {
				$zoner->zoner_delete_from_table('zoner_agent_from_agencies', array('agency_id' => $agency_id), array('%d'));
			}
		}
		die();
	}
}	



if ( ! function_exists( 'zoner_delete_property_act' ) ) {
	function zoner_delete_property_act() {
		global $wpdb, $zoner;
		check_ajax_referer( 'zoner_ajax_nonce','security');

		if (isset($_POST) && ($_POST['action'] == 'delete_property_act')) {
			$property_id = esc_attr($_POST['property_id']);
			wp_delete_post( $property_id, true );
		}
		die();
	}
}	

if ( ! function_exists( 'zoner_check_user_password_act' ) ) {				
	function zoner_check_user_password_act() {
		$user = '';
		if (isset($_POST) && ($_POST['action'] == 'zoner_check_user_password')) {
			$user = get_user_by( 'id', get_current_user_id() );
			$pass = $_POST['form-account-password-current'];
			if ( $user && wp_check_password( $pass, $user->data->user_pass, $user->ID) )
				echo 'true';
			else
				echo 'false';
		}	
		die();	
	}
}	

if ( ! function_exists( 'delete_invite_agent_act' ) ) {				
	function delete_invite_agent_act() {
		global $wpdb, $zoner;
		check_ajax_referer( 'zoner_ajax_nonce','security');
		
		$is_delete = false;
		if (isset($_POST) && ($_POST['action'] == 'delete_invite_agent')) {
			$invite_id = esc_attr($_POST['invite_id']);
			$zoner->zoner_delete_from_table('zoner_agent_from_agencies', array('invite_id' => $invite_id), array('%d')); 		
		}
		die();
	}
}	


if ( ! function_exists( 'zoner_change_user_pass_act' ) ) {
	function zoner_change_user_pass_act() {
		$user_id = get_current_user_id();	
		if (isset($_POST) && isset($_POST['change_password']) && wp_verify_nonce($_POST['change_password'], 'zoner_change_password')) {
			$pass = '';
			if (isset($_POST['form-account-password-confirm-new']))
			  $pass = $_POST['form-account-password-confirm-new'];
		  	  wp_set_password( $pass, get_current_user_id());
			  wp_set_auth_cookie( $user_id, false, is_ssl() );
			  
		}
		
		if (!empty($_POST) && isset($_POST['change_password'])) wp_safe_redirect( zoner_curPageURL() );
	}
}	 

if ( ! function_exists( 'zoner_add_extra_user_column' ) ) {
	function zoner_add_extra_user_column($columns) { 
		return array_merge( $columns, 
						array(
								'property' 	=> __('Properties', 'zoner'), 
								'faq' 		=> __('FAQ', 'zoner'), 
								'timeline' 	=> __('Timeline', 'zoner'), 
							 ) 
					  );
	}
}	

if ( ! function_exists( 'zoner_add_extra_user_columns_values' ) ) {
	function zoner_add_extra_user_columns_values( $value, $column_name, $id ) { 
		global $wpdb;
		$r = 0;
	    $count = 0;
		if ( $column_name == 'property' ) {
			$count = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(ID) FROM $wpdb->posts WHERE  post_type IN ('property') AND post_status = 'publish' AND post_author = %d", $id ) );
		}	elseif ( $column_name == 'faq' ) {
			$count = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(ID) FROM $wpdb->posts WHERE  post_type IN ('faq') AND post_status = 'publish' AND post_author = %d", $id ) );
		} elseif ( $column_name == 'timeline' ) {
			$count = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(ID) FROM $wpdb->posts WHERE  post_type IN ('timeline') AND post_status = 'publish' AND post_author = %d", $id ) );
		}
		if ( $count > 0 ) $r = $count;
		return $r;
	}
}	