<?php

namespace IcKomiApp\models;

use IcKomiApp\core\Functions;

class Install {

	private $db_name = 'mysql';
	private $host = 'localhost';
	private $login = '';
	private $password = '';
	private $charset = 'utf8mb4';

	private $create_db_name = 'atx';
	private $create_user = 'atx';
	private $create_user_password = 'AtxDatabase2020';
  private $admin_hash_password = '$2y$10$wqYILsqibXuGNYc/TRwiNuPnQgt.UB9hd/pmJt.b/EaAmm3SQkUZO';

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
		$this->create_tasks($link);
    $this->create_data($link);

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
  `owner_car` int(11) NOT NULL DEFAULT 0 COMMENT 'Владелец ТС. Спр. 40',
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
	DELETE FROM cars_dopog WHERE id_car = OLD.id;
  DELETE FROM cars_old_gos_znak WHERE id_car = OLD.id;
  DELETE FROM cars_wheels WHERE id_car = OLD.id;
  DELETE FROM car_battery WHERE id_car = OLD.id;
  DELETE FROM car_calibration WHERE id_car = OLD.id;
  DELETE FROM car_dvr WHERE id_car = OLD.id;
  DELETE FROM car_fire_extinguisher WHERE id_car = OLD.id;
  DELETE FROM car_first_aid_kid WHERE id_car = OLD.id;
  DELETE FROM car_for_driver WHERE car_id = OLD.id;
  DELETE FROM car_glonass WHERE id_car = OLD.id;
  DELETE FROM car_link_document WHERE id_car = OLD.id;
  DELETE FROM car_tachograph WHERE id_car = OLD.id;
  DELETE FROM car_warning_triangle WHERE id_car = OLD.id;
  DELETE FROM certificate_registration WHERE id_car = OLD.id;
  DELETE FROM osago WHERE id_car = OLD.id;
  DELETE FROM pts WHERE id_car = OLD.id;
  DELETE FROM speedometer WHERE id_car = OLD.id;
  DELETE FROM speedometer_first_testimony WHERE id_car = OLD.id;
  DELETE FROM technical_inspection WHERE id_car = OLD.id;
  DELETE FROM files WHERE category_file = 13 AND id_object = OLD.id;
  UPDATE dtp SET id_car = 0 WHERE id_car = OLD.id;
  UPDATE adm_offense SET id_car = 0 WHERE id_car = OLD.id;
  UPDATE car_repair SET id_car = 0 WHERE id_car = OLD.id;
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
CREATE TRIGGER `table_insert_`` BEFORE INSERT ON `certificate_registration` FOR EACH ROW BEGIN
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
	  DELETE FROM drivers_card WHERE id_driver = OLD.id;
    DELETE FROM drivers_document WHERE id_driver = OLD.id;
    DELETE FROM drivers_document_cran WHERE id_driver = OLD.id;
    DELETE FROM drivers_document_tractor WHERE id_driver = OLD.id;
    DELETE FROM drivers_dopog WHERE id_driver = OLD.id;
    DELETE FROM car_for_driver WHERE id_driver = OLD.id;
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
		$sql = "CREATE USER IF NOT EXISTS '" . $this->create_user . "'@'localhost' IDENTIFIED BY '" . $this->create_user_password . "';GRANT USAGE ON *.* TO '" . $this->create_user . "'@'%' REQUIRE NONE WITH MAX_QUERIES_PER_HOUR 0 MAX_CONNECTIONS_PER_HOUR 0 MAX_UPDATES_PER_HOUR 0 MAX_USER_CONNECTIONS 0;";

		if(!$this->multi_query($link, $sql, "Ошибка при создании пользователя!"))
			return false;

		$sql = "GRANT ALL PRIVILEGES ON `" . $this->create_db_name . "`.* TO '" . $this->create_user . "'@'localhost' WITH GRANT OPTION;";
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

    $sql = "ALTER TABLE `certificate_registration` MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID записи', AUTO_INCREMENT=1;";
    mysqli_query($link, $sql);

    $sql = "ALTER TABLE `car_fire_extinguisher` MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID записи', AUTO_INCREMENT=1;";
    mysqli_query($link, $sql);

    $sql = "ALTER TABLE `car_first_aid_kid` MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID записи', AUTO_INCREMENT=1;";
    mysqli_query($link, $sql);

    $sql = "ALTER TABLE `car_warning_triangle` MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID записи', AUTO_INCREMENT=1;";
    mysqli_query($link, $sql);

    $sql = "ALTER TABLE `osago` MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID записи', AUTO_INCREMENT=1;";
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

	/*
		Функция загрузки событий
	*/
	private function create_tasks($link) {
		$sql = "CREATE EVENT `generate_notice_events` ON SCHEDULE EVERY 2 HOUR ON COMPLETION NOT PRESERVE ENABLE DO BEGIN CALL get_notice(); END;";
		mysqli_query($link, $sql);
	}

  private function create_data($link) {
    $sql = "INSERT INTO `cars` (`id`, `vin`, `marka`, `model`, `gos_znak`, `n_dvig`, `shassi`, `kuzov`, `color`, `car_vat`, `car_v`, `mass_max`, `mass_min`, `inventory_n`, `ibd_arx`, `dostup`, `kodrai`, `n_reg`, `kateg_ts`, `god_car`, `tip_strah`, `kateg_mvd`, `kateg_gost`, `marka1`, `prim`, `slugba`, `num_speedometer`, `mileage`, `mileage_oil`, `basic_fuel`, `summer_fuel`, `winter_fuel`, `balance_price`, `exception_notice_events`, `write_off`, `owner_car`, `sh_polz`, `dt_reg`, `dt_izm`) VALUES
(1, NULL, 1, 1, 'У715СА', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 1, 1, 0, 177, 4, NULL, 0, 0, 0, NULL, NULL, 0, 1, 0.00, 10000, NULL, NULL, NULL, NULL, 0, 0, 0, 0, '2020-12-20 17:24:40', '2020-12-20 17:24:40'),
(2, NULL, 2, 2, 'О326СВ', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 1, 1, 0, 11, 2, NULL, 0, 0, 0, NULL, NULL, 0, 1, 0.00, 10000, NULL, NULL, NULL, NULL, 0, 0, 0, 0, '2020-12-20 17:24:40', '2020-12-20 17:24:40'),
(3, NULL, 3, 3, 'Р602АА', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 1, 1, 0, 11, 3, NULL, 0, 0, 0, NULL, NULL, 0, 1, 0.00, 10000, NULL, NULL, NULL, NULL, 0, 0, 0, 0, '2020-12-20 17:24:40', '2020-12-20 17:24:40'),
(4, NULL, 4, 4, 'В567ТУ ', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 1, 1, 0, 198, 1, NULL, 0, 0, 0, NULL, NULL, 0, 1, 0.00, 10000, NULL, NULL, NULL, NULL, 0, 0, 0, 0, '2020-12-20 17:24:40', '2020-12-20 17:24:40'),
(5, NULL, 5, 5, 'О311ТР', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 1, 1, 0, 11, 1, NULL, 0, 0, 0, NULL, NULL, 0, 1, 0.00, 10000, NULL, NULL, NULL, NULL, 0, 0, 0, 0, '2020-12-20 17:24:40', '2020-12-20 17:24:40'),
(6, NULL, 6, 6, 'О520УВ', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 1, 1, 0, 11, 1, NULL, 0, 0, 0, NULL, NULL, 0, 1, 0.00, 10000, NULL, NULL, NULL, NULL, 0, 0, 0, 0, '2020-12-20 17:24:40', '2020-12-20 17:24:40'),
(7, NULL, 6, 6, 'О521УВ', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 1, 1, 0, 11, 1, NULL, 0, 0, 0, NULL, NULL, 0, 1, 0.00, 10000, NULL, NULL, NULL, NULL, 0, 0, 0, 0, '2020-12-20 17:24:40', '2020-12-20 17:24:40'),
(8, NULL, 7, 7, 'Р158НК', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 1, 1, 0, 11, 1, NULL, 0, 0, 0, NULL, NULL, 0, 1, 0.00, 10000, NULL, NULL, NULL, NULL, 0, 0, 0, 0, '2020-12-20 17:24:41', '2020-12-20 17:24:41'),
(9, NULL, 5, 8, 'Р737НА', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 1, 1, 0, 11, 1, NULL, 0, 0, 0, NULL, NULL, 0, 1, 0.00, 10000, NULL, NULL, NULL, NULL, 0, 0, 0, 0, '2020-12-20 17:24:41', '2020-12-20 17:24:41'),
(10, NULL, 5, 8, 'В027СА', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 1, 1, 0, 43, 1, NULL, 0, 0, 0, NULL, NULL, 0, 1, 0.00, 10000, NULL, NULL, NULL, NULL, 0, 0, 0, 0, '2020-12-20 17:24:41', '2020-12-20 17:24:41'),
(11, NULL, 8, 9, 'С927ОА', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 1, 1, 0, 123, 1, NULL, 0, 0, 0, NULL, NULL, 0, 1, 0.00, 10000, NULL, NULL, NULL, NULL, 0, 0, 0, 0, '2020-12-20 17:24:41', '2020-12-20 17:24:41'),
(12, NULL, 9, 10, 'Н777ВО', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 1, 1, 0, 11, 1, NULL, 0, 0, 0, NULL, NULL, 0, 1, 0.00, 10000, NULL, NULL, NULL, NULL, 0, 0, 0, 0, '2020-12-20 17:24:41', '2020-12-20 17:24:41'),
(13, NULL, 9, 10, 'К099АХ', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 1, 1, 0, 11, 1, NULL, 0, 0, 0, NULL, NULL, 0, 1, 0.00, 10000, NULL, NULL, NULL, NULL, 0, 0, 0, 0, '2020-12-20 17:24:41', '2020-12-20 17:24:41'),
(14, NULL, 10, 11, 'Х609НА', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 1, 1, 0, 33, 1, NULL, 0, 0, 0, NULL, NULL, 0, 1, 0.00, 10000, NULL, NULL, NULL, NULL, 0, 0, 0, 0, '2020-12-20 17:24:41', '2020-12-20 17:24:41'),
(15, NULL, 4, 0, 'Р414НХ', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 1, 1, 0, 11, 1, NULL, 0, 0, 0, NULL, NULL, 0, 1, 0.00, 10000, NULL, NULL, NULL, NULL, 0, 0, 0, 0, '2020-12-20 17:24:41', '2020-12-20 17:24:41'),
(16, NULL, 2, 12, 'Н439ОХ', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 1, 1, 0, 11, 2, 2005, 0, 0, 0, NULL, 'Кол-во посадочных мест 22', 0, 1, 0.00, 10000, NULL, NULL, NULL, NULL, 0, 0, 1, 0, '2020-12-20 17:39:51', '2020-12-20 17:39:51'),
(17, NULL, 2, 13, 'Н829ХВ', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 1, 1, 0, 11, 2, 2010, 0, 0, 0, NULL, 'Кол-во посадочных мест 30', 0, 1, 0.00, 10000, NULL, NULL, NULL, NULL, 0, 0, 1, 0, '2020-12-20 17:39:51', '2020-12-20 17:39:51'),
(18, NULL, 2, 14, 'О665НУ', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 1, 1, 0, 11, 2, 2012, 0, 0, 0, NULL, 'Кол-во посадочных мест 25', 0, 1, 0.00, 10000, NULL, NULL, NULL, NULL, 0, 0, 1, 0, '2020-12-20 17:39:52', '2020-12-20 17:39:52'),
(19, NULL, 1, 1, 'У715СА', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 1, 1, 0, 177, 4, 2005, 0, 0, 0, NULL, 'Кол-во посадочных мест 22', 0, 1, 0.00, 10000, NULL, NULL, NULL, NULL, 0, 0, 1, 0, '2020-12-20 17:39:52', '2020-12-20 17:39:52'),
(20, NULL, 1, 1, 'О089ХТ', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 1, 1, 0, 11, 4, 2008, 0, 0, 0, NULL, 'Кол-во посадочных мест 22', 0, 1, 0.00, 10000, NULL, NULL, NULL, NULL, 0, 0, 1, 0, '2020-12-20 17:39:52', '2020-12-20 17:39:52'),
(21, NULL, 1, 1, 'Н307СО', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 1, 1, 0, 11, 4, 2007, 0, 0, 0, NULL, 'Кол-во посадочных мест 22', 0, 1, 0.00, 10000, NULL, NULL, NULL, NULL, 0, 0, 1, 0, '2020-12-20 17:39:52', '2020-12-20 17:39:52'),
(22, NULL, 11, 15, 'О623АМ', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 1, 1, 0, 11, 4, 2007, 0, 0, 0, NULL, 'Кол-во посадочных мест 26', 0, 1, 0.00, 10000, NULL, NULL, NULL, NULL, 0, 0, 1, 0, '2020-12-20 17:39:52', '2020-12-20 17:39:52'),
(23, NULL, 2, 16, 'О326СВ', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 1, 1, 0, 11, 2, 2010, 0, 0, 0, NULL, 'Кол-во посадочных мест 24', 0, 1, 0.00, 10000, NULL, NULL, NULL, NULL, 0, 0, 1, 0, '2020-12-20 17:39:52', '2020-12-20 17:39:52'),
(24, NULL, 2, 14, 'О860РВ', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 1, 1, 0, 11, 2, 2010, 0, 0, 0, NULL, 'Кол-во посадочных мест 23', 0, 1, 0.00, 10000, NULL, NULL, NULL, NULL, 0, 0, 1, 0, '2020-12-20 17:39:52', '2020-12-20 17:39:52'),
(25, NULL, 12, 17, 'В187НЕ', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 1, 1, 0, 11, 2, 2007, 0, 0, 0, NULL, 'Кол-во посадочных мест 18', 0, 1, 0.00, 10000, NULL, NULL, NULL, NULL, 0, 0, 2, 0, '2020-12-20 17:39:52', '2020-12-20 17:39:52'),
(26, NULL, 3, 18, 'В122НА', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 1, 1, 0, 11, 5, 2006, 0, 0, 0, NULL, '', 0, 1, 0.00, 10000, NULL, NULL, NULL, NULL, 0, 0, 1, 0, '2020-12-20 17:44:47', '2020-12-20 17:44:47'),
(27, NULL, 3, 18, 'О452АМ', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 1, 1, 0, 11, 5, 2007, 0, 0, 0, NULL, '', 0, 1, 0.00, 10000, NULL, NULL, NULL, NULL, 0, 0, 1, 0, '2020-12-20 17:44:47', '2020-12-20 17:44:47'),
(28, NULL, 3, 19, 'О167СВ ', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 1, 1, 0, 11, 5, 2011, 0, 0, 0, NULL, '', 0, 1, 0.00, 10000, NULL, NULL, NULL, NULL, 0, 0, 1, 0, '2020-12-20 17:44:47', '2020-12-20 17:44:47'),
(29, NULL, 3, 19, 'О209СВ', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 1, 1, 0, 11, 5, 2011, 0, 0, 0, NULL, '', 0, 1, 0.00, 10000, NULL, NULL, NULL, NULL, 0, 0, 1, 0, '2020-12-20 17:44:47', '2020-12-20 17:44:47'),
(30, NULL, 13, 20, 'АК6543', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 1, 1, 0, 11, 6, 2003, 0, 0, 0, NULL, 'Масса 23.5 тн', 0, 1, 0.00, 10000, NULL, NULL, NULL, NULL, 0, 0, 1, 0, '2020-12-20 17:56:52', '2020-12-20 17:56:52'),
(31, NULL, 1, 21, 'АК6542', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 1, 1, 0, 11, 6, 2008, 0, 0, 0, NULL, 'Масса 16.4 тн', 0, 1, 0.00, 10000, NULL, NULL, NULL, NULL, 0, 0, 1, 0, '2020-12-20 17:56:52', '2020-12-20 17:56:52'),
(32, NULL, 1, 21, 'АК5971', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 1, 1, 0, 11, 6, 2006, 0, 0, 0, NULL, 'Масса 16.4 тн', 0, 1, 0.00, 10000, NULL, NULL, NULL, NULL, 0, 0, 1, 0, '2020-12-20 17:56:52', '2020-12-20 17:56:52'),
(33, NULL, 13, 20, 'АК0005', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 1, 1, 0, 11, 6, 2006, 0, 0, 0, NULL, 'Масса 21 тн', 0, 1, 0.00, 10000, NULL, NULL, NULL, NULL, 0, 0, 1, 0, '2020-12-20 17:56:53', '2020-12-20 17:56:53'),
(34, NULL, 14, 22, 'АК9528', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 1, 1, 0, 11, 6, 0, 0, 0, 0, NULL, '', 0, 1, 0.00, 10000, NULL, NULL, NULL, NULL, 0, 0, 1, 0, '2020-12-20 17:58:16', '2020-12-20 17:58:16'),
(35, NULL, 15, 23, 'АК6541', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 1, 1, 0, 11, 7, 2008, 0, 0, 0, NULL, 'Масса 32 тн', 0, 1, 0.00, 10000, NULL, NULL, NULL, NULL, 0, 0, 1, 0, '2020-12-20 17:58:16', '2020-12-20 17:58:16'),
(36, NULL, 0, 0, 'АК8081', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 1, 1, 0, 11, 8, 2011, 0, 0, 0, NULL, 'Масса 19.9 (23 561) тн', 0, 1, 0.00, 10000, NULL, NULL, NULL, NULL, 0, 0, 1, 0, '2020-12-20 17:58:16', '2020-12-20 17:58:16'),
(37, NULL, 15, 24, 'АО4252', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 1, 1, 0, 11, 7, 2014, 0, 0, 0, NULL, 'Масса 31 тн', 0, 1, 0.00, 10000, NULL, NULL, NULL, NULL, 0, 0, 1, 0, '2020-12-20 17:58:16', '2020-12-20 17:58:16'),
(38, NULL, 16, 25, 'АО5418', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 1, 1, 0, 11, 6, 1999, 0, 0, 0, NULL, 'Масса 15 тн', 0, 1, 0.00, 10000, NULL, NULL, NULL, NULL, 0, 0, 1, 0, '2020-12-20 17:58:16', '2020-12-20 17:58:16'),
(39, NULL, 11, 26, 'Н840ХВ', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 1, 1, 0, 11, 9, 2006, 0, 0, 0, NULL, '', 0, 1, 0.00, 10000, NULL, NULL, NULL, NULL, 0, 0, 1, 0, '2020-12-20 18:07:03', '2020-12-20 18:07:03'),
(40, NULL, 17, 27, 'Н328СА', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 1, 1, 0, 11, 30, 1991, 0, 0, 0, NULL, '', 0, 1, 0.00, 10000, NULL, NULL, NULL, NULL, 0, 0, 3, 0, '2020-12-20 18:07:03', '2020-12-20 18:07:03'),
(41, NULL, 3, 28, 'О216МН', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 1, 1, 0, 11, 9, 2012, 0, 0, 0, NULL, '', 0, 1, 0.00, 10000, NULL, NULL, NULL, NULL, 0, 0, 1, 0, '2020-12-20 18:07:03', '2020-12-20 18:07:03'),
(42, NULL, 3, 29, 'О960РХ', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 1, 1, 0, 11, 9, 2013, 0, 0, 0, NULL, '', 0, 1, 0.00, 10000, NULL, NULL, NULL, NULL, 0, 0, 1, 0, '2020-12-20 18:07:03', '2020-12-20 18:07:03'),
(43, NULL, 3, 29, 'О443УВ', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 1, 1, 0, 11, 9, 2014, 0, 0, 0, NULL, '', 0, 1, 0.00, 10000, NULL, NULL, NULL, NULL, 0, 0, 1, 0, '2020-12-20 18:07:03', '2020-12-20 18:07:03'),
(44, NULL, 18, 30, 'Н306СО', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 1, 1, 0, 11, 10, 2006, 0, 0, 0, NULL, 'Кол-во пассажиров 5', 0, 1, 0.00, 10000, NULL, NULL, NULL, NULL, 0, 0, 1, 0, '2020-12-20 18:20:15', '2020-12-20 18:20:15'),
(45, NULL, 18, 31, 'В674МР', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 1, 1, 0, 11, 10, 2006, 0, 0, 0, NULL, '', 0, 1, 0.00, 10000, NULL, NULL, NULL, NULL, 0, 0, 1, 0, '2020-12-20 18:20:15', '2020-12-20 18:20:15'),
(46, NULL, 18, 32, 'В767НС', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 1, 1, 0, 11, 11, 2005, 0, 0, 0, NULL, '', 0, 1, 0.00, 10000, NULL, NULL, NULL, NULL, 0, 0, 1, 0, '2020-12-20 18:20:15', '2020-12-20 18:20:15'),
(47, NULL, 18, 33, 'М147АА', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 1, 1, 0, 10, 10, 2011, 0, 0, 0, NULL, '', 0, 1, 0.00, 10000, NULL, NULL, NULL, NULL, 0, 0, 1, 0, '2020-12-20 18:20:15', '2020-12-20 18:20:15'),
(48, NULL, 18, 34, 'Р284МР', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 1, 1, 0, 11, 12, 2019, 0, 0, 0, NULL, '', 0, 1, 0.00, 10000, NULL, NULL, NULL, NULL, 0, 0, 1, 0, '2020-12-20 18:20:16', '2020-12-20 18:20:16'),
(49, NULL, 18, 35, 'О927МН', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 1, 1, 0, 11, 13, 2005, 0, 0, 0, NULL, '', 0, 1, 0.00, 10000, NULL, NULL, NULL, NULL, 0, 0, 1, 0, '2020-12-20 18:20:16', '2020-12-20 18:20:16'),
(50, NULL, 3, 36, 'О920АМ', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 1, 1, 0, 11, 14, 2007, 0, 0, 0, NULL, 'Кол-во пассажиров 6', 0, 1, 0.00, 10000, NULL, NULL, NULL, NULL, 0, 0, 1, 0, '2020-12-20 18:20:16', '2020-12-20 18:20:16'),
(51, NULL, 11, 37, 'В131КН', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 1, 1, 0, 11, 15, 1990, 0, 0, 0, NULL, 'Масса 3 тн', 0, 1, 0.00, 10000, NULL, NULL, NULL, NULL, 0, 0, 1, 0, '2020-12-20 18:20:16', '2020-12-20 18:20:16'),
(52, NULL, 3, 38, 'О868РВ', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 1, 1, 0, 11, 15, 2013, 0, 0, 0, NULL, 'Масса 6 тн', 0, 1, 0.00, 10000, NULL, NULL, NULL, NULL, 0, 0, 1, 0, '2020-12-20 18:20:16', '2020-12-20 18:20:16'),
(53, NULL, 3, 39, 'Р952КА', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 1, 1, 0, 11, 15, 2018, 0, 0, 0, NULL, 'Масса 6 тн', 0, 1, 0.00, 10000, NULL, NULL, NULL, NULL, 0, 0, 1, 0, '2020-12-20 18:20:16', '2020-12-20 18:20:16'),
(54, NULL, 3, 40, 'В054МН', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 1, 1, 0, 82, 16, 2005, 0, 0, 0, NULL, '', 0, 1, 0.00, 10000, NULL, NULL, NULL, NULL, 0, 0, 1, 0, '2020-12-20 18:20:16', '2020-12-20 18:20:16'),
(55, NULL, 3, 41, 'А735СС', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 1, 1, 0, 83, 17, 2010, 0, 0, 0, NULL, '', 0, 1, 0.00, 10000, NULL, NULL, NULL, NULL, 0, 0, 1, 0, '2020-12-20 18:20:16', '2020-12-20 18:20:16'),
(56, NULL, 3, 42, 'Р602АА', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 1, 1, 0, 11, 3, 2011, 0, 0, 0, NULL, 'Масса 20 тн', 0, 1, 0.00, 10000, NULL, NULL, NULL, NULL, 0, 0, 1, 0, '2020-12-20 18:20:16', '2020-12-20 18:20:16'),
(57, NULL, 7, 7, 'О590КВ', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 1, 1, 0, 11, 18, 2003, 0, 0, 0, NULL, 'Харьяга (механики)', 0, 1, 0.00, 10000, NULL, NULL, NULL, NULL, 0, 0, 0, 0, '2020-12-20 18:45:15', '2020-12-20 18:45:15'),
(58, NULL, 19, 43, 'О892РЕ', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 1, 1, 0, 11, 19, 2013, 0, 0, 0, NULL, 'киров', 0, 1, 0.00, 10000, NULL, NULL, NULL, NULL, 0, 0, 0, 0, '2020-12-20 18:45:15', '2020-12-20 18:45:15'),
(59, NULL, 4, 4, 'В567ТУ', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 1, 1, 0, 198, 1, 2019, 0, 0, 0, NULL, 'ГД', 0, 1, 0.00, 10000, NULL, NULL, NULL, NULL, 0, 0, 0, 0, '2020-12-20 18:45:15', '2020-12-20 18:45:15'),
(60, NULL, 20, 44, 'О926РХ', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 1, 1, 0, 11, 1, 0, 0, 0, 0, NULL, 'Яковлев', 0, 1, 0.00, 10000, NULL, NULL, NULL, NULL, 0, 0, 0, 0, '2020-12-20 18:45:15', '2020-12-20 18:45:15'),
(61, NULL, 5, 5, 'О310ТР', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 1, 1, 0, 11, 18, 2013, 0, 0, 0, NULL, 'Ермаков М.', 0, 1, 0.00, 10000, NULL, NULL, NULL, NULL, 0, 0, 0, 0, '2020-12-20 18:45:15', '2020-12-20 18:45:15'),
(62, NULL, 5, 5, 'О311ТР', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 1, 1, 0, 11, 18, 2013, 0, 0, 0, NULL, 'СГМ', 0, 1, 0.00, 10000, NULL, NULL, NULL, NULL, 0, 0, 0, 0, '2020-12-20 18:45:15', '2020-12-20 18:45:15'),
(63, NULL, 6, 6, 'О520УВ', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 1, 1, 0, 11, 1, 2014, 0, 0, 0, NULL, 'Дежурка', 0, 1, 0.00, 10000, NULL, NULL, NULL, NULL, 0, 0, 0, 0, '2020-12-20 18:45:15', '2020-12-20 18:45:15'),
(64, NULL, 6, 6, 'О521УВ', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 1, 1, 0, 11, 1, 2014, 0, 0, 0, NULL, 'МТО', 0, 1, 0.00, 10000, NULL, NULL, NULL, NULL, 0, 0, 0, 0, '2020-12-20 18:45:15', '2020-12-20 18:45:15'),
(65, NULL, 21, 0, 'В027МН', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 1, 1, 0, 82, 18, 2017, 0, 0, 0, NULL, 'Соболев (СДО)', 0, 1, 0.00, 10000, NULL, NULL, NULL, NULL, 0, 0, 0, 0, '2020-12-20 18:45:16', '2020-12-20 18:45:16'),
(66, NULL, 7, 7, 'А027ЕО', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 1, 1, 0, 83, 1, 2006, 0, 0, 0, NULL, 'на ремонте', 0, 1, 0.00, 10000, NULL, NULL, NULL, NULL, 0, 0, 0, 0, '2020-12-20 18:45:16', '2020-12-20 18:45:16'),
(67, NULL, 7, 7, 'А921ЕО', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 1, 1, 0, 83, 18, 2008, 0, 0, 0, NULL, 'на ремонте', 0, 1, 0.00, 10000, NULL, NULL, NULL, NULL, 0, 0, 0, 0, '2020-12-20 18:45:16', '2020-12-20 18:45:16'),
(68, NULL, 7, 7, '', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 1, 1, 0, 0, 18, 0, 0, 0, 0, NULL, 'Строители Харьяга', 0, 1, 0.00, 10000, NULL, NULL, NULL, NULL, 0, 0, 0, 0, '2020-12-20 18:45:38', '2020-12-20 18:45:38'),
(69, NULL, 5, 8, 'Р737НА', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 1, 1, 0, 11, 1, 2019, 0, 0, 0, NULL, 'Яковлев', 0, 1, 0.00, 10000, NULL, NULL, NULL, NULL, 0, 0, 0, 0, '2020-12-20 18:45:38', '2020-12-20 18:45:38'),
(70, NULL, 21, 45, 'О226СВ', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 1, 1, 0, 11, 1, 2011, 0, 0, 0, NULL, 'Яковлев(на ремонте)', 0, 1, 0.00, 10000, NULL, NULL, NULL, NULL, 0, 0, 0, 0, '2020-12-20 18:45:38', '2020-12-20 18:45:38'),
(71, NULL, 22, 46, 'КК8542', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 1, 1, 0, 11, 20, 2007, 0, 0, 0, NULL, '', 0, 1, 0.00, 10000, NULL, NULL, NULL, NULL, 0, 0, 1, 0, '2020-12-20 18:59:35', '2020-12-20 18:59:35'),
(72, NULL, 23, 47, 'КК8613', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 1, 1, 0, 11, 21, 2013, 0, 0, 0, NULL, '', 0, 1, 0.00, 10000, NULL, NULL, NULL, NULL, 0, 0, 1, 0, '2020-12-20 18:59:36', '2020-12-20 18:59:36'),
(73, NULL, 24, 48, 'КК8351', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 1, 1, 0, 11, 22, 2007, 0, 0, 0, NULL, '', 0, 1, 0.00, 10000, NULL, NULL, NULL, NULL, 0, 0, 2, 0, '2020-12-20 18:59:36', '2020-12-20 18:59:36'),
(74, NULL, 25, 0, 'КК8268', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 1, 1, 0, 11, 23, 1984, 0, 0, 0, NULL, '', 0, 1, 0.00, 10000, NULL, NULL, NULL, NULL, 0, 0, 1, 0, '2020-12-20 18:59:36', '2020-12-20 18:59:36'),
(75, NULL, 26, 49, 'KХ3415', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 1, 1, 0, 11, 24, 2012, 0, 0, 0, NULL, '', 0, 1, 0.00, 10000, NULL, NULL, NULL, NULL, 0, 0, 2, 0, '2020-12-20 18:59:36', '2020-12-20 18:59:36'),
(76, NULL, 26, 50, 'KК1800', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 1, 1, 0, 11, 24, 2018, 0, 0, 0, NULL, '', 0, 1, 0.00, 10000, NULL, NULL, NULL, NULL, 0, 0, 2, 0, '2020-12-20 18:59:36', '2020-12-20 18:59:36'),
(77, NULL, 27, 51, 'КХ5333', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 1, 1, 0, 11, 25, 2001, 0, 0, 0, NULL, '', 0, 1, 0.00, 10000, NULL, NULL, NULL, NULL, 0, 0, 1, 0, '2020-12-20 19:00:19', '2020-12-20 19:00:19'),
(78, NULL, 28, 52, 'КХ5026', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 1, 1, 0, 11, 20, 2014, 0, 0, 0, NULL, '', 0, 1, 0.00, 10000, NULL, NULL, NULL, NULL, 0, 0, 1, 0, '2020-12-20 19:00:20', '2020-12-20 19:00:20'),
(79, NULL, 29, 53, 'КХ7206', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 1, 1, 0, 11, 20, 2006, 0, 0, 0, NULL, '', 0, 1, 0.00, 10000, NULL, NULL, NULL, NULL, 0, 0, 3, 0, '2020-12-20 19:00:20', '2020-12-20 19:00:20'),
(80, NULL, 30, 54, 'КО7710', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 1, 1, 0, 11, 26, 2009, 0, 0, 0, NULL, '', 0, 1, 0.00, 10000, NULL, NULL, NULL, NULL, 0, 0, 3, 0, '2020-12-20 19:00:20', '2020-12-20 19:00:20'),
(81, NULL, 18, 55, 'КК4843', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 1, 1, 0, 11, 27, 2001, 0, 0, 0, NULL, '', 0, 1, 0.00, 10000, NULL, NULL, NULL, NULL, 0, 0, 2, 0, '2020-12-20 19:00:20', '2020-12-20 19:00:20'),
(82, NULL, 31, 56, '3407КХ', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 1, 1, 0, 11, 28, 2008, 0, 0, 0, NULL, '6 мест', 0, 1, 0.00, 10000, NULL, NULL, NULL, NULL, 0, 0, 1, 0, '2020-12-20 19:00:20', '2020-12-20 19:00:20'),
(83, NULL, 32, 57, 'О740МН', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 1, 1, 0, 11, 29, 2000, 0, 0, 0, NULL, '7,5 м3', 0, 1, 0.00, 10000, NULL, NULL, NULL, NULL, 0, 0, 3, 0, '2020-12-20 19:00:20', '2020-12-20 19:00:20'),
(84, NULL, 31, 56, '3407КХ', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 1, 1, 0, 11, 28, 2008, 0, 0, 0, NULL, '', 0, 1, 0.00, 10000, NULL, NULL, NULL, NULL, 0, 0, 1, 0, '2020-12-20 19:00:20', '2020-12-20 19:00:20');
";
    mysqli_query($link, $sql);

    $sql = "INSERT INTO `s2i_klass` (`nomer`, `text`, `kod`) VALUES
(2, 'ПЕРВИЧНАЯ РЕГИСТРАЦИЯ ТС', 1),
(2, 'ЗАМЕНА СОБСТВЕННИКА', 2),
(2, 'ЗАМЕНА ГОСУДАРСТВЕННОГО РЕГИСТРАЦ. ЗНАКА', 3),
(2, 'РЕГИСТРАЦИЯ СНЯТЫХ С УЧЕТА ТС', 5),
(2, 'ВЫДАЧА ДУБЛИКАТА РЕГИСТРАЦИОННОГО ДОКУМЕНТА', 6),
(2, 'ЗАМЕНА НОМЕРНОГО АГРЕГАТА, ЦВЕТА И ПР.', 8),
(2, 'ВЫДАЧА ДУБЛИКАТА СВИДЕТЕЛЬСТВА О РЕГИСТРАЦИИ', 9),
(2, 'ЗАМЕНА ПТС', 10),
(2, 'ЗАМЕНА СОР В СВЯЗИ С УТЕРЕЙ', 11),
(2, 'ЗАМЕНА ДВС', 12),
(2, 'КОРРЕКЦИЯ ИНЫХ РЕКВИЗИТОВ', 13),
(3, 'НЕФАЗ', 1),
(3, 'ПАЗ', 2),
(3, 'КамАЗ', 3),
(3, 'LEXUS', 4),
(3, 'TOYOTA', 5),
(3, 'LADA', 6),
(3, 'MITSUBISHI', 7),
(3, 'JEEP', 8),
(3, 'KIA', 9),
(3, 'HONDA', 10),
(3, 'УРАЛ', 11),
(3, 'КАВЗ', 12),
(3, 'СЗАП', 13),
(3, 'BLUMHARDT SAL', 14),
(3, 'ТСП', 15),
(3, 'МТМ', 16),
(3, 'МАЗ', 17),
(3, 'ГАЗ', 18),
(3, 'FORD', 19),
(3, 'HYUNDAI', 20),
(3, 'УАЗ', 21),
(3, 'CATERPILLAR', 22),
(3, 'Погрузчик', 23),
(3, 'Амкодор', 24),
(3, 'ЕГЕРЬ', 25),
(3, 'CASE', 26),
(3, 'КУБОТА', 27),
(3, 'БТ', 28),
(3, 'Б', 29),
(3, 'ТР', 30),
(3, 'ТРЭКОЛ', 31),
(3, 'АТЗ', 32),
(4, '4208-11-13', 1),
(4, '320402-03', 2),
(4, '6520', 3),
(4, 'LX 570', 4),
(4, 'HILUX', 5),
(4, 'LARGUS', 6),
(4, 'L200', 7),
(4, 'LAND CRUISER 150', 8),
(4, 'GRAND CHEROKEE', 9),
(4, 'SPORTAGE', 10),
(4, 'CRV', 11),
(4, '32054', 12),
(4, '4234-05', 13),
(4, '3206-110', 14),
(4, '3255-0010-41', 15),
(4, '320402-03', 16),
(4, '397665', 17),
(4, '44108-10', 18),
(4, '44108-24', 19),
(4, '93272А', 20),
(4, '9334-10', 21),
(4, '38.22.122Е', 22),
(4, '94163-0000030', 23),
(4, '94163-0000031', 24),
(4, '933004', 25),
(4, '4320 КС-55713-3', 26),
(4, '5334 КС-3577-41', 27),
(4, '43118-15 КС-45721', 28),
(4, '43118-46 КС-55713', 29),
(4, '3307', 30),
(4, '27901', 31),
(4, '4795-0000010-33', 32),
(4, '47957-0000010-31', 33),
(4, '33086-47955-0000010', 34),
(4, '33081', 35),
(4, '43118-10 57350F', 36),
(4, '4320-31', 37),
(4, '780552', 38),
(4, '43118-46 (670460)', 39),
(4, '43118 39384Р', 40),
(4, '740300 А2585546', 41),
(4, '6520', 42),
(4, 'Transit', 43),
(4, 'Santa Fe', 44),
(4, 'Хантер', 45),
(4, 'D5N', 46),
(4, 'LG936', 47),
(4, '342B', 48),
(4, 'CX290B', 49),
(4, 'CX300C', 50),
(4, 'K022', 51),
(4, '170МБ1', 52),
(4, '170МБ 0121-2В4', 53),
(4, '20-23-02', 54),
(4, '3409 \"Бобр\"', 55),
(4, '39292', 56),
(4, '7,5-55576', 57),
(5, 'ЛЕГКОВОЙ', 1),
(5, 'АВТОБУС', 2),
(5, 'САМОСВАЛ', 3),
(5, 'ВАХТОВЫЙ АВТОМОБИЛЬ', 4),
(5, 'СЕДЕЛЬНЫЙ ТЯГАЧ', 5),
(5, 'ПЛОЩАДКА', 6),
(5, 'ТРАЛ', 7),
(5, 'ППЦ', 8),
(5, 'АВТОКРАН 25 тн', 9),
(5, 'АВТОМАСТЕРСКАЯ', 10),
(5, 'ПРМ', 11),
(5, 'СПЕЦМАСТЕРСКАЯ', 12),
(5, 'БОРТОВОЙ', 13),
(5, 'ФУРГОН', 14),
(5, 'БОРТОВОЙ с КМУ', 15),
(5, 'ГПА', 16),
(5, 'ГРУЗОВОЙ', 17),
(5, 'ПИКАП', 18),
(5, 'МИКРОАВТОБУС', 19),
(5, 'БУЛЬДОЗЕР', 20),
(5, 'ФРОНТАЛЬНЫЙ ПОГРУЗЧИК', 21),
(5, 'ПОГРУЗЧИК', 22),
(5, 'ТРАКТОРНОЕ СРЕДСТВО', 23),
(5, 'ЭКСКАВАТОР', 24),
(5, 'МИНИ-ЭКСКАВАТОР', 25),
(5, 'КРАН-ТРУБОУКЛАДЧИК', 26),
(5, 'ГУСЕНИЧНЫЙ ТЯГАЧ', 27),
(5, 'ВЕЗДЕХОД 6х6', 28),
(5, 'АВТОТОПЛИВОЗАПРАВЩИК', 29),
(5, 'АВТОКРАН 14 тн', 30),
(6, 'А/М БОРТОВОЙ', 1),
(6, 'АВТОБУС', 2),
(6, 'АВТОБУС 7 МЕСТ', 3),
(6, 'АВТОБУС ДО 20 СИДЯЧИХ МЕСТ', 4),
(6, 'АВТОБУС КЛАСС А', 5),
(6, 'АВТОБУС КЛАССА В (12 МЕСТ)', 6),
(6, 'АВТОБУС НА 10 МЕСТ', 7),
(6, 'АВТОБУС СВЫШЕ 20 СИДЯЧИХ МЕСТ', 8),
(6, 'АВТОБУСТ КАТЕГОРИИ М2 КЛАСС В', 9),
(6, 'АВТОГИДРОПОДЪЕМНИК', 10),
(6, 'АВТОМОБИЛЬ-САМОСВАЛ', 11),
(6, 'АВТОЦИСТЕРНА ДЛЯ ПЕРЕВОЗКИ МОЛОКА', 12),
(6, 'БУЛЬДОЗЕР', 13),
(6, 'ВНЕДОРОЖНОЕ ТС', 14),
(6, 'ГРУЗОВОЙ', 15),
(6, 'ГРУЗОВОЙ БОРТОВОЙ', 16),
(6, 'ГРУЗОВОЙ С БОРТОВОЙ ПЛАТФОРМОЙ', 17),
(6, 'ГРУЗОВОЙ ФУРГОН', 18),
(6, 'ГРУЗОВОЙ ФУРГОН ЦЕЛЬНО МЕТАЛЛИЧЕСКИЙ', 19),
(6, 'ГРУЗОВОЙ ФУРГОН ЦЕЛЬНОМЕТАЛЛИЧЕСКИЙ (3 МЕСТА)', 20),
(6, 'ГРУЗОВОЙ ФУРГОН ЦЕЛЬНОМЕТАЛЛИЧЕСКИЙ (7 МЕСТ)', 21),
(6, 'ГРУЗОПАССАЖИРСКИЙ А/М', 22),
(6, 'КРИМИНАЛИСТИЧЕСКАЯ ЛАБОРАТОРИЯ', 23),
(6, 'ЛЕГКОВОЙ', 24),
(6, 'МАШИНА ДОРОЖНАЯ СТРОИТЕЛЬНАЯ УНИВЕРСАЛЬНАЯ', 25),
(6, 'МАШИНА КОММУНАЛЬНО-СТРОИТЕЛЬНАЯ МНОГОЦЕЛЕВАЯ', 26),
(6, 'ОПЕРАТИВНО-СЛУЖЕБНЫЙ', 27),
(6, 'ПАТРУЛЬНЫЙ', 28),
(6, 'ПОГРУЗЧИК', 29),
(6, 'ПОГРУЗЧИК ОДНОКОВШОВЫЙ ФРОНТАЛЬНЫЙ', 30),
(6, 'ПОГРУЗЧИК ФРОНТАЛЬНЫЙ', 31),
(6, 'ПОЛУПРИЦЕП БОРТОВОЙ С ТЕНТОМ', 32),
(6, 'ПОЛУПРИЦЕП С ПОЛИВОМОЕЧНЫМ ОБОРУДОВАНИЕМ', 33),
(6, 'ПОЛУПРИЦЕП ТРАКТОРНЫЙ', 34),
(6, 'ПОЛУПРИЦЕП АВТОМОБИЛЬНЫЙ', 35),
(6, 'ПРИЦЕП ДЛЯ ПЕРЕВОЗКИ ВОДНОЙ ТЕХНИКИ', 36),
(6, 'ПРИЦЕП ДЛЯ ПЕРЕВОЗКИ ГРУЗОВ', 37),
(6, 'ПРИЦЕП ДЛЯ ПЕРЕВОЗКИ ЛОДОК', 38),
(6, 'ПРИЦЕП К ЛЕГКОВОМУ АВТОМОБИЛЮ', 39),
(6, 'ПРИЦЕП СПЕЦИАЛЬНЫЙ', 40),
(6, 'ПРИЦЕП-ЦИСТЕРНА', 41),
(6, 'ПРОЧИЙ СПЕЦИАЛЬНЫЙ АВТОМОБИЛЬ ЦВЕТОГРАФИЧЕСКАЯ СХЕМА А10 ПО ГОСТ Р 50574-02)', 42),
(6, 'ПРОЧИЙ СПЕЦИАЛЬНЫЙ АВТОМОБИЛЬ ЦВЕТОГРАФИЧЕСКАЯ СХЕМА А13 ПО ГОСТ Р 50574-02)', 43),
(6, 'ПРОЧИЙ СПЕЦИАЛЬНЫЙ АВТОМОБИЛЬ ЦВЕТОГРАФИЧЕСКАЯ СХЕМА А15 ПО ГОСТ Р 50574-02)', 44),
(6, 'САМОСВАЛ', 45),
(6, 'СНЕГОХОД', 46),
(6, 'СПЕЦИАЛИЗИРОВАННЫЙ БРОНИРОВАННЫЙ', 47),
(6, 'СПЕЦИАЛЬНОЕ', 48),
(6, 'СПЕЦИАЛЬНОЕ ПАССАЖИРСКОЕ ТРАНСПОРТНОЕ СРЕДСТВО', 49),
(6, 'СПЕЦИАЛЬНОЕ ПАССАЖИРСКОЕ ТРАНСПОРТНОЕ СРЕДСТВО (13 МЕСТ)', 50),
(6, 'СПЕЦИАЛЬНОЕ ТС ОПЕРАТИВНО СЛУЖЕБНОЕ', 51),
(6, 'СПЕЦИАЛЬНОЕ ТС-АВТОЗАК', 52),
(6, 'СПЕЦИАЛЬНОЕ ТС-ДЕЖУРНАЯ ЧАСТЬ', 53),
(6, 'СПЕЦИАЛЬНЫЙ 3309-АЗ', 54),
(6, 'СПЕЦИАЛЬНЫЙ 3302-АЗ', 55),
(6, 'СПЕЦИАЛЬНЫЙ СКП', 56),
(6, 'ТРАКТОР', 57),
(6, 'ТРАКТОР КОЛЕСНЫЙ', 58),
(6, 'ТЯГАЧ СЕДЕЛЬНЫЙ', 59),
(6, 'ФУРГОН', 60),
(6, 'ЭВАКУАТОР', 61),
(6, 'ЭЛЕКТРИЧЕСКИЙ ПОГРУЗЧИК', 62),
(6, 'СПЕЦИАЛИЗИРОВАННЫЕ, ПРОЧИЕ', 63),
(6, 'СПЕЦ.ПРОЧИЕ', 64),
(6, 'ПРОЧИЙ СПЕЦИАЛЬНЫЙ АВТОМОБИЛЬ ЦВЕТОГРАФИЧЕСКАЯ СХЕМА Р. А14 ПО ГОСТ Р50574-02', 65),
(6, 'ПРОЧИЙ СПЕЦИАЛЬНЫЙ АВТОМОБИЛЬ ЦВЕТОГРАФИЧЕСКАЯ СХЕМА  ПО ГОСТ Р50574-02', 66),
(6, 'ППСМ СПЕЦИАЛЬНЫЙ 392501', 67),
(6, 'ЭКСКАВАТОР', 68),
(6, 'ГРУЗОВОЙ ПРОЧЕЕ', 69),
(6, 'ЛАБОРАТОРИЯ ДОРОЖНОЙ ИНСПЕКЦИИ', 70),
(6, 'АВТОМОБИЛЬ-ЛАБОРАТОРИЯ', 71),
(6, 'МАШИНА ВАКУУМНАЯ', 72),
(6, 'АВТОМОБИЛЬ СПЕЦ. А21R23-АЗ', 73),
(6, 'ЛЕГКОВОЙ СЕДАН', 74),
(6, 'ГРУЗОВОЙ (САМОСВАЛ)', 75),
(6, 'ЛЕГКОВОЙ УНИВЕРСАЛ', 76),
(6, 'ЛЕГКОВОЙ ХЭТЧБЕК', 77),
(6, 'АВТОМОБИЛЬ ОПЕРАТИВНО-СЛУЖЕБНЫЙ \"ДЕЖУРНАЯ ЧАСТЬ\"', 78),
(6, 'АВТОМОБИЛЬ ПАТРУЛЬНЫЙ ГОСАВТОИНСПЕКЦИИ', 79),
(6, 'ЛЕГКОВОЙ, БРОНИРОВАННЫЙ', 81),
(6, 'АВТОБУС, СПЕЦИАЛЬНЫЙ, БРОНИРОВАННЫЙ', 82),
(6, 'МАШИНА КОММУНАЛЬНАЯ', 83),
(6, 'ПРИЦЕП Д/ПЕРЕВ. ГРУЗОВ И САМ. ТЕХНИКИ', 84),
(6, 'ГИДРОЦИКЛ', 85),
(6, 'СНЕГОБОЛОТОХОД', 86),
(6, 'СПЕЦИАЛЬНЫЙ АВТОЭВАКУАТОР', 87),
(6, 'СПЕЦИАЛИЗИРОВАННЫЙ', 88),
(7, 'АВТОБУС', 1),
(7, 'АВТОБУС ДО 16 СИДЯЧИХ МЕСТ', 2),
(7, 'АВТОБУС СВЫШЕ 16 СИДЯЧИХ МЕСТ', 3),
(7, 'ВНЕДОРОЖНОЕ ТС', 4),
(7, 'ГРУЗОВОЙ', 5),
(7, 'ГРУЗОВОЙ ДО 16 ТОНН', 6),
(7, 'ГРУЗОВОЙ СВЫШЕ 16 ТОНН', 7),
(7, 'ЛЕГКОВОЙ', 8),
(7, 'ПОГРУЗЧИК', 9),
(7, 'ПОЛУПРИЦЕП К ГРУЗОВОМУ А/М', 10),
(7, 'ПРИЦЕП', 11),
(7, 'ПРИЦЕП К ЛЕГКОВОМУ А/М', 12),
(7, 'СНЕГОХОД', 13),
(7, 'ТРАКТОР', 14),
(7, 'ГРУЗОВОЙ (САМОСВАЛ)', 15),
(8, 'ОПЕРАТИВНО-СЛУЖЕБНЫЕ', 1),
(8, 'ПРОЧИЕ', 2),
(8, 'ПАТРУЛЬНЫЕ', 3),
(9, 'N 1', 1),
(9, 'N 2', 2),
(9, 'N 3', 3),
(9, 'М 1', 4),
(9, 'М 2', 5),
(9, 'М 3', 6),
(9, 'О 1', 7),
(9, 'О 2', 8),
(9, 'О 4', 9),
(10, '\"УЗДЭУ АВТО\" УЗБЕКИСТАН', 1),
(10, 'АО \"КАФ\" РОССИЯ', 2),
(10, 'АООТ УАЗ', 3),
(10, 'ВАЗ', 4),
(10, 'ГАЗ', 5),
(10, 'ДАЕВУ (КОРЕЯ)', 6),
(10, 'ДМИТРОВСКИЙ ФИЛИАЛ ГУ НПО \"СТИС\" МВД РОССИИ', 7),
(10, 'ЗАО \" ФОРД МОТОР КОМПАНИ\" ', 8),
(10, 'ЗАО \"АВТОТОР\" РОССИЯ', 9),
(10, 'ЗАО \"ДЖИ ЭМ - АВТОВАЗ\" РОССИЯ', 10),
(10, 'ЗАО \"МЕТРОВАГОНМАШ\" РОССИЯ', 11),
(10, 'ЗАО \"ПРЕДПРИЯТИЕ \"ЭЛИС\"', 12),
(10, 'ЗАО \"ПРОИЗВОДСТВЕННОЕ ПРЕДПРИЯТИЕ \"ТЕХНИКА\"', 13),
(10, 'ЗАО \"ФОРД МОТОР КОМПАНИ\" РОССИЯ', 14),
(10, 'ЗАО \"ЭРМЗ\"', 15),
(10, 'ЗАО КОМПАНИЯ \"ИМЯ-М\" РОССИЯ ', 16),
(10, 'ИКАРУС (ВЕНГРИЯ)', 17),
(10, 'ИНЫЕ ПРЕДП. ИЗГ. \"ТОЙОТА\" ПОЛЬША', 18),
(10, 'МИЦУБИСИ МОТОРС КОРП ЯПОНИЯ', 19),
(10, 'ОАО \" УФИМСКОЕ МОТОРОСТРОИТЕЛЬНОЕ ПРОИЗВОДСТВЕННОЕ ОБЪЕДИНЕНИЕ\"', 20),
(10, 'ОАО \"ГОЛИЦЫНСКИЙ АВТОБУСНЫЙ ЗАВОД\" РОССИЯ', 21),
(10, 'ОАО \"ИЖАВТО\"', 22),
(10, 'ОАО \"КАМАЗ\"', 23),
(10, 'ОАО \"КАФ\" РОССИЯ', 24),
(10, 'ОАО \"КОММАШ\" РОССИЯ', 25),
(10, 'ОАО \"КУРГАНМАШЗАВОД\"', 26),
(10, 'ОАО \"КЭМЗ\"', 27),
(10, 'ОАО \"МАШИНОСТРОИТЕЛЬНЫЙ ЗАВОД ИМ. М.И. КАЛИНИНА, Г. ЕКАТЕРИНБУРГ\" РОССИЯ', 28),
(10, 'ОАО \"МЗ\"АРСЕНАЛ\" РОССИЯ', 29),
(10, 'ОАО \"НЕФАЗ\" БАШКОРТОСТАН', 30),
(10, 'ОАО \"РУССКАЯ МЕХАНИКА\"', 31),
(10, 'ОАО \"СОЛЛЕРС-НАБЕРЕЖНЫЕ ЧЕЛНЫ\"', 32),
(10, 'ОАО \"СРП №3\" РОССИЯ', 33),
(10, 'ОАО \"УАЗ\" РОССИЯ', 34),
(10, 'ОАО \"ЭКСПЕРТЦЕНТР\" РОССИЯ', 35),
(10, 'ОАО УЛЬЯНОВСКИЙ АВТОРЕМОНТНЫЙ ЗАВОД', 36),
(10, 'ООО \" ЯМАХА МОТОР СИ-АЙ-ЭС\"', 37),
(10, 'ООО \"АВТОДОМ\"', 38),
(10, 'ООО \"АВТОЛИК\" РОССИЯ', 39),
(10, 'ООО \"АКРО\" РОССИЯ', 40),
(10, 'ООО \"ВЕКТОР\"', 41),
(10, 'ООО \"ВЕТЕРАН-СПЕЦНАЗ-ПОДДЕРЖКА\"', 42),
(10, 'ООО \"ВОЛЖСКИЙ ПОГРУЗЧИК\"', 43),
(10, 'ООО \"КАВЗ\"', 44),
(10, 'ООО \"КОММУНСЕЛЬХОЗТЕХНИКА\"', 45),
(10, 'ООО \"ЛИАЗ\"', 46),
(10, 'ООО \"МЗСА\" РОССИЯ', 47),
(10, 'ООО \"НПП\"АВТОМАШ\"', 48),
(10, 'ООО \"ПАВЛОВСКИЙ АВТОБУСНЫЙ ЗАВОД\"', 49),
(10, 'ООО \"ПИНГО-АВТО\" РОССИЯ', 50),
(10, 'ООО \"ПРОИЗВОДСТВЕННОЕ ПРЕДПРИЯТИЕ \"СПЕЦТЕХНИКА\"', 51),
(10, 'ООО \"ПСП\" РОССИЯ', 52),
(10, 'ООО \"РОСТРАК\" РОССИЯ', 53),
(10, 'ООО \"САРЭКС-ТОРГОВЫЙ ДОМ\"', 54),
(10, 'ООО \"СДМ ТЕХНО\"', 55),
(10, 'ООО \"СОЛЛЕРС-ЕЛАБУГА\" РОССИЯ', 56),
(10, 'ООО \"СОЛЛЕРС-СА\" РОССИЯ', 57),
(10, 'ООО \"СОФТЭКСПЕРТ\"', 58),
(10, 'ООО \"СПРУТ\" РОССИЯ', 59),
(10, 'ООО \"СТ НИЖЕГОРОДЕЦ\" РОССИЯ', 60),
(10, 'ООО \"ТАГАЗ\" (РОССИЯ)', 61),
(10, 'ООО \"ТЕХНОКОР\" РОССИЯ', 62),
(10, 'ООО \"ТОЙОТА МОТОР МАНУФЭКЧУРИНГ РОССИЯ\"', 63),
(10, 'ООО \"ТОРГОВЫЙ ДОМ МТЗ-ЕЛАЗ\"', 64),
(10, 'ООО \"ТРАНСЛАЙН\"', 65),
(10, 'ООО \"ТРЕЙЛЕР\" РОССИЯ', 66),
(10, 'ООО \"УАЗ\" РОССИЯ', 67),
(10, 'ООО \"ФОЛЬКСВАГЕН ГРУП РУС\"', 68),
(10, 'ООО \"ФОРД СОЛЛЕРС ХОЛДИНГ\" РОССИЯ', 69),
(10, 'ООО \"ХММР\" РОССИЯ', 70),
(10, 'ООО \"ЦЕНТРАЛЬНЫЙ РЕГИОН\"', 71),
(10, 'ООО \"ЭКСПОТРАНС\" РОССИЯ', 72),
(10, 'ООО \"ЭЛЛАДА ИНТЕРТРЕЙД\" РОССИЯ', 73),
(10, 'ООО АЗК \"ТОР\" РОССИЯ', 74),
(10, 'ООО НПФ \"ТРЭКОЛ\"', 75),
(10, 'ООО ПКП \"КОМБИ-ЛЮКС\" РОССИЯ', 76),
(10, 'ПАО \"КАМАЗ\" РОССИЯ', 77),
(10, 'ПО \"МИНСКИЙ ТРАКТОРНЫЙ ЗАВОД\"', 78),
(10, 'СЕМЕНОВСКИЙ АО \"СЕМЕР\"', 79),
(10, 'ФГУП \"ГОЭСП-1 МВД РОССИИ\"', 80),
(10, 'ФГУП ПО \"УРАЛВАГОНЗАВОД\"', 81),
(10, 'ФОРД ВЕРКЕ ГМБХ (БЕЛЬГИЯ)', 82),
(10, 'ЧМЗАП', 83),
(10, 'ОАО \"ГАЗ\"', 84),
(10, 'ОАО \" АВТОВАЗ\"', 85),
(10, 'ООО\" АВТОМОБИЛЬНЫЙ ЗАВОД ГАЗ\"', 86),
(10, 'ОАО \"АВТОФРАМОС\"', 87),
(10, 'ООО \"ДЖИ ЭМ АВТО\"', 88),
(10, 'ГОСУДАРСТВЕННАЯ МЕЖРАЙОННАЯ ИНСПЕКЦИЯ ТЕХНАДЗОРА Г.СЫКТЫВКАРА И СЫКТЫВДИНСКОГО РАЙОНА', 89),
(10, 'ПАО \"АВТОВАЗ\"', 90),
(10, 'АС ПСА ВИС АВТО (РОССИЯ)', 91),
(10, 'ООО \"АВТОЗАВОД \"ГАЗ\"', 92),
(10, 'ООО \"РОСАВТО\" Г. ТОЛЬЯТТИ', 93),
(10, 'ООО \"ЛИГА-АВТО\"', 94),
(10, 'ОГИБДД УМВД РФ ПО Г. СЫКТЫВКАР', 95),
(10, 'САРАНСКИЙ ЭКСКАВАТОРНЫЙ ЗАВОД', 96),
(10, 'ОГИБДД ОМВД РФ ПО Г.ВОРКУТЕ', 97),
(10, 'РЭО ОГИБДД ОМВД РОССИИ ПО Г.УХТЕ', 98),
(10, 'ЗАО \"СААЗ АМО ЗИЛ\" РОССИЯ', 99),
(10, 'ИНЫЕ (РОССИЯ)', 100),
(10, 'ОАО СНПЦ \"РОСДОРТЕХ\" (РОССИЯ)', 101),
(10, 'ООО \"КРАСНОГОРСК-АВТО\"', 102),
(10, 'ЗАО \"НИИ СТТ\"', 103),
(10, 'ОАО \"ОРЕЛСТРОЙМАШ\"', 104),
(10, 'МРЭО ГАИ ЖДАНОВСКОГО РУВД', 105),
(10, 'ОГИБДД ОМВД РФ ПО Г.ИНТЕ', 106),
(10, 'ЗАО \"РЕНО РОССИЯ\"', 107),
(10, 'ООО \"ФОРД МОТОР\" (РОССИЯ)', 108),
(10, 'ООО \"СЕРКОНС\"', 109),
(10, 'ООО \"ГЛОБАЛ СТАНДАРТ\"', 110),
(10, 'ООО \"ТОЙОТА МОТОРС\"', 111),
(10, 'ООО \"ЦЕНТР ПОДГОТОВКИ И ПОДДЕРЖКИ \"ДВИЖЕНИЕ\"', 112),
(10, 'ООО \"АЛАРМ-МОТОРС ОЗЕРКИ\"', 113),
(10, 'ООО \"ТОЙОТА МОТОР\"', 114),
(10, 'АО \"ЧЛМЗ\"', 115),
(10, 'СЫКТЫВКАРСКИЙ УЧАСТОК ФКУ \"ЦЕНТР ГИМС МЧС РФ ПО РК\"', 116),
(10, 'ООО \"ЖУКОВСКИЙ ВЕЛОМОТОЗАВОД\"', 117),
(10, 'ГИБДД ЭЖВИНСКОГО РОВД', 118),
(10, 'ФИЛИАЛ ООО \"ТОЙОТА МОТОР\" В САНКТ-ПЕТЕРБУРГЕ', 119),
(10, 'АО \"ЛАДА ЗАПАД ТЛТ\"', 120),
(10, 'ООО \"РУСКОМТРАНС\"', 121),
(10, 'ООО \"ИМЯ-АВТО\"', 122),
(12, 'АВАНТЮРИН', 1),
(12, 'АВАНТЮРИН МЕТАЛЛИК', 2),
(12, 'АЙСБЕРГ', 3),
(12, 'АЛМАЗНОЕ СЕРЕБРО', 4),
(12, 'АРКТИКА', 5),
(12, 'БАЛТИКА', 6),
(12, 'БЕЛАЯ НОЧЬ', 7),
(12, 'БЕЛО-ЗЕЛЕНЫЙ', 8),
(12, 'БЕЛО-СЕРЫЙ', 9),
(12, 'БЕЛЫЙ', 10),
(12, 'БЕЛЫЙ (АЙСБЕРГ)', 11),
(12, 'БУРАН', 12),
(12, 'БУРАН/СЕРЕБРИСТЫЙ', 13),
(12, 'ВИШНЕВЫЙ', 14),
(12, 'ГОЛУБОЙ', 15),
(12, 'ГОСТ Р 50574-2002', 16),
(12, 'ГОСТ Р 50574-2002 РИС. А.10', 17),
(12, 'ГОСТ Р 50574-2002 РИС. А.13', 18),
(12, 'ГОСТ Р 50574-2002 РИС. А.14', 19),
(12, 'ГОСТ Р 50574-2002 РИС. А.15', 20),
(12, 'ГОСТ Р 50574-2002 РИС. А.16', 21),
(12, 'ГРАФИТОВЫЙ МЕТАЛЛИК', 22),
(12, 'ЖЕЛТО-СЕРЕБРИСТЫЙ МЕТАЛЛИК', 23),
(12, 'ЖЕЛТЫЙ', 24),
(12, 'ЗАЩИТНЫЙ', 25),
(12, 'ЗЕЛЕНЫЙ', 26),
(12, 'ЗОЛОТИСТЫЙ ТЕМНО-ЗЕЛЕНЫЙ', 27),
(12, 'КРАСНЫЙ', 28),
(12, 'КРАСНЫЙ МЕТАЛ.', 29),
(12, 'ЛАЗУРИТ', 30),
(12, 'ЛАСВЕГАС', 31),
(12, 'МЕДЕО', 32),
(12, 'МОР. БРИЗ', 33),
(12, 'НАУТИЛУС', 34),
(12, 'ОРАНЖЕВЫЙ', 35),
(12, 'СВ. ДЫМКА', 36),
(12, 'СВЕТЛО СЕРЫЙ', 37),
(12, 'СВЕТЛО-ГОЛУБОЙ', 38),
(12, 'СВЕТЛО-ДЫМЧАТЫЙ', 39),
(12, 'СВЕТЛО-СЕРЕБРИСТЫЙ МЕТАЛЛИК', 40),
(12, 'СЕРЕБРИСТО-БЕЖЕВЫЙ', 41),
(12, 'СЕРЕБРИСТО-ЖЕЛТО-ГОЛУБОЙ', 42),
(12, 'СЕРЕБРИСТО-ЖЕЛТЫЙ МЕТАЛЛИК', 43),
(12, 'СЕРЕБРИСТО-ТЕМНО-СИНИЙ', 44),
(12, 'СЕРЕБРИСТЫЙ', 45),
(12, 'СЕРЕБРИСТЫЙ КРАСНЫЙ', 46),
(12, 'СЕРЕБРИСТЫЙ МЕТАЛЛИК', 47),
(12, 'СЕРО-БЕЛЫЙ', 48),
(12, 'СЕРОЕ ОЛОВО', 49),
(12, 'СЕРО-СИНЕ-ЗЕЛЕНЫЙ', 50),
(12, 'СЕРО-СИНИЙ', 51),
(12, 'СЕРЫЙ', 52),
(12, 'СЕРЫЙ СЕРЕБРИСТЫЙ', 53),
(12, 'СИЛЬВЕР', 54),
(12, 'СИНЕ-ЗЕЛЕНЫЙ', 55),
(12, 'СИНЕ-ФИОЛЕТОВЫЙ', 56),
(12, 'СИНЕ-ЧЕРНЫЙ', 57),
(12, 'СИНИЙ', 58),
(12, 'СИНЯЯ ПОЛНОЧЬ', 59),
(12, 'СИРЕНЕВЫЙ МЕТ.', 60),
(12, 'СРЕДНИЙ СЕРО-ЗЕЛЕНЫЙ МЕТ.', 61),
(12, 'ТЕМНО-БОРДОВЫЙ', 62),
(12, 'ТЕМНО-ВИШНЕВЫЙ', 63),
(12, 'ТЕМНО-ГОЛУБОЙ', 64),
(12, 'ТЕМНО-ЗЕЛЕНЫЙ', 65),
(12, 'ТЕМНО-СЕРЫЙ', 66),
(12, 'ТЕМНО-СЕРЫЙ МЕТАЛЛИК', 67),
(12, 'ТЕМНО-СИНИЙ', 68),
(12, 'ТЕМНЫЙ СЕРО-ЗЕЛЕНЫЙ МЕТАЛЛИК', 69),
(12, 'ТЕМНЫЙ СИНЕ-ФИОЛЕТОВЫЙ', 70),
(12, 'ФИОЛЕТОВЫЙ', 71),
(12, 'ХАКИ', 72),
(12, 'ЧЕРНО-КРАСНЫЙ', 73),
(12, 'ЧЕРНО-ОРАНЖЕВЫЙ', 74),
(12, 'ЧЕРНО-СИНИЙ', 75),
(12, 'ЧЕРНО-СИНИЙ МЕТАЛЛИК', 76),
(12, 'ЧЕРНЫЙ', 77),
(12, 'ЧЕРНЫЙ ГРАФИТ', 78),
(12, 'ЧЕРНЫЙ МЕТАЛЛИК', 79),
(12, 'ЯРКО-БЕЛЫЙ', 80),
(12, 'СИНИЙ МЕТАЛЛИК', 81),
(12, 'СВЕТЛО-СЕРЫЙ МЕТАЛЛИК', 82),
(12, 'БЕЛО-ЧЕРНЫЙ', 83),
(12, 'ПЕСОЧНЫЙ', 84),
(12, 'БЕЖЕВЫЙ', 85),
(12, 'СЕРЫЙ МЕТАЛЛИК', 86),
(12, 'ТЕМНО-КОРИЧНЕВЫЙ', 87),
(12, 'СЕРО-БЕЖЕВЫЙ', 88),
(12, 'СЕРО-ГОЛУБОЙ', 89),
(12, 'КОРИЧНЕВЫЙ', 90),
(12, 'АМУЛЕТ-МЕТАЛЛИК', 91),
(12, 'СЕРЫЙ СТАЛЬНОЙ', 92),
(12, 'ГОСТ Р50574-2019', 93),
(12, 'СВЕТЛО-ЗЕЛЕНЫЙ', 94),
(12, 'БЕЛО-СИНИЙ', 95),
(12, 'ГОСТ Р 50574-2019 СХЕМА А 10', 96),
(12, 'БЕЛЫЙ НЕМЕТАЛЛИК', 97),
(12, 'МНОГОЦВЕТНЫЙ', 98),
(12, 'ОРАНЖЕВЫЙ RAL 2009', 99),
(13, '1. ПЛАНОВЫЙ', 1),
(13, '2. ВНЕПЛАНОВЫЙ', 2),
(14, 'ПРИКАЗ', 1),
(14, 'РАПОРТ', 2),
(15, 'СТРАХОВОЕ АКЦИОНЕРНОЕ ОБЩЕСТВО \"ВСК\"', 1),
(15, 'ПАО СК \"РОСГОССТРАХ\"', 2),
(15, 'АО \"ГОС. СК \"ЮГОРИЯ\"', 3),
(15, 'АО \"АЛЬФА СТРАХОВАНИЕ\"', 4),
(15, 'СПАО \"ИНГОССТРАХ\"', 5),
(16, 'ООО \"АВТОКОНТРОЛЬ\"', 1),
(16, 'ООО \"АВТОТЕХОСМОТР\"', 2),
(16, 'ООО \"НОБЕЛЬ-АВТО\"', 3),
(16, 'ООО \"СТО ПЕЧОРА\" (Г. ПЕЧОРА, УЛ. ЛЕСНАЯ, 2)', 4),
(16, 'НОВОЕ ТС', 5),
(16, 'ООО \"ЦЕНТРАВТОПРОМ\" (С. УСТЬ-КУЛОМ)', 6),
(16, 'ГОСУДАРСТВЕННАЯ ИНСПЕКЦИЯ ТЕХНАДЗОРА ПО ПРИЛУЗСКОМУ РАЙОНУ', 7),
(16, 'ГОСУДАРСТВЕННАЯ ИНСПЕКЦИЯ ТЕХНАДЗОРА ПО УСТЬ-КУЛОМСКОМУ РАЙОНУ', 8),
(16, 'ГОСУДАРСТВЕННАЯ ИНСПЕКЦИЯ ТЕХНАДЗОРА ПО Г. СЫКТЫВКАРУ И СЫКТЫВДИНСКОМУ РАЙОНУ', 9),
(16, 'ГОСУДАРСТВЕННАЯ ИНСПЕКЦИЯ ТЕХНАДЗОРА ПО ИНТИНСКОМУ И ПЕЧОРСКОМУ РАЙОНАМ', 10),
(16, 'ГОСУДАРСТВЕННАЯ ИНСПЕКЦИЯ ТЕХНАДЗОРА ПО Г. УХТЕ, Г. СОСНОГОРСКУ, Г. ВУКТЫЛУ И ТР-ПЕЧОРСКОМУ РАЙОНУ', 11),
(16, 'ГОСУДАРСТВЕННАЯ ИНСПЕКЦИЯ ТЕХНАДЗОРА ПО Г. УСИНСКУ', 12),
(16, 'ГОСУДАРСТВЕННАЯ ИНСПЕКЦИЯ ТЕХНАДЗОРА ПО Г. ВОРКУТЕ', 13),
(16, 'ГОСУДАРСТВЕННАЯ ИНСПЕКЦИЯ ТЕХНАДЗОРА ПО СЫСОЛЬСКОМУ И КОЙГОРОДСКОМУ РАЙОНАМ', 14),
(16, 'ГОСУДАРСТВЕННАЯ ИНСПЕКЦИЯ ТЕХНАДЗОРА ПО УСТЬ-ЦИЛЕМСКОМУ РАЙОНУ', 15),
(16, 'ГОСУДАРСТВЕННАЯ ИНСПЕКЦИЯ ТЕХНАДЗОРА ПО УСТЬ-ВЫМСКОМУ РАЙОНУ', 16),
(16, 'ГОСУДАРСТВЕННАЯ ИНСПЕКЦИЯ ТЕХНАДЗОРА ПО КНЯЖПОГОСТСКОМУ РАЙОНУ', 17),
(16, 'ГОСУДАРСТВЕННАЯ ИНСПЕКЦИЯ ТЕХНАДЗОРА ПО ИЖЕМСКОМУ РАЙОНУ', 18),
(16, 'ГОСУДАРСТВЕННАЯ ИНСПЕКЦИЯ ТЕХНАДЗОРА ПО УДОРСКОМУ РАЙОНУ', 19),
(16, 'ГИМС', 20),
(17, 'ТЕХНИЧЕСКИЙ ОСМОТР ТРАНСПОРТНОГО СРЕДСТВА', 1),
(17, 'ПЛАНОВОЕ СНЯТИЕ ПОКАЗАНИЙ СО СПИДОМЕТРА', 2),
(17, 'РЕМОНТ ТРАНСПОРТНОГО СРЕДСТВА', 3),
(18, 'ООО \"БОШ СЕРВИС ВЕГО\" (Г. СЫКТЫВКАР, УЛ. ПЕЧОРСКАЯ, 67/11)', 4),
(18, 'ООО \"АТП-ТОРГОВЛИ\" (Г. СЫКТЫВКАР, УЛ. ИНДУСТРИАЛЬНАЯ, 10)', 5),
(18, 'ООО \"АВТОГАРАНТСЕРВИС\" (Г. СЫКТЫВКАР, УЛ. ЗАВОДСКАЯ, 84)', 6),
(18, 'АТХ ФКУ \"ЦХИСО МВД ПО РК\"', 7),
(18, 'ООО \"К-СЕРВИС\" (Г. УХТА, УЛ. СЕВАСТОПОЛЬСКАЯ, 8)', 8),
(18, 'ООО \"ЛОГИСТИЧЕСКАЯ ПРОФЕССИОНАЛЬНАЯ ГРУППА\" (Г. УСИНСК УЛ. ПАРКОВАЯ, 20)', 9),
(18, 'ООО \"СТО УНИВЕРСАЛ\" (Г. СЫКТЫВКАР, УЛ. ЛЕСОПАРКОВАЯ, 32)', 10),
(18, 'ООО \"ГРАД\" (Г. ВОРКУТА, УЛ. МИРА, 2)', 11),
(18, 'ИП КАЛИНИЧЕНКО ДАРЬЯ ВЛАДИМИРОВНА (Г. ИНТА, УЛ. СТРОИТЕЛЬНАЯ) ЧЕРЕЗ ООО \"БОШ СЕРВИС ВЕГО\"', 12),
(18, 'САМОСТОЯТЕЛЬНЫЙ РЕМОНТ', 13),
(18, 'ООО «ИНТЭР-А» (Г. СОСНОГОРСК, УЛ. ОКТЯБРЬСКАЯ, Д.4)', 14),
(18, 'ООО \"ЕВРОЛЮКС\" (Г. УХТА, УЛ. ЗАПАДНАЯ, Д.16)', 16),
(18, 'ООО \"АВТОДОК\" (Г. СЫКТЫВКАР, УЛ. МОРОЗОВА, 108/2)', 17),
(19, 'ДВИГАТЕЛЬ', 1),
(19, 'КУЗОВ', 2),
(19, 'ШАССИ', 3),
(19, 'ЭЛЕКТРООБОРУДОВАНИЕ', 4),
(20, '1. ПРИХОД', 1),
(20, '2. РАСХОД', 2),
(21, '1. ДИАГНОСТИКА', 1),
(21, '2. ПОКРАСКА', 2),
(21, 'ТО', 3),
(22, 'ОГИБДД УМВД РОССИИ ПО Г. СЫКТЫВКАРУ', 1),
(22, 'ОГИБДД ОМВД РОССИИ ПО Г. УХТЕ', 2),
(22, 'ОГИБДД МОМВД РФ \"СЫСОЛЬСКИЙ\"', 3),
(22, 'ГОСУДАРСТВЕННАЯ МЕЖРАЙОННАЯ ИНСПЕКЦИЯ ТЕХНАДЗОРА Г.СЫКТЫВКАРА И СЫКТЫВДИНСКОГО РАЙОНА', 4),
(22, 'ОГИБДД ОМВД РФ ПО ПРИЛУЗСКОМУ РАЙОНУ', 5),
(22, 'ОГИБДД ОМВД РФ ПО УСТЬ-КУЛОМСКОМУ  Р-НУ', 6),
(22, 'ОГИБДД ОМВД РФ ПО УСТЬ-ВЫМСКОМУ Р-НУ', 7),
(22, 'УГИБДД МВД ПО РЕСПУБЛИКЕ КОМИ', 8),
(22, 'ОГИБДД ОМВД РФ ПО КНЯЖПОГОСТСКОМУ РАЙОНУ', 9),
(22, 'ОМВД РФ ПО Г. ИНТЕ', 10),
(22, 'ОГИБДД ОМВД РФ ПО Г. ПЕЧОРЕ', 11),
(22, 'ОМВД РФ ПО ТРОИЦКО-ПЕЧОРСКОМУ РАЙОНУ', 12),
(22, 'ОМВД РФ ПО УДОРСКОМУ РАЙОНУ', 13),
(22, 'ОГИБДД ОВМД РФ ПО Г. СОСНОГОРСКУ', 14),
(22, 'ОГИБДД ОМВД РФ ПО ИЖЕМСКОМУ РАЙОНУ', 15),
(22, 'ОГИБДД ОМВД РФ ПО УСТЬ-ЦИЛЕМСКОМУ РАЙОНУ', 16),
(22, 'ОГИБДД ОМВД РФ ПО Г.ВУКТЫЛУ', 17),
(22, 'ОГИБДД ОМВД РФ ПО Г. УСИСНКУ', 18),
(22, 'ОГИБДД ОМВД РФ ПО Г. ВОРКУТЕ', 19),
(22, 'РЭО ОГИБДД ОМВД РФ ПО Г. УХТЕ', 20),
(22, 'ООО \"ДЖИ ЭМ АВТО\"', 21),
(23, 'ПРИКАЗ', 1),
(23, 'АКТ', 2),
(23, 'ПРОЧЕЕ', 3),
(23, 'ДОГОВОР', 4),
(23, 'НАКЛАДНАЯ', 5),
(23, 'СВИДЕТЕЛЬСТВО ОБ УТИЛИЗАЦИИ', 6),
(24, 'О ПЕРЕДАЧЕ', 1),
(24, 'О ВВОДЕ В ЭКСПЛУАТАЦИЮ', 2),
(24, 'О ВЫВОДЕ ЗА ШТАТ', 3),
(24, 'О ВЫВОДЕ ИЗ ЭКСПЛУАТАЦИИ', 4),
(24, 'О СНЯТИИ С БАЛАНСОВОГО УЧЕТА', 5),
(24, 'ОБ УТВЕРЖДЕНИИ НОРМ ТОПЛИВА', 6),
(24, 'О СДАЧЕ В МЕТАЛЛОЛЛОМ', 7),
(24, 'О ЗАМЕНЕ СПИДОМЕТРА', 8),
(25, 'B', 1),
(25, 'C', 2),
(25, 'D', 3),
(26, '12.1 ч.1', 1),
(26, '12.1 ч.1.1', 2),
(26, '12.1 ч.2', 3),
(26, '12.2 ч.1', 4),
(26, '12.2 ч.2', 5),
(26, '12.2 ч.3', 6),
(26, '12.2 ч.4', 7),
(26, '12.3 ч.1', 8),
(26, '12.3 ч.2', 9),
(26, '12.3 ч.2.1', 10),
(26, '12.3 ч.3', 11),
(26, '12.4 ч.1', 12),
(26, '12.4 ч.2', 13),
(26, '12.4 ч.3', 14),
(26, '12.5 ч.1', 15),
(26, '12.5 ч.2', 16),
(26, '12.5 ч.3', 17),
(26, '12.5 ч.3.1', 18),
(26, '12.5 ч.4', 19),
(26, '12.5 ч.4.1', 20),
(26, '12.5 ч.5', 21),
(26, '12.5 ч.6', 22),
(26, '12.5 ч.7', 23),
(26, '12.6', 24),
(26, '12.7 ч.1', 25),
(26, '12.7 ч.2', 26),
(26, '12.7 ч.3', 27),
(26, '12.8 ч.1', 28),
(26, '12.8 ч.2', 29),
(26, '12.8 ч.3', 30),
(26, '12.9 ч.2', 31),
(26, '12.9 ч.3', 32),
(26, '12.9 ч.4', 33),
(26, '12.9 ч.5', 34),
(26, '12.9 ч.6', 35),
(26, '12.9 ч.7', 36),
(26, '12.10 ч.1', 37),
(26, '12.10 ч.2', 38),
(26, '12.10 ч.3', 39),
(26, '12.11 ч.1', 40),
(26, '12.11 ч.2', 41),
(26, '12.11 ч.3', 42),
(26, '12.12 ч.1', 43),
(26, '12.12 ч.2', 44),
(26, '12.12 ч.3', 45),
(26, '12.13 ч.1', 46),
(26, '12.13 ч.2', 47),
(26, '12.14 ч.1', 48),
(26, '12.14 ч.1.1', 49),
(26, '12.14 ч.2', 50),
(26, '12.14 ч.3', 51),
(26, '12.15 ч.1', 52),
(26, '12.15 ч.1.1', 53),
(26, '12.15 ч.2', 54),
(26, '12.15 ч.3', 55),
(26, '12.15 ч.4', 56),
(26, '12.15 ч.5', 57),
(26, '12.16 ч.1', 58),
(26, '12.16 ч.2', 59),
(26, '12.16 ч.3', 60),
(26, '12.16 ч.3.1', 61),
(26, '12.16 ч.4', 62),
(26, '12.16 ч.5', 63),
(26, '12.16 ч.6', 64),
(26, '12.16 ч.7', 65),
(26, '12.17 ч.1', 66),
(26, '12.17 ч.1.1', 67),
(26, '12.17 ч.1.2', 68),
(26, '12.17 ч.2', 69),
(26, '12.18', 70),
(26, '12.19 ч.1', 71),
(26, '12.19 ч.2', 72),
(26, '12.19 ч.3', 73),
(26, '12.19 ч.3.1', 74),
(26, '12.19 ч.3.2', 75),
(26, '12.19 ч.4', 76),
(26, '12.19 ч.5', 77),
(26, '12.19 ч.6', 78),
(26, '12.20', 79),
(26, '12.21 ч.1', 80),
(26, '12.21.1 ч.1', 81),
(26, '12.21.1 ч.2', 82),
(26, '12.21.1.3 ч.3', 83),
(26, '12.21.1 ч.4', 84),
(26, '12.21.1 ч.5', 85),
(26, '12.21.1 ч.6', 86),
(26, '12.21.1 ч.7', 87),
(26, '12.21.1 ч.8', 88),
(26, '12.21.1 ч.9', 89),
(26, '12.21.1 ч.10', 90),
(26, '12.21.1 ч.11', 91),
(26, '12.21.2 ч.1', 92),
(26, '12.21.2 ч.2', 93),
(26, '12.22', 94),
(26, '12.23 ч.1', 95),
(26, '12.23 ч.2', 96),
(26, '12.23 ч.3', 97),
(26, '12.24 ч.1', 98),
(26, '12.24 ч.2', 99),
(26, '12.25 ч.1', 100),
(26, '12.25 ч.2', 101),
(26, '12.25 ч.3', 102),
(26, '12.26 ч.1', 103),
(26, '12.26 ч.1', 104),
(26, '12.26 ч.2', 105),
(26, '12.27 ч.1', 106),
(26, '12.27 ч.2', 107),
(26, '12.27 ч.3', 108),
(26, '12.28 ч.1', 109),
(26, '12.28 ч.2', 110),
(26, '12.29 ч.1', 111),
(26, '12.29 ч.2', 112),
(26, '12.29 ч.3', 113),
(26, '12.31.1 ч.2', 114),
(26, '12.31.1 ч.3', 115),
(26, '12.32', 116),
(26, '12.32.1', 117),
(26, '12.33', 118),
(26, '12.34', 119),
(26, '12.35', 120),
(26, '12.36.1', 121),
(26, '12.37 ч.1', 122),
(26, '12.37 ч.2', 123),
(26, '12.3 ч.1', 124),
(26, '12.3 ч.2', 125),
(26, '12.3 ч.2.1', 126),
(26, '12.3 ч.3', 127),
(26, '12.30 ч.1', 128),
(26, '12.30 ч.2', 129),
(26, '12.31 ч.1', 130),
(26, '12.31 ч.2', 131),
(26, '12.31 ч.3', 132),
(26, '12.31 ч.4', 133),
(26, '12.31.1 ч.1', 134),
(27, 'Лето', 1),
(27, 'Зима', 2),
(28, 'Основное', 1),
(28, 'Запасное', 2),
(29, 'Марка', 1),
(29, 'КАМА', 2),
(29, 'NOKIAN', 3),
(29, 'YOKOHAMA', 4),
(29, 'FORMULA', 5),
(29, 'CORDIANT', 6),
(29, 'BELSHINA', 7),
(29, 'MARSHAL', 8),
(29, 'HANKOOK', 9),
(29, 'AMTEL', 10),
(29, 'KC-4', 11),
(29, 'G-FORCE', 12),
(29, 'MAXXIS', 13),
(29, 'DUNLOP', 14),
(29, 'MICHELIN', 15),
(29, 'ROSAVA', 16),
(29, 'KUMHO', 17),
(29, 'NORDMAN', 18),
(29, 'VIATTI', 19),
(29, 'TIGAN', 20),
(29, 'MATADOR', 21),
(29, 'MAXXIS', 22),
(29, 'GOODYEAR', 23),
(29, 'BARGUZIN', 24),
(29, 'TUNGA', 25),
(29, 'K-244', 26),
(29, 'KORMARAN', 27),
(29, 'К-153', 28),
(29, 'К-155', 29),
(29, 'KINGSTAR', 30),
(30, 'Модель', 1),
(30, 'ЕВРО', 2),
(30, 'HAKKAPELITTA 7', 3),
(30, '-', 4),
(30, 'ENERGY', 5),
(30, 'ЕВРО-219', 6),
(30, 'ЕВРО-131', 7),
(30, '264', 8),
(30, 'ЕВРО-519', 9),
(30, 'К-715', 10),
(30, 'PLANET', 11),
(30, 'ROAD RUNNER', 12),
(30, 'GO', 13),
(30, 'AT-771', 14),
(30, 'GR TREK ICE', 15),
(30, 'LCV-131', 16),
(30, 'BREEZE', 17),
(30, 'NM EVO', 18),
(30, 'ЕВРО-228', 19),
(30, 'ICE', 20),
(30, 'X-ICH NORTH', 21),
(30, 'PREMIUM', 22),
(30, '221', 23),
(30, 'V-522', 24),
(30, 'MP-47', 25),
(30, 'NP-3', 26),
(30, 'OPTIMO', 27),
(30, 'ЕВРО-224', 28),
(30, '505', 29),
(30, 'K-175', 30),
(30, 'ZODIAK-2', 31),
(30, 'STANDART', 32),
(30, '205', 33),
(30, 'ЕВРО-129', 34),
(30, 'HAKKAPELITA 4', 35),
(30, 'ENERGY', 36),
(30, 'ROAD PERFOMANCE', 37),
(30, 'KW-22', 38),
(30, 'SPORT 3', 39),
(31, 'Размерность', 1),
(31, '175/65R14', 2),
(31, '205/65R16', 3),
(31, '185/65R15', 4),
(31, '195/65R15', 5),
(31, '205/75R16С', 6),
(31, '185/65R14', 7),
(31, '215/60R16', 8),
(31, '175/70R14', 9),
(31, '285/65R17', 10),
(31, '275/65R17', 11),
(31, '195/75R16C', 12),
(31, '225/75R16', 13),
(31, '225/70R16', 14),
(31, '205/70R15', 15),
(31, '205/75R15', 16),
(31, '235/70R16', 17),
(31, '245/70R16', 18),
(31, '205/55R16', 19),
(31, '205/60R16', 20),
(31, '215/50R17', 21),
(31, '185/60R14', 22),
(31, '225/75R17', 23),
(31, '225/55R17', 24),
(31, '235/75R16', 25),
(31, '175/70R13', 26),
(31, '215/55R16', 27),
(31, '225/55R16', 28),
(32, 'Машинист крана', 1),
(32, 'Машинист крана-манипулятора', 2),
(33, 'ООО \"Учебный центр\"', 1),
(34, 'Организация, выдавшая ДОПОГ', 1),
(35, 'Просрочен срок', 1),
(35, 'Предупреждения', 2),
(35, 'Уведомления', 3),
(36, 'Водители. Водительские удостоверения', 1),
(36, 'Водители. Удостоверение тракториста-машиниста', 2),
(36, 'Водители. Удостоверение ДОПОГ', 3),
(36, 'Транспортные средства. ОСАГО', 4),
(36, 'Транспортные средства. Тех. осмотр', 5),
(36, 'Транспортные средства. Удостоверение ДОПОГ', 6),
(36, 'Транспортные средства. Калибровка (экспертиза)', 7),
(36, 'Транспортные средства. Огнетушитель', 8),
(36, 'Транспортные средства. Аптечка', 9),
(36, 'Транспортные средства. Замена масла', 10),
(37, 'Фирма, которая произвела калибровку', 1),
(38, 'Фирма, которая выдала карту водителя', 1),
(39, 'Модель тахографа', 1),
(40, 'ООО \"Портал\"', 1),
(40, 'ООО \"Квартет-Сервис\"', 2),
(40, 'Гаврилов К.С.', 3);";
    mysqli_query($link, $sql);

    $sql = "INSERT INTO `role` (`id`, `category`, `text`) VALUES
(1, 1, 'Пользователь (режим просмотра)'),
(2, 2, 'Оператор'),
(3, 3, 'Оператор АТХ'),
(4, 8, 'Администратор'),
(5, 9, 'Системный администратор'),
(6, 4, 'Оператор (урезанный)');";
    mysqli_query($link, $sql);

    $sql = "INSERT INTO `spr_list` (`id`, `nomer`, `text`, `type`) VALUES
(1, 1, '1. СЛУЖБЫ', 1),
(2, 2, '2. ТЕХНОЛОГИЧЕСКАЯ ОПЕРАЦИЯ', 0),
(3, 3, '3. МАРКА ТРАНСПОРТНОГО СРЕДСТВА', 0),
(4, 4, '4. МОДЕЛЬ ТРАСПОРТНОГО СРЕДСТВА', 0),
(5, 5, '5. КАТЕГОРИЯ ТРАНСПОРТНОГО СРЕДСТВА', 0),
(6, 6, '6. ТИП ТРАНСПОРТНОГО СРЕДСТВА', 0),
(7, 7, '7. ТИП СТРАХОВКИ ТРАНСПОРТНОГО СРЕДСТВА', 0),
(8, 8, '8. КАТЕГОРИЯ ТРАНСПОРТНОГО СРЕДСТВА В МВД', 0),
(9, 9, '9. КАТЕГОРИЯ ТРАНСПОРТНОГО СРЕДСТВА ПО ГОСТ', 0),
(10, 10, '10. ПРОИЗВОДИТЕЛЬ ТРАНСПОРТНОГО СРЕДСТВА (ОРГАНИЗАЦИЯ ВЫДАВШАЯ ПТС)', 0),
(11, 11, '11. РАЙОНЫ', 1),
(12, 12, '12. ЦВЕТ ТРАНСПОРТНОГО СРЕДСТВА', 0),
(13, 13, '13. ТИП ЭКЗАМЕНА ПО ЗНАНИЮ ПДД', 0),
(14, 14, '14. ОСНОВАНИЕ ЗАКРЕПЛЕНИЯ', 0),
(15, 15, '15. СТРАХОВЫЕ КОМПАНИИ', 0),
(16, 16, '16. СТАНЦИИ ТЕХНИЧЕСКОГО ОСМОТРА', 0),
(17, 17, '17. ОСНОВАНИЕ СНЯТИЯ СВЕДЕНИЙ С СПИДОМЕТРА', 0),
(18, 18, '18. СТАНЦИИ РЕМОНТА ТРАНСПОРТНЫХ СРЕДСТВ', 0),
(19, 19, '19. КАТЕГОРИЯ ЗАПЧАСТЕЙ (ОСНОВНЫЕ ЧАСТИ ТРАНСПОРТНОГО СРЕДСТВА)', 0),
(20, 20, '20. ОПЕРАЦИИ ПРИХОД/РАСХОД ЗАПЧАСТЕЙ', 1),
(21, 21, '21. УСЛУГИ, ОКАЗАННЫЕ В РАМКАХ РЕМОНТА', 0),
(22, 22, '22. ОРГАН, ВЫДАВШИЙ СВИДЕТЕЛЬСТВО О РЕГИСТРАЦИИ ТРАНСПОРТНОГО СРЕДСТВА', 0),
(23, 23, '23. ТИП ДОКУМЕНТА НА ТРАНСПОРТНОЕ СРЕДСТВО', 0),
(24, 24, '24. СОДЕРЖАНИЕ ДОКУМЕНТОВ', 0),
(25, 25, '25. КАТЕГОРИЯ ТС ДЛЯ РАЗРЕШЕНИЯ НА СПЕЦСИГНАЛЫ', 0),
(26, 26, '26. Глава 12 КоАП РФ \"Безопасность дорожного движения\"', 0),
(27, 27, '27. СЕЗОННОСТЬ ШИН', 0),
(28, 28, '28. ТИП ШИН', 0),
(29, 29, '29. МАРКА ШИН', 0),
(30, 30, '30. МОДЕЛЬ ШИН', 0),
(31, 31, '31. РАЗМЕРНОСТЬ', 0),
(32, 32, '32. Фирма, выдавшая удостоверение на кран', 0),
(33, 33, '33. Квалификация для удостоверения на кран', 0),
(34, 34, '34. Фирма, выдавшая свидетельство ДОПОГ', 0),
(35, 35, '35. Важность уведомлений', 1),
(36, 36, '36. Подсистемы для уведомлений', 1),
(37, 37, '37. Фирма, производившая калибровку', 0),
(38, 38, '38. Кем выдана карта водителя', 0),
(39, 39, '39. Модель тахографа', 0),
(40, 40, '40. Собственник транспортного средства', 0);";
    mysqli_query($link, $sql);

    $sql = "INSERT INTO `users` (`id`, `fam`, `imj`, `otch`, `login`, `slugba`, `passwd_hash`, `hash`, `role`, `access`, `block`, `notice_events`, `dt_reg`, `date_last_login`) VALUES
(1, 'АДМИНИСТРАТОР', 'АДМИНИСТРАТОР', 'АДМИНИСТРАТОР', 'admin', 0, '" . $this->admin_hash_password . "', '73edc05c50a3a65b3688aa6b974af5234b7d62f937a81522e38344bbe07c675f', 9, 1, 0, '', '2019-03-11 00:00:00', '2020-11-01 16:59:36');";

    mysqli_query($link, $sql);
  }
}