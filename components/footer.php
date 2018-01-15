<?php
$social_sprites = Grav\WP\SVGSpriteManager::get_sprite('social');

$social_links = array();

if ($social_icon_fields = get_field('theme_options_social_site_links', 'option')) {
	$social_links = array_filter($social_icon_fields, function($icon) {
		return $icon['title'] && $icon['url'] && $icon['icon'];
	});
}

?>
<footer class="site-footer bg-black">
    <div class="row">
		<div class="columns small-6 medium-8">
            <nav class="site-footer__menu site-footer__menu--primary">
				<?php Grav\WP\Menus::display_for_location('footer-menu', array('depth' => 1)); ?>
            </nav>
        </div>
		<div class="columns small-6 medium-4">
            <div class="site-footer__social-links">
                <?php
				foreach ($social_links as $social_link)
				{
					?>
					<a href="<?php echo esc_attr($social_link['url']); ?>"
						class="site-footer__social-link"
						rel="external noopener nofollow"
						target="_blank"
						title="<?php echo esc_attr($social_link['title']); ?>">
						<span class="site-footer__social-icon">
							<?php $social_sprites->the_svg_symbol($social_link['icon']); ?>
						</span>
					</a>
					<?php
				}
				?>
			</div>
		</div>
    </div>
	<div class="row">
		 <div class="columns small-12 site-footer__legal">
              <p class="site-footer__copyright">&copy; <?php echo date('Y'); ?> <?php the_field('copyright_text', 'option'); ?></p>
              <nav class="site-footer__menu site-footer__menu--secondary">
                  <?php Grav\WP\Menus::display_for_location('footer-links', array('depth' => 1)); ?>
              </nav>
         </div>
	</div>
</footer>
