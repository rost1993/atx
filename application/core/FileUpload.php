<?php

namespace IcKomiApp\core;

use IcKomiApp\lib\Database\DB;
use IcKomiApp\core\User;
use IcKomiApp\core\Logic;
use IcKomiApp\core\Functions;

class FileUpload {
	const SUPER_MAX_FILE_SIZE = 41943040; 	// Максимально допустимый размер файла для загрузки на сервер 40 MB
	const MAX_SIZE_IMAGE = 300;				// Максимально допустимый файл картинки, 400 kB
	const QUALITY_IMAGE = 60;				// Допустимое качество изображения
	#public $MAX_FILE_SIZE = 15728640; 		// Максимально допустимый размер файла для загрузки на сервер 15 MB (данный параметр может установить пользователь)
	public $MAX_FILE_SIZE = 41943040; 		// Максимально допустимый размер файла для загрузки на сервер 40 MB (данный параметр может установить пользователь)
	public $LAST_COPY_FILE = '';
	
	const CLASS_CAR_DOCUMENT = 'CarDocument';
	const CLASS_TECHNICAL_INSPECTION_DOCUMENT = 'TechnicalInspection';
	const CLASS_OSAGO_DOCUMENT = 'Osago';
	const CLASS_PTS_DOCUMENT = 'Pts';
	const CLASS_VU_DOCUMENT = 'VU';
	const CLASS_VU_DOCUMENT_TRACTOR = 'VU_tractor';
	const CLASS_VU_DOCUMENT_BOAT = 'VU_boat';
	const CLASS_CERTIFICATE_REGISTRATION_DOCUMENT = 'CertificateRegistration';
	const CLASS_CAR_FOR_DRIVER_DOCUMENT = 'CarForDriver';
	const CLASS_EXAM_DOCUMENT = 'Exam';
	const CLASS_REPAIR_DOCUMENT = 'Repair';
	const CLASS_DTP = 'Dtp';
	const CLASS_CARS = 'Cars';
	const CLASS_SPEC_SIGNALS = 'PermissionSpecSignals';
	const CLASS_ADM_OFFENSE = 'AdmOffense';
	const CLASS_CARS_DOPOG = 'CarsDopog';
	
	// Функция очистки содержимого директорий от файлов
	private function clearPath($path) {
		if(is_dir($path)) {
			foreach(glob($path . '*') as $file)
				unlink($file);
		}
	}
	
	// Функция создания директории куда необходимо сохранить файл
	private function createDirectory($path) {
		$array_path = explode('/', $path);
		$temp_path = '';
		
		for($i = 0; $i < count($array_path); $i++) {
			$temp_path .= $array_path[$i] . '/';
			
			if(($array_path[$i] == '..') || ($array_path[$i] == '.'))	
				continue;
			
			if(!is_dir($temp_path)) {
				if(!mkdir($temp_path, 0777))
					return false;
			}
		}
		return true;
	}
	
	// Функция, которая проверяет существует ли указанный файл по пути
	// Если файл существует, то функция перебирает индексы и проверяет существует ли новый файл
	// Функция всегда возвращает название файла
	private function checkForFile($uploaddir, $file) {
		
		$arr_file = explode('.', $file);
		$extensions =  '.' . $arr_file[count($arr_file) - 1];
		$temp_name = '';
		
		for($i = 0; $i < (count($arr_file) - 1); $i++)
			$temp_name .= $arr_file[$i];
		
		$new_file_name = $uploaddir . mb_strtolower($file);
		
		for($i = 0; $i < 100000; $i++) {
			if(file_exists($new_file_name)) {
				$new_file_name = $uploaddir . $temp_name . '_' . $i . $extensions;
			} else {
				return $new_file_name;
			}
		}
		
		return ($temp_name . '_' . rand(100001, 1000000) . $extensions);
	}
	
