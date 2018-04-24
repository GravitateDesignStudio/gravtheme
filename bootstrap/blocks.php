<?php

// enforce background color choices
Grav\Vendor\GravitateBlocks::enforce_background_colors(array(
	'block-bg-none' => 'None',
	'block-bg-image' => 'Image',
	'bg-white' => 'White',
	'bg-black' => 'Black'
));

// make sure blocks appear in alphabetical order by label in the flexible content field
Grav\Vendor\GravitateBlocks::sort_block_names_alphabetically();

// Ensure Grav Blocks are viewable on the pages that require them
add_filter('grav_is_viewable', function($is_viewable) {
	if (is_home() || is_singular() || is_404()) {
		$is_viewable = true;
	}

	return $is_viewable;
});

// Add 'wysiwyg' class to content (v2) block columns
add_filter('grav_get_css', function($css, $block_name) {
	if (($block_name == 'content' || $block_name == 'contentv2') && in_array('columns', $css)) {
		$css[] = 'wysiwyg';
	}

	return $css;
}, 10, 2);

add_action('rest_api_init', function() {
	register_rest_field(['post', 'page'], 'grav_blocks', [
		'get_callback' => function($post) {
			return get_field('grav_blocks');
		}
	]);
});


add_filter('hello_bar_fields_settings', function($fields) {
	$fields = array_filter($fields, function($field) {
		return $field['key'] !== 'hello_bar_icon';
	});

	return $fields;
});

add_filter('hello_bar_colors', function($colors) {
	return array(
		'hellobar--blue' => 'Blue',
		'hellobar--red' => 'Red'
	);
});
