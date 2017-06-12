<?php

class TestModel extends DataBaseModel {

	function __construct($dbconfig) {
		parent::__construct($dbconfig);
	}

	function M_getTestRow($arr = array()) {
		$this->_set_params($arr);
		return $this->_compile_select()->fetchRow();
	}

	function M_getTestLimit($arr = array()) {
		$this->_set_params($arr);
		return $this->_compile_select()->fetchAll();
	}

	function M_getTestCount() {
		return $this->count()->_compile_select()->fetchOne();
	}

	function M_addTest($data = array()) {
		return $this->insert($data);
	}

	function M_upTest($data = array(), $arr = array()) {
		return $this->update($data, $arr);
	}

	function M_delTest($arr = array()) {
		return $this->delete($arr);
	}

	function M_getBlueNumFre() {
		$sql = "SELECT code7 as c,COUNT(*) AS s FROM ls_test GROUP BY code7";
		return $this->manualexec($sql)->fetchAll();
	}

	function M_getRedNumFre() {
		$sql = "SELECT A.code1 AS c,SUM(A.s) AS s FROM 
				(
				SELECT code1,COUNT(*) AS s FROM ls_test GROUP BY code1
				UNION
				SELECT code2,COUNT(*) AS s FROM ls_test GROUP BY code2
				UNION
				SELECT code3,COUNT(*) AS s FROM ls_test GROUP BY code3
				UNION
				SELECT code4,COUNT(*) AS s FROM ls_test GROUP BY code4
				UNION
				SELECT code5,COUNT(*) AS s FROM ls_test GROUP BY code5
				UNION
				SELECT code6,COUNT(*) AS s FROM ls_test GROUP BY code6
				) AS A
				GROUP BY code1";
		return $this->manualexec($sql)->fetchAll();
	}

}
