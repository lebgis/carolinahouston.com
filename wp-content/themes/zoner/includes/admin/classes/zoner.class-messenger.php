<?php

/**
 * Zoner conversation
*/
 
class zoner_conversation {
	public $conversation_tbl = null;
	public $message_tbl  = null;
	
	public function __construct() {
		global $wpdb;
		
		$this->conversation_tbl = $wpdb->prefix . 'zoner_conversation';
		$this->message_tbl   	= $wpdb->prefix . 'zoner_message';
		
		add_action( 'wp_ajax_create_new_conversation', 		  array($this,'zc_create_new_conversation') );
		add_action( 'wp_ajax_nopriv_create_new_conversation', array($this,'zc_create_new_conversation') );
		
		add_action( 'wp_ajax_get_all_msg', 		  array($this,'zc_get_all_msg') );
		add_action( 'wp_ajax_nopriv_get_all_msg', array($this,'zc_get_all_msg') );
		
		add_action( 'wp_ajax_delete_conversation', 		  array($this,'zc_delete_conversation') );
		add_action( 'wp_ajax_nopriv_delete_conversation', array($this,'zc_delete_conversation') );
		
		add_action( 'wp_ajax_new_chat_message', 		array($this,'zc_new_chat_message') );
		add_action( 'wp_ajax_nopriv_new_chat_message', 	array($this,'zc_new_chat_message') );
		
		add_filter('heartbeat_received', 		array($this, 'zc_heartbeat_upd_count_conv'), 600, 2);
		add_filter('heartbeat_nopriv_received', array($this, 'zc_heartbeat_upd_count_conv'), 600, 2);
		
		$this->zc_create_mtables();
	}	
	
	
	public function zc_create_mtables() {
		global $wpdb;
		
		if (!empty ($wpdb->charset)) $charset_collate  = "DEFAULT CHARACTER SET {$wpdb->charset}";
		if (!empty ($wpdb->collate)) $charset_collate .= " COLLATE {$wpdb->collate}";
		
		if( $wpdb->get_var( "SHOW TABLES LIKE '$this->conversation_tbl'" ) != $this->conversation_tbl ) {
			$sql = '';     
			$sql .= "CREATE TABLE $this->conversation_tbl (
										conversation_id	bigint(20) unsigned NOT NULL AUTO_INCREMENT,
										sender_id 		bigint(20) NOT NULL DEFAULT '0',
										recipient_id 	bigint(20) NOT NULL DEFAULT '0',
										row_date 		datetime NOT NULL default '0000-00-00 00:00:00',
										row_date_gmt 	datetime NOT NULL default '0000-00-00 00:00:00',		 
										PRIMARY KEY (conversation_id),
										KEY senders (sender_id),
										KEY recipients (recipient_id)
						) $charset_collate;";
	 
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta($sql);
		}
		
