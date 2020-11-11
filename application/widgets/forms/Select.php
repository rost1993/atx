<?php

namespace IcKomiApp\widgets\forms;

class Select extends HtmlBasicElement {

	protected $base_html_element = "<select {class} {style} {params}>{value}</select>";
	protected $base_class = 'custom-select custom-select-sm black-text';
	protected $base_style = '';

	protected function special_processing_html_code($html) {
		return $html;
	}
}