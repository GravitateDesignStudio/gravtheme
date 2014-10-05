<?php

$acf_layout = array (
				'name' => 'sideimage',
				'label' => 'Content With Image',
				'display' => 'row',
				'sub_fields' => array (
					$acf_background,
					$acf_background_image,
					array (
						'key' => 'field_sideimage_0000000000001',
						'label' => 'Image Placement',
						'name' => 'image_placement',
						'prefix' => '',
						'type' => 'radio',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'column_width' => '',
						'choices' => array (
							'left' => 'Left',
							'right' => 'Right',
						),
						'other_choice' => 0,
						'save_other_choice' => 0,
						'default_value' => '',
						'layout' => 'horizontal',
					),
					array (
						'key' => 'field_sideimage_0000000000002',
						'label' => 'Image Size',
						'name' => 'image_size',
						'prefix' => '',
						'type' => 'radio',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'column_width' => '',
						'choices' => array (
							'small' => 'Small',
							'medium' => 'Medium',
							'large' => 'Large',
						),
						'other_choice' => 0,
						'save_other_choice' => 0,
						'default_value' => '',
						'layout' => 'horizontal',
					),
					array (
						'key' => 'field_sideimage_0000000000003',
						'label' => 'Content',
						'name' => 'content',
						'prefix' => '',
						'type' => 'wysiwyg',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'column_width' => '',
						'default_value' => '',
						'tabs' => 'all',
						'toolbar' => 'full',
						'media_upload' => 0,
					),
					array (
						'key' => 'field_sideimage_0000000000004',
						'label' => 'Images',
						'name' => 'images',
						'prefix' => '',
						'type' => 'repeater',
						'instructions' => 'If more than one image is uploaded, image area will become a slider.',
						'required' => 0,
						'conditional_logic' => 0,
						'column_width' => '',
						'min' => '',
						'max' => '',
						'layout' => 'row',
						'button_label' => 'Add Image',
						'sub_fields' => array (
							array (
								'key' => 'field_sideimage_0000000000005',
								'label' => 'Image',
								'name' => 'image',
								'prefix' => '',
								'type' => 'image',
								'instructions' => '',
								'required' => 0,
								'conditional_logic' => 0,
								'column_width' => '',
								'return_format' => 'array',
								'preview_size' => 'medium',
								'library' => 'all',
							),
						),
					),
				),
				'min' => '',
				'max' => '',
			);

?>