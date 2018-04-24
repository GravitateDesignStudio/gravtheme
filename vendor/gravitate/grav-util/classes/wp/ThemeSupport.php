<?php
namespace Grav\WP;

class ThemeSupport {
	static public $support_items = array();
    static private $hook_registered = false;

    static public function add($name, $params='') {
		self::$support_items[$name] = $params;

        if (!self::$hook_registered) {
            add_action('after_setup_theme', function() {
                foreach (\Grav\WP\ThemeSupport::$support_items as $name => $params) {
                    if ($params) {
                        add_theme_support($name, $params);
                    } else {
                        add_theme_support($name);
                    }
                }
            });

            self::$hook_registered = true;
        }
    }

	public static function post_thumbnails($width=300, $height=300, $crop=true) {
		add_action('after_setup_theme', function() use (&$width, &$height, &$crop) {
			add_theme_support('post-thumbnails');
			set_post_thumbnail_size($width, $height, $crop);
		});
	}

	public static function image_sizes($sizes) {
		if (!is_array($sizes)) {
			return;
		}

		add_action('after_setup_theme', function() use (&$sizes) {
			foreach ($sizes as $key => $values) {
				if (!isset($values['width']) || !isset($values['height'])) {
					continue;
				}

				if (!isset($values['crop'])) {
					$values['crop'] = false;
				}

				add_image_size($key, $values['width'], $values['height'], $values['crop']);
			}
		});
	}

	public static function automatic_feed_links($opts=array()) {
		add_action('after_setup_theme', function() use (&$opts) {
			add_theme_support('automatic-feed-links');

			if (isset($opts['disable_comments_feed']) && $opts['disable_comments_feed']) {
				add_filter('feed_links_show_comments_feed', '__return_false');
			}
		});
	}

	public static function custom_logo() {
		add_action('after_setup_theme', function() {
			add_theme_support('custom-logo');
		});
	}

	public static function register_menus($menus) {
		add_action('after_setup_theme', function() use (&$menus) {
			add_theme_support('menus');
			register_nav_menus($menus);
		});
	}
}
