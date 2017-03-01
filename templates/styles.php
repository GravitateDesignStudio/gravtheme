<?php
/*
*	Template Name: Styles
*/
get_header();

if (have_posts()) { while (have_posts()) { the_post();

	?>

	<!--  CUSTOM STYLES and Blocks Here -->








	<?php

	if(class_exists('GRAV_BLOCKS')){
		GRAV_BLOCKS::display();
	}
}}

get_footer();
