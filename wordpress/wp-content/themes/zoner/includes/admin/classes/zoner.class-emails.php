<?php
/**
 * Zoner emails send
*/
 
class zoner_emails {
	
	public function __construct() {
		global $zoner_config;
		
		add_action( 'wp_ajax_zoner_mail_form_sending', array($this, 'zoner_mail_form_sending_act') );
		add_action( 'wp_ajax_nopriv_zoner_mail_form_sending', array($this, 'zoner_mail_form_sending_act') );
		
		add_action( 'zoner_user_register', 		array($this, 'zoner_mail_to_register_act'), 10, 2);
		add_action( 'user_register', 			array($this, 'zoner_mail_to_register_admin'), 10, 1);
		add_action( 'wp_insert_post', 			array($this, 'zoner_mail_notification_add_property'), 10, 3 );
		add_action( 'transition_post_status', 	array($this, 'zoner_pending_to_publish'), 10, 3 );
		
		add_action ('zoner_create_new_conversation', array($this, 'zoner_mail_new_conversation'), 10, 1 );
		
		/*Enabled smtp phpMailer hook*/
		$is_smtp = 0;
		$is_smtp = (int) $zoner_config['emails-authentication'];
		
		if ($zoner_config['emails-smtp'] == 1)
		add_action( 'phpmailer_init', array($this, 'zoner_smtp_init_smtp'));
	}	
	
	
	function zoner_smtp_init_smtp( $phpmailer ) {              
		global $prefix, $zoner, $zoner_config;
		
		$from_email = $from_name = $enc_type_name = null;
		$enc_type   = 0;
		
		$from_email = sanitize_email($zoner_config['emails-from-email']);
		$from_name  = esc_attr($zoner_config['emails-from-name']);
		$enc_type	= (int) $zoner_config['emails-type-enc'];
		$is_auth	= (int) $zoner_config['emails-authentication'];
		
		$user_name  = $zoner_config['emails-smtp-user'];
		$user_pass  = $zoner_config['emails-smtp-pass'];
		
		$host = $zoner_config['emails-smtp-host']; 
		$port = (int) $zoner_config['emails-smtp-port']; 
		
        if (!empty($from_email) && !empty($from_name)) {
			$phpmailer->From 	 = $from_email;
			$phpmailer->FromName = $from_name;
			$phpmailer->SetFrom($phpmailer->From, $phpmailer->FromName);
		}	
		
		if ( $enc_type !== 0 ) {
			 $enc_type_name = 'ssl';
			 if ($enc_type == 3)
				 $enc_type_name = 'tls';
			 $phpmailer->SMTPSecure = $enc_type_name;
		}
		
		if (!empty($host) && !empty($port)) {
			$phpmailer->Host = $host;
			$phpmailer->Port = $port; 
		}	

		/* If we're using smtp auth, set the username & password */
		if( $is_auth == 1 ) {
			if (!empty($user_name) && !empty($user_pass)) {
				$phpmailer->SMTPAuth = true;
				$phpmailer->Username = $user_name;
				$phpmailer->Password = $this->zoner_smtp_get_password($user_pass);
			}
		}	
		
		$phpmailer->IsSMTP();
	}
	
	function zoner_smtp_get_password($in_password = null) {
		global $prefix, $zoner, $zoner_config;
		$password = $decoded_pass = null;
		
		$decoded_pass = base64_decode($in_password);
		if (base64_encode($decoded_pass) === $in_password) {  
			if(false === mb_detect_encoding($decoded_pass)) { 
				$password = $in_password;
			} else {
				$password = base64_decode($in_password); 
			}               
		} else { 
			$password = $in_password;
		}
		return $password;
	}
	

	public function zoner_get_email_template_path() {
		return get_template_directory_uri() . '/includes/admin/classes/email-templates/';
	}
	
	public function zoner_get_email_resource_template_path() {
		return get_template_directory_uri() . '/includes/admin/classes/email-templates/images/';
	}
	
	public function zoner_get_from_name () {
		global $zoner_config;
		$name = get_bloginfo('name');
		if (!empty($_POST['email_from_data_name']))	$name = $_POST['email_from_data_name'];
		elseif (!empty($zoner_config['emails-from-name'])) $name = $zoner_config['emails-from-name'];
		return $name;
	}	
	
	public function zoner_get_from_name_to_admin () {
		$name = get_bloginfo('name');
		return $name;
	}
	
