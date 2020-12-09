<?php

namespace IcKomiApp\core;

use IcKomiApp\core\User;
use IcKomiApp\core\CSSLoader;
use IcKomiApp\core\JavaScriptLoader;

/*
	Класс для формирования заголовков веб-ресурса
	Расставляются основные тэги, задается favicon, title, различные настройки,
	подгружаются CSS, JavaScript
*/
class HeaderLoader {

	// Default name web-resource
	protected static $web_title_default = 'IC Komi MVC framework';

	/*
		Функция генерирования заголовка веб-ресурса
		Генерируются кодировки, расставляются базовые тэги и т.д.
		Производится подгрузка CSS и JavaScript
	*/
	public static function getHeader() {

		$role = User::get('role');

		echo "<head>";
		echo "<meta http-equiv='Content-Type' content='text/html; charset=UTF-8;'>";
		echo "<meta charset='UTF-8'>";
		echo "<meta http-equiv='X-UA-Compatible' content='IE=edge'>";
		echo "<meta name='viewport' content='width=device-width, initial-scale=1.0, user-scalable=no'>";

		echo "<link rel='shortcut icon' href='" . self::getFavicon() . "' type='image/x-icon'>";

		CSSLoader::getCSS();
		JavaScriptLoader::getJavaScript();

		echo "<title>" . self::getTitle() . "</title>";

		echo "</head>";
	}

	/*
		Сконфигурировать название веб-ресурса
		Конфигурирование названия: config/web.php -> параметр title_web
	*/
	protected static function getTitle() {
		if(empty($GLOBALS['web_config']))
			return self::$web_title_default;

		if(!array_key_exists('title_web', $GLOBALS['web_config']))
			return self::$web_title_default;
		return $GLOBALS['web_config']['title_web'];
	}

	/*
		Сконфигурировать favicon
		Если в конфигурационном файле будет пусто, то будет сконфигурирована пустая иконка для сайта
		Конфигурирование favicon: config/web.php -> параметр favicon
	*/
	protected static function getFavicon() {
		if(empty($GLOBALS['web_config']))
			return "data:image/x-icon;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQEAYAAABPYyMiAAAABmJLR0T///////8JWPfcAAAACXBIWXMAAABIAAAASABGyWs+AAAAF0lEQVRIx2NgGAWjYBSMglEwCkbBSAcACBAAAeaR9cIAAAAASUVORK5CYII=";

		if(!array_key_exists('favicon', $GLOBALS['web_config']))
			return "data:image/x-icon;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQEAYAAABPYyMiAAAABmJLR0T///////8JWPfcAAAACXBIWXMAAABIAAAASABGyWs+AAAAF0lEQVRIx2NgGAWjYBSMglEwCkbBSAcACBAAAeaR9cIAAAAASUVORK5CYII=";

		if(!file_exists('assets/favicon/' . $GLOBALS['web_config']['favicon']))
			return "data:image/x-icon;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQEAYAAABPYyMiAAAABmJLR0T///////8JWPfcAAAACXBIWXMAAABIAAAASABGyWs+AAAAF0lEQVRIx2NgGAWjYBSMglEwCkbBSAcACBAAAeaR9cIAAAAASUVORK5CYII=";
		return 'assets/favicon/' . $GLOBALS['web_config']['favicon'];
	}

}