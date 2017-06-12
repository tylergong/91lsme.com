<?php

/**
 * Adapter_Pdo_Abstract 类是所有 PDO 驱动的基础类
 *
 * @package database
 */
abstract class Adapter_Pdo_Abstract {

	protected $_bind_enabled = false;
	protected $_pdo_type = 'mysql';
	public $_conn = null;
	protected $_fetch_mode = QDB::FETCH_MODE_ASSOC;

	public function __construct($dsn) {
		$this->_dsn = $dsn;
	}

	public function connect($pconnect = false, $force_new = false) {

		if(!$force_new && $this->isConnected()) {
			return;
		}

		$dsn = array();
		if(!empty($this->_dsn['database'])) {
			$dsn['dbname'] = $this->_dsn['database'];
		}
		if(!empty($this->_dsn['host'])) {
			$dsn['host'] = $this->_dsn['host'];
		}
		if(!empty($this->_dsn['port'])) {
			$dsn['port'] = $this->_dsn['port'];
		}

		$user = $this->_dsn['login'];
		$password = $this->_dsn['password'];

		$dsn_string = sprintf('%s:%s', $this->_pdo_type, http_build_query($dsn, '', ';'));

		try {

			$this->_conn = new PDO($dsn_string, $user, $password, array(PDO::ATTR_TIMEOUT => 10));

			if(isset($this->_dsn['charset']) && $this->_dsn['charset'] != '') {
				$charset = $this->_dsn['charset'];
				$this->execute("SET NAMES '" . $charset . "'");
			}
		} catch(PDOException $e) {
			throw $e;
		}
	}

	public function close() {
		parent::_clear();
	}

	public function pconnect() {
		$this->connect();
	}

	public function nconnect() {
		$this->connect(false, true);
	}

	public function isConnected() {
		return $this->_conn instanceof PDO;
	}

	public function qstr($value) {
		if(is_array($value)) {
			foreach($value as $offset => $v) {
				$value[$offset] = $this->qstr($v);
			}
			return $value;
		}
		if(is_int($value) || is_float($value)) {
			return $value;
		}
		if(is_bool($value)) {
			return $value ? $this->_true_value : $this->_false_value;
		}
		if(is_null($value)) {
			return $this->_null_value;
		}

		if(!$this->isConnected()) {
			$this->connect();
		}
		return $this->_conn->quote($value);
	}

	public function affectedRows() {
		return $this->_lastrs instanceof PDOStatement ? $this->_lastrs->rowCount() : 0;
	}

	public function is_write($sql) {
		if(!preg_match('/^\s*"?(SET|INSERT|UPDATE|DELETE|REPLACE|CREATE|DROP|LOAD DATA|COPY|ALTER|GRANT|REVOKE|LOCK|UNLOCK)\s+/i', $sql)) {
			return false;
		}
		return true;
	}

	public function execute($sql, $inputarr = null) {

		if(!$this->isConnected()) {
			$this->connect();
		}

		$sth = $this->_conn->prepare($sql);

		$result = $sth->execute((array)$inputarr);
		if(false === $result) {
			$error = $sth->errorInfo();
			$this->_last_err = $error[2];
			$this->_last_err_code = $error[0];
			$this->_has_failed_query = true;

			throw new Exception($sql . ' ' . $this->_last_err . ' ' . $this->_last_err_code);
		}

		$this->_lastrs = $sth;

		if('select' == strtolower(substr($sql, 0, 6))) {
			return new Result_Pdo($this->_lastrs, $this->_fetch_mode);
		} else {
			return $this->affectedRows();
		}
	}

	public function exec($sql, $inputarr = null) {
		if(!$this->isConnected()) {
			$this->connect();
		}

		return $this->_conn->exec($sql); // 直接返回受影响行数
	}

	protected function escape_str($str) {
		if(is_array($str)) {
			foreach($str as $key => $val) {
				$str[$key] = $this->escape_str($val);
			}

			return $str;
		}
		return addslashes($str);
//
//		if (function_exists('mysql_real_escape_string') && !empty($this->_conn)) { echo 1;
//			return mysql_real_escape_string($str);
//		} elseif (function_exists('mysql_escape_string') && !empty($this->_conn)) { echo 2;
//			return mysql_escape_string($str);
//		} else { echo 3;
//			return addslashes($str);
//		}
	}

	public function lastInsertId() {
		if(!empty($this->_conn)) {
			return $this->_conn->lastInsertId();
		}
		return null;
	}

	function selectLimit($sql, $offset = 0, $length = 30, array $inputarr = null) {
		$sql = sprintf('%s LIMIT %d OFFSET %d', $sql, $length, $offset);
		return $this->_execute($sql, $inputarr);
	}

	protected function _table($table) {
		return '`' . $table . '`';
	}

	protected function num_rows() {

		if(!$this->isConnected()) {
			$this->connect();
		}

		return $this->_conn->fetchColumn();
	}

