<?php

namespace IcKomiApp\models;

use IcKomiApp\core\Model;
use IcKomiApp\lib\Database\DB;
use IcKomiApp\core\Logic;

use Mpdf\QrCode\QrCode;
use Mpdf\QrCode\Output;

use Picqer\Barcode\BarcodeGeneratorPNG;
//use Mpdf;

class TicketCard extends Model {
	protected $table = 'D_TICKET_ADM';

	protected $sql_get_record = "SELECT {table}.*, L_DIRECTORY.text as PAYER_ADDRESS_KODRAI_TEXT  FROM {table} "
		. " LEFT JOIN L_DIRECTORY ON L_DIRECTORY.kod={table}.PAYER_ADDRESS_KODRAI AND L_DIRECTORY.nomer=2 "
		. " WHERE {table}.id={id}";

	protected $field = ['LASTNAME' => ['type' => 'char', 'maxlength' => '350'],
						'FIRSTNAME' =>  ['type' => 'char', 'maxlength' => '350'],
						'MIDDLENAME' =>  ['type' => 'char', 'maxlength' => '350'],
						'BIRTHDATE' =>  ['type' => 'date'],
						'PAYERINN' =>  ['type' => 'number'],
						'PAYER_ADDRESS_KODRAI' =>  ['type' => 'number'],
						'PAYER_ADDRESS_CITY' =>  ['type' => 'char', 'maxlength' => '50'],
						'PAYER_ADDRESS_STREET' =>  ['type' => 'char', 'maxlength' => '50'],
						'PAYER_ADDRESS_HOUSE' =>  ['type' => 'char', 'maxlength' => '10'],
						'PAYER_ADDRESS_KORP' =>  ['type' => 'char', 'maxlength' => '10'],
						'PAYER_ADDRESS_FLAT' =>  ['type' => 'char', 'maxlength' => '10'],
						'KODRAI' =>  ['type' => 'number'],
						'ST' =>  ['type' => 'number'],
						'NUMBER_POST' =>  ['type' => 'char', 'maxlength' => '6'],
						'DATE_POST' =>  ['type' => 'date'],
						'UIN' =>  ['type' => 'char', 'maxlength' => '30'],
						'CBC' =>  ['type' => 'char', 'maxlength' => '20'],
						'SUM' =>  ['type' => 'number'],
						'OKTMO' =>  ['type' => 'number'],
						'TYPE_BLANK' =>  ['type' => 'number']
					];

	protected $service_field = [
		'PAYEEINN' => ['type' => 'char', 'value' => '1101093814'],
		'KPP' => ['type' => 'char', 'value' => '110101001'],
		'PERSONALACC' => ['type' => 'char', 'value' => '40101810000000010004'],
		'NAME' => ['type' => 'char', 'value' => 'УФК по Республике Коми (ГКУ РУ "Центр ОДМЮ", л/с 04072D00371)'],
		'BANKNAME' => ['type' => 'char', 'value' => 'Отделение-НБ Республика Коми г. Сыктывкар'],
		'BIC' => ['type' => 'char', 'value' => '048702001'],
		'CORRESPACC' => ['type' => 'char', 'value' => '']
	];

