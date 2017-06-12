<?php

require_once (FRAMEWORK_PATH . 'base/view/smarty.view.php');


class TestView extends smartView {


	public function __construct() {
		parent::__construct();

	}

	public function index() {
 		$this->caching = false;
 		$this->assign('a',121);
		$this->display('index1.shtml');
	}


}
