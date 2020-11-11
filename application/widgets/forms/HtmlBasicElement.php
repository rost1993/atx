<?php

namespace IcKomiApp\widgets\forms;

abstract class HtmlBasicElement {

	protected $base_html_element = '';
	protected $base_class = '';
	protected $base_style = '';

	protected $html_value = '';
	protected $html_class = '';
	protected $html_style = '';
	protected $html_params = '';

	public function __construct($value = '', $class = '', $style = '', $params = []) {
		$this->get_class($class);
		$this->get_style($style);
		$this->get_params($params);
		$this->get_value($value);
	}

	public function build() {
		$array_patterns = ['/{class}/i', '/{style}/i', '/{params}/i', '/{value}/i'];
		$array_replacements = [$this->html_class, $this->html_style, $this->html_params, $this->html_value];

		$html = preg_replace($array_patterns, $array_replacements, $this->base_html_element);
		$html = $this->special_processing_html_code($html);

		return $html;
	}

	private function get_class($class = '') {
		if(!is_string($class)) {
			$this->html_class = '';
			return;
		}

		if(empty($class) || (mb_strlen($class) == 0))
			$this->html_class = " class='" . $this->base_class . "' ";
		else
			$this->html_class = " class='" . $class . "' ";
	}

	private function get_style($style = '') {
		if(!is_string($style)) {
			$this->html_style = '';
			return;
		}

		if(empty($style) || (mb_strlen($style) == 0))
			$this->html_style = " style='" . $this->base_style . "' ";
		else
			$this->html_style = " style='" . $style . "' ";
	}

	private function get_params($params = []) {
		if(!is_array($params)) {
			$this->html_params = [];
			return;
		}

		foreach($params as $param => $value)
			$this->html_params .= " " . $param . "='" . $value . "'";
	}

	private function get_value($value) {
		$this->html_value = $value;
	}

	abstract protected function special_processing_html_code($html);
}