<?php


include_once('grav/grav_functions.php'); // Grav Functions
include_once('grav/grav_arrays.php'); // Grav Arrays

/************* ACTIONS AND FILTERS  *****************/
// UN COMMENT TO ACTIVATE GRAVITATE TRACKING : IN ALPHA AS OF 12/30/13

$grav_config = array(
    'jsDebug' => false,
    'jsUseRequire' => true,
    'addGravTracking' => false,
    'themeURI' => get_template_directory_uri(),
    'themeUseFoundation5' => true
);

function grav_setup() {
    global $grav_config;

    wp_enqueue_style(
        'master',
        get_template_directory_uri() . '/library/css/master.css'
    );

    wp_enqueue_script('jquery');
    wp_localize_script( 'jquery', 'gData', $grav_config);

    
    //Do Config
    if($grav_config['jsUseRequire'] === false){
        wp_enqueue_script('grav_script', get_template_directory_uri() . 'library/js/scripts.js',array('jquery'));
    }
    
    if($grav_config['addGravTracking'] === true){
        require_once 'tracking/tracking-intergration.php';
    }

    if($grav_config['themeUseFoundation5'] === true){
        wp_enqueue_style(
            'foundation',
            get_template_directory_uri() . '/library/css/foundation.min.css',
            array(),
            '5.0.2'
        );
    }
}

add_action( 'wp_enqueue_scripts', 'grav_setup' );



// clean up wordpress head output (we don't need all this usually)
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



// Related Posts Function (call using grav_related_posts(); ) -- relates based on tags
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



