<?php
####################################################
/**
 * Standard Functions for Gravitate
 *
 * Copyright (c) 2013-2016
 * Version: 1.11.0
 * Written by Brian F. and Geoff G.
 */
####################################################

class GRAV_FUNC {

	/**
	*
	* Create shortcode for menu intended for sitemap use
	*
	**/
	public static function menu_shortcode($atts, $content = null)
	{
		extract(shortcode_atts(array( 'name' => null, ), $atts));
		return wp_nav_menu( array( 'menu' => $name, 'echo' => false ) );
	}


	/**
	 * Get svg file to include from library/images/svgs
	 *
	 * Pass the name of the file as the argument minus the file extension
	 *
	 **/
	public static function get_svg($svg)
	{
		$path = get_template_directory().'/assets/images/svgs/'.$svg.'.svg';
		if(file_exists($path))
		{
			return file_get_contents($path);
		}
		elseif(file_exists($svg))
		{
			return file_get_contents($svg);
		}
		return '';
	}


	/**
	 * Create a Menu in the Template
	 *
	 * @param  $menu  (string) The name of the menu you want to use.
	 *
	 * Note: use a custom name to create a new menu. Otherwise use the default main|top|footer|sitemap|mobile
	 *
	 * @return (array)
	 * @author GG
	 **/
	public static function menu($menu='main', $class='')
	{
	    switch($menu)
	    {
	        case 'top':
	            wp_nav_menu(array(
	                'menu' => 'Top Menu', /* menu name */
	                'menu_class' => $class,
	                'theme_location' => 'top_nav', /* where in the theme it's assigned */
	                'container' => '' /* no container */
	            ));
	        break;
	        case 'footer':
	            wp_nav_menu(array(
	                'menu' => 'footer_links', /* menu name */
	                'menu_class' => $class,
	                'theme_location' => 'footer_links', /* where in the theme it's assigned */
	                'container' => '' /* no container */
	            ));
	        break;
	        case 'sitemap':
	            wp_nav_menu(array(
	                'menu' => 'SiteMap Menu', /* menu name */
	                'menu_class' => $class,
	                'theme_location' => 'sitemap_nav', /* where in the theme it's assigned */
	                'container' => '' /* no container */
	            ));
	        break;
	        case 'mobile':
	            wp_nav_menu(array(
	                'menu' => 'Mobile Menu', /* menu name */
	                'menu_class' => $class,
	                'theme_location' => 'mobile_nav', /* where in the theme it's assigned */
	                'container' => '' /* no container */
	            ));
	        break;
	        case 'main':
	            wp_nav_menu(array(
	                'menu' => 'Main Menu', /* menu name */
	                'menu_class' => $class,
	                'theme_location' => 'main_nav', /* where in the theme it's assigned */
	                'container' => '' /* no container */
	            ));
	        break;
	        default:
	        	wp_nav_menu(array(
	                'menu' => ucwords($menu), /* menu name */
	                'menu_class' => $class,
	                'theme_location' => $menu.'_nav', /* where in the theme it's assigned */
	                'container' => '' /* no container */
	            ));
	        break;
	    }
	}

	/**
	 * Check all Post Types and Taxonomies for changes.  If so then flush rewrite rules.
	 *
	 * @return (void)
	 * @author GG
	 *
	 **/
	public static function update_registered_post_types()
	{
	    $cpts = implode('',get_post_types()).implode('',get_taxonomies());
	    if(get_option( 'grav_registered_post_types' ) != $cpts)
	    {
	        flush_rewrite_rules();
	        update_option( 'grav_registered_post_types', $cpts );
	    }
	}

	/**
	 * CSV File to Array function
	 *
	 * @param  $args  (array) of configurations to set for the function. Defaults are shown in the function.
	 *
	 * @return (array)
	 * @author GG
	 **/
	public static function csv2array($args = array())
	{
		// Set Defaults
		$args['file'] = (!empty($args['file']) ? $args['file'] : ''); // Absolute Path to FIle. Required
		$args['separator'] = (!empty($args['separator']) ? $args['separator'] : ','); // Field Separator in file
		$args['has_labels'] = (!empty($args['has_labels']) ? true : false);

		// Data to Return
		$data = array();

		if(file_exists($args['file']))
		{
			if (($handle = fopen($args['file'], 'r')) !== false)
			{
				while (($row = fgetcsv($handle, 2000, $args['separator'])) !== false)
				{
					$new_data = array();

					// Set Labels
					if($args['has_labels'] && empty($labels))
					{
						$labels = $row;
						continue;
					}

					foreach($row as $num => $value)
					{
						if($args['has_labels'] && !empty($labels))
						{
							$new_data[$labels[$num]] = $value;
						}
						else
						{
							$new_data[] = $value;
						}

					}

					$data[] = $new_data;
				}
			}
		}

		return $data;
	}

	/**
	 * CSV File to Posts function
	 *
	 * @param  $args  (array) of configurations to set for the function. Defaults are shown in the function.
	 *
	 * @return (void)
	 * @author GG
	 *
	 * Requires csv2array()
	 * Labels must match WP Post Table Fields (ie. post_type, post_title, etc)
	 * Required Lables: post_title
	 * All other Fields are optional
	 *
	 * If "ID" is set in the label then it will be used to Update the
	 *
	 * PostMeta:
	 * In order to add postmeta values just prefix the labels with "postmeta_"
	 *
	 * Taxonomies:
	 * In order to add taxonomy terms just prefix the labels with "taxonomy_" followed by the taxonomy name and the value needs to be the terms "slug" separated by commas.
	 *
	 * NOTE: In order for Custom Taxonomies to work, this public static function needs to be run after the Custom Taxonomy has been Registered.
	 **/
	public static function csv2posts($args = array())
	{
		// Set Defaults
		$args['file'] = (!empty($args['file']) ? $args['file'] : ''); // Absolute Path to FIle. Required
		$args['separator'] = (!empty($args['separator']) ? $args['separator'] : ','); // Field Separator in file
		$args['has_labels'] = true; // This is Required to be true.

		// Data to Return
		$data = self::csv2array($args);

		foreach($data as $post)
		{
			if(!empty($post['post_type']))
			{
				$post = array();
				$post['post_name'] = sanitize_title(strtolower($post['post_title']));
				$post['post_status'] == (!empty($post['post_status']) ? trim($post['post_status']) : 'publish'); // Field Separator in file

				$post_id = wp_insert_post( $post );

				if($post_id)
				{
					foreach ($post as $key => $value)
					{
						// Check if Field is Post Meta
						if(strpos($key, 'postmeta_') !== false)
						{
							// Add Post Meta
							$meta_key = str_replace('postmeta_', '', trim($key));
							update_post_meta($post_id, $meta_key, $value);
						}

						// Check if Field is Taxonomy
						if(strpos($key, 'taxonomy_') !== false)
						{
							// Add Taxonomy
							$taxonomy = str_replace('postmeta_', '', trim($key));
							wp_set_post_terms( $post_id, trim(str_replace(' ', '', $value)), $taxonomy);
						}
					}
				}
			}
		}
	}

