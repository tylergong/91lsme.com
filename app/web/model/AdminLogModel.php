<?php

class AdminLogModel extends DataBaseModel {

	function __construct($dbconfig) {
		parent::__construct($dbconfig);
	}

	function M_getAdminLogRow($arr = array()) {
		$this->_set_params($arr);
		return $this->_compile_select()->fetchRow();
	}

	function M_getAdminLogAll($arr = array()) {
		$this->_set_params($arr);
		return $this->_compile_select()->fetchAll();
	}

	function M_getAdminLogCount() {
		return $this->count()->_compile_select()->fetchOne();
	}

	function M_addAdminLog($data = array()) {
		return $this->insert($data);
	}

}
