<?php

require_once(FRAMEWORK_PATH . 'base/controller/Base.controller.php');

class adminController extends BaseController {

	private $IDBModel = null;

	public function init() {
		$defaultDbConfig = '';
		require(APP_WEB_PATH . 'config/db.conf.php');
		$this->IDBModel = new IDBModel($defaultDbConfig);
	}

	/**
	 * 登录
	 *
	 * @param type $uname
	 * @param type $upwd
	 *
	 * @return type
	 */
	public function C_login($uname, $upwd) {
		$user = $this->C_getAdminByName($uname);
		if($user) {
			if($user['upwd'] == MD5($upwd)) {
				$this->C_setLoginStatus($user);
				$res['error'] = 0;
			} else {
				$res['error'] = -1;
				$res['message'] = "密码错误";
			}
		} else {
			$res['error'] = -2;
			$res['message'] = "用户不存在";
		}
		return $res;
	}

	/**
	 * 退出登录
	 *
	 * @return boolean
	 */
	public function C_logout() {
		$user['uid'] = '';
		$user['uname'] = '';
		$this->C_setLoginStatus($user);
		return true;
	}

	/**
	 * 设置登陆状态
	 *
	 * @param type $user
	 */
	private function C_setLoginStatus($user) {
		setcookie('91lsme_uid', $user['id'], 0, '/');
		setcookie('91lsme_uname', $user['uname'], 0, '/');
	}

	/**
	 * 判断登录状态
	 *
	 * @return boolean
	 */
	public function C_chkLoginStatus() {
		if(!isset($_COOKIE['91lsme_uid']) && !isset($_COOKIE['91lsme_uname'])) {
			return false;
		}
		return true;
	}

	/**
	 *  检测管理员权限（是否为超级管理员）
	 */
	public function C_chkAdminSuper() {
		$arr['where'] = array('id' => $_COOKIE['91lsme_uid']);
		$arr['table'] = 'ls_admin';
		$user = $this->IDBModel->M_getRow($arr);
		if($user && $user['super'] == 1) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * 获取管理员数量
	 *
	 * @return type
	 */
	public function C_getAdminCount() {
		$arr['table'] = 'ls_admin';
		return $this->IDBModel->M_getCount($arr);
	}

	/**
	 * 获取管理员列表
	 *
	 * @param type $limit
	 * @param type $offset
	 *
	 * @return type
	 */
	public function C_getAdminList($limit = 10, $offset = 0) {
		if($limit > 0) {
			$arr['limit'] = array($limit,
								  $offset);
		}
		$arr['table'] = 'ls_admin';
		$list = $this->IDBModel->M_getLimit($arr);
		return $list;
	}

	/**
	 * 获取管理员分页数据
	 *
	 * @param type $page
	 *
	 * @return type
	 */
	public function C_getAdminListAdminPage($page = 1) {
		$count = $this->C_getAdminCount();

		$limit = PAGE_ADMIN_SIZE;
		$offset = intval($page - 1) * $limit;
		$res = $this->C_getAdminList($limit, $offset);

		$data['count'] = $count['count'];
		$data['list'] = $res;
		return $data;
	}

	/**
	 * 根据ID获取管理员信息
	 *
	 * @param type $id
	 *
	 * @return type
	 */
	public function C_getAdminById($id = 0) {
		$arr['where'] = array("id" => $id);
		$arr['table'] = 'ls_admin';
		$res = $this->IDBModel->M_getRow($arr);
		return $res;
	}

	/**
	 * 根据名称获取管理员信息
	 *
	 * @param type $name
	 *
	 * @return type
	 */
	public function C_getAdminByName($name = '') {
		$arr['where'] = array('uname' => $name);
		$arr['table'] = 'ls_admin';
		$res = $this->IDBModel->M_getRow($arr);
		return $res;
	}

	/**
	 * 新增管理员
	 *
	 * @param type $data
	 *
	 * @return type
	 */
	public function C_addAdmin($data) {
		$res = $this->IDBModel->M_add('ls_admin', $data);
		return $res;
	}

	/**
	 * 修改管理员
	 *
	 * @param type $id
	 * @param type $data
	 *
	 * @return type
	 */
	public function C_upAdmin($id, $data) {
		$arr['where'] = array('id' => $id);
		$arr['table'] = 'ls_admin';
		$res = $this->IDBModel->M_update($data, $arr);
		return $res;
	}

	/**
	 * 记录管理员操作日志
	 *
	 * @param type $methods 类::函数
	 * @param type $action  动作
	 */
	public function C_addAdminLog($methods, $action = '', $other = '') {
		$data['methods'] = $methods;
		$data['action'] = $action;
		$data['other'] = $other;
		$data['admin_id'] = $_COOKIE['91lsme_uid'];
		$data['create_time'] = date('Y-m-d H:i:s', time());
		$res = $this->IDBModel->M_add('ls_adminlog', $data);
		return $res;
	}

}
