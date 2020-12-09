<?php

namespace IcKomiApp\models;

use IcKomiApp\core\User;
use IcKomiApp\core\Model;
use IcKomiApp\core\Logic;
use IcKomiApp\core\Session;
use IcKomiApp\core\Functions;
use IcKomiApp\lib\Database\DB;
use IcKomiApp\widgets\Directory;

class CarDocument extends Model {

	protected $table = 'car_documents';
	protected $trigger_operation = 5;
	protected $remove_directory = 1;
	protected $sql_get_record = "SELECT * FROM {table} WHERE id={id}";

	protected $sql_get_list = "";

	
	// Получение всех полей таблицы по ее ID (без раскрытия справочников)
	public function get($get) {
		$data = parent::get($get);
		$array_additional = $this->draw_additional_information((empty($data[0]['id']) ? -1 : $data[0]['id']));

		if(count($data) != 0) {
			$data[0]['list_add_car'] = $array_additional[0];
			$data[0]['list_files_doc'] = Functions::rendering_icon_file($data[0]['path_to_file'], $data[0]['file_extension'], $data[0]['id'], 8, true);
		}

		return $data;
	}

	public function draw_additional_information($id) {

		// Получаем дополнительную информацию о водителе
		if(($data_car = $this->get_additional_information($id)) === false)
			return false;

		$html = '';
		$style_border = "style='vertical-align: middle; border: 1px solid gray;'";
		for($i = 0; $i < count($data_car); $i++) {
			$page = "'http://" . $_SERVER['HTTP_HOST'] . "/car?id=" . $data_car[$i]['id'] . "'";
			$html .= "<tr><td " . $style_border . ">" . ($i+1) . "</td>"
					. "<td " . $style_border . ">" . $data_car[$i]['marka'] . "&nbsp;" . $data_car[$i]['model'] . "</td>"
					. "<td " . $style_border . "><a href=" . $page . " target='_blank'>" . $data_car[$i]['gos_znak'] . "</a></td>"
					. "<td " . $style_border . ">" . $data_car[$i]['comment'] . "</td>"
					. "</tr>";
		}
		
		if(mb_strlen($html) > 0) {
			$html = "<table class='table table-striped table-sm table-hover text-center' style='font-size: 13px;' id='tableCars'>"
						. "<tr class='table-info'>"
						. "<th class='table_bordered_2' style='vertical-align: middle; border: 1px solid gray;' scope='col'>№ п/п</th>"
						. "<th class='table_bordered_2' style='vertical-align: middle; border: 1px solid gray;' scope='col'>Транспортное средство</th>"
						. "<th class='table_bordered_2' style='vertical-align: middle; border: 1px solid gray;' scope='col'>Гос. знак</th>"
						. "<th class='table_bordered_2' style='vertical-align: middle; border: 1px solid gray;' scope='col'>Комментарий</th>"
					. "</tr>" . $html . "</table>";
		}

		return [$html];
	}

	// Get list cars add for document
	public function get_additional_information($id) {
		$role = User::get('role');

		if($role == 9)
			$sql = "SELECT a.id, x3.text AS marka, x4.text AS model, a.gos_znak, b.comment, b.id as link_id "
					  . " FROM cars a "
					  . " INNER JOIN car_link_document b ON b.id_car=a.id AND b.id_document=" . $id
					  . " LEFT JOIN s2i_klass x3 ON a.marka=x3.kod AND x3.nomer=3 "
					  . " LEFT JOIN s2i_klass x4 ON a.model=x4.kod AND x4.nomer=4 "
					  . " ORDER BY a.id";
		else if($role == 2)
			$sql = "SELECT a.id, x3.text AS marka, x4.text AS model, a.gos_znak, b.comment, b.id as link_id "
					  . " FROM cars a "
					  . " INNER JOIN car_link_document b ON b.id_car=a.id AND b.id_document=" . $id
					  . " LEFT JOIN s2i_klass x3 ON a.marka=x3.kod AND x3.nomer=3 "
					  . " LEFT JOIN s2i_klass x4 ON a.model=x4.kod AND x4.nomer=4 "
					  . " WHERE a.dostup=1 "
					  . " ORDER BY a.id";
		else if($role == 1)
			$sql = "SELECT a.id, x3.text AS marka, x4.text AS model, a.gos_znak, b.comment, b.id as link_id "
					  . " FROM cars a "
					  . " INNER JOIN car_link_document b ON b.id_car=a.id AND b.id_document=" . $id
					  . " LEFT JOIN s2i_klass x3 ON a.marka=x3.kod AND x3.nomer=3 "
					  . " LEFT JOIN s2i_klass x4 ON a.model=x4.kod AND x4.nomer=4 "
					  . " WHERE a.dostup=1 "
					  . " ORDER BY a.id";
		else
			return false;

		if(($data = DB::query($sql)) === false)
			return false;
		return $data;
	}

