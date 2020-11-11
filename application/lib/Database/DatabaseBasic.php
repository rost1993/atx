<?php

namespace IcKomiApp\lib\Database;

abstract class DatabaseBasic {

	protected $db_type = '';
	protected $login = '';
	protected $password = '';
	protected $host = '';
	protected $db_name = '';
	protected $charset = '';

	public $msgError = '';
	public $resultQuery = [];
	public $id = '';

	const DB_TYPE_MYSQL = 'mysql';
	const DB_TYPE_ORACLE = 'oracle';

	const DB_INSERT_OR_UPDATE = 1;
	const DB_SELECT = 2;
	const DB_OTHER = 3;

	public function __construct($alias = '') {

		if(mb_strlen($alias) == 0 || empty($alias) || $alias == null) {
			if(empty($GLOBALS['web_config']['database']))
				return;

			$this->db_type = (empty($GLOBALS['web_config']['database']['db_type'])) ? '' : $GLOBALS['web_config']['database']['db_type'];
			$this->login = (empty($GLOBALS['web_config']['database']['db_username'])) ? '' : $GLOBALS['web_config']['database']['db_username'];
			$this->password = (empty($GLOBALS['web_config']['database']['db_password'])) ? '' : $GLOBALS['web_config']['database']['db_password'];
			$this->host = (empty($GLOBALS['web_config']['database']['db_host'])) ? '' : $GLOBALS['web_config']['database']['db_host'];
			$this->db_name = (empty($GLOBALS['web_config']['database']['db_name'])) ? '' : $GLOBALS['web_config']['database']['db_name'];
			$this->charset = (empty($GLOBALS['web_config']['database']['charset'])) ? '' : $GLOBALS['web_config']['database']['charset'];
		} else {
			if(empty($GLOBALS['web_config']['databases']))
				return;

			if(empty($GLOBALS['web_config']['databases'][$alias]))
				return;

			$temp = $GLOBALS['web_config']['databases'][$alias];

			$this->db_type = (empty($temp['db_type'])) ? '' : $temp['db_type'];
			$this->login = (empty($temp['db_username'])) ? '' : $temp['db_username'];
			$this->password = (empty($temp['db_password'])) ? '' : $temp['db_password'];
			$this->host = (empty($temp['db_host'])) ? '' : $temp['db_host'];
			$this->db_name = (empty($temp['db_name'])) ? '' : $temp['db_name'];
			$this->charset = (empty($temp['charset'])) ? '' : $temp['charset'];
		}

	}

}