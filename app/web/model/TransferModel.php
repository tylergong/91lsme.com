<?php

class TransferModel extends DataBaseModel {

	function __construct($dbconfig) {
		parent::__construct($dbconfig);
	}

	public function M_getTransferAll($arr = array()) {
		$this->_set_params($arr);
		return $this->_compile_select()->fetchAll();
	}

}
