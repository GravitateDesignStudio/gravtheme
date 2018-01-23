<?php
namespace Grav\WP;

class Templates {
	public static function use_path_for_templates($path) {
		add_filter('template_include', function($template) use (&$path) {
			if (is_page_template() && file_exists($template)) {
				return $template;
			}
	
			$template_dir = get_template_directory() . '/'.$path;
			$post_type = str_replace('_', '-', get_post_type());
			$taxonomy = get_query_var('taxonomy');
	
			if (is_front_page() && file_exists($template_dir.'/home.php')) {
				return $template_dir.'/home.php';
			} else if ((is_tax() || is_tag() || is_category()) && $taxonomy && file_exists($template_dir.'/taxonomy-'.$taxonomy.'.php')) {
				return $template_dir.'/archive-'.$taxonomy.'.php';
			} else if ((is_tax() || is_tag() || is_category()) && file_exists($template_dir.'/taxonomy.php')) {
				return $template_dir.'/archive.php';
			} else if ((is_archive() || is_home()) && file_exists($template_dir.'/archive-'.$post_type.'.php')) {
				return $template_dir.'/archive-'.$post_type.'.php';
			} else if ((is_archive() || is_home()) && file_exists($template_dir.'/archive.php')) {
				return $template_dir.'/archive.php';
			} else if (is_search() && file_exists($template_dir.'/search.php')) {
				return $template_dir.'/search.php';
			} else if (is_404() && file_exists($template_dir.'/404.php')) {
				return $template_dir.'/404.php';
			} else if (is_author() && file_exists($template_dir.'/author.php')) {
				return $template_dir.'/author.php';
			} else if (is_singular() && file_exists($template_dir.'/single-'.$post_type.'.php')) {
				return $template_dir.'/single-'.$post_type.'.php';
			} else if (is_single() && file_exists($template_dir.'/single.php')) {
				return $template_dir.'/single.php';
			} else if (is_singular() && file_exists($template_dir.'/singular.php')) {
				return $template_dir.'/singular.php';
			}
	
			if (file_exists($template_dir.'/archive.php')) {
				return $template_dir.'/archive.php';
			}
		}, 99);
	}
}
