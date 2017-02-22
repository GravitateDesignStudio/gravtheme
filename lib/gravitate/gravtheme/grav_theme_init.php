<?php

include_once(dirname(__FILE__).'/grav_functions.class.php'); // Grav Functions

add_action( 'init', array('GRAV_FUNC', 'theme_default_init') ); // launching operation cleanup
//add_filter('excerpt_more', 'grav_excerpt_more'); // Fixing the Read More in the Excerpts to removes the annoying […]
add_filter( 'get_search_form', array('GRAV_FUNC', 'get_wp_search_form') ); // adding the search form
//add_filter('get_the_excerpt', 'grav_improved_trim_excerpt', 20); // create a custom excert length
add_action( 'after_setup_theme', array('GRAV_FUNC', 'theme_support') ); // launching this stuff after theme setup

// remove the p from around imgs (http://css-tricks.com/snippets/wordpress/remove-paragraph-tags-from-around-images/)
add_filter( 'the_content', array('GRAV_FUNC', 'filter_ptags_on_images') );
add_filter( 'the_excerpt', array('GRAV_FUNC', 'filter_ptags_on_images') );

// adding sidebar1 to Wordpress
add_action( 'widgets_init', array('GRAV_FUNC', 'register_sidebar1') );

// Add Button Support to Tiny MCE
add_filter( 'tiny_mce_before_init', array('GRAV_FUNC', 'mce_formats') );

// Add menu shortcode that allows to add sitemap menu to wysiwyg
add_shortcode( 'menu', array('GRAV_FUNC', 'menu_shortcode') );

// Move Yoast to the bottom
add_filter( 'wpseo_metabox_prio', create_function( '', 'return "low";' ) );

// Update Image Compression Quality
add_filter( 'jpeg_quality', create_function( '', 'return 75;' ) );

// Use Templates in the /template folder instead.
add_filter( 'template_include', array('GRAV_FUNC', 'template_include'), 99 );

add_action( 'wp_loaded', array('GRAV_FUNC', 'wp_loaded'), 11 );

if( !defined('define') && getenv('WP_ENV') === 'local' )
{
    define( 'ACF_LITE' , true ); // true hides acf from the admin panel. false shows it.
}
