<?php

namespace IcKomiApp\core;

class View {

	public $path;
	public $route;
	public $layout = 'default';

	public function __construct($route) {
		$this->route = $route;
		$this->path = 'site/' . $route['action'];
	}

	public function render($vars = []) {
		extract($vars);

		$file_view = '../views/' . $this->path . '.php';

		if(file_exists($file_view)) {
			ob_start();
			require_once('../views/' . $this->path . '.php');
			$content = ob_get_clean();
			require_once('../views/layouts/' . $this->layout . '.php');
		} else {
			$this->errorCode(404);
		}
	}

	public static function errorCode($code) {
		http_response_code($code);
		$error_page = '../views/layouts/errors/' . $code . '.php';
		if(file_exists($error_page))
			require_once($error_page);
		exit;
	}

	public static function redirect($url) {
		header('location: ' . $url);
		exit;
	}
}