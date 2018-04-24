<?php
namespace Grav\WP;

class TinyMCE {
	public static function add_formats($formats, $merge_formats = true) {
		add_filter('tiny_mce_before_init', function($init_array) use (&$formats, &$merge_formats) {
			/*
			* Each array child is a format with it's own settings
			* Notice that each array has title, block, classes, and wrapper arguments
			* Title is the label which will be visible in Formats menu
			* Block defines whether it is a span, div, selector, or inline style
			* Classes allows you to define CSS classes
			* Wrapper whether or not to add a new block-level element around any selected elements
			*/

			// Insert the array, JSON ENCODED, into 'style_formats'
			$init_array['style_formats'] = json_encode($formats);
			$init_array['style_formats_merge'] = $merge_formats;

			return $init_array;
		});
	}

	public static function set_options($options = array()) {
		add_filter('tiny_mce_before_init', function($init_array) use (&$options) {
			return array_merge($init_array, $options);
		});
	}
}
