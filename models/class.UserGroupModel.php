<?php

class UserGroupModel extends Model {

	public function __construct() {

		parent::__construct();

		$this->_table 	= 'dp_sys_user_group';
		$this->_id 		= 'user_id';
	}

	public function getGroup($id){
        
        global $cfg;

        $sql = "SELECT 
        			* 
        		FROM  
        			dp_sys_user_group, dp_sys_group
        		WHERE 
        			dp_sys_user_group.user_id 	= '$id' AND
        			dp_sys_group.group_id 		= .dp_sys_user_group.group_id
        		";

        $res = null;

        try {
            $this->_dbObj->query($sql);

            $res = $this->_dbObj->fetch();
        } catch (DbException $e) { Error::store('User', $e->getMessage()); }

        return $res;
    }

}