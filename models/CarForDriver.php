<?php

namespace IcKomiApp\models;

use IcKomiApp\core\Model;
use IcKomiApp\core\Logic;
use IcKomiApp\core\Functions;
use IcKomiApp\lib\Database\DB;
use IcKomiApp\widgets\Directory;

class CarForDriver extends Model {
	protected $table = 'car_for_driver';
	protected $remove_directory = 1;
	protected $sql_get_record = "SELECT * FROM {table} WHERE id={id}";

	protected $sql_get_list = "";

	// Функция формирования списка водителей, которые закреплены за данным ТС
	public function get_list_drivers_fixed($id_car) {
		/*Session::start();
		$role = Session::get('role');
		$kodrai = Session::get('slugba');
		Session::commit();*/

		$role = 9;
		
		$sql = "SELECT b.fam, b.imj, b.otch, a.ibd_arx as actual, a.id as id_fix, a.car_id, a.id_driver, "
				. " a.number_doc_fix, a.date_doc_fix, x3.text as type_doc_fix, a.path_to_file, a.file_extension "
				. " FROM " . $this->table . " a "
				. " LEFT JOIN drivers b ON b.id=a.id_driver "
				. " LEFT JOIN s2i_klass x3 ON x3.kod=a.type_doc_fix AND x3.nomer=14 "
				. " WHERE a.car_id=" . $id_car;
		
		if($role == 1)
			$sql .= " AND a.dostup=1";
		
		if($role == 2)
			$sql .= " AND a.dostup=1 ";
		
		$sql .= " ORDER BY b.fam, b.imj, b.otch";

		if(($data = DB::query($sql)) === false)
			return false;
		return $data;
	}

