<?php

namespace IcKomiApp\models;

use IcKomiApp\core\User;
use IcKomiApp\core\Model;
use IcKomiApp\core\Logic;
use IcKomiApp\core\Functions;
use IcKomiApp\lib\Database\DB;
use IcKomiApp\widgets\Directory;

class Accessories extends Model {
	protected $table = 'speedometer';
	protected $remove_directory = 0;
	protected $sql_get_record = "SELECT * FROM {table} WHERE id={id}";
	protected $sql_get_list = "SELECT * FROM {table} WHERE id_car={id}";

	// CONST table name for accessories
	const CAR_FIRST_AID_KID = 'car_first_aid_kid';
	const CAR_EXTINGUISHER = 'car_fire_extinguisher';
	const CAR_WARNING_TRIANGLE = 'car_warning_triangle';
	const CAR_BATTERY = 'car_battery';
	const CAR_DVR = 'car_dvr';
	
	// CONST trigger operation move to archive
	const TRIGGER_CAR_FIRST_AID_KID = 12;
	const TRIGGER_CAR_EXTINGUISHER = 122;
	const TRIGGER_WARNING_TRIANGLE = 1222;
	const TRIGGER_CAR_BATTERY = 12222;
	const TRIGGER_CAR_DVR = 122222;
	
	// CONST tittle form
	const TITLE_FORM_CAR_FIRST_AID_KID = 'Аптечка';
	const TITLE_FORM_TRIGGER_CAR_EXTINGUISHER = 'Огнетушитель';
	const TITLE_FORM_WARNING_TRIANGLE = 'Знак аварийной остановки';
	const TITLE_FORM_CAR_BATTERY = 'Аккумуляторная батарея';
	const TITLE_FORM_CAR_DVR = 'Видеорегистратор';
	
	public $title_form = 'Акссесуары';

	public function __construct($object = '') {
		if(mb_strlen($object) == 0)
			$this->table = '';
		
		if($object == self::CAR_FIRST_AID_KID) {
			$this->table = self::CAR_FIRST_AID_KID;
			$this->trigger_operation = self::TRIGGER_CAR_FIRST_AID_KID;
			$this->title_form = self::TITLE_FORM_CAR_FIRST_AID_KID;
		} else if($object == self::CAR_EXTINGUISHER) {
			$this->table = self::CAR_EXTINGUISHER;
			$this->trigger_operation = self::TRIGGER_CAR_EXTINGUISHER;
			$this->title_form = self::TITLE_FORM_TRIGGER_CAR_EXTINGUISHER;
		} else if($object == self::CAR_WARNING_TRIANGLE) {
			$this->table = self::CAR_WARNING_TRIANGLE;
			$this->trigger_operation = self::TRIGGER_WARNING_TRIANGLE;
			$this->title_form = self::TITLE_FORM_WARNING_TRIANGLE;
		} else if($object == self::CAR_BATTERY) {
			$this->table = self::CAR_BATTERY;
			$this->trigger_operation = self::TRIGGER_CAR_BATTERY;
			$this->title_form = self::TITLE_FORM_CAR_BATTERY;
		} else if($object == self::CAR_DVR) {
			$this->table = self::CAR_DVR;
			$this->trigger_operation = self::TRIGGER_CAR_DVR;
			$this->title_form = self::TITLE_FORM_CAR_DVR;
		} else {
			$this->table = '';
			$this->trigger_operation = 0;
			$this->title_form = '';
		}
	}

