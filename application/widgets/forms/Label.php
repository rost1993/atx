<?php

namespace IcKomiApp\widgets\forms;

use IcKomiApp\widgets\forms\HtmlBasicElement;

class Label extends HtmlBasicElement {

	protected $base_html_element = "<label {class} {style} {params}>{value}</label>";
	protected $base_class = 'text-muted font-weight-bold';
	protected $base_style = 'font-size: 13px;';

	protected function special_processing_html_code($html) {
		return $html;
	}
}