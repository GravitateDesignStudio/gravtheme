<?php
$acf_group = 'theme_options_scripts';

acf_add_local_field_group(array (
    'key' => 'group_'.$acf_group,
    'title' => 'Scripts',
    'fields' => array (
        array (
            'key' => 'field_'.$acf_group.'_global_head_top_content',
            'label' => 'Top &lt;head&gt; Tag Content',
            'name' => 'global_head_top_content',
            'type' => 'textarea',
            'instructions' => 'This will be inserted at the top of the &lt;head&gt; tag on all pages.<br>Warning! This must be formatted correctly or could break the website.',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array (
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'default_value' => '',
            'placeholder' => '',
            'maxlength' => '',
            'rows' => '',
            'new_lines' => '',        // wpautop | br | ''
            'readonly' => 0,
            'disabled' => 0,
        ),
        array (
            'key' => 'field_'.$acf_group.'_global_head_bottom_content',
            'label' => 'Bottom &lt;head&gt; Tag Content',
            'name' => 'global_head_bottom_content',
            'type' => 'textarea',
            'instructions' => 'This will be inserted at the end of the &lt;head&gt; tag on all pages.<br>Warning! This must be formatted correctly or could break the website.',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array (
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'default_value' => '',
            'placeholder' => '',
            'maxlength' => '',
            'rows' => '',
            'new_lines' => '',        // wpautop | br | ''
            'readonly' => 0,
            'disabled' => 0,
        ),
        array (
            'key' => 'field_'.$acf_group.'_global_body_top_content',
            'label' => 'Top &lt;body&gt; Tag Content',
            'name' => 'global_body_top_content',
            'type' => 'textarea',
            'instructions' => 'This will be inserted at the top of the &lt;body&gt; tag on all pages.<br>Warning! This must be formatted correctly or could break the website.',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array (
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'default_value' => '',
            'placeholder' => '',
            'maxlength' => '',
            'rows' => '',
            'new_lines' => '',        // wpautop | br | ''
            'readonly' => 0,
            'disabled' => 0,
        ),
        array (
            'key' => 'field_'.$acf_group.'_global_body_bottom_content',
            'label' => 'Bottom &lt;body&gt; Tag Content',
            'name' => 'global_body_bottom_content',
            'type' => 'textarea',
            'instructions' => 'This will be inserted at the end of the &lt;body&gt; tag on all pages.<br>Warning! This must be formatted correctly or could break the website.',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array (
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'default_value' => '',
            'placeholder' => '',
            'maxlength' => '',
            'rows' => '',
            'new_lines' => '',        // wpautop | br | ''
            'readonly' => 0,
            'disabled' => 0,
        ),
    ),
    'location' => array (
        array (
            array (
                'param' => 'options_page', // post_type | post | page | page_template | post_category | taxonomy | options_page
                'operator' => '==',
                'value' => 'acf-theme-options-scripts',        // if options_page then use: acf-options  | if page_template then use:  template-example.php
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
