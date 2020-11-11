<?php

namespace IcKomiApp\widgets\forms;

use IcKomiApp\widgets\forms\Label;
use IcKomiApp\widgets\forms\Input;
use IcKomiApp\widgets\forms\Select;
use IcKomiApp\widgets\forms\Textarea;
use IcKomiApp\widgets\forms\Checkbox;
use IcKomiApp\widgets\forms\Radio;

class FormBuilder {

	
	protected static $base_class_row = 'form-row mb-0';
	protected static $base_style_row = '';

	protected static $base_class_cell = 'col col-sm align-middle';
	protected static $base_style_cell = '';


	public static function build($title_form, $array_items = [], $array_values = []) {

		echo "<div class='row'>";
		echo "<div class='col'>";
		echo "<div class='card text-center border-dark' style='margin-top: 80px; background: #E6E6E6; min-width: 620px;'>";
		echo self::build_form_header($title_form);
		echo self::build_form_body($array_items);
		echo "</div></div></div>";
	}

	private static function build_form_header($title_form) {
		return "<div class='card-header'>
				<h4>" . $title_form . "</h4>
				<div id=''></div>
			</div>";
	}

	private static function build_form_body($array_items) {
		$html = "<div class='card-body' id=''>";
		$html .= "<div class='col'>";

		foreach($array_items as $row) {

			$class = " class='" . ((empty($row['class_row'])) ? self::$base_class_row : $row['class']) . "' ";
			$style = " style='" . ((empty($row['style_row'])) ? self::$base_style_row : $row['style']) . "' ";

			$html .= "<div" . $class . $style . ">";

			if(array_key_exists('objects', $row))
				$html .= self::get_html_row_objects($row['objects']);
			
			$html .= "</div>";
		}

		$html .= "</div></div>";
		return $html;
	}

	private static function get_html_row_objects($objects) {

		$html = '';
		foreach($objects as $object) {

			$class_cell = (empty($object['class_cell'])) ? self::$base_class_cell : $object['class_cell'];
			$style_cell = (empty($object['style_cell'])) ? self::$base_style_cell : $object['style_cell'];

			$html .= "<div class='" . $class_cell . "' style='" . $style_cell . "'>";

			$value = (empty($object['value'])) ? '' : $object['value'];
			$class = (empty($object['class'])) ? '' : $object['class'];
			$style = (empty($object['style'])) ? '' : $object['style'];
			$params = (empty($object['params'])) ? [] : $object['params'];

			switch(mb_strtolower($object['object'])) {
				case 'label':
					$html .= (new Label($value, $class, $style, $params))->build();
					break;

				case 'input':
					$html .= (new Input($value, $class, $style, $params))->build();
					break;

				case 'select':
					$html .= (new Select($value, $class, $style, $params))->build();
					break;

				case 'textarea':
					$html .= (new Textarea($value, $class, $style, $params))->build();
					break;

				case 'checkbox':
					$html .= (new Checkbox($value, $class, $style, $params))->build();
					break;

				case 'radio':
					$html .= (new Radio($value, $class, $style, $params))->build();
					break;

				default:
					$html .= '';
					break;
			}
			$html .= "</div>";
		}

		return $html;
	}

	private static function build_form_footer() {
		return "";
	}

	
	
}