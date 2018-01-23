<?php
namespace Grav\WP;

class MenuWalkerSelect extends \Walker_Nav_Menu {

	public $indent;

	public function __construct($indent='&dash;') {
		$this->indent = $indent;
	}

    // ignore start_lvl
    public function start_lvl(&$output, $depth=0, $args=array()) { }

    // ignore end_lvl
    public function end_lvl(&$output, $depth=0, $args=array()) { }

    public function start_el(&$output, $item, $depth=0, $args=array(), $id=0) {

		// add spacing to the title based on the current depth
		if ($depth) {
			$item->title = str_repeat($this->indent, $depth).' '.$item->title;
		}

		$output .= "<option value=\"{$item->url}\"";

		if (get_the_ID() == $item->object_id) {
			$output .= ' selected="selected"';
		}

		$output .= ">{$item->title}</option>\n";
    }

    // ignore end_el
    public function end_el(&$output, $item, $depth=0, $args=array()) {}
}
