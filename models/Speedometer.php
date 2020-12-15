<?php

namespace IcKomiApp\models;

use IcKomiApp\core\User;
use IcKomiApp\core\Model;
use IcKomiApp\core\Logic;
use IcKomiApp\core\Functions;
use IcKomiApp\lib\Database\DB;
use IcKomiApp\widgets\Directory;

class Speedometer extends Model {
	protected $table = 'speedometer';
	protected $trigger_operation = 3;
	protected $sql_get_record = "SELECT * FROM {table} WHERE id={id}";

	protected $sql_get_list = "SELECT a.id, a.id_car, CAST(a.testimony_speedometer AS CHAR) + 0 as testimony_speedometer, a.date_speedometer, a.id_speedometer, a.ibd_arx, "
				. " a.ibd_arx, x1.text as reason_speedometer "
				. " FROM {table} a "
				. " LEFT JOIN s2i_klass x1 ON x1.kod=a.reason_speedometer AND x1.nomer=17 "
				. " WHERE a.id_car={id} ORDER BY a.id_speedometer DESC, a.date_speedometer DESC, a.testimony_speedometer DESC LIMIT 100";

	// Функция отрисовки списка ПТС
	function rendering_list($post) {
		if(empty($post['nsyst']))
			return false;

		if(($array_data = $this->get_list(addslashes($post['nsyst']))) === false)
			return false;
		
		$html = "";
		if(count($array_data) == 0) {
			$html = "<div class='text-center'><p>Сведений в базе данных не обнаружено!</p></div>";
		} else {
			$role = User::get('role');
			
			$style_border = "style='vertical-align: middle; border: 1px solid gray;'";
			// Формируем готовый HTML код для списка закрепленных ТС
			$html = "<table class='table table-striped table-sm table-hover text-center' style='font-size: 13px; margin: 10px;'>"
					. "<tr class='table-info'>"
						. "<th " . $style_border . " scope='col'>№ п/п</th>"
						. "<th " . $style_border . " scope='col'>№ спидометра</th>"
						. "<th " . $style_border . " scope='col'>Показание</th>"
						. "<th " . $style_border . " scope='col'>Дата</th>"
						. "<th " . $style_border . " scope='col'>Основание передачи показаний</th>"
						. "<th " . $style_border . " scope='col'>Изменить</th>"
						. "<th " . $style_border . " scope='col'>Удалить</th>"
					. "</tr>";
			
			$list_archive = "";
			
			// Сортируем массив. Используем сложную сортировку сначала по номеру спидометра, затем по дате снятия показаний
			$column_id_speedometer = array_column($array_data, 'id_speedometer');
			$column_date_speedometer = array_column($array_data, 'date_speedometer');
			array_multisort($column_id_speedometer, SORT_DESC, $column_date_speedometer, SORT_DESC, $array_data);
	
			for($i = 0, $j = 1, $k = 1; $i < count($array_data); $i++) {
				$html .= "<tr>"
						. "<td " . $style_border . ">" . ($j++) . "</td>"
						. "<td " . $style_border . ">" . $array_data[$i]['id_speedometer'] . "-й спидометр</td>"
						. "<td " . $style_border . ">" . $array_data[$i]['testimony_speedometer'] . "</td>"
						. "<td " . $style_border . ">" . Functions::convertToDate($array_data[$i]['date_speedometer']) . "</td>"
						. "<td " . $style_border . ">" . $array_data[$i]['reason_speedometer'] . "</td>";
				
				if(($array_data[$i]['ibd_arx'] == 1) || ($role >= 2)) {
					if($role >= 2) {
						$html .= "<td " . $style_border . ">"
							. "<button type='button' class='btn btn-sm btn-info' id='btnEditItem' data-item='4' data-nsyst='" . $array_data[$i]['id'] . "' data-car='" . $array_data[$i]['id_car'] . "' title='Скорректировать переданные сведения спидометра'><span class='fa fa-pencil'>&nbsp;</span>Изменить</button></td>";
						$html .= "<td " . $style_border . "><div class='dropdown'>"
							. "<button type='button' class='btn btn-sm btn-danger dropdown-toggle' id='btnDropdownRemoveTestimonySpeedometer' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false' title='Удалить переданные сведения спидометра'><span class='fa fa-close'>&nbsp;</span>Удалить</button>"
							. "<div class='dropdown-menu' aria-labelledby='btnDropdownRemoveTO'>"
								. "<button type='button' class='dropdown-item' id='btnRemoveItem' data-item='4' data-nsyst='" . $array_data[$i]['id'] . "' data-object='" . $array_data[$i]['id_car'] . "'><span class='fa fa-check text-success'>&nbsp;</span>Подтверждаю удаление</button></div></div>"
						. "</td>";
					} else {
						$html .= "<td " . $style_border . "></td><td " . $style_border . "></td>";
					}
				} else {
					$html .= "<td " . $style_border . "></td><td " . $style_border . "></td>";
				}
				$html .= "</tr>";
			}
		}
		
		$html .= "</table>";

		return [$html];
	}

