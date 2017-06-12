<?php

abstract class DataBase {

	public $db;

	public function factory($dbconfig) {
		require_once(FRAMEWORK_PATH . 'lib/pdo/pdomysql.php');

		$option = array(
			'database' => $dbconfig['mysql_db'],
			'host' => $dbconfig['mysql_server'],
			'port' => $dbconfig['mysql_prot'],
			'login' => $dbconfig['mysql_user'],
			'password' => $dbconfig['mysql_pass'],
			'charset' => $dbconfig['mysql_code'],
		);
		$driver = 'pdomysql';
		if(!empty($this->db[$driver])) {
			return $this->db[$driver];
		}
		$class = 'Database_' . $driver;

		$db = $this->db[$driver] = new $class($option);
		return $db;
	}

}
