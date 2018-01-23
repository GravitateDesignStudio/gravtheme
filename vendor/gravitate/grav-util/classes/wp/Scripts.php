<?php
namespace Grav\WP;

class Scripts {
	static $scripts = array();
	static $hook_registered = false;

	public static function register_hook() {
		if (self::$hook_registered) {
			return;
		}

		$scripts = &self::$scripts;

		foreach ($scripts as &$script) {
			if (!isset($script['url'])) {
				continue;
			}

			// calculate versions for scripts if they are local files
			if (!isset($script['version'])) {
				if (strpos($script['url'], get_template_directory_uri()) !== false) {
					// If Local file then get the time of when it was modified
					$file_path = str_replace(get_template_directory_uri(), get_template_directory(), $script['url']);

					if (file_exists($file_path)) {
						$script['version'] = filemtime($file_path);
					}
				} else {
					// If the value is not set to null WordPress will use it's version number as the script version
					$script['version'] = null;
				}
			}

			// add a preload tag if a preload hook is specified
			if (isset($script['preload_hook']) && $script['preload_hook']) {
				$url = $script['url'];

				if (isset($script['version'])) {
					$url .= '?ver='.$script['version'];
				}
				
				add_action($script['preload_hook'], function() use (&$url) {
					echo '<link rel="preload" href="'.$url.'" as="script">'."\n";
				});
			}
		}

		// register all the scripts
		add_action('wp_enqueue_scripts', function() use (&$scripts) {
			foreach ($scripts as $name => $params) {
				if (!isset($params['url'])) continue;
				if (!isset($params['deps'])) $params['deps'] = array();
				if (!isset($params['footer'])) $params['footer'] = true;

				wp_register_script($name, $params['url'], $params['deps'], $params['version'], $params['footer']);

				if (isset($params['localize'])) {
					wp_localize_script($name, $params['localize']['name'], $params['localize']['data']);
				}

				wp_enqueue_script($name);
			}
		});

		// add defer/async attributes if they are specified
		add_filter('script_loader_tag', function($tag, $handle) use (&$scripts) {
			$attrs = array();

			if (isset($scripts[$handle]['async']) && $scripts[$handle]['async']) {
				$attrs[] = 'async';
			}

			if (isset($scripts[$handle]['defer']) && $scripts[$handle]['defer']) {
				$attrs[] = 'defer';
			}

			if ($attrs) {
				$tag = str_replace(' src', ' '.implode(' ', $attrs).' src', $tag);
			}

			return $tag;
		}, 10, 2);

		self::$hook_registered = true;
	}

	public static function enqueue_scripts($scripts) {
		self::$scripts = array_merge(self::$scripts, $scripts);
		self::register_hook();
	}
}
