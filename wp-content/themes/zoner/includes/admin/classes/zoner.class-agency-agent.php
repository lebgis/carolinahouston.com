<?php
/**
 * Zoner Agent form Agnecies linked
*/
 
class zoner_agent_agency_invites {
	
	
	public function __construct() {
		global $wpdb;	
		$type = 'zoner_agent_from_agencies';
		$table_name = $wpdb->prefix . $type;

		$this->create_invite_table($table_name, $type);
		
		$variable_name = $type;
		$wpdb->$variable_name = $table_name;
		
		add_action( 'add_meta_boxes', array($this, 'zoner_add_agents_admin_panel_mt') ); 
		
		add_action( 'admin_print_scripts-post-new.php', array($this, 'zoner_add_agency_post_type_script'), 11 );
		add_action( 'admin_print_scripts-post.php', array($this, 'zoner_add_agency_post_type_script'), 11 );
		
		add_action( 'wp_ajax_admin_add_agent_to_agency', array($this,'zoner_admin_add_agent_to_agency_act') );
		add_action( 'wp_ajax_nopriv_admin_add_agent_to_agency', array($this,'zoner_admin_add_agent_to_agency_act') );
		
		add_action( 'wp_ajax_admin_delete_agent_from_agency', array($this,'zoner_admin_delete_agent_from_agency_act') );
		add_action( 'wp_ajax_nopriv_admin_delete_agent_from_agency', array($this,'zoner_admin_delete_agent_from_agency_act') );
		
	}	
	
