<?php

namespace IcKomiApp\models;

use IcKomiApp\core\User;
use IcKomiApp\core\Model;
use IcKomiApp\core\Logic;
use IcKomiApp\core\Functions;
use IcKomiApp\lib\Database\DB;
use IcKomiApp\widgets\Directory;

class CranVu extends Model {

	protected $table = 'drivers_document_cran';
	protected $trigger_operation = 0;
	protected $sql_get_record = "SELECT * FROM {table} WHERE id={id}";
	protected $remove_directory = 1;

	protected $sql_get_list = "SELECT a.id, a.id_driver, a.date_document, a.number_document, a.ibd_arx, x1.text as qualification_text, x2.text as education_text, a.path_to_file, a.file_extension FROM {table} a "
			. " LEFT JOIN s2i_klass x1 ON x1.kod=a.qualification AND x1.nomer=32 "
			. " LEFT JOIN s2i_klass x2 ON x2.kod=a.education_institute AND x2.nomer=33 "
			. " WHERE a.id_driver={id}"
			. " ORDER BY a.date_document DESC";

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
						. "<th " . $style_border . " scope='col'>Номер удостоверения</th>"
						. "<th " . $style_border . " scope='col'>Дата выдачи</th>"
						. "<th " . $style_border . " scope='col'>Учебное заведение</th>"
						. "<th " . $style_border . " scope='col'>Квалификация</th>"
						. "<th " . $style_border . " scope='col'>Эл. образ</th>"
						. "<th " . $style_border . " scope='col'>Изменить</th>"
						. "<th " . $style_border . " scope='col'>Удалить</th>"
					. "</tr>";
			
			$list_archive = "";
	
