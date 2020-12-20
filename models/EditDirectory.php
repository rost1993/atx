<?php

namespace IcKomiApp\models;

use IcKomiApp\core\Model;
use IcKomiApp\core\Functions;
use IcKomiApp\lib\Database\DB;
use IcKomiApp\widgets\Directory;

class EditDirectory extends Model {
	
	protected $table = 's2i_klass';

	public function get_value_directory($post) {
		if(!is_array($post) || empty($post['directory']))
			return false;

		$number_directory = $post['directory'];
		return $directory_select = Directory::get_directory($number_directory);
	}

	public function remove_value_directory($post) {
		if(!is_array($post) || empty($post['directory']) || empty($post['value']))
			return false;

		$number_directory = $post['directory'];
		$code_value = $post['value'];

		$sql = "DELETE FROM " . $this->table . " WHERE nomer=" . $number_directory . " AND kod=" . $code_value;
		if(DB::query($sql, DB::OTHER) === false)
			return false;
		return true;
	}

	public function save_value_directory($post) {
		/*if(!is_array($post) || empty($post['directory']))
			return false;*/

		$number_directory = $post['directory'];
		$old_value = (empty($post['value'])) ? '' : $post['value'];
		$new_value = (empty($post['new_value'])) ? '' : $post['new_value'];

		$sql = '';
		if((mb_strlen($old_value) == 0) && (mb_strlen($new_value) == 0))
			return false;
		
		if(mb_strlen($old_value) == 0) {
			$sql = "SELECT IFNULL(MAX(kod), 0)+1 as max_code FROM " . $this->table . " WHERE nomer=" . $number_directory;
			if(($data = DB::query($sql)) === false)
				return false;

			if(count($data) == 0)
				return false;

			$max_code = $data[0]['max_code'];

			$sql = "INSERT INTO " . $this->table . " (nomer, text, kod) VALUES (" . $number_directory . ",'" . $new_value . "'," . $max_code . " )";
		} else {
			$sql = "UPDATE " . $this->table . " SET text='" . $new_value . "' WHERE nomer=" . $number_directory . " AND kod=" . $old_value;
		}

		if(DB::query($sql, DB::INSERT_OR_UPDATE) === false)
			return false;
		return true;
	}
}