	/**
	 * Get Reall IP based on Server Settings
	 * *
	 * @return (string)
	 * @author GG
	 **/
	public static function get_real_ip()
	{
		if (!empty($_SERVER['HTTP_CLIENT_IP']))
		{
			$clientIP = $_SERVER['HTTP_CLIENT_IP'];
		}
		elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
		{
			$clientIP = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		elseif (!empty($_SERVER['HTTP_X_REAL_IP']))
		{
			$clientIP = $_SERVER['HTTP_X_REAL_IP'];
		}
		else
		{
			$clientIP = $_SERVER['REMOTE_ADDR'];
		}
		return $clientIP;
	}

	/**
	 * Uses Maxmind's API to call the location information based on IP
	 * *
	 * NOTE: This requires you to define the MAXMIND_LICENSE_KEY for the clients account.  They will need to sign up with Maxmind. This also uses the get_real_ip() public static function to get the real IP.
	 *
	 * @return (array|false)
	 * @author GG
	 **/
	public static function get_geoip_info($manual_ip=false)
	{
		$geo_info = false;

		if($manual_ip)
		{
			$geo_info = self::get_geoip_info_by_ip($manual_ip);
		}
		else
		{
			$ips = array($_SERVER['HTTP_CLIENT_IP'], $_SERVER['HTTP_X_FORWARDED_FOR'], $_SERVER['HTTP_X_REAL_IP'], $_SERVER['REMOTE_ADDR']);

			foreach ($ips as $ip)
			{
				if(!empty($ip) && filter_var($ip, FILTER_VALIDATE_IP))
				{
					$geo_info = self::get_geoip_info_by_ip($ip);

					if(!empty($geo_info['latitude']) && !empty($geo_info['longitude']))
					{
						return $geo_info;
					}
				}
			}
		}

		return $geo_info;
	}

	public static function get_geoip_info_by_ip($ip=false)
	{
		if($ip)
		{
			$real_ip = $ip;
		}
		else
		{
			$real_ip = self::get_real_ip();
		}

		$cookie_key = 'geoip_info_'.$real_ip;

		if(!empty($_COOKIE[$cookie_key]) && !$ip)
		{
			return unserialize(base64_decode($_COOKIE[$cookie_key]));
		}
		else if(!self::is_bot() && defined('MAXMIND_LICENSE_KEY'))
		{
			$params = getopt('l:i:');

			if (!isset($params['l'])) $params['l'] = MAXMIND_LICENSE_KEY;
			if (!isset($params['i'])) $params['i'] = trim(strpos($real_ip, '10.0.10.') !== false ? '173.12.186.189' : $real_ip); // Gravitates IP = 173.12.186.189

			$query = 'https://geoip.maxmind.com/f?' . http_build_query($params);

			$keys =
			  array(
				'country_code',
				'region_code',
				'city_name',
				'postal_code',
				'latitude',
				'longitude',
				'metro_code',
				'area_code',
				//'time_zone',
				//'continent_code',
				'isp_name',
				'organization_name',
				//'domain',
				//'as_number',
				//'netspeed',
				//'user_type',
				//'accuracy_radius',
				//'country_confidence',
				//'city_confidence',
				//'region_confidence',
				//'postal_confidence',
				'error'
				);

			$curl = curl_init();
			curl_setopt_array(
				$curl,
				array(
					CURLOPT_URL => $query,
					CURLOPT_USERAGENT => $_SERVER['HTTP_USER_AGENT'],
					CURLOPT_RETURNTRANSFER => true
				)
			);

			$resp = curl_exec($curl);

	        if(curl_errno($curl))
			{
				trigger_error('GeoIP request failed with a curl_errno of ' . curl_errno($curl), E_USER_WARNING);
			}

			$values = str_getcsv($resp);
			$values = array_pad($values, sizeof($keys), '');
			$results = array_combine($keys, $values);
			$results['ip'] = $params['i'];

			if(isset($results['metro_code'])) unset($results['metro_code']);
			if(isset($results['area_code'])) unset($results['area_code']);
			if(isset($results['isp_name'])) unset($results['isp_name']);
			if(isset($results['organization_name'])) unset($results['organization_name']);

			$domain = explode('.', $_SERVER['HTTP_HOST']);
			$domain = array_reverse($domain);
			$domain = $domain[1].'.'.$domain[0];

			setcookie($cookie_key, base64_encode(serialize($results)), (time()+600), '/', '.'.$domain);

			return $results;
		}
		else
		{
			return false;
		}
	}


	/**
	 * Address to Geo Location (latitude, longitude) function
	 *
	 * @param  $address  (string) of Address or just ZipCode.
	 *
	 * NOTE: This is intended for single instances only. Google only allows a few requests per second.
	 *
	 * @return (array)
	 * @author GG
	 **/
	public static function address2location($address)
	{

		$address = str_replace (" ", "+", urlencode($address));
		$details_url = "http://maps.googleapis.com/maps/api/geocode/json?address=".$address."&sensor=false";

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $details_url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$response = json_decode(curl_exec($ch), true);

		// If Status Code is ZERO_RESULTS, OVER_QUERY_LIMIT, REQUEST_DENIED or INVALID_REQUEST
		if ($response['status'] != 'OK')
		{
			return false;
		}

		$geometry = $response['results'][0]['geometry'];

		$longitude = $geometry['location']['lng'];
		$latitude = $geometry['location']['lat'];

		$array = array(
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
	 * @author GG
	 *
	 * Requires address2location()
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

		if(is_array($from_location))
		{
			if(isset($args['from_location']['latitude']))
			{
				$from_latitude = $args['from_location']['latitude'];
				$from_longitude = $args['from_location']['longitude'];
			}
			else if(isset($args['from_location'][0]))
			{
				$from_latitude = $args['from_location'][0];
				$from_longitude = $args['from_location'][1];
			}
		}
		else
		{
			$location = self::address2location($from_location);
			$from_latitude = $location['latitude'];
			$from_longitude = $location['longitude'];
		}

		$sql = "SELECT $wpdb->posts.*, ( $measurement * acos( cos( radians($from_latitude) ) * cos( radians( pm1.meta_value ) )
	  * cos( radians( pm2.meta_value ) - radians($from_longitude) ) + sin( radians($from_latitude) )
	  * sin( radians( pm1.meta_value ) ) ) ) AS distance
	  FROM $wpdb->posts
	  INNER JOIN ".$wpdb->postmeta." AS pm1 ON (".$wpdb->posts.".ID = pm1.post_id AND pm1.meta_key = '$latitude')
	  INNER JOIN ".$wpdb->postmeta." AS pm2 ON (".$wpdb->posts.".ID = pm2.post_id AND pm2.meta_key = '$longitude')
	  WHERE post_type = '$post_type' AND post_status = 'publish' HAVING distance < $within AND distance >= $from
	  ORDER BY distance LIMIT $limit";

	  return $wpdb->get_results($sql);

	}