	protected $logic = [
		['number' => '1',
		 'type' => '1',
		 'operation' => '!',
		 'field1' => 'LASTNAME',
		 'message' => 'Не заполнен обязательный реквизит: Фамилия!',
			],
		['number' => '2',
		 'type' => '1',
		 'operation' => '!',
		 'field1' => 'FIRSTNAME',
		 'message' => 'Не заполнен обязательный реквизит: Имя!',
			],
		['number' => '3',
		 'type' => '1',
		 'operation' => '!',
		 'field1' => 'MIDDLENAME',
		 'message' => 'Не заполнен обязательный реквизит: Отчетсво!',
			],

		['number' => '4',
		 'type' => '1',
		 'operation' => '!',
		 'field1' => 'PAYER_ADDRESS_KODRAI',
		 'message' => 'Не заполнен обязательный реквизит: Адрес плательщика (район)!',
			],
		['number' => '5',
		 'type' => '1',
		 'operation' => '!',
		 'field1' => 'PAYER_ADDRESS_CITY',
		 'message' => 'Не заполнен обязательный реквизит: Адрес плательщика (населенный пункт)!',
			],
		['number' => '6',
		 'type' => '1',
		 'operation' => '!',
		 'field1' => 'PAYER_ADDRESS_STREET',
		 'message' => 'Не заполнен обязательный реквизит: Адрес плательщика (улица)!',
			],
		['number' => '7',
		 'type' => '1',
		 'operation' => '!',
		 'field1' => 'PAYER_ADDRESS_HOUSE',
		 'message' => 'Не заполнен обязательный реквизит: Адрес плательщика (дом)!',
			],
		['number' => '8',
		 'type' => '1',
		 'operation' => '!',
		 'field1' => 'KODRAI',
		 'message' => 'Не заполнен обязательный реквизит: Район!',
			],
		['number' => '9',
		 'type' => '1',
		 'operation' => '!',
		 'field1' => 'ST',
		 'message' => 'Не заполнен обязательный реквизит: Статья!',
			],

			['number' => '10',
		 'type' => '1',
		 'operation' => '!',
		 'field1' => 'UIN',
		 'message' => 'Не заполнен обязательный реквизит: УИН!',
			],
			['number' => '11',
		 'type' => '1',
		 'operation' => '!',
		 'field1' => 'TYPE_BLANK',
		 'message' => 'Не заполнен обязательный реквизит: Тип бланка!',
			],
		['number' => '12',
		 'type' => '1',
		 'operation' => '=',
		 'field1' => 'TYPE_BLANK',
		 'value1' => '5',
		 'true' => '13',
		 'message' => 'Первая проверка 1',
			],

			['number' => '13',
		 'type' => '2',
		 'operation' => 'LENGTH=',
		 'field1' => 'UIN',
		 'value1' => '25',
		 'message' => 'Длина УИН должна составлять 25 знаков!',
			],

			['number' => '14',
		 'type' => '1',
		 'operation' => '!=',
		 'field1' => 'TYPE_BLANK',
		 'value1' => '5',
		 'true' => '15',
		 'message' => 'Первая проверка 2',
			],

			['number' => '15',
		 'type' => '2',
		 'operation' => 'LENGTH=',
		 'field1' => 'UIN',
		 'value1' => '20',
		 'message' => 'Длина УИН должна составлять 20 знаков!',
			],
		];
	
	// Функция поиска
	public function search($post) {
		$where = $page = $excel = '';
		foreach ($post as $field => $value) {
			if($field == 'page') {
				$page = $value;
				continue;
			}

			if($field == 'excel') {
				$excel = $value;
				continue;
			}

			if(!array_key_exists($field, $this->field))
				continue;

			if(mb_strlen(trim($value)) == 0)
				continue;

			if($value == '0')
				continue;

			$where .= (mb_strlen($where) == 0) ? $this->get_value_for_search($field, $value, $this->field, $this->table) : " AND " . $this->get_value_for_search($field, $value, $this->field, $this->table);
		}

		if(mb_strlen(trim($where)) != 0)
			$where = " WHERE " . $where;

		$sql = "SELECT " . $this->table . ".*, x1.text as KODRAI_TEXT, x2.text as ST_TEXT "
			 . " FROM " . $this->table
			 . " LEFT JOIN L_DIRECTORY x1 ON x1.kod=" . $this->table . ".KODRAI AND x1.nomer=2 "
			 . " LEFT JOIN L_DIRECTORY x2 ON x2.kod=" . $this->table . ".ST AND x2.nomer=3 "
			 . $where
			 . " ORDER BY " . $this->table . ".DATE_POST ";

		if(($data = DB::query($sql, DB::SELECT)) === false)
			return false;

		return $this->generate_table($data, $page);
	}