	// Функция формирования списка ТС, которые закреплены за данным водителем
	public function get_list_cars_fixed($id_driver) {
		/*Session::start();
		$role = Session::get('role');
		$kodrai = Session::get('slugba');
		Session::commit();*/

		$role = 9;

		$sql = "SELECT a.id as id_fix, x1.text as marka_ts, x2.text as model_ts, b.gos_znak, a.ibd_arx as actual, a.car_id, a.id_driver, "
				. " a.number_doc_fix, a.date_doc_fix, x3.text as type_doc_fix, a.path_to_file, a.file_extension "
				. " FROM " . $this->table . " a "
				. " LEFT JOIN cars b ON b.id=a.car_id "
				. " LEFT JOIN s2i_klass x1 ON x1.kod=b.marka AND x1.nomer=3 "
				. " LEFT JOIN s2i_klass x2 ON x2.kod=b.model AND x2.nomer=4 "
				. " LEFT JOIN s2i_klass x3 ON x3.kod=a.type_doc_fix AND x3.nomer=14 "
				. " WHERE a.id_driver=" . $id_driver;

		if($role == 1)
			$sql .= " AND a.dostup=1 ";
		
		if($role == 2)
			$sql .= " AND a.dostup=1 ";

		$sql .= " ORDER BY a.date_doc_fix ";
				
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
		if($type == 1)
			$data = $this->get_list_cars_fixed(addslashes($post['nsyst']));
		else
			$data = $this->get_list_drivers_fixed(addslashes($post['nsyst']));

		if($data === false)
			return false;

		if(count($data) == 0) {
			return ["<div class='text-center'><p>Сведений в базе данных не найдено!</p></div>"];
		}
		
		/*Session::start();
		$role = Session::get('role');
		Session::commit();*/

		$role = 9;

		$header = '';
		$header_archive = '';
		$style_border = "style='vertical-align: middle; border: 1px solid gray;'"; // Стиль для ячейки
		
		if($type == 2) {
			$header = "<table class='table table-striped table-sm table-hover text-center' style='font-size: 13px; margin: 10px;' id='ActualCarForDriver'>"
				. "<tr class='table-info'>"
						. "<th style='vertical-align: middle; border: 1px solid gray;' scope='col'>№ п/п</th>"
						. "<th style='vertical-align: middle; border: 1px solid gray;' scope='col'>ФИО</th>"
						. "<th style='vertical-align: middle; border: 1px solid gray;' scope='col'>Основание</th>"
						. "<th style='vertical-align: middle; border: 1px solid gray;' scope='col'>Скорректировать</th>"
						. "<th style='vertical-align: middle; border: 1px solid gray;' scope='col'>Отправить в архив</th>"
						. "<th style='vertical-align: middle; border: 1px solid gray;' scope='col'>Удалить</th>"
				. "</tr>";
			  
			  $header_archive = "<table class='table table-striped table-sm table-hover text-center' style='font-size: 13px; margin: 10px;' id='ArchiveCarForDriver'>"
				. "<tr class='table-success'><th colspan='8' " . $style_border . ">АРХИВ</th></tr>"
				. "<tr class='table-info'>"
						. "<th style='vertical-align: middle; border: 1px solid gray;' scope='col'>№ п/п</th>"
						. "<th style='vertical-align: middle; border: 1px solid gray;' scope='col'>ФИО</th>"
						. "<th style='vertical-align: middle; border: 1px solid gray;' scope='col'>Основание</th>"
						. "<th style='vertical-align: middle; border: 1px solid gray;' scope='col'>Скорректировать</th>"
						. "<th style='vertical-align: middle; border: 1px solid gray;' scope='col'>Отправить в архив</th>"
						. "<th style='vertical-align: middle; border: 1px solid gray;' scope='col'>Удалить</th>"
				. "</tr>";
		} else {
			$header = "<table class='table table-striped table-sm table-hover text-center' style='font-size: 13px; margin: 10px;' id='ActualCarForDriver'>"
				. "<tr class='table-info'>"
						. "<th style='vertical-align: middle; border: 1px solid gray;' scope='col'>№ п/п</th>"
						. "<th style='vertical-align: middle; border: 1px solid gray;' scope='col'>Транспортное средство</th>"
						. "<th style='vertical-align: middle; border: 1px solid gray;' scope='col'>Гос. номер</th>"
						. "<th style='vertical-align: middle; border: 1px solid gray;' scope='col'>Основание</th>"
						. "<th style='vertical-align: middle; border: 1px solid gray;' scope='col'>Скорректировать</th>"
						. "<th style='vertical-align: middle; border: 1px solid gray;' scope='col'>Отправить в архив</th>"
						. "<th style='vertical-align: middle; border: 1px solid gray;' scope='col'>Удалить</th>"
				. "</tr>";
			  
			  $header_archive = "<table class='table table-striped table-sm table-hover text-center' style='font-size: 13px; margin: 10px;' id='ArchiveCarForDriver'>"
				. "<tr class='table-success'><th colspan='8' " . $style_border . ">АРХИВ</th></tr>"
				. "<tr class='table-info'>"
						. "<th style='vertical-align: middle; border: 1px solid gray;' scope='col'>№ п/п</th>"
						. "<th style='vertical-align: middle; border: 1px solid gray;' scope='col'>Транспортное средство</th>"
						. "<th style='vertical-align: middle; border: 1px solid gray;' scope='col'>Гос. номер</th>"
						. "<th style='vertical-align: middle; border: 1px solid gray;' scope='col'>Основание</th>"
						. "<th style='vertical-align: middle; border: 1px solid gray;' scope='col'>Скорректировать</th>"
						. "<th style='vertical-align: middle; border: 1px solid gray;' scope='col'>Отправить в архив</th>"
						. "<th style='vertical-align: middle; border: 1px solid gray;' scope='col'>Удалить</th>"
				. "</tr>";
		}

		$html_actual = '';
		$html_archve = '';
		
		for($i = 0, $j = 0, $k = 0; $i < count($data); $i++) {
			if($data[$i]['actual'] == 1) {
				$html_actual .= "<tr><td " . $style_border . ">" . ++$j . "</td>";
					
				if($type == 2) {
					$html_actual .= "<td " . $style_border . ">" . $data[$i]['fam'] . " " . $data[$i]['imj'] .  " " . $data[$i]['otch'] . "</td>";
					$html_actual .= "<td " . $style_border . ">" . $data[$i]['type_doc_fix'] . " № " . $data[$i]['number_doc_fix'] . " от " . Functions::convertToDate($data[$i]['date_doc_fix']) . "&nbsp;" . Functions::rendering_icon_file($data[$i]['path_to_file'], $data[$i]['file_extension']) . "</td>";
					
					if(($role > 1) && ($role != 4)) {
						$html_actual .= "<td " . $style_border . ">"
						. "<button type='button' class='btn btn-sm btn-info' id='btnEditCarForDrivers' data-fix='" . $data[$i]['id_fix'] . "' data-mode-show='2' data-nsyst='" . $data[$i]['id_driver'] . "' title='Скорректировать сведения о закреплении'><span class='fa fa-pencil'></span>&nbsp;Изменить</button></td>";
					} else {
						$html_actual .= "<td " . $style_border . "></td>";
					}

				} else {
					$html_actual .= "<td " . $style_border . ">" . $data[$i]['marka_ts'] . " " . $data[$i]['model_ts'] . "</td>";
					$html_actual .= "<td " . $style_border . ">" . $data[$i]['gos_znak'] . "</td>";
					$html_actual .= "<td " . $style_border . ">" . $data[$i]['type_doc_fix'] . " № " . $data[$i]['number_doc_fix'] . " от " . Functions::convertToDate($data[$i]['date_doc_fix']) . "&nbsp;" . Functions::rendering_icon_file($data[$i]['path_to_file'], $data[$i]['file_extension']) . "</td>";

					if(($role > 1) && ($role != 4)) {
						$html_actual .= "<td " . $style_border . ">"
						. "<button type='button' class='btn btn-sm btn-info' id='btnEditCarForDrivers' data-fix='" . $data[$i]['id_fix'] . "' data-mode-show='1' data-nsyst='" . $data[$i]['car_id'] . "' title='Скорректировать сведения о закреплении'><span class='fa fa-pencil'></span>&nbsp;Изменить</button></td>";
					} else {
						$html_actual .= "<td " . $style_border . "></td>";	
					}
				}

				if(($role > 1) && ($role != 4)) {
					$html_actual .= "<td " . $style_border . "><button type='button' class='btn btn-sm btn-primary dropdown-toggle' id='moveCarForDriverToArchive' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false' title='Переместить в архив'><span class='fa fa-folder'></span>&nbsp;В архив</button>"
					. "<div class='dropdown-menu' aria-labelledby='moveCarForDriverToArchive'>"
					. "<button type='button' class='dropdown-item' id='btnMoveCarForDriversArchive' data-operation='2' data-nsyst='" . $data[$i]['id_fix'] . "'><span class='fa fa-check text-success'></span>&nbspПодтверждаю перемещение в архив</button></div></td>"
					
					. "<td " . $style_border . "><div class='dropdown'>"
					. "<button type='button' class='btn btn-sm btn-danger dropdown-toggle' id='removeCarForDrive' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false' title='Удалить сведения о закреплении'><span class='fa fa-close'></span>&nbspУдалить</button>"
					. "<div class='dropdown-menu' aria-labelledby='removeCarForDriven'>"
					. "<button type='button' class='dropdown-item' id='btnRemoveCarForDrive' data-nsyst='" . $data[$i]['id_fix'] . "'><span class='fa fa-check text-success'></span>&nbspПодтверждаю удаление</button></div></td>";
				} else {
					$html_actual .= "<td " . $style_border . "></td><td " . $style_border . "></td>";
				}

				$html_actual .= "</tr>";
			} else {
				$html_archve .= "<tr><td " . $style_border . ">" . ++$k . "</td>";
				
				if($type == 2) {
					$html_archve .= "<td " . $style_border . ">" . $data[$i]['fam'] . " " . $data[$i]['imj'] .  " " . $data[$i]['otch'] . "</td>";
					$html_archve .= "<td " . $style_border . ">" . $data[$i]['type_doc_fix'] . " № " . $data[$i]['number_doc_fix'] . " от " . Functions::convertToDate($data[$i]['date_doc_fix']) . "&nbsp;" . Functions::rendering_icon_file($data[$i]['path_to_file'], $data[$i]['file_extension']) . "</td>";

					if(($role > 1) && ($role != 4)) {
						$html_archve .= "<td " . $style_border . ">"
						. "<button type='button' class='btn btn-sm btn-info' id='btnEditCarForDrivers' data-fix='" . $data[$i]['id_fix'] . "'data-mode-show='2' data-nsyst='" . $data[$i]['id_driver'] . "' title='Скорректировать сведения о закреплении'><span class='fa fa-pencil'></span>&nbsp;Изменить</button></td>";
					} else {
						$html_archve .= "<td " . $style_border . "></td>";	
					}
					
				} else {
					$html_archve .= "<td " . $style_border . ">" . $data[$i]['marka_ts'] . " " . $data[$i]['model_ts'] . "</td>";
					$html_archve .= "<td " . $style_border . ">" . $data[$i]['gos_znak'] . "</td>";
					$html_archve .= "<td " . $style_border . ">" . $data[$i]['type_doc_fix'] . " № " . $data[$i]['number_doc_fix'] . " от " . Functions::convertToDate($data[$i]['date_doc_fix']) . "&nbsp;" . Functions::rendering_icon_file($data[$i]['path_to_file'], $data[$i]['file_extension']) . "</td>";

					if(($role > 1) && ($role != 4)) {
						$html_archve .= "<td " . $style_border . ">"
						. "<button type='button' class='btn btn-sm btn-info' id='btnEditCarForDrivers' data-fix='" . $data[$i]['id_fix'] . "' data-mode-show='1' data-nsyst='" . $data[$i]['car_id'] . "' title='Скорректировать сведения о закреплении'><span class='fa fa-pencil'></span>&nbsp;Изменить</button></td>";
					} else {
						$html_archve .= "<td " . $style_border . "></td>";	
					}
				}

				if(($role > 1) && ($role != 4)) {
					$html_archve .= "<td " . $style_border . "><button type='button' class='btn btn-sm btn-primary dropdown-toggle' id='moveCarForDriverToArchive' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false' title='Восстановить из архива'><span class='fa fa-folder'></span>&nbspИз архива</button>"
					. "<div class='dropdown-menu' aria-labelledby='moveCarForDriverToArchive'>"
					. "<button type='button' class='dropdown-item' id='btnMoveCarForDriversArchive' data-operation='1' data-nsyst='" . $data[$i]['id_fix'] . "'><span class='fa fa-check text-success'></span>&nbspПодтверждаю восстановление из архива</button></div></td>"
					
					. "<td " . $style_border . "><div class='dropdown'>"
					. "<button type='button' class='btn btn-sm btn-danger dropdown-toggle' id='removeCarForDrive' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false' title='Удалить сведения о закреплении'><span class='fa fa-close'></span>&nbspУдалить</button>"
					. "<div class='dropdown-menu' aria-labelledby='removeCarForDriven'>"
					. "<button type='button' class='dropdown-item' id='btnRemoveCarForDrive' data-nsyst='" . $data[$i]['id_fix'] . "'><span class='fa fa-check text-success'></span>&nbspПодтверждаю удаление</button></div></td>";
				} else {
					$html_archve .= "<td " . $style_border . "></td><td " . $style_border . "></td>";
				}

				$html_archve .= "</tr>";
			}
		}

		$html = $header . $html_actual . "</table>";
		
		if(mb_strlen($html_archve) != 0)
			$html .= $header_archive . $html_archve . "</table>";
		
		return [$html];
	}

