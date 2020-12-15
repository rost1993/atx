<?php

namespace IcKomiApp\core;

use IcKomiApp\core\Logic;
use IcKomiApp\core\Session;
use IcKomiApp\core\Functions;
use IcKomiApp\lib\Database\DB;
use IcKomiApp\core\Registration;

class User extends UserBasic {

	protected static $table = "users";

	protected static $default_password = 'atx';

	// Функция загрузки содержимого для панели администратора
	// RETURN: -1 - error, -2 - access denied, 1 - succesfully
	public static function get_list_users() {
		$role = self::get('role');
		
		if($role < 8)
			return false;

		$sql = "SELECT users.id, users.fam, users.otch, users.imj, users.login, users.slugba, users.hash, users.role, users.access, users.block FROM users
					WHERE role<>9 
					ORDER BY id ASC";
		if($role == 9) {
			$sql = "SELECT users.id, users.fam, users.otch, users.imj, users.login, users.slugba, users.hash, users.role, users.access, users.block FROM users
					ORDER BY id ASC";
		}

		if(($data = DB::query($sql)) === false)
			return false;
		return $data;
	}

	/*
		Функция предоставления доступа пользователю
		$post - массив с данными
		Return: TRUE - успех, FALSE - неудача
	*/
	public static function access_user($post) {
		if(!is_array($post) || empty($post))
			return false;

		$post_check = array_change_key_case($post);
		if(empty($post_check['id']) || empty($post_check['hash']))
			return false;

		$sql = "UPDATE " . self::$table . " SET access = MOD(access+1, 2) WHERE id=" . $post_check['id'] . " AND hash='" . $post_check['hash'] . "'";

		if(DB::query($sql, DB::INSERT_OR_UPDATE) === false)
			return false;
		return true;
	} 

	/*
		Функция перемещения в архив пользователя
		$post - массив с данными
		Return: TRUE - успех, FALSE - неудача
	*/
	public static function move_archive($post) {
		if(!is_array($post) || empty($post))
			return false;

		$post_check = array_change_key_case($post);
		if(empty($post_check['id']) || empty($post_check['hash']))
			return false;

		$sql = "DELETE FROM " . self::$table . " WHERE id=" . $post_check['id'] . " AND hash='" . $post_check['hash'] . "'";

		if(DB::query($sql, DB::OTHER) === false)
			return false;
		return true;
	}

	/*
		Функция сброса пароля на пароль по умолчанию
		$post - массив с данными
		Return: TRUE - успех, FALSE - неудача
	*/
	public static function reset_default_password($post) {
		if(!is_array($post) || empty($post))
			return false;

		$post_check = array_change_key_case($post);
		if(empty($post_check['id']) || empty($post_check['hash']))
			return false;

		$hash_password = password_hash(self::$default_password, PASSWORD_BCRYPT);

		$sql = "UPDATE " . self::$table . " SET passwd_hash = '" . $hash_password . "' "
			 . " WHERE id=" . $post_check['id'] . " AND hash='" . $post_check['hash'] . "'";

		if(DB::query($sql, DB::INSERT_OR_UPDATE) === false)
			return false;
		return true;
	}

	/*
		Функция сброса пароля на пароль по умолчанию
		$post - массив с данными
		Return: TRUE - успех, FALSE - неудача
	*/
	public static function change_role($post) {
		if(!is_array($post) || empty($post))
			return false;

		$post_check = array_change_key_case($post);
		if(empty($post_check['id']) || empty($post_check['hash']) || empty($post_check['role']))
			return false;

		$sql = "UPDATE " . self::$table . " SET role = '" . $post_check['role'] . "' "
			 . " WHERE id=" . $post_check['id'] . " AND hash='" . $post_check['hash'] . "'";

		if(DB::query($sql, DB::INSERT_OR_UPDATE) === false)
			return false;
		return true;
	}