	/*
	 	Функция отрисовывания таблицы
	 	$data - массив с данными
	 	$page - номер страны на веб-форме
	*/
	private function generate_table($data, $page = 0) {

		$record_limit = (($page - 1) < 0) ? 0 : $page - 1;
		$record_tail_limit = $record_limit * $this->list_items_for_one_page + 1;
		$record_head_limit = $record_tail_limit + $this->list_items_for_one_page - 1;

		$page_left = $page_right = 0;
		$page_left_disabled = $page_right_disabled = '';
		if(($page - 1) <= 0) {
			$page_left = 1;
			$page_left_disabled = ' disabled ';
		} else {
			$page_left = $page - 1;
		}

		$x = intdiv(count($data), $this->list_items_for_one_page) + (((count($data) % $this->list_items_for_one_page) > 0) ? 1 : 0);

		if(($page + 1) < $x) {
			$page_right = $page + 1;
		} else if (($page + 1) == $x){
			$page_right = $x;
		} else if(($page + 1) > $x){
			$page_right_disabled = ' disabled ';
			$page_right = $x;
		}

		$html = "";
		if(count($data) == 0) {
			$html = "<p>Сведений в базе данных не обнаружено</p>";
		} else {

			$style_border = "style='vertical-align: middle; border: 1px solid gray;'";

			$html_table = "<table class='table table-striped table-sm table-hover text-center' style='font-size: 13px;'>";
			$html_table .= "<tr class='table-info'>"
					. "<th " . $style_border . " scope='col'>№ п/п</th>"
					. "<th " . $style_border . " scope='col'>ФИО</th>"
					. "<th " . $style_border . " scope='col'>УИН</th>"
					. "<th " . $style_border . " scope='col'>Район</th>"
					. "<th " . $style_border . " scope='col'>ОКТМО</th>"
					. "<th " . $style_border . " scope='col'>Статья</th>"
					. "<th " . $style_border . " scope='col'>КБК</th>"
					. "<th " . $style_border . " scope='col'>Номер<br>постановления</th>"
					. "<th " . $style_border . " scope='col'>Дата<br>постановления</th>"
				. "</tr>";
			// Строим список водителей
			$j = 0;
			for($i = 0; $i < count($data); $i++) {
				
				$link = "window.open('http://" . $_SERVER['HTTP_HOST'] . "/ticket?id=" . $data[$i]['ID'] . "')";
				$j++;
				if(($j >= $record_tail_limit) && ($j <= $record_head_limit)) {

					$html_table .=  "<tr style='cursor: pointer;' onclick=" . $link . ">"
							. "<td " . $style_border . ">" . ($i + 1) . "</td>"
							. "<td " . $style_border . ">" . $data[$i]['LASTNAME'] . "&nbsp;" . $data[$i]['FIRSTNAME'] . "&nbsp;" . $data[$i]['MIDDLENAME'] . "</td>"
							. "<td " . $style_border . ">" . $data[$i]['UIN'] . "</td>"
							. "<td " . $style_border . ">" . $data[$i]['KODRAI_TEXT'] . "</td>"
							. "<td " . $style_border . ">" . $data[$i]['OKTMO'] . "</td>"
							. "<td " . $style_border . ">" . $data[$i]['ST_TEXT'] . "</td>"
							. "<td " . $style_border . ">" . $data[$i]['CBC'] . "</td>"
							. "<td " . $style_border . ">" . $data[$i]['NUMBER_POST'] . "</td>"
							. "<td " . $style_border . ">" . $data[$i]['DATE_POST'] . "</td>"
							. "</tr>";
				}

				if($j > $record_head_limit)
					break;
			}
			$html_table .= "</table>";

			// Class button
			$class_btn = 'btn-search';
			
			// Подсчет количества уникальных значений в массиве для переключения между страницами
			$count_uniq_array = count(array_count_values(array_map(function($a) { return $a['ID']; }, $data)));
			
			$text_list = "Записи:&nbsp;" . $record_tail_limit . '&nbsp;-&nbsp;' . (($j > $record_head_limit) ? $record_head_limit : $j) . "&nbsp;из&nbsp;" . $count_uniq_array;
			$html_bottom_text = "<div class='text-right' style='display: block; font-size: 15px;'>" . $text_list . "</div>";
	
			$html_btn_top = "<div class='col btn-group text-right' role='group' style='display: block; padding: 0px;'>"
				. "<button type='button' class='btn btn-sm btn-secondary mb-1 mr-2 " . $class_btn . "' title='Перейти к предыдущим записям' data-excel='-1' data-page='" . $page_left . "'" . $page_left_disabled . "><span class='fa fa-mail-reply'></span></button>"
				. "<label>" . $text_list . "</label>";

			$html_btn_bottom = "<div class='col btn-group text-center' role='group' style='display: block;'>"
				. "<button type='button' class='btn btn-secondary mb-1 mr-2 " . $class_btn . "' title='Перейти к предыдущим записям' data-excel='-1' data-page='" . $page_left . "'" . $page_left_disabled . "><span class='fa fa-mail-reply'>&nbsp;</span>Предыдущие записи</button>";
			
			$html_btn_top .= "<button type='button' class='btn btn-sm btn-secondary mb-1 ml-2 " . $class_btn . "' title='Перейти к следующим записям' data-excel='-1' data-page='" . $page_right . "'" . $page_right_disabled . "><span class='fa fa-mail-forward'></span></button>";
			$html_btn_bottom .= "<button type='button' class='btn btn-secondary mb-1 ml-2 " . $class_btn . "' title='Перейти к следующим записям' data-excel='-1' data-page='" . $page_right . "'" . $page_right_disabled . ">Следующие записи&nbsp;<span class='fa fa-mail-forward'></span></button>";

			$html_btn_top .= "</div>";
			$html_btn_bottom .= "</div>";
			
			$html = $html_btn_top . $html_table . $html_bottom_text . $html_btn_bottom;
		}
		return $html;
	}

