<?php

namespace IcKomiApp\widgets;

use IcKomiApp\lib\Database\DB;

class Directory {

	protected static $table_dictionary = 's2i_klass';

	public static function get_directory($number_directory, $selected_value = '', $html = true, $order = 'text', $alias = '') {
		if((mb_strlen($number_directory) == 0) || empty($number_directory) || ($number_directory === null))
			return false;

		$order_by = ((mb_strlen($order) == 0) || empty($order) || ($order === null)) ? '' : ' ORDER BY ' . $order;
		$sql = 'SELECT * FROM ' . self::$table_dictionary . " WHERE nomer=" . $number_directory . " " . $order_by;

		if((mb_strlen($alias) == 0) || empty($alias) || ($alias === null)) {
			if(($data = DB::query($sql)) === false)
				return false;	
		} else {
			if(($data = DB::query_db_alias($sql, $alias)) === false)
				return false;
		}

		if($html) {
			$dictionary = "<option value='0'></option>";

			$selected_value = (empty($selected_value)) ? 0 : $selected_value;
			for($i = 0; $i < count($data); $i++) {
				if($data[$i]['kod'] == $selected_value)
					$dictionary .= "<option value='" . $data[$i]['kod'] . "' selected>" . $data[$i]['text'] . "</option>";
				else
					$dictionary .= "<option value='" . $data[$i]['kod'] . "'>" . $data[$i]['text'] . "</option>";
			}
			return $dictionary;
		}

		return $data;
	}

	public static function get_directory_where($number_directory, $where = '', $selected_value = '', $html = true, $order = 'text', $alias = '') {
		if((mb_strlen($number_directory) == 0) || empty($number_directory) || ($number_directory === null))
			return false;

		$order_by = ((mb_strlen($order) == 0) || empty($order) || ($order === null)) ? '' : ' ORDER BY ' . $order;
		$sql = 'SELECT * FROM ' . self::$table_dictionary . " WHERE nomer=" . $number_directory . ' AND ' . $where . $order_by;

		if((mb_strlen($alias) == 0) || empty($alias) || ($alias === null)) {
			if(($data = DB::query($sql)) === false)
				return false;	
		} else {
			if(($data = DB::query_db_alias($sql, $alias)) === false)
				return false;
		}

		if($html) {
			$dictionary = "<option value='0'></option>";

			for($i = 0; $i < count($data); $i++) {
				if($data[$i]['kod'] == $selected_value)
					$dictionary .= "<option value='" . $data[$i]['kod'] . "' selected>" . $data[$i]['text'] . "</option>";
				else
					$dictionary .= "<option value='" . $data[$i]['kod'] . "'>" . $data[$i]['text'] . "</option>";
			}
			return $dictionary;
		}

		return $data;
	}

	public static function get_multiple_directory($number_directory, $selected_value = [], $html = true, $order = 'nomer', $alias = '') {
		if(!is_array($number_directory) || empty($number_directory) || ($number_directory === null))
			return false;

		if(!is_array($selected_value))
			return false;

		$order_by = " ORDER BY nomer " . ((mb_strlen($order) == 0) || empty($order)) ? '' : ',' . $order;
		
		$list_numbers = "";
		for($i = 0; $i < count($number_directory); $i++)
			$list_numbers .= (mb_strlen($list_numbers) == 0) ? $number_directory[$i] : "," . $number_directory[$i];
		
		$sql = "SELECT * FROM " . self::$table_dictionary . " WHERE nomer IN (" . $list_numbers . ") " . $order_by;

		if((mb_strlen($alias) == 0) || empty($alias) || ($alias === null)) {
			if(($data = DB::query($sql)) === false)
				return false;
		} else {
			if(($data = DB::query($sql)) === false)
				return false;
		}

		$dictionary = array();

		if($html) {
			for($i = 0; $i < count($number_directory); $i++)
				$dictionary[$number_directory[$i]] = "<option value='0'></option>";

			for($i = 0; $i < count($data); $i++) {
				$value = empty($selected_value[$data[$i]['nomer']]) ? '' : $selected_value[$data[$i]['nomer']];
				if($value == $data[$i]['kod'])
					$dictionary[$data[$i]['nomer']] .= "<option value='" . $data[$i]['kod'] . "' selected>" . $data[$i]['text'] . "</option>";
				else
					$dictionary[$data[$i]['nomer']] .= "<option value='" . $data[$i]['kod'] . "'>" . $data[$i]['text'] . "</option>";
			}
		} else {

			for($i = 0; $i < count($number_directory); $i++)
				$dictionary[$number_directory[$i]] = array();

			for($i = 0; $i < count($data); $i++)
				array_push($dictionary[$data[$i]['nomer']], $data[$i]);
		}

		return $dictionary;
	}

