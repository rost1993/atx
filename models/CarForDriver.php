<?php

namespace IcKomiApp\models;

use IcKomiApp\core\Model;
use IcKomiApp\core\Logic;
use IcKomiApp\core\Functions;
use IcKomiApp\lib\Database\DB;

class CarForDriver extends Model {
	protected $table = 'car_for_driver';

	protected $sql_get_record = "";

	protected $sql_get_list = "";

	// Функция формирования списка водителей, которые закреплены за данным ТС
	public function get_list_drivers_fixed($id_car) {
		/*Session::start();
		$role = Session::get('role');
		$kodrai = Session::get('slugba');
		Session::commit();*/

		$role = 9;
		
		$sql = "SELECT b.fam, b.imj, b.otch, x1.text as kodrai_driver, x2.text as slugba_driver, a.ibd_arx as actual, a.id as id_fix, a.car_id, a.id_driver, "
				. " a.number_doc_fix, a.date_doc_fix, x3.text as type_doc_fix, a.path_to_file, a.file_extension "
				. " FROM " . $this->table . " a "
				. " LEFT JOIN drivers b ON b.id=a.id_driver "
				. " LEFT JOIN s2i_klass x1 ON x1.kod=b.kodrai AND x1.nomer=11 "
				. " LEFT JOIN s2i_klass x2 ON x2.kod=b.slugba AND x2.nomer=1 "
				. " LEFT JOIN s2i_klass x3 ON x3.kod=a.type_doc_fix AND x3.nomer=14 "
				. " WHERE a.car_id=" . $id_car;
		
		if($role == 1)
			$sql .= " AND a.dostup=1";
		
		if($role == 2)
			$sql .= " AND a.dostup=1 ";
		
		$sql .= " ORDER BY b.kodrai, b.slugba, b.fam, b.imj, b.otch";

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
						. "<th style='vertical-align: middle; border: 1px solid gray;' scope='col'>Район</th>"
						. "<th style='vertical-align: middle; border: 1px solid gray;' scope='col'>Служба</th>"
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
						. "<th style='vertical-align: middle; border: 1px solid gray;' scope='col'>Район</th>"
						. "<th style='vertical-align: middle; border: 1px solid gray;' scope='col'>Служба</th>"
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
					$html_actual .= "<td " . $style_border . ">" . $data[$i]['kodrai_driver'] . "</td>";
					$html_actual .= "<td " . $style_border . ">" . $data[$i]['slugba_driver'] . "</td>";
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
					$html_archve .= "<td " . $style_border . ">" . $data[$i]['kodrai_driver'] . "</td>";
					$html_archve .= "<td " . $style_border . ">" . $data[$i]['slugba_driver'] . "</td>";
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

	
}