	/*
		Функция формирования квитанции на штраф в формате PDF
		$post - массив $_POST
	*/
	public function get_pdf_document($post) {
		if(!is_array($post) || empty($post))
			return false;

		if(empty($post['nsyst']))
			return false;

		$id = addslashes($post['nsyst']);

		// Получаем запись о квитанции из базы данных
		if(($data = $this->get_record($id)) === false)
			return false;

		// Формируем PDF квитанцию
		return $this->generate_pdf_document($data);
	}

	/*
		Формирование непосредственно PDF документа
		$data - массив с данными о квитанции, полученный из базы данных
		Возвращаемое значение: строка в base64 формате, в которую сгенерирована PDF квитанция
	*/
	private function generate_pdf_document($data) {
		setlocale(LC_CTYPE, 'ru_RU.UTF8');

		$uin = $this->formating_uin($this->get_val($data, 'UIN'));
		$payer_address = $this->get_payer_address($data);

		$purpose = (($this->get_val($data, 'TYPE_BLANK') == 4) || ($this->get_val($data, 'TYPE_BLANK')) == 5 ) ? 'ЗА ПРОТОКОЛ №' : ' ЗА ПОСТАНОВЛЕНИЕ (ПРОТОКОЛ) №';
		$purpose .= $this->get_val($data, 'NUMBER_POST') . ' ОТ ' . $this->get_val($data, 'DATE_POST');

		$mpdf = new \Mpdf\Mpdf(['default_font' => 'dejavusanscondensed']);
		$mpdf->autoScriptToLang = true;
		$mpdf->baseScript = 1;
		$mpdf->autoLangToFont = true;

		$mpdf->SetDocTemplate('template/ticket_1.pdf', 0);
		$mpdf->AddPage();
		
		$mpdf->SetFontSize(13);
		$mpdf->setFont('dejavusanscondensed', 'B');
		$mpdf->WriteText(75, 16, $uin);
		$mpdf->WriteText(75, 109.5, $uin);

		$mpdf->SetFontSize(9);
		$mpdf->setFont('dejavusanscondensed', 'N');
		$mpdf->WriteText(70, 21.5, $this->service_field['PAYEEINN']['value']);
		$mpdf->WriteText(105, 21.5, $this->service_field['KPP']['value']);
		$mpdf->WriteText(75, 26.5, $this->service_field['PERSONALACC']['value']);
		$mpdf->WriteText(60, 32.5, $this->service_field['NAME']['value']);
		$mpdf->WriteText(60, 39.5, $this->service_field['BANKNAME']['value']);
		$mpdf->WriteText(70, 46, $this->service_field['BIC']['value']);
		$mpdf->WriteText(135, 46, $this->service_field['CORRESPACC']['value']);
		$mpdf->WriteText(70, 52, $this->get_val($data, 'CBC'));
		$mpdf->WriteText(135, 52, $this->get_val($data, 'OKTMO'));

		$mpdf->WriteText(85, 57.5, mb_strtoupper($this->get_val($data, 'LASTNAME') . ' ' . $this->get_val($data, 'FIRSTNAME') . ' ' . $this->get_val($data, 'MIDDLENAME')));
		$mpdf->WriteText(60, 62.5, $purpose);
		$mpdf->WriteText(160, 68, $this->get_val($data, 'SUM'));
		$mpdf->WriteText(85, 76, mb_strtoupper($this->get_val($data, 'LASTNAME') . ' ' . $this->get_val($data, 'FIRSTNAME') . ' ' . $this->get_val($data, 'MIDDLENAME')));
		$mpdf->WriteText(95, 83.5, $this->get_val($data, 'PAYERINN'));
		$mpdf->WriteText(95, 91, $payer_address);

		$mpdf->WriteText(70, 115.7, $this->service_field['PAYEEINN']['value']);
		$mpdf->WriteText(105, 115.7, $this->service_field['KPP']['value']);
		$mpdf->WriteText(75, 120.5, $this->service_field['PERSONALACC']['value']);
		$mpdf->WriteText(60, 127, $this->service_field['NAME']['value']);
		$mpdf->WriteText(60, 134, $this->service_field['BANKNAME']['value']);
		$mpdf->WriteText(70, 141, $this->service_field['BIC']['value']);
		$mpdf->WriteText(135, 141, $this->service_field['CORRESPACC']['value']);
		$mpdf->WriteText(70, 146.7, $this->get_val($data, 'CBC'));
		$mpdf->WriteText(135, 147, $this->get_val($data, 'OKTMO'));

		$mpdf->WriteText(85, 152.5, mb_strtoupper($this->get_val($data, 'LASTNAME') . ' ' . $this->get_val($data, 'FIRSTNAME') . ' ' . $this->get_val($data, 'MIDDLENAME')));
		$mpdf->WriteText(60, 157.5, $purpose);
		$mpdf->WriteText(160, 163, $this->get_val($data, 'SUM'));
		$mpdf->WriteText(85, 171, mb_strtoupper($this->get_val($data, 'LASTNAME') . ' ' . $this->get_val($data, 'FIRSTNAME') . ' ' . $this->get_val($data, 'MIDDLENAME')));
		$mpdf->WriteText(95, 179, $this->get_val($data, 'PAYERINN'));
		$mpdf->WriteText(95, 186.5, $payer_address);

		$qrcode_image = $this->generate_qr_code($data);
		$mpdf->Image($qrcode_image, 10, 19, 45);
		$mpdf->Image($qrcode_image, 10, 115, 45);

		$mpdf->SetFontSize(6);
		$barcode_image = $this->generate_barcode($data);
		$mpdf->Image($barcode_image, 135, 19, 60, 8);
		$mpdf->WriteText(145, 29, 'УИН: ' . $this->get_val($data, 'UIN'));
		$mpdf->Image($barcode_image, 135, 113, 60, 8);
		$mpdf->WriteText(145, 123.5, 'УИН: ' . $this->get_val($data, 'UIN'));

		$temp_ticket_file = tempnam(sys_get_temp_dir(), 'ticket-');
		$mpdf->Output($temp_ticket_file);
		$base64_str = base64_encode(file_get_contents($temp_ticket_file));

		$this->remove_file($qrcode_image);
		$this->remove_file($barcode_image);
		$this->remove_file($temp_ticket_file);

		return $base64_str;
	}

