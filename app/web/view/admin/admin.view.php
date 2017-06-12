<?php

require_once (FRAMEWORK_PATH . 'base/view/smarty.view.php');
require_once (APP_WEB_PATH . 'controller/admin.controller.php');
require_once (APP_WEB_PATH . 'controller/upload.controller.php');
require_once (APP_WEB_PATH . 'controller/article.controller.php');
require_once (APP_WEB_PATH . 'controller/ad.controller.php');
require_once (APP_WEB_PATH . 'controller/flinks.controller.php');

class AdminView extends smartView {

	private $adminController = null;
	private $articleController = null;
	private $adController = null;
	private $uploadController = null;
	private $flinksController = null;

	public function __construct() {
		parent::__construct();

		$this->adminController = new adminController();
		$this->articleController = new articleController();
		$this->adController = new adController();
		$this->uploadController = new uploadController();
		$this->flinksController = new flinksController();
	}

	public function index() {
		$this->caching = false;
		if (!$this->adminController->C_chkLoginStatus()) {
			header("Location: /admin-login/");
		} else {

			$this->display('admin.shtml');
		}
	}

	public function login() {
		$this->caching = false;

		FileHelp::WriteLog(1, 'y', __METHOD__, 'admin', 'admin/');

		$this->display('login.shtml');
	}

	public function loginsub() {
		$this->caching = false;

		$uname = isset($_POST['uname']) ? trim($_POST['uname']) : '';
		$upwd = isset($_POST['upwd']) ? trim($_POST['upwd']) : '';
		$res = $this->adminController->C_login($uname, $upwd);

		$this->adminController->C_addAdminLog(__METHOD__, 'login', '[' . $uname . ']' . serialize($res));
		FileHelp::WriteLog(1, 'y', __METHOD__ . ' [' . $uname . '] ' . serialize($res), 'admin', 'admin/');

		die(json_encode($res));
	}

	public function logout() {
		$this->caching = false;

		$this->adminController->C_addAdminLog(__METHOD__, 'logout', '[' . $_COOKIE['91lsme_uname'] . ']');
		FileHelp::WriteLog(1, 'y', __METHOD__ . ' [' . $_COOKIE['91lsme_uname'] . '] ', 'admin', 'admin/');

		$res = $this->adminController->C_logout();
		header("Location: /admin");
	}

	public function channellist() {
		$this->caching = false;
		if (!$this->adminController->C_chkLoginStatus()) {
			header("Location: /admin-login/");
		} else {
			$_p = (isset($_GET['page']) && !empty($_GET['page'])) ? intval($_GET['page']) : 1;
			$list = $this->articleController->C_getChannelListAdminPage($_p);
			$page = BaseCom::GetPageListAdmin(PAGE_ADMIN_SIZE, $_p, $list['count'], '/admin-channellist', '');

			$this->assign('pageview', $page);
			$this->assign('cpage', $_p);
			$this->assign('list', $list['list']);
			$html = $this->fetch('channellist.shtml');
			die(json_encode($html));
		}
	}

	public function channel() {
		$this->caching = false;
		if (!$this->adminController->C_chkLoginStatus()) {
			header("Location: /admin-login/");
		} else {
			$type = (isset($_GET['type']) && !empty($_GET['type'])) ? trim($_GET['type']) : 'add';
			if ($type == 'add') {
				
			} else {
				$id = (isset($_GET['id']) && !empty($_GET['id'])) ? intval($_GET['id']) : 0;
				if ($id == 0) {
					
				} else {
					// 根据 id 查询 频道信息
					$res = $this->articleController->C_getChannelById($id);
					$this->assign('channel', $res);
				}
			}
			$this->assign('type', $type);
			$html = $this->fetch('channel.shtml');
			die(json_encode($html));
		}
	}

