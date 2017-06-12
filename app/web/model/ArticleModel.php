<?php

class ArticleModel extends DataBaseModel {

	function __construct($dbconfig) {
		parent::__construct($dbconfig);
	}

	function M_getArticleRow($arr = array()) {
		$this->_set_params($arr);
		$res = $this->_compile_select()->fetchRow();
		if (is_array($res)) {
			foreach ($res as &$v) {
				$v = $this->handles_lashes($v);
			}
		}
		return $res;
	}

	function M_getArticleLimit($arr = array()) {
		$this->_set_params($arr);
		return $this->_compile_select()->fetchAll();
	}

	function M_getArticleCount($arr = array()) {
		$this->_set_params($arr);
		return $this->count()->_compile_select()->fetchOne();
	}

	function M_addArticle($data = array()) {
		return $this->insert($data);
	}

	function M_upArticle($data = array(), $arr = array()) {
		return $this->update($data, $arr);
	}

}
