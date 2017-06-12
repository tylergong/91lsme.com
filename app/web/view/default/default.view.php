<?php

require_once (FRAMEWORK_PATH . 'base/view/smarty.view.php');
require_once (APP_WEB_PATH . 'controller/default.controller.php');
require_once (APP_WEB_PATH . 'controller/article.controller.php');
require_once (APP_WEB_PATH . 'controller/ad.controller.php');
require_once (APP_WEB_PATH . 'controller/flinks.controller.php');

class DefaultView extends smartView {

	private $defaultController = null;
	private $articleController = null;
	private $adController = null;
	private $flinksController = null;

	public function __construct() {
		parent::__construct();

		$this->defaultController = new defaultController();
		$this->articleController = new articleController();
		$this->adController = new adController();
		$this->flinksController = new flinksController();
	}

	public function index() {
		$this->caching = PAGE_STATIC ? false : PAGE_MODEL;

		if (!$this->isCached('index.shtml')) {
			$this->setpagedata();
		}
		$this->display('index.shtml');
	}

	/**
	 * 静态页模式
	 * 	/article/n.shtml
	 */
	public function release() {
		$this->caching = false;

		$this->setpagedata();

		$html = $this->fetch('index.shtml');
		$file = APP_WEB_PATH . '/templates/default/static/index.shtml';
		FileHelp::BuildHtml($file, $html);
		FileHelp::CopyFile($file, ROOT_PATH . '/index.shtml');
	}

	public function setpagedata() {
		// 获取首页4个文章列表
		$list_gz = $this->articleController->C_getArticleListByCid(1, 0, INDEX_LIST_NUM, 0, false);
		$list_jw = $this->articleController->C_getArticleListByCid(2, 0, INDEX_LIST_NUM, 0, false);
		$list_jx = $this->articleController->C_getArticleListByCid(3, 0, INDEX_LIST_NUM, 0, false);
		$list_sb = $this->articleController->C_getArticleListByCid(4, 0, INDEX_LIST_NUM, 0, false);
		// 获取广告图
		$list_ad = $this->adController->C_getAdList(INDEX_AD_NUM);
		// 获取友情链接
		$list_flink = $this->flinksController->C_getFLinksList(0);

		$this->assign('list_gz', $list_gz);
		$this->assign('list_jw', $list_jw);
		$this->assign('list_jx', $list_jx);
		$this->assign('list_sb', $list_sb);
		$this->assign('list_ad', $list_ad);
		$this->assign('list_flink', $list_flink);
	}

	public function qq() {
		$qq = $_GET['qq'];
		$en_qq = implode('', $this->defaultController->qq_encrypt($qq));
		echo $en_qq;
		echo "----";
		$de_qq = $this->defaultController->qq_decrypt($en_qq);
		echo $de_qq;
	}