		if( $wpdb->get_var( "SHOW TABLES LIKE '$this->message_tbl'" ) != $this->message_tbl ) {
			$sql  = '';     
			$sql .= "CREATE TABLE $this->message_tbl (
										message_id	bigint(20) unsigned NOT NULL AUTO_INCREMENT,
										conversation_id	bigint(20) unsigned NOT NULL default '0',
										author_id   bigint(20) unsigned NOT NULL default '0',
										message_content longtext NOT NULL,
										message_status int(11) NOT NULL default '0',
										message_date datetime NOT NULL default '0000-00-00 00:00:00',
										message_date_gmt datetime NOT NULL default '0000-00-00 00:00:00',
										PRIMARY KEY (message_id),
										KEY conversation_id (conversation_id)
						) $charset_collate;";
	 
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta($sql);
		}
	}
	
	
	public function zc_heartbeat_upd_count_conv($response, $data) {
		$count_notification = 0;
		
		if (!empty($data['is_notification_update'])) {
			$count_notification = (int) $this->zc_get_messages_notification_cnt();
			$response['notifications'] = $count_notification;
		}
		
		return $response;
	}
		
		
	public function zc_is_conv_exists($sID = null, $rID = null) {
		global $wpdb;
		
		$sql_    = "SELECT conversation_id conv_id FROM {$this->conversation_tbl} WHERE sender_id = {$sID} AND recipient_id = {$rID}";
		
		$conv_id = $wpdb->get_col($sql_);
		if (!empty($conv_id)) {
			$conv_id = $conv_id[0];
		} else {
			$conv_id = -1;	
		}
		return $conv_id;
	}
	
	public function zc_create_new_conversation() {
		global $zoner, $wpdb, $post, $wp_query;
		$args = array();
		$args_message = array();
		
		if (isset($_POST) && ($_POST['action'] == 'create_new_conversation') && (!empty($_POST['chatMessage']))) {
			check_ajax_referer('zoner_ajax_nonce', 'security');
			$sender_id 		= get_current_user_id();	
			$recipient_id 	= (int) $_POST['authorID'];
			$chatMessage	= 	    $_POST['chatMessage'];
			
			$args = array(
				'sender_id' 	=> $sender_id,
				'recipient_id'  => $recipient_id,
			);
			
			$conv_id = null;
			$conv_id = $this->zc_is_conv_exists($sender_id, $recipient_id);
			
			if ($conv_id == -1)
			$conv_id = $this->zc_create_conversation($args);
			
			$args_message = array(
					'conversation_id' => $conv_id,
					'author_id'		  => $sender_id,
					'message_content' => $chatMessage
			);
			
			$this->zc_create_message($args_message);
			
			do_action( 'zoner_create_new_conversation', array($conv_id, $sender_id, $recipient_id, $chatMessage));
		}
		
		die();
	}
	
	public function zc_create_conversation($args = array()) {
		global $wpdb;
		$now 	 = current_time( 'mysql', 0 );
		$now_gmt = current_time( 'mysql', 1 );	
		
		$conversation_id = -1;
		
		$wpdb->insert( 
			$this->conversation_tbl, 
				array( 
					'sender_id' 	=> (int) $args['sender_id'], 
					'recipient_id' 	=> (int) $args['recipient_id'],
					'row_date'		=> $now,
					'row_date_gmt'	=> $now_gmt
				), 
				array( 
				'%d', 
				'%d',
				'%s',
				'%s'
			) 
		);
		
		$conversation_id = $wpdb->insert_id;
		
		return $conversation_id;
	}
	
	public function zc_create_message($args = array()) {
		global $wpdb;
		
		$now 	 = current_time( 'mysql', 0 );
		$now_gmt = current_time( 'mysql', 1 );	
		$msg_id = -1;
		
		if (!empty($args['conversation_id'])) {
			$wpdb->insert( 
				$this->message_tbl, 
					array( 
						'conversation_id' => (int) $args['conversation_id'], 
						'author_id'		  => (int) $args['author_id'], 
						'message_content' => sanitize_text_field($args['message_content']), 
						'message_status'  => 0,
						'message_date' 	  	=> $now,
						'message_date_gmt'	=> $now_gmt,
					), 
					array( 
						'%d', 
						'%d',
						'%s',
						'%d',
						'%s',
						'%s'
				) 
			);
		}	
		
		$msg_id = $wpdb->insert_id;
		return $msg_id;
	}
	
	
	public function zc_new_chat_message() {
		global $prefix, $zoner, $zoner_config;
		if (isset($_POST) && ($_POST['action'] == 'new_chat_message') && !empty($_POST['conv_id']) && !empty($_POST['chatMessage'])) {
			check_ajax_referer('zoner_ajax_nonce', 'security');
			$conv_id     = (int) $_POST['conv_id'];
			$chatMessage = $_POST['chatMessage'];
			$author_id = get_current_user_id();
			
			$args_message = array(
					'conversation_id' => $conv_id,
					'author_id'		  => $author_id,
					'message_content' => $chatMessage
			);
			
			$msg_id    = $this->zc_create_message($args_message);
			
			$user_meta = get_user_meta( $author_id );
			$user_data = get_userdata ( $author_id ); 
			
			$msg_content = $chatMessage;
			$now 	     = current_time( 'mysql', 0 );
					
			$msg_content = preg_replace('/\[.+\]/','',  $msg_content);
			$msg_content = apply_filters('the_content', $msg_content); 
			$msg_content = str_replace(']]>', ']]&gt;', $msg_content);
			
			$msg_date    = sprintf(__("%1s at %2s", 'zoner'), date_i18n(get_option( 'date_format' ), strtotime($now)), date_i18n(get_option( 'time_format' ), strtotime($now)));
					
			if (!empty($user_meta[$prefix.'avatar'])) {
				$avatar_url = $user_meta[$prefix.'avatar'][0];
			} else {
				$avatar_url = get_template_directory_uri() . '/includes/theme/profile/res/avatar.jpg';	
			}
			$display_name = zoner_get_user_name($user_data);
			echo '<div id="message-'.$msg_id.'" class="message">';
				echo '<figure><div class="avatar-box"><img src="'.esc_url($avatar_url).'" alt="" /></div></figure>';
				echo '<div class="mesage-wrapper">';
					echo '<d`iv class="author-bio">'.$display_name.'</div>';
					echo '<div class="msg-date"><span class="fa fa-calendar"></span>'.$msg_date.'</div>';
					echo '<div class="message-content">'.$msg_content.'</div>';
				echo '</div>';
			echo '</div>';
			
			
			$recipient_id = -1;
			$conv_row = $this->zc_get_conversation_by_id($conv_id);
			if (!empty($conv_row)) {
				$sid = $conv_row->sender_id;
				$rid = $conv_row->recipient_id;
				if ($sid == $author_id) {
					$recipient_id = $rid;
				} else {
					$recipient_id = $sid;
				}
				do_action( 'zoner_create_new_conversation', array($conv_id, $author_id, $recipient_id, $chatMessage));
			}
		}
		
		die();		
	}
	
	public function zc_update_status($conv_id = -1, $userID = -1) {
		global $wpdb;
		
		$sql_ = "UPDATE  $this->message_tbl msg,
				    	 $this->conversation_tbl convs
					  SET msg.message_status = 1
					WHERE (convs.recipient_id = {$userID} OR convs.sender_id = {$userID})
					  AND convs.conversation_id = msg.conversation_id 
					  AND msg.message_status = 0 
					  AND convs.conversation_id = {$conv_id}
					  AND msg.author_id != {$userID}";
					  
		$wpdb->query($sql_);
  
	}
	
	public function zc_delete_conversation() {
		
		if (isset($_POST) && ($_POST['action'] == 'delete_conversation') && (!empty($_POST['conv_id']))) {
			$conv_id = (int) $_POST['conv_id'];
			$this->zc_delete_messages($conv_id);
		}
		die();
	}
	
	public function zc_delete_messages($conv_id = 0) {
		global $wpdb;
		
		$wpdb->query( $wpdb->prepare("DELETE FROM $this->conversation_tbl WHERE conversation_id = %d", $conv_id));
		$wpdb->query( $wpdb->prepare("DELETE FROM $this->message_tbl      WHERE conversation_id = %d", $conv_id));
	}
	
	public function zc_get_conversation_by_id($conv_id = -1) {
		global $wpdb;
		$conv_query  = null;
		
		$conv_query  = $wpdb->get_row( "SELECT sender_id, recipient_id, row_date FROM {$this->conversation_tbl} WHERE conversation_id = {$conv_id}");
		
		if (!empty($conv_query)) {
			return $conv_query;
		} else {
			return -1;
		}
	}
		
	public function zc_get_conversations() {
		global $wpdb;
		$user_id 	 = get_current_user_id();
		$conv_query  = null;
		
		$conv_query  = $wpdb->get_results( "SELECT conversation_id, sender_id, recipient_id, row_date FROM {$this->conversation_tbl} WHERE (recipient_id = {$user_id} || sender_id = {$user_id})  ORDER BY row_date DESC");
		
		if (!empty($conv_query)) {
			return $conv_query;
		} else {
			return -1;
		}
	}
		
		
	public function zc_get_all_msg() {
		global $zoner, $zoner_config, $wpdb, $prefix;
		
		if (isset($_POST) && ($_POST['action'] == 'get_all_msg') && (!empty($_POST['conv_id']))) {
			$curr_user = get_current_user_id();	
			$conv_id   = (int) $_POST['conv_id'];
			
			/*Update and set status viewed*/
			$this->zc_update_status($conv_id, $curr_user);
			
			$all_messages = $this->zc_get_messages($conv_id);
			
			if (!empty($all_messages)) {
				echo '<div class="chat-messages">';
							
				foreach($all_messages as $msg) {
					$msg_id    = $msg->message_id;
					$author_id = $msg->author_id;
					$user_meta = get_user_meta( $author_id );
					$user_data = get_userdata ( $author_id ); 
					$msg_content  = $msg->message_content;
					$message_date = $msg->message_date;
					
					
					$now 	     = current_time( 'mysql', 0 );
					$msg_content = preg_replace('/\[.+\]/','',  $msg_content);
					$msg_content = apply_filters('the_content', $msg_content); 
					$msg_content = str_replace(']]>', ']]&gt;', $msg_content);
					
          $msg_date    = sprintf(__("%1s at %2s", 'zoner'), date_i18n(get_option( 'date_format' ), strtotime($message_date)), date_i18n(get_option( 'time_format' ), strtotime($message_date)));
					
					if (!empty($user_meta[$prefix.'avatar'])) {
						$avatar_url = $user_meta[$prefix.'avatar'][0];
					} else {
						$avatar_url = get_template_directory_uri() . '/includes/theme/profile/res/avatar.jpg';	
					}

					$display_name = zoner_get_user_name($user_data);
					
					
					if ($curr_user == $author_id) {
						echo '<div id="message-'.$msg_id.'" class="message">';
					} else {
						echo '<div id="message-'.$msg_id.'" class="message message-alt">';
					}					
							echo '<figure><div class="avatar-box"><img src="'.esc_url($avatar_url).'" alt="" /></div></figure>';
							echo '<div class="mesage-wrapper">';
								echo '<div class="author-bio">'.$display_name.'</div>';
								echo '<div class="msg-date"><span class="fa fa-calendar"></span>'.$msg_date.'</div>';
								echo '<div class="message-content">'.$msg_content.'</div>';
							echo '</div>';
						echo '</div>';
				}
				echo '</div>';
				
				echo '<form id="form-reply" name="form-reply" class="form-reply" action="#">';
					echo '<input type="hidden" name="conv_id" value="'.$conv_id.'"/>';
					echo '<div class="row">';
						echo '<div class="col-md-12">';
							echo '<div class="form-group">';
								echo '<textarea id="type-message" name="type-message" class="form-control" rows="5" maxlength="512" placeholder="'. __('Type your message...', 'zoner').'"></textarea>';
							echo '</div><!-- /.form-group -->';
							echo '<div class="form-group">';
								echo '<div class="pull-right">';
									echo '<button type="submit" class="btn btn-default medium">'. __('Send Message', 'zoner').'</button>';
									echo '<a href="#" id="back-btn" class="back-btn" title="'.__('Back to Message List', 'zoner').'"><i class="fa fa-arrow-circle-left"></i></a>';
								echo '</div>';
							echo '</div>';
						echo '</div>';
					echo '</div>';
				echo '</form>';
			}
		}
		
		die();
	}
	
	public function zc_get_messages($conv_id = -1) {
		global $wpdb;
		
		$all_msg = $wpdb->get_results( "SELECT * FROM $this->message_tbl msg WHERE msg.conversation_id = {$conv_id} ORDER BY message_date ");
		
		return $all_msg;
	}
	
	public function zc_get_last_message($conv_id = -1) {
		global $wpdb;
		$msg      =	null;
		$last_msg = $wpdb->get_results( "SELECT msg.message_content msg FROM  $this->message_tbl msg  where msg.conversation_id = {$conv_id} ORDER BY message_date DESC limit 0, 1");
		
		if (!empty($last_msg)) {
			$msg = $last_msg[0]->msg;
			$msg = zoner_string_limit_words($msg, 8);
		}
		
		return $msg;
	}
	
	
	public function zc_get_messages_notification_cnt() {
		global $wpdb;
		
		$count_of_notification = 0;
		$userID = get_current_user_id();
		
		$sql_notifications = null;
		$sql_notifications = "SELECT COUNT(*) notifications 
							    FROM $this->conversation_tbl convs, 
									 $this->message_tbl msg  
							   WHERE (convs.recipient_id = {$userID} || convs.sender_id = {$userID})
 								 AND convs.conversation_id = msg.conversation_id 
								 AND msg.message_status = 0
								 AND msg.author_id != {$userID}";
								
		return $count_of_notifications = $wpdb->get_var($sql_notifications);
	}
	
	public function zc_get_messages_notification()
	{
		global $zoner_config;
		if (!empty($zoner_config['property-agent-conversation'])) {
			$count_of_notifications = $this->zc_get_messages_notification_cnt();
			$userID = get_current_user_id();

			$arr_of_class = array();

			$arr_of_class[] = 'promoted';
			$arr_of_class[] = 'notifications';

			if ($count_of_notifications > 0)
				$arr_of_class[] = 'is_active';

			?>
			<a class="<?php echo implode(" ", $arr_of_class); ?>"
			   href="<?php echo add_query_arg(array('profile-page' => 'my_messages'), get_author_posts_url($userID)); ?>"><i
					class="fa fa-weixin"></i> <strong><?php echo $count_of_notifications; ?></strong></a>
			<?php
		}
	}
	
}