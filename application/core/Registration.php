<?php

namespace IcKomiApp\core;

/*
	Класс Registration наследует функционал класса RegistrationBasic
	Copyright: Rostislav Gashin, 2020, Syktyvkar, Komi Republic, rostislav-gashin@yandex.ru
*/
class Registration extends RegistrationBasic {
	
	protected $table = 'users';

	const BLACK_MAGIC_CONSTANT = 'IamSuperRost1993';

	protected $name_field_login = 'login';
	protected $name_field_password = 'password';
	protected $name_field_password2 = 'password2';

	protected $fields = ['LASTNAME' =>
							['type' => 'char', 'form' => 'LASTNAME'],
						 'FIRSTNAME' =>
						 	['type' => 'char', 'form' => 'FIRSTNAME'],
						 'MIDDLENAME' =>
						 	['type' => 'char', 'form' => 'MIDDLENAME'],
						 'LOGIN' =>
						 	['type' => 'char', 'form' => 'LOGIN'],
						 'REGION' =>
						 	['type' => 'number', 'form' => 'REGION'],
						 'SERVICE' =>
						 	['type' => 'number', 'form' => 'SERVICE'],
						 'PASSWD_HASH' =>
						 	['type' => 'char', 'form' => null, 'action' => 'generate_hash_password'],
						 'UNIQ_HASH' =>
						 	['type' => 'char', 'form' => null, 'action' => 'generate_hash'],
						 'EMAIL_ADDRESS' =>
						 	['type' => 'char', 'form' => null, 'action' => 'generate_email_address'],
						 'ID_GROUP' =>
						 	['type' => 'number', 'form' => null, 'default' => '0'],
						 'FLG_ACCESS' =>
						 	['type' => 'number', 'form' => null, 'default' => '0'],
						 'IBD_ARX' =>
						 	['type' => 'number', 'form' => null, 'default' => '1']
						];

	protected $logic = [
		['number' => '1',
		 'type' => '1',
		 'operation' => '!',
		 'field1' => 'LOGIN',
		 'message' => 'Не заполнен обязательный реквизит: Логин!',
			],
		];

	/*
		Функция генерации уникального хэша для пользователя
		Это специализированная функция, ссылка на которую находится в массиве fields и не входит в стандартную процедуру регистрации
		$user_array - $_POST массив переданный от формы регистрации
		Возвращаемое значение: 64-байтный уникальный хэш
	*/
	protected function generate_hash($user_array) {
		$current_date = date('Y') . "-" . date('m') . "-" . date('d') . "-" . date('h-i-s');

		if(!is_array($user_array))
			return $hash = hash('sha256', $current_date . self::BLACK_MAGIC_CONSTANT);

		$login = (empty($user_array[$this->name_field_login])) ? '' : trim($user_array[$this->name_field_login]);
		$password = (empty($user_array[$this->name_field_password])) ? '' : trim($user_array[$this->name_field_password]);
		$hash = hash('sha256', $login . $password . $current_date . self::BLACK_MAGIC_CONSTANT);

		return $hash;
	}

	/*
		Функция форомирования email-адреса на основе логина пользователя
		Это специализированная функция, ссылка на которую находится в массиве fields и не входит в стандартную процедуру регистрации
		$user_array - $_POST массив переданный от формы регистрации
		Возвращаемое значение: email-адрес или пустое значение
	*/
	protected function generate_email_address($user_array) {
		if(!is_array($user_array))
			return '';

		$login = (empty($user_array[$this->name_field_login])) ? '' : trim($user_array[$this->name_field_login]);
		$email = $login . '@mvd.ru';
		return $email;
	}
}