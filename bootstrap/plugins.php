<?php
// ********************
// W3TC
// ********************
Grav\Vendor\W3TC::lock_settings_page();

// ********************
// Yoast SEO
// ********************
// move Yoast SEO fields to the bottom of the editor
add_filter('wpseo_metabox_prio', function() {
	return 'low';
});
