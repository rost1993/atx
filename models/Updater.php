<?php

namespace IcKomiApp\models;

use IcKomiApp\core\User;
use IcKomiApp\core\Model;
use IcKomiApp\core\Functions;
use IcKomiApp\lib\Database\DB;

class Updater extends Model {

	private $message_error = '';
	protected $array_file_extension = ['sql'];


	private $db_name = 'atx';
	private $host = 'localhost';
	private $login = 'atx';
	private $password = 'AtxDatabase2020';
	private $charset = 'utf8mb4';


	private function connect() {
		$link = mysqli_connect($this->host, $this->login, $this->password, $this->db_name);
		
		if(!$link)
			return null;
		
		mysqli_query($link, "SET NAMES '" . $this->charset . "'");
		return $link;
	}

	private function disconnect($link) {
		return mysqli_close($link);
	}

	private function multi_query($link, $sql, $error) {
		if(!mysqli_multi_query($link, $sql)) {
			$this->message_error = $error;
			return false;
		}

		do {
        	if ($result = mysqli_store_result($link))
            	mysqli_free_result($result);
            if(!mysqli_more_results($link))
            	break;
    	} while (mysqli_next_result($link));
    	return true;
	}


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

		if(($link = $this->connect()) === null)
			return [-2, 'Ошибка при подключении к MySQL!'];

		foreach ($files as $file) {
			if(!$this->check_file($file))
				return [-2, $this->message_error];


			if(!$this->processing_file($link, $file))
				return [-2, $this->message_error];
		}

		return true;
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
		Обработка файла
	*/
	private function processing_file($link, $file) {

		$fp = fopen($file['tmp_name'], 'r');

		$sql = '';
		while (!feof($fp)) {
  			$buffer = trim(fgets($fp, 4096));

    		if(preg_match('/\$\$/ui', $buffer)) {

    			if(!$this->query_database($link, $sql))
    				return false;

    			$sql = '';
    			continue;
    		}
    		
    		$sql .= $buffer;
		}

		if(!$this->query_database($link, $sql))
    		return false;

		fclose($fp);

		$this->message_error = $sql;
		return false;
		//return true;
	}

	/*
		Выполнение SQL-запроса в базе данных
		$sql - SQL-запрос
		Возвращаемое значение:
			TRUE - в случае успешного выполнения
			FALSE - в случае ошибки, будет установлена переменная message_error
	*/
	private function query_database($link, $sql) {
		if(empty($sql))
			return true;

		if(mb_strlen(trim($sql)) == 0)
			return true;

		if(!$this->multi_query($link, $sql, 'Ошибка при создании триггеров adm_offense!'))
			return false;

		//try {
			/*if(DB::query($sql, DB::OTHER) === false) {
				$this->message_error = "Ошибка при выполнении запроса к базе данных: " . $sql;
				return false;
			}*/
		/*} catch(Exception $error) {
			$this->message_error = $error;
			return false;
		}*/

		return true;
	}
}