	/**
	 * Inject Posts into a Posts Array function
	 *
	 * This functions allows you to inject miscellaneous data in a Posts array. Useful if wanting to include Users in Search page. Etc
	 * Also nice if you need to include Links to Archive Pages within a Search.
	 *
	 * @param  $post  (array) of configurations to set for each post. Use Post fields defaults (ie post_title, post_content, etc) use post_name for Link. For users you can include "user_id"
	 * @param  $placement  (string) either "front" or "end".
	 * @param  $query  (object) This is the WP_Query object to inject to. Leave empty if injecting to $wp_query.
	 *
	 * @return (void)
	 * @author GG
	 *
	 * Example Usage
	 *
	 * $custom_posts = new WP_Query($args);
	 * $new_post = array('post_title' => 'Testing', 'post_name' => 'testing', 'post_content' => 'bla bla bla');
	 * GRAV_FUNC::inject_post($new_post, 'front', $custom_posts);
	 * if (have_posts()){ while (have_posts()){ the_post();
	 *		the_title();
	 *		the_excerpt();
	 *		the_permalink();
	 * }}
	 */
	public static function inject_post($post=array(), $placement='end', $query=false)
	{
		global $wp_query;

		if(is_array($post))
		{
			$item = new stdClass();
			if(!$query) $query = $wp_query;

			$item->ID = ($post['ID'] ? $post['ID'] : 999999);
			$item->post_name = $_gds_globals['url'].($post['post_name'] ? $post['post_name'] : ($post['post_title'] ? strtolower(str_replace(' ', '-', $post['post_title'])) : 'Posts'));
			$item->post_title = ($post['post_title'] ? $post['post_title'] : 'Posts');
			$item->post_content = ($post['post_content'] ? $post['post_content'] : 'List of all of our '.$post['title']);
			if(isset($post['user_id']))
			{
				$item->user_id = $post['user_id'];
			}
			if(isset($post['post_type']))
			{
				$item->post_type = $post['post_type'];
			}
			$item->post_status = 'publish';
		}
		else
		{
			$item = $post;
		}

		if($placement == 'front')
		{
			array_unshift($query->posts, $item);
		}
		else
		{
			$query->posts[] = $item;
		}
		$query->post_count++;
	}

	/**
	 * Checks whether User has a specific Role function
	 *
	 * @param  $role  (string) role name.
	 * @param  $user_id  (int) (Optional) Check against another User. If Blank then Checks Current User.
	 *
	 * @return (array)
	 * @author GG
	 *
	 **/
	public static function user_has_role( $role, $user_id = null )
	{
	    if ( is_numeric( $user_id ) )
		{
			$user = get_userdata( $user_id );
		}
	    else
		{
	        $user = wp_get_current_user();
		}

	    if ( empty( $user ) ) return false;

	    return in_array( $role, (array) $user->roles );
	}


