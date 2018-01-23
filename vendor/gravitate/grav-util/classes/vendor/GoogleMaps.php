<?php
namespace Grav\Vendor;

class GoogleMaps {
	public static $registered_maps = array();
	public static $api_key = '';
	public static $script_deps = array();
	public static $js_init_callback = 'google_maps_init';

	public static $init_done = false;


	public static function init($opts) {
		if (!isset($opts['api_key'])) {
			return;
			// throw new Exception(__CLASS__.'::'.__FUNCTION__.' - Google Maps API key must be set with "api_key" option key');
		}

		self::$api_key = $opts['api_key'];

		if (isset($opts['script_deps'])) self::$script_deps = $opts['script_deps'];
		if (isset($opts['js_init_callback'])) self::$js_init_callback = $opts['js_init_callback'];

		self::$init_done = true;

		self::register_key_for_acf(self::$api_key);
	}

	public static function register_key_for_acf($key) {
		add_filter('acf/init', function() use (&$key) {
			acf_update_setting('google_api_key', $key);
		});
	}

	public static function google_map_is_used() {
		return wp_script_is('google-maps', 'registered') ||
			wp_script_is('google-maps', 'enqueued') ||
			wp_script_is('google-maps', 'done') ||
			wp_script_is('google-maps', 'todo');
	}

	public static function enqueue_google_maps_js_script() {
		if (self::google_map_is_used()) {
			return;
		}

		if (!self::$api_key) {
			return;
			// throw new Exception(__CLASS__.'::'.__FUNCTION__.' - Google Maps API key must be set using '.self::class.'::init');
		}

		// wp_register_script('google-maps', 'https://maps.googleapis.com/maps/api/js?key='.self::$api_key.'&callback='.$opts['js_callback'], $opts['deps'], null, true);
		wp_enqueue_script('google-maps', 'https://maps.googleapis.com/maps/api/js?key='.self::$api_key.'&callback='.self::$js_init_callback, self::$script_deps, null, true);

		// if (isset($opts['pins'])) {
		// 	wp_localize_script('google-maps', 'mapPins', $opts['pins']);
		// }

		// wp_enqueue_script('google-maps');

		add_filter('script_loader_tag', function($tag, $handle, $src) {
			if ($handle == 'google-maps' && GoogleMaps::google_map_is_used()) {
				?>
				<script type='text/javascript'>
				function <?php echo GoogleMaps::$js_init_callback; ?>() {
					<?php foreach (GoogleMaps::$registered_maps as $map) { ?>
					initMap('<?php echo $map['id']; ?>', <?php echo json_encode($map['opts']); ?>, <?php echo json_encode($map['markers']); ?>);
					<?php } ?>
				}
				</script>
				<?php
			}

			return $tag;
		}, 10, 3);
	}

	public static function register_map($map_id, $opts=array(), $markers=array()) {
		if (!self::$init_done) {
			return;
			// throw new Exception(__CLASS__.'::'.__FUNCTION__.' - Google Maps class not initialized');
		}

		self::enqueue_google_maps_js_script();

		self::$registered_maps[] = array(
			'id' => $map_id,
			'opts' => $opts,
			'markers' => $markers
		);
	}
}
