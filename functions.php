<?php
if ( !defined('ABSPATH') ) exit;
###############################################################################
###########################   DEFAULT FUNCTIONALITY  ##########################
###############################################################################

// Includes
include_once('library/grav_functions.php'); // Grav Functions
//include_once('library/grav_arrays.php'); // Grav Arrays
include_once('library/acf.php'); // Advanced Custom Fields

add_action('wp_enqueue_scripts', 'grav_enqueue_scripts'); // Add and enqueue CSS and JS Files
add_action('init', 'grav_init'); // launching operation cleanup
add_filter('excerpt_more', 'grav_excerpt_more'); // Fixing the Read More in the Excerpts to removes the annoying […]
add_filter('get_search_form', 'grav_wpsearch'); // adding the search form
add_filter('get_the_excerpt', 'grav_improved_trim_excerpt', 20); // create a custom excert length
add_action('after_setup_theme','grav_theme_support'); // launching this stuff after theme setup

// remove the p from around imgs (http://css-tricks.com/snippets/wordpress/remove-paragraph-tags-from-around-images/)
add_filter('the_content', 'grav_filter_ptags_on_images');
add_filter('the_excerpt', 'grav_filter_ptags_on_images');

// adding sidebars to Wordpress (these are created in functions.php)
add_action('widgets_init', 'grav_register_sidebars');

// Add Button Support to Tiny MCE
add_filter('tiny_mce_before_init', 'grav_mce_init' );
add_filter("mce_external_plugins", "grav_add_anchor_btn_js");
add_filter('mce_buttons', 'grav_add_anchor_btn');

// Add SVG MIME type support to WP
add_filter('upload_mimes', 'grav_mime_types');

// Add menu shortcode that allows to add sitemap menu to wysiwyg
add_shortcode('menu', 'grav_menu_shortcode');

###############################################################################
#######################   CUSTOM THEME FUNCTIONALITY  #########################
###############################################################################

// Add and enqueue CSS and JS Files
$grav_enqueue_files = array(
    'master_css' => get_template_directory_uri() . '/library/css/master.css',
    'master_js' => get_template_directory_uri() . '/library/js/master.js',
);

//add_editor_style("library/css/editor-styles.css"); // editor styles for tinymce

// Add Custom Post Types
// include_once('library/cpt/resources.php');
// include_once('library/cpt/team.php');

// Disable Access to W3 Total Cache
if(isset($_GET['page']) && strpos($_GET['page'], 'w3tc_') !== false && $_GET['page'] != 'w3tc_dashboard')
{
  echo 'Sorry, This Plugin is Locked by Gravitate. <br><br><a href="javascript:history.go(-1);">< Back</a>';
  exit;
}

