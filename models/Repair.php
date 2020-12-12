<?php

namespace IcKomiApp\models;

use IcKomiApp\core\User;
use IcKomiApp\core\Model;
use IcKomiApp\core\Functions;
use IcKomiApp\lib\Database\DB;
use IcKomiApp\lib\excel\GenerateExcel;

class Repair extends Model {
	protected $table = 'car_repair';
	protected $remove_directory = 1;

	protected $sql_get_record = "SELECT a.id, a.id_car, b.id_goods, a.car_mileage, a.org_repair, a.date_start_repair, a.date_end_repair, a.prim_repair, a.price_repair, a.change_oil, "
			. " b.cost_repair, x1.text as operation, b.operation as kod_operation, b.comment, c.name_goods, c.article_goods, d.kodrai as kodrai_ts "
			. " FROM {table} a "
			. " LEFT JOIN car_repair_details b ON b.id_repair=a.id "
			. " LEFT JOIN s2i_klass x1 ON x1.kod=b.operation AND x1.nomer=21 "
			. " LEFT JOIN goods c ON c.id=b.id_goods "
			. " LEFT JOIN cars d ON d.id=a.id_car "
			. " WHERE a.id={id}";

	// Получение всех полей таблицы по ее ID (без раскрытия справочников)
	public function get($get) {
		$data = parent::get($get);

		if(($data_files = $this->get_files(addslashes($_GET['id']))) === false)
			return false;
			
		$list_files_doc = '';

		for($i = 0; $i < count($data_files); $i++) {
			if($data_files[$i]['file_extension'] == 'pdf')
				$list_files_doc .= Functions::rendering_icon_file($data_files[$i]['path_to_file'], $data_files[$i]['file_extension'], $data_files[$i]['id'], 11, true);
		}
		$data[0]['list_files_doc'] = $list_files_doc;
		return $data;
	}

	public function get_list($post = []) {
		$role = User::get('role');
		
		if($role == 9)
			$this->sql_get_list = "SELECT a.id, a.id_car, x1.text as org_repair, a.date_start_repair, a.date_end_repair, a.prim_repair, a.price_repair, b.gos_znak, x2.text as marka_ts, x3.text as model_ts, "
				. " c.path_to_file, c.file_extension "
				. " FROM car_repair a "
				. " INNER JOIN cars b ON a.id_car=b.id "
				. " LEFT JOIN files c ON a.id=c.id_object AND c.category_file=11 "
				. " LEFT JOIN s2i_klass x1 ON x1.kod=a.org_repair AND x1.nomer=18 "
				. " LEFT JOIN s2i_klass x2 ON x2.kod=b.marka AND x2.nomer=3 "
				. " LEFT JOIN s2i_klass x3 ON x3.kod=b.model AND x3.nomer=4 "
				. " WHERE a.ibd_arx=1 ORDER BY a.date_start_repair DESC";
		else if($role == 2)
			$this->sql_get_list = "SELECT a.id, a.id_car, x1.text as org_repair, a.date_start_repair, a.date_end_repair, a.prim_repair, a.price_repair, b.gos_znak, x2.text as marka_ts, x3.text as model_ts, "
				. " c.path_to_file, c.file_extension "
				. " FROM car_repair a "
				. " INNER JOIN cars b ON a.id_car=b.id AND b.dostup=1 "
				. " LEFT JOIN files c ON a.id=c.id_object AND c.category_file=11 "
				. " LEFT JOIN s2i_klass x1 ON x1.kod=a.org_repair AND x1.nomer=18 "
				. " LEFT JOIN s2i_klass x2 ON x2.kod=b.marka AND x2.nomer=3 "
				. " LEFT JOIN s2i_klass x3 ON x3.kod=b.model AND x3.nomer=4 "
				. " WHERE a.ibd_arx=1 ORDER BY a.date_start_repair DESC";
		else if($role == 1)
			$this->sql_get_list = "SELECT a.id, a.id_car, x1.text as org_repair, a.date_start_repair, a.date_end_repair, a.prim_repair, a.price_repair, b.gos_znak, x2.text as marka_ts, x3.text as model_ts, "
				. " c.path_to_file, c.file_extension "
				. " FROM car_repair a "
				. " INNER JOIN cars b ON a.id_car=b.id AND b.dostup=1 "
				. " LEFT JOIN files c ON a.id=c.id_object AND c.category_file=11 "
				. " LEFT JOIN s2i_klass x1 ON x1.kod=a.org_repair AND x1.nomer=18 "
				. " LEFT JOIN s2i_klass x2 ON x2.kod=b.marka AND x2.nomer=3 "
				. " LEFT JOIN s2i_klass x3 ON x3.kod=b.model AND x3.nomer=4 "
				. " WHERE a.ibd_arx=1 ORDER BY a.date_start_repair DESC";
		else
			return false;

		if(($data = parent::get_list()) === false)
			return false;

		$html = $this->draw_result_table($data, 1, (empty($post['page']) ? 1 : $post['page']));
		return ['search_result' => $html];
	}