	// Функция отрисовки списка ПТС
	function rendering_list($post) {
		if(empty($post['nsyst']) || empty($post['object']))
			return false;
		
		$object = addslashes($post['object']);

		if(($data = $this->get_list(addslashes($post['nsyst']))) === false)
			return false;

		$role = User::get('role');
		
		$html = "";
		if(count($data) == 0) {
			$html = "<div class='text-center'><p>Сведений в базе данных не найдено!</p></div>";
		} else {
			$style_border = "style='vertical-align: middle; border: 1px solid gray;'";

			$html = "";
			if($object == self::CAR_EXTINGUISHER || $object == self::CAR_FIRST_AID_KID) {
				$html = "<table class='table table-striped table-sm table-hover text-center fs-13' style='margin: 10px;'>"
					. "<tr class='table-info'>"
						. "<th " . $style_border . " scope='col'>№ п/п</th>"
						. "<th " . $style_border . " scope='col'>Дата выдачи</th>"
						. "<th " . $style_border . " scope='col'>Дата начала срока годности</th>"
						. "<th " . $style_border . " scope='col'>Дата окончания срока годности</th>"
						. "<th " . $style_border . " scope='col'>Изменить</th>"
						. "<th " . $style_border . " scope='col'>Удалить</th>"
					. "</tr>";
			} else if($object == self::CAR_WARNING_TRIANGLE) {
				$html = "<table class='table table-striped table-sm table-hover text-center fs-13' style='margin: 10px;'>"
					. "<tr class='table-info'>"
						. "<th " . $style_border . " scope='col'>№ п/п</th>"
						. "<th " . $style_border . " scope='col'>Дата выдачи</th>"
						. "<th " . $style_border . " scope='col'>Изменить</th>"
						. "<th " . $style_border . " scope='col'>Удалить</th>"
					. "</tr>";
			} else if($object == self::CAR_BATTERY) {
				$html = "<table class='table table-striped table-sm table-hover text-center fs-13' style='margin: 10px;'>"
					. "<tr class='table-info'>"
						. "<th " . $style_border . " scope='col'>№ п/п</th>"
						. "<th " . $style_border . " scope='col'>Тип АКБ</th>"
						. "<th " . $style_border . " scope='col'>Номер АКБ</th>"
						. "<th " . $style_border . " scope='col'>Изготовитель АКБ</th>"
						. "<th " . $style_border . " scope='col'>Дата изготовления</th>"
						. "<th " . $style_border . " scope='col'>Дата установки</th>"
						. "<th " . $style_border . " scope='col'>Изменить</th>"
						. "<th " . $style_border . " scope='col'>Удалить</th>"
					. "</tr>";
			} else if($object == self::CAR_DVR) {
				$html = "<table class='table table-striped table-sm table-hover text-center fs-13' style='margin: 10px;'>"
					. "<tr class='table-info'>"
						. "<th " . $style_border . " scope='col'>№ п/п</th>"
						. "<th " . $style_border . " scope='col'>Номер</th>"
						. "<th " . $style_border . " scope='col'>Марка</th>"
						. "<th " . $style_border . " scope='col'>Модель</th>"
						. "<th " . $style_border . " scope='col'>Дата установки</th>"
						. "<th " . $style_border . " scope='col'>Изменить</th>"
						. "<th " . $style_border . " scope='col'>Удалить</th>"
					. "</tr>";
			}
			
			$list_archive = "";
	
			for($i = 0, $j = 1, $k = 1; $i < count($data); $i++) {
				if($data[$i]['ibd_arx'] == 1) {
					$html .= "<tr>"
							. "<td " . $style_border . ">" . ($j++) . "</td>";
							
					if($object == self::CAR_EXTINGUISHER || $object == self::CAR_FIRST_AID_KID) {
						$html .= "<td " . $style_border . ">" . Functions::convertToDate($data[$i]['issued_date']) . "</td>"
								. "<td " . $style_border . ">" . Functions::convertToDate($data[$i]['start_date']) . "</td>"
								. "<td " . $style_border . ">" . Functions::convertToDate($data[$i]['end_date']) . "</td>";
					} else if($object == self::CAR_WARNING_TRIANGLE) {
						$html .= "<td " . $style_border . ">" . Functions::convertToDate($data[$i]['issued_date']) . "</td>";
					} else if($object == self::CAR_BATTERY) {
						$html .= "<td " . $style_border . ">" . $data[$i]['type_battery'] . "</td>"
								. "<td " . $style_border . ">" . $data[$i]['number_battery'] . "</td>"
								. "<td " . $style_border . ">" . $data[$i]['firma_battery'] . "</td>"
								. "<td " . $style_border . ">" . Functions::convertToDate($data[$i]['producion_date']) . "</td>"
								. "<td " . $style_border . ">" . Functions::convertToDate($data[$i]['start_date']) . "</td>";
					} else if($object == self::CAR_DVR) {
						$html .= "<td " . $style_border . ">" . $data[$i]['number_dvr'] . "</td>"
								. "<td " . $style_border . ">" . $data[$i]['marka_dvr'] . "</td>"
								. "<td " . $style_border . ">" . $data[$i]['model_dvr'] . "</td>"
								. "<td " . $style_border . ">" . Functions::convertToDate($data[$i]['date_issued_dvr']) . "</td>";
					}
					
					if($role >= 2) {
						$html .= "<td " . $style_border . ">"
							. "<button type='button' class='btn btn-sm btn-info' id='btnEditItem' data-nsyst='" . $data[$i]['id'] . "' data-item='12' data-object='" . $object . "' data-title-form='" . $this->title_form . "'><span class='fa fa-pencil'>&nbsp;</span>Изменить</button></td>"
							. "<td " . $style_border . "><div class='dropdown'>"
								. "<button type='button' class='btn btn-sm btn-danger dropdown-toggle' id='btnDropdownRemove' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false' title='Удалить'><span class='fa fa-close'>&nbsp;</span>Удалить</button>"
								. "<div class='dropdown-menu' aria-labelledby='btnDropdownRemove'>"
									. "<button type='button' class='dropdown-item' id='btnRemoveItem' data-nsyst='" . $data[$i]['id'] . "' data-item='12' data-id-object='" . $data[$i]['id_car'] . "' data-object='" . $object . "'><span class='fa fa-check text-success'>&nbsp;</span>Подтверждаю удаление</button></div></div>"
							. "</td>";
					} else {
						$html .= "<td " . $style_border . "></td><td " . $style_border . "></td>";
					}
					$html .= "</tr>";
				} else {
					$list_archive .= "<tr>"
							. "<td " . $style_border . ">" . ($k++) . "</td>";
							
					if($object == self::CAR_EXTINGUISHER || $object == self::CAR_FIRST_AID_KID) {
						$list_archive .= "<td " . $style_border . ">" . Functions::convertToDate($data[$i]['issued_date']) . "</td>"
								. "<td " . $style_border . ">" . Functions::convertToDate($data[$i]['start_date']) . "</td>"
								. "<td " . $style_border . ">" . Functions::convertToDate($data[$i]['end_date']) . "</td>";
					} else if($object == self::CAR_WARNING_TRIANGLE) {
						$list_archive .= "<td " . $style_border . ">" . Functions::convertToDate($data[$i]['issued_date']) . "</td>";
					} else if($object == self::CAR_BATTERY) {
						$list_archive .= "<td " . $style_border . ">" . $data[$i]['type_battery'] . "</td>"
								. "<td " . $style_border . ">" . $data[$i]['number_battery'] . "</td>"
								. "<td " . $style_border . ">" . $data[$i]['firma_battery'] . "</td>"
								. "<td " . $style_border . ">" . Functions::convertToDate($data[$i]['producion_date']) . "</td>"
								. "<td " . $style_border . ">" . Functions::convertToDate($data[$i]['start_date']) . "</td>";
					} else if($object == self::CAR_DVR) {
						$list_archive .= "<td " . $style_border . ">" . $data[$i]['number_dvr'] . "</td>"
								. "<td " . $style_border . ">" . $data[$i]['marka_dvr'] . "</td>"
								. "<td " . $style_border . ">" . $data[$i]['model_dvr'] . "</td>"
								. "<td " . $style_border . ">" . Functions::convertToDate($data[$i]['date_issued_dvr']) . "</td>";
					}

					if($role >= 2) {
						$list_archive .= "<td " . $style_border . ">"
							. "<button type='button' class='btn btn-sm btn-info' id='btnEditItem' data-nsyst='" . $data[$i]['id'] . "' data-item='12' data-object='" . $object . "' data-title-form='" . $this->title_form . "'><span class='fa fa-pencil'>&nbsp;</span>Изменить</button></td>"
							. "<td " . $style_border . "><div class='dropdown'>"
								. "<button type='button' class='btn btn-sm btn-danger dropdown-toggle' id='btnDropdownRemove' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false' title='Удалить'><span class='fa fa-close'>&nbsp;</span>Удалить</button>"
								. "<div class='dropdown-menu' aria-labelledby='btnDropdownRemove'>"
									. "<button type='button' class='dropdown-item' id='btnRemoveItem' data-nsyst='" . $data[$i]['id'] . "' data-item='12' data-id-object='" . $data[$i]['id_car'] . "' data-object='" . $object . "'><span class='fa fa-check text-success'>&nbsp;</span>Подтверждаю удаление</button></div></div>"
							. "</td>";
					} else {
						$list_archive .= "<td " . $style_border . "></td><td " . $style_border . "></td>";
					}
					$list_archive .= "</tr>";
				}
			}
			
			$html .= "</table>";
			
			if(mb_strlen($list_archive) > 0) {
				if($object == Accessories::CAR_EXTINGUISHER || $object == Accessories::CAR_FIRST_AID_KID) {
				$html .= "<table class='table table-striped table-sm table-hover text-center fs-13' style='margin: 10px;'>"
					. "<tr class='table-success'><th colspan='6' " . $style_border . " scope='col'>АРХИВ</th></tr>"
					. "<tr class='table-info'>"
						. "<th " . $style_border . " scope='col'>№ п/п</th>"
						. "<th " . $style_border . " scope='col'>Дата выдачи</th>"
						. "<th " . $style_border . " scope='col'>Дата начала срока годности</th>"
						. "<th " . $style_border . " scope='col'>Дата окончания срока годности</th>"
						. "<th " . $style_border . " scope='col'>Изменить</th>"
						. "<th " . $style_border . " scope='col'>Удалить</th>"
					. "</tr>";
				} else if($object == Accessories::CAR_WARNING_TRIANGLE) {
					$html .= "<table class='table table-striped table-sm table-hover text-center fs-13' style='margin: 10px;'>"
						. "<tr class='table-success'><th colspan='4' " . $style_border . " scope='col'>АРХИВ</th></tr>"
						. "<tr class='table-info'>"
							. "<th " . $style_border . " scope='col'>№ п/п</th>"
							. "<th " . $style_border . " scope='col'>Дата выдачи</th>"
							. "<th " . $style_border . " scope='col'>Изменить</th>"
							. "<th " . $style_border . " scope='col'>Удалить</th>"
						. "</tr>";
				} else if($object == Accessories::CAR_BATTERY) {
					$html .= "<table class='table table-striped table-sm table-hover text-center fs-13' style='margin: 10px;'>"
						. "<tr class='table-success'><th colspan='8' " . $style_border . " scope='col'>АРХИВ</th></tr>"
						. "<tr class='table-info'>"
							. "<th " . $style_border . " scope='col'>№ п/п</th>"
							. "<th " . $style_border . " scope='col'>Тип АКБ</th>"
							. "<th " . $style_border . " scope='col'>Номер АКБ</th>"
							. "<th " . $style_border . " scope='col'>Изготовитель АКБ</th>"
							. "<th " . $style_border . " scope='col'>Дата изготовления</th>"
							. "<th " . $style_border . " scope='col'>Дата установки</th>"
							. "<th " . $style_border . " scope='col'>Изменить</th>"
							. "<th " . $style_border . " scope='col'>Удалить</th>"
						. "</tr>";
				} else if($object == self::CAR_DVR) {
					$html .= "<table class='table table-striped table-sm table-hover text-center fs-13' style='margin: 10px;'>"
						. "<tr class='table-success'><th colspan='8' " . $style_border . " scope='col'>АРХИВ</th></tr>"
						. "<tr class='table-info'>"
						. "<th " . $style_border . " scope='col'>№ п/п</th>"
						. "<th " . $style_border . " scope='col'>Номер</th>"
						. "<th " . $style_border . " scope='col'>Марка</th>"
						. "<th " . $style_border . " scope='col'>Модель</th>"
						. "<th " . $style_border . " scope='col'>Дата установки</th>"
						. "<th " . $style_border . " scope='col'>Изменить</th>"
						. "<th " . $style_border . " scope='col'>Удалить</th>"
					. "</tr>";
			}
				$html .= $list_archive . "</table>";
			}
		}

		return [$html];
	}

