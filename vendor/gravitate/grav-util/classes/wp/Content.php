<?php
namespace Grav\WP;

class Content {
	/**
	 * Advanced version of `get_template_part` that allows for variables to be passed
	 * to the template being called
	 *
	 * @param  string $slug   Template part slug -- same as `get_template_part` first argument
	 * @param  array  $params Array of arguments to pass. Each key becomes and argument availble
	 *                        to the template. For example, `'my_var' => $my_current_var` would
	 *                        make `$my_var` available in the template with the value of
	 *                        `$my_current_var` initially assigned to it
	 * @param  boolean $output Set this to false to return the template output as a variable
	 * @return string         The template output if `$output` is set to false
	 *
	 * @author DF
	 */
	public static function get_template_part($slug, $params=array(), $output=true) {
		if (!$output) {
			ob_start();
		}

	    if (!$template_file = locate_template("{$slug}.php", false, false)) {
	    	trigger_error("Error locating '{$slug}' for inclusion", E_USER_ERROR);
	    }

	    extract($params, EXTR_SKIP);
	    require($template_file);

		if (!$output) {
			return ob_get_clean();
		}
	}

	public static function get_related_posts($object_id=0, $opts=array()) {
		// No object id? Use the current post.
		if (!$object_id) {
			$object_id = the_post_ID();
		}

		// Return an empty array if there is no object id
		if (!$object_id) {
			return array();
		}

		$source_post = get_post($object_id);

		if (!isset($opts['post_type'])) $opts['post_type'] = $source_post->post_type;
		if (!isset($opts['num_posts'])) $opts['num_posts'] = 4;

		// default get_posts arguments
		$base_args = array(
			'post_type' => $opts['post_type'],
			'post_status' => 'publish'
		);

		// get taxonomies used by post
		global $wpdb;

		$sql_get_used_taxonomies = $wpdb->prepare("SELECT {$wpdb->term_taxonomy}.term_id, {$wpdb->term_taxonomy}.taxonomy FROM {$wpdb->term_relationships} JOIN {$wpdb->term_taxonomy} ON {$wpdb->term_taxonomy}.term_taxonomy_id = {$wpdb->term_relationships}.term_taxonomy_id WHERE {$wpdb->term_relationships}.object_id = %d", $object_id);
		$results = $wpdb->get_results($sql_get_used_taxonomies);

		if (!$results) {
			// return the most recent posts if there are no taxonomies
			return get_posts(array_merge($base_args, array(
				'posts_per_page' => $opts['num_posts']
			)));
		}

		// create 'tax_query' parameters
		$tax_queries = array_map(function($item) use (&$object_id) {
			$terms = get_the_terms($object_id, $item->taxonomy);
			$term_ids = $terms ? array_map(function($term) {
				return $term->term_id;
			}, $terms) : array();

			return array(
				'taxonomy' => $item->taxonomy,
				'field' => 'term_id',
				'terms' => $term_ids,
				'operator' => 'IN'
			);
		}, $results);

		$exclude_ids = array($object_id);
		$return_posts = array();

		// find posts matching all taxonomy terms
		$return_posts = get_posts(array_merge($base_args, array(
			'posts_per_page' => $opts['num_posts'],
			'tax_query' => array_merge(array('relation' => 'AND'), $tax_queries),
			'post__not_in' => $exclude_ids
		)));

		if (count($return_posts) >= $opts['num_posts']) {
			return $return_posts;
		}

		// find posts matching any taxonomy terms
		$add_exclude_ids = count($return_posts) ? array_map(function($item) { return $item->ID; }, $return_posts) : array();
		$exclude_ids = array_merge(array($object_id), $add_exclude_ids);

		$match_some_posts = get_posts(array_merge($base_args, array(
			'posts_per_page' => $opts['num_posts']-count($return_posts),
			'tax_query' => array_merge(array('relation' => 'OR'), $tax_queries),
			'post__not_in' => $exclude_ids
		)));

		$return_posts = array_merge($return_posts, $match_some_posts);

		if (count($return_posts) >= $opts['num_posts']) {
			return $return_posts;
		}

		// fill out the remainder with the latest posts
		$add_exclude_ids = count($return_posts) ? array_map(function($item) { return $item->ID; }, $return_posts) : array();
		$exclude_ids = array_merge(array($object_id), $add_exclude_ids);

		$most_recent_posts = get_posts(array_merge($base_args, array(
			'posts_per_page' => $opts['num_posts']-count($return_posts),
			'post__not_in' => $exclude_ids
		)));

		$return_posts = array_merge($return_posts, $most_recent_posts);

		return $return_posts;
	}

