<?php
namespace Grav\Vendor;

class Instagram {
	protected $access_token = '';
	protected $cache_time = 300; // value is in seconds
	protected $last_error;
	

	public function __construct($access_token, $cache_time = 300) {
		$this->access_token = $access_token;
		$this->cache_time = $cache_time;
	}

	protected function set_last_error($msg, $details) {
		$this->last_error = (object)array(
			'message' => $msg,
			'details' => $details
		);
	}

	protected function get_last_error() {
		return $this->last_error;
	}

	public function get_feed_items($user_id, $count = 10) {
		if (!$this->access_token) {
			$this->set_last_error(__METHOD__.': access token not specified');
			
			return false;
		}

		$cache_key = "instagram_{$user_id}_{$count}";
		$data = get_transient($cache_key);

		if (!$data) {
			$url = "https://api.instagram.com/v1/users/{$user_id}/media/recent/?count={$count}&access_token={$this->access_token}";
			$result = @file_get_contents($url);
			
			if (!$result) {
				$this->set_last_error(__METHOD__.': failed to connect to the Instagram API', var_export($result, true));
				
				return false;
			}

			$data = $result;

			set_transient($cache_key, $data, $this->cache_time);
		}

		return json_decode($data);
	}
}