	// Функция получения папки куда необходимо сохранить файл
	public function generate_uploaddir($class_name, $id_main_object, $id_object) {
		if((mb_strlen($class_name) == 0) || (!ServiceFunction::check_number($id_object)))
			return false;

		$path_to_file = '/uploads-files/';

		switch($class_name) {
			case self::CLASS_CAR_DOCUMENT:
				$path_to_file .= '/car_documents/' . $id_object . '/';
				break;
			
			case self::CLASS_TECHNICAL_INSPECTION_DOCUMENT:
				$path_to_file .= 'cars/' . $id_main_object . '/technical_inspection/' . $id_object . '/';
				break;
			
			case self::CLASS_OSAGO_DOCUMENT:
				$path_to_file .= 'cars/' . $id_main_object . '/osago/' . $id_object . '/';
				break;
			
			case self::CLASS_PTS_DOCUMENT:
				$path_to_file .= 'cars/' . $id_main_object . '/pts/' . $id_object . '/';
				break;
			
			case self::CLASS_CERTIFICATE_REGISTRATION_DOCUMENT:
				$path_to_file .= 'cars/' . $id_main_object . '/certificate_registration/' . $id_object . '/';
				break;
			
			case self::CLASS_VU_DOCUMENT:
				$path_to_file .= 'drivers/' . $id_main_object . '/vu/' . $id_object . '/';
				break;
			
			case self::CLASS_VU_DOCUMENT_TRACTOR:
				$path_to_file .= 'drivers/' . $id_main_object . '/vu_tractor/' . $id_object . '/';
				break;
			
			case self::CLASS_VU_DOCUMENT_BOAT:
				$path_to_file .= 'drivers/' . $id_main_object . '/vu_boat/' . $id_object . '/';
				break;
			
			case self::CLASS_CAR_FOR_DRIVER_DOCUMENT:
				$path_to_file .= 'car_for_driver/' . $id_object . '/';
				break;
			
			case self::CLASS_EXAM_DOCUMENT:
				$path_to_file .= 'exams/' . $id_object . '/';
				break;
			
			case self::CLASS_REPAIR_DOCUMENT:
				$path_to_file .= 'repair/' . $id_object . '/';
				break;
			
			case self::CLASS_DTP:
				$path_to_file .= 'dtp/' . $id_object . '/';
				break;
			
			case self::CLASS_CARS:
				$path_to_file .= 'cars/' . $id_object . '/';
				break;
			
			case self::CLASS_SPEC_SIGNALS:
				$path_to_file .= 'drivers/' . $id_main_object . '/permission_spec_signals/' . $id_object . '/';
				break;
			
			case self::CLASS_ADM_OFFENSE:
				$path_to_file .= 'adm_offense/' . $id_object . '/';
				break;

			case self::CLASS_CARS_DOPOG:
				$path_to_file .= 'cars/' . $id_main_object . '/pts/' . $id_object . '/';
				break;
			
			default:
				$path_to_file = false;
				break;
		}
		
		return $path_to_file;
	}
	
