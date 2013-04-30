<!doctype html>
<html <?php language_attributes(); ?>>

	<head>
	
	
		<meta charset="utf-8">
		
		<title><?php bloginfo("name"); ?> <?php wp_title("&bull;"); ?></title>
		
		<!--<meta name="viewport" content="width=device-width">-->
		<meta name="application-name" content="<?php bloginfo('name'); ?>" />
		<link rel="shortcut icon" href="<?php echo get_template_directory_uri(); ?>/favicon.ico">
  		<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>">
  		
  		<link rel="stylesheet" type="text/css" href="<?php bloginfo("template_url"); ?>/library/css/master.css" />

		<script src="<?php bloginfo("template_url");?>/library/js/libs/jquery.js"></script>
		
		<?php wp_head(); ?>
		
	</head>
	

	<body <?php body_class(); ?>>
	