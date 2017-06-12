<?php

require_once(APP_WEB_PATH . 'controller/weixin.controller.php');

class weixinView {

	private $weixinController = null;

	public function __construct() {
		$this->weixinController = new weixinController();
	}

	/**
	 * 接收微信消息
	 */
	public function index() {
		if($_SERVER['REQUEST_METHOD'] == 'POST') {
			FileHelp::WriteLog(1, 'y', serialize($GLOBALS["HTTP_RAW_POST_DATA"]), 'responseMsg', 'weixin/');
			$this->weixinController->responseMsg();
		} else {
			$this->weixinController->valid(); // 服务器校验
		}
	}

	/**
	 * 自定义微信公众号导航
	 */
	public function cm() {
		$a = $this->weixinController->createMenu();
		print_r($a);
	}

	/**
	 * 获取微信公众号导航
	 */
	public function gm() {
		$a = $this->weixinController->getMenu();
		print_r($a);
	}
}