	public function get_list_add_car($id) {
		$role = User::get('role');

		$sql = '';
		if($role == 9)
			$sql = "SELECT a.id, x3.text AS marka, x4.text AS model, a.gos_znak, b.comment, b.id as link_id "
					  . " FROM cars a "
					  . " INNER JOIN car_link_document b ON b.id_car=a.id AND b.id_document=" . $id
					  . " LEFT JOIN s2i_klass x3 ON a.marka=x3.kod AND x3.nomer=3 "
					  . " LEFT JOIN s2i_klass x4 ON a.model=x4.kod AND x4.nomer=4 ";
		else if($role == 2)
			$sql = "SELECT a.id, x3.text AS marka, x4.text AS model, a.gos_znak, b.comment, b.id as link_id "
					  . " FROM cars a "
					  . " INNER JOIN car_link_document b ON b.id_car=a.id AND b.id_document=" . $id
					  . " LEFT JOIN s2i_klass x3 ON a.marka=x3.kod AND x3.nomer=3 "
					  . " LEFT JOIN s2i_klass x4 ON a.model=x4.kod AND x4.nomer=4 "
					  . " WHERE a.dostup=1 "
					  . " ORDER BY a.id";
		else if($role == 1)
			$sql = "SELECT a.id, x3.text AS marka, x4.text AS model, a.gos_znak, b.comment, b.id as link_id "
					  . " FROM cars a "
					  . " INNER JOIN car_link_document b ON b.id_car=a.id AND b.id_document=" . $id
					  . " LEFT JOIN s2i_klass x3 ON a.marka=x3.kod AND x3.nomer=3 "
					  . " LEFT JOIN s2i_klass x4 ON a.model=x4.kod AND x4.nomer=4 "
					  . " WHERE a.dostup=1 "
					  . " ORDER BY a.id";
		else
			return false;

		if(($data = DB::query($sql)) === false)
			return false;
		return $data;
	}

	public function get_list_add_document($id) {
		$sql = "SELECT a.id, a.number_car_document, DATE_FORMAT(a.date_car_document, '%d.%m.%Y') as date_car_document, x1.text as type_car_document, b.comment, b.id as link_id "
				  . " FROM car_documents a "
				  . " INNER JOIN car_link_document b ON b.id_document=a.id AND b.id_car=" . $id
				  . " LEFT JOIN s2i_klass x1 ON a.type_car_document=x1.kod AND x1.nomer=23 "
				  . " ORDER BY a.date_car_document";

		if(($data = DB::query($sql)) === false)
			return false;
		return $data;
	}

