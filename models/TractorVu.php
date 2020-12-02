<?php

namespace IcKomiApp\models;

use IcKomiApp\core\Model;
use IcKomiApp\core\Logic;
use IcKomiApp\core\Functions;
use IcKomiApp\lib\Database\DB;
use IcKomiApp\widgets\Directory;

class TractorVu extends Model {

	protected $table = 'drivers_document_tractor';
	protected $trigger_operation = 44;
	protected $sql_get_record = "SELECT * FROM {table} WHERE id={id}";
	protected $sql_get_list = "SELECT * FROM {table} WHERE id_driver={id} ORDER BY ibd_arx, doc_date DESC";
	protected $remove_directory = 1;

	public function __construct($object = '') {
	}

	// Функция отрисовки списка ПТС
	function rendering_list($post) {
		if(empty($post['nsyst']))
			return;

		if(($data = $this->get_list(addslashes($post['nsyst']))) === false)
			return false;
		
		$result = array();
		if(count($data) == 0) {
			$html = "<div class='text-center'><p>Сведений в базе данных не найдено!</p></div>";
		} else {
			$style_border = "style='vertical-align: middle; border: 1px solid gray;'";
		
			// Текущие ВУ
			$html = "<table class='table table-striped table-sm table-hover text-center' style='font-size: 13px; margin: 10px;'>";
			$html .= "<tr class='table-info'>"
					. "<th " . $style_border . " scope='col'>№ п/п</th>"
					. "<th " . $style_border . " scope='col'>Серия и номер ВУ</th>"
					. "<th " . $style_border . " scope='col'>Дата выдачи ВУ</th>"
					. "<th " . $style_border . " scope='col'>Дата окончания ВУ</th>"
					. "<th " . $style_border . " scope='col'>Эл. образ</th>"
					. "<th " . $style_border . " scope='col'>Скорректировать ВУ</th>"
					. "<th " . $style_border . " scope='col'>Удалить ВУ</th>"
				. "</tr>";

			$list_vu_archive = $list_vu = "";		// Переменные для хранения списка водительских удостоверений
			for($i = 0, $k1 = 1, $k2 = 1; $i < count($data); $i++) {
				$temp = "<td " . $style_border . ">" . $data[$i]['doc_s'] . " " . $data[$i]['doc_n'] . "</td>"
				   . "<td " . $style_border . ">" . Functions::convertToDate($data[$i]['doc_date']) . "</td>"
				   . "<td " . $style_border . ">" . Functions::convertToDate($data[$i]['doc_end_date']) . "</td>"
				   . "<td " . $style_border . ">" . Functions::rendering_icon_file($data[$i]['path_to_file'], $data[$i]['file_extension']) . "</td>"
				   . "<td " . $style_border . "><button type='button' id='btnEditItem' data-item='10' data-nsyst='" . $data[$i]['id'] . "' class='btn btn-sm btn-info' title='Изменить водительское удостоверение'><span class='fa fa-pencil'>&nbsp</span>Изменить</button></td>"
				   . "<td " . $style_border . "><div class='dropdown'>"
				   . "<button type='button'  class='btn btn-sm btn-danger dropdown-toggle' id='btnDropdownRemoveVU' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false' title='Удалить водительское удостоверение'><span class='fa fa-trash'>&nbsp</span>Удалить</button>"
				   . "<div class='dropdown-menu' aria-labelledby='btnDropdownRemoveVU'>"
				   . "<button class='dropdown-item' type='button' id='btnRemoveItem' data-item='10' data-nsyst='" . $data[$i]['id'] . "' data-object='" . $data[$i]['id_driver'] . "'><span class='fa fa-check text-success'>&nbsp</span>Подтверждаю удаление</button></div></div></td>";
			
				if($data[$i]['ibd_arx'] == 1)
					$list_vu .= "<tr><td " . $style_border . ">" . $k1++ . "</td>" . $temp . "</tr>";
				else
					$list_vu_archive .= "<tr><td " . $style_border . ">" . $k2++ . "</td>" . $temp . "</tr>";
			}
		
			$html .= $list_vu . "</table>";
		
			if(mb_strlen($list_vu_archive) > 0) {
				// Архивные ВУ
				$html .= "<table class='table table-striped table-sm table-hover text-center' style='font-size: 13px; margin: 10px;'>";
				$html .= "<tr class='table-success'><th colspan='7' " . $style_border . " scope='col'>АРХИВ</th></tr>";
				$html .= "<tr class='table-info'>"
						. "<th " . $style_border . " scope='col'>№ п/п</th>"
						. "<th " . $style_border . " scope='col'>Серия и номер ВУ</th>"
						. "<th " . $style_border . " scope='col'>Дата выдачи ВУ</th>"
						. "<th " . $style_border . " scope='col'>Дата окончания ВУ</th>"
						. "<th " . $style_border . " scope='col'>Эл. образ</th>"
						. "<th " . $style_border . " scope='col'>Скорректировать ВУ</th>"
						. "<th " . $style_border . " scope='col'>Удалить ВУ</th>"
					. "</tr>";
				$html .= $list_vu_archive . "</table>";
			}
		}

		return [$html];
	}

