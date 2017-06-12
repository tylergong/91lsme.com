<?php

if (!defined('DS')) {
	define('DS', DIRECTORY_SEPARATOR);
}

if (!defined('ROOT_PATH')) {
	define('ROOT_PATH', dirname(__FILE__) . DS);
}

// 默认应用文件夹
if (!defined('ADP_PATH')) {
	define('ADP_PATH', 'app/web');
}

// 默认全局配置文件目录
if (!defined('CONFIG_PATH')) {
	define('CONFIG_PATH', ROOT_PATH . 'config' . DS);
}

// 默认类包目录
if (!defined('FRAMEWORK_PATH')) {
	define('FRAMEWORK_PATH', ROOT_PATH . 'framework' . DS);
}

// 默认插件目录
if (!defined('PLUGIN_PATH')) {
	define('PLUGIN_PATH', ROOT_PATH . 'plugin' . DS);
}

// 默认日志目录
if (!defined('LOG_PATH')) {
	define('LOG_PATH', ROOT_PATH . 'log' . DS);
}

// 默认应用目录全路径
if (!defined('APP_WEB_PATH')) {
	define('APP_WEB_PATH', ROOT_PATH . DS . ADP_PATH . DS);
}

// 默认应用入口文件夹
$app = "default";
if (isset($_GET['app']) && $_GET['app'] != null && $_GET['app'] != "") {
	$app = $_GET['app'];
}

// 默认应用视图程序
$view = "default";
if (isset($_GET['view']) && $_GET['view'] != null && $_GET['view'] != "") {
	$view = $_GET['view'];
}

// 默认应用方法
$action = "index";
if (isset($_GET['act']) && $_GET['act'] != null && $_GET['act'] != "") {
	$action = $_GET['act'];
}

$pattern = "/[0-9a-zA-Z]*/";
if (!preg_match($pattern, $app) || !preg_match($pattern, $view) || !preg_match($pattern, $action)) {
	echo 'error path';
	exit();
}

error_reporting(E_ALL ^ E_NOTICE);
require_once ROOT_PATH . 'config/config.php';
require_once FRAMEWORK_PATH . 'php/core.require.php';

try {
	$viewClassName = $view . "View";
	$viewClassFile = ADP_PATH . '/view/' . $app . "/" . $view . ".view.php";
	#echo $viewClassFile;
	if (!file_exists($viewClassFile)) {
		BaseCom::ShowError();
	}

	require($viewClassFile);
	$viewClass = new $viewClassName();

	if (!method_exists($viewClass, $action)) {
		BaseCom::ShowError();
	}

	$viewClass->$action();
} catch (Exception $e) {
	if (WEB_DEBUG == true) {
		print_r($e);
	} else {
		BaseCom::ShowError();
	}
}