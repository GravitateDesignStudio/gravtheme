<?php
// ********************
// W3TC
// ********************
if (!defined('ENVIRONMENT') || constant('ENVIRONMENT') !== 'local') {
	Grav\Vendor\W3TC::lock_settings_page();
}

// ********************
// Yoast SEO
// ********************
// move Yoast SEO fields to the bottom of the editor
add_filter('wpseo_metabox_prio', function() {
	return 'low';
});

// ********************
// Gravity Forms
// ********************
add_filter('gform_init_scripts_footer', '__return_true');

// Wrap the inline JS that Gravity Forms outputs with an event
// listener that fires after the page has been loaded.
// This ensures that jQuery is available even if it has been
// deferred. Without this hook, jQuery must be loadeded in the
// <head> and cannot be deferred.
add_filter('gform_cdata_open', function($js) {
	if ((defined('DOING_AJAX') && DOING_AJAX) || isset($_POST['gform_ajax'])) {
		return $js;
	}

	return "document.addEventListener('DOMContentLoaded', function() { ";
});

add_filter('gform_cdata_close', function($js) {
	if ((defined('DOING_AJAX') && DOING_AJAX) || isset($_POST['gform_ajax'])) {
		return $js;
	}

	return " });";
});
