<?php

namespace IcKomiApp\widgets\forms;

use IcKomiApp\widgets\forms\HtmlBasicElement;

class Textarea extends HtmlBasicElement {

	protected $base_html_element = "<textarea {class} {style} {params}>{value}</textarea>";
	protected $base_class = 'form-control';
	protected $base_style = '';

	protected function special_processing_html_code($html) {
		return $html;
	}
}