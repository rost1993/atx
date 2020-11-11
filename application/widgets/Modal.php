<?php

namespace IcKomiApp\widgets;

/*
	Класс Modal генерирует HTML-код для того чтобы автоматически подгружались различные типы модальных окон.
	Автоматически генерируется элемент DIV с необходимым классом для того чтобы инициализировать JavaScript модуль modal-ic-komi.js

	Copyright: Rostislav Gashin, 2020, Syktyvkar Komi Republic, rostislav-gashin@yandex.ru
*/
class Modal {

	// Function get HTML-code for the moda-window-basic
	public static function getModal() {
		$modal = "<div class='modal-ic-komi-basic'></div>";
		return $modal;
	}

	// Function get HTML-code for the modal-window-document-view
	public static function getModalDocumentView() {
		$modal = "<div class='modal-ic-komi-document-view'></div>";
		return $modal;
	}

	// Function get HTML-code for the modal-window-view
	public static function getModalView() {
		$modal = "<div class='modal-ic-komi-view'></div>";
		return $modal;
	}

	// Function get HTML-code for the modal-window-service-interface
	public static function getModalViewServiceInterface() {
		$modal = "<div class='modal-ic-komi-service-interface'></div>";
		return $modal;
	}
	
}