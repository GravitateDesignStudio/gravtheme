<?php
get_header();

Grav\WP\Content::get_template_part('components/banners/banner-default', array(
    'title' => 'Gravitate WordPress Theme'
));

Grav\WP\Content::get_template_part('components/testing/theme-welcome');

if (have_posts()) {
	while (have_posts()) {
		the_post();

		if (class_exists('GRAV_BLOCKS')) {
			GRAV_BLOCKS::display();
		}
	}
}

get_footer();