	/*
		Функция генерации QR-кода
		$data - массив с данными
		Возвращаемое значение: путь к файлу с qr-кодом
	*/
	private function generate_qr_code($data) {
		$qrcode_image = tempnam(sys_get_temp_dir(), 'qrcode-');

		$purpose = $this->get_val($data, 'LASTNAME')
				 . " " . $this->get_val($data, 'FIRSTNAME')
				 . " " . $this->get_val($data, 'MIDDLENAME')
				 . " ЗА ПРОТОКОЛ №" . $this->get_val($data, 'NUMBER_POST') . " ОТ " . $this->get_val($data, 'DATE_POST');

		$qrString = 'ST00012|Name=' . $this->get_val($data, 'NAME')
				  . "|PersonalAcc=" . $this->get_val($data, 'PERSONALACC')
				  . "|BankName=" . $this->get_val($data, 'BANKNAME')
				  . "|BIC=" . $this->get_val($data, 'BIC')
				  . "|CorrespAcc=0"
				  . "|PayeeINN=" . $this->get_val($data, 'PAYEEINN')
				  . "|KPP=" . $this->get_val($data, 'KPP')
				  . "|CBC=" . $this->get_val($data, 'CBC')
				  . "|OKTMO=" .$this->get_val($data, 'OKTMO')
				  . "|LastName=" . $this->get_val($data, 'LASTNAME')
				  . "|FirstName=" . $this->get_val($data, 'FIRSTNAME')
				  . "|MiddleName=" . $this->get_val($data, 'MIDDLENAME')
				  . "|PayerAddress=0"
				  . "|PayerIdType=0"
				  . "|PayerIdNum=0"
				  . "|PayerINN=0"
				  . "|UIN=" . $this->get_val($data, 'UIN')
				  . "|Purpose=" . $purpose
				  . "|Sum=" . $this->get_val($data, 'SUM')
				  . "|TechCode=3";

		$qrCode = new QrCode($qrString);
		$output = new Output\Png();

		file_put_contents($qrcode_image, $output->output($qrCode, 200, [255, 255, 255], [0, 0, 0]));
		return $qrcode_image;
	}

