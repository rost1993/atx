<?php
namespace IcKomiApp\core;

/*
	Класс для взимодействия с сессиями
	Позволяет осуществлять все действия с сессиями
	Настройки сессии указываются в конфигурационном файле config/web.php -> параметр session
*/
class Session {

	// Настройки сессии
	protected $session_name = '';
	protected $session_cipher = '';

	// Название по умолчанию для сессий
	protected $session_name_default = 'IC_KOMI';

	/*
		Конструктор класса для взаимодействия с сессиями
		Производит считывание данных с конфигурационного файла и запись в служебные переменные
	*/
	public function __construct() {
		if(empty($GLOBALS['web_config']['session'])) {
			$session_name = 'IC KOMI';
			$session_cipher = '';
		} else {
			$this->session_name = (empty($GLOBALS['web_config']['session']['name']) ? $this->session_name_default : $GLOBALS['web_config']['session']['name']);
			$this->session_cipher = (empty($GLOBALS['web_config']['session']['cipher']) ? $this->session_name_default : $GLOBALS['web_config']['session']['cipher']);;
		}
	}

	// Старт интерфейса для взаимодействия с массивами сессий
	public function start() {
		if(!$this->is_started()) {
			session_name($this->session_name);
			session_start();
		}
	}

	/*
		Проверка запущена сессия или нет
		Возвращаемое значение: TRUE - запущена, FALSE - не запущена
	*/
	private function is_started() {
		if(!(session_status() === PHP_SESSION_ACTIVE))
			return false;

		if(!(session_name() === $this->session_name)) {
			$this->destroy();
			return false;
		}
		return true;
	}

	/*
		Получить значение параметра из массива сессий
		$name - название параметра
		Возвращаемое значение: null - если параметр не найден или значение параметра
	*/
	public function get($name) {
		if($this->is_started())
			return ((empty($_SESSION[$name])) ? null : $_SESSION[$name]);
	}

	/*
		Получить значение параметра из массива сессий, используя значение по умолчанию
		$name - название параметра
		$default_value - значение по умолчанию
		Возвращаемое значение: значение по умолчанию или значение параметра
	*/
	public function get_value($name, $default_value) {
		if($this->is_started())
			return ((empty($_SESSION[$name])) ? $default_value : $_SESSION[$name]);
	}

	/*
		Установить значение в массив сессий
		$name - название параметра
		$value - значение параметра
	*/
	public function set($name, $value = '') {
		if($this->is_started())
			$_SESSION[$name] = $value;
	}

	/*
		Удаление параметра в массиве сессий
		$name - название параметра
	*/
	public function del($name) {
		if($this->is_started())
			unset($_SESSION[$name]);
	}

	// Уничтожение данных массива сессий и уничтожение сведений о сессии
	public function destroy() {
		if(session_status() === PHP_SESSION_ACTIVE) {
			$_SESSION = array();
			session_unset();
			session_destroy();
		}
	}

	// Очистка данных массива сессий
	public function clear() {
		if($this->is_started())
			unset($_SESSION);
	}

	// Перезапустить сессию веб-ресурса
	public function restart() {
		$this->destroy();
		$this->start();
	}

	// Получить сессионные данные в виде массива
	public function get_array_session() {
		if($this->is_started())
			return $_SESSION;
	}

	// Запись изменений и закрытие сеанса работы с классом сессий
	public function commit() {
		if($this->is_started())
			session_write_close();
	}

	/*
		Проверка существует или нет параметр $name в массиве сессий
		$name - название параметр
		Возвращает: TRUE - существует, FALSE - не существует
	*/
	public function has($name) {
		return empty($_SESSION[$name]) ? false : true;
	}
}