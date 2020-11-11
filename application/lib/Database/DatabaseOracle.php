<?php

namespace IcKomiApp\lib\Database;

use IcKomiApp\lib\Database\DatabaseBasic;

class DatabaseOracle extends DatabaseBasic {

	private function connect() {
		if(!$link = oci_connect($this->login, $this->password, $this->host . '/' . $this->db_name, $this->charset)) {
			$e = oci_error();
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
			return null;
		}
		
		if(!($query = oci_parse($link, "ALTER SESSION SET NLS_DATE_FORMAT = 'DD.MM.RRRR'")))
			return null;
		
		oci_execute($query);

		return $link;
	}

	private function disconnect($link) {
		return oci_close($link);
	}

	public function query($sql, $mode = self::DB_SELECT) {
		$this->resultQuery = array();

		if(!(($mode == self::DB_SELECT) || ($mode == self::DB_INSERT_OR_UPDATE) || ($mode == self::DB_OTHER))) {
			$this->msgError = 'UNDEFINED CODE ORACLE QUERY';
			return false;
		}

		if(($link = $this->connect()) == NULL)
			return false;
		
		if(!($result = oci_parse($link, $sql))) {
			return false;
		}

		if(!oci_execute($result)) {
			return false;
		}

		if($mode == self::DB_SELECT) {
			while($data = oci_fetch_assoc($result))
				array_push($this->resultQuery, $data);
		}

		oci_free_statement($result);
		$this->disconnect($link);
			
		return true;

	}


}