	/*
		Основная функция для загрузки файлов на сервер
		file_array - массив с файлом
		uploaddir - путь к директории куда необходимо сохранить файл
		file_name - полное абсолютное название файла
		flg_type_return - код возврата. Если код установлен в 1 - то возвращается относительный путь к файлу, если 2 - то возвращается ссылка на файл
		msg_error - ошибка при сохранении
		В случае ошибки функция вернет 0, в случае успешного выполнения вернет полное название файла
	*/
	public function downdloadFileToServer($file_array, $uploaddir, &$file_name, $flg_type_return, &$msg_error) {
		
		setlocale(LC_CTYPE, 'ru_RU.UTF8');
		
		// Проверяем загружен ли файл на сервер
		if($file_array['error'] != 0) {
			$msg_error = $file_array['error'];
			return false;
		}
		
		// Проверяем максимально допустимый размер файла
		if($this->MAX_FILE_SIZE > self::SUPER_MAX_FILE_SIZE)
			$this->MAX_FILE_SIZE = self::SUPER_MAX_FILE_SIZE;
		
		if($file_array['size'] > $this->MAX_FILE_SIZE) {
			$msg_error = 'Превышен максимальный размер файла!';
			return false;
		}
		
		// Проверяем что путь к файлу обязатлно оканчивался слэшэм
		$temp_uploaddir = $uploaddir;
		if($temp_uploaddir[mb_strlen($temp_uploaddir - 1)] != '/')
			$temp_uploaddir .= '/';
		
		if(!$this->createDirectory($temp_uploaddir)) {
			$msg_error = 'Ошибка при создании директории!';
			return false;
		}
		
		// Генерируем имя файла
		$newFileName = str_replace(' ', '_', basename($file_array['name']));
		$newFileName = $this->checkForFile($temp_uploaddir, $newFileName);

		
		/*if(!move_uploaded_file($file_array['tmp_name'], $newFileName)) {
			$msg_error = 'Ошибка при копировании временного файла!';
			return false;
		}*/

		// Копируем файл на сервер. Используем вместо перемещения копирование, так как иногда нам необходимо размножить файлы.
		// Гарантируется удаление файла из временного хранилища, так как в спецификации написано что удаление произойдет после завершения работы скрипта
		/*if(!copy($file_array['tmp_name'], $newFileName)) {
			$msg_error = 'Ошибка при копировании временного файла!';
			return false;
		}*/
		
		if(!$this->copy_temp_file_to_server($file_array['tmp_name'], $newFileName)) {
			$msg_error = 'Ошибка при копировании временного файла!';
			return false;
		}
		
		// Возвращаем либо относительный путь, либо ссылку на загруженный файл
		switch($flg_type_return) {
			case 1:
				$file_name = str_replace('../', '', $newFileName);
				break;
				
			case 2:
				$file_name = $_SERVER['DOCUMENT_ROOT'] . str_replace('../', '', $newFileName);
				break;
				
			case 3:
				$file_name = 'http://' . $_SERVER['HTTP_HOST'] . '/' . str_replace('../', '', $newFileName);
				break;
			
			default:
				$file_name = $newFileName;
				break;
		}
		
		return true;
	}
	
	// Функция получения таблицы в базе данных для сохранения информации
	private function get_table_for_object($class_name) {
		$table = '';
		switch($class_name) {
			case self::CLASS_CAR_DOCUMENT:
				$table = 'car_documents';
				break;
			
			case self::CLASS_TECHNICAL_INSPECTION_DOCUMENT:
				$table = 'technical_inspection';
				break;
			
			case self::CLASS_OSAGO_DOCUMENT:
				$table = 'osago';
				break;
			
			case self::CLASS_PTS_DOCUMENT:
				$table = 'pts';
				break;
			
			case self::CLASS_CERTIFICATE_REGISTRATION_DOCUMENT:
				$table = 'certificate_registration';
				break;
			
			case self::CLASS_VU_DOCUMENT:
				$table = 'drivers_document';
				break;
			
			case self::CLASS_VU_DOCUMENT_TRACTOR:
				$table = 'drivers_document_tractor';
				break;
			
			case self::CLASS_VU_DOCUMENT_BOAT:
				$table = 'drivers_document_boat';
				break;
			
			case self::CLASS_SPEC_SIGNALS:
				$table = 'drivers_permission_spec_signals';
				break;
			
			case self::CLASS_CAR_FOR_DRIVER_DOCUMENT:
				$table = 'car_for_driver';
				break;
			
			case self::CLASS_EXAM_DOCUMENT:
			case self::CLASS_REPAIR_DOCUMENT:
			case self::CLASS_DTP:
			case self::CLASS_CARS:
			case self::CLASS_ADM_OFFENSE:
				$table = 'files';
				break;

			case self::CLASS_CARS_DOPOG:
				$table = 'cars_dopog';
				break;

			default:
				$table = false;
				break;
		}
		return $table;
	}
	
	// Функция возвращает номер категории файла для сохранения в таблицу с общим списком файлов
	private function get_number_cathegory_files($class_name) {
		$cathegory = 0;
		switch($class_name) {
			case self::CLASS_EXAM_DOCUMENT:
				$cathegory = 10;
				break;
			case self::CLASS_REPAIR_DOCUMENT:
				$cathegory = 11;
				break;
			case self::CLASS_DTP:
				$cathegory = 12;
				break;
			case self::CLASS_CARS:
				$cathegory = 13;
				break;
			case self::CLASS_ADM_OFFENSE:
				$cathegory = 15;
				break;
		}
		return $cathegory;
	}
	
