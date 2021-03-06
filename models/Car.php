<?php

namespace IcKomiApp\models;

use IcKomiApp\core\User;
use IcKomiApp\core\Model;
use IcKomiApp\core\Logic;
use IcKomiApp\core\Functions;
use IcKomiApp\lib\Database\DB;
use IcKomiApp\lib\excel\GenerateExcel;

class Car extends Model {
	protected $table = 'cars';
	protected $remove_directory = 1;
	protected $trigger_operation = 0;
	protected $array_file_extension = ['jpg', 'png', 'jpeg'];

	protected $sql_get_record = "SELECT cars.*, x1.text as firma_osago, b.n_osago, CAST(cars.mileage AS CHAR) + 0 as mileage,
					 DATE_FORMAT(b.start_date_osago, '%d.%m.%Y') as start_dt_osago, DATE_FORMAT(b.end_date_osago, '%d.%m.%Y') as end_dt_osago, 
					 b.path_to_file file_osago, b.file_extension as ext_file_osago, 
					 c.number_certificate, x2.text as firma_technical_inspection, c.address_technical_inspection,
					 DATE_FORMAT(c.date_certificate, '%d.%m.%Y') as date_certificate, DATE_FORMAT(c.end_date_certificate, '%d.%m.%Y') as end_date_certificate, 
					 c.path_to_file as file_tech_inspection, c.file_extension as ext_file_tech_inspection, 
					 g.s_pts, g.n_pts, DATE_FORMAT(g.date_pts, '%d.%m.%Y') as date_pts, x3.text as type_ts_pts, x4.text as firma_pts, 
					 g.path_to_file as file_pts, g.file_extension as ext_file_pts, 
					 h.s_certificate_reg, h.n_certificate_reg, DATE_FORMAT(h.date_certificate_reg, '%d.%m.%Y') as date_certificate_reg,
					 h.comment_certificate_reg, x5.text as org_certificate_reg, 
					 h.path_to_file as file_cert_reg, h.file_extension as ext_file_cert_reg,
					 DATE_FORMAT(i.start_date, '%d.%m.%Y') as start_date_fire_extinguisher, DATE_FORMAT(i.end_date, '%d.%m.%Y') as end_date_fire_extinguisher, DATE_FORMAT(i.issued_date, '%d.%m.%Y') as issued_date_fire_extinguisher,
					 DATE_FORMAT(j.start_date, '%d.%m.%Y') as start_date_first_aid_kid, DATE_FORMAT(j.end_date, '%d.%m.%Y') as end_date_first_aid_kid, DATE_FORMAT(j.issued_date, '%d.%m.%Y') as issued_date_first_aid_kid,
					 DATE_FORMAT(k.issued_date, '%d.%m.%Y') as issued_date_warning_triangle, DATE_FORMAT(l.start_date, '%d.%m.%Y') as start_date_car_battery, l.type_battery, l.firma_battery,
					 m.number_dopog, DATE_FORMAT(m.date_start_dopog, '%d.%m.%Y') as date_start_dopog, DATE_FORMAT(m.date_end_dopog, '%d.%m.%Y') as date_end_dopog, x6.text as firma_dopog_text,
					 m.path_to_file as file_dopog, m.file_extension as ext_file_dopog,
					 DATE_FORMAT(n.date_calibration, '%d.%m.%Y') as date_calibration, DATE_FORMAT(n.date_next_calibration, '%d.%m.%Y') as date_next_calibration, x7.text as firma_calibration_text,
					 n.path_to_file as file_calibration, n.file_extension as ext_file_calibration,
					 o.number_tachograph, DATE_FORMAT(o.date_start_skzi, '%d.%m.%Y') as date_start_skzi, DATE_FORMAT(o.date_end_skzi, '%d.%m.%Y') as date_end_skzi, x8.text as model_tachograph_text,
					 o.path_to_file as file_tachograph, o.file_extension as ext_file_tachograph,
					 p.number_dvr, p.marka_dvr, p.model_dvr,
					 q.number_glonass, DATE_FORMAT(q.date_glonass, '%d.%m.%Y') as date_glonass, q.number_dut_glonass_1, q.number_dut_glonass_2,
					 q.path_to_file as file_glonass, q.file_extension as ext_file_glonass,
					 DATE_FORMAT(r.date_maintenance, '%d.%m.%Y') as date_maintenance, r.mileage_maintenance,
					 r.path_to_file as file_maintenance, r.file_extension as ext_file_maintenance
					 FROM {table} 
					 LEFT JOIN osago b ON b.id_car=cars.id AND b.ibd_arx=1 
					 LEFT JOIN technical_inspection c ON c.id_car=cars.id AND c.ibd_arx=1 
					 LEFT JOIN pts g ON g.id_car=cars.id AND g.ibd_arx=1 
					 LEFT JOIN certificate_registration h ON h.id_car=cars.id AND h.ibd_arx=1
					 LEFT JOIN car_fire_extinguisher i ON i.id_car=cars.id AND i.ibd_arx=1
					 LEFT JOIN car_first_aid_kid j ON j.id_car=cars.id AND j.ibd_arx=1
					 LEFT JOIN car_warning_triangle k ON k.id_car=cars.id AND k.ibd_arx=1
					 LEFT JOIN car_battery l ON l.id_car=cars.id AND l.ibd_arx=1
					 LEFT JOIN cars_dopog m ON m.id_car=cars.id AND m.ibd_arx=1
					 LEFT JOIN car_calibration n ON n.id_car=cars.id AND n.ibd_arx=1
					 LEFT JOIN car_tachograph o ON o.id_car=cars.id AND o.ibd_arx=1
					 LEFT JOIN car_dvr p ON p.id_car=cars.id AND p.ibd_arx=1
					 LEFT JOIN car_glonass q ON q.id_car=cars.id AND q.ibd_arx=1
					 LEFT JOIN car_maintenance r ON r.id_car=cars.id AND r.ibd_arx=1
					 LEFT JOIN s2i_klass x1 ON x1.kod=b.firma_osago AND x1.nomer=15 
					 LEFT JOIN s2i_klass x2 ON x2.kod=c.firma_technical_inspection AND x2.nomer=16 
					 LEFT JOIN s2i_klass x3 ON x3.kod=g.type_ts_pts AND x3.nomer=6 
					 LEFT JOIN s2i_klass x4 ON x4.kod=g.firma_pts AND x4.nomer=10 
					 LEFT JOIN s2i_klass x5 ON x5.kod=h.org_certificate_reg AND x5.nomer=22
					 LEFT JOIN s2i_klass x6 ON x6.kod=m.firma_dopog AND x6.nomer=34
					 LEFT JOIN s2i_klass x7 ON x7.kod=n.firma_calibration AND x7.nomer=37
					 LEFT JOIN s2i_klass x8 ON x8.kod=o.model_tachograph AND x8.nomer=39
					 WHERE cars.id={id}";

