<?php

namespace IcKomiApp\core;

use IcKomiApp\lib\Database\DB;
use IcKomiApp\core\Logic;

/*
	RegistrationBasic - абстрактный класс для регистрации пользователя.
	Данный класс содержит свойства и методы необходимые для проведения процедуры заведения нового пользователя

	Константы:
		TABLE - название таблицы с пользователями
		MIN_LENGTH_PASSWORD - минимальная длина пароля
		NAME_FIELD_LOGIN - название поля в котором передан логин с формы
		NAME_FIELD_PASSWORD - название поля в котором передан пароль
		NAME_FIELD_PASSWORD2 - название поля для проверки пароля 
		FIELDS - массив с полями по умолчанию
		GLOBALS_ARRAY_SETTINGS - название массива с регистрационными настройками (массив берется из config\web.php)

	Массив FIELDS ($fields) для настроек полей.
	Данный массив необходим для настроек полей, которые будут переданы с формы и их соответствие с полями базы данных.
	На данный момент реализована следующая логика работы с массивом.
	$fields = [
		'название_поля_в_базе_данных' => ['type' => 'тип_данных',
										  'form' => 'название_на_форме',
										  'default' => 'значение_по_умолчанию'
										  'action' => 'название_функции_которая_будет_применена_для_этого_поля']
	]; 
	Замечание: если form установлен в null то данное поле не передано с формы и его не стоит искать в массиве $_POST

	Свойства:
		$table - название таблицы где хранятся сведения о пользователях
		$fields - массив с полями. Это основной массив на основе которого строится взаимодействие и работа всего класса
		$logic - массив с логическими условиями (см. класс IcKomiApp\core\Logic)
		$name_field_login - название поля (в массиве $_POST) для получения логина
		$name_field_password - название поля (в массиве $_POST) для получения пароля
		$name_field_password2 - название поля (в массиве $_POST) для проверки повторного правильного ввода пароля
		$min_length_password - минимальная длина пароля
		$message_error - публичная переменная для хранения текста последней ошибки

	Методы:
		Пользователь вправе переопределять функции public protected в наследуемом классе

	public:
		registration - основная функция для регистрации пользователя

	protected:
		check_user - функция проверки существует или такой пользователь или нет
		check_login	- функция проверки логина на корректность
		check_password - функция проверки пароля на валидность
		generate_hash_password - функция генерации хэша пароля
		get_value - функция получения значения для формирования SQL-запроса
		get_sql_insert - функция формирования SQL-запроса

	private:
		get_array_from_post - функция формирования безопасного массива из массива $_POST

	Copyright: Rostislav Gashin, 2020, Syktyvkar, Komi Republic, rostislav-gashin@yandex.ru
*/
abstract class RegistrationBasic {

	// Название таблицы с пользователями
	const TABLE = 'S_USERS';

	// Минимальная длина пароля
	const MIN_LENGTH_PASSWORD = 6;

	// Название поля для логина
	const NAME_FIELD_LOGIN = 'login';

	// Название поля для проверки пароля
	const NAME_FIELD_PASSWORD = 'password';

	// Название поля для подтверждения пароля
	const NAME_FIELD_PASSWORD2 = 'password2';

	// Набор полей по умолчанию
	const FIELDS = [];

	// Название глобального массива для забора настроек
	const GLOBALS_ARRAY_SETTINGS = 'registration_settings';

	// Таблица с пользователями
	protected $table = '';

	// Массив с перечислением полей
	protected $fields = [];

	// Массив с логическими условиями
	protected $logic = [];

	// Название поля (в массиве $_POST) для получения логина
	protected $name_field_login = '';

	// название поля (в массиве $_POST) для получения пароля
	protected $name_field_password = '';
	
	// Название поля (в массиве $_POST) для проверки повторного правильного ввода пароля
	protected $name_field_password2 = '';

	// Минимальная длина пароля
	protected $min_length_password = 0;

	// Переменная, содержащая ошибку
	public $message_error = '';

