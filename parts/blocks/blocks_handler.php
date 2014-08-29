<?php

if(get_field('blocks'))
{
	while(the_flexible_field("blocks"))
	{
		?>

		<section class="block-container block-<?php echo strtolower(str_replace('_', '-', get_row_layout()));?> <?php the_sub_field('block_background');?>"<?php if(get_sub_field('block_background') == 'image' && get_sub_field('block_background_image')){ ?> style="background-image: url(<?php $block_background_image = get_sub_field('block_background_image'); echo $block_background_image['url'];?>);"<?php } ?>>

			<?php 
				$layout = strtolower(str_replace('_', '-', get_row_layout()));
				get_template_part('parts/blocks/'.$layout.'/'.$layout);
			?>

		</section>

		<?php
	}
}