	public function rendering_window($post) {
		if(empty($post['object']) || empty($post['nsyst']))
			return false;

		$issued_date = $start_date = $end_date = $shelf_life = $type_battery = $number_battery = $producion_date = $firma_battery = $debit_date = $standart_term_battery = $standart_term_debit_battery = '';
		$number_dvr = $marka_dvr = $model_dvr = $date_issued_dvr = '';
		$data = [];
		$id = addslashes($post['nsyst']);
		if(addslashes($post['nsyst']) != -1) {
			if(($data = $this->get(['id' => $id])) === false)
				return false;

			if(count($data) > 0) {
				
				if($post['object'] == self::CAR_FIRST_AID_KID || $post['object'] == self::CAR_EXTINGUISHER) {
					$issued_date = Functions::convertToDate($data[0]['start_date']);
					$start_date = Functions::convertToDate($data[0]['end_date']);
					$end_date = Functions::convertToDate($data[0]['end_date']);
					$shelf_life = $data[0]['shelf_life'];
				} else if($post['object'] == self::CAR_WARNING_TRIANGLE) {
					$issued_date = Functions::convertToDate($data[0]['issued_date']);
				} else if($post['object'] == self::CAR_BATTERY) {
					$start_date = Functions::convertToDate($data[0]['start_date']);
					$type_battery = $data[0]['type_battery'];
					$number_battery = $data[0]['number_battery'];
					$producion_date = Functions::convertToDate($data[0]['producion_date']);
					$firma_battery = $data[0]['firma_battery'];
					$debit_date = Functions::convertToDate($data[0]['debit_date']);
					$standart_term_battery = $data[0]['standart_term_battery'];
					$standart_term_debit_battery = $data[0]['standart_term_debit_battery'];
				} else if($post['object'] == self::CAR_DVR) {
					$number_dvr = $data[0]['number_dvr'];
					$marka_dvr = $data[0]['marka_dvr'];
					$model_dvr = $data[0]['model_dvr'];
					$date_issued_dvr = Functions::convertToDate($data[0]['date_issued_dvr']);
				}
			}
		}
		
		$html = "";
		if(addslashes($post['object']) == self::CAR_WARNING_TRIANGLE) {
			$html = "<div class='col-12'>"
				. "<div id='formAccessories'>"
					. "<div class='form-row'>"
						. "<div class='col-4 mb-1 text-right' style='vertical-align: center;'>"
							. "<label for='start_date' class='text-muted' style='font-size: 13px;'><strong>Дата выдачи</strong></label>"
						. "</div>"
						. "<div class='col-3 mb-1'>"
							. "<input type='text' class='form-control form-control-sm black-text' id='issued_date' maxlength='20' placeholder='Дата выдачи' data-mandatory='true' data-message-error='Заполните обязательное поле: Дата выдачи' data-datatype='date' value='" . $issued_date . "'>"
						. "</div>"
					. "</div>"

				
				. "<div class='form-row'>"
					. "<div class='col col-sm-12 mb-1' style='vertical-align: center;'>"
						. "<strong><label class='form-check-label' id='error-message' style='font-size: 13px; color: red;'></label></strong>"
					. "</div>"
				. "</div>"
				
			. "</div></div>";
		} else if(addslashes($post['object']) == self::CAR_BATTERY) {
			$html = "<div class='col-12'>"
				. "<div id='formAccessories'>"
					
					. "<div class='form-row'>"
						. "<div class='col-4 mb-1 text-right' style='vertical-align: center;'>"
							. "<label for='type_battery' class='text-muted' style='font-size: 13px;'><strong>Тип АКБ</strong></label>"
						. "</div>"
						. "<div class='col-5 mb-1'>"
							. "<input type='text' class='form-control form-control-sm black-text' id='type_battery' maxlength='50' placeholder='Тип АКБ' data-mandatory='true' data-message-error='Заполните обязательное поле: Тип АКБ' data-datatype='char' value='" . $type_battery . "'>"
						. "</div>"
					. "</div>"
					
					. "<div class='form-row'>"
						. "<div class='col-4 mb-1 text-right' style='vertical-align: center;'>"
							. "<label for='number_battery' class='text-muted' style='font-size: 13px;'><strong>Номер АКБ</strong></label>"
						. "</div>"
						. "<div class='col-5 mb-1'>"
							. "<input type='text' class='form-control form-control-sm black-text' id='number_battery' maxlength='50' placeholder='Номер АКБ' data-mandatory='true' data-message-error='Заполните обязательное поле: Номер АКБ' data-datatype='char' value='" . $number_battery . "'>"
						. "</div>"
					. "</div>"
					
					. "<div class='form-row'>"
						. "<div class='col-4 mb-1 text-right' style='vertical-align: center;'>"
							. "<label for='firma_battery' class='text-muted' style='font-size: 13px;'><strong>Изготовитель АКБ</strong></label>"
						. "</div>"
						. "<div class='col-5 mb-1'>"
							. "<input type='text' class='form-control form-control-sm black-text' id='firma_battery' maxlength='100' placeholder='Изготовитель АКБ' data-mandatory='true' data-message-error='Заполните обязательное поле: Изготовитель АКБ' data-datatype='char' value='" . $firma_battery . "'>"
						. "</div>"
					. "</div>"
					
					. "<div class='form-row'>"
						. "<div class='col-4 mb-1 text-right' style='vertical-align: center;'>"
							. "<label for='producion_date' class='text-muted' style='font-size: 13px;'><strong>Дата изготовления</strong></label>"
						. "</div>"
						. "<div class='col-3 mb-1'>"
							. "<input type='text' class='form-control form-control-sm black-text' id='producion_date' maxlength='10' placeholder='Дата изготовления' data-datatype='date' value='" . $producion_date . "'>"
						. "</div>"
					. "</div>"
					
					. "<div class='form-row'>"
						. "<div class='col-4 mb-1 text-right' style='vertical-align: center;'>"
							. "<label for='start_date' class='text-muted' style='font-size: 13px;'><strong>Дата установки АКБ</strong></label>"
						. "</div>"
						. "<div class='col-3 mb-1'>"
							. "<input type='text' class='form-control form-control-sm black-text' id='start_date' maxlength='10' placeholder='Дата установки АКБ' data-mandatory='true' data-message-error='Заполните обязательное поле: Дата установки АКБ' data-datatype='date' value='" . $start_date . "'>"
						. "</div>"
					. "</div>"
					
					. "<div class='form-row'>"
						. "<div class='col-4 mb-1 text-right' style='vertical-align: center;'>"
							. "<label for='standart_term_battery' class='text-muted' style='font-size: 13px;'><strong>Нормативный срок эксплуатации до списания</strong></label>"
						. "</div>"
						. "<div class='col-3 mb-1'>"
							. "<input type='text' class='form-control form-control-sm black-text' id='standart_term_battery' maxlength='10' placeholder='Нормативный срок эксплуатации до списания' data-datatype='number' value='" . $standart_term_battery . "'>"
						. "</div>"
					. "</div>"
					
					. "<div class='form-row'>"
						. "<div class='col-4 mb-1 text-right' style='vertical-align: center;'>"
							. "<label for='standart_term_debit_battery' class='text-muted' style='font-size: 13px;'><strong>Нормативная наработка АКБ до списания</strong></label>"
						. "</div>"
						. "<div class='col-3 mb-1'>"
							. "<input type='text' class='form-control form-control-sm black-text' id='standart_term_debit_battery' maxlength='10' placeholder='Нормативная наработка АКБ до списания' data-datatype='number' value='" . $standart_term_debit_battery . "'>"
						. "</div>"
					. "</div>"
					
					. "<div class='form-row'>"
						. "<div class='col-4 mb-1 text-right' style='vertical-align: center;'>"
							. "<label for='debit_date' class='text-muted' style='font-size: 13px;'><strong>Дата списания АКБ</strong></label>"
						. "</div>"
						. "<div class='col-3 mb-1'>"
							. "<input type='text' class='form-control form-control-sm black-text' id='debit_date' maxlength='10' placeholder='Дата списания АКБ' data-datatype='date' value='" . $debit_date . "'>"
						. "</div>"
					. "</div>"

				
				. "<div class='form-row'>"
					. "<div class='col col-sm-12 mb-1' style='vertical-align: center;'>"
						. "<strong><label class='form-check-label' id='error-message' style='font-size: 13px; color: red;'></label></strong>"
					. "</div>"
				. "</div>"
				
			. "</div></div>";
		} else if(addslashes($post['object']) == self::CAR_DVR) {
			$html = "<div class='col-12'>"
				. "<div id='formAccessories'>"
					. "<div class='form-row'>"
						. "<div class='col-4 mb-1 text-right'>"
							. "<label for='number_dvr' class='text-muted font-weight-bold fs-13'>Номер</label>"
						. "</div>"
						. "<div class='col-5 mb-1'>"
							. "<input type='text' class='form-control form-control-sm black-text' id='number_dvr' maxlength='30' placeholder='Номер' data-mandatory='true' data-message-error='Заполните обязательное поле: Номер' data-datatype='char' value='" . $number_dvr . "'>"
						. "</div>"
					. "</div>"
					
					. "<div class='form-row'>"
						. "<div class='col-4 mb-1 text-right'>"
							. "<label for='marka_dvr' class='text-muted font-weight-bold fs-13'>Марка</label>"
						. "</div>"
						. "<div class='col-5 mb-1'>"
							. "<input type='text' class='form-control form-control-sm black-text' id='marka_dvr' maxlength='50' placeholder='Марка' data-mandatory='true' data-message-error='Заполните обязательное поле:Марка' data-datatype='char' value='" . $marka_dvr . "'>"
						. "</div>"
					. "</div>"
					
					. "<div class='form-row'>"
						. "<div class='col-4 mb-1 text-right'>"
							. "<label for='model_dvr' class='text-muted font-weight-bold fs-13'>Модель</label>"
						. "</div>"
						. "<div class='col-5 mb-1'>"
							. "<input type='text' class='form-control form-control-sm black-text' id='model_dvr' maxlength='50' placeholder='Модель' data-mandatory='true' data-message-error='Заполните обязательное поле: Модель' data-datatype='char' value='" . $model_dvr . "'>"
						. "</div>"
					. "</div>"

					. "<div class='form-row'>"
						. "<div class='col-4 mb-1 text-right'>"
							. "<label for='date_issued_dvr' class='text-muted font-weight-bold fs-13'>Дата установки</label>"
						. "</div>"
						. "<div class='col-5 mb-1'>"
							. "<input type='text' class='form-control form-control-sm black-text' id='date_issued_dvr' maxlength='10' placeholder='Дата установки' data-mandatory='true' data-message-error='Заполните обязательное поле: Дата установки' data-datatype='date' value='" . $date_issued_dvr . "'>"
						. "</div>"
					. "</div>"
				
				. "<div class='form-row'>"
					. "<div class='col col-sm-12 mb-1'>"
						. "<label class='form-check-label font-weight-bold fs-13' id='error-message' style='color: red;'></label>"
					. "</div>"
				. "</div>"
				
			. "</div></div>";
		}
		else {
			$html = "<div class='col-12'>"
				. "<div id='formAccessories'>"
					. "<div class='form-row'>"
						. "<div class='col-4 mb-1 text-right' style='vertical-align: center;'>"
							. "<label for='start_date' class='text-muted' style='font-size: 13px;'><strong>Дата выдачи</strong></label>"
						. "</div>"
						. "<div class='col-3 mb-1'>"
							. "<input type='text' class='form-control form-control-sm black-text' id='issued_date' maxlength='20' placeholder='Дата выдачи' data-mandatory='true' data-message-error='Заполните обязательное поле: Дата выдачи' data-datatype='date' value='" . $issued_date . "'>"
						. "</div>"
					. "</div>"
					
					. "<div class='form-row'>"
						. "<div class='col-4 mb-1 text-right' style='vertical-align: center;'>"
							. "<label for='start_date' class='text-muted' style='font-size: 13px;'><strong>Дата изготовления</strong></label>"
						. "</div>"
						. "<div class='col-3 mb-1'>"
							. "<input type='text' class='form-control form-control-sm black-text' id='start_date' maxlength='20' placeholder='Дата изготовления' data-min-view='months' data-view='months' data-mandatory='true' data-message-error='Заполните обязательное поле: Дата изготовления' data-datatype='date' value='" . $start_date . "'>"
						. "</div>"
					. "</div>"
					
					. "<div class='form-row'>"
						. "<div class='col-4 mb-1 text-right' style='vertical-align: center;'>"
							. "<label for='shelf_life' class='text-muted' style='font-size: 13px;'><strong>Срок годности, лет</strong></label>"
						. "</div>"
						. "<div class='col-3 mb-1'>"
							. "<input type='number' class='form-control form-control-sm black-text' id='shelf_life' min='1' max='20' step='1' maxlength='2' placeholder='Срок годности' data-mandatory='true' data-message-error='Заполните обязательное поле: Дата с' data-datatype='number' value='" . $shelf_life . "'>"
						. "</div>"
					. "</div>"
					
					. "<div class='form-row'>"
						. "<div class='col-4 mb-1 text-right' style='vertical-align: center;'>"
							. "<label for='end_date' class='text-muted' style='font-size: 13px;'><strong>Дата окончания срока годности</strong></label>"
						. "</div>"
						. "<div class='col-3 mb-1'>"
							. "<input type='text' class='form-control form-control-sm black-text' id='end_date' maxlength='20' placeholder='Дата окончания срока годности' data-min-view='months' data-view='months' data-mandatory='true' data-message-error='Заполните обязательное поле: Дата окончания срока годности' data-datatype='date' value='" . $end_date . "'>"
						. "</div>"
					. "</div>"

				
				. "<div class='form-row'>"
					. "<div class='col col-sm-12 mb-1' style='vertical-align: center;'>"
						. "<strong><label class='form-check-label' id='error-message' style='font-size: 13px; color: red;'></label></strong>"
					. "</div>"
				. "</div>"
				
			. "</div></div>";
		}

		return [$html];
	}
	
}