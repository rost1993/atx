<?php

namespace IcKomiApp\models;

use IcKomiApp\core\User;
use IcKomiApp\core\Model;
use IcKomiApp\core\Logic;
use IcKomiApp\core\Functions;
use IcKomiApp\lib\Database\DB;
use IcKomiApp\widgets\Directory;

class TechnicalInspection extends Model {
	protected $table = 'technical_inspection';
	protected $trigger_operation = 2;
	protected $remove_directory = 1;
	protected $sql_get_record = "SELECT * FROM {table} WHERE id={id}";

	protected $sql_get_list = "SELECT a.id, a.number_certificate, a.date_certificate, a.end_date_certificate, a.address_technical_inspection, a.id_car, "
				. " a.ibd_arx, x1.text as firma_to, a.path_to_file, a.file_extension "
				. " FROM {table} a "
				. " LEFT JOIN s2i_klass x1 ON x1.kod=a.firma_technical_inspection AND x1.nomer=16 "
				. " WHERE a.id_car={id} ORDER BY a.date_certificate DESC";

	// Функция отрисовки списка ПТС
	function rendering_list($post) {
		if(empty($post['nsyst']))
			return false;

		if(($array_data = $this->get_list(addslashes($post['nsyst']))) === false)
			return false;

		$role = User::get('role');
		
		$html = "";
		if(count($array_data) == 0) {
			$html = "<div class='text-center'><p>Сведений в базе данных не обнаружено!</p></div>";
		} else {
			$style_border = "style='vertical-align: middle; border: 1px solid gray;'";

			// Формируем готовый HTML код для списка закрепленных ТС
			$html = "<table class='table table-striped table-sm table-hover text-center' style='font-size: 13px; margin: 10px;'>"
					. "<tr class='table-info'>"
						. "<th " . $style_border . " scope='col'>№ п/п</th>"
						. "<th " . $style_border . " scope='col'>Номер сертификата</th>"
						. "<th " . $style_border . " scope='col'>Срок действия</th>"
						. "<th " . $style_border . " scope='col'>Организация</th>"
						. "<th " . $style_border . " scope='col'>Адрес прохождения</th>"
						. "<th " . $style_border . " scope='col'>Эл. образ</th>"
						. "<th " . $style_border . " scope='col'>Изменить</th>"
						. "<th " . $style_border . " scope='col'>Удалить</th>"
					. "</tr>";
			
			$list_archive = "";
	
			for($i = 0, $j = 1, $k = 1; $i < count($array_data); $i++) {
				if($array_data[$i]['ibd_arx'] == 1) {
					$html .= "<tr>"
							. "<td " . $style_border . ">" . ($j++) . "</td>"
							. "<td " . $style_border . ">" . $array_data[$i]['number_certificate'] . "</td>"
							. "<td " . $style_border . ">" . Functions::convertToDate($array_data[$i]['date_certificate']) . " - " . Functions::convertToDate($array_data[$i]['end_date_certificate']) . "</td>"
							. "<td " . $style_border . ">" . $array_data[$i]['firma_to'] . "</td>"
							. "<td " . $style_border . ">" . $array_data[$i]['address_technical_inspection'] . "</td>"
							. "<td " . $style_border . ">" . Functions::rendering_icon_file($array_data[$i]['path_to_file'], $array_data[$i]['file_extension']) . "</td>";

					if($role > 2) {
						$html .= "<td " . $style_border . ">"
							. "<button type='button' class='btn btn-sm btn-info' id='btnEditItem' data-nsyst='" . $array_data[$i]['id'] . "' data-item='3' title='Скорректировать сведения о ТО'><span class='fa fa-pencil'></span>&nbspИзменить</button></td>"
							. "<td " . $style_border . "><div class='dropdown'>"
								. "<button type='button' class='btn btn-sm btn-danger dropdown-toggle' id='btnDropdownRemoveTO' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false' title='Удалить сведения о ТО'><span class='fa fa-close'></span>&nbspУдалить</button>"
								. "<div class='dropdown-menu' aria-labelledby='btnDropdownRemoveTO'>"
									. "<button type='button' class='dropdown-item' id='btnRemoveItem' data-nsyst='" . $array_data[$i]['id'] . "' data-item='3' data-object='" . $array_data[$i]['id_car'] . "'><span class='fa fa-check text-success'></span>&nbspПодтверждаю удаление</button></div></div>"
							. "</td>";
					} else {
						$html .= "<td " . $style_border . "></td><td " . $style_border . "></td>";
					}
					$html .= "</tr>";
				} else {
					$list_archive .= "<tr>"
							. "<td " . $style_border . ">" . ($k++) . "</td>"
							. "<td " . $style_border . ">" . $array_data[$i]['number_certificate'] . "</td>"
							. "<td " . $style_border . ">" . Functions::convertToDate($array_data[$i]['date_certificate']) . " - " . Functions::convertToDate($array_data[$i]['end_date_certificate']) . "</td>"
							. "<td " . $style_border . ">" . $array_data[$i]['firma_to'] . "</td>"
							. "<td " . $style_border . ">" . $array_data[$i]['address_technical_inspection'] . "</td>"
							. "<td " . $style_border . ">" . Functions::rendering_icon_file($array_data[$i]['path_to_file'], $array_data[$i]['file_extension']) . "</td>";

					if($role > 2) {
						$list_archive .= "<td " . $style_border . ">"
							. "<button type='button' class='btn btn-sm btn-info' id='btnEditItem' data-nsyst='" . $array_data[$i]['id'] . "' data-item='3' title='Скорректировать сведения о ТО'><span class='fa fa-pencil'></span>&nbspИзменить</button></td>"
							. "<td " . $style_border . "><div class='dropdown'>"
								. "<button type='button' class='btn btn-sm btn-danger dropdown-toggle' id='btnDropdownRemoveTO' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false' title='Удалить сведения о ТО'><span class='fa fa-close'></span>&nbspУдалить</button>"
								. "<div class='dropdown-menu' aria-labelledby='btnDropdownRemoveTO'>"
									. "<button type='button' class='dropdown-item' id='btnRemoveItem' data-nsyst='" . $array_data[$i]['id'] . "' data-item='3' data-object='" . $array_data[$i]['id_car'] . "'><span class='fa fa-check text-success'></span>&nbspПодтверждаю удаление</button></div></div>"
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
						. "<tr class='table-success'><th colspan='8' " . $style_border . ">АРХИВ</th></tr>"
						. "<tr class='table-info'>"
							. "<th " . $style_border . " scope='col'>№ п/п</th>"
							. "<th " . $style_border . " scope='col'>Номер сертификата</th>"
							. "<th " . $style_border . " scope='col'>Срок действия</th>"
							. "<th " . $style_border . " scope='col'>Организация</th>"
							. "<th " . $style_border . " scope='col'>Адрес прохождения</th>"
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
		
		$number = $firma = $address = $end_date = $date = $path_to_file = $file_extension = $car_mileage = "";
		if($id != -1) {
			if(($array_data = $this->get(['id' => $id])) === false)
				return false;
			
			if(count($array_data) > 0) {
				$number = $array_data[0]['number_certificate'];
				$firma = $array_data[0]['firma_technical_inspection'];
				$address = $array_data[0]['address_technical_inspection'];
				$end_date = Functions::convertToDate($array_data[0]['end_date_certificate']);
				$date = Functions::convertToDate($array_data[0]['date_certificate']);
				$path_to_file = $array_data[0]['path_to_file'];
				$file_extension = $array_data[0]['file_extension'];
				$car_mileage = $array_data[0]['car_mileage'];
			}
		}
		
		$spr_firma = Directory::get_directory(16, $firma);
		
		$html = "<div class='col-12'>"
				. "<div id='formTechnicalInspection'>"
					. "<div class='form-row'>"
						. "<div class='col-4 mb-1 text-right' style='vertical-align: center;'>"
							. "<label for='number_certificate' class='text-muted' style='font-size: 13px;'><strong>Номер сертификата</strong></label>"
						. "</div>"
						. "<div class='col-4 mb-1'>"
							. "<input type='text' class='form-control form-control-sm black-text' id='number_certificate' maxlength='20' placeholder='Номер' data-mandatory='true' data-message-error='Заполните обязательное поле: Номер сертификата' data-datatype='char' value='" . $number . "'>"
						. "</div>"
					. "</div>"
				
					. "<div class='form-row'>"
						. "<div class='col-4 mb-1 text-right' style='vertical-align: center;'>"
							. "<label for='date_certificate' class='text-muted' style='font-size: 13px;'><strong>Срок действия</strong></label>"
						. "</div>"
						. "<div class='col-2 mb-1'>"
							. "<div class='input-group input-group-sm'>"
								. "<div class='input-group-prepend'><label class='input-group-text' for='date_certificate'>с</label></div>"
								. "<input type='text' class='form-control form-control-sm black-text' id='date_certificate' maxlength='10' placeholder='Дата' data-mandatory='true' data-message-error='Заполните обязательное поле: Дата с' data-datatype='date' value='" . $date . "'>"
							. "</div>"
						. "</div>"
						. "<div class='col-2 mb-1'>"
							. "<div class='input-group input-group-sm'>"
								. "<div class='input-group-prepend'><label class='input-group-text' for='end_date_certificate'>по</label></div>"
								. "<input type='text' class='form-control form-control-sm black-text' id='end_date_certificate' maxlength='10' placeholder='Дата' data-mandatory='true' data-message-error='Заполните обязательное поле: Дата по' data-datatype='date' value='" . $end_date . "'>"
							. "</div>"
						. "</div>"
					. "</div>"
				
					. "<div class='form-row'>"
						. "<div class='col-4 mb-1 text-right' style='vertical-align: center;'>"
							. "<label for='firma_technical_inspection' class='text-muted' style='font-size: 13px;'><strong>Организация</strong></label>"
						. "</div>"
						. "<div class='col-6 mb-1'>"
							. "<select class='custom-select custom-select-sm black-text' id='firma_technical_inspection' data-mandatory='true' data-message-error='Заполните обязательное поле: Организация' data-datatype='number'>" . $spr_firma . "</select>"
						. "</div>"
					. "</div>"
				
					. "<div class='form-row'>"
						. "<div class='col-4 mb-1 text-right' style='vertical-align: center;'>"
							. "<label for='address_technical_inspection' class='text-muted' style='font-size: 13px;'><strong>Адрес прохождения</strong></label>"
						. "</div>"
						. "<div class='col-6 mb-1'>"
							. "<input type='text' class='form-control form-control-sm black-text' id='address_technical_inspection' maxlength='150' placeholder='Адрес прохождения' data-mandatory='true' data-message-error='Заполните обязательное поле: Адрес прохождения' data-datatype='char' value='" . $address . "'>"
						. "</div>"
					. "</div>"
					
					. "<div class='form-row'>"
						. "<div class='col-4 mb-1 text-right' style='vertical-align: center;'>"
							. "<label for='car_mileage' class='text-muted' style='font-size: 13px;'><strong>Пробег</strong></label>"
						. "</div>"
						. "<div class='col-4 mb-1'>"
							. "<input type='text' class='form-control form-control-sm black-text' id='car_mileage' maxlength='10' placeholder='Пробег, км' data-mandatory='true' data-message-error='Заполните обязательное поле: Пробег' data-datatype='number' value='" . $car_mileage . "'>"
						. "</div>"
					. "</div>"
					
				. "</div>"
				
				. "<div class='form-row'>"
				
					. "<div class='col-4 mb-1 text-right' style='vertical-align: center;'>"
						. "<label for='comment_certificate_reg' class='text-muted' style='font-size: 13px;'><strong>Эл. образ</strong></label>"
					. "</div>"
					
					. "<div class='col-4 mb-1 text-left'>"
						. "<div id='uploadFileContainer'>" . Functions::rendering_icon_file($path_to_file, $file_extension, $id, 3, true) . "</div>"
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