	public function save_path_to_file_database($file_name, $class_name, $id_object) {
		if(!ServiceFunction::check_number($id_object))
			return false;
		
		if(mb_strlen($file_name) == 0)
			return false;
		
		$file_explode = explode('.', $file_name);
		$file_extension = $file_explode[count($file_explode) - 1];

		if(($table = $this->get_table_for_object($class_name)) === false)
			return false;
		
		$sqlQuery = "UPDATE " . $table . " SET path_to_file='" . $file_name . "', file_extension='" . $file_extension . "' WHERE id=" . $id_object;

		if($table == 'files') {
			$sh_polz = User::get_user_id();
			$cathegory = $this->get_number_cathegory_files($class_name);
			$sqlQuery = "INSERT INTO " . $table . " (id_object, category_file, path_to_file, file_extension, sh_polz) VALUES (" . $id_object . "," . $cathegory . ",'" . $file_name . "', '" . $file_extension . "'," . $sh_polz . ")";
		}

		$mysql = new mysqlRun();
		if(!$mysql->mysqlQuery($sqlQuery, mysqlRun::MYSQL_INSERT_OR_UPDATE))
			return false;
		return true;
	}
	
	
	/*
		Функция удаления файла с сервера.
		$path_to_file - путь к файлу.
		
		В случае успешного удаления функция возвращает TRUE, в случае возникновения ошибки возвращается FALSE.
	*/
	public function removeFileToServer($path_to_file) {
		
		if(!file_exists($path_to_file))
			return false;
		
		if(unlink($path_to_file))
			return true;
		else
			return false;
	}
	
	/*
		Функция удаления директории с сервера.
		$path_to_file - путь к файлу.
		
		В случае успешного удаления функция возвращает TRUE, в случае возникновения ошибки возвращается FALSE.
	*/
	public function removeDirectoryToServer($path_to_directory) {
		
		if(!file_exists($path_to_directory))
			return false;
		
		$this->clearPath($path_to_directory);
		return rmdir($path_to_directory);
	}
	
	public function remove_file($class_name, $id_object) {
		if(!ServiceFunction::check_number($id_object))
			return false;
		
		$table = $this->get_table_for_object($class_name);
		$sqlQuery = "SELECT path_to_file, file_extension FROM " . $table . " WHERE id=" . $id_object;

		$mysql = new mysqlRun();
		if(!$mysql->mysqlQuery($sqlQuery, mysqlRun::MYSQL_SELECT))
			return false;

		if(count($mysql->resultQuery) == 0)
			return true;
		
		if($this->removeFileToServer('../../' . $mysql->resultQuery[0]['path_to_file']) === false)
			return false;

		if($class_name == self::CLASS_EXAM_DOCUMENT || $class_name == self::CLASS_REPAIR_DOCUMENT || $class_name == self::CLASS_DTP || $class_name == self::CLASS_CARS || $class_name == self::CLASS_ADM_OFFENSE) {
			$sqlQuery = "DELETE FROM " . $table . " WHERE id=" . $id_object;
			if(!$mysql->mysqlQuery($sqlQuery, mysqlRun::MYSQL_OTHER))
				return false;
		} else {
			$sqlQuery = "UPDATE " . $table . " SET path_to_file='', file_extension='' WHERE id=" . $id_object;
			if(!$mysql->mysqlQuery($sqlQuery, mysqlRun::MYSQL_INSERT_OR_UPDATE))
				return false;
		}

		return true;
	}
	
	
	private function copy_temp_file_to_server($temp_file, $dest_file) {
		$file_explode = explode('.', $dest_file);
		$file_extension = mb_strtolower($file_explode[count($file_explode) - 1]);
		switch($file_extension) {
			case 'jpg':
			case 'jpeg':
			case 'png':
			case 'gif':
			case 'tiff':
				$result = $this->compress_image($temp_file, $dest_file);
				break;

			case 'pdf':
			case 'xls':
			case 'xlsx':
				$result = copy($temp_file, $dest_file);
				break;

			default:
				$result = false;
				break;
		}
		
		return $result;
	}
	
