<?php
namespace Grav\WP;

class Styles {
	public static function enqueue_styles($styles) {
		foreach ($styles as &$style) {
			// calculate versions for scripts if they are local files
			if (!isset($style['version'])) {
				if (strpos($style['url'], get_template_directory_uri()) !== false) {
					// If Local file then get the time of when it was modified
					$file_path = str_replace(get_template_directory_uri(), get_template_directory(), $style['url']);

					if (file_exists($file_path)) {
						$style['version'] = filemtime($file_path);
					}
				} else {
					// If the value is not set to null WordPress will use it's version number as the script version
					$style['version'] = null;
				}
			}

			// add a preload tag if a preload hook is specified
			if (isset($style['preload_hook']) && $style['preload_hook']) {
				$url = $style['url'];

				if (isset($style['version'])) {
					$url .= '?ver='.$style['version'];
				}
				
				add_action($style['preload_hook'], function() use (&$url) {
					echo '<link rel="preload" href="'.$url.'" as="style">'."\n";
				});
			}
		}

		// enqueue all the styles
		add_action('wp_enqueue_scripts', function() use (&$styles) {
			foreach ($styles as $name => $params) {
				if (!isset($params['url'])) continue;
				if (!isset($params['deps'])) $params['deps'] = array();

				wp_enqueue_style($name, $params['url'], $params['deps'], $params['version']);
			}
		});
	}
}
