<?php

namespace IcKomiApp\models;

use IcKomiApp\core\User;
use IcKomiApp\core\Model;
use IcKomiApp\core\Logic;
use IcKomiApp\core\Functions;
use IcKomiApp\lib\Database\DB;
use IcKomiApp\widgets\Directory;

class Osago extends Model {
	protected $table = 'osago';
	protected $trigger_operation = 1;
	protected $remove_directory = 1;

	protected $sql_get_record = "SELECT * FROM {table} WHERE id={id}";

	protected $sql_get_list = "SELECT a.id, a.n_osago, a.start_date_osago, a.end_date_osago,a.id_car, "
				. " a.ibd_arx, x1.text as firma_osago, a.path_to_file, a.file_extension "
				. " FROM {table} a "
				. " LEFT JOIN s2i_klass x1 ON x1.kod=a.firma_osago AND x1.nomer=15 "
				. " WHERE a.id_car={id} ORDER BY a.end_date_osago DESC";

	// Функция отрисовки списка ПТС
	function rendering_list($post) {
		if(empty($post['nsyst']))
			return false;

		if(($array_data = $this->get_list(addslashes($post['nsyst']))) === false)
			return false;

		$role = User::get('role');

		$html = "";
		if(count($array_data) == 0) {
			$html = "<div class='text-center'><p>Сведений в базе данных не найдено!</p></div>";
		} else {
			$style_border = "style='vertical-align: middle; border: 1px solid gray;'";

			// Формируем готовый HTML код для списка закрепленных ТС
			$html = "<table class='table table-striped table-sm table-hover text-center' style='font-size: 13px; margin: 10px;'>"
					. "<tr class='table-info'>"
						. "<th " . $style_border . " scope='col'>№ п/п</th>"
						. "<th " . $style_border . " scope='col'>Серия и номер</th>"
						. "<th " . $style_border . " scope='col'>Страховая компания</th>"
						. "<th " . $style_border . " scope='col'>Дата окончания полиса</th>"
						. "<th " . $style_border . " scope='col'>Эл. образ</th>"
						. "<th " . $style_border . " scope='col'>Изменить</th>"
						. "<th " . $style_border . " scope='col'>Удалить</th>"
					. "</tr>";
			
			$list_archive = "";
	
			for($i = 0, $j = 1, $k = 1; $i < count($array_data); $i++) {
				if($array_data[$i]['ibd_arx'] == 1) {
					$html .= "<tr>"
							. "<td " . $style_border . ">" . ($j++) . "</td>"
							. "<td " . $style_border . ">" . $array_data[$i]['n_osago'] . "</td>"
							. "<td " . $style_border . ">" . $array_data[$i]['firma_osago'] . "</td>"
							. "<td " . $style_border . ">" . Functions::convertToDate($array_data[$i]['end_date_osago']) . "</td>"
							. "<td " . $style_border . ">" . Functions::rendering_icon_file($array_data[$i]['path_to_file'], $array_data[$i]['file_extension']) . "</td>";

					if($role >= 2) {
						$html .= "<td " . $style_border . ">"
								. "<button type='button' class='btn btn-sm btn-info' id='btnEditItem' data-nsyst='" . $array_data[$i]['id'] . "' data-item='2'><span class='fa fa-pencil'>&nbsp</span>Изменить</button></td>"
								. "<td " . $style_border . "><div class='dropdown'>"
								. "<button type='button' class='btn btn-sm btn-danger dropdown-toggle' id='btnDropdownRemoveOsago' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false' title='Удалить полис ОСАГО'><span class='fa fa-close'>&nbsp</span>Удалить</button>"
								. "<div class='dropdown-menu' aria-labelledby='btnDropdownRemoveOsago'>"
									. "<button type='button' class='dropdown-item' id='btnRemoveItem' data-nsyst='" . $array_data[$i]['id'] . "' data-item='2' data-object='" . $array_data[$i]['id_car'] . "'><span class='fa fa-check text-success'>&nbsp</span>Подтверждаю удаление</button></div></div>"
								. "</td>";
							} else {
								$html .= "<td " . $style_border . "></td><td " . $style_border . "></td>";
							}

					$html .= "</tr>";
				} else {
					$list_archive .= "<tr>"
							. "<td " . $style_border . ">" . ($k++) . "</td>"
							. "<td " . $style_border . ">" . $array_data[$i]['n_osago'] . "</td>"
							. "<td " . $style_border . ">" . $array_data[$i]['firma_osago'] . "</td>"
							. "<td " . $style_border . ">" . Functions::convertToDate($array_data[$i]['end_date_osago']) . "</td>"
							. "<td " . $style_border . ">" . Functions::rendering_icon_file($array_data[$i]['path_to_file'], $array_data[$i]['file_extension']) . "</td>";

					if($role >= 2) {
						$list_archive .= "<td " . $style_border . ">"
								. "<button type='button' class='btn btn-sm btn-info' id='btnEditItem' data-nsyst='" . $array_data[$i]['id'] . "' data-item='2'><span class='fa fa-pencil'>&nbsp</span>Изменить</button></td>"
								. "<td " . $style_border . "><div class='dropdown'>"
								. "<button type='button' class='btn btn-sm btn-danger dropdown-toggle' id='btnDropdownRemoveOsago' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false' title='Удалить полис ОСАГО'><span class='fa fa-close'>&nbsp</span>Удалить</button>"
								. "<div class='dropdown-menu' aria-labelledby='btnDropdownRemoveOsago'>"
									. "<button type='button' class='dropdown-item' id='btnRemoveItem' data-nsyst='" . $array_data[$i]['id'] . "' data-item='2' data-object='" . $array_data[$i]['id_car'] . "'><span class='fa fa-check text-success'>&nbsp</span>Подтверждаю удаление</button></div></div>"
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
							. "<th " . $style_border . " scope='col'>Серия и номер</th>"
							. "<th " . $style_border . " scope='col'>Страховая компания</th>"
							. "<th " . $style_border . " scope='col'>Дата окончания полиса</th>"
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

		$n_osago = $end_date_osago = $firma_osago = $path_to_file = $file_extension = "";
		if($id != -1) {
			if(($array_data = $this->get(['id' => $id])) === false)
				return false;
			
			if(count($array_data) > 0) {
				$n_osago = $array_data[0]['n_osago'];
				$end_date_osago = Functions::convertToDate($array_data[0]['end_date_osago']);
				$firma_osago = $array_data[0]['firma_osago'];
				$path_to_file = $array_data[0]['path_to_file'];
				$file_extension = $array_data[0]['file_extension'];
			}
		}

		$spr_firma_osago = Directory::get_directory(15, $firma_osago);
		
		$html = "<div class='col-12'>"
				. "<div id='formOsago'>"
					. "<div class='form-row'>"
						. "<div class='col-4 mb-1 text-right' style='vertical-align: center;'>"
							. "<label for='s_osago' class='text-muted' style='font-size: 13px;'><strong>Полис ОСАГО</strong></label>"
						. "</div>"
						. "<div class='col-5 mb-1'>"
							. "<input type='text' class='form-control form-control-sm black-text' id='n_osago' maxlength='50' placeholder='Номер' data-mandatory='true' data-message-error='Заполните обязательное поле: Серия и номер ОСАГО' data-datatype='char' value='" . $n_osago . "'>"
						. "</div>"
					. "</div>"
				
					. "<div class='form-row'>"
						. "<div class='col-4 mb-1 text-right' style='vertical-align: center;'>"
							. "<label for='start_date_osago' class='text-muted' style='font-size: 13px;'><strong>Дата окончания полиса</strong></label>"
						. "</div>"
						. "<div class='col-4 mb-1'>"
							. "<div class='input-group input-group-sm'>"
								. "<div class='input-group-prepend'><label class='input-group-text' for='end_date_osago'>по</label></div>"
								. "<input type='text' class='form-control form-control-sm black-text' id='end_date_osago' maxlength='10' placeholder='Дата' data-mandatory='true' data-message-error='Заполните обязательное поле: Дата по' data-datatype='date' value='" . $end_date_osago . "'>"
							. "</div>"
						. "</div>"
					. "</div>"
				
					. "<div class='form-row'>"
						. "<div class='col-4 mb-1 text-right' style='vertical-align: center;'>"
							. "<label for='firma_osago' class='text-muted' style='font-size: 13px;'><strong>Страховая компания</strong></label>"
						. "</div>"
						. "<div class='col-6 mb-1'>"
							. "<select class='custom-select custom-select-sm black-text' id='firma_osago' data-mandatory='true' data-message-error='Заполните обязательное поле: Страховая компания' data-datatype='number'>" . $spr_firma_osago . "</select>"
						. "</div>"
					. "</div>"
				. "</div>"
				
				. "<div class='form-row'>"
				
					. "<div class='col-4 mb-1 text-right' style='vertical-align: center;'>"
						. "<label for='comment_certificate_reg' class='text-muted' style='font-size: 13px;'><strong>Эл. образ</strong></label>"
					. "</div>"
					
					. "<div class='col-4 mb-1 text-left'>"
						. "<div id='uploadFileContainer'>" . Functions::rendering_icon_file($path_to_file, $file_extension, $id, 2, true) . "</div>"
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