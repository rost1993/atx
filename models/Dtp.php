<?php

namespace IcKomiApp\models;

use IcKomiApp\core\Model;
use IcKomiApp\lib\Database\DB;
use IcKomiApp\core\Logic;
use IcKomiApp\core\Functions;

class Dtp extends Model {
	protected $table = 'dtp';
	protected $array_file_extension = ['pdf', 'jpeg', 'jpg', 'png'];
	protected $remove_directory = 1;

	protected $sql_get_record = "SELECT {table}.*, b.kodrai as kodrai_ts, c.kodrai kodrai_driver FROM {table} 
									INNER JOIN cars b ON b.id={table}.id_car
									LEFT JOIN drivers c ON c.id={table}.id_driver
									WHERE {table}.id={id}";

	// Получение всех полей таблицы по ее ID (без раскрытия справочников)
	public function get($get) {
		$data = parent::get($get);

		if(($data_files = $this->get_files(addslashes($_GET['id']))) === false)
			return false;
			
		$list_files_doc = $list_files_image = '';

		for($i = 0; $i < count($data_files); $i++) {
			if($data_files[$i]['file_extension'] == 'pdf')
				$list_files_doc .= Functions::rendering_icon_file($data_files[$i]['path_to_file'], $data_files[$i]['file_extension'], $data_files[$i]['id'], 12, true);
			else
				$list_files_image .= Functions::rendering_icon_file($data_files[$i]['path_to_file'], $data_files[$i]['file_extension'], $data_files[$i]['id'], 12, true);
		}
		$data[0]['list_files_doc'] = $list_files_doc;
		$data[0]['list_files_image'] = $list_files_image;
		return $data;
		//echo json_encode(array(1, $data[0], $list_files_doc, $list_files_image));
	}

