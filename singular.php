<?php get_header(); ?>


	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>


    	<div class="post">

    		<?php get_template_part('parts/entry'); ?>

    	</div>


    	<?php comments_template(); ?>


	<?php endwhile; ?>

<?php else : ?>

    	<?php get_template_part('parts/not-found');?>

<?php endif; ?>

<?php
	if(class_exists('GRAV_BLOCKS')){
		GRAV_BLOCKS::display();
	}
?>

<?php get_sidebar(); // sidebar 1 ?>

<?php get_footer(); ?>
