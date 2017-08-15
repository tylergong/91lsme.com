<?php

/**
 * 默认数据源
 */
$defaultDbConfig = array(
	'mysql_code' => 'utf8',
	'mysql_server' => '127.0.0.1',
	'mysql_prot' => 3306,
	'mysql_user' => 'root',
	'mysql_pass' => '111111',
	'mysql_db' => 'qdm114542290_db',
	'mysql_prefix' => '',
	'mysql_master' => 1
);

/**
 *  其他数据源（读）
 */
$otherDbconf = array(
	array(
		'mysql_code' => 'utf8',
		'mysql_server' => '127.0.0.1',
		'mysql_prot' => 3306,
		'mysql_user' => 'root',
		'mysql_pass' => '111111',
		'mysql_db' => 'qdm114542290_db',
		'mysql_prefix' => '',
		'mysql_master' => 0
	)
);
$i = rand(0, count($otherDbconf) - 1);
$readerDbConfig = $otherDbconf[$i];