	// Отрисовка окна закрепления ТС за водителем
	public function rendering_window_drivers_for_car($post) {
		if(empty($post['nsyst']))
			return false;
		
		$nsyst = addslashes($post['nsyst']);		// ID ТС
		$operation = addslashes($post['operation']);
		$array_data = array();
		$id_fix = -1;
		
		if($operation == 1) {
			if(($array_data = $this->get_list_drvers_no_fixed($nsyst)) === false)
				return false;
		} else {
			if(empty($post['fix']))
				return false;
			$id_fix = addslashes($post['fix']);
			
			if(($array_data = $this->get_information_fixed_driver($id_fix, $nsyst)) === false)
				return false;
		}

		// Формируем список
		$list_driver = "<table class='table table-bordered table-sm table-hover' id='ListFixedItem'>";
		for($i = 0; $i < count($array_data); $i++) {
			if($operation == 1)
				$list_driver .= "<tr style='font-size: 13px;' data-check='0' id='" . $array_data[$i]['id'] . "'>"
					  . "<td class='text-center'><input type='checkbox' id='checkboxTextList'></td>"
					  . "<td id='textList'><strong>" . $array_data[$i]['fam'] . " " . $array_data[$i]['imj'] . " " . $array_data[$i]['otch'] . "</strong></td></tr>";
			else
				$list_driver .= "<tr style='font-size: 13px;' class='table-success' data-check='1' id='" . $array_data[$i]['id'] . "'>"
					  . "<td class='text-center'><input type='checkbox' id='checkboxTextList' checked disabled></td>"
					  . "<td id='textList'><strong>" . $array_data[$i]['fam'] . " " . $array_data[$i]['imj'] . " " . $array_data[$i]['otch'] . "</strong></td></tr>";
		}
		
		$list_driver .= "</table>";

		if(($html = $this->rendering_window_fix($list_driver, $nsyst, 1, $id_fix)) === false)
			return false;

		return [$html];
	}


