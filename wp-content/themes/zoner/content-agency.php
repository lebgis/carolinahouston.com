<?php 

/**
 * The Template for displaying property type content
 *
 * @package WordPress
 * @subpackage Zoner_Theme
 * @since Zoner Theme 1.0
 */
?>	 
	<?php if ( is_search() || !is_single()) { ?>
		<?php 
			global $prefix, $post;
			
			$id_ = $post->ID;
			$address = nl2br(get_post_meta($id_, $prefix . 'agency_address', true));
			$email 	 = get_post_meta($id_, $prefix . 'agency_email', true);
			$tel 	 = get_post_meta($id_, $prefix . 'agency_tel', true); 
			$mob 	 = get_post_meta($id_, $prefix . 'agency_mob', true); 
			$skype   = get_post_meta($id_, $prefix . 'agency_skype', true); 
			$sfi 	 = esc_url(get_post_meta($id_, $prefix . 'agency_line_img', true));
			
			$out_image = '<img data-src="holder.js/200x200?text='.__('No Image', 'zoner') .'" alt="" />';
				if ($sfi) {
					$out_image = '<img class="" src="'.$sfi.'" alt="" />';
				} 					
		?>	
		
		<div id="agency-<?php echo $id_; ?>" class="agency">
			<a href="<?php get_permalink(); ?>" class="agency-image"><?php echo $out_image; ?></a>
			<div class="wrapper">
				<header><a href="<?php the_permalink(); ?>"><h2><?php the_title(); ?></h2></a></header>
				<dl>
					<?php if ($tel) { ?>
						<dt><?php _e('Phone', 'zoner'); ?>:</dt>
						<dd><?php echo $tel; ?> </dd>
					<?php }	 ?>
					
					<?php if ($mob) { ?>
						<dt><?php _e('Mobile', 'zoner'); ?>:</dt>
						<dd><?php echo $mob; ?></dd>
					<?php }	?>
					
					<?php if ($email) { ?>
						<dt><?php _e('Email', 'zoner'); ?>:</dt>
						<dd><a href="mailto:<?php echo $email; ?>"><?php echo $email; ?></a></dd>
					<?php }	?>
					
					<?php if ($skype) { ?>
						<dt><?php _e('Skype', 'zoner'); ?>:</dt>
						<dd><a href="skype:<?php echo $skype; ?>'?call"><?php echo $skype; ?></a></dd>
					<?php }	?>
				
				</dl>
					
				<address>
					<h3><?php _e('Address', 'zoner'); ?></h3>
					<strong><?php the_title(); ?></strong><br />
					<?php echo $address; ?>
				</address>
			</div>
		</div><!-- /.agency -->
	<?php 
		} else { 
	?>
		<article id="post-<?php the_ID(); ?>">
			<section id="agent-detail" class="agency-detail">
				<?php zoner_get_agency_header(); ?>
				<?php zoner_get_agency_info(); ?>
				<?php zoner_get_agency_properties(); ?>
				<?php zoner_get_agency_agents(); ?>
				<?php zoner_get_agency_additional_fields(); ?>
			</section>
		</article>	
		
	<?php } ?>