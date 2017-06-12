<?php

require_once (FRAMEWORK_PATH . 'base/view/smarty.view.php');
require_once (APP_WEB_PATH . 'controller/admin.controller.php');
require_once (APP_WEB_PATH . 'controller/PHPExcel.controller.php');

class PHPExcelView extends smartView {

	private $adminController = null;
	private $phpexcelController = null;

	public function __construct() {
		parent::__construct();

		$this->adminController = new adminController();
		$this->phpexcelController = new HighChartsController();
	}

	public function index() {
		$this->caching = false;
		if (!$this->adminController->C_chkLoginStatus()) {
			header("Location: /admin-login/");
		} else {
			$html = $this->fetch('PHPExcel/index.shtml');
			die(json_encode($html));
		}
	}

	public function down() {
		$this->caching = false;
		if (!$this->adminController->C_chkLoginStatus()) {
			header("Location: /admin-login/");
		} else {
			$this->phpexcelController->C_Down();
		}
	}

}