	// Функция сжатия изображения. Производит процедуру сжатия временного изображения и сохраняет в нужное место на диске
	private function compress_image($source_file, $dest_file) {
		if(!file_exists($source_file))
			return false;

		// Получаем размер файла, который необходимо сжать
		$file_size = round(filesize($source_file) / 1024);
	
		// Если размер уже достаточно маленький, то пробуем уменьшить качество изображения
		if(($file_size > 150) && ($file_size < 200)) {
			$this->image_reduction_quality($source_file, $dest_file);
		} else {
			// Получаем информацию о копируемом файле.
			// Производим анализ ширины изображения. В зависимости от этого будем производить уменьшение размера изображения, сохраняя пропорции
			$info = getimagesize($source_file);
			
						
			// Определяем максимальный размер по значению длины или ширины
			$size_width = $info[0];
			$size_height = $info[1];
			if($size_width > $size_height)
				$max_size = $size_width;
			else
				$max_size = $size_height;
				
			if($max_size < 1200)
				$this->image_reduction_size($source_file, $dest_file, 1.3);
			else if(($max_size >= 1200) && ($max_size <= 1900))
				$this->image_reduction_size($source_file, $dest_file, 2);
			else if(($max_size > 1900) && ($max_size <= 2500))
				$this->image_reduction_size($source_file, $dest_file, 3);
			else if(($max_size > 2500) && ($max_size <= 3000))
				$this->image_reduction_size($source_file, $dest_file, 3);
			else if(($max_size > 3000) && ($max_size <= 3500))
				$this->image_reduction_size($source_file, $dest_file, 4);
			else if(($max_size > 3500) && ($max_size <= 4000))
				$this->image_reduction_size($source_file, $dest_file, 5);
			else
				$this->image_reduction_size($source_file, $dest_file, 6);
		}
		
		// Дополнительно проверяем размер полученного изображения. Если  все же еще большой, пробуем снова уменьшить качество
		$file_size = round(filesize($dest_file) / 1024);
		if($file_size > 130)
			$this->image_reduction_quality($dest_file, $dest_file, 70);
		
		return true;
	}
	
	// Функция уменьшения качества изображения. Уменьшает качество изображения заданного уровня. ПО умолчанию используется 60%
	private function image_reduction_quality($source_file, $dest_file, $quality = self::QUALITY_IMAGE) {
		$info = getimagesize($source_file);
		if($info['mime'] == 'image/jpeg') {
			$image = imagecreatefromjpeg($source_file);
			imagejpeg($image, $dest_file, $quality);
		} else if($info['mime'] == 'image/gif') {
			$image = imagecreatefromgif($source_file);
			imagegif($image, $dest_file, $quality);
		} else if($info['mime'] == 'image/png') {
			$image = imagecreatefrompng($source_file);
			imagepng($image, $dest_file, $quality);
		}
		
		imagedestroy($image);
	}
	
	// Функция уменьшения размера изображений. Уменьшает изображение на определенный коэффициент при этом сохраняет пропорции изображения
	private function image_reduction_size($source_file, $dest_file, $coefficient) {
		$info = getimagesize($source_file);
		if($info['mime'] == 'image/jpeg') {
			$image_src = imagecreatefromjpeg($source_file);
			$image_dst = imagecreatetruecolor(round($info[0] / $coefficient), round($info[1] / $coefficient));
			imagecopyresampled($image_dst, $image_src, 0, 0, 0, 0, round($info[0] / $coefficient), round($info[1] / $coefficient), $info[0], $info[1]);
			imagejpeg($image_dst, $dest_file);
		} else if($info['mime'] == 'image/gif') {
			$image_src = imagecreatefromgif($source_file);
			$image_dst = imagecreatetruecolor(round($info[0] / $coefficient), round($info[1] / $coefficient));
			imagecopyresampled($image_dst, $image_src, 0, 0, 0, 0, round($info[0] / $coefficient), round($info[1] / $coefficient), $info[0], $info[1]);
			imagegif($image_dst, $dest_file);
		} else if($info['mime'] == 'image/png') {
			$image_src = imagecreatefrompng($source_file);
			$image_dst = imagecreatetruecolor(round($info[0] / $coefficient), round($info[1] / $coefficient));
			imagecopyresampled($image_dst, $image_src, 0, 0, 0, 0, round($info[0] / $coefficient), round($info[1] / $coefficient), $info[0], $info[1]);
			imagepng($image_dst, $dest_file);
			
		}
		
		imagedestroy($image_src);
		imagedestroy($image_dst);
	}
	
}