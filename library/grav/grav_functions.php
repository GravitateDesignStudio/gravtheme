<?php
####################################################
/**
 * Standard Functions for Gravitate
 *
 * Copyright (c) 2013-2014
 * Version: 1.0.1
 */
####################################################


/**
 * CSV File to Array function
 *
 * @param  $args  (array) of configurations to set for the function. Defaults are shown in the function.
 *
 * @return (array)
 * @author GG
 **/
function grav_csv2array($args = array())
{
	// Set Defaults
	$args['file'] == (!empty($args['file']) ? $args['separator'] : ''); // Absolute Path to FIle. Required
	$args['separator'] == (!empty($args['separator']) ? $args['separator'] : ','); // Field Separator in file
	$args['has_labels'] == (!empty($args['has_labels']) ? true : false);

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
 * Requires grav_csv2array()
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
 * NOTE: In order for Custom Taxonomies to work, this function needs to be run after the Custom Taxonomy has been Registered.
 **/
function grav_csv2posts($args = array())
{
	// Set Defaults
	$args['file'] == (!empty($args['file']) ? $args['separator'] : ''); // Absolute Path to FIle. Required
	$args['separator'] == (!empty($args['separator']) ? $args['separator'] : ','); // Field Separator in file
	$args['has_labels'] == true; // This is Required to be true.

	// Data to Return
	$data = grav_csv2array($args);

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
 * Address to Geo Location (latitude, longitude) function
 *
 * @param  $address  (string) of Address or just ZipCode.
 *
 * NOTE: This is intended for single instances only. Google only allows a few requests per second.
 *
 * @return (array)
 * @author GG
 **/
function grav_address2location($address)
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
 * Requires grav_address2location()
 *
 **/
function grav_get_posts_by_location($args = array())
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
	else if(function_exists('grav_address2location'))
	{
		$location = grav_address2location($from_location);
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
 * grav_inject_post($new_post, 'front', $custom_posts);
 * if (have_posts()){ while (have_posts()){ the_post();
 *		the_title();
 *		the_excerpt();
 *		the_permalink();
 * }}
 */
function grav_inject_post($post=array(), $placement='end', $query=false)
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
function grav_user_has_role( $role, $user_id = null )
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
function grav_get_post_calendar($post_type='post', $post_meta_date='post_date')
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
function grav_msort($array = array(), $key="id", $dir="ASC") {
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
function grav_is_mobile()
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
function grav_is_bot()
{
    $bots = array("Teoma", "alexa", "froogle", "Gigabot", "inktomi",
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
 * Converts a URL to a verified YouTube URL that works on most devices and ColorBox function
 *
 * @param  $url  (string) Url of a defined Youtube Video.
 *
 * TODO: will also add a check for Vimeo soon.
 *
 * @return (str)
 * @author GG
 *
 **/
function grav_get_youtube_id($url)
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

	return false;
}

/**
 * Converts a URL to a verified YouTube video ID function
 *
 * @param  $url  (string) Url of a defined Youtube Video.
 *
 * REQUIRES: function grav_get_youtube_id()
 *
 * @return (str)
 * @author GG
 *
 **/
function grav_get_video_url($url)
{
	$id = grav_get_youtube_id($url);

	if($id)
	{
		$link = 'https://www.youtube.com/embed/'.$id.'?rel=0&amp;iframe=true&amp;wmode=transparent';
		if(function_exists('grav_is_mobile'))
		{
			if(grav_is_mobile())
			{
				$link = 'https://www.youtube.com/watch?v='.$id.'&amp;rel=0&amp;iframe=true&amp;wmode=transparent';
			}
		}
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
function grav_get_singular($value)
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
function grav_get_plural($value)
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

function grav_excerpt($new_length = 20)
{
  add_filter('excerpt_length', function () use ($new_length) {
    return $new_length;
  }, 11);
  the_excerpt();
}

// var dump something, wrapped in <pre>
// @mixed $var (the variable you want var_dumped)
function grav_dump($var)
{
    echo '<pre>';
    var_dump($var);
    echo '</pre>';
}

// Search Form
function grav_wpsearch()
{
    $form = '<form role="search" method="get" name="searchform" action="' . home_url( '/' ) . '" >
    <label class="screen-reader-text" for="s">' . __('Search for:', 'bonestheme') . '</label>
    <input type="text" value="' . get_search_query() . '" name="s" class="s" placeholder="Search the Site..." />
    <input type="submit" class="searchsubmit" value="'. esc_attr__('Search') .'" />
    </form>';
    return $form;
}

// Comment Layout
function grav_comments($comment, $args, $depth)
{
   $GLOBALS['comment'] = $comment; ?>
    <li <?php comment_class(); ?>>
        <article id="comment-<?php comment_ID(); ?>" class="comment-block">
            <header class="comment-author vcard">
                <?php echo get_avatar($comment,$size='32',$default='<path_to_url>' ); ?>
                <?php printf(__('<cite class="fn">%s</cite>'), get_comment_author_link()) ?>
                <time><a href="<?php echo htmlspecialchars( get_comment_link( $comment->comment_ID ) ) ?>"><?php printf(__('%1$s'), get_comment_date(),  get_comment_time()) ?></a></time>
                <?php edit_comment_link(__('(Edit)'),'  ','') ?>
            </header>
            <?php if ($comment->comment_approved == '0') : ?>
                <div class="help">
                    <p><?php _e('Your comment is awaiting moderation.') ?></p>
                </div>
            <?php endif; ?>
            <section class="comment_content">
                <?php comment_text() ?>
            </section>
            <?php comment_reply_link(array_merge( $args, array('depth' => $depth, 'max_depth' => $args['max_depth']))) ?>
        </article>
    <!-- </li> is added by wordpress automatically -->
<?php
}

// Related Posts Function (call using grav_related_posts(); ) -- relates based on tags
function grav_related_posts($number_of_posts=3)
{
	echo '<ul id="grav-related-posts">';
	global $post;
	$tags = wp_get_post_tags($post->ID);
	if($tags) {
		foreach($tags as $tag) { $tag_arr .= $tag->slug . ','; }
        $args = array(
        	'tag' => $tag_arr,
        	'numberposts' => $number_of_posts,
        	'post__not_in' => array($post->ID)
     	);
        $related_posts = get_posts($args);
        if($related_posts) {
        	foreach ($related_posts as $post) : setup_postdata($post); ?>
	           	<li class="related_post"><a href="<?php the_permalink() ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></li>
	        <?php endforeach; }
	    else { ?>
            <li class="no_related_post">No Related Posts Yet!</li>
		<?php }
	}
	wp_reset_query();
	echo '</ul>';
}

function grav_get_related_posts($number_of_posts=3)
{
	global $post;
	$tags = wp_get_post_tags($post->ID);
	if($tags)
	{
		foreach($tags as $tag) { $tag_arr .= $tag->slug . ','; }
        $args = array(
        	'tag' => $tag_arr,
        	'numberposts' => $number_of_posts,
        	'post__not_in' => array($post->ID)
     	);
    	return get_posts($args);
	}
}


?>