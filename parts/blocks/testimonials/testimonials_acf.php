<?php
	$acf_layout = array (
					'label' => 'Testimonials',
					'name' => 'testimonials',
					'display' => 'row',
					'min' => '',
					'max' => '',
					'sub_fields' => array (
						$background_acf,
						$background_acf_image,
						array (
							'key' => 'field_testimonials_1000000000001',
							'label' => 'Testimonials',
							'name' => 'testimonials',
							'type' => 'repeater',
							'column_width' => '',
							'sub_fields' => array (
								array (
									'key' => 'field_testimonials_1000000000002',
									'label' => 'Testimonial',
									'name' => 'testimonial',
									'type' => 'textarea',
									'column_width' => '',
									'default_value' => '',
									'placeholder' => '',
									'maxlength' => '',
									'rows' => '',
									'formatting' => 'none',
								),
								array (
									'key' => 'field_testimonials_1000000000003',
									'label' => 'Image',
									'name' => 'image',
									'type' => 'image',
									'instructions' => '(Optional)',
									'column_width' => '',
									'save_format' => 'object',
									'preview_size' => 'thumbnail',
									'library' => 'all',
								),
								array (
									'key' => 'field_testimonials_1000000000004',
									'label' => 'Attribution',
									'name' => 'attribution',
									'type' => 'text',
									'instructions' => '(Optional)',
									'column_width' => '',
									'default_value' => '',
									'placeholder' => '',
									'prepend' => '',
									'append' => '',
									'formatting' => 'none',
									'maxlength' => '',
								),
							),
							'row_min' => '',
							'row_limit' => '',
							'layout' => 'row',
							'button_label' => 'Add Testimonial',
						),
					),
				);
?>