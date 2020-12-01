<?php

namespace IcKomiApp\models;

use IcKomiApp\core\Model;
use IcKomiApp\core\Logic;
use IcKomiApp\core\Functions;
use IcKomiApp\lib\Database\DB;
use IcKomiApp\widgets\Directory;

class CarsDopog extends Model {

	protected $table = 'cars_dopog';
	protected $trigger_operation = 16;
	protected $sql_get_record = "SELECT * FROM {table} WHERE id={id}";

	protected $sql_get_list = "SELECT a.id, a.id_car, a.number_dopog, a.date_start_dopog, a.date_end_dopog, x1.text as firma_dopog_text, a.ibd_arx, a.path_to_file, a.file_extension FROM {table} a "
			. " LEFT JOIN s2i_klass x1 ON x1.kod=a.firma_dopog AND x1.nomer=34 "
			. " WHERE a.id_car={id} "
			. " ORDER BY a.date_end_dopog DESC";

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
						. "<th " . $style_border . " scope='col'>Номер свидетельства</th>"
						. "<th " . $style_border . " scope='col'>Срок действия</th>"
						. "<th " . $style_border . " scope='col'>Кем выдано</th>"
						. "<th " . $style_border . " scope='col'>Эл. образ</th>"
						. "<th " . $style_border . " scope='col'>Изменить</th>"
						. "<th " . $style_border . " scope='col'>Удалить</th>"
					. "</tr>";
			
			$list_archive = "";
	
