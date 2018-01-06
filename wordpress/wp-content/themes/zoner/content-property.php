<?php 

/**
 * The Template for displaying property type content
 *
 * @package WordPress
 * @subpackage Zoner_Theme
 * @since Zoner Theme 1.0
 */
 
 global $zoner_config, $prefix, $post, $zoner;
		$allow_rating = get_post_meta($post->ID, $prefix.'allow_raiting', true); 
?>	 
	<?php if ( is_search() || !is_single()) { ?>
		<?php 
			
			if (isset($zoner_config['page-property-grid'])) {
				$grid_type = $zoner_config['page-property-grid'];
				if ( $grid_type == 1) {
					zoner_get_property_grid_items_masonry();
				} elseif($grid_type == 2) {
					zoner_get_property_grid_items_original();				
				} elseif($grid_type == 3) {
					zoner_get_property_grid_items_lines();
				} else {
					zoner_get_property_grid_items_masonry();
				}
			}
			
		?>	
	<?php 
		} else { 
	?>
		<article id="post-<?php the_ID(); ?>">
			<section id="property-detail">
				<?php
					zoner_setPropViews(); 
					zoner_get_property_header();
					zoner_get_gallery_property();
				?>

				<div class="row">
					
					<?php 
						//Add Custom Widget Area
						$main_section_class   = array();
						$main_section_class[] = 'col-sm-12';
						
						
						if (zoner_active_sidebar('property-info')) {
							$main_section_class[] = 'col-md-8';
					?>
					
					<div class="col-md-4 col-sm-12">
						<?php 
							zoner_sidebar('property-info');
							edit_post_link( '<i title="' . __("Edit", 'zoner') . '" class="fa fa-pencil-square-o"></i><span class="edit-link-text">'.__("Edit", 'zoner') .'</span>', '', '' ); 
						?>
					</div>
					
					<?php 
						} else { 
							$main_section_class[] = 'col-md-12';
						} 
					 ?>
					
					<div class="<?php echo implode(' ', $main_section_class); ?>">
						<?php 
							zoner_get_property_description();
							zoner_get_property_files();
							zoner_get_property_features();
							zoner_get_property_floor_plans();
							zoner_get_property_map();
							if ($allow_rating == 'on') zoner_get_rating_form();
							zoner_get_video_presents();
						?>
					</div>
					
					<div class="col-md-12 col-sm-12">
						<?php 
							zoner_get_contact_agent();
							zoner_get_single_similar_properties();
							if ($allow_rating == 'on') {
								do_action('zoner_comments_template'); 
							}
						?>
					</div>
				</div>						
			</section>
		</article>	
		
	<?php } ?>