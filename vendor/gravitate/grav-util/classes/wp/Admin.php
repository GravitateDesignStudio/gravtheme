<?php
namespace Grav\WP;

class Admin {
	/**
	 * Change the label of an admin menu item and the post type labels associated with it
	 *
	 * @param  string $post_type       The post type (ex: 'post', 'page', 'custom_type', etc...)
	 * @param  string $slug            The URL slug in the admin to target (ex: 'post.php')
	 * @param  string $label           The new label for the item
	 * @param  array  $sub_menu_labels (optional) Array of submenu items in 'slug.php' => 'new label' format
	 * @param  array  $post_labels     (optional) Array of post type label overrides - key is the label field,
	 *                                 value is the new label (ex: 'new_item' => 'New Type Item',
	 *                                 'not_found_in_trash' => 'Type not found in trash')
	 *
	 * @author DF
	 */
	public static function change_menu_label($post_type, $slug, $label, $sub_menu_labels=array(), $post_labels=array()) {
		$label_singular = is_array($label) ? $label[0] : $label;
		$label_plural = is_array($label) ? $label[1] : $label;

		add_action('admin_menu', function() use ($slug, $label_singular, $label_plural, $sub_menu_labels) {
			global $menu;
			global $submenu;

			$menu_index = false;

			foreach ($menu as $menu_key => $menu_values) {
				if ($menu_values[2] == $slug) {
					$menu_index = $menu_key;
					break;
				}
			}

			if ($menu_index === false) {
				return;
			}

			$menu[$menu_index][0] = $label_plural;

			if (isset($submenu[$slug])) {
				foreach ($submenu[$slug] as &$sub_menu_values) {
					if (isset($sub_menu_labels[$sub_menu_values[2]])) {
						$sub_menu_values[0] = $sub_menu_labels[$sub_menu_values[2]];
					}
				}
			}
		});

		add_action('init', function() use ($post_type, $label_singular, $label_plural, $post_labels) {
			global $wp_post_types;

			if (!isset($post_labels)) {
				$post_labels = array();
			}

			$labels = &$wp_post_types[$post_type]->labels;
		    $labels->name = isset($post_labels['name']) ? $post_labels['name'] : $label_plural;
		    $labels->singular_name = isset($post_labels['singular_name']) ? $post_labels['singular_name'] : $label_singular;
		    $labels->add_new = isset($post_labels['add_new']) ? $post_labels['add_new'] : "Add {$label_singular}";
		    $labels->add_new_item = isset($post_labels['add_new_item']) ? $post_labels['add_new_item'] : "Add {$label_singular} Item";
		    $labels->edit_item = isset($post_labels['edit_item']) ? $post_labels['edit_item'] : "Edit {$label_singular}";
		    $labels->new_item = isset($post_labels['new_item']) ? $post_labels['new_item'] : $label_singular;
		    $labels->view_item = isset($post_labels['view_item']) ? $post_labels['view_item'] : "View {$label_singular}";
		    $labels->search_items = isset($post_labels['search_items']) ? $post_labels['search_items'] : "Search {$label_plural}";
		    $labels->not_found = isset($post_labels['not_found']) ? $post_labels['not_found'] : "No {$label_plural} found";
		    $labels->not_found_in_trash = isset($post_labels['not_found_in_trash']) ? $post_labels['not_found_in_trash'] : "No {$label_plural} found in Trash";
		    $labels->all_items = isset($post_labels['all_items']) ? $post_labels['all_items'] : "All {$label_plural}";
		    $labels->menu_name = isset($post_labels['menu_name']) ? $post_labels['menu_name'] : $label_plural;
		    $labels->name_admin_bar = isset($post_labels['menu_admin_bar']) ? $post_labels['menu_admin_bar'] : $label_plural;
		});
	}

	/**
	 * Retrieve the current post type that is being show in the admin panel.
	 * Returns false if no post type is being shown/edited
	 *
	 * @return string The current post type being shown/edited
	 */
	public static function get_current_post_type() {
		if (isset($_GET['post_type'])) {
			return $_GET['post_type'];
		}
		$post_id = $_GET['post'] ? (int)$_GET['post'] : ($_POST['post_ID'] ? (int)$_POST['post_ID'] : 0);
		if ($post_id) {
			return get_post_type($post_id);
		}
		return false;
	}

	public static function remove_taxonomy_table_columns($tax_slug, $remove_columns = array(), $priority = 10) {
		add_filter('manage_edit-'.$tax_slug.'_columns', function($columns) use (&$remove_columns) {
			foreach ($remove_columns as $remove_column) {
				if (isset($columns[$remove_column])) {
					unset($columns[$remove_column]);
				}
			}
		
			return $columns;
		}, $priority);
	}

	public static function add_taxonomy_table_columns($tax_slug, $columns = array(), $priority = 11) {
		add_filter('manage_edit-'.$tax_slug.'_columns', function($defaults) use (&$columns) {
			foreach (array_keys($columns) as $col_name) {
				$col_key = strtolower(preg_replace('/[^\da-z]/i', '', $col_name));
				
				$defaults[$col_key] = $col_name;
			}

			return $defaults;
		}, $priority);

		add_filter('manage_'.$tax_slug.'_custom_column', function($content, $column_name, $term_id) use (&$columns) {
			foreach (array_keys($columns) as $new_col_key) {
				if (strtolower(preg_replace('/[^\da-z]/i', '', $new_col_key)) == $column_name) {
					$col_func = $columns[$new_col_key];
					echo $col_func($term_id);
				}
			}

			return $content;
		}, 10, 3);
	}

	public static function remove_taxonomy_editor_fields($tax_slug, $remove_fields = array()) {
		$classes = array();

		foreach ($remove_fields as $field) {
			$classes[] = '.term-'.$field.'-wrap';
		}

		add_action('admin_print_styles', function() use (&$classes, &$tax_slug) {
			$current_screen = get_current_screen();
		
			if ($current_screen->id == 'edit-'.$tax_slug) {
				?>
				<style><?php echo esc_attr(implode(', ', $classes)); ?> { display:none; }</style>
				<?php
			}
		}, 999);
	}

	public static function remove_table_columns($post_type, $remove_ids = array(), $priority = 10) {
		add_filter('manage_'.$post_type.'_posts_columns', function($defaults) use (&$remove_ids) {
			foreach ($remove_ids as $remove_id) {
				if (isset($defaults[$remove_id])) {
					unset($defaults[$remove_id]);
				}
			}

			return $defaults;
		}, $priority);
	}

	public static function add_table_columns($post_type, $new_columns = array(), $priority = 11) {
		add_filter('manage_'.$post_type.'_posts_columns', function($defaults) use (&$new_columns) {
			foreach (array_keys($new_columns) as $col_name) {
				$col_key = strtolower(preg_replace('/[^\da-z]/i', '', $col_name));
				
				$defaults[$col_key] = $col_name;
			}

			return $defaults;
		}, $priority);

		add_action('manage_'.$post_type.'_posts_custom_column', function($column_name, $post_id) use (&$new_columns) {
			foreach (array_keys($new_columns) as $new_col_key) {
				if (strtolower(preg_replace('/[^\da-z]/i', '', $new_col_key)) == $column_name) {
					$col_func = $new_columns[$new_col_key];
					echo $col_func($post_id);
				}
			}
		}, 10, 2);
	}
}
