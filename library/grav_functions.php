<?php
####################################################
/**
 * Standard Functions for Gravitate
 *
 * Copyright (c) 2013-2015
 * Version: 1.9.0
 * Written by Brian F. and Geoff G.
 */
####################################################



/**
*
* Create shortcode for menu intended for sitemap use
*
**/
function grav_menu_shortcode($atts, $content = null) {
	extract(shortcode_atts(array( 'name' => null, ), $atts));
	return wp_nav_menu( array( 'menu' => $name, 'echo' => false ) );
}

/**
*
* Allow svgs to be uploaded via WP media library
*
**/
function grav_mime_types($mimes) {
	$mimes['svg'] = 'image/svg+xml';
	return $mimes;
}


/**
 * Get svg file to include from library/images/svgs
 *
 * Pass the name of the file as the argument minus the file extension
 *
 **/
function grav_get_svg($svg)
{
	$path = get_template_directory().'/library/images/svgs/'.$svg.'.svg';
	if(file_exists($path))
	{
		return file_get_contents($path);
	}
	return '';
}

function grav_set_permissions($allowed=true)
{
	if($allowed)
	{
		add_action( 'admin_init', 'grav_add_update_capabilities' );
	}
	else
	{
		add_action( 'admin_init', 'grav_remove_update_capabilities');
	}
}

/**
 * Remove file update capabilities from all roles.
 */
function grav_remove_update_capabilities()
{
    if(is_admin())
    {
        global $wp_roles;

        // A list of capabilities to remove from all roles.
        $caps = array(
            'update_core',
            'update_plugins',
            'install_plugins',
            'delete_plugins',
            'update_themes',
            'install_themes',
            'delete_themes'
        );

        if(!empty($wp_roles->roles))
        {
            foreach ( $wp_roles->roles as $role_name => $role )
            {
                foreach ( $caps as $cap )
                {
                    if(isset($role['capabilities'][$cap]))
                    {
                        $role_obj = get_role( $role_name );
                        if(!empty($role_obj))
                        {
                            $role_obj->remove_cap( $cap );
                        }
                    }
                }
            }
        }
    }
}

/**
 * Add file update capabilities to Admins.
 */
