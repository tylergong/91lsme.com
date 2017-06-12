<?php

class BaseController {

	private $isLoadMC = false;
	private static $_lang = '';

	//初始信息
	public function __construct() {
		$this->_init();
	}

	public function init() {

	}

	protected function _init() {
		//配置页面引用全局
		spl_autoload_register(array($this, 'loadClass'));
		$this->init();
	}

	protected function loadClass($class) {
		if(empty($this->isLoadMC)) {
			set_include_path(get_include_path() . PATH_SEPARATOR . FRAMEWORK_PATH . 'base/model');
			set_include_path(get_include_path() . PATH_SEPARATOR . APP_WEB_PATH . 'model');
			$this->isLoadMC = true;
		}
		include_once strtr($class, '_\\', '//') . '.php';

		//$filename = FRAMEWORK_PATH . 'base/model/' . strtr($class, '_\\', '//') . '.php';
		//if (is_file($filename)) {
		//	require_once $filename;
		//}
	}
}
