<?php

require_once (APP_WEB_PATH . 'controller/upload.controller.php');

class UploadView {

	private $uploadController = null;

	public function __construct() {
		$this->uploadController = new uploadController();
	}

	public function images() {
		$dir = isset($_GET['dir']) ? $_GET['dir'] : '';
		$names = 'imgFile';
		return $this->uploadController->C_uploadfiles($_FILES, $names, $dir, true);
	}

}