	public function zoner_get_from_email () {
		global $zoner_config;
		$email = get_bloginfo('admin_email');
		if (!empty($_POST['email_from_data_email'])) $email = $_POST['email_from_data_email'];
		elseif (!empty($zoner_config['emails-from-email'])) $email = $zoner_config['emails-from-email'];
		return $email; 
	}	
	
	public function zoner_get_from_email_to_admin () {
		$email = 'noreply@' . preg_replace('#^www\.#', '', strtolower($_SERVER['SERVER_NAME']));
		return $email; 
	}	

	
	public function zoner_send_mail( $to, $subject, $message, $headers, $attachments, $to_wp_admin = false) {

		if ($to_wp_admin) {
			add_filter( 'wp_mail_from', 	 array($this, 'zoner_get_from_email_to_admin'));
			add_filter( 'wp_mail_from_name', array($this, 'zoner_get_from_name_to_admin'));
		} else {
			add_filter( 'wp_mail_from', 	 array($this, 'zoner_get_from_email'));
			add_filter( 'wp_mail_from_name', array($this, 'zoner_get_from_name'));
		}
		
		add_filter( 'wp_mail_content_type', array( $this, 'zoner_get_content_type' ) );

		$return = wp_mail( $to, $subject, $message, $headers, $attachments );

		if ($to_wp_admin) {
			remove_filter( 'wp_mail_from', array($this, 'zoner_get_from_email_to_admin'));
			remove_filter( 'wp_mail_from_name', array($this, 'zoner_get_from_name_to_admin'));
		} else {
			remove_filter( 'wp_mail_from', array($this, 'zoner_get_from_email'));
			remove_filter( 'wp_mail_from_name', array($this, 'zoner_get_from_name'));
		}
		remove_filter( 'wp_mail_content_type', array( $this, 'zoner_get_content_type' ) );

		return $return;
	}
	
	public function zoner_get_content_type() {
		return 'text/html';
	}

	private	function zoner_get_ob_file_content($name) {
		$out_html = '';
		ob_start();
		get_template_part('/includes/admin/classes/email-templates/' . $name);  
		$out_html =  ob_get_contents();
		ob_end_clean();
		
		return $out_html; 
	}
	
	
	public function zoner_mail_notification_add_property($post_id, $post, $update) {
		if ($update) return;
		
		global 	$agentEmail,  
				$agentName, 
				$propertyTitle,
				$propertyLink,
				$zoner, 
				$zoner_config; 
		
		$post_type   = get_post_type($post_id);
		$post 	     = get_post($post_id);
		
		if (($post_type == 'property') && (!empty($post))) {
			$post_author = (int)$post->post_author;
			$role 		 = $zoner->zoner_get_user_role_by_id($post_author);
			if ($role != 'agent') return;
		
			if (!empty($zoner_config['emails-from-email'])) {
				$emailTo = sanitize_email($zoner_config['emails-from-email']);
			} else {
				$emailTo = get_bloginfo('admin_email');
			}
		
			if (!empty($zoner_config['logo']['url'])) 
			$logo_url = esc_url($zoner_config['logo']['url']);

			$res_path = $this->zoner_get_email_resource_template_path();
			$attachments = $headers = array();
			$attachments = array(
				$logo_url,
				$res_path . 'email_bg.jpg'
			);	
			
			 $user_info = get_user_by( 'id', $post_author );
			 $agentName  = $user_info->data->display_name;
			 $agentEmail = $user_info->data->user_email;
			 
			 $propertyTitle = $post->post_title;
			 $propertyLink  = get_permalink($post_id);
			
			 $send_email_template = '';
			 $send_email_template = $this->zoner_get_ob_file_content('email-create-property');
			
			 $success = $this->zoner_send_mail( $emailTo, __('New property was added', 'zoner'), $send_email_template, $headers, $attachments);
		}
	}
	
	
	public function zoner_mail_form_sending_act() {
		global 	$userName, $userEmail,  $userMessage,  $agentName, $pageLink, $zoner_config; 
				$emailTo = $agentName = $logo_url = '';
					
		if (isset($_POST) && ( $_POST['action'] == 'zoner_mail_form_sending')) {
				
			$data = stripslashes_deep($_POST['formData']);	
			parse_str($data,  $search_data);
				
			$userName	 = esc_attr($search_data['mfs-name']);
			$userEmail   = $search_data['mfs-email'];
			$userMessage = wp_kses_data($search_data['mfs-message']);
			$_POST['email_from_data_name'] = $userName;
			$_POST['email_from_data_email'] = $userEmail;
			if (!empty($search_data['wsend_email']))
				$emailTo = $search_data['wsend_email'];
			if (!empty($search_data['wsend_agent']))
				$agentName = esc_attr($search_data['wsend_agent']);
			if (!empty($search_data['send_from_page']))
				$pageLink = '<a href="'.get_the_permalink(esc_attr($search_data['send_from_page'])).'">'.get_the_title(esc_attr($search_data['send_from_page'])).'</a>';
				
			$send_email_template = '';
			$send_email_template = $this->zoner_get_ob_file_content('email-contact-agent');

			if (!empty($zoner_config['logo']['url'])) 
				$logo_url = esc_url($zoner_config['logo']['url']);

			$res_path = $this->zoner_get_email_resource_template_path();
			$attachments = $headers = array();
			$attachments = array(
				$logo_url,
				$res_path . 'email_bg.jpg'
			);	
				
			$success = $this->zoner_send_mail( $emailTo, __('Client Email', 'zoner'), $send_email_template, $headers, $attachments);
			unset($_POST['email_from_data_name']);
			unset($_POST['email_from_data_email']);
			if ($success) {
				echo 1;
			} else {
				echo 0;
			}
				
		}	
		
		die();
	}
	
