<?php

require_once (FRAMEWORK_PATH . 'base/controller/Base.controller.php');

class flinksController extends BaseController {

	private $IDBModel = null;

	public function init() {
		$defaultDbConfig = '';
		require(APP_WEB_PATH . 'config/db.conf.php');
		$this->IDBModel = new IDBModel($defaultDbConfig);
	}

	/**
	 * 获取友情链接数量
	 * @return type
	 */
	public function C_getFLinksCount() {
		$arr['table'] = 'ls_flinks';
		return $this->IDBModel->M_getCount($arr);
	}

	/**
	 * 获取友情链接列表
	 * 
	 * @param type $limit
	 * @param type $offset
	 * @return type
	 */
	public function C_getFLinksList($limit = 10, $offset = 0) {
		if ($limit > 0) {
			$arr['limit'] = array($limit, $offset);
		}
		$arr['by'] = array('orderby' => 'desc');
		$arr['table'] = 'ls_flinks';
		$list = $this->IDBModel->M_getLimit($arr);
		return $list;
	}

	/**
	 * 获取友情链接分页数据
	 * 
	 * @param type $page
	 * @return type
	 */
	public function C_getFLinksListAdminPage($page = 1) {
		$count = $this->C_getFLinksCount();

		$limit = PAGE_ADMIN_SIZE;
		$offset = intval($page - 1) * $limit;
		$res = $this->C_getFLinksList($limit, $offset);

		$data['count'] = $count['count'];
		$data['list'] = $res;
		return $data;
	}

	/**
	 * 根据ID获取友情链接信息
	 * 
	 * @param type $id
	 * @return type
	 */
	public function C_getFLinksById($id = 0) {
		$arr['where'] = array("id" => $id);
		$arr['table'] = 'ls_flinks';
		$res = $this->IDBModel->M_getRow($arr);
		return $res;
	}

	/**
	 * 根据名称获取友情链接信息
	 * 
	 * @param type $name
	 * @return type
	 */
	public function C_getFLinksByName($name = '') {
		$arr['where'] = array('uname' => $name);
		$arr['table'] = 'ls_flinks';
		$res = $this->IDBModel->M_getRow($arr);
		return $res;
	}

	/**
	 * 新增友情链接
	 * 
	 * @param type $data
	 * @return type
	 */
	public function C_addflinks($data) {
		$res = $this->IDBModel->M_addF('ls_flinks',$data);
		return $res;
	}

	/**
	 * 修改友情链接
	 * 
	 * @param type $id
	 * @param type $data
	 * @return type
	 */
	public function C_upFLinks($id, $data) {
		$arr['where'] = array('id' => $id);
		$arr['table'] = 'ls_flinks';
		$res = $this->IDBModel->M_update($data, $arr);
		return $res;
	}

	/**
	 * 删除友情链接
	 * @param type $ids
	 * @return type
	 */
	public function C_delFLinks($ids) {
		$arr['in'] = array('id' => $ids);
		$res = $this->IDBModel->M_delFLinks('ls_flinks',$arr);
		return $res;
	}

}
