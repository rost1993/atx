<?php

namespace IcKomiApp\models;

use IcKomiApp\core\Model;
use IcKomiApp\core\Functions;
use IcKomiApp\lib\Database\DB;

class Driver extends Model {
	protected $table = 'drivers';

	protected $sql_get_record = "SELECT {table}.id, {table}.fam, {table}.imj, {table}.otch, {table}.dt_rojd, {table}.mob_phone, {table}.slugba, {table}.kodrai, {table}.ibd_arx, {table}.dostup, b.doc_s, b.doc_n, "
			. " DATE_FORMAT(b.doc_date, '%d.%m.%Y') as doc_date, DATE_FORMAT(b.doc_end_date, '%d.%m.%Y') as doc_end_date, "
			. " b.c_a, b.c_a1, b.c_b, b.c_b1, b.c_c, b.c_c1, b.c_d, b.c_d1, b.c_be, b.c_ce, b.c_c1e, b.c_de, b.c_d1e, b.c_m, b.c_tm, b.c_tb, b.path_to_file as file_vu, b.file_extension as ext_file_vu,"
			. " c.doc_s as doc_s_tractor, c.doc_n as doc_n_tractor, DATE_FORMAT(c.doc_date, '%d.%m.%Y') as doc_date_tractor, DATE_FORMAT(c.doc_end_date, '%d.%m.%Y') as doc_end_date_tractor, "
			. " c.c_a1 as c_a1_tr, c.c_a2 as c_a2_tr, c.c_a3 as c_a3_tr, c.c_a4 as c_a4_tr, c.c_b as c_b_tr, c.c_c as c_c_tr, c.c_d as c_d_tr, c.c_e as c_e_tr, c.c_f as c_f_tr, c.path_to_file as file_vu_tractor, c.file_extension as ext_file_vu_tractor,"
			. " c5.text as TEXT_SLUGBA, c6.text as TEXT_KODRAI, "
			. " f.number_dopog, DATE_FORMAT(f.date_start_dopog, '%d.%m.%Y') as date_start_dopog, DATE_FORMAT(f.date_end_dopog, '%d.%m.%Y') as date_end_dopog, "
			. " f.path_to_file as file_dopog, f.file_extension as ext_file_dopog"
			. " FROM {table} "
			. " LEFT JOIN drivers_document b ON {table}.id = b.id_driver AND b.ibd_arx=1 "
			. " LEFT JOIN drivers_document_tractor c ON {table}.id = c.id_driver AND c.ibd_arx=1 "
			. " LEFT JOIN drivers_dopog f ON {table}.id = f.id_driver AND f.ibd_arx=1 "
			. " LEFT JOIN s2i_klass c5 ON c5.kod={table}.slugba AND c5.nomer=1 "
			. " LEFT JOIN s2i_klass c6 ON c6.kod={table}.kodrai AND c6.nomer=11 "
			. " WHERE {table}.id={id} ";

	protected $field = ['fam' => ['type' => 'char', 'maxlength' => '150'],
						'imj' =>  ['type' => 'char', 'maxlength' => '150'],
						'otch' =>  ['type' => 'char', 'maxlength' => '150'],
						'dt_rojd' =>  ['type' => 'date'],
						'mob_phone' =>  ['type' => 'char', 'maxlength' => '20']
					];

