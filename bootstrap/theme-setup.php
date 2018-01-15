<?php
Grav\WP\Templates::use_path_for_templates('templates');

// remove the p from around imgs (http://css-tricks.com/snippets/wordpress/remove-paragraph-tags-from-around-images/)
Grav\WP\Content::filter_p_tags_on_images();

Grav\WP\Head::cleanup();
// Grav\WP\Head::remove_rss_feed_links();

Grav\WP\Util::autoflush_rewrite_rules();

Grav\WP\ThemeSupport::automatic_feed_links();

Grav\WP\ThemeSupport::custom_logo();

// The WP CSS customizer is the devil
add_action('customize_register', function($wp_customize) {
	$wp_customize->remove_section('custom_css');
});
