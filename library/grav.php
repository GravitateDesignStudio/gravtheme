<?php
// Cleaning up the Wordpress Head
function grav_head_cleanup() {
	// remove header links
	
	// these two are for RSS feeds - only uncomment if you don't want RSS
	//remove_action( 'wp_head', 'feed_links_extra', 3 );                 // Category Feeds
	//remove_action( 'wp_head', 'feed_links', 2 );                       // Post and Comment Feeds
	
	remove_action( 'wp_head', 'rsd_link' );                               // EditURI link
	remove_action( 'wp_head', 'wlwmanifest_link' );                       // Windows Live Writer
	remove_action( 'wp_head', 'index_rel_link' );                         // index link
	remove_action( 'wp_head', 'parent_post_rel_link', 10, 0 );            // previous link
	remove_action( 'wp_head', 'start_post_rel_link', 10, 0 );             // start link
	remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0 ); // Links for Adjacent Posts
	remove_action( 'wp_head', 'wp_generator' );                           // WP version
}
	// launching operation cleanup
	add_action('init', 'grav_head_cleanup');
	// remove WP version from RSS
	
// Fixing the Read More in the Excerpts
// This removes the annoying […] to a Read More link
function grav_excerpt_more($more) {
	global $post;
	// edit here if you like
	return '...  <a href="'. get_permalink($post->ID) . '" title="Read '.get_the_title($post->ID).'">Read more &raquo;</a>';
}
add_filter('excerpt_more', 'grav_excerpt_more');
	
// Adding WP 3+ Functions & Theme Support
function grav_theme_support() {
	add_theme_support('post-thumbnails');      // wp thumbnails (sizes handled in functions.php)
	set_post_thumbnail_size(125, 125, true);   // default thumb size
	
	//add_custom_background();                   // wp custom background
	add_theme_support('automatic-feed-links'); // rss thingy

	// adding post format support
	add_theme_support( 'post-formats',      // post formats
		array( 
			'aside',   // title less blurb
			'gallery', // gallery of images
			'link',    // quick link to other site
			'image',   // an image
			'quote',   // a quick quote
			'status',  // a Facebook like status update
			'video',   // video 
			'audio',   // audio
			'chat'     // chat transcript 
		)
	);	
	add_theme_support( 'menus' );            // wp menus

	register_nav_menus(                      // wp3+ menus
		array( 
			'main_nav' => 'The Main Menu',   // main nav in header
			'footer_links' => 'Footer Links' // secondary nav in footer
		)
	);	
}

	// launching this stuff after theme setup
	add_action('after_setup_theme','grav_theme_support');	
	// adding sidebars to Wordpress (these are created in functions.php)
	add_action( 'widgets_init', 'grav_register_sidebars' );
	// adding the search form (created in functions.php)
	add_filter( 'get_search_form', 'grav_wpsearch' );
	

class grav_walker extends Walker_Nav_Menu {

	function start_el(&$output, $item, $depth, $args) {
		global $wp_query;
		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

		$class_names = $value = '';

		$classes = empty( $item->classes ) ? array() : (array) $item->classes;

		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item ) );
		$class_names = ' class="' . esc_attr( $class_names ) . '"';

		$output .= $indent . '<li id="menu-item-'. $item->ID . '"' . $value . $class_names .'>';

		$attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
		$attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
		$attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
		$attributes .= ! empty( $item->url )        ? ' href="'   . esc_attr( $item->url        ) .'"' : '';

		$item_output = $args->before;
		$item_output .= '<a'. $attributes .'>';
		$item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
		$item_output .= '<br /><span class="sub">' . $item->description . '</span>';
		$item_output .= '</a>';
		$item_output .= $args->after;

		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}
}	


function grav_main_nav() {
	// display the wp3 menu if available
	$walker = new grav_walker;

    	wp_nav_menu(array( 
    		'menu' => 'main_nav', /* menu name */
    		'theme_location' => 'main_nav', /* where in the theme it's assigned */
    		'container_class' => 'menu', /* container class */
			'walker' => $walker /* customizes the output of the menu */
			
    	));
}

