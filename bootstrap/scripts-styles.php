<?php
// ********************
// JavaScript enqueue
// ********************
Grav\WP\Scripts::enqueue_scripts(array(
	'master_js' => array(
		'url' => get_template_directory_uri().'/dist/js/master.min.js',
		'deps' => array('jquery'),
		'defer' => true,
		'preload_hook' => 'global_head_top_content'
	)
));


// ********************
// CSS enqueue
// ********************
Grav\WP\Styles::enqueue_styles(array(
	'master_css' => array(
		'url' => get_template_directory_uri().'/dist/css/master.min.css',
		'preload_hook' => 'global_head_top_content'
	)
));
