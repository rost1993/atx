<?php

namespace IcKomiApp\core;

/*
	Класс для взаимодействия с данными COOKIE
	Позволяет добавлять, изменять, удалять данные COOKIE
*/
class Cookie {

	// Время жизни COOKIE
	protected static $life_time = 60*60*24*30*12;

	/*
		Получение значение массиваCOOKIE
		$name - название параметра
		Возвращаемое значение: null - если параметр не найден, или значение параметра
	*/
	public static function get($name) {
		return ((empty($_COOKIE[$name])) ? null : $_COOKIE[$name]);
	}

	/*
		Получить значение массива COOKIE, если не будет найден то будет возвращено значение по умолчанию
		$name - значение параметра
		$default_value - значение по умолчанию
	*/
	public static function get_value($name, $default_value) {
		return ((empty($_COOKIE[$name])) ? $default_value : $_COOKIE[$name]);
	}

	/*
		Установить значение параметра в массив COOKIE
		$name - название параметра
		$value - значение параметра
	*/
	public static function set($name, $value = '') {
		setcookie($name, $value, time() + self::$life_time, '/');
	}

	/*
		Проверка существует или нет указанный параметр в массиве COOKIE
		$name - название параметра
		Возвращаемое значение: TRUE - существует, FALSE - не существует
	*/
	public static function has($name) {
		return !(empty($_COOKIE[$name]));
	}

}