	public function channeledit() {
		$this->caching = false;
		if (!$this->adminController->C_chkLoginStatus()) {
			header("Location: /admin-login/");
		} else {
			$cid = (isset($_POST['cid']) && !empty($_POST['cid'])) ? intval($_POST['cid']) : 0;
			$cname = isset($_POST['cname']) ? htmlspecialchars(trim($_POST['cname'])) : '';
			$is_show = isset($_POST['is_show']) ? intval($_POST['is_show']) : 1;
			$res = false;

			$data['cname'] = $cname;
			$data['is_show'] = $is_show;
			if ($cid == 0) {
				// 新增频道 
				$rtn = $this->articleController->C_addChannel($data);
				$this->adminController->C_addAdminLog(__METHOD__, 'addChannel', 'CID:' . $rtn . ' CNAME:' . $cname);
				if ($rtn) {
					$res = $rtn;
				}
			} else {
				// 修改频道  
				$rtn = $this->articleController->C_upChannel($cid, $data);
				$this->adminController->C_addAdminLog(__METHOD__, 'upChannel', 'CID:' . $cid);
				if ($rtn >= 0) {
					$res = $cid;
				}
			}
		}
		echo json_encode($res);
		die();
	}

	public function articlelist() {
		$this->caching = false;
		if (!$this->adminController->C_chkLoginStatus()) {
			header("Location: /admin-login/");
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
			$html = $this->fetch('articlelist.shtml');
			die(json_encode($html));
		}
	}

	public function article() {
		$this->caching = false;
		if (!$this->adminController->C_chkLoginStatus()) {
			header("Location: /admin-login/");
		} else {
			// 获取频道信箱
			$clist = $this->articleController->C_getChannelList(100);
			$this->assign('clist', $clist);
			$type = (isset($_GET['type']) && !empty($_GET['type'])) ? trim($_GET['type']) : 'add';
			if ($type == 'add') {
				
			} else {
				$id = (isset($_GET['id']) && !empty($_GET['id'])) ? intval($_GET['id']) : 0;
				if ($id == 0) {
					
				} else {
					// 根据 id 查询 频道信息
					$res = $this->articleController->C_getArticleById($id, true);
					$res['content'] = htmlspecialchars($res['content']);
					$this->assign('article', $res);
				}
			}
			$this->assign('type', $type);
			$html = $this->fetch('article.shtml');
			die(json_encode($html));
		}
	}

	public function articleedit() {
		$this->caching = false;
		if (!$this->adminController->C_chkLoginStatus()) {
			header("Location: /admin-login/");
		} else {
			$id = (isset($_POST['id']) && !empty($_POST['id'])) ? intval($_POST['id']) : 0;
			$title = isset($_POST['title']) ? htmlspecialchars(trim($_POST['title'])) : '';
			$is_link = isset($_POST['is_link']) ? intval($_POST['is_link']) : 0;
			$jumpurl = isset($_POST['jumpurl']) ? htmlspecialchars(trim($_POST['jumpurl'])) : '';
			$cid = isset($_POST['cid']) ? intval($_POST['cid']) : 0;
			$content = isset($_POST['content']) ? trim($_POST['content']) : '';
			$is_show = isset($_POST['is_show']) ? intval($_POST['is_show']) : 1;
			$up = isset($_POST['is_up']) ? intval($_POST['is_up']) : 0;
			$rel_link = isset($_POST['rel_link']) ? htmlspecialchars(trim($_POST['rel_link'])) : '';
			$res = false;

			$data['title'] = $title;
			$data['is_link'] = $is_link;
			if ($is_link == 1) {
				$data['jumpurl'] = $jumpurl;
			}
			$data['cid'] = $cid;
			$data['content'] = $content;
			$data['is_show'] = $is_show;
			$data['up'] = $up;
			$data['rel_link'] = $rel_link;
			if ($id == 0) {
				// 新增文章
				$data['create_time'] = date('Y-m-d H:i:s', time());
				$rtn = $this->articleController->C_addArticle($data);
				$this->adminController->C_addAdminLog(__METHOD__, 'addArtirle', 'ID:' . $rtn . ' TITLE:' . $title);
				if ($rtn) {
					$res = $rtn;
				}
			} else {
				// 修改文章  
				$data['up_time'] = date('Y-m-d H:i:s', time());
				$rtn = $this->articleController->C_upArticle($id, $data);
				$this->adminController->C_addAdminLog(__METHOD__, 'upArtirle', 'ID:' . $id);
				if ($rtn >= 0) {
					$res = $id;
				}
			}
			echo json_encode($res);
			die();
		}
	}