	public function get_list($post = []) {
		/*Session::start();
		$role = Session::get('role');
		$kodrai = Session::get('slugba');
		Session::commit();
		
		$where_archive1 = $where_archive2 = "";
		if($archive == 1) {
			$where_archive1 = " WHERE a.ibd_arx <> 1 ";
			$where_archive2 = " AND a.ibd_arx <> 1 ";
		} else if($archive == 2){
			$where_archive1 = " WHERE a.ibd_arx = 1 ";
			$where_archive2 = " AND a.ibd_arx = 1 ";
		} else {
			$where_archive1 = " WHERE a.ibd_arx <> 0 ";
			$where_archive2 = " AND a.ibd_arx <> 0 ";
		}*/

		/*if($role == 9 || $role == 8)
			$this->sql_get_list = "SELECT a.id, a.fam, a.imj, a.otch, a.mob_phone, b.text as slugba, c.text as kodrai, a.dostup, a.ibd_arx FROM {table} a "
					  . " LEFT JOIN s2i_klass b ON a.slugba = b.kod AND b.nomer=1 "
					  . " LEFT JOIN s2i_klass c ON a.kodrai = c.kod AND c.nomer=11 "
					  . $where_archive1
					  . " ORDER BY a.kodrai, a.slugba, a.fam, a.imj, a.otch";
		else if($role == 2)
			$this->sql_get_list = "SELECT a.id, a.fam, a.imj, a.otch, a.mob_phone, b.text as slugba, c.text as kodrai, a.dostup, a.ibd_arx FROM {table} a "
					  . " LEFT JOIN s2i_klass b ON a.slugba = b.kod AND b.nomer=1 "
					  . " LEFT JOIN s2i_klass c ON a.kodrai = c.kod AND c.nomer=11 "
					  . " WHERE a.dostup=1 " . $where_archive2
					  . " ORDER BY a.kodrai, a.slugba, a.fam, a.imj, a.otch";
		else if($role == 4)
			$this->sql_get_list = "SELECT a.id, a.fam, a.imj, a.otch, a.mob_phone, b.text as slugba, c.text as kodrai, a.dostup, a.ibd_arx FROM {table} a "
					  . " LEFT JOIN s2i_klass b ON a.slugba = b.kod AND b.nomer=1 "
					  . " LEFT JOIN s2i_klass c ON a.kodrai = c.kod AND c.nomer=11 "
					  . " WHERE a.dostup=1 " . $where_archive2 . " AND a.slugba IN " . User::get_all_slugba()
					  . " ORDER BY a.kodrai, a.slugba, a.fam, a.imj, a.otch";
		else
			$this->sql_get_list = "SELECT a.id, a.fam, a.imj, a.otch, a.mob_phone, b.text as slugba, c.text as kodrai, a.dostup, a.ibd_arx FROM {table} a "
					  . " LEFT JOIN s2i_klass b ON a.slugba = b.kod AND b.nomer=1 "
					  . " LEFT JOIN s2i_klass c ON a.kodrai = c.kod AND c.nomer=11 "
					  . " WHERE a.dostup=1 " . $where_archive2
					  . " ORDER BY a.kodrai, a.slugba, a.fam, a.imj, a.otch";*/


		$this->sql_get_list = "SELECT a.id, a.fam, a.imj, a.otch, a.mob_phone, a.dostup, a.ibd_arx FROM {table} a "
					  . " ORDER BY a.kodrai, a.slugba, a.fam, a.imj, a.otch";
		
		if(($data = parent::get_list()) === false)
			return false;

		$html = $this->draw_result_table($data, 1, (empty($post['page']) ? 1 : $post['page']));
		return [ 'search_result' => $html];
	}

