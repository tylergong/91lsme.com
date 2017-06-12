<?php

require_once (FRAMEWORK_PATH . 'base/controller/Base.controller.php');

class uploadController extends BaseController {

	public function C_uploadfiles($files, $names, $dir, $is_ajax) {
		return Uploads::uploadfiles($files, $names, $dir, $is_ajax);
	}

}