	public static function save($post) {
		if(!is_array($post) || empty($post))
			return [false, 'Нет данных'];

		$id = self::get('id');
		$hash =self::get('hash');

		$post_check = array_change_key_case($post);

		$registration = new Registration();
		if($registration->check_login($post_check) === false) {
			return [false, $registration->message_error];
		}
		
		$fields = '';
		if(!empty($post_check['fam']))
			$fields .= (mb_strlen($fields) == 0) ? "fam='" . $post_check['fam'] . "'" : ",fam='" . $post_check['fam'] . "'";
		if(!empty($post_check['imj']))
			$fields .= (mb_strlen($fields) == 0) ? "imj='" . $post_check['imj'] . "'" : ",imj='" . $post_check['imj'] . "'";
		if(!empty($post_check['otch']))
			$fields .= (mb_strlen($fields) == 0) ? "otch='" . $post_check['otch'] . "'" : ",otch='" . $post_check['otch'] . "'";
		if(!empty($post_check['login']))
			$fields .= (mb_strlen($fields) == 0) ? "login='" . $post_check['login'] . "'" : ",login='" . $post_check['login'] . "'";

		$sql = "UPDATE " . self::$table . " SET " . $fields . " WHERE id=" . $id . " AND hash='" . $hash . "'";

		if(($data = DB::query($sql, DB::INSERT_OR_UPDATE)) === false)
			return [false, 'Ошибка при выполнении запроса'];
		return true;
	}

	public static function get_user_info() {
		$id = self::get('id');
		$hash = self::get('hash');

		$sql = "SELECT " . self::$table . ".*, role.text as role_text FROM " . self::$table
			. " LEFT JOIN role ON role.category=" . self::$table . ".role "
			. " WHERE " . self::$table . ".id=" . $id . " AND hash='" . $hash . "'";

		if(($data = DB::query($sql)) === false)
			return false;

		if(count($data) == 0)
			return false;
		
		return $data[0];
	}

	public static function change_password($post) {
		if(!is_array($post) || empty($post))
			return false;

		$id = self::get('id');
		$hash =self::get('hash');

		$post_check = array_change_key_case($post);

		$registration = new Registration();
		if($registration->check_password($post_check) === false) {
			return [false, $registration->message_error];
		}

		$passwd_hash = $registration->generate_hash_password($post_check);

		$sql = "UPDATE " . self::$table . " SET passwd_hash='" . $passwd_hash . "' WHERE id=" . $id . " AND hash='" . $hash . "'";
		if(($data = DB::query($sql,DB::INSERT_OR_UPDATE)) === false)
			return [false, 'Ошибка при выполнении запроса'];

		return true;
	}

	/*
		Функция установки значений по умолчанию для массива
	*/
	private static function default_information_user() {
		$session = new Session();
		$session->start();
		$session->set('login', '');
		$session->set('fam', '');
		$session->set('imj', '');
		$session->set('otch', '');
		$session->set('role', '');
		$session->set('hash', '');
		$session->set('id', '');
		$session->commit();
	}

	/*
		Функция для подгрузки актуальной информации о пользователе
	*/
	public static function load_actual_information_user() {
		$id = self::get('id');
		$hash = self::get('hash');
		$login = self::get('login');

		if((mb_strlen($id) == 0) || (mb_strlen($login) == 0) || (mb_strlen($hash) == 0)) {
			self::default_information_user();
			return;
		}

		$sql = "SELECT * FROM " . self::$table . " WHERE id=" . $id . " AND hash='" . $hash . "' AND login='" . $login . "'";
		if(($data = DB::query($sql)) === false) {
			self::default_information_user();
			return;
		}

		if(count($data) == 0) {
			self::default_information_user();
			return;
		}

		if($data[0]['access'] != 1) {
			self::default_information_user();
			return;	
		}

		$session = new Session();
		$session->start();
		$session->set('login', ((empty($data[0]['login'])) ? '' : $data[0]['login'] ));
		$session->set('fam', ((empty($data[0]['fam'])) ? '' : $data[0]['fam'] ));
		$session->set('imj', ((empty($data[0]['imj'])) ? '' : $data[0]['imj'] ));
		$session->set('otch', ((empty($data[0]['otch'])) ? '' : $data[0]['otch'] ));
		$session->set('role', ((empty($data[0]['role'])) ? '' : $data[0]['role'] ));
		$session->set('hash', ((empty($data[0]['hash'])) ? '' : $data[0]['hash'] ));
		$session->set('id', ((empty($data[0]['id'])) ? '' : $data[0]['id'] ));
		$session->commit();
	}

}