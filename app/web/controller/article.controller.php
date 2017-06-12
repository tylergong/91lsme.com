<?php

require_once(FRAMEWORK_PATH . 'base/controller/Base.controller.php');

class articleController extends BaseController {

	private $IDBModel = null;

	public function init() {
		$defaultDbConfig = '';
		require(APP_WEB_PATH . 'config/db.conf.php');
		$this->IDBModel = new IDBModel($defaultDbConfig);

	}

	/**
	 * 根据文章id获取文章信息
	 *
	 * @param type $id
	 *
	 * @return type
	 */
	public function C_getArticleById($id, $is_admin = false) {
		$arr['where'] = array('id' => $id,
							  'is_del' => 0);
		if(!$is_admin) {
			$arr['where']['is_show'] = 1;
		}
		$arr['table'] = 'ls_article';
		$res = $this->IDBModel->M_getRow($arr);
		return $res;
	}

	/**
	 * 根据文章id获取文章上下篇
	 *
	 * @param $id
	 * @param $cid
	 *
	 * @return mixed
	 */
	public function C_getArticleByIdForUpDown($id, $cid) {
		$c['select'] = 'id';
		$c['where'] = array('cid' => $cid,
							'is_del' => 0,
							'is_show' => 1);
		$c['by'] = array('id' => 'desc');
		$c['table'] = 'ls_article';
		$updown['c'] = $this->IDBModel->M_getLimit($c);
		foreach($updown['c'] as $k => $v) {
			if($id == $v['id']) {
				$upk = $k - 1;
				$downk = $k + 1;
				break;
			}
		}
		$upid = $updown['c'][$upk]['id'];
		$downid = $updown['c'][$downk]['id'];

		$up['select'] = 'id,title';
		$up['where'] = array('id' => $upid,
							 'cid' => $cid,
							 'is_del' => 0,
							 'is_show' => 1);
		$up['table'] = 'ls_article';
		$updown['up'] = $this->IDBModel->M_getRow($up);

		$down['select'] = 'id,title';
		$down['where'] = array('id' => $downid,
							   'cid' => $cid,
							   'is_del' => 0,
							   'is_show' => 1);
		$down['table'] = 'ls_article';
		$updown['down'] = $this->IDBModel->M_getRow($down);

		//print_r($updown);
		//die;
		return $updown;
	}

	/**
	 * 根据文章类型获取 文章数量
	 *
	 * @param cid $cid
	 *
	 * @return type
	 */
	public function C_getArticleCountByCid($cid = 0, $is_del = 0, $is_admin = false) {
		$arr = array();
		if($cid != 0) {
			$arr['where']['cid'] = $cid;
		}
		$arr['where']['is_del'] = $is_del;
		if(!$is_admin) {
			$arr['where']['is_show'] = 1;
		}
		$arr['table'] = 'ls_article';
		$res = $this->IDBModel->M_getCount($arr);
		return $res;
	}

	/**
	 * 根据文章类型获取 分页数据
	 *
	 * @param type $type
	 * @param type $limit
	 * @param type $offset
	 *
	 * @return type
	 */
	public function C_getArticleListByCid($cid = 0, $is_del = 0, $limit = 10, $offset = 0, $is_admin = false) {
		if($cid != 0) {
			$arr['where']['cid'] = $cid;
		}
		$arr['where']['is_del'] = $is_del;
		if(!$is_admin) {
			$arr['where']['is_show'] = 1;
		}
		$arr['by'] = array("up" => "desc",
						   "id" => "desc");
		if($limit > 0) {
			$arr['limit'] = array($limit,
								  $offset);
		}
		$arr['table'] = 'ls_article';
		$res = $this->IDBModel->M_getLimit($arr);
		return $res;
	}

	/**
	 * 按照文章分类获取分页数据（前台使用）
	 *
	 * @param type $type
	 * @param type $page
	 *
	 * @return type
	 */
	public function C_getArticleListByCidPage($cid = 0, $page = 1) {
		$count = $this->C_getArticleCountByCid($cid, 0, false);

		$limit = PAGE_SIZE;
		$offset = intval($page - 1) * $limit;
		$res = $this->C_getArticleListByCid($cid, 0, $limit, $offset, false);

		$data['count'] = $count['count'];
		$data['list'] = $res;
		$data['cur'] = $page;
		$data['all'] = ceil($data['count'] / $limit);
		return $data;
	}

