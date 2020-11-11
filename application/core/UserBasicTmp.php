<?php

namespace IcKomiApp\core;

use IcKomiApp\lib\Database\DB;
use IcKomiApp\core\Logic;

abstract class UserBasic {
	
	const MIN_LENGTH_PASSWORD = 6;
	const TABLE_USER = 'users';

	/*
		Массив со значениями таблицы с пользователями
		Индекс: название поля для функций в коде
		Значение: название поля в таблице
	*/
	protected static $user_array = [
							'login' => 
								['require' => true, 'type' => 'char', 'ru_name' => 'Логин'],
							'fam' =>
								['require' => true, 'type' => 'char', 'ru_name' => 'Фамилия'],
							'imj' =>
								['require' => true, 'type' => 'char', 'ru_name' => 'Имя'],
							'otch' =>
								['require' => true, 'type' => 'char', 'ru_name' => 'Отчество'],
							'kodrai' =>
								['require' => true, 'type' => 'char', 'ru_name' => 'Район'],
							'password1' =>
								['require' => true, 'type' => 'char', 'ru_name' => 'Пароль'],
							'password2' => 
								['require' => true, 'type' => 'char', 'ru_name' => 'Пароль'],
							'hash' =>
								['require' => false, 'type' => 'char', 'ru_name' => 'Хэш'],
							'passwd_hash' => 
								['require' => false, 'type' => 'char', 'ru_name' => 'Хэш пароля'],
							'ibd_arx' => 
								['require' => false, 'type' => 'number', 'ru_name' => 'Архив'],
							'acl' => 
								['require' => false, 'type' => 'number', 'ru_name' => 'Доступ'],
						];

	/*
		Функция проверки массива с пользовательскими настройками
		Функция проверяет переданный массив с эталонным массивом
		Возвращает: true - все необходимые элементы найдены, false - не все найдены
	*/
	private static function check_user_array($array_user_data, &$message_error) {
		foreach (self::$user_array as $key => $params) {

			if(!$params['require'])
				continue;

			if(array_key_exists($key, $array_user_data)) {
				if($params['type'] == 'char') {
					if(mb_strlen($array_user_data[$key]) == 0) {
						$message_error = 'Не заполнен обязательный реквизит: ' . self::$user_array[$key]['ru_name'];
						return false;
					}
				} else {
					if($array_user_data[$key] == 0) {
						$message_error = 'Не заполнен обязательный реквизит: ' . self::$user_array[$key]['ru_name'];
						return false;
					}
				}
			} else {
				$message_error = 'Не заполнен обязательный реквизит: ' . self::$user_array[$key]['ru_name'];
				return false;
			}
		}

		return true;
	}

	// Проверка пароля на стойкость
	private static function check_password($password, &$message_error) {
		if(!is_string($password)) {
			$message_error = 'Поле пароль является не строкой!';
			return false;
		}

		if(mb_strlen($password) < self::MIN_LENGTH_PASSWORD) {
			$message_error = 'Минимальная длина пароля: ' . self::MIN_LENGTH_PASSWORD;
			return false;
		}

		if(preg_match('/[^a-z0-9]+/i', $password) == 1) {
			$message_error = 'В поле пароль разрешается вводить только латинские буквы и цифры!';
			return false;
		}

		if(preg_match('/^[0-9]+$/i', $password) == 1) {
			$message_error = 'Пароль должен содержать цифры и буквы латинского алфавита!';
			return false;
		}

		if(preg_match('/^[a-z]+$/i', $password) == 1) {
			$message_error = 'Пароль должен содержать цифры и буквы латинского алфавита!';
			return false;
		}

		return true;
	}

	// Проверка логина
	private static function check_login($login, &$message_error) {
		if(!is_string($login)) {
			$message_error = 'Поле пароль является не строкой!';
			return false;
		}

		if(preg_match('/[^a-z0-9]+/i', $login) == 1) {
			$message_error = 'В поле логин разрешается вводить только латинские буквы и цифры!';
			return false;
		}

		if(preg_match('/^[0-9]+$/i', $login) == 1) {
			$message_error = 'Логин должен содержать хотя бы одну букву латинского алфавита!';
			return false;
		}

		return true;
	}

	private static function check_user($login, &$message_error) {
		$sql = "SELECT count(*) as count FROM " . self::TABLE_USER . " WHERE login ='" . addslashes($login) . "'";
		if(($data = DB::query($sql)) === false) {
			$message_error = 'Ошибка при выполнении запроса к базе данных!';
			return false;
		}

		if($data[0]['count'] == 1) {
			$message_error = 'Пользователь с таким логином уже существует!';
			return false;
		}

		return true;
	}

	public static function reg($post, &$message_error) {
		if(!(is_array($post)) || empty($post) || ($post === null))
			return false;

		if(!self::check_user_array($post, $message_error))
			return false;

		extract($post, EXTR_OVERWRITE);
		$login = trim($login);
		$password1 = trim($password1);
		$password2 = trim($password2);

		if(trim($password1) != trim($password2)) {
			$message_error = 'Не совпадают пароли!';
			return false;
		}

		if(!self::check_login($login, $message_error))
			return false;

		if(!self::check_password($password1, $message_error))
			return false;

		if(!self::check_user($login, $message_error))
			return false;

		$hash = $hashSHA = hash('sha256', $login . $fam . $imj . $otch . $password1);
		$hash_password = password_hash($password1, PASSWORD_BCRYPT);
		$role = 1;

		$sql = "INSERT INTO " . self::TABLE_USER . " (fam, imj, otch, login, passwd_hash, hash, kodrai, role) "
			 . " VALUES ('" . mb_strtoupper($fam) . "','" . mb_strtoupper($imj) . "','" . mb_strtoupper($otch) . "','" . $login . "','" . $hash_password . "','" . $hash . "'," . $kodrai . "," . $role . ")";

		if(!DB::query($sql, DB::INSERT_OR_UPDATE)) {
			$message_error = 'При создании пользователя произошла ошибка!';
			return false;
		}

		return true;
	}


}