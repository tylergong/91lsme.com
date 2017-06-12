<?php

require_once (FRAMEWORK_PATH . 'base/view/smarty.view.php');
require_once (APP_WEB_PATH . 'controller/subway.controller.php');

class SubwayView extends smartView {

	private $subwayController = null;

	public function __construct() {
		parent::__construct();

		$this->subwayController = new subwayController();
	}

	public function index() {
		$cid = isset($_GET['cid']) ? intval($_GET['cid']) : 1;

		$res_city = $this->subwayController->C_getCity();
		$res_line = $this->subwayController->C_getLineByCityFormat($cid);
		$platform = PlatfomHelp::CheckPlatform();

		$this->assign('cid', $cid);
		$this->assign('res_city', $res_city);
		$this->assign('res_line', $res_line);
		$this->assign('platform', $platform);

		$this->display('index.shtml');
	}

	public function getsitebyline() {
		$cid = isset($_GET['cid']) ? intval($_GET['cid']) : 1;
		$line = isset($_GET['line']) ? intval($_GET['line']) : 1;

		$result = $this->subwayController->C_getSiteByLineFormat($cid, $line);

		echo json_encode($result);
		die;
	}

	public function submit() {
		$start = isset($_GET['s_s']) ? trim($_GET['s_s']) : '';
		$end = isset($_GET['e_s']) ? trim($_GET['e_s']) : '';

		$result = $this->subwayController->C_getSubwayTransferMode($start, $end);
		echo json_encode($result);
		die;
	}

}