	public function articledelete() {
		$this->caching = false;
		if (!$this->adminController->C_chkLoginStatus()) {
			header("Location: /admin-login/");
		} else {
			$ids = isset($_POST['ids']) ? trim($_POST['ids']) : '';
			$res['success'] = false;
			$res['errorMsg'] = '删除失败';
			if (!empty($ids)) {
				$rtn = $this->articleController->C_delArticle($ids);
				if ($rtn) {
					$this->adminController->C_addAdminLog(__METHOD__, 'delArtirle', 'IDS:' . $ids);
					$res['success'] = true;
					$res['errorMsg'] = '删除成功';
				}
			}
			echo json_encode($res);
			die();
		}
	}

	public function articlerecyclelist() {
		$this->caching = false;
		if (!$this->adminController->C_chkLoginStatus()) {
			header("Location: /admin-login/");
		} else {
			$_p = (isset($_GET['page']) && !empty($_GET['page'])) ? intval($_GET['page']) : 1;
			$list = $this->articleController->C_getArticleListRecycleAdminPage(0, $_p);
			$page = BaseCom::GetPageListAdmin(PAGE_ADMIN_SIZE, $_p, $list['count'], '/admin-articlerecyclelist', '');

			$this->assign('pageview', $page);
			$this->assign('cpage', $_p);
			$this->assign('list', $list['list']);
			$html = $this->fetch('recyclelist.shtml');
			die(json_encode($html));
		}
	}

	public function articlerecycle() {
		$this->caching = false;
		if (!$this->adminController->C_chkLoginStatus()) {
			header("Location: /admin-login/");
		} else {
			$ids = isset($_POST['ids']) ? trim($_POST['ids']) : '';
			$res['success'] = false;
			$res['errorMsg'] = '恢复失败';
			if (!empty($ids)) {
				$rtn = $this->articleController->C_recArticle($ids);
				if ($rtn) {
					$this->adminController->C_addAdminLog(__METHOD__, 'recArtirle', 'IDS:' . $ids);
					$res['success'] = true;
					$res['errorMsg'] = '恢复成功';
				}
			}
			echo json_encode($res);
			die();
		}
	}

	public function config() {
		$this->caching = false;
		if (!$this->adminController->C_chkLoginStatus()) {
			header("Location: /admin-login/");
		} else {
			$file = APP_WEB_PATH . 'config/smarty.conf';

			$this->assign('cdn_url', FileHelp::get_config($file, 'cdn_url'));
			$this->assign('web_url', FileHelp::get_config($file, 'web_url'));
			$this->assign('web_title', FileHelp::get_config($file, 'web_title'));
			$this->assign('admin_title', FileHelp::get_config($file, 'admin_title'));
			$this->assign('web_keywords', FileHelp::get_config($file, 'web_keywords'));
			$this->assign('web_description', FileHelp::get_config($file, 'web_description'));

			$html = $this->fetch('config.shtml');
			die(json_encode($html));
		}
	}

	public function configedit() {
		$this->caching = false;
		if (!$this->adminController->C_chkLoginStatus()) {
			header("Location: /admin-login/");
		} else {
			if (!$this->adminController->C_chkAdminSuper()) {
				$this->showerror(-200, 'code');
			}

			$file = APP_WEB_PATH . 'config/smarty.conf';
			$limit_array = array('cdn_url', 'web_url', 'web_title', 'admin_title', 'web_keywords', 'web_description');
			foreach ($_POST as $k => $v) {
				if (in_array($k, $limit_array)) {
					FileHelp::update_config($file, $k, $v);
				}
			}

			$this->adminController->C_addAdminLog(__METHOD__, 'upconfig', serialize($_POST));

			echo true;
			die();
		}
	}