	// Функция поиска
	public function search($post, $flg_excel = -1) {
		if(empty($post['JSON']))
			return false;

		if(!$array_data_decode = json_decode($post['JSON']))
			return false;

		/*Session::start();
		$role = Session::get('role');
		$kodrai = Session::get('slugba');
		Session::commit();*/
		
		$where = "WHERE a.ibd_arx=1 ";
		
		$date_repair1 = $date_repair2 = '';

		// Разбираем критерии поиска
		foreach($array_data_decode as $field => $array_value) {
			$array_value_decode = (array)$array_value;

			if(trim($array_value_decode['value']) == "0" || (mb_strlen(trim($array_value_decode['value'])) == 0))
				continue;
			
			if($array_value_decode['type'] == 'char') {
				if(mb_strlen($where) == 0)
					$where .= $field . " LIKE '%" . $array_value_decode['value'] . "%'";
				else
					$where .= " AND " . $field . " LIKE '%" . $array_value_decode['value'] . "%'";
			} else if($array_value_decode['type'] == 'date') {
				if($field == 'date_repair1')
					$date_repair1 = Functions::convertToMySQLDateFormat($array_value_decode['value']);
				else
					$date_repair2 = Functions::convertToMySQLDateFormat($array_value_decode['value']);
			} else {
				if(mb_strlen($where) == 0)
					$where .= $field . "=" . $array_value_decode['value'];
				else
					$where .= " AND " . $field . "=" . $array_value_decode['value'];
			}
		}
		
		if((mb_strlen($date_repair1) != 0) && (mb_strlen($date_repair2) != 0))
			$where .= " AND date_start_repair BETWEEN '" . $date_repair1 . "' AND '" . $date_repair2 . "'";
		else if(mb_strlen($date_repair1) != 0)
			$where .= " AND date_start_repair = '" . $date_repair1 . "'";
		else if(mb_strlen($date_repair2) != 0)
			$where .= " AND date_start_repair = '" . $date_repair2 . "'";
		
		$left_join_files = " LEFT JOIN files c ON a.id=c.id_object AND c.category_file=11 ";
		$field_files = " ,c.path_to_file, c.file_extension ";
		if(!empty($array_data['excel'])) {
			if($array_data['excel'] == 1) {
				$left_join_files = " ";
				$field_files = " ";
			}
		}
		
		$sql = "SELECT a.id, a.id_car, x1.text as org_repair, a.date_start_repair, a.date_end_repair, a.prim_repair, a.price_repair, b.gos_znak, x2.text as marka_ts, x3.text as model_ts, "
				. " a.car_mileage, a.change_oil, IF(change_oil = 1, 'ДА', 'НЕТ') as change_oil_text " . $field_files
				. " FROM car_repair a "
				. " INNER JOIN cars b ON a.id_car=b.id "
				. $left_join_files
				. " LEFT JOIN s2i_klass x1 ON x1.kod=a.org_repair AND x1.nomer=18 "
				. " LEFT JOIN s2i_klass x2 ON x2.kod=b.marka AND x2.nomer=3 "
				. " LEFT JOIN s2i_klass x3 ON x3.kod=b.model AND x3.nomer=4 "
				. $where . " ORDER BY a.date_start_repair DESC";

		/*if($role == 8 || $role == 9) {
			$sqlQuery = "SELECT a.id, a.id_car, x1.text as org_repair, a.date_start_repair, a.date_end_repair, a.prim_repair, a.price_repair, b.gos_znak, x2.text as marka_ts, x3.text as model_ts, "
				. " x4.text as kodrai_ts, x5.text as slugba_ts, a.car_mileage, a.change_oil, IF(change_oil = 1, 'ДА', 'НЕТ') as change_oil_text " . $field_files
				. " FROM car_repair a "
				. " INNER JOIN cars b ON a.id_car=b.id "
				. $left_join_files
				//. " LEFT JOIN files c ON a.id=c.id_object AND c.category_file=11 "
				. " LEFT JOIN s2i_klass x1 ON x1.kod=a.org_repair AND x1.nomer=18 "
				. " LEFT JOIN s2i_klass x2 ON x2.kod=b.marka AND x2.nomer=3 "
				. " LEFT JOIN s2i_klass x3 ON x3.kod=b.model AND x3.nomer=4 "
				. " LEFT JOIN s2i_klass x4 ON x4.kod=b.kodrai AND x4.nomer=11 "
				. " LEFT JOIN s2i_klass x5 ON x5.kod=b.slugba AND x5.nomer=1 "
				. $where . " ORDER BY a.date_start_repair DESC";
		} else if($role == 4) {
			$sqlQuery = "SELECT a.id, a.id_car, x1.text as org_repair, a.date_start_repair, a.date_end_repair, a.prim_repair, a.price_repair, b.gos_znak, x2.text as marka_ts, x3.text as model_ts, "
				. " x4.text as kodrai_ts, x5.text as slugba_ts, a.car_mileage, a.change_oil, IF(change_oil = 1, 'ДА', 'НЕТ') as change_oil_text " . $field_files
				. " FROM car_repair a "
				. " INNER JOIN cars b ON a.id_car=b.id AND b.dostup=1 AND b.slugba IN " . User::get_all_slugba()
				. $left_join_files
				. " LEFT JOIN s2i_klass x1 ON x1.kod=a.org_repair AND x1.nomer=18 "
				. " LEFT JOIN s2i_klass x2 ON x2.kod=b.marka AND x2.nomer=3 "
				. " LEFT JOIN s2i_klass x3 ON x3.kod=b.model AND x3.nomer=4 "
				. " LEFT JOIN s2i_klass x4 ON x4.kod=b.kodrai AND x4.nomer=11 "
				. " LEFT JOIN s2i_klass x5 ON x5.kod=b.slugba AND x5.nomer=1 "
				. $where . " ORDER BY a.date_start_repair DESC";
		} else if($role == 3) {
			$sqlQuery = "SELECT a.id, a.id_car, x1.text as org_repair, a.date_start_repair, a.date_end_repair, a.prim_repair, a.price_repair, b.gos_znak, x2.text as marka_ts, x3.text as model_ts, "
				. " x4.text as kodrai_ts, x5.text as slugba_ts, a.car_mileage, a.change_oil, IF(change_oil = 1, 'ДА', 'НЕТ') as change_oil_text " . $field_files
				. " FROM car_repair a "
				. " INNER JOIN cars b ON a.id_car=b.id "
				. $left_join_files
				//. " LEFT JOIN files c ON a.id=c.id_object AND c.category_file=11 "
				. " LEFT JOIN s2i_klass x1 ON x1.kod=a.org_repair AND x1.nomer=18 "
				. " LEFT JOIN s2i_klass x2 ON x2.kod=b.marka AND x2.nomer=3 "
				. " LEFT JOIN s2i_klass x3 ON x3.kod=b.model AND x3.nomer=4 "
				. " LEFT JOIN s2i_klass x4 ON x4.kod=b.kodrai AND x4.nomer=11 "
				. " LEFT JOIN s2i_klass x5 ON x5.kod=b.slugba AND x5.nomer=1 "
				. $where . " ORDER BY a.date_start_repair DESC";
		} else if($role == 2) {
			$sqlQuery = "SELECT a.id, a.id_car, x1.text as org_repair, a.date_start_repair, a.date_end_repair, a.prim_repair, a.price_repair, b.gos_znak, x2.text as marka_ts, x3.text as model_ts, "
				. " x4.text as kodrai_ts, x5.text as slugba_ts, a.car_mileage, a.change_oil, IF(change_oil = 1, 'ДА', 'НЕТ') as change_oil_text " . $field_files
				. " FROM car_repair a "
				. " INNER JOIN cars b ON a.id_car=b.id AND b.dostup=1 AND b.kodrai=" . $kodrai
				. $left_join_files
				//. " LEFT JOIN files c ON a.id=c.id_object AND c.category_file=11 "
				. " LEFT JOIN s2i_klass x1 ON x1.kod=a.org_repair AND x1.nomer=18 "
				. " LEFT JOIN s2i_klass x2 ON x2.kod=b.marka AND x2.nomer=3 "
				. " LEFT JOIN s2i_klass x3 ON x3.kod=b.model AND x3.nomer=4 "
				. " LEFT JOIN s2i_klass x4 ON x4.kod=b.kodrai AND x4.nomer=11 "
				. " LEFT JOIN s2i_klass x5 ON x5.kod=b.slugba AND x5.nomer=1 "
				. $where . " ORDER BY a.date_start_repair DESC";
		} else {
			return false;
		}*/

		if(($data = DB::query($sql)) === false)
			return false;

		$html = ($_POST['excel'] != -1) ? $this->generate_excel_document($data) : $this->draw_result_table($data, 2, addslashes($post['page']), 2);

		return [ 'search_result' => $html];
	}