	public function get_list($post = []) {
		$where_archive1 = $where_archive2 = "";

		$where_archive1 = " WHERE a.ibd_arx <> 2 ";
		$where_archive2 = " AND a.ibd_arx <> 2 ";

		/*if($archive == 1) {
			$where_archive1 = " WHERE a.ibd_arx <> 1 ";
			$where_archive2 = " AND a.ibd_arx <> 1 ";
		} else if($archive == 2) {
			$where_archive1 = " WHERE a.ibd_arx = 1 ";
			$where_archive2 = " AND a.ibd_arx = 1 ";
		} else {
			$where_archive1 = " WHERE a.ibd_arx <> 0 ";
			$where_archive2 = " AND a.ibd_arx <> 0 ";
		}*/
		
		$role = User::get('role');

		if($role == 9)
			$this->sql_get_list = "SELECT a.id, e.text AS marka, f.text AS model, a.gos_znak, d.text AS color, g.text AS kateg, a.god_car, a.dostup, a.ibd_arx, a.mileage, a.write_off, "
					  . " DATE_FORMAT(osago.end_date_osago, '%d.%m.%Y') as end_date_osago, DATE_FORMAT(technical_inspection.end_date_certificate, '%d.%m.%Y') as end_date_certificate "
					  . " FROM " . $this->table . " a "
					  . " LEFT JOIN osago ON osago.id_car=a.id AND osago.ibd_arx=1 "
					  . " LEFT JOIN technical_inspection ON technical_inspection.id_car=a.id AND technical_inspection.ibd_arx=1 "
					  . " LEFT JOIN s2i_klass d ON a.color=d.kod AND d.nomer=12 "
					  . " LEFT JOIN s2i_klass e ON a.marka=e.kod AND e.nomer=3 "
					  . " LEFT JOIN s2i_klass f ON a.model=f.kod AND f.nomer=4 "
					  . " LEFT JOIN s2i_klass g ON a.kateg_ts=g.kod AND g.nomer=5 "
					  . $where_archive1
					  . "ORDER BY a.god_car";
		else if($role == 2)
			$this->sql_get_list = "SELECT a.id, e.text AS marka, f.text AS model, a.gos_znak, d.text AS color, g.text AS kateg, a.god_car, a.dostup, a.ibd_arx, a.mileage, a.write_off, "
					  . " DATE_FORMAT(osago.end_date_osago, '%d.%m.%Y') as end_date_osago, DATE_FORMAT(technical_inspection.end_date_certificate, '%d.%m.%Y') as end_date_certificate "
					  . " FROM " . $this->table . " a "
					  . " LEFT JOIN osago ON osago.id_car=a.id AND osago.ibd_arx=1 "
					  . " LEFT JOIN technical_inspection ON technical_inspection.id_car=a.id AND technical_inspection.ibd_arx=1 "
					  . " LEFT JOIN s2i_klass d ON a.color=d.kod AND d.nomer=12 "
					  . " LEFT JOIN s2i_klass e ON a.marka=e.kod AND e.nomer=3 "
					  . " LEFT JOIN s2i_klass f ON a.model=f.kod AND f.nomer=4 "
					  . " LEFT JOIN s2i_klass g ON a.kateg_ts=g.kod AND g.nomer=5 "
					  . " WHERE a.dostup=1 " . $where_archive2
					  . "ORDER BY a.god_car";
		else if($role == 1)
			$this->sql_get_list = "SELECT a.id, e.text AS marka, f.text AS model, a.gos_znak, d.text AS color, g.text AS kateg, a.god_car, a.dostup, a.ibd_arx, a.mileage, a.write_off, "
					  . " DATE_FORMAT(osago.end_date_osago, '%d.%m.%Y') as end_date_osago, DATE_FORMAT(technical_inspection.end_date_certificate, '%d.%m.%Y') as end_date_certificate "
					  . " FROM " . $this->table . " a "
					  . " LEFT JOIN osago ON osago.id_car=a.id AND osago.ibd_arx=1 "
					  . " LEFT JOIN technical_inspection ON technical_inspection.id_car=a.id AND technical_inspection.ibd_arx=1 "
					  . " LEFT JOIN s2i_klass d ON a.color=d.kod AND d.nomer=12 "
					  . " LEFT JOIN s2i_klass e ON a.marka=e.kod AND e.nomer=3 "
					  . " LEFT JOIN s2i_klass f ON a.model=f.kod AND f.nomer=4 "
					  . " LEFT JOIN s2i_klass g ON a.kateg_ts=g.kod AND g.nomer=5 "
					  . " WHERE a.dostup=1 " . $where_archive2
					  . "ORDER BY a.god_car";
		else
			return false;

		if(($data = parent::get_list()) === false)
			return false;

		$html = $this->draw_result_table($data, 1, (empty($post['page']) ? 1 : $post['page']));
		return [ 'search_result' => $html];
	}


	// Функция поиска
	public function search($post, $flg_excel = -1) {
		$role = User::get('role');
		
		if($role == 0)
			return false;
		
		if(empty($post['JSON']))
			return false;
		
		if(!$array_data_decode = json_decode($post['JSON']))
			return false;

		if(!empty($post['excel']))
			$flg_excel = $post['excel'];
		
		// Начинаем формировать условие для поиска
		// Если уровень прав пользователя меньше чем 2, то ставим ограничение, что ищем только открытые ТС
		$where = '';
		
		if($role <= 2)
			$where = " a.dostup=1 ";

		$end_dt_osago1 = $end_dt_osago2 = ''; // Поля для хранения даты полиса ОСАГО
		$end_date_certificate1 = $end_date_certificate2 = ''; // Поля для хранения даты окончания тех. осмотра
		$gos_znak = '';
		
		foreach($array_data_decode as $field => $array_value) {
			$array_value_decode = (array)$array_value;

			if($array_value_decode['type'] == 'char') {
				if(trim($array_value_decode['value']) == "0" || (mb_strlen(trim($array_value_decode['value'])) == 0))
					continue;

				if($field == 'gos_znak') {
					if(mb_strlen($where) == 0)
						$where .= " (a." . $field . " LIKE '%" . $array_value_decode['value'] . "%' OR a.id IN (SELECT id_car FROM cars_old_gos_znak WHERE cars_old_gos_znak.gos_znak like '%" . $array_value_decode['value'] . "%')) ";
					else
						$where .= " AND (a." . $field . " LIKE '%" . $array_value_decode['value'] . "%' OR a.id IN (SELECT id_car FROM cars_old_gos_znak WHERE cars_old_gos_znak.gos_znak like '%" . $array_value_decode['value'] . "%')) ";
					continue;
				}
				
				if(mb_strlen($where) == 0)
					$where .= " a." . $field . " LIKE '%" . $array_value_decode['value'] . "%'";
				else
					$where .= " AND a." . $field . " LIKE '%" . $array_value_decode['value'] . "%'";

			} else if($array_value_decode['type'] == 'date') {
				if(trim($array_value_decode['value']) == "0" || (mb_strlen(trim($array_value_decode['value'])) == 0))
					continue;
				
				if($field == 'end_dt_osago1') {
					$end_dt_osago1 = Functions::convertToMySQLDateFormat($array_value_decode['value']);
				} else if($field == 'end_dt_osago2') {
					$end_dt_osago2 = Functions::convertToMySQLDateFormat($array_value_decode['value']);
				} else if($field == 'end_date_certificate1') {
					$end_date_certificate1 = Functions::convertToMySQLDateFormat($array_value_decode['value']);
				} else if($field == 'end_date_certificate2') {
					$end_date_certificate2 = Functions::convertToMySQLDateFormat($array_value_decode['value']);
				} else {
					if(mb_strlen($where) == 0)
						$where .= " a." . $field . "='" . $array_value_decode['value'] . "'";
					else
						$where .= " AND a." . $field . "='" . $array_value_decode['value'] . "'";
				}
			} else if($array_value_decode['type'] == 'checkbox') {

				if($array_value_decode['value'] == 'true') {
					if(mb_strlen($where) == 0)
						$where .= " a." . $field . " IN (1,2) ";
					else
						$where .= " AND a." . $field . " IN (1,2) ";
				} else {
					if(mb_strlen($where) == 0)
						$where .= " a." . $field . "=1 ";
					else
						$where .= " AND a." . $field . "=1 ";
				}
				
			} else if($array_value_decode['type'] == 'radio') {
				if($array_value_decode['value'] == 1)
					$where .= (mb_strlen($where) == 0) ? " a." . $field . " <> 1" : " AND a." . $field . " <> 1 ";
				else if($array_value_decode['value'] == 2)
					$where .= (mb_strlen($where) == 0) ? " a." . $field . " = 1 " : " AND a." . $field . " = 1 ";
				else if($array_value_decode['value'] == 3)
					$where .= (mb_strlen($where) == 0) ? " a." . $field . " <> 0 " : " AND a." . $field . " <> 0 ";
				else if($array_value_decode['value'] == 4)
					$where .= (mb_strlen($where) == 0) ? " a.write_off=1 " : " AND a.write_off=1 ";
			} else {
				if(trim($array_value_decode['value']) == "0" || (mb_strlen(trim($array_value_decode['value'])) == 0))
					continue;

				if(mb_strlen($where) == 0)
					$where .= " a." . $field . "=" . $array_value_decode['value'];
				else
					$where .= " AND a." . $field . "=" . $array_value_decode['value'];
			}
		}

		$join_gos_znak = '';
		if(mb_strlen($gos_znak) != 0)
			$join_gos_znak = " OR a.id IN (SELECT id_car FROM cars_old_gos_znak WHERE cars_old_gos_znak.gos_znak like '%" . $gos_znak . "%') ";

		$inner_join_osago = '';
		if((mb_strlen($end_dt_osago1) != 0) && (mb_strlen($end_dt_osago2) != 0))
			$inner_join_osago = " INNER JOIN osago ON osago.id_car=a.id AND osago.ibd_arx=1 AND end_date_osago BETWEEN '" . $end_dt_osago1 . "' AND '" . $end_dt_osago2 . "'";
		else if(mb_strlen($end_dt_osago1) != 0)
			$inner_join_osago = " INNER JOIN osago ON osago.id_car=a.id AND osago.ibd_arx=1 AND end_date_osago = '" . $end_dt_osago1 . "'";
		else if(mb_strlen($end_dt_osago2) != 0)
			$inner_join_osago = " INNER JOIN osago ON osago.id_car=a.id AND osago.ibd_arx=1 AND end_date_osago <= '" . $end_dt_osago2 . "'";
		else
			$inner_join_osago = " LEFT JOIN osago ON osago.id_car=a.id AND osago.ibd_arx=1 ";
		
		$inner_join_technical_inspection = '';
		if((mb_strlen($end_date_certificate1) != 0) && (mb_strlen($end_date_certificate2) != 0))
			$inner_join_technical_inspection = " INNER JOIN technical_inspection ON technical_inspection.id_car=a.id AND technical_inspection.ibd_arx=1 AND end_date_certificate BETWEEN '" . $end_date_certificate1 . "' AND '" . $end_date_certificate2 . "'";
		else if(mb_strlen($end_date_certificate1) != 0)
			$inner_join_technical_inspection = " INNER JOIN technical_inspection ON technical_inspection.id_car=a.id AND technical_inspection.ibd_arx=1 AND end_date_certificate = '" . $end_date_certificate1 . "'";
		else if(mb_strlen($end_date_certificate2) != 0)
			$inner_join_technical_inspection = " INNER JOIN technical_inspection ON technical_inspection.id_car=a.id AND technical_inspection.ibd_arx=1 AND end_date_certificate <= '" . $end_date_certificate2 . "'";
		else
			$inner_join_technical_inspection = " LEFT JOIN technical_inspection ON technical_inspection.id_car=a.id AND technical_inspection.ibd_arx=1 ";
		
		if($flg_excel == -1)
			$sql = "SELECT a.id, x1.text AS marka, x2.text AS model, x3.text AS color, a.gos_znak, a.n_reg, a.god_car, a.ibd_arx, x6.text AS kateg_ts, "
					. " a.basic_fuel, a.summer_fuel, a.winter_fuel, a.inventory_n, a.prim, a.vin, a.n_dvig, a.shassi, a.kuzov, a.mass_max, a.mass_min, a.car_vat, a.car_v, CAST(a.mileage AS CHAR) + 0 as mileage, DATE_FORMAT(osago.end_date_osago, '%d.%m.%Y') as end_date_osago, "
					. " DATE_FORMAT(technical_inspection.end_date_certificate, '%d.%m.%Y') as end_date_certificate, a.write_off "
					. " FROM " . $this->table . " a "
					. $inner_join_osago
					. $inner_join_technical_inspection
					. " LEFT JOIN s2i_klass x1 ON a.marka = x1.kod AND x1.nomer = 3 "
					. " LEFT JOIN s2i_klass x2 ON a.model = x2.kod AND x2.nomer = 4 "
					. " LEFT JOIN s2i_klass x3 ON a.color = x3.kod AND x3.nomer = 12 "
					. " LEFT JOIN s2i_klass x6 ON a.kateg_ts = x6.kod AND x6.nomer = 5 ";
		else
			$sql = "SELECT a.id, x1.text AS marka, x2.text AS model, x3.text AS color, a.gos_znak, a.n_reg, a.god_car, a.ibd_arx, x9.text AS kateg_ts, "
					. " a.basic_fuel, a.summer_fuel, a.winter_fuel, a.inventory_n, a.prim, a.vin, a.n_dvig, a.shassi, a.kuzov, a.mass_max, a.mass_min, a.car_vat, a.car_v, "
					. " x6.text as tip_strah, x7.text as kateg_gost, CAST(a.mileage AS CHAR) + 0 as mileage, DATE_FORMAT(osago.end_date_osago, '%d.%m.%Y') as end_date_osago, x10.text as firma_osago, osago.n_osago, "
					. " DATE_FORMAT(technical_inspection.end_date_certificate, '%d.%m.%Y') as end_date_certificate,  DATE_FORMAT(technical_inspection.date_certificate, '%d.%m.%Y') as date_certificate, "
					. " technical_inspection.address_technical_inspection, x11.text as firma_technical_inspection, technical_inspection.number_certificate, "
					. " DATE_FORMAT(car_maintenance.date_maintenance, '%d.%m.%Y') as date_maintenance, car_maintenance.mileage_maintenance "
					. " FROM " . $this->table . " a "
					. $inner_join_osago
					. $inner_join_technical_inspection
					. " LEFT JOIN car_maintenance ON car_maintenance.id_car=a.id AND car_maintenance.ibd_arx=1 "
					. " LEFT JOIN s2i_klass x1 ON a.marka = x1.kod AND x1.nomer = 3 "
					. " LEFT JOIN s2i_klass x2 ON a.model = x2.kod AND x2.nomer = 4 "
					. " LEFT JOIN s2i_klass x3 ON a.color = x3.kod AND x3.nomer = 12 "
					. " LEFT JOIN s2i_klass x6 ON a.tip_strah = x6.kod AND x6.nomer = 7 "
					. " LEFT JOIN s2i_klass x7 ON a.kateg_gost = x7.kod AND x7.nomer = 9 "
					. " LEFT JOIN s2i_klass x9 ON a.kateg_ts = x9.kod AND x9.nomer = 5 "
					. " LEFT JOIN s2i_klass x10 ON osago.firma_osago = x10.kod AND x10.nomer = 15 "
					. " LEFT JOIN s2i_klass x11 ON technical_inspection.firma_technical_inspection = x11.kod AND x11.nomer = 16 ";

		if(mb_strlen($where) > 0)
			$sql .= " WHERE (" . $where . ") " . $join_gos_znak;
		$sql .= " ORDER BY a.god_car ";
		
		if(($data = DB::query($sql)) === false)
			return false;

		$html = ($_POST['excel'] != -1) ? $this->generate_excel_document($data) : $this->draw_result_table($data, 2, addslashes($post['page']), 2);
		return [ 'search_result' => $html];
	}


