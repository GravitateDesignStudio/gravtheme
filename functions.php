<?php
if (!defined('ABSPATH')) {
	exit;
}

// check for/require composer autoloader
if (!file_exists(dirname(__FILE__).'/vendor/autoload.php')) {
	die("The required composer dependencies must be installed for this theme. Please run 'composer install' from the theme root.");
}

require_once('vendor/autoload.php');

// check for existence of gravitate/grav-util package
if (!class_exists('\Grav\WP\Content')) {
	die("The 'gravitate/grav-util' composer package is required for this theme. Please run 'composer install' from the theme root.");
}

// redirect image URLs for local development
if (defined('LOCAL_IMAGE_REDIRECT') && WP_HOME !== null && stripos(WP_HOME, '.local.com') !== -1) {
	Grav\Dev\Image::local_image_redirect(LOCAL_IMAGE_REDIRECT);
}

require_once('bootstrap/theme-setup.php');
require_once('bootstrap/performance.php');
require_once('bootstrap/media.php');
require_once('bootstrap/scripts-styles.php');
require_once('bootstrap/custom-post-types.php');
require_once('bootstrap/taxonomies.php');
require_once('bootstrap/acf.php');
require_once('bootstrap/menus.php');
require_once('bootstrap/tinymce.php');
require_once('bootstrap/plugins.php');
require_once('bootstrap/blocks.php');
require_once('bootstrap/theme-settings-pages.php');
