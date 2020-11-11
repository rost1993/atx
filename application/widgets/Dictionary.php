<?php

namespace IcKomiApp\widgets;

use IcKomiApp\lib\Database\DB;

class Dictionary {

	protected static $table_dictionary = 's2i_klass_kasu';

	public static function get_dictionary($number_dictionary, $selected_value = '', $html = true, $order = 'text', $alias = '') {
		if((mb_strlen($number_dictionary) == 0) || empty($number_dictionary) || ($number_dictionary === null))
			return false;

		$order_by = ((mb_strlen($order) == 0) || empty($order) || ($order === null)) ? '' : ' ORDER BY ' . $order;
		$sql = 'SELECT * FROM ' . self::$table_dictionary . " WHERE nomer=" . $number_dictionary . " " . $order_by;

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
				if($data[$i]['KOD'] == $selected_value)
					$dictionary .= "<option value='" . $data[$i]['KOD'] . "' selected>" . $data[$i]['TEXT'] . "</option>";
				else
					$dictionary .= "<option value='" . $data[$i]['KOD'] . "'>" . $data[$i]['TEXT'] . "</option>";
			}
			return $dictionary;
		}

		return $data;
	}

	public static function get_dictionary_where($number_dictionary, $where = '', $selected_value = '', $html = true, $order = 'text', $alias = '') {
		if((mb_strlen($number_dictionary) == 0) || empty($number_dictionary) || ($number_dictionary === null))
			return false;

		$order_by = ((mb_strlen($order) == 0) || empty($order) || ($order === null)) ? '' : ' ORDER BY ' . $order;
		$sql = 'SELECT * FROM ' . self::$table_dictionary . " WHERE nomer=" . $number_dictionary . ' AND ' . $where . $order_by;

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

	public static function get_multiple_dictionary($number_dictionary, $html = true, $order = 'nomer', $alias = '') {
		if(!is_array($number_dictionary) || empty($number_dictionary) || ($number_dictionary === null))
			return false;

		$order_by = " ORDER BY nomer " . ((mb_strlen($order) == 0) || empty($order)) ? '' : ',' . $order;
		
		$list_numbers = "";
		for($i = 0; $i < count($number_dictionary); $i++)
			$list_numbers .= (mb_strlen($list_numbers) == 0) ? $number_dictionary[$i] : "," . $number_dictionary[$i];
		
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
			for($i = 0; $i < count($number_dictionary); $i++)
				$dictionary[$number_dictionary[$i]] = "<option value='0'></option>";

			for($i = 0; $i < count($data); $i++)
				$dictionary[$data[$i]['nomer']] .= "<option value='" . $data[$i]['kod'] . "'>" . $data[$i]['text'] . "</option>";
		} else {

			for($i = 0; $i < count($number_dictionary); $i++)
				$dictionary[$number_dictionary[$i]] = array();

			for($i = 0; $i < count($data); $i++)
				array_push($dictionary[$data[$i]['nomer']], $data[$i]);
		}

		return $dictionary;
	}

	/*
		Модифицированный загрузчик справочников
		Для кода используется другое поле
	*/
	public static function get_dictionary_spec($number_dictionary, $selected_value = '', $html = true, $order = 'text', $alias = '') {
		if((mb_strlen($number_dictionary) == 0) || empty($number_dictionary) || ($number_dictionary === null))
			return false;

		$order_by = ((mb_strlen($order) == 0) || empty($order) || ($order === null)) ? '' : ' ORDER BY ' . $order;
		$sql = 'SELECT * FROM ' . self::$table_dictionary . " WHERE nomer=" . $number_dictionary . " " . $order_by;

		if((mb_strlen($alias) == 0) || empty($alias) || ($alias === null)) {
			if(($data = DB::query($sql)) === false)
				return false;	
		} else {
			if(($data = DB::query_db_alias($sql, $alias)) === false)
				return false;
		}

		if($html) {
			$dictionary = "<option value='0' data-param='0'></option>";

			$selected_value = (empty($selected_value)) ? 0 : $selected_value;
			for($i = 0; $i < count($data); $i++) {
				if($data[$i]['KOD'] == $selected_value)
					$dictionary .= "<option value='" . $data[$i]['KOD'] . "' data-param1='" . $data[$i]['PARAM_1'] . "' data-param2='" . $data[$i]['PARAM_2'] . "' selected>" . $data[$i]['TEXT'] . "</option>";
				else
					$dictionary .= "<option value='" . $data[$i]['KOD'] . "' data-param1='" . $data[$i]['PARAM_1'] . "' data-param2='" . $data[$i]['PARAM_2'] . "'>" . $data[$i]['TEXT'] . "</option>";
			}
			return $dictionary;
		}

		return $data;
	}
	
}