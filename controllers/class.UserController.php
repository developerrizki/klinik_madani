<?php

class UserController extends Controller {

	private $_userObj;

	public function __construct() {

		parent::__construct();
		$this->_userObj 		= $this->loadModel('User');
		$this->theme->setRootTemplate('root_login');
	}

	public function index() {

		global $cfg, $globalVar;

		if ($this->sessObj->getUserID()) {
			HTTP::redirect(ROOT_URL .'/dashboard');
		}

		$usermail = $this->postParam('email');
		$password = $this->postParam('password');

		$error ="";

		try {
			if (!empty($usermail) && !empty($password)) {
				
				Loader::loadClass('Auth', CLASS_DIR . '/blackcat/auth');
				
				$auth 	= Auth::getInstance('db');
				
				$auth->setConnection($this->dbObj);

				if ($auth->verify($usermail, $password)) {
				
					$userDetail	= $this->_userObj->emailExist($usermail);				
					$getGroup	= $this->_userObj->getGroup($userDetail->user_id);

					$this->sessObj->login($userDetail->user_id, $userDetail->user_email);
				
					$this->sessObj->setVar('lastlogtime', $userDetail->user_last_login_time);				
					$this->sessObj->setVar('lastlogfrom', $userDetail->user_last_login_from);

					$this->_userObj->updateLastLog($userDetail->user_id);
				
					HTTP::redirect(ROOT_URL .'/dashboard');

				} else {
					$error = "
					<div class='alert alert-danger alert-dismissable'>
                       <button type'button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
                        Nama Pengguna atau Kata sandi yang anda masukan salah !
                    </div>";
				}
			} else {
				$error ="";
			} 
			
		} catch (Exception $e) {
			Error::store('UserController', $e->getMessage());
		}

		$this->set('error',$error);

		return $this->getHTML('login');		
		
	}

	public function logout() {

        $this->sessObj->logout();

        HTTP::redirect(ROOT_URL);
	}


}

?>