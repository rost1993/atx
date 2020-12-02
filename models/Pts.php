<?php

namespace IcKomiApp\models;

use IcKomiApp\core\Model;
use IcKomiApp\core\Logic;
use IcKomiApp\core\Functions;
use IcKomiApp\lib\Database\DB;
use IcKomiApp\widgets\Directory;

class Pts extends Model {

	protected $table = 'pts';
	protected $trigger_operation = 5;
	protected $sql_get_record = "SELECT * FROM {table} WHERE id={id}";
	protected $remove_directory = 1;

	protected $sql_get_list = "SELECT a.id, a.id_car, a.s_pts, a.n_pts, a.date_pts, a.ibd_arx, x1.text as text_type, x2.text as text_firma, a.path_to_file, a.file_extension FROM {table} a "
			. " LEFT JOIN s2i_klass x1 ON x1.kod=a.type_ts_pts AND x1.nomer=6 "
			. " LEFT JOIN s2i_klass x2 ON x2.kod=a.firma_pts AND x2.nomer=10 "
			. " WHERE a.id_car={id}"
			. " ORDER BY a.date_pts DESC";

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
						. "<th " . $style_border . " scope='col'>Серия и номер</th>"
						. "<th " . $style_border . " scope='col'>Дата выдачи</th>"
						. "<th " . $style_border . " scope='col'>Тип ТС по ПТС</th>"
						. "<th " . $style_border . " scope='col'>Орган, выдавший ПТС</th>"
						. "<th " . $style_border . " scope='col'>Эл. образ</th>"
						. "<th " . $style_border . " scope='col'>Изменить</th>"
						. "<th " . $style_border . " scope='col'>Удалить</th>"
					. "</tr>";
			
			$list_archive = "";
	
