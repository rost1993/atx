<?php

namespace IcKomiApp\models;

use IcKomiApp\core\Model;
use IcKomiApp\core\Logic;
use IcKomiApp\core\Functions;
use IcKomiApp\lib\Database\DB;
use IcKomiApp\widgets\Directory;

class Wheel extends Model {
	protected $table = 'cars_wheels';
	protected $trigger_operation = 0;
	protected $remove_directory = 0;
	protected $sql_get_record = "SELECT * FROM {table} WHERE id={id}";

	protected $sql_get_list = "SELECT a.*, "
				. "  x1.text as season_wheel_text, x2.text as type_wheel_text, x3.text as marka_wheel_text, x4.text as model_wheel_text, x5.text as size_wheel_text"
				. " FROM {table} a "
				. " LEFT JOIN s2i_klass x1 ON x1.kod=a.season_wheel AND x1.nomer=27 "
				. " LEFT JOIN s2i_klass x2 ON x2.kod=a.type_wheel AND x2.nomer=28 "
				. " LEFT JOIN s2i_klass x3 ON x3.kod=a.marka_wheel AND x3.nomer=29 "
				. " LEFT JOIN s2i_klass x4 ON x4.kod=a.model_wheel AND x4.nomer=30 "
				. " LEFT JOIN s2i_klass x5 ON x5.kod=a.size_wheel AND x5.nomer=31 "
				. " WHERE a.id_car={id} ORDER BY a.date_installation DESC";

