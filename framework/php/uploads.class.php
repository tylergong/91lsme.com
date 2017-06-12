<?php

class Uploads {

	// 定义允许上传的文件扩展名
	private $ext_arr;
	// 最大文件大小
	private $max_size;
	// 文件保存目录路径
	private $save_path;
	// 文件保存目录URL
	private $save_url;

	public function __construct() {
		
	}

	public static function uploadfiles($files, $names = 'imgFile', $dir = 'image', $is_ajax = true) {
		$obj = new self();
		$obj->ext_arr = array(
			'image' => array('gif', 'jpg', 'jpeg', 'png', 'bmp'),
			'flash' => array('swf', 'flv'),
			'media' => array('swf', 'flv', 'mp3', 'wav', 'wma', 'wmv', 'mid', 'avi', 'mpg', 'asf', 'rm', 'rmvb'),
			'file' => array('doc', 'docx', 'xls', 'xlsx', 'ppt', 'htm', 'html', 'txt', 'zip', 'rar', 'gz', 'bz2'),
			'ad' => array('gif', 'jpg', 'jpeg', 'png', 'bmp'),
		);
		$obj->max_size = 1000000;
		$obj->save_path = ROOT_PATH . 'attached/';
		$obj->save_url = 'attached/';

		//PHP上传失败
		if (!empty($files[$names]['error'])) {
			switch ($files[$names]['error']) {
				case '1':
					$error = '超过php.ini允许的大小。';
					break;
				case '2':
					$error = '超过表单允许的大小。';
					break;
				case '3':
					$error = '图片只有部分被上传。';
					break;
				case '4':
					$error = '请选择图片。';
					break;
				case '6':
					$error = '找不到临时目录。';
					break;
				case '7':
					$error = '写文件到硬盘出错。';
					break;
				case '8':
					$error = 'File upload stopped by extension。';
					break;
				case '999':
				default:
					$error = '未知错误。';
			}
			$obj->alert($error);
		}

		// 有上传文件时
		if (empty($files) === false) {
			//原文件名
			$file_name = $files[$names]['name'];
			//服务器上临时文件名
			$tmp_name = $files[$names]['tmp_name'];
			//文件大小
			$file_size = $files[$names]['size'];
			//检查文件名
			if (!$file_name) {
				$obj->alert("请选择文件。");
			}
			//检查目录
			if (@is_dir($obj->save_path) === false) {
				$obj->alert("上传目录不存在。", $is_ajax);
			}
			//检查目录写权限
			if (@is_writable($obj->save_path) === false) {
				$obj->alert("上传目录没有写权限。", $is_ajax);
			}
			//检查是否已上传
			if (@is_uploaded_file($tmp_name) === false) {
				$obj->alert("上传失败。", $is_ajax);
			}
			//检查文件大小
			if ($file_size > $obj->max_size) {
				$obj->alert("上传文件大小超过限制。", $is_ajax);
			}
			//检查目录名
			$dir_name = empty($dir) ? 'image' : trim($dir);
			if (empty($obj->ext_arr[$dir_name])) {
				$obj->alert("目录名不正确。", $is_ajax);
			}
			//获得文件扩展名
			$temp_arr = explode(".", $file_name);
			$file_ext = array_pop($temp_arr);
			$file_ext = trim($file_ext);
			$file_ext = strtolower($file_ext);
			//检查扩展名
			if (in_array($file_ext, $obj->ext_arr[$dir_name]) === false) {
				$obj->alert("上传文件扩展名是不允许的扩展名。\n只允许" . implode(",", $obj->ext_arr[$dir_name]) . "格式。", $is_ajax);
			}
			//创建文件夹
			if ($dir_name !== '') {
				$obj->save_path .= $dir_name . "/";
				$obj->save_url .= $dir_name . "/";
				if (!file_exists($obj->save_path)) {
					mkdir($obj->save_path);
				}
			}
			$ymd = date("Ymd");
			$obj->save_path .= $ymd . "/";
			$obj->save_url .= $ymd . "/";
			if (!file_exists($obj->save_path)) {
				mkdir($obj->save_path);
			}
			//新文件名
			$new_file_name = date("YmdHis") . '_' . rand(10000, 99999) . '.' . $file_ext;
			//移动文件
			$file_path = $obj->save_path . $new_file_name;
			if (move_uploaded_file($tmp_name, $file_path) === false) {
				$obj->alert("上传文件失败。", $is_ajax);
			}
			@chmod($file_path, 0644);
			$file_url = $obj->save_url . $new_file_name;
			if ($is_ajax) {
				header('Content-type: text/html; charset=UTF-8');
				echo json_encode(array('error' => 0, 'url' => $file_url));
				exit;
			} else {
				return json_encode(array('error' => 0, 'url' => $file_url));
			}
		}
	}

	public static function alert($msg, $is_ajax = true) {
		if ($is_ajax) {
			header('Content-type: text/html; charset=UTF-8');
			echo json_encode(array('error' => 1, 'message' => $msg));
			exit;
		} else {
			return json_encode(array('error' => 1, 'message' => $msg));
		}
	}

}
