<?php

require_once(FRAMEWORK_PATH . 'lib/smarty/Smarty.class.php');

class smartView extends Smarty {

	private static $request_from;
	public $request = null;

	function __construct() {
		// Class Constructor.
		// These automatically get set with each new instance.
		parent::__construct();

		$this->request = self::request_from();

		$app = "default";
		if (isset($_GET['app']) && $_GET['app'] != null && $_GET['app'] != "") {
			$app = $_GET['app'];
		}

		$this->left_delimiter = '<{';
		$this->right_delimiter = '}>';
		//$this->compile_id = $lang;
		$this->compile_dir = APP_WEB_PATH . 'cache/templates/'; // 编译目录
		$this->config_dir = APP_WEB_PATH . 'config/'; // 外部配置文件夹目录
		//
		//$this->caching = false; // 是否开启缓存
		$this->cache_dir = APP_WEB_PATH . 'cache/html/'; // 缓存存放目录;
		$this->cache_lifetime = CACHE_LIFETIME; // 缓存存活时间（秒） 

		$this->template_dir = APP_WEB_PATH . 'templates/' . $app . '/html/'; // 模板目录
		//$this->debugging = false; // 调试模式 
		//$this->force_compile = true; // 强迫编译（用于开发模式） 

		$this->assign('_hmPixel', $this->hmPixel());

		// 是否启用静态页 
		$this->assign('is_static', PAGE_STATIC);
	}

	//function isCached($template = null, $cache_id = null, $compile_id = null, $parent = null) {
	//$cache_id = empty($cache_id) ? md5($_SERVER['REQUEST_URI']) : $cache_id;
	//return parent::isCached($template, $cache_id, $compile_id, $parent);
	//}
	//function display($template = null, $cache_id = null, $compile_id = null, $parent = null) {
	//$cache_id = empty($cache_id) ? md5($_SERVER['REQUEST_URI']) : $cache_id;
	//return parent::display($template, $cache_id, $compile_id, $parent);
	//}

	function hmPixel() {
		require(FRAMEWORK_PATH . "php/hm.php");
		$_hmt = new _HMT("4e12425c4f2ca16acaab6b2a8cdc8fbc");
		$_hmtPixel = $_hmt->trackPageView();
		return $_hmtPixel;
	}

	/**
	 * 加载request类
	 *
	 * @return Request
	 */
	public static function request_from() {
		if(!self::$request_from) {
			require_once(FRAMEWORK_PATH . 'base/view/Request.view.php');
			self::$request_from = Request::from_env();
		}
		return self::$request_from;
	}
}
