<?php
namespace Grav\Vendor;

class GravityForms {
	private static $_form_choices;
	private static $_script_srcs = array();

    public static function get_all_forms() {
		if (!self::$_form_choices) {
			$form_choices = array();

            if (method_exists('RGFormsModel', 'get_forms')) {
			    $forms = \RGFormsModel::get_forms(null, 'title');

                foreach($forms as $form) {
			        $form_choices[$form->id] = $form->title;
			    }
			}

			self::$_form_choices = $form_choices;
		}

		return self::$_form_choices;
	}

	public static function move_scripts_to_footer() {
		// add_filter('gform_init_scripts_footer', '__return_true');
		add_filter('gform_get_form_filter', array(__CLASS__, '_move_scripts_form_filter'), 10, 2);
		add_action('wp_footer', array(__CLASS__, '_move_scripts_footer_print'), 999);
	}

	public static function _move_scripts_form_filter($form_string, $form) {
		$matches = array();

		preg_match_all("/<script\b[^>]*>([\s\S]*?)<\/script>/", $form_string, $matches);

		if (isset($matches[1]) && is_array($matches[1])) {
			if (is_array($matches[1])) {
				self::$_script_srcs = array_merge(self::$_script_srcs, array_values($matches[1]));
			} else {
				self::$_script_srcs[] = $matches[1];
			}

			return preg_replace("/<script\b[^>]*>([\s\S]*?)<\/script>/", '', $form_string);
		}

		return $form_string;
	}

	public static function _move_scripts_footer_print() {
		$scripts = array_unique(self::$_script_srcs);

		?>
		<script type="text/javascript">
			document.addEventListener('DOMContentLoaded', function() {
				<?php echo implode("\n\n", $scripts); ?>
			});
		</script>
		<?php
	}
}
