<?php
namespace Grav\WP;

class Taxonomy {
	public static function register($label_single, $label_plural, $post_types, $options=array()) {
		$default_options = array(
			'labels' => array(
				'name' => $label_plural,
				'singular_name' => $label_single,
				'search_items' => "Search {$label_plural}",
				'all_items' => "All {$label_plural}",
				'parent_item' => "Parent {$label_single}",
				'parent_item_colon' => "Parent {$label_single}:",
				'edit_item' => "Edit {$label_single}",
				'update_item' => "Update {$label_single}",
				'add_new_item' => "Add New {$label_single}",
				'new_item_name' => "New {$label_single} Name"
			),
			'hierarchical' => true,
			'show_ui' => true,
			'query_var' => true,
		);

		$slug = sanitize_title($label_plural);
		$options = array_merge($default_options, $options);

		add_action('init', function() use (&$slug, &$post_types, &$options) {
			register_taxonomy($slug, $post_types, $options);
		});
	}

	public static function remove($slug) {
		$slugs = is_array($slug) ? $slug : array($slug);

		add_action('init', function() use (&$slugs) {
			global $wp_taxonomies;

			foreach ($slugs as $slug) {
				if (taxonomy_exists($slug)) {
					unset($wp_taxonomies[$slug]);
				}
			}
		}, 999);
	}
}