	// Получение всех полей таблицы по ее ID (без раскрытия справочников)
	public function get($get) {
		$data = parent::get($get);
		$array_additional = $this->draw_additional_information((empty($data[0]['id']) ? -1 : $data[0]['id']));

		if(count($data) != 0) {
			$data[0]['list_driver'] = $array_additional[0];
			$data[0]['list_repair'] = $array_additional[1];
			$data[0]['list_car_doc'] = $array_additional[2];
			$data[0]['list_images'] = $array_additional[3];
			$data[0]['list_dtp'] = $array_additional[4];
			$data[0]['list_adm'] = $array_additional[5];
			$data[0]['list_old_gos_znak'] = $array_additional[6];
			$data[0]['list_wheels'] = $array_additional[7];
		}

		return $data;
	}

	// Функция для получения дополнительной информации по транспортному средству
	// Получает информацию о ремонте, документах на ТС, закрепленных водителях
	public function get_additional_information($id) {
	
		$sql = "SELECT	1 as ID_MODULE, "
			. " a.id as AA, "
			. " a.date_car_document as BB, "
			. " a.number_car_document as CC, "
			. " b.comment as DD, "
			. " x1.text as EE, "
			. " a.path_to_file as PATH_TO_FILE, "
			. " a.file_extension as FILE_EXTENSION, "
			. " x2.text as FF,"
			. " 0 as GG "
		. " FROM car_documents a"
		. " INNER JOIN car_link_document b ON b.id_document=a.id AND b.id_car=" . $id
		. " LEFT JOIN s2i_klass x1 ON x1.kod=a.type_car_document AND x1.nomer=23 "
		. " LEFT JOIN s2i_klass x2 ON x2.kod=b.title_document AND x2.nomer=24 "

			. " UNION ALL "
				. " SELECT 2, "
					. " a.id, "
					. " a.date_start_repair,"
					. " DATE_FORMAT(a.date_end_repair, '%d.%m.%Y'),"
					. " a.prim_repair,"
					. " x1.text,"
					. " b.path_to_file,"
					. " b.file_extension, "
					. " a.car_mileage, "
					. " a.price_repair "
				. " FROM car_repair a "
				. " LEFT JOIN files b ON b.id_object=a.id AND b.category_file=11 "
				. " LEFT JOIN s2i_klass x1 ON x1.kod=a.org_repair AND x1.nomer=18 "
				. " WHERE a.id_car=" . $id

			. " UNION ALL "
				. " SELECT 3, "
					. " b.id, "
					. " a.date_doc_fix,"
					. " a.number_doc_fix, "
					. " x1.text, "
					. " CONCAT(b.fam, ' ', b.imj, ' ', b.otch),"
					. " a.path_to_file, "
					. " a.file_extension, "
					. " x2.text, "
					. " x3.text "
				. " FROM car_for_driver a "
				. " LEFT JOIN drivers b ON b.id=a.id_driver "
				. " LEFT JOIN s2i_klass x1 ON x1.kod=a.type_doc_fix AND x1.nomer=14 "
				. " LEFT JOIN s2i_klass x2 ON x2.kod=b.slugba AND x2.nomer=1 "
				. " LEFT JOIN s2i_klass x3 ON x3.kod=b.kodrai AND x3.nomer=11 "
				. " WHERE a.car_id=" . $id . " AND a.ibd_arx=1 "
				
			. " UNION ALL "
				. " SELECT 4, "
					. " a.id, "
					. " 0, "
					. " 0, "
					. " 0, "
					. " 0, "
					. " a.path_to_file, "
					. " a.file_extension, "
					. " 0, "
					. " 0 "
				. " FROM files a "
				. " WHERE a.id_object=" . $id . " AND a.category_file=13 "
				
			. " UNION ALL "
				. " SELECT 5, "
					. " a.id, "
					. " a.place_committing, "
					. " a.date_committing, "
					. " a.time_committing, "
					. " a.comment_committing, "
					. " 0, "
					. " 0, "
					. " a.sum_committing, "
					. " 0 "
				. " FROM dtp a "
				. " WHERE a.id_car=" . $id

			. " UNION ALL "
				. " SELECT 6, "
					. " a.id_driver, "
					. " a.place_adm, "
					. " DATE_FORMAT(a.date_adm, '%d.%m.%Y'), "
					. " a.time_adm, "
					. " x1.text, "
					. " 0, "
					. " IF(a.oplat_adm=1, 'ДА', 'НЕТ'), "
					. " a.sum_adm, "
					. " CONCAT(b.fam, ' ', b.imj, ' ', b.otch) "
				. " FROM adm_offense a "
				. " LEFT JOIN drivers b ON b.id=a.id_driver "
				. " LEFT JOIN s2i_klass x1 ON x1.kod=a.st_chast_koap AND x1.nomer=26 "
				. " WHERE a.id_car=" . $id
				
			. " UNION ALL "
				. " SELECT 7, "
					. " a.id_car, "
					. " a.gos_znak, "
					. " null, "
					. " 0, "
					. " 0, "
					. " 0, "
					. " 0, "
					. " 0, "
					. " 0 "
				. " FROM cars_old_gos_znak a "
				. " WHERE a.id_car=" . $id

			. " UNION ALL "
				. " SELECT 8, "
					. " a.id, "
					. " DATE_FORMAT(a.date_installation, '%d.%m.%Y'), "
					. " null, "
					. " x1.text , "
					. " x2.text, "
					. " 0, "
					. " a.comment_wheel, "
					. " x5.text, "
					. " CONCAT(x3.text, ' ', x4.text) "
				. " FROM cars_wheels a "
				. " LEFT JOIN s2i_klass x1 ON x1.kod=a.season_wheel AND x1.nomer=27 "
				. " LEFT JOIN s2i_klass x2 ON x2.kod=a.type_wheel AND x2.nomer=28 "
				. " LEFT JOIN s2i_klass x3 ON x3.kod=a.marka_wheel AND x3.nomer=29 "
				. " LEFT JOIN s2i_klass x4 ON x4.kod=a.model_wheel AND x4.nomer=30 "
				. " LEFT JOIN s2i_klass x5 ON x5.kod=a.size_wheel AND x5.nomer=31 "
				. " WHERE a.ibd_arx=1 AND a.id_car=" . $id
				
				. " ORDER BY ID_MODULE, BB ";

		if(($data = DB::query($sql)) === false)
			return false;
		return $data;
	}

