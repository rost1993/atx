<?php

namespace IcKomiApp\models;

use IcKomiApp\core\Functions;

class Install {

	private $db_name = 'mysql';
	private $host = 'localhost';
	private $login = '';
	private $password = '';
	private $charset = 'utf8mb4';

	private $create_db_name = 'atx123';
	private $create_user = 'atx123';
	private $create_user_password = 'AtxDatabase2020';

	private $message_error = '';

	private function connect() {
		$link = mysqli_connect($this->host, $this->login, $this->password, $this->db_name);
		
		if(!$link)
			return null;
		
		mysqli_query($link, "SET NAMES '" . $this->charset . "'");
		return $link;
	}

	private function disconnect($link) {
		return mysqli_close($link);
	}

	private function multi_query($link, $sql, $error) {
		if(!mysqli_multi_query($link, $sql)) {
			$this->message_error = $error;
			return false;
		}

		do {
        	if ($result = mysqli_store_result($link))
            	mysqli_free_result($result);
            if(!mysqli_more_results($link))
            	break;
    	} while (mysqli_next_result($link));
    	return true;
	}


	public function install_database($post) {
		if(empty($post['login']))
			return [-1];

		$this->login = $post['login'];
		$this->password = $post['password'];

		if(($link = $this->connect()) === null)
			return [-2, 'Ошибка при подключении к MySQL!'];

		if(!$this->create_database($link))
			return [-2, $this->message_error];

		if(!$this->create_tables($link))
			return [-2, $this->message_error];

		if(!$this->create_trigger($link))
			return [-2, $this->message_error];

		if(!$this->create_user($link))
			return [-2, $this->message_error];

		$this->create_index($link);
		$this->create_auto_increment($link);
		$this->create_procedure($link);

		$this->disconnect($link);

		return [1];
	}




	/*
		Функция создания базы данных
	*/
	private function create_database($link) {
		$sql = "CREATE DATABASE IF NOT EXISTS `" . $this->create_db_name . "` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci";

		if(!mysqli_query($link, $sql)) {
			$this->message_error = 'Ошибка при создании базы данных!';
			return false;
		}
		return true;
	}