			for($i = 0, $j = 1, $k = 1; $i < count($data); $i++) {
				if($data[$i]['ibd_arx'] == 1) {
					$html .= "<tr>"
							. "<td " . $style_border . ">" . ($j++) . "</td>"
							. "<td " . $style_border . ">" . $data[$i]['s_pts'] . " " . $data[$i]['n_pts'] . "</td>"
							. "<td " . $style_border . ">" . Functions::convertToDate($data[$i]['date_pts']) . "</td>"
							. "<td " . $style_border . ">" . $data[$i]['text_type'] . "</td>"
							. "<td " . $style_border . ">" . $data[$i]['text_firma'] . "</td>"
							. "<td " . $style_border . ">" . Functions::rendering_icon_file($data[$i]['path_to_file'], $data[$i]['file_extension']) . "</td>";
							
							if(($role > 1) && ($role != 4)) {
								$html .= "<td " . $style_border . ">"
								. "<button type='button' class='btn btn-sm btn-info' id='btnEditItem' data-nsyst='" . $data[$i]['id'] . "' data-item='5'><span class='fa fa-pencil'>&nbsp</span>Изменить</button></td>"
								. "<td " . $style_border . "><div class='dropdown'>"
								. "<button type='button' class='btn btn-sm btn-danger dropdown-toggle' id='btnDropdownRemove' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false' title='Удалить полис ОСАГО'><span class='fa fa-close'>&nbsp</span>Удалить</button>"
								. "<div class='dropdown-menu' aria-labelledby='btnDropdownRemove'>"
									. "<button type='button' class='dropdown-item' id='btnRemoveItem' data-nsyst='" . $data[$i]['id'] . "' data-item='5' data-object='" . $data[$i]['id_car'] . "'><span class='fa fa-check text-success'>&nbsp</span>Подтверждаю удаление</button></div></div>"
							. "</td>";
							} else {
								$html .= "<td " . $style_border . "></td><td " . $style_border . "></td>";
							}

						$html .= "</tr>";
				} else {
					$list_archive .= "<tr>"
							. "<td " . $style_border . ">" . ($k++) . "</td>"
							. "<td " . $style_border . ">" . $data[$i]['s_pts'] . " " . $data[$i]['n_pts'] . "</td>"
							. "<td " . $style_border . ">" . Functions::convertToDate($data[$i]['date_pts']) . "</td>"
							. "<td " . $style_border . ">" . $data[$i]['text_type'] . "</td>"
							. "<td " . $style_border . ">" . $data[$i]['text_firma'] . "</td>"
							. "<td " . $style_border . ">" . Functions::rendering_icon_file($data[$i]['path_to_file'], $data[$i]['file_extension']) . "</td>";

							if(($role > 1) && ($role != 4)) {
								$list_archive .= "<td " . $style_border . ">"
								. "<button type='button' class='btn btn-sm btn-info' id='btnEditItem' data-nsyst='" . $data[$i]['id'] . "' data-item='5'><span class='fa fa-pencil'>&nbsp</span>Изменить</button></td>"
								. "<td " . $style_border . "><div class='dropdown'>"
								. "<button type='button' class='btn btn-sm btn-danger dropdown-toggle' id='btnDropdownRemove' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false' title='Удалить полис ОСАГО'><span class='fa fa-close'>&nbsp</span>Удалить</button>"
								. "<div class='dropdown-menu' aria-labelledby='btnDropdownRemove'>"
									. "<button type='button' class='dropdown-item' id='btnRemoveItem' data-nsyst='" . $data[$i]['id'] . "' data-item='5' data-object='" . $data[$i]['id_car'] . "'><span class='fa fa-check text-success'>&nbsp</span>Подтверждаю удаление</button></div></div>"
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
							. "<th " . $style_border . " scope='col'>Серия и номер</th>"
							. "<th " . $style_border . " scope='col'>Дата выдачи</th>"
							. "<th " . $style_border . " scope='col'>Тип ТС по ПТС</th>"
							. "<th " . $style_border . " scope='col'>Орган, выдавший ПТС</th>"
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
		
		$s_pts = $n_pts = $date_pts = $type_ts_pts = $firma_pts = $path_to_file = $file_extension = "";
		if($id != -1) {
			if(($data = $this->get(['id' => $id])) === false)
				return false;
			
			if(count($data) > 0) {
				$s_pts = $data[0]['s_pts'];
				$n_pts = $data[0]['n_pts'];
				$date_pts = Functions::convertToDate($data[0]['date_pts']);
				$type_ts_pts = $data[0]['type_ts_pts'];
				$firma_pts = $data[0]['firma_pts'];
				$path_to_file = $data[0]['path_to_file'];
				$file_extension = $data[0]['file_extension'];
			}
		}
		

		$array_directory = Directory::get_multiple_directory([10, 6], ['10' => $firma_pts, '6' => $type_ts_pts]);
		$spr_firma_pts = $array_directory[10];
		$spr_type_ts_pts = $array_directory[6];

		$html = "<div class='col-12'>"
				. "<div id='formPts'>"
					. "<div class='form-row'>"
						. "<div class='col-4 mb-1 text-right' style='vertical-align: center;'>"
							. "<label for='s_pts' class='text-muted' style='font-size: 13px;'><strong>ПТС</strong></label>"
						. "</div>"
						. "<div class='col-2 mb-1'>"
							. "<input type='text' class='form-control form-control-sm black-text' id='s_pts' maxlength='20' placeholder='Серия' data-mandatory='true' data-message-error='Заполните обязательное поле: Серия ПТС' data-datatype='char' value='" . $s_pts . "'>"
						. "</div>"	
						. "<div class='col-3 mb-1'>"
							. "<input type='text' class='form-control form-control-sm black-text' id='n_pts' maxlength='20' placeholder='Номер' data-mandatory='true' data-message-error='Заполните обязательное поле: Номер ПТС' data-datatype='char' value='" . $n_pts . "'>"
						. "</div>"
					. "</div>"
				
					. "<div class='form-row'>"
						. "<div class='col-4 mb-1 text-right' style='vertical-align: center;'>"
							. "<label for='date_pts' class='text-muted' style='font-size: 13px;'><strong>Дата выдачи ПТС</strong></label>"
						. "</div>"
						. "<div class='col-2 mb-1'>"
							. "<input type='text' class='form-control form-control-sm black-text' id='date_pts' maxlength='10' placeholder='Дата' data-mandatory='true' data-message-error='Заполните обязательное поле: Дата выдачи ПТС' data-datatype='date' value='" . $date_pts . "'>"
						. "</div>"
					. "</div>"
				
					. "<div class='form-row'>"
						. "<div class='col-4 mb-1 text-right' style='vertical-align: center;'>"
							. "<label for='type_ts_pts' class='text-muted' style='font-size: 13px;'><strong>Тип ТС по ПТС</strong></label>"
						. "</div>"
						. "<div class='col-6 mb-1'>"
							. "<select class='custom-select custom-select-sm black-text' id='type_ts_pts' data-mandatory='true' data-message-error='Заполните обязательное поле: Тип ТС по ПТС' data-datatype='number'>" . $spr_type_ts_pts . "</select>"
						. "</div>"
					. "</div>"
				
					. "<div class='form-row'>"
						. "<div class='col-4 mb-1 text-right' style='vertical-align: center;'>"
							. "<label for='firma_pts' class='text-muted' style='font-size: 13px;'><strong>Орган, выдавший ПТС</strong></label>"
						. "</div>"
						. "<div class='col-6 mb-1'>"
							. "<select class='custom-select custom-select-sm black-text' id='firma_pts' data-mandatory='true' data-message-error='Заполните обязательное поле: Орган, выдавший ПТС' data-datatype='number'>" . $spr_firma_pts . "</select>"
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