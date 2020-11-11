<?php

namespace IcKomiApp\widgets;

/*
	Класс Downloader генерирует HTML-код для того чтобы автоматически подгружался индикатор загрузки.
	Автоматически генерируется элемент DIV с необходимым классом для того чтобы инициализировать JavaScript модуль downloader-ic-komi.js

	Copyright: Rostislav Gashin, 2020, Syktyvkar Komi Republic, rostislav-gashin@yandex.ru
*/
class Downloader {

	// Function get HTML-code for the downloader-ic-komi
	public static function getDownloader() {
		$modal = "<div class='downloader-ic-komi'></div>";
		return $modal;
	}
}