	public function zoner_mail_to_register_act($userID, $userPass) {
		global 	$userName, 
				$userIDout, 
				$userPassword,
				$zoner, 
				$zoner_config; 
		
		$logo_url = '';
					
		if (!empty($userID)) {
			
			$user = get_user_by( 'id', $userID );
			$role = $zoner->zoner_get_user_role_by_id($userID);
			
			$userName  = zoner_get_user_name($user);
			$userPassword  = $userPass;
			$userIDout = $userID;
			
			$send_email_template = '';
			
			
			if ($role == 'agent') {
				$send_email_template = $this->zoner_get_ob_file_content('email-agent');
			} else {
				$send_email_template = $this->zoner_get_ob_file_content('email-user');
			}			

			if (!empty($zoner_config['logo']['url'])) 
				$logo_url = esc_url($zoner_config['logo']['url']);

			$res_path = $this->zoner_get_email_resource_template_path();
			
			$email_btn_1  	= $res_path	 . 'email_btn_1.jpg';
			$email_btn_2	= $res_path	 . 'email_btn_2.jpg';
			$email_btn_3 	= $res_path	 . 'email_btn_3.jpg';
			$email_btn_4 	= $res_path	 . 'email_btn_4.jpg';
			$email_btn_5  	= $res_path	 . 'email_btn_5.jpg';
			$email_btn_6	= $res_path	 . 'email_btn_6.jpg';
			
			
			$attachments = $headers = array();
			$attachments = array(
				$logo_url,
				$email_btn_1,
				$email_btn_2,
				$email_btn_3,
				$email_btn_4,
				$email_btn_5,
				$email_btn_6,
				$res_path . 'email_bg.jpg'
			);	
				
			$success = $this->zoner_send_mail( $user->user_email, __('Thanks for signing up!', 'zoner'), $send_email_template, $headers, $attachments);
		}	
	}
	
	
	public function zoner_mail_to_register_admin($userID) {
		global $zoner_config, $zoner, $userName, $userRole, $userEmail;
		
		$user = get_user_by( 'id', $userID );
		$role = $zoner->zoner_get_user_role_by_id($userID);
		
		if (!empty($zoner_config['emails-from-email'])) {
			$emailTo = sanitize_email($zoner_config['emails-from-email']);
		} else {
			$emailTo = get_bloginfo('admin_email');
		}
		
		$userName   = zoner_get_user_name($user);
		$userEmail  = $user->user_email;
		$userRole   = $role;
			
		
		if (!empty($zoner_config['logo']['url'])) 
			$logo_url = esc_url($zoner_config['logo']['url']);

		
		$send_email_template = $this->zoner_get_ob_file_content('email-admin-user-registered');
		$res_path = $this->zoner_get_email_resource_template_path();
			
		$attachments = $headers = array();
		$attachments = array(
			$logo_url,
			$res_path . 'email_bg.jpg'
		);	
		
		
		$success = $this->zoner_send_mail( $emailTo, __('New user is registered!', 'zoner'), $send_email_template, $headers, $attachments, true);
	}
	
