<!doctype html>
<html <?php language_attributes(); ?>>


<head>

	<title><?php bloginfo("name"); ?> <?php wp_title('&bull;'); ?></title>

	<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="application-name" content="<?php bloginfo('name'); ?>" />

	<link rel="shortcut icon" href="<?php echo get_template_directory_uri(); ?>/library/images/favicon.ico">
	<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>">

	<?php wp_head(); ?>

</head>



<body id="body" <?php body_class(); ?>>

<main id="global-content" class="global-wrapper">
