<?php

require_once(FRAMEWORK_PATH . 'base/controller/Base.controller.php');

class JqueryEasyUIController extends BaseController {

	private $IDBModel = null;

	public function init() {
		$defaultDbConfig = '';
		require(APP_WEB_PATH . 'config/db.conf.php');
		$this->IDBModel = new IDBModel($defaultDbConfig);
	}

	/**
	 * 统计频道数量
	 *
	 * @return type
	 */
	public function C_getChannelCount() {
		$arr['table'] = 'ls_channel';
		$res = $this->IDBModel->M_getCount($arr);
		return $res;
	}

	/**
	 * 获取频道列表
	 *
	 * @param type $limit
	 * @param type $offset
	 *
	 * @return type
	 */
	public function C_getChannelList($limit = 10, $offset = 0, $orderby = '') {
		$arr['limit'] = array($limit,
							  $offset);
		if(empty($orderby)) {
			$arr['by'] = array("id" => "desc");
		} else {
			$arr['by'] = $orderby;
		}
		$arr['table'] = 'ls_channel';
		$list = $this->IDBModel->M_getLimit($arr);
		return $list;
	}

	/**
	 * 获取频道翻页列表
	 *
	 * @param type $page
	 * @param type $limit
	 * @param type $orderby
	 *
	 * @return type
	 */
	public function C_getChannelListByAdmin($page = 1, $limit = 0, $orderby = '') {
		$count = $this->C_getChannelCount();

		$limit = (intval($limit) > 0) ? intval($limit) : PAGE_ADMIN_SIZE;
		$offset = intval($page - 1) * $limit;
		$res = $this->C_getChannelList($limit, $offset, $orderby);

		$data['total'] = $count['count'];
		$data['rows'] = $res;
		return $data;
	}

	/**
	 * 删除频道（逻辑删除）
	 *
	 * @return type
	 */
	public function C_delChannel($ids = 0) {
		$data = array('is_show' => 0);
		$arr['in'] = array('id' => $ids);
		$arr['table'] = 'ls_channel';
		$res = $this->IDBModel->M_update($data, $arr);
		return $res;
	}

	/**
	 * 统计文章篇数
	 *
	 * @param type $cid
	 * @param type $is_del
	 *
	 * @return type
	 */
	public function C_getArticleCount($cid = 0, $is_del = 0) {
		if($cid != 0) {
			$arr['where']['cid'] = $cid;
		}
		$arr['where']['is_del'] = $is_del;
		$arr['table'] = 'ls_article';
		$res = $this->IDBModel->M_getCount($arr);
		return $res;
	}

	/**
	 * 获取文章列表
	 *
	 * @param type $cid
	 * @param type $is_del
	 * @param type $limit
	 * @param type $offset
	 * @param type $orderby
	 *
	 * @return type
	 */
	public function C_getArticleList($cid = 0, $is_del = 0, $limit = 10, $offset = 0, $orderby = '') {
		if($cid != 0) {
			$arr['where']['cid'] = $cid;
		}
		$arr['where']['is_del'] = $is_del;
		if(empty($orderby)) {
			$arr['by'] = array("id" => "desc");
		} else {
			$arr['by'] = $orderby;
		}
		$arr['limit'] = array($limit,
							  $offset);
		$arr['table'] = 'ls_article';
		$res = $this->IDBModel->M_getLimit($arr);
		return $res;
	}

	/**
	 * 获取文章翻页列表
	 *
	 * @param type $cid
	 * @param type $page
	 * @param type $limit
	 * @param type $orderby
	 *
	 * @return type
	 */
	public function C_getArticleListNoDelByAdmin($cid = 0, $page = 1, $limit = 0, $orderby = '') {
		$count = $this->C_getArticleCount($cid, 0);
		$channle_t = $this->C_getChannelList(100);
		foreach($channle_t as $k => $v) {
			$channle[$v['id']] = $v['cname'];
		}
		$limit = (intval($limit) > 0) ? intval($limit) : PAGE_ADMIN_SIZE;
		$offset = intval($page - 1) * $limit;
		$res = $this->C_getArticleList($cid, 0, $limit, $offset, $orderby);
		foreach($res as $k => $v) {
			$res[$k]['channel'] = $channle[$v['cid']];
		}
		$data['total'] = $count['count'];
		$data['rows'] = $res;
		return $data;
	}

	/**
	 * 删除文章（逻辑删除）
	 *
	 * @param type $ids
	 *
	 * @return type
	 */
	public function C_delArticle($ids = 0) {
		$data = array('is_del' => 1);
		$arr['in'] = array('id' => $ids);
		$arr['table'] = 'ls_article';
		$res = $this->IDBModel->M_update($data, $arr);
		return $res;
	}

	/**
	 * 新增频道
	 *
	 * @param type $data
	 *
	 * @return type
	 */
	public function C_addChannel($data) {
		$res = $this->IDBModel->M_add('ls_channel', $data);
		return $res;
	}

	/**
	 * 修改频道
	 *
	 * @param type $cid
	 * @param type $data
	 *
	 * @return type
	 */
	public function C_upChannel($id, $data) {
		$arr['where'] = array('id' => $id);
		$arr['table'] = 'ls_channel';
		$res = $this->IDBModel->M_update($data, $arr);
		return $res;
	}

}
