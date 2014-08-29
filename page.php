<?php get_header(); ?>

	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>


		<h1 class="page-title" itemprop="headline"><?php the_title(); ?></h1>

		<?php the_content(); ?>

		<?php get_template_part('parts/blocks/blocks_handler'); ?>

	<?php endwhile; endif; ?>


<?php get_footer(); ?>