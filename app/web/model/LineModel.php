<?php

class LineModel extends DataBaseModel {

	function __construct($dbconfig) {
		parent::__construct($dbconfig);
	}

	function M_getLineAll($arr = array()) {
		$this->_set_params($arr);
		return $this->_compile_select()->fetchAll();
	}

}
