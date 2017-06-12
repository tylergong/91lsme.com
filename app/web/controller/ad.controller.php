<?php

require_once(FRAMEWORK_PATH . 'base/controller/Base.controller.php');

class adController extends BaseController {

	private $IDBModel = null;

	public function init() {
		$defaultDbConfig = '';
		require(APP_WEB_PATH . 'config/db.conf.php');
		$this->IDBModel = new IDBModel($defaultDbConfig);
	}

	/**
	 * 获取广告数量
	 *
	 * @return type
	 */
	public function C_getAdCount() {
		$arr['table'] = 'ls_ad';
		return $this->IDBModel->M_getCount($arr);
	}

	/**
	 * 获取广告列表
	 *
	 * @param int $limit
	 * @param int $offset
	 *
	 * @return type
	 */
	public function C_getAdList($limit = 10, $offset = 0) {
		if($limit > 0) {
			$arr['limit'] = array($limit,
								  $offset);
		}
		$arr['by'] = array('id' => 'desc');
		$arr['table'] = 'ls_ad';
		$list = $this->IDBModel->M_getLimit($arr);
		return $list;
	}

	/**
	 * 获取广告分页数据
	 *
	 * @param int $page
	 *
	 * @return type
	 */
	public function C_getAdListAdminPage($page = 1) {
		$count = $this->C_getAdCount();

		$limit = PAGE_ADMIN_SIZE;
		$offset = intval($page - 1) * $limit;
		$res = $this->C_getAdList($limit, $offset);

		$data['count'] = $count['count'];
		$data['list'] = $res;
		return $data;
	}

	/**
	 * 根据ID获取广告信息
	 *
	 * @param int $id
	 *
	 * @return type
	 */
	public function C_getAdById($id = 0) {
		$arr['where'] = array("id" => $id);
		$arr['table'] = 'ls_ad';
		$res = $this->IDBModel->M_getRow($arr);
		return $res;
	}

	/**
	 * 根据名称获取广告信息
	 *
	 * @param string $name
	 *
	 * @return type
	 */
	public function C_getAdByName($name = '') {
		$arr['where'] = array('uname' => $name);
		$arr['table'] = 'ls_ad';
		$res = $this->IDBModel->M_getRow($arr);
		return $res;
	}

	/**
	 * 新增广告
	 *
	 * @param type $data
	 *
	 * @return type
	 */
	public function C_addAd($data) {
		$res = $this->IDBModel->M_add('ls_ad', $data);
		return $res;
	}

	/**
	 * 修改广告
	 *
	 * @param type $id
	 * @param type $data
	 *
	 * @return type
	 */
	public function C_upAd($id, $data) {
		$arr['where'] = array('id' => $id);
		$arr['table'] = 'ls_ad';
		$res = $this->IDBModel->M_update($data, $arr);
		return $res;
	}

	/**
	 * 删除广告
	 *
	 * @param type $ids
	 *
	 * @return type
	 */
	public function C_delAd($ids) {
		$arr['in'] = array('id' => $ids);
		$res = $this->IDBModel->M_delete('ls_ad', $arr);
		return $res;
	}

}