	/**
	 * Display calendar with days that have posts as links.
	 *
	 * The calendar is cached, which will be retrieved, if it exists. If there are
	 * no posts for the month, then it will not be displayed.
	 *
	 * @param string $post_type
	 * @param string $post_meta_date - The Meta key you want to compare against. This must be in the datatime format (ie 2013-12-25 08:15:55)
	 * @return string|null String when retrieving, null when displaying.
	 */
	public static function get_post_calendar($post_type='post', $post_meta_date='post_date')
	{
		global $wpdb, $m, $monthnum, $year, $wp_locale, $posts;

		$field = (!empty($post_meta_date) && $post_meta_date != 'post_date' ? 'meta_value' : 'post_date');
		$linkage = ($field == 'post_date' ? " WHERE ".$field." " : ", ".$wpdb->postmeta." WHERE post_id = ID AND meta_key = '".$post_meta_date."' AND ".$field." " );

		$cache = array();
		$key = md5( $m . $monthnum . $year );
		if ( $cache = wp_cache_get( 'get_calendar', 'calendar' ) )
		{
			if ( is_array($cache) && isset( $cache[ $key ] ) )
			{
				return apply_filters( 'get_calendar',  $cache[$key] );
			}
		}

		if ( !is_array($cache) )
			$cache = array();

		// Quick check. If we have no posts at all, abort!
		if ( !$posts ) {
			$gotsome = $wpdb->get_var("SELECT 1 as test FROM $wpdb->posts WHERE post_type = '".$post_type."' AND post_status = 'publish' LIMIT 1");
			if ( !$gotsome ) {
				$cache[ $key ] = '';
				wp_cache_set( 'get_calendar', $cache, 'calendar' );
				return;
			}
		}

		if ( isset($_GET['w']) )
			$w = ''.intval($_GET['w']);

		// week_begins = 0 stands for Sunday
		$week_begins = intval(get_option('start_of_week'));

		// Let's figure out when we are
		if(!empty($_GET['cy']) && !empty($_GET['cm']))
		{
			$thisyear = $_GET['cy'];
			$thismonth = $_GET['cm'];
		}
		elseif ( !empty($monthnum) && !empty($year) ) {
			$thismonth = ''.zeroise(intval($monthnum), 2);
			$thisyear = ''.intval($year);
		} elseif ( !empty($w) ) {
			// We need to get the month from MySQL
			$thisyear = ''.intval(substr($m, 0, 4));
			$d = (($w - 1) * 7) + 6; //it seems MySQL's weeks disagree with PHP's
			$thismonth = $wpdb->get_var("SELECT DATE_FORMAT((DATE_ADD('{$thisyear}0101', INTERVAL $d DAY) ), '%m')");
		} elseif ( !empty($m) ) {
			$thisyear = ''.intval(substr($m, 0, 4));
			if ( strlen($m) < 6 )
					$thismonth = '01';
			else
					$thismonth = ''.zeroise(intval(substr($m, 4, 2)), 2);
		} else {
			$thisyear = gmdate('Y', current_time('timestamp'));
			$thismonth = gmdate('m', current_time('timestamp'));
		}

		$unixmonth = mktime(0, 0 , 0, $thismonth, 1, $thisyear);
		$last_day = date('t', $unixmonth);

		// Get the next and previous month and year with at least one post
		$previous = $wpdb->get_row("SELECT MONTH(".$field.") AS month, YEAR(".$field.") AS year
			FROM $wpdb->posts".$linkage."< '$thisyear-$thismonth-01'
			AND post_type = '".$post_type."' AND post_status = 'publish'
				ORDER BY ".$field." DESC
				LIMIT 1");
		$next = $wpdb->get_row("SELECT MONTH(".$field.") AS month, YEAR(".$field.") AS year
			FROM $wpdb->posts".$linkage."> '$thisyear-$thismonth-{$last_day} 23:59:59'
			AND post_type = '".$post_type."' AND post_status = 'publish'
				ORDER BY ".$field." ASC
				LIMIT 1");

		/* translators: Calendar caption: 1: month name, 2: 4-digit year */
		$calendar_caption = _x('%1$s %2$s', 'calendar caption');
		$calendar_output = '
		<input type="hidden" name="cy" value="'.$thisyear.'">
		<input type="hidden" name="cm" value="'.$thismonth.'">
		<table id="wp-calendar">

		<thead>
		<tr class="thead-top-row">';

		if ( $previous ) {
			$calendar_output .= "\n\t\t".'<td colspan="1" id="prev"><a href="?cy=' . $previous->year.'&amp;cm='.$previous->month . '" title="' . esc_attr( sprintf(__('View posts for %1$s %2$s'), $wp_locale->get_month($previous->month), date('Y', mktime(0, 0 , 0, $previous->month, 1, $previous->year)))) . '">&lt;</a></td>';
		} else {
			$calendar_output .= "\n\t\t".'<td colspan="1" id="prev" class="pad">&nbsp;</td>';
		}

		$calendar_output .= "\n\t\t".'<td class="month" colspan="5">' . $wp_locale->get_month($thismonth) . '</td>';

		if ( $next ) {
			$calendar_output .= "\n\t\t".'<td colspan="1" id="next"><a href="?cy=' . $next->year.'&amp;cm='.$next->month . '" title="' . esc_attr( sprintf(__('View posts for %1$s %2$s'), $wp_locale->get_month($next->month), date('Y', mktime(0, 0 , 0, $next->month, 1, $next->year))) ) . '">&gt;</a></td>';
		} else {
			$calendar_output .= "\n\t\t".'<td colspan="1" id="next" class="pad">&nbsp;</td>';
		}

		$calendar_output .= '
		</tr>
		<tr class="thead-bottom-row">';

		$myweek = array();

		for ( $wdcount=0; $wdcount<=6; $wdcount++ ) {
			$myweek[] = $wp_locale->get_weekday(($wdcount+$week_begins)%7);
		}

		foreach ( $myweek as $wd ) {
			$day_name = $wp_locale->get_weekday_abbrev($wd);
			$wd = esc_attr($wd);
			$calendar_output .= "\n\t\t<th scope=\"col\" title=\"$wd\">".substr($day_name, 0, 1).'<span class="calendar-weekday-abbreviation">'.substr($day_name, 1).'</span></th>';
		}

		$calendar_output .= '
		</tr>
		</thead>
		<tbody>
		<tr>';

		// Get days with posts
		$dayswithposts = $wpdb->get_results("SELECT DISTINCT DAYOFMONTH(".$field.")
			FROM $wpdb->posts".$linkage.">= '{$thisyear}-{$thismonth}-01 00:00:00'
			AND post_type = '".$post_type."' AND post_status = 'publish'
			AND ".$field." <= '{$thisyear}-{$thismonth}-{$last_day} 23:59:59'", ARRAY_N);
		if ( $dayswithposts ) {
			foreach ( (array) $dayswithposts as $daywith ) {
				$daywithpost[] = $daywith[0];
			}
		} else {
			$daywithpost = array();
		}

		if (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false || stripos($_SERVER['HTTP_USER_AGENT'], 'camino') !== false || stripos($_SERVER['HTTP_USER_AGENT'], 'safari') !== false)
			$ak_title_separator = "\n";
		else
			$ak_title_separator = ', ';

		$ak_titles_for_day = array();
		$ak_ids_for_day = array();
		$ak_post_titles = $wpdb->get_results("SELECT ID, post_title, DAYOFMONTH(".$field.") as dom "
			."FROM $wpdb->posts".$linkage.">= '{$thisyear}-{$thismonth}-01 00:00:00' "
			."AND ".$field." <= '{$thisyear}-{$thismonth}-{$last_day} 23:59:59' "
			."AND post_type = '".$post_type."' AND post_status = 'publish'"
		);

		if ( $ak_post_titles )
		{
			foreach ( (array) $ak_post_titles as $ak_post_title )
			{
				/** This filter is documented in wp-includes/post-template.php */
				$post_title = esc_attr( apply_filters( 'the_title', $ak_post_title->post_title, $ak_post_title->ID ) );

				if ( empty($ak_titles_for_day['day_'.$ak_post_title->dom]) )
				{
					$ak_titles_for_day['day_'.$ak_post_title->dom] = '';
					$ak_ids_for_day['day_'.$ak_post_title->dom] = '';
				}

				if ( empty($ak_titles_for_day["$ak_post_title->dom"]) ) // first one
				{
					$ak_titles_for_day["$ak_post_title->dom"] = $post_title;
					$ak_ids_for_day["$ak_post_title->dom"] = $ak_post_title->ID;
				}
				else
				{
					$ak_titles_for_day["$ak_post_title->dom"] .= $ak_title_separator . $post_title;
				}
			}
		}

		// See how much we should pad in the beginning
		$pad = calendar_week_mod(date('w', $unixmonth)-$week_begins);
		if ( 0 != $pad )
			$calendar_output .= "\n\t\t".'<td colspan="'. esc_attr($pad) .'" class="pad">&nbsp;</td>';

		$daysinmonth = intval(date('t', $unixmonth));
		for ( $day = 1; $day <= $daysinmonth; ++$day )
		{

			if ( isset($newrow) && $newrow )
				$calendar_output .= "\n\t</tr>\n\t<tr>\n\t\t";
			$newrow = false;

			if ( $day == gmdate('j', current_time('timestamp')) && $thismonth == gmdate('m', current_time('timestamp')) && $thisyear == gmdate('Y', current_time('timestamp')) )
				$calendar_output .= '<td id="today">';
			else
				$calendar_output .= '<td'.(in_array($day, $daywithpost) ? ' class="event"' : '').'>';

			if ( in_array($day, $daywithpost) ) // any posts today?
			{
				$event_class = '';
				$calendar_output .= '<a'.$event_class.' href="' . get_permalink( $ak_ids_for_day[ $day ] ) . '" title="' . esc_attr( $ak_titles_for_day[ $day ] ) . "\">$day</a>";
			}
			else
			{
				$calendar_output .= $day;
			}
			$calendar_output .= '</td>';

			if ( 6 == calendar_week_mod(date('w', mktime(0, 0 , 0, $thismonth, $day, $thisyear))-$week_begins) )
				$newrow = true;
		}

		$pad = 7 - calendar_week_mod(date('w', mktime(0, 0 , 0, $thismonth, $day, $thisyear))-$week_begins);
		if ( $pad != 0 && $pad != 7 )
			$calendar_output .= "\n\t\t".'<td class="pad" colspan="'. esc_attr($pad) .'">&nbsp;</td>';

		$calendar_output .= "\n\t</tr>\n\t</tbody>\n\t</table>";

		$cache[ $key ] = $calendar_output;
		wp_cache_set( 'get_calendar', $cache, 'calendar' );

		return apply_filters( 'get_calendar',  $calendar_output );

	}

	/**
	 * Sort a multi Demensional Array OR Object function
	 *
	 * @param  $array  (array) Array/Object to sort and Return.
	 * @param  $key  (string) the key or field to sort by.
	 * @param  $dir  (string) The order in which to sort (ASC | DESC).
	 *
	 * @return (array)
	 * @author GG
	 *
	 **/
	public static function msort($array = array(), $key="id", $dir="ASC") {
		$temp_array = array();
		sort($array);
		while(count($array)>0)
		{
			$lowest_id = 0;
			$index=0;
			foreach ($array as $item) {
				if(substr(strtolower($dir),0,1) == "a"){
					if(is_object($item))
					{
						if (strtolower($item->$key)<strtolower($array[$lowest_id]->$key)) {
							$lowest_id = $index;
						}
					}
					else
					{
						if (strtolower($item[$key])<strtolower($array[$lowest_id][$key])) {
							$lowest_id = $index;
						}
					}

				}else{
					if(is_object($item))
					{
						if (strtolower($item->$key)>strtolower($array[$lowest_id]->$key)) {
							$lowest_id = $index;
						}
					}
					else
					{
						if (strtolower($item[$key])>strtolower($array[$lowest_id][$key])) {
							$lowest_id = $index;
						}
					}
				}
				$index++;
			}
			$temp_array[] = $array[$lowest_id];
			$array = array_merge(array_slice($array, 0,$lowest_id), array_slice($array, $lowest_id+1));
		}
		return $temp_array;
	}

	/**
	 * Check if Device is a Mobile Device function
	 *
	 * NOTE: This needs work.  It only checks if Android or Iphone as of now.
	 *
	 * @return (boolean)
	 * @author GG
	 *
	 **/
	public static function is_mobile()
	{
		$agent = strtolower($_SERVER['HTTP_USER_AGENT']);
		if(strpos($agent, 'android') || strpos($agent, 'iphone'))
		{
			return true;
		}
		return false;
	}

	/**
	 * Check if client is a bot or spam function
	 *
	 * NOTE: This needs work.  It only checks a few types.
	 *
	 * @return (boolean)
	 * @author GG
	 *
	 **/
	public static function is_bot()
	{
	    $bots = array("Bot", "Teoma", "alexa", "froogle", "Gigabot", "inktomi",
	    "looksmart", "URL_Spider_SQL", "Firefly", "NationalDirectory",
	    "Ask Jeeves", "TECNOSEEK", "InfoSeek", "WebFindBot", "girafabot",
	    "crawler", "www.galaxy.com", "Googlebot", "Scooter", "Slurp",
	    "msnbot", "appie", "FAST", "WebBug", "Spade", "ZyBorg", "rabaz",
	    "Baiduspider", "Feedfetcher-Google", "TechnoratiSnoop", "Rankivabot",
	    "Mediapartners-Google", "Sogou web spider", "WebAlta Crawler","TweetmemeBot",
	    "Butterfly","Twitturls","Me.dium","Twiceler", "WordPress");

	    foreach($bots as $bot)
	    {
	    	if(stripos($_SERVER['HTTP_USER_AGENT'], $bot) !== false)
			{
	       		return true;
			}
	    }

	    return false;
	}

	/**
	 * Converts a URL to a verified Vimeo ID
	 *
	 * @param  $url  (string) Url of a defined Vimeo Video.
	 *
	 * @return (int)
	 * @author GG
	 *
	 **/
	public static function get_vimeo_id($url)
	{
		preg_match('/([0-9]+)/', $url, $matches);

		if(!empty($matches[1]) && is_numeric($matches[1]))
		{
			return $matches[1];
		}
		else if(!$pos && strpos($url, 'http') === false)
		{
			return $url;
		}

		return 0;
	}

	/**
	 * onverts a URL to a verified YouTube ID
	 *
	 * @param  $url  (string) Url of a defined Youtube Video.
	 *
	 * @return (int)
	 * @author GG
	 *
	 **/
	public static function get_youtube_id($url)
	{
		if(!$pos = strpos($url, 'youtu.be/'))
		{
			$pos = strpos($url, '/watch?v=');
		}

		if($pos)
		{
			$split = explode("?", substr($url, ($pos+9)));
			$split = explode("&", $split[0]);
			return $split[0];
		}
		else if($pos = strpos($url, '/embed/'))
		{
			$split = explode("?", substr($url, ($pos+7)));
			return $split[0];
		}
		else if($pos = strpos($url, '/v/'))
		{
			$split = explode("?", substr($url, ($pos+3)));
			return $split[0];
		}
		else if(!$pos && strpos($url, 'http') === false)
		{
			return $url;
		}

		return 0;
	}

	/**
	 * Converts a URL to a verified YouTube video ID function
	 *
	 * @param  $url  (string) Url of a defined Youtube Video.
	 *
	 * REQUIRES: get_youtube_id()
	 *
	 * @return (str)
	 * @author GG
	 *
	 **/
	public static function filter_video_url($url)
	{

		$autoplay = 1;

		if(strpos($url, 'autoplay=0') || strpos($url, 'autoplay=false'))
		{
			$autoplay = 0;
		}

		if(strpos($url, 'vimeo'))
		{
			$id = self::get_vimeo_id($url);

			if(is_numeric($id))
			{
				return 'https://player.vimeo.com/video/'.$id.'?autoplay='.$autoplay;
			}
			return $url;
		}

		$id = self::get_youtube_id($url);

		if($id)
		{
			$link = 'https://www.youtube.com/embed/'.$id.'?rel=0&amp;iframe=true&amp;wmode=transparent&amp;autoplay='.$autoplay;

			return $link;
		}

		return '';
	}

	/**
	 * Converts a String to its Singular Format function
	 *
	 * @param  $value  (string) String to be converted.
	 *
	 * @return (str)
	 * @author GG
	 *
	 **/
	public static function get_singular($value)
	{
		if(substr($value, -3) == 'IES')
		{
			return substr($value, 0, -3) . 'Y';
		}
		else if(substr($value, -3) == 'ies')
		{
			return substr($value, 0, -3) . 'y';
		}
		else if(substr($value, -1) == 'S' || substr($value, -1) == 's')
		{
			return substr($value, 0, -1);
		}
	}


	/**
	 * Converts a String to its Plural Format function
	 *
	 * @param  $value  (string) String to be converted.
	 *
	 * @return (str)
	 * @author GG
	 *
	 **/
	public static function get_plural($value)
	{
		if(substr($value, -1) == 'Y')
		{
			return substr($value, 0, -1) . 'IES';
		}
		else if(substr($value, -1) == 'y')
		{
			return substr($value, 0, -1) . 'ies';
		}
		else if(substr($value, -1) == 'S')
		{
			return substr($value, 0, -1) . 'ES';
		}
		else if(substr($value, -1) == 's')
		{
			return substr($value, 0, -1) . 'es';
		}
		else if(substr($value, -1) != 'S' || substr($value, -1) != 's')
		{
			if(function_exists('ctype_upper') && ctype_upper($value))
			{
				return $value . 'S';
			}
			return $value . 's';
		}
	}

	public static function excerpt($new_length = 20)
	{
	  add_filter('excerpt_length', function () use ($new_length) {
	    return $new_length;
	  }, 11);
	  the_excerpt();
	}

	/**
	 * Displays Debug info on a variable
	 *
	 * @param  $var  (mixed)
	 * @param  $show_types  (bool)
	 * @param  $refurn  (bool)
	 *
	 * @return (mixed)
	 * @author GG
	 *
	 **/
	public static function debug($var, $show_types = false, $return = false)
	{
		$trace = '';
		if($dbtrace = debug_backtrace())
		{
			if(!empty($dbtrace[0]['file']) && !empty($dbtrace[0]['line']))
			{
				$trace = "\n############################################\n".
				         $dbtrace[0]['file']." [".$dbtrace[0]['line']."]".
						 "\n############################################\n";
			}
		}

		if($return)
		{
			if($show_types)
			{
				ob_start();
				var_dump($var);
				$result = ob_get_clean();
				return $trace.$result;
			}
			return $trace.print_r($var, true);
		}

		echo '<pre>';
		echo $trace;
		if($show_types)
		{
			var_dump($var);
		}
		else
		{
			print_r($var);
		}
	    echo '</pre>';

		return '';

	}

	// Search Form
	public static function get_wp_search_form()
	{
	    $form = '<form role="search" method="get" name="searchform" action="' . home_url( '/' ) . '" >
	    <label class="screen-reader-text" for="s">' . __('Search for:', 'bonestheme') . '</label>
	    <input type="text" value="' . get_search_query() . '" name="s" class="s" placeholder="Search the Site..." />
	    <input type="submit" class="searchsubmit" value="'. esc_attr__('Search') .'" />
	    </form>';
	    return $form;
	}

	/**
	 * Returns an Array of Related Posts by Tags.
	 * Returns in order of highest matches.
	 *
	 * @param  $post_id  (int)
	 * @param  $total  (int)
	 *
	 * @return (mixed)
	 * @author GG
	 *
	 **/
	function get_related_posts_by_tags($post_id='', $total=3)
	{
		if(empty($post_id))
		{
			$post_id = get_the_ID();
		}

	    $this_post = $post_id;
	    $posttags = get_the_tags($post_id);

		if($posttags)
		{
		    $ids = array();
			$relatedpostids = array();

			foreach ($posttags as $tag)
			{
			    $id =  $tag -> term_id;
			    $ids[] = $id;
			}

			foreach ($ids as $id)
			{
			    $args = array (
					'tag_id' => $id,
					'numberposts' => 3,
					'post__not_in' => array($this_post)
			    );

				$posts_array = get_posts( $args );
				foreach( $posts_array as $post )
				{
			  		$relatedpostids[$post->ID] = (isset($relatedpostids[$post->ID]) ? ($relatedpostids[$post->ID]+1) : 1);
				}
			}
		}

		if(!empty($relatedpostids))
		{
	        // Sort posts with the highest matches first
	    	arsort($relatedpostids);

			$args = array(
				'post_type' => 'post',
				'posts_per_page' => '$total',
				'post__in' => array_keys(array_slice($relatedpostids, 0, $total, true))
			);

			return get_posts($args);
	    }

	    return array();
	}


	///////////////////////////
	// Function to CHECK INTERSECTION OF TERMS ACROSS 2 TAXONOMIES
	///////////////////////////
	public static function intersected_terms( $tax, $term, $joined_tax ) {

	    global $wpdb;

	    $term_from = get_term_by( 'slug', $term, $tax );
	    $tax_to = esc_sql($joined_tax);

	    $query = "
		    SELECT term_id FROM {$wpdb->term_taxonomy} WHERE taxonomy = '{$tax_to}' AND term_taxonomy_id IN (
		        SELECT term_taxonomy_id FROM {$wpdb->term_relationships} WHERE object_id IN (
		            SELECT object_id FROM {$wpdb->term_relationships}
		            INNER JOIN {$wpdb->posts}
		        	ON {$wpdb->term_relationships}.object_id = {$wpdb->posts}.ID
		        	WHERE term_taxonomy_id = {$term_from->term_taxonomy_id}
		        	AND {$wpdb->posts}.post_status = 'publish'
		        )
		    )
	    ";

	    $term_ids = $wpdb->get_col( $query );

	    if( empty( $term_ids) )
	        return array();

	    return get_terms( $joined_tax, array( 'include' => $term_ids ) );
	}


	// clean up wordpress head output (we don't need all this usually)
	public static function theme_default_init()
	{
		// remove header links

		// these two are for RSS feeds - only uncomment if you don't want RSS
		//remove_action( 'wp_head', 'feed_links_extra', 3 );                 // Category Feeds
		//remove_action( 'wp_head', 'feed_links', 2 );                       // Post and Comment Feeds

		remove_action( 'wp_head', 'rsd_link' );                               // EditURI link
		remove_action( 'wp_head', 'wlwmanifest_link' );                       // Windows Live Writer
		remove_action( 'wp_head', 'index_rel_link' );                         // index link
		remove_action( 'wp_head', 'parent_post_rel_link', 10, 0 );            // previous link
		remove_action( 'wp_head', 'start_post_rel_link', 10, 0 );             // start link
		remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0 ); // Links for Adjacent Posts
		remove_action( 'wp_head', 'wp_generator' );                           // WP version

		// Check and Update Permalinks
		self::update_registered_post_types();
	}

	public static function custom_excerpt($content='', $word_limit=55, $elipsis='...', $highlight_word='')
	{
		$content = strip_tags($content);
		$words = explode(' ', $content, $word_limit + 1);
		if(count($words) > $word_limit)
		{
			array_pop($words);
			array_push($words, $elipsis);
			$content = implode(' ', $words);
		}

		if($read_more_link)
		{
			$content.= '<a href="'.$read_more_link.'">';
		}

	}

	// Numeric Page Navi, pass a custom query object if using a custom query
	public static function page_navi($before = '', $after = '', &$custom_query=null)
	{
		global $wpdb, $wp_query;

		if(isset($custom_query) && !is_null($custom_query)) {
		    $wp_query = $custom_query;
		}

		$request = $wp_query->request;
		$posts_per_page = intval(get_query_var('posts_per_page'));
		$paged = intval(get_query_var('paged'));
		$numposts = $wp_query->found_posts;
		$max_page = $wp_query->max_num_pages;
		if ( $numposts <= $posts_per_page ) { return; }
		if(empty($paged) || $paged == 0) {
			$paged = 1;
		}
		$pages_to_show = 7;
		$pages_to_show_minus_1 = $pages_to_show-1;
		$half_page_start = floor($pages_to_show_minus_1/2);
		$half_page_end = ceil($pages_to_show_minus_1/2);
		$start_page = $paged - $half_page_start;
		if($start_page <= 0) {
			$start_page = 1;
		}
		$end_page = $paged + $half_page_end;
		if(($end_page - $start_page) != $pages_to_show_minus_1) {
			$end_page = $start_page + $pages_to_show_minus_1;
		}
		if($end_page > $max_page) {
			$start_page = $max_page - $pages_to_show_minus_1;
			$end_page = $max_page;
		}
		if($start_page <= 0) {
			$start_page = 1;
		}
		echo $before.'<nav class="page-navigation"><ol class="grav_page_navi clearfix">'."";
		if ($start_page >= 2 && $pages_to_show < $max_page) {
			$first_page_text = "First";
			echo '<li class="bpn-first-page-link"><a href="'.get_pagenum_link().'" title="'.$first_page_text.'">'.$first_page_text.'</a></li>';
		}
		echo '<li class="bpn-prev-link">';
		previous_posts_link('<<');
		echo '</li>';
		for($i = $start_page; $i  <= $end_page; $i++) {
			if($i == $paged) {
				echo '<li class="bpn-current">'.$i.'</li>';
			} else {
				echo '<li><a href="'.get_pagenum_link($i).'">'.$i.'</a></li>';
			}
		}
		echo '<li class="bpn-next-link">';
		next_posts_link('>>');
		echo '</li>';
		if ($end_page < $max_page) {
			$last_page_text = "Last";
			echo '<li class="bpn-last-page-link"><a href="'.get_pagenum_link($max_page).'" title="'.$last_page_text.'">'.$last_page_text.'</a></li>';
		}
		echo '</ol></nav>'.$after."";
	}

	// remove the p from around imgs (http://css-tricks.com/snippets/wordpress/remove-paragraph-tags-from-around-images/)
	public static function filter_ptags_on_images($content)
	{
	    $content = preg_replace('/<p>\s*(<a .*>)?\s*(<img .* \/>)\s*(<\/a>)?\s*<\/p>/iU', '\1\2\3', $content);
	    return preg_replace('/<p>\s*(<iframe .*>*.<\/iframe>)\s*<\/p>/iU', '\1', $content);
	}

	public static function get_social_share_link($social, $twitter_screen_name='')
	{
		switch($social)
		{
			case 'facebook':
				return "window.open('https://www.facebook.com/sharer/sharer.php?u='+encodeURIComponent(location.href),'facebookShare','width=626,height=436');return false;";
			break;

			case 'twitter':
				return "window.open('https://twitter.com/share?text=".get_the_title()."&url='+encodeURIComponent(location.href)+'&via=".$twitter_screen_name."','twitterShare','width=626,height=436');return false;";
			break;

			case 'googleplus':
				return "window.open('https://plus.google.com/share?url='+encodeURIComponent(location.href),'googlePlusShare','width=626,height=436');return false;";
			break;

			case 'pinterest':
				return "window.open('https://www.pinterest.com/pin/create/button/?url='+encodeURIComponent(location.href)+'&media=&description=".get_the_title()."','pinterestShare','width=626,height=436');return false;";
			break;

			case 'linkedin':
				return "window.open('https://www.linkedin.com/cws/share?url='+encodeURIComponent(location.href),'linkedinShare','width=626,height=436');return false;";
			break;
		}
	}

	public static function mce_formats( $settings ) {

	    $style_formats = array(
	        array(
	            'title' => 'Button',
	            'selector' => 'a',
	            'classes' => 'button'
	        ),
	    );

	    $settings['style_formats_merge'] = true;
	    $settings['style_formats'] = json_encode( $style_formats );
		$settings['paste_as_text'] = true;

	    return $settings;
	}


	public static function register_sidebar1()
	{
	    register_sidebar(array(
	        'id' => 'sidebar1',
	        'name' => 'Primary Sidebar',
	        'description' => 'The first (primary) sidebar.',
	        'before_widget' => '<div id="%1$s" class="widget %2$s">',
	        'after_widget' => '</div>',
	        'before_title' => '<h4 class="widgettitle">',
	        'after_title' => '</h4>',
	    ));
	}

	// Adding WP 3+ Functions & Theme Support
	public static function theme_support()
	{
		add_theme_support('post-thumbnails');      		// wp thumbnails (sizes handled in functions.php)
		set_post_thumbnail_size(300, 300, true);   		// default thumb size

		// Add New Image Sizes
		add_image_size('small', 300, 300, false);
		add_image_size('xlarge', 1440, 1900, false);

		add_theme_support( 'automatic-feed-links'); 		// rss thingy
		add_theme_support( 'menus' );            		// wp menus
		add_theme_support( 'custom-logo');				// adds ability for custom logo in customizer
		register_nav_menus(                      		// wp3+ menus
	        array(
	            'main_nav' => 'The Main Menu',   		// main nav in header
	            'mobile_nav' => 'The Mobile Menu',   	// Mobile nav in header
	            'top_nav' => 'The Top Menu',   			// top right nav in header
	            'footer_links' => 'Footer Links', 		// secondary nav in footer
	            'site_map' => 'Site Map Links' 			// Sitemap Links
	        )
	    );
	}

	public static function enqueue_file($script_name, $script_file)
	{
    	$cache_var = 0;

    	if(strpos($script_file, get_template_directory_uri()) !== false) // If Local file then get the time of when it was modified
    	{
        	$file_path = str_replace(get_template_directory_uri(), get_template_directory(), $script_file);

            if(file_exists($file_path))
            {
                $cache_var = filemtime($file_path);
            }
        }

    	if(strpos($script_file, '.js'))
        {
        	wp_enqueue_script($script_name, $script_file, array('jquery'), $cache_var, true);
        }
        else if(strpos($script_file, '.css') || strpos($script_file, '/css'))
        {
        	wp_enqueue_style($script_name, $script_file, array(), $cache_var);
        }
	}

	public static function _get($value)
	{
		if(isset($_GET[$value]))
		{
			return $_GET[$value];
		}
		return '';
	}

	public static function _post($value)
	{
		if(isset($_POST[$value]))
		{
			return $_POST[$value];
		}
		return '';
	}

	public static function _request($value)
	{
		if(isset($_REQUEST[$value]))
		{
			return $_REQUEST[$value];
		}
		return '';
	}

	/*
	*
	* Get array of created gravity forms
	*
	*/
	public static function get_gravity_forms()
	{
		$form_list = array();

		if(class_exists('RGFormsModel'))
		{
			$forms = RGFormsModel::get_forms( null, 'title' );

			foreach($forms as $form){
				$form_list[$form->id] = $form->title;
			}
		}

		return $form_list;
	}

	public static function update_postmeta_with_s3($bucket='', $region='', $overwrite=false)
	{
	    global $wpdb;

	    if($bucket)
	    {
	        $meta_values = $wpdb->get_results("SELECT * FROM ".$wpdb->postmeta." WHERE meta_key = '_wp_attached_file'");

	        if(!empty($meta_values))
	        {
	            foreach ($meta_values as $meta)
	            {
	                if(!empty($meta->post_id))
	                {
	                    if($overwrite || !get_post_meta($meta->post_id, 'amazonS3_info', true )) // If does not currently have amazonS3_info then add it
	                    {
	                        update_post_meta($meta->post_id, 'amazonS3_info', array('bucket' => $bucket, 'key' => $meta->meta_value, $region));
	                        echo 'Updated '.$meta->meta_value.' from the S3 Bucket<br>';
	                    }
	                }
	            }
	        }
	    }
	}


	/*
	* Function to get thumbnail
	* @param  $size, $post_id
	*
	* @return url of thumbnail image size
	* @author BF
	 */
	public static function get_thumbnail_url($size = 'thumbnail', $post_id = 0)
	{
	    $post_id = ($post_id != 0) ? $post_id : get_the_ID();
	    $thumb = wp_get_attachment_image_src( get_post_thumbnail_id($post_id), $size );
	    $url = ($thumb) ? $thumb['0'] : false;
	    return $url;
	}

	public static function template_include($template)
	{
		$template_dir = get_template_directory() . '/templates';

	    if(is_front_page() && file_exists($template_dir.'/home.php'))
	    {
	        return $template_dir.'/home.php';
	    }
	    elseif(is_home() && file_exists($template_dir.'/blog.php'))
	    {
	        return $template_dir.'/blog.php';
	    }
	    elseif(is_archive() && file_exists($template_dir.'/archive.php'))
	    {
	        return $template_dir.'/archive.php';
	    }
	    elseif(is_search() && file_exists($template_dir.'/search.php'))
	    {
	        return $template_dir.'/search.php';
	    }
	    elseif(is_404() && file_exists($template_dir.'/404.php'))
	    {
	        return $template_dir.'/404.php';
	    }
		elseif(is_page() && is_singular() && file_exists($template_dir.'/_default.php'))
	    {
	        return $template_dir.'/_default.php';
	    }
	    else

		if(file_exists($template_dir.'/_default.php'))
		{
	    	return $template_dir.'/_default.php';
		}

	}

	public static function lock_w3tc_settings_pages()
	{
		// Disable Access to W3 Total Cache
		if(isset($_GET['page']) && strpos($_GET['page'], 'w3tc_') !== false && $_GET['page'] != 'w3tc_dashboard')
		{
		  echo 'Sorry, This Plugin is Locked by Gravitate. <br><br><a href="javascript:history.go(-1);">< Back</a>';
		  exit;
		}
	}

	public static function wp_loaded()
	{
		///////////////////////////////////////////////////
		// Register ACF Fields
		///////////////////////////////////////////////////

	    if(function_exists("register_field_group"))
	    {
	        foreach( glob( get_template_directory() . '/acf/*' ) as $file )
	        {
	        	include_once($file);
	        }
	    }
	}

}
