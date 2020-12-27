<?php

namespace IcKomiApp\models;

use IcKomiApp\core\User;
use IcKomiApp\core\Model;
use IcKomiApp\core\Logic;
use IcKomiApp\core\Functions;
use IcKomiApp\lib\Database\DB;
use IcKomiApp\widgets\Directory;

class Maintenance extends Model {

	protected $table = 'car_maintenance';
	protected $trigger_operation = 21;
	protected $sql_get_record = "SELECT * FROM {table} WHERE id={id}";
	protected $remove_directory = 1;

	protected $sql_get_list = "SELECT a.* FROM {table} a "
			. " WHERE a.id_car={id}"
			. " ORDER BY a.date_maintenance DESC";

	// Функция отрисовки списка ПТС
	function rendering_list($post) {
		if(empty($post['nsyst']))
			return false;

		if(($data = $this->get_list(addslashes($post['nsyst']))) === false)
			return false;
		
		$role = User::get('role');

		$html = "";
		if(count($data) == 0) {
			$html = "<div class='text-center'><p>Сведений в базе данных не найдено!</p></div>";
		} else {
			$style_border = "style='vertical-align: middle; border: 1px solid gray;'";

			// Формируем готовый HTML код для списка закрепленных ТС
			$html = "<table class='table table-striped table-sm table-hover text-center' style='font-size: 13px; margin: 10px;'>"
					. "<tr class='table-info'>"
						. "<th " . $style_border . " scope='col'>№ п/п</th>"
						. "<th " . $style_border . " scope='col'>Дата тех. обслуживания</th>"
						. "<th " . $style_border . " scope='col'>Пробег на момент тех. обслуживания</th>"
						. "<th " . $style_border . " scope='col'>Эл. образ</th>"
						. "<th " . $style_border . " scope='col'>Изменить</th>"
						. "<th " . $style_border . " scope='col'>Удалить</th>"
					. "</tr>";
			
			$list_archive = "";
	
			for($i = 0, $j = 1, $k = 1; $i < count($data); $i++) {
				if($data[$i]['ibd_arx'] == 1) {
					$html .= "<tr>"
							. "<td " . $style_border . ">" . ($j++) . "</td>"
							. "<td " . $style_border . ">" . Functions::convertToDate($data[$i]['date_maintenance']) . "</td>"
							. "<td " . $style_border . ">" . $data[$i]['mileage_maintenance'] . "</td>"
							. "<td " . $style_border . ">" . Functions::rendering_icon_file($data[$i]['path_to_file'], $data[$i]['file_extension']) . "</td>";
							
							if($role >= 2) {
								$html .= "<td " . $style_border . ">"
								. "<button type='button' class='btn btn-sm btn-info' id='btnEditItem' data-nsyst='" . $data[$i]['id'] . "' data-item='21'><span class='fa fa-pencil'>&nbsp;</span>Изменить</button></td>"
								. "<td " . $style_border . "><div class='dropdown'>"
								. "<button type='button' class='btn btn-sm btn-danger dropdown-toggle' id='btnDropdownRemove' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false' title='Удалить'><span class='fa fa-close'>&nbsp;</span>Удалить</button>"
								. "<div class='dropdown-menu' aria-labelledby='btnDropdownRemove'>"
									. "<button type='button' class='dropdown-item' id='btnRemoveItem' data-nsyst='" . $data[$i]['id'] . "' data-item='21' data-object='" . $data[$i]['id_car'] . "'><span class='fa fa-check text-success'>&nbsp;</span>Подтверждаю удаление</button></div></div>"
							. "</td>";
							} else {
								$html .= "<td " . $style_border . "></td><td " . $style_border . "></td>";
							}

						$html .= "</tr>";
				} else {
					$list_archive .= "<tr>"
							. "<td " . $style_border . ">" . ($k++) . "</td>"
							. "<td " . $style_border . ">" . Functions::convertToDate($data[$i]['date_maintenance']) . "</td>"
							. "<td " . $style_border . ">" . $data[$i]['mileage_maintenance'] . "</td>"
							. "<td " . $style_border . ">" . Functions::rendering_icon_file($data[$i]['path_to_file'], $data[$i]['file_extension']) . "</td>";

							if($role >= 2) {
								$list_archive .= "<td " . $style_border . ">"
								. "<button type='button' class='btn btn-sm btn-info' id='btnEditItem' data-nsyst='" . $data[$i]['id'] . "' data-item='21'><span class='fa fa-pencil'>&nbsp;</span>Изменить</button></td>"
								. "<td " . $style_border . "><div class='dropdown'>"
								. "<button type='button' class='btn btn-sm btn-danger dropdown-toggle' id='btnDropdownRemove' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false' title='Удалить'><span class='fa fa-close'>&nbsp;</span>Удалить</button>"
								. "<div class='dropdown-menu' aria-labelledby='btnDropdownRemove'>"
									. "<button type='button' class='dropdown-item' id='btnRemoveItem' data-nsyst='" . $data[$i]['id'] . "' data-item='21' data-object='" . $data[$i]['id_car'] . "'><span class='fa fa-check text-success'>&nbsp;</span>Подтверждаю удаление</button></div></div>"
							. "</td>";
							} else {
								$list_archive .= "<td " . $style_border . "></td><td " . $style_border . "></td>";
							}
						$list_archive .= "</tr>";
				}
			}
			
			$html .= "</table>";
			
			if(mb_strlen($list_archive) > 0) {
				// Формируем готовый HTML код для списка закрепленных ТС
				$html .= "<table class='table table-striped table-sm table-hover text-center' style='font-size: 13px; margin: 10px;'>"
						. "<tr class='table-success'><th colspan='8' style='vertical-align: middle;  border: 1px solid gray;'>АРХИВ</th></tr>"
						. "<tr class='table-info'>"
							. "<th " . $style_border . " scope='col'>№ п/п</th>"
							. "<th " . $style_border . " scope='col'>Дата тех. обслуживания</th>"
							. "<th " . $style_border . " scope='col'>Пробег на момент тех. обслуживания</th>"
							. "<th " . $style_border . " scope='col'>Эл. образ</th>"
							. "<th " . $style_border . " scope='col'>Изменить</th>"
							. "<th " . $style_border . " scope='col'>Удалить</th>"
						. "</tr>";
				$html .= $list_archive . "</table>";
			}
		}

		return [$html];
	}

