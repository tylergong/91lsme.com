<?php

require_once(FRAMEWORK_PATH . 'base/view/smarty.view.php');
require_once(APP_WEB_PATH . 'controller/admin.controller.php');
require_once(APP_WEB_PATH . 'controller/article.controller.php');

class adminnewView extends smartView {

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
			header("Location: /adminnew-login");
		} else {

			$this->display('index.html');
		}
	}

	// 登录页
	public function login() {
		$this->caching = false;

		FileHelp::WriteLog(1, 'y', __METHOD__, 'admin', 'admin/');

		$this->display('login.html');
	}

	// 提交登录数据
	public function loginsub() {
		$this->caching = false;
		$uname = isset($_POST['uname']) ? trim($_POST['uname']) : '';
		$upwd = isset($_POST['upwd']) ? trim($_POST['upwd']) : '';

		$res = $this->adminController->C_login($uname, $upwd);

		$this->adminController->C_addAdminLog(__METHOD__, 'login', '[' . $uname . ']' . serialize($res));
		FileHelp::WriteLog(1, 'y', __METHOD__ . ' [' . $uname . '] ' . serialize($res), 'admin', 'admin/');

		die(json_encode($res));
	}

	// 退出登录（锁屏处理）
	public function lock() {
		$this->caching = false;

		$this->adminController->C_addAdminLog(__METHOD__, 'logout', '[' . $_COOKIE['91lsme_uname'] . ']');
		FileHelp::WriteLog(1, 'y', __METHOD__ . ' [' . $_COOKIE['91lsme_uname'] . '] ', 'admin', 'admin/');

		$this->adminController->C_logout();
	}

	// 退出登录（注销处理，跳转至首页）
	public function logout() {
		$this->caching = false;

		$this->adminController->C_addAdminLog(__METHOD__, 'logout', '[' . $_COOKIE['91lsme_uname'] . ']');
		FileHelp::WriteLog(1, 'y', __METHOD__ . ' [' . $_COOKIE['91lsme_uname'] . '] ', 'admin', 'admin/');

		$this->adminController->C_logout();
		header("Location: /adminnew");
	}

	// 频道列表
	public function channellist() {
		$this->caching = false;
		if(!$this->adminController->C_chkLoginStatus()) {
			die('Login information missing, please log in again');
		} else {
			$_p = (isset($_GET['page']) && !empty($_GET['page'])) ? intval($_GET['page']) : 1;
			$list = $this->articleController->C_getChannelListAdminPage($_p);
			$this->assign('list', $list['list']);
			$this->display('channellist.html');
		}
	}

	// 文章列表
	public function articlelist() {
		$this->caching = false;
		if($this->request->is_ajax()) {
			if(!$this->adminController->C_chkLoginStatus()) {
				die('Login information missing, please log in again');
			} else {
				$_p = (isset($_GET['page']) && !empty($_GET['page'])) ? intval($_GET['page']) : 1;
				$_cid = (isset($_GET['cid']) && !empty($_GET['cid'])) ? intval($_GET['cid']) : 0;

				$clist = $this->articleController->C_getChannelList();

				$list = $this->articleController->C_getArticleListAdminPage($_cid, $_p);
				$page = BaseCom::GetPageListAdmin(PAGE_ADMIN_SIZE, $_p, $list['count'], '/admin-articlelist', 'cid=' . $_cid);

				$this->assign('pageview', $page);
				$this->assign('cpage', $_p);
				$this->assign('list', $list['list']);
				$this->assign('cid', $_cid);
				$this->assign('clist', $clist);
				$html = $this->fetch('articlelist_ajax.shtml');
				die(json_encode($html));
			}
		} else {
			if(!$this->adminController->C_chkLoginStatus()) {
				header("Location: /adminnew-login");
			} else {
				$this->display('articlelist.html');
			}
		}
	}

}