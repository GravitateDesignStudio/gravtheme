<?php
get_header();

Grav\WP\Content::get_template_part('components/testing/theme-welcome');

?>
<section class="section-container">
	<div class="section-inner">
		<div class="row">
			<div class="columns small-12">
				<?php Grav\WP\Content::get_template_part('components/entry', array('post_id' => 2)); ?>
			</div>
		</div>
	</div>
</section>
<?php

if (have_posts()) {
	while (have_posts()) {
		the_post();

		if (class_exists('GRAV_BLOCKS')){
			GRAV_BLOCKS::display();
		}
	}
}

get_footer();
