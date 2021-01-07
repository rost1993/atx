$$
DROP PROCEDURE IF EXISTS `add_testimony_speedometer`;

$$
CREATE PROCEDURE `add_testimony_speedometer`(IN `ind` INT, IN `car` INT, IN `testimony` INT, IN `old_testimony` INT, IN `date_testimony` DATE, IN `old_date_testimony` DATE, IN `polz` INT, IN `type_operation` INT) NOT DETERMINISTIC NO SQL SQL SECURITY DEFINER BEGIN
DECLARE num_speed INT;
DECLARE reason INT;
DECLARE id_speed INT;

IF(type_operation = 1) THEN
  SELECT kod INTO reason FROM s2i_klass WHERE nomer=17 AND UPPER(text) LIKE '%РЕМОНТ%' LIMIT 1;
ELSEIF(type_operation = 2) THEN
  SELECT kod INTO reason FROM s2i_klass WHERE nomer=17 AND UPPER(text) LIKE '%ТЕХНИЧЕСКИЙ ОСМОТР%' LIMIT 1;
ELSE
  SELECT kod INTO reason FROM s2i_klass WHERE nomer=17 AND text LIKE '%ТЕХНИЧЕСКОЕ ОБСЛУЖИВАНИЕ%' LIMIT 1;
END IF;

IF (ind = 1) THEN
  SELECT num_speedometer INTO num_speed FROM cars WHERE id=car;
  INSERT INTO speedometer (id_car, id_speedometer, testimony_speedometer, reason_speedometer, date_speedometer, sh_polz) VALUES (car, num_speed, testimony, reason, date_testimony, polz);
END IF;

IF(ind = 2) THEN
  SELECT IFNULL(id, 0), IFNULL(id_speedometer, 0) INTO id_speed, num_speed FROM speedometer WHERE id_car=car AND testimony_speedometer=old_testimony AND date_speedometer = old_date_testimony LIMIT 1;

  IF(id_speed IS NULL) THEN
    SELECT num_speedometer INTO num_speed FROM cars WHERE id=car LIMIT 1;
    INSERT INTO speedometer (id_car, id_speedometer, testimony_speedometer, reason_speedometer, date_speedometer, sh_polz) VALUES (car, num_speed, testimony, reason, date_testimony, polz);
  ELSE
    UPDATE speedometer SET id_speedometer=num_speed, testimony_speedometer=testimony, reason_speedometer=reason, date_speedometer=date_testimony, sh_polz=polz WHERE id=id_speed;
  END IF;

END IF;

CALL move_to_archive(car, 3);
END

$$
INSERT INTO `s2i_klass` (`nomer`, `text`, `kod`) SELECT '17', 'ТЕХНИЧЕСКОЕ ОБСЛУЖИВАНИЕ ТРАНСПОРТНОГО СРЕДСТВА', IFNULL(MAX(kod), 0)+1 FROM `s2i_klass` WHERE nomer=17;

$$
ALTER TABLE `technical_inspection` MODIFY id INT NOT NULL;

$$
ALTER TABLE `technical_inspection` DROP INDEX IF EXISTS `PRIMARY`;

$$
ALTER TABLE `technical_inspection` DROP INDEX IF EXISTS `UNIQ_IND_TECHNICAL_SERTIFICATE`;

$$
ALTER TABLE `technical_inspection` DROP INDEX IF EXISTS `IND_TECHNICAL_SERTIFICATE`;

$$
ALTER TABLE `technical_inspection` ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `UNIQ_IND_TECHNICAL_SERTIFICATE` (`id_car`,`date_certificate`), ADD KEY `IND_TECHNICAL_SERTIFICATE` (`ibd_arx`,`end_date_certificate`);

$$
ALTER TABLE `technical_inspection` MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Уникальный номер', AUTO_INCREMENT=1;

$$
DROP TRIGGER IF EXISTS insert_table_technical_inspection;

$$
DROP TRIGGER IF EXISTS update_table_technical_inspection;

$$
CREATE TRIGGER `insert_table_technical_inspection` BEFORE INSERT ON `technical_inspection` FOR EACH ROW BEGIN
SET new.dt_reg=now();
SET new.dt_izm=now();
CALL add_testimony_speedometer(1, new.id_car, new.car_mileage, 0, new.date_certificate, null, new.sh_polz, 2);
END;

$$
CREATE TRIGGER `update_table_technical_inspection` BEFORE UPDATE ON `technical_inspection` FOR EACH ROW BEGIN
SET new.dt_izm=now();
IF(new.car_mileage <> old.car_mileage OR new.date_certificate <> old.date_certificate) THEN
    CALL add_testimony_speedometer(2, new.id_car, new.car_mileage, old.car_mileage, new.date_certificate, old.date_certificate, new.sh_polz, 2);
    END IF;
END;

$$
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

$$
DROP PROCEDURE IF EXISTS move_to_archive;

$$
CREATE PROCEDURE `move_to_archive` (IN `id_item` INT, IN `ind` INT)  NO SQL
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

IF(ind = 21) THEN
	SELECT MAX(date_maintenance) INTO dd FROM car_maintenance WHERE id_car = id_item;
	UPDATE car_maintenance SET ibd_arx=1 WHERE id_car = id_item AND date_maintenance = dd;
	UPDATE car_maintenance SET ibd_arx=2 WHERE id_car = id_item AND date_maintenance  < dd AND ibd_arx=1;
END IF;
END;

$$
ALTER TABLE `car_repair` MODIFY id INT NOT NULL;

$$
ALTER TABLE `car_repair` DROP INDEX IF EXISTS `PRIMARY`;

$$
ALTER TABLE `car_repair` DROP INDEX IF EXISTS `ind_car_repair_ibd_arx`;

$$
ALTER TABLE `car_repair` DROP INDEX IF EXISTS `id_car`;

$$
ALTER TABLE `car_repair` DROP INDEX IF EXISTS `date_start_repair`;

$$
ALTER TABLE `car_repair` ADD PRIMARY KEY (`id`), ADD KEY `ind_car_repair_ibd_arx` (`ibd_arx`), ADD KEY `id_car` (`id_car`), ADD KEY `date_start_repair` (`date_start_repair`);
		
$$
ALTER TABLE `car_repair` MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Уникальный ID ремонта', AUTO_INCREMENT=1;