	/*
		Функция генерации штрих-кода (используется поле УИН)
		$data - массив с данными
		Возвращаемое значение: путь к файлу со штрих-кодом
	*/
	private function generate_barcode($data) {
		$barcode_image = tempnam(sys_get_temp_dir(), 'barcode-');
		$barcode = new BarcodeGeneratorPNG();
		file_put_contents($barcode_image, $barcode->getBarcode($this->get_val($data, 'UIN'), $barcode::TYPE_CODE_128));
		return $barcode_image;
	}

	/*
		Удаление файла
		$path_file - файл, который необходимо удалить
	*/
	private function remove_file($path_file) {
		if(file_exists($path_file))
			unlink($path_file);
	}


	/*
		Проверка на существование элемента
		$data - массив в котором необходимо проверить налиичие элемента
		$item - название элемента
		Возвращаемое значение: значение элемента если существует или пустую строку
	*/
	private function get_val($data, $item) {
		if(!is_array($data))
			return '';

		$value = '';
		try {
			$value = (empty($data[0][$item])) ? '' : $data[0][$item];
		} catch(Exception $e) {
			$value = '';
		}

		return $value;
	}

	/*
		Функция привода УИН-а в необходимый формат с пробелами
		$uin - строка с УИН-ом
		Возвращаемое значение: УИН сгенерированный в формате с пробелами
	*/
	private function formating_uin($uin) {
		if(mb_strlen($uin) == 0)
			return $uin;

		if(mb_strlen($uin) < 25)
			for($i = (mb_strlen($uin) - 1); $i < 25; $i++)
				$uin .= ' ';

		$new_uin = '';
		try {
			$new_uin = $uin[ 0] . $uin[ 1] . $uin[ 2] . ' '
					 . $uin[ 3] . $uin[ 4] . $uin[ 5] . ' '
					 . $uin[ 6] . $uin[ 7] . $uin[ 8] . ' '
					 . $uin[ 9] . $uin[10] . $uin[11] . ' '
					 . $uin[12] . $uin[13] . $uin[14] . ' '
					 . $uin[15] . $uin[16] . $uin[17] . ' '
					 . $uin[18] . $uin[19] . $uin[20] . ' '
					 . $uin[21] . $uin[22] . $uin[23] . $uin[24];
		} catch(Exception $e) {
			$new_uin = $uin;
		}
		return $new_uin;
	}

