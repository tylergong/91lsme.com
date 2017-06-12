<?php

class FileHelp {

	/**
	 * 生成静态文件
	 * $file 文件绝对路径
	 * $html 文件内容
	 */
	public static function BuildHtml($file, $html) {
		$fileopen = fopen($file, "w");
		fwrite($fileopen, $html);
		fclose($fileopen);
	}

	/**
	 * 拷贝文件至新目录
	 */
	public static function CopyFile($oldDir, $newDir) {

		if (!is_file($oldDir)) {
			echo "Please create '" . $oldDir . "' first.";
		}

		if (!copy($oldDir, $newDir)) {
			echo "failed to copy $oldDir...\n";
		}
	}

	/**
	 * 日志接口函数
	 * @access public
	 * @param int $modle			1: 固定模式 2: 自由模式 
	 * @param string $log_time		d:按天存入 m:按月存 y:按年存 null or '':无需区分
	 * @param string $content		日志主体内容(必须是字符串形式)
	 * @param string $filename		日志文件名（如果为空，就取写日志的文件名）
	 * @param string $dir			日志存放的文件夹路径
	 * @return viod
	 */
	public static function WriteLog($modle = 1, $log_time = '', $content = '', $filename = '', $dir = '') {
		$str = '';
		// 定义文件初始目录
		$root_path = ROOT_PATH . 'log/';

		// 判断是日志文件名称区分
		switch ($log_time) {
			case "d":
				$log_date = '_' . date("Ymd");
				break;
			case "m":
				$log_date = '_' . date("Ym");
				break;
			case "y":
				$log_date = '_' . date("Y");
				break;
			default:
				$log_date = '';
				break;
		}
		$filename = $filename . $log_date . '.log';

		// 判断文件存放路径是否存在
		$log_path = $root_path . $dir;
		if (!file_exists($log_path)) {
			self::makeDir($log_path); // 创建目录（支持多级）
		}

		// 判断是什么写入模式（这里规则暂时这么定义，简单一点）
		if ($modle == 1) {
			$str .= "Exec Time [ " . date("Y-m-d H:i:s", time()) . " ] ip [ " . HttpHelp::GetIPaddress() . " ] ";
		}
		$str .= $content;
		$str .= "\n";

		$log_path = $log_path . $filename;

		//file_put_contents($log_path, $str, FILE_APPEND);
		$file = new SplFileObject($log_path, "a+");
		$written = $file->fwrite($str);
	}

	/**
	 * 检查目标文件夹是否存在，如果不存在则自动创建该目录
	 * @access public
	 * @param string $folder 目录路径。不能使用相对于网站根目录的URL
	 * @return Boolean
	 */
	public static function makeDir($folder) {
		$reval = false;
		if (!file_exists($folder)) {
			// 如果目录不存在则尝试创建该目录
			@umask(0);
			// 将目录路径拆分成数组
			preg_match_all('/([^\/]*)\/?/i', $folder, $atmp);
			// 如果第一个字符为/则当作物理路径处理
			$base = ($atmp[0][0] == '/') ? '/' : '';
			// 遍历包含路径信息的数组
			foreach ($atmp[1] AS $val) {
				if ('' != $val) {
					$base .= $val;
					if ('..' == $val || '.' == $val) {
						// 如果目录为.或者..则直接补/继续下一个循环
						$base .= '/';
						continue;
					}
				} else {
					continue;
				}
				$base .= '/';
				if (!file_exists($base)) {
					// 尝试创建目录，如果创建失败则继续循环
					if (@mkdir(rtrim($base, '/'), 0775)) {
						@chmod($base, 0775);
						$reval = true;
					}
				}
			}
		} else {
			// 路径已经存在。返回该路径是不是一个目录
			$reval = is_dir($folder);
		}
		clearstatcache();
		return $reval;
	}

	/**
	 * 获取配置文件内容
	 * @param type $file
	 * @param type $ini
	 * @param type $type
	 * @return boolean
	 */
	public static function get_config($file, $ini, $type = "string") {
		if (!file_exists($file)) {
			return false;
		}
		$str = file_get_contents($file);
		if ($type == "int") {
			$config = preg_match("/" . preg_quote($ini) . "=(.*)/", $str, $res);
			return $res[1];
		} else {
			$config = preg_match("/" . preg_quote($ini) . "=\"(.*)\"/", $str, $res);
			if ($res[1] == null) {
				$config = preg_match("/" . preg_quote($ini) . "='(.*)'/", $str, $res);
			}
			return $res[1];
		}
	}

	/**
	 * 修改配置文件类容
	 * @param type $file
	 * @param type $ini
	 * @param type $value
	 * @param type $type
	 * @return string|boolean
	 */
	public static function update_config($file, $ini, $value, $type = "string") {
		if (!file_exists($file)) {
			return false;
		}
		$str = file_get_contents($file);
		$str2 = "";
		if ($type == "int") {
			$str2 = preg_replace("/" . preg_quote($ini) . "=(.*)/", $ini . "=" . $value, $str);
		} else {
			$str2 = preg_replace("/" . preg_quote($ini) . "=(.*)/", $ini . "=\"" . $value . "\"", $str);
		}
		file_put_contents($file, $str2);
		return ture;
	}

}

?>