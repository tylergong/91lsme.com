<?php

class CityModel extends DataBaseModel {

	function __construct($dbconfig) {
		parent::__construct($dbconfig);
	}

	function M_getCityAll($arr = array()) {
		$this->_set_params($arr);
		return $this->_compile_select()->fetchAll();
	}

}
