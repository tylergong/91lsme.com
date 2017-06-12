<?php

require_once (FRAMEWORK_PATH . 'base/view/smarty.view.php');
require_once (APP_WEB_PATH . 'controller/admin.controller.php');
require_once (APP_WEB_PATH . 'controller/HighCharts.controller.php');

class HighChartsView extends smartView {

	private $adminController = null;
	private $highchartsController = null;

	public function __construct() {
		parent::__construct();

		$this->adminController = new adminController();
		$this->highchartsController = new HighChartsController();
	}

	public function pie() {
		$this->caching = false;
		if (!$this->adminController->C_chkLoginStatus()) {
			header("Location: /admin-login/");
		} else {
			$html = $this->fetch('HighCharts/pie.shtml');
			die(json_encode($html));
		}
	}

	public function line() {
		$this->caching = false;
		if (!$this->adminController->C_chkLoginStatus()) {
			header("Location: /admin-login/");
		} else {
			$html = $this->fetch('HighCharts/line.shtml');
			die(json_encode($html));
		}
	}

	public function column() {
		$this->caching = false;
		if (!$this->adminController->C_chkLoginStatus()) {
			header("Location: /admin-login/");
		} else {
			$html = $this->fetch('HighCharts/column.shtml');
			die(json_encode($html));
		}
	}

	public function column2() {
		$this->caching = false;
		if (!$this->adminController->C_chkLoginStatus()) {
			header("Location: /admin-login/");
		} else {
			$html = $this->fetch('HighCharts/column-drilldown.shtml');
			die(json_encode($html));
		}
	}

	public function spline() {
		$this->caching = false;
		if (!$this->adminController->C_chkLoginStatus()) {
			header("Location: /admin-login/");
		} else {
			$html = $this->fetch('HighCharts/spline.shtml');
			die(json_encode($html));
		}
	}

	/*
	 * =========================================================================================================
	 *  数据获取部分
	 * =========================================================================================================
	 */

	public function getpie() {
		$this->caching = false;
		$rtn = array('MSIE' => 54, 'Firefox' => 21, 'Chrome' => 16, 'Safari' => 6, 'Opera' => 3);
		die(json_encode($rtn));
	}

	public function getline() {
		$this->caching = false;
		$data = $this->highchartsController->C_getExpectLine();
		foreach ($data as $v) {
			$rtn[$v['expect']]['a'] = $v['code1'];
			$rtn[$v['expect']]['b'] = $v['code2'];
			$rtn[$v['expect']]['c'] = $v['code3'];
		}
		die(json_encode($rtn));
	}

	public function getcolumn() {
		$this->caching = false;
		$data = $this->highchartsController->C_getBlueNumFre();
		foreach ($data as $v) {
			$rtn[$v['c']] = $v['s'];
		}
		die(json_encode($rtn));
	}

	public function getbrowser() {
		$arr = array(
			'MSIE' => array('MSIE 6.0' => '10.85', 'MSIE 7.0' => '7.35', 'MSIE 8.0' => '33.16', 'MSIE 9.0' => '2.81'),
			'Firefox' => array('Firefox 2.0' => 0.20, 'Firefox 3.0' => 0.83, 'Firefox 3.5' => 1.58, 'Firefox 3.6' => 13.12, 'Firefox 4.0' => 5.43),
			'Chrome' => array('Chrome 5.0' => 0.12, 'Chrome 6.0' => 0.19, 'Chrome 7.0' => 0.12, 'Chrome 8.0' => 0.36, 'Chrome 9.0' => 1.38, 'Chrome 10.0' => 9.91, 'Chrome 11.0' => 1.50, 'Chrome 12.0' => 1.22),
			'Safari' => array('Safari 5.0' => 4.55, 'Safari 4.0' => 1.42, 'Safari Win 5.0' => 0.23, 'Safari 4.1' => 0.21, 'Safari/Maxthon' => 0.20, 'Safari 3.1' => 0.19, 'Safari 4.1' => 0.14),
			'Opera' => array('Opera 9.x' => 0.12, 'Opera 10.x' => 1.37, 'Opera 11.x' => 1.65),
		);
		die(json_encode($arr));
	}

	public function cp() {
		$arr = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36);
		shuffle($arr);
		$k = array_rand($arr, 6);
		foreach ($k as $val) {
			$r[] = $arr[$val];
		}
		sort($r);
		foreach ($r as $v) {
			$rr .= $v . ' ';
		}
		print_r($rr);
		echo ', ';
		$arr2 = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16);
		shuffle($arr2);
		$k2 = array_rand($arr2, 1);
		$r2 = $arr2[$k2];
		print_r($r2);
	}

	public function addcp() {
		$data = $this->highchartsController->C_insetCp('ssq', 50);
	}

}
