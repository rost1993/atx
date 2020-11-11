<?php

namespace IcKomiApp\core;

use IcKomiApp\controllers;
use IcKomiApp\core\Rights;

class Router {
	
	protected $routes = [];
	protected $params = [];
	protected $url = '';

	public function __construct() {
		$arr = require_once('../config/routes.php');
		foreach($arr as $key => $val)
			$this->add($key, $val);
		$this->get_url();
	}

	private function add($route, $params) {
		$route = '#^' . $route . '$#';
		$this->routes[$route] = $params;
	}

	private function get_url() {
		$url = trim($_SERVER['REQUEST_URI'], '/');
		$url = preg_replace('/\..*?$/i', '', $url);
		$url = preg_replace('/\?.*?$/i', '', $url);

		$this->url = $url;
	}

	private function match() {
		foreach($this->routes as $route => $params) {
			if(preg_match($route, $this->url, $matches)) {
				$this->params = $params;
				return true;
			}
		}
		return false;
	}

	public function run() {
		if(!$this->match())
			View::errorCode(404);

		$class_controller = 'IcKomiApp\controllers\\' . ucfirst($this->params['controller']) . 'Controller';
		if(!class_exists($class_controller))
			View::errorCode(404);

		$action = $this->params['action'] . 'Action';
		if(!method_exists($class_controller, $action))
			View::errorCode(404);

		if(!Rights::check_access_page($this->url))
			View::errorCode(403);

		$controller = new $class_controller($this->params);
		$controller->$action();
	}

}