	// Отрисовка списка ТС
	function draw_result_table($data, $type = 1, $page = 0) {
		$html = "";
		$record_limit = (($page - 1) < 0) ? 0 : $page - 1;
		$record_tail_limit = $record_limit * $this->list_items_for_one_page + 1;
		$record_head_limit = $record_tail_limit + $this->list_items_for_one_page - 1;

		$page_left = $page_right = 0;
		$page_left_disabled = $page_right_disabled = '';
		if(($page - 1) <= 0) {
			$page_left = 1;
			$page_left_disabled = ' disabled ';
		} else {
			$page_left = $page - 1;
		}

		$x = intdiv(count($data), $this->list_items_for_one_page) + (((count($data) % $this->list_items_for_one_page) > 0) ? 1 : 0);

		if(($page + 1) < $x) {
			$page_right = $page + 1;
		} else if (($page + 1) == $x){
			$page_right = $x;
		} else if(($page + 1) > $x){
			$page_right_disabled = ' disabled ';
			$page_right = $x;
		}
		
		if(count($data) == 0) {
			$html = "<p>Сведений в базе данных не обнаружено</p>";
		} else {
			$style_border = "style='vertical-align: middle; border: 1px solid gray;'";
			$html_table = "<table class='table table-striped table-sm table-hover text-center' style='font-size: 13px;'>";
			$html_table .= "<tr class='table-info'>"
				. "<th " . $style_border . " scope='col'>№ п/п</th>"
				. "<th " . $style_border . " scope='col'>Марка и модель</th>"
				. "<th " . $style_border . " scope='col'>Гос. номер</th>"
				. "<th " . $style_border . " scope='col'>Год выпуска</th>"
				. "<th " . $style_border . " scope='col'>Пробег, км</th>"
				. "<th " . $style_border . " scope='col'>Цвет</th>"
				. "<th " . $style_border . " scope='col'>Дата<br>окончания<br>ОСАГО</th>"
				. "<th " . $style_border . " scope='col'>Дата<br>окончания<br>техосмотра</th></tr>";
		
			// Строим список водителей
			$j = 0;
			for($i = 0; $i < count($data); $i++) {
				
				$link = 'http://' . $_SERVER['HTTP_HOST'] . '/car?id=' . $data[$i]['id'];
				$j++;
				
				if(($j >= $record_tail_limit) && ($j <= $record_head_limit)) {
					$html_table .= '<tr>';
					if($data[$i]['ibd_arx'] != 1)
						$html_table .= "<tr class='table-danger'>";
					else if($data[$i]['write_off'] == 1)
						$html_table .= "<tr class='table-yellow'>";
					else
						$html_table .= "<tr>";

					$html_table .= "<td " . $style_border . ">" . ($i+1) . "</th>"
						. "<td " . $style_border . "><a href='" . $link . "' target='_blank'>" . $data[$i]['marka'] . " " . $data[$i]['model'] . "</a></td>"
						. "<td " . $style_border . ">" . $data[$i]['gos_znak']. "</td>"
						. "<td " . $style_border . ">" . $data[$i]['god_car']. "</td>"
						. "<td " . $style_border . ">" . $data[$i]['mileage'] . "</td>"
						. "<td " . $style_border . ">" . $data[$i]['color']. "</td>"
						. "<td " . $style_border . ">" . $data[$i]['end_date_osago']. "</td>"
						. "<td " . $style_border . ">" . $data[$i]['end_date_certificate']. "</td>";
					$html_table .= "</tr>";
				}
				
				if($j > $record_head_limit)
					break;
			}
			
			$html_table .= "</table>";

			// Class button
			$class_btn = ($type == 1) ? 'btn-list-cars' : 'btn-search-cars';
			
			// Подсчет количества уникальных значений в массиве для переключения между страницами
			$count_uniq_array = count(array_count_values(array_map(function($a) { return $a['id']; }, $data)));
			
			$text_list = "Записи:&nbsp;" . $record_tail_limit . '&nbsp;-&nbsp;' . (($j > $record_head_limit) ? $record_head_limit : $j) . "&nbsp;из&nbsp;" . $count_uniq_array;
			$html_bottom_text = "<div class='text-right' style='display: block; font-size: 15px;'>" . $text_list . "</div>";
	
			$html_btn_top = "<div class='col btn-group text-right' role='group' style='display: block; padding: 0px;'>"
				. "<button type='button' class='btn btn-sm btn-secondary mb-1 mr-2 " . $class_btn . "' title='Перейти к предыдущим записям' data-excel='-1' data-page='" . $page_left . "'" . $page_left_disabled . "><span class='fa fa-mail-reply'></span></button>"
				. "<label>" . $text_list . "</label>";
			
			$html_btn_bottom = "<div class='col btn-group text-center' role='group' style='display: block;'>"
				. "<button type='button' class='btn btn-secondary mb-1 mr-2 " . $class_btn . "' title='Перейти к предыдущим записям' data-excel='-1' data-page='" . $page_left . "'" . $page_left_disabled . "><span class='fa fa-mail-reply'>&nbsp;</span>Предыдущие записи</button>";
			
			$html_btn_top .= "<button type='button' class='btn btn-sm btn-secondary mb-1 ml-2 " . $class_btn . "' title='Перейти к следующим записям' data-excel='-1' data-page='" . $page_right . "'" . $page_right_disabled . "><span class='fa fa-mail-forward'></span></button>";
			$html_btn_bottom .= "<button type='button' class='btn btn-secondary mb-1 ml-2 " . $class_btn . "' title='Перейти к следующим записям' data-excel='-1' data-page='" . $page_right . "'" . $page_right_disabled . ">Следующие записи&nbsp<span class='fa fa-mail-forward'></span></button>";

			$html_btn_top .= "</div>";
			$html_btn_bottom .= "</div>";

			$html = $html_btn_top . $html_table . $html_bottom_text . $html_btn_bottom;
		}
		
		return $html;
	}