	/*
		Конструктор класса
		$settings - массив с настройками для регистрации, содержит [ключ] => [значение]
	*/
	public function __construct($settings = []) {

		if(!empty($settings['table']))
			$this->table = $settings['table'];
		else if(!empty($this->table))
			$this->table = $this->table;
		else if(!empty($GLOBALS[self::GLOBALS_ARRAY_SETTINGS]['table']))
			$this->table = $GLOBALS[self::GLOBALS_ARRAY_SETTINGS]['table'];
		else
			$this->table = self::TABLE;

		if(!empty($settings['name_field_login']))
			$this->name_field_login = $settings['name_field_login'];
		else if(!empty($this->name_field_login))
			$this->name_field_login = $this->name_field_login;
		else if(!empty($GLOBALS[self::GLOBALS_ARRAY_SETTINGS]['name_field_login']))
			$this->name_field_login = $GLOBALS[self::GLOBALS_ARRAY_SETTINGS]['name_field_login'];
		else
			$this->name_field_login = self::NAME_FIELD_LOGIN;

		if(!empty($settings['name_field_password']))
			$this->name_field_password = $settings['name_field_password'];
		else if(!empty($this->name_field_password))
			$this->name_field_password = $this->name_field_password;
		else if(!empty($GLOBALS[self::GLOBALS_ARRAY_SETTINGS]['name_field_password']))
			$this->name_field_password = $GLOBALS[self::GLOBALS_ARRAY_SETTINGS]['name_field_password'];
		else
			$this->name_field_password = self::NAME_FIELD_PASSWORD;

		if(!empty($settings['name_field_password2']))
			$this->name_field_password2 = $settings['name_field_password2'];
		else if(!empty($this->name_field_password2))
			$this->name_field_password2 = $this->name_field_password2;
		else if(!empty($GLOBALS[self::GLOBALS_ARRAY_SETTINGS]['name_field_password2']))
			$this->name_field_password2 = $GLOBALS[self::GLOBALS_ARRAY_SETTINGS]['name_field_password2'];
		else
			$this->name_field_password2 = self::NAME_FIELD_PASSWORD2;

		if(!empty($settings['min_length_password']))
			$this->min_length_password = $settings['min_length_password'];
		else if(!empty($this->min_length_password))
			$this->min_length_password = $this->min_length_password;
		else if(!empty($GLOBALS[self::GLOBALS_ARRAY_SETTINGS]['min_length_password']))
			$this->min_length_password = $GLOBALS[self::GLOBALS_ARRAY_SETTINGS]['min_length_password'];
		else
			$this->min_length_password = self::MIN_LENGTH_PASSWORD;

		if(!empty($settings['fields']))
			$this->fields = $settings['fields'];
		else if(!empty($this->fields))
			$this->fields = $this->fields;
		else if(!empty($GLOBALS[self::GLOBALS_ARRAY_SETTINGS]['fields']))
			$this->fields = $GLOBALS[self::GLOBALS_ARRAY_SETTINGS]['fields'];
		else
			$this->fields = self::FIELDS;

		$this->message_error = '';
	}

	/*
		Функция преобразования массива $_POST в необходимый формат
		$post - массив $_POST
	*/
	private function get_array_from_post($post) {
		return $post;
	}

	/*
		Функция проверки существует ли данный пользователь в базе данных
		$user_array - массив с пользовательскими данными
		Возвращаемое значение: TRUE - не существует, FALSE - существует
	*/
	public function check_user($user_array) {
		if(!is_array($user_array))
			return false;

		$login = (empty($user_array[$this->name_field_login])) ? '' : $user_array[$this->name_field_login];

		$sql = "SELECT COUNT(*) as COUNT FROM " . $this->table . " WHERE " . $this->name_field_login . " ='" . $login . "'";
		if(($data = DB::query($sql)) === false) {
			$this->message_error = 'Ошибка при выполнении запроса к базе данных!';
			return false;
		}

		if($data[0]['COUNT'] == 1) {
			$this->message_error = 'Пользователь с таким логином уже существует!';
			return false;
		}

		return true;
	}

	/*
		Функция которая проверяет корректность ввода логина.
		Пользователь может задать свою функцию проверки, тогда данная функция может быть проигнорирована
		$user_array - массив с данными пользователя
	*/
	public function check_login($user_array) {
		if(!is_array($user_array))
			return false;

		$login = (empty($user_array[$this->name_field_login])) ? '' : trim($user_array[$this->name_field_login]);

		if(preg_match('/[^a-z0-9]+$/ui', $login) == 1) {
			$this->message_error = 'В поле логин разрешается вводить только латинские буквы и цифры!';
			return false;
		}

		if(preg_match('/^[0-9]+$/ui', $login) == 1) {
			$this->message_error = 'Логин должен содержать хотя бы одну букву латинского алфавита!';
			return false;
		}

		return true;
	}

