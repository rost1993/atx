<?php

namespace IcKomiApp\core;

use IcKomiApp\core\Router;
use IcKomiApp\core\Functions;

/*
	Класс, инициализирующий работу приложения.
	Производит настройки приложения и загружает приложение
*/
class Application {

	// Configurations file
	const PATH_CONFIG_FILE = __DIR__ . '/../../config/web.php';

	// Constructor class
	public function __construct() {
		ini_set('display_errors', 1);
		ini_set('max_execution_time', 600);
		error_reporting(E_ALL);

		if(file_exists(self::PATH_CONFIG_FILE))
			$GLOBALS['web_config'] = require_once(self::PATH_CONFIG_FILE);
	}

	// Initialization application
	public function run() {
		(new Router())->run();
	}

}
