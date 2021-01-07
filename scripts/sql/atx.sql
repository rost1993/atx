CREATE TABLE IF NOT EXISTS `car_maintenance` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'Уникальный ID ТС',
  `id_car` int(11) NOT NULL DEFAULT '0' COMMENT 'ID ТС',
  `date_maintenance` date DEFAULT NULL COMMENT 'Дата тех. обслуживания',
  `mileage_maintenance` double(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Пробег тех. обслуживания',
  `path_to_file` varchar(100) DEFAULT NULL COMMENT 'Путь к файлу',
  `file_extension` varchar(50) DEFAULT NULL COMMENT 'Расширение файла',
  `ibd_arx` int(11) NOT NULL DEFAULT '1' COMMENT 'Архив',
  `sh_polz` int(11) NOT NULL DEFAULT '0' COMMENT 'Шифр пользователя',
  `dt_reg` int(11) DEFAULT NULL COMMENT 'Дата ввода',
  `dt_izm` int(11) DEFAULT NULL COMMENT 'Дата корректировки'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Техническое обслуживание ТС';

$$
DROP TRIGGER IF EXISTS ins_car_maintenance;

$$
CREATE TRIGGER `ins_car_maintenance` BEFORE INSERT ON `car_maintenance` FOR EACH ROW BEGIN
SET new.dt_reg = NOW();
SET new.dt_izm = NOW();
CALL add_testimony_speedometer(1, new.id_car, new.mileage_maintenance, 0, new.date_maintenance, NULL, new.sh_polz, 3);
END;

$$
DROP TRIGGER IF EXISTS upd_car_maintenance;

$$
CREATE TRIGGER `upd_car_maintenance` BEFORE UPDATE ON `car_maintenance` FOR EACH ROW BEGIN
SET new.dt_izm = NOW();
IF(new.mileage_maintenance <> old.mileage_maintenance OR new.date_maintenance <> old.date_maintenance) THEN
CALL add_testimony_speedometer(2, new.id_car, new.mileage_maintenance, old.mileage_maintenance, new.date_maintenance, old.date_maintenance, new.sh_polz, 3);
END IF;
END;

$$
DROP INDEX IF EXISTS `ind_car_maintenance` ON `car_maintenance`;

$$
ALTER TABLE `car_maintenance` ADD PRIMARY KEY (`id`), ADD KEY `ind_car_maintenance` (`id_car`,`date_maintenance`,`ibd_arx`);

$$
ALTER TABLE `car_maintenance` MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Уникальный ID ТС', AUTO_INCREMENT=1;

$$
DROP TRIGGER IF EXISTS trigger_delete_cars;

$$
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
  DELETE FROM car_maintenance WHERE id_car = OLD.id;
  DELETE FROM files WHERE category_file = 13 AND id_object = OLD.id;
  UPDATE dtp SET id_car = 0 WHERE id_car = OLD.id;
  UPDATE adm_offense SET id_car = 0 WHERE id_car = OLD.id;
  UPDATE car_repair SET id_car = 0 WHERE id_car = OLD.id;
END;