	public function zoner_mail_invite_agent($nameAgent = '', $emailAgent = '', $nameAgency = '', $invite_link = '') {
		global 	$userName, $agencyName, $inviteLink, $zoner, $zoner_config; 
		$logo_url = '';
			
		$userName = $nameAgent;
		$agencyName = $nameAgency;
		$inviteLink = $invite_link;
		
		$send_email_template = '';
		$send_email_template = $this->zoner_get_ob_file_content('email-invite-agent');

			if (!empty($zoner_config['logo']['url'])) 
				$logo_url = esc_url($zoner_config['logo']['url']);

			$res_path = $this->zoner_get_email_resource_template_path();
			
			$attachments = $headers = array();
			$attachments = array(
				$logo_url,
				$res_path . 'email_bg.jpg'
			);	
				
			$success = $this->zoner_send_mail( $emailAgent, __('Invite to agency', 'zoner'), $send_email_template, $headers, $attachments);
	}
	
	public function zoner_mail_reset_password($userNameIn = '', $userEmailIn = '', $tempPassIn = '') {
		global 	$userName, $tempPass, $zoner, $zoner_config; 
		$logo_url = $send_email_template = '';
			
		$userName = $userNameIn;
		$tempPass = $tempPassIn;
		$userEmail = $userEmailIn;
		
		$send_email_template = $this->zoner_get_ob_file_content('email-change-password');

		if (!empty($zoner_config['logo']['url'])) 
			$logo_url = esc_url($zoner_config['logo']['url']);

		$res_path = $this->zoner_get_email_resource_template_path();
		
		$attachments = $headers = array();
		$attachments = array(
			$logo_url,
			$res_path . 'email_bg.jpg'
		);	
			
		$success = $this->zoner_send_mail( $userEmail, __('Reset Password', 'zoner'), $send_email_template, $headers, $attachments);
	}
	
	
	public function zoner_mail_to_invoice_act($invoice_id) {
		global 	$invNum, $zoner,  $zoner_config; 
		
		$logo_url = '';
		
		if (!empty($zoner_config['emails-from-email'])) {
			$emailTo = sanitize_email($zoner_config['emails-from-email']);
		} else {
			$emailTo = get_bloginfo('admin_email');
		}
		$invNum	= $invoice_id;
		
		$send_email_template = '';
		$send_email_template = $this->zoner_get_ob_file_content('email-admin-invoice');

		if (!empty($zoner_config['logo']['url'])) 
		$logo_url = esc_url($zoner_config['logo']['url']);
		$res_path = $this->zoner_get_email_resource_template_path();
		
		
		$attachments = $headers = array();
		$attachments = array(
			$logo_url,
			$res_path . 'email_bg.jpg'
		);	
			
		$success = $this->zoner_send_mail( $emailTo, __('New invoice notification.', 'zoner'), $send_email_template, $headers, $attachments, true);

	}
	
	public function zoner_mail_to_agent_payment_package($packageName = null, $email = null, $display_name = null) {
		global 	$pkgName, $userName, $zoner, $zoner_config; 
		$logo_url = '';
		
		$emailTo = $email;
		$pkgName = $packageName;
		$userName = $display_name;
		
		$send_email_template = '';
		$send_email_template = $this->zoner_get_ob_file_content('email-agent-package-payment');

		if (!empty($zoner_config['logo']['url'])) 
		$logo_url = esc_url($zoner_config['logo']['url']);
		$res_path = $this->zoner_get_email_resource_template_path();
		
		
		$attachments = $headers = array();
		$attachments = array(
			$logo_url,
			$res_path . 'email_bg.jpg'
		);	
			
		$success = $this->zoner_send_mail( $emailTo, sprintf(__('[%1s] Membership Activated.', 'zoner'), get_bloginfo('name')), $send_email_template, $headers, $attachments, true);
	}
	
	public function zoner_mail_to_agent_nopayment_package($email = null, $display_name = null) {
		global 	$userName, $zoner, $zoner_config; 
		$logo_url = '';
		
		$emailTo  = $email;
		$userName = $display_name;
		
		$send_email_template = '';
		$send_email_template = $this->zoner_get_ob_file_content('email-agent-package-nopayment');

		if (!empty($zoner_config['logo']['url'])) 
		$logo_url = esc_url($zoner_config['logo']['url']);
		$res_path = $this->zoner_get_email_resource_template_path();
		
		
		$attachments = $headers = array();
		$attachments = array(
			$logo_url,
			$res_path . 'email_bg.jpg'
		);	
			
		$success = $this->zoner_send_mail( $emailTo, sprintf(__('[%1s] Membership deactivated.', 'zoner'), get_bloginfo('name')), $send_email_template, $headers, $attachments, true);
	}
	