	public function rendering_list($post) {
		if(empty($post['nsyst']) || empty($post['item']))
			return false;

		$role = User::get('role');
		
		$item = addslashes($post['item']);
		$data = [];
		$style_border = "style='vertical-align: middle; border: 1px solid gray;'"; // Стиль для ячейки
		if($item == 'document') {
			if(($data = $this->get_list_add_car(addslashes($post['nsyst']))) === false)
				return false;
			
			$header = "<table class='table table-striped table-sm table-hover text-center' style='font-size: 13px; margin: 10px;'>"
				. "<tr class='table-info'>"
						. "<th " . $style_border . " scope='col'>№ п/п</th>"
						. "<th " . $style_border . " scope='col'>Транспортное средство</th>"
						. "<th " . $style_border . " scope='col'>Гос. знак</th>"
						. "<th " . $style_border . " scope='col'>Комментарий</th>"
						. "<th " . $style_border . " scope='col'>Скорректировать</th>"
						. "<th " . $style_border . " scope='col'>Удалить</th>"
				. "</tr>";
		} else {
			if(($data = $this->get_list_add_document(addslashes($post['nsyst']))) === false)
				return false;
			
			$header = "<table class='table table-striped table-sm table-hover text-center' style='font-size: 13px; margin: 10px;'>"
				. "<tr class='table-info'>"
						. "<th " . $style_border . " scope='col'>№ п/п</th>"
						. "<th " . $style_border . " scope='col'>Тип документа</th>"
						. "<th " . $style_border . " scope='col'>Номер документа</th>"
						. "<th " . $style_border . " scope='col'>Дата документа</th>"
						. "<th " . $style_border . " scope='col'>Комментарий</th>"
						. "<th " . $style_border . " scope='col'>Скорректировать</th>"
						. "<th " . $style_border . " scope='col'>Удалить</th>"
				. "</tr>";
		}

		$table = "";
		for($i = 0; $i < count($data); $i++) {
			
			if($item == 'document') {
				$page = "'http://" . $_SERVER['HTTP_HOST'] . "/car?id=" . $data[$i]['id'] . "'";
				$table .= "<tr>"
						. "<td " . $style_border . ">" . ($i+1) . "</td>"
						. "<td " . $style_border . ">" . $data[$i]['model'] . "&nbsp;" . $data[$i]['marka'] . "</td>"
						. "<td " . $style_border . "><a href=" . $page . " target='_blank'>" . $data[$i]['gos_znak'] . "</a></td>"
						. "<td " . $style_border . ">" . $data[$i]['comment'] . "</td>"
						. "<td " . $style_border . "><button class='btn btn-sm btn-info btnEditLinkCarDocument' data-id='" . $data[$i]['link_id'] . "' data-item='car'><span class='fa fa-pencil'>&nbsp;</span>Изменить</button></td>"
						. "<td " . $style_border . "><div class='dropdown'>"
							. "<button type='button' class='btn btn-sm btn-danger dropdown-toggle' id='dropdownRemoveLinkCarDocument' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false' title='Удалить'><span class='fa fa-close'>&nbsp;</span>Удалить</button>"
							. "<div class='dropdown-menu' aria-labelledby='dropdownRemoveLinkCarDocument'>"
							. "<button type='button' class='dropdown-item btnRemoveLinkCarDocument' data-id='" . $data[$i]['link_id'] . "'><span class='fa fa-check text-success'>&nbsp;</span>Подтверждаю удаление</button></div></td>"
						. "</tr>";
			} else {
				$table .= "<tr>"
						. "<td " . $style_border . ">" . ($i+1) . "</td>"
						. "<td " . $style_border . ">" . $data[$i]['type_car_document'] . "</td>"
						. "<td " . $style_border . ">" . $data[$i]['number_car_document'] . "</td>"
						. "<td " . $style_border . ">" . $data[$i]['date_car_document'] . "</td>"
						. "<td " . $style_border . ">" . $data[$i]['comment'] . "</td>";

				if($role >= 2) {
					$table .= "<td " . $style_border . "><button class='btn btn-sm btn-info btnEditLinkCarDocument' data-id='" . $data[$i]['link_id'] . "' data-item='document'><span class='fa fa-pencil'>&nbsp;</span>Изменить</button></td>"
						. "<td " . $style_border . "><div class='dropdown'>"
							. "<button type='button' class='btn btn-sm btn-danger dropdown-toggle' id='dropdownRemoveLinkCarDocument' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false' title='Удалить'><span class='fa fa-close'>&nbsp;</span>Удалить</button>"
							. "<div class='dropdown-menu' aria-labelledby='dropdownRemoveLinkCarDocument'>"
							. "<button type='button' class='dropdown-item btnRemoveLinkCarDocument' data-id='" . $data[$i]['link_id'] . "'><span class='fa fa-check text-success'>&nbsp;</span>Подтверждаю удаление</button></div></td>";
				} else {
					$table .= "<td " . $style_border . "></td><td " . $style_border . "></td>";
				}
				$table .= "</tr>";
			}
			
		}
		
		$html = $header . $table . "</table>";
		return [$html];
	}