	public function status() {
		$this->caching = false;
		if (!$this->adminController->C_chkLoginStatus()) {
			header("Location: /admin-login/");
		} else {
			$count['user'] = $this->adminController->C_getAdminCount();
			$count['article'] = $this->articleController->C_getArticleCountByCid(0, 0, true);

			$this->assign('count', $count);
			$html = $this->fetch('index.shtml');
			die(json_encode($html));
		}
	}

	public function staticinfo() {
		$this->caching = false;
		if (!$this->adminController->C_chkLoginStatus()) {
			header("Location: /admin-login/");
		} else {
			$count['user'] = $this->adminController->C_getAdminCount();
			$count['article'] = $this->articleController->C_getArticleCountByCid(0, 0, true);

			$this->assign('st_mode', PAGE_STATIC);
			$this->assign('count', $count);
			$html = $this->fetch('staticinfo.shtml');
			die(json_encode($html));
		}
	}

	public function accountlist() {
		$this->caching = false;
		if (!$this->adminController->C_chkLoginStatus()) {
			header("Location: /admin-login/");
		} else {
			$_p = (isset($_GET['page']) && !empty($_GET['page'])) ? intval($_GET['page']) : 1;
			$list = $this->adminController->C_getAdminListAdminPage($_p);
			$page = BaseCom::GetPageListAdmin(PAGE_ADMIN_SIZE, $_p, $list['count'], '/admin-accountlist', '');

			$this->assign('pageview', $page);
			$this->assign('cpage', $_p);
			$this->assign('list', $list['list']);
			$html = $this->fetch('accountlist.shtml');
			die(json_encode($html));
		}
	}

	public function account() {
		$this->caching = false;
		if (!$this->adminController->C_chkLoginStatus()) {
			header("Location: /admin-login/");
		} else {
			if (!$this->adminController->C_chkAdminSuper()) {
				$this->showerror(-200);
			}

			$type = (isset($_GET['type']) && !empty($_GET['type'])) ? trim($_GET['type']) : 'add';
			if ($type == 'add') {
				
			} else {
				$id = (isset($_GET['id']) && !empty($_GET['id'])) ? intval($_GET['id']) : 0;
				if ($id == 0) {
					
				} else {
					// 根据 id 查询 管理员信息
					$res = $this->adminController->C_getAdminById($id);
					$this->assign('account', $res);
				}
			}
			$this->assign('type', $type);
			$html = $this->fetch('account.shtml');
			die(json_encode($html));
		}
	}

	public function accountedit() {
		$this->caching = false;
		if (!$this->adminController->C_chkLoginStatus()) {
			header("Location: /admin-login/");
		} else {
			if (!$this->adminController->C_chkAdminSuper()) {
				$this->showerror(-200, 'code');
			}

			$id = (isset($_POST['id']) && !empty($_POST['id'])) ? intval($_POST['id']) : 0;
			$uname = isset($_POST['uname']) ? htmlspecialchars(trim($_POST['uname'])) : '';
			$upwd = isset($_POST['upwd']) ? htmlspecialchars(trim($_POST['upwd'])) : '';
			$is_show = isset($_POST['is_show']) ? intval($_POST['is_show']) : 1;
			$res = false;

			$data['uname'] = $uname;
			$data['is_show'] = $is_show;
			if (!empty($upwd)) {
				$data['upwd'] = md5($upwd);
			}

			if ($id == 0) {
				// 新增管理员
				$data['create_time'] = date('Y-m-d H:i:s', time());
				$rtn = $this->adminController->C_addAdmin($data);
				$this->adminController->C_addAdminLog(__METHOD__, 'addAdmin', 'ID:' . $rtn . ' UNAME:' . $uname);
				if ($rtn) {
					$res = $rtn;
				}
			} else {
				// 修改管理员 
				$rtn = $this->adminController->C_upAdmin($id, $data);
				$this->adminController->C_addAdminLog(__METHOD__, 'upAdmin', 'ID:' . $id);
				if ($rtn >= 0) {
					$res = $id;
				}
			}
		}
		echo json_encode($res);
	}

