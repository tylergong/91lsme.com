<?php

/**
 *
 * select(),avg(),max(),min(),sum(),count(),
 * from(),join(),distinct(),in(),notin(),where(),orwhere(),
 * like(),orlike(),group(),by(),limit(),limitPage(),
 * _compile_select(),
 */
require_once(FRAMEWORK_PATH . 'base/model/DataBase.php');

class DataBaseInstanceModel extends DataBase {

	public $db = null;
	private static $Masterinstance = null;// 主库单例
	private static $Slaveinstance = null;// 从库单例
	public $cache;
	protected $_select = array();
	protected $_join = array();
	protected $_from = array();
	protected $_distinct = false;
	protected $_where = array();
	protected $_like = array();
	protected $_instr = array();
	protected $_offset = '';
	protected $_limit = '';
	protected $_group = array();
	protected $_order = array();
	protected $_autoData = array();
	protected $_tableName = '';
	protected $_tablePrefix = '';
	protected $_fields = null;
	protected $_fieldsData = array();
	protected $_lastCompileSql = '';
	protected $_cache_time = CACHE_LIFETIME_SQL;

	//  私有化构造函数，防止外界实例化对象
	private function __construct($dbconfig) {
		$this->_tablePrefix = $dbconfig['mysql_prefix'];
		if($this->db == null) {
			$this->db = $this->factory($dbconfig);
		}
	}

	// 私有化克隆函数，防止外界克隆对象
	private function __clone() {
	}

	/**
	 * 静态方法, 单例统一访问入口
	 *
	 * @param $dbConfig
	 *
	 * @return object  返回对象的唯一实例
	 */
	public static function getInstance($dbConfig) {
		// 判断主从分别实现单例
		$Instance = $dbConfig['mysql_master'] ? 'Masterinstance' : 'Slaveinstance';
		if(!(self::$$Instance instanceof self)) {
			self::$$Instance = new self($dbConfig);
		}
		return self::$$Instance;
	}

	/**
	 * 设置表名
	 *
	 * @param string $table
	 *
	 * @return $this
	 */
	public function setTableName($table = '') {
		if(!$table == '') {
			$this->_tableName = $this->_tablePrefix . $table;
		}
		return $this;
	}

	/**
	 * 返回表名
	 *
	 * @access public
	 *
	 * @param string $table 表名
	 *
	 * @return string
	 */
	public function getTableName($table = '') {
		// 有表名
		if($table != '') {
			// 不包含前缀 
			if(!empty($this->_tablePrefix) && strpos($table, $this->_tablePrefix) === false) {
				return $this->_tablePrefix . $table;
			}
			return $table;
		}
		return $this->_tableName;
	}

	/**
	 * 定义sql语句中查询的字段
	 *
	 * @access public
	 *
	 * @param array|string $field 字段名
	 *
	 * @return this
	 */
	public function select($field = '*') {
		if(!is_array($field)) {
			$field = explode(',', $field);
		}
		foreach($field as $v) {
			$this->_select[] = $v;
		}
		return $this;
	}

	/**
	 * 取得某个字段的最大值
	 *
	 * @access public
	 *
	 * @param string $field 字段名
	 * @param string $alias 别名
	 *
	 * @return this
	 */
	public function max($field, $alias = 'max') {
		$alias = ($alias != '') ? $alias : $field;
		$sql = 'MAX(' . $field . ') AS ' . $alias;
		$this->_select[] = $sql;
		return $this;
	}

	/**
	 * 取得某个字段的最小值
	 *
	 * @access public
	 *
	 * @param string $field 字段名
	 * @param string $alias 别名
	 *
	 * @return this
	 */
	public function min($field, $alias = 'min') {
		$alias = ($alias != '') ? $alias : $field;
		$sql = 'MIN(' . $field . ') AS ' . $alias;
		$this->_select[] = $sql;
		return $this;
	}

	/**
	 * 统计某个字段的平均值
	 *
	 * @access public
	 *
	 * @param string $field 字段名
	 * @param string $alias 别名
	 *
	 * @return this
	 */
	public function avg($field, $alias = 'avg') {
		$alias = ($alias != '') ? $alias : $field;
		$sql = 'AVG(' . $field . ') AS ' . $alias;
		$this->_select[] = $sql;
		return $this;
	}

	/**
	 * 统计某个字段的总和
	 *
	 * @access public
	 *
	 * @param string $field 字段名
	 * @param string $alias 别名
	 *
	 * @return this
	 */
	public function sum($field, $alias = 'sum') {
		$alias = ($alias != '') ? $alias : $field;
		$sql = 'SUM(' . $field . ') AS ' . $alias;
		$this->_select[] = $sql;
		return $this;
	}

