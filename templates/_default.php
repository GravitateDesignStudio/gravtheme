<?php
/*
*	Template Name: Default Template
*/
get_header();

if (have_posts()) { while (have_posts()) { the_post();

	if(class_exists('GRAV_BLOCKS')){
		GRAV_BLOCKS::display();
	}
}}

get_footer();
