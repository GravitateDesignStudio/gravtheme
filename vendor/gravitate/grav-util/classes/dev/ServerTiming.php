<?php
namespace Grav\Dev;

class ServerTiming {
	static private $proc = [];

	static public function init() {
		$procs = &self::$proc;

		add_action('send_headers', function() use (&$procs) {
			$items = [];

			foreach ($procs as $key => $values) {
				$display_name = isset($values['title']) ? $values['title'] : $key;

				if (!$values['time_total']) {
					$values['time_total'] = microtime(true) - self::$proc[$key]['time_start'];
					$display_name = '! '.$display_name;
				}

				$items[] = "{$key}={$values['time_total']}; \"{$display_name}\" ";
			}

			header('Server-Timing: '.implode(', ', $items));
		}, 9999);
	}

	static public function start($name, $title='') {
		$key = sanitize_title($name);

		if (isset(self::$proc[$key])) {
			return;
		}

		self::$proc[$key] = [
			'time_start' => microtime(true),
			'time_total' => 0,
			'title' => $title
		];
	}

	static public function stop($name) {
		$key = sanitize_title($name);

		if (!isset(self::$proc[$key])) {
			return;
		}

		self::$proc[$key]['time_total'] = microtime(true) - self::$proc[$key]['time_start'];

		return self::$proc[$key]['time_total'];
	}
}
