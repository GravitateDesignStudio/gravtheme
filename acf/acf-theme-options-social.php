<?php
$acf_group = 'theme_options_social';
$social_sprites = Grav\WP\SVGSpriteManager::get_sprite('social');
$sprite_symbols = $social_sprites->get_symbols_array();

acf_add_local_field_group(array (
    'key' => 'group_'.$acf_group,
    'title' => 'Social Media Settings',
    'fields' => array (
        array (
            'key' => 'field_'.$acf_group.'_site_links',
            'label' => 'Social Links',
            'name' => $acf_group.'_site_links',
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
            'min' => 0,
            'max' => '',
            'layout' => 'table',         // table | block | row
            'button_label' => 'Add Social Media Link',
            'sub_fields' => array (
                array (
                    'key' => 'field_'.$acf_group.'_link_title',
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
                    'key' => 'field_'.$acf_group.'_link_url',
                    'label' => 'URL',
                    'name' => 'url',
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
                    'key' => 'field_'.$acf_group.'_link_icon',
                    'label' => 'Icon',
                    'name' => 'icon',
                    'type' => 'select',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array (
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'choices' => $sprite_symbols,
                    'default_value' => (is_array($sprite_symbols) && isset($sprite_symbols[0])) ? $sprite_symbols[0] : '',
                    'allow_null' => 0,
                    'multiple' => 0,         // allows for multi-select
                    'ui' => 0,               // creates a more stylized UI
                    'ajax' => 0,
                    'placeholder' => '',
                    'disabled' => 0,
                    'readonly' => 0,
                ),
            ),
        ),
    ),
    'location' => array (
        array (
            array (
                'param' => 'options_page', // post_type | post | page | page_template | post_category | taxonomy | options_page
                'operator' => '==',
                'value' => 'acf-theme-options-social',        // if options_page then use: acf-options  | if page_template then use:  template-example.php
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