			for($i = 0, $j = 1, $k = 1; $i < count($data); $i++) {
				if($data[$i]['ibd_arx'] == 1) {
					$html .= "<tr>"
							. "<td " . $style_border . ">" . ($j++) . "</td>"
							. "<td " . $style_border . ">" . $data[$i]['number_document'] . "</td>"
							. "<td " . $style_border . ">" . Functions::convertToDate($data[$i]['date_document']) . "</td>"
							. "<td " . $style_border . ">" . $data[$i]['education_text'] . "</td>"
							. "<td " . $style_border . ">" . $data[$i]['qualification_text'] . "</td>"
							. "<td " . $style_border . ">" . Functions::rendering_icon_file($data[$i]['path_to_file'], $data[$i]['file_extension']) . "</td>";
							
							if($role >= 2) {
								$html .= "<td " . $style_border . ">"
								. "<button type='button' class='btn btn-sm btn-info' id='btnEditItem' data-nsyst='" . $data[$i]['id'] . "' data-item='14'><span class='fa fa-pencil'>&nbsp</span>Изменить</button></td>"
								. "<td " . $style_border . "><div class='dropdown'>"
								. "<button type='button' class='btn btn-sm btn-danger dropdown-toggle' id='btnDropdownRemove' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false' title='Удалить полис ОСАГО'><span class='fa fa-close'>&nbsp</span>Удалить</button>"
								. "<div class='dropdown-menu' aria-labelledby='btnDropdownRemove'>"
									. "<button type='button' class='dropdown-item' id='btnRemoveItem' data-nsyst='" . $data[$i]['id'] . "' data-item='14' data-object='" . $data[$i]['id_driver'] . "'><span class='fa fa-check text-success'>&nbsp</span>Подтверждаю удаление</button></div></div>"
							. "</td>";
							} else {
								$html .= "<td " . $style_border . "></td><td " . $style_border . "></td>";
							}

						$html .= "</tr>";
				} else {
					$list_archive .= "<tr>"
							. "<td " . $style_border . ">" . ($k++) . "</td>"
							. "<td " . $style_border . ">" . $data[$i]['number_document'] . "</td>"
							. "<td " . $style_border . ">" . Functions::convertToDate($data[$i]['date_document']) . "</td>"
							. "<td " . $style_border . ">" . $data[$i]['education_text'] . "</td>"
							. "<td " . $style_border . ">" . $data[$i]['qualification_text'] . "</td>"
							. "<td " . $style_border . ">" . Functions::rendering_icon_file($data[$i]['path_to_file'], $data[$i]['file_extension']) . "</td>";

							if($role >= 2) {
								$list_archive .= "<td " . $style_border . ">"
								. "<button type='button' class='btn btn-sm btn-info' id='btnEditItem' data-nsyst='" . $data[$i]['id'] . "' data-item='14'><span class='fa fa-pencil'>&nbsp</span>Изменить</button></td>"
								. "<td " . $style_border . "><div class='dropdown'>"
								. "<button type='button' class='btn btn-sm btn-danger dropdown-toggle' id='btnDropdownRemove' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false' title='Удалить полис ОСАГО'><span class='fa fa-close'>&nbsp</span>Удалить</button>"
								. "<div class='dropdown-menu' aria-labelledby='btnDropdownRemove'>"
									. "<button type='button' class='dropdown-item' id='btnRemoveItem' data-nsyst='" . $data[$i]['id'] . "' data-item='14' data-object='" . $data[$i]['id_driver'] . "'><span class='fa fa-check text-success'>&nbsp</span>Подтверждаю удаление</button></div></div>"
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
							. "<th " . $style_border . " scope='col'>Номер удостоверения</th>"
							. "<th " . $style_border . " scope='col'>Дата выдачи</th>"
							. "<th " . $style_border . " scope='col'>Учебное заведение</th>"
							. "<th " . $style_border . " scope='col'>Квалификация</th>"
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
		
		$number_document = $education_institute = $date_document = $qualification = $path_to_file = $file_extension = "";
		if($id != -1) {
			if(($data = $this->get(['id' => $id])) === false)
				return false;
			
			if(count($data) > 0) {
				$number_document = $data[0]['number_document'];
				$date_document = Functions::convertToDate($data[0]['date_document']);
				$education_institute = $data[0]['education_institute'];
				$qualification = $data[0]['qualification'];
				$path_to_file = $data[0]['path_to_file'];
				$file_extension = $data[0]['file_extension'];
			}
		}
		

		$array_directory = Directory::get_multiple_directory([32, 33], ['32' => $qualification, '33' => $education_institute]);
		$qualification = $array_directory[32];
		$education_institute = $array_directory[33];

		$html = "<div class='col-12'>"
				. "<div id='formCranVu'>"
					. "<div class='form-row'>"
						. "<div class='col-4 mb-1 text-right' style='vertical-align: center;'>"
							. "<label for='number_document' class='text-muted' style='font-size: 13px;'><strong>Номер удостоверения</strong></label>"
						. "</div>"
						. "<div class='col-5 mb-1'>"
							. "<input type='text' class='form-control form-control-sm black-text' id='number_document' maxlength='20' placeholder='Номер' data-mandatory='true' data-message-error='Заполните обязательное поле: Номер удостоверения' data-datatype='char' value='" . $number_document . "'>"
						. "</div>"
					. "</div>"
				
					. "<div class='form-row'>"
						. "<div class='col-4 mb-1 text-right' style='vertical-align: center;'>"
							. "<label for='date_document' class='text-muted' style='font-size: 13px;'><strong>Дата выдачи</strong></label>"
						. "</div>"
						. "<div class='col-3 mb-1'>"
							. "<input type='text' class='form-control form-control-sm black-text' id='date_document' maxlength='10' placeholder='Дата' data-mandatory='true' data-message-error='Заполните обязательное поле: Дата выдачи удостоверения' data-datatype='date' value='" . $date_document . "'>"
						. "</div>"
					. "</div>"
				
					. "<div class='form-row'>"
						. "<div class='col-4 mb-1 text-right' style='vertical-align: center;'>"
							. "<label for='education_institute' class='text-muted' style='font-size: 13px;'><strong>Учебное заведение</strong></label>"
						. "</div>"
						. "<div class='col-6 mb-1'>"
							. "<select class='custom-select custom-select-sm black-text' id='education_institute' data-mandatory='true' data-message-error='Заполните обязательное поле: Учебное заведение' data-datatype='number'>" . $education_institute . "</select>"
						. "</div>"
					. "</div>"
				
					. "<div class='form-row'>"
						. "<div class='col-4 mb-1 text-right' style='vertical-align: center;'>"
							. "<label for='qualification' class='text-muted' style='font-size: 13px;'><strong>Квалификация</strong></label>"
						. "</div>"
						. "<div class='col-6 mb-1'>"
							. "<select class='custom-select custom-select-sm black-text' id='qualification' data-mandatory='true' data-message-error='Заполните обязательное поле: Квалификация' data-datatype='number'>" . $qualification . "</select>"
						. "</div>"
					. "</div>"
				. "</div>"
				
				. "<div class='form-row'>"
				
					. "<div class='col-4 mb-1 text-right' style='vertical-align: center;'>"
						. "<label for='comment_certificate_reg' class='text-muted' style='font-size: 13px;'><strong>Эл. образ</strong></label>"
					. "</div>"
					
					. "<div class='col-4 mb-1 text-left'>"
						. "<div id='uploadFileContainer'>" . Functions::rendering_icon_file($path_to_file, $file_extension, $id, 14, true) . "</div>"
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