	public function xh() {
		// 获取当前年月日
		$now = date('Y-m-d', time());
		// 获取当前星期
		$z = date('N', time());
		$week = array(1 => '星期一', 2 => '星期二', 3 => '星期三', 4 => '星期四', 5 => '星期五', 6 => '星期六', 7 => '星期日');
		// 法定假日
//		$nolimitday = array(
//			'2015-01-01', '2015-01-02', '2015-01-03',
//			'2015-02-18', '2015-02-19', '2015-02-20', '2015-02-21', '2015-02-22', '2015-02-23', '2015-02-24',
//			'2015-04-04', '2015-04-05', '2015-04-06',
//			'2015-05-01', '2015-05-02', '2015-05-03',
//			'2015-06-20', '2015-06-21', '2015-06-22',
//			'2015-09-26', '2015-09-27',
//			'2015-10-01', '2015-10-02', '2015-10-03', '2015-10-04', '2015-10-05', '2015-10-06', '2015-10-07',
//			'2016-01-01', '2016-01-02', '2016-01-03',
//			'2016-02-07', '2016-02-08', '2016-02-09', '2016-02-10', '2016-02-11', '2016-02-12', '2016-02-13',
//			'2016-04-02', '2016-04-03', '2016-04-04',
//			'2016-04-30', '2016-05-01', '2016-05-02',
//			'2016-06-09', '2016-06-10', '2016-06-11',
//			'2016-09-15', '2016-09-16', '2016-09-17',
//			'2016-10-01', '2016-10-02', '2016-10-03', '2016-10-04', '2016-10-05', '2016-10-06', '2016-10-07',
//			'2016-12-31', '2017-01-01', '2017-01-02',
//		);
		$nolimitday = array(
			'2017-01-27', '2017-01-28', '2017-01-29', '2017-01-30', '2017-01-31', '2017-02-01', '2017-02-02',
			'2017-04-02', '2017-04-03', '2017-04-04',
			'2017-04-29', '2017-04-30', '2017-05-01',
			'2017-05-28', '2017-05-29', '2017-05-30',
			'2017-10-01', '2017-10-02', '2017-10-03', '2017-10-04', '2017-10-05', '2017-10-06', '2017-10-07', '2017-10-08',
		);
//		if($now >= '2015-08-20' && $now <= '2015-09-03'){
//			// 临时单双限号
//			$xh = array(1 => '单', 2 => '双');
//			$d = date('d', time());
//			if ($d%2 == 1) {
//				$j = 1;
//			} else {
//				$j = 2;
//			}
//			$str = "今天是 " . $now . ' ' . $week[$z] . ", 北京限行" . $xh[$j] . "号";
//		} else {
			// 限号轮询
//			if ($now >= '2014-04-14' && $now <= '2014-07-12') {
//				$xh = array(1 => '0 和 5', 2 => '1 和 6', 3 => '2 和 7', 4 => '3 和 8', 5 => '4 和 9');
//			} else if ($now >= '2014-07-13' && $now <= '2014-10-11') {
//				$xh = array(2 => '0 和 5', 3 => '1 和 6', 4 => '2 和 7', 5 => '3 和 8', 1 => '4 和 9');
//			} else if ($now >= '2014-10-12' && $now <= '2015-01-10') {
//				$xh = array(3 => '0 和 5', 4 => '1 和 6', 5 => '2 和 7', 1 => '3 和 8', 2 => '4 和 9');
//			} else if ($now >= '2015-01-11' && $now <= '2015-04-11') {
//				$xh = array(4 => '0 和 5', 5 => '1 和 6', 1 => '2 和 7', 2 => '3 和 8', 3 => '4 和 9');
//			} else if ($now >= '2015-04-12' && $now <= '2015-07-11') {
//				$xh = array(5 => '0 和 5', 1 => '1 和 6', 2 => '2 和 7', 3 => '3 和 8', 4 => '4 和 9');
//			} else if ($now >= '2015-07-12' && $now <= '2015-10-10') {
//				$xh = array(1 => '0 和 5', 2 => '1 和 6', 3 => '2 和 7', 4 => '3 和 8', 5 => '4 和 9');
//			} else if ($now >= '2015-10-11' && $now <= '2016-01-09') {
//				$xh = array(2 => '0 和 5', 3 => '1 和 6', 4 => '2 和 7', 5 => '3 和 8', 1 => '4 和 9');
//			} else if ($now >= '2016-01-10' && $now <= '2016-04-10') {
//				$xh = array(3 => '0 和 5', 4 => '1 和 6', 5 => '2 和 7', 1 => '3 和 8', 2 => '4 和 9');
//			} else if ($now >= '2016-04-11' && $now <= '2016-07-10') {
//				$xh = array(4 => '0 和 5', 5 => '1 和 6', 1 => '2 和 7', 2 => '3 和 8', 3 => '4 和 9');
//			} else if ($now >= '2016-07-11' && $now <= '2016-10-09') {
//				$xh = array(5 => '0 和 5', 1 => '1 和 6', 2 => '2 和 7', 3 => '3 和 8', 4 => '4 和 9');
//			} else if ($now >= '2016-10-11' && $now <= '2017-01-08') {
//				$xh = array(1 => '0 和 5', 2 => '1 和 6', 3 => '2 和 7', 4 => '3 和 8', 5 => '4 和 9');
			if ($now >= '2017-01-09' && $now <= '2017-04-09') {
				$xh = array(2 => '0 和 5', 3 => '1 和 6', 4 => '2 和 7', 5 => '3 和 8', 1 => '4 和 9');
			} else if ($now >= '2017-04-10' && $now <= '2017-07-09') {
				$xh = array(3 => '0 和 5', 4 => '1 和 6', 5 => '2 和 7', 1 => '3 和 8', 2 => '4 和 9');
			} else if ($now >= '2017-07-10' && $now <= '2017-10-08') {
				$xh = array(4 => '0 和 5', 5 => '1 和 6', 1 => '2 和 7', 2 => '3 和 8', 3 => '4 和 9');
			} else if ($now >= '2017-10-09' && $now <= '2018-01-07') {
				$xh = array(5 => '0 和 5', 1 => '1 和 6', 2 => '2 和 7', 3 => '3 和 8', 4 => '4 和 9');
			} else if ($now >= '2018-01-08' && $now <= '2018-04-08') {
				$xh = array(1 => '0 和 5', 2 => '1 和 6', 3 => '2 和 7', 4 => '3 和 8', 5 => '4 和 9');
			}
			//header("Content-Type: text/html; charset=UTF-8");
			if (in_array($now, $nolimitday) || empty($xh[$z])) {
				$str = "今天是 " . $now . ' ' . $week[$z] . ", 北京不限行";
			} else {
				$str = "今天是 " . $now . ' ' . $week[$z] . ", 北京限行尾号：" . $xh[$z];
			}
//		}
		die(json_encode($str));
	}

}
