<?php
$acf_group = 'theme_options_404';

acf_add_local_field_group(array (
    'key' => 'group_'.$acf_group,
    'title' => '404 Settings',
    'fields' => array (
		array ( 
			'key' => 'field_'.$acf_group.'_404_title',
			'label' => '404 Title',
			'name' => $acf_group.'_title',
			'type' => 'text',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array (
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => '404 - Not Found',
			'placeholder' => '',
			'formatting' => 'none',       // none | html
			'prepend' => '',
			'append' => '',
			'maxlength' => '',
			'readonly' => 0,
			'disabled' => 0,
		),
		array (
			'key' => 'field_'.$acf_group.'_404_content',
			'label' => '404 Content',
			'name' => $acf_group.'_content',
			'type' => 'wysiwyg',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array (
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => "We're sorry, the page you requested cannot be found.\n\nIf you typed the URL yourself, please make sure that the spelling is correct.\nIf you clicked on a link to get here, there may be a problem with the link.\nTry using your browsers \"Back\" button or the \"Return to previous page\" link below to choose a different link on that page, or use search to find what you are looking for.\n\nWe apologize for the inconvenience!",
			'tabs' => 'all',         // all | visual | text
			'toolbar' => 'full',     // full | basic
			'media_upload' => 1,
		),
		array (
			'key' => 'field_'.$acf_group.'_404_blocks',
			'label' => '404 Blocks',
			'name' => '404_blocks',
			'type' => 'clone',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array (
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'clone' => array (
				0 => 'group_grav_blocks',
			),
			'display' => 'seamless',
			'layout' => 'block',
			'prefix_label' => 1,
			'prefix_name' => 1,
		),
    ),
    'location' => array (
        array (
            array (
                'param' => 'options_page', // post_type | post | page | page_template | post_category | taxonomy | options_page
                'operator' => '==',
                'value' => 'acf-theme-options-404',        // if options_page then use: acf-options  | if page_template then use:  template-example.php
                'order_no' => 0,
                'group_no' => 1,
            ),
        ),
    ),
    'menu_order' => 0,
    'position' => 'normal',                 // side | normal | acf_after_title
    'style' => 'default',                    // default | seamless
    'label_placement' => 'top',                // top | left
    'instruction_placement' => 'label',     // label | field
    'hide_on_screen' => array (
      //    0 => 'permalink',
      //    1 => 'the_content',
      //    2 => 'excerpt',
      //    3 => 'custom_fields',
      //    4 => 'discussion',
      //    5 => 'comments',
      //    6 => 'revisions',
      //    7 => 'slug',
      //    8 => 'author',
      //    9 => 'format',
      //    10 => 'featured_image',
      //    11 => 'categories',
      //    12 => 'tags',
      //    13 => 'send-trackbacks',
    ),
    'active' => 1,
    'description' => '',
));
