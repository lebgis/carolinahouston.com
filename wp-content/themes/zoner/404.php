<?php
/**
 * The template for displaying 404 pages (Not Found)
 *
 * @package WordPress
 * @subpackage Zoner_Theme
 * @since Zoner Theme 1.0
 */

get_header(); ?>
<?php 
	global $zoner_config;
	if (!empty($zoner_config['404-image']))
		$bg_404 = $zoner_config['404-image'];
	
	$text_404 = __('404', 'zoner');
	if (isset($zoner_config['404-text'])) 
		$text_404 = esc_attr($zoner_config['404-text']);
?>

	<div class="container">
		<section id="404">
			<div class="error-page">
				<div class="title">
					<?php if (!empty($bg_404['url']))  { ?>
						<img alt="<?php echo $text_404; ?>" src="<?php echo esc_url($bg_404['url']); ?>" class="top">
					<?php } ?>	
                    
					<header><?php echo $text_404; ?></header>
                    <?php if (!empty($bg_404['url'])) { ?>
						<img alt="" src="<?php echo esc_url($bg_404['url']); ?>" class="bottom">
					<?php } ?>
                 </div>
                 <h2 class="no-border"><?php _e('Page not found', 'zoner'); ?></h2>
                 <a href="<?php echo home_url(''); ?>" class="link-arrow back" onclick="history.back(-1)"><?php _e('Go Back', 'zoner'); ?></a>
			</div>
		</section>
	</div><!-- /.container -->

<?php
	get_footer();