	/*
		Функция создания таблиц базы данных
	*/
	private function create_tables($link) {
		$sql = "USE `" . $this->create_db_name . "`";
		if(!mysqli_query($link, $sql)) {
			$this->message_error = 'Ошибка при создании базы данных!';
			return false;
		}

		$sql = "CREATE TABLE IF NOT EXISTS `adm_offense` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'ID записи',
  `id_driver` int(10) UNSIGNED NOT NULL COMMENT 'ID водителя',
  `id_car` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'ID ТС',
  `date_adm` date NOT NULL COMMENT 'Дата',
  `time_adm` varchar(5) NOT NULL COMMENT 'Время совершения административки',
  `st_chast_koap` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Статья и часть КоАП (спр. 26)',
  `st_adm` varchar(10) DEFAULT NULL COMMENT 'Статья КоАП РФ',
  `chast_adm` varchar(5) DEFAULT NULL COMMENT 'Часть КоАП РФ',
  `sum_adm` decimal(10,2) NOT NULL COMMENT 'Сумма штрафа',
  `oplat_adm` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Оплачен или нет штраф',
  `comment_adm` varchar(1000) NOT NULL COMMENT 'Примечание',
  `place_adm` varchar(500) DEFAULT NULL COMMENT 'Место совершения',
  `ibd_arx` int(10) UNSIGNED NOT NULL DEFAULT 1 COMMENT 'Актуальность записи',
  `sh_polz` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Шифр пользователя',
  `dt_reg` datetime DEFAULT NULL COMMENT 'Дата ввода записи',
  `dt_izm` datetime DEFAULT NULL COMMENT 'Дата корректировки записи'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Таблица с административными правонарушениями водителей';";

		if(!mysqli_query($link, $sql)) {
			$this->message_error = 'Ошибка при создании таблицы adm_offense!';
			return false;
		}

		$sql = "CREATE TABLE IF NOT EXISTS `cars` (
  `id` int(11) UNSIGNED NOT NULL COMMENT 'Уникальный номер ТС',
  `vin` varchar(100) DEFAULT NULL COMMENT 'VIN ТС',
  `marka` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Марка ТС',
  `model` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Модель ТС',
  `gos_znak` varchar(11) DEFAULT NULL COMMENT 'Гос знак',
  `n_dvig` varchar(100) DEFAULT NULL COMMENT 'Номер двигателя',
  `shassi` varchar(100) DEFAULT NULL COMMENT 'Номер шасси',
  `kuzov` varchar(100) DEFAULT NULL COMMENT 'Номер кузова',
  `color` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Цвет ТС',
  `car_vat` decimal(10,2) DEFAULT NULL COMMENT 'Мощность ТС',
  `car_v` int(11) UNSIGNED DEFAULT NULL COMMENT 'Объем двигателя',
  `mass_max` int(11) UNSIGNED DEFAULT NULL COMMENT 'Максимальная масса ТС',
  `mass_min` int(11) UNSIGNED DEFAULT NULL COMMENT 'Минимальная масса ТС',
  `inventory_n` varchar(50) DEFAULT NULL COMMENT 'Инвентарный номер',
  `ibd_arx` int(11) UNSIGNED NOT NULL DEFAULT 1 COMMENT 'Архив: 1 - актуальный',
  `dostup` int(11) UNSIGNED NOT NULL DEFAULT 1 COMMENT 'Доступ к ТС: 1 - доступ открыт',
  `kodrai` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Код района кому принадлежит ТС',
  `n_reg` int(11) UNSIGNED DEFAULT NULL COMMENT 'Номер региона для гос знака',
  `kateg_ts` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Категория ТС',
  `god_car` int(11) UNSIGNED DEFAULT NULL COMMENT 'Год выпуска ТС',
  `tip_strah` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Тип страховки',
  `kateg_mvd` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Категория ТС для МВД',
  `kateg_gost` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Категория ТС по ГОСТ',
  `marka1` varchar(200) DEFAULT NULL COMMENT 'Марка 1',
  `prim` varchar(3000) DEFAULT NULL COMMENT 'Примечание',
  `slugba` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Служба которой принадлежит ТС',
  `num_speedometer` int(10) NOT NULL DEFAULT 1 COMMENT 'Количество спидометров',
  `mileage` double(10,2) NOT NULL DEFAULT 0.00 COMMENT 'Пробег транспортрного средства',
  `mileage_oil` int(10) UNSIGNED DEFAULT 10000 COMMENT 'Пробег для замены масла',
  `basic_fuel` decimal(10,2) DEFAULT NULL COMMENT 'Базовая норма расхода топлива',
  `summer_fuel` decimal(10,2) DEFAULT NULL COMMENT 'Летняя норма топлива',
  `winter_fuel` decimal(10,2) DEFAULT NULL COMMENT 'Зимняя норма',
  `balance_price` decimal(10,2) DEFAULT NULL COMMENT 'Первоначальная балансовая стоимость',
  `exception_notice_events` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Исключения для сверок. 0 - не исключать, 1 - исключать',
  `write_off` int(11) NOT NULL DEFAULT 0 COMMENT 'ТС готовится к списанию: 0 - нет, 1 - да',
  `sh_polz` int(11) NOT NULL DEFAULT 0 COMMENT 'Шифр пользователя, сделавшего изменения',
  `dt_reg` datetime DEFAULT NULL COMMENT 'Дата ввода',
  `dt_izm` datetime DEFAULT NULL COMMENT 'Дата корректировки'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

		if(!mysqli_query($link, $sql)) {
			$this->message_error = 'Ошибка при создании таблицы cars!';
			return false;
		}

		$sql = "CREATE TABLE IF NOT EXISTS `cars_dopog` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'Уникальный ID',
  `id_car` int(10) UNSIGNED NOT NULL COMMENT 'ID транспортного средства',
  `number_dopog` varchar(50) NOT NULL COMMENT 'Номер свидетельства',
  `date_start_dopog` date DEFAULT NULL COMMENT 'Дата выдачи',
  `date_end_dopog` date DEFAULT NULL COMMENT 'Дата окончания',
  `firma_dopog` int(11) NOT NULL DEFAULT 0 COMMENT 'Фирма, выдавшая. Спр. 34',
  `path_to_file` varchar(100) DEFAULT NULL COMMENT 'Путь к файлу',
  `file_extension` varchar(50) DEFAULT NULL COMMENT 'Расшиерение файла',
  `ibd_arx` int(11) NOT NULL DEFAULT 1 COMMENT 'Архив',
  `sh_polz` int(11) NOT NULL DEFAULT 0 COMMENT 'Шифр пользователя',
  `dt_reg` datetime DEFAULT NULL COMMENT 'Дата ввода',
  `dt_izm` datetime DEFAULT NULL COMMENT 'Дата корректировки'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Свидетельство ДОПОГ для ТС';";

		if(!mysqli_query($link, $sql)) {
			$this->message_error = 'Ошибка при создании таблицы cars_dopog!';
			return false;
		}

		$sql = "CREATE TABLE IF NOT EXISTS `cars_old_gos_znak` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'Уникальный ID записи',
  `id_car` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'ID транспортного средства',
  `gos_znak` varchar(11) DEFAULT NULL COMMENT 'Гос номер транспортного средства',
  `sh_polz` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Шифр пользователя',
  `dt_reg` datetime DEFAULT NULL COMMENT 'Дата ввода'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Таблица для хранения старых номеров ТС';";

		if(!mysqli_query($link, $sql)) {
			$this->message_error = 'Ошибка при создании таблицы cars_old_gos_znak!';
			return false;
		}

		$sql = "CREATE TABLE IF NOT EXISTS `cars_wheels` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'Уникальный ID',
  `id_car` int(10) UNSIGNED NOT NULL COMMENT 'ID транспортного средства',
  `type_wheel` int(10) UNSIGNED NOT NULL COMMENT 'Тип (спр. 28)',
  `season_wheel` int(10) UNSIGNED NOT NULL COMMENT 'Тип сезона (спр. 27)',
  `size_wheel` int(11) NOT NULL DEFAULT 0 COMMENT 'Размер (спр. 31)',
  `marka_wheel` int(11) NOT NULL COMMENT 'Марка шины (спр. 29)',
  `model_wheel` int(11) NOT NULL COMMENT 'Модель шины (спр. 30)',
  `date_installation` date DEFAULT NULL COMMENT 'Дата установки',
  `comment_wheel` text DEFAULT NULL COMMENT 'Примечание',
  `ibd_arx` int(10) UNSIGNED NOT NULL DEFAULT 1 COMMENT 'Архив',
  `dt_izm` datetime DEFAULT NULL COMMENT 'Дата корректировки записи',
  `dt_reg` datetime DEFAULT NULL COMMENT 'Дата ввода записи',
  `sh_polz` int(11) NOT NULL COMMENT 'Шифр пользователя'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Учет шин';";

		if(!mysqli_query($link, $sql)) {
			$this->message_error = 'Ошибка при создании таблицы cars_wheels!';
			return false;
		}

		$sql = "CREATE TABLE IF NOT EXISTS `car_battery` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'ID записи',
  `id_car` int(10) UNSIGNED NOT NULL COMMENT 'ID транспортного средства',
  `type_battery` varchar(50) NOT NULL COMMENT 'Тип АКБ',
  `number_battery` varchar(50) NOT NULL COMMENT 'Номер АКБ',
  `producion_date` date DEFAULT NULL COMMENT 'Дата изготовления АКБ',
  `firma_battery` varchar(100) NOT NULL COMMENT 'Производитель АКБ',
  `start_date` date NOT NULL COMMENT 'Дата установки АКБ',
  `debit_date` date DEFAULT NULL COMMENT 'Дата списания АКБ',
  `standart_term_battery` int(11) DEFAULT NULL COMMENT 'Нормативный срок эксплуатации до списания',
  `standart_term_debit_battery` int(11) DEFAULT NULL COMMENT 'Нормативная наработка АКБ до списания',
  `ibd_arx` int(11) NOT NULL DEFAULT 1 COMMENT 'Архив',
  `sh_polz` int(11) NOT NULL DEFAULT 0 COMMENT 'Шифр пользователя',
  `dt_izm` datetime DEFAULT NULL COMMENT 'Дата изменения',
  `dt_reg` datetime DEFAULT NULL COMMENT 'Дата ввода'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Аккумуляторы';";

		if(!mysqli_query($link, $sql)) {
			$this->message_error = 'Ошибка при создании таблицы car_battery!';
			return false;
		}

		$sql = "CREATE TABLE IF NOT EXISTS `car_calibration` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'Уникальный ID',
  `id_car` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'ID ТС',
  `date_calibration` date DEFAULT NULL COMMENT 'Дата клибровки',
  `firma_calibration` int(11) NOT NULL DEFAULT 0 COMMENT 'Кем проведена калибровка',
  `date_next_calibration` date DEFAULT NULL COMMENT 'Дата следующей калибровки',
  `path_to_file` varchar(100) DEFAULT NULL COMMENT 'Путь к файлу',
  `file_extension` varchar(50) DEFAULT NULL COMMENT 'Расширение файла',
  `sh_polz` int(11) NOT NULL DEFAULT 0 COMMENT 'Шифр пользователя',
  `ibd_arx` int(11) NOT NULL DEFAULT 1 COMMENT 'Архив',
  `dt_reg` datetime DEFAULT NULL COMMENT 'Дата ввода',
  `dt_izm` datetime DEFAULT NULL COMMENT 'Дата изменения'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Калибровка ТС';";

		if(!mysqli_query($link, $sql)) {
			$this->message_error = 'Ошибка при создании таблицы car_calibration!';
			return false;
		}

		$sql = "CREATE TABLE IF NOT EXISTS `car_documents` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'Уникальный ID',
  `type_car_document` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Тип документа (спр. 23)',
  `date_car_document` date DEFAULT NULL COMMENT 'Дата документа',
  `number_car_document` varchar(20) DEFAULT NULL COMMENT 'Номер документа',
  `path_to_file` varchar(500) DEFAULT NULL COMMENT 'Путь к файлу',
  `file_extension` varchar(10) DEFAULT NULL COMMENT 'Расширение файла',
  `sh_polz` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Шифр пользователя',
  `dt_reg` datetime DEFAULT NULL COMMENT 'Дата ввода',
  `dt_izm` datetime DEFAULT NULL COMMENT 'Дата корректировки'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Таблица с документами,связанными с ТС';";

		if(!mysqli_query($link, $sql)) {
			$this->message_error = 'Ошибка при создании таблицы car_documents!';
			return false;
		}

		$sql = "CREATE TABLE IF NOT EXISTS `car_dvr` (
  `id` int(11) NOT NULL COMMENT 'Уникальный ID',
  `id_car` int(11) NOT NULL DEFAULT 0 COMMENT 'ID ТС',
  `number_dvr` varchar(30) DEFAULT NULL COMMENT 'Номер',
  `marka_dvr` varchar(50) DEFAULT NULL COMMENT 'Марка',
  `model_dvr` varchar(50) DEFAULT NULL COMMENT 'Модель',
  `date_issued_dvr` date DEFAULT NULL COMMENT 'Дата установки',
  `ibd_arx` int(11) NOT NULL DEFAULT 1 COMMENT 'Архив',
  `sh_polz` int(11) NOT NULL DEFAULT 0 COMMENT 'Шифр пользователя',
  `dt_reg` datetime DEFAULT NULL COMMENT 'Дата ввода',
  `dt_izm` datetime DEFAULT NULL COMMENT 'Дата корректировки'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Видеорегистраторы';";

		if(!mysqli_query($link, $sql)) {
			$this->message_error = 'Ошибка при создании таблицы car_dvr!';
			return false;
		}

		$sql = "CREATE TABLE IF NOT EXISTS `car_fire_extinguisher` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'Уникальный ID',
  `id_car` int(10) UNSIGNED NOT NULL COMMENT 'ID транспортного средства',
  `issued_date` date DEFAULT NULL COMMENT 'Дата выдачи',
  `start_date` date DEFAULT NULL COMMENT 'Дата ввода',
  `end_date` date DEFAULT NULL COMMENT 'Дата окончания',
  `shelf_life` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Срок годности',
  `ibd_arx` int(10) UNSIGNED NOT NULL DEFAULT 1 COMMENT 'Архив',
  `sh_polz` int(10) UNSIGNED DEFAULT 0 COMMENT 'Шифр пользователя',
  `dt_reg` datetime DEFAULT NULL COMMENT 'Дата ввода',
  `dt_izm` datetime DEFAULT NULL COMMENT 'Дата корректировки'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Огнетушители';";

		if(!mysqli_query($link, $sql)) {
			$this->message_error = 'Ошибка при создании таблицы car_fire_extinguisher!';
			return false;
		}

		$sql = "CREATE TABLE IF NOT EXISTS `car_first_aid_kid` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'Уникальный ID',
  `id_car` int(10) UNSIGNED NOT NULL COMMENT 'ID транспортного средства',
  `issued_date` date DEFAULT NULL COMMENT 'Дата выдачи',
  `start_date` date DEFAULT NULL COMMENT 'Начало эксплуатации',
  `end_date` date DEFAULT NULL COMMENT 'Конец эксплуатации',
  `shelf_life` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Срок годности',
  `ibd_arx` int(10) UNSIGNED NOT NULL DEFAULT 1 COMMENT 'Архив',
  `sh_polz` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Шифр пользователя',
  `dt_reg` datetime DEFAULT NULL COMMENT 'Дата ввода',
  `dt_izm` datetime DEFAULT NULL COMMENT 'Дата корректировки'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Медицинские аптечки';";
	
		if(!mysqli_query($link, $sql)) {
			$this->message_error = 'Ошибка при создании таблицы car_first_aid_kid!';
			return false;
		}

		$sql = "CREATE TABLE IF NOT EXISTS `car_for_driver` (
  `id` int(11) NOT NULL COMMENT 'Светчик',
  `id_driver` int(11) NOT NULL COMMENT 'связь с водителем',
  `type_doc_fix` int(2) NOT NULL COMMENT 'Справочник оснований',
  `number_doc_fix` varchar(20) NOT NULL COMMENT 'номер основания',
  `date_doc_fix` date NOT NULL COMMENT 'дата закрепления',
  `car_id` int(11) NOT NULL COMMENT 'связь с машиной',
  `path_to_file` varchar(500) DEFAULT NULL COMMENT 'Путь к файлу',
  `file_extension` varchar(10) DEFAULT NULL COMMENT 'Расширение файла',
  `dt_reg` datetime NOT NULL COMMENT 'Дата ввода',
  `dt_izm` datetime DEFAULT NULL COMMENT 'Дата корректировки',
  `ibd_arx` int(11) NOT NULL COMMENT 'Статус записи',
  `dostup` int(11) NOT NULL COMMENT 'Уровень видимости',
  `sh_polz` int(11) NOT NULL DEFAULT 0 COMMENT 'Шифр пользователя'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Связь транспортных средств и водителей';";

		if(!mysqli_query($link, $sql)) {
			$this->message_error = 'Ошибка при создании таблицы car_for_driver!';
			return false;
		}

		$sql = "CREATE TABLE IF NOT EXISTS `car_glonass` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'Уникальный ID',
  `id_car` int(11) NOT NULL DEFAULT 0 COMMENT 'ID ТС',
  `number_glonass` varchar(50) DEFAULT NULL COMMENT 'Номер ГЛОНАСС',
  `number_dut_glonass_1` varchar(50) DEFAULT NULL COMMENT 'Номер ДУТ1',
  `number_dut_glonass_2` varchar(50) DEFAULT NULL COMMENT 'Номер ДУТ2',
  `date_glonass` date DEFAULT NULL COMMENT 'Дата установки',
  `path_to_file` varchar(100) DEFAULT NULL COMMENT 'Путь к файлу',
  `file_extension` varchar(50) DEFAULT NULL COMMENT 'Расширение файла',
  `ibd_arx` int(11) NOT NULL DEFAULT 1 COMMENT 'Архив',
  `sh_polz` int(11) NOT NULL DEFAULT 0 COMMENT 'Шифр пользователя',
  `dt_reg` datetime DEFAULT NULL COMMENT 'Дата ввода',
  `dt_izm` datetime DEFAULT NULL COMMENT 'Дата корректировки'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='ГЛОНАСС';";

		if(!mysqli_query($link, $sql)) {
			$this->message_error = 'Ошибка при создании таблицы car_glonass!';
			return false;
		}

		$sql = "CREATE TABLE IF NOT EXISTS `car_link_document` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'ID записи',
  `id_car` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'ID транспортного средства',
  `id_document` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'ID документа',
  `title_document` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Содержание документа (спр. 24)',
  `comment` varchar(1000) DEFAULT NULL COMMENT 'Комментарий для связи',
  `sh_polz` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Шифр пользователя',
  `dt_reg` datetime DEFAULT NULL COMMENT 'Дата ввода',
  `dt_izm` datetime DEFAULT NULL COMMENT 'Дата редактирования'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Связь автомобилей и документов';";

		if(!mysqli_query($link, $sql)) {
			$this->message_error = 'Ошибка при создании таблицы car_link_document!';
			return false;
		}

		$sql = "CREATE TABLE IF NOT EXISTS `car_repair` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'Уникальный ID ремонта',
  `id_car` int(10) UNSIGNED NOT NULL COMMENT 'ID транспортного средства',
  `car_mileage` double(10,2) NOT NULL DEFAULT 0.00 COMMENT 'Пробег ТС',
  `org_repair` int(10) UNSIGNED NOT NULL COMMENT 'Организация производящая ремонт (спр. 18)',
  `date_start_repair` date DEFAULT NULL COMMENT 'Дата начала ремонта',
  `date_end_repair` date DEFAULT NULL COMMENT 'Дата окончания ремонта',
  `price_repair` decimal(10,2) DEFAULT NULL COMMENT 'Цена ремонта',
  `prim_repair` varchar(4000) DEFAULT NULL COMMENT 'Примечание ремонта',
  `change_oil` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Была ли произведена замена масла: 0 - нет, 1 - да',
  `dt_reg` datetime DEFAULT NULL COMMENT 'Дата ввода записи',
  `dt_izm` datetime DEFAULT NULL COMMENT 'Дата редактирования',
  `sh_polz` int(11) NOT NULL DEFAULT 0 COMMENT 'Шифр пользователя',
  `ibd_arx` int(11) NOT NULL DEFAULT 1 COMMENT 'Архив'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Список ремонтов ТС';";

		if(!mysqli_query($link, $sql)) {
			$this->message_error = 'Ошибка при создании таблицы car_repair!';
			return false;
		}

		$sql = "CREATE TABLE IF NOT EXISTS `car_tachograph` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'Уникальный ID записи',
  `id_car` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'ID ТС',
  `number_tachograph` varchar(30) DEFAULT NULL COMMENT 'Номер тахографа',
  `model_tachograph` int(11) NOT NULL DEFAULT 0 COMMENT 'Модель тахографа. Спр. 39',
  `date_start_skzi` date DEFAULT NULL COMMENT 'Дата установки блока СКЗИ',
  `date_end_skzi` date DEFAULT NULL COMMENT 'Дата окончания блока СКЗИ',
  `path_to_file` varchar(100) DEFAULT NULL COMMENT 'Путь к файлу',
  `file_extension` varchar(50) DEFAULT NULL COMMENT 'Расширение файла',
  `ibd_arx` int(10) UNSIGNED NOT NULL DEFAULT 1 COMMENT 'Архив',
  `sh_polz` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Шифр пользователя',
  `dt_reg` datetime DEFAULT NULL COMMENT 'Дата ввода',
  `dt_izm` datetime DEFAULT NULL COMMENT 'Дата корректировки'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Тахограф';";

		if(!mysqli_query($link, $sql)) {
			$this->message_error = 'Ошибка при создании таблицы car_tachograph!';
			return false;
		}

		$sql = "CREATE TABLE IF NOT EXISTS `car_warning_triangle` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'Уникальный ID',
  `id_car` int(10) UNSIGNED NOT NULL COMMENT 'ID транспортного средства',
  `issued_date` date DEFAULT NULL COMMENT 'Дата выдачи',
  `ibd_arx` int(10) UNSIGNED NOT NULL DEFAULT 1 COMMENT 'Архив',
  `sh_polz` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Шифр пользователя',
  `dt_izm` datetime DEFAULT NULL COMMENT 'Дата корректировки',
  `dt_reg` datetime DEFAULT NULL COMMENT 'Дата ввода'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Знаки аварийной остановки';";

		if(!mysqli_query($link, $sql)) {
			$this->message_error = 'Ошибка при создании таблицы car_warning_triangle!';
			return false;
		}

		$sql = "CREATE TABLE IF NOT EXISTS `certificate_registration` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'Уникальный ID',
  `id_car` int(10) UNSIGNED NOT NULL COMMENT 'ID ТС',
  `s_certificate_reg` varchar(10) NOT NULL COMMENT 'Серия сертификата',
  `n_certificate_reg` varchar(10) NOT NULL COMMENT 'Номер сертификата',
  `date_certificate_reg` date DEFAULT NULL COMMENT 'Дата выдачи сертификата',
  `org_certificate_reg` int(10) UNSIGNED NOT NULL COMMENT 'Кем выдан сертификат (спр. 22)',
  `comment_certificate_reg` varchar(1000) NOT NULL COMMENT 'Комментарий',
  `path_to_file` varchar(500) DEFAULT NULL COMMENT 'Путь к файлу',
  `file_extension` varchar(10) DEFAULT NULL COMMENT 'Расширение файла',
  `ibd_arx` int(10) UNSIGNED NOT NULL DEFAULT 1 COMMENT 'Архив',
  `sh_polz` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Шифр пользователя',
  `dt_reg` datetime DEFAULT NULL COMMENT 'Дата ввода',
  `dt_izm` datetime DEFAULT NULL COMMENT 'Дата корректировки'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Таблица со списком свидетельств о регистрации ТС';";

		if(!mysqli_query($link, $sql)) {
			$this->message_error = 'Ошибка при создании таблицы certificate_registration!';
			return false;
		}

		$sql = "CREATE TABLE IF NOT EXISTS `drivers` (
  `id` int(11) UNSIGNED NOT NULL COMMENT 'Уникальный номер водителя',
  `fam` varchar(150) NOT NULL COMMENT 'Фамилия водителя',
  `imj` varchar(150) NOT NULL COMMENT 'Имя водителя',
  `otch` varchar(150) NOT NULL COMMENT 'Отчество водителя',
  `dt_rojd` date DEFAULT NULL COMMENT 'Дата рождения водителя',
  `mob_phone` varchar(20) DEFAULT NULL COMMENT 'Мобильный телефон водителя',
  `home_address` varchar(300) DEFAULT NULL COMMENT 'Домашний адрес водителя',
  `slugba` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Служба водителя',
  `ibd_arx` int(11) UNSIGNED NOT NULL DEFAULT 1 COMMENT 'Архив',
  `dostup` int(11) UNSIGNED NOT NULL DEFAULT 1 COMMENT 'Доступ к водителю',
  `kodrai` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Район водителя',
  `sh_polz` int(11) DEFAULT 0 COMMENT 'Шифр пользователя, сделавшего последнее изменение',
  `dt_reg` datetime DEFAULT NULL COMMENT 'Дата ввода записи',
  `dt_izm` datetime DEFAULT NULL COMMENT 'Дата корректировки'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Водители';";

		if(!mysqli_query($link, $sql)) {
			$this->message_error = 'Ошибка при создании таблицы drivers!';
			return false;
		}

		$sql = "CREATE TABLE IF NOT EXISTS `drivers_card` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'Уникальный ID',
  `id_driver` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'ID водителя',
  `number_card` varchar(30) NOT NULL COMMENT 'Номер карты водителя',
  `firma_card` int(11) NOT NULL DEFAULT 0 COMMENT 'Кто выдал. Спр. ',
  `date_start_card` date DEFAULT NULL COMMENT 'Дата выдачи',
  `date_end_card` date DEFAULT NULL COMMENT 'Дата окончания',
  `path_to_file` varchar(100) DEFAULT NULL COMMENT 'Путь к файлу',
  `file_extension` varchar(50) DEFAULT NULL COMMENT 'Расширение файла',
  `sh_polz` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Шифр пользователя',
  `ibd_arx` int(11) NOT NULL DEFAULT 1 COMMENT 'Архив',
  `dt_reg` datetime DEFAULT NULL COMMENT 'Дата ввода',
  `dt_izm` datetime DEFAULT NULL COMMENT 'Дата корректировки'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Карта водителя';";

		if(!mysqli_query($link, $sql)) {
			$this->message_error = 'Ошибка при создании таблицы drivers_card!';
			return false;
		}

		$sql = "CREATE TABLE IF NOT EXISTS `drivers_document` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'Уникальный номер ВУ',
  `doc_s` varchar(4) NOT NULL COMMENT 'Серия водительского удостоверения',
  `doc_n` varchar(6) NOT NULL COMMENT 'Номер водительского удостоверения',
  `doc_date` date NOT NULL COMMENT 'Дата выдачи водительского удостоверения',
  `doc_end_date` date NOT NULL COMMENT 'Дата окончания водительского удостоверения',
  `ibd_arx` int(10) UNSIGNED NOT NULL DEFAULT 1 COMMENT 'Актуальность записи: 1 - актуальна, 2 - нет',
  `id_driver` int(10) UNSIGNED NOT NULL COMMENT 'ID водителя',
  `c_a` int(1) NOT NULL DEFAULT 0 COMMENT 'Категория автомобильных прав',
  `c_a1` int(1) NOT NULL DEFAULT 0 COMMENT 'Категория автомобильных прав',
  `c_b` int(1) NOT NULL DEFAULT 0 COMMENT 'Категория автомобильных прав',
  `c_b1` int(1) NOT NULL DEFAULT 0 COMMENT 'Категория автомобильных прав',
  `c_c` int(1) NOT NULL DEFAULT 0 COMMENT 'Категория автомобильных прав',
  `c_c1` int(1) NOT NULL DEFAULT 0 COMMENT 'Категория автомобильных прав',
  `c_d` int(1) NOT NULL DEFAULT 0 COMMENT 'Категория автомобильных прав',
  `c_d1` int(1) NOT NULL DEFAULT 0 COMMENT 'Категория автомобильных прав',
  `c_be` int(1) NOT NULL DEFAULT 0 COMMENT 'Категория автомобильных прав',
  `c_ce` int(1) NOT NULL DEFAULT 0 COMMENT 'Категория автомобильных прав',
  `c_c1e` int(1) NOT NULL DEFAULT 0 COMMENT 'Категория автомобильных прав',
  `c_de` int(1) NOT NULL DEFAULT 0 COMMENT 'Категория автомобильных прав',
  `c_d1e` int(1) NOT NULL DEFAULT 0 COMMENT 'Категория автомобильных прав',
  `c_m` int(1) NOT NULL DEFAULT 0 COMMENT 'Категория автомобильных прав',
  `c_tm` int(1) NOT NULL DEFAULT 0 COMMENT 'Категория автомобильных прав',
  `c_tb` int(1) NOT NULL DEFAULT 0 COMMENT 'Категория автомобильных прав',
  `path_to_file` varchar(500) DEFAULT NULL COMMENT 'Путь к файлу',
  `file_extension` varchar(10) DEFAULT NULL COMMENT 'Расширение файла',
  `sh_polz` int(11) NOT NULL DEFAULT 0 COMMENT 'Шифр пользователя',
  `dt_reg` datetime DEFAULT NULL COMMENT 'Дата ввода',
  `dt_izm` datetime DEFAULT NULL COMMENT 'Дата редактирования'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Таблица водительских удостоверений';";

		if(!mysqli_query($link, $sql)) {
			$this->message_error = 'Ошибка при создании таблицы drivers_document!';
			return false;
		}

		$sql = "CREATE TABLE IF NOT EXISTS `drivers_document_cran` (
  `id` int(10) UNSIGNED NOT NULL,
  `id_driver` int(11) NOT NULL DEFAULT 0,
  `date_document` date DEFAULT NULL,
  `number_document` varchar(10) NOT NULL,
  `education_institute` int(11) DEFAULT NULL COMMENT 'Место обучения. Спр. 33',
  `qualification` int(11) DEFAULT NULL COMMENT 'Квалификация. Спр.32',
  `path_to_file` varchar(100) NOT NULL,
  `file_extension` varchar(50) NOT NULL,
  `sh_polz` int(11) NOT NULL DEFAULT 0,
  `ibd_arx` int(11) NOT NULL DEFAULT 1,
  `dt_reg` datetime DEFAULT NULL,
  `dt_izm` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Таблица со списком удостоверений на краны';";

		if(!mysqli_query($link, $sql)) {
			$this->message_error = 'Ошибка при создании таблицы drivers_document_cran!';
			return false;
		}

		$sql = "CREATE TABLE IF NOT EXISTS `drivers_document_tractor` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'Уникальный ID',
  `doc_s` varchar(4) NOT NULL COMMENT 'Серия документа',
  `doc_n` varchar(6) NOT NULL COMMENT 'Номер документа',
  `doc_date` date DEFAULT NULL COMMENT 'Дата выдачи',
  `doc_end_date` date DEFAULT NULL COMMENT 'Дата окончания',
  `id_driver` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'ID драйвер',
  `c_a1` int(11) NOT NULL DEFAULT 0 COMMENT 'Категория A1',
  `c_a2` int(11) NOT NULL DEFAULT 0 COMMENT 'Категория A2',
  `c_a3` int(11) NOT NULL DEFAULT 0 COMMENT 'Категория A3',
  `c_a4` int(11) DEFAULT 0 COMMENT 'Категория A4',
  `c_b` int(11) DEFAULT 0 COMMENT 'Категория B',
  `c_c` int(11) NOT NULL DEFAULT 0 COMMENT 'Категория C',
  `c_d` int(11) NOT NULL DEFAULT 0 COMMENT 'Категория D',
  `c_e` int(11) NOT NULL DEFAULT 0 COMMENT 'Категория E',
  `c_f` int(11) NOT NULL DEFAULT 0 COMMENT 'Категория F',
  `path_to_file` varchar(500) DEFAULT NULL COMMENT 'Путь к файлу',
  `file_extension` varchar(10) DEFAULT NULL COMMENT 'Расширение файла',
  `sh_polz` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Шифр пользователя',
  `dt_reg` datetime DEFAULT NULL COMMENT 'Дата ввода',
  `dt_izm` datetime DEFAULT NULL COMMENT 'Дата изменения',
  `ibd_arx` int(10) UNSIGNED NOT NULL DEFAULT 1 COMMENT 'Архив'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Удостоверение тракториста';";

		if(!mysqli_query($link, $sql)) {
			$this->message_error = 'Ошибка при создании таблицы drivers_document_tractor!';
			return false;
		}

		$sql = "CREATE TABLE IF NOT EXISTS `drivers_dopog` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'Уникальный ID',
  `id_driver` int(10) UNSIGNED NOT NULL COMMENT 'ID водителя',
  `number_dopog` varchar(50) NOT NULL COMMENT 'Номер ДОПОГ',
  `date_start_dopog` date DEFAULT NULL COMMENT 'Дата выдачи',
  `date_end_dopog` date DEFAULT NULL COMMENT 'Дата окончания',
  `path_to_file` varchar(100) DEFAULT NULL,
  `file_extension` varchar(50) DEFAULT NULL,
  `ibd_arx` int(11) NOT NULL DEFAULT 1 COMMENT 'Архив',
  `sh_polz` int(11) NOT NULL DEFAULT 0 COMMENT 'Шифр пользователя',
  `dt_reg` datetime DEFAULT NULL COMMENT 'Дата ввода',
  `dt_izm` datetime DEFAULT NULL COMMENT 'Дата корректировки'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Свидетельство ДОПОГ для водителя';";

		if(!mysqli_query($link, $sql)) {
			$this->message_error = 'Ошибка при создании таблицы drivers_dopog!';
			return false;
		}

		$sql = "CREATE TABLE IF NOT EXISTS `dtp` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'Уникальный ID',
  `id_car` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'ID ТС',
  `id_driver` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'ID водителя',
  `place_committing` varchar(500) NOT NULL COMMENT 'Место ДТП',
  `date_committing` date NOT NULL COMMENT 'Дата ДТП',
  `time_committing` varchar(5) DEFAULT NULL COMMENT 'Время ДТП',
  `comment_committing` varchar(4000) DEFAULT NULL COMMENT 'Описание ДТП',
  `recovery_committing` varchar(4000) DEFAULT NULL COMMENT 'Восстановление после ремонта',
  `sum_committing` int(10) UNSIGNED DEFAULT NULL COMMENT 'Сумма ущерба',
  `offender` int(11) NOT NULL DEFAULT 0 COMMENT 'Виновен или нет: 0 - нет, 1 - виновен',
  `date_recovery_cars` date DEFAULT NULL COMMENT 'Дата восстановления ТС',
  `sh_polz` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Шифр пользователя',
  `dt_reg` datetime DEFAULT NULL COMMENT 'Дата ввода записи',
  `dt_izm` datetime DEFAULT NULL COMMENT 'Дата корректировки записи'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='ДТП с участием ТС и водителей';";

		if(!mysqli_query($link, $sql)) {
			$this->message_error = 'Ошибка при создании таблицы dtp!';
			return false;
		}

		$sql = "CREATE TABLE IF NOT EXISTS `files` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'Уникальный номер файла',
  `id_object` int(10) UNSIGNED NOT NULL COMMENT 'ID объекта (привязка к подсистеме)',
  `category_file` int(10) UNSIGNED NOT NULL COMMENT 'Категория файла для точности поиска',
  `path_to_file` varchar(1000) NOT NULL COMMENT 'Путь к файлу с корня сайта',
  `file_extension` varchar(10) NOT NULL COMMENT 'Расширение файла для отрисовки пиктограммы',
  `dt_upload` datetime NOT NULL COMMENT 'Дата загрузки файла',
  `sh_polz` int(10) UNSIGNED NOT NULL COMMENT 'Шифр пользователя загрузившего файл'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Таблица со списком файлов';";

		if(!mysqli_query($link, $sql)) {
			$this->message_error = 'Ошибка при создании таблицы files!';
			return false;
		}

		$sql = "CREATE TABLE IF NOT EXISTS `notice_events` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'Уникальный id',
  `notice_subsystem` varchar(100) DEFAULT NULL COMMENT 'Подсистема в текстовом формате',
  `notice_text` varchar(500) DEFAULT NULL COMMENT 'Сообщение',
  `notice_status` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Уровень критичности',
  `notice_object` varchar(100) DEFAULT NULL COMMENT 'Объект уведомления',
  `notice_id_object` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'ID объекта уведомления, для перехода по ссылке',
  `notice_table_object` varchar(20) DEFAULT NULL COMMENT 'Таблица оъекта для ссылки',
  `notice_code_subsystem` int(11) NOT NULL DEFAULT 0 COMMENT 'Код подсистемы',
  `notice_dostup` int(11) UNSIGNED NOT NULL DEFAULT 1 COMMENT 'Доступ к сведениям: 1 - открыт',
  `dt_reg` datetime DEFAULT NULL COMMENT 'Дата выявления'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Уведомление пользователей о событиях';";

		if(!mysqli_query($link, $sql)) {
			$this->message_error = 'Ошибка при создании таблицы notice_events!';
			return false;
		}

		$sql = "CREATE TABLE IF NOT EXISTS `osago` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'Уникальный номер полиса',
  `id_car` int(11) NOT NULL COMMENT 'ID транспортного средста',
  `s_osago` varchar(20) DEFAULT NULL COMMENT 'Серия ОСАГО (не используется)',
  `n_osago` varchar(50) NOT NULL COMMENT 'Серия и номер ОСАГО',
  `start_date_osago` date DEFAULT NULL COMMENT 'Дата с которой действует полис',
  `end_date_osago` date NOT NULL COMMENT 'Дата окончания действия полиса',
  `firma_osago` int(10) UNSIGNED NOT NULL COMMENT 'Код страховой компании',
  `date_osago` date DEFAULT NULL COMMENT 'Дата заключения договора (не используется)',
  `path_to_file` varchar(500) DEFAULT NULL COMMENT 'Путь к файлу',
  `file_extension` varchar(10) DEFAULT NULL COMMENT 'Расширение файла',
  `dt_reg` datetime NOT NULL COMMENT 'Дата ввода в базу',
  `dt_izm` datetime NOT NULL COMMENT 'Дата редактирования',
  `ibd_arx` int(11) UNSIGNED NOT NULL DEFAULT 1 COMMENT 'Актуальность полиса',
  `sh_polz` int(11) NOT NULL DEFAULT 0 COMMENT 'Шифр пользователя'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Таблица с полисами ОСАГО';";

		if(!mysqli_query($link, $sql)) {
			$this->message_error = 'Ошибка при создании таблицы osago!';
			return false;
		}

		$sql = "CREATE TABLE IF NOT EXISTS `pts` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'Уникальный номер записи',
  `id_car` int(10) UNSIGNED NOT NULL COMMENT 'ID ТС',
  `s_pts` varchar(20) NOT NULL COMMENT 'Серия ПТС',
  `n_pts` varchar(20) NOT NULL COMMENT 'Номер ПТС',
  `date_pts` date DEFAULT NULL COMMENT 'Дата выдачи ПТС',
  `type_ts_pts` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Тип ТС по ПТС (спр. 6)',
  `firma_pts` int(10) UNSIGNED NOT NULL COMMENT 'Фирма, выдавшая ПТС (спр. 10)',
  `path_to_file` varchar(500) DEFAULT NULL COMMENT 'Путь к файлу',
  `file_extension` varchar(10) DEFAULT NULL COMMENT 'Расширение файла',
  `ibd_arx` int(10) UNSIGNED NOT NULL DEFAULT 1 COMMENT 'Архив',
  `sh_polz` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Шифр пользователя',
  `dt_reg` datetime NOT NULL COMMENT 'Дата ввода',
  `dt_izm` datetime NOT NULL COMMENT 'Дата корректировки'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Паспорт тех. средства';";

		if(!mysqli_query($link, $sql)) {
			$this->message_error = 'Ошибка при создании таблицы pts!';
			return false;
		}

		$sql = "CREATE TABLE IF NOT EXISTS `role` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'Уникальный номер категории',
  `category` int(10) UNSIGNED NOT NULL COMMENT 'Номер категории',
  `text` varchar(50) NOT NULL COMMENT 'Название категории'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Таблица с категориями поьзователей';";

		if(!mysqli_query($link, $sql)) {
			$this->message_error = 'Ошибка при создании таблицы role!';
			return false;
		}

		$sql = "CREATE TABLE IF NOT EXISTS `s2i_klass` (
  `nomer` int(11) NOT NULL,
  `text` varchar(200) NOT NULL,
  `kod` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='справочники';";

		if(!mysqli_query($link, $sql)) {
			$this->message_error = 'Ошибка при создании таблицы s2i_klass!';
			return false;
		}

		$sql = "CREATE TABLE IF NOT EXISTS `speedometer` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'Уникальный номер показания',
  `id_car` int(10) UNSIGNED NOT NULL COMMENT 'ID транспортного средства',
  `id_speedometer` int(10) UNSIGNED NOT NULL COMMENT 'ID спидометра',
  `testimony_speedometer` double(10,2) UNSIGNED NOT NULL DEFAULT 0.00 COMMENT 'Показание спидометра',
  `reason_speedometer` int(10) UNSIGNED NOT NULL COMMENT 'Основание передачи показания',
  `date_speedometer` date NOT NULL COMMENT 'Дата передачи показания',
  `dt_reg` datetime NOT NULL COMMENT 'Дата ввода записи',
  `dt_izm` datetime NOT NULL COMMENT 'Дата изменения записи',
  `ibd_arx` int(10) UNSIGNED NOT NULL DEFAULT 1 COMMENT 'Актуальность записи: 1 - актуально, 0 - архив',
  `sh_polz` int(11) NOT NULL DEFAULT 0 COMMENT 'Шифр пользователя'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Таблица с показателями спидометра';";

		if(!mysqli_query($link, $sql)) {
			$this->message_error = 'Ошибка при создании таблицы speedometer!';
			return false;
		}

		$sql = "CREATE TABLE IF NOT EXISTS `speedometer_first_testimony` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'Уникальный ID',
  `id_car` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'ID транспортного средства',
  `id_speedometer` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Порядковый номер спидометра',
  `testimony` int(11) NOT NULL DEFAULT 0 COMMENT 'Показание спидометра',
  `ibd_arx` int(11) NOT NULL DEFAULT 1 COMMENT 'Актуальность записи: 0 - нет, 1 - да',
  `sh_polz` int(11) NOT NULL DEFAULT 0 COMMENT 'Шифр пользователя',
  `dt_reg` datetime DEFAULT NULL COMMENT 'Дата ввода',
  `dt_izm` datetime DEFAULT NULL COMMENT 'Дата изменения'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Показания для спидометра, которые нужно вычитать';";

		if(!mysqli_query($link, $sql)) {
			$this->message_error = 'Ошибка при создании таблицы speedometer_first_testimony!';
			return false;
		}

		$sql = "CREATE TABLE IF NOT EXISTS `spr_list` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'Уникальный код справочника',
  `nomer` int(10) UNSIGNED NOT NULL COMMENT 'Номер справочника в таблице s2i_klass',
  `text` varchar(100) NOT NULL COMMENT 'Название справочника',
  `type` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Доступен ли справочник для редактирования'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Описание всех справочников доступных в системе';";

		if(!mysqli_query($link, $sql)) {
			$this->message_error = 'Ошибка при создании таблицы spr_list!';
			return false;
		}

		$sql = "CREATE TABLE IF NOT EXISTS `technical_inspection` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'Уникальный номер техосмотра',
  `id_car` int(10) UNSIGNED NOT NULL COMMENT 'ID транспортного средства',
  `number_certificate` varchar(20) NOT NULL COMMENT 'Номер сертификата о прохождении техосмотра',
  `date_certificate` date NOT NULL COMMENT 'Дата прохождения техосмотра',
  `end_date_certificate` date NOT NULL COMMENT 'Дата окончания техосмотра',
  `firma_technical_inspection` int(10) UNSIGNED NOT NULL COMMENT 'Код фирмы где пройден техосмотр',
  `address_technical_inspection` varchar(150) NOT NULL COMMENT 'Адрес прохождения техосмотра',
  `car_mileage` double(10,2) NOT NULL DEFAULT 0.00 COMMENT 'Пробег ТС',
  `dt_reg` datetime NOT NULL COMMENT 'Дата ввода в базу',
  `dt_izm` datetime NOT NULL COMMENT 'Дата редактирования',
  `path_to_file` varchar(500) DEFAULT NULL COMMENT 'Путь к файлу',
  `file_extension` varchar(10) DEFAULT NULL COMMENT 'Расширение файла',
  `ibd_arx` int(10) UNSIGNED NOT NULL DEFAULT 1 COMMENT 'Актуальность записи',
  `sh_polz` int(11) NOT NULL DEFAULT 0 COMMENT 'Шифр пользователя'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Таблица со списком технических осмотров ТС';";

		if(!mysqli_query($link, $sql)) {
			$this->message_error = 'Ошибка при создании таблицы technical_inspection!';
			return false;
		}

		$sql = "CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'Системный номер пользователя',
  `fam` varchar(100) NOT NULL COMMENT 'Фамилия',
  `imj` varchar(100) NOT NULL COMMENT 'Имя',
  `otch` varchar(100) NOT NULL COMMENT 'Отчество',
  `login` varchar(20) NOT NULL COMMENT 'Логин пользователя',
  `slugba` int(10) UNSIGNED NOT NULL COMMENT 'Код службы или района к которому привязан пользователь',
  `passwd_hash` varchar(100) NOT NULL COMMENT 'Хэш пароля',
  `hash` varchar(64) DEFAULT NULL COMMENT 'Уникальный шифр пользователя',
  `role` int(10) UNSIGNED NOT NULL COMMENT 'Роль пользователя',
  `access` int(10) UNSIGNED NOT NULL COMMENT 'Открыт ли доступ пользователю',
  `block` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Флаг, заблокирован или нет пользователь',
  `notice_events` varchar(17) DEFAULT '0' COMMENT 'Имеются ли уведолмения для пользователя',
  `dt_reg` datetime DEFAULT NULL COMMENT 'Дата регистрации',
  `date_last_login` datetime DEFAULT NULL COMMENT 'Дата последнего входа в систему'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Таблица с пользователями';";

		if(!mysqli_query($link, $sql)) {
			$this->message_error = 'Ошибка при создании таблицы users!';
			return false;
		}

		return true;
	}

