<?php
/**
* 
*/
class MenuController extends Controller
{
	
	function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		global $cfg, $globalVar, $sessObj;

		return $this->getHTML('menu/home');
	}
}
?>