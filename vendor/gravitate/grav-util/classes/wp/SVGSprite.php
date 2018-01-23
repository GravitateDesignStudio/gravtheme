<?php
namespace Grav\WP;

class SVGSprite {
	private $symbols = array();
	private $default_excludes = array();
	private $use_handler_registered = false;
	private $use_icon_ids = array();

	public function __construct($filename) {
		if (!file_exists($filename)) {
			return;
		}
	
		libxml_use_internal_errors(true);
	
		$dom = new \DOMDocument();
		// $dom->loadHTMLFile($filename, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
		$dom->loadHTMLFile($filename);
	
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
	
			if ($id && !isset($this->symbols[$id])) {
				$this->symbols[$id] = (object)array(
					'id' => $id,
					'viewbox' => $viewbox,
					'markup' => $markup,
					'preserveAspectRatio' => $preserve_aspect_ratio ? $preserve_aspect_ratio : ''
				);
			}
		}
	}
	
	
	public function set_default_symbols_array_exclusions($excludes) {
		if (!is_array($excludes)) {
			return;
		}
	
		$this->default_excludes = $excludes;
	}
	
	/**
	* Outputs the array of SVGs from the sprite file
	*
	* @param none
	*
	* @return array of key value pair for icon class and icon title
	*/
	public function get_symbols_array($opts = array()) {
		$remove_text = isset($opts['remove_text']) ? $opts['remove_text'] : 'icon-';
		$exclude_icons = (isset($opts['exclude_icons']) && is_array($opts['exclude_icons'])) ? array_merge($this->default_excludes, $opts['exclude_icons']) : $this->default_excludes;
		$icon_array = array();
	
		foreach (array_keys($this->symbols) as $svg_id) {
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
	public function get_svg_symbol($icon, $opts = array()) {
		if (!in_array($icon, array_keys($this->symbols))) {
			return '';
		}

		$class = (isset($opts['class']) && is_string($opts['class'])) ? $opts['class'] : $icon;
		$output = '';
	
		$attributes = array(
			'class="'.$class.'"',
			'viewBox="'.$this->symbols[$icon]->viewbox.'"'
		);
	
		if (isset($opts['preserve_aspect_ratio'])) {
			$attributes[] = 'preserveAspectRatio="'.$opts['preserve_aspect_ratio'].'"';
		} else if (!isset($opts['preserve_aspect_ratio']) && $this->symbols[$icon]->preserveAspectRatio) {
			$attributes[] = 'preserveAspectRatio="'.$this->symbols[$icon]->preserveAspectRatio.'"';
		}
	
		if (isset($opts['no_use']) && $opts['no_use']) {
			// direct output
			$output = '<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" xml:space="preserve" '.implode(' ', $attributes).'>'.$this->symbols[$icon]->markup.'</svg>';
		} else {
			// 'use' reference
			$output = '<svg '.implode(' ', $attributes).'><use xlink:href="#'.$icon.'"></use></svg>';
	
			if (!in_array($icon, $this->use_icon_ids)) {
				$this->use_icon_ids[] = $icon;
			}
	
			$this->register_use_handler();
		}

		return $output;
	}

	/**
	* Outputs the svg markup of a specific SVG built from the sprite file
	*
	* @param string $icon the icon id within the sprite file
	*
	* @return will echo out the svg with the content if it was found
	*/
	public function the_svg_symbol($icon, $opts = array()) {
		echo $this->get_svg_symbol($icon, $opts);
	}
	
	protected function register_use_handler() {
		if ($this->use_handler_registered) {
			return;
		}
	
		add_action('wp_footer', array(&$this, 'svg_use_handler'));
	
		$this->use_handler_registered = true;
	}
	
	public function svg_use_handler() {
		foreach ($this->use_icon_ids as $icon_id) {
			echo '<svg style="display:none" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="'.$this->symbols[$icon_id]->viewbox.'" xml:space="preserve"><symbol id="'.$icon_id.'">'.$this->symbols[$icon_id]->markup.'</symbol></svg>';
		}
	}
}
