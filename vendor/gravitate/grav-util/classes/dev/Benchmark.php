<?php
namespace Grav\Dev;

class Benchmark {
	public static function get_microtime($func, ...$params) {
		$time_start = microtime(true);

		$func(...$params);

		$time_end = microtime(true);

		return (float)$time_end - (float)$time_start;
	}
}
