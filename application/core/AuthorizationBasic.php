<?php

namespace IcKomiApp\core;

use IcKomiApp\lib\Database\DB;
use IcKomiApp\core\Session;

/*
	Copyright: Rostislav Gashin, 2020, Syktyvkar, Komi Republic, rostislav-gashin@yandex.ru
*/
abstract class AuthorizationBasic {

	const TABLE = 'S_USERS';
	const NAME_FIELD_LOGIN = 'LOGIN';
	const NAME_FIELD_PASSWORD = 'PASSWORD';

	// Название глобального массива для забора настроек
	const GLOBALS_ARRAY_SETTINGS = 'registration_settings';

	public $message_error = '';

	protected $table = '';
	protected $name_field_login = '';
	protected $name_field_password = '';

	/*
		Конструктор класса
		$settings - массив с настройками для регистрации, содержит [ключ] => [значение]
	*/
	public function __construct($settings = []) {
		if(!empty($settings['table']))
			$this->table = $settings['table'];
		else if(!empty($this->table))
			$this->table = $this->table;
		else if(!$GLOBALS[self::GLOBALS_ARRAY_SETTINGS]['table'])
			$this->table = $GLOBALS[self::GLOBALS_ARRAY_SETTINGS]['table'];
		else
			$this->table = self::TABLE;

		if(!empty($settings['name_field_login']))
			$this->name_field_login = $settings['name_field_login'];
		else if(!empty($this->name_field_login))
			$this->name_field_login = $this->name_field_login;
		else if(!$GLOBALS[self::GLOBALS_ARRAY_SETTINGS]['name_field_login'])
			$this->name_field_login = $GLOBALS[self::GLOBALS_ARRAY_SETTINGS]['name_field_login'];
		else
			$this->name_field_login = self::NAME_FIELD_LOGIN;

		if(!empty($settings['name_field_password']))
			$this->name_field_password = $settings['name_field_password'];
		else if(!empty($this->name_field_password))
			$this->name_field_password = $this->name_field_password;
		else if(!$GLOBALS[self::GLOBALS_ARRAY_SETTINGS]['name_field_password'])
			$this->name_field_password = $GLOBALS[self::GLOBALS_ARRAY_SETTINGS]['name_field_password'];
		else
			$this->name_field_password = self::NAME_FIELD_PASSWORD;

		$this->message_error = '';
	}

	public function login($post) {
		if(!is_array($post))
			return false;

		$login = (empty($post[$this->name_field_login])) ? '' : trim($post[$this->name_field_login]);
		$password = (empty($post[$this->name_field_password])) ? : $post[$this->name_field_password];

		$sql = "SELECT * FROM  " . $this->table . " WHERE login='" . $login . "'";

		if(($data = DB::query($sql)) === false) {
			$this->message_error = 'Ошибка выполнения запроса!';
			return false;
		}

		if(count($data) == 0) {
			$this->message_error = 'Пользователь не найден!';
			return false;
		}

		if(empty($data[0]['access']) || ($data[0]['access'] != 1)) {
			$this->message_error = 'Отказано в доступе!';
			return false;
		}

		if(!password_verify($password, $data[0]["passwd_hash"])) {
			$this->message_error = 'В доступе отказано!';
			return false;
		}

		$this->mark_session_data($data);
		return true;
	}

	protected function mark_session_data($data) {
		$session = new Session();
		$session->start();
		$session->commit();
	}

	public function logout() {
		$session = new Session();
		$session->start();
		$session->destroy();
	}
}