	public function get_list($post = []) {
		/*if(!ServiceFunction::check_number($id))
			return false;

		Session::start();
		$role = Session::get('role');
		$kodrai = Session::get('slugba');
		Session::commit();
		
		$where = '';
		if($id != -1)
			$where = " WHERE a.id=" . $id;

		if($role == 9 || $role == 8) {
			$this->sql_get_list = "SELECT a.id, c.id as id_driver, b.id as id_car, x1.text as kodrai_ts, x2.text as slugba_ts, x3.text as marka_ts, x4.text as model_ts, b.gos_znak, b.n_reg, "
				. " CONCAT(c.fam, ' ', c.imj, ' ', c.otch) as  driver, "
				. " x5.text as kodrai_driver, x6.text as slugba_driver, a.date_committing, a.time_committing, a.place_committing, a.comment_committing, a.sum_committing, a.offender "
				. " FROM {table} a "
				. " LEFT JOIN cars b ON b.id=a.id_car "
				. " LEFT JOIN drivers c ON c.id=a.id_driver "
				. " LEFT JOIN s2i_klass x1 ON x1.kod=b.kodrai AND x1.nomer=11 "
				. " LEFT JOIN s2i_klass x2 ON x2.kod=b.slugba AND x2.nomer=1 "
				. " LEFT JOIN s2i_klass x3 ON x3.kod=b.marka AND x3.nomer=3 "
				. " LEFT JOIN s2i_klass x4 ON x4.kod=b.model AND x4.nomer=4 "
				. " LEFT JOIN s2i_klass x5 ON x5.kod=c.kodrai AND x5.nomer=11 "
				. " LEFT JOIN s2i_klass x6 ON x6.kod=c.slugba AND x6.nomer=1 " . $where
				. " ORDER BY a.date_committing DESC ";
		} else if($role == 4) {
			$this->sql_get_list = "SELECT a.id, c.id as id_driver, b.id as id_car, x1.text as kodrai_ts, x2.text as slugba_ts, x3.text as marka_ts, x4.text as model_ts, b.gos_znak, b.n_reg, "
				. " CONCAT(c.fam, ' ', c.imj, ' ', c.otch) as  driver, "
				. " x5.text as kodrai_driver, x6.text as slugba_driver, a.date_committing, a.time_committing, a.place_committing, a.comment_committing, a.sum_committing, a.offender "
				. " FROM {table} a "
				. " INNER JOIN cars b ON b.id=a.id_car AND b.dostup=1 AND b.slugba IN " . User::get_all_slugba()
				. " INNER JOIN drivers c ON c.id=a.id_driver AND c.dostup=1 AND c.slugba IN " . User::get_all_slugba()
				. " LEFT JOIN s2i_klass x1 ON x1.kod=b.kodrai AND x1.nomer=11 "
				. " LEFT JOIN s2i_klass x2 ON x2.kod=b.slugba AND x2.nomer=1 "
				. " LEFT JOIN s2i_klass x3 ON x3.kod=b.marka AND x3.nomer=3 "
				. " LEFT JOIN s2i_klass x4 ON x4.kod=b.model AND x4.nomer=4 "
				. " LEFT JOIN s2i_klass x5 ON x5.kod=c.kodrai AND x5.nomer=11 "
				. " LEFT JOIN s2i_klass x6 ON x6.kod=c.slugba AND x6.nomer=1 " . $where
				. " ORDER BY a.date_committing DESC ";
		} else if($role == 3) {
			$this->sql_get_list = "SELECT a.id, c.id as id_driver, b.id as id_car, x1.text as kodrai_ts, x2.text as slugba_ts, x3.text as marka_ts, x4.text as model_ts, b.gos_znak, b.n_reg, "
				. " CONCAT(c.fam, ' ', c.imj, ' ', c.otch) as  driver, "
				. " x5.text as kodrai_driver, x6.text as slugba_driver, a.date_committing, a.time_committing, a.place_committing, a.comment_committing, a.sum_committing, a.offender "
				. " FROM {table} a "
				. " LEFT JOIN cars b ON b.id=a.id_car AND b.dostup=1 "
				. " LEFT JOIN drivers c ON c.id=a.id_driver AND c.dostup=1 "
				. " LEFT JOIN s2i_klass x1 ON x1.kod=b.kodrai AND x1.nomer=11 "
				. " LEFT JOIN s2i_klass x2 ON x2.kod=b.slugba AND x2.nomer=1 "
				. " LEFT JOIN s2i_klass x3 ON x3.kod=b.marka AND x3.nomer=3 "
				. " LEFT JOIN s2i_klass x4 ON x4.kod=b.model AND x4.nomer=4 "
				. " LEFT JOIN s2i_klass x5 ON x5.kod=c.kodrai AND x5.nomer=11 "
				. " LEFT JOIN s2i_klass x6 ON x6.kod=c.slugba AND x6.nomer=1 " . $where
				. " ORDER BY a.date_committing DESC ";
		} else if($role == 2) {
			$this->sql_get_list = "SELECT a.id, c.id as id_driver, b.id as id_car, x1.text as kodrai_ts, x2.text as slugba_ts, x3.text as marka_ts, x4.text as model_ts, b.gos_znak, b.n_reg, "
				. " CONCAT(c.fam, ' ', c.imj, ' ', c.otch) as  driver, "
				. " x5.text as kodrai_driver, x6.text as slugba_driver, a.date_committing, a.time_committing, a.place_committing, a.comment_committing, a.sum_committing, a.offender "
				. " FROM {table} a "
				. " INNER JOIN cars b ON b.id=a.id_car AND b.dostup=1 AND b.kodrai=" . $kodrai
				. " INNER JOIN drivers c ON c.id=a.id_driver AND c.dostup=1 AND c.kodrai=" . $kodrai
				. " LEFT JOIN s2i_klass x1 ON x1.kod=b.kodrai AND x1.nomer=11 "
				. " LEFT JOIN s2i_klass x2 ON x2.kod=b.slugba AND x2.nomer=1 "
				. " LEFT JOIN s2i_klass x3 ON x3.kod=b.marka AND x3.nomer=3 "
				. " LEFT JOIN s2i_klass x4 ON x4.kod=b.model AND x4.nomer=4 "
				. " LEFT JOIN s2i_klass x5 ON x5.kod=c.kodrai AND x5.nomer=11 "
				. " LEFT JOIN s2i_klass x6 ON x6.kod=c.slugba AND x6.nomer=1 " . $where
				. " ORDER BY a.date_committing DESC ";
		} else {
			return false;
		}*/

		$this->sql_get_list = "SELECT a.id, c.id as id_driver, b.id as id_car, x1.text as kodrai_ts, x2.text as slugba_ts, x3.text as marka_ts, x4.text as model_ts, b.gos_znak, b.n_reg, "
				. " CONCAT(c.fam, ' ', c.imj, ' ', c.otch) as  driver, "
				. " x5.text as kodrai_driver, x6.text as slugba_driver, a.date_committing, a.time_committing, a.place_committing, a.comment_committing, a.sum_committing, a.offender "
				. " FROM {table} a "
				. " LEFT JOIN cars b ON b.id=a.id_car "
				. " LEFT JOIN drivers c ON c.id=a.id_driver "
				. " LEFT JOIN s2i_klass x1 ON x1.kod=b.kodrai AND x1.nomer=11 "
				. " LEFT JOIN s2i_klass x2 ON x2.kod=b.slugba AND x2.nomer=1 "
				. " LEFT JOIN s2i_klass x3 ON x3.kod=b.marka AND x3.nomer=3 "
				. " LEFT JOIN s2i_klass x4 ON x4.kod=b.model AND x4.nomer=4 "
				. " LEFT JOIN s2i_klass x5 ON x5.kod=c.kodrai AND x5.nomer=11 "
				. " LEFT JOIN s2i_klass x6 ON x6.kod=c.slugba AND x6.nomer=1 "
				. " ORDER BY a.date_committing DESC ";

		if(($data = parent::get_list()) === false)
			return false;

		$html = $this->draw_result_table($data, 1, (empty($post['page']) ? 1 : $post['page']));

		return ['search_result' => $html];
	}