	public function adlist() {
		$this->caching = false;
		if (!$this->adminController->C_chkLoginStatus()) {
			header("Location: /admin-login/");
		} else {
			$_p = (isset($_GET['page']) && !empty($_GET['page'])) ? intval($_GET['page']) : 1;
			$list = $this->adController->C_getAdListAdminPage($_p);
			$page = BaseCom::GetPageListAdmin(PAGE_ADMIN_SIZE, $_p, $list['count'], '/admin-adlist', '');

			$this->assign('pageview', $page);
			$this->assign('cpage', $_p);
			$this->assign('list', $list['list']);
			$html = $this->fetch('adlist.shtml');
			die(json_encode($html));
		}
	}

	public function ad() {
		$this->caching = false;
		if (!$this->adminController->C_chkLoginStatus()) {
			header("Location: /admin-login/");
		} else {
			$type = (isset($_GET['type']) && !empty($_GET['type'])) ? trim($_GET['type']) : 'add';
			if ($type == 'add') {
				
			} else {
				$id = (isset($_GET['id']) && !empty($_GET['id'])) ? intval($_GET['id']) : 0;
				if ($id == 0) {
					
				} else {
					// 根据 id 查询 广告信息
					$res = $this->adController->C_getAdById($id);
					$this->assign('ad', $res);
				}
			}
			$this->assign('type', $type);
			$html = $this->fetch('ad.shtml');
			die(json_encode($html));
		}
	}

	public function adedit() {
		$this->caching = false;
		if (!$this->adminController->C_chkLoginStatus()) {
			header("Location: /admin-login/");
		} else {
			$id = (isset($_POST['id']) && !empty($_POST['id'])) ? intval($_POST['id']) : 0;
			$title = isset($_POST['title']) ? htmlspecialchars(trim($_POST['title'])) : '';
			$imgurl = isset($_POST['imgurl']) ? htmlspecialchars(trim($_POST['imgurl'])) : '';
			$is_link = isset($_POST['is_link']) ? intval($_POST['is_link']) : 0;
			$jumpurl = isset($_POST['jumpurl']) ? htmlspecialchars(trim($_POST['jumpurl'])) : '';
			$res = false;

			// 处理图片
			if (empty($_FILES) === false) {
				$res = $this->uploadController->C_uploadfiles($_FILES, 'cimgurl', 'ad', false);
				$res = json_decode($res);
				if ($res->error == 0) {
					$data['imgurl'] = $res->url;
				} else {
					$data['imgurl'] = $res->message;
				}
			}

			$data['title'] = $title;
			$data['is_link'] = $is_link;
			if ($is_link == 1) {
				$data['jumpurl'] = $jumpurl;
			}
			if ($id == 0) {
				// 新增广告
				$data['create_time'] = date('Y-m-d H:i:s', time());
				$rtn = $this->adController->C_addAd($data);
				$this->adminController->C_addAdminLog(__METHOD__, 'addAd', 'ID:' . $rtn);
				if ($rtn) {
					$res = $rtn;
				}
			} else {
				// 修改广告
				$rtn = $this->adController->C_upAd($id, $data);
				$this->adminController->C_addAdminLog(__METHOD__, 'upAd', 'ID:' . $id);
				if ($rtn >= 0) {
					$res = $id;
				}
			}
		}
		echo json_encode($res);
		die();
	}

