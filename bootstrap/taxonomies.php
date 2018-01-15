<?php
// include all taxonomies from the 'taxonomies' path (exclude files that begin with '_")
Grav\WP\Util::include_all_files(get_template_directory().'/taxonomies/*.php', function($file) {
	return substr(basename($file), 0, 1) !== '_';
});