	// Функция формирования списка водителей, которые не закреплены за данным ТС
	public function get_list_drvers_no_fixed($id_car) {
		if(empty($id_car))
			return false;

		/*Session::start();
		$role = Session::get('role');
		$kodrai = Session::get('slugba');
		Session::commit();*/

		$role = 9;
		
		$sql = "SELECT a.id, a.fam, a.imj, a.otch FROM drivers a "
					. " LEFT JOIN " . $this->table . " x ON x.id_driver=a.id AND x.car_id=" . $id_car
					. " WHERE x.id IS NULL ";

		if($role == 1)
			$sql .= " AND a.dostup=1 ";
		
		if($role == 2)
			$sql .= " AND a.dostup=1 ";

		if(($data = DB::query($sql)) === false)
			return false;
		return $data;
	}

	// Функция подгрузки информации водителя, который закреплен за ТС
	public function get_information_fixed_driver($id_fix, $id_driver) {
		if(empty($id_fix) || empty($id_driver))
			return false;
		
		/*Session::start();
		$role = Session::get('role');
		$kodrai = Session::get('slugba');
		Session::commit();*/

		$role = 9;

		$sql = "SELECT a.id as id_fix, b.fam, b.imj, b.otch, a.ibd_arx as actual, a.car_id, a.id_driver, b.id, "
				. " a.number_doc_fix, a.date_doc_fix, a.type_doc_fix "
				. " FROM " . $this->table . " a "
				. " LEFT JOIN drivers b ON b.id=a.id_driver "
				. " WHERE a.id=" . $id_fix . " AND a.id_driver=" . $id_driver;
		
		if($role == 1)
			$sql .= " AND b.dostup=1 ";
		if($role == 2)
			$sql .= " AND b.dostup=1 ";

		if(($data = DB::query($sql)) === false)
			return false;
		return $data;
	}
	
