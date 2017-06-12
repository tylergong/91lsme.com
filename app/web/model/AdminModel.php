<?php

class AdminModel extends DataBaseModel {

	function __construct($dbconfig) {
		parent::__construct($dbconfig);
	}

	function M_getAdminRow($arr = array()) {
		$this->_set_params($arr);
		return $this->_compile_select()->fetchRow();
	}

	function M_getAdminAll($arr = array()) {
		$this->_set_params($arr);
		return $this->_compile_select()->fetchAll();
	}

	function M_getAdminCount() {
		return $this->count()->_compile_select()->fetchOne();
	}

	function M_addAdmin($data = array()) {
		return $this->insert($data);
	}

	function M_upAdmin($data = array(), $arr = array()) {
		return $this->update($data, $arr);
	}

}
