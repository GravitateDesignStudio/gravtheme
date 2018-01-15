<?php
$acf_group = 'theme_options_footer';

acf_add_local_field_group(array (
    'key' => 'group_'.$acf_group,
    'title' => 'Footer Options',
    'fields' => array (
        array (
            'key' => 'field_'.$acf_group.'_copyright_text',
            'label' => 'Copyright Text',
            'name' => 'copyright_text',
            'type' => 'text',
            'instructions' => 'The Year is automatically added at the front of the text.',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array (
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'default_value' => get_bloginfo('name').'. All Rights Reserved.',
            'placeholder' => '',
            'formatting' => 'none',       // none | html
            'prepend' => '© '.date('Y').' ',
            'append' => '',
            'maxlength' => '',
            'readonly' => 0,
            'disabled' => 0,
        )
    ),
    'location' => array (
        array (
            array (
                'param' => 'options_page', // post_type | post | page | page_template | post_category | taxonomy | options_page
                'operator' => '==',
                'value' => 'acf-theme-options-footer',        // if options_page then use: acf-options  | if page_template then use:  template-example.php
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
