<?php
namespace Grav\Vendor;

class ResponsiveImages {
	public static function get_default_settings() {
		if (!class_exists('GRAV_BLOCKS')) {
			return;
		}

        $image_sizes = array();

        foreach (\GRAV_BLOCKS::get_image_sizes() as $name => $image) {
			// Only include sizes that are not cropped.
			if(empty($image['crop']) && $image['width']) {
				$image_sizes[$name] = $image['width'];
			}
		}

        // Sort Sizes from smallest to largest by width
		asort($image_sizes);

        // Create json format for jquery
		$image_sizes_array = array();

        foreach ($image_sizes as $name => $width) {
			$image_sizes_array[] = array('name' => $name, 'size' => $width);
		}

        $image_sizes_array[] = array('name' => 'full', 'size' => 99999);

        $responsive_image_settings = array(
			'watch' => 'tag',
			'throttle' => 100,
			'downscale' => false,
			'downsize' => false,
			'onload' => true,
			'lazyload' => true,
			'lazyload_threshold' => 400,
			'sizes' => $image_sizes_array
		);

		return apply_filters('grav_blocks_responsive_image_settings', $responsive_image_settings);
	}

	public static function remove_default_script_enqueue() {
		if (!class_exists('GRAV_BLOCKS')) {
			return;
		}

		add_action('wp_footer', function() {
			remove_action('wp_footer', array('GRAV_BLOCKS', 'add_footer_js', 10));
		}, 1);
	}
}
