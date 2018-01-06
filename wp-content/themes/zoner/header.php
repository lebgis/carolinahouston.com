<?php
/**
 * The Header for our theme
 *
 * Displays all of the <head> section and everything up till <div id="main">
 *
 * @package WordPress
 * @subpackage Zoner_Theme
 * @since Zoner Theme 1.0
 */
?><!DOCTYPE html>
<!--[if IE 7]>
<html class="ie ie7" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 8]>
<html class="ie ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 7) | !(IE 8) ]><!-->
<html <?php language_attributes(); ?>>
<!--<![endif]-->
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta content="width=device-width, initial-scale=1.0" name="viewport">
	<title><?php wp_title( '|', true, 'right' ); ?></title>
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?> data-offset="90" data-target=".navigation" data-spy="scroll" id="page-top">
	<div id="page" class="hfeed site wrapper">
		 <!-- Navigation -->
		<div class="navigation">
			<?php zoner_seconadry_navigation(); ?>
			<div class="container">
				<header class="navbar" id="top" role="banner">
					<div class="navbar-header">
						<button class="navbar-toggle" type="button" data-toggle="collapse" data-target=".bs-navbar-collapse">
							<span class="sr-only"><?php _e('Toggle navigation', 'zoner'); ?></span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
						</button>
						<?php zoner_get_logo(); ?>
					</div>
					<?php zoner_get_main_nav(); ?>
					<?php zoner_add_property(); ?>
					<?php zoner_add_compare(); ?>
				</header><!-- /.navbar -->
			</div><!-- /.container -->
		</div><!-- /.navigation -->
		<!-- end Navigation -->
		<?php zoner_get_header_variations(); ?>
		<div id="page-content" class="site-main">
		<?php 
		global $prefix;
		$is_breadcrumb_show = get_post_meta(get_the_ID() , $prefix . 'is_breadcrumb_show', true);
		if(empty($is_breadcrumb_show) || $is_breadcrumb_show=='on'): 
			zoner_add_breadcrumbs(); 
		endif; 
		?>