	// Функция отрисовки окна с интерфейсом закрепления водителей за транспортным средством
	// nsyst - системный номер объекта относительно которого происходит закрепление: ID ТС или водителя
	// type: 1 - закрепление водителей за ТС, 2 - закрепление ТС за водителем
	public function rendering_window_fix($list_fix_item, $nsyst, $type, $id_fix) {
		$num_doc_fix = $date_doc_fix = $type_doc_fix = $path_to_file = $file_extension = '';
		$ibd_arx = 1;
		$nsyst_temp = $nsyst;
		
		// Название формы для закрепления
		$name_form_fix = '';
		if($type == 1)
			$name_form_fix = 'Список водителей не закрепленных за данным ТС';
		else
			$name_form_fix = 'Список ТС не закрепленных за данным водителем';
		
		if($id_fix != -1) {
			if(($array_data = $this->get(['id' => $id_fix])) === false)
				return false;

			$type_doc_fix = $array_data[0]['type_doc_fix'];
			$num_doc_fix = $array_data[0]['number_doc_fix'];
			$date_doc_fix = Functions::convertToDate($array_data[0]['date_doc_fix']);
			$ibd_arx = $array_data[0]['ibd_arx'];
			$path_to_file = $array_data[0]['path_to_file'];
			$file_extension = $array_data[0]['file_extension'];

			if($type == 1) {
				$nsyst_temp = $array_data[0]['car_id'];
				$name_form_fix = 'Водитель закрепленный за данным ТС';
			} else {
				$nsyst_temp = $array_data[0]['id_driver'];
				$name_form_fix = 'ТС закрепленный за данным водителем';
			}
		}

		$list_type_fix_doc = Directory::get_directory(14, $type_doc_fix);

		$html = 	"<div class='col-sm-12'>"
						. "<div class='card-deck' style='margin: 10px;'>"
						
							. "<div class='col-sm-6'>"
								. "<div class='card border-dark'>"
									. "<div class='card-header text-center'><strong>" . $name_form_fix . "</strong>"
										. "<div class='input-group'>"
											. "<input type='text' class='form-control form-control-sm black-text' id='searchFieldTextCarForDrivers' placeholder='Введите критерий поиска ...'>"
											. "<div class='input-group-append'>"
												. "<button type='button' class='btn btn-sm btn-outline-secondary' id='btnSearchListCarForDrivers' title='Поиск'><span class='fa fa-search'></span>&nbspПоиск</button>"
											. "</div>"
										. "</div>"
									. "</div>"
								
									. "<div class='card-body card-block-list-drivers'>"
										. $list_fix_item
									. "</div>"
								. "</div>"
							. "</div>"
							
							. "<div class='col-sm-6'>"
								. "<div class='card border-dark'>"
									. "<div class='card-header text-center'><strong>Заполните необходимые поля</strong></div>"
									. "<div class='card-body'>"
									
									. "<div id='cardItemsFixDocument'>"
										. "<div class='form-row'>"
											. "<div class='col col-sm-3 mb-1 text-right'>"
												. "<label for='type_doc_fix' class='text-muted' style='font-size: 13px;'><strong>Основание</strong></label>"
											. "</div>"
											. "<div class='col col-sm-9 mb-1'>"
												. "<select class='custom-select custom-select-sm black-text' id='type_doc_fix' data-mandatory='true' data-datatype='number' data-message-error='Заполните обязательное поле: Основание'>" . $list_type_fix_doc . "</select>"
											. "</div>"
										. "</div>"
										
										. "<div class='form-row'>"
											. "<div class='col col-sm-3 mb-1 text-right'>"
												. "<label for='number_doc_fix' class='text-muted' style='font-size: 13px;'><strong>Номер</strong></label>"
											. "</div>"
											. "<div class='col col-sm-5 mb-1'>"
												. "<input type='text' class='form-control form-control-sm black-text' id='number_doc_fix' maxlength='20' placeholder='Номер документа' data-mandatory='true' 	data-datatype='char' data-message-error='Заполните обязательное поле: Номер' value='" . $num_doc_fix . "'>"
											. "</div>"
										. "</div>"
										
										. "<div class='form-row'>"
											. "<div class='col col-sm-3 mb-1 text-right'>"
												. "<label for='date_doc_fix' class='text-muted' style='font-size: 13px;'><strong>Дата</strong></label>"
											. "</div>"
											. "<div class='col col-sm-5 mb-1'>"
												. "<input type='text' class='form-control form-control-sm black-text datepicker-here' id='date_doc_fix' maxlength='10' placeholder='Дата документа' data-mandatory='true' data-datatype='date' data-message-error='Заполните обязательное поле: Дата документа' value='" . $date_doc_fix . "'>"
											. "</div>"
										. "</div>"
									. "</div>"

										. "<div class='form-row'>"
				
											. "<div class='col-3 mb-1 text-right' style='vertical-align: center;'>"
												. "<label for='comment_certificate_reg' class='text-muted' style='font-size: 13px;'><strong>Эл. образ</strong></label>"
											. "</div>"
											
											. "<div class='col-5 mb-1 text-left'>"
												. "<div id='uploadFileContainer'>" . Functions::rendering_icon_file($path_to_file, $file_extension, $id_fix, 9, true) . "</div>"
											. "</div>"
											
											. "<div class='col-4 mb-1 text-right'>"
												. "<span class='btn btn-sm btn-primary fileinput-button' title='Выберите файл'>"
													. "<span class='fa fa-folder-open'>&nbsp</span>Выберите файл"
														. "<input id='btnAddFileModalWindow' type='file' name='files' accept='.pdf'>"
												. "</span>"
											. "</div>"
											
										. "</div>"

									. "<div>"
								. "</div>"
							. "</div>"
							
						. "</div>";
		$html .= "</div>";

		$html .= "<div class='col-sm-12 text-center'><button class='btn btn-success' id='saveFixCarForDriver' style='margin: 10px;' data-nsyst='" . $nsyst_temp . "' data-fix='" . $id_fix . "' data-type-save='" . $type . "' data-ibd-arx='" . $ibd_arx . "'><span class='fa fa-check'></span>&nbsp;Сохранить изменения</button></div>";

		return [$html];
	}

