<?php

namespace IcKomiApp\lib\excel;

use IcKomiApp\core\Functions;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

/*
	Служебный класс для формирования Excel-документов
*/
class GenerateExcel {

	/*
		Функция генерации Excel-документа
		В данную функцию необходимо передать массивы для того чтобы функция сформировала Excel-документ
		$prefix - префикс для формирования файла
		$worksheet - название листа
		$header - массив с названиями столбцов таблицы
		$body - массив с перечислением полей которые есть в $data. $body = array(['{index}'], ['fam'], ['dd', 'date']). Поле {index} означает порядковый номер.
		В качестве значений массива передаются массивы. Первым параметром идет название, вторым тип. По умолчанию значение просто вставляется, если тип "date" -> о конвертируется в дату
		$data - ассоциативный массив с данными. $data[$i]['fam']
	*/
	public static function generate_excel_document($prefix, $worksheet, $header, $body, $data) {
		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();
		$sheet->setTitle($worksheet);
		
		// Переменные для задания стилей
		$font_header = array('font' => array('size' => 14, 'name' => 'TimesNewRoman'),
							 'alignment' => array('horizontal' => Alignment::HORIZONTAL_CENTER),
							 'borders' => array('outline' => array('borderStyle' => Border::BORDER_THIN)),
							 'fill' => array('fillType' => Fill::FILL_SOLID, 'startColor' => array('rgb' => 'A9A9A9')));

		$font = array('font' => array('size' => 10, 'name' => 'TimesNewRoman'),
					  'alignment' => array('horizontal' => Alignment::HORIZONTAL_CENTER),
					  'borders' => array('outline' => array('borderStyle' => Border::BORDER_THIN)));
	
		for($i = 0; $i < count($header); $i++) {
			$cell = self::get_cell($i);
			$cell_number = $cell . "1";
			
			$sheet->setCellValue($cell_number, $header[$i]);
			$sheet->getStyle($cell_number)->applyFromArray($font_header);
			$sheet->getColumnDimension($cell)->setAutoSize(true);
		}
		
		for($i = 0, $y = 2; $i < count($data); $i++, $y++) {
			
			for($j = 0; $j < count($body); $j++) {
				$cell = self::get_cell($j);
				$cell_number = $cell . $y;
				
				if($body[$j][0] == '{index}') {
					$sheet->setCellValue($cell_number, ($i + 1));
				} else {
					if(!empty($body[$j][1])) {
						if($body[$j][1] == 'date')
							$sheet->setCellValueExplicit($cell_number, Functions::convertToDate($data[$i][$body[$j][0]]), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
						else
							$sheet->setCellValueExplicit($cell_number, $data[$i][$body[$j][0]], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
					} else {
						$sheet->setCellValueExplicit($cell_number, $data[$i][$body[$j][0]], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
					}
				}

				$sheet->getStyle($cell_number)->applyFromArray($font);
				$sheet->getColumnDimension($cell)->setAutoSize(true);
			}
		}
		
		$writer = new Xlsx($spreadsheet);
		$temp_path_to_file = tempnam(sys_get_temp_dir(), $prefix . '-');
		$temp = $temp_path_to_file;
		$temp_path_to_file .= '.xlsx';
		$writer->save($temp_path_to_file);
		$temp_path_to_file = preg_replace('/\\\/', '/', $temp_path_to_file);
		$html = "<script> downloadFile('$temp_path_to_file'); </script>";
		return $html;
	}
	
	// Функция генерации ячейки Excel документа
	private static function get_cell($index) {
		$alphabet = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
		
		$count = count($alphabet);
		$index_cell = $count;
		$length_cell = 1;
		$exp = 1;
		while($index >= $index_cell) {
			$exp++;
			$index_cell = pow($count, $exp);
			$index_cell += $count;
			$length_cell++;
		}
	
		$cell = '';
		$exp = $length_cell;
		for($i = 1; $i <= $length_cell; $i++) {
			if($i == $length_cell) {
				$c = ($index < $count) ? $index : $index - ($count * intdiv($index, $count));
			} else {
				if($index < $count) {
					$c = $index;
				} else {
					$c = intdiv($index, pow($count, ($exp - 1))) - 1;
					$c = ($c < $count) ? $c : $c - $count;
					$index = $index - pow($count, ($exp - 1));
					$exp--;
				}
			}
			$cell .= $alphabet[$c];
		}
		return $cell;
	}
}