	/**
	 * 统计满足条件的记录个数
	 *
	 * @access public
	 *
	 * @param string $field    字段名
	 * @param string $alias    别名
	 * @param bool   $distinct 排重
	 *
	 * @return this
	 */
	public function count($field = '*', $alias = 'count', $distinct = false) {
		$alias = ($alias != '') ? $alias : $field;
		$field = $field == '*' ? $field : ($distinct ? 'DISTINCT ' . $field : $field);
		$sql = 'COUNT(' . $field . ') AS ' . $alias;
		$this->_select[] = $sql;
		return $this;
	}

	/**
	 * 设置查询主表名
	 *
	 * @param string|array $table 表名
	 *
	 * @return this
	 */
	public function from($table) {
		if(!is_array($table)) {
			$_tableName = $this->getTableName($table);
		} else {
			foreach($table as $k => $v) {
				$_tableName = $this->getTableName($k) . ' AS ' . $v;
			}
		}
		$this->_tableName = $_tableName;
		return $this;
	}

	/**
	 * 连表查询
	 *    join('abc as a'=>'a.id=b.id and a.name=b.name')
	 *
	 * @param string $table 表名
	 * @param string $type  LEFT|RIGHT|INNER
	 *
	 * @return this
	 */
	public function join($table, $type = 'LEFT') {
		$type = strtoupper($type);
		foreach($table as $k => $v) {
			$this->_join[] = $type . ' JOIN ' . $this->getTableName($k) . ' ON ' . $v;
		}
		return $this;
	}

	/**
	 * 排重
	 *
	 * @param bool $val
	 *
	 * @return this
	 */
	public function distinct($val = true) {
		$this->_distinct = (is_bool($val)) ? $val : true;
		return $this;
	}

	/**
	 * 筛选条件 NOT IN
	 *
	 * @param array  $where
	 * @param string $type AND|OR
	 *
	 * @return this
	 */
	public function notin($where, $type = 'AND') {
		return $this->in($where, true, $type);
	}

	/**
	 * 筛选条件 IN
	 *
	 * @param        $where
	 * @param bool   $not  false(in)|true(not in)
	 * @param string $type AND|OR
	 *
	 * @return this
	 */
	public function in($where, $not = false, $type = 'AND') {
		foreach($where as $k => $v) {
			$prefix = (count($this->_where) == 0) ? '' : $type . ' ';
			$not = ($not) ? ' NOT' : '';
			$arr = array();
			if(is_array($v)) {
				foreach($v as $value) {
					$arr[] = $this->db->escape($value);
				}
				if(!empty($arr)) {
					$this->_where[] = $prefix . $k . $not . " IN (" . implode(", ", $arr) . ") ";
				}
			} else {
				$this->_where[] = $prefix . $k . $not . " IN (" . $v . ") ";
			}
		}
		return $this;
	}

	/**
	 * 筛选 WHERE 条件 OR
	 *
	 * @param type $where
	 *
	 * @return this
	 */
	public function orwhere($where) {
		return $this->where($where, 'OR');
	}

	/**
	 * 筛选 WHERE 条件 AND
	 *    "a.line" => 1                    : a.line = 1
	 *    "a.name (!=|=|<>) aa" => null    : a.name (!=|=|<>) 'aa'
	 *    "a.name is (not) null" => null    : a.name is (not) null
	 *    "a.name" => null                : a.name IS NULL
	 *
	 * @param array  $where
	 * @param string $type  AND|OR
	 * @param string $type2 AND|OR
	 *
	 * @return this
	 */
	public function where($where, $type = 'AND', $type2 = '') {
		foreach($where as $k => $v) {
			// 若k中存在. 则转换为. ..//个人感觉无用，多此一举
//			if(strpos($k, '.')) {
//				list($alias, $column) = explode('.', $k);
//				$k = $alias . '.' . $column . '';
//			}
			// 统计是第几个where，从第二个开始加上 type
			$prefix = (count($this->_where) == 0) ? '' : $type . ' ';
			// k无特定符号 且 v为空
			if(!$this->db->_parse($k) && is_null($v)) {
				$k .= ' IS NULL';
			}
			// k无特定符号
			if(!$this->db->_parse($k)) {
				$k .= ' =';
			}
			// 根据不同的字符类型构造sql中使用的v（格式化v值）
			if(!is_null($v)) {
				$v = $this->db->escape($v);
			}
			// 若有组合条件，则独立构造本次where
			if(!empty($type2)) {
				$_where[] = $k . ' ' . $v;
			} else {
				$this->_where[] = $prefix . $k . ' ' . $v;
			}
		}
		// or 和 and 的组合条件判断
		if(!empty($type2) && !empty($_where)) {
			// (array("a.line"=>1,"a.name"=>'aa'),'or','and')	:  or (a.line = 1 and a.name = 'aa')
			$this->_where[] = $prefix . '(' . implode(" $type2 ", $_where) . ') ';
		}
		// 无条件直接赋值1，防止无条件报错
		if(empty($this->_where))
			$this->_where[] = '1';
		return $this;
	}

