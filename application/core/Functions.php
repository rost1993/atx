<?php

namespace IcKomiApp\core;

/*
	Служебный класс, содержащий различные вспомогательные функции
*/
class Functions {

	/*
		Функция вывода ошибок. Производит форматированный вывод ошибок с вывовдом типа данных
		После вывода объекта функция завершает работу скрипта. И производит выход из приложения
		$object - объект, который необходимо вывести на экран
	*/
	public static function debug($object) {
		echo '<pre>';
		var_dump($object);
		echo '</pre>';
		exit;
	}

	// Функция конвертирования даты из формата MySQL в нормальный человеческий вид
	public static function convertToDate($date) {
		if(mb_strlen(trim($date)) == 0)
			return "";
		
		$newDate = mb_substr($date, 8, 2);
		$newDate .= "." . mb_substr($date, 5, 2);
		$newDate .= "." . mb_substr($date, 0, 4);
		return $newDate;
	}
	
	// Функция конвертирования даты из формата MySQL в нормальный человеческий вид
	public static function convertToMySQLDateFormat($date) {
		if(mb_strlen(trim($date)) == 0)
			return "";
		
		$newDate = mb_substr($date, 6, 4);
		$newDate .= "-" . mb_substr($date, 3, 2);
		$newDate .= "-" . mb_substr($date, 0, 2);
		return $newDate;
	}

	// Функция отрисовки значка файла.
	// flg_remove - флаг-показатель надо ли отрисовывать возможность удаления файла
	// Функция возвращает HTML код для отрисовки иконки файла
	// action_item - номер действия для кнопок
	public static function rendering_icon_file($path_to_file, $file_extension, $id_file = 0, $action_item = 0, $flg_remove = false, $class_name = 'undefined') {
		if((mb_strlen(trim($path_to_file)) == 0) || ($path_to_file === NULL))
			return "";
		
		$icon_file_extension = 'fa fa-file-pdf-o';
		$type_file = 'pdf';
		$flg_generate_html_document = true; // Флаг-показатель что необходимо генерировать модуль для открытия картинки или документа
		switch(mb_strtolower(trim($file_extension))) {
			case 'pdf':
				$icon_file_extension = 'fa fa-file-pdf-o';
				$type_file = 'pdf';
				break;
			case 'xlsx':
			case 'xls':
				$icon_file_extension = 'fa fa-file-excel-o';
				$type_file = 'excel';
				break;
			case 'jpeg':
			case 'jpg':
			case 'png':
			case 'tif':
			case 'gif':
				$icon_file_extension = '';
				$type_file = 'image';
				$flg_generate_html_document = false;
				break;
			default:
				$icon_file_extension = 'fa fa-file';
				$type_file = 'pdf';
				break;
		}
		
		// Если передан параметр, то отрисовываем возможность удаления файла
		$btn_remove = "";
		
		/*Session::start();
		$role = Session::get('role');
		Session::commit();*/

		$role = 9;
	
		if($flg_remove && ($role > 1))
			$btn_remove = "<button class='btn my_close_button dropdown-toggle' id='btnDropdownDeleteFile' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>&times</button>"
						. "<div class='dropdown-menu' aria-labelledby='btnDropdownDeleteFile'>"
						. "<button type='button' class='dropdown-item' id='btnDeleteFile' data-save='" . $id_file . "' data-item='" . $action_item . "' data-class='" . $class_name . "'><span class='fa fa-check text-success'>&nbsp</span>Подтверждаю удаление</button></div>";
		
		$link_file = "http://" . $_SERVER['HTTP_HOST'] . "/" . $path_to_file;
		if($flg_generate_html_document) {
			$html_file = "<span class='badge badge-pill badge-secondary image-preview-block' style='font-size: 15px;'>"
				. "<span class='" . $icon_file_extension . " file-badge' id='openModalFile' data-href='" . $link_file . "' data-file-format='" . $type_file . "'></span>" . $btn_remove . "</span>";
		} else {
			if(!file_exists($path_to_file))
				return '';

			$base64_str = base64_encode(file_get_contents($path_to_file));
			$mime_type = mime_content_type($path_to_file);
			$html_file = "<span class='badge badge-pill badge-secondary image-preview-block' style='font-size: 15px;'>"
					// . "<img class='image-preview1 rounded' id='openModalFile' data-href='" . $link_file . "' src='" . $link_file . "' data-file-format='" . $type_file . "'>" . $btn_remove . "</span>";
					 . "<img class='image-preview1 rounded' id='openModalFile' data-href='" . $link_file . "' src='data:" . $mime_type . ";base64," .  $base64_str . "' data-file-format='" . $type_file . "'>" . $btn_remove . "</span>";
		}
		return $html_file;
	}