	/*
	*/
	private function get_payer_address($data) {

		$address = '';

		if(preg_match('/г\./i', $this->get_val($data, 'PAYER_ADDRESS_KODRAI_TEXT')) == 1) {
			$area1 = trim(preg_replace('/г\./i', '', $this->get_val($data, 'PAYER_ADDRESS_KODRAI_TEXT')));
			//$area2 = trim(preg_replace('г\.', '', $data['PAYER_ADDRESS_CITY']));

			if(preg_match('/' . $area1 . '/i', $this->get_val($data, 'PAYER_ADDRESS_CITY')) == 1)
				$address = $this->get_val($data, 'PAYER_ADDRESS_KODRAI_TEXT');
			else
				$address = $this->get_val($data, 'PAYER_ADDRESS_KODRAI_TEXT')
					. ((mb_strlen($this->get_val($data, 'PAYER_ADDRESS_CITY')) == 0) ? '' : ', ' . $this->get_val($data, 'PAYER_ADDRESS_CITY'));

			$address .= ((mb_strlen($this->get_val($data, 'PAYER_ADDRESS_STREET')) == 0) ? '' : ', ' . $this->get_val($data, 'PAYER_ADDRESS_STREET'))
				. ((mb_strlen($this->get_val($data, 'PAYER_ADDRESS_HOUSE')) == 0 ) ? '' : ', ' . $this->get_val($data, 'PAYER_ADDRESS_HOUSE'))
				. ((mb_strlen($this->get_val($data, 'PAYER_ADDRESS_KORP')) == 0) ? '' : ', ' . $this->get_val($data, 'PAYER_ADDRESS_KORP'))
				. ((mb_strlen($this->get_val($data, 'PAYER_ADDRESS_FLAT')) == 0) ? '' : ', ' . $this->get_val($data, 'PAYER_ADDRESS_FLAT'));
		} else {
			$address = $this->get_val($data, 'PAYER_ADDRESS_KODRAI_TEXT')
				. ((mb_strlen($this->get_val($data, 'PAYER_ADDRESS_CITY')) == 0) ? '' : ', ' . $this->get_val($data, 'PAYER_ADDRESS_CITY'))
				. ((mb_strlen($this->get_val($data, 'PAYER_ADDRESS_STREET')) == 0) ? '' : ', ' . $this->get_val($data, 'PAYER_ADDRESS_STREET'))
				. ((mb_strlen($this->get_val($data, 'PAYER_ADDRESS_HOUSE')) == 0 ) ? '' : ', ' . $this->get_val($data, 'PAYER_ADDRESS_HOUSE'))
				. ((mb_strlen($this->get_val($data, 'PAYER_ADDRESS_KORP')) == 0) ? '' : ', ' . $this->get_val($data, 'PAYER_ADDRESS_KORP'))
				. ((mb_strlen($this->get_val($data, 'PAYER_ADDRESS_FLAT')) == 0) ? '' : ', ' . $this->get_val($data, 'PAYER_ADDRESS_FLAT'));
		}

		return $address;
	}

}