			for($i = 0, $j = 1, $k = 1; $i < count($data); $i++) {
				if($data[$i]['ibd_arx'] == 1) {
					$html .= "<tr>"
							. "<td " . $style_border . ">" . ($j++) . "</td>"
							. "<td " . $style_border . ">" . $data[$i]['number_dopog'] . "</td>"
							. "<td " . $style_border . ">" . Functions::convertToDate($data[$i]['date_start_dopog']) . "&nbsp;-&nbsp;" . Functions::convertToDate($data[$i]['date_end_dopog']) . "</td>"
							. "<td " . $style_border . ">" . $data[$i]['firma_dopog_text'] . "</td>"
							. "<td " . $style_border . ">" . Functions::rendering_icon_file($data[$i]['path_to_file'], $data[$i]['file_extension']) . "</td>";
							
							if(($role > 1) && ($role != 4)) {
								$html .= "<td " . $style_border . ">"
								. "<button type='button' class='btn btn-sm btn-info' id='btnEditItem' data-nsyst='" . $data[$i]['id'] . "' data-item='16'><span class='fa fa-pencil'>&nbsp</span>Изменить</button></td>"
								. "<td " . $style_border . "><div class='dropdown'>"
								. "<button type='button' class='btn btn-sm btn-danger dropdown-toggle' id='btnDropdownRemove' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false' title='Удалить полис ОСАГО'><span class='fa fa-close'>&nbsp</span>Удалить</button>"
								. "<div class='dropdown-menu' aria-labelledby='btnDropdownRemove'>"
									. "<button type='button' class='dropdown-item' id='btnRemoveItem' data-nsyst='" . $data[$i]['id'] . "' data-item='16' data-object='" . $data[$i]['id_car'] . "'><span class='fa fa-check text-success'>&nbsp</span>Подтверждаю удаление</button></div></div>"
							. "</td>";
							} else {
								$html .= "<td " . $style_border . "></td><td " . $style_border . "></td>";
							}

						$html .= "</tr>";
				} else {
					$list_archive .= "<tr>"
							. "<td " . $style_border . ">" . ($k++) . "</td>"
							. "<td " . $style_border . ">" . $data[$i]['number_dopog'] . "</td>"
							. "<td " . $style_border . ">" . Functions::convertToDate($data[$i]['date_start_dopog']) . "&nbsp;-&nbsp;" . Functions::convertToDate($data[$i]['date_end_dopog']) . "</td>"
							. "<td " . $style_border . ">" . $data[$i]['firma_dopog_text'] . "</td>"
							. "<td " . $style_border . ">" . Functions::rendering_icon_file($data[$i]['path_to_file'], $data[$i]['file_extension']) . "</td>";

							if(($role > 1) && ($role != 4)) {
								$list_archive .= "<td " . $style_border . ">"
								. "<button type='button' class='btn btn-sm btn-info' id='btnEditItem' data-nsyst='" . $data[$i]['id'] . "' data-item='16'><span class='fa fa-pencil'>&nbsp</span>Изменить</button></td>"
								. "<td " . $style_border . "><div class='dropdown'>"
								. "<button type='button' class='btn btn-sm btn-danger dropdown-toggle' id='btnDropdownRemove' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false' title='Удалить полис ОСАГО'><span class='fa fa-close'>&nbsp</span>Удалить</button>"
								. "<div class='dropdown-menu' aria-labelledby='btnDropdownRemove'>"
									. "<button type='button' class='dropdown-item' id='btnRemoveItem' data-nsyst='" . $data[$i]['id'] . "' data-item='16' data-object='" . $data[$i]['id_car'] . "'><span class='fa fa-check text-success'>&nbsp</span>Подтверждаю удаление</button></div></div>"
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
							. "<th " . $style_border . " scope='col'>Номер свидетельства</th>"
							. "<th " . $style_border . " scope='col'>Срок действия</th>"
							. "<th " . $style_border . " scope='col'>Кем выдано</th>"
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
		
		$number_dopog = $date_start_dopog = $date_end_dopog = $firma_dopog = $path_to_file = $file_extension = "";
		if($id != -1) {
			if(($data = $this->get(['id' => $id])) === false)
				return false;
			
			if(count($data) > 0) {
				$number_dopog = $data[0]['number_dopog'];
				$date_start_dopog = Functions::convertToDate($data[0]['date_start_dopog']);
				$date_end_dopog = Functions::convertToDate($data[0]['date_end_dopog']);
				$path_to_file = $data[0]['path_to_file'];
				$file_extension = $data[0]['file_extension'];
				$firma_dopog = $data[0]['firma_dopog'];
			}
		}

		$spr_firma_dopog = Directory::get_directory(34, $firma_dopog);

		$html = "<div class='col-12'>"
				. "<div id='formCarsDopog'>"
					. "<div class='form-row'>"
						. "<div class='col-4 mb-1 text-right' style='vertical-align: center;'>"
							. "<label for='number_dopog' class='text-muted' style='font-size: 13px;'><strong>Свидетельство</strong></label>"
						. "</div>"
						. "<div class='col-5 mb-1'>"
							. "<input type='text' class='form-control form-control-sm black-text' id='number_dopog' maxlength='20' placeholder='Номер' data-mandatory='true' data-message-error='Заполните обязательное поле: Номер удостоверения' data-datatype='char' value='" . $number_dopog . "'>"
						. "</div>"
					. "</div>"
				
					. "<div class='form-row'>"
						. "<div class='col-4 mb-1 text-right' style='vertical-align: center;'>"
							. "<label for='date_start_dopog' class='text-muted' style='font-size: 13px;'><strong>Дата выдачи</strong></label>"
						. "</div>"
						. "<div class='col-3 mb-1'>"
							. "<input type='text' class='form-control form-control-sm black-text' id='date_start_dopog' maxlength='10' placeholder='Дата' data-mandatory='true' data-message-error='Заполните обязательное поле: Дата выдачи свидетельства' data-datatype='date' value='" . $date_start_dopog . "'>"
						. "</div>"
					. "</div>"

					. "<div class='form-row'>"
						. "<div class='col-4 mb-1 text-right' style='vertical-align: center;'>"
							. "<label for='date_end_dopog' class='text-muted' style='font-size: 13px;'><strong>Дата окончания</strong></label>"
						. "</div>"
						. "<div class='col-3 mb-1'>"
							. "<input type='text' class='form-control form-control-sm black-text' id='date_end_dopog' maxlength='10' placeholder='Дата' data-mandatory='true' data-message-error='Заполните обязательное поле: Дата окончания свидетельства' data-datatype='date' value='" . $date_end_dopog . "'>"
						. "</div>"
					. "</div>"

					. "<div class='form-row'>"
						. "<div class='col-4 mb-1 text-right' style='vertical-align: center;'>"
							. "<label for='date_end_dopog' class='text-muted' style='font-size: 13px;'><strong>Кем выдано</strong></label>"
						. "</div>"
						. "<div class='col-5 mb-1'>"
							. "<select class='custom-select custom-select-sm black-text' id='firma_dopog' data-mandatory='true' data-message-error='Заполните обязательное поле: Организация' data-datatype='number'>" . $spr_firma_dopog . "</select>"
						. "</div>"
					. "</div>"

				. "</div>"
				
				. "<div class='form-row'>"
				
					. "<div class='col-4 mb-1 text-right' style='vertical-align: center;'>"
						. "<label for='comment_certificate_reg' class='text-muted' style='font-size: 13px;'><strong>Эл. образ</strong></label>"
					. "</div>"
					
					. "<div class='col-4 mb-1 text-left'>"
						. "<div id='uploadFileContainer'>" . Functions::rendering_icon_file($path_to_file, $file_extension, $id, 5, true) . "</div>"
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