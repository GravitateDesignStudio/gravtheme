<?php
	$acf_layout = array (
				'label' => 'HTML',
				'name' => 'html',
				'display' => 'row',
				'min' => '',
				'max' => '',
				'sub_fields' => array (
					$background_acf, 
					$background_acf_image, 
					array (
						'key' => 'field_html_1000000000001',
						'label' => 'HTML Content',
						'name' => 'content',
						'type' => 'wysiwyg',
						'default_value' => '',
						'toolbar' => 'full',
						'media_upload' => 'yes',
					),
				),
			);
?>