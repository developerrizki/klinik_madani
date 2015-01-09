<?php
/**
* 
*/
class StatusController extends Controller
{
	
	function __construct()
	{
		parent::__construct();
	}

	public function index()
	{		
		global $cfg;
		global $globalVar;
		global $sessObj;

		$this->requireLogin();
		return $this->getHTML('status/home');
	}
}
?>

