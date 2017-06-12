<?php

class ChannelModel extends DataBaseModel {

	function __construct($dbconfig) {
		parent::__construct($dbconfig);
	}

	function M_getChannelRow($arr = array()) {
		$this->_set_params($arr);
		return $this->_compile_select()->fetchRow();
	}

	function M_getChannelAll($arr = array()) {
		$this->_set_params($arr);
		return $this->_compile_select()->fetchAll();
	}

	function M_getChannelCount() {
		return $this->count()->_compile_select()->fetchOne();
	}

	function M_addChannel($data = array()) {
		return $this->insert($data);
	}

	function M_upChannel($data = array(), $arr = array()) {
		return $this->update($data, $arr);
	}

}
