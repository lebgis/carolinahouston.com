<?php
/**
 * Zoner bookmark
*/
 
class zoner_bookmark {
		
	public function __construct() {
		global $wpdb;	
		$type = 'zoner_bookmark';
		$table_name = $wpdb->prefix . $type;

		$this->create_bookmark_table($table_name, $type);
		
		$variable_name = $type;
		$wpdb->$variable_name = $table_name;
		
		add_action( "wp_ajax_nopriv_add_user_bookmark", array ( $this, 'zoner_set_bookmark_ajax' ) );
        add_action( "wp_ajax_add_user_bookmark",        array ( $this, 'zoner_set_bookmark_ajax' ) );
		add_action( 'before_delete_post', 				array ( $this, 'zoner_remove_bookmarks_on_delete_property' ) );
	}	
		
	
	public function create_bookmark_table($table_name, $type) {
		global $wpdb;
 
		if (!empty ($wpdb->charset)) $charset_collate  = "DEFAULT CHARACTER SET {$wpdb->charset}";
		if (!empty ($wpdb->collate)) $charset_collate .= " COLLATE {$wpdb->collate}";
        
		if( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) != $table_name ) {
		
			$sql = '';     
			$sql = "CREATE TABLE $table_name (
					bookmark_id bigint(20) NOT NULL AUTO_INCREMENT,
					user_id 	bigint(20) NOT NULL default 0,
					property_id varchar(255) DEFAULT NULL,
					is_choose 	bigint(20) NOT NULL default 0,
					time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
					UNIQUE KEY bookmark_id (bookmark_id)
			) $charset_collate;";
     
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($sql);
		}
	}
	
	public function zoner_set_bookmark_ajax() {
		
		if ($_POST['action'] != 'add_user_bookmark') return;
		$property_id = $is_choose = 0;
		
		$property_id = $_POST['property_id'];
		$is_choose 	 = $_POST['is_choose'];
		
		if (($property_id != 0) || (!empty($property_id)))
			echo $this->zoner_set_bookmark($property_id, $is_choose);
		
		die('');
	}
	
	public function zoner_set_bookmark($property_id, $is_choose) {
		global $wpdb;
		$user_id =  get_current_user_id();
		
		$is_update = $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->zoner_bookmark where property_id = ". $property_id . " AND user_id = ". $user_id );
		
		if ($is_update == 0) {
			$wpdb->insert( 
				$wpdb->zoner_bookmark,
				array( 
					'user_id' 	  => $user_id,
					'property_id' => $property_id,
					'is_choose'	  => $is_choose,
					'time'		  => current_time( 'mysql' )
				) 
			);
		} else {
			$wpdb->update( 
				$wpdb->zoner_bookmark, 
				array( 'is_choose' => $is_choose), 
				array( 	
					'property_id'  => $property_id,
					'user_id'  	   => $user_id,
				),
				array( 	'%d' ), 
				array( 	'%d', '%d' )
			
			);
		}		
		return $is_choose;
	}
	
	public function zoner_get_bookmark($property_id) {
		global $wpdb;
		$user_id =  get_current_user_id();
		return $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->zoner_bookmark where property_id = ". $property_id . " AND user_id = ". $user_id . " AND is_choose = 1");
		
	}		
	
	public function zoner_get_all_bookmark_by_user ($user_id = null) {
		global $wpdb;
		
		if (empty($user_id))
		$user_id =  get_current_user_id();
		
		$result = $wpdb->get_results( "SELECT property_id FROM $wpdb->zoner_bookmark bkmr where bkmr.user_id = ". $user_id . " AND bkmr.is_choose = 1 ORDER BY property_id ASC");
		return $result;
	}		
	
	public function zoner_remove_bookmarks_on_delete_property( $post_id ) {
		global $wpdb;
		$post_type = get_post_type($post_id);
		
		if ($post_type == 'property') {
			$wpdb->delete( $wpdb->zoner_bookmark, array( 'property_id' => $post_id ), array( '%d' ) ); 
		}
	}
	
}