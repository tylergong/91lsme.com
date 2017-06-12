<?php

/**
 * Class IDBModel
 *    单例模式处理 执行数据库操作
 */
class IDBModel extends DataBaseInstanceModel {

	private $_dbconfig;

	function __construct($dbconfig) {
		$this->_dbconfig = $dbconfig;
	}

	/**
	 * 获取一行记录（支持sql缓存）
	 *
	 * @param array $arr
	 * @param bool  $_is_cache
	 * @param int   $_cache_time
	 *
	 * @return mixed    返回结果集
	 */
	function M_getRow($arr = array(), $_is_cache = false, $_cache_time = CACHE_LIFETIME_SQL) {
		return DataBaseInstanceModel::getInstance($this->_dbconfig)
									->getRowCache($arr, $_is_cache, $_cache_time);
	}

	/**
	 * 获取多行记录（支持sql缓存）
	 *
	 * @param array $arr
	 * @param bool  $_is_cache
	 * @param int   $_cache_time
	 *
	 * @return mixed    返回结果集
	 */
	function M_getLimit($arr = array(), $_is_cache = false, $_cache_time = CACHE_LIFETIME_SQL) {
		return DataBaseInstanceModel::getInstance($this->_dbconfig)
									->getLimitCache($arr, $_is_cache, $_cache_time);
	}

	/**
	 * 获取count统计记录（支持sql缓存）
	 *
	 * @param array $arr
	 * @param bool  $_is_cache
	 * @param int   $_cache_time
	 *
	 * @return mixed    返回结果集
	 */
	function M_getCount($arr = array(), $_is_cache = false, $_cache_time = CACHE_LIFETIME_SQL) {
		return DataBaseInstanceModel::getInstance($this->_dbconfig)
									->getCountCache($arr, $_is_cache, $_cache_time);
	}

	/**
	 * 更新表数据
	 *
	 * @param array $data
	 * @param array $where
	 *
	 * @return mixed    返回受影响行数
	 */
	function M_update($data = array(), $where = array()) {
		return DataBaseInstanceModel::getInstance($this->_dbconfig)
									->update($data, $where);
	}

	/**
	 * 新增表数据（这里需要独立填写表名）
	 *
	 * @param        $table
	 * @param array  $data
	 * @param bool   $duplicate
	 * @param string $pkey
	 *
	 * @return mixed 返回新增ID
	 */
	function M_add($table, $data = array(), $duplicate = false, $pkey = 'id') {
		if(!$duplicate) {
			return DataBaseInstanceModel::getInstance($this->_dbconfig)
										->setTableName($table)
										->insert($data);
		} else {
			//防止主键重复插入
			return DataBaseInstanceModel::getInstance($this->_dbconfig)
										->setTableName($table)
										->duplicateInsert($data, $pkey);
		}
	}

	/**
	 * 删除表数据（这里需要独立填写表名）
	 *
	 * @param       $table
	 * @param array $where
	 *
	 * @return mixed    返回受影响行数
	 */
	function M_delete($table, $where = array()) {
		return DataBaseInstanceModel::getInstance($this->_dbconfig)
									->setTableName($table)
									->delete($where);
	}

	/**
	 * 执行一条sql语句（支持sql缓存）
	 *
	 * @param      $sql
	 * @param bool $_is_cache
	 * @param int  $_cache_time
	 *
	 * @return mixed    返回结果集
	 */
	function M_execSQL($sql, $_is_cache = false, $_cache_time = CACHE_LIFETIME_SQL) {
		return DataBaseInstanceModel::getInstance($this->_dbconfig)
									->execCache($sql, $_is_cache, $_cache_time);
	}

	/**
	 * 无需返回值 执行一条sql语句（无缓存）
	 *
	 * @param $sql
	 */
	function M_execSQL2($sql) {
		DataBaseInstanceModel::getInstance($this->_dbconfig)
							 ->manualexec($sql);
	}

}