	/**
	 * 筛选条件 LIKE OR
	 *
	 * @param type   $where
	 * @param bool   $not  true(like)|false(not like)
	 * @param string $like all(全匹配)|left(左匹配)|right(右匹配)
	 *
	 * @return this
	 */
	public function orlike($where, $not = false, $like = 'all') {
		return $this->like($where, $not, 'OR', $like);
	}

	/**
	 * 筛选条件 LIKE AND
	 *
	 * @param array  $where
	 * @param bool   $not  true(like)|false(not like)
	 * @param string $type AND|OR
	 * @param string $like all(全匹配)|left(左匹配)|right(右匹配)
	 *
	 * @return this
	 */
	public function like($where, $not = false, $type = 'AND', $like = 'all') {
		$type = strtoupper($type);
		$like = strtolower($like);
		foreach($where as $k => $v) {
			$prefix = (count($this->_like) == 0) ? '' : $type . ' ';
			$not = ($not) ? ' NOT' : '';
			$arr = array();
			$v = str_replace("+", " ", $v);
			$values = explode(' ', $v);
			foreach($values as $value) {
				if($like == 'left') {
					$keyword = "'%{$value}'";
				} else if($like == 'right') {
					$keyword = "'{$value}%'";
				} else {
					$keyword = "'%{$value}%'";
				}
				$arr[] = $k . $not . ' LIKE ' . $keyword;
			}
			$this->_like[] = $prefix . '(' . implode(" OR ", $arr) . ') ';
		}
		return $this;
	}

	/**
	 * 合并查询
	 *
	 * @param type $by
	 *
	 * @return this
	 */
	public function group($by) {
		if(is_string($by)) {
			$by = explode(',', $by);
		}
		foreach($by as $v) {
			$this->_group[] = $v;
		}
		return $this;
	}

	/**
	 * 排序规则
	 *    array('field'=>'desc|asc')
	 *
	 * @param string $by 排序字段规则
	 *
	 * @return this
	 */
	public function by($by) {
		if(!is_array($by)) {
			$this->_order[] = $by;
		} else {
			foreach($by as $k => $v) {
				$this->_order[] = $k . ' ' . strtoupper($v);
			}
		}
		return $this;
	}

	/**
	 * 限制记录条数
	 *
	 * @param        $value  限制记录条数
	 * @param string $offset 偏移量
	 *
	 * @return $this
	 */
	public function limit($value, $offset = '') {
		if(!is_array($value)) {
			$this->_limit = abs(intval($value));
			if($offset != '') {
				$this->_offset = abs(intval($offset));
			}
		} else {
			$this->_limit = abs(intval($value[0]));
			$this->_offset = abs(intval($value[1]));
		}

		return $this;
	}

	/**
	 * 分页查询
	 *
	 * @param int $page      当前页数
	 * @param int $page_size 每页数量
	 * @param int $base      基页
	 *
	 * @return this
	 */
	function limitPage($page, $page_size = 30, $base = 1) {
		$page = abs(intval($page));
		$page_size = abs(intval($page_size));
		$base = abs(intval($base));
		if($page < $base) {
			$page = $base;
		}
		return $this->limit($page_size, ($page - $base) * $page_size);
	}

	/**
	 * 检测并设置查询参数
	 *
	 * @param array $arr 各种条件组合
	 *
	 * @return this
	 */
	function _set_params($arr) {
		if(isset($arr['table'])) {
			$this->setTableName($arr['table']);
		}
		if(isset($arr['select'])) {
			$this->select($arr['select']);
		}
		if(isset($arr['where'])) {
			$this->where($arr['where']);
		}
		if(isset($arr['from'])) {
			$this->from($arr['from']);
		}
		if(isset($arr['join'])) {
			$this->join($arr['join']);
		}
		if(isset($arr['notin'])) {
			$this->notin($arr['notin']);
		}
		if(isset($arr['by'])) {
			$this->by($arr['by']);
		}
		if(isset($arr['limit'])) {
			$this->limit($arr['limit']);
		}
		if(isset($arr['in'])) {
			$this->in($arr['in']);
		}
		if(isset($arr['group'])) {
			$this->group($arr['group']);
		}
		return $this;
	}

