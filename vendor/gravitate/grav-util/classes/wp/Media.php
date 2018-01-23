<?php
namespace Grav\WP;

class Media {
	public static function add_upload_mime_types($types) {
		add_filter('upload_mimes', function($mimes) use (&$types) {
			foreach ($types as $key => $value) {
				$mimes[$key] = $value;
			}

			return $mimes;
		});
	}
}