	private function create_trigger($link) {
		$sql = "DROP TRIGGER IF EXISTS trigger_insert_adm_offense;
				DROP TRIGGER IF EXISTS trigger_update_adm_offense;
		CREATE TRIGGER `trigger_insert_adm_offense` BEFORE INSERT ON `adm_offense` FOR EACH ROW BEGIN
	SET new.dt_reg = NOW();
    SET new.dt_izm = NOW();
END;
CREATE TRIGGER `trigger_update_adm_offense` BEFORE UPDATE ON `adm_offense` FOR EACH ROW BEGIN
SET new.dt_izm = NOW();
END;";

		if(!$this->multi_query($link, $sql, 'Ошибка при создании триггеров adm_offense!'))
			return false;

		$sql = "DROP TRIGGER IF EXISTS table_insert_cars;
				DROP TRIGGER IF EXISTS table_update_cars;
				DROP TRIGGER IF EXISTS trigger_delete_cars;
CREATE TRIGGER `table_insert_cars` BEFORE INSERT ON `cars` FOR EACH ROW BEGIN
SET new.dt_reg = NOW();
SET new.dt_izm = NOW();
END;
CREATE TRIGGER `table_update_cars` BEFORE UPDATE ON `cars` FOR EACH ROW BEGIN
	SET new.dt_izm = NOW();
    IF(new.gos_znak <> old.gos_znak) THEN
    	INSERT INTO cars_old_gos_znak (id_car, gos_znak, sh_polz) VALUES (new.id, old.gos_znak, new.sh_polz);
    END IF;
END;
CREATE TRIGGER `trigger_delete_cars` BEFORE DELETE ON `cars` FOR EACH ROW BEGIN
	DELETE FROM car_link_document WHERE id_car = old.id;
END;";

		if(!$this->multi_query($link, $sql, 'Ошибка при создании триггеров cars!'))
			return false;
		
		$sql = "DROP TRIGGER IF EXISTS trigger_insert_old_gos_znak;
		CREATE TRIGGER `trigger_insert_old_gos_znak` BEFORE INSERT ON `cars_old_gos_znak` FOR EACH ROW BEGIN
	SET new.dt_reg = NOW();
END;";

		if(!$this->multi_query($link, $sql, 'Ошибка при создании триггеров cars_old_gos_znak!'))
			return false;

		$sql = "DROP TRIGGER IF EXISTS table_insert_car_battery;
				DROP TRIGGER IF EXISTS table_update_car_battery;
		CREATE TRIGGER `table_insert_car_battery` BEFORE INSERT ON `car_battery` FOR EACH ROW BEGIN
	SET new.dt_izm = NOW();
    SET new.dt_reg = NOW();
END;
CREATE TRIGGER `table_update_car_battery` BEFORE UPDATE ON `car_battery` FOR EACH ROW BEGIN
	SET new.dt_izm = NOW();
END;";

		if(!$this->multi_query($link, $sql, 'Ошибка при создании триггеров car_battery!'))
			return false;

		$sql = "DROP TRIGGER IF EXISTS trigger_delete_car_document;
				DROP TRIGGER IF EXISTS trigger_insert_car_documents_new;
				DROP TRIGGER IF EXISTS trigger_update_car_documents_new;
		CREATE TRIGGER `trigger_delete_car_document` BEFORE DELETE ON `car_documents` FOR EACH ROW BEGIN
	DELETE FROM car_link_document WHERE id_document = old.id;
END;
CREATE TRIGGER `trigger_insert_car_documents_new` BEFORE INSERT ON `car_documents` FOR EACH ROW BEGIN
SET new.dt_reg = NOW();
SET new.dt_izm = NOW();
END;
CREATE TRIGGER `trigger_update_car_documents_new` BEFORE UPDATE ON `car_documents` FOR EACH ROW BEGIN
SET new.dt_izm = NOW();
END;";

		if(!$this->multi_query($link, $sql, 'Ошибка при создании триггеров car_documents!'))
			return false;

		$sql = "DROP TRIGGER IF EXISTS ins_car_dvr;
				DROP TRIGGER IF EXISTS upd_car_dvr;
CREATE TRIGGER `ins_car_dvr` BEFORE INSERT ON `car_dvr` FOR EACH ROW BEGIN
	SET new.dt_reg = NOW();
    SET new.dt_izm = NOW();
END;
CREATE TRIGGER `upd_car_dvr` BEFORE UPDATE ON `car_dvr` FOR EACH ROW BEGIN
	SET new.dt_izm = NOW();
END;";

		if(!$this->multi_query($link, $sql, 'Ошибка при создании триггеров car_dvr!'))
			return false;

		$sql = "DROP TRIGGER IF EXISTS insert_car_fire_extinguisher;
				DROP TRIGGER IF EXISTS update_car_fire_extingquisher;
CREATE TRIGGER `insert_car_fire_extinguisher` BEFORE INSERT ON `car_fire_extinguisher` FOR EACH ROW BEGIN
SET new.dt_reg = NOW();
SET new.dt_izm = NOW();
END;
CREATE TRIGGER `update_car_fire_extingquisher` BEFORE UPDATE ON `car_fire_extinguisher` FOR EACH ROW BEGIN
SET NEW.dt_izm = NOW();
END;";

		if(!$this->multi_query($link, $sql, 'Ошибка при создании триггеров car_fire_extinguisher!'))
			return false;

		$sql = "DROP TRIGGER IF EXISTS insert_car_first_aid_kid;
				DROP TRIGGER IF EXISTS update_car_first_aid_kid;
CREATE TRIGGER `insert_car_first_aid_kid` BEFORE INSERT ON `car_first_aid_kid` FOR EACH ROW BEGIN
SET new.dt_izm = NOW();
SET new.dt_reg = NOW();
END;
CREATE TRIGGER `update_car_first_aid_kid` BEFORE UPDATE ON `car_first_aid_kid` FOR EACH ROW BEGIN
SET NEW.dt_izm = NOW();
END;";

		if(!$this->multi_query($link, $sql, 'Ошибка при создании триггеров car_first_aid_kid!'))
			return false;

		$sql = "DROP TRIGGER IF EXISTS table_insert_car_for_driver;
				DROP TRIGGER IF EXISTS table_update_car_for_driver;
CREATE TRIGGER `table_insert_car_for_driver` BEFORE INSERT ON `car_for_driver` FOR EACH ROW BEGIN
SET NEW.dt_reg = NOW();
SET NEW.DT_IZM = NOW();
END;
CREATE TRIGGER `table_update_car_for_driver` BEFORE UPDATE ON `car_for_driver` FOR EACH ROW BEGIN
SET NEW.dt_izm = NOW();
END;";

		if(!$this->multi_query($link, $sql, 'Ошибка при создании триггеров car_for_driver!'))
			return false;

		$sql = "DROP TRIGGER IF EXISTS trigger_insert_car_link_doc;
				DROP TRIGGER IF EXISTS trigger_update_car_link_doc;
CREATE TRIGGER `trigger_insert_car_link_doc` BEFORE INSERT ON `car_link_document` FOR EACH ROW BEGIN
SET new.dt_reg = NOW();
SET new.dt_izm = NOW();
END;
CREATE TRIGGER `trigger_update_car_link_doc` BEFORE UPDATE ON `car_link_document` FOR EACH ROW BEGIN
	SET new.dt_izm = NOW();
END;";

		if(!$this->multi_query($link, $sql, 'Ошибка при создании триггеров car_link_document!'))
			return false;

		$sql = "DROP TRIGGER IF EXISTS insert_table_car_repair;
				DROP TRIGGER IF EXISTS update_table_car_repair;
CREATE TRIGGER `insert_table_car_repair` BEFORE INSERT ON `car_repair` FOR EACH ROW BEGIN
SET new.dt_reg=now();
SET new.dt_izm=now();
CALL add_testimony_speedometer(1, new.id_car, new.car_mileage, 0, new.date_start_repair, new.sh_polz, 1);
END;
CREATE TRIGGER `update_table_car_repair` BEFORE UPDATE ON `car_repair` FOR EACH ROW BEGIN
SET new.dt_izm=now();
     IF(new.car_mileage <> old.car_mileage) THEN
    	 CALL add_testimony_speedometer(2, new.id_car, new.car_mileage, old.car_mileage, new.date_start_repair, new.sh_polz, 1);
    END IF;
END;";

		if(!$this->multi_query($link, $sql, 'Ошибка при создании триггеров car_repair!'))
			return false;

		$sql = "DROP TRIGGER IF EXISTS ins_car_tachograph;
				DROP TRIGGER IF EXISTS upd_car_tachograph;
CREATE TRIGGER `ins_car_tachograph` BEFORE INSERT ON `car_tachograph` FOR EACH ROW BEGIN
SET new.dt_reg = NOW();
SET new.dt_izm = NOW();
END;
CREATE TRIGGER `upd_car_tachograph` BEFORE UPDATE ON `car_tachograph` FOR EACH ROW BEGIN
SET new.dt_izm = NOW();
END;";

		if(!$this->multi_query($link, $sql, 'Ошибка при создании триггеров car_tachograph!'))
			return false;

		$sql = "DROP TRIGGER IF EXISTS insert_car_warning_triangle;
				DROP TRIGGER IF EXISTS update_car_warning_triangle;
CREATE TRIGGER `insert_car_warning_triangle` BEFORE INSERT ON `car_warning_triangle` FOR EACH ROW BEGIN
SET new.dt_reg = NOW();
SET new.dt_izm = NOW();
END;
CREATE TRIGGER `update_car_warning_triangle` BEFORE UPDATE ON `car_warning_triangle` FOR EACH ROW BEGIN
SET NEW.dt_izm = NOW();
END;";

		if(!$this->multi_query($link, $sql, 'Ошибка при создании триггеров car_warning_triangle!'))
			return false;

		$sql = "DROP TRIGGER IF EXISTS table_insert_certificate_registration;
				DROP TRIGGER IF EXISTS table_update_certificate_registration;
CREATE TRIGGER `table_insert_certificate_registration` BEFORE INSERT ON `certificate_registration` FOR EACH ROW BEGIN
SET NEW.dt_reg = NOW();
SET NEW.dt_izm = NOW();
END;
CREATE TRIGGER `table_update_certificate_registration` BEFORE UPDATE ON `certificate_registration` FOR EACH ROW BEGIN
SET NEW.dt_izm = NOW();
END;";

		if(!$this->multi_query($link, $sql, 'Ошибка при создании триггеров certificate_registration!'))
			return false;

		$sql = "DROP TRIGGER IF EXISTS table_drivers_delete;
				DROP TRIGGER IF EXISTS table_drivers_insert;
				DROP TRIGGER IF EXISTS table_drivers_update;
CREATE TRIGGER `table_drivers_delete` BEFORE DELETE ON `drivers` FOR EACH ROW BEGIN
	DELETE FROM drivers_document WHERE id_driver = OLD.id;
    DELETE FROM car_for_driver WHERE id_driver = OLD.id;
   DELETE FROM drivers_document_cran WHERE id_driver = OLD.id;
    DELETE FROM drivers_document_tractor WHERE id_driver = OLD.id;
    DELETE FROM drivers_dopog WHERE id_driver = OLD.id;
    UPDATE dtp SET id_driver = 0 WHERE id_driver = OLD.id;
    UPDATE adm_offense SET id_driver = 0 WHERE id_driver = OLD.id;
END;
CREATE TRIGGER `table_drivers_insert` BEFORE INSERT ON `drivers` FOR EACH ROW BEGIN
	SET new.dt_reg = NOW();
SET new.dt_izm = NOW();
END;
CREATE TRIGGER `table_drivers_update` BEFORE UPDATE ON `drivers` FOR EACH ROW BEGIN
	SET new.dt_izm = now();
    IF(new.ibd_arx <> old.ibd_arx) THEN
    	UPDATE car_for_driver SET ibd_arx=new.ibd_arx WHERE id_driver = new.id;
    END IF;
END;";

		if(!$this->multi_query($link, $sql, 'Ошибка при создании триггеров drivers!'))
			return false;

		$sql = "DROP TRIGGER IF EXISTS ins_drivers_card;
				DROP TRIGGER IF EXISTS upd_drivers_card;
CREATE TRIGGER `ins_drivers_card` BEFORE INSERT ON `drivers_card` FOR EACH ROW BEGIN
SET new.dt_reg = NOW();
SET new.dt_izm = NOW();
END;
CREATE TRIGGER `upd_drivers_card` BEFORE UPDATE ON `drivers_card` FOR EACH ROW BEGIN
SET new.dt_izm = NOW();
END;";

		if(!$this->multi_query($link, $sql, 'Ошибка при создании триггеров drivers_card!'))
			return false;

		$sql = "DROP TRIGGER IF EXISTS table_insert_drivers_document;
				DROP TRIGGER IF EXISTS table_update_drivers_document;
CREATE TRIGGER `table_insert_drivers_document` BEFORE INSERT ON `drivers_document` FOR EACH ROW BEGIN
SET NEW.dt_reg = NOW();
SET NEW.dt_izm = NOW();
END;
CREATE TRIGGER `table_update_drivers_document` BEFORE UPDATE ON `drivers_document` FOR EACH ROW BEGIN
SET NEW.dt_izm = NOW();
END;";

		if(!$this->multi_query($link, $sql, 'Ошибка при создании триггеров drivers_document!'))
			return false;

		$sql = "DROP TRIGGER IF EXISTS trigger_insert_drivers_doc_2;
				DROP TRIGGER IF EXISTS trigger_update_drivers_doc_2;
CREATE TRIGGER `trigger_insert_drivers_doc_2` BEFORE INSERT ON `drivers_document_tractor` FOR EACH ROW BEGIN
SET new.dt_izm = NOW();
SET new.dt_reg = NOW();
END;
CREATE TRIGGER `trigger_update_drivers_doc_2` BEFORE UPDATE ON `drivers_document_tractor` FOR EACH ROW BEGIN
SET new.dt_izm = NOW();
END;";

		if(!$this->multi_query($link, $sql, 'Ошибка при создании триггеров drivers_document_tractor!'))
			return false;

		$sql = "DROP TRIGGER IF EXISTS trg_drivers_dopog_insert;
				DROP TRIGGER IF EXISTS trg_drivers_dopog_update;
CREATE TRIGGER `trg_drivers_dopog_insert` BEFORE INSERT ON `drivers_dopog` FOR EACH ROW BEGIN
SET new.dt_reg = NOW();
SET new.dt_izm = NOW();
END;
CREATE TRIGGER `trg_drivers_dopog_update` BEFORE UPDATE ON `drivers_dopog` FOR EACH ROW BEGIN
SET new.dt_izm = NOW();
END;";

		if(!$this->multi_query($link, $sql, 'Ошибка при создании триггеров drivers_dopog!'))
			return false;

		$sql = "DROP TRIGGER IF EXISTS table_insert_dtp;
				DROP TRIGGER IF EXISTS table_update_dtp;
CREATE TRIGGER `table_insert_dtp` BEFORE INSERT ON `dtp` FOR EACH ROW BEGIN
SET NEW.dt_reg = NOW();
SET NEW.dt_izm = NOW();
END;
CREATE TRIGGER `table_update_dtp` BEFORE UPDATE ON `dtp` FOR EACH ROW BEGIN
SET NEW.dt_izm = NOW();
END;";

		if(!$this->multi_query($link, $sql, 'Ошибка при создании триггеров dtp!'))
			return false;

		$sql = "DROP TRIGGER IF EXISTS insert_table_files;
CREATE TRIGGER `insert_table_files` BEFORE INSERT ON `files` FOR EACH ROW BEGIN
SET new.dt_upload=now();
END;";

		if(!$this->multi_query($link, $sql, 'Ошибка при создании триггеров files!'))
			return false;

		$sql = "DROP TRIGGER IF EXISTS insert_table_osago;
				DROP TRIGGER IF EXISTS update_table_osago;
CREATE TRIGGER `insert_table_osago` BEFORE INSERT ON `osago` FOR EACH ROW BEGIN
SET new.dt_reg=now();
SET new.dt_izm=now();
END;
CREATE TRIGGER `update_table_osago` BEFORE UPDATE ON `osago` FOR EACH ROW BEGIN
SET NEW.dt_izm=now();
END;";

		if(!$this->multi_query($link, $sql, 'Ошибка при создании триггеров osago!'))
			return false;

		$sql = "DROP TRIGGER IF EXISTS insert_table_pts;
				DROP TRIGGER IF EXISTS update_table_pts;
CREATE TRIGGER `insert_table_pts` BEFORE INSERT ON `pts` FOR EACH ROW BEGIN
SET NEW.dt_reg = NOW();
SET NEW.dt_izm = NOW();
END;
CREATE TRIGGER `update_table_pts` BEFORE UPDATE ON `pts` FOR EACH ROW BEGIN
SET NEW.dt_izm = NOW();
END;";

		if(!$this->multi_query($link, $sql, 'Ошибка при создании триггеров pts!'))
			return false;

		$sql = "DROP TRIGGER IF EXISTS table_insert_speedometer;
				DROP TRIGGER IF EXISTS table_update_speedometer;
CREATE TRIGGER `table_insert_speedometer` BEFORE INSERT ON `speedometer` FOR EACH ROW BEGIN
SET NEW.dt_reg = now();
SET NEW.dt_izm = now();
END;
CREATE TRIGGER `table_update_speedometer` BEFORE UPDATE ON `speedometer` FOR EACH ROW BEGIN
SET NEW.dt_izm = now();
END;";

		if(!$this->multi_query($link, $sql, 'Ошибка при создании триггеров speedometer!'))
			return false;

		$sql = "DROP TRIGGER IF EXISTS trigger_after_ins_first_speed;
				DROP TRIGGER IF EXISTS trigger_after_upd_first_speed;
				DROP TRIGGER IF EXISTS trigger_insert_first_speedometer;
				DROP TRIGGER IF EXISTS trigger_update_first_speedometer;
CREATE TRIGGER `trigger_after_ins_first_speed` AFTER INSERT ON `speedometer_first_testimony` FOR EACH ROW BEGIN
CALL move_to_archive(new.id_car, 3);
END;
CREATE TRIGGER `trigger_after_upd_first_speed` AFTER UPDATE ON `speedometer_first_testimony` FOR EACH ROW BEGIN
CALL move_to_archive(new.id_car, 3);
END;
CREATE TRIGGER `trigger_insert_first_speedometer` BEFORE INSERT ON `speedometer_first_testimony` FOR EACH ROW BEGIN
SET new.dt_reg = NOW();
SET new.dt_izm = NOW();
END;
CREATE TRIGGER `trigger_update_first_speedometer` BEFORE UPDATE ON `speedometer_first_testimony` FOR EACH ROW BEGIN
SET new.dt_izm = NOW();
END;";

		if(!$this->multi_query($link, $sql, 'Ошибка при создании триггеров speedometer_first_testimony!'))
			return false;

		$sql = "DROP TRIGGER IF EXISTS insert_table_technical_inspection;
				DROP TRIGGER IF EXISTS update_table_technical_inspection;
CREATE TRIGGER `insert_table_technical_inspection` BEFORE INSERT ON `technical_inspection` FOR EACH ROW BEGIN
SET new.dt_reg=now();
SET new.dt_izm=now();
CALL add_testimony_speedometer(1, new.id_car, new.car_mileage, 0, new.date_certificate, new.sh_polz, 2);
END;
CREATE TRIGGER `update_table_technical_inspection` BEFORE UPDATE ON `technical_inspection` FOR EACH ROW BEGIN
SET new.dt_izm=now();
IF(new.car_mileage <> old.car_mileage) THEN
    CALL add_testimony_speedometer(2, new.id_car, new.car_mileage, old.car_mileage, new.date_certificate, new.sh_polz, 2);
    END IF;
END;";

		if(!$this->multi_query($link, $sql, 'Ошибка при создании триггеров technical_inspection!'))
			return false;

		return true;
	}