	// Функция формирования SQL-запроса для сохранения/обновления информации о водителе
	public static function generate_sql_query($flg_insert, $array_data, $table, $nsyst = 0) {
		$sqlQuery = "";
		
		if(!$array_data_decode = json_decode($array_data))
			return $sqlQuery;
		
		// В зависимости от добавления или корректировки выбираем то или иное действие
		if($flg_insert) {
			$sql_field = $sql_value = "";
			
			foreach($array_data_decode as $field => $array_value) {
				$array_value_decode = (array)$array_value;
				
				if(mb_strlen($sql_field) == 0)
					$sql_field = $field;
				else
					$sql_field .= "," . $field;
				
				if($array_value_decode['type'] == 'char') {
					if(mb_strlen($sql_value) == 0)
						$sql_value = "'" . rawurldecode($array_value_decode['value']) . "'";
					else
						$sql_value .= ",'" . rawurldecode($array_value_decode['value']) . "'";
				} else if($array_value_decode['type'] == 'date') {
					
					// Обработка даты особая, если дата пустая то вставляем пустое значение
					$temp_value = "";
					if(mb_strlen(trim(rawurldecode($array_value_decode['value']))) == 0)
						$temp_value = 'NULL';
					else
						$temp_value = "'" . self::convertToMySQLDateFormat(rawurldecode($array_value_decode['value'])) . "'";

					if(mb_strlen($sql_value) == 0)
						$sql_value .= $temp_value;
					else
						$sql_value .= "," . $temp_value;
				} else if ($array_value_decode['type'] == 'checkbox') {
					$temp_value = "";
					
					if(rawurldecode($array_value_decode['value']) == 'true')
						$temp_value .= '1';
					else
						$temp_value .= '0';
					
					if(mb_strlen($sql_value) == 0)
						$sql_value .= $temp_value;
					else
						$sql_value .= ',' . $temp_value;
				} else {
					// Обработка числа особая, если дата пустая то вставляем пустое значение
					$temp_value = "";
					if(mb_strlen(trim(rawurldecode($array_value_decode['value']))) == 0)
						$temp_value = 'NULL';
					else
						$temp_value = str_replace(',', '.', rawurldecode($array_value_decode['value']));
					
					if(mb_strlen($sql_value) == 0)
						$sql_value = $temp_value;
					else
						$sql_value .= "," . $temp_value;
				}
			}
			
			$sqlQuery = "INSERT INTO " . $table . " (" . $sql_field . ") VALUES (" . $sql_value . ")";
		} else {
			$sql = "";
			
			foreach($array_data_decode as $field => $array_value) {
				$array_value_decode = (array)$array_value;

				if($array_value_decode['type'] == 'char') {
					if(mb_strlen($sql) == 0)
						$sql .= $field . "='" . rawurldecode($array_value_decode['value']) . "'";
					else
						$sql .= "," . $field . "='" . rawurldecode($array_value_decode['value']) . "'";
				} else if($array_value_decode['type'] == 'date') {
					
					// Обработка даты особая, если дата пустая то вставляем пустое значение
					$temp_value = "";
					if(mb_strlen(trim(rawurldecode($array_value_decode['value']))) == 0)
						$temp_value = 'NULL';
					else
						$temp_value = "'" . self::convertToMySQLDateFormat(rawurldecode($array_value_decode['value'])) . "'";

					if(mb_strlen($sql) == 0)
						$sql .= $field . "=" . $temp_value;
					else
						$sql .= "," . $field . "=" . $temp_value;
				} else if ($array_value_decode['type'] == 'checkbox') {
					$temp_value = '';
					if(rawurldecode($array_value_decode['value']) == 'true')
						$temp_value = '=1';
					else
						$temp_value = '=0';
					
					if(mb_strlen($sql) == 0)
						$sql .= $field . $temp_value;
					else
						$sql .= ',' . $field . $temp_value;
				} else {
					// Обработка числа особая, если дата пустая то вставляем пустое значение
					$temp_value = "";
					if(mb_strlen(trim(rawurldecode($array_value_decode['value']))) == 0)
						$temp_value = 'NULL';
					else
						$temp_value = str_replace(',', '.', rawurldecode($array_value_decode['value']));
					
					if(mb_strlen($sql) == 0)
						$sql .= $field . "=" . $temp_value;
					else
						$sql .= "," . $field . "=" . $temp_value;
				}
			}
			$sqlQuery = "UPDATE " . $table . " SET " . $sql . " WHERE id=" . $nsyst;
		}

		return $sqlQuery;
	}

	/*
		Функция поиска заданного параметра в массиве в формате JSON
		$array_data - JSON массив, в котором необходимо искать параметр
		$param - название ключа в массиве, который будем искать
		Возвращаемое значение: null - в случае ошибки, значение - в случае успеха
	*/
	public static function get_id_from_json($array_data, $param) {
		if(!$array_data_decode = json_decode($array_data))
			return null;

		$value = '';

		foreach($array_data_decode as $field => $array_value) {
			$array_value_decode = (array)$array_value;
			if($field == $param)
				$value = $array_value_decode['value'];
		}

		return $value;
	}

	// Функция, которая проверяет входит ли расширение файла (которое вычисляется из названия файла) в массив допустимых расширений
	// Возвращает TRUE - если файл с таким расширением входит в список в массиве, FALSE - в любом другом случае
	public static function check_extension_files($file_name, $array_file_extension) {		
		// Вычисляем расширение файла по его названию
		$file_extension = '';
		$file_name_explode = explode('.', $file_name);
		if(count($file_name_explode) != 0)
			$file_extension = mb_strtolower($file_name_explode[count($file_name_explode) - 1]);
		else
			return false;

		//$array_file_extension_lower = array_map('mb_strtolower', $array);
		return in_array($file_extension, array_map('mb_strtolower', $array_file_extension));
	}

}