	public function generate_excel_document($data) {
		$header = array('№ п/п', 'Марка', 'Модель', 'Гос. номер', 'Год выпуска', 'Цвет', 'Пробег', 'Категория ТС', 'Тип для страховой', 'Категория ГОСТ', 'VIN / зав. № машины (рамы)', 'Двигатель',
			'Шасси / коробка передач', 'Кузов / осн. ведущий мост (мосты)', 'Разр. макс. масса', 'Масса без нагрузки', 'Мощность л.с', 'Раб. объем двигателя куб.см', 'Базовая норма', 'Эксплуатационная летняя норма',
			'Эксплуатационная зимняя норма', 'Инвентарный номер', 'Примечание', 'Дата окончания полиса ОСАГО', 'Серия и номер полиса ОСАГО', 'Страховая компания', 'Номер сертификата технического осмотра',
			'Дата выдачи сертификата технического осмотра', 'Дата окончания сертификата технического осмотра', 'Организация выдавшая сертификат технического осмотра', 'Адрес прохождения технического осмотра', 'Дата тех. обслуживания', 'Пробег на момент тех. обслуживания');
		$body = array(['{index}'], ['marka'], ['model'], ['gos_znak'], ['god_car'], ['color'], ['mileage'], ['kateg_ts'],
			['tip_strah'], ['kateg_gost'], ['vin'], ['n_dvig'], ['shassi'], ['kuzov'], ['mass_max'], ['mass_min'], ['car_vat'],
			['car_v'], ['basic_fuel'], ['summer_fuel'], ['winter_fuel'], ['inventory_n'], ['prim'], ['end_date_osago'], ['n_osago'], ['firma_osago'], ['number_certificate'],
			['date_certificate'], ['end_date_certificate'], ['firma_technical_inspection'], ['address_technical_inspection'], ['date_maintenance'], ['mileage_maintenance']);
		return GenerateExcel::generate_excel_document('cars', 'Транспортные средства', $header, $body, $data);
	}

	public function draw_additional_information($id) {
		$style_border = "style='vertical-align: middle; border: 1px solid gray;'"; // Стиль для ячейки

		$list_driver = $list_repair = $list_car_doc = $list_images = $list_dtp = $list_adm = $list_old_gos_znak = $list_wheels = "";	// Переменные для хранения таблиц
		
		if(($data_add = $this->get_additional_information(addslashes($id))) === false)
			return false;
		
		$id_repair = 0;
		
		for($i = 0, $j = 0, $k = 0, $l = 0, $n = 0, $x = 0, $y = 0; $i < count($data_add); $i++) {
			
			if($data_add[$i]['ID_MODULE'] == 1) {
				$page = 'http://' . $_SERVER['HTTP_HOST'] . '/car_document?id=' . $data_add[$i]['AA'];
				$list_car_doc .= "<tr><td " . $style_border . ">" . ++$l . "</td>"
							. "<td " . $style_border . "><a href='" . $page . "' target='_blank'>" . $data_add[$i]['EE'] . "&nbsp;" . $data_add[$i]['FF'] . "</a></td>"
							. "<td " . $style_border . ">" . $data_add[$i]['CC'] . "</td>"
							. "<td " . $style_border . ">" . Functions::convertToDate($data_add[$i]['BB']) . "</td>"
							. "<td " . $style_border . ">" . Functions::rendering_icon_file($data_add[$i]['PATH_TO_FILE'], $data_add[$i]['FILE_EXTENSION']) . "</td>"
							. "<td " . $style_border . ">" . $data_add[$i]['DD'] . "</td>"
							. "</tr>";
			} else if($data_add[$i]['ID_MODULE'] == 2) {
				if($data_add[$i]['AA'] == $id_repair)
					continue;
				$page = 'http://' . $_SERVER['HTTP_HOST'] . '/repair?id=' . $data_add[$i]['AA'];
				$list_repair .= "<tr><td " . $style_border . ">" . ++$k . "</td>"
							. "<td style='vertical-align: middle; border: 1px solid gray; font-size: 11px;'><a href='" . $page . "' target='_blank'>" . $data_add[$i]['EE'] . "</a></td>"
							. "<td " . $style_border . ">" . Functions::convertToDate($data_add[$i]['BB']) . " - " . $data_add[$i]['CC'] . "</td>"
							. "<td " . $style_border . ">" . $data_add[$i]['FF'] . "</td>"
							. "<td " . $style_border . ">" . $data_add[$i]['GG'] . "</td>"
							//. "<td " . $style_border . "><span class='repair-show-information' data-nsyst='" . $data_add[$i]['AA'] . "'>открыть</span></td>"
							. "<td class='text-left' style='vertical-align: middle; border: 1px solid gray; font-size: 11px;'>" . $data_add[$i]['DD'] . "</td>"
							//. "<td " . $style_border . ">" . ServiceFunction::rendering_icon_file($data_add[$i]['PATH_TO_FILE'], $data_add[$i]['FILE_EXTENSION']) . "</td>"
							. "<td " . $style_border . ">" . $this->search_files($data_add, $i, 2, $data_add[$i]['BB']) . "</td>"
							. "</tr>";
				$id_repair = $data_add[$i]['AA'];
			} else if($data_add[$i]['ID_MODULE'] == 3) {
				$page = 'http://' . $_SERVER['HTTP_HOST'] . '/driver?id=' . $data_add[$i]['AA'];
				$list_driver .= "<tr><td " . $style_border . ">" . ++$j . "</td>"
							. "<td " . $style_border . "><a href='" . $page . "' target='_blank'>" . $data_add[$i]['EE'] . "</a></td>"
							. "<td " . $style_border . ">" . $data_add[$i]['GG'] . "</td>"
							. "<td " . $style_border . ">" . $data_add[$i]['FF'] . "</td>"
							. "<td " . $style_border . ">" . $data_add[$i]['DD'] . " № " . $data_add[$i]['CC'] . " от " . Functions::convertToDate($data_add[$i]['BB']) . "&nbsp" .
								Functions::rendering_icon_file($data_add[$i]['PATH_TO_FILE'], $data_add[$i]['FILE_EXTENSION']) . "</td></tr>";
			} else if($data_add[$i]['ID_MODULE'] == 4) {
				$list_images .= Functions::rendering_icon_file($data_add[$i]['PATH_TO_FILE'], $data_add[$i]['FILE_EXTENSION'], $data_add[$i]['AA'], 13, true);
			} else if($data_add[$i]['ID_MODULE'] == 5) {
				$list_dtp .= "<tr><td " . $style_border . ">" . ++$n . "</td>"
							. "<td " . $style_border . ">" . Functions::convertToDate($data_add[$i]['CC']) . "&nbsp" . $data_add[$i]['DD'] . "</td>"
							. "<td style='vertical-align: middle; border: 1px solid gray; font-size: 11px;'>" . Functions::convertToDate($data_add[$i]['BB']) . "</td>"
							. "<td " . $style_border . ">" . $data_add[$i]['FF'] . "</td>"
							. "<td class='text-left' style='vertical-align: middle; border: 1px solid gray; font-size: 11px;'>" . $data_add[$i]['EE'] . "</td>"
							. "<td " . $style_border . "><span class='dtp-show-information' data-nsyst='" . $data_add[$i]['AA'] . "'>открыть</span></td></tr>";
			} else if($data_add[$i]['ID_MODULE'] == 6) {
				$page = 'http://' . $_SERVER['HTTP_HOST'] . '/driver?id=' . $data_add[$i]['AA'];
				$list_adm .= "<tr><td " . $style_border . ">" . ++$x . "</td>"
							. "<td " . $style_border . "><a href='" . $page . "' target='_blank'>" . $data_add[$i]['GG'] . "</a></td>"
							. "<td style='vertical-align: middle; border: 1px solid gray;'>" . $data_add[$i]['CC'] . "&nbsp;" . $data_add[$i]['DD'] . "</td>"
							. "<td " . $style_border . ">ст.&nbsp;" . $data_add[$i]['EE'];
				
				$list_adm .= "<td class='text-left' style='vertical-align: middle; border: 1px solid gray; font-size: 11px;'>" . $data_add[$i]['BB'] . "</td>"
							. "<td " . $style_border . ">" . $data_add[$i]['FF'] ."</td>"
							. "<td " . $style_border . ">" . $data_add[$i]['FILE_EXTENSION'] . "</td></tr>";
			} else if($data_add[$i]['ID_MODULE'] == 7) {
				$list_old_gos_znak .= $data_add[$i]['BB'] . "<br>";
			} else if($data_add[$i]['ID_MODULE'] == 8) {
				$list_wheels .= "<tr><td " . $style_border . ">" . ++$y . "</td>"
							. "<td " . $style_border . ">" . $data_add[$i]['EE'] . "</td>"
							. "<td " . $style_border . ">" . $data_add[$i]['GG'] . "</td>"
							. "<td " . $style_border . ">" . $data_add[$i]['DD'] . "</td>"
							. "<td " . $style_border . ">" . $data_add[$i]['FF'] . "</td>"
							. "<td " . $style_border . ">" . $data_add[$i]['BB'] . "</td>"
							. "<td " . $style_border . ">" . $data_add[$i]['FILE_EXTENSION'] . "</td>"
							. "</tr>";

			}
		}
		
		// Формируем готовый HTML код для списка закрепленных водителей
		if(mb_strlen($list_driver) > 0) {
			$list_driver = "<table class='table table-striped table-sm table-hover text-center' style='font-size: 13px;'>"
						. "<tr class='table-info'>"
							. "<th " . $style_border . " scope='col'>№ п/п</th>"
							. "<th " . $style_border . " scope='col'>ФИО</th>"
							. "<th " . $style_border . " scope='col'>Район</th>"
							. "<th " . $style_border . " scope='col'>Служба</th>"
							. "<th " . $style_border . " scope='col'>Основание</th>"
						. "</tr>" . $list_driver;
			$list_driver .= "</table>";
		}
		
		// Формируем готовый HTML код для списка ремонтов
		if(mb_strlen($list_repair) > 0) {
			$list_repair = "<table class='table table-striped table-sm table-hover text-center' style='font-size: 13px;'>"
						. "<tr class='table-info'>"
							. "<th " . $style_border . " scope='col'>№ п/п</th>"
							. "<th " . $style_border . " scope='col'>Станция ремонта</th>"
							. "<th " . $style_border . " scope='col'>Дата ремонта</th>"
							. "<th " . $style_border . " scope='col'>Пробег<br>на момент ремонта</th>"
							. "<th " . $style_border . " scope='col'>Стоимость</th>"
							//. "<th " . $style_border . " scope='col'>Перечень услуг</th>"
							. "<th " . $style_border . " scope='col'>Комментарий</th>"
							. "<th " . $style_border . " scope='col'>Эл. образы</th>"
						. "</tr>" . $list_repair;
			$list_repair .= "</table>";
		}
		
		// Формируем готовый HTML код для списка документов
		if(mb_strlen($list_car_doc) > 0) {			
			$list_car_doc = "<table class='table table-striped table-sm table-hover text-center' style='font-size: 13px;'>"
						. "<tr class='table-info'>"
							. "<th " . $style_border . " scope='col'>№ п/п</th>"
							. "<th " . $style_border . " scope='col'>Тип документа</th>"
							. "<th " . $style_border . " scope='col'>Номер документа</th>"
							. "<th " . $style_border . " scope='col'>Дата документа</th>"
							. "<th " . $style_border . " scope='col'>Эл. образ</th>"
							. "<th " . $style_border . " scope='col'>Комментарий</th>"
						. "</tr>" . $list_car_doc;
			$list_car_doc .= "</table>";
		}
		
		// Формируем готовый HTML код для списка ДТП
		if(mb_strlen($list_dtp) > 0) {
			$list_dtp = "<table class='table table-striped table-sm table-hover text-center' style='font-size: 13px;'>"
						. "<tr class='table-info'>"
							. "<th " . $style_border . " scope='col'>№ п/п</th>"
							. "<th " . $style_border . " scope='col'>Дата и время ДТП</th>"
							. "<th " . $style_border . " scope='col'>Место ДТП</th>"
							. "<th " . $style_border . " scope='col'>Сумма ущерба, руб</th>"
							. "<th " . $style_border . " scope='col'>Описание ДТП</th>"
							. "<th " . $style_border . " scope='col'>Подробнее</th>"
						. "</tr>" . $list_dtp;
			$list_dtp .= "</table>";
		}
		
		// Формируем готовый HTML код для списка ДТП
		if(mb_strlen($list_adm) > 0) {
			$list_adm = "<table class='table table-striped table-sm table-hover text-center' style='font-size: 13px;'>"
						. "<tr class='table-info'>"
							. "<th " . $style_border . " scope='col'>№ п/п</th>"
							. "<th " . $style_border . " scope='col'>Водитель</th>"
							. "<th " . $style_border . " scope='col'>Дата и время совершения</th>"
							. "<th " . $style_border . " scope='col'>Статья и часть КоАП РФ</th>"
							. "<th " . $style_border . " scope='col'>Место совершения</th>"
							. "<th " . $style_border . " scope='col'>Штраф, руб</th>"
							. "<th " . $style_border . " scope='col'>Оплата штрафа</th>"
						. "</tr>" . $list_adm;
			$list_adm .= "</table>";
		}
		
		if(mb_strlen($list_old_gos_znak) > 0) {
			$list_old_gos_znak = "<a href='#' class='btn-cars-old-gos-znak' data-toggle='popover' title='Старые гос. номера' data-html='true' data-content='" . $list_old_gos_znak . "'>Старые номера</a>";
		}

		if(mb_strlen($list_wheels) > 0) {
			$list_wheels = "<table class='table table-striped table-sm table-hover text-center' style='font-size: 13px;'>"
						. "<tr class='table-info'>"
							. "<th " . $style_border . " scope='col'>№ п/п</th>"
							. "<th " . $style_border . " scope='col'>Тип</th>"
							. "<th " . $style_border . " scope='col'>Марка/модель</th>"
							. "<th " . $style_border . " scope='col'>Сезон</th>"
							. "<th " . $style_border . " scope='col'>Размер</th>"
							. "<th " . $style_border . " scope='col'>Дата установки</th>"
							. "<th " . $style_border . " scope='col'>Примечание</th>"
						. "</tr>" . $list_wheels;
			$list_wheels .= "</table>";
		}

		return array($list_driver, $list_repair, $list_car_doc, $list_images, $list_dtp, $list_adm, $list_old_gos_znak, $list_wheels);
	}

