<?php

if ( !defined('ABSPATH') ) exit;

###############################################################################
##  Theme Setup
###############################################################################

// Includes
include_once('lib/gravitate/gravtheme/grav_theme_init.php'); // Grav Theme Initiate

GRAV_FUNC::enqueue_file('master_css', get_template_directory_uri() . '/assets/css/min/master.min.css');
GRAV_FUNC::enqueue_file('master_js', get_template_directory_uri() . '/assets/js/min/master.min.js');

// Add Custom Post Types
// include_once('library/cpt/resources.php');
// include_once('library/cpt/team.php');

// editor styles for tinymce
add_editor_style("/assets/css/min/editor-styles.min.css");

add_action('admin_init', array('GRAV_FUNC', 'lock_w3tc_settings_pages')); // Block client from making W3TC Changes

if(function_exists('acf_add_options_page'))
{
	acf_add_options_page(array('page_title' => 'Theme', 'menu_slug' => 'acf-options-theme', 'position' => 2));
}

// Remove jQuery Migrate
add_action( 'wp_default_scripts', array('GRAV_FUNC', 'dequeue_jquery_migrate') );


###############################################################################
#######################   CUSTOM THEME FUNCTIONALITY  #########################
###############################################################################
