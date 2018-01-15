<?php
get_header();

Grav\WP\Content::get_template_part('components/banners/banner-default');

if (have_posts()) {
	while (have_posts()) {
		the_post();

		if (get_the_content()) {
			?>
			<section class="section-container">
				<div class="section-inner">
					<div class="row">
						<div class="columns small-12 wysiwyg">
							<?php the_content(); ?>
						</div>
					</div>
				</div>
			</section>
			<?php
		}

		if (class_exists('GRAV_BLOCKS')) {
			GRAV_BLOCKS::display();
		}
	}
}

get_footer();