	/**
	 * 最终的sql组合 并执行
	 *
	 * @param bool $auto true|false 是否自动执行
	 *
	 * @return string
	 */
	public function _compile_select($auto = true) {
		$sql = (!$this->_distinct) ? 'SELECT ' : 'SELECT DISTINCT ';

		$sql .= (count($this->_select) == 0) ? '*' : implode(', ', $this->_select);

		$sql .= " FROM ";

		$sql .= $this->getTableName();

		$sql .= " ";

		$sql .= implode(" ", $this->_join);

		if(count($this->_where) > 0 OR count($this->_like) > 0 OR count($this->_instr) > 0) {
			$sql .= " WHERE ";
		}

		$sql .= implode(" ", $this->_where);

		if(count($this->_like) > 0) {
			if(count($this->_where) > 0) {
				$sql .= " AND ";
			}
			$sql .= implode(" ", $this->_like);
		}

		if(count($this->_instr) > 0) {
			if(count($this->_where) > 0 OR count($this->_like) > 0) {
				$sql .= " AND ";
			}
			$sql .= implode(" ", $this->_instr);
		}

		if(count($this->_group) > 0) {
			$sql .= " GROUP BY ";
			$sql .= implode(', ', $this->_group);
		}

		if(count($this->_order) > 0) {
			$sql .= " ORDER BY ";
			$sql .= implode(', ', $this->_order);
		}
		if(is_numeric($this->_limit)) {
			$sql .= $this->db->limit($this->_limit, $this->_offset);
		}
		$this->_lastCompileSql = $sql;
		FileHelp::WriteLog(1, 'd', $sql, 'mysql', 'mysql/');
		$this->_reset_select();

		if($auto) {
			try {
				return $this->db->execute($sql);
			} catch(Exception $e) {
				return '';
			}
		}

		return $sql;
	}

	/**
	 * 获取最后的执行sql
	 *
	 * @return type
	 */
	public function get_compile_sql() {
		return $this->_lastCompileSql;
	}

	/**
	 * 执行重置操作
	 *
	 * @param array $vars 需要重置的数据集合
	 */
	public function _reset_run($vars) {
		foreach($vars as $item => $default_value) {
			$this->$item = $default_value;
		}
	}

	/**
	 * 重置各种搜索条件
	 */
	public function _reset_select() {
		$vars = array(
			'_select' => array(),
			'_join' => array(),
			'_where' => array(),
			'_like' => array(),
			'_instr' => array(),
			'_group' => array(),
			'_having' => array(),
			'_order' => array(),
			'_distinct' => false,
			'_limit' => false,
			'_offset' => false,
		);
		$this->_reset_run($vars);
	}

	public function _reset_write() {
		$this->_where = array();
//		$this->_autoData = array();
	}

	public function handles_lashes($str) {
		if(!get_magic_quotes_gpc()) {
			$laststr = $str;
		} else {
			$laststr = stripslashes($str);
		}
		return $laststr;
	}

	/*
	 * *************************************************************************
	 * ***以下是具体的数据操作，返回真实的数据。常用的数据操作可以封装在这里****
	 * *************************************************************************
	 */

	/**
	 * 插入数据（ if($replace==true) 删除主键记录再插入 ）
	 *
	 * @param      $data
	 * @param bool $replace
	 * @param bool $auto
	 *
	 * @return mixed
	 */
	public function insert($data, $replace = false, $auto = true) {
		$tableName = $this->getTableName();
		$sql = $this->db->insert($tableName, $data, $replace);
		FileHelp::WriteLog(1, 'd', $sql, 'mysql', 'mysql/');
		if($auto) {
			return $this->db->query($sql)
							->insert_id();
		} else {
			return $this->db->query($sql)
							->affected_rows();
		}
	}

	/**
	 * 防止主键重复插入
	 *
	 * @param $data
	 * @param $prkey
	 *
	 * @return mixed
	 */
	public function duplicateInsert($data, $prkey) {
		$tableName = $this->getTableName();
		$sql = $this->db->duplicateInsert($tableName, $data, $prkey);
		FileHelp::WriteLog(1, 'd', $sql, 'mysql', 'mysql/');
		return $this->db->query($sql)
						->affected_rows();
	}

	/**
	 * 更新数据
	 *
	 * @param array $data
	 *        array('num = num + 1'=>null);
	 *        array('num'=>1);
	 * @param type  $where
	 *
	 * @return string
	 */
	public function update($data, $where) {
		if(!isset($where) || !is_array($where)) {
			exit('参数错误');
		}
		$this->_set_params($where);
		$tableName = $this->getTableName();
		$sql = $this->db->update($tableName, $data, $this->_where, $this->_limit);
		FileHelp::WriteLog(1, 'd', $sql, 'mysql', 'mysql/');
		$this->_reset_select();
		return $this->db->query($sql)
						->affected_rows();
	}

