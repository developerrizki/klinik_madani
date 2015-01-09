<?php

class DashboardController extends Controller {

	private $_userObj;

	public function __construct() {

		parent::__construct();
		$this->theme->setRootTemplate('root');
	}

	public function index() {

		global $cfg;
		global $globalVar;
		global $sessObj;

		$this->requireLogin();
		return $this->getHTML('dashboard/dashboard');		
		
	}
}

?>