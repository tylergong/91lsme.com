<?php

class Request {

	public $method = null;
	public $uri = null;
	public $ip = null;
	public $host = null;
	public $script_name = null;
	public $refer = null;
	public $query_string = null;
	public $query_params = null;
	public $post_params = null;

	public static function from_env() {
		$req = new Request();

		$req->uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
		$req->method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET';
		$req->query_string = isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : array();
		$uris = explode('?', $req->uri);
		$_GET['q'] = isset($uris[0]) ? $uris[0] : '/';
		$req->query_params = $_GET;
		$req->post_params = $_POST;

		$req->document_uri = isset($_SERVER['DOCUMENT_URI']) ? $_SERVER['DOCUMENT_URI'] : '';
		$req->refer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
		$req->ip = self::get_ip();
		if(isset($_SERVER['HTTP_HOST'])) {
			$req->host = $_SERVER['HTTP_HOST'];
		}
		$req->script_name = isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : '';

		return $req;
	}

	public static function get_ip($format = false) {
		$ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
		if(isset($_SERVER['HTTP_X_REAL_FORWARDED_FOR']) && preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER['HTTP_X_REAL_FORWARDED_FOR'])) {
			$ip = $_SERVER['HTTP_X_REAL_FORWARDED_FOR'];
		} elseif(isset($_SERVER['HTTP_X_FORWARDED_FOR']) && preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} elseif(isset($_SERVER['HTTP_X_REAL_IP']) && preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER['HTTP_X_REAL_IP'])) {
			$ip = $_SERVER['HTTP_X_REAL_IP'];
		} elseif(isset($_SERVER['HTTP_CLIENT_IP']) && preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER['HTTP_CLIENT_IP'])) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		}

		if($format == true) {
			$ip = self::iptolong($ip);
		}
		return $ip;
	}

	public static function iptolong($ip) {
		if(empty($ip))
			return 0;
		list($a, $b, $c, $d) = explode(".", $ip);
		$long = (($a * 256 + $b) * 256 + $c) * 256 + $d;
		return sprintf('%u', $long);
	}

	public static function is_ip($ip) {
		$p = '/^((([01]?[0-9]{1,2})|(2[0-4][0-9])|(25[0-5]))[.]){3}(([0-1]?[0-9]{1,2})|(2[0-4][0-9])|(25[0-5]))$/';
		return preg_match($p, $ip);
	}

	public function get_param($name) {

		if(array_key_exists($name, $this->query_params)) {
			return $this->query_params[$name];
		}

		if(array_key_exists($name, $this->post_params)) {
			return $this->post_params[$name];
		}

		return null;
	}

	public function get_int($name, $default = 0) {
		$r = $this->get_param($name);
		if($r === null) {
			return $default;
		}

		return (int)$r;
	}

	public function get_str($name, $default = '', $is_filter = true) {
		$r = $this->get_param($name);
		if($r === null) {
			return $default;
		}
		if($is_filter) {
			return $this->filter(trim((string)$r));
		} else {
			return trim((string)$r);
		}
	}

	public function get_array($name, $default = array()) {
		$r = $this->get_param($name);
		if($r === null) {
			return $default;
		}

		return (array)$r;
	}

	public function get_float($name, $default = 0.0, $precision = 4) {
		$r = $this->get_param($name);
		if($r === null) {
			return $default;
		}
		return (float)round($r, $precision);
	}

	public function get_raw_post($name, $default = '') {
		$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
		$postObj = json_decode($postStr);
		if(empty($postObj->$name)) {
			return $default;
		}
		return $postObj->$name;
	}

	public function is_get() {
		return $this->method === 'GET';
	}

	public function is_post() {
		return $this->method === 'POST';
	}

	public function is_ajax() {
		// php 判断是否为 ajax 请求
		if(isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && strtolower($_SERVER["HTTP_X_REQUESTED_WITH"]) == "xmlhttprequest") {
			return true;
		} else {
			return false;
		}
	}

	public function is_domain() {
		$servername = $_SERVER['HTTP_HOST'];//当前运行脚本所在服务器主机的名字。
		$sub_from = $_SERVER["HTTP_REFERER"];//链接到当前页面的前一页面的 URL 地址
		$sub_len = strlen($servername);//统计服务器的名字长度。
		$http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? '8' : '7';
		$checkfrom = substr($sub_from, $http_type, $sub_len);//截取提交到前一页面的url，不包含http:://的部分。
		if($checkfrom != $servername) {
			return false;
		} else {
			return true;
		}
	}

	public function filter($str) {
//		$tTrans[0] = array('%20' => '', '%27' => '', '%2527' => '', '*' => '', '"' => ' ', "'" => '', ';' => '', '<' => '&lt;', '>' => '&gt;', "{" => '', '}' => '', '\\' => '', '`' => '');
//		$tTrans[1] = array('<script' => '', '<iframe' => '', '<frame' => '', '</script>' => '', '</iframe>' => '', '</frame>' => '');
//		$str = strtr($str, $tTrans[0]);
//		$str = strtr($str, $tTrans[1]);
		return $str;
	}

}
