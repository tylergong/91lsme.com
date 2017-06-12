<?php

class BaseCom {

	public static function ShowError($code = 404) {
		switch ($code) {
			case 404:
				header('Location: ' . WEB_SITES_URL . '/404.shtml');
				die();
				break;
			default:
				break;
		}
	}

	/**
	 *  分页
	 * @param type $pageSize	每页显示多少
	 * @param type $curPage		当前页数
	 * @param type $count		一共多少记录
	 * @param type $pagePer		分页间隔 默认5
	 * @param type $url			跳转url
	 * @param type $query		url参数 不用以"?"开头，参数间使用"&"连接，如：xxx=123&yyy=345&zzz=098
	 * @return string
	 */
	public static function GetPageList($pageSize, $curPage, $count, $pagePer, $url, $query) {
		$pageTemplate = ""; //分页HTML	
		$pageLast = ceil($count / $pageSize);
		if ($pageLast <= 1) {
			return $pageTemplate;
		}
		$pageBtnNum = $pagePer;
		$pageCorrection = $pageCorrectionPro = $pageCorrectionBfo = floor($pageBtnNum / 2);
		if ($pageBtnNum % 2 == 0) {
			$pageCorrectionPro -= 1;
		}
		$pageBeginNum = floor(($curPage - $pageCorrection ) / $pageBtnNum) * $pageBtnNum;
		$pageEndNum = floor($curPage / $pageBtnNum) * $pageBtnNum + $pageBtnNum;
		$pageBeginNum = $curPage - $pageCorrectionPro - 2 <= 0 ? 1 : $curPage - $pageCorrectionPro;
		if ($pageLast - $curPage + $pageCorrectionPro < $pageBtnNum) {
			$pageBeginNum = $pageLast - $pageBtnNum + 1;
		}
		if ($pageBeginNum <= 0) {
			$pageBeginNum = 1;
		}
		$pageEndNum = $pageLast - $curPage - 1 <= $pageCorrection ? $pageLast : $curPage + $pageCorrection;
		if ($curPage - $pageBtnNum + 1 < 0 && $pageLast > $pageBtnNum) {
			$pageEndNum = $pageBtnNum;
		}
		if ($pageLast <= $pageBtnNum + 1) {
			$pageBeginNum = 1;
			$pageEndNum = $pageLast;
		}
		if ($curPage > 1) {
			$pageTemplate .= "<a  href='" . $url . "?page=" . ($curPage - 1) . "&" . $query . "'>上一页</a>";
		}
		if ($pageBeginNum > 2 && $pageLast != $pageBtnNum && $pageLast != $pageBtnNum + 1) {
			$pageTemplate .= "<a  href='" . $url . "?page=" . 1 . "&" . $query . "'>1</a>";
			$pageTemplate .= "<a>...</a>";
		}
		for ($i = $pageBeginNum; $i <= $pageEndNum; $i++) {
			if ($i == $curPage) {
				$pageTemplate .= "<span>[" . $i . "]</span>";
			} else {
				$pageTemplate .= "<a  href='" . $url . "?page=" . $i . "&" . $query . "'>$i</a>";
			}
		}
		if ($pageEndNum < $pageLast - 1 && $pageLast != $pageBtnNum && $pageLast != $pageBtnNum + 1) {
			$pageTemplate .= "<a>...</a>";
			$pageTemplate .= "<a  href='" . $url . "?page=" . $pageLast . "&" . $query . "'>$pageLast</a>";
		}
		if ($curPage != $pageLast) {
			$pageTemplate .= "<a  href='" . $url . "?page=" . ($curPage + 1) . "&" . $query . "'>下一页</a>";
		}
		return $pageTemplate;
	}

