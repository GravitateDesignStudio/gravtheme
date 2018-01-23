<?php
namespace Grav\WP;

class Head {
	public static function cleanup() {
		add_action('init', function() {
			remove_action('wp_head', 'rsd_link');                               // EditURI link
			remove_action('wp_head', 'wlwmanifest_link');                       // Windows Live Writer
			remove_action('wp_head', 'index_rel_link');                         // index link
			remove_action('wp_head', 'parent_post_rel_link', 10, 0);            // previous link
			remove_action('wp_head', 'start_post_rel_link', 10, 0);             // start link
			remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0); // Links for Adjacent Posts
			remove_action('wp_head', 'wp_generator');                           // WP version
	
			if (!is_admin()) {
				wp_deregister_script('wp-embed');
			}
		});
	}

	public static function remove_rss_feed_links() {
		add_action('init', function() {
			remove_action('wp_head', 'feed_links_extra', 3);                 // Category Feeds
			remove_action('wp_head', 'feed_links', 2);                       // Post and Comment Feeds
		});
	}
}