	// Get list cars no add for document
	public function get_list_no_add_car($id) {
		$role = User::get('role');

		$sql = '';
		if($role == 9)
			$sql = "SELECT a.id, x3.text AS marka, x4.text AS model, a.gos_znak "
					  . " FROM cars a "
					  . " LEFT JOIN car_link_document b ON b.id_car=a.id AND b.id_document=" . $id
					  . " LEFT JOIN s2i_klass x3 ON a.marka=x3.kod AND x3.nomer=3 "
					  . " LEFT JOIN s2i_klass x4 ON a.model=x4.kod AND x4.nomer=4 "
					  . " WHERE b.id IS NULL ";
		else if($role == 2)
			$sql = "SELECT a.id, x3.text AS marka, x4.text AS model, a.gos_znak "
					  . " FROM cars a "
					  . " LEFT JOIN car_link_document b ON b.id_car=a.id AND b.id_document=" . $id
					  . " LEFT JOIN s2i_klass x3 ON a.marka=x3.kod AND x3.nomer=3 "
					  . " LEFT JOIN s2i_klass x4 ON a.model=x4.kod AND x4.nomer=4 "
					  . " WHERE a.dostup=1 AND b.id IS NULL "
					  . " ORDER BY a.id";
		else if($role == 1)
			$sql = "SELECT a.id, x3.text AS marka, x4.text AS model, a.gos_znak "
					  . " FROM cars a "
					  . " LEFT JOIN car_link_document b ON b.id_car=a.id AND b.id_document=" . $id
					  . " LEFT JOIN s2i_klass x3 ON a.marka=x3.kod AND x3.nomer=3 "
					  . " LEFT JOIN s2i_klass x4 ON a.model=x4.kod AND x4.nomer=4 "
					  . " WHERE a.dostup=1 AND b.id IS NULL "
					  . " ORDER BY a.id";
		else
			return false;

		if(($data = DB::query($sql)) === false)
			return false;
		return $data;
	}

	// Get list documents no add for car
	public function get_list_no_add_document($id) {
		$sql = "SELECT a.id, a.number_car_document, DATE_FORMAT(a.date_car_document, '%d.%m.%Y') as date_car_document, x1.text as type_car_document "
				  . " FROM car_documents a "
				  . " LEFT JOIN car_link_document b ON b.id_document=a.id AND b.id_car=" . $id
				  . " LEFT JOIN s2i_klass x1 ON a.type_car_document=x1.kod AND x1.nomer=23 "
				  . " WHERE b.id IS NULL "
				  . " ORDER BY a.date_car_document";

		if(($data = DB::query($sql)) === false)
			return false;
		return $data;
	}

	public function rendering_window($post) {
		if(empty($post['nsyst']) || empty($post['item']))
			return false;

		$list = $title = '';
		$type = $post['item'];
		if($type == 'document') {

			$id = addslashes($post['nsyst']);
			if(($data1 = $this->get_list_no_add_car($id)) === false)
				return false;
		
			// Обработка ТС, которые добавлены к документу
			$list = "<table class='table table-bordered table-sm table-hover' id='list-object' style='font-size: 12px;'>";
			for($i = 0; $i < count($data1); $i++) {
				$list .= "<tr id='" . $data1[$i]['id'] . "' data-save='0'>"
								. "<td class='text-center'><input type='checkbox'></td>"
								. "<td id='kodrai'>" . $data1[$i]['gos_znak'] . "&nbsp;" . $data1[$i]['marka'] . "&nbsp;" . $data1[$i]['model'] . "</td></tr>";
			}
			$list .= "</table>";
			$title = 'Список доступных для выбора ТС';
		} else {
			$id = addslashes($post['nsyst']);
			if(($data1 = $this->get_list_no_add_document($id)) === false)
				return false;
		
			// Обработка ТС, которые добавлены к документу
			$list = "<table class='table table-bordered table-sm table-hover' id='list-object' style='font-size: 12px;'>";
			for($i = 0; $i < count($data1); $i++) {
				$list .= "<tr id='" . $data1[$i]['id'] . "' data-save='0'>"
								. "<td class='text-center'><input type='checkbox'></td>"
								. "<td id='kodrai'>" . $data1[$i]['type_car_document'] . "&nbsp;№&nbsp;" . $data1[$i]['number_car_document'] . "&nbsp;от&nbsp;" . $data1[$i]['date_car_document'] . "</td></tr>";
			}
			$list .= "</table>";
			$title = 'Список доступных для выбора документов';
		}

		$html = $this->rendering_window_temp($type, $id, -1, $list, $title);
		return [$html];
	}

