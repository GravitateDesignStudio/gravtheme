<?php
namespace Grav\WP;

class REST {
	/**
	 * Disable the specified REST endpoints
	 * @param  array  $disable_endpoints array of endpoints to disable
	 *
	 * @since 2017.01.11
	 * @author DF
	 */
	public static function disable_endpoints($disable_endpoints=array()) {
		add_filter('rest_endpoints', function($endpoints) use (&$disable_endpoints) {
			foreach ($disable_endpoints as $disable_endpoint) {
				if (isset($endpoints[$disable_endpoint])) {
					unset($endpoints[$disable_endpoint]);
				}
			}

		    return $endpoints;
		});
	}

	/**
	 * Fixes an issue with oAuth failing because it believes URL's don't match when generating secrets during authorization.
	 *
	 * @since 2017.01.11
	 * @author DF
	 */
	public static function fix_oauth_url_match_issue() {
		add_filter('rest_oauth.check_callback', create_function('$valid,$url,$consumer', 'return true;'), 10, 3);
	}

	/**
	 * Restrict access to the REST API to authenticated users only
	 * @param string $minimum_user_level The minimum user level to allow access for (default is 0)
	 *
	 * @since 2017.02.08
	 * @author DF
	 */
	public static function restrict_to_authenticated_users($minimum_user_level=0) {
		add_filter('rest_authentication_errors', function($access) use (&$minimum_user_level) {
			$cur_user = wp_get_current_user();

			if (is_user_logged_in() && $cur_user && isset($cur_user->allcaps['level_'.$minimum_user_level]) && $cur_user->allcaps['level_'.$minimum_user_level]) {
				return $access;
			}

			return new \WP_Error('access-denied', 'REST API access denied', array('status' => rest_authorization_required_code()));
		});
	}

	public static function register_routes($namespace, $controllers) {
		add_action('rest_api_init', function() use ($namespace, $controllers) {
			foreach ($controllers as $url => $controller) {
				new $controller($namespace, $url);
			}
		});
	}
}