	// Функция поиска
	public function search($post, $flg_excel = -1) {
		if(!$array_data_decode = json_decode($post['JSON']))
			return false;
		
		$where = '';
		foreach($array_data_decode as $field => $array_value) {
			$array_value_decode = (array)$array_value;

			if(trim($array_value_decode['value']) == "0" || (mb_strlen(trim($array_value_decode['value'])) == 0))
				continue;
			
			if($field == 'gos_znak') {
				if(mb_strlen($where) == 0)
					$where = " b.gos_znak LIKE '%" . $array_value_decode['value'] . "%'";
				else
					$where .= " AND b.gos_znak LIKE '%" . $array_value_decode['value'] . "%'";
			} else if($field == 'fam'){
				if(mb_strlen($where) == 0)
					$where = " c.fam LIKE '%" . $array_value_decode['value'] . "%'";
				else
					$where .= " AND c.fam LIKE '%" . $array_value_decode['value'] . "%'";
			} else if($field == 'imj'){
				if(mb_strlen($where) == 0)
					$where = " c.imj LIKE '%" . $array_value_decode['value'] . "%'";
				else
					$where .= " AND c.imj LIKE '%" . $array_value_decode['value'] . "%'";
			} else if($field == 'kodrai') {
				if(mb_strlen($where) == 0)
					$where = " (b.kodrai=" . $array_value_decode['value'] . " OR c.kodrai=" . $array_value_decode['value'] . ")";
				else
					$where .= " AND (b.kodrai=" . $array_value_decode['value'] . " OR c.kodrai=" . $array_value_decode['value'] . ")";
			} else if($field == 'slugba') {
				if(mb_strlen($where) == 0)
					$where = " (b.slugba=" . $array_value_decode['value'] . " OR c.slugba=" . $array_value_decode['value'] . ")";
				else
					$where .= " AND (b.slugba=" . $array_value_decode['value'] . " OR c.slugba=" . $array_value_decode['value'] . ")";
			} else if($field == 'date_dtp1') {
				if(mb_strlen($where) == 0)
					$where = " a.date_committing >= '" . Functions::convertToMySQLDateFormat($array_value_decode['value']) . "'";
				else
					$where .= " AND a.date_committing >= '" . Functions::convertToMySQLDateFormat($array_value_decode['value']) . "'";
			} else if($field == 'date_dtp2') {
				if(mb_strlen($where) == 0)
					$where = " a.date_committing <= '" . Functions::convertToMySQLDateFormat($array_value_decode['value']) . "'";
				else
					$where .= " AND a.date_committing <= '" . Functions::convertToMySQLDateFormat($array_value_decode['value']) . "'";
			}
		}
		
		/*Session::start();
		$role = Session::get('role');
		$kodrai = Session::get('slugba');
		Session::commit();*/
		
		if(mb_strlen($where) > 0)
				$where = " WHERE " . $where;

		$sql = "SELECT a.id, c.id as id_driver, b.id as id_car, x3.text as marka_ts, x4.text as model_ts, b.gos_znak, CONCAT(c.fam, ' ', c.imj, ' ', c.otch) as  driver, "
				. " a.date_committing, a.time_committing, a.place_committing, a.comment_committing, a.offender, IF(a.offender = 1, 'ДА', 'НЕТ') as offender_text "
				. " FROM dtp a "
				. " LEFT JOIN cars b ON b.id=a.id_car "
				. " LEFT JOIN drivers c ON c.id=a.id_driver "
				. " LEFT JOIN s2i_klass x3 ON x3.kod=b.marka AND x3.nomer=3 "
				. " LEFT JOIN s2i_klass x4 ON x4.kod=b.model AND x4.nomer=4 " . $where
				. " ORDER BY a.date_committing DESC";

		/*if($role == 9 || $role == 8) {
			$sqlQuery = "SELECT a.id, c.id as id_driver, b.id as id_car, x1.text as kodrai_ts, x2.text as slugba_ts, x3.text as marka_ts, x4.text as model_ts, b.gos_znak, CONCAT(c.fam, ' ', c.imj, ' ', c.otch) as  driver, "
				. " x5.text as kodrai_driver, x6.text as slugba_driver, a.date_committing, a.time_committing, a.place_committing, a.comment_committing, a.offender, IF(a.offender = 1, 'ДА', 'НЕТ') as offender_text "
				. " FROM dtp a "
				. " LEFT JOIN cars b ON b.id=a.id_car "
				. " LEFT JOIN drivers c ON c.id=a.id_driver "
				. " LEFT JOIN s2i_klass x1 ON x1.kod=b.kodrai AND x1.nomer=11 "
				. " LEFT JOIN s2i_klass x2 ON x2.kod=b.slugba AND x2.nomer=1 "
				. " LEFT JOIN s2i_klass x3 ON x3.kod=b.marka AND x3.nomer=3 "
				. " LEFT JOIN s2i_klass x4 ON x4.kod=b.model AND x4.nomer=4 "
				. " LEFT JOIN s2i_klass x5 ON x5.kod=c.kodrai AND x5.nomer=11 "
				. " LEFT JOIN s2i_klass x6 ON x6.kod=c.slugba AND x6.nomer=1 " . $where
				. " ORDER BY a.date_committing DESC";
		} else if($role == 4) {
			if(mb_strlen($where) == 0)
				$where = " WHERE b.slugba IN " . User::get_all_slugba() . " OR c.slugba IN " . User::get_all_slugba();
			else
				$where .= " AND (b.slugba IN " . User::get_all_slugba() . " OR c.slugba IN " . User::get_all_slugba() . ") ";

			$sqlQuery = "SELECT a.id, c.id as id_driver, b.id as id_car, x1.text as kodrai_ts, x2.text as slugba_ts, x3.text as marka_ts, x4.text as model_ts, b.gos_znak, CONCAT(c.fam, ' ', c.imj, ' ', c.otch) as  driver, "
				. " x5.text as kodrai_driver, x6.text as slugba_driver, a.date_committing, a.time_committing, a.place_committing, a.comment_committing, a.offender, IF(a.offender = 1, 'ДА', 'НЕТ') as offender_text "
				. " FROM dtp a "
				. " INNER JOIN cars b ON b.id=a.id_car AND b.dostup=1 "
				. " INNER JOIN drivers c ON c.id=a.id_driver AND c.dostup=1 "
				. " LEFT JOIN s2i_klass x1 ON x1.kod=b.kodrai AND x1.nomer=11 "
				. " LEFT JOIN s2i_klass x2 ON x2.kod=b.slugba AND x2.nomer=1 "
				. " LEFT JOIN s2i_klass x3 ON x3.kod=b.marka AND x3.nomer=3 "
				. " LEFT JOIN s2i_klass x4 ON x4.kod=b.model AND x4.nomer=4 "
				. " LEFT JOIN s2i_klass x5 ON x5.kod=c.kodrai AND x5.nomer=11 "
				. " LEFT JOIN s2i_klass x6 ON x6.kod=c.slugba AND x6.nomer=1 " . $where
				. " ORDER BY a.date_committing DESC";
		} else if($role == 2) {
			if(mb_strlen($where) == 0)
				$where = " WHERE b.kodrai=" . $kodrai . " OR c.kodrai=" . $kodrai;
			else
				$where .= " AND (b.kodrai=" . $kodrai . " OR c.kodrai=" . $kodrai . ") ";

			$sqlQuery = "SELECT a.id, c.id as id_driver, b.id as id_car, x1.text as kodrai_ts, x2.text as slugba_ts, x3.text as marka_ts, x4.text as model_ts, b.gos_znak, CONCAT(c.fam, ' ', c.imj, ' ', c.otch) as  driver, "
				. " x5.text as kodrai_driver, x6.text as slugba_driver, a.date_committing, a.time_committing, a.place_committing, a.comment_committing, a.offender, IF(a.offender = 1, 'ДА', 'НЕТ') as offender_text "
				. " FROM dtp a "
				. " INNER JOIN cars b ON b.id=a.id_car AND b.dostup=1 "
				. " INNER JOIN drivers c ON c.id=a.id_driver AND c.dostup=1 "
				. " LEFT JOIN s2i_klass x1 ON x1.kod=b.kodrai AND x1.nomer=11 "
				. " LEFT JOIN s2i_klass x2 ON x2.kod=b.slugba AND x2.nomer=1 "
				. " LEFT JOIN s2i_klass x3 ON x3.kod=b.marka AND x3.nomer=3 "
				. " LEFT JOIN s2i_klass x4 ON x4.kod=b.model AND x4.nomer=4 "
				. " LEFT JOIN s2i_klass x5 ON x5.kod=c.kodrai AND x5.nomer=11 "
				. " LEFT JOIN s2i_klass x6 ON x6.kod=c.slugba AND x6.nomer=1 " . $where
				. " ORDER BY a.date_committing DESC";
		} else if($role == 1) {
			$sqlQuery = "SELECT a.id, c.id as id_driver, b.id as id_car, x1.text as kodrai_ts, x2.text as slugba_ts, x3.text as marka_ts, x4.text as model_ts, b.gos_znak, CONCAT(c.fam, ' ', c.imj, ' ', c.otch) as  driver, "
				. " x5.text as kodrai_driver, x6.text as slugba_driver, a.date_committing, a.time_committing, a.place_committing, a.comment_committing, a.offender, IF(a.offender = 1, 'ДА', 'НЕТ') as offender_text "
				. " FROM dtp a "
				. " LEFT JOIN cars b ON b.id=a.id_car AND b.dostup=1"
				. " LEFT JOIN drivers c ON c.id=a.id_driver AND c.dostup=1"
				. " LEFT JOIN s2i_klass x1 ON x1.kod=b.kodrai AND x1.nomer=11 "
				. " LEFT JOIN s2i_klass x2 ON x2.kod=b.slugba AND x2.nomer=1 "
				. " LEFT JOIN s2i_klass x3 ON x3.kod=b.marka AND x3.nomer=3 "
				. " LEFT JOIN s2i_klass x4 ON x4.kod=b.model AND x4.nomer=4 "
				. " LEFT JOIN s2i_klass x5 ON x5.kod=c.kodrai AND x5.nomer=11 "
				. " LEFT JOIN s2i_klass x6 ON x6.kod=c.slugba AND x6.nomer=1 " . $where
				. " ORDER BY a.date_committing DESC";
		} else {
			$sql = "";
		}*/

		if(($data = DB::query($sql)) === false)
			return false;
		
		$html = ($_POST['excel'] != -1) ? $this->generate_excel_document($data) : $this->draw_result_table($data, 2, addslashes($post['page']), 2);

		return [ 'search_result' => $html];
	}