	/**
	 * 删除数据
	 *
	 * @param array $where
	 *
	 * @return type
	 */
	public function delete($where) {
		$tableName = $this->getTableName();
		if(!isset($where) || !is_array($where)) {
			exit('参数错误');
		}
		$this->_set_params($where);
		$sql = $this->db->delete($tableName, $this->_where);
		FileHelp::WriteLog(1, 'd', $sql, 'mysql', 'mysql/');
		$this->_reset_select();
		return $this->db->query($sql)
						->affected_rows();
	}

	/**
	 * 直接执行手写sql语句
	 *
	 * @param type $sql
	 *
	 * @return type
	 */
	public function manualexec($sql) {
		FileHelp::WriteLog(1, 'd', $sql, 'mysql', 'mysql/');
		if($this->db->is_write($sql)) {
			return $this->db->exec($sql);
		}
		return $this->db->execute($sql);
	}

	/**
	 * 查询上次执行SQL语句
	 *
	 * @return mixed
	 */
	public function last_query() {
		return $this->db->last_query();
	}


	/**
	 * 返回单条记录 获取sqlcache数据
	 *
	 * @param array $arr
	 * @param bool  $_is_cache
	 * @param int   $_cache_time
	 *
	 * @return mixed
	 */
	public function getRowCache($arr = array(), $_is_cache = false, $_cache_time = CACHE_LIFETIME_SQL) {
		$sql = $this->_set_params($arr)
					->_compile_select(false);
		return $this->_getCache($sql, 'fetchRow', $_is_cache, $_cache_time);
	}

	/**
	 * 返回多条记录 获取sqlcache数据
	 *
	 * @param array $arr
	 * @param bool  $_is_cache
	 * @param int   $_cache_time
	 *
	 * @return mixed
	 */
	public function getLimitCache($arr = array(), $_is_cache = false, $_cache_time = CACHE_LIFETIME_SQL) {
		$sql = $this->_set_params($arr)
					->_compile_select(false);
		return $this->_getCache($sql, 'fetchAll', $_is_cache, $_cache_time);
	}

	/**
	 * 返回表count数据 获取sqlcache数据
	 *
	 * @param array $arr
	 * @param bool  $_is_cache
	 * @param int   $_cache_time
	 *
	 * @return mixed
	 */
	public function getCountCache($arr = array(), $_is_cache = false, $_cache_time = CACHE_LIFETIME_SQL) {
		$this->_set_params($arr);
		$sql = $this->count()
					->_compile_select(false);
		return $this->_getCache($sql, 'fetchRow', $_is_cache, $_cache_time);
	}

	/**
	 * 返回直接执行sql语句的缓存结果
	 *
	 * @param     $sql
	 * @param     $_is_cache
	 * @param int $_cache_time
	 *
	 * @return mixed
	 */
	public function execCache($sql, $_is_cache = false, $_cache_time = CACHE_LIFETIME_SQL) {
		return $this->_getCache($sql, 'fetchAll', $_is_cache, $_cache_time);
	}

	/**
	 * 获取缓存结果
	 *
	 * @param        $sql
	 * @param string $fun
	 * @param bool   $_is_cache
	 * @param int    $_cache_time
	 *
	 * @return mixed
	 */
	public function _getCache($sql, $fun = 'fetchRow', $_is_cache = false, $_cache_time = CACHE_LIFETIME_SQL) {
		$startime = BaseCom::getMillisecond();
//		$cache_sql = 'sql_' . md5($sql);
//		if($_is_cache) {
//			$res = SglRedis::getInstance()
//						   ->get($cache_sql);
//			if($res) {
//				FileHelp::WriteLog(1, 'd', 'SQL cache [' . $sql . '] cache [' . $cache_sql . '] ', 'mysqlcache', 'mysql/');
//				return json_decode($res, true);
//			}
//		}
//		if(!$res) {
 			$rtn = $this->db->execute($sql)
							->$fun();
//			$_is_cache ? SglRedis::getInstance()
//								 ->setex($cache_sql, $_cache_time, json_encode($rtn)) : '';
			$exetime = (BaseCom::getMillisecond() - $startime) / 1000;
			FileHelp::WriteLog(1, 'd', 'SQL exec [' . $sql . '] exectime [' . $exetime . '] ', 'mysqlcache', 'mysql/');
//		}
		return $rtn;
	}
}

?>