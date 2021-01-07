<?php

namespace IcKomiApp\models;

use IcKomiApp\core\User;
use IcKomiApp\core\Model;
use IcKomiApp\core\Functions;
use IcKomiApp\lib\Database\DB;

/*
	Класс обновлений компонентов базы данных.
	Необходимо передать файл в формате .sql, который должен содержать строки с SQL кодом
	Каждый новый SQL-запрос должен разделяться знаками $$ для программы это будет сигналом того что следует обработать новый SQL-запрос.

	Рекомендуется помещать файлы для обновления компонентов базы данных в директорию sql, которая находится в корне движка.

	Rostislav Gashin (rost1993), 2021
*/
class Updater extends Model {

	// Глобальная переменная с тектом ошибки
	private $message_error = '';

	// Глобальный массив с перечислением 
	protected $array_file_extension = ['sql'];

	/*
		Функция загрузки файла обновления базы данных
		Возвращаемое значение:
			 1 - при успешном завершении работы функции
			-2 - при обрабатываемой ошибке. В переменную message_error будет установлен тект ошибки
			-1 - при необрабатываемой ошибке
	*/
	public function update_database($files) {
		if(empty($files))
			return [-2, 'Не передан файл обновления базы данных!'];

		foreach ($files as $file) {
			if(!$this->check_file($file))
				return [-2, $this->message_error];


			if(!$this->processing_file($file))
				return [-2, $this->message_error];
		}

		return [1];
	}

	/*
		Функция проверки расширения файла
		$files - массив с файлом
		Возвращает: TRUE - если все переданные файлы допустимого расширения, FALSE - если присутствует файл с недопустимым расширением
	*/
	private function check_file($file) {
		$name_explode = explode('.', $file['name']);
		$file_extension = $name_explode[count($name_explode) - 1];
			
		if(!in_array($file_extension, $this->array_file_extension)) {
			$this->message_error = 'Файл с таким расширением запрещено загружать на сервер!';
			return false;
		}
		return true;
	}

	/*
		Построчная обработка файла
		$file - массив с файлом, в котором содержится вся служебная информация о файле. По сути это один из элементов глобального массива $_FILES
		Возвращаемые значения:
			TRUE - в случае успешного выполения
			FALSE - в случае любой ошибки при этом будет установления переменная $this->message_error с текстом ошибки
	*/
	private function processing_file($file) {
		if(($fp = fopen($file['tmp_name'], 'r')) === false) {
			$this->message_error = "Ошибка при открытии файла!";
			return false;
		}

		$sql = '';
		while (!feof($fp)) {
  			//$buffer = trim(fgets($fp, 4096));
  			$buffer = fgets($fp, 4096);

    		if(preg_match('/\$\$/ui', $buffer)) {

    			if(!$this->query_database($sql))
    				return false;

    			$sql = '';
    			continue;
    		}
    		
    		$sql .= ' ' . $buffer;
		}

		if(!$this->query_database($sql))
    		return false;

    	// Close file
		fclose($fp);

		// Remove temp file
		unlink($file['tmp_name']);

		return true;
	}

	/*
		Выполнение SQL-запроса в базе данных
		$sql - SQL-запрос
		Возвращаемое значение:
			TRUE - в случае успешного выполнения
			FALSE - в случае ошибки, будет установлена переменная message_error
	*/
	private function query_database($sql) {
		if(empty($sql))
			return true;

		if(mb_strlen(trim($sql)) == 0)
			return true;

		try {
			if(DB::query($sql, DB::OTHER) === false) {
				$this->message_error = "Ошибка при выполнении запроса к базе данных: " . $sql;
				return false;
			}
		} catch(Exception $error) {
			$this->message_error = $error;
			return false;
		}

		return true;
	}
}