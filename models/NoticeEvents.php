<?php

namespace IcKomiApp\models;

use IcKomiApp\core\Model;
use IcKomiApp\core\Logic;
use IcKomiApp\core\Functions;
use IcKomiApp\lib\Database\DB;
use IcKomiApp\widgets\Directory;

class NoticeEvents extends Model {

	protected $table = 'notice_events';

	// Функция получения 
	public function get_list_notice($flg_html = true) {
		/*Session::start();
		$role = Session::get('role');
		$kodrai = Session::get('slugba');
		Session::commit();*/

		$role = 9;
		
		if($role == 9 || $role == 8) {
			$sql = "SELECT {table}.* FROM {table}
				 ORDER BY notice_status ASC";
		} else if($role == 3) {
			$sql = "SELECT {table}.* FROM {table}
				 WHERE {table}.notice_dostup=1
				 ORDER BY notice_status ASC";
		} else if($role == 2) {
			$sql = "SELECT {table}.* FROM {table}
				 WHERE {table}.notice_dostup=1 "
			 . " ORDER BY notice_status ASC";
		} else {
			return false;
		}
		
		if(($sql = preg_replace('/\{table\}/i', $this->table, $sql)) === NULL)
			return false;

		if(($data = DB::query($sql)) === false)
			return false;
		
		if(!$flg_html)
			return $data;

		$result_alert_html = $this->rendering_alert($data);
		return [ 'list_notice_events' => $result_alert_html];
	}


	// Внутренняя функция отрисовки
	private function rendering_alert($data) {
		$html = '';
		for($i = 0; $i < count($data); $i++) {

			$status_notice_class = $disclaimer_notice_class = '';
			if($data[$i]['notice_status'] == 1) {
				$status_notice_class = 'alert-danger';
				$disclaimer_notice_class = 'disclaimer-alert-danger';
			} else if($data[$i]['notice_status'] == 2) {
				$status_notice_class = 'alert-warning';
				$disclaimer_notice_class = 'disclaimer-alert-warning';
			} else {
				$status_notice_class = 'alert-info';
				$disclaimer_notice_class = 'disclaimer-alert-info';
			}
			
			$page = $data[$i]['notice_table_object'] . '?id=' . $data[$i]['notice_id_object'];
			$html .= "<div class='alert " . $status_notice_class . "' role='alert'>"
						. "<div class='" . $disclaimer_notice_class . " text-left'><strong class='notice-subsystem'>" . ($i+1) . ".&nbsp;Подсистема&nbsp;&laquo;" . $data[$i]['notice_subsystem'] . "&raquo;</strong><br>"
						. "<a href='" . $page . "' class='alert-link' target='_blank' title='Нажмите для того чтобы перейти к объекту'>" . $data[$i]['notice_object'] . "</a>&nbsp;" . $data[$i]['notice_text'] . "</div>"
					. "</div>";
		}
		return $html;
	}

	public function search($post, $flg_excel = -1) {
		$where_status = (empty($post['status'])) ? '' : " AND {table}.notice_status=" . addslashes($post['status']);
		//$subsystem = (empty($post['subsystem'])) ? '' :  " {table}.notice_status=" . addslashes($post['subsystem']);

		$role = 9;
		
		if($role == 9 || $role == 8) {
			$sql = "SELECT {table}.* FROM {table} "
				. " WHERE {table}.notice_dostup IN (1,2) " . $where_status
				. " ORDER BY notice_status ASC";
		} else if($role == 3) {
			$sql = "SELECT {table}.* FROM {table} "
				. " WHERE {table}.notice_dostup=1 " . $where_status
				. " ORDER BY notice_status ASC";
		} else if($role == 2) {
			$sql = "SELECT {table}.* FROM {table} "
			 	. " WHERE {table}.notice_dostup=1 " . $where_status
			 	. " ORDER BY notice_status ASC";
		} else {
			return false;
		}
		
		if(($sql = preg_replace('/\{table\}/i', $this->table, $sql)) === NULL)
			return false;

		if(($data = DB::query($sql)) === false)
			return false;

		$result_alert_html = $this->rendering_alert($data);
		return $result_alert_html;
	}
}