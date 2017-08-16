<footer class="global-footer">

    <div class="row">

		<div class="columns small-6 medium-8">

            <nav class="global-footer-main-menu">
				<?php GRAV_FUNC::menu('footer-menu'); ?>
            </nav>

        </div>

		<div class="columns small-6 medium-4 text-right">

            <ul class="social-links">
                <?php
				if ($social_icons = get_field('theme_options_social_links', 'option'))
				{
					foreach($social_icons as $social)
					{
						if ($social['link'] && $social['title'])
						{
							?>
							<li>
		                        <a
		                            href="<?php echo esc_attr($social['link']); ?>"
		                            rel="external noopener nofollow"
		                            target="_blank"
		                            title="<?php echo esc_attr($social['title']); ?>">
		                            <span class="<?php echo esc_attr($social['icon']); ?>"></span>
		                        </a>
		                    </li>
							<?php
						}
					}
				}
				?>
			</ul>

		</div>

    </div>

	<div class="row">
		 <div class="columns small-12 global-footer-legal text-right">

              <p class="text-right">&copy; <?php echo date('Y');?> <?php the_field('copyright_text', 'option'); ?></p>

              <nav class="global-footer-utility-links">
                  <?php GRAV_FUNC::menu('footer-links'); ?>
              </nav>

         </div>
    </div>

</footer>
