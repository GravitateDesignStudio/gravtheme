<?php
// include all CPTs from the 'post-types' path (exclude files that begin with '_')
Grav\WP\Util::include_all_files(get_template_directory().'/post-types/*.php', function($file) {
	return substr(basename($file), 0, 1) !== '_';
});
