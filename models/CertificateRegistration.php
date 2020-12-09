<?php

namespace IcKomiApp\models;

use IcKomiApp\core\User;
use IcKomiApp\core\Model;
use IcKomiApp\core\Logic;
use IcKomiApp\core\Functions;
use IcKomiApp\lib\Database\DB;
use IcKomiApp\widgets\Directory;

class CertificateRegistration extends Model {
	protected $table = 'certificate_registration';
	protected $trigger_operation = 6;
	protected $sql_get_record = "SELECT * FROM {table} WHERE id={id}";
	protected $remove_directory = 1;

	protected $sql_get_list = "SELECT a.id, a.id_car, a.s_certificate_reg, a.n_certificate_reg, a.date_certificate_reg, a.comment_certificate_reg, a.ibd_arx, x1.text as text_org, a.path_to_file, a.file_extension FROM {table} a "
			. " LEFT JOIN s2i_klass x1 ON x1.kod=a.org_certificate_reg AND x1.nomer=22 "
			. " WHERE a.id_car={id}"
			. " ORDER BY a.date_certificate_reg DESC";

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
						. "<th " . $style_border . " scope='col'>Серия и номер</th>"
						. "<th " . $style_border . " scope='col'>Дата выдачи</th>"
						. "<th " . $style_border . " scope='col'>Кем выдано</th>"
						. "<th " . $style_border . " scope='col'>Комментарий</th>"
						. "<th " . $style_border . " scope='col'>Эл. образ</th>"
						. "<th " . $style_border . " scope='col'>Изменить</th>"
						. "<th " . $style_border . " scope='col'>Удалить</th>"
					. "</tr>";
			
			$list_archive = "";
	
