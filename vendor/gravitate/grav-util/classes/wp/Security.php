<?php
namespace Grav\WP;

class Security {
	public static function disable_dashboard_access_for_roles($roles) {
		add_action('init', function() use (&$roles) {
			if (is_admin() && !defined('DOING_AJAX')) {
				foreach ($roles as $role) {
					if (current_user_can($role)) {
						wp_redirect(home_url());
						exit;
					}
				}
			}
		});
	}

	public static function set_mimimum_dashboard_access_level($min_level=1) {
		add_action('init', function() use (&$roles) {
			if (is_admin() && !defined('DOING_AJAX')) {
				$cur_user = wp_get_current_user();

				if (!$cur_user || !isset($cur_user->allcaps['level_'.$minimum_level]) || !$cur_user->allcaps['level_'.$minimum_level]) {
					wp_redirect(home_url());
					exit;
				}
			}
		});
	}

	public static function disable_xmlrpc() {
		add_filter('xmlrpc_enabled', '__return_false');
	}

	public static function remove_wp_version_meta() {
		add_filter('the_generator', '__return_false');
	}
}
