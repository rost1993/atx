<?php

namespace IcKomiApp\core;

/*
	Класс для подгрузки каскадных таблиц стилей CSS
	Происходит автоматическая подгрузка CSS из файла конфигурации
	Файл конфигурации: config/css.php
	Необходимо помещать файлы в папку web/assets/css
*/
class CSSLoader {

	// Path configuration file
	protected static $css_conf_path = '../config/css.php';

	// Loading CSS files
	public static function getCSS() {
		if(!file_exists(self::$css_conf_path))
			return;

		$arr_css = require_once(self::$css_conf_path);
		foreach ($arr_css as $css) {
			$path_to_css = "assets/css/" . $css;
			if(file_exists($path_to_css)) {
				$src = $path_to_css . '?ver=' . md5_file($path_to_css);
				echo "<link rel='stylesheet' href='" . $src . "'>";
			}
		}
	}
	
}