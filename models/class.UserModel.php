<?php
/**
 * User Model
 *
 * Last updated: May 28, 2012, 05:22 PM
 *
 * @author Lorensius W. L. T <lorenz@londatiga.net>
 *
 */

class UserModel extends Model
{
    public function __construct()
    {
        global $cfg;

        parent::__construct();

        $this->_table = $cfg['sys']['tblPrefix'] . '_sys_user';
        $this->_id    = 'user_id';
    }

    public function emailExist($usermail)
    {
        return $this->find(array('filter' => array("user_email = '$usermail'")));
    }

    public function getGroup($id){
        
        global $cfg;

        $sql = "SELECT * FROM  " . $cfg['sys']['tblPrefix'] . "_sys_user_group WHERE user_id = '$id'";

        $res = null;

        try {
            $this->_dbObj->query($sql);

            $res = $this->_dbObj->fetch();
        } catch (DbException $e) { Error::store('User', $e->getMessage()); }

        return $res;
    }

    public function getDetailById($id){
        
        global $cfg;

        $sql = "SELECT * FROM  " . $cfg['sys']['tblPrefix'] . "_sys_user WHERE user_id = '$id'";

        $res = null;

        try {
            $this->_dbObj->query($sql);

            $res = $this->_dbObj->fetch();
        } catch (DbException $e) { Error::store('User', $e->getMessage()); }

        return $res;
    }

     /**
     * Update last login data
     *
     * @param int $id User id
     *
     * @return void
     */
    public function updateLastLog($id)
    {
        $ip   = $_SERVER['REMOTE_ADDR'];

        $this->update(array("user_last_login_time = NOW()", "user_last_login_from = '$ip'"), array("user_id = '$id'"));
    }

    public function getGroupList() {
        
        global $cfg;

        $sql = "SELECT * FROM dp_sys_group";
        $res = null;

        try {
            $this->_dbObj->query($sql);

            $res = $this->_dbObj->fetchAll();
        } catch (DbException $e) {
            Error::store('User', $e->getMessage());
        }

        return $res;
    }

    public function getLastRegister(){
        
        global $cfg;

        $sql = "SELECT * FROM  " . $cfg['sys']['tblPrefix'] . "_sys_user ORDER BY user_id DESC LIMIT 1";

        $res = null;

        try {
            $this->_dbObj->query($sql);

            $res = $this->_dbObj->fetch();
        } catch (DbException $e) { Error::store('User', $e->getMessage()); }

        return $res;
    }

}