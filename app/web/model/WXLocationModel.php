<?php

class WXLocationModel extends DataBaseModel {

	function __construct($dbconfig) {
		parent::__construct($dbconfig);
	}

	function M_getWXLocationRow($arr = array()) {
		$this->_set_params($arr);
		return $this->_compile_select()->fetchRow();
	}

	function M_getWXLocationLimit($arr = array()) {
		$this->_set_params($arr);
		return $this->_compile_select()->fetchAll();
	}

	function M_getWXLocationCount() {
		return $this->count()->_compile_select()->fetchOne();
	}

	function M_addWXLocation($data = array()) {
		return $this->duplicateInsert($data, 'wxid');
	}

	function M_upWXLocation($data = array(), $arr = array()) {
		return $this->update($data, $arr);
	}

	function M_delWXLocation($arr = array()) {
		return $this->delete($arr);
	}

}