	/*
		Получения списка всехсправочников
	*/
	public static function get_list_directory($html = true) {
		$sql = "SELECT * FROM spr_list WHERE type = 0";
		if(($data = DB::query($sql)) === false)
			return false;

		$directory = [];

		if($html) {
			$directory = "<option value='0'></option>";
			for($i = 0; $i < count($data); $i++)
				$directory .= "<option value='" . $data[$i]['nomer'] . "'>" . $data[$i]['text'] . "</option>";
		} else {
			for($i = 0; $i < count($data); $i++)
				array_push($directory[$data[$i]['nomer']], $data[$i]);
		}

		return $directory;
	}

	public static function get_directory_car($selected_value = '') {
		/*Session::start();
		$role = Session::get('role');
		$kodrai = Session::get('slugba');
		Session::commit();

		if($role == 8 || $role == 9) {
			$sqlQuery = "SELECT a.id as kod, IFNULL(a.gos_znak, '') as text, IFNULL(x1.text, '') as model_ts, IFNULL(x2.text, '') as marka_ts, IFNULL(x3.text, '') as slugba_text,"
				. " IFNULL(x4.text, '') as kodrai_text, a.kodrai, a.slugba, 1 as number_spr FROM cars a "
				. " LEFT JOIN s2i_klass x1 ON x1.kod=a.model AND x1.nomer=4 "
				. " LEFT JOIN s2i_klass x2 ON x2.kod=a.marka AND x2.nomer=3 "
				. " LEFT JOIN s2i_klass x3 ON x3.kod=a.slugba AND x3.nomer=1 "
				. " LEFT JOIN s2i_klass x4 ON x4.kod=a.kodrai AND x4.nomer=11 "
				. " UNION ALL "
				. " SELECT kod, text, '', '', '', '', 0, 0, 2 FROM s2i_klass WHERE nomer=18 "
				. " ORDER BY number_spr, kodrai, slugba ";
		} else if($role == 4) {
			$sqlQuery = "SELECT a.id as kod, IFNULL(a.gos_znak, '') as text, IFNULL(x1.text, '') as model_ts, IFNULL(x2.text, '') as marka_ts, IFNULL(x3.text, '') as slugba_text,"
				. " IFNULL(x4.text, '') as kodrai_text, a.kodrai, a.slugba, 1 as number_spr FROM cars a "
				. " LEFT JOIN s2i_klass x1 ON x1.kod=a.model AND x1.nomer=4 "
				. " LEFT JOIN s2i_klass x2 ON x2.kod=a.marka AND x2.nomer=3 "
				. " LEFT JOIN s2i_klass x3 ON x3.kod=a.slugba AND x3.nomer=1 "
				. " LEFT JOIN s2i_klass x4 ON x4.kod=a.kodrai AND x4.nomer=11 "
				. " WHERE a.dostup=1 AND a.slugba IN " . User::get_all_slugba()
				. " UNION ALL "
				. " SELECT kod, text, '', '', '', '', 0, 0, 2 FROM s2i_klass WHERE nomer=18 "
				. " ORDER BY number_spr, kodrai, slugba ";
		} else if($role == 3) {
			$sqlQuery = "SELECT a.id as kod, IFNULL(a.gos_znak, '') as text, IFNULL(x1.text, '') as model_ts, IFNULL(x2.text, '') as marka_ts, IFNULL(x3.text, '') as slugba_text,"
				. " IFNULL(x4.text, '') as kodrai_text, a.kodrai, a.slugba, 1 as number_spr FROM cars a "
				. " LEFT JOIN s2i_klass x1 ON x1.kod=a.model AND x1.nomer=4 "
				. " LEFT JOIN s2i_klass x2 ON x2.kod=a.marka AND x2.nomer=3 "
				. " LEFT JOIN s2i_klass x3 ON x3.kod=a.slugba AND x3.nomer=1 "
				. " LEFT JOIN s2i_klass x4 ON x4.kod=a.kodrai AND x4.nomer=11 "
				. " WHERE a.dostup = 1 "
				. " UNION ALL "
				. " SELECT kod, text, '', '', '', '', 0, 0, 2 FROM s2i_klass WHERE nomer=18 "
				. " ORDER BY number_spr, kodrai, slugba ";
		} else if($role == 2) {
			$sqlQuery = "SELECT a.id as kod, IFNULL(a.gos_znak, '') as text, IFNULL(x1.text, '') as model_ts, IFNULL(x2.text, '') as marka_ts, IFNULL(x3.text, '') as slugba_text,"
				. " IFNULL(x4.text, '') as kodrai_text, a.kodrai, a.slugba, 1 as number_spr FROM cars a "
				. " LEFT JOIN s2i_klass x1 ON x1.kod=a.model AND x1.nomer=4 "
				. " LEFT JOIN s2i_klass x2 ON x2.kod=a.marka AND x2.nomer=3 "
				. " LEFT JOIN s2i_klass x3 ON x3.kod=a.slugba AND x3.nomer=1 "
				. " LEFT JOIN s2i_klass x4 ON x4.kod=a.kodrai AND x4.nomer=11 "
				. " WHERE a.dostup=1 AND a.kodrai=" . $kodrai
				. " UNION ALL "
				. " SELECT kod, text, '', '', '', '', 0, 0, 2 FROM s2i_klass WHERE nomer=18 "
				. " ORDER BY number_spr, kodrai, slugba ";
		} else {
			return false;
		}*/

		$sql = "SELECT a.id as kod, IFNULL(a.gos_znak, '') as text, IFNULL(x1.text, '') as model_ts, IFNULL(x2.text, '') as marka_ts, IFNULL(x3.text, '') as slugba_text,"
				. " IFNULL(x4.text, '') as kodrai_text, a.kodrai, a.slugba, 1 as number_spr FROM cars a "
				. " LEFT JOIN s2i_klass x1 ON x1.kod=a.model AND x1.nomer=4 "
				. " LEFT JOIN s2i_klass x2 ON x2.kod=a.marka AND x2.nomer=3 "
				. " LEFT JOIN s2i_klass x3 ON x3.kod=a.slugba AND x3.nomer=1 "
				. " LEFT JOIN s2i_klass x4 ON x4.kod=a.kodrai AND x4.nomer=11 "
				. " ORDER BY number_spr, kodrai, slugba ";

		if(($data = DB::query($sql)) === false)
			return false;

		$dictionary = "<option value='0'></option>";
		for($i = 0; $i < count($data); $i++) {
			if($data[$i]['kod'] == $selected_value)
				$dictionary .= "<option value='" . $data[$i]['kod'] . "' selected>" . $data[$i]['text'] . "</option>";
			else
				$dictionary .= "<option value='" . $data[$i]['kod'] . "'>" . $data[$i]['text'] . "</option>";
		}
		return $dictionary;
	}

