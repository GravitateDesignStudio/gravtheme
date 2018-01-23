<?php
namespace Grav\Vendor;

class GravitateBlocks {
	protected static $bg_colors;

	/**
	 * Enforce background color choices
	 *
	 * @since 2017.01.20
	 * @author DF
	 */
	public static function enforce_background_colors($new_colors) {
		if (!is_array($new_colors)) {
			return;
		}

		self::$bg_colors = $new_colors;

		add_filter('grav_block_background_colors', function($colors) use (&$new_colors) {
			return $new_colors;
		});
	}

	public static function get_bg_colors($opts = array()) {
		if (!self::$bg_colors || !is_array(self::$bg_colors)) {
			return array();
		}

		$colors = array();

		foreach (self::$bg_colors as $class => $title) {
			if (isset($opts['exclude']) && is_array($opts['exclude']) && in_array($class, $opts['exclude'])) {
				continue;
			}

			$colors[$class] = $title;
		}

		// NOTE: use this version once we can move past PHP 5.3 on the dev server
		// returnBa array_filter(self::$bg_colors, function($name, $class) use (&$opts) {
		// 	if (isset($opts['exclude']) && is_array($opts['exclude']) && in_array($class, $opts['exclude'])) {
		// 		return false;
		// 	}
			
		// 	return true;
		// }, ARRAY_FILTER_USE_BOTH);

		return $colors;
	}

	/**
	 * Sort the order blocks appear in the flexible field dropdown list
	 * alphabetically
	 * 
	 * @since 2018.01.15
	 * @author DF
	 */
	public static function sort_block_names_alphabetically() {
		add_filter('grav_block_fields', function ($layouts) {
			uasort($layouts, function($a, $b) {
				return strcasecmp($a['label'], $b['label']);
			});
		
			return $layouts;
		}, 1000);
	}
}