	protected function _fetch_one($type) {
		if($this->num_rows() == 0) {
			return array();
		}
		$rs = array();
		$rs = $type == 'assoc' ? $this->_lastrs->fetch(PDO::FETCH_ASSOC) : $this->_lastrs->fetch(PDO::FETCH_OBJ);

		return $rs;
	}

}

class Result_Pdo extends Result_Abstract {

	protected function _getFetchMode() {
		$fetch_mode = PDO::FETCH_BOTH;

		if(QDB::FETCH_MODE_ASSOC == $this->fetch_mode) {
			$fetch_mode = PDO::FETCH_ASSOC;
		}

		return $fetch_mode;
	}

	public function free() {
		$this->_handle = null;
	}

	public function fetchAll() {
		return $this->_handle->fetchAll($this->_getFetchMode());
	}

	public function fetchRow() {
		return $this->_handle->fetch($this->_getFetchMode());
	}

	public function fetchCol($column = 0) {
		return $this->_handle->fetchAll(PDO::FETCH_COLUMN, $column);
	}

	public function fetch_one() {

		return $this->_handle->fetchColumn();
	}

	public function fetchColumn($number){
		return $this->_handle->fetchColumn($number);
	}
	/**
	 * 从查询句柄提取记录集，以指定的字段名为数组的key
	 * 如果不指定key，以记录的第一个字段为key
	 *
	 * @param string $key
	 *
	 * @return array
	 */
	public function fetchAssoc($key = null) {
		if(null === $key) {
			$meta = $this->getColumnsMeta(0);
			$key = $meta['name'];
		}
		$rowset = array();
		while($row = $this->fetchRow()) {
			$rowset[$row[$key]] = $row;
		}
		return $rowset;
	}

	public function fetch_array() {
		$rs = $this->fetchAssoc();

		return $rs;
	}

	public function getColumnsMeta($column = null) {
		if(null === $column) {
			$meta = array();
			for($i = 0, $len = $this->_handle->columnCount(); $i < $len; $i++) {
				$meta[] = $this->_handle->getColumnMeta($i);
			}
		} else {
			$meta = $this->_handle->getColumnMeta($column);
		}

		return $meta;
	}

}

abstract class Result_Abstract {

	/**
	 * 指示返回结果集的形式
	 *
	 * @var const
	 */
	public $fetch_mode;

	/**
	 * 指示是否将查询结果中的字段名转换为全小写
	 *
	 * @var boolean
	 */
	public $result_field_name_lower = false;

	/**
	 * 查询句柄
	 *
	 * @var resource
	 */
	protected $_handle = null;

	/**
	 * 构造函数
	 *
	 * @param resource $handle
	 * @param const    $fetch_mode
	 */
	function __construct($handle, $fetch_mode) {
		if(is_resource($handle) || is_object($handle)) {
			$this->_handle = $handle;
		}
		$this->fetch_mode = $fetch_mode;
	}

	/**
	 * 析构函数
	 */
	function __destruct() {
		$this->free();
	}

	/**
	 * 返回句柄
	 *
	 * @return resource
	 */
	function handle() {
		return $this->_handle;
	}

	/**
	 * 指示句柄是否有效
	 *
	 * @return boolean
	 */
	function valid() {
		return $this->_handle != null;
	}

	/**
	 * 释放句柄
	 */
	abstract function free();

	/**
	 * 从查询句柄提取一条记录
	 *
	 * @return array
	 */
	abstract function fetchRow();

	/**
	 * 从查询句柄中提取记录集
	 *
	 * @return array
	 */
	function fetchAll() {
		$rowset = array();
		while(($row = $this->fetchRow())) {
			$rowset[] = $row;
		}
		return $rowset;
	}

	/**
	 * 从查询句柄提取一条记录，并返回该记录的第一个字段
	 *
	 * @return mixed
	 */
	function fetchOne() {
		$row = $this->fetchRow();
		return $row;
	}

	/**
	 * 从查询句柄提取记录集，并返回包含每行指定列数据的数组，如果 $col 为 0，则返回第一列
	 *
	 * @param int $col
	 *
	 * @return array
	 */
	function fetchCol($col = 0) {
		$mode = $this->fetch_mode;
		$this->fetch_mode = QDB::FETCH_MODE_ARRAY;
		$cols = array();
		while(($row = $this->fetchRow())) {
			$cols[] = $row[$col];
		}
		$this->fetch_mode = $mode;
		return $cols;
	}

