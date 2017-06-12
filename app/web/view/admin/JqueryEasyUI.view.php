<?php

require_once (FRAMEWORK_PATH . 'base/view/smarty.view.php');
require_once (APP_WEB_PATH . 'controller/admin.controller.php');
require_once (APP_WEB_PATH . 'controller/JqueryEasyUI.controller.php');

class JqueryEasyUIView extends smartView {

	private $adminController = null;
	private $JqueryEasyUIController = null;

	public function __construct() {
		parent::__construct();

		$this->adminController = new adminController();
		$this->JqueryEasyUIController = new JqueryEasyUIController();
	}

	public function articlelist() {
		$this->caching = false;
		if (!$this->adminController->C_chkLoginStatus()) {
			header("Location: /admin-login/");
		} else {

			$html = $this->fetch('JqueryEasyUI/articlelist.shtml');
			die(json_encode($html));
		}
	}

	public function getarticlelist() {

		$_page = (isset($_GET['page']) && !empty($_GET['page'])) ? intval($_GET['page']) : 1;
		$_rows = (isset($_GET['rows']) && !empty($_GET['rows'])) ? intval($_GET['rows']) : 10;
		$_sort = (isset($_GET['sort']) && !empty($_GET['sort'])) ? trim($_GET['sort']) : 'id';
		$_order = (isset($_GET['order']) && !empty($_GET['order'])) ? trim($_GET['order']) : 'desc';
		$_cid = (isset($_GET['cid']) && !empty($_GET['cid'])) ? intval($_GET['cid']) : 0;

		$list = $this->JqueryEasyUIController->C_getArticleListNoDelByAdmin($_cid, $_page, $_rows, array($_sort => $_order));

		die(json_encode($list));
	}

	public function delarticle() {
		$this->caching = false;
		if (!$this->adminController->C_chkLoginStatus()) {
			header("Location: /admin-login/");
		} else {
			$ids = isset($_POST['ids']) ? trim($_POST['ids']) : '';
			$res['success'] = false;
			$res['errorMsg'] = '删除失败';
			if (!empty($ids)) {
				$rtn = $this->JqueryEasyUIController->C_delArticle($ids);
				if ($rtn) {
					$res['success'] = true;
					$res['errorMsg'] = '删除成功';
				}
			}
			echo json_encode($res);
			die();
		}
	}

	public function savearticle() {
		$this->caching = false;
		if (!$this->adminController->C_chkLoginStatus()) {
			header("Location: /admin-login/");
		} else {
			$id = (isset($_POST['id']) && !empty($_POST['id'])) ? intval($_POST['id']) : 0;
			$title = isset($_POST['title']) ? trim($_POST['title']) : '';
			$is_link = isset($_POST['is_link']) ? intval($_POST['is_link']) : 0;
			$jumpurl = isset($_POST['jumpurl']) ? trim($_POST['jumpurl']) : '';
			$cid = isset($_POST['cid']) ? intval($_POST['cid']) : 0;
			$content = isset($_POST['content']) ? trim($_POST['content']) : '';
			$is_show = isset($_POST['is_show']) ? intval($_POST['is_show']) : 1;
			$res = false;

			$data['title'] = $title;
			$data['is_link'] = $is_link;
			if ($is_link == 1) {
				$data['jumpurl'] = $jumpurl;
			}
			$data['cid'] = $cid;
			$data['content'] = $content;
			$data['is_show'] = $is_show;

			if ($id == 0) {
				// 新增文章
				$data['create_time'] = date('Y-m-d H:i:s', time());
				$rtn = $this->JqueryEasyUIController->add_article($data);
			} else {
				// 修改文章  
				$data['up_time'] = date('Y-m-d H:i:s', time());
				$rtn = $this->JqueryEasyUIController->up_article($id, $data);
			}
			if ($rtn) {
				$res = true;
			}
			echo json_encode($res);
			die();
		}
	}

	public function getarticlechannel() {
		$list = $this->JqueryEasyUIController->C_getChannelList();
		$data = null;
		if ($list && is_array($list)) {
			foreach ($list as $key => $val) {
				$data[$key]['cid'] = $val['id'];
				$data[$key]['channel'] = $val['cname'];
			}
		}
		die(json_encode($data));
	}

	public function channellist() {
		$this->caching = false;
		if (!$this->adminController->C_chkLoginStatus()) {
			header("Location: /admin-login/");
		} else {

			$html = $this->fetch('JqueryEasyUI/channellist.shtml');
			die(json_encode($html));
		}
	}

	public function getchannellist() {
		$_page = (isset($_GET['page']) && !empty($_GET['page'])) ? intval($_GET['page']) : 1;
		$_rows = (isset($_GET['rows']) && !empty($_GET['rows'])) ? intval($_GET['rows']) : 10;
		$_sort = (isset($_GET['sort']) && !empty($_GET['sort'])) ? trim($_GET['sort']) : 'id';
		$_order = (isset($_GET['order']) && !empty($_GET['order'])) ? trim($_GET['order']) : 'desc';

		$list = $this->JqueryEasyUIController->C_getChannelListByAdmin($_page, $_rows, array($_sort => $_order));

		die(json_encode($list));
	}

	public function delchannel() {
		$this->caching = false;
		if (!$this->adminController->C_chkLoginStatus()) {
			header("Location: /admin-login/");
		} else {
			$ids = isset($_POST['ids']) ? trim($_POST['ids']) : '';
			$res['success'] = false;
			$res['errorMsg'] = '删除失败';
			if (!empty($ids)) {
				$rtn = $this->JqueryEasyUIController->C_delChannel($ids);
				if ($rtn) {
					$res['success'] = true;
					$res['errorMsg'] = '删除成功';
				}
			}
			echo json_encode($res);
			die();
		}
	}

	public function savechannel() {
		$this->caching = false;
		if (!$this->adminController->C_chkLoginStatus()) {
			header("Location: /admin-login/");
		} else {
			$id = (isset($_POST['id']) && !empty($_POST['id'])) ? intval($_POST['id']) : 0;
			$cname = isset($_POST['cname']) ? trim($_POST['cname']) : '';
			$is_show = isset($_POST['is_show']) ? intval($_POST['is_show']) : 1;
			$res = false;

			$data['cname'] = $cname;
			$data['is_show'] = $is_show;

			if ($id == 0) {
				// 新增频道
				$rtn = $this->JqueryEasyUIController->C_addChannel($data);
			} else {
				// 修改频道  
				$rtn = $this->JqueryEasyUIController->C_upChannel($id, $data);
			}
			if ($rtn) {
				$res = true;
			}
			echo json_encode($res);
			die();
		}
	}

}
