<?php

namespace IcKomiApp\core;

/*
	Класс для подгрузки JavaScript файлов
	Происходит автоматическая подгрузка JavaScript из файла конфигурации
	Файл конфигурации: config/javascript.php
	Необходимо помещать файлы в папку web/assets/javascript
*/
class JavaScriptLoader {

	// Path configuration file
	protected static $javascript_conf_path = '../config/javascript.php';

	// Loading JavaScript files
	public static function getJavaScript() {
		if(!file_exists(self::$javascript_conf_path))
			return;

		$arr_javascript = require_once(self::$javascript_conf_path);
		foreach ($arr_javascript as $script) {
			$path_to_javascript = "assets/js/" . $script;
			if(file_exists($path_to_javascript)) {
				$src = $path_to_javascript . '?ver=' . md5_file($path_to_javascript);
				echo "<script src='" . $src . "'></script>";
			}
		}
	}
	
}