	public function rendering_window($post) {
		if(empty($post['nsyst']))
			return false;
		
		$id = addslashes($post['nsyst']);
		
		$date_maintenance = $mileage_maintenance = $path_to_file = $file_extension = "";
		if($id != -1) {
			if(($data = $this->get(['id' => $id])) === false)
				return false;
			
			if(count($data) > 0) {
				$date_maintenance = Functions::convertToDate($data[0]['date_maintenance']);
				$mileage_maintenance = $data[0]['mileage_maintenance'];
				$path_to_file = $data[0]['path_to_file'];
				$file_extension = $data[0]['file_extension'];
			}
		}

		$html = "<div class='col-12'>"
				. "<div id='formMaintenance'>"
					. "<div class='form-row'>"
						. "<div class='col-4 mb-1 text-right'>"
							. "<label for='date_maintenance' class='text-muted font-weight-bold fs-13'>Дата тех. обслуживания</label>"
						. "</div>"
						. "<div class='col-4 mb-1'>"
							. "<input type='text' class='form-control form-control-sm black-text' id='date_maintenance' maxlength='10' placeholder='Дата' data-mandatory='true' data-message-error='Заполните обязательное поле: Дата' data-datatype='date' value='" . $date_maintenance . "'>"
						. "</div>"
					. "</div>"

					. "<div class='form-row'>"
						. "<div class='col-4 mb-1 text-right'>"
							. "<label for='mileage_maintenance' class='text-muted font-weight-bold fs-13'>Пробег на момент тех. обслуживания</label>"
						. "</div>"
						. "<div class='col-4 mb-1'>"
							. "<input type='text' class='form-control form-control-sm black-text' id='mileage_maintenance' maxlength='20' placeholder='Пробег' data-mandatory='true' data-message-error='Заполните обязательное поле: Пробег' data-datatype='char' value='" . $mileage_maintenance . "'>"
						. "</div>"
					. "</div>"
				. "</div>"
				
				. "<div class='form-row'>"
				
					. "<div class='col-4 mb-1 text-right'>"
						. "<label for='comment_certificate_reg' class='text-muted font-weight-bold fs-13'>Эл. образ</label>"
					. "</div>"
					
					. "<div class='col-4 mb-1 text-left'>"
						. "<div id='uploadFileContainer'>" . Functions::rendering_icon_file($path_to_file, $file_extension, $id, 22, true) . "</div>"
					. "</div>"
					
					. "<div class='col-2 mb-1 text-right'>"
						. "<span class='btn btn-sm btn-primary fileinput-button' title='Выберите файл'>"
							. "<span class='fa fa-folder-open'>&nbsp;</span>Выберите файл"
								. "<input id='btnAddFileModalWindow' type='file' name='files' accept='.pdf'>"
						. "</span>"
					. "</div>"
					
				. "</div>"
				
				. "<div class='form-row'>"
					. "<div class='col col-sm-12 mb-1'>"
						. "<label class='form-check-label font-weight-bold fs-13' id='error-message' style='color: red;'></label>"
					. "</div>"
				. "</div>"
				
			. "</div>";

		return [$html];
	}
	
}