	// Функция ищет ве файлы по ремонту и соединяет в один
	function search_files($data, $cur_ind_array, $id_module, $id_object) {
		$list_files = '';
		for($i = $cur_ind_array; $i < count($data); $i++) {
			if($data[$i]['ID_MODULE'] != $id_module)
				break;
			
			if($data[$i]['BB'] == $id_object)
				$list_files .= Functions::rendering_icon_file($data[$i]['PATH_TO_FILE'], $data[$i]['FILE_EXTENSION']);
		}
		return $list_files;
	}

	// Функция перемещения/восстановления из архива
	public function move_to_archive($post) {
		if(empty($post['nsyst']))
			return false;

		$nsyst = addslashes($post['nsyst']);
		
		$sql = "UPDATE " . $this->table . " SET ibd_arx=MOD(ibd_arx, 2)+1 WHERE id=" . $nsyst;
		if(DB::query($sql, DB::INSERT_OR_UPDATE) === false)
			return false;
		return true;
	}

	// Функция блокирования/разблокирования доступа к водителю
	public function lock_unlock_car($post) {
		if(empty($post['nsyst']))
			return false;
		
		if(User::get('role') != 9)
			return false;
		
		$id = addslashes($post['nsyst']);
		
		$sql = "UPDATE " . $this->table . " SET dostup=MOD(dostup, 2)+1 WHERE id=" . $id;
		if(DB::query($sql, DB::INSERT_OR_UPDATE) === false)
			return false;
		return true;
	}

	// Функция включения/отключения уведомлений на ТС
	public function car_enable_disable_notice_events($post) {
		if(empty($post['nsyst']))
			return false;
		
		if(User::get('role') != 9)
			return false;
		
		$id = addslashes($post['nsyst']);
		
		$sql = "UPDATE " . $this->table . " SET exception_notice_events=MOD(exception_notice_events+1, 2) WHERE id=" . $id;
		if(DB::query($sql, DB::INSERT_OR_UPDATE) === false)
			return false;
		return true;
	}

	// Функция включения/отключения уведомлений на ТС
	public function car_write_off($post) {
		if(empty($post['nsyst']))
			return false;
		
		if(User::get('role') != 9)
			return false;
		
		$id = addslashes($post['nsyst']);
		
		$sql = "UPDATE " . $this->table . " SET write_off=MOD(write_off+1, 2) WHERE id=" . $id;
		if(DB::query($sql, DB::INSERT_OR_UPDATE) === false)
			return false;
		return true;
	}