	public function addelete() {
		$this->caching = false;
		if (!$this->adminController->C_chkLoginStatus()) {
			header("Location: /admin-login/");
		} else {
			$ids = isset($_POST['ids']) ? trim($_POST['ids']) : '';
			$res['success'] = false;
			$res['errorMsg'] = '删除失败';
			if (!empty($ids)) {
				$rtn = $this->adController->C_delAd($ids);
				if ($rtn) {
					$this->adminController->C_addAdminLog(__METHOD__, 'delAd', 'IDS:' . $ids);
					$res['success'] = true;
					$res['errorMsg'] = '删除成功';
				}
			}
			echo json_encode($res);
			die();
		}
	}

	public function flinkslist() {
		$this->caching = false;
		if (!$this->adminController->C_chkLoginStatus()) {
			header("Location: /admin-login/");
		} else {
			$_p = (isset($_GET['page']) && !empty($_GET['page'])) ? intval($_GET['page']) : 1;
			$list = $this->flinksController->C_getFLinksListAdminPage($_p);
			$page = BaseCom::GetPageListAdmin(PAGE_ADMIN_SIZE, $_p, $list['count'], '/admin-flinkslist', '');

			$this->assign('pageview', $page);
			$this->assign('cpage', $_p);
			$this->assign('list', $list['list']);
			$html = $this->fetch('flinkslist.shtml');
			die(json_encode($html));
		}
	}

	public function flinks() {
		$this->caching = false;
		if (!$this->adminController->C_chkLoginStatus()) {
			header("Location: /admin-login/");
		} else {
			$type = (isset($_GET['type']) && !empty($_GET['type'])) ? trim($_GET['type']) : 'add';
			if ($type == 'add') {
				
			} else {
				$id = (isset($_GET['id']) && !empty($_GET['id'])) ? intval($_GET['id']) : 0;
				if ($id == 0) {
					
				} else {
					// 根据 id 查询 广告信息
					$res = $this->flinksController->C_getFLinksById($id);
					$this->assign('flinks', $res);
				}
			}
			$this->assign('type', $type);
			$html = $this->fetch('flinks.shtml');
			die(json_encode($html));
		}
	}

	public function flinksedit() {
		$this->caching = false;
		if (!$this->adminController->C_chkLoginStatus()) {
			header("Location: /admin-login/");
		} else {
			$id = (isset($_POST['id']) && !empty($_POST['id'])) ? intval($_POST['id']) : 0;
			$fname = isset($_POST['fname']) ? htmlspecialchars(trim($_POST['fname'])) : '';
			$flink = isset($_POST['flink']) ? htmlspecialchars(trim($_POST['flink'])) : '';
			$is_show = isset($_POST['is_show']) ? intval($_POST['is_show']) : 0;
			$orderby = isset($_POST['orderby']) ? intval($_POST['orderby']) : 0;
			$res = false;

			$data['fname'] = $fname;
			$data['flink'] = $flink;
			$data['is_show'] = $is_show;
			$data['orderby'] = $orderby;
			if ($id == 0) {
				// 新增友链
				$rtn = $this->flinksController->C_addFLinks($data);
				$this->adminController->C_addAdminLog(__METHOD__, 'addFlinks', 'ID:' . $rtn);
				if ($rtn) {
					$res = $rtn;
				}
			} else {
				// 修改友链
				$rtn = $this->flinksController->C_upFLinks($id, $data);
				$this->adminController->C_addAdminLog(__METHOD__, 'upFlinks', 'ID:' . $id);
				if ($rtn >= 0) {
					$res = $id;
				}
			}
		}
		echo json_encode($res);
		die();
	}

	public function flinksdelete() {
		$this->caching = false;
		if (!$this->adminController->C_chkLoginStatus()) {
			header("Location: /admin-login/");
		} else {
			$ids = isset($_POST['ids']) ? trim($_POST['ids']) : '';
			$res['success'] = false;
			$res['errorMsg'] = '删除失败';
			if (!empty($ids)) {
				$rtn = $this->flinksController->C_delFLinks($ids);
				if ($rtn) {
					$this->adminController->C_addAdminLog(__METHOD__, 'delFLinks', 'IDS:' . $ids);
					$res['success'] = true;
					$res['errorMsg'] = '删除成功';
				}
			}
			echo json_encode($res);
			die();
		}
	}

