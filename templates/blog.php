<?php
/*
*	Template Name: Blog
*/
get_header();
?>
	<h1><?php echo GRAV_FUNC::get_current_page_title();?></h1>

		<?php

		if(have_posts())
		{
			while(have_posts())
			{
				the_post();
				get_template_part('entry');
			}

			?>

			<div class="page-navi">
				<?php GRAV_FUNC::page_navi(); ?>
			</div>

			<?php /*

			<div class="page-navi">
				<ul>
					<li class="next-link"><?php next_posts_link(__('&laquo; Older Entries')) ?></li>
					<li class="prev-link"><?php previous_posts_link(__('Newer Entries &raquo;')) ?></li>
				</ul>
			</div>

			*/
			?>


		<?php
		}
		else
		{
		?>

			<div class="post">
		    	<?php get_template_part('parts/not-found');?>
			</div>

		<?php
		}
		?>


<?php //get_template_part( 'parts/sidebars/sidebar', 'style1' ); ?>


<?php

get_footer();