	// Функция построения списка после результатов выборки
	// data - массив с данными
	// type - тип построения: 1 - строки их в блоки div, 2 - строит просто таблицу
	function draw_result_table($data, $type = 1, $page = 0) {
		$html = '';
		
		if(count($data) == 0) {
			$html .= "<p>По Вашему запросу ничего не найдено</p>";
		} else {
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
			
			$style_border = "style='vertical-align: middle; border: 1px solid gray;'";
			
			$html_table = "<table class='table table-striped table-sm table-hover text-center' style='font-size: 13px;'>";
			$html_table .= "<tr class='table-info'>"
					. "<th " . $style_border . " scope='col'>№ п/п</th>"
					. "<th " . $style_border . " scope='col'>Транспортное средство</th>"
					. "<th " . $style_border . " scope='col'>Водитель</th>"
					. "<th " . $style_border . " scope='col'>Дата и время совершения</th>"
					. "<th " . $style_border . " scope='col'>Место совершения</th>"
					. "<th " . $style_border . " scope='col'>Описание</th>"
				. "</tr>";

			$j = 0;
			for($i = 0; $i < count($data); $i++) {
				$page_driver = "http://" . $_SERVER['HTTP_HOST'] . "/driver?id=" . $data[$i]['id_driver'] . "";
				$page_car = "http://" . $_SERVER['HTTP_HOST'] . "/car?id=" . $data[$i]['id_car'] . "";
				$page_dtp = "window.open('http://" . $_SERVER['HTTP_HOST'] . "/dtp?id=" . $data[$i]['id'] . "')";
				
				$j++;
				if(($j >= $record_tail_limit) && ($j <= $record_head_limit)) {
					$html_table .= "<tr style='cursor: pointer;' data-nsyst=" . $data[$i]['id'] . ">"
						. "<td " . $style_border . " onclick=" . $page_dtp . ">" . ($i+1) . "</td>"
						. "<td " . $style_border . "><a href='" . $page_car . "' target='_blank'>" . $data[$i]['gos_znak'] . "<br>" . $data[$i]['marka_ts'] . " " . $data[$i]['model_ts'] . "</a></td>"
						. "<td " . $style_border . "><a href='" . $page_driver . "' target='_blank'>" . $data[$i]['driver'] . "</a></td>"
						. "<td " . $style_border . " onclick=" . $page_dtp . ">" . Functions::convertToDate($data[$i]['date_committing']) . " " . $data[$i]['time_committing'] . "</td>"
						. "<td style='vertical-align: middle; border: 1px solid gray; font-size: 11px;' onclick=" . $page_dtp . ">" . $data[$i]['place_committing'] . "</td>"
						. "<td class='text-left' style='vertical-align: middle; border: 1px solid gray; font-size: 11px;' onclick=" . $page_dtp . ">" . $data[$i]['comment_committing'] . "</td>"
						. "</tr>";
				}
				
				if($j > $record_head_limit)
					break;
			}
			
			$html_table .= "</table>";

			// Class button
			$class_btn = ($type == 1) ? 'btn-list-dtp' : 'btn-search-dtp';
			
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

	}

	// Функция построения списка ДТП по ID транспортного средства
	public function get_list_dtp_for_car($id_car) {
		$sql = "SELECT a.* FROM " . $this->table . " a"
			. " WHERE a.id_car=" . $id_car
			. " ORDER BY a.date_committing DESC";
		
		if(($data = DB::query($sql)) === false)
			return false;
		return $data;
	}
	
	// Функция построения списка ДТП по ID водителя
	public function get_list_dtp_for_driver($id_driver) {
		$sql = "SELECT a.* FROM " . $this->table . " a"
			. " WHERE a.id_driver=" . $id_driver
			. " ORDER BY a.date_committing DESC";
		
		if(($data = DB::query($sql)) === false)
			return false;
		return $data;
	}

		// Функция отрисовки списка ПТС
	function rendering_list($post) {
		if(empty($_POST['nsyst']) || empty($_POST['type']))
			return false;

		$data = [];
		if(addslashes($_POST['type']) == 1) {
			if(($data = $this->get_list_dtp_for_car(addslashes($_POST['nsyst']))) === false)
				return false;
		} else {
			if(($data = $this->get_list_dtp_for_driver(addslashes($_POST['nsyst']))) === false)
				return false;
		}

		/*Session::start();
		$role = Session::get('role');
		Session::commit();*/

		$role = 9;
		
		$html = "";
		if(count($data) == 0) {
			$html = "<div class='text-center'><p>Сведений в базе данных не найдено!</p></div>";
		} else {
			$style_border = "style='vertical-align: middle; border: 1px solid gray;'";

			// Формируем готовый HTML код для списка закрепленных ТС
			$html = "<table class='table table-striped table-sm table-hover text-center' style='font-size: 13px; margin: 10px;'>"
					. "<tr class='table-info'>"
						. "<th " . $style_border . " scope='col'>№ п/п</th>"
						. "<th " . $style_border . " scope='col'>Дата и время ДТП</th>"
						. "<th " . $style_border . " scope='col'>Место ДТП</th>"
						. "<th " . $style_border . " scope='col'>Сумма ущерба, руб.</th>"
						. "<th " . $style_border . " scope='col'>Описание ДТП</th>"
						. "<th " . $style_border . " scope='col'>Скорректировать</th>"
					. "</tr>";
	
			for($i = 0; $i < count($data); $i++) {
				$page = 'http://' . $_SERVER['HTTP_HOST'] . '/pages/dtp.php?id=' . $data[$i]['id'];
				$html .= "<tr>"
					. "<td " . $style_border . ">" . ($i+1) . "</td>"
					. "<td " . $style_border . ">" . Functions::convertToDate($data[$i]['date_committing']) . "&nbsp" . $data[$i]['time_committing'] . "</td>"
					. "<td " . $style_border . ">" . $data[$i]['place_committing'] . "</td>"
					. "<td " . $style_border . ">" . $data[$i]['sum_committing'] . "</td>"
					. "<td class='text-left' style='vertical-align: middle; border: 1px solid gray; font-size: 11px;'>" . $data[$i]['comment_committing'] . "</td>";
				$html .= "<td " . $style_border . ">";
				$html .= (($role > 1) && ($role != 4)) ? "<a href='" . $page . "' role='button' class='btn btn-sm btn-info' target='_blank' title='Скорректировать ДТП' onclick='" . $page . "'><span class='fa fa-pencil'>&nbsp;</span></a>": "";
				$html .= "</td></tr>";
			}
		}
		
		return [$html];
	}

	// Функция, которая получает список файлов, прикрепленных к данному ремонту
	public function get_files($id) {
		$sql = "SELECT * FROM files WHERE id_object=" . $id . " AND category_file=12";
		if(($data = DB::query($sql)) === false)
			return false;
		return $data;
	}
	
}