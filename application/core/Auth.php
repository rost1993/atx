<?php

namespace IcKomiApp\core;

use IcKomiApp\lib\Database\DB;
use IcKomiApp\core\Session;
use IcKomiApp\core\Cookie;

class Auth {
	
	const TABLE_USERS = 'users';

	public static function login($post, &$message_error) {

		if(empty($_POST['login_user'])) {
			$message_error = 'Не заполнено поле логин!';
			return false;
		}

		extract($post, EXTR_OVERWRITE);

		$sql = "SELECT * FROM " . self::TABLE_USERS . " WHERE login='" . addslashes($login_user) . "'";

		if(($data = DB::query($sql)) === false) {
			$message_error = 'Ошибка выполнения запроса!';
			return false;
		}

		if(count($data) == 0) {
			$message_error = 'Пользователь не найден!';
			return false;
		}

		if($data[0]['acl'] != 1) {
			$message_error = 'В доступе отказано!';
			return false;
		}

		if(!password_verify($password_user, $data[0]["passwd_hash"])) {
			$message_error = 'В доступе отказано!';
			return false;
		}
		
		$session = new Session();
		$session->start();
		$session->set("login", $data[0]["login"]);
		$session->set("id", $data[0]["id"]);
		$session->set("role", $data[0]["role"]);
		$session->set("kodrai", $data[0]["kodrai"]);
		$session->set("security", $data[0]["hash"]);
		$session->set("fam", $data[0]["fam"]);
		$session->set("imj", $data[0]["imj"]);
		$session->set("otch", $data[0]["otch"]);
		$session->commit();
		
		return true;
	}

	// Функция уничтожения данных сессий
	public static function logout() {
		$session = new Session();
		$session->start();
		$session->destroy();
	}

}