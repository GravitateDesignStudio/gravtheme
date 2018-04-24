<?php
namespace Grav\Vendor;

class ACF
{
	public static function get_image_urls($attachment)
	{
		if (is_numeric($attachment)) {
			$attachment = acf_get_attachment($attachment);
		}

		$used_urls = array($attachment['url']);
		$image_urls = array(
			array(
				'url' => $attachment['url'],
				'width' => $attachment['width'],
				'height' => $attachment['height']
			)
		);

		foreach ($attachment['sizes'] as $key => $url) {
			// check if this a width or height key and not an image URL
			if (stripos($key, '-width') !== false || stripos($key, '-height') !== false) {
				continue;
			}

			// check if this URL has already been used
			if (in_array($url, $used_urls)) {
				continue;
			}

			$used_urls[] = $url;
			$image_urls[] = array(
				'url' => $url,
				'width' => $attachment['sizes'][$key.'-width'],
				'height' => $attachment['sizes'][$key.'-height']
			);
		}

		return $image_urls;
	}
}