	/*
		Функция проверяет пароль на валидность
		$user_array -  массив с данными пользователя
	*/
	public function check_password($user_array) {
		if(!is_array($user_array))
			return false;

		$password = (empty($user_array[$this->name_field_password])) ? '' : trim($user_array[$this->name_field_password]);
		$password2 = (empty($user_array[$this->name_field_password2])) ? '' : trim($user_array[$this->name_field_password2]);
		if(mb_strlen($password) < self::MIN_LENGTH_PASSWORD) {
			$this->message_error = 'Минимальная длина пароля: ' . self::MIN_LENGTH_PASSWORD;
			return false;
		}

		if(preg_match('/[^a-z0-9]+/i', $password) == 1) {
			$this->message_error = 'В поле пароль разрешается вводить только латинские буквы и цифры!';
			return false;
		}

		if(preg_match('/^[0-9]+$/i', $password) == 1) {
			$this->message_error = 'Пароль должен содержать цифры и буквы латинского алфавита!';
			return false;
		}

		if(preg_match('/^[a-z]+$/i', $password) == 1) {
			$this->message_error = 'Пароль должен содержать цифры и буквы латинского алфавита!';
			return false;
		}

		if($password <> $password2) {
			$this->message_error = 'Пароли не совпадают!';
			return false;
		}

		return true;
	}

	/*
		$user_array - 
	*/
	public function generate_hash_password($user_array) {
		if(!is_array($user_array))
			return false;
		$password = trim($user_array[$this->name_field_password]);
		$hash_password = password_hash($password, PASSWORD_BCRYPT);
		return $hash_password;
	}

	/*
		Разбор полей
	 	Для определения типа параметра (надо или нет добавлять кавычки)
	 */
	protected function get_value($field, $user_array) {
		if(!is_array($user_array))
			return '';

		$value = '';
		$settings = $this->fields[$field];
		if($settings['form'] != null) {
			$value = $user_array[$settings['form']];
		} else {
			if(!empty($settings['default'])) {
				$value = $settings['default'];
			} else if(!empty($settings['action'])) {
				$function = $settings['action'];
				$value = $this->$function($user_array);
			}
		}

		$str_value = "";
		switch ($this->fields[$field]['type']) {
			case 'char':
				$str_value = "'" . $value . "'";
				break;

			case 'number':
					$str_value = (mb_strlen($value) == 0) ? 0 : preg_replace('/,/i', '.', $value);
				break;

			case 'date':
				$str_value = "'" . $value . "'";
				break;
			
			default:
				$str_value = $value;
				break;
		}
		return $str_value;
	}

	/*
		$user_array - 
	*/
	protected function get_sql_insert($user_array) {
		if(!is_array($user_array))
			return false;

		$sql_fields = $sql_values = '';
		foreach($this->fields as $field => $settings) {
			$sql_fields .= (mb_strlen($sql_fields) == 0) ? $field : ',' . $field;
			$sql_values .= (mb_strlen($sql_values) == 0) ? $this->get_value($field, $user_array) : ',' . $this->get_value($field, $user_array);
		}

		$sql = "INSERT INTO " . $this->table . " (" . $sql_fields . ") VALUES (" . $sql_values . ") ";
		return $sql;
	}

	/*
		Функция регистрации нового пользователя.
		Это публичная функция поэтому пользователь вправе ее переопределить самостоятельно
		$post - $_POST массив с данными от формы регистрации нового пользователя
		Возвращаемое значение: TRUE - в случае успешной регистрации пользователя; FALSE - в случае ошибки, также будет установлена переменная $message_error с текстом ошибки
	*/
	public function registration($post) {
		if(!(is_array($post)) || empty($post) || ($post === null))
			return false;

		$user_array = $this->get_array_from_post($post);

		$logic = new Logic($user_array, $this->logic);
		if(!$logic->check_logic($number_logic, $message_error)) {
			$this->message_error = $message_error;
			return false;
		}

		if(!$this->check_login($user_array))
			return false;

		if(!$this->check_password($user_array))
			return false;

		if(!$this->check_user($user_array))
			return false;

		if(($sql = $this->get_sql_insert($user_array)) === false) {
			$this->message_error = "Ошибка при формировании SQL-запроса!";
			return false;
		}

		if(!DB::query($sql, DB::INSERT_OR_UPDATE)) {
			$this->message_error = 'При создании пользователя произошла ошибка!';
			return false;
		}

		return true;
	}

}