	private function create_user($link) {
		$sql = "CREATE USER IF NOT EXISTS '" . $this->create_user . "'@'%' IDENTIFIED BY '" . $this->create_user_password . "';GRANT USAGE ON *.* TO '" . $this->create_user . "'@'%' REQUIRE NONE WITH MAX_QUERIES_PER_HOUR 0 MAX_CONNECTIONS_PER_HOUR 0 MAX_UPDATES_PER_HOUR 0 MAX_USER_CONNECTIONS 0;";

		if(!$this->multi_query($link, $sql, "Ошибка при создании пользователя!"))
			return false;

		$sql = "GRANT ALL PRIVILEGES ON `" . $this->create_db_name . "`.* TO '" . $this->create_user . "'@'%' WITH GRANT OPTION;";
		if(!mysqli_query($link, $sql)) {
			$this->message_error = "Ошибка при предоставлении прав доступа пользователю!";
			return false;
		}
		return true;
	}

	private function create_auto_increment($link) {
		$sql = "ALTER TABLE `adm_offense` MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID записи', AUTO_INCREMENT=1;";
		mysqli_query($link, $sql);

		$sql = "ALTER TABLE `cars` MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Уникальный номер ТС', AUTO_INCREMENT=1;";
		mysqli_query($link, $sql);

		$sql = "ALTER TABLE `cars_dopog` MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Уникальный ID', AUTO_INCREMENT=1;";
		mysqli_query($link, $sql);

		$sql = "ALTER TABLE `cars_old_gos_znak` MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Уникальный ID записи', AUTO_INCREMENT=1;";
		mysqli_query($link, $sql);

		$sql = "ALTER TABLE `cars_wheels` MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Уникальный ID', AUTO_INCREMENT=1;";
		mysqli_query($link, $sql);

		$sql = "ALTER TABLE `car_battery` MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID записи', AUTO_INCREMENT=1;";
		mysqli_query($link, $sql);

		$sql = "ALTER TABLE `car_calibration` MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Уникальный ID', AUTO_INCREMENT=1;";
		mysqli_query($link, $sql);

		$sql = "ALTER TABLE `car_documents` MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Уникальный ID', AUTO_INCREMENT=1;";
		mysqli_query($link, $sql);

		$sql = "ALTER TABLE `car_dvr` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Уникальный ID', AUTO_INCREMENT=1;";
		mysqli_query($link, $sql);

		$sql = "ALTER TABLE `car_for_driver` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Светчик', AUTO_INCREMENT=1;";
		mysqli_query($link, $sql);

		$sql = "ALTER TABLE `car_glonass` MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Уникальный ID', AUTO_INCREMENT=1;";
		mysqli_query($link, $sql);

		$sql = "ALTER TABLE `car_link_document` MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID записи', AUTO_INCREMENT=1;";
		mysqli_query($link, $sql);

		$sql = "ALTER TABLE `car_repair` MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Уникальный ID ремонта', AUTO_INCREMENT=1;";
		mysqli_query($link, $sql);

		$sql = "ALTER TABLE `car_tachograph` MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Уникальный ID записи', AUTO_INCREMENT=1;";
		mysqli_query($link, $sql);

		$sql = "ALTER TABLE `drivers` MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Уникальный номер водителя', AUTO_INCREMENT=1;";
		mysqli_query($link, $sql);

		$sql = "ALTER TABLE `drivers_card` MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Уникальный ID', AUTO_INCREMENT=1;";
		mysqli_query($link, $sql);

		$sql = "ALTER TABLE `drivers_document` MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Уникальный номер ВУ', AUTO_INCREMENT=1;";
		mysqli_query($link, $sql);

		$sql = "ALTER TABLE `drivers_document_cran` MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;";
		mysqli_query($link, $sql);

		$sql = "ALTER TABLE `drivers_document_tractor` MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT comment 'Уникальный ID', AUTO_INCREMENT=1;";
		mysqli_query($link, $sql);

		$sql = "ALTER TABLE `drivers_dopog` MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Уникальный ID', AUTO_INCREMENT=1;";
		mysqli_query($link, $sql);

		$sql = "ALTER TABLE `dtp` MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Уникальный ID', AUTO_INCREMENT=1;";
		mysqli_query($link, $sql);

		$sql = "ALTER TABLE `files` MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Уникальный номер файла', AUTO_INCREMENT=1;";
		mysqli_query($link, $sql);

		$sql = "ALTER TABLE `notice_events` MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Уникальный id', AUTO_INCREMENT=1;";
		mysqli_query($link, $sql);

		$sql = "ALTER TABLE `pts` MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Уникальный номер записи', AUTO_INCREMENT=1;";
		mysqli_query($link, $sql);

		$sql = "ALTER TABLE `speedometer` MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Уникальный номер показания', AUTO_INCREMENT=1;";
		mysqli_query($link, $sql);

		$sql = "ALTER TABLE `speedometer_first_testimony` MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Уникальный ID', AUTO_INCREMENT=1;";
		mysqli_query($link, $sql);

		$sql = "ALTER TABLE `spr_list` MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Уникальный код справочника', AUTO_INCREMENT=1;";
		mysqli_query($link, $sql);

		$sql = "ALTER TABLE `users` MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Системный номер пользователя', AUTO_INCREMENT=1;";
		mysqli_query($link, $sql);
	}

