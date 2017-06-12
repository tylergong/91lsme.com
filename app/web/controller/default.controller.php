<?php

require_once (FRAMEWORK_PATH . 'base/controller/Base.controller.php');

class DefaultController extends BaseController {

	public function init() {
		
	}

	// 自定义qq号加密
	// 从0位开始，每次取2位，取出第一位记录，将第二位放至队列末尾。如此循环
	public function qq_encrypt($str) {
		$head = 0;
		$tail = strlen($str);
		$en_str = array();
		$tmp_str = $str;
		while ($head < $tail) {
			$en_str[] = $tmp_str[$head];
			$head++;
			if ($head == $tail) {
				break;
			}
			$tmp_str[$tail] = $tmp_str[$head];
			$tail++;
			$head++;
		}
		return $en_str;
	}

	// 自定义qq号解密
	// 从末尾开始，每次都将最后一位向前挪动 i++ 位。
	public function qq_decrypt($str) {
		$head = 0;
		$tail = strlen($str) - 1;
		$tmp_str = $str;
		for ($i = 1; $i < $tail; $i++) {
			for ($j = 0; $j < $i; $j++) {
				$de_str = $tmp_str[$tail - $j];
				$tmp_str[$tail - $j] = $tmp_str[$tail - $j - 1];
				$tmp_str[$tail - $j - 1] = $de_str;
			}
		} 
		return $tmp_str;
	}

}