function grav_add_update_capabilities()
{

    $role = get_role( 'administrator' );

    $role->add_cap( 'update_core' );
    $role->add_cap( 'update_plugins' );
    $role->add_cap( 'install_plugins' );
    $role->add_cap( 'delete_plugins' );
    $role->add_cap( 'update_themes' );
    $role->add_cap( 'install_themes' );
    $role->add_cap( 'delete_themes' );
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
function grav_menu($menu='main', $class='')
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
function grav_check_registered_post_types()
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
function grav_csv2array($args = array())
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
	$args['file'] = (!empty($args['file']) ? $args['file'] : ''); // Absolute Path to FIle. Required
	$args['separator'] = (!empty($args['separator']) ? $args['separator'] : ','); // Field Separator in file
	$args['has_labels'] = true; // This is Required to be true.

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
 * Function to Combine CSS and JS Files.  The function will check to see if any file gets updated and will re-compress the files.
 * This function must be used within "wp_enqueue_scripts" action.
 * Ex.
 *       add_action( 'wp_enqueue_scripts', 'grav_compress_enqueue_files');
 *
 * @return (void)
 * @author GG
 **/
function grav_compress_enqueue_files()
{
    global $wp_scripts, $wp_styles;

    $types = array('js' => $wp_scripts, 'css' => $wp_styles);

    foreach ($types as $ext => $type)
    {

        $cache_var = 0;

        $files_var = '';

        if(!empty($type->queue))
        {
            foreach ($type->queue as $queued_file)
            {
                $queued = $type->registered[$queued_file];

                $files_var.= $queued->handle;

                if(strpos($queued->src, site_url()) !== false)
                {
                    $file = str_replace(array(site_url().'/', 'pscss'), array(ABSPATH, 'scss'), $queued->src);

                    if(file_exists($file))
                    {
                        $cache_var+= filemtime($file);
                    }
                }
            }

            $cache_var.= md5($files_var);

            $min_file_path = get_template_directory().'/library/'.$ext.'/theme.min.'.$cache_var.'.'.$ext;
            $min_file_url = get_template_directory_uri().'/library/'.$ext.'/theme.min.'.$cache_var.'.'.$ext;

        	if(is_dir(dirname($min_file_path)))
        	{
        		if(!file_exists($min_file_path))
	            {
	                $fp = fopen($min_file_path, 'a');
	                foreach ($type->queue as $queued_file)
	                {
	                    $queued = $type->registered[$queued_file];

	                    if($fp && $queued->handle != 'admin-bar')
	                    {
	                        if(strpos($queued->src, '/icomoon.io/'))
	                        {
	                            $content = gzinflate(substr(file_get_contents($queued->src),10,-8));
	                        }
	                        else
	                        {
	                            $content = file_get_contents(str_replace('pscss', 'pscss?style=compressed', $queued->src));
	                        }

				            // Write File
	                        if(fwrite($fp, str_replace(array("  "), array(" "), $content)))
	                        {
	                        	// Remove Old Files
								if($oldfiles = glob(dirname($min_file_path).'/theme.min.*.'.$ext))
					            {
					            	foreach ($oldfiles as $oldfile)
					            	{
					            		if($oldfile != $min_file_path) // make sure it is not the file we just created.
					            		{
					            			unlink($oldfile);
					            		}
					            	}
					            }
	                        }
	                    }
	                }
	                fclose($fp);
	            }

                if(file_exists($min_file_path))
	            {
	                if($ext == 'js')
	                {
	                    wp_enqueue_script('theme-min', $min_file_url, array('jquery'), '', true);
	                }
	                else if($ext == 'css')
	                {
	                    wp_enqueue_style('theme-min', $min_file_url, array());
	                }

	                foreach ($type->queue as $queued_file)
	                {
	                    $queued = $type->registered[$queued_file];

	                    if(strpos($queued->handle, 'theme-min') === false && $queued->handle != 'admin-bar')
	                    {
	                        if($ext == 'js')
	                        {
	                            wp_deregister_script( $queued->handle );
	                        }
	                        else if($ext == 'css')
	                        {
	                            wp_deregister_style( $queued->handle );
	                        }
	                    }
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
function grav_get_real_ip()
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
 * NOTE: This requires you to define the MAXMIND_LICENSE_KEY for the clients account.  They will need to sign up with Maxmind. This also uses the grav_get_real_ip() function to get the real IP.
 *
 * @return (array|false)
 * @author GG
 **/
function grav_get_geoip_info($manual_ip=false)
{
	$geo_info = false;

	if($manual_ip)
	{
		$geo_info = grav_get_geoip_info_by_ip($manual_ip);
	}
	else
	{
		$ips = array($_SERVER['HTTP_CLIENT_IP'], $_SERVER['HTTP_X_FORWARDED_FOR'], $_SERVER['HTTP_X_REAL_IP'], $_SERVER['REMOTE_ADDR']);

		foreach ($ips as $ip)
		{
			if(!empty($ip) && filter_var($ip, FILTER_VALIDATE_IP))
			{
				$geo_info = grav_get_geoip_info_by_ip($ip);

				if(!empty($geo_info['latitude']) && !empty($geo_info['longitude']))
				{
					return $geo_info;
				}
			}
		}
	}

	return $geo_info;
}

function grav_get_geoip_info_by_ip($ip=false)
{
	if($ip)
	{
		$real_ip = $ip;
	}
	else
	{
		$real_ip = grav_get_real_ip();
	}

	$cookie_key = 'geoip_info_'.$real_ip;

	if(!empty($_COOKIE[$cookie_key]) && !$ip)
	{
		return unserialize(base64_decode($_COOKIE[$cookie_key]));
	}
	else if(!grav_is_bot() && defined('MAXMIND_LICENSE_KEY'))
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

		if (curl_errno($curl)) {
			throw new Exception(
				'GeoIP request failed with a curl_errno of '
				. curl_errno($curl)
			);
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
function grav_get_vimeo_id($url)
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

	return 0;
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

	$autoplay = 1;

	if(strpos($url, 'autoplay=0') || strpos($url, 'autoplay=false'))
	{
		$autoplay = 0;
	}

	if(strpos($url, 'vimeo'))
	{
		$id = grav_get_vimeo_id($url);

		if(is_numeric($id))
		{
			return 'http://player.vimeo.com/video/'.$id.'?autoplay='.$autoplay;
		}
		return $url;
	}

	$id = grav_get_youtube_id($url);

	if($id)
	{
		$link = 'https://www.youtube.com/embed/'.$id.'?rel=0&amp;iframe=true&amp;wmode=transparent&amp;autoplay='.$autoplay;

		if(function_exists('grav_is_mobile'))
		{
			if(grav_is_mobile())
			{
				$link = 'https://www.youtube.com/watch?v='.$id.'&amp;rel=0&amp;iframe=true&amp;wmode=transparent&amp;autoplay='.$autoplay;
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

// print_r something, wrapped in <pre>
// @mixed $var (the variable you want print_r)
function grav_print_r($var, $return = false)
{
    echo '<pre>';
    print_r($var, $return);
    echo '</pre>';
}

// echos the URL to the /library/ folder in the theme
// @bool $output_echo (default true, if false this function will only return the url)
function grav_library_url($output_echo=true)
{
    if($output_echo === false) return get_bloginfo('template_url') . '/library';
    echo get_bloginfo('template_url') . '/library';
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
                <?php echo get_avatar($comment,$size='32' ); ?>
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


///////////////////////////
// Function to CHECK INTERSECTION OF TERMS ACROSS 2 TAXONOMIES
///////////////////////////
function grav_intersected_terms( $tax, $term, $joined_tax ) {

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

// reset roles function
// accepts 'administrator', 'author', 'editor', 'contributor', or 'subscriber'
// only needs to run once, changes DB
function grav_reset_role( $role )
{
    $default_roles = array(
        'administrator' => array(
        'switch_themes' => 1,
        'edit_themes' => 1,
        'activate_plugins' => 1,
        'edit_plugins' => 1,
        'edit_users' => 1,
        'edit_files' => 1,
        'manage_options' => 1,
        'moderate_comments' => 1,
        'manage_categories' => 1,
        'manage_links' => 1,
        'upload_files' => 1,
        'import' => 1,
        'unfiltered_html' => 1,
        'edit_posts' => 1,
        'edit_others_posts' => 1,
        'edit_published_posts' => 1,
        'publish_posts' => 1,
        'edit_pages' => 1,
        'read' => 1,
        'level_10' => 1,
        'level_9' => 1,
        'level_8' => 1,
        'level_7' => 1,
        'level_6' => 1,
        'level_5' => 1,
        'level_4' => 1,
        'level_3' => 1,
        'level_2' => 1,
        'level_1' => 1,
        'level_0' => 1,
        'edit_others_pages' => 1,
        'edit_published_pages' => 1,
        'publish_pages' => 1,
        'delete_pages' => 1,
        'delete_others_pages' => 1,
        'delete_published_pages' => 1,
        'delete_posts' => 1,
        'delete_others_posts' => 1,
        'delete_published_posts' => 1,
        'delete_private_posts' => 1,
        'edit_private_posts' => 1,
        'read_private_posts' => 1,
        'delete_private_pages' => 1,
        'edit_private_pages' => 1,
        'read_private_pages' => 1,
        'delete_users' => 1,
        'create_users' => 1,
        'unfiltered_upload' => 1,
        'edit_dashboard' => 1,
        'update_plugins' => 1,
        'delete_plugins' => 1,
        'install_plugins' => 1,
        'update_themes' => 1,
        'install_themes' => 1,
        'update_core' => 1,
        'list_users' => 1,
        'remove_users' => 1,
        'add_users' => 1,
        'promote_users' => 1,
        'edit_theme_options' => 1,
        'delete_themes' => 1,
        'export' => 1,
        ),

        'editor' => array(
        'moderate_comments' => 1,
        'manage_categories' => 1,
        'manage_links' => 1,
        'upload_files' => 1,
        'unfiltered_html' => 1,
        'edit_posts' => 1,
        'edit_others_posts' => 1,
        'edit_published_posts' => 1,
        'publish_posts' => 1,
        'edit_pages' => 1,
        'read' => 1,
        'level_7' => 1,
        'level_6' => 1,
        'level_5' => 1,
        'level_4' => 1,
        'level_3' => 1,
        'level_2' => 1,
        'level_1' => 1,
        'level_0' => 1,
        'edit_others_pages' => 1,
        'edit_published_pages' => 1,
        'publish_pages' => 1,
        'delete_pages' => 1,
        'delete_others_pages' => 1,
        'delete_published_pages' => 1,
        'delete_posts' => 1,
        'delete_others_posts' => 1,
        'delete_published_posts' => 1,
        'delete_private_posts' => 1,
        'edit_private_posts' => 1,
        'read_private_posts' => 1,
        'delete_private_pages' => 1,
        'edit_private_pages' => 1,
        'read_private_pages' => 1,
        ),

        'author' => array(
        'upload_files' => 1,
        'edit_posts' => 1,
        'edit_published_posts' => 1,
        'publish_posts' => 1,
        'read' => 1,
        'level_2' => 1,
        'level_1' => 1,
        'level_0' => 1,
        'delete_posts' => 1,
        'delete_published_posts' => 1,
        ),

        'contributor' => array(
        'edit_posts' => 1,
        'read' => 1,
        'level_1' => 1,
        'level_0' => 1,
        'delete_posts' => 1,
        ),

        'subscriber' => array(
        'read' => 1,
        'level_0' => 1,
        ),

        'display_name' => array(
        'administrator' => 'Administrator',
        'editor'	=> 'Editor',
        'author'	=> 'Author',
        'contributor' => 'Contributor',
        'subscriber'	=> 'Subscriber',
        ),

    );

    $role = strtolower( $role );

    remove_role( $role );

    return add_role( $role, $default_roles['display_name'][$role], $default_roles[$role] );
}

//  remove some menus from the dashboard you don't need (for all users)
function grav_remove_menus () {
    global $menu, $grav_restricted_menus;

    if(!$grav_restricted_menus)
    {
    	$grav_restricted_menus = array(__('Posts'), __('Links'), __('Comments'));
    }
    end ($menu);
    while (prev($menu)){
        $value = explode(' ',$menu[key($menu)][0]);

        if(in_array($value[0] != NULL?$value[0]:"" , $grav_restricted_menus)){unset($menu[key($menu)]);}
    }
}

// clean up wordpress head output (we don't need all this usually)
function grav_head_cleanup() {
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
}

// Fixing the Read More in the Excerpts
// This removes the annoying […] to a Read More link
function grav_excerpt_more($more) {
	global $post;
	// edit here if you like
	return '...  <a href="'. get_permalink($post->ID) . '" title="Read '.get_the_title($post->ID).'">Read more &raquo;</a>';
}

function grav_improved_trim_excerpt($text) {
  global $post;
  if ( '' == $text ) {
          $text = get_the_content('');
          $text = apply_filters('the_content', $text);
          $text = str_replace('\]\]\>', ']]&gt;', $text);
          $text = preg_replace('@<script[^>]*?>.*?</script>@si', '', $text);
          $text = strip_tags($text, '<p>');
          $excerpt_length = apply_filters('excerpt_length', 55);
          $words = explode(' ', $text, $excerpt_length + 1);
          if (count($words)> $excerpt_length) {
                  array_pop($words);
                  array_push($words, '...');
                  $text = implode(' ', $words);
          }
  }
  return $text;
}

// Numeric Page Navi, pass a custom query object if using a custom query
function grav_page_navi($before = '', $after = '', &$custom_query=null)
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
function grav_filter_ptags_on_images($content)
{
    $content = preg_replace('/<p>\s*(<a .*>)?\s*(<img .* \/>)\s*(<\/a>)?\s*<\/p>/iU', '\1\2\3', $content);
    return preg_replace('/<p>\s*(<iframe .*>*.<\/iframe>)\s*<\/p>/iU', '\1', $content);
}

function grav_get_social_share_link($social, $twitter_screen_name='')
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

/*
* Add Button Support to Tiny MCE
*/
function grav_mce_init( $init )
{
	$init['relative_urls'] = true;
	$init['document_base_url'] = get_bloginfo('url');
	return $init;
}
function grav_add_anchor_btn_js($plugin_array)
{
	if(file_exists(get_template_directory().'/library/js/mce_anchor_add_buttons.js'))
	{
	   	$plugin_array['grav_add_anchor_btn'] = get_template_directory_uri().'/library/js/mce_anchor_add_buttons.js'; // CHANGE THE BUTTON SCRIPT HERE
	}
	return $plugin_array;
}
function grav_add_anchor_btn($buttons)
{
   array_splice($buttons, 12, 0, "grav_add_anchor_btn");
   return $buttons;
}
/*
* END - Add Button Support to Tiny MCE
*/


function grav_register_sidebars()
{
    register_sidebar(array(
        'id' => 'sidebar1',
        'name' => 'Sidebar 1',
        'description' => 'The first (primary) sidebar.',
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h4 class="widgettitle">',
        'after_title' => '</h4>',
    ));
}

// Adding WP 3+ Functions & Theme Support
function grav_theme_support()
{
	add_theme_support('post-thumbnails');      // wp thumbnails (sizes handled in functions.php)
	set_post_thumbnail_size(125, 125, true);   // default thumb size

	//add_custom_background();                   // wp custom background
	add_theme_support('automatic-feed-links'); // rss thingy

	// adding post format support
	add_theme_support( 'post-formats',      // post formats
		array(
			'aside',   // title less blurb
			'gallery', // gallery of images
			'link',    // quick link to other site
			'image',   // an image
			'quote',   // a quick quote
			'status',  // a Facebook like status update
			'video',   // video
			'audio',   // audio
			'chat'     // chat transcript
		)
	);
	add_theme_support( 'menus' );            // wp menus

	register_nav_menus(                      // wp3+ menus
        array(
            'main_nav' => 'The Main Menu',   // main nav in header
            'mobile_nav' => 'The Mobile Menu',   // Mobile nav in header
            'top_nav' => 'The Top Menu',   // top right nav in header
            'footer_links' => 'Footer Links', // secondary nav in footer
            'site_map' => 'Site Map Links' // Sitemap Links
        )
    );
}

function grav_enqueue_scripts()
{
	global $grav_enqueue_files;

    if(!empty($grav_enqueue_files))
    {
        foreach ($grav_enqueue_files as $key => $file)
        {
        	$cache_var = 0;

        	if(strpos($file, get_template_directory_uri()) !== false) // If Local file then get the time of when it was modified
        	{
	        	$file_path = str_replace(get_template_directory_uri(), get_template_directory(), $file);

	            if(file_exists($file_path))
	            {
	                $cache_var = filemtime($file_path);
	            }
	        }

        	if(strpos($file, '.js'))
	        {
	        	wp_enqueue_script($key, $file, array('jquery'), $cache_var, true);
	        }
	        else if(strpos($file, '.css') || strpos($file, '/css'))
	        {
	        	wp_enqueue_style($key, $file, array(), $cache_var);
	        }
        }
    }
}

// adding formats to Tiny MCE
function grav_mce_button( $init_array )
{

	$style_formats = array(
		array(
			'title' => 'Button',
			'block' => 'span',
			'classes' => 'button-container',
			'wrapper' => true,
		),
	);
	$init_array['style_formats'] = json_encode( $style_formats );

	return $init_array;

}

function _get($value)
{
	if(isset($_GET[$value]))
	{
		return $_GET[$value];
	}
	return '';
}

function _post($value)
{
	if(isset($_POST[$value]))
	{
		return $_POST[$value];
	}
	return '';
}

function _request($value)
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
function grav_get_forms(){
	$forms = RGFormsModel::get_forms( null, 'title' );
	$form_list = array();
	foreach($forms as $form){
		$form_list[$form->id] = $form->title;
	}
	return $form_list;
}

function grav_update_postmeta_with_s3($bucket='', $region='', $overwrite=false)
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