	/**
	 *  分页
	 * @param type $pageSize	每页显示多少
	 * @param type $curPage		当前页数
	 * @param type $count		一共多少记录
	 * @param type $pagePer		分页间隔 默认5
	 * @param type $url			跳转url
	 * @param type $query		url参数 不用以"?"开头，参数间使用"&"连接，如：xxx=123&yyy=345&zzz=098
	 * @return string
	 */
	public static function GetPageListStatic($pageSize, $curPage, $count, $pagePer, $url, $query) {
		$pageTemplate = ""; //分页HTML	
		$pageLast = ceil($count / $pageSize);
		if ($pageLast <= 1) {
			return $pageTemplate;
		}
		$pageBtnNum = $pagePer;
		$pageCorrection = $pageCorrectionPro = $pageCorrectionBfo = floor($pageBtnNum / 2);
		if ($pageBtnNum % 2 == 0) {
			$pageCorrectionPro -= 1;
		}
		$pageBeginNum = floor(($curPage - $pageCorrection ) / $pageBtnNum) * $pageBtnNum;
		$pageEndNum = floor($curPage / $pageBtnNum) * $pageBtnNum + $pageBtnNum;
		$pageBeginNum = $curPage - $pageCorrectionPro - 2 <= 0 ? 1 : $curPage - $pageCorrectionPro;
		if ($pageLast - $curPage + $pageCorrectionPro < $pageBtnNum) {
			$pageBeginNum = $pageLast - $pageBtnNum + 1;
		}
		if ($pageBeginNum <= 0) {
			$pageBeginNum = 1;
		}
		$pageEndNum = $pageLast - $curPage - 1 <= $pageCorrection ? $pageLast : $curPage + $pageCorrection;
		if ($curPage - $pageBtnNum + 1 < 0 && $pageLast > $pageBtnNum) {
			$pageEndNum = $pageBtnNum;
		}
		if ($pageLast <= $pageBtnNum + 1) {
			$pageBeginNum = 1;
			$pageEndNum = $pageLast;
		}
		if ($curPage > 1) {
			$pageTemplate .= "<a  href='" . $url . "/" . $query . "_" . ( $curPage - 1) . ".shtml'>上一页</a>";
		}
		if ($pageBeginNum > 2 && $pageLast != $pageBtnNum && $pageLast != $pageBtnNum + 1) {
			$pageTemplate .= "<a  href='" . $url . "/" . $query . "_" . 1 . ".shtml'>1</a>";
			$pageTemplate .= "<a>...</a>";
		}
		for ($i = $pageBeginNum; $i <= $pageEndNum; $i++) {
			if ($i == $curPage) {
				$pageTemplate .= "<span>[" . $i . "]</span>";
			} else {
				$pageTemplate .= "<a  href='" . $url . "/" . $query . "_" . $i . ".shtml'>$i</a>";
			}
		}
		if ($pageEndNum < $pageLast - 1 && $pageLast != $pageBtnNum && $pageLast != $pageBtnNum + 1) {
			$pageTemplate .= "<a>...</a>";
			$pageTemplate .= "<a  href='" . $url . "/" . $query . "_" . $pageLast . ".shtml'>$pageLast</a>";
		}
		if ($curPage != $pageLast) {
			$pageTemplate .= "<a  href='" . $url . "/" . $query . "_" . ($curPage + 1) . ".shtml'>下一页</a>";
		}
		return $pageTemplate;
	}

	/**
	 *  分页(后台)
	 * @param type $pageSize	每页显示多少
	 * @param type $curPage		当前页数
	 * @param type $count		一共多少记录 
	 * @param type $url			跳转url
	 * @param type $query		url参数 不用以"?"开头，参数间使用"&"连接，如：xxx=123&yyy=345&zzz=098
	 * @return string
	 */
	public static function GetPageListAdmin($pageSize, $curPage, $count, $url, $query) {

		$pageTemplate = "共" . $count . "条数据"; //分页HTML	
		$pageLast = ceil($count / $pageSize);
		if ($pageLast <= 1) {
			$pageTemplate .= " 当前 1/1页";
		} else {
			$pageTemplate .= " 当前 " . $curPage . "/" . $pageLast . "页";
			if ($curPage == 1) {
				$pageTemplate .= "<span>首页</span><span>上一页</span>";
			} else {
				$pageTemplate .= "<a  onclick=\"menu.tourl('" . $url . "?page=1" . "&" . $query . "')\">首页</a>";
				$pageTemplate .= "<a  onclick=\"menu.tourl('" . $url . "?page=" . ($curPage - 1) . "&" . $query . "')\">上一页</a>";
			}
			if ($curPage == $pageLast) {
				$pageTemplate .= "<span>下一页</span><span>末页</span>";
			} else {
				$pageTemplate .= "<a  onclick=\"menu.tourl('" . $url . "?page=" . ( $curPage + 1) . "&" . $query . "')\">下一页</a>";
				$pageTemplate .= "<a  onclick=\"menu.tourl('" . $url . "?page=" . $pageLast . "&" . $query . "')\">末页</a>";
			}
		}
		return $pageTemplate;
	}

	public static function EncodeId($id) {
		$td = mcrypt_module_open(MCRYPT_DES, '', 'ecb', ''); //使用MCRYPT_DES算法,ecb模式
		$iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
		$ks = mcrypt_enc_get_key_size($td);
		$key = "658974123"; //密钥
		$key = substr(md5($key), 0, $ks);
		mcrypt_generic_init($td, $key, $iv); //初始处理
		//加密
		$encrypted = base64_encode(base64_encode(mcrypt_generic($td, $id)));

		//结束处理
		mcrypt_generic_deinit($td);
		mcrypt_module_close($td);
		return $encrypted;
	}

	public static function DecodeId($crypt) {
		$crypt = base64_decode(base64_decode(trim($crypt)));
		if (empty($crypt)) {
			return '';
		}
		$td = mcrypt_module_open(MCRYPT_DES, '', 'ecb', ''); //使用MCRYPT_DES算法,ecb模式
		$iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
		$ks = mcrypt_enc_get_key_size($td);
		$key = "658974123"; //密钥
		$key = substr(md5($key), 0, $ks);
		mcrypt_generic_init($td, $key, $iv); //初始处理
		//初始解密处理
		mcrypt_generic_init($td, $key, $iv);
		//解密
		$decrypted = mdecrypt_generic($td, $crypt);
		//结束
		mcrypt_generic_deinit($td);
		mcrypt_module_close($td);
		return trim($decrypted);
	}

	/**
	 * 获取毫秒
	 *
	 * @return float
	 */
	public static function getMillisecond() {
		list($t1, $t2) = explode(' ', microtime());
		return (float)sprintf('%.0f', (floatval($t1) + floatval($t2)) * 1000);
	}

}

?>