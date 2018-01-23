<?php
namespace Grav\WP;

class SVGSpriteManager {
	static protected $sprites = array();

	static public function add_sprite($name, $filename) {
		if (isset(self::$sprites[$name])) {
			trigger_error(__METHOD__." - The sprite '{$name}' has already been added", E_USER_ERROR);
			return false;
		}

		if (!file_exists($filename)) {
			trigger_error(__METHOD__." - {$filename} does not exist", E_USER_ERROR);
			return false;
		}

		self::$sprites[$name] = new \Grav\WP\SVGSprite($filename);

		return self::get_sprite($name);
	}

	static public function get_sprite($name) {
		if (!isset(self::$sprites[$name])) {
			return false;
		}

		return self::$sprites[$name];
	}

	static public function get_registered_sprites() {
		return array_keys(self::$sprites);
	}
}
