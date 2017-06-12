<?php

/**
 * 默认数据源
 */
$defaultDbConfig = array(
	'mysql_code' => 'utf8',
	'mysql_server' => 'qdm114542290.my3w.com',
	'mysql_prot' => 3306,
	'mysql_user' => 'qdm114542290',
	'mysql_pass' => 'gongming1986',
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
		'mysql_server' => 'qdm114542290.my3w.com',
		'mysql_prot' => 3306,
		'mysql_user' => 'qdm114542290',
		'mysql_pass' => 'gongming1986',
		'mysql_db' => 'qdm114542290_db',
		'mysql_prefix' => '',
		'mysql_master' => 0
	)
);
$i = rand(0, count($otherDbconf) - 1);
$readerDbConfig = $otherDbconf[$i];
