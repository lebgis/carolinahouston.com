<?php
/**
 * Zoner raiting
*/
 
class zoner_ratings {
	
	public function __construct() {
		add_action('transition_comment_status', array($this,'zoner_comment_approve'), 10, 3);
	}	

	function zoner_comment_approve($new_status, $old_status, $comment) {
		global $zoner, $prefix, $post;
		$post->ID = $comment->comment_post_ID;
		update_post_meta($post->ID, $prefix.'avg_rating', $this->zoner_calculate_rating_by_property());
	}
	
	public function zoner_calculate_rating_by_property($status = 'approve') {
		global $prefix, $zoner_config, $post;
		$ratings_array = array();
		
		$args = array(
			'post_id' => $post->ID, 
			'status'  => $status
		);
		$total_votes = 0;
		
		$comments = get_comments($args);
		foreach ($comments as $comment) {
			$rating = get_comment_meta( $comment->comment_ID, $prefix.'rating', true ); 
		    $ratings_array[] = $rating;
			$total_votes++;
		}
		
		$count_values = array_count_values($ratings_array);
		$one_stars = $two_stars = $three_stars = $four_stars = $five_stars = 0;
		
		if (isset($count_values[1])) $one_stars  = (int) $count_values[1];
		if (isset($count_values[2])) $two_stars  = (int) $count_values[2]*2;
		if (isset($count_values[3])) $three_stars = (int) $count_values[3]*3;
		if (isset($count_values[4])) $four_stars = (int) $count_values[4]*4;
		if (isset($count_values[5])) $five_stars = (int) $count_values[5]*5;
		if (!empty($count_values)) {
			return round((( $one_stars + $two_stars + $three_stars + $four_stars + $five_stars) / $total_votes), 1);	
		} else {
			return 0;
		}		
	}
		
}