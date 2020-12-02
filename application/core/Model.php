<?php

namespace IcKomiApp\core;

use IcKomiApp\lib\Database\DB;
use IcKomiApp\core\User;
use IcKomiApp\core\Logic;
use IcKomiApp\core\Functions;
use IcKomiApp\core\FileUpload;

abstract class Model {

	protected $table = '';
	protected $field = [];
	protected $logic = [];
	protected $user_field = 'sh_polz';

	protected $sql_get_record = '';
	protected $sql_get_list = '';
	protected $list_items_for_one_page = 100;

	protected $trigger_operation = 0;
	protected $remove_directory = 0;
	protected $array_file_extension = ['pdf'];

	public function __construct($object = '') {
	}


	// Функция сохранения объекта
	public function save($array_data) {
		if(empty($array_data['JSON']) || empty($array_data['nsyst']))
			return false;

		$id = addslashes($array_data['nsyst']);

		// Определяем тип вставка или обновление
		$flg_insert = false;
		if($id == -1)
			$flg_insert = true;
		else
			$flg_insert = false;
		
		// Вставка шифра пользователя
		$array_json = $this->insert_sh_polz_to_array($array_data['JSON']);

		$sql = Functions::generate_sql_query($flg_insert, $array_json, $id, $this->table);

		if(($data = DB::query($sql, DB::INSERT_OR_UPDATE)) === false)
			return false;

		if($flg_insert)
			$id = $data;
		
		if($this->trigger_operation != 0)
			$this->call_trigger($array_data['JSON']);
		
		return $id;
	}

	/*
		Функция вызова хранимой процедуры для выполнения внутренней логики с объектом.
		$array_data - 
		$id_object - ID объекта для передачи в хранимую процедуру. Если будет передан параметр -1, то поиск будет осуществляться в переданном ассоциативном массиве.
	*/
	private function call_trigger($array_data, $id_object = -1) {
		$id = "";	// ID объекта для передачи в хранимую процедуру

		if($id_object == -1) {
			if(!$array_data_decode = json_decode($array_data))
				return false;

			foreach($array_data_decode as $field => $array_value) {
				$array_value_decode = (array)$array_value;
	
				if($field == 'id_car' || $field == 'id_driver')
					$id = $array_value_decode['value'];
			}
		} else {
			$id = $id_object;
		}
		
		$sql = "CALL move_to_archive(" . $id . ", " . $this->trigger_operation . ")";
		if(($data = DB::query($sql, DB::OTHER)) === false)
			return false;
		return true;
	}

	// Функция добавления SH_POLZ шифра пользователя в запрос. Для того чтобы фиксировать историю изменения реквизитов
	private function insert_sh_polz_to_array($array_data) {
		$json_array = json_decode($array_data);	// Декодированный массив формата JSON с массивом параметров для вставки
		$temp_array = array();
		
		// Формируем новый массив из формата stdClass  формат [поле] => { массив параметров }
		foreach($json_array as $field => $array_field_item)
			$temp_array[$field] = $array_field_item;
		
		// Получаем шифр пользователя из сессионных переменных
		//$id_user = User::get_user_id();
		$id_user = 100;
		
		// Вставляем во временный массив шифр пользователя
		$temp_array['sh_polz'] = array("value" => $id_user, "type" => "number");
		
		// Формируем и возвращаем результирующий массив для сохранения
		return json_encode($temp_array);
	}

	// id_object - ID объекта относительно которого производится удаление. Данный параметр является не обязательным и используется исключительно для служебных нужд
	//public function remove($id, $id_object = -1) {
	public function remove($post) {
		if(!is_array($post))
			return false;

		if(empty($post['nsyst']))
			return false;

		$id = $post['nsyst'];
		$id_object = empty($post['object']) ? '' : $post['object'];

		if(mb_strlen($this->table) == 0)
			return false;
		
		$sql = "DELETE FROM " . $this->table . " WHERE id=" . $id;
		if(($data = DB::query($sql, DB::OTHER)) === false)
			return false;
		
		if($this->trigger_operation != 0)
			$this->call_trigger($post, $id_object);
		
		// Удаляем директорию с файлами
		/*if($this->remove_directory != 0)
			if(!$this->remove_directory($id_object, $id))
				return false;*/
		
		return true;
	}

	// Функция загрузки файла на сервер
	public function save_file($array_data_file, $id_object, $id_main_object = -1) {
		if(empty($array_data_file))
			return false;
	
		$class_name = get_class($this);

		$fileUploadClass = new FileUpload();
		if(($uploaddir = $fileUploadClass->generate_uploaddir($class_name, $id_main_object, $id_object)) === false)
			return false;

		$file_name = $msg_error = '';
		foreach($array_data_file as $file) {
			
			// Проверка расширения файла, можно ли загрузить файл с таким расширением
			if(!Functions::check_extension_files(basename($file['name']), $this->array_file_extension))
				continue;

			if(!$fileUploadClass->downdloadFileToServer($file, $uploaddir, $file_name, 1, $msg_error))
				return false;

			if(!$fileUploadClass->save_path_to_file_database($file_name, $class_name, $id_object)) {
				$msg_error = 'Ошибка при сохранении информации в базу данных!';
				return false;
			}
		}
	
		return true;
	}

	// Функция удаления файла с сервера
	public function remove_file($post) {
		if(empty($post['nsyst']))
			return false;

		$id_object = $post['nsyst'];

		$class_name = get_class($this);
		$fileUploadClass = new FileUpload();
		return $fileUploadClass->remove_file($class_name, $id_object);
	}

	/*







	*

	/*
		Функция поиска для каждого класса своя, должна определяться в своем классе
		Иначе если использовать функцию-родителя, то будет возвраен пустой массив
	*/
	public function search($post, $flg_excel = -1) {
		return [];
	}

	// Получение всех полей таблицы по ее ID (без раскрытия справочников)
	public function get($get) {
		if(empty($get['id']))
			return false;

		$id = $get['id'];

		$sql = $this->sql_get_record;
		if(($sql = preg_replace('/\{table\}/i', $this->table, $sql)) === NULL)
			return false;
		
		if(($sql = preg_replace('/\{id\}/i', $id, $sql)) === NULL)
			return false;

		if(($data = DB::query($sql)) === false)
			return false;
		return $data;
	}

	public function get_list($id = '') {
		if(mb_strlen($this->sql_get_list) == 0)
			return false;
		
		$sql = $this->sql_get_list;
		if(($sql = preg_replace('/\{table\}/i', $this->table, $sql)) === NULL)
			return false;

		if(($sql = preg_replace('/\{id\}/i', $id, $sql)) === NULL)
			return false;

		if(($data = DB::query($sql)) === false)
			return false;

		return $data;
	}




}