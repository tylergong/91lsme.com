<?php

class FLinksModel extends DataBaseModel {

	function __construct($dbconfig) {
		parent::__construct($dbconfig);
	}

	function M_getFLinksRow($arr = array()) {
		$this->_set_params($arr);
		return $this->_compile_select()->fetchRow();
	}

	function M_getFLinksLimit($arr = array()) {
		$this->_set_params($arr);
		return $this->_compile_select()->fetchAll();
	}

	function M_getFLinksCount() {
		return $this->count()->_compile_select()->fetchOne();
	}

	function M_addFLinks($data = array()) {
		return $this->insert($data);
	}

	function M_upFLinks($data = array(), $arr = array()) {
		return $this->update($data, $arr);
	}

	function M_delFLinks($arr = array()) {
		return $this->delete($arr);
	}

}