	/**
	 * 按照文章分类获取分页数据（后台使用）
	 *
	 * @param type $type
	 * @param type $page
	 *
	 * @return type
	 */
	public function C_getArticleListAdminPage($cid = 0, $page = 1) {
		$count = $this->C_getArticleCountByCid($cid, 0, true);

		$channle_t = $this->C_getChannelList();
		foreach($channle_t as $k => $v) {
			$channle[$v['id']] = $v['cname'];
		}

		$limit = PAGE_ADMIN_SIZE;
		$offset = intval($page - 1) * $limit;
		$res = $this->C_getArticleListByCid($cid, 0, $limit, $offset, true);
		foreach($res as $k => $v) {
			$res[$k]['channel'] = $channle[$v['cid']];
		}

		$data['count'] = $count['count'];
		$data['list'] = $res;
		$data['cur'] = $page;
		$data['all'] = ceil($data['count'] / $limit);
		return $data;
	}

	/**
	 * 按照文章分类获取删除的分页数据（后台使用）
	 *
	 * @param type $type
	 * @param type $page
	 *
	 * @return type
	 */
	public function C_getArticleListRecycleAdminPage($cid = 0, $page = 1) {
		$count = $this->C_getArticleCountByCid($cid, 1, true);

		$channle_t = $this->C_getChannelList();
		foreach($channle_t as $k => $v) {
			$channle[$v['id']] = $v['cname'];
		}

		$limit = PAGE_ADMIN_SIZE;
		$offset = intval($page - 1) * $limit;
		$res = $this->C_getArticleListByCid($cid, 1, $limit, $offset, true);
		foreach($res as $k => $v) {
			$res[$k]['channel'] = $channle[$v['cid']];
		}

		$data['count'] = $count['count'];
		$data['list'] = $res;
		return $data;
	}

	/**
	 * 新增文章
	 *
	 * @param type $data
	 *
	 * @return type
	 */
	public function C_addArticle($data) {
		$res = $this->IDBModel->M_add('ls_article', $data);
		return $res;
	}

	/**
	 * 修改文章
	 *
	 * @param type $id
	 * @param type $data
	 *
	 * @return type
	 */
	public function C_upArticle($id, $data) {
		$arr['where'] = array('id' => $id);
		$arr['table'] = 'ls_article';
		$res = $this->IDBModel->M_update($data, $arr);
		return $res;
	}

	/**
	 * 删除文章（逻辑删除）
	 *
	 * @param type $ids
	 *
	 * @return type
	 */
	public function C_delArticle($ids) {
		$data = array('is_del' => 1);
		$arr['in'] = array('id' => $ids);
		$arr['table'] = 'ls_article';
		$res = $this->IDBModel->M_update($data, $arr);
		return $res;
	}

	/**
	 * 恢复被删除的文章（逻辑删除）
	 *
	 * @param type $ids
	 *
	 * @return type
	 */
	public function C_recArticle($ids) {
		$data = array('is_del' => 0);
		$arr['in'] = array('id' => $ids);
		$arr['table'] = 'ls_article';
		$res = $this->IDBModel->M_update($data, $arr);
		return $res;
	}

	/**
	 * 根据id 获取频道信息
	 *
	 * @param type $id
	 */
	public function C_getChannelById($id) {
		$arr['where'] = array("id" => $id);
		$arr['table'] = 'ls_channel';
		$res = $this->IDBModel->M_getRow($arr);
		return $res;
	}

	/**
	 * 频道数量
	 *
	 * @return type
	 */
	public function C_getChannelCount() {
		$arr['table'] = 'ls_channel';
		return $this->IDBModel->M_getCount($arr);
	}

	/**
	 * 频道列表数据
	 *
	 * @param type $limit
	 * @param type $offset
	 *
	 * @return type
	 */
	public function C_getChannelList($limit = 10, $offset = 0, $isadmin = true) {
		if($limit > 0) {
			$arr['limit'] = array($limit,
								  $offset);
		}
		if(!$isadmin) {
			$arr['where'] = array('isshow' => 1);
		}
		$arr['table'] = 'ls_channel';
		$list = $this->IDBModel->M_getLimit($arr);
		return $list;
	}

	/**
	 * 频道分页数据（后台使用）
	 *
	 * @param int $page
	 * @param int $pageSize
	 *
	 * @return mixed
	 */
	public function C_getChannelListAdminPage($page = 1, $pageSize = PAGE_ADMIN_SIZE) {

		$count = $this->C_getChannelCount();

		$limit = $pageSize;
		$offset = intval($page - 1) * $limit;
		$res = $this->C_getChannelList($limit, $offset);

		$data['count'] = $count['count'];
		$data['list'] = $res;
		return $data;
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

	/**
	 * 删除频道
	 *
	 * @param $id
	 *
	 * @return mixed
	 */
	public function C_delChannel($id) {
		$arr['where'] = array('id' => $id);
		$res = $this->IDBModel->M_delete('ls_channel', $arr);
		return $res;
	}

}