	public static function get_excerpt($opts = array()) {
		// options and variables
		$force_blocks = isset($opts['force_blocks']) ? $opts['force_blocks'] : false;
		$force_default = isset($opts['force_default']) ? $opts['force_default'] : false;
		$full_content = isset($opts['full_content']) ? $opts['full_content'] : false;
		$autop = isset($opts['wpautop']) ? $opts['wpautop'] : false;
		$strip_tags = isset($opts['strip_tags']) ? $opts['strip_tags'] : true;
		$strip_shortcodes = isset($opts['strip_shortcodes']) ? $opts['strip_shortcodes'] : true;
		$prefer_excerpt = isset($opts['prefer_excerpt']) ? $opts['prefer_excerpt'] : false;
		$excerpt_length = isset($opts['length']) ? $opts['length'] : apply_filters('excerpt_length', 55);
		$excerpt_more = isset($opts['more']) ? $opts['more'] : apply_filters('excerpt_more', ' ' . '[&hellip;]');
		$filter_nbsp = isset($opts['filter_nbsp']) ? $opts['filter_nbsp'] : true;
		$meta_key_match = (isset($opts['meta_key_match']) && is_array($opts['meta_key_match'])) ? $opts['meta_key_match'] : array('grav\_blocks\__\_content', 'grav\_blocks\__\_content\_column\__\_column', 'grav\_blocks\__\_column\__');
		$post_id = isset($opts['post_id']) ? (int)$opts['post_id'] : get_the_ID();
		$post = get_post($post_id);

		
		if ($prefer_excerpt && trim($post->post_excerpt))
		{
			if ($autop)
			{
				return wpautop($post->post_excerpt);
			}

			return $post->post_excerpt;
		}

		$content = $post->post_content;

		// get the content from blocks
		if ((empty($content) || $force_blocks) && !$force_default)
		{
			$meta = get_metadata('post', $post_id);

			if (isset($meta['grav_blocks']) && is_array($meta['grav_blocks']) && $meta['grav_blocks'][0])
			{
				global $wpdb;

				// prepare our sql query
				$meta_key_match = array_map(function($match_str) {
					return "meta_key LIKE '{$match_str}'";
				}, $meta_key_match);

				$meta_key_match_str = implode(' OR ', $meta_key_match);

				$sql = "SELECT meta_value FROM {$wpdb->postmeta} WHERE ({$meta_key_match_str}) AND post_id = {$post_id} LIMIT 10";
				
				// fetch our results
				if ($results = $wpdb->get_results($sql, ARRAY_N))
				{
					// flatten our multidimensional array and
					// concatenate it to usable content
					$content_parts = array();

					foreach ($results as $result) {
						$content_parts = array_merge($content_parts, $result);
					}

					$content = implode(' ', $content_parts);
				}
				else
				{
					// default to empty content
					$content = '';
				}
			}
		}

		// option to strip tags
		if ($strip_tags)
		{
			$content = strip_tags($content);
		}

		// option to strip shortcodes
		if ($strip_shortcodes)
		{
			$content = strip_shortcodes($content);
		}

		// escape html
		$content = str_replace(array(
			']]>',
			'\]\]\>'
		) , ']]&gt;', $content);

		// escape scripts
		$content = preg_replace('@<script[^>]*?>.*?</script>@si', '', $content);

		// handle as excerpt if not full content
		if (!$full_content)
		{
			$content = apply_filters('the_excerpt', $content);
			$content = wp_trim_words($content, $excerpt_length, $excerpt_more);
		}

		// option to wpautop
		if ($autop)
		{
			$content = wpautop($content);
		}

		if ($filter_nbsp) {
			$content = str_replace('&nbsp;', '', $content);
		}

		return trim($content);
	}

	public static function filter_p_tags_on_images() {
		$process_func = function($content) {
			$content = preg_replace('/<p>\s*(<a .*>)?\s*(<img .* \/>)\s*(<\/a>)?\s*<\/p>/iU', '\1\2\3', $content);
			
			return preg_replace('/<p>\s*(<iframe .*>*.<\/iframe>)\s*<\/p>/iU', '\1', $content);
		};

		add_filter('the_content', $process_func);
		add_filter('the_excerpt', $process_func);
		add_filter('acf/format_value/type=wysiwyg', $process_func);
	}
}
