<?php
namespace Grav\WP;

class Plugins {
	public static function force_activate_plugins($plugins=array()) {
		if (!$plugins) {
			return;
		}

		add_action(is_admin() ? 'admin_init' : 'wp', function() use (&$plugins) {
			if (!is_admin()) {
				include_once (ABSPATH.'wp-admin/includes/plugin.php');
			}

			foreach ($plugins as $plugin) {
				if (!is_plugin_active($plugin)) {
					activate_plugin($plugin);
				}
			}
		});
	}

	public static function disallow_updates($plugins) {
		add_filter('site_transient_update_plugins', function($value) use (&$plugins) {
			foreach ($plugins as $plugin) {
				if (isset($value->response) && isset($value->response[$plugin])) {
					unset($value->response[$plugin]);
				}
			}

			return $value;
	   });
	}
}