	public function generate_reference_car($post) {
		if(empty($post['nsyst']))
			return false;

		$id = addslashes($post['nsyst']);
		$role = User::get('role');
		$sql = '';

		if($role >= 2) {
			$sql = "SELECT cars.*,
					 x10.text as marka_text, x11.text as model_text, x12.text as color_text, x13.text as tip_strah_text, x14.text as kateg_gost_text, x15.text as kateg_ts_text,
					 x1.text as firma_osago, b.n_osago, CAST(cars.mileage AS CHAR) + 0 as mileage,
					 DATE_FORMAT(b.start_date_osago, '%d.%m.%Y') as start_dt_osago, DATE_FORMAT(b.end_date_osago, '%d.%m.%Y') as end_dt_osago, 
					 c.number_certificate, x2.text as firma_technical_inspection, c.address_technical_inspection,
					 DATE_FORMAT(c.date_certificate, '%d.%m.%Y') as date_certificate, DATE_FORMAT(c.end_date_certificate, '%d.%m.%Y') as end_date_certificate, 
					 g.s_pts, g.n_pts, DATE_FORMAT(g.date_pts, '%d.%m.%Y') as date_pts, x3.text as type_ts_pts, x4.text as firma_pts, 
					 h.s_certificate_reg, h.n_certificate_reg, DATE_FORMAT(h.date_certificate_reg, '%d.%m.%Y') as date_certificate_reg,
					 h.comment_certificate_reg, x5.text as org_certificate_reg, 
					 DATE_FORMAT(i.start_date, '%d.%m.%Y') as start_date_fire_extinguisher, DATE_FORMAT(i.end_date, '%d.%m.%Y') as end_date_fire_extinguisher, DATE_FORMAT(i.issued_date, '%d.%m.%Y') as issued_date_fire_extinguisher,
					 DATE_FORMAT(j.start_date, '%d.%m.%Y') as start_date_first_aid_kid, DATE_FORMAT(j.end_date, '%d.%m.%Y') as end_date_first_aid_kid, DATE_FORMAT(j.issued_date, '%d.%m.%Y') as issued_date_first_aid_kid,
					 DATE_FORMAT(k.issued_date, '%d.%m.%Y') as issued_date_warning_triangle, DATE_FORMAT(l.start_date, '%d.%m.%Y') as start_date_car_battery, l.type_battery, l.firma_battery, l.number_battery, DATE_FORMAT(l.producion_date, '%d.%m.%Y') as producion_date_car_battery, DATE_FORMAT(l.debit_date, '%d.%m.%Y') as debit_date_car_battery, l.standart_term_battery, l.standart_term_debit_battery,
					 m.number_dopog, DATE_FORMAT(m.date_start_dopog, '%d.%m.%Y') as date_start_dopog, DATE_FORMAT(m.date_end_dopog, '%d.%m.%Y') as date_end_dopog, x6.text as firma_dopog_text,
					 DATE_FORMAT(n.date_calibration, '%d.%m.%Y') as date_calibration, DATE_FORMAT(n.date_next_calibration, '%d.%m.%Y') as date_next_calibration, x7.text as firma_calibration_text,
					 o.number_tachograph, DATE_FORMAT(o.date_start_skzi, '%d.%m.%Y') as date_start_skzi, DATE_FORMAT(o.date_end_skzi, '%d.%m.%Y') as date_end_skzi, x8.text as model_tachograph_text,
					 p.number_dvr, p.marka_dvr, p.model_dvr,
					 q.number_glonass, DATE_FORMAT(q.date_glonass, '%d.%m.%Y') as date_glonass, q.number_dut_glonass_1, q.number_dut_glonass_2
					 FROM " . $this->table
			     . " LEFT JOIN osago b ON b.id_car=cars.id AND b.ibd_arx=1 
					 LEFT JOIN technical_inspection c ON c.id_car=cars.id AND c.ibd_arx=1 
					 LEFT JOIN pts g ON g.id_car=cars.id AND g.ibd_arx=1 
					 LEFT JOIN certificate_registration h ON h.id_car=cars.id AND h.ibd_arx=1
					 LEFT JOIN car_fire_extinguisher i ON i.id_car=cars.id AND i.ibd_arx=1
					 LEFT JOIN car_first_aid_kid j ON j.id_car=cars.id AND j.ibd_arx=1
					 LEFT JOIN car_warning_triangle k ON k.id_car=cars.id AND k.ibd_arx=1
					 LEFT JOIN car_battery l ON l.id_car=cars.id AND l.ibd_arx=1
					 LEFT JOIN cars_dopog m ON m.id_car=cars.id AND m.ibd_arx=1
					 LEFT JOIN car_calibration n ON n.id_car=cars.id AND n.ibd_arx=1
					 LEFT JOIN car_tachograph o ON o.id_car=cars.id AND o.ibd_arx=1
					 LEFT JOIN car_dvr p ON p.id_car=cars.id AND p.ibd_arx=1
					 LEFT JOIN car_glonass q ON q.id_car=cars.id AND q.ibd_arx=1
					 LEFT JOIN s2i_klass x1 ON x1.kod=b.firma_osago AND x1.nomer=15 
					 LEFT JOIN s2i_klass x2 ON x2.kod=c.firma_technical_inspection AND x2.nomer=16 
					 LEFT JOIN s2i_klass x3 ON x3.kod=g.type_ts_pts AND x3.nomer=6 
					 LEFT JOIN s2i_klass x4 ON x4.kod=g.firma_pts AND x4.nomer=10 
					 LEFT JOIN s2i_klass x5 ON x5.kod=h.org_certificate_reg AND x5.nomer=22
					 LEFT JOIN s2i_klass x6 ON x6.kod=m.firma_dopog AND x6.nomer=34
					 LEFT JOIN s2i_klass x7 ON x7.kod=n.firma_calibration AND x7.nomer=37
					 LEFT JOIN s2i_klass x8 ON x8.kod=o.model_tachograph AND x8.nomer=39

					 LEFT JOIN s2i_klass x10 ON cars.marka = x10.kod AND x10.nomer = 3 
					 LEFT JOIN s2i_klass x11 ON cars.model = x11.kod AND x11.nomer = 4 
					 LEFT JOIN s2i_klass x12 ON cars.color = x12.kod AND x12.nomer = 12 
					 LEFT JOIN s2i_klass x13 ON cars.tip_strah = x13.kod AND x13.nomer = 7 
					 LEFT JOIN s2i_klass x14 ON cars.kateg_gost = x14.kod AND x14.nomer = 9
					 LEFT JOIN s2i_klass x15 ON cars.kateg_ts = x15.kod AND x15.nomer = 5 

					 WHERE cars.id=" . $id;
		} else {
			$sql = '';
		}

		if(($data = DB::query($sql)) === false)
			return false;

		return ['data:application/pdf;base64,' . $this->generate_pdf($data)];
	}

