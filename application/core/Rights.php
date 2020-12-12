<?php

namespace IcKomiApp\core;

use IcKomiApp\core\User;
use IcKomiApp\core\Session;
use IcKomiApp\core\Functions;
use IcKomiApp\lib\Database\DB;

class Rights {

	private static $rules_page_for_role = [
		'0' => ['', 'login'],
		'1' => ['', 'logout', 'car', 'car_search', 'driver', 'driver_search', 'repair', 'repair_search', 'dtp', 'dtp_search', 'adm', 'adm_search', 'car_document', 'edit', 'notice_events', 'cranvu', 'vu', 'car_document', 'car_for_driver', 'accessories', 'wheel', 'speedometer', 'osago', 'pts', 'certificate_registration', 'drivers_dopog', 'cars_dopog', 'technical_inspection', 'tractor_vu', 'calibration', 'drivers_card', 'car_tachograph', 'download_file', 'car_glonass'],
		'2' => ['', 'logout', 'car', 'car_search', 'driver', 'driver_search', 'repair', 'repair_search', 'dtp', 'dtp_search', 'adm', 'adm_search', 'car_document', 'edit', 'notice_events', 'cranvu', 'vu', 'car_document', 'car_for_driver', 'accessories', 'wheel', 'speedometer', 'osago', 'pts', 'certificate_registration', 'drivers_dopog', 'cars_dopog', 'technical_inspection', 'tractor_vu', 'calibration', 'drivers_card', 'car_tachograph', 'download_file', 'car_glonass'],
		'9' => ['', 'logout', 'car', 'car_search', 'driver', 'driver_search', 'repair', 'repair_search', 'dtp', 'dtp_search', 'adm', 'adm_search', 'car_document', 'edit', 'edit_directory', 'admin_panel', 'notice_events', 'cranvu', 'vu', 'car_document', 'car_for_driver', 'accessories', 'wheel', 'speedometer', 'osago', 'pts', 'certificate_registration', 'drivers_dopog', 'cars_dopog', 'technical_inspection', 'tractor_vu', 'calibration', 'drivers_card', 'car_tachograph', 'download_file', 'car_glonass']
	];

	// Функция проверки страницы, для отрисовки пользователем
	public static function check_access_html_page($page) {
		if(!is_string($page))
			return false;

		$role = User::get('role');

		$page = trim($page, '/');

		$array_rules = [];
		if(array_key_exists($role, self::$rules_page_for_role))
			$array_rules = self::$rules_page_for_role[$role];
		else
			$array_rules = self::$rules_page_for_role[0];

		if(!in_array($page, $array_rules))
			return false;
		return true;
	}

	/*
		Функция проверки прав доступа к странице.
		Данная функция работает в 2-х режимах. Сначала осуществляется запрос в базу данных в таблицу прав доступа.
		Если при возникновении ошибки при выполнении запроса в базу данных (например если таблица не существует) будет вызвана функция
		для проверки прав доступа с использованием служебного статического массива (массив указан в данном классе)
		$page - страница к которой необходимо проверить доступ
	*/
	public static function check_access_page($page) {
		if(!is_string($page))
			return false;

		$role = User::get('role');

		// Flag ACL (access list user)
		$acl = false;
		$page = trim($page, '/');

		$acl = self::check_access_with_array($page, $role);
		return $acl;
	}

	/*
		Функция проверки доступа к веб-странице с помощью служебного массива
		$page - страница к которой необходимо проверить доступ
		$role - роль пользователя
	*/
	private static function check_access_with_array($page, $role) {
		if(!is_string($page))
			return false;

		$array_rules = [];
		if(array_key_exists($role, self::$rules_page_for_role))
			$array_rules = self::$rules_page_for_role[$role];
		else
			$array_rules = self::$rules_page_for_role[0];

		if(!in_array($page, $array_rules))
			return false;

		return true;
	}

	/*
		Функция проверки доступа к веб-странице с помощью базы данных
		$page - страница к которой необходимо проверить доступ
		$role - роль пользователя
		$data - массив с результатами выборки из таблицы прав доступа
	*/
	private static function check_access_with_database($page, $role, $data) {
		if(!is_string($page))
			return false;

		if(empty($data) || (count($data) == 0))
			return false;

		if(!in_array('acl', $data[0]))
			return false;

		if($data[0]['acl'] != 1)
			return false;

		return true;
	}

}