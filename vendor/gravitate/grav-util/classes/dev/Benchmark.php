<?php
namespace Grav\Dev;

class Benchmark {
	protected static $timings = array();


	public static function get_microtime($func, ...$params) {
		$time_start = microtime(true);

		$func(...$params);

		$time_end = microtime(true);

		return (float)$time_end - (float)$time_start;
	}

	public static function start($timing_id) {
		self::$timings[$timing_id] = array('start' => (float)microtime(true), 'stop' => '');
	}

	public static function stop($timing_id, $to_error_log = true) {
		if (!isset(self::$timings[$timing_id])) {
			return false;
		}

		self::$timings[$timing_id]['stop'] = (float)microtime(true);

		$total_time = self::$timings[$timing_id]['stop'] - self::$timings[$timing_id]['start'];

		if ($to_error_log) {
			error_log("{$timing_id}: {$total_time}");
		}

		return $total_time;
	}
}