	public static function get_directory_driver($selected_value = '') {
		/*Session::start();
		$role = Session::get('role');
		$kodrai = Session::get('slugba');
		Session::commit();
		
		if($role == 8 || $role == 9) {
			$sqlQuery = "SELECT a.id as kod, IFNULL(a.gos_znak, '') as text, IFNULL(x1.text, '') as model_ts, IFNULL(x2.text, '') as marka_ts, IFNULL(x3.text, '') as slugba_text,"
				. " IFNULL(x4.text, '') as kodrai_text, a.kodrai, a.slugba, 1 as number_spr FROM cars a "
				. " LEFT JOIN s2i_klass x1 ON x1.kod=a.model AND x1.nomer=4 "
				. " LEFT JOIN s2i_klass x2 ON x2.kod=a.marka AND x2.nomer=3 "
				. " LEFT JOIN s2i_klass x3 ON x3.kod=a.slugba AND x3.nomer=1 "
				. " LEFT JOIN s2i_klass x4 ON x4.kod=a.kodrai AND x4.nomer=11 "
				. " UNION ALL "
				. " SELECT a.id, CONCAT(a.fam, ' ' , a.imj, ' ' , a.otch), 0, 0, x1.text, x2.text, a.kodrai, a.slugba, 2 FROM drivers a "
				. " LEFT JOIN s2i_klass x1 ON x1.kod=a.slugba AND x1.nomer=1 "
				. " LEFT JOIN s2i_klass x2 ON x2.kod=a.kodrai AND x2.nomer=11 "
				. " ORDER BY number_spr, kodrai, slugba ";
		} else if($role == 4) {
			$sqlQuery = "SELECT a.id as kod, IFNULL(a.gos_znak, '') as text, IFNULL(x1.text, '') as model_ts, IFNULL(x2.text, '') as marka_ts, IFNULL(x3.text, '') as slugba_text,"
				. " IFNULL(x4.text, '') as kodrai_text, a.kodrai, a.slugba, 1 as number_spr FROM cars a "
				. " LEFT JOIN s2i_klass x1 ON x1.kod=a.model AND x1.nomer=4 "
				. " LEFT JOIN s2i_klass x2 ON x2.kod=a.marka AND x2.nomer=3 "
				. " LEFT JOIN s2i_klass x3 ON x3.kod=a.slugba AND x3.nomer=1 "
				. " LEFT JOIN s2i_klass x4 ON x4.kod=a.kodrai AND x4.nomer=11 "
				. " WHERE a.dostup=1 AND a.slugba IN " . User::get_all_slugba()
				. " UNION ALL "
				. " SELECT a.id, CONCAT(a.fam, ' ' , a.imj, ' ' , a.otch), 0, 0, x1.text, x2.text, a.kodrai, a.slugba, 2 FROM drivers a "
				. " LEFT JOIN s2i_klass x1 ON x1.kod=a.slugba AND x1.nomer=1 "
				. " LEFT JOIN s2i_klass x2 ON x2.kod=a.kodrai AND x2.nomer=11 "
				. " WHERE a.dostup=1 AND a.slugba IN " . User::get_all_slugba()
				. " ORDER BY number_spr, kodrai, slugba ";
		} else if($role == 3) {
			$sqlQuery = "SELECT a.id as kod, IFNULL(a.gos_znak, '') as text, IFNULL(x1.text, '') as model_ts, IFNULL(x2.text, '') as marka_ts, IFNULL(x3.text, '') as slugba_text,"
				. " IFNULL(x4.text, '') as kodrai_text, a.kodrai, a.slugba, 1 as number_spr FROM cars a "
				. " LEFT JOIN s2i_klass x1 ON x1.kod=a.model AND x1.nomer=4 "
				. " LEFT JOIN s2i_klass x2 ON x2.kod=a.marka AND x2.nomer=3 "
				. " LEFT JOIN s2i_klass x3 ON x3.kod=a.slugba AND x3.nomer=1 "
				. " LEFT JOIN s2i_klass x4 ON x4.kod=a.kodrai AND x4.nomer=11 "
				. " WHERE a.dostup = 1 "
				. " UNION ALL "
				. " SELECT a.id, CONCAT(a.fam, ' ' , a.imj, ' ' , a.otch), 0, 0, x1.text, x2.text, a.kodrai, a.slugba, 2 FROM drivers a "
				. " LEFT JOIN s2i_klass x1 ON x1.kod=a.slugba AND x1.nomer=1 "
				. " LEFT JOIN s2i_klass x2 ON x2.kod=a.kodrai AND x2.nomer=11 "
				. " WHERE a.dostup=1 "
				. " ORDER BY number_spr, kodrai, slugba ";
		} else if($role == 2) {
			$sqlQuery = "SELECT a.id as kod, IFNULL(a.gos_znak, '') as text, IFNULL(x1.text, '') as model_ts, IFNULL(x2.text, '') as marka_ts, IFNULL(x3.text, '') as slugba_text,"
				. " IFNULL(x4.text, '') as kodrai_text, a.kodrai, a.slugba, 1 as number_spr FROM cars a "
				. " LEFT JOIN s2i_klass x1 ON x1.kod=a.model AND x1.nomer=4 "
				. " LEFT JOIN s2i_klass x2 ON x2.kod=a.marka AND x2.nomer=3 "
				. " LEFT JOIN s2i_klass x3 ON x3.kod=a.slugba AND x3.nomer=1 "
				. " LEFT JOIN s2i_klass x4 ON x4.kod=a.kodrai AND x4.nomer=11 "
				. " WHERE a.dostup=1 AND a.kodrai=" . $kodrai
				. " UNION ALL "
				. " SELECT a.id, CONCAT(a.fam, ' ' , a.imj, ' ' , a.otch), 0, 0, x1.text, x2.text, a.kodrai, a.slugba, 2 FROM drivers a "
				. " LEFT JOIN s2i_klass x1 ON x1.kod=a.slugba AND x1.nomer=1 "
				. " LEFT JOIN s2i_klass x2 ON x2.kod=a.kodrai AND x2.nomer=11 "
				. " WHERE a.dostup=1 AND a.kodrai=" . $kodrai
				. " ORDER BY number_spr, kodrai, slugba ";
		} else {
			return false;
		}*/

		$sql = " SELECT a.id as kod, CONCAT(a.fam, ' ' , a.imj, ' ' , a.otch) as text FROM drivers a "
				. " LEFT JOIN s2i_klass x1 ON x1.kod=a.slugba AND x1.nomer=1 "
				. " LEFT JOIN s2i_klass x2 ON x2.kod=a.kodrai AND x2.nomer=11 "
				. " ORDER BY kodrai, slugba ";

		if(($data = DB::query($sql)) === false)
			return false;

		$dictionary = "<option value='0'></option>";
		for($i = 0; $i < count($data); $i++) {
			if($data[$i]['kod'] == $selected_value)
				$dictionary .= "<option value='" . $data[$i]['kod'] . "' selected>" . $data[$i]['text'] . "</option>";
			else
				$dictionary .= "<option value='" . $data[$i]['kod'] . "'>" . $data[$i]['text'] . "</option>";
		}
		return $dictionary;
	}
	
}