function grav_footer_links() { 
	// display the wp3 menu if available
    wp_nav_menu(
    	array(
    		'menu' => 'footer_links', /* menu name */
    		'theme_location' => 'footer_links', /* where in the theme it's assigned */
    		'container_class' => 'footer-links clearfix' /* container class */
    	)
	);
}
 

/****************** PLUGINS & EXTRA FEATURES **************************/
	
// Related Posts Function (call using grav_related_posts(); )
function grav_related_posts() {
	echo '<ul id="grav-related-posts">';
	global $post;
	$tags = wp_get_post_tags($post->ID);
	if($tags) {
		foreach($tags as $tag) { $tag_arr .= $tag->slug . ','; }
        $args = array(
        	'tag' => $tag_arr,
        	'numberposts' => 5, /* you can change this to show more */
        	'post__not_in' => array($post->ID)
     	);
        $related_posts = get_posts($args);
        if($related_posts) {
        	foreach ($related_posts as $post) : setup_postdata($post); ?>
	           	<li class="related_post"><a href="<?php the_permalink() ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></li>
	        <?php endforeach; } 
	    else { ?>
            <li class="no_related_post">No Related Posts Yet!</li>
		<?php }
	}
	wp_reset_query();
	echo '</ul>';
}

// Numeric Page Navi (built into the theme by default)
function page_navi($before = '', $after = '') {
	global $wpdb, $wp_query;
	$request = $wp_query->request;
	$posts_per_page = intval(get_query_var('posts_per_page'));
	$paged = intval(get_query_var('paged'));
	$numposts = $wp_query->found_posts;
	$max_page = $wp_query->max_num_pages;
	if ( $numposts <= $posts_per_page ) { return; }
	if(empty($paged) || $paged == 0) {
		$paged = 1;
	}
	$pages_to_show = 7;
	$pages_to_show_minus_1 = $pages_to_show-1;
	$half_page_start = floor($pages_to_show_minus_1/2);
	$half_page_end = ceil($pages_to_show_minus_1/2);
	$start_page = $paged - $half_page_start;
	if($start_page <= 0) {
		$start_page = 1;
	}
	$end_page = $paged + $half_page_end;
	if(($end_page - $start_page) != $pages_to_show_minus_1) {
		$end_page = $start_page + $pages_to_show_minus_1;
	}
	if($end_page > $max_page) {
		$start_page = $max_page - $pages_to_show_minus_1;
		$end_page = $max_page;
	}
	if($start_page <= 0) {
		$start_page = 1;
	}
	echo $before.'<nav class="page-navigation"><ol class="grav_page_navi clearfix">'."";
	if ($start_page >= 2 && $pages_to_show < $max_page) {
		$first_page_text = "First";
		echo '<li class="bpn-first-page-link"><a href="'.get_pagenum_link().'" title="'.$first_page_text.'">'.$first_page_text.'</a></li>';
	}
	echo '<li class="bpn-prev-link">';
	previous_posts_link('<<');
	echo '</li>';
	for($i = $start_page; $i  <= $end_page; $i++) {
		if($i == $paged) {
			echo '<li class="bpn-current">'.$i.'</li>';
		} else {
			echo '<li><a href="'.get_pagenum_link($i).'">'.$i.'</a></li>';
		}
	}
	echo '<li class="bpn-next-link">';
	next_posts_link('>>');
	echo '</li>';
	if ($end_page < $max_page) {
		$last_page_text = "Last";
		echo '<li class="bpn-last-page-link"><a href="'.get_pagenum_link($max_page).'" title="'.$last_page_text.'">'.$last_page_text.'</a></li>';
	}
	echo '</ol></nav>'.$after."";
}
	
// remove the p from around imgs (http://css-tricks.com/snippets/wordpress/remove-paragraph-tags-from-around-images/)
function filter_ptags_on_images($content){
   return preg_replace('/<p>\s*(<a .*>)?\s*(<img .* \/>)\s*(<\/a>)?\s*<\/p>/iU', '\1\2\3', $content);
}

add_filter('the_content', 'filter_ptags_on_images');