	// Функция отрисовки списка ПТС
	function rendering_list($post) {
		if(empty($post['nsyst']))
			return false;
		
		$wheel = new Wheel();
		if(($array_data = $wheel->get_list(addslashes($post['nsyst']))) === false)
			return false;

		/*Session::start();
		$role = Session::get('role');
		Session::commit();*/
		$role = 9;
		
		$html = "";
		if(count($array_data) == 0) {
			$html = "<div class='text-center'><p>Сведений в базе данных не найдено!</p></div>";
		} else {
			$style_border = "style='vertical-align: middle; border: 1px solid gray;'";

			// Формируем готовый HTML код для списка закрепленных ТС
			$html = "<table class='table table-striped table-sm table-hover text-center' style='font-size: 13px; margin: 10px;'>"
					. "<tr class='table-info'>"
						. "<th " . $style_border . " scope='col'>№ п/п</th>"
						. "<th " . $style_border . " scope='col'>Тип</th>"
						. "<th " . $style_border . " scope='col'>Марка/модель</th>"
						. "<th " . $style_border . " scope='col'>Сезон</th>"
						. "<th " . $style_border . " scope='col'>Размер</th>"
						. "<th " . $style_border . " scope='col'>Дата<br>установки</th>"
						. "<th " . $style_border . " scope='col'>Примечание</th>"
						. "<th " . $style_border . " scope='col'>Изменить</th>"
						. "<th " . $style_border . " scope='col'>Архив</th>"
						. "<th " . $style_border . " scope='col'>Удалить</th>"
					. "</tr>";
			
			$list_archive = "";
	
			for($i = 0, $j = 1, $k = 1; $i < count($array_data); $i++) {
				if($array_data[$i]['ibd_arx'] == 1) {
					$html .= "<tr>"
							. "<td " . $style_border . ">" . ($j++) . "</td>"
							. "<td " . $style_border . ">" . $array_data[$i]['type_wheel_text'] . "</td>"
							. "<td " . $style_border . ">" . $array_data[$i]['marka_wheel_text'] . '&nbsp;' . $array_data[$i]['model_wheel_text'] . "</td>"
							. "<td " . $style_border . ">" . $array_data[$i]['season_wheel_text'] . "</td>"
							. "<td " . $style_border . ">" . $array_data[$i]['size_wheel_text'] . "</td>"
							. "<td " . $style_border . ">" . Functions::convertToDate($array_data[$i]['date_installation']) . "</td>"
							. "<td " . $style_border . ">" . $array_data[$i]['comment_wheel'] . "</td>";

					if(($role > 1) && ($role != 4)) {
						$html .= "<td " . $style_border . ">"
							. "<button type='button' class='btn btn-sm btn-info' id='btnEditItem' data-nsyst='" . $array_data[$i]['id'] . "' data-item='13'><span class='fa fa-pencil'>&nbsp;</span>Изменить</button></td>"
							. "<td " . $style_border . ">"
							. "<button type='button' class='btn btn-sm btn-primary' id='btnMoveArchiveItem' data-nsyst='" . $array_data[$i]['id'] . "' data-item='13' data-archive='1'><span class='fa fa-folder'>&nbsp;</span>В архив</button></td>"
							. "<td " . $style_border . "><div class='dropdown'>"
								. "<button type='button' class='btn btn-sm btn-danger dropdown-toggle' id='btnDropdownRemoveWheel' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false' title='Удалить полис ОСАГО'><span class='fa fa-close'>&nbsp;</span>Удалить</button>"
								. "<div class='dropdown-menu' aria-labelledby='btnDropdownRemoveWheel'>"
									. "<button type='button' class='dropdown-item' id='btnRemoveItem' data-nsyst='" . $array_data[$i]['id'] . "' data-item='13'><span class='fa fa-check text-success'>&nbsp;</span>Подтверждаю удаление</button></div></div>"
							. "</td>";
					} else {
						$html .= "<td " . $style_border . "></td><td " . $style_border . "></td><td " . $style_border . "></td>";
					}
							
					$html .= "</tr>";
				} else {
					$list_archive .= "<tr>"
							. "<td " . $style_border . ">" . ($j++) . "</td>"
							. "<td " . $style_border . ">" . $array_data[$i]['type_wheel_text'] . "</td>"
							. "<td " . $style_border . ">" . $array_data[$i]['marka_wheel_text'] . '&nbsp;' . $array_data[$i]['model_wheel_text'] . "</td>"
							. "<td " . $style_border . ">" . $array_data[$i]['season_wheel_text'] . "</td>"
							. "<td " . $style_border . ">" . $array_data[$i]['size_wheel_text'] . "</td>"
							. "<td " . $style_border . ">" . Functions::convertToDate($array_data[$i]['date_installation']) . "</td>"
							. "<td " . $style_border . ">" . $array_data[$i]['comment_wheel'] . "</td>";

					if(($role > 1) && ($role != 4)) {
						$list_archive .= "<td " . $style_border . ">"
							. "<button type='button' class='btn btn-sm btn-info' id='btnEditItem' data-nsyst='" . $array_data[$i]['id'] . "' data-item='13'><span class='fa fa-pencil'>&nbsp;</span>Изменить</button></td>"
							. "<td " . $style_border . ">"
							. "<button type='button' class='btn btn-sm btn-primary' id='btnMoveArchiveItem' data-nsyst='" . $array_data[$i]['id'] . "' data-item='13' data-archive='2'><span class='fa fa-folder'>&nbsp;</span>Из архива</button></td>"
							. "<td " . $style_border . "><div class='dropdown'>"
								. "<button type='button' class='btn btn-sm btn-danger dropdown-toggle' id='btnDropdownRemoveWheel' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false' title='Удалить полис ОСАГО'><span class='fa fa-close'>&nbsp;</span>Удалить</button>"
								. "<div class='dropdown-menu' aria-labelledby='btnDropdownRemoveWheel'>"
									. "<button type='button' class='dropdown-item' id='btnRemoveItem' data-nsyst='" . $array_data[$i]['id'] . "' data-item='13'><span class='fa fa-check text-success'>&nbsp;</span>Подтверждаю удаление</button></div></div>"
							. "</td>";
					} else {
						$list_archive .= "<td " . $style_border . "></td><td " . $style_border . "></td><td " . $style_border . "></td>";
					}
							
					$list_archive .= "</tr>";
				}
			}
			
			$html .= "</table>";
			
			if(mb_strlen($list_archive) > 0) {
				// Формируем готовый HTML код для списка закрепленных ТС
				$html .= "<table class='table table-striped table-sm table-hover text-center' style='font-size: 13px; margin: 10px;'>"
						. "<tr class='table-success'><th colspan='10' " . $style_border . ">АРХИВ</th></tr>"
						. "<tr class='table-info'>"
							. "<th " . $style_border . " scope='col'>№ п/п</th>"
							. "<th " . $style_border . " scope='col'>Тип</th>"
							. "<th " . $style_border . " scope='col'>Марка/модель</th>"
							. "<th " . $style_border . " scope='col'>Сезон</th>"
							. "<th " . $style_border . " scope='col'>Размер</th>"
							. "<th " . $style_border . " scope='col'>Дата<br>установки</th>"
							. "<th " . $style_border . " scope='col'>Примечание</th>"
							. "<th " . $style_border . " scope='col'>Изменить</th>"
							. "<th " . $style_border . " scope='col'>Архив</th>"
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
		$season_wheel = $type_wheel = $marka_wheel = $model_wheel = $size_wheel = $date_installation = $comment_wheel = '';
		if($id != -1) {
			if(($array_data = $this->get(['id' => $id])) === false)
				return false;
			
			if(count($array_data) > 0) {
				$type_wheel = $array_data[0]['type_wheel'];
				$season_wheel = $array_data[0]['season_wheel'];
				$size_wheel = $array_data[0]['size_wheel'];
				$marka_wheel = $array_data[0]['marka_wheel'];
				$model_wheel = $array_data[0]['model_wheel'];
				$date_installation = Functions::convertToDate($array_data[0]['date_installation']);
				$comment_wheel = $array_data[0]['comment_wheel'];
			}
		}


		
		$array_directory = Directory::get_multiple_directory([27, 28, 29, 30, 31], ['27' => $season_wheel, '28' => $type_wheel, '29' => $marka_wheel, '30' => $model_wheel, '31' => $size_wheel]);
		$select_season = $array_directory[27];
		$select_type = $array_directory[28];
		$select_marka = $array_directory[29];
		$select_model = $array_directory[30];
		$select_size = $array_directory[31];

		/*$spr = new Spr();
		if(($data = $spr->get_multiple_spr([27, 28, 29, 30, 31])) === false)
			return false;

		for($i = 0; $i < count($data); $i++) {
			if($data[$i]['nomer'] == 27) {
				if($data[$i]['kod'] == $season_wheel)
					$select_season .= "<option value='" . $data[$i]['kod'] . "' selected>" . $data[$i]['text'] . "</option>";
				else
					$select_season .= "<option value='" . $data[$i]['kod'] . "'>" . $data[$i]['text'] . "</option>";
			} else if($data[$i]['nomer'] == 28) {
				if($data[$i]['kod'] == $type_wheel)
					$select_type .= "<option value='" . $data[$i]['kod'] . "' selected>" . $data[$i]['text'] . "</option>";
				else
					$select_type .= "<option value='" . $data[$i]['kod'] . "'>" . $data[$i]['text'] . "</option>";
			} else if($data[$i]['nomer'] == 29) {
				if($data[$i]['kod'] == $marka_wheel)
					$select_marka .= "<option value='" . $data[$i]['kod'] . "' selected>" . $data[$i]['text'] . "</option>";
				else
					$select_marka .= "<option value='" . $data[$i]['kod'] . "'>" . $data[$i]['text'] . "</option>";
			} else if($data[$i]['nomer'] == 30) {
				if($data[$i]['kod'] == $model_wheel)
					$select_model .= "<option value='" . $data[$i]['kod'] . "' selected>" . $data[$i]['text'] . "</option>";
				else
					$select_model .= "<option value='" . $data[$i]['kod'] . "'>" . $data[$i]['text'] . "</option>";
			} else {
				if($data[$i]['kod'] == $size_wheel)
					$select_size .= "<option value='" . $data[$i]['kod'] . "' selected>" . $data[$i]['text'] . "</option>";
				else
					$select_size .= "<option value='" . $data[$i]['kod'] . "'>" . $data[$i]['text'] . "</option>";
			}
		}*/
		
		$html = "<div class='col-12'>"
				. "<div id='formWheel'>"
					
					. "<div class='form-row'>"
						. "<div class='col-4 mb-1 text-right' style='vertical-align: center;'>"
							. "<label for='size_wheel' class='text-muted' style='font-size: 13px;'><strong>Размерность</strong></label>"
						. "</div>"
						. "<div class='col-6 mb-1'>"
							. "<select class='custom-select custom-select-sm black-text' id='size_wheel' data-mandatory='true' data-message-error='Заполните обязательное поле: Размерность' data-datatype='number'>"
							. $select_size
							. "</select>"
						. "</div>"
					. "</div>"

					. "<div class='form-row'>"
						. "<div class='col-4 mb-1 text-right' style='vertical-align: center;'>"
							. "<label for='season_wheel' class='text-muted' style='font-size: 13px;'><strong>Сезон</strong></label>"
						. "</div>"
						. "<div class='col-6 mb-1'>"
							. "<select class='custom-select custom-select-sm black-text' id='season_wheel' data-mandatory='true' data-message-error='Заполните обязательное поле: Сезон' data-datatype='number'>"
							. $select_season
							. "</select>"
						. "</div>"
					. "</div>"

					. "<div class='form-row'>"
						. "<div class='col-4 mb-1 text-right' style='vertical-align: center;'>"
							. "<label for='type_wheel' class='text-muted' style='font-size: 13px;'><strong>Тип</strong></label>"
						. "</div>"
						. "<div class='col-6 mb-1'>"
							. "<select class='custom-select custom-select-sm black-text' id='type_wheel' data-mandatory='true' data-message-error='Заполните обязательное поле: Тип' data-datatype='number'>"
							. $select_type
							. "</select>"
						. "</div>"
					. "</div>"

					. "<div class='form-row'>"
						. "<div class='col-4 mb-1 text-right' style='vertical-align: center;'>"
							. "<label for='marka_wheel' class='text-muted' style='font-size: 13px;'><strong>Марка</strong></label>"
						. "</div>"
						. "<div class='col-6 mb-1'>"
							. "<select class='custom-select custom-select-sm black-text' id='marka_wheel' data-mandatory='true' data-message-error='Заполните обязательное поле: Марка' data-datatype='number'>"
							. $select_marka
							. "</select>"
						. "</div>"
					. "</div>"

					. "<div class='form-row'>"
						. "<div class='col-4 mb-1 text-right' style='vertical-align: center;'>"
							. "<label for='model_wheel' class='text-muted' style='font-size: 13px;'><strong>Модель</strong></label>"
						. "</div>"
						. "<div class='col-6 mb-1'>"
							. "<select class='custom-select custom-select-sm black-text' id='model_wheel' data-mandatory='true' data-message-error='Заполните обязательное поле: Модель' data-datatype='number'>"
							. $select_model
							. "</select>"
						. "</div>"
					. "</div>"

					. "<div class='form-row'>"
						. "<div class='col-4 mb-1 text-right' style='vertical-align: center;'>"
							. "<label for='date_installation' class='text-muted' style='font-size: 13px;'><strong>Дата установки</strong></label>"
						. "</div>"
						. "<div class='col-4 mb-1'>"
							. "<input type='text' class='form-control form-control-sm black-text' id='date_installation' maxlength='10' placeholder='Дата установки' data-mandatory='true' data-message-error='Заполните обязательное поле: Дата установки' data-datatype='date' value='" . $date_installation . "'>"
						. "</div>"
					. "</div>"

					. "<div class='form-row'>"
						. "<div class='col-4 mb-1 text-right' style='vertical-align: center;'>"
							. "<label for='comment_wheel' class='text-muted' style='font-size: 13px;'><strong>Примечание</strong></label>"
						. "</div>"
						. "<div class='col-6 mb-1'>"
							. "<textarea type='text' class='form-control form-control-sm black-text' id='comment_wheel' maxlength='1000' placeholder='Примечание' data-datatype='char' rows='3'>" . $comment_wheel. "</textarea>"
						. "</div>"
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