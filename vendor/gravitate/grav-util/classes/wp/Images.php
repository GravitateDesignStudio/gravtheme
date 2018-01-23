<?php
namespace Grav\WP;

class Images {
	public static function use_imagick_if_available() {
		if (!class_exists('Imagick')) {
			return false;
		}

		add_filter('wp_image_editors', function() {
			return array('WP_Image_Editor_Imagick');
		});

		return true;
	}
}