	public function rendering_window($post) {
		if(empty($post['nsyst']))
			return false;
		
		$id = addslashes($post['nsyst']);
		$id_car = addslashes($post['car']);

		$testimony = $reason = $date = $id_speedometer = "";
		if($id != -1) {			
			if(($data = $this->get(['id' => $id])) === false)
				return false;
			
			if(count($data) > 0) {
				$testimony = $data[0]['testimony_speedometer'];
				$reason = $data[0]['reason_speedometer'];
				$date = Functions::convertToDate($data[0]['date_speedometer']);
				$id_speedometer = $data[0]['id_speedometer'];
			}
		}

		$spr_reason = Directory::get_directory(17, $reason);
		
		if(($data = $this->get_number_speedometer($id_car)) === false)
			return false;

		$role = User::get('role');
		
		// Только администратору разрешается корректировать показания спидометра
		$disabled_speedometer = "";
		if($role >= 2)
			$disabled_speedometer = " disabled ";
		
		// Формируем список спидометров
		$list_speedometer = "<option value='0'></option>" . $data[0]['num_speedometer'];
		for($i = $data[0]['num_speedometer']; $i > 0; $i--) {
			if($i == $data[0]['num_speedometer']) {
				if($id_speedometer == $i)
					$list_speedometer .= "<option value='" . $i . "' selected>" . $i . "-й спидометр</option>";
				else
					$list_speedometer .= "<option value='" . $i . "'>" . $i . "-й спидометр</option>";
			} else {
				$list_speedometer .= "<option value='" . $i . "' style='color: red;' " . $disabled_speedometer . ">" . $i . "-й спидометр (архив)</option>";
			}
		}
		
		$html = "<div class='col-12' id='formSpeedometer'>"
				. "<div class='form-row'>"
					. "<div class='col-4 mb-1 text-right' style='vertical-align: center;'>"
						. "<label for='id_speedometer' class='text-muted' style='font-size: 13px;'><strong>Спидометр</strong></label>"
					. "</div>"
					. "<div class='col-6 mb-1'>"
						. "<select class='custom-select custom-select-sm black-text' id='id_speedometer' data-mandatory='true' data-message-error='Заполните обязательное поле: Спидометр' data-datatype='number'>" . $list_speedometer . "</select>"
					. "</div>"
				. "</div>"
		
		
				. "<div class='form-row'>"
					. "<div class='col-4 mb-1 text-right' style='vertical-align: center;'>"
						. "<label for='testimony_speedometer' class='text-muted' style='font-size: 13px;'><strong>Показание спидометра, км</strong></label>"
					. "</div>"
					. "<div class='col-4 mb-1'>"
						. "<input type='text' class='form-control form-control-sm black-text' id='testimony_speedometer' maxlength='20' placeholder='Пробег, км' data-mandatory='true' data-message-error='Заполните обязательное поле: Показание спидометра' data-datatype='number' value='" . $testimony . "'>"
					. "</div>"
				. "</div>"
				
				. "<div class='form-row'>"
					. "<div class='col-4 mb-1 text-right' style='vertical-align: center;'>"
						. "<label for='date_speedometer' class='text-muted' style='font-size: 13px;'><strong>Дата</strong></label>"
					. "</div>"
					. "<div class='col-2 mb-1'>"
						. "<input type='text' class='form-control form-control-sm black-text' id='date_speedometer' maxlength='10' placeholder='Дата' data-mandatory='true' data-message-error='Заполните обязательное поле: Дата' data-datatype='date' value='" . $date . "'>"
					. "</div>"
				. "</div>"
				
				. "<div class='form-row'>"
					. "<div class='col-4 mb-1 text-right' style='vertical-align: center;'>"
						. "<label for='reason_tachometer' class='text-muted' style='font-size: 13px;'><strong>Основание</strong></label>"
					. "</div>"
					. "<div class='col-6 mb-1'>"
						. "<select class='custom-select custom-select-sm black-text' id='reason_speedometer' data-mandatory='true' data-message-error='Заполните обязательное поле: Основание' data-datatype='number'>" . $spr_reason . "</select>"
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

	// Функция получения количества спидометров у конкретного ТС
	// id_car - уникальный ID  транспортного средства
	public function get_number_speedometer($id_car) {
		$sql = "SELECT num_speedometer FROM cars WHERE id=" . $id_car;
		if(($data = DB::query($sql)) === false)
			return false;
		return $data;
	}

	// Функция добавления спидометра к ТС. Увеличивает на 1 поле num_speedometer таблицы cars
	// id_car - уникальный ID  транспортного средства
	public function add_speedometer($post) {
		if(empty($post['nsyst']))
			return false;

		$id_car = $post['nsyst'];

		$sql = "UPDATE cars SET num_speedometer=num_speedometer+1 WHERE id=" . $id_car;
		if((DB::query($sql, DB::INSERT_OR_UPDATE)) === false)
			return false;
		return true;
	}

	// Функция удаления спидометра у ТС. Уменьшает на 1 поле num_speedometer таблицы cars
	// id_car - уникальный ID  транспортного средства
	public function remove_speedometer($post) {
		if(empty($post['nsyst']))
			return false;

		$id_car = $post['nsyst'];
		
		$sql = "UPDATE cars SET num_speedometer=IF((num_speedometer-1) <= 0, 1, num_speedometer-1) WHERE id=" . $id_car;
		if((DB::query($sql, DB::INSERT_OR_UPDATE)) === false)
			return false;
		return true;
	}

	// Функция вычисления начальных показаний спидометра
	function settings_speedometer($post) {
		if(empty($post['nsyst']))
			return false;

		if(($data = $this->list_speedometer_values(addslashes($post['nsyst']))) === false)
			return false;

		$html = '';
		
		if(count($data) == 0) {
			$html = "<p>Сведений в базе данных не обнаружено!</p>";
		} else {
			
			$speedometers = array();
			for($i = 1; $i <= $data[0]['BB']; $i++) {
				$speedometers[$i]['name'] = $i . '-й спидометр';
				$speedometers[$i]['id_car'] = addslashes($_POST['nsyst']);
				$speedometers[$i]['id_speedometer'] = $i;
				$speedometers[$i]['id'] = '-1';
				$speedometers[$i]['testimony'] = '';
			}
			
			for($i = 0; $i < count($data); $i++) {
				if($data[$i]['II'] == 2) {
					$speedometers[$data[$i]['CC']]['testimony'] = $data[$i]['DD'];
					$speedometers[$data[$i]['CC']]['id'] = $data[$i]['BB'];
				}
			}
			
			$style_border = "style='vertical-align: middle; border: 1px solid gray;'";
			// Формируем готовый HTML код для списка закрепленных ТС
			$html = "<table class='table table-striped table-sm table-hover text-center' style='font-size: 13px; margin: 10px;'>"
					. "<tr class='table-info'>"
						. "<th " . $style_border . " scope='col'>№ п/п</th>"
						. "<th " . $style_border . " scope='col'>№ спидометра</th>"
						. "<th " . $style_border . " scope='col'>Первое показание спидометра</th>"
						. "<th " . $style_border . " scope='col'>Изменить</th>"
					. "</tr>";
			
			for($i = 1; $i <= count($speedometers); $i++) {
				$html .= "<tr>"
						. "<td " . $style_border . ">" . ($i+1) . "</td>"
						. "<td " . $style_border . ">" . $speedometers[$i]['name'] . "</td>"
						. "<td " . $style_border . "><input type='text' class='form-control form-control-sm black-text inputValueFirstTestimonySpeedometer' value='" . $speedometers[$i]['testimony'] . "' placeholder='Введите значение спидометра'></td>"
						. "<td " . $style_border . "><button type='button' class='btn btn-sm btn-info' id='btnSaveFirstTestimonySpeedometers' data-nsyst='" . $speedometers[$i]['id'] . "' data-id-car='" . $speedometers[$i]['id_car'] . "' data-id-speedometer='" . $speedometers[$i]['id_speedometer'] . "'><span class='fa fa-pencil'>&nbsp;</span>Изменить</button></td>"
						. "</tr>";
			}
		}
		
		return [$html];
	}

	// Список спидометров и их начальных показаний на момент замены
	public function list_speedometer_values($id_car) {
		if(empty($id_car))
			return false;
		
		$sql = "SELECT 1 as II, id as AA, num_speedometer as BB, 0 as CC, 0 as DD FROM cars WHERE id=" . $id_car
				. " UNION ALL "
				. " SELECT 2, id_car, id, id_speedometer, testimony FROM speedometer_first_testimony WHERE id_car=" . $id_car
				. " ORDER BY II ASC";
		   
		if(($data = DB::query($sql)) === false)
			return false;
		return $data;
	}

	// Сохранение данных для начальных показаний спидометра
	//public function save_data_first_testimony($nsyst, $id_car, $id_speedometer, $value) {
	public function save_first_testimony($post) {
		if(empty($post['nsyst']) || empty($post['car']) || empty($post['speedometer']))
			return false;

		$sh_polz = User::get('id');
		$nsyst = $post['nsyst'];
		$id_car = $post['car'];
		$id_speedometer = $post['speedometer'];
		$value = str_replace(',', '.', rawurldecode($post['value']));
		
		$sql = '';
		if($nsyst == -1) {
			$sql = "INSERT INTO speedometer_first_testimony (id_car, id_speedometer, testimony, sh_polz) VALUES (" . $id_car . "," . $id_speedometer . "," . $value . "," . $sh_polz . ")";
		} else {
			$sql = "UPDATE speedometer_first_testimony SET testimony=" . $value . ",sh_polz=" . $sh_polz . " WHERE id_car=" . $id_car . " AND id_speedometer=" . $id_speedometer . " AND id=" . $nsyst;
		}

		if((DB::query($sql, DB::INSERT_OR_UPDATE)) === false)
			return false;
		return true;
	}
	
}