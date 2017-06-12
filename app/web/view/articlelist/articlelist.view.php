<?php

require_once(FRAMEWORK_PATH . 'base/view/smarty.view.php');
require_once(APP_WEB_PATH . 'controller/article.controller.php');
require_once(APP_WEB_PATH . 'controller/ad.controller.php');
require_once(APP_WEB_PATH . 'controller/flinks.controller.php');

class ArticlelistView extends smartView {

	private $articleController = null;
	private $adController = null;
	private $flinksController = null;

	public function __construct() {
		parent::__construct();

		$this->articleController = new articleController();
		$this->adController = new adController();
		$this->flinksController = new flinksController();
	}

	/**
	 * 缓存模式
	 *  /article?id=n
	 */
	public function index() {
		$this->caching = PAGE_STATIC ? false : PAGE_MODEL;

		$_type = (isset($_GET['type'])) ? intval($_GET['type']) : 1;
		$_p = (isset($_GET['page'])) ? intval($_GET['page']) : 1;

		if(!$this->isCached('articlelist.shtml', $_type . '_' . $_p)) {
			$this->setpagedata($_type, $_p);
		}

		$this->display('articlelist.shtml', $_type . '_' . $_p);
	}

	/**
	 * 静态页模式
	 *    /articlelist/t_p.shtml
	 */
	public function release() {
		$this->caching = false;
		$_type = (isset($_GET['type'])) ? intval($_GET['type']) : 1;
		$_p = (isset($_GET['page'])) ? intval($_GET['page']) : 1;

		$this->setpagedata($_type, $_p);

		$html = $this->fetch('articlelist.shtml');
		$file = APP_WEB_PATH . '/templates/articlelist/static/' . $_type . '_' . $_p . '.shtml';
		FileHelp::BuildHtml($file, $html);
	}

	public function setpagedata($_type, $_p) {
		// 获取文章列表
		$list = $this->articleController->C_getArticleListByCidPage($_type, $_p);
		if(PAGE_STATIC) {
			$page = BaseCom::GetPageListStatic(PAGE_SIZE, $_p, $list['count'], PAGE_SPACE, '/articlelist', $_type);
		} else {
			$page = BaseCom::GetPageList(PAGE_SIZE, $_p, $list['count'], PAGE_SPACE, '/articlelist', 'type=' . $_type);
		}
		$this->assign('type', $_type);
		$this->assign('pageview', $page);
		$this->assign('list', $list['list']);
	}

	public function show() {
		$this->caching = false;
		$cid = isset($_GET['cid']) ? $_GET['cid'] : 0;
		$cur = isset($_GET['p']) ? $_GET['p'] : 1;
		$list = $this->articleController->C_getArticleListAdminPage($cid, $cur);
		die(json_encode($list));
	}

	public function del() {
		$this->caching = false;
		$ids = isset($_GET['ids']) ? $_GET['ids'] : '';
		if(empty($ids)) {
			return false;
		}
		$ids = implode(',', json_decode($ids));
		$rtn = $this->articleController->C_delArticle($ids);
		die(json_encode($rtn));
	}

	public function detail() {
		$this->caching = false;
		$id = isset($_GET['id']) ? $_GET['id'] : '';
		if(empty($id)) {
			return false;
		}
		$detail = $this->articleController->C_getArticleById($id,true);
		die(json_encode($detail));
	}

	public function save() {
		$this->caching = false;
		$detail = isset($_POST['detail']) ? $_POST['detail'] : '';
		if(empty($detail)) {
			return false;
		}
		$detail = json_decode($detail);

		$data['title'] = $detail->title;
		$data['is_link'] = $detail->is_link;
		$data['jumpurl'] = $detail->jumpurl;
		$data['cid'] = $detail->cid;
		$data['content'] = $detail->content;
		$data['is_show'] = $detail->is_show;
		$data['up'] = $detail->up;
		$data['rel_link'] = $detail->rel_link;
		$data['up_time'] = date('Y-m-d H:i:s', time());

		$res = $this->articleController->C_upArticle($detail->id,$data);
		die(json_encode($res));
	}
}
