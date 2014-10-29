<?php

include_once('grav/grav_functions.php'); // Grav Functions
//include_once('grav/grav_arrays.php'); // Grav Arrays
include_once('acf.php'); // Advanced Custom Fields
include_once(dirname(dirname(__FILE__)).'/parts/blocks/blocks_acf.php'); // Blocks Advanced Custom Fields

// Add Custom Post Types
//include_once('grav/cpt/resources.php'); // Resources cpt


// Add and enqueue CSS and JS Files
add_action( 'wp_enqueue_scripts', 'grav_setup' );

// launching operation cleanup
add_action('init', 'grav_head_cleanup');

// Fixing the Read More in the Excerpts
// This removes the annoying […] to a Read More link
add_filter('excerpt_more', 'grav_excerpt_more');

// adding the search form (created in functions.php)
add_filter( 'get_search_form', 'grav_wpsearch' );

/* create a custom excert length ---------- */
add_filter('get_the_excerpt', 'grav_improved_trim_excerpt', 20);

// launching this stuff after theme setup
add_action('after_setup_theme','grav_theme_support');

// remove the p from around imgs (http://css-tricks.com/snippets/wordpress/remove-paragraph-tags-from-around-images/)
add_filter('the_content', 'grav_filter_ptags_on_images');
add_filter('the_excerpt', 'grav_filter_ptags_on_images');

// adding sidebars to Wordpress (these are created in functions.php)
add_action( 'widgets_init', 'grav_register_sidebars' );

// adding format options to tiny MCE ( better with Tiny MCE Advanced )
add_filter( 'tiny_mce_before_init', 'grav_mce_button' ); 


/************* ACTIONS AND FILTERS  *****************/


function grav_setup()
{
    $cache_var = 0; // Leave 0 to Auto Detect

    if(empty($cache_var)) // Run Auto Detect
    {
        $cached_files = array('/library/css/master.css',
                              '/library/css/master.scss',
                              '/library/js/master.js');

        if(!empty($cached_files))
        {
            foreach ($cached_files as $file)
            {
                $file = get_template_directory().$file;

                if(file_exists($file))
                {
                    $cache_var+= filemtime($file);
                }
            }
        }
    }

    // wp_enqueue_style(
    //     'css_plugins',
    //     get_template_directory_uri() . '/library/css/plugins.css',
    //     array(),
    //     $cache_var
    // );


    wp_enqueue_style(
        'css_master',
        get_template_directory_uri() . '/library/css/master.pscss',
        //get_template_directory_uri() . '/library/css/master.css',
        array(),
        $cache_var
    );

    wp_enqueue_script(
        'js_plugins',
        get_template_directory_uri() . '/library/js/plugins.js',
        array('jquery'),
        $cache_var,
        false
    );

    wp_enqueue_script(
        'js_master',
        get_template_directory_uri() . '/library/js/master.js',
        array('jquery', 'js_plugins'),
        $cache_var,
        true
    );
}


// Adding WP 3+ Functions & Theme Support
function grav_theme_support()
{
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
            'mobile_nav' => 'The Mobile Menu',   // Mobile nav in header
            'top_nav' => 'The Top Menu',   // top right nav in header
            'footer_links' => 'Footer Links', // secondary nav in footer
            'site_map' => 'Site Map Links' // Sitemap Links
        )
    );
}

// adding formats to Tiny MCE
function grav_mce_button( $init_array ) {  

	$style_formats = array(  
		array(  
			'title' => 'Button',  
			'block' => 'span',  
			'classes' => 'button-container',
			'wrapper' => true,
		),
	);  
	$init_array['style_formats'] = json_encode( $style_formats );  
	
	return $init_array;  
  
} 


/************* SIDEBARS, MENUS, ROLES  *****************/
function grav_register_sidebars()
{
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
//$grav_restricted_menus = array(__('Posts'), __('Links'), __('Comments'));
//add_action('admin_menu', 'grav_remove_menus');

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
//grav_reset_role( 'author' );


/************* SOME UTILITY FUNCTIONS  ********************/



