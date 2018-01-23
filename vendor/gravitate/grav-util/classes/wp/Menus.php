<?php
namespace Grav\WP;

class Menus {
	public static function get_menus() {
		$menus = array();

		foreach(get_terms('nav_menu') as $menu) {
			$menus[$menu->term_id] = $menu->name;
		}

		return $menus;
	}

	public static function get_locations() {
		return get_registered_nav_menus();
	}

	public static function display_for_location($theme_location, $opts = array()) {
		$menu_opts = array_merge(array(
			'theme_location' => $theme_location,
			'container' => ''
		), $opts);

		wp_nav_menu($menu_opts);
	}

	public static function create_default_menus($menus) {
		add_action('after_setup_theme', function() use (&$menus) {
			$menu_locations = get_theme_mod('nav_menu_locations');
			
			if (empty($menu_locations)){
				$menu_locations = array();
			}
	
			$updated_menus = false;
	
			foreach ($menus as $menu_slug => $menu_title) {
				if (!in_array($menu_slug, array_keys(get_registered_nav_menus()))) {
					$menu_id = wp_create_nav_menu($menu_slug);

					if (is_wp_error($menu_id)) {
						continue;
					}
					
					$menu_locations[$menu_slug] = $menu_id;
					
					wp_update_nav_menu_item($menu_id, 0, array(
						'menu-item-title' =>  __('Home'),
						'menu-item-classes' => 'home',
						'menu-item-url' => '/',
						'menu-item-status' => 'publish'
					));
	
					$updated_menus = true;
				}
			}
	
			if ($updated_menus) {
				set_theme_mod('nav_menu_locations', $menu_locations);
			}
		});
	}
}