	// Функция поиска
	public function search($post, $flg_excel = -1) {
		if(empty($post['JSON']))
			return false;

		if(!$array_data_decode = json_decode($post['JSON']))
			return false;
	
		// Начинаем формировать условие для поиска
		// Если уровень прав пользователя меньше чем 2, то ставим ограничение, что ищем только открытые ТС
		$where = '';
		/*if(!User::check_level_user(8))
			$where = " a.dostup=1 ";*/

		// Ограничение по коду района
		/*if($role == 2)
			$where .= (mb_strlen($where) == 0) ? " a.kodrai IN " . $kodrai : " AND a.kodrai IN " . $kodrai;

		if($role == 4)
			$where .= (mb_strlen($where) == 0) ? " a.slugba IN " . User::get_all_slugba() : " AND a.slugba IN " . User::get_all_slugba();*/

		foreach($array_data_decode as $field => $array_value) {
			$array_value_decode = (array)$array_value;

			if($array_value_decode['type'] == 'char') {
				if(trim($array_value_decode['value']) == "0" || (mb_strlen(trim($array_value_decode['value'])) == 0))
					continue;
			
				if(mb_strlen($where) == 0)
					$where .= "a." . $field . " LIKE '%" . $array_value_decode['value'] . "%'";
				else
					$where .= " AND a." . $field . " LIKE '%" . $array_value_decode['value'] . "%'";
			} else if($array_value_decode['type'] == 'date') {
				if(trim($array_value_decode['value']) == "0" || (mb_strlen(trim($array_value_decode['value'])) == 0))
					continue;
			
				if(mb_strlen($where) == 0)
					$where .= "a." . $field . "='" . $array_value_decode['value'] . "'";
				else
					$where .= " AND a." . $field . "='" . $array_value_decode['value'] . "'";
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
				else
					$where .= (mb_strlen($where) == 0) ? " a." . $field . " <> 0 " : " AND a." . $field . " <> 0 ";
			} else {
				if(trim($array_value_decode['value']) == "0" || (mb_strlen(trim($array_value_decode['value'])) == 0))
					continue;
				if(mb_strlen($where) == 0)
					$where .= "a." . $field . "=" . $array_value_decode['value'];
				else
					$where .= " AND a." . $field . "=" . $array_value_decode['value'];
			}
		}

		$sql = " SELECT a.id, a.fam, a.imj, a.otch, a.mob_phone, a.dostup, a.ibd_arx FROM " . $this->table . " a ";

		if(mb_strlen($where) > 0)
			$sql .= " WHERE " . $where;
		
		$sql .= " ORDER BY a.kodrai, a.slugba, a.fam, a.imj ";
		
		if(($data = DB::query($sql)) === false)
			return false;

		$html = ($_POST['excel'] != -1) ? $this->generate_excel_document($data) : $this->draw_result_table($data, 2, addslashes($post['page']), 2);

		return [ 'search_result' => $html];
	}