	// Get HTML-code window
	private function rendering_window_temp($type, $id, $nsyst, $list1, $title, $comment = "", $title_doc = 0) {
		$select = Directory::get_directory(24, $title_doc);

		$html = "<div class='col-sm-12'>"
			
				. "<div class='card-deck' style='margin: 10px;'>"
					. "<div class='col-sm-6'>"
					
						. "<div class='card border-dark'>"
							. "<div class='card-header text-center'><strong>" . $title . "</strong>"
								. "<div class='input-group'>"
									. "<input type='text' class='form-control form-control-sm black-text' id='searchText' placeholder='Поиск ...'>"
									. "<div class='input-group-append'>"
										. "<button type='button' class='btn btn-sm btn-outline-secondary' id='btnSearchListText' title='Поиск'><span class='fa fa-search'>&nbsp;</span>Поиск</button>"
									. "</div>"
								. "</div>"
							. "</div>"
							. "<div class='card-body card-block-list-drivers'>"
							. $list1
							. "</div>"
						. "</div>"
					. "</div>"

					. "<div class='col-sm-6'>"
						. "<div class='card border-dark'>"
							. "<div class='card-header text-center'><strong>Дополнительные поля</strong>"
							. "</div>"
							. "<div class='card-body'>"

								. "<div class='form-row'>"
									. "<div class='col col-sm-2 mb-1 text-right'>"
										. "<label for='comment' class='text-muted' style='font-size: 13px;'><strong>Комментарий</strong></label>"
									. "</div>"
									. "<div class='col col-sm-10 mb-1'>"
										. "<textarea type='text' class='form-control form-control-sm black-text' id='comment' maxlength='100' rows='3' placeholder='Комментарий' data-datatype='char'>" . $comment. "</textarea>"
									. "</div>"
								. "</div>"

								. "<div class='form-row'>"
									. "<div class='col col-sm-2 mb-1 text-right'>"
										. "<label for='title_document' class='text-muted' style='font-size: 13px;'><strong>Содержание</strong></label>"
									. "</div>"
									. "<div class='col col-sm-10 mb-1'>"
										. "<select type='text' class='form-control form-control-sm black-text' id='title_document' data-datatype='number'>" . $select. "</select>"
									. "</div>"
								. "</div>"

							. "</div>"
						. "</div>"
					. "</div>"
					
				. "</div>"
			. "</div>";
		
		$html .= "<div class='col-sm-12 error-message-car-document text-danger font-weight-bold'></div>";
		$html .= "<div class='col-sm-12 text-center'><button class='btn btn-success' id='btnSaveLinkCarDocument' title='Сохранить изменения' style='margin: 10px;' data-nsyst='" . $nsyst . "' data-id='" . $id . "' data-type-save='" . $type . "'><span class='fa fa-check'>&nbsp;</span>Сохранить изменения</button></div>";
		return $html;
	}

	// Get information link
	public function get_info_link($id) {
		$sql = "SELECT a.id, a.comment, b.gos_znak, x1.text as marka, x2.text as model, x5.text as type_car_document, c.number_car_document, DATE_FORMAT(c.date_car_document, '%d.%m.%Y') as date_car_document, a.title_document FROM car_link_document a "
					. " INNER JOIN cars b ON b.id=a.id_car "
					. " INNER JOIN car_documents c ON c.id=a.id_document "
					. " LEFT JOIN s2i_klass x1 ON x1.kod=b.marka AND x1.nomer=3 "
					. " LEFT JOIN s2i_klass x2 ON x2.kod=b.model AND x2.nomer=4 "
					. " LEFT JOIN s2i_klass x5 ON x5.kod=c.type_car_document AND x5.nomer=23 "
					. " WHERE a.id=" . $id;

		if(($data = DB::query($sql)) === false)
			return false;
		return $data;
	}

