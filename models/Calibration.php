<?php

namespace IcKomiApp\models;

use IcKomiApp\core\Model;
use IcKomiApp\core\Logic;
use IcKomiApp\core\Functions;
use IcKomiApp\lib\Database\DB;
use IcKomiApp\widgets\Directory;

class Calibration extends Model {

	protected $table = 'car_calibration';
	protected $trigger_operation = 17;
	protected $remove_directory = 1;

	protected $sql_get_record = "SELECT * FROM {table} WHERE id={id}";
	protected $sql_get_list = "SELECT a.*, x1.text as firma_calibration_text FROM {table} a "
			. " LEFT JOIN s2i_klass x1 ON x1.kod=a.firma_calibration AND x1.nomer=37 "
			. " WHERE a.id_car={id}"
			. " ORDER BY a.date_calibration DESC";

	// Функция отрисовки списка ПТС
	function rendering_list($post) {
		if(empty($post['nsyst']))
			return false;

		if(($data = $this->get_list(addslashes($post['nsyst']))) === false)
			return false;

		/*Session::start();
		$role = Session::get('role');
		Session::commit();*/
		
		$role =9;

		$html = "";
		if(count($data) == 0) {
			$html = "<div class='text-center'><p>Сведений в базе данных не найдено!</p></div>";
		} else {
			$style_border = "style='vertical-align: middle; border: 1px solid gray;'";

			// Формируем готовый HTML код для списка закрепленных ТС
			$html = "<table class='table table-striped table-sm table-hover text-center' style='font-size: 13px; margin: 10px;'>"
					. "<tr class='table-info'>"
						. "<th " . $style_border . " scope='col'>№ п/п</th>"
						. "<th " . $style_border . " scope='col'>Дата калибровки</th>"
						. "<th " . $style_border . " scope='col'>Дата следующей калибровки</th>"
						. "<th " . $style_border . " scope='col'>Кем произведена калибровка</th>"
						. "<th " . $style_border . " scope='col'>Эл. образ</th>"
						. "<th " . $style_border . " scope='col'>Изменить</th>"
						. "<th " . $style_border . " scope='col'>Удалить</th>"
					. "</tr>";
			
			$list_archive = "";
	
			for($i = 0, $j = 1, $k = 1; $i < count($data); $i++) {
				if($data[$i]['ibd_arx'] == 1) {
					$html .= "<tr>"
							. "<td " . $style_border . ">" . ($j++) . "</td>"
							. "<td " . $style_border . ">" . Functions::convertToDate($data[$i]['date_calibration']) . "</td>"
							. "<td " . $style_border . ">" . Functions::convertToDate($data[$i]['date_next_calibration']) . "</td>"
							. "<td " . $style_border . ">" . $data[$i]['firma_calibration_text'] . "</td>"
							. "<td " . $style_border . ">" . Functions::rendering_icon_file($data[$i]['path_to_file'], $data[$i]['file_extension']) . "</td>";
							
							if(($role > 1) && ($role != 4)) {
								$html .= "<td " . $style_border . ">"
								. "<button type='button' class='btn btn-sm btn-info' id='btnEditItem' data-nsyst='" . $data[$i]['id'] . "' data-item='17'><span class='fa fa-pencil'>&nbsp;</span>Изменить</button></td>"
								. "<td " . $style_border . "><div class='dropdown'>"
								. "<button type='button' class='btn btn-sm btn-danger dropdown-toggle' id='btnDropdownRemove' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false' title='Удалить полис ОСАГО'><span class='fa fa-close'>&nbsp;</span>Удалить</button>"
								. "<div class='dropdown-menu' aria-labelledby='btnDropdownRemove'>"
									. "<button type='button' class='dropdown-item' id='btnRemoveItem' data-nsyst='" . $data[$i]['id'] . "' data-item='17' data-object='" . $data[$i]['id_car'] . "'><span class='fa fa-check text-success'>&nbsp;</span>Подтверждаю удаление</button></div></div>"
							. "</td>";
							} else {
								$html .= "<td " . $style_border . "></td><td " . $style_border . "></td>";
							}

						$html .= "</tr>";
				} else {
					$list_archive .= "<tr>"
							. "<td " . $style_border . ">" . ($k++) . "</td>"
							. "<td " . $style_border . ">" . Functions::convertToDate($data[$i]['date_calibration']) . "</td>"
							. "<td " . $style_border . ">" . Functions::convertToDate($data[$i]['date_next_calibration']) . "</td>"
							. "<td " . $style_border . ">" . $data[$i]['firma_calibration_text'] . "</td>"
							. "<td " . $style_border . ">" . Functions::rendering_icon_file($data[$i]['path_to_file'], $data[$i]['file_extension']) . "</td>";

							if(($role > 1) && ($role != 4)) {
								$list_archive .= "<td " . $style_border . ">"
								. "<button type='button' class='btn btn-sm btn-info' id='btnEditItem' data-nsyst='" . $data[$i]['id'] . "' data-item='17'><span class='fa fa-pencil'>&nbsp;</span>Изменить</button></td>"
								. "<td " . $style_border . "><div class='dropdown'>"
								. "<button type='button' class='btn btn-sm btn-danger dropdown-toggle' id='btnDropdownRemove' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false' title='Удалить полис ОСАГО'><span class='fa fa-close'>&nbsp;</span>Удалить</button>"
								. "<div class='dropdown-menu' aria-labelledby='btnDropdownRemove'>"
									. "<button type='button' class='dropdown-item' id='btnRemoveItem' data-nsyst='" . $data[$i]['id'] . "' data-item='17' data-object='" . $data[$i]['id_car'] . "'><span class='fa fa-check text-success'>&nbsp;</span>Подтверждаю удаление</button></div></div>"
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
							. "<th " . $style_border . " scope='col'>Дата калибровки</th>"
							. "<th " . $style_border . " scope='col'>Дата следующей калибровки</th>"
							. "<th " . $style_border . " scope='col'>Кем произведена калибровка</th>"
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
		
		$date_calibration = $date_next_calibration = $firma_calibration = $path_to_file = $file_extension = "";
		if($id != -1) {
			if(($data = $this->get(['id' => $id])) === false)
				return false;
			
			if(count($data) > 0) {
				$date_calibration = Functions::convertToDate($data[0]['date_calibration']);
				$date_next_calibration = Functions::convertToDate($data[0]['date_next_calibration']);
				$firma_calibration = $data[0]['firma_calibration'];
				$path_to_file = $data[0]['path_to_file'];
				$file_extension = $data[0]['file_extension'];
			}
		}
		

		$select_firma_calibration = Directory::get_directory(37, $firma_calibration);

		$html = "<div class='col-12'>"
				. "<div id='formCalibration'>"
					. "<div class='form-row'>"
						. "<div class='col-4 mb-1 text-right' style='vertical-align: center;'>"
							. "<label for='date_calibration' class='text-muted font-weight-bold fs-13'>Дата калибровки</label>"
						. "</div>"
						. "<div class='col-3 mb-1'>"
							. "<input type='text' class='form-control form-control-sm black-text' id='date_calibration' maxlength='20' placeholder='Дата калибровки' data-mandatory='true' data-message-error='Заполните обязательное поле: Дата калибровки' data-datatype='date' value='" . $date_calibration . "'>"
						. "</div>"
					. "</div>"
				
					. "<div class='form-row'>"
						. "<div class='col-4 mb-1 text-right' style='vertical-align: center;'>"
							. "<label for='date_next_calibration' class='text-muted font-weight-bold fs-13'>Дата следующей калибровки</label>"
						. "</div>"
						. "<div class='col-3 mb-1'>"
							. "<input type='text' class='form-control form-control-sm black-text' id='date_next_calibration' maxlength='10' placeholder='Дата следующей калибровки' data-mandatory='true' data-message-error='Заполните обязательное поле: Дата следующей калибровки' data-datatype='date' value='" . $date_next_calibration . "'>"
						. "</div>"
					. "</div>"
				
					. "<div class='form-row'>"
						. "<div class='col-4 mb-1 text-right' style='vertical-align: center;'>"
							. "<label for='firma_calibration' class='text-muted font-weight-bold fs-13'>Кем произведена калибровка</label>"
						. "</div>"
						. "<div class='col-6 mb-1'>"
							. "<select class='custom-select custom-select-sm black-text' id='firma_calibration' data-mandatory='true' data-message-error='Заполните обязательное поле: Кем произведена калибровка' data-datatype='number'>" . $select_firma_calibration . "</select>"
						. "</div>"
					. "</div>"

				. "</div>"
				
				. "<div class='form-row'>"
					. "<div class='col-4 mb-1 text-right' style='vertical-align: center;'>"
						. "<label for='comment_certificate_reg' class='text-muted font-weight-bold fs-13'>Эл. образ</label>"
					. "</div>"
					
					. "<div class='col-4 mb-1 text-left'>"
						. "<div id='uploadFileContainer'>" . Functions::rendering_icon_file($path_to_file, $file_extension, $id, 18, true) . "</div>"
					. "</div>"
					
					. "<div class='col-2 mb-1 text-right'>"
						. "<span class='btn btn-sm btn-primary fileinput-button' title='Выберите файл'>"
							. "<span class='fa fa-folder-open'>&nbsp</span>Выберите файл"
								. "<input id='btnAddFileModalWindow' type='file' name='files' accept='.pdf'>"
						. "</span>"
					. "</div>"
					
				. "</div>"
				
				. "<div class='form-row'>"
					. "<div class='col col-sm-12 mb-1' style='vertical-align: center;'>"
						. "<strong><label class='form-check-label' id='error-message' style='font-size: 13px; color: red;'></label></strong>"
					. "</div>"
				. "</div>"
				
			. "</div>";

		return [$html];
	}
	
}