<?php
/**
 * Custom template tags for Zoner Theme
 *
 * @package WordPress
 * @subpackage Zoner_Theme
 * @since Zoner Theme 1.0
 */

if ( ! function_exists( 'zoner_paging_nav' ) ) :
/**
 * Display navigation to next/previous set of posts when applicable.
 *
 * @since Zoner Theme 1.0
 *
 * @return void
 */
function zoner_paging_nav() {
	if(is_singular()) return;
	
	global $wp_query, $wp_rewrite;
		   $gen_nav_text = '';
	$links = array();
	$max = 0;
	if ( $wp_query->max_num_pages <= 1 ) return;
	
	$previous_post_label = __('Previous', 'zoner');
	$next_post_label = __('Next', 'zoner');
	
	if 	   ( get_query_var('paged') ) { $paged = get_query_var('paged'); }
	elseif ( get_query_var('page') )  { $paged = get_query_var('page');  }
	else   { $paged = 1; }
	
	$max   = intval( $wp_query->max_num_pages );
	
	
	if ( $paged >= 1 ) $links[] = $paged;
	if ( $paged >= 3 ) {
	 	$links[] = $paged - 1;
		$links[] = $paged - 2;
	}
	if ( ( $paged + 2 ) <= $max ) {
		$links[] = $paged + 2;
		$links[] = $paged + 1;
	}

	$gen_nav_text = '<div class="center" role="navigation">';
		$gen_nav_text .= '<ul class="pagination loop-pagination">';

	if ( get_previous_posts_link() ) $gen_nav_text .=  sprintf( '<li>%s</li>' . "\n", get_previous_posts_link($previous_post_label) );
	
	if ( ! in_array( 1, $links ) ) { 
		$class = 1 == $paged ? ' class="active"' : '';
		$gen_nav_text .= sprintf( '<li%s><a href="%s">%s</a></li>' . "\n", $class, esc_url( get_pagenum_link( 1 ) ), '1' );
		if ( ! in_array( 2, $links ) )
			$gen_nav_text .= '<li><a href="#">…</a></li>';
	}

	sort( $links );
	foreach ( (array) $links as $link ) {
		$class = $paged == $link ? ' class="active"' : '';
		$gen_nav_text .= sprintf( '<li%s><a href="%s">%s</a></li>' . "\n", $class, esc_url( get_pagenum_link( $link ) ), $link );
	}
	if ( ! in_array( $max, $links ) ) {
		if ( ! in_array( $max - 1, $links ) )
			$gen_nav_text .= '<li><a href="#">…</a></li>' . "\n";

		$class = $paged == $max ? ' class="active"' : '';
		$gen_nav_text .= sprintf( '<li%s><a href="%s">%s</a></li>' . "\n", $class, esc_url( get_pagenum_link( $max ) ), $max );
	}
	if ( get_next_posts_link() ) $gen_nav_text .= sprintf( '<li>%s</li>' . "\n", get_next_posts_link($next_post_label) );
	
		$gen_nav_text .= '</ul>';
	$gen_nav_text .= '</div>';
	echo $gen_nav_text;
}
endif;

if ( ! function_exists( 'zoner_post_nav' ) ) :
/**
 * Display navigation to next/previous post when applicable.
 *
 * @since Zoner Theme 1.0
 *
 * @return void
 */
function zoner_post_nav() {
	// Don't print empty markup if there's nowhere to navigate.
	$previous = ( is_attachment() ) ? get_post( get_post()->post_parent ) : get_adjacent_post( false, '', true );
	$next     = get_adjacent_post( false, '', false );

	if ( ! $next && ! $previous ) {
		return;
	}

	?>
	<nav class="navigation post-navigation" role="navigation">
		<h1 class="screen-reader-text"><?php _e( 'Post navigation', 'zoner' ); ?></h1>
		<div class="nav-links">
			<?php
			if ( is_attachment() ) :
				previous_post_link( '%link', __( '<span class="meta-nav">Published In</span>%title', 'zoner' ) );
			else :
				previous_post_link( '%link', __( '<span class="meta-nav">Previous Post</span>%title', 'zoner' ) );
				next_post_link( '%link', __( '<span class="meta-nav">Next Post</span>%title', 'zoner' ) );
			endif;
			?>
		</div><!-- .nav-links -->
	</nav><!-- .navigation -->
	<?php
}
endif;


/**
 * Find out if blog has more than one category.
 *
 * @since Zoner Theme 1.0
 *
 * @return boolean true if blog has more than 1 category
 */
function zoner_categorized_blog() {
	if ( false === ( $all_the_cool_cats = get_transient( 'zoner_category_count' ) ) ) {
		// Create an array of all the categories that are attached to posts
		$all_the_cool_cats = get_categories( array(
			'hide_empty' => 1,
		) );

		// Count the number of categories that are attached to the posts
		$all_the_cool_cats = count( $all_the_cool_cats );

		set_transient( 'zoner_category_count', $all_the_cool_cats );
	}

	if ( 1 !== (int) $all_the_cool_cats ) {
		// This blog has more than 1 category so zoner_categorized_blog should return true
		return true;
	} else {
		// This blog has only 1 category so zoner_categorized_blog should return false
		return false;
	}
}

/**
 * Flush out the transients used in zoner_categorized_blog.
 *
 * @since Zoner Theme 1.0
 *
 * @return void
 */
function zoner_category_transient_flusher() {
	// Like, beat it. Dig?
	delete_transient( 'zoner_category_count' );
}
add_action( 'edit_category', 'zoner_category_transient_flusher' );
add_action( 'save_post',     'zoner_category_transient_flusher' );