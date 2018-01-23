<?php
namespace Grav\Dev;

class Image {
	public static function local_image_redirect($remote_domain) {
		// create function for the redirect as a variable so it can be used in the two filters below
		$redirect_image = function($url) use (&$remote_domain) {
			$parts = parse_url($url);

			// if local file exists, rewrite the url to use the local host
			if (file_exists(ABSPATH . substr($parts['path'], 1))) {
				return WP_HOME . $parts['path'];
			}

			return str_replace($parts['host'], $remote_domain, $url);
		};

		// filter for general image attachments
		add_filter('wp_get_attachment_url', function($url) use (&$redirect_image) {
			return $redirect_image($url);
		});

		// filter for images in posts that include srcset attributes
		add_filter('wp_calculate_image_srcset', function($sources, $size_array, $image_src, $image_meta, $attachment_id) use (&$redirect_image) {
			foreach ($sources as &$source) {
				$source['url'] = $redirect_image($source['url']);
			}

			return $sources;
		}, 10, 5);
	}
}
