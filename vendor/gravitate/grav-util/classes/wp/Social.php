<?php
namespace Grav\WP;

class Social {
	public static function get_share_link($site, $opts = array()) {
		$opts['post_title'] = isset($opts['post_title']) ? $opts['post_title'] : get_the_title();
		$opts['twitter_username'] = isset($opts['twitter_username']) ? $opts['twitter_username'] : '';

		$share_link = '';

		switch ($site) {
			case 'facebook':
				$share_link = "window.open('https://www.facebook.com/sharer/sharer.php?u='+encodeURIComponent(location.href),'facebookShare','width=626,height=436');return false;";
			break;

			case 'twitter':
				$share_link = "window.open('https://twitter.com/share?text=".$opts['post_title']."&url='+encodeURIComponent(location.href)+'&via=".$opts['twitter_username']."','twitterShare','width=626,height=436');return false;";
			break;

			case 'googleplus':
				$share_link = "window.open('https://plus.google.com/share?url='+encodeURIComponent(location.href),'googlePlusShare','width=626,height=436');return false;";
			break;

			case 'pinterest':
				$share_link = "window.open('https://www.pinterest.com/pin/create/button/?url='+encodeURIComponent(location.href)+'&media=&description=".$opts['post_title']."','pinterestShare','width=626,height=436');return false;";
			break;

			case 'linkedin':
				$share_link = "window.open('https://www.linkedin.com/cws/share?url='+encodeURIComponent(location.href),'linkedinShare','width=626,height=436');return false;";
			break;

			default:
			break;
		}

		return $share_link;
	}

	public static function share_link($site, $twitter_screen_name='', $post_id=0) {
		trigger_error('Method '.__METHOD__.' will be deprecated in a future version of grav-util. Please use '.__CLASS__.'::get_share_link instead', E_USER_DEPRECATED);
		
		if (!$post_id) {
			$post_id = get_the_ID();
		}

		return self::get_share_link($site, array(
			'post_title' => get_the_title($post_id),
			'twitter_username' => $twitter_screen_name
		));
	}
}
