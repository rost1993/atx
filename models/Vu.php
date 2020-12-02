<?php

namespace IcKomiApp\models;

use IcKomiApp\core\Model;
use IcKomiApp\core\Logic;
use IcKomiApp\core\Functions;
use IcKomiApp\lib\Database\DB;
use IcKomiApp\widgets\Directory;

class Vu extends Model {

	protected $table = '';
	protected $trigger_operation = 4;
	protected $sql_get_record = "SELECT * FROM {table} WHERE id={id}";
	protected $sql_get_list = "SELECT * FROM {table} WHERE id_driver={id} ORDER BY ibd_arx, doc_date DESC";

	// CONST class
	const VU = 'car';
	const VU_TRACTOR = 'tractor';
	const VU_BOAT = 'boat';

	// CONST table name for accessories
	const VU_TABLE = 'drivers_document';
	const VU_TRACTOR_TABLE = 'drivers_document_tractor';
	const VU_BOAT_TABLE = 'drivers_document_boat';
	
	// CONST trigger operation move to archive
	const TRIGGER_VU = 12;
	const TRIGGER_VU_TRACTOR = 122;
	const TRIGGER_VU_BOAT = 1222;

	public function __construct($object = '') {
		if(mb_strlen($object) == 0)
			$this->table = '';
		
		if($object == self::VU) {
			$this->table = self::VU_TABLE;
			$this->trigger_operation = self::TRIGGER_VU;
		} else if($object == self::VU_TRACTOR) {
			$this->table = self::VU_TRACTOR_TABLE;
			$this->trigger_operation = self::TRIGGER_VU_TRACTOR;
		} else if($object == self::VU_BOAT) {
			$this->table = self::VU_BOAT_TABLE;
			$this->trigger_operation = self::TRIGGER_VU_BOAT;
		} else {
			$this->table = '';
			$this->trigger_operation = 0;
		}
	}

