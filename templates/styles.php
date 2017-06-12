<?php
/*
*	Template Name: Styles
*/
get_header();

if (have_posts()) { while (have_posts()) { the_post();

	?>
	<section class="section-container">
		<div class="row section-inner">
			<div class="columns small-12 medium-6">
				<?php get_template_part( 'parts/styles' ); ?>
			</div>
			<div class="columns small-12 medium-6 bg-gray-dark">
				<?php get_template_part( 'parts/styles' ); ?>
			</div>
		</div>
	</section>
	<?php

	if(class_exists('GRAV_BLOCKS')){
		GRAV_BLOCKS::display();
	}
}}

get_footer();