// Numeric Page Navi, pass a custom query object if using a custom query
function page_navi($before = '', $after = '', &$custom_query=null) {
	global $wpdb, $wp_query;

	if(isset($custom_query) && !is_null($custom_query)) {
	    $wp_query = $custom_query;
	}

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






/* create a custom excert length ---------- */

remove_filter('get_the_excerpt', 'wp_trim_excerpt');
function improved_trim_excerpt($text) {
  global $post;
          if ( '' == $text ) {
                  $text = get_the_content('');
                  $text = apply_filters('the_content', $text);
                  $text = str_replace('\]\]\>', ']]&gt;', $text);
                  $text = preg_replace('@<script[^>]*?>.*?</script>@si', '', $text);
                  $text = strip_tags($text, '<p>');
                  $excerpt_length = apply_filters('excerpt_length', 55);
                  $words = explode(' ', $text, $excerpt_length + 1);
                  if (count($words)> $excerpt_length) {
                          array_pop($words);
                          array_push($words, '...');
                          $text = implode(' ', $words);
                  }
          }
          return $text;
}
add_filter('get_the_excerpt', 'improved_trim_excerpt', 20);





function grav_excerpt($new_length = 20) {
  add_filter('excerpt_length', function () use ($new_length) {
    return $new_length;
  }, 11);
  the_excerpt();
}





// remove the p from around imgs (http://css-tricks.com/snippets/wordpress/remove-paragraph-tags-from-around-images/)
function filter_ptags_on_images($content){
    $content = preg_replace('/<p>\s*(<a .*>)?\s*(<img .* \/>)\s*(<\/a>)?\s*<\/p>/iU', '\1\2\3', $content);
    return preg_replace('/<p>\s*(<iframe .*>*.<\/iframe>)\s*<\/p>/iU', '\1', $content);
}

add_filter('the_content', 'filter_ptags_on_images');
add_filter('the_excerpt', 'filter_ptags_on_images');









/************* SIDEBARS, MENUS, ROLES  *****************/
function grav_register_sidebars() {

    register_sidebar(array(
        'id' => 'sidebar1',
        'name' => 'Sidebar 1',
        'description' => 'The first (primary) sidebar.',
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h4 class="widgettitle">',
        'after_title' => '</h4>',
    ));

  }



//  remove some menus from the dashboard you don't need (for all users)
//  uncomment the add_action to enable
function remove_menus () {
global $menu;
    $restricted = array(__('Posts'), __('Links'), __('Comments'));
    end ($menu);
    while (prev($menu)){
        $value = explode(' ',$menu[key($menu)][0]);

        if(in_array($value[0] != NULL?$value[0]:"" , $restricted)){unset($menu[key($menu)]);}
    }
}
//add_action('admin_menu', 'remove_menus');



// Add Gforms capabilities to a role
/*$role = get_role( 'ROLE_NAME' );
$role->add_cap( 'gravityforms_edit_forms' );
$role->add_cap( 'gravityforms_delete_forms' );
$role->add_cap( 'gravityforms_create_form' );
$role->add_cap( 'gravityforms_view_entries' );
$role->add_cap( 'gravityforms_edit_entries' );
$role->add_cap( 'gravityforms_delete_entries' );
$role->add_cap( 'gravityforms_view_settings' );
$role->add_cap( 'gravityforms_edit_settings' );
$role->add_cap( 'gravityforms_export_entries' );
$role->add_cap( 'gravityforms_view_entry_notes' );
$role->add_cap( 'gravityforms_edit_entry_notes' );*/



// permanently delete a role.  use reset_role() to bring it back
// only needs to be run once, changes DB
/*$wp_roles = new WP_Roles();
$wp_roles->remove_role("author");
$wp_roles->remove_role("subscriber");
$wp_roles->remove_role("editor");
$wp_roles->remove_role("contributor");*/



// reset roles function
// accepts 'administrator', 'author', 'editor', 'contributor', or 'subscriber'
// only needs to run once, changes DB
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



/************* COMMENT LAYOUT *********************/

// Comment Layout
function grav_comments($comment, $args, $depth) {
   $GLOBALS['comment'] = $comment; ?>
    <li <?php comment_class(); ?>>
        <article id="comment-<?php comment_ID(); ?>" class="comment-block">
            <header class="comment-author vcard">
                <?php echo get_avatar($comment,$size='32',$default='<path_to_url>' ); ?>
                <?php printf(__('<cite class="fn">%s</cite>'), get_comment_author_link()) ?>
                <time><a href="<?php echo htmlspecialchars( get_comment_link( $comment->comment_ID ) ) ?>"><?php printf(__('%1$s'), get_comment_date(),  get_comment_time()) ?></a></time>
                <?php edit_comment_link(__('(Edit)'),'  ','') ?>
            </header>
            <?php if ($comment->comment_approved == '0') : ?>
                <div class="help">
                    <p><?php _e('Your comment is awaiting moderation.') ?></p>
                </div>
            <?php endif; ?>
            <section class="comment_content">
                <?php comment_text() ?>
            </section>
            <?php comment_reply_link(array_merge( $args, array('depth' => $depth, 'max_depth' => $args['max_depth']))) ?>
        </article>
    <!-- </li> is added by wordpress automatically -->
<?php
}
















/************* SEARCH FORM LAYOUT *****************/

// Search Form
function grav_wpsearch($form) {
    $form = '<form role="search" method="get" id="searchform" action="' . home_url( '/' ) . '" >
    <label class="screen-reader-text" for="s">' . __('Search for:', 'bonestheme') . '</label>
    <input type="text" value="' . get_search_query() . '" name="s" id="s" placeholder="Search the Site..." />
    <input type="submit" id="searchsubmit" value="'. esc_attr__('Search') .'" />
    </form>';
    return $form;
}







/************* SOME UTILITY FUNCTIONS  ********************/

// var dump something, wrapped in <pre>
// @mixed $var (the variable you want var_dumped)
function grav_dump($var){
    echo '<pre>';
    var_dump($var);
    echo '</pre>';
}

// echos the URL to the /library/ folder in the theme
// @bool $output_echo (default true, if false this function will only return the url)
function library_url($output_echo=true){
    if($output_echo === false) return get_bloginfo('template_url') . '/library';
    echo get_bloginfo('template_url') . '/library';
}



