<?php
namespace Grav\WP;

// A php class to be used with an svg sprite sheet
class SVG {
    private static $svg_path = '';
    private static $symbols = array();
    private static $default_excludes = array();
    private static $use_handler_registered = false;
    private static $use_icon_ids = array();


    public static function set_default_symbols_array_exclusions($excludes) {
        if (!is_array($excludes)) {
            return;
        }

        self::$default_excludes = $excludes;
    }

    public static function use_symbols_file($path) {
        if (!file_exists($path)) {
            return;
        }

        self::$svg_path = $path;

        libxml_use_internal_errors(true);

        $dom = new \DOMDocument();
        // $dom->loadHTMLFile($path, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        $dom->loadHTMLFile($path);

        libxml_use_internal_errors(false);

        $svgs = $dom->getElementsByTagName('symbol');

        foreach ($svgs as $svg) {
            $id = $svg->getAttribute('id');
            $viewbox = $svg->getAttribute('viewbox');
            $preserve_aspect_ratio = $svg->getAttribute('preserveaspectratio');
            $markup = '';

            foreach ($svg->childNodes as $child) {
                if (version_compare(PHP_VERSION, '5.3.6') >= 0) {
                    $markup .= $child->ownerDocument->saveHTML($child);
                } else {
                    $markup .= $child->ownerDocument->saveXML($child);
                }
            }

            if ($id && !isset(self::$symbols[$id])) {
                self::$symbols[$id] = (object)array(
                    'id' => $id,
                    'viewbox' => $viewbox,
                    'markup' => $markup,
                    'preserveAspectRatio' => $preserve_aspect_ratio ? $preserve_aspect_ratio : ''
                );
            }
        }
    }

    /**
    * Outputs the array of SVGs from the sprite file
    *
    * @param none
    *
    * @return array of key value pair for icon class and icon title
    */
    public static function get_symbols_array($opts = array('remove_text' => 'icon-')) {
        $remove_text = isset($opts['remove_text']) ? $opts['remove_text'] : false;
        $exclude_icons = (isset($opts['exclude_icons']) && is_array($opts['exclude_icons'])) ? array_merge(self::$default_excludes, $opts['exclude_icons']) : self::$default_excludes;
        $icon_array = array();

        foreach (array_keys(self::$symbols) as $svg_id) {
            if (in_array($svg_id, $exclude_icons)) {
                continue;
            }

            $icon_label = $svg_id;

            if ($remove_text) {
                $icon_label = str_replace($remove_text, '', $icon_label);
            }

            $icon_label = ucwords(str_replace(array('-', '_'), ' ', $icon_label));

            $icon_array[$svg_id] = $icon_label;
        }

        return $icon_array;
    }

    /**
    * Returns the svg markup of a specific SVG built from the sprite file
    *
    * @param string $icon the icon id within the sprite file
    *
    * @return string the svg content
    */
    public static function get_svg_symbol($icon, $opts = array()) {
        if (!in_array($icon, array_keys(self::$symbols))) {
            return '';
        }

        $class = (isset($opts['class']) && is_string($opts['class'])) ? $opts['class'] : $icon;
        $output = '';

        $attributes = array(
            'class="'.$class.'"',
            'viewBox="'.self::$symbols[$icon]->viewbox.'"'
        );

        if (isset($opts['preserve_aspect_ratio'])) {
            $attributes[] = 'preserveAspectRatio="'.$opts['preserve_aspect_ratio'].'"';
        } else if (!isset($opts['preserve_aspect_ratio']) && self::$symbols[$icon]->preserveAspectRatio) {
            $attributes[] = 'preserveAspectRatio="'.self::$symbols[$icon]->preserveAspectRatio.'"';
        }

        if (isset($opts['no_use']) && $opts['no_use']) {
            // direct output
            $output = '<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" xml:space="preserve" '.implode(' ', $attributes).'>'.self::$symbols[$icon]->markup.'</svg>';
        } else {
            // 'use' reference
            $output = '<svg '.implode(' ', $attributes).'><use xlink:href="#'.$icon.'"></use></svg>';

            if (!in_array($icon, self::$use_icon_ids)) {
                self::$use_icon_ids[] = $icon;
            }

            self::register_use_handler();
        }

        return $output;
    }

    public static function register_use_handler() {
        if (self::$use_handler_registered) {
            return;
        }

        add_action('wp_footer', array('\Grav\WP\SVG', 'svg_use_handler'));

        self::$use_handler_registered = true;
    }

    public static function svg_use_handler() {
        foreach (self::$use_icon_ids as $icon_id) {
            echo '<svg style="display:none" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="'.self::$symbols[$icon_id]->viewbox.'" xml:space="preserve"><symbol id="'.$icon_id.'">'.self::$symbols[$icon_id]->markup.'</symbol></svg>';
        }
    }

    /**
    * Outputs the svg markup of a specific SVG built from the sprite file
    *
    * @param string $icon the icon id within the sprite file
    *
    * @return will echo out the svg with the content if it was found
    */
    public static function the_svg_symbol($icon, $opts = array()) {
        echo self::get_svg_symbol($icon, $opts);
    }

    public static function get_clean_svg($filename, $opts=array()) {
		if (!file_exists($filename)) {
			if (isset($opts['debug']) && $opts['debug']) {
				error_log(__METHOD__." - failed to open file [{$filename}]");
			}

			return;
		}

		$content = preg_replace(array(
			'/(<\?xml\ .*\?>)/i', // remove XML tag - causes issues with W3C validation
			'/(<!--.*-->)/i', // remove comments
			'/(\<title>[^\<]+\<\/title\>)/', // remove 'title' tag
			'/(\<desc>[^\<]+\<\/desc\>)/', // remove 'desc' tag
			'/\s\s+/', // remove 2+ sequential spaces
			'/(\n|\r)/' // remove line breaks
		), '', file_get_contents($filename));

		// remove 'id' attributes
		if (isset($opts['remove_ids']) && $opts['remove_ids']) {
			$content = preg_replace('/(id=\"[^\"]+\")/', '', $content);
		}

		if (isset($opts['debug']) && $opts['debug']) {
			error_log($filename);
			error_log($content);
		}

		return $content;
	}
}
