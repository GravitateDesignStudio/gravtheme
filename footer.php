		</main> <?php // end .global-content ?>

		<?php get_template_part( 'parts/footer', 'default' );?>

	</div> <?php // end .global-wrapper ?>

	<?php wp_footer(); ?>

	<?php the_field('global_body_bottom_content', 'option');?>

</body>
</html>
