<?php
/*
*	Template Name: Styles
*/
get_header();

if (have_posts()) {
	while (have_posts()) {
		the_post();

		?>
		<section class="section-container wysiwyg">
			<div class="row section-inner">
				<div class="columns small-12">
					<?php Grav\WP\Content::get_template_part('components/testing/style-testing'); ?>
				</div>
			</div>
		</section>

		<section class="section-container bg-black wysiwyg">
			<div class="row section-inner">
				<div class="columns small-12">
					<?php Grav\WP\Content::get_template_part('components/testing/style-testing'); ?>
				</div>
			</div>
		</section>

		<section class="section-container bg-blue wysiwyg">
			<div class="row section-inner">
				<div class="columns small-12">
					<?php Grav\WP\Content::get_template_part('components/testing/style-testing'); ?>
				</div>
			</div>
		</section>

		<section class="section-container bg-red wysiwyg">
			<div class="row section-inner">
				<div class="columns small-12">
					<?php Grav\WP\Content::get_template_part('components/testing/style-testing'); ?>
				</div>
			</div>
		</section>

		<section class="section-container bg-gray wysiwyg">
			<div class="row section-inner">
				<div class="columns small-12">
					<?php Grav\WP\Content::get_template_part('components/testing/style-testing'); ?>
				</div>
			</div>
		</section>
		<?php

		if (class_exists('GRAV_BLOCKS')) {
			GRAV_BLOCKS::display();
		}
	}
}

get_footer();
