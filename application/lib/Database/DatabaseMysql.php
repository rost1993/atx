<?php

namespace IcKomiApp\lib\Database;

use IcKomiApp\lib\Database\DatabaseBasic;
use IcKomiApp\core\Functions;

class DatabaseMysql extends DatabaseBasic {

	private function connect() {

		$link = @mysqli_connect($this->host, $this->login, $this->password, $this->db_name);
		
		if(!$link) {
			$this->msgError = mysqli_connect_error();
			return null;
		}
		
		mysqli_query($link, "SET NAMES '" . $this->charset . "'");

		return $link;
	}

	private function disconnect($link) {
		return mysqli_close($link);
	}

	public function query($sql, $mode = self::DB_SELECT) {
		$this->resultQuery = [];

		if(!($mode == self::DB_SELECT || ($mode == self::DB_INSERT_OR_UPDATE) || ($mode == self::DB_OTHER))) {
			$this->msgError = 'UNDEFINED CODE MYSQL QUERY';
			return false;
		}

		if(($link = $this->connect()) == NULL)
			return false;

		if(!($result = mysqli_query($link, $sql))) {
			$this->msgError = mysqli_error($link);
			return false;
		}

		if($mode == self::DB_SELECT) {
			while($data = mysqli_fetch_assoc($result))
				array_push($this->resultQuery, $data);
			mysqli_free_result($result);
		} else {
			$this->id = mysqli_insert_id($link);
		}

		$this->disconnect($link);

		return true;		
	}

}