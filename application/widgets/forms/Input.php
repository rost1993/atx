<?php

namespace IcKomiApp\widgets\forms;

use IcKomiApp\widgets\forms\HtmlBasicElement;

class Input extends HtmlBasicElement {

	protected $base_html_element = "<input {class} {style} value='{value}' {params}>";
	protected $base_class = 'form-control form-control-sm black-text';
	protected $base_style = '';

	protected function special_processing_html_code($html) {
		return $html;
	}
}