<?php

require_once (FRAMEWORK_PATH . 'base/controller/Base.controller.php');

class HighChartsController extends BaseController {

	private $TestModel = null;

	public function init() {
		require (APP_WEB_PATH . 'config/db.conf.php');

		$this->TestModel = new TestModel($defaultDbConfig);
	}

	/**
	 *  获取往期彩票中奖号码
	 * @param type $code	彩票代码	双色球[ssq]\大乐透[dlt]
	 * @param type $rows	返回行数
	 * @return type
	 */
	public function C_getCp($code, $rows) {
		// 最多获取50期
		$url = "http://f.opencai.net/utf8/" . $code . "-" . $rows . ".json";
		$json = HttpHelp::CurlRequest($url);
		$json = json_decode($json);
		foreach ($json->data as $k => $v) {
			$cp[$k]['expect'] = $v->expect;
			$cp[$k]['opencode'] = $v->opencode;
		}
		return $cp;
	}

	public function C_insetCp($code = 'ssq', $rows = 5) {
		$data = $this->C_getCp($code, $rows);
		foreach ($data as $k => $v) {
			$vals[$k] = $v['expect'];
		}
		array_multisort($vals, SORT_ASC, SORT_STRING, $data);
		//print_r($data);die;
		foreach ($data as $k => $v) {
			$code = explode('+', $v['opencode']);
			$code_red = explode(',', $code[0]);
			$code_blue = $code[1];

			$arr['expect'] = $v['expect'];
			$arr['code1'] = $code_red[0];
			$arr['code2'] = $code_red[1];
			$arr['code3'] = $code_red[2];
			$arr['code4'] = $code_red[3];
			$arr['code5'] = $code_red[4];
			$arr['code6'] = $code_red[5];
			$arr['code7'] = $code_blue;
			$this->TestModel->M_addTest($arr);
		}
	}

	public function C_getBlueNumFre() {
		$fre = $this->TestModel->M_getBlueNumFre();
		return $fre;
	}

	public function C_getRedNumFre() {
		$fre = $this->TestModel->M_getRedNumFre();
		return $fre;
	}

	public function C_getExpectLine() {
		$arr['select'] = 'expect,code1,code2,code3';
		$arr['by'] = array('expect' => 'asc'); 
		$res = $this->TestModel->M_getTestLimit($arr);
		return $res;
	}

}