	// Painting list repairs
	function draw_result_table($data, $type = 1, $page = 0) {
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

			// Формируем готовый HTML код для списка закрепленных ТС
			$html_table = "<table class='table table-striped table-sm table-hover text-center' style='font-size: 13px;'>"
					. "<tr class='table-info'>"
						. "<th " . $style_border . " scope='col'>№ п/п</th>"
						. "<th " . $style_border . " scope='col'>Транспортное средство</th>"
						. "<th " . $style_border . " scope='col'>Станция ремонта</th>"
						. "<th " . $style_border . " scope='col'>Дата ремонта</th>"
						. "<th " . $style_border . " scope='col'>Стоимость ремонта</th>"
						. "<th " . $style_border . " scope='col'>Примечание</th>"
						. "<th " . $style_border . " scope='col'>Эл. образ</th>"
					. "</tr>";
					
			$id_repair = 0;
			$j = 0;
			$k = 1;
			for($i = 0; $i < count($data); $i++) {
	
				if($data[$i]['id'] != $id_repair) {
					++$j;
					if(($j >= $record_tail_limit) && ($j <= $record_head_limit)) {
						$page_cars = 'http://' . $_SERVER['HTTP_HOST'] . '/car?id=' . $data[$i]['id_car'];
						$page_repair = "window.open('http://" . $_SERVER['HTTP_HOST'] . "/repair?id=" . $data[$i]['id'] . "')";
						$html_table .= "<tr style='cursor: pointer;'>" 
							. "<td " . $style_border . " onclick=" . $page_repair . ">" . $j . "</td>"
							. "<td " . $style_border . "><a href='" . $page_cars . "' target='_blank'>" . $data[$i]['gos_znak'] . "<br>" . $data[$i]['marka_ts'] . " " . $data[$i]['model_ts'] . "</a></td>"
							. "<td style='vertical-align: middle; border: 1px solid gray; font-size: 11px;' onclick=" . $page_repair . ">" . $data[$i]['org_repair'] . "</td>"
							. "<td style='vertical-align: middle; border: 1px solid gray; font-size: 11px;' onclick=" . $page_repair . ">" . Functions::convertToDate($data[$i]['date_start_repair']) . " - " . Functions::convertToDate($data[$i]['date_end_repair']) . "</td>"
							. "<td style='vertical-align: middle; border: 1px solid gray; font-size: 11px;' onclick=" . $page_repair . ">" . $data[$i]['price_repair'] . "</td>"
							. "<td class='text-left' style='vertical-align: middle; border: 1px solid gray; font-size: 11px;' onclick=" . $page_repair . ">" . $data[$i]['prim_repair'] . "</td>"
							. "<td " . $style_border . ">" . $this->get_list_files_for_repair($data, $i, $data[$i]['id']) . "</td>"
							. "</tr>";
					}
				}
				$id_repair = $data[$i]['id'];
				
				if($j > $record_head_limit)
					break;
			}
		
			$html_table .= "</table>";
			
			// Class button
			$class_btn = ($type == 1) ? 'btn-list-repair' : 'btn-search-repair' ;
			
			// Подсчет количества уникальных значений в массиве для переключения между страницами
			$count_uniq_array = count(array_count_values(array_map(function($a) { return $a['id']; }, $data)));
			
			// Кнопки переключения между страницами
			$text_list = "Записи:&nbsp;" . $record_tail_limit . '&nbsp;-&nbsp;' . (($j > $record_head_limit) ? $record_head_limit : $j) . "&nbsp;из&nbsp;" . $count_uniq_array;
			$html_bottom_text = "<div class='text-right' style='display: block; font-size: 15px;'>" . $text_list . "</div>";
	
			$html_btn_top = "<div class='col btn-group text-right' role='group' style='display: block; padding: 0px;'>"
				. "<button type='button' class='btn btn-sm btn-secondary mb-1 mr-2 " . $class_btn . "' title='Перейти к предыдущим записям' data-excel='-1' data-page='" . $page_left . "'" . $page_left_disabled . "><span class='fa fa-mail-reply'></span></button>"
				. "<label>" . $text_list . "</label>";
			
			$html_btn_bottom = "<div class='col btn-group text-center' role='group' style='display: block;'>"
				. "<button type='button' class='btn btn-secondary mb-1 mr-2 " . $class_btn . "' title='Перейти к предыдущим записям' data-excel='-1' data-page='" . $page_left . "'" . $page_left_disabled . "><span class='fa fa-mail-reply'>&nbsp;</span>Предыдущие записи</button>";
			
			$html_btn_top .= "<button type='button' class='btn btn-sm btn-secondary mb-1 ml-2 " . $class_btn . "' title='Перейти к следующим записям' data-excel='-1' data-page='" . $page_right . "'" . $page_right_disabled . "><span class='fa fa-mail-forward'></span></button>";
			$html_btn_bottom .= "<button type='button' class='btn btn-secondary mb-1 ml-2 " . $class_btn . "' title='Перейти к следующим записям' data-excel='-1' data-page='" . $page_right . "'" . $page_right_disabled . ">Следующие записи&nbsp;<span class='fa fa-mail-forward'></span></button>";
	
			$html_btn_top .= "</div>";
			$html_btn_bottom .= "</div>";
			
			$html = $html_btn_top . $html_table . $html_bottom_text . $html_btn_bottom;
		}
		return $html;
	}

	public function generate_excel_document($data) {
		$header = array('№ п/п', 'Гос. знак', 'Марка', 'Модель', 'Станция ремонта', 'Начало ремонта', 'Окончание ремонта', 'Стоимость ремонта', 'Пробег', 'Замена масла в ДВС', 'Примечание');
		$body = array(['{index}'], ['gos_znak'], ['marka_ts'], ['model_ts'], ['org_repair'], ['date_start_repair', 'date'], ['date_end_repair', 'date'], ['price_repair'], ['car_mileage'], ['change_oil_text'], ['prim_repair']);
		return GenerateExcel::generate_excel_document('repairs', 'Ремонты', $header, $body, $data);
	}

	// Функция получения списка ремонтов для данного ТС
	// id_car - транспортное средство
	public function get_list_repairs_for_car($id_car) {
		$sql = "SELECT a.id, a.id_car, a.date_start_repair, a.date_end_repair, a.prim_repair, x1.text as org_repair FROM car_repair a"
			. " LEFT JOIN s2i_klass x1 ON x1.kod=a.org_repair AND x1.nomer=18 "
			. " WHERE a.id_car=" . $id_car
			. " ORDER BY date_start_repair DESC";
		
		if(($data = DB::query($sql)) === false)
			return false;
		return $data;
	}

	// Функция отрисовки списка ПТС
	function rendering_list($post) {
		if(empty($post['nsyst']))
			return false;

		if(($data = $this->get_list_repairs_for_car(addslashes($_POST['nsyst']))) === false)
			return false;
		
		/*Session::start();
		$role = Session::get('role');
		Session::commit();*/

		$role = 9;

		if(count($data) == 0) {
			$html = "<div class='text-center'><p>Сведений в базе данных не найдено!</p></div>";
		} else {
			$style_border = "style='vertical-align: middle; border: 1px solid gray;'";

			// Формируем готовый HTML код для списка закрепленных ТС
			$html = "<table class='table table-striped table-sm table-hover text-center' style='font-size: 13px; margin: 10px;'>"
					. "<tr class='table-info'>"
						. "<th " . $style_border . " scope='col'>№ п/п</th>"
						. "<th " . $style_border . " scope='col'>Станция ремонта</th>"
						. "<th " . $style_border . " scope='col'>Дата ремонта</th>"
						. "<th " . $style_border . " scope='col'>Примечание</th>"
						. "<th " . $style_border . " scope='col'>Скорректировать</th>"
					. "</tr>";
	
			for($i = 0; $i < count($data); $i++) {
				$page = 'http://' . $_SERVER['HTTP_HOST'] . '/pages/repairs.php?id=' . $data[$i]['id'];
				$html .= "<tr>"
					. "<td " . $style_border . ">" . ($i+1) . "</td>"
					. "<td " . $style_border . ">" . $data[$i]['org_repair'] . "</td>"
					. "<td " . $style_border . ">" . Functions::convertToDate($data[$i]['date_start_repair']) . " - " . Functions::convertToDate($data[$i]['date_end_repair']) . "</td>"
					. "<td " . $style_border . ">" . $data[$i]['prim_repair'] . "</td>";
				$html .= "<td " . $style_border . ">";
				$html .= (($role > 1) && ($role != 4)) ? "<a href='" . $page . "' role='button' class='btn btn-sm btn-info' target='_blank' title='Скорректировать ремонт' onclick='" . $page . "'><span class='fa fa-pencil'>&nbsp;</span></a>" : "";
				$html .= "</td></tr>";
			}
		}
		
		return [$html];
	}

	// Функция, которая получает список файлов, прикрепленных к данному ремонту
	public function get_files($id) {
		$sql = "SELECT * FROM files WHERE id_object=" . $id . " AND category_file=11";
		if(($data = DB::query($sql)) === false)
			return false;
		return $data;
	}
	
	// Функция получения списка всех файлов, которые привязаны к данному ремонту.
	// Мы получаем общий список со всеми файлами и должны для каждого конкретного ремонта сформировать список файлов.
	// Идея заключается в том что мы бежим по массиву начиная с первого найденного элемента и до тех пор пока не закончится интересующий нас ремонты
	function get_list_files_for_repair($data, $index, $search_item) {
		$list_files = '';
		for($i = $index; $i < count($data); $i++) {
			if($data[$i]['id'] != $search_item)
				break;
			$list_files .= Functions::rendering_icon_file($data[$i]['path_to_file'], $data[$i]['file_extension']);
		}
		return $list_files;
	}
}