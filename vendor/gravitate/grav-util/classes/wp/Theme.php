<?php
/**
 * Theme Utilities
 * @category theme
 * @author GG
 */

namespace Grav\WP;

/**
 * Theme Class containing all methods related to Theme
 */
class Theme {

    /**
     * Initiates the Theme with Dfault Settings
     *
     * @access public
     * @return void
     * @author GG
     */
    public static function init()
    {
        // launching operation cleanup
        add_action( 'init', array(__CLASS__, 'head_cleanup') );

        // Check if Permalinks needs to be updated
        add_action( 'init', array('\\Grav\\WordPressUtilities\\Posts', 'permalinks_update') );

        // launching this stuff after theme setup
        add_action( 'after_setup_theme', array(__CLASS__, 'after_setup_configuration') );

        // Use Templates in the /template folder instead.
        add_filter( 'template_include', array(__CLASS__, 'template_include'), 99 );

        // Move Yoast to the bottom
        add_filter( 'wpseo_metabox_prio', create_function( '', 'return "low";' ) );

        // Update Image Compression Quality
        add_filter( 'jpeg_quality', create_function( '', 'return 75;' ) );
    }



    /**
     * Cleanup WP Head Functions and other things that are no longer needed
     *
     * @access public
     * @return void
     * @author GG
     */
	public static function head_cleanup()
	{
		remove_action( 'wp_head', 'rsd_link' );                               // EditURI link
		remove_action( 'wp_head', 'wlwmanifest_link' );                       // Windows Live Writer
		remove_action( 'wp_head', 'index_rel_link' );                         // index link
		remove_action( 'wp_head', 'parent_post_rel_link', 10, 0 );            // previous link
		remove_action( 'wp_head', 'start_post_rel_link', 10, 0 );             // start link
		remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0 ); // Links for Adjacent Posts
		remove_action( 'wp_head', 'wp_generator' );                           // WP version

		if (!is_admin()) {
			wp_deregister_script('wp-embed');
		}

		remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
		remove_action( 'wp_print_styles', 'print_emoji_styles' );
	}



    /**
	 * Add Theme Support
	 *
	 * @access public
	 * @return void
	 * @author GG
	 *
	 * @example examples/Theme/add_theme_support.txt Example #1 Running the function in a WordPress hook
	 *
	 */
	public static function after_setup_configuration()
	{
		set_post_thumbnail_size(300, 300, true);   		// default thumb size

		// Add New Image Sizes
		add_image_size('small', 300, 300, false);
		add_image_size('xlarge', 1440, 1900, false);

		add_theme_support( 'automatic-feed-links'); 		// rss thingy
		add_theme_support( 'menus' );            			// wp menus
		add_theme_support( 'custom-logo');					// adds ability for custom logo in customizer
		register_nav_menus(self::get_default_menus());

		$menu_locations = get_theme_mod( 'nav_menu_locations' );
		if(empty($menu_locations))
		{
			$menu_locations = array();
		}

		$updated_menus = false;

		foreach (self::get_default_menus() as $menu => $menu_title)
		{
			if(!wp_get_nav_menu_object($menu))
			{
				$menu_id = wp_create_nav_menu($menu);
				$menu_locations[$menu] = $menu_id;

				wp_update_nav_menu_item($menu_id, 0, array(
			        'menu-item-title' =>  __('Home'),
			        'menu-item-classes' => 'home',
			        'menu-item-url' => '/',
			        'menu-item-status' => 'publish')
				);

				$updated_menus = true;
			}
		}

		if($updated_menus)
		{
			set_theme_mod('nav_menu_locations', $menu_locations);
		}
	}



    /**
     * Returns the default Array of menus.
     * Is fiterable through 'grav_wp_default_menus'.
     *
     * @access public
     * @see after_setup_configuration()
     *
     * @example examples/Theme/get_default_menus.txt Example #1 - Using the Filter
     *
     * @uses grav_wp_default_menus
     * @return array
     * @author GG
     */
    public static function get_default_menus()
	{
        $default_menus = array(
			'main-menu' => 'Main Menu',   				// main nav in header
			'main-links' => 'Main Utility Links',   	// main nav in header
			'footer-menu' => 'Footer Menu', 			// secondary nav in footer
			'footer-links' => 'Footer Utility Links', 	// secondary nav in footer
			'mobile-menu' => 'Mobile Menu',   			// Mobile nav in header
			'mobile-links' => 'Mobile Utility Links',   // Mobile nav in header
			'sitemap-menu' => 'SiteMap Menu' 			// Sitemap Links
		);

        /**
        * Filters the Default Menus to create.
        *
        * @see get_default_menus()
        *
        * @param array $default_menus Array of wp_nav_menu() arguments.
        */
        $menus = apply_filters( 'grav_wp_default_menus', $default_menus);

		return $menus;
	}



    /**
	 * Setup Template Folder Structure and Include Path
	 *
	 * @access public
     * @param string $template String to original template file.
	 * @return string
	 * @author GG
	 *
	 **/
    public static function template_include($template)
	{
		if(is_page_template() && file_exists($template))
		{
			return $template;
		}

		$template_dir = get_template_directory() . '/templates';
		$post_type = str_replace('_', '-', get_post_type());
		$taxonomy = get_query_var('taxonomy');

	    if(is_front_page() && file_exists($template_dir.'/home.php'))
	    {
	        return $template_dir.'/home.php';
	    }
		elseif((is_tax() || is_tag() || is_category()) && $taxonomy && file_exists($template_dir.'/taxonomy-'.$taxonomy.'.php'))
	    {
	        return $template_dir.'/archive-'.$taxonomy.'.php';
	    }
		elseif((is_tax() || is_tag() || is_category()) && file_exists($template_dir.'/taxonomy.php'))
	    {
	        return $template_dir.'/archive.php';
	    }
	    elseif((is_archive() || is_home()) && file_exists($template_dir.'/archive-'.$post_type.'.php'))
	    {
	        return $template_dir.'/archive-'.$post_type.'.php';
	    }
		elseif((is_archive() || is_home()) && file_exists($template_dir.'/archive.php'))
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
		elseif(is_author() && file_exists($template_dir.'/author.php'))
	    {
	        return $template_dir.'/author.php';
	    }
		elseif(is_singular() && file_exists($template_dir.'/single-'.$post_type.'.php'))
	    {
	        return $template_dir.'/single-'.$post_type.'.php';
	    }
		elseif(is_singular() && file_exists($template_dir.'/single.php'))
	    {
	        return $template_dir.'/single.php';
	    }
        elseif(is_singular() && file_exists($template_dir.'/singular.php'))
	    {
	        return $template_dir.'/singular.php';
	    }

		if(file_exists($template_dir.'/archive.php'))
		{
	    	return $template_dir.'/archive.php';
		}
	}
}