	private function generate_pdf($data) {
		if(count($data) == 0)
			return false;

		setlocale(LC_CTYPE, 'ru_RU.UTF8');

		$mpdf = new \Mpdf\Mpdf(['default_font' => 'dejavusanscondensed']);
		$mpdf->autoScriptToLang = true;
		$mpdf->baseScript = 1;
		$mpdf->autoLangToFont = true;

		$mpdf->SetDocTemplate('template/car_template.pdf', 0);
		$mpdf->AddPage();

		$mpdf->SetFontSize(9);
		$mpdf->setFont('dejavusanscondensed', 'B');
		$mpdf->WriteText(37, 25.5, empty($data[0]['marka_text']) ? '' : $data[0]['marka_text']);
		$mpdf->WriteText(40, 31, empty($data[0]['model_text']) ? '' : $data[0]['model_text']);
		$mpdf->WriteText(48, 36.5, (empty($data[0]['gos_znak']) ? '' : $data[0]['gos_znak']) . ' ' . (empty($data[0]['n_reg']) ? '' : $data[0]['n_reg']));
		$mpdf->WriteText(34, 41.5, empty($data[0]['color_text']) ? '' : $data[0]['color_text']);
		$mpdf->WriteText(50, 47, empty($data[0]['god_car']) ? '' : $data[0]['god_car']);
		$mpdf->WriteText(52, 52.5, empty($data[0]['kateg_ts_text']) ? '' : $data[0]['kateg_ts_text']);
		$mpdf->WriteText(57, 58, empty($data[0]['kateg_gost_text']) ? '' : $data[0]['kateg_gost_text']);
		$mpdf->WriteText(62, 63, empty($data[0]['tip_strah_text']) ? '' : $data[0]['tip_strah_text']);
		$mpdf->WriteText(81, 68.5, empty($data[0]['vin']) ? '' : $data[0]['vin']);
		$mpdf->WriteText(43, 73.5, empty($data[0]['n_dvig']) ? '' : $data[0]['n_dvig']);
		$mpdf->WriteText(73, 79, empty($data[0]['shassi']) ? '' : $data[0]['shassi']);
		$mpdf->WriteText(92, 84.5, empty($data[0]['kuzov']) ? '' : $data[0]['kuzov']);
		$mpdf->WriteText(59, 90, empty($data[0]['mass_max']) ? '' : $data[0]['mass_max']);
		$mpdf->WriteText(62, 95.5, empty($data[0]['mass_min']) ? '' : $data[0]['mass_min']);
		$mpdf->WriteText(52, 101, empty($data[0]['car_vat']) ? '' : $data[0]['car_vat']);
		$mpdf->WriteText(81, 106.5, empty($data[0]['car_v']) ? '' : $data[0]['car_v']);
		$mpdf->WriteText(70, 111.5, empty($data[0]['mileage_oil']) ? '' : $data[0]['mileage_oil']);
		
		$mpdf->WriteText(52, 117, empty($data[0]['basic_fuel']) ? '' : $data[0]['basic_fuel']);
		$mpdf->WriteText(90, 122, empty($data[0]['summer_fuel']) ? '' : $data[0]['summer_fuel']);
		$mpdf->WriteText(90, 127.5, empty($data[0]['winter_fuel']) ? '' : $data[0]['winter_fuel']);
		$mpdf->WriteText(67, 133.2, empty($data[0]['inventory_n']) ? '' : $data[0]['inventory_n']);
		$mpdf->WriteText(104, 138.5, empty($data[0]['balance_price']) ? '' : $data[0]['balance_price']);
		$mpdf->WriteText(57, 143.5, empty($data[0]['mileage']) ? '' : $data[0]['mileage']);

		$mpdf->WriteText(82, 159.5, (empty($data[0]['s_certificate_reg']) ? '' : $data[0]['s_certificate_reg']) . ' ' . (empty($data[0]['n_certificate_reg']) ? '' : $data[0]['n_certificate_reg']));
		$mpdf->WriteText(52, 165, empty($data[0]['date_certificate_reg']) ? '' : $data[0]['date_certificate_reg']);
		$mpdf->WriteText(49, 170.5, empty($data[0]['org_certificate_reg']) ? '' : $data[0]['org_certificate_reg']);

		$mpdf->WriteText(60, 187, (empty($data[0]['s_pts']) ? '' : $data[0]['s_pts']) . ' ' . (empty($data[0]['n_pts']) ? '' : $data[0]['n_pts']));
		$mpdf->WriteText(52, 192.5, empty($data[0]['date_pts']) ? '' : $data[0]['date_pts']);
		$mpdf->WriteText(55, 197.5, empty($data[0]['type_ts_pts']) ? '' : $data[0]['type_ts_pts']);
		$mpdf->WriteText(70, 203, empty($data[0]['firma_pts']) ? '' : $data[0]['firma_pts']);

		$mpdf->WriteText(57, 219, empty($data[0]['n_osago']) ? '' : $data[0]['n_osago']);
		$mpdf->WriteText(69, 224.5, empty($data[0]['end_dt_osago']) ? '' : $data[0]['end_dt_osago']);
		$mpdf->WriteText(68, 230, empty($data[0]['firma_osago']) ? '' : $data[0]['firma_osago']);

		$mpdf->WriteText(62, 246, empty($data[0]['number_certificate']) ? '' : $data[0]['number_certificate']);
		$mpdf->WriteText(55, 251.5, (empty($data[0]['date_certificate']) ? '' : $data[0]['date_certificate']) . ' - ' . (empty($data[0]['end_date_certificate']) ? '' : $data[0]['end_date_certificate']));
		$mpdf->WriteText(53, 257, empty($data[0]['firma_technical_inspection']) ? '' : $data[0]['firma_technical_inspection']);
		$mpdf->WriteText(63, 262, empty($data[0]['address_technical_inspection']) ? '' : $data[0]['address_technical_inspection']);

		$mpdf->AddPage();

		$mpdf->WriteText(67, 19, empty($data[0]['number_dopog']) ? '' : $data[0]['number_dopog']);
		$mpdf->WriteText(55, 24.5, (empty($data[0]['date_start_dopog']) ? '' : $data[0]['date_start_dopog']) . ' - ' . (empty($data[0]['date_end_dopog']) ? '' : $data[0]['date_end_dopog']));
		$mpdf->WriteText(52, 30, empty($data[0]['firma_dopog_text']) ? '' : $data[0]['firma_dopog_text']);

		$mpdf->WriteText(85, 46, empty($data[0]['date_calibration']) ? '' : $data[0]['date_calibration']);
		$mpdf->WriteText(107, 51, empty($data[0]['date_next_calibration']) ? '' : $data[0]['date_next_calibration']);
		$mpdf->WriteText(52, 57, empty($data[0]['firma_calibration_text']) ? '' : $data[0]['firma_calibration_text']);

		$mpdf->WriteText(38, 73, empty($data[0]['number_tachograph']) ? '' : $data[0]['number_tachograph']);
		$mpdf->WriteText(40, 78, (empty($data[0]['date_start_skzi']) ? '' : $data[0]['date_start_skzi']) . ' - ' . (empty($data[0]['date_end_skzi']) ? '' : $data[0]['date_end_skzi']));
		$mpdf->WriteText(75, 83.5, empty($data[0]['model_tachograph_text']) ? '' : $data[0]['model_tachograph_text']);

		$mpdf->WriteText(63, 100, empty($data[0]['number_glonass']) ? '' : $data[0]['number_glonass']);
		$mpdf->WriteText(55, 105, empty($data[0]['date_glonass']) ? '' : $data[0]['date_glonass']);
		$mpdf->WriteText(73, 110.5, empty($data[0]['number_dut_glonass_1']) ? '' : $data[0]['number_dut_glonass_1']);
		$mpdf->WriteText(73, 116, empty($data[0]['number_dut_glonass_2']) ? '' : $data[0]['number_dut_glonass_2']);

		$mpdf->WriteText(37, 137.5, empty($data[0]['issued_date_fire_extinguisher']) ? '' : $data[0]['issued_date_fire_extinguisher']);
		$mpdf->WriteText(55, 143, (empty($data[0]['start_date_fire_extinguisher']) ? '' : $data[0]['start_date_fire_extinguisher']) . ' - ' . (empty($data[0]['end_date_fire_extinguisher']) ? '' : $data[0]['end_date_fire_extinguisher']));

		$mpdf->WriteText(40, 159, empty($data[0]['issued_date_first_aid_kid']) ? '' : $data[0]['issued_date_first_aid_kid']);
		$mpdf->WriteText(55, 164, (empty($data[0]['start_date_first_aid_kid']) ? '' : $data[0]['start_date_first_aid_kid']) . ' - ' . (empty($data[0]['end_date_first_aid_kid']) ? '' : $data[0]['end_date_first_aid_kid']));

		$mpdf->WriteText(37, 180.5, empty($data[0]['issued_date_warning_triangle']) ? '' : $data[0]['issued_date_warning_triangle']);

		$mpdf->WriteText(32, 196.5, empty($data[0]['type_battery']) ? '' : $data[0]['type_battery']);
		$mpdf->WriteText(37, 202, empty($data[0]['number_battery']) ? '' : $data[0]['number_battery']);
		$mpdf->WriteText(52, 207.5, empty($data[0]['firma_battery']) ? '' : $data[0]['firma_battery']);
		$mpdf->WriteText(63, 212.5, empty($data[0]['producion_date_car_battery']) ? '' : $data[0]['producion_date_car_battery']);
		$mpdf->WriteText(56, 218, empty($data[0]['start_date_car_battery']) ? '' : $data[0]['start_date_car_battery']);
		$mpdf->WriteText(88, 223.5, empty($data[0]['standart_term_battery']) ? '' : $data[0]['standart_term_battery']);
		$mpdf->WriteText(107, 229, empty($data[0]['standart_term_debit_battery']) ? '' : $data[0]['standart_term_debit_battery']);
		$mpdf->WriteText(55, 234.5, empty($data[0]['debit_date_car_battery']) ? '' : $data[0]['debit_date_car_battery']);

		$mpdf->WriteText(37, 250.5, empty($data[0]['number_dvr']) ? '' : $data[0]['number_dvr']);
		$mpdf->WriteText(37, 256, empty($data[0]['marka_dvr']) ? '' : $data[0]['marka_dvr']);
		$mpdf->WriteText(41, 261, empty($data[0]['model_dvr']) ? '' : $data[0]['model_dvr']);

		$temp_file = tempnam(sys_get_temp_dir(), 'car-');
		$mpdf->Output($temp_file);
		$base64_str = base64_encode(file_get_contents($temp_file));

		self::remove_file_pdf($temp_file);

		return $base64_str;
	}

	/*
		Удаление файла
		$path_file - файл, который необходимо удалить
	*/
	public function remove_file_pdf($path_file) {
		if(file_exists($path_file))
			unlink($path_file);
	}
}