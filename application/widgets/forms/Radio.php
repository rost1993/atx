<?php

namespace IcKomiApp\widgets\forms;

use IcKomiApp\widgets\forms\HtmlBasicElement;

class Radio extends HtmlBasicElement {

	protected $base_html_element = "<div class='form-check'><input type='radio' {class} {style} {params}>"
								 . "<label class='form-check-label'>{value}</label></div>";
	protected $base_class = 'form-check-input';
	protected $base_style = '';

	protected function special_processing_html_code($html) {
		if(preg_match("/id='(.*?)'/i", $this->html_params, $matches) == 1) {
			$html = preg_replace('/<label/i', "<label for='" . $matches[1] . "' ", $html);
		}
		return $html;
	}
}