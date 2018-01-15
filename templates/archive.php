<?php
if (!function_exists('archive_get_title')) {
	function archive_get_title() {
		$queried_object = get_queried_object();
	
		$title_prefix = '';
		$title = '';
	
		if (is_category()) {
			$title_prefix = __('Posts Categorized:');
			$title = single_cat_title('', false);
		} else if (is_tag()) {
			$title_prefix = __('Posts Tagged:');
			$title = single_tag_title('', false);
		} else if (is_author()) {
			$title_prefix = __('Posts By:');
			$title = get_the_author_meta('display_name');
		} else if (is_day()) {
			$title_prefix = __('Daily Archives:');
			$title = get_the_time('l, F j, Y');
		} else if (is_month()) {
			$title_prefix = __('Monthly Archives:');
			$title = get_the_time('F Y');
		} else if (is_year()) {
			$title_prefix = __('Yearly Archives:');
			$title = get_the_time('Y');
		} else {
			$title = $queried_object->label;
		}
	
		$formatted_title = $title;
	
		if ($title_prefix) {
			$formatted_title = '<span class="archive__title-prefix>'.$title_prefix.'</span> '.$title;
		}
	
		return $formatted_title;
	}
}

get_header();

Grav\WP\Content::get_template_part('components/banners/banner-default', array(
	'title' => archive_get_title()
));

if (have_posts())
{
	?>
	<div class="row small-up-1 medium-up-2 large-up-3 archive__cards-container">
		<?php
		while (have_posts())
		{
			?>
			<div class="columns">
				<?php
				the_post();

				Grav\WP\Content::get_template_part('components/cards/card-default');
				?>
			</div>
			<?php
		}
	?>
	</div>
	<?php

	Grav\WP\Content::get_template_part('components/archive/navigation');
} else {
	Grav\WP\Content::get_template_part('components/not-found');
}

get_footer();
