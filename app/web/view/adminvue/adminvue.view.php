<?php

require_once(FRAMEWORK_PATH . 'base/view/smarty.view.php');
require_once(APP_WEB_PATH . 'controller/admin.controller.php');
require_once(APP_WEB_PATH . 'controller/article.controller.php');

class adminvueView extends smartView {

	private $adminController = null;
	private $articleController = null;

	public function __construct() {
		parent::__construct();

		$this->adminController = new adminController();
		$this->articleController = new articleController();
	}

	// 后台首页（未登录自动跳登录页）
	public function index() {
		$this->caching = false;
		if(!$this->adminController->C_chkLoginStatus()) {
			header("Location: /admin-login");
		} else {
 			$this->display('admin.shtml');
		}
	}
}