<?php
namespace Grav\WP;

class Geo
{
	/**
	 * Calculates the distance between two set of coordinate points.
	 * See http://stackoverflow.com/questions/10053358/measuring-the-distance-between-two-coordinates-in-php for more information.
	 *
	 * @param  float  $fromLat           source latitude value
	 * @param  float  $fromLng           source longitude valude
	 * @param  float  $toLat             destination latitude value
	 * @param  float  $toLng             destination longitude value
	 * @param  string  $format           return format - can be 'mi' (default) for miles or 'm' for meters
	 * @param  boolean $vincenty_formula optionally use the vincenty formula for calculation (default is the haversine formula)
	 * @return float                     distance between the two points in the specified format
	 */
	public static function distance_between_points($fromLat, $fromLng, $toLat, $toLng, $format = 'mi', $vincenty_formula = false)
	{
		// convert from degrees to radians
		$fromLat = deg2rad($fromLat);
		$fromLng = deg2rad($fromLng);
		$toLat = deg2rad($toLat);
		$toLng = deg2rad($toLng);

		$angle = 0;
		$earthRadius = ($format == 'mi') ? 3959 : 6371000;

		if ($vincenty_formula) {
			$lngDelta = $toLng - $fromLng;
			$a = pow(cos($toLat) * sin($lngDelta), 2) + pow(cos($fromLat) * sin($toLat) - sin($fromLat) * cos($toLat) * cos($lngDelta), 2);
			$b = sin($fromLat) * sin($toLat) + cos($fromLat) * cos($toLat) * cos($lngDelta);

			$angle = atan2(sqrt($a), $b);
		} else {
			$latDelta = $toLat - $fromLat;
			$lngDelta = $toLng - $fromLng;

			$angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) + cos($fromLat) * cos($toLat) * pow(sin($lngDelta / 2), 2)));
		}

		return $angle * $earthRadius;
	}

	/**
	 * Address to Geo Location (latitude, longitude) function
	 *
	 * @param  $address  (string) of Address or just ZipCode.
	 *
	 * NOTE: This is intended for single instances only. Google only allows a few requests per second.
	 *
	 * @return (object)
	 * @author GG
	 **/
	public static function address_to_location($address, $key = '')
	{
		$query_parts = array(
			'address='.str_replace(' ', '+', urlencode($address)),
			'sensor=false'
		);

		if ($key) {
			$query_parts[] = 'key='.$key;
		}

		$details_url = 'https://maps.googleapis.com/maps/api/geocode/json?'.implode('&', $query_parts);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $details_url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$response = json_decode(curl_exec($ch), true);

		// If Status Code is ZERO_RESULTS, OVER_QUERY_LIMIT, REQUEST_DENIED or INVALID_REQUEST
		if ($response['status'] != 'OK') {
			return false;
		}

		$geometry = $response['results'][0]['geometry'];

		$longitude = $geometry['location']['lng'];
		$latitude = $geometry['location']['lat'];

		$array = (object)array(
			'latitude' => $geometry['location']['lat'],
			'longitude' => $geometry['location']['lng'],
			'location_type' => $geometry['location_type'],
		);

		return $array;
	}

	/**
	 * Get Posts by Location function
	 *
	 * @param  $args  (array) of configurations to set for the function. Defaults are shown in the function.
	 *
	 * @return (array)
	 * @author GG, DF
	 *
	 **/
	public static function get_posts_by_location($args = array())
	{
		global $wpdb;

		// Set Defaults
		$from_location = (!empty($args['from_location']) ? esc_sql($args['from_location']) : '98668'); // 98668 is default
		$post_type = (!empty($args['post_type']) ? esc_sql($args['post_type']) : 'post'); // post is default
		$latitude = (!empty($args['latitude']) ? esc_sql($args['latitude']) : 'latitude'); // latitude is default
		$longitude = (!empty($args['longitude']) ? esc_sql($args['longitude']) : 'longitude'); // longitude is default
		$measurement = (!empty($args['measurement']) && $args['measurement'] == 'kilometers' ? 6371 : 3959); // 3959 = miles
		$within = (!empty($args['within']) ? esc_sql($args['within']) : 25); // 25 is default
		$from = (!empty($args['from']) ? esc_sql($args['from']) : 0); // 0 is default
		$limit = (!empty($args['limit']) ? esc_sql($args['limit']) : 999999); // 999999 is default
		$meta_query = (!empty($args['meta_query'])) ? $args['meta_query'] : array();

		if (is_array($from_location)) {
			if (isset($args['from_location']['latitude'])) {
				$from_latitude = $args['from_location']['latitude'];
				$from_longitude = $args['from_location']['longitude'];
			} else if (isset($args['from_location'][0])) {
				$from_latitude = $args['from_location'][0];
				$from_longitude = $args['from_location'][1];
			}
		} else {
			$location = self::address_to_location($from_location);
			$from_latitude = $location->latitude;
			$from_longitude = $location->longitude;
		}

		// build select clauses
		$select_clauses = array(
			"{$wpdb->posts}.*",
			"( {$measurement} * acos( cos( radians({$from_latitude}) ) * cos( radians( pm1.meta_value ) )
		  * cos( radians( pm2.meta_value ) - radians({$from_longitude}) ) + sin( radians({$from_latitude}) )
		  * sin( radians( pm1.meta_value ) ) ) ) AS distance",
	  	);

		// build join clauses
		$join_clauses = array(
			"INNER JOIN {$wpdb->postmeta} AS pm1 ON ({$wpdb->posts}.ID = pm1.post_id AND pm1.meta_key = '{$latitude}')",
			"INNER JOIN {$wpdb->postmeta} AS pm2 ON ({$wpdb->posts}.ID = pm2.post_id AND pm2.meta_key = '{$longitude}')"
		);

		// build where clauses
		$where_having_clauses = array(
			"distance < {$within}",
			"distance >= {$from}",
		);

		// add meta query clauses
		for ($i=0; $i<count($meta_query); $i++) {
			if (!is_array($meta_query[$i]) || !isset($meta_query[$i]['key']) || !isset($meta_query[$i]['value'])) {
				continue;
			}

			$key = esc_sql($meta_query[$i]['key']);
			$value = esc_sql($meta_query[$i]['value']);
			$compare = isset($meta_query[$i]['compare']) ? esc_sql($meta_query[$i]['compare']) : '=';
			$name = "mq{$i}";
			$select_clauses[] = "{$name}.meta_value AS {$key}";
			$join_clauses[] = "INNER JOIN {$wpdb->postmeta} AS {$name} ON ({$wpdb->posts}.ID = {$name}.post_id AND {$name}.meta_key = '{$key}')";
			$where_having_clauses[] = "{$key} {$compare} {$value}";
		}

		$sql = "SELECT ".implode(', ', $select_clauses)." FROM {$wpdb->posts} ".implode(' ', $join_clauses)." WHERE post_type = '{$post_type}' AND post_status = 'publish' HAVING ".implode(' AND ', $where_having_clauses)." ORDER BY distance LIMIT {$limit}";

		return $wpdb->get_results($sql);
	}

	public static function get_user_ip()
	{
		if (isset($_SERVER['HTTP_CLIENT_IP'])) {
			$client_ip = $_SERVER['HTTP_CLIENT_IP'];
		} else if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$client_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else if (isset($_SERVER['HTTP_X_REAL_IP'])) {
			$client_ip = $_SERVER['HTTP_X_REAL_IP'];
		} else {
			$client_ip = $_SERVER['REMOTE_ADDR'];
		}

		return $client_ip;
	}
}