			for($i = 0, $j = 1, $k = 1; $i < count($data); $i++) {
				if($data[$i]['ibd_arx'] == 1) {
					$html .= "<tr>"
							. "<td " . $style_border . ">" . ($j++) . "</td>"
							. "<td " . $style_border . ">" . $data[$i]['s_certificate_reg'] . " " . $data[$i]['n_certificate_reg'] . "</td>"
							. "<td " . $style_border . ">" . Functions::convertToDate($data[$i]['date_certificate_reg']) . "</td>"
							. "<td " . $style_border . ">" . $data[$i]['text_org'] . "</td>"
							. "<td " . $style_border . ">" . $data[$i]['comment_certificate_reg'] . "</td>"
							. "<td " . $style_border . ">" . Functions::rendering_icon_file($data[$i]['path_to_file'], $data[$i]['file_extension']) . "</td>";

					if($role >= 2) {
						$html .= "<td " . $style_border . ">"
							. "<button type='button' class='btn btn-sm btn-info' id='btnEditItem' data-nsyst='" . $data[$i]['id'] . "' data-item='6'><span class='fa fa-pencil'>&nbsp</span>Изменить</button></td>"
							. "<td " . $style_border . "><div class='dropdown'>"
								. "<button type='button' class='btn btn-sm btn-danger dropdown-toggle' id='btnDropdownRemove' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false' title='Удалить полис ОСАГО'><span class='fa fa-close'>&nbsp</span>Удалить</button>"
								. "<div class='dropdown-menu' aria-labelledby='btnDropdownRemove'>"
									. "<button type='button' class='dropdown-item' id='btnRemoveItem' data-nsyst='" . $data[$i]['id'] . "' data-item='6' data-object='" . $data[$i]['id_car'] . "'><span class='fa fa-check text-success'>&nbsp</span>Подтверждаю удаление</button></div></div>"
							. "</td>";
					} else {
						$html .= "<td " . $style_border . "></td><td " . $style_border . "></td>";
					}	
					$html .= "</tr>";
				} else {
					$list_archive .= "<tr>"
							. "<td " . $style_border . ">" . ($k++) . "</td>"
							. "<td " . $style_border . ">" . $data[$i]['s_certificate_reg'] . " " . $data[$i]['n_certificate_reg'] . "</td>"
							. "<td " . $style_border . ">" . Functions::convertToDate($data[$i]['date_certificate_reg']) . "</td>"
							. "<td " . $style_border . ">" . $data[$i]['text_org'] . "</td>"
							. "<td " . $style_border . ">" . $data[$i]['comment_certificate_reg'] . "</td>"
							. "<td " . $style_border . ">" . Functions::rendering_icon_file($data[$i]['path_to_file'], $data[$i]['file_extension']) . "</td>";

					if($role >= 2) {
						$list_archive .= "<td " . $style_border . ">"
							. "<button type='button' class='btn btn-sm btn-info' id='btnEditItem' data-nsyst='" . $data[$i]['id'] . "' data-item='6'><span class='fa fa-pencil'>&nbsp</span>Изменить</button></td>"
							. "<td " . $style_border . "><div class='dropdown'>"
								. "<button type='button' class='btn btn-sm btn-danger dropdown-toggle' id='btnDropdownRemove' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false' title='Удалить полис ОСАГО'><span class='fa fa-close'>&nbsp</span>Удалить</button>"
								. "<div class='dropdown-menu' aria-labelledby='btnDropdownRemove'>"
									. "<button type='button' class='dropdown-item' id='btnRemoveItem' data-nsyst='" . $data[$i]['id'] . "' data-item='6' data-object='" . $data[$i]['id_car'] . "'><span class='fa fa-check text-success'>&nbsp</span>Подтверждаю удаление</button></div></div>"
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
						. "<tr class='table-success'><th colspan='7' style='vertical-align: middle;  border: 1px solid gray;'>АРХИВ</th></tr>"
						. "<tr class='table-info'>"
							. "<th " . $style_border . " scope='col'>№ п/п</th>"
							. "<th " . $style_border . " scope='col'>Серия и номер</th>"
							. "<th " . $style_border . " scope='col'>Дата выдачи</th>"
							. "<th " . $style_border . " scope='col'>Кем выдано</th>"
							. "<th " . $style_border . " scope='col'>Комментарий</th>"
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
		$s_certificate = $n_certificate = $date_certificate = $org_certificate = $comment_certificate = $path_to_file = $file_extension = "";
		if($id != -1) {
			if(($data = $this->get(['id' => $id])) === false)
				return false;
			
			if(count($data) > 0) {
				$s_certificate = $data[0]['s_certificate_reg'];
				$n_certificate = $data[0]['n_certificate_reg'];
				$date_certificate = Functions::convertToDate($data[0]['date_certificate_reg']);
				$org_certificate = $data[0]['org_certificate_reg'];
				$comment_certificate = $data[0]['comment_certificate_reg'];
				$path_to_file = $data[0]['path_to_file'];
				$file_extension = $data[0]['file_extension'];
			}
		}

		$spr_org_certificate = Directory::get_directory(22, $org_certificate);
		
		$html = "<div class='col-12'>"
				. "<div id='formCertificate'>"
					. "<div class='form-row'>"
						. "<div class='col-4 mb-1 text-right' style='vertical-align: center;'>"
							. "<label for='s_certificate_reg' class='text-muted' style='font-size: 13px;'><strong>Свидетельство о регистрации</strong></label>"
						. "</div>"
						. "<div class='col-2 mb-1'>"
							. "<input type='text' class='form-control form-control-sm black-text' id='s_certificate_reg' maxlength='10' placeholder='Серия' data-mandatory='true' data-message-error='Заполните обязательное поле: Серия свидетельства' data-datatype='char' value='" . $s_certificate . "'>"
						. "</div>"	
						. "<div class='col-3 mb-1'>"
							. "<input type='text' class='form-control form-control-sm black-text' id='n_certificate_reg' maxlength='10' placeholder='Номер' data-mandatory='true' data-message-error='Заполните обязательное поле: Номер свидетельства' data-datatype='char' value='" . $n_certificate . "'>"
						. "</div>"
					. "</div>"
				
					. "<div class='form-row'>"
						. "<div class='col-4 mb-1 text-right' style='vertical-align: center;'>"
							. "<label for='date_certificate_reg' class='text-muted' style='font-size: 13px;'><strong>Дата выдачи</strong></label>"
						. "</div>"
						. "<div class='col-2 mb-1'>"
							. "<input type='text' class='form-control form-control-sm black-text' id='date_certificate_reg' maxlength='10' placeholder='Дата' data-mandatory='true' data-message-error='Заполните обязательное поле: Дата выдачи' data-datatype='date' value='" . $date_certificate . "'>"
						. "</div>"
					. "</div>"
				
					. "<div class='form-row'>"
						. "<div class='col-4 mb-1 text-right' style='vertical-align: center;'>"
							. "<label for='org_certificate_reg' class='text-muted' style='font-size: 13px;'><strong>Кем выдано</strong></label>"
						. "</div>"
						. "<div class='col-6 mb-1'>"
							. "<select class='custom-select custom-select-sm black-text' id='org_certificate_reg' data-mandatory='true' data-message-error='Заполните обязательное поле: Кем выдано' data-datatype='number'>" . $spr_org_certificate . "</select>"
						. "</div>"
					. "</div>"
				
					. "<div class='form-row'>"
						. "<div class='col-4 mb-1 text-right' style='vertical-align: center;'>"
							. "<label for='comment_certificate_reg' class='text-muted' style='font-size: 13px;'><strong>Комментарий</strong></label>"
						. "</div>"
						. "<div class='col-6 mb-1'>"
							. "<input type='text' class='form-control form-control-sm black-text' id='comment_certificate_reg' maxlength='1000' placeholder='Комментарий' data-datatype='char' value='" . $comment_certificate . "'>"
						. "</div>"
					. "</div>"
				. "</div>"
				
				. "<div class='form-row'>"
				
					. "<div class='col-4 mb-1 text-right' style='vertical-align: center;'>"
						. "<label for='comment_certificate_reg' class='text-muted' style='font-size: 13px;'><strong>Эл. образ</strong></label>"
					. "</div>"
					
					. "<div class='col-4 mb-1 text-left'>"
						. "<div id='uploadFileContainer'>" . Functions::rendering_icon_file($path_to_file, $file_extension, $id, 6, true) . "</div>"
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