	private function create_index($link) {
		$sql = "ALTER TABLE `adm_offense` ADD PRIMARY KEY (`id`), ADD KEY `ind_drivers_adm` (`id_driver`,`id_car`,`ibd_arx`);";
		mysqli_query($link, $sql);

		$sql = "ALTER TABLE `cars` ADD PRIMARY KEY (`id`), ADD KEY `IND_CARS_VIN` (`vin`), ADD KEY `ibd_arx` (`ibd_arx`);";
		mysqli_query($link, $sql);

		$sql = "ALTER TABLE `cars_dopog` ADD PRIMARY KEY (`id`), ADD KEY `ind_cars_dopog` (`id_car`,`date_start_dopog`,`date_end_dopog`,`ibd_arx`);";
		mysqli_query($link, $sql);

		$sql = "ALTER TABLE `cars_old_gos_znak` ADD PRIMARY KEY (`id`), ADD KEY `ind_cars_old_gos_znak` (`id_car`);";
		mysqli_query($link, $sql);

		$sql = "ALTER TABLE `cars_wheels` ADD PRIMARY KEY (`id`), ADD KEY `ind_cars_wheels` (`id_car`,`ibd_arx`);";
		mysqli_query($link, $sql);

		$sql = "ALTER TABLE `car_battery` ADD PRIMARY KEY (`id`), ADD KEY `ind_car_battery` (`id_car`), ADD KEY `ibd_arx` (`ibd_arx`), ADD KEY `date_start` (`start_date`);";
		mysqli_query($link, $sql);

		$sql = "ALTER TABLE `car_calibration` ADD PRIMARY KEY (`id`), ADD KEY `ind_car_calibration` (`id_car`,`ibd_arx`);";
		mysqli_query($link, $sql);

		$sql = "ALTER TABLE `car_documents` ADD PRIMARY KEY (`id`), ADD KEY `ind_car_documents` (`type_car_document`,`date_car_document`);";
		mysqli_query($link, $sql);

		$sql = "ALTER TABLE `car_dvr` ADD PRIMARY KEY (`id`), ADD KEY `ind_car_dvr` (`id_car`,`ibd_arx`,`date_issued_dvr`) USING BTREE;";
		mysqli_query($link, $sql);

		$sql = "ALTER TABLE `car_fire_extinguisher` ADD PRIMARY KEY (`id`), ADD KEY `ind_fire_extinguisher` (`id_car`,`ibd_arx`);";
		mysqli_query($link, $sql);

		$sql = "ALTER TABLE `car_first_aid_kid` ADD PRIMARY KEY (`id`), ADD KEY `ind_car_first_aid_kid` (`id_car`,`ibd_arx`);";
		mysqli_query($link, $sql);

		$sql = "ALTER TABLE `car_for_driver` ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `driver_id_2` (`id_driver`,`car_id`,`ibd_arx`), ADD KEY `driver_id` (`id_driver`,`car_id`), ADD KEY `car_id` (`car_id`);";
		mysqli_query($link, $sql);

		$sql = "ALTER TABLE `car_glonass` ADD PRIMARY KEY (`id`), ADD KEY `ind_car_glonass` (`id_car`,`ibd_arx`,`date_glonass`) USING BTREE;";
		mysqli_query($link, $sql);

		$sql = "ALTER TABLE `car_link_document` ADD PRIMARY KEY (`id`), ADD KEY `ind_car_link_document` (`id_car`,`id_document`);";
		mysqli_query($link, $sql);

		$sql = "ALTER TABLE `car_repair` ADD PRIMARY KEY (`id`), ADD KEY `ind_car_repair_ibd_arx` (`ibd_arx`), ADD KEY `id_car` (`id_car`), ADD KEY `
		date_start_repair` (`date_start_repair`);";
		mysqli_query($link, $sql);

		$sql = "ALTER TABLE `car_repair_details` ADD PRIMARY KEY (`id`), ADD KEY `ind_car_repair_details` (`id_repair`,`id_goods`);";
		mysqli_query($link, $sql);

		$sql = "ALTER TABLE `car_tachograph` ADD PRIMARY KEY (`id`), ADD KEY `ind_car_tachograph` (`id_car`,`date_end_skzi`,`date_start_skzi`,`ibd_arx`);";
		mysqli_query($link, $sql);

		$sql = "ALTER TABLE `car_warning_triangle` ADD PRIMARY KEY (`id`), ADD KEY `ind_warning_triangle` (`id_car`,`ibd_arx`);";
		mysqli_query($link, $sql);

		$sql = "ALTER TABLE `certificate_registration` ADD PRIMARY KEY (`id`), ADD KEY `ind_certificate_registration` (`id_car`,`date_certificate_reg`,`ibd_arx`);";
		mysqli_query($link, $sql);

		$sql = "ALTER TABLE `drivers` ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `drivers_uniq` (`fam`,`imj`,`otch`,`dt_rojd`);";
		mysqli_query($link, $sql);

		$sql = "ALTER TABLE `drivers_card` ADD PRIMARY KEY (`id`), ADD KEY `ind_drivers_card` (`id_driver`,`ibd_arx`,`date_end_card`);";
		mysqli_query($link, $sql);

		$sql = "ALTER TABLE `drivers_document` ADD PRIMARY KEY (`id`), ADD KEY `IND_DRIVERS_DOCUMENTS` (`id_driver`,`ibd_arx`);";
		mysqli_query($link, $sql);

		$sql = "ALTER TABLE `drivers_document_cran` ADD PRIMARY KEY (`id`), ADD KEY `ind_drivers_doc_cran` (`id_driver`);";
		mysqli_query($link, $sql);

		$sql = "ALTER TABLE `drivers_document_tractor` ADD PRIMARY KEY (`id`), ADD KEY `ind_drivers_doc_tr` (`id_driver`,`ibd_arx`,`doc_end_date`);";
		mysqli_query($link, $sql);

		$sql = "ALTER TABLE `drivers_dopog` ADD PRIMARY KEY (`id`), ADD KEY `ind_drivers_dopog` (`id_driver`,`date_start_dopog`,`date_end_dopog`,`ibd_arx`);";
		mysqli_query($link, $sql);

		$sql = "ALTER TABLE `dtp` ADD PRIMARY KEY (`id`), ADD KEY `ind_dtp` (`id_car`,`id_driver`,`date_committing`,`offender`);";
		mysqli_query($link, $sql);

		$sql = "ALTER TABLE `files` ADD PRIMARY KEY (`id`), ADD KEY `ind_files` (`id_object`,`category_file`);";
		mysqli_query($link, $sql);

		$sql = "ALTER TABLE `notice_events` ADD PRIMARY KEY (`id`);";
		mysqli_query($link, $sql);

		$sql = "ALTER TABLE `osago` ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `IND_UNIQ_OSAGO` (`id_car`,`end_date_osago`), ADD KEY `IND_OSAGO` (`ibd_arx`);";
		mysqli_query($link, $sql);

		$sql = "ALTER TABLE `pts` ADD PRIMARY KEY (`id`), ADD KEY `pts_index` (`id_car`,`ibd_arx`,`date_pts`);";
		mysqli_query($link, $sql);

		$sql = "ALTER TABLE `role` ADD PRIMARY KEY (`id`), ADD KEY `IND_ROLE` (`category`);";
		mysqli_query($link, $sql);

		$sql = "ALTER TABLE `s2i_klass` ADD UNIQUE KEY `nomer_2` (`nomer`,`kod`), ADD KEY `nomer` (`nomer`);";
		mysqli_query($link, $sql);

		$sql = "ALTER TABLE `speedometer` ADD PRIMARY KEY (`id`), ADD KEY `ind_speedometer` (`ibd_arx`), ADD KEY `id_speedometer` (`id_speedometer`), ADD KEY `id_car` (`id_car`), ADD KEY `date_speedometer` (`date_speedometer`);";
		mysqli_query($link, $sql);

		$sql = "ALTER TABLE `speedometer_first_testimony` ADD PRIMARY KEY (`id`);";
		mysqli_query($link, $sql);

		$sql = "ALTER TABLE `spr_list` ADD PRIMARY KEY (`id`), ADD KEY `IND_SPR_LIST` (`nomer`);";
		mysqli_query($link, $sql);

		$sql = "ALTER TABLE `technical_inspection` ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `UNIQ_IND_TECHNICAL_SERTIFICATE` (`id_car`,`date_certificate`), ADD KEY `IND_TECHNICAL_SERTIFICATE` (`ibd_arx`,`end_date_certificate`);";
		mysqli_query($link, $sql);

		$sql = "ALTER TABLE `test` ADD PRIMARY KEY (`id`);";
		mysqli_query($link, $sql);

		$sql = "ALTER TABLE `users` ADD PRIMARY KEY (`id`);";
		mysqli_query($link, $sql);
	}

	/*
		Функция создания процедур
	*/
	private function create_procedure($link) {
		$sql = "CREATE DEFINER=`root`@`localhost` PROCEDURE `add_testimony_speedometer` (IN `ind` INT, IN `car` INT, IN `testimony` INT, IN `old_testimony` INT, IN `date_testimony` DATE, IN `polz` INT, IN `type_operation` INT)  NO SQL
BEGIN
DECLARE num_speed INT;
DECLARE reason INT;
DECLARE id_speed INT;

IF(type_operation = 1) THEN
	SELECT kod INTO reason FROM s2i_klass WHERE nomer=17 AND text LIKE '%РЕМОНТ%' LIMIT 1;
ELSE
	SELECT kod INTO reason FROM s2i_klass WHERE nomer=17 AND text LIKE '%ТЕХНИЧЕСКИЙ ОСМОТР%' LIMIT 1;
END IF;


IF (ind = 1) THEN
	SELECT num_speedometer INTO num_speed FROM cars WHERE id=car;
	INSERT INTO speedometer (id_car, id_speedometer, testimony_speedometer, reason_speedometer, date_speedometer, sh_polz) VALUES (car, num_speed, testimony, reason, date_testimony, polz);
END IF;


IF(ind = 2) THEN
	SELECT IFNULL(id, 0), IFNULL(id_speedometer, 0) INTO id_speed, num_speed FROM speedometer WHERE id_car=car AND testimony_speedometer=old_testimony LIMIT 1;

	IF(id_speed IS NULL) THEN
		SELECT num_speedometer INTO num_speed FROM cars WHERE id=car LIMIT 1;
		INSERT INTO speedometer (id_car, id_speedometer, testimony_speedometer, reason_speedometer, date_speedometer, sh_polz) VALUES (car, num_speed, testimony, reason, date_testimony, polz);
	ELSE
		UPDATE speedometer SET id_speedometer=num_speed, testimony_speedometer=testimony, reason_speedometer=reason, date_speedometer=date_testimony, sh_polz=polz WHERE id=id_speed;
	END IF;

END IF;

CALL move_to_archive(car, 3);
END;";
		mysqli_query($link, $sql);

		$sql = "CREATE DEFINER=`root`@`localhost` PROCEDURE `move_to_archive` (IN `id_item` INT, IN `ind` INT)  NO SQL
BEGIN
DECLARE dd DATE;
DECLARE tt INT;
DECLARE ttt DECIMAL(10,2);
DECLARE done INTEGER DEFAULT FALSE;

DECLARE cur CURSOR FOR SELECT id FROM drivers_permission_spec_signals WHERE (end_date_permission, category) IN (SELECT MAX(end_date_permission), category from drivers_permission_spec_signals WHERE id_driver = id_item GROUP BY category); 

DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

IF(ind = 1) THEN
	SELECT MAX(end_date_osago) INTO dd FROM osago WHERE id_car = id_item;
	UPDATE osago SET ibd_arx=1 WHERE id_car = id_item AND end_date_osago = dd;
	UPDATE osago SET ibd_arx=2 WHERE id_car = id_item AND end_date_osago < dd AND ibd_arx=1;
END IF;


IF(ind = 2) THEN
	SELECT MAX(date_certificate) INTO dd FROM technical_inspection  WHERE id_car = id_item;
	UPDATE technical_inspection  SET ibd_arx=1 WHERE id_car = id_item AND date_certificate = dd;
	UPDATE technical_inspection  SET ibd_arx=2 WHERE id_car = id_item AND date_certificate < dd AND ibd_arx=1;
END IF;


IF(ind = 3) THEN   
	SELECT IFNULL(SUM(x.dist), 0) INTO ttt FROM (SELECT MAX(testimony_speedometer - IFNULL(speedometer_first_testimony.testimony, 0)) as dist, speedometer.id_speedometer FROM speedometer
	LEFT JOIN speedometer_first_testimony ON speedometer_first_testimony.id_car=speedometer.id_car and speedometer_first_testimony.id_speedometer=speedometer.id_speedometer
	WHERE speedometer.id_car = id_item GROUP BY speedometer.id_speedometer) x;
	
    UPDATE cars SET mileage = ttt WHERE id = id_item;
    
    SELECT MAX(id_speedometer) INTO tt FROM speedometer WHERE id_car = id_item;
    SELECT MAX(date_speedometer) INTO dd FROM speedometer WHERE id_car = id_item AND id_speedometer = tt;
	SELECT MAX(testimony_speedometer) INTO ttt FROM speedometer WHERE id_car = id_item AND date_speedometer = dd;
    
	UPDATE speedometer SET ibd_arx=2 WHERE id_car = id_item AND ibd_arx=1;
	UPDATE speedometer SET ibd_arx=1 WHERE id_car = id_item AND date_speedometer = dd AND testimony_speedometer = ttt;
END IF;


IF(ind = 4) THEN
	SELECT MAX(doc_date) INTO dd FROM drivers_document WHERE id_driver = id_item;
	UPDATE drivers_document SET ibd_arx=1 WHERE id_driver = id_item AND doc_date = dd;
	UPDATE drivers_document SET ibd_arx=2 WHERE id_driver = id_item AND doc_date < dd AND ibd_arx=1;
END IF;


IF(ind = 44) THEN
	SELECT MAX(doc_date) INTO dd FROM drivers_document_tractor WHERE id_driver = id_item;
	UPDATE drivers_document_tractor SET ibd_arx=1 WHERE id_driver = id_item AND doc_date = dd;
	UPDATE drivers_document_tractor SET ibd_arx=2 WHERE id_driver = id_item AND doc_date < dd AND ibd_arx=1;
END IF;

IF(ind = 5) THEN
	SELECT MAX(date_pts) INTO dd FROM pts WHERE id_car = id_item;
	UPDATE pts SET ibd_arx=1 WHERE id_car = id_item AND date_pts = dd;
	UPDATE pts SET ibd_arx=2 WHERE id_car = id_item AND IFNULL(date_pts, '1900-01-01') < dd AND ibd_arx=1;
END IF;


IF(ind = 6) THEN
	SELECT MAX(date_certificate_reg) INTO dd FROM certificate_registration WHERE id_car = id_item;
	UPDATE certificate_registration SET ibd_arx=1 WHERE id_car = id_item AND date_certificate_reg = dd;
	UPDATE certificate_registration SET ibd_arx=2 WHERE id_car = id_item AND IFNULL(date_certificate_reg, '1900-01-01') < dd AND ibd_arx=1;
END IF;

IF(ind = 12) THEN
	SELECT MAX(end_date) INTO dd FROM car_first_aid_kid WHERE id_car = id_item;
	UPDATE car_first_aid_kid SET ibd_arx=1 WHERE id_car = id_item AND end_date = dd;
	UPDATE car_first_aid_kid SET ibd_arx=2 WHERE id_car = id_item AND end_date < dd AND ibd_arx=1;
END IF;


IF(ind = 122) THEN
	SELECT MAX(end_date) INTO dd FROM car_fire_extinguisher WHERE id_car = id_item;
	UPDATE car_fire_extinguisher SET ibd_arx=1 WHERE id_car = id_item AND end_date = dd;
	UPDATE car_fire_extinguisher SET ibd_arx=2 WHERE id_car = id_item AND end_date < dd AND ibd_arx=1;
END IF;


IF(ind = 1222) THEN
	SELECT MAX(issued_date) INTO dd FROM car_warning_triangle WHERE id_car = id_item;
	UPDATE car_warning_triangle SET ibd_arx=1 WHERE id_car = id_item AND issued_date = dd;
	UPDATE car_warning_triangle SET ibd_arx=2 WHERE id_car = id_item AND issued_date < dd AND ibd_arx=1;
END IF;

IF(ind = 12222) THEN
	SELECT MAX(start_date) INTO dd FROM car_battery WHERE id_car = id_item;
	UPDATE car_battery SET ibd_arx=1 WHERE id_car = id_item AND start_date = dd;
	UPDATE car_battery SET ibd_arx=2 WHERE id_car = id_item AND start_date < dd AND ibd_arx=1;
END IF;

IF(ind = 122222) THEN
	SELECT MAX(date_issued_dvr) INTO dd FROM car_dvr WHERE id_car = id_item;
	UPDATE car_dvr SET ibd_arx=1 WHERE id_car = id_item AND date_issued_dvr = dd;
	UPDATE car_dvr SET ibd_arx=2 WHERE id_car = id_item AND date_issued_dvr < dd AND ibd_arx=1;
END IF;

IF(ind = 15) THEN
	SELECT MAX(date_end_dopog) INTO dd FROM drivers_dopog WHERE id_car = id_item;
	UPDATE drivers_dopog SET ibd_arx=1 WHERE id_car = id_item AND date_end_dopog  = dd;
	UPDATE drivers_dopog SET ibd_arx=2 WHERE id_car = id_item AND date_end_dopog  < dd AND ibd_arx=1;
END IF;

IF(ind = 16) THEN
	SELECT MAX(date_end_dopog) INTO dd FROM cars_dopog WHERE id_car = id_item;
	UPDATE cars_dopog SET ibd_arx=1 WHERE id_car = id_item AND date_end_dopog  = dd;
	UPDATE cars_dopog SET ibd_arx=2 WHERE id_car = id_item AND date_end_dopog  < dd AND ibd_arx=1;
END IF;

IF(ind = 17) THEN
	SELECT MAX(date_next_calibration) INTO dd FROM car_calibration WHERE id_car = id_item;
	UPDATE car_calibration SET ibd_arx=1 WHERE id_car = id_item AND date_next_calibration = dd;
	UPDATE car_calibration SET ibd_arx=2 WHERE id_car = id_item AND date_next_calibration < dd AND ibd_arx=1;
END IF;

IF(ind = 18) THEN
	SELECT MAX(date_end_card) INTO dd FROM drivers_card WHERE id_car = id_item;
	UPDATE drivers_card SET ibd_arx=1 WHERE id_car = id_item AND date_end_card  = dd;
	UPDATE drivers_card SET ibd_arx=2 WHERE id_car = id_item AND date_end_card  < dd AND ibd_arx=1;
END IF;

IF(ind = 19) THEN
	SELECT MAX(date_end_skzi) INTO dd FROM car_tachograph WHERE id_car = id_item;
	UPDATE car_tachograph SET ibd_arx=1 WHERE id_car = id_item AND date_end_skzi = dd;
	UPDATE car_tachograph SET ibd_arx=2 WHERE id_car = id_item AND date_end_skzi < dd AND ibd_arx=1;
END IF;

IF(ind = 20) THEN
	SELECT MAX(date_glonass) INTO dd FROM car_glonass WHERE id_car = id_item;
	UPDATE car_glonass SET ibd_arx=1 WHERE id_car = id_item AND date_glonass = dd;
	UPDATE car_glonass SET ibd_arx=2 WHERE id_car = id_item AND date_glonass  < dd AND ibd_arx=1;
END IF;

END;";
		mysqli_query($link, $sql);

		$sql = "CREATE DEFINER=`root`@`localhost` PROCEDURE `get_notice_drivers` ()  NO SQL
BEGIN
DECLARE dd DATE;
DECLARE id INTEGER DEFAULT 0;
DECLARE fio VARCHAR(100);
DECLARE param1 VARCHAR(17);
DECLARE dostup INTEGER DEFAULT 0;
DECLARE done INTEGER DEFAULT FALSE;


DECLARE cur1 CURSOR FOR SELECT drivers_document.doc_end_date, drivers.id, CONCAT(drivers.fam, ' ', drivers.imj, ' ', drivers.otch), drivers.dostup FROM drivers_document
	INNER JOIN drivers ON drivers.id = drivers_document.id_driver AND drivers.ibd_arx=1
	WHERE drivers_document.ibd_arx = 1 AND drivers_document.doc_end_date < CURDATE();

DECLARE cur2 CURSOR FOR SELECT drivers_document.doc_end_date, drivers.id, CONCAT(drivers.fam, ' ', drivers.imj, ' ', drivers.otch), drivers.dostup FROM drivers_document
	INNER JOIN drivers ON drivers.id = drivers_document.id_driver AND drivers.ibd_arx=1
	WHERE drivers_document.ibd_arx = 1 AND (drivers_document.doc_end_date BETWEEN CURDATE() AND CURDATE() + INTERVAL 15 DAY);
	
DECLARE cur3 CURSOR FOR SELECT drivers_card.date_end_card, drivers.id, CONCAT(drivers.fam, ' ', drivers.imj, ' ', drivers.otch), drivers.dostup FROM drivers_card
	INNER JOIN drivers ON drivers.id = drivers_card.id_driver AND drivers.ibd_arx=1
	WHERE drivers_card.ibd_arx = 1 AND drivers_card.date_end_card < CURDATE();

DECLARE cur4 CURSOR FOR SELECT drivers_card.date_end_card , drivers.id, CONCAT(drivers.fam, ' ', drivers.imj, ' ', drivers.otch), drivers.dostup FROM drivers_card
	INNER JOIN drivers ON drivers.id = drivers_card.id_driver AND drivers.ibd_arx=1
	WHERE drivers_card.ibd_arx = 1 AND (drivers_card.date_end_card  BETWEEN CURDATE() AND CURDATE() + INTERVAL 15 DAY);

DECLARE cur5 CURSOR FOR SELECT drivers_document_tractor.doc_end_date, drivers.id, CONCAT(drivers.fam, ' ', drivers.imj, ' ', drivers.otch), drivers.dostup FROM drivers_document_tractor
	INNER JOIN drivers ON drivers.id = drivers_document_tractor.id_driver AND drivers.ibd_arx=1
	WHERE drivers_document_tractor.ibd_arx = 1 AND drivers_document_tractor.doc_end_date < CURDATE();

DECLARE cur6 CURSOR FOR SELECT drivers_document_tractor.doc_end_date  , drivers.id, CONCAT(drivers.fam, ' ', drivers.imj, ' ', drivers.otch), drivers.dostup FROM drivers_document_tractor
	INNER JOIN drivers ON drivers.id = drivers_document_tractor.id_driver AND drivers.ibd_arx=1
	WHERE drivers_document_tractor.ibd_arx = 1 AND (drivers_document_tractor.doc_end_date   BETWEEN CURDATE() AND CURDATE() + INTERVAL 15 DAY);

DECLARE cur7 CURSOR FOR SELECT drivers_dopog.date_end_dopog , drivers.id, CONCAT(drivers.fam, ' ', drivers.imj, ' ', drivers.otch), drivers.dostup FROM drivers_dopog
	INNER JOIN drivers ON drivers.id = drivers_dopog.id_driver AND drivers.ibd_arx=1
	WHERE drivers_dopog.ibd_arx = 1 AND drivers_dopog.date_end_dopog  < CURDATE();

DECLARE cur8 CURSOR FOR SELECT drivers_dopog.date_end_dopog   , drivers.id, CONCAT(drivers.fam, ' ', drivers.imj, ' ', drivers.otch), drivers.dostup FROM drivers_dopog
	INNER JOIN drivers ON drivers.id = drivers_dopog.id_driver AND drivers.ibd_arx=1
	WHERE drivers_dopog.ibd_arx = 1 AND (drivers_dopog.date_end_dopog    BETWEEN CURDATE() AND CURDATE() + INTERVAL 15 DAY);

DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
	
OPEN cur1;
read_loop: LOOP
	FETCH cur1 INTO dd, id, fio, dostup;
    
    IF done THEN 
    	LEAVE read_loop;
     END IF;
    
    INSERT INTO notice_events (notice_subsystem, notice_text, notice_status, notice_object, notice_id_object, notice_table_object, notice_dostup, notice_code_subsystem, dt_reg)
		VALUES
		('ВОДИТЕЛИ. ВОДИТЕЛЬСКИЕ УДОСТОВЕРЕНИЯ.', CONCAT('истек срок водительского удостоверения ', DATE_FORMAT(dd, '%d.%m.%Y')), 1, fio, id, 'driver', dostup, 1, NOW());
    
END LOOP;
CLOSE cur1;

SET done = FALSE;

OPEN cur2;
read_loop: LOOP
	FETCH cur2 INTO dd, id, fio, dostup;
	
	IF done THEN 
    	LEAVE read_loop;
     END IF;
    
    INSERT INTO notice_events (notice_subsystem, notice_text, notice_status, notice_object, notice_id_object, notice_table_object, notice_dostup, notice_code_subsystem, dt_reg)
		VALUES
		('ВОДИТЕЛИ. ВОДИТЕЛЬСКИЕ УДОСТОВЕРЕНИЯ.', CONCAT('истекает срок водительского удостоверения ', DATE_FORMAT(dd, '%d.%m.%Y')), 2, fio, id, 'driver', dostup, 1, NOW());
    
END LOOP;
CLOSE cur2;

SET done = FALSE;

OPEN cur3;
read_loop: LOOP
	FETCH cur3 INTO dd, id, fio, dostup;
    
    IF done THEN 
    	LEAVE read_loop;
     END IF;
    
    INSERT INTO notice_events (notice_subsystem, notice_text, notice_status, notice_object, notice_id_object, notice_table_object, notice_dostup, notice_code_subsystem, dt_reg)
		VALUES
		('ВОДИТЕЛИ. КАРТА ВОДИТЕЛЯ.', CONCAT('истек срок карты водителя ', DATE_FORMAT(dd, '%d.%m.%Y')), 1, fio, id, 'driver', dostup, 2, NOW());
    
END LOOP;
CLOSE cur3;

SET done = FALSE;

OPEN cur4;
read_loop: LOOP
	FETCH cur4 INTO dd, id, fio, dostup;
	
	IF done THEN 
    	LEAVE read_loop;
     END IF;
    
    INSERT INTO notice_events (notice_subsystem, notice_text, notice_status, notice_object, notice_id_object, notice_table_object, notice_dostup, notice_code_subsystem, dt_reg)
		VALUES
		('ВОДИТЕЛИ. КАРТА ВОДИТЕЛЯ.', CONCAT('истекает срок карты водителя ', DATE_FORMAT(dd, '%d.%m.%Y')), 2, fio, id, 'driver', dostup, 2, NOW());
    
END LOOP;
CLOSE cur4;

SET done = FALSE;

OPEN cur5;
read_loop: LOOP
	FETCH cur5 INTO dd, id, fio, dostup;
    
    IF done THEN 
    	LEAVE read_loop;
     END IF;
    
    INSERT INTO notice_events (notice_subsystem, notice_text, notice_status, notice_object, notice_id_object, notice_table_object, notice_dostup, notice_code_subsystem, dt_reg)
		VALUES
		('ВОДИТЕЛИ. УДОСТОВЕРЕНИЕ ТРАКТОРИСТА-МАШИНИСТА.', CONCAT('истек срок удостоверения тракториста-машиниста ', DATE_FORMAT(dd, '%d.%m.%Y')), 1, fio, id, 'driver', dostup, 3, NOW());
    
END LOOP;
CLOSE cur5;

SET done = FALSE;

OPEN cur6;
read_loop: LOOP
	FETCH cur6 INTO dd, id, fio, dostup;
	
	IF done THEN 
    	LEAVE read_loop;
     END IF;
    
    INSERT INTO notice_events (notice_subsystem, notice_text, notice_status, notice_object, notice_id_object, notice_table_object, notice_dostup, notice_code_subsystem, dt_reg)
		VALUES
		('ВОДИТЕЛИ. УДОСТОВЕРЕНИЕ ТРАКТОРИСТА-МАШИНИСТА.', CONCAT('истекает срок удостоверения тракториста-машиниста ', DATE_FORMAT(dd, '%d.%m.%Y')), 2, fio, id, 'driver', dostup, 3, NOW());
    
END LOOP;
CLOSE cur6;

SET done = FALSE;

OPEN cur7;
read_loop: LOOP
	FETCH cur7 INTO dd, id, fio, dostup;
    
    IF done THEN 
    	LEAVE read_loop;
     END IF;
    
    INSERT INTO notice_events (notice_subsystem, notice_text, notice_status, notice_object, notice_id_object, notice_table_object, notice_dostup, notice_code_subsystem, dt_reg)
		VALUES
		('ВОДИТЕЛИ. УДОСТОВЕРЕНИЕ ДОПОГ.', CONCAT('истек срок удостоверения ДОПОГ ', DATE_FORMAT(dd, '%d.%m.%Y')), 1, fio, id, 'driver', dostup, 4, NOW());
    
END LOOP;
CLOSE cur7;

SET done = FALSE;

OPEN cur8;
read_loop: LOOP
	FETCH cur8 INTO dd, id, fio, dostup;
	
	IF done THEN 
    	LEAVE read_loop;
     END IF;
    
    INSERT INTO notice_events (notice_subsystem, notice_text, notice_status, notice_object, notice_id_object, notice_table_object, notice_dostup, notice_code_subsystem, dt_reg)
		VALUES
		('ВОДИТЕЛИ. УДОСТОВЕРЕНИЕ ДОПОГ.', CONCAT('истекает срок удостоверения ДОПОГ ', DATE_FORMAT(dd, '%d.%m.%Y')), 2, fio, id, 'driver', dostup, 4, NOW());
    
END LOOP;
CLOSE cur8;

END;";
		mysqli_query($link, $sql);

		$sql = "CREATE DEFINER=`root`@`localhost` PROCEDURE `get_notice_cars` ()  NO SQL
BEGIN
DECLARE dd DATE;
DECLARE id INTEGER DEFAULT 0;
DECLARE fio VARCHAR(100);
DECLARE param1 VARCHAR(17);
DECLARE kodrai INTEGER DEFAULT 0;
DECLARE slugba INTEGER DEFAULT 0;
DECLARE dostup INTEGER DEFAULT 0;
DECLARE done INTEGER DEFAULT FALSE;

DECLARE cur1 CURSOR FOR SELECT osago.end_date_osago, cars.id, CONCAT(x1.text, ' ', x2.text, ' г.р.з. ', cars.gos_znak), cars.dostup FROM osago 
	INNER JOIN cars ON cars.id=osago.id_car AND cars.ibd_arx=1 AND cars.exception_notice_events = 0
	LEFT JOIN s2i_klass x1 ON x1.kod=cars.marka AND x1.nomer=3
	LEFT JOIN s2i_klass x2 ON x2.kod=cars.model AND x2.nomer=4
	WHERE osago.ibd_arx=1 AND osago.end_date_osago < CURDATE();
	
DECLARE cur2 CURSOR FOR SELECT osago.end_date_osago, cars.id, CONCAT(x1.text, ' ', x2.text, ' г.р.з. ', cars.gos_znak), cars.dostup FROM osago 
	INNER JOIN cars ON cars.id=osago.id_car  AND cars.ibd_arx=1 AND cars.exception_notice_events = 0
	LEFT JOIN s2i_klass x1 ON x1.kod=cars.marka AND x1.nomer=3
	LEFT JOIN s2i_klass x2 ON x2.kod=cars.model AND x2.nomer=4
	WHERE osago.ibd_arx=1 AND (osago.end_date_osago BETWEEN CURDATE() AND CURDATE() + INTERVAL 15 DAY);

DECLARE cur3 CURSOR FOR SELECT technical_inspection.end_date_certificate, cars.id, CONCAT(x1.text, ' ', x2.text, ' г.р.з. ', cars.gos_znak), cars.dostup FROM technical_inspection 
	INNER JOIN cars ON cars.id=technical_inspection.id_car AND cars.ibd_arx=1 AND cars.exception_notice_events = 0
	LEFT JOIN s2i_klass x1 ON x1.kod=cars.marka AND x1.nomer=3
	LEFT JOIN s2i_klass x2 ON x2.kod=cars.model AND x2.nomer=4
	WHERE technical_inspection.ibd_arx=1 AND technical_inspection.end_date_certificate < CURDATE();

DECLARE cur4 CURSOR FOR SELECT technical_inspection.end_date_certificate, cars.id, CONCAT(x1.text, ' ', x2.text, ' г.р.з. ', cars.gos_znak), cars.dostup FROM technical_inspection 
	INNER JOIN cars ON cars.id=technical_inspection.id_car AND cars.ibd_arx=1 AND cars.exception_notice_events = 0
	LEFT JOIN s2i_klass x1 ON x1.kod=cars.marka AND x1.nomer=3
	LEFT JOIN s2i_klass x2 ON x2.kod=cars.model AND x2.nomer=4
	WHERE technical_inspection.ibd_arx=1 AND (technical_inspection.end_date_certificate BETWEEN CURDATE() AND CURDATE() + INTERVAL 15 DAY);

DECLARE cur5 CURSOR FOR SELECT car_first_aid_kid.end_date, cars.id, CONCAT(x1.text, ' ', x2.text, ' г.р.з. ', cars.gos_znak), cars.dostup
	FROM car_first_aid_kid 
	INNER JOIN cars ON cars.id=car_first_aid_kid.id_car AND cars.ibd_arx=1 AND cars.exception_notice_events = 0
	LEFT JOIN s2i_klass x1 ON x1.kod=cars.marka AND x1.nomer=3
	LEFT JOIN s2i_klass x2 ON x2.kod=cars.model AND x2.nomer=4
	WHERE car_first_aid_kid.ibd_arx=1 AND car_first_aid_kid.end_date < CURDATE();

DECLARE cur6 CURSOR FOR SELECT car_first_aid_kid.end_date, cars.id, CONCAT(x1.text, ' ', x2.text, ' г.р.з. ', cars.gos_znak), cars.dostup
	FROM car_first_aid_kid 
	INNER JOIN cars ON cars.id=car_first_aid_kid.id_car AND cars.ibd_arx=1 AND cars.exception_notice_events = 0
	LEFT JOIN s2i_klass x1 ON x1.kod=cars.marka AND x1.nomer=3
	LEFT JOIN s2i_klass x2 ON x2.kod=cars.model AND x2.nomer=4
	WHERE car_first_aid_kid.ibd_arx=1 AND (car_first_aid_kid.end_date BETWEEN CURDATE() AND CURDATE() + INTERVAL 15 DAY);

DECLARE cur7 CURSOR FOR SELECT car_fire_extinguisher.end_date, cars.id, CONCAT(x1.text, ' ', x2.text, ' г.р.з. ', cars.gos_znak), cars.dostup
	FROM car_fire_extinguisher 
	INNER JOIN cars ON cars.id=car_fire_extinguisher.id_car AND cars.ibd_arx=1 AND cars.exception_notice_events = 0
	LEFT JOIN s2i_klass x1 ON x1.kod=cars.marka AND x1.nomer=3
	LEFT JOIN s2i_klass x2 ON x2.kod=cars.model AND x2.nomer=4
	WHERE car_fire_extinguisher.ibd_arx=1 AND car_fire_extinguisher.end_date < CURDATE();

DECLARE cur8 CURSOR FOR SELECT car_fire_extinguisher.end_date, cars.id, CONCAT(x1.text, ' ', x2.text, ' г.р.з. ', cars.gos_znak), cars.dostup
	FROM car_fire_extinguisher 
	INNER JOIN cars ON cars.id=car_fire_extinguisher.id_car AND cars.ibd_arx=1 AND cars.exception_notice_events = 0
	LEFT JOIN s2i_klass x1 ON x1.kod=cars.marka AND x1.nomer=3
	LEFT JOIN s2i_klass x2 ON x2.kod=cars.model AND x2.nomer=4
	WHERE car_fire_extinguisher.ibd_arx=1 AND (car_fire_extinguisher.end_date BETWEEN CURDATE() AND CURDATE() + INTERVAL 15 DAY);
	
DECLARE cur9 CURSOR FOR SELECT c.date_start_repair, a.id, CONCAT(x1.text, ' ', x2.text, ' г.р.з. ', a.gos_znak), a.dostup FROM cars a
	INNER JOIN (SELECT id_car, MAX(car_mileage) as car_mileage FROM car_repair
	WHERE change_oil = 1 GROUP BY id_car) b ON a.id = b.id_car
	INNER JOIN car_repair c ON c.id_car = b.id_car AND c.car_mileage = b.car_mileage AND c.change_oil = 1
	LEFT JOIN s2i_klass x1 ON x1.kod=a.marka AND x1.nomer=3
	LEFT JOIN s2i_klass x2 ON x2.kod=a.model AND x2.nomer=4
	WHERE (a.mileage - b.car_mileage) > a.mileage_oil AND a.exception_notice_events = 0;

DECLARE cur10 CURSOR FOR SELECT cars_dopog.date_end_dopog, cars.id, CONCAT(x1.text, ' ', x2.text, ' г.р.з. ', cars.gos_znak), cars.dostup FROM cars_dopog 
	INNER JOIN cars ON cars.id=cars_dopog.id_car AND cars.ibd_arx=1 AND cars.exception_notice_events = 0
	LEFT JOIN s2i_klass x1 ON x1.kod=cars.marka AND x1.nomer=3
	LEFT JOIN s2i_klass x2 ON x2.kod=cars.model AND x2.nomer=4
	WHERE cars_dopog.ibd_arx=1 AND cars_dopog.date_end_dopog  < CURDATE();
	
DECLARE cur11 CURSOR FOR SELECT cars_dopog.date_end_dopog, cars.id, CONCAT(x1.text, ' ', x2.text, ' г.р.з. ', cars.gos_znak), cars.dostup FROM cars_dopog 
	INNER JOIN cars ON cars.id=cars_dopog.id_car  AND cars.ibd_arx=1 AND cars.exception_notice_events = 0
	LEFT JOIN s2i_klass x1 ON x1.kod=cars.marka AND x1.nomer=3
	LEFT JOIN s2i_klass x2 ON x2.kod=cars.model AND x2.nomer=4
	WHERE cars_dopog.ibd_arx=1 AND (cars_dopog.date_end_dopog  BETWEEN CURDATE() AND CURDATE() + INTERVAL 15 DAY);

DECLARE cur12 CURSOR FOR SELECT car_calibration.date_next_calibration, cars.id, CONCAT(x1.text, ' ', x2.text, ' г.р.з. ', cars.gos_znak), cars.dostup FROM car_calibration 
	INNER JOIN cars ON cars.id=car_calibration.id_car AND cars.ibd_arx=1 AND cars.exception_notice_events = 0
	LEFT JOIN s2i_klass x1 ON x1.kod=cars.marka AND x1.nomer=3
	LEFT JOIN s2i_klass x2 ON x2.kod=cars.model AND x2.nomer=4
	WHERE car_calibration.ibd_arx=1 AND car_calibration.date_next_calibration  < CURDATE();
	
DECLARE cur13 CURSOR FOR SELECT car_calibration.date_next_calibration, cars.id, CONCAT(x1.text, ' ', x2.text, ' г.р.з. ', cars.gos_znak), cars.dostup FROM car_calibration 
	INNER JOIN cars ON cars.id=car_calibration.id_car  AND cars.ibd_arx=1 AND cars.exception_notice_events = 0
	LEFT JOIN s2i_klass x1 ON x1.kod=cars.marka AND x1.nomer=3
	LEFT JOIN s2i_klass x2 ON x2.kod=cars.model AND x2.nomer=4
	WHERE car_calibration.ibd_arx=1 AND (car_calibration.date_next_calibration  BETWEEN CURDATE() AND CURDATE() + INTERVAL 15 DAY);

DECLARE cur14 CURSOR FOR SELECT car_tachograph.date_end_skzi, cars.id, CONCAT(x1.text, ' ', x2.text, ' г.р.з. ', cars.gos_znak), cars.dostup FROM car_tachograph 
	INNER JOIN cars ON cars.id=car_tachograph.id_car AND cars.ibd_arx=1 AND cars.exception_notice_events = 0
	LEFT JOIN s2i_klass x1 ON x1.kod=cars.marka AND x1.nomer=3
	LEFT JOIN s2i_klass x2 ON x2.kod=cars.model AND x2.nomer=4
	WHERE car_tachograph.ibd_arx=1 AND car_tachograph.date_end_skzi   < CURDATE();
	
DECLARE cur15 CURSOR FOR SELECT car_tachograph.date_end_skzi, cars.id, CONCAT(x1.text, ' ', x2.text, ' г.р.з. ', cars.gos_znak), cars.dostup FROM car_tachograph 
	INNER JOIN cars ON cars.id=car_tachograph.id_car  AND cars.ibd_arx=1 AND cars.exception_notice_events = 0
	LEFT JOIN s2i_klass x1 ON x1.kod=cars.marka AND x1.nomer=3
	LEFT JOIN s2i_klass x2 ON x2.kod=cars.model AND x2.nomer=4
	WHERE car_tachograph.ibd_arx=1 AND (car_tachograph.date_end_skzi   BETWEEN CURDATE() AND CURDATE() + INTERVAL 15 DAY);

DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

SET done = FALSE;

OPEN cur1;
read_loop: LOOP
	FETCH cur1 INTO dd, id, fio, dostup;
    
	IF done THEN 
    	LEAVE read_loop;
     END IF;
	
    INSERT INTO notice_events (notice_subsystem, notice_text, notice_status, notice_object, notice_id_object, notice_table_object, notice_dostup, notice_code_subsystem, dt_reg)
		VALUES
		('ТРАНСПОРТНЫЕ СРЕДСТВА. ПОЛИС ОСАГО.', CONCAT('истек срок действия полиса ОСАГО ', DATE_FORMAT(dd, '%d.%m.%Y')), 1, fio, id, 'car', dostup, 5, NOW());
    
END LOOP;
CLOSE cur1;

SET done = FALSE;

OPEN cur2;
read_loop: LOOP
	FETCH cur2 INTO dd, id, fio, dostup;
	
	IF done THEN 
    	LEAVE read_loop;
     END IF;
    
    INSERT INTO notice_events (notice_subsystem, notice_text, notice_status, notice_object, notice_id_object, notice_table_object, notice_dostup, notice_code_subsystem, dt_reg)
		VALUES
		('ТРАНСПОРТНЫЕ СРЕДСТВА. ПОЛИС ОСАГО.', CONCAT('истекает срок действия полиса ОСАГО ', DATE_FORMAT(dd, '%d.%m.%Y')), 2, fio, id, 'car', dostup, 5, NOW());
    
END LOOP;
CLOSE cur2;

SET done = FALSE;

OPEN cur3;
read_loop: LOOP
	FETCH cur3 INTO dd, id, fio, dostup;
    
	IF done THEN 
    	LEAVE read_loop;
     END IF;
	
    INSERT INTO notice_events (notice_subsystem, notice_text, notice_status, notice_object, notice_id_object, notice_table_object, notice_dostup, notice_code_subsystem, dt_reg)
		VALUES
		('ТРАНСПОРТНЫЕ СРЕДСТВА. ТЕХНИЧЕСКИЙ ОСМОТР.', CONCAT('истек срок действия сертификата прохождения ТЕХНИЧЕСКОГО ОСМОТРА ', DATE_FORMAT(dd, '%d.%m.%Y')), 1, fio, id, 'car', dostup, 6, NOW());
    
END LOOP;
CLOSE cur3;

SET done = FALSE;

OPEN cur4;
read_loop: LOOP
	FETCH cur4 INTO dd, id, fio, dostup;
	
	IF done THEN 
    	LEAVE read_loop;
     END IF;
    
    INSERT INTO notice_events (notice_subsystem, notice_text, notice_status, notice_object, notice_id_object, notice_table_object, notice_dostup, notice_code_subsystem, dt_reg)
		VALUES
		('ТРАНСПОРТНЫЕ СРЕДСТВА. ТЕХНИЧЕСКИЙ ОСМОТР.', CONCAT('истекает срок действия сертификата прохождения ТЕХНИЧЕСКОГО ОСМОТРА ', DATE_FORMAT(dd, '%d.%m.%Y')), 2, fio, id, 'car', dostup, 6, NOW());
    
END LOOP;
CLOSE cur4;

SET done = FALSE;

OPEN cur5;
read_loop: LOOP
	FETCH cur5 INTO dd, id, fio, dostup;
	
	IF done THEN 
    	LEAVE read_loop;
     END IF;
    
    INSERT INTO notice_events (notice_subsystem, notice_text, notice_status, notice_object, notice_id_object, notice_table_object, notice_dostup, notice_code_subsystem, dt_reg)
		VALUES
		('ТРАНСПОРТНЫЕ СРЕДСТВА. АПТЕЧКИ.', CONCAT('истек срок годности аптечки ', DATE_FORMAT(dd, '%d.%m.%Y')), 1, fio, id, 'car', dostup, 7, NOW());
    
END LOOP;
CLOSE cur5;

SET done = FALSE;

OPEN cur6;
read_loop: LOOP
	FETCH cur6 INTO dd, id, fio, dostup;
	
	IF done THEN 
    	LEAVE read_loop;
     END IF;
    
    INSERT INTO notice_events (notice_subsystem, notice_text, notice_status, notice_object, notice_id_object, notice_table_object, notice_dostup, notice_code_subsystem, dt_reg)
		VALUES
		('ТРАНСПОРТНЫЕ СРЕДСТВА. АПТЕЧКИ.', CONCAT('истекает срок годности аптечки ', DATE_FORMAT(dd, '%d.%m.%Y')), 2, fio, id, 'car', dostup, 7, NOW());
    
END LOOP;
CLOSE cur6;

SET done = FALSE;

OPEN cur7;
read_loop: LOOP
	FETCH cur7 INTO dd, id, fio, dostup;
	
	IF done THEN 
    	LEAVE read_loop;
     END IF;
    
    INSERT INTO notice_events (notice_subsystem, notice_text, notice_status, notice_object, notice_id_object, notice_table_object, notice_dostup, notice_code_subsystem, dt_reg)
		VALUES
		('ТРАНСПОРТНЫЕ СРЕДСТВА. ОГНЕТУШИТЕЛИ.', CONCAT('истек срок годности огнетушителя ', DATE_FORMAT(dd, '%d.%m.%Y')), 1, fio, id, 'car', dostup, 8, NOW());
    
END LOOP;
CLOSE cur7;

SET done = FALSE;

OPEN cur8;
read_loop: LOOP
	FETCH cur8 INTO dd, id, fio, dostup;
	
	IF done THEN 
    	LEAVE read_loop;
     END IF;
    
    INSERT INTO notice_events (notice_subsystem, notice_text, notice_status, notice_object, notice_id_object, notice_table_object, notice_dostup, notice_code_subsystem, dt_reg)
		VALUES
		('ТРАНСПОРТНЫЕ СРЕДСТВА. ОГНЕТУШИТЕЛИ.', CONCAT('истекает срок годности огнетушителя ', DATE_FORMAT(dd, '%d.%m.%Y')), 2, fio, id, 'car', dostup, 8, NOW());
    
END LOOP;
CLOSE cur8;

SET done = FALSE;

OPEN cur9;
read_loop: LOOP
	FETCH cur9 INTO dd, id, fio, dostup;
	
	IF done THEN 
    	LEAVE read_loop;
     END IF;
    
    INSERT INTO notice_events (notice_subsystem, notice_text, notice_status, notice_object, notice_id_object, notice_table_object, notice_dostup, notice_code_subsystem, dt_reg)
		VALUES
		('ТРАНСПОРТНЫЕ СРЕДСТВА. ЗАМЕНА МАСЛА.', CONCAT('требуется замена масла. Последняя замена масла была осуществлена ', DATE_FORMAT(dd, '%d.%m.%Y')), 1, fio, id, 'car', dostup, 9, NOW());
    
END LOOP;
CLOSE cur9;

SET done = FALSE;

OPEN cur10;
read_loop: LOOP
	FETCH cur10 INTO dd, id, fio, dostup;
    
	IF done THEN 
    	LEAVE read_loop;
     END IF;
	
    INSERT INTO notice_events (notice_subsystem, notice_text, notice_status, notice_object, notice_id_object, notice_table_object, notice_dostup, notice_code_subsystem, dt_reg)
		VALUES
		('ТРАНСПОРТНЫЕ СРЕДСТВА. УДОСТОВЕРЕНИЕ ДОПОГ.', CONCAT('истек срок действия удостоверения ДОПОГ ', DATE_FORMAT(dd, '%d.%m.%Y')), 1, fio, id, 'car', dostup, 10, NOW());
    
END LOOP;
CLOSE cur10;

SET done = FALSE;

OPEN cur11;
read_loop: LOOP
	FETCH cur11 INTO dd, id, fio, dostup;
	
	IF done THEN 
    	LEAVE read_loop;
     END IF;
    
    INSERT INTO notice_events (notice_subsystem, notice_text, notice_status, notice_object, notice_id_object, notice_table_object, notice_dostup, notice_code_subsystem, dt_reg)
		VALUES
		('ТРАНСПОРТНЫЕ СРЕДСТВА. УДОСТОВЕРЕНИЕ ДОПОГ.', CONCAT('истекает срок действия удостоверения ДОПОГ ', DATE_FORMAT(dd, '%d.%m.%Y')), 2, fio, id, 'car', dostup, 10, NOW());
    
END LOOP;
CLOSE cur11;

SET done = FALSE;

OPEN cur12;
read_loop: LOOP
	FETCH cur12 INTO dd, id, fio, dostup;
    
	IF done THEN 
    	LEAVE read_loop;
     END IF;
	
    INSERT INTO notice_events (notice_subsystem, notice_text, notice_status, notice_object, notice_id_object, notice_table_object, notice_dostup, notice_code_subsystem, dt_reg)
		VALUES
		('ТРАНСПОРТНЫЕ СРЕДСТВА. КАЛИБРОВКА (ЭКСПЕРТИЗА).', CONCAT('истек срок действия калибровки (экспертизы) ', DATE_FORMAT(dd, '%d.%m.%Y')), 1, fio, id, 'car', dostup, 11, NOW());
    
END LOOP;
CLOSE cur12;

SET done = FALSE;

OPEN cur13;
read_loop: LOOP
	FETCH cur13 INTO dd, id, fio, dostup;
	
	IF done THEN 
    	LEAVE read_loop;
     END IF;
    
    INSERT INTO notice_events (notice_subsystem, notice_text, notice_status, notice_object, notice_id_object, notice_table_object, notice_dostup, notice_code_subsystem, dt_reg)
		VALUES
		('ТРАНСПОРТНЫЕ СРЕДСТВА. КАЛИБРОВКА (ЭКСПЕРТИЗА).', CONCAT('истекает срок действия калибровки (экспертизы) ', DATE_FORMAT(dd, '%d.%m.%Y')), 2, fio, id, 'car', dostup, 11, NOW());
    
END LOOP;
CLOSE cur13;

SET done = FALSE;

OPEN cur14;
read_loop: LOOP
	FETCH cur14 INTO dd, id, fio, dostup;
    
	IF done THEN 
    	LEAVE read_loop;
     END IF;
	
    INSERT INTO notice_events (notice_subsystem, notice_text, notice_status, notice_object, notice_id_object, notice_table_object, notice_dostup, notice_code_subsystem, dt_reg)
		VALUES
		('ТРАНСПОРТНЫЕ СРЕДСТВА. ТАХОГРАФ.', CONCAT('истек срок действия тахографа ', DATE_FORMAT(dd, '%d.%m.%Y')), 1, fio, id, 'car', dostup, 12, NOW());
    
END LOOP;
CLOSE cur14;

SET done = FALSE;

OPEN cur15;
read_loop: LOOP
	FETCH cur15 INTO dd, id, fio, dostup;
	
	IF done THEN 
    	LEAVE read_loop;
     END IF;
    
    INSERT INTO notice_events (notice_subsystem, notice_text, notice_status, notice_object, notice_id_object, notice_table_object, notice_dostup, notice_code_subsystem, dt_reg)
		VALUES
		('ТРАНСПОРТНЫЕ СРЕДСТВА. ТАХОГРАФ.', CONCAT('истекает срок действия тахографа ', DATE_FORMAT(dd, '%d.%m.%Y')), 2, fio, id, 'car', dostup, 12, NOW());
    
END LOOP;
CLOSE cur15;

END;";
		mysqli_query($link, $sql);

		$sql = "CREATE DEFINER=`root`@`localhost` PROCEDURE `get_users_notice` ()  NO SQL
		BEGIN

UPDATE users SET notice_events = (SELECT CONCAT(IFNULL(COUNT(*), 0), '-', IFNULL(BB.warning, 0), '-', IFNULL(CC.info, 0)) FROM notice_events AA
	LEFT JOIN (SELECT COUNT(*) as warning, '1' as tt FROM notice_events WHERE notice_status = 2 GROUP BY tt) BB ON BB.tt = 1
	LEFT JOIN (SELECT COUNT(*) as info, '1' as tt FROM notice_events WHERE notice_status = 3 GROUP BY tt) CC ON CC.tt = 1
	WHERE notice_status = 1) WHERE role >= 2;

UPDATE users SET notice_events = (SELECT CONCAT(IFNULL(COUNT(*), 0), '-', IFNULL(BB.warning, 0), '-', IFNULL(CC.info, 0)) FROM notice_events AA
	LEFT JOIN (SELECT COUNT(*) as warning, '1' as tt FROM notice_events WHERE notice_status = 2 AND notice_dostup = 1 GROUP BY tt) BB ON BB.tt = 1
	LEFT JOIN (SELECT COUNT(*) as info, '1' as tt FROM notice_events WHERE notice_status = 3 AND notice_dostup = 1 GROUP BY tt) CC ON CC.tt = 1
	WHERE notice_status = 1 AND notice_dostup = 1) WHERE role < 2;

END;";
		mysqli_query($link, $sql);

		$sql = "CREATE DEFINER=`root`@`localhost` PROCEDURE `get_notice` ()  NO SQL
		BEGIN
	DELETE FROM notice_events;
    ALTER TABLE notice_events AUTO_INCREMENT = 1;
    
    CALL get_notice_cars();
    CALL get_notice_drivers();
    CALL get_users_notice();
END;";
		mysqli_query($link, $sql);
	}
}