	/**
	 * 返回记录集和指定字段的值集合，以及以该字段值作为索引的结果集
	 *
	 * 假设数据表 posts 有字段 post_id 和 title，并且包含下列数据：
	 *
	 * @param array   $fields
	 * @param array   $fields_value
	 * @param array   $ref
	 * @param boolean $clean_up
	 *
	 * @return array
	 */
	function fetchAllRefby(array $fields, & $fields_value, & $ref, $clean_up) {
		$ref = $fields_value = $data = array();
		$offset = 0;

		if($clean_up) {
			while(($row = $this->fetchRow())) {
				$data[$offset] = $row;
				foreach($fields as $field) {
					$field_value = $row[$field];
					$fields_value[$field][$offset] = $field_value;
					$ref[$field][$field_value][] = & $data[$offset];
					unset($data[$offset][$field]);
				}
				$offset++;
			}
		} else {
			while(($row = $this->fetchRow())) {
				$data[$offset] = $row;
				foreach($fields as $field) {
					$field_value = $row[$field];
					$fields_value[$field][$offset] = $field_value;
					$ref[$field][$field_value][] = & $data[$offset];
				}
				$offset++;
			}
		}

		return $data;
	}

}

abstract class QDB {

	/**
	 * QDB 数据库架构参数格式
	 */
	// 问号作为参数占位符
	const PARAM_QM = '?';
	// 冒号开始的命名参数
	const PARAM_CL_NAMED = ':';
	// $符号开始的序列
	const PARAM_DL_SEQUENCE = '$';
	// @开始的命名参数
	const PARAM_AT_NAMED = '@';

	/**
	 * QDB 数据库架构查询结果返回格式
	 */
	// 返回的每一个记录就是一个索引数组
	const FETCH_MODE_ARRAY = 1;
	// 返回的每一个记录就是一个以字段名作为键名的数组
	const FETCH_MODE_ASSOC = 2;

	/**
	 * QDB 数据库关联模式
	 */
	// 一对一关联
	const HAS_ONE = 'has_one';
	// 一对多关联
	const HAS_MANY = 'has_many';
	// 从属关联
	const BELONGS_TO = 'belongs_to';
	// 多对多关联
	const MANY_TO_MANY = 'many_to_many';

	/**
	 * QDB 数据库架构字段和属性名映射
	 */
	// 字段
	const FIELD = 'field';
	// 属性
	const PROP = 'prop';

	/**
	 * 字段元类型
	 */

	/**
	 * 获得一个数据库连接对象
	 *
	 * $dsn_name 参数指定要使用应用程序设置中的哪一个项目作为创建数据库连接的 DSN 信息。
	 * 对于同样的 DSN 信息，只会返回一个数据库连接对象。
	 *
	 * 所有的数据库连接信息都存储在应用程序设置 db_dsn_pool 中。
	 * 默认的数据库连接信息存储为 db_dsn_pool/default。
	 *
	 * @code php
	 * // 获得默认数据库连接对应的数据库访问对象
	 * $dbo = QDB::getConn();
	 *
	 * // 获得数据库连接信息 db_dsn_pool/news_db 对应的数据库访问对象
	 * $dbo_news = QDB::getConn('news_db');
	 * @endcode
	 *
	 * @param string $dsn_name 要使用的数据库连接
	 *
	 * @return QDB_Adapter_Abstract 数据库访问对象
	 */
	static function getConn($dsn_name = null) {


		$dsn = array();


		if(empty($dsn)) {
			// LC_MSG: Invalid DSN.
			trigger_error('invalid dsn');
			throw new Exception('Invalid DSN.');
		}

		$dbtype = $dsn['driver'];
		$objid = "dbo_{$dbtype}_" . md5(serialize($dsn));


		$class_name = 'QDB_Adapter_' . ucfirst($dbtype);
		$dbo = new $class_name($dsn, $objid);

		return $dbo;
	}

	/**
	 * 将字符串形式的 DSN 转换为数组
	 *
	 * @code php
	 * $string = 'mysql://root:mypass@localhost/test';
	 * $dsn = QDB::parseDSN($string);
	 * // 输出
	 * // array(
	 * //     driver:   mysql
	 * //     host:     localhost
	 * //     login:    root
	 * //     password: mypass
	 * //     database: test
	 * //     port:
	 * // )
	 * @endcode
	 *
	 * @param string $dsn 要分析的 DSN 字符串
	 *
	 * @return array 分析后的数据库连接信息
	 */
	static function parseDSN($dsn) {
		$dsn = str_replace('@/', '@localhost/', $dsn);
		$parse = parse_url($dsn);
		if(empty($parse['scheme'])) {
			return false;
		}

		$dsn = array();
		$dsn['host'] = isset($parse['host']) ? $parse['host'] : 'localhost';
		$dsn['port'] = isset($parse['port']) ? $parse['port'] : '';
		$dsn['login'] = isset($parse['user']) ? $parse['user'] : '';
		$dsn['password'] = isset($parse['pass']) ? $parse['pass'] : '';
		$dsn['driver'] = isset($parse['scheme']) ? strtolower($parse['scheme']) : '';
		$dsn['database'] = isset($parse['path']) ? substr($parse['path'], 1) : '';

		return $dsn;
	}

}
