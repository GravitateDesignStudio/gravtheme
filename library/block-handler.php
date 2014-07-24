<?php

if(get_field('blocks'))
{
	while(the_flexible_field("blocks"))
	{
		?>

		<section class="block <?php echo strtolower(str_replace('_', '-', get_row_layout()));?> <?php the_sub_field('background_class');?>"<?php if(get_sub_field('background_class') == 'image'){ ?> style="background-image: url(<?php $background_image = get_sub_field('background_image'); echo $background_image['url'];?>);"<?php } ?>>

			<?php get_template_part('parts/'.strtolower(str_replace('_', '-', get_row_layout())));?>

		</section>

		<?php
	}
}