<?php

/*
*	Template Name: Home
*/

get_header();

if (have_posts()) { while (have_posts()) { the_post();

	?>

	<section class="section-container">
		<div class="section-inner">
			<div class="row">
				<div class="columns small-12">
					<h1>Home Page Tempalte</h1>
				</div>
			</div>
		</div>
	</section>

	<?php

	if(class_exists('GRAV_BLOCKS')){
		GRAV_BLOCKS::display();
	}
	
}}

get_footer();
