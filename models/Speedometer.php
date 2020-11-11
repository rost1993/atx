<?php

namespace IcKomiApp\models;

use IcKomiApp\core\Model;
use IcKomiApp\core\Logic;
use IcKomiApp\core\Functions;
use IcKomiApp\lib\Database\DB;

class Speedometer extends Model {
	protected $table = 'speedometer';

	protected $sql_get_record = "";

	protected $sql_get_list = "SELECT a.id, a.id_car, CAST(a.testimony_speedometer AS CHAR) + 0 as testimony_speedometer, a.date_speedometer, a.id_speedometer, a.ibd_arx, "
				. " a.ibd_arx, x1.text as reason_speedometer "
				. " FROM {table} a "
				. " LEFT JOIN s2i_klass x1 ON x1.kod=a.reason_speedometer AND x1.nomer=17 "
				. " WHERE a.id_car={id} ORDER BY a.id_speedometer DESC, a.date_speedometer DESC, a.testimony_speedometer DESC";

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
			/*Session::start();
			$role = Session::get('role');
			Session::commit();*/

			$role = 9;
			
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
				
				if(($array_data[$i]['ibd_arx'] == 1) || ($role >= 8)) {
					if(($role > 1) && ($role != 4)) {
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
	
}