	// Функция отрисовки списка ПТС
	function rendering_list($post) {
		if(empty($post['nsyst']) || empty($post['class']))
			return;

		$class = addslashes($post['class']);

		/*$vu = 0;
		if($class === 'car')
			$vu = new VU();
		else if($class === 'tractor')
			$vu = new VU_tractor();
		else
			$vu = new VU_boat();*/
//Functions::debug($this->table);

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
				   . "<td " . $style_border . "><button type='button' id='btnEditItem' data-item='1' data-nsyst='" . $data[$i]['id'] . "' data-class='" . $class . "' class='btn btn-sm btn-info' title='Изменить водительское удостоверение'><span class='fa fa-pencil'>&nbsp</span>Изменить</button></td>"
				   . "<td " . $style_border . "><div class='dropdown'>"
				   . "<button type='button'  class='btn btn-sm btn-danger dropdown-toggle' id='btnDropdownRemoveVU' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false' title='Удалить водительское удостоверение'><span class='fa fa-trash'>&nbsp</span>Удалить</button>"
				   . "<div class='dropdown-menu' aria-labelledby='btnDropdownRemoveVU'>"
				   . "<button class='dropdown-item' type='button' id='btnRemoveItem' data-item='1' data-nsyst='" . $data[$i]['id'] . "' data-object='" . $data[$i]['id_driver'] . "' data-class='" . $class . "'><span class='fa fa-check text-success'>&nbsp</span>Подтверждаю удаление</button></div></div></td>";
			
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
		if(empty($post['class']))
			return false;
		$class = addslashes($post['class']);
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
				
				if($class === 'car') {
					if((int)($data[0]['c_a']) > 0)
						$c_a=' checked ';
					if((int)($data[0]['c_a1']) > 0)
						$c_a1=' checked ';
					if((int)($data[0]['c_b']) > 0)
						$c_b=' checked ';
					if((int)($data[0]['c_b1']) > 0)
						$c_b1=' checked ';
					if((int)($data[0]['c_c']) > 0)
						$c_c=' checked ';
					if((int)($data[0]['c_c1']) > 0)
						$c_c1=' checked ';
					if((int)($data[0]['c_d']) > 0)
						$c_d=' checked ';
					if((int)($data[0]['c_d1']) > 0)
						$c_d1=' checked ';
					if((int)($data[0]['c_be']) > 0)
						$c_be=' checked ';
					if((int)($data[0]['c_ce']) > 0)
						$c_ce=' checked ';
					if((int)($data[0]['c_c1e']) > 0)
						$c_c1e=' checked ';
					if((int)($data[0]['c_de']) > 0)
						$c_de=' checked ';
					if((int)($data[0]['c_d1e']) > 0)
						$c_d1e=' checked ';
					if((int)($data[0]['c_m']) > 0)
						$c_m=' checked ';
					if((int)($data[0]['c_tm']) > 0)
						$c_tm=' checked ';
					if((int)($data[0]['c_tb']) > 0)
						$c_tb=' checked ';
				} else if($class === 'tractor'){
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
				} else {
					if((int)($data[0]['c_gydro']) > 0)
						$c_a1=' checked ';
					if((int)($data[0]['c_moto']) > 0)
						$c_a2=' checked ';
					if((int)($data[0]['c_cater']) > 0)
						$c_a3=' checked ';
					if((int)($data[0]['c_parus_12']) > 0)
						$c_a4=' checked ';
					if((int)($data[0]['c_parus_22']) > 0)
						$c_b=' checked ';
					if((int)($data[0]['c_parus_60']) > 0)
						$c_c=' checked ';
					if((int)($data[0]['c_parus_more_60']) > 0)
						$c_d=' checked ';
				}
			}	
		}

		$html = "<div class='col-sm-12'>"
			. "<div id='VU'>"
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
				
				if($class === 'car') {
					$html .= "<div class='form-row' style='vertical-align: middle;'>"
							. "<div class='col col-sm-4 mb-1 text-right' style='vertical-align: middle;'> "
								. "<label for='doc_s' class='text-muted' style='font-size: 13px; vertical-align: middle;'><strong>Категории</strong></label>"
							. "</div>"
							. "<div class='col col-sm-4 mb-1 text-left'>"
							. "<div class='form-check form-check-inline' style='margin: 3px;'>"
								
								. "<span class='form-check-inline'>"
									. "<input type='checkbox' class='form-check-input' data-datatype='checkbox' id='c_a' " . $c_a . ">"
									. "<label class='form-check-label' for='c_a'>А</label>"
								. "</span>"
								
								. "<span class='form-check-inline'>"
									. "<input type='checkbox' class='form-check-input' data-datatype='checkbox' id='c_a1'" . $c_a1 . ">"
									. "<label class='form-check-label' for='c_a1'>А1</label>"
								. "</span>"
								
								. "<span class='form-check-inline'>"
									. "<input type='checkbox' class='form-check-input' data-datatype='checkbox' id='c_b'".$c_b.">"
									. "<label class='form-check-label' for='c_b'>B</label>"
								. "</span>"
								
								. "<span class='form-check-inline'>"
									. "<input type='checkbox' class='form-check-input' data-datatype='checkbox' id='c_b1'" . $c_b1 . ">"
									. "<label class='form-check-label' for='c_b1'>B1</label>"
								. "</span>"
								
								. "<span class='form-check-inline'>"
									. "<input type='checkbox' class='form-check-input' data-datatype='checkbox' id='c_c'" . $c_c . ">"
									. "<label class='form-check-label' for='c_c'>С</label>"
								. "</span>"

								. "<span class='form-check-inline'>"
									. "<input type='checkbox' class='form-check-input' data-datatype='checkbox' id='c_c1'" . $c_c1 . ">"
									. "<label class='form-check-label' for='c_c1'>С1</label>"
								. "</span>"

								. "<span class='form-check-inline'>"
									. "<input type='checkbox' class='form-check-input' data-datatype='checkbox' id='c_d'" . $c_d . ">"
									. "<label class='form-check-label' for='c_d'>D</label>"
								. "</span>"

								. "<span class='form-check-inline'>"
									. "<input type='checkbox' class='form-check-input' data-datatype='checkbox' id='c_d1'" . $c_d1 . ">"
									. "<label class='form-check-label' for='c_d1'>D1</label>"
								. "</span>"
							. "</div>"
								
							. "<div class='form-check form-check-inline' style='margin: 3px;'>"
								. "<span class='form-check-inline'>"
									. "<input type='checkbox' class='form-check-input' data-datatype='checkbox' id='c_BE'" . $c_be . ">"
									. "<label class='form-check-label' for='c_BE'>BE</label>"
								. "</span>"
								
								. "<span class='form-check-inline'>"
									. "<input type='checkbox' class='form-check-input' data-datatype='checkbox' id='c_CE'" . $c_ce . ">"
									. "<label class='form-check-label' for='c_CE'>CE</label>"
								. "</span>"
								
								. "<span class='form-check-inline'>"
									. "<input type='checkbox' class='form-check-input' data-datatype='checkbox' id='c_C1E'" . $c_c1e . ">"
									. "<label class='form-check-label' for='c_C1E'>C1E</label>"
								. "</span>"
								
								. "<span class='form-check-inline'>"
									. "<input type='checkbox' class='form-check-input' data-datatype='checkbox' id='c_DE'" . $c_de . ">"
									. "<label class='form-check-label' for='c_DE'>DE</label>"
								. "</span>"
								
								. "<span class='form-check-inline'>"
									. "<input type='checkbox' class='form-check-input' data-datatype='checkbox' id='c_D1E'" . $c_d1e . ">"
									. "<label class='form-check-label' for='c_D1E'>D1E</label>"
								. "</span>"
								
								. "<span class='form-check-inline'>"
									. "<input type='checkbox' class='form-check-input' data-datatype='checkbox' id='c_M'" . $c_m . ">"
									. "<label class='form-check-label' for='c_M'>М</label>"
								. "</span>"
								
								. "<span class='form-check-inline'>"
									. "<input type='checkbox' class='form-check-input' data-datatype='checkbox' id='c_TM'" . $c_tm . ">"
									. "<label class='form-check-label' for='c_TM'>TM</label>"
								. "</span>"
								
								. "<span class='form-check-inline'>"
									. "<input type='checkbox' class='form-check-input' data-datatype='checkbox' id='c_TB'" . $c_tb . ">"
									. "<label class='form-check-label' for='c_TB'>TB</label>"
								. "</span>"
						. "</div></div></div></div>";
				} else if($class == 'tractor'){
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
				} else {
					$html .= "<div class='form-row' style='vertical-align: middle;'>"
							. "<div class='col col-sm-4 mb-1 text-right' style='vertical-align: middle;'> "
								. "<label for='doc_s' class='text-muted' style='font-size: 13px; vertical-align: middle;'><strong>Категории</strong></label>"
							. "</div>"
							. "<div class='col col-sm-8 mb-1 text-left'>"
							. "<div class='form-check form-check-inline' style='margin: 3px;'>"
								
								. "<span class='form-check-inline'>"
									. "<input type='checkbox' class='form-check-input' data-datatype='checkbox' id='c_gydro' " . $c_a1 . ">"
									. "<label class='form-check-label' for='c_gydro'>Гидроцикл</label>"
								. "</span>"
								
								. "<span class='form-check-inline'>"
									. "<input type='checkbox' class='form-check-input' data-datatype='checkbox' id='c_moto'" . $c_a2 . ">"
									. "<label class='form-check-label' for='c_moto'>Мотолодка</label>"
								. "</span>"
								. "<span class='form-check-inline'>"
									. "<input type='checkbox' class='form-check-input' data-datatype='checkbox' id='c_cater'".$c_a3.">"
									. "<label class='form-check-label' for='c_cater'>Катер</label>"
								. "</span>"

							. "</div></div></div>"

							. "<div class='form-row' style='vertical-align: middle;'>"
							. "<div class='col col-sm-12 mb-1 text-center'>"
							. "<div class='form-check form-check-inline' style='margin: 3px;'>"

								. "<span class='form-check-inline'>"
									. "<input type='checkbox' class='form-check-input' data-datatype='checkbox' id='c_parus_12'" . $c_a4 . ">"
									. "<label class='form-check-label' for='c_parus_12'>Парусное до 12 кв.м</label>"
								. "</span>"

								. "<span class='form-check-inline'>"
									. "<input type='checkbox' class='form-check-input' data-datatype='checkbox' id='c_parus_22'" . $c_b . ">"
									. "<label class='form-check-label' for='c_parus_22'>Парусное до 22 кв.м</label>"
								. "</span>"

								. "<span class='form-check-inline'>"
									. "<input type='checkbox' class='form-check-input' data-datatype='checkbox' id='c_parus_60'" . $c_c . ">"
									. "<label class='form-check-label' for='c_parus_60'>Парусное до 60 кв.м</label>"
								. "</span>"

								. "<span class='form-check-inline'>"
									. "<input type='checkbox' class='form-check-input' data-datatype='checkbox' id='c_parus_more_60'" . $c_d . ">"
									. "<label class='form-check-label' for='c_parus_more_60'>Парусное более 60 кв.м</label>"
								. "</span>"

							. "</div>"

						. "</div></div></div></div>";
				}
				
				$html .= "<div class='form-row'>"
				
					. "<div class='col-4 mb-1 text-right' style='vertical-align: center;'>"
						. "<label for='comment_certificate_reg' class='text-muted' style='font-size: 13px;'><strong>Эл. образ</strong></label>"
					. "</div>"
					
					. "<div class='col-4 mb-1 text-left'>"
						. "<div id='uploadFileContainer'>" . Functions::rendering_icon_file($path_to_file, $file_extension, $nsyst, 1, true, $class) . "</div>"
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