	public function create_invite_table($table_name, $type) {
		global $wpdb;
 
		if (!empty ($wpdb->charset)) $charset_collate  = "DEFAULT CHARACTER SET {$wpdb->charset}";
		if (!empty ($wpdb->collate)) $charset_collate .= " COLLATE {$wpdb->collate}";
        
		
		
		if( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) != $table_name ) {
			$sql = '';     
			$sql .= "CREATE TABLE $table_name (
										invite_id  	bigint(20) NOT NULL AUTO_INCREMENT,
										agency_id 	bigint(20) NOT NULL,
										user_id 	bigint(20) NOT NULL DEFAULT '-1',
										user_email 	varchar(255) DEFAULT NULL,
										user_temporary_name varchar(255) DEFAULT NULL,										
										user_id_owner int(20) NOT NULL,
										status int(1) NOT NULL,
										invite_hash varchar(255) DEFAULT NULL,
										invite_date datetime DEFAULT '0000-00-00 00:00:00',
										PRIMARY KEY (invite_id)
						) $charset_collate;";
	 
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta($sql);
		}
	}
	
	public function zoner_add_agents_admin_panel_mt() {
		add_meta_box('zoner_add_agent_to_agency', __('Add Agents', 'zoner'), array( $this, 'zoner_add_agent_to_agency' ), 'agency', 'normal', 'low');
	}	
	
	public function zoner_add_agent_to_agency($post, $callback_args) {
		global $post, $zoner;

		$args = array();
		$args = array( 'role' => 'agent');
		
		wp_nonce_field( 'zoner-chosen-agents', 'zoner-chosen-agents' );	
		$all_agents = get_users( $args );
		$all_agents_grid = $this->zoner_get_all_agents_from_agency($post->ID, -1);

		?>
		
		<table class="form-table">
			<tbody>
				<tr>
					<th style="width:18%;">
						<label for="chosen-agents"><?php _e('Add agent in your agency', 'zoner'); ?></label>
					</th>
					<td style="padding:10px 0;">
						<?php 
							if(!empty($all_agents)) {
								echo '<select name="chosen-agents" multiple data-placeholder="'.__('Select agents', 'zoner').'" id="chosen-agents" class="chosen-agents">';									
									foreach ($all_agents as $agent) {
										 	 $is_choose = $invite_id = '';
										if ($post->post_author != $agent->ID) {
										
											if (!empty($all_agents_grid)) {
											
												foreach ($all_agents_grid as $agent_in_agency) {
													 if (($agent_in_agency->user_id == $agent->ID) && ($agent_in_agency->status == 1)) {
														 $is_choose = $agent_in_agency->user_id;
														 $invite_id = $agent_in_agency->invite_id;
														 
													  }	
												}
												
											}
											echo '<option data-inviteid="'.$invite_id.'" value="'.$agent->ID.'" '. selected( $agent->ID, $is_choose, false ).'>'.zoner_get_user_name($agent).'</option>';
										} 
									}
									
								echo '</select>';
							}
						?>						
					</td>
				</tr>
				
				<tr>
					<td colspan="2" style="padding:10px 0;">
						<div id="agents-for-agency" class="agents-for-agency datagrid">
							<table id="agents-grid" class="agents-grid" width="100%">
								<thead>
									<tr>
										<th><?php _e('User name', 'zoner'); ?></th>
										<th><?php _e('Email', 'zoner'); ?></th>
										<th><?php _e('Status', 'zoner'); ?></th>
										<th><?php _e('Who invited', 'zoner'); ?></th>
										<th><?php _e('Admin agency', 'zoner'); ?></th>
									</tr>
								</thead>
								<tbody>
									
									<?php 
										/*Add Admin Agency*/
										$cnt = 1;
										$user_admin_agency = get_user_by('id', $post->post_author);
										
										echo '<tr id="agent-link-'.$user_admin_agency->ID.'" class="agent-item">';
											echo '<td>'.zoner_get_user_name($user_admin_agency).'</td>';
											echo '<td>'.$user_admin_agency->user_email.'</td>';
											echo '<td>'.__('Active', 'zoner').'</td>';
											echo '<td></td>';
											echo '<td><input type="checkbox" checked disabled="disabled"/></td>';		
										echo '</tr>';
									?>
									
									<?php 
										
										if (!empty($all_agents_grid)) {
											foreach ($all_agents_grid as $agent) {
												$curr_user   = get_user_by('id', $agent->user_id_owner);
												$user_invite = get_user_by('id', $agent->user_id);
												
												$arr_class = array();
												$arr_class[] = 'agent-item';
												
												
												if (!empty($user_invite)) {
													$invite_email = $user_invite->user_email;
													$invite_name  = zoner_get_user_name($user_invite);
												} else {
													$invite_email = $agent->user_email;
													$invite_name  = $agent->user_temporary_name;
												}
												
												$status = $this->zoner_get_agent_status($agent->status);
												
												echo '<tr id="agent-link-'.$agent->invite_id.'" class="'.implode(' ', $arr_class).'">';
													echo '<td>'.$invite_name.'</td>';
													echo '<td>'.$invite_email.'</td>';
													
													echo '<td>'.$status.'</td>';
													echo '<td>'.zoner_get_user_name($curr_user).'</td>';
								
													if ($agent->user_id_owner == 0) {	
														echo '<td><input type="checkbox" checked disabled="disabled"/></td>';		
													} else {
														echo '<td><input type="checkbox" disabled="disabled"/></td>';		
													}								
								
												echo '</tr>';

											}
										}
									?>	
									
								</tbody>
							</table>
						</div>
					</td>
				</tr>
			</tbody>
		</table>
		
		<?php
	}
	
	public function zoner_get_all_agents_from_agency($agency_id = null, $status = 1) {
		global $wpdb; 
		$where_status = array();
		
		if ($status == -1) {
			$where_status[] = '';
		} else {
			$where_status[] = "AND status = '". $status ."'";
		}
		
		return $wpdb->get_results( "SELECT * FROM $wpdb->zoner_agent_from_agencies where agency_id = " . $agency_id . " ". implode(' ', $where_status) . " ORDER BY user_email" );
	}
	
	public function zoner_get_all_agencies_from_agent($user_id = null) {
		global $wpdb; 
		return $wpdb->get_results( "SELECT * FROM $wpdb->zoner_agent_from_agencies where user_id = "   . $user_id . " AND status = 1 ORDER BY user_email" );
	}
	
	public function zoner_get_invite_user_info($invite_hash = null) {
		global $wpdb; 
		return $wpdb->get_results( "SELECT * FROM $wpdb->zoner_agent_from_agencies where invite_hash = '"   . $invite_hash . "' AND status = 0" );
	}
	
	public function zoner_get_count_agencies_from_agent($user_id = null) {
		global $wp_query;
		
		$args = array();
		$args = array(
				'post_type'	=> 'agency',
				'post_status' => 'publish',
				'author'	=> $user_id,
				'orderby'   => 'DATE',
				'order'		=> 'ASC'
		);
		
		$query_agency = new WP_Query($args);
		return $query_agency->found_posts;
		
		wp_reset_postdata();
			
	}
	
	public function zoner_get_current_agency_id_by_agent($user_id = null) {
		global $wp_query;
		$out_id = -1;
		$args = array();
		$args = array(
				'post_type' 	 => 'agency',
				'post_status' 	 => 'publish',
				'author'		 =>  $user_id,
				'orderby'   	 => 'DATE',
				'order'			 => 'ASC',
				'posts_per_page' => 1
		);
		
		$query_agency = new WP_Query($args);
		if ($query_agency->have_posts() ) {
			while ( $query_agency->have_posts() ) : $query_agency->the_post();
				$out_id = get_the_ID();	
			endwhile;				
		}		
				
		wp_reset_postdata();
		return $out_id;
			
	}
	

	public function zoner_get_agent_status($status = 0) {
		$text = __('Pending', 'zoner');
		
		if ($status == 1) $text = __('Active', 'zoner');
		return $text;
	}
	
	
	public function zoner_admin_add_agent_to_agency_act() {
		$html = $invite_id = '';
		if (isset($_POST) && ($_POST['action'] == 'admin_add_agent_to_agency'))  {
			$userID 	= $_POST['user_id'];
			$post_id 	= $_POST['post_id'];
			$arr_field 	= $arr_types = array();
			
			$user = get_user_by( 'id', $userID );
			$user_invite = get_user_by( 'id', get_current_user_id());
			$arr_field = array(	
								'agency_id' 		=> $post_id, 
								'user_id' 			=> $userID,
								'user_id_owner' 	=> get_current_user_id(),
								'status' 			=> 1,
								'user_email' 		=> $user->user_email,
								'invite_hash'		=> '',
								'invite_date'		=> current_time( 'mysql')
							 );
									
			$arr_types = array( '%d', '%d', '%d', '%d', '%s', '%s', '%s');	
				
			$uniq_id = ZONER_GO()->zoner_insert_row_to_table('zoner_agent_from_agencies', $arr_field, $arr_types);
			
			$html = '<tr id="agent-link-'.$uniq_id.'" class="agent-item">';
				$html .= '<td>'.zoner_get_user_name($user).'</td>';
				$html .= '<td>'.$user->user_email.'</td>';
				$html .= '<td>'.__('Active', 'zoner').'</td>';
				$html .= '<td>'.zoner_get_user_name($user_invite).'</td>';
				$html .= '<td><input type="checkbox" disabled="disabled"/></td>';
			$html .= '</tr>';
			$invite_id = $uniq_id;
			
			$out = array($html, $invite_id);
			echo json_encode($out);
				
		}
		
		die();
	}
	
	public function zoner_admin_delete_agent_from_agency_act() {
		global $post, $zoner;
		if (isset($_POST) && ($_POST['action'] == 'admin_delete_agent_from_agency'))  {
			$invite_id = $_POST['invite_id'];
			$post_id   = $_POST['post_id'];
			
			$zoner->zoner_delete_from_table('zoner_agent_from_agencies', array('invite_id' => $invite_id, 'agency_id' => $post_id), array('%d', '%d')); 
		}	
		
		die('');
	}
	
	public function zoner_add_agency_post_type_script() {
		global $post_type;
		
		if( 'agency' == $post_type ) {
			wp_enqueue_style(  'zoner-chosen',	get_template_directory_uri() . '/includes/admin/classes/res/chosen/chosen.min.css');
			wp_enqueue_script( 'zoner-chosen',	get_template_directory_uri() . '/includes/admin/classes/res/chosen/chosen.jquery.min.js','','', true);
			wp_enqueue_style(  'zoner-chosen-init',  get_template_directory_uri() . '/includes/admin/classes/res/chosen/chosen.init.css');
			wp_enqueue_script( 'zoner-chosen-init' , get_template_directory_uri() . '/includes/admin/classes/res/chosen/chosen.init.js','','', true);
			
			
		}
	}
	
}