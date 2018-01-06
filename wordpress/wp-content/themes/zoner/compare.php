<?php
/**
 * Template Name: Compare Properties
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages and that
 * other 'pages' on your WordPress site will use a different template.
 *
 * @package WordPress
 * @subpackage Zoner_Theme
 * @since Zoner Theme 1.0
 */

get_header(); ?>
	<?php do_action('zoner_before_content') ?>
		<?php do_action('zoner_compare_content'); ?>
	<?php do_action('zoner_after_content') ?>
<?php
   get_footer();