	// Отрисовка окна закрепления водителей за ТС
	public function rendering_window_cars_for_driver($post) {
		if(empty($post['nsyst']) || empty($post['operation']))
			return false;

		$nsyst = addslashes($post['nsyst']);		// ID водителя
		$operation = addslashes($post['operation']);
		$array_data = array();
		
		$id_fix = -1;
		
		if($operation == 1) {
			if(($array_data = $this->get_list_cars_no_fixed($nsyst)) === false)
				return false;
		} else {
			if(empty($post['fix']))
				return false;
			$id_fix = addslashes($post['fix']);
			
			if(($array_data = $this->get_information_fixed_car($id_fix, $nsyst)) === false)
				return false;
		}
		
		// Формируем список ТС
		$list_car = "<table class='table table-bordered table-sm table-hover' id='ListFixedItem'>";
		for($i = 0; $i < count($array_data); $i++) {
			if($operation == 1)
				$list_car .= "<tr style='font-size: 15px;' data-check='0' id='" . $array_data[$i]['id'] . "'>"
					  . "<td class='text-center'><input type='checkbox' id='checkboxTextList'></td>"
					  . "<td id='textList' class='text-left'><strong>" . $array_data[$i]['gos_znak'] . "</strong> " . $array_data[$i]['markats'] . " " . $array_data[$i]['modelts'] . "</td></tr>";
			else
				$list_car .= "<tr style='font-size: 15px;' data-check='1' class='table-success' id='" . $array_data[$i]['id'] . "'>"
					  . "<td class='text-center'><input type='checkbox' id='checkboxTextList' checked disabled></td>"
					  . "<td id='textList'><strong>" . $array_data[$i]['gos_znak'] . "</strong> " . $array_data[$i]['markats'] . " " . $array_data[$i]['modelts'] . "</td></tr>";
		}
		$list_car .= "</table>";

		if(($html = $this->rendering_window_fix($list_car, $nsyst, 2, $id_fix)) === false)
			return false;

		return [$html];
	}