	// Edit link
	function rendering_window_edit($post) {
		if(empty($post['nsyst']) || empty($post['item']))
			return false;
		
		$type = addslashes($post['item']);
		$id = addslashes($post['nsyst']);
		if(($data = $this->get_info_link($id)) === false)
			return false;
		
		$list = $comment = "";
		if($type == 'document') {
			// Обработка ТС, которые добавлены к документу
			$list = "<table class='table table-bordered table-sm table-hover' id='list-object' style='font-size: 12px;'>";
			for($i = 0; $i < count($data); $i++) {
				$list .= "<tr id='" . $data[$i]['id'] . "' data-save='1' class='table-success'>"
									. "<td class='text-center'><input type='checkbox' checked='true' disabled></td>"
									. "<td id='kodrai'>" . $data[$i]['type_car_document'] . "&nbsp;№&nbsp;" . $data[$i]['number_car_document'] . "&nbsp;от&nbsp;" . $data[$i]['date_car_document'] . "</td></tr>";
				$comment = $data[$i]['comment'];
				$title_doc = $data[$i]['title_document'];
			}
			$list .= "</table>";
		} else {
			// Обработка ТС, которые добавлены к документу
			$list = "<table class='table table-bordered table-sm table-hover' id='list-object' style='font-size: 12px;'>";
			for($i = 0; $i < count($data); $i++) {
				$list .= "<tr id='" . $data[$i]['id'] . "' data-save='1' class='table-success'>"
									. "<td class='text-center'><input type='checkbox' checked='true' disabled></td>"
									. "<td id='kodrai'>" . $data[$i]['gos_znak'] . "&nbsp;" . $data[$i]['marka'] . "&nbsp;" . $data[$i]['model']  . "</td></tr>";
				$comment = $data[$i]['comment'];
				$title_doc = $data[$i]['title_document'];
			}
			$list .= "</table>";
		}
		
		$html = $this->rendering_window_temp(addslashes($post['item']), -1, $id, $list, 'Объект связи', $comment, $title_doc);
		return [$html];
	}

	public function save_link($post) {
		if(empty($post))
			return false;

		if(empty($post['nsyst']) || empty($post['JSON']) || empty($post['save']) || empty($post['action']))
			return false;

		if(!$array_data_decode = json_decode($post['JSON']))
			return false;

		$id_user = User::get('id');

		$sqlQueryInsert = $sqlQueryUpdate = '';
		
		foreach($array_data_decode as $field => $array_value) {
			if($post['action'] == 'insert') 
				$sqlQueryInsert .= (mb_strlen($sqlQueryInsert) == 0) ? "(" . $post['nsyst'] . "," . $field . ",'" . $array_value[0] . "'," . $array_value[1] . "," . $id_user . ")" : ",(" . $post['nsyst'] . "," . $field . ",'" . $array_value . "'," . $id_user . ")";
			else 
				$sqlQueryUpdate = "UPDATE car_link_document SET comment='" . $array_value[0] . "', title_document=" . $array_value[1] . " WHERE id=" . $field;
		}

		if(mb_strlen($sqlQueryInsert) != 0) {
			if($post['save'] == 'car')
				$sqlQueryInsert = "INSERT INTO car_link_document (id_car, id_document, comment, title_document, sh_polz) VALUES " . $sqlQueryInsert;
			else
				$sqlQueryInsert = "INSERT INTO car_link_document (id_document, id_car, comment, title_document, sh_polz) VALUES " . $sqlQueryInsert;

			if(DB::query($sqlQueryInsert, DB::INSERT_OR_UPDATE) === false)
				return false;
		}
		
		if(mb_strlen($sqlQueryUpdate) != 0) {
			if(DB::query($sqlQueryUpdate, DB::INSERT_OR_UPDATE) === false)
				return false;
		}

		return true;
	}

	
}