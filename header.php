<!doctype html>
<html <?php language_attributes(); ?>>

	<head>
	
		<title><?php bloginfo("name"); ?> <?php wp_title('&bull;'); ?></title>
		
		<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
		<meta name="viewport" content="width=device-width">
		<meta name="application-name" content="<?php bloginfo('name'); ?>" />
		
		<link rel="shortcut icon" href="<?php echo get_template_directory_uri(); ?>/favicon.ico">
  		<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>">
		<?php wp_head(); ?>
		
		<?php global $grav_config; if($grav_config['jsUseRequire']): ?>
		<script data-main="<?php echo get_template_directory_uri(); ?>/library/js/require/require.config" src="<?php echo get_template_directory_uri(); ?>/library/js/require/require.min.js"></script>
		<?php endif; ?>
	
	</head>
	

	<body <?php body_class(); ?>>
	