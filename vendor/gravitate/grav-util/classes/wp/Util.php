<?php
namespace Grav\WP;

class Util {
	public static function strip_specific_tags($str, $tags) {
		foreach ($tags as $tag) {
			$str = preg_replace('/<'.$tag.'[^>]*>/i', '', $str);
		    $str = preg_replace('/<\/'.$tag.'>/i', '', $str);
		}

		return trim($str);
	}

	public static function include_all_files($path, $filter_func = false) {
		$files = glob($path);
		
		foreach ($files as $file) {
			if (is_callable($filter_func) && !$filter_func($file)) {
				continue;
			}

			include_once($file);
		}
	}

	public static function autoflush_rewrite_rules() {
		add_action('init', function() {
			$cpts = implode('', get_post_types()).implode('', get_taxonomies());
			
			if (get_option('grav_registered_post_types') != $cpts) {
				flush_rewrite_rules();
				update_option('grav_registered_post_types', $cpts);
			}
		});
	}
}