	public function zoner_send_agent_change_status_property($email = null, $display_name = null, $post_id = null ) {
		global 	$zoner, $zoner_config, $agentName, $propertyName, $propertyLink; 
		
		$emailTo  = $email;
		$agentName = $display_name;
		$propertyName	= get_the_title($post_id);
		$propertyLink	= get_the_permalink($post_id);
		
		$send_email_template = '';
		$send_email_template = $this->zoner_get_ob_file_content('email-agent-property-change-status');
		
		if (!empty($zoner_config['logo']['url'])) 
		$logo_url = esc_url($zoner_config['logo']['url']);
		$res_path = $this->zoner_get_email_resource_template_path();
		
		
		$attachments = $headers = array();
		$attachments = array(
			$logo_url,
			$res_path . 'email_bg.jpg'
		);	
		
		$success = $this->zoner_send_mail( $emailTo, sprintf(__('[%1s] Property Published.', 'zoner'), get_bloginfo('name')), $send_email_template, $headers, $attachments, true);
	}
	
	public function zoner_pending_to_publish($new_status, $old_status, $post) {
		global 	$zoner, $zoner_config; 
		if ($new_status == 'publish' && $old_status == 'zoner-pending' && !empty($post)) {
			if ($post->post_type == 'property') {
				$autor = $post->post_author;
				$curr_user_id = wp_get_current_user();
				$user_role 	  = $zoner->zoner_get_user_role_by_id($curr_user_id);
				if ($user_role == 'administrator') {
					$user_data 		= get_userdata( $autor );
					
					
					$user_email 	= $user_data->user_email;
					$display_name 	= zoner_get_user_name($user_data);
					$post_id 		= $post->ID;
					
					$this->zoner_send_agent_change_status_property($user_email, $display_name, $post_id);
				}
			}
		}
	}

	public function zoner_mail_to_user_bacs_payment_data($packageName = '', $packagePrice = '', $email = null, $display_name = null) {
		global 	$pkgName, $pkgPrice, $userName, $zoner, $zoner_config; 
		$logo_url = '';
		$emailTo = $email;
		$pkgName = $packageName;
		$pkgPrice = $packagePrice;
		$userName = $display_name;
		
		$send_email_template = '';
		$send_email_template = $this->zoner_get_ob_file_content('email-user-bacs-payment');

		if (!empty($zoner_config['logo']['url'])) 
		$logo_url = esc_url($zoner_config['logo']['url']);
		$res_path = $this->zoner_get_email_resource_template_path();
		
		$attachments = $headers = array();
		$attachments = array(
			$logo_url,
			$res_path . 'email_bg.jpg'
		);	

		$success = $this->zoner_send_mail( $emailTo, sprintf(__('[%1s] Getting package %2s.', 'spotter'), get_bloginfo('name'), $pkgName), $send_email_template, $headers, $attachments, true);
	}
	
	public function zoner_mail_new_conversation($args) {
		global 	$zoner, 
				$zoner_config, 
				$message, 
				$author, 
				$recipient, 
				$recipient_id; 
		
		if (!empty($args)) {
			$conv_id 	  = $args[0];
			$author_id 	  = $args[1];
			$recipient_id = $args[2];
			$message 	  = $args[3];
			
			$ua = get_userdata( $author_id );
			$ur = get_userdata( $recipient_id );
			
			
			$author 	= $uadn = zoner_get_user_name($ua);
			$recipient  = $urdn = zoner_get_user_name($ur);
			
			$uae  = $ua->user_email;
			$ure  = $ur->user_email;
			
			if (!empty($zoner_config['logo']['url'])) 
			$logo_url = esc_url($zoner_config['logo']['url']);
			$res_path = $this->zoner_get_email_resource_template_path();
		
			$send_email_template = '';
			$send_email_template = $this->zoner_get_ob_file_content('email-conversation-notification');
			
			$attachments = $headers = array();
			$attachments = array(
				$logo_url,
				$res_path . 'email_bg.jpg'
			);	
			
		$success = $this->zoner_send_mail( $ure, sprintf(__('[%1s] You have a new message.', 'zoner'), get_bloginfo('name')), $send_email_template, $headers, $attachments, true);
			
		}
	}
}