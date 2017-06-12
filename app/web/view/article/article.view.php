<?php

require_once (FRAMEWORK_PATH . 'base/view/smarty.view.php');
require_once (APP_WEB_PATH . 'controller/article.controller.php');
require_once (APP_WEB_PATH . 'controller/ad.controller.php');
require_once (APP_WEB_PATH . 'controller/flinks.controller.php');

class ArticleView extends smartView {

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

		$_id = (isset($_GET['id'])) ? intval($_GET['id']) : 0;

		if (!$this->isCached('article.shtml', $_id)) {
			$this->setpagedata($_id);
		}

		$this->display('article.shtml', $_id);
	}

	/**
	 * 静态页模式
	 * 	/article/n.shtml
	 */
	public function release() {
		$this->caching = false;
		$_id = (isset($_GET['id'])) ? intval($_GET['id']) : 0;

		$this->setpagedata($_id);

		$html = $this->fetch('article.shtml');
		$file = APP_WEB_PATH . '/templates/article/static/' . $_id . '.shtml';
		FileHelp::BuildHtml($file, $html);
	}

	public function setpagedata($_id) {
		// 获取文章内容
		$data = $this->articleController->C_getArticleById($_id);
		if (empty($data)) {
			BaseCom::ShowError();
		}
		$this->assign('data', $data);

		// 获取上下篇章
		$updown = $this->articleController->C_getArticleByIdForUpDown($_id, $data['cid']);
		$this->assign('updown', $updown);
	}

}