	public function rendering_window($post) {
		$nsyst = '';

		if(empty($post['nsyst'])) {
			$nsyst = 0;
		} else {
			if($post['nsyst'] == -1)
				$nsyst = 0;
			else
				$nsyst = addslashes($post['nsyst']);
		}
		
		$doc_s = $doc_n = $doc_date = $doc_end_date = $path_to_file = $file_extension = '';
		$c_a = $c_a1 = $c_a2 = $c_a3 = $c_a4 = $c_b = $c_b1 = $c_c = $c_c1 = $c_d = $c_d1 = $c_be = $c_ce = $c_c1e = $c_de = $c_d1e = $c_m = $c_tm = $c_tb = $c_e = $c_f = '';
		$ibd_arx = 0;

		if($nsyst != 0) {
			if(($data = $this->get(['id' => $nsyst])) === false)
				return false;

			if(count($data) > 0) {
				$doc_s = $data[0]['doc_s'];
				$doc_n = $data[0]['doc_n'];
				$doc_date = Functions::convertToDate($data[0]['doc_date']);
				$doc_end_date = Functions::convertToDate($data[0]['doc_end_date']);
				$ibd_arx = $data[0]['ibd_arx'];
				$path_to_file = $data[0]['path_to_file'];
				$file_extension = $data[0]['file_extension'];
				
				if((int)($data[0]['c_a1']) > 0)
					$c_a1=' checked ';
				if((int)($data[0]['c_a2']) > 0)
					$c_a2=' checked ';
				if((int)($data[0]['c_a3']) > 0)
					$c_a3=' checked ';
				if((int)($data[0]['c_a4']) > 0)
					$c_a4=' checked ';
				if((int)($data[0]['c_b']) > 0)
					$c_b=' checked ';
				if((int)($data[0]['c_c']) > 0)
					$c_c=' checked ';
				if((int)($data[0]['c_d']) > 0)
					$c_d=' checked ';
				if((int)($data[0]['c_e']) > 0)
					$c_e=' checked ';
				if((int)($data[0]['c_f']) > 0)
					$c_f=' checked ';
			}	
		}

		$html = "<div class='col-sm-12'>"
			. "<div id='TractorVU'>"
				. "<div class='form-row'>"
					. "<div class='col col-sm-4 mb-1 text-right' style='vertical-align: center;'>"
						. "<label for='doc_s' class='text-muted' style='font-size: 13px;'><strong>Водительское удостоверение</strong></label>"
					. "</div>"
					. "<div class='col col-sm-3 mb-1'>"
						. "<input type='text' class='form-control form-control-sm black-text' id='doc_s' maxlength='4' placeholder='Серия' data-mandatory='true' data-message-error='Заполните обязательное поле: Серия ВУ' data-datatype='char' value='" . $doc_s . "'>"
					. "</div>"
					. "<div class='col col-sm-3 mb-1'>"
						. "<input type='text' class='form-control form-control-sm black-text' id='doc_n' maxlength='6' placeholder='Номер' data-mandatory='true' data-message-error='Заполните обязательное поле: Номер ВУ' data-datatype='char' value='" . $doc_n . "'>"
					. "</div>"
				. "</div>"
				
				. "<div class='form-row'>"
					. "<div class='col col-sm-4 mb-1 text-right' style='vertical-align: center;'>"
						. "<label for='doc_date' class='text-muted' style='font-size: 13px;'><strong>Дата выдачи ВУ</strong></label>"
					. "</div>"
					. "<div class='col col-sm-3 mb-1'>"
						. "<input type='text' class='form-control form-control-sm black-text datepicker-here' id='doc_date' maxlength='10' placeholder='Дата выдачи ВУ' data-mandatory='true' data-message-error='Заполните обязательное поле: Дата выдачи ВУ' data-datatype='date' value='" . $doc_date . "'>"
					. "</div>"
				. "</div>"
				
				. "<div class='form-row'>"
					. "<div class='col col-sm-4 mb-1 text-right' style='vertical-align: center;'>"
						. "<label for='doc_end_date' class='text-muted' style='font-size: 13px;'><strong>Дата окончания ВУ</strong></label>"
					. "</div>"
					. "<div class='col col-sm-3 mb-1'>"
						. "<input type='text' class='form-control form-control-sm black-text datepicker-here' id='doc_end_date' maxlength='10' placeholder='Дата окончания ВУ' data-mandatory='true' data-message-error='Заполните обязательное поле: Дата окончания ВУ' data-datatype='date' value='" . $doc_end_date . "'>"
					. "</div>"
				. "</div>";
				
				
					$html .= "<div class='form-row' style='vertical-align: middle;'>"
							. "<div class='col col-sm-4 mb-1 text-right' style='vertical-align: middle;'> "
								. "<label for='doc_s' class='text-muted' style='font-size: 13px; vertical-align: middle;'><strong>Категории</strong></label>"
							. "</div>"
							. "<div class='col col-sm-4 mb-1 text-left'>"
							. "<div class='form-check form-check-inline' style='margin: 3px;'>"
								
								. "<span class='form-check-inline'>"
									. "<input type='checkbox' class='form-check-input' data-datatype='checkbox' id='c_a1' " . $c_a1 . ">"
									. "<label class='form-check-label' for='c_a1'>А1</label>"
								. "</span>"
								
								. "<span class='form-check-inline'>"
									. "<input type='checkbox' class='form-check-input' data-datatype='checkbox' id='c_a2'" . $c_a2 . ">"
									. "<label class='form-check-label' for='c_a2'>А2</label>"
								. "</span>"
								
								. "<span class='form-check-inline'>"
									. "<input type='checkbox' class='form-check-input' data-datatype='checkbox' id='c_a3'".$c_a3.">"
									. "<label class='form-check-label' for='c_a3'>A3</label>"
								. "</span>"
								
								. "<span class='form-check-inline'>"
									. "<input type='checkbox' class='form-check-input' data-datatype='checkbox' id='c_a4'" . $c_a4 . ">"
									. "<label class='form-check-label' for='c_a4'>A4</label>"
								. "</span>"
								
								. "<span class='form-check-inline'>"
									. "<input type='checkbox' class='form-check-input' data-datatype='checkbox' id='c_b'" . $c_b . ">"
									. "<label class='form-check-label' for='c_b'>B</label>"
								. "</span>"

								. "<span class='form-check-inline'>"
									. "<input type='checkbox' class='form-check-input' data-datatype='checkbox' id='c_c'" . $c_c . ">"
									. "<label class='form-check-label' for='c_c'>С</label>"
								. "</span>"

								. "<span class='form-check-inline'>"
									. "<input type='checkbox' class='form-check-input' data-datatype='checkbox' id='c_d'" . $c_d . ">"
									. "<label class='form-check-label' for='c_d'>D</label>"
								. "</span>"

								. "<span class='form-check-inline'>"
									. "<input type='checkbox' class='form-check-input' data-datatype='checkbox' id='c_e'" . $c_e . ">"
									. "<label class='form-check-label' for='c_e'>E</label>"
								. "</span>"
							. "</div>"

						. "</div></div></div></div>";

				$html .= "<div class='form-row'>"
				
					. "<div class='col-4 mb-1 text-right' style='vertical-align: center;'>"
						. "<label for='comment_certificate_reg' class='text-muted' style='font-size: 13px;'><strong>Эл. образ</strong></label>"
					. "</div>"
					
					. "<div class='col-4 mb-1 text-left'>"
						. "<div id='uploadFileContainer'>" . Functions::rendering_icon_file($path_to_file, $file_extension, $nsyst, 10, true) . "</div>"
					. "</div>"
					
					. "<div class='col-2 mb-1 text-right'>"
						. "<span class='btn btn-sm btn-primary fileinput-button' title='Выберите файл'>"
							. "<span class='fa fa-folder-open'>&nbsp;</span>Выберите файл"
								. "<input id='btnAddFileModalWindow' type='file' name='files' accept='.pdf'>"
							. "</span>"
					. "</div>"
					
				. "</div>";

				$html .= "<div class='form-row'>"
				. "<div class='col col-sm-12 mb-1' style='vertical-align: center;'>"
					. "<strong><label class='form-check-label' id='error-message' style='font-size: 13px; color: red;'></label></strong>"
				. "</div></div></div></form>";

		return [$html];
	}
	
}