// reset roles function
// accepts 'administrator', 'author', 'editor', 'contributor', or 'subscriber'
function reset_role( $role ) {
    $default_roles = array(
        'administrator' => array(
        'switch_themes' => 1,
        'edit_themes' => 1,
        'activate_plugins' => 1,
        'edit_plugins' => 1,
        'edit_users' => 1,
        'edit_files' => 1,
        'manage_options' => 1,
        'moderate_comments' => 1,
        'manage_categories' => 1,
        'manage_links' => 1,
        'upload_files' => 1,
        'import' => 1,
        'unfiltered_html' => 1,
        'edit_posts' => 1,
        'edit_others_posts' => 1,
        'edit_published_posts' => 1,
        'publish_posts' => 1,
        'edit_pages' => 1,
        'read' => 1,
        'level_10' => 1,
        'level_9' => 1,
        'level_8' => 1,
        'level_7' => 1,
        'level_6' => 1,
        'level_5' => 1,
        'level_4' => 1,
        'level_3' => 1,
        'level_2' => 1,
        'level_1' => 1,
        'level_0' => 1,
        'edit_others_pages' => 1,
        'edit_published_pages' => 1,
        'publish_pages' => 1,
        'delete_pages' => 1,
        'delete_others_pages' => 1,
        'delete_published_pages' => 1,
        'delete_posts' => 1,
        'delete_others_posts' => 1,
        'delete_published_posts' => 1,
        'delete_private_posts' => 1,
        'edit_private_posts' => 1,
        'read_private_posts' => 1,
        'delete_private_pages' => 1,
        'edit_private_pages' => 1,
        'read_private_pages' => 1,
        'delete_users' => 1,
        'create_users' => 1,
        'unfiltered_upload' => 1,
        'edit_dashboard' => 1,
        'update_plugins' => 1,
        'delete_plugins' => 1,
        'install_plugins' => 1,
        'update_themes' => 1,
        'install_themes' => 1,
        'update_core' => 1,
        'list_users' => 1,
        'remove_users' => 1,
        'add_users' => 1,
        'promote_users' => 1,
        'edit_theme_options' => 1,
        'delete_themes' => 1,
        'export' => 1,
        ),
        
        'editor' => array(
        'moderate_comments' => 1,
        'manage_categories' => 1,
        'manage_links' => 1,
        'upload_files' => 1,
        'unfiltered_html' => 1,
        'edit_posts' => 1,
        'edit_others_posts' => 1,
        'edit_published_posts' => 1,
        'publish_posts' => 1,
        'edit_pages' => 1,
        'read' => 1,
        'level_7' => 1,
        'level_6' => 1,
        'level_5' => 1,
        'level_4' => 1,
        'level_3' => 1,
        'level_2' => 1,
        'level_1' => 1,
        'level_0' => 1,
        'edit_others_pages' => 1,
        'edit_published_pages' => 1,
        'publish_pages' => 1,
        'delete_pages' => 1,
        'delete_others_pages' => 1,
        'delete_published_pages' => 1,
        'delete_posts' => 1,
        'delete_others_posts' => 1,
        'delete_published_posts' => 1,
        'delete_private_posts' => 1,
        'edit_private_posts' => 1,
        'read_private_posts' => 1,
        'delete_private_pages' => 1,
        'edit_private_pages' => 1,
        'read_private_pages' => 1,
        ),
        
        'author' => array(
        'upload_files' => 1,
        'edit_posts' => 1,
        'edit_published_posts' => 1,
        'publish_posts' => 1,
        'read' => 1,
        'level_2' => 1,
        'level_1' => 1,
        'level_0' => 1,
        'delete_posts' => 1,
        'delete_published_posts' => 1,
        ),
        
        'contributor' => array(
        'edit_posts' => 1,
        'read' => 1,
        'level_1' => 1,
        'level_0' => 1,
        'delete_posts' => 1,
        ),
        
        'subscriber' => array(
        'read' => 1,
        'level_0' => 1,
        ),
        
        'display_name' => array(
        'administrator' => 'Administrator',
        'editor'	=> 'Editor',
        'author'	=> 'Author',
        'contributor' => 'Contributor',
        'subscriber'	=> 'Subscriber',
        ),
        
    );
    
    $role = strtolower( $role );
    
    remove_role( $role );
    
    return add_role( $role, $default_roles['display_name'][$role], $default_roles[$role] );
} 



?>
