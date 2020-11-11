<?php

namespace IcKomiApp\lib\Database;

use IcKomiApp\lib\Database\DatabaseBasic;
use IcKomiApp\lib\Database\DatabaseMysql;
use IcKomiApp\lib\Database\DatabaseOracle;

class DB {

	const INSERT_OR_UPDATE = DatabaseBasic::DB_INSERT_OR_UPDATE;
	const SELECT = DatabaseBasic::DB_SELECT;
	const OTHER = DatabaseBasic::DB_OTHER;

	public static function query($sql, $mode = DatabaseBasic::DB_SELECT) {
		if(empty($GLOBALS['web_config']['database']))
			return false;

		$type_db = mb_strtolower($GLOBALS['web_config']['database']['db_type']);

		$db = ($type_db == DatabaseBasic::DB_TYPE_MYSQL) ? new DatabaseMysql() : new DatabaseOracle();

		if(($result = $db->query($sql, $mode)) === false)
			return false;

		if($mode == DatabaseBasic::DB_SELECT)
			return $db->resultQuery;
		return $result;
	}

	public static function query_db_alias($sql, $alias, $mode = DatabaseBasic::DB_SELECT) {
		if(empty($GLOBALS['web_config']['databases']))
			return false;

		if(empty($GLOBALS['web_config']['databases'][$alias]))
			return false;

		$type_db = mb_strtolower($GLOBALS['web_config']['databases'][$alias]['db_type']);

		$db = ($type_db == DatabaseBasic::DB_TYPE_MYSQL) ? new DatabaseMysql($alias) : new DatabaseOracle($alias);

		if(($result = $db->query($sql, $mode)) === false)
			return false;

		if($mode == DatabaseBasic::DB_SELECT)
			return $db->resultQuery;
		return $result;
	}
}