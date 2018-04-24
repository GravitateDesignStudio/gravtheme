<?php
namespace Grav\Vendor;

class WooCommerce {
	/**
	 * Add theme support for WooCommerce
	 *
	 * @since 2017.01.11
	 * @author DF
	 */
	public static function add_theme_support() {
		add_action('after_setup_theme', function() {
			add_theme_support('woocommerce');
		});
	}

	/**
	 * Modify the default WooCommerce loop output wrapper
	 *
	 * @param  string $openHTML  the open wrapper HTML
	 * @param  string $closeHTML the close wrapper HTML
	 *
	 * @since 2017.01.11
	 * @author DF
	 */
	public static function modify_woocommerce_wrapper($openHTML, $closeHTML) {
		remove_action('woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
		remove_action('woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);

		add_action('woocommerce_before_main_content', function() use (&$openHTML) {
			echo $openHTML;
		}, 10);

		add_action('woocommerce_after_main_content', function() use (&$closeHTML) {
			echo $closeHTML;
		}, 10);
	}

	/**
	 * Display additional WooCommerce fields in the product post editor
	 *
	 * @param int 	$post_id 	post id that will have admin fields added to it
	 * @param array $fields 	array of fields to add
	 *
	 * @since 2017.03.08
	 * @author DF
	 */
	public static function display_wc_fields($post_id, $fields=array()) {
		foreach ($fields as $field) {
			$base_values = array(
				'id' => $field['id'].'['.$post_id.']',
				'label' => $field['label'],
				'desc_tip' => isset($field['description']) ? true : false,
				'description' => isset($field['description']) ? $field['description'] : '',
				'value' => get_post_meta($post_id, $field['id'], true)
			);

			switch ($field['type']) {
				case 'textarea':
					\woocommerce_wp_textarea_input(array_merge($base_values, array(
						'placeholder' => isset($field['placeholder']) ? $field['placeholder'] : '',
					)));
					break;

				case 'select':
					\woocommerce_wp_select(array_merge($base_values, array(
						'options' => isset($field['options']) ? $field['options'] : '',
					)));
					break;

				case 'checkbox':
					\woocommerce_wp_checkbox($base_values);
					break;

				case 'hidden':
					unset($base_values['label']);
					unset($base_values['desc_tip']);
					unset($base_values['description']);

					\woocommerce_wp_hidden_input($base_values);
					break;

				case 'text':
				default:
					\woocommerce_wp_text_input(array_merge($base_values, array(
						'placeholder' => isset($field['placeholder']) ? $field['placeholder'] : '',
					)));
					break;
			}
		}
	}

	/**
	 * Save additional WooCommerce fields from the product post editor
	 *
	 * @param int 	$post_id 	post id that will have additional admin fields saved
	 * @param array $fields 	array of fields to save
	 *
	 * @since 2017.03.08
	 * @author DF
	 */
	public static function save_wc_fields($post_id, $fields=array()) {
		foreach ($fields as $field) {
			switch ($field['type']) {
				case 'checkbox':
					$data = isset($_POST[$field['id']][ $post_id ]) ? 'yes' : 'no';
					update_post_meta($post_id, $field['id'], $data);

					break;

				default:
					$data = $_POST[$field['id']][$post_id];
					if (!empty($data)) {
						update_post_meta($post_id, $field['id'], esc_attr($data));
					}
					break;
			}
		}
	}

	/**
	 * Add custom product fields
	 *
	 * @param array $fields array of fields to add
	 *
	 * @since 2017.03.09
	 * @author DF
	 */
	public static function add_product_fields($fields=array()) {

		foreach ($fields as &$field) {
			if (!is_array($field)) {
				throw new Exception(__CLASS__.'::'.__METHOD__.' - fields must be arrays');
			}

			// FIXME: don't check for labels on hidden input types
			if (!isset($field['id']) || !isset($field['label']) || !isset($field['type'])) {
				throw new Exception(__CLASS__.'::'.__METHOD__.' - fields must contain "id", "label", and "type" values');
			}
		}

		$class_ref = __CLASS__;

		add_action('woocommerce_product_options_general_product_data', function() use (&$fields, &$class_ref) {
			$class_ref::display_wc_fields(get_the_ID(), $fields);
		});

		// save the custom variation attributes when the product is saved/updated
		add_action('woocommerce_process_product_meta', function($post_id) use (&$fields, &$class_ref) {
			$class_ref::save_wc_fields($post_id, $fields);
		});
	}

	/**
	 * Add custom product variation fields
	 *
	 * @param array $fields array of fields to add
	 *
	 * @since 2017.02.17
	 * @author DF
	 */
	public static function add_product_variation_fields($fields=array()) {

		foreach ($fields as &$field) {
			if (!is_array($field)) {
				throw new Exception(__CLASS__.'::'.__METHOD__.' - fields must be arrays');
			}

			// FIXME: don't check for labels on hidden input types
			if (!isset($field['id']) || !isset($field['label']) || !isset($field['type'])) {
				throw new Exception(__CLASS__.'::'.__METHOD__.' - fields must contain "id", "label", and "type" values');
			}
		}

		$class_ref = __CLASS__;

		// add the custom variation attributes to the admin UI
		add_action('woocommerce_product_after_variable_attributes', function($loop, $variation_data, $variation) use (&$fields, &$class_ref) {
			$class_ref::display_wc_fields($variation->ID, $fields);
		}, 10, 3);

		// save the custom variation attributes when the product is saved/updated
		add_action('woocommerce_save_product_variation', function($post_id) use (&$fields, &$class_ref) {
			$class_ref::save_wc_fields($post_id, $fields);
		}, 10, 2);
	}

	/**
	 * Add custom product variation fields
	 *
	 * @param int 		$post_id 		post id to pull the value from
	 * @param string 	$field_name 	the variation field name
	 *
	 * @since 2017.03.08
	 * @author DF
	 */
	public static function get_product_variation_field($post_id, $field_name) {
		return get_post_meta($post_id, $field_name, true);
	}

	/**
	 * Return the permalink for a WooCommerce page
	 * 
	 * @param string	$page	page name to get the permalink for
	 * 
	 * @return string	the permalink
	 * 
	 * @since 2018.01.21
	 * @author DF
	 */
	public static function get_page_permalink($page) {
		if (!function_exists('wc_get_page_id')) {
			return '';
		}

		return get_the_permalink(\wc_get_page_id($page));
	}

	/**
	 * Get an attribute taxonomy name
	 * 
	 * @param string	$name	taxonomy name (ex: color)
	 * 
	 * @return string	the taxonomy term name (ex: pa_color)
	 * 
	 * @since 2018.01.21
	 * @author DF
	 */
	public static function get_attribute_taxonomy_name($name) {
		$wc_attribute_taxonomies = \wc_get_attribute_taxonomies();

		if (!$wc_attribute_taxonomies) {
			return '';
		}

		foreach ($wc_attribute_taxonomies as $tax) {
			$tax_name = \wc_attribute_taxonomy_name($tax->attribute_name);
	
			if (!$tax_name) {
				continue;
			}

			if ($tax->attribute_name == $name) {
				return $tax_name;
			}
		}

		return '';
	}
}