	// Функция отрисовки таблицы со списком водителей
	// Данная функция возвращает HTML код
	// archive - флаг показатель нужно ли отрисовывать информацию по архиву
	function draw_result_table($data, $type, $page = 0) {
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
					. "<th " . $style_border . " scope='col'>ФИО</th>"
					. "<th " . $style_border . " scope='col'>Контактный телефон</th>"
					. "<th " . $style_border . " scope='col'>Уровень доступа</th>"
					. "<th " . $style_border . " scope='col'>Архив</th>"
				. "</tr>";
		
			// Строим список водителей
			$j = 0;
			for($i = 0; $i < count($data); $i++) {
				
				$link = 'http://' . $_SERVER['HTTP_HOST'] . '/driver?id=' . $data[$i]['id'];
				$j++;
				if(($j >= $record_tail_limit) && ($j <= $record_head_limit)) {
					//Отрисовываем красным цветом строку для архивной записи при режиме поиска
					$html_table .= ($data[$i]['ibd_arx'] != 1) ? "<tr class='table-danger'>" : "<tr>";

					$html_table .=  "<td " . $style_border . ">" . ($i + 1) . "</td>"
							. "<td " . $style_border . "><a href='" . $link . "' target='_blank'>" . $data[$i]['fam'] . "&nbsp;" . $data[$i]['imj'] . "&nbsp;" . $data[$i]['otch']. "</a></td>"
							. "<td " . $style_border . ">" . $data[$i]['mob_phone'] . "</td>";

					$html_table .= ($data[$i]['dostup'] == 1) ? "<td " . $style_border . ">ОТКРЫТ</td>" : "<td " . $style_border . ">ЗАКРЫТ</td>";
					$html_table .= ($data[$i]['ibd_arx'] == 1) ? "<td " . $style_border . ">НЕТ</td>" : "<td " . $style_border . ">ДА</td>";
					$html_table .= "</tr>";
				}

				if($j > $record_head_limit)
					break;
			}
			$html_table .= "</table>";

			// Class button
			$class_btn = ($type == 1) ? 'btn-list-drivers' : 'btn-search-drivers';
			
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

	// Функция получения дополнительной информации
	// ID - идентификатор водителя
	public function get_additional_information($id) {
		$sql = "SELECT	1 as ID_MODULE, "
			. " a.id as ID1, "
			. " 0 as ID2, "
			. " DATE_FORMAT(a.start_date_permission, '%d.%m.%Y') as AA, "
			. " DATE_FORMAT(a.end_date_permission, '%d.%m.%Y') as BB, "
			. " x1.text as CC, "
			. " NULL as DD, "
			. " NULL as EE, "
			. " NULL as FF, "
			. " NULL as GG, "
			. " NULL as HH, "
			. " NULL as II, "
			. " NULL as JJ, "
			. " NULL as KK, "
			. " NULL as LL, "
			. " a.path_to_file as PATH_TO_FILE, "
			. " a.file_extension as FILE_EXTENSION "
		. " FROM drivers_permission_spec_signals a"
		. " LEFT JOIN s2i_klass x1 ON x1.kod=a.category AND x1.nomer=25 "
		. " WHERE a.id_driver=" . $id . " AND a.ibd_arx=1 "
		
		. " UNION ALL "
			. " SELECT 2, "
			. " a.id, "
			. " a.car_id, "
			. " DATE_FORMAT(a.date_doc_fix, '%d.%m.%Y'), "
			. " NULL, "
			. " x1.text, "
			. " x2.text, "
			. " x3.text, "
			. " b.gos_znak, "
			. " a.number_doc_fix, "
			. " x4.text, "
			. " NULL, "
			. " NULL, "
			. " NULL, "
			. " NULL, "
			. " a.path_to_file, "
			. " a.file_extension "
		. " FROM car_for_driver a "
		. " LEFT JOIN cars b ON b.id=a.car_id "
		. " LEFT JOIN s2i_klass x1 ON x1.kod=b.model AND x1.nomer=4"
		. " LEFT JOIN s2i_klass x2 ON x2.kod=b.marka AND x2.nomer=3 "
		. " LEFT JOIN s2i_klass x3 ON x3.kod=b.color AND x3.nomer=12 "
		. " LEFT JOIN s2i_klass x4 ON x4.kod=a.type_doc_fix AND x4.nomer=14 "
		. " WHERE a.id_driver=" . $id . " AND a.ibd_arx=1 "

		. " UNION ALL "
			. " SELECT 3, "
			. " a.id, "
			. " 0, "
			. " DATE_FORMAT(a.date_committing, '%d.%m.%Y'), "
			. " NULL, "
			. " a.place_committing, "
			. " a.time_committing, "
			. " a.comment_committing, "
			. " a.sum_committing, "
			. " NULL, "
			. " NULL, "
			. " NULL, "
			. " NULL, "
			. " NULL, "
			. " NULL, "
			. " NULL, "
			. " NULL "
		. " FROM dtp a WHERE a.id_driver=" . $id
		
		. " UNION ALL "
			. " SELECT 4, " 
			. " a.id, "
			. " a.id_car, "
			. " DATE_FORMAT(a.date_adm, '%d.%m.%Y'), "
			. " NULL, "
			. " a.time_adm, "
			. " x3.text, "
			. " 0, "
			. " a.sum_adm, "
			. " a.place_adm, "
			. " a.comment_adm, "
			. " IF(a.oplat_adm=1, 'ДА', 'НЕТ'), "
			. " b.gos_znak, "
			. " x1.text, "
			. " x2.text, "
			. " NULL, "
			. " NULL "
		. " FROM adm_offense a "
		. " LEFT JOIN cars b ON b.id=a.id_car "
		. " LEFT JOIN s2i_klass x1 ON x1.kod=b.model AND x1.nomer=4"
		. " LEFT JOIN s2i_klass x2 ON x2.kod=b.marka AND x2.nomer=3 "
		. " LEFT JOIN s2i_klass x3 ON x3.kod=a.st_chast_koap AND x3.nomer=26 "
		. " WHERE a.id_driver=" . $id . " AND a.ibd_arx=1 "
		. " ORDER BY ID_MODULE, AA";

		if(($data = DB::query($sql)) === false)
			return false;
		return $data;
	}

	// Получение всех полей таблицы по ее ID (без раскрытия справочников)
	public function get($get) {
		$data = parent::get($get);
		$array_additional = $this->draw_additional_information((empty($data[0]['id']) ? -1 : $data[0]['id']));

		if(count($data) != 0) {
			$data[0]['list_car_for_driver'] = $array_additional[0];
			$data[0]['list_dtp'] = $array_additional[1];
			$data[0]['list_adm'] = $array_additional[2];
			$data[0]['list_permission_spec'] = $array_additional[3];
		}

		return $data;
	}

	public function draw_additional_information($id) {

		// Получаем дополнительную информацию о водителе
		if(($data_add = $this->get_additional_information($id)) === false)
			return false;

		$style_border = "style='vertical-align: middle; border: 1px solid gray;'"; // Стиль для ячейки
		$list_permission_spec = $list_car_for_driver = $list_dtp = $list_adm = '';
		for($i = 0, $j = 0, $k = 0, $l = 0, $m = 0, $x = 0; $i < count($data_add); $i++) {
			if($data_add[$i]['ID_MODULE'] == 1) {
				$list_permission_spec .= "<tr><td " . $style_border . ">" . ++$j . "</td>"
							. "<td " . $style_border . ">" . $data_add[$i]['AA'] . "</td>"
							. "<td " . $style_border . ">" . $data_add[$i]['BB'] . "</td>"
							. "<td " . $style_border . ">" . $data_add[$i]['CC'] . "</td>"
							. "<td " . $style_border . ">" . Functions::rendering_icon_file($data_add[$i]['PATH_TO_FILE'], $data_add[$i]['FILE_EXTENSION']) . "</td>"
							. "</tr>";
			}
			
			if($data_add[$i]['ID_MODULE'] == 2) {
				$page = 'http://' . $_SERVER['HTTP_HOST'] . '/car?id=' . $data_add[$i]['ID2'];

				$list_car_for_driver .= "<tr>"
					. "<td " . $style_border . ">" . ++$k . "</td>"
					. "<td " . $style_border . "><a href='" . $page . "' target='_blank'>" . $data_add[$i]['CC'] . " " . $data_add[$i]['DD'] . "</a></td>"
					. "<td " . $style_border . "><a href='" . $page . "' target='_blank'>" . $data_add[$i]['FF'] . "</a></td>"
					. "<td " . $style_border . ">" . $data_add[$i]['EE'] . "</td>"
					. "<td " . $style_border . ">" . $data_add[$i]['HH'] . " № " . $data_add[$i]['GG'] . " от " . $data_add[$i]['AA'] . "&nbsp;"
					. Functions::rendering_icon_file($data_add[$i]['PATH_TO_FILE'], $data_add[$i]['FILE_EXTENSION']) . "</td>"
					. "</tr>";
			}
			
			if($data_add[$i]['ID_MODULE'] == 3) {
				$list_dtp .= "<tr><td " . $style_border . ">" . ++$l . "</td>"
						. "<td " . $style_border . ">" . $data_add[$i]['AA'] . "&nbsp;" . $data_add[$i]['DD'] . "</td>"
						. "<td " . $style_border . ">" . $data_add[$i]['CC'] . "</td>"
						. "<td " . $style_border . ">" . $data_add[$i]['FF'] . "</td>"
						. "<td " . $style_border . " class='text-left'>" . $data_add[$i]['EE'] . "</td>"
						. "<td " . $style_border . "><span class='dtp-show-information' data-nsyst='" . $data_add[$i]['ID1'] . "'>открыть</span></td></tr>";
			}
			
			if($data_add[$i]['ID_MODULE'] == 4) {
				$page = 'http://' . $_SERVER['HTTP_HOST'] . '/car?id=' . $data_add[$i]['ID2'];

				$list_adm .= "<tr><td " . $style_border . ">" . ++$x . "</td>"
							. "<td " . $style_border . "><a href='" . $page . "' target='_blank'>" . $data_add[$i]['LL'] . "&nbsp;" . $data_add[$i]['KK'] . "</td>"
							. "<td " . $style_border . "><a href='" . $page . "' target='_blank'>" . $data_add[$i]['JJ'] . "</td>"
							. "<td " . $style_border . ">" . $data_add[$i]['AA'] . "&nbsp;" . $data_add[$i]['CC'] . "</td>"
							. "<td " . $style_border . ">ст.&nbsp;" . $data_add[$i]['DD'];

				$list_adm .= "<td class='align-middle' style='border: 1px solid gray; font-size: 10px;'>" . $data_add[$i]['GG'] . "</td>"
							. "<td " . $style_border . ">" . $data_add[$i]['FF'] . "</td>"
							. "<td " . $style_border . ">" . $data_add[$i]['II'] . "</td></tr>";
			}
			
		}

		// Если есть ДТП то только тогда добавляем заголовок таблички
		if(mb_strlen($list_dtp) != 0) {
			$list_dtp = "<table class='table table-striped table-sm table-hover text-center' style='font-size: 13px;'>"
						. "<tr class='table-info'>"
							. "<th " . $style_border . " scope='col'>№ п/п</th>"
							. "<th " . $style_border . " scope='col'>Дата и время ДТП</th>"
							. "<th " . $style_border . " scope='col'>Место ДТП</th>"
							. "<th " . $style_border . " scope='col'>Сумма ущерба</th>"
							. "<th " . $style_border . " scope='col'>Описание ДТП</th>"
							. "<th " . $style_border . " scope='col'>Подробнее</th>"
						. "</tr>" . $list_dtp . "</table>";
		}
		
		// Если есть сведения то только тогда добавляем заголовок таблички
		if(mb_strlen($list_car_for_driver)) {
			$list_car_for_driver = "<table class='table table-striped table-sm table-hover text-center' style='font-size: 13px;'>"
								. "<tr class='table-info'>"
									. "<th " . $style_border . " scope='col'>№ п/п</th>"
									. "<th " . $style_border . " scope='col'>Транспортное средство</th>"
									. "<th " . $style_border . " scope='col'>Гос. номер</th>"
									. "<th " . $style_border . " scope='col'>Цвет</th>"
									. "<th " . $style_border . " scope='col'>Основание</th>"
								. "</tr>" . $list_car_for_driver . "</table>";
		}
		
		if(mb_strlen($list_adm)) {
			$list_adm = "<table class='table table-striped table-sm table-hover text-center' style='font-size: 13px;'>"
							. "<tr class='table-info'>"
								. "<th " . $style_border . " scope='col'>№ п/п</th>"
								. "<th " . $style_border . " scope='col'>Транспортное средство</th>"
								. "<th " . $style_border . " scope='col'>Гос. номер</th>"
								. "<th " . $style_border . " scope='col'>Дата и время совершения</th>"
								. "<th " . $style_border . " scope='col'>Статья и часть КоАП РФ</th>"
								. "<th " . $style_border . " scope='col'>Место совершения</th>"
								. "<th " . $style_border . " scope='col'>Штраф, руб</th>"
								. "<th " . $style_border . " scope='col'>Оплата штрафа</th>"
							. "</tr>" . $list_adm . "</table>";
		}
		
		// Формируем готовый HTML код для списка закрепленных водителей
		if(mb_strlen($list_permission_spec) > 0) {
			$list_permission_spec = "<table class='table table-striped table-sm table-hover text-center' style='font-size: 13px;'>"
						. "<tr class='table-info'>"
							. "<th " . $style_border . " scope='col'>№ п/п</th>"
							. "<th " . $style_border . " scope='col'>Начало действия разрешения</th>"
							. "<th " . $style_border . " scope='col'>Окончание действия разрешения</th>"
							. "<th " . $style_border . " scope='col'>Категория ТС</th>"
							. "<th " . $style_border . " scope='col'>Эл. образ</th>"
						. "</tr>" . $list_permission_spec . "</table>";
		}

		return array($list_car_for_driver, $list_dtp, $list_adm, $list_permission_spec);
	}

	

	
}