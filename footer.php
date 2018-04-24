		</main> <?php // end .global-content ?>

		<?php Grav\WP\Content::get_template_part('components/footer'); ?>

	</div> <?php // end .global-wrapper ?>

	<?php
	wp_footer();

	if (!defined('IGNORE_USER_SCRIPTS') || !constant('IGNORE_USER_SCRIPTS')) {
		the_field('global_body_bottom_content', 'option', false);
	}
	?>
</body>
</html>
