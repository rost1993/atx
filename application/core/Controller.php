<?php

namespace IcKomiApp\core;

use IcKomiApp\core\View;
use IcKomiApp\models;
use IcKomiApp\core\Functions;

abstract class Controller {
	public $route;
	public $view;
	public $model;

	public function __construct($route) {
		$this->route = $route;
		$this->view = new View($this->route);
		$this->model = $this->loadModel($this->route['controller']);
	}

	public function loadModel($name) {
		$class_model = 'models\\' . ucfirst($name);
		if(class_exists($class_model)) {
			return new $class_model;
		}
	}

	public function redirect($url) {
		if(!is_string($url))
			return false;

		if(mb_strlen($url) == 0)
			return false;

		header('Location: ' . $url);
	}
}
