<?php

$acf_group = 'theme_options';
acf_add_local_field_group(array (
    'key' => 'group_'.$acf_group,
    'title' => 'Theme Options',
    'fields' => array (
        array (
            'key' => 'field_'.$acf_group.'_logo_tab',
            'label' => 'Logo',
            'name' => '',
            'type' => 'tab',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array (
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'placement' => 'top',
            'endpoint' => 0,
        ),
        array (
            'key' => 'field_'.$acf_group.'_logo',
            'label' => 'Logo Image',
            'name' => $acf_group.'_logo',
            'instructions' => '',
            'type' => 'image',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array (
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'return_format' => 'object',       // array | url | id
            'preview_size' => 'medium',
            'library' => 'all',       // all | uploadedTo
            'min_width' => '',
            'min_height' => '',
            'min_size' => '',
            'max_width' => '',
            'max_height' => '',
            'max_size' => '',
            'mime_types' => '',
        ),
        array (
            'key' => 'field_'.$acf_group.'_social_tab',
            'label' => 'Social Links',
            'name' => '',
            'type' => 'tab',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array (
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'placement' => 'top',
            'endpoint' => 0,
        ),
        array (
            'key' => 'field_'.$acf_group.'_social_links',
            'label' => 'field_label',
            'name' => $acf_group.'_social_links',
            'type' => 'repeater',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array (
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'collapsed' => '',
            'min' => '1',
            'max' => '',
            'layout' => 'table',         // table | block | row
            'button_label' => 'Add Social Icon',
            'sub_fields' => array (
                array (
                    'key' => 'field_'.$acf_group.'_social_title',
                    'label' => 'Title',
                    'name' => 'title',
                    'type' => 'text',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array (
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'default_value' => '',
                    'placeholder' => '',
                    'formatting' => 'none',       // none | html
                    'prepend' => '',
                    'append' => '',
                    'maxlength' => '',
                    'readonly' => 0,
                    'disabled' => 0,
                ),
                array (
                    'key' => 'field_'.$acf_group.'_social_link',
                    'label' => 'Link',
                    'name' => 'link',
                    'type' => 'text',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array (
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'default_value' => '',
                    'placeholder' => 'https://',
                    'formatting' => 'none',       // none | html
                    'prepend' => '',
                    'append' => '',
                    'maxlength' => '',
                    'readonly' => 0,
                    'disabled' => 0,
                ),
                array (
                    'key' => 'field_'.$acf_group.'_social_icon',
                    'label' => 'Icon Class',
                    'name' => 'icon',
                    'type' => 'text',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array (
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'default_value' => '',
                    'placeholder' => '',
                    'formatting' => 'none',       // none | html
                    'prepend' => '',
                    'append' => '',
                    'maxlength' => '',
                    'readonly' => 0,
                    'disabled' => 0,
                ),
            ),
        ),
    ),
    'location' => array (
        array (
            array (
                'param' => 'options_page', // post_type | post | page | page_template | post_category | taxonomy | options_page
                'operator' => '==',
                'value' => 'acf-options-theme',        // if options_page then use: acf-options  | if page_template then use:  template-example.php
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
