<?php
/**
 * Posts Utilities
 * @category posts
 * @author GG
 */

namespace Grav\WP;

/**
 * Posts Class containing all Methods related to Posts
 */
class Posts {

    /**
	 * Check all Post Types and Taxonomies for changes.  If so then flush rewrite rules.
	 *
	 * @access public
	 * @return (void)
	 * @author GG
	 *
	 **/
	public static function permalinks_update()
	{
	    $cache = implode('',get_post_types()).implode('',get_taxonomies());
	    if(get_option( 'grav_registered_permalinks_cache' ) != $cache)
	    {
	        flush_rewrite_rules();
	        update_option( 'grav_registered_permalinks_cache', $cache );
	    }
	}
}