	// Функция формирования списка ТС, которые не закреплены за данным водителем
	public function get_list_cars_no_fixed($id_driver) {
		if(empty($id_driver))
			return false;
		
		/*Session::start();
		$role = Session::get('role');
		$kodrai = Session::get('slugba');
		Session::commit();*/

		$role = 9;
		
		$sql = "SELECT a.id, x1.text as markats, x2.text as modelts, a.gos_znak FROM cars a "
				   . " LEFT JOIN " . $this->table . " x ON x.car_id=a.id AND x.ibd_arx=1 AND x.id_driver=" . $id_driver
				   . " LEFT JOIN s2i_klass x1 ON a.marka=x1.kod AND x1.nomer=3 "
				   . " LEFT JOIN s2i_klass x2 ON a.model=x2.kod AND x2.nomer=4 "
				   . " WHERE x.id IS NULL ";
		
		if($role == 1)
			$sql .= " AND a.dostup=1 ";
		
		if($role == 2)
			$sql .= " AND a.dostup=1 ";
		
		$sql .= " ORDER BY a.id ";
	   
		if(($data = DB::query($sql)) === false)
			return false;
		return $data;
	}

	// Функция подгрузи информации ТС, которое закреплено за водителем
	public function get_information_fixed_car($id_fix, $id_car) {
		if(empty($id_fix) || empty($id_car))
			return false;
		
		/*Session::start();
		$role = Session::get('role');
		$kodrai = Session::get('slugba');
		Session::commit();*/

		$role = 9;
		
		$sql = "SELECT a.id as id_fix, x1.text as markats, x2.text as modelts, b.gos_znak, a.ibd_arx as actual, a.car_id, a.id_driver, b.id, "
				. " a.number_doc_fix, a.date_doc_fix, a.type_doc_fix "
				. " FROM " . $this->table . " a "
				. " LEFT JOIN cars b ON b.id=a.car_id "
				. " LEFT JOIN s2i_klass x1 ON x1.kod=b.marka AND x1.nomer=3 "
				. " LEFT JOIN s2i_klass x2 ON x2.kod=b.model AND x2.nomer=4 "
				. " WHERE a.id=" . $id_fix . " AND a.car_id=" . $id_car;
		
		if($role == 1)
			$sql .= " AND b.dostup=1 ";
		if($role == 2)
			$sql .= " AND b.dostup=1 ";
		   
		if(($data = DB::query($sql)) === false)
			return false;
		return $data;
	}

