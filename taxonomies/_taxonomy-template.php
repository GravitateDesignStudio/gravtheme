<?php
$single_label = 'TaxonomyTemplate';
$plural_label = 'TaxonomyTemplates';
$name = strtolower(sanitize_title($single_label));
$slug = $name;

register_taxonomy(
	$slug,
	array('post', 'page'),
	array(
		'hierarchical' => true,
		'show_ui' => true,
		'query_var' => true,
		'labels' => array(
			'name' => $plural_label,
			'singular_name' => $single_label,
			'search_items' => 'Search '.$plural_label,
			'all_items' => 'All '.$plural_label,
			'parent_item' => 'Parent '.$single_label,
			'parent_item_colon' => 'Parent '.$single_label.':',
			'edit_item' => 'Edit '.$single_label,
			'update_item' => 'Update '.$single_label,
			'add_new_item' => 'Add New '.$single_label,
			'new_item_name' => 'New '.$single_label.' Name'
		)
	)
);
