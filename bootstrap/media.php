<?php
// set JPEG compression quality
add_filter('jpeg_quality', function() {
	return 75;
});

// enable media library support for SVGs
Grav\WP\Media::add_upload_mime_types(array(
	'svg' => 'image/svg+xml'
));

// load SVG sprites
Grav\WP\SVGSpriteManager::add_sprite('social', get_template_directory().'/media/svg/social-icons.svg');

// enable post thumbnail support
Grav\WP\ThemeSupport::post_thumbnails(300, 300, true);

// set image sizes
Grav\WP\ThemeSupport::image_sizes(array(
	'small' => array(
		'width' => 300,
		'height' => 300,
		'crop' => false
	),
	'xlarge' => array(
		'width' => 1440,
		'height' => 1900,
		'crop' => false
	)
));