	public function upcache() {
		$_act = isset($_POST['act']) ? trim($_POST['act']) : 'cache';
		$_type = isset($_POST['type']) ? trim($_POST['type']) : 'index';
		switch ($_type) {
			case "index":
				if ($_act == 'cache') {
					$this->clearCache(APP_WEB_PATH . '/templates/default/html/index.shtml');
				} else {
					HttpHelp::CurlRequest(WEB_SITES_URL . "/default-release");
				}
				break;
			case "list":
				// 清除所有列表静态页
				if (PAGE_STATIC) {
					$this->unlinkallfile(APP_WEB_PATH . '/templates/articlelist/static');
				}
				// 获取频道列表
				$list = $this->articleController->C_getChannelList(0);
				if ($list && is_array($list)) {
					foreach ($list as $k => $v) {
						// 获取频道下文章数量
						$count = $this->articleController->C_getArticleCountByCid($v['id']);
						// 计算总分页数量
						$c_p = ceil($count['count'] / PAGE_SIZE);
						for ($i = 1; $i <= $c_p; $i++) {
							if ($_act == 'cache') {
								$this->clearCache(APP_WEB_PATH . '/templates/articlelist/html/articlelist.shtml', $v['id'] . '_' . $i);
							} else {
								HttpHelp::CurlRequest(WEB_SITES_URL . "/articlelist-release?type=" . $v['id'] . "&page=" . $i);
							}
						}
					}
				}
				break;
			case "article":
				// 清除所有文章静态页
				if (PAGE_STATIC) {
					$this->unlinkallfile(APP_WEB_PATH . '/templates/article/static');
				}
				// 获取所有文章
				$list = $this->articleController->C_getArticleListByCid(0, 0, 0);
				if ($list && is_array($list)) {
					foreach ($list as $k => $v) {
						if ($_act == 'cache') {
							$this->clearCache(APP_WEB_PATH . '/templates/article/html/article.shtml', $v['id']);
						} else {
							HttpHelp::CurlRequest(WEB_SITES_URL . "/article-release?id=" . $v['id']);
						}
					}
				}
				break;
			default :
				break;
		}
		die(true);
	}

	public function upacticle() {
		$_id = isset($_POST['id']) ? intval(trim($_POST['id'])) : '0';
		$_cid = isset($_POST['cid']) ? intval(trim($_POST['cid'])) : '0';

		// 更新文章
		HttpHelp::CurlRequest(WEB_SITES_URL . "/article-release?id=" . $_id);

		// 更新列表页
		$count = $this->articleController->C_getArticleCountByCid($_cid); // 获取频道下文章数量
		$c_p = ceil($count['count'] / PAGE_SIZE); // 计算总分页数量
		for ($i = 1; $i <= $c_p; $i++) {
			HttpHelp::CurlRequest(WEB_SITES_URL . "/articlelist-release?type=" . $_cid . "&page=" . $i);
		}
		die(true);
	}

	public function unlinkallfile($dir) {
		$dh = opendir($dir);
		while (($file = readdir($dh)) !== false) {
			$fullpath = $dir . "/" . $file;
			if (!is_dir($fullpath)) {
				$info = pathinfo($fullpath);
				if ($info['extension'] == 'shtml') {
					@unlink($fullpath);
				}
			}
		}
		closedir($dh);
	}

	public function showerror($ecode, $type = 'html') {
		switch ($ecode) {
			case -200:
				$str = '对不起，暂无权限！！！';
				break;
			default :
				break;
		}
		if ($type == 'html') {
			$this->assign('str', $str);
			$html = $this->fetch('error.shtml');
			die(json_encode($html));
		} else {
			die(json_encode($ecode));
		}
	}

}

?>