	// Процедура сохранения водительских удостоверений
	public function save_car_for_driver($post) {
		if(empty($post['nsyst']) || empty($post['JSON']) || empty($post['arrayItemFix']) || empty($post['typeSave']))
			return false;

		$type_save = addslashes($post['typeSave']);	// Тип сохранения: 1 - закрепить водителей за ТС, 2 - закрепить ТС за водителем
		$temp_array = array();	// Временный массив, в который собираем сведения
		$json_array = json_decode($post['JSON']);	// Декодированный массив формата JSON с массивом параметров для вставки
		
		// Формируем новый массив из формата stdClass  формат [поле] => { массив параметров }
		foreach($json_array as $field => $array_field_item) {
			$temp_array[$field] = $array_field_item;
		}
		
		// Добавляем к полученному массиву ID водителя или ТС для вставки в базу данных
		$array_item_fix = json_decode($post['arrayItemFix']);	
		foreach($array_item_fix as $item_value) {
			if($type_save == 1)
				$temp_array['id_driver'] = array("value" => $item_value, "type" => "number");
			else
				$temp_array['car_id'] = array("value" => $item_value, "type" => "number");

			// Формируем результирующий массив для сохранения
			$result_save_array = array('JSON' => json_encode($temp_array), 'nsyst' => addslashes($post['nsyst']));

			// Выполняем процедуру сохранения
			if(($id = $this->save($result_save_array)) === false)
				return false;
			
			$msg_error = $file_name = '';

			if(!empty($_FILES)) {
				if($this->save_file($_FILES, $id) === false)
					return false;
			}
		}
		
		return true;
	}

	// Функция перевода в архив сведений о закреплении
	public function move_to_archive($post) {
		if(empty($post['nsyst']) || empty($post['operation']))
			return false;

		$operation = addslashes($post['operation']);
		$id_fix = addslashes($post['nsyst']);
		
		$archive = 1;	// Значение архива
		if($operation == 1)
			$archive = 1;
		else
			$archive = 2;
		
		$sql = "UPDATE " . $this->table . " SET ibd_arx=" . $archive . " WHERE id=" . $id_fix;		   
		if(DB::query($sql, DB::INSERT_OR_UPDATE) === false)
			return false;
		return true;
	}
}