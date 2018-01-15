<?php
if (function_exists('acf_add_options_page') && function_exists('acf_add_options_sub_page')) {
	$parent = acf_add_options_page(array(
		'page_title' => 'Theme Settings', 
		'menu_slug' => 'acf-options-theme', 
		'position' => 2
	));

	$sub_pages = array(
		'acf-theme-options-social' => 'Social Media',
		'acf-theme-options-footer' => 'Footer',
		'acf-theme-options-404' => '404',
		'acf-theme-options-scripts' => 'Scripts'
	);

	foreach ($sub_pages as $menu_slug => $page_title) {
		acf_add_options_sub_page(array(
			'page_title' => $page_title,
			'menu_slug' => $menu_slug,
			'parent_slug' => $parent['menu_slug'],
			'autoload' => true
		));
	}
}
