<?php

namespace IcKomiApp\models;

use IcKomiApp\core\User;
use IcKomiApp\core\Model;
use IcKomiApp\core\Logic;
use IcKomiApp\core\Functions;
use IcKomiApp\lib\Database\DB;

class Adm extends Model {
	protected $table = 'adm_offense';
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
			
		$list_files_doc = '';

		for($i = 0; $i < count($data_files); $i++) {
			if($data_files[$i]['file_extension'] == 'pdf')
				$list_files_doc .= Functions::rendering_icon_file($data_files[$i]['path_to_file'], $data_files[$i]['file_extension'], $data_files[$i]['id'], 15, true);
		}
		$data[0]['list_files_doc'] = $list_files_doc;
		return $data;
	}

	public function get_list($post = []) {
		$role = User::get('role');

		if($role == 9)
			$this->sql_get_list = "SELECT a.*, c.id as id_driver, b.id as id_car, x3.text as marka_ts, x4.text as model_ts, b.gos_znak, b.n_reg, "
				. " CONCAT(c.fam, ' ', c.imj, ' ', c.otch) as  driver, "
				. " x7.text as st_chast_koap_text "
				. " FROM {table} a "
				. " LEFT JOIN cars b ON b.id=a.id_car "
				. " LEFT JOIN drivers c ON c.id=a.id_driver "
				. " LEFT JOIN s2i_klass x3 ON x3.kod=b.marka AND x3.nomer=3 "
				. " LEFT JOIN s2i_klass x4 ON x4.kod=b.model AND x4.nomer=4 "
				. " LEFT JOIN s2i_klass x7 ON x7.kod=a.st_chast_koap AND x7.nomer=26 "
				. " ORDER BY a.date_adm DESC";
		else if($role == 2)
			$this->sql_get_list = "SELECT a.*, c.id as id_driver, b.id as id_car, x3.text as marka_ts, x4.text as model_ts, b.gos_znak, b.n_reg, "
				. " CONCAT(c.fam, ' ', c.imj, ' ', c.otch) as  driver, "
				. " x7.text as st_chast_koap_text "
				. " FROM {table} a "
				. " LEFT JOIN cars b ON b.id=a.id_car AND b.dostup=1 "
				. " LEFT JOIN drivers c ON c.id=a.id_driver AND c.dostup=1 "
				. " LEFT JOIN s2i_klass x3 ON x3.kod=b.marka AND x3.nomer=3 "
				. " LEFT JOIN s2i_klass x4 ON x4.kod=b.model AND x4.nomer=4 "
				. " LEFT JOIN s2i_klass x7 ON x7.kod=a.st_chast_koap AND x7.nomer=26 "
				. " ORDER BY a.date_adm DESC";
		else if($role == 1)
			$this->sql_get_list = "SELECT a.*, c.id as id_driver, b.id as id_car, x3.text as marka_ts, x4.text as model_ts, b.gos_znak, b.n_reg, "
				. " CONCAT(c.fam, ' ', c.imj, ' ', c.otch) as  driver, "
				. " x7.text as st_chast_koap_text "
				. " FROM {table} a "
				. " LEFT JOIN cars b ON b.id=a.id_car AND b.dostup=1 "
				. " LEFT JOIN drivers c ON c.id=a.id_driver AND c.dostup=1 "
				. " LEFT JOIN s2i_klass x3 ON x3.kod=b.marka AND x3.nomer=3 "
				. " LEFT JOIN s2i_klass x4 ON x4.kod=b.model AND x4.nomer=4 "
				. " LEFT JOIN s2i_klass x7 ON x7.kod=a.st_chast_koap AND x7.nomer=26 "
				. " ORDER BY a.date_adm DESC";
		else
			return false;
		
		if(($data = parent::get_list()) === false)
			return false;

		$html = $this->draw_result_table($data, 1, (empty($post['page']) ? 1 : $post['page']));
		return ['search_result' => $html];
	}

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
			} else if($field == 'date_adm1') {
				if(mb_strlen($where) == 0)
					$where = " a.date_adm >= '" . Functions::convertToMySQLDateFormat($array_value_decode['value']) . "'";
				else
					$where .= " AND a.date_adm >= '" . Functions::convertToMySQLDateFormat($array_value_decode['value']) . "'";
			} else if($field == 'date_adm2') {
				if(mb_strlen($where) == 0)
					$where = " a.date_adm <= '" . Functions::convertToMySQLDateFormat($array_value_decode['value']) . "'";
				else
					$where .= " AND a.date_adm <= '" . Functions::convertToMySQLDateFormat($array_value_decode['value']) . "'";
			}
		}

		$sql = '';
		$role = User::get('role');
		
		if(mb_strlen($where) > 0)
				$where = " WHERE " . $where;

		if($role == 9)
			$sql = "SELECT a.id, c.id as id_driver, b.id as id_car, x3.text as marka_ts, x4.text as model_ts, b.gos_znak, CONCAT(c.fam, ' ', c.imj, ' ', c.otch) as  driver, "
				. " a.date_adm, a.time_adm, a.place_adm, a.sum_adm, a.oplat_adm, x7.text as st_chast_koap_text, a.comment_adm, IF(a.oplat_adm = 1, 'ДА', 'НЕТ') as oplat_adm_text "
				. " FROM adm_offense a "
				. " LEFT JOIN cars b ON b.id=a.id_car "
				. " LEFT JOIN drivers c ON c.id=a.id_driver "
				. " LEFT JOIN s2i_klass x3 ON x3.kod=b.marka AND x3.nomer=3 "
				. " LEFT JOIN s2i_klass x4 ON x4.kod=b.model AND x4.nomer=4 "
				. " LEFT JOIN s2i_klass x7 ON x7.kod=a.st_chast_koap AND x7.nomer=26 " . $where
				. " ORDER BY a.date_adm DESC";
		else if($role == 2)
			$sql = "SELECT a.id, c.id as id_driver, b.id as id_car, x3.text as marka_ts, x4.text as model_ts, b.gos_znak, CONCAT(c.fam, ' ', c.imj, ' ', c.otch) as  driver, "
				. " a.date_adm, a.time_adm, a.place_adm, a.sum_adm, a.oplat_adm, x7.text as st_chast_koap_text, a.comment_adm, IF(a.oplat_adm = 1, 'ДА', 'НЕТ') as oplat_adm_text "
				. " FROM adm_offense a "
				. " LEFT JOIN cars b ON b.id=a.id_car AND b.dostup=1 "
				. " LEFT JOIN drivers c ON c.id=a.id_driver "
				. " LEFT JOIN s2i_klass x3 ON x3.kod=b.marka AND x3.nomer=3 "
				. " LEFT JOIN s2i_klass x4 ON x4.kod=b.model AND x4.nomer=4 "
				. " LEFT JOIN s2i_klass x7 ON x7.kod=a.st_chast_koap AND x7.nomer=26 " . $where
				. " ORDER BY a.date_adm DESC";
		else if($role == 1)
			$sql = "SELECT a.id, c.id as id_driver, b.id as id_car, x3.text as marka_ts, x4.text as model_ts, b.gos_znak, CONCAT(c.fam, ' ', c.imj, ' ', c.otch) as  driver, "
				. " a.date_adm, a.time_adm, a.place_adm, a.sum_adm, a.oplat_adm, x7.text as st_chast_koap_text, a.comment_adm, IF(a.oplat_adm = 1, 'ДА', 'НЕТ') as oplat_adm_text "
				. " FROM adm_offense a "
				. " LEFT JOIN cars b ON b.id=a.id_car AND b.dostup=1 "
				. " LEFT JOIN drivers c ON c.id=a.id_driver "
				. " LEFT JOIN s2i_klass x3 ON x3.kod=b.marka AND x3.nomer=3 "
				. " LEFT JOIN s2i_klass x4 ON x4.kod=b.model AND x4.nomer=4 "
				. " LEFT JOIN s2i_klass x7 ON x7.kod=a.st_chast_koap AND x7.nomer=26 " . $where
				. " ORDER BY a.date_adm DESC";
		else
			return false;

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
			$html .= "<p>По Вашему запросу ничего не найдено!</p>";
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
					. "<th " . $style_border . " scope='col'>Статья и часть КоАП РФ</th>"
					. "<th " . $style_border . " scope='col'>Сумма, руб</th>"
					. "<th " . $style_border . " scope='col'>Оплата штрафа</th>"
				. "</tr>";

			$j = 0;
			for($i = 0; $i < count($data); $i++) {
				$oplat_adm = ($data[$i]['oplat_adm'] == 1) ? 'ДА' : 'НЕТ';
				
				$page_driver = "http://" . $_SERVER['HTTP_HOST'] . "/driver?id=" . $data[$i]['id_driver'] . "";
				$page_car = "http://" . $_SERVER['HTTP_HOST'] . "/car?id=" . $data[$i]['id_car'] . "";
				$page_adm = "window.open('http://" . $_SERVER['HTTP_HOST'] . "/adm?id=" . $data[$i]['id'] . "')";
				
				$j++;
				if(($j >= $record_tail_limit) && ($j <= $record_head_limit)) {
					$html_table .= "<tr style='cursor: pointer;'>"
						. "<td " . $style_border . " onclick=" . $page_adm . ">" . ($i+1) . "</td>"
						. "<td " . $style_border . "><a href='" . $page_car . "' target='_blank'>" . $data[$i]['gos_znak'] . "<br>" . $data[$i]['marka_ts'] . " " . $data[$i]['model_ts'] . "</a></td>"
						. "<td " . $style_border . "><a href='" . $page_driver . "' target='_blank'>" . $data[$i]['driver'] . "</a></td>"
						. "<td " . $style_border . " onclick=" . $page_adm . ">" . Functions::convertToDate($data[$i]['date_adm']) . " " . $data[$i]['time_adm'] . "</td>"
						. "<td class='text-left' style='vertical-align: middle; border: 1px solid gray; font-size: 11px;' onclick=" . $page_adm . ">" . $data[$i]['place_adm'] . "</td>"
						. "<td style='vertical-align: middle; border: 1px solid gray; font-size: 11px;' onclick=" . $page_adm . ">ст.&nbsp;" . $data[$i]['st_chast_koap_text']
						. "<td style='vertical-align: middle; border: 1px solid gray; font-size: 11px;' onclick=" . $page_adm . ">" . $data[$i]['sum_adm'] . "</td>"
						. "<td style='vertical-align: middle; border: 1px solid gray; font-size: 11px;' onclick=" . $page_adm . ">" . $oplat_adm . "</td>"
						. "</tr>";
				}
				
				if($j > $record_head_limit)
					break;
			}
			$html_table .= "</table>";
			
			// Class button
			$class_btn = ($type == 1) ? 'btn-list-adm-offense' : 'btn-search-adm-offense';
			
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

	function generate_excel_document($data) {

	}

	// Получение списка адм. правонарушений для страницы водители
	public function get_list_adm_for_driver($nsyst) {
		$sql = "SELECT {table}.*, DATE_FORMAT({table}.date_adm, '%d.%m.%Y') as date_adm, cars.gos_znak, x1.text as marka, x2.text as model, x3.text as st_chast_koap_text FROM {table} 
				LEFT JOIN cars ON cars.id={table}.id_car
				LEFT JOIN s2i_klass x1 ON x1.kod=cars.marka AND x1.nomer=3
				LEFT JOIN s2i_klass x2 ON x2.kod=cars.marka AND x2.nomer=4
				LEFT JOIN s2i_klass x3 ON x3.kod={table}.st_chast_koap AND x3.nomer=26
				WHERE {table}.id_driver=" . $nsyst;
		
		if(($sql = preg_replace('/\{table\}/i', $this->table, $sql)) === NULL)
			return false;

		if(($data = DB::query($sql)) === false)
			return false;
		return $data;
	}

	// Получение списка адм. правонарушений для страницы ТС
	public function get_list_adm_for_car($nsyst) {
		$sql = "SELECT {table}.*, DATE_FORMAT({table}.date_adm, '%d.%m.%Y') as date_adm, CONCAT(drivers.fam, ' ', drivers.imj, ' ', drivers.otch) as fio, x1.text as st_chast_koap_text FROM {table}
				LEFT JOIN drivers ON drivers.id={table}.id_driver
				LEFT JOIN s2i_klass x1 ON x1.kod={table}.st_chast_koap AND x1.nomer=26
				WHERE {table}.id_car=" . $nsyst;
		
		if(($sql = preg_replace('/\{table\}/i', $this->table, $sql)) === NULL)
			return false;

		if(($data = DB::query($sql)) === false)
			return false;
		return $data;
	}

	// Функция отрисовки списка ПТС
	function rendering_list($post) {
		if(empty($post['nsyst']) || empty($post['type']))
			return false;

		$type = $post['type'];
		$data = [];
		if($post['type'] == 1)
			$data = $this->get_list_adm_for_car(addslashes($post['nsyst']));
		else
			$data = $this->get_list_adm_for_driver(addslashes($post['nsyst']));

		if($data === false)
			return false;

		$style_border = "style='vertical-align: middle; border: 1px solid gray;'";
		$html = "";

		$role = User::get('role');

		if(count($data) == 0) {
			return "<p class='text-center'>Сведений в базе данных не найдено!</p>";
		} else {
			$html .= "<table class='table table-striped table-sm table-hover text-center' style='font-size: 13px; margin: 10px;'>";
			$html .= "<tr class='table-info'>"
				. "<th " . $style_border . " scope='col'>№ п/п</th>";
			
			if($type == 1)
				$html .= "<th " . $style_border . " scope='col'>Водитель</th>";
			else
				$html .= "<th " . $style_border . " scope='col'>Транспортное средство</th>"
					   . "<th " . $style_border . " scope='col'>Гос. номер</th>";
			
			$html .= "<th " . $style_border . " scope='col'>Дата и время совершения</th>"
					. "<th " . $style_border . " scope='col'>Статья и часть КоАП РФ</th>"
					. "<th " . $style_border . " scope='col'>Место совершения</th>"
					. "<th " . $style_border . " scope='col'>Сумма, руб</th>"
					. "<th " . $style_border . " scope='col'>Оплата штрафа</th>"
					. "<th " . $style_border . " scope='col'>Изменить</th>"
				. "</tr>";
		}
		
		for($i = 0; $i < count($data); $i++) {
			$page = "http://" . $_SERVER['HTTP_HOST'] . "/adm?id=" . $data[$i]['id'];
			$oplat_adm = ($data[$i]['oplat_adm'] == 1) ? 'ДА' : 'НЕТ';
			$html .= "<tr><td " . $style_border . ">" . ($i+1) . "</td>";
			
			if($type == 1)
				$html .= "<td " . $style_border . ">" . $data[$i]['fio'] . "</td>";
			else
				$html .= "<td " . $style_border . ">" . $data[$i]['marka'] . "&nbsp;" . $data[$i]['model'] . "</td>"
					   . "<td " . $style_border . ">" . $data[$i]['gos_znak'] . "</td>";
			
			$html .= "<td " . $style_border . ">" . $data[$i]['date_adm'] . "&nbsp;" . $data[$i]['time_adm'] . "</td>"
				   . "<td style='vertical-align: middle; border: 1px solid gray; font-size: 11px;'>ст.&nbsp;" . $data[$i]['st_chast_koap_text'];

			$html .= "<td style='vertical-align: middle; border: 1px solid gray; font-size: 10px;'>" . $data[$i]['place_adm'] . "</td>"
				   . "<td " . $style_border . ">" . $data[$i]['sum_adm'] . "</td>"
			       . "<td style='vertical-align: middle; border: 1px solid gray; font-size: 11px;'>" . $oplat_adm . "</td>";
			$html .= ($role >= 2) ? "<td style='vertical-align: middle; border: 1px solid gray; font-size: 11px;'><a href='" . $page . "' class='btn btn-sm btn-info' target='_blank'><span class='fa fa-pencil'>&nbsp;</span>Изменить</a></td>" : "<td " . $style_border . "></td>";
			$html .= "</tr>";
		}
		$html .= "</table>";
		
		return [$html];
	}
	
	// Функция, которая получает список файлов, прикрепленных к данному ремонту
	public function get_files($id) {
		$sql = "SELECT * FROM files WHERE id_object=" . $id . " AND category_file=15";
		if(($data = DB::query($sql)) === false)
			return false;
		return $data;
	}
}