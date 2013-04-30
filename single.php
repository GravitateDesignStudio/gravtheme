<?php get_header(); ?>

			
	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
	
	
    	<div class="post">
    
    		<?php get_template_part('entry'); ?>
    	
    	</div>
    	
    	
    	<?php comments_template(); ?>
    	
		
	<?php endwhile; ?>			
		
<?php else : ?>

    	<h2>Not Found</h2>
    	
    	<p>Sorry, but the requested resource was not found on this site.</p>
	
<?php endif; ?>
	

<?php get_sidebar(); // sidebar 1 ?>


<?php get_footer(); ?>