<?php

class AdModel extends DataBaseModel {

	function __construct($dbconfig) {
		parent::__construct($dbconfig);
	}

	function M_getAdRow($arr = array()) {
		$this->_set_params($arr);
		return $this->_compile_select()->fetchRow();
	}

	function M_getAdLimit($arr = array()) {
		$this->_set_params($arr);
		return $this->_compile_select()->fetchAll();
	}

	function M_getAdCount() {
		return $this->count()->_compile_select()->fetchOne();
	}

	function M_addAd($data = array()) {
		return $this->insert($data);
	}

	function M_upAd($data = array(), $arr = array()) {
		return $this->update($data, $arr);
	}

	function M_delAd($arr = array()) {
		return $this->delete($arr);
	}

}
