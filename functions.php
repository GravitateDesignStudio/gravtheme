<?php
if ( !defined('ABSPATH') ) exit;
###############################################################################
###########################   DEFAULT FUNCTIONALITY  ##########################
###############################################################################

// Includes
include_once('lib/gravitate/gravtheme/grav_theme_init.php'); // Grav Theme Initiate
//include_once('library/acf.php'); // Advanced Custom Fields

###############################################################################
#######################   CUSTOM THEME FUNCTIONALITY  #########################
###############################################################################

// Add and enqueue CSS and JS Files
// $grav_enqueue_files = array(
//     'master_css' => get_template_directory_uri() . '/library/css/master.min.css',
//     'master_js' => get_template_directory_uri() . '/library/js/master.js',
// );

GRAV_FUNC::enqueue_file('master_css', get_template_directory_uri() . '/assets/css/min/master.min.css');
GRAV_FUNC::enqueue_file('master_js', get_template_directory_uri() . '/assets/js/master.js');

// editor styles for tinymce
add_editor_style("/assets/css/min/editor-styles.min.css");

// Add SVG MIME type support to WP
add_filter('upload_mimes', 'grav_mime_types');

// Add Custom Post Types
// include_once('library/cpt/resources.php');
// include_once('library/cpt/team.php');

add_action('admin_init', array('GRAV_FUNC', 'lock_w3tc_settings_pages')); // Block client from making W3TC Changes

/**
*
* Allow svgs to be uploaded via WP media library
*
**/
function grav_mime_types($mimes)
{
    $mimes['svg'] = 'image/svg+xml';
    return $mimes;
}


add_filter( 'theme_page_templates', 'ggg' );

function ggg($templates)
{
    $templates = array(
        'templates/archive.php' => 'Archive'
    );

    //echo '<pre>';print_r($templates);echo '</pre>';
    //exit;

    return $templates;
}
