<?php
/**
 * BlackCat PHP 5 Framework
 *
 * Last updated: June 17, 2010, 12:35 AM
 *
 * @package   user
 * @author    Lorensius W. L. T <lorenz@londatiga.net>
 * @version   1.0
 * @copyright Copyright (c) 2010 Lorensius W. L. T
 */

/**
 * Error handler class
 */
require_once CLASS_DIR . '/blackcat/model/class.Model.php';


/**
 * User handling class
 *
 * @package   user
 * @author    Lorensius W. L. T <lorenz@londatiga.net>
 * @version   1.0
 * @copyright Copyright (c) 2010 Lorensius W. L. T
 *
 */
class User extends Model
{

    /**
     * Constructor.
     * Create a new instance of this class
     *
     * @return void
     */
    public function __construct()
    {
        global $cfg;

        parent::__construct();

        $this->_dbObj = Registry::get('db');
        $this->_table = 'cr_users';
    }

    public function getInfo($username)
    {
        global $cfg;

        $sql = "SELECT
                            a.*,
                            b.id_user,
                            b.status,
                            CONCAT(b.tanggal_expired, ' ', '00:00:00') AS tanggal_expired
                FROM
                            users a
                JOIN
                            user_souniq_client b
                USING(id_user)
                WHERE
                            email = '$username'";

        $res = null;

        try {
            $this->_dbObj->query($sql);

            $res = ($this->_dbObj->getNumRows()) ? $this->_dbObj->fetch() : null;
        } catch (DbException $e) { Error::store('User', $e->getMessage()); }

        return $res;
    }

    public function getInfoBySession($session)
    {
        global $cfg;

        $sql = "SELECT
                            a.*,
                            b.id_user,
                            b.status,
                            CONCAT(b.tanggal_expired, ' ', '00:00:00') AS tanggal_expired,
                            c.*
                FROM
                            users a
                JOIN
                            user_souniq_client b
                USING(id_user)
                JOIN
                            user_souniq_session c
                ON a.email = c.username
                WHERE
                            session_id = '$session'";

        $res = null;

        try {
            $this->_dbObj->query($sql);

            $res = ($this->_dbObj->getNumRows()) ? $this->_dbObj->fetch() : null;
        } catch (DbException $e) { Error::store('User', $e->getMessage()); }

        return $res;
    }

    public function valid($username, $password)
    {
        global $cfg;

        $sql  = "SELECT
                            *
                 FROM
                            users
                 JOIN
                            user_souniq_client
                 USING(id_user)
                 WHERE
                            email = '$username'
                            AND
                            pwd = md5('$password')
                            AND
                            status = '1'";

        $res = false;

        try {
            $this->_dbObj->query($sql);

            $res = ($this->_dbObj->getNumRows()) ? true : false;
        } catch (DbException $e) { Error::store('User', $e->getMessage()); }

        return $res;
    }

    public function sessionExists($session)
    {
        $sql = "SELECT
                        *
                FROM
                        user_souniq_session
                WHERE
                        session_id = '$session'";
        $res = false;

        try {
            $this->_dbObj->query($sql);

            $res = ($this->_dbObj->getNumRows()) ? true : false;
        } catch (DbException $e) { Error::store('User', $e->getMessage()); }

        return $res;
    }

    public function isLoggedin($username)
    {
        $sql = "SELECT
                        *
                FROM
                        user_souniq_session
                WHERE
                        username = '$username'";
        $res = false;

        try {
            $this->_dbObj->query($sql);

            $res = ($this->_dbObj->getNumRows()) ? true : false;
        } catch (DbException $e) { Error::store('User', $e->getMessage()); }

        return $res;
    }

    public function saveSession($username, $session)
    {
        $res = false;

        try {
            $value      = array();

            $value[]    = "username     = '$username'";
            $value[]    = "session_id   = '$session'";
            $value[]    = "time         =  NOW()";

            $this->_dbObj->insertRecord('user_souniq_session', $value);

            $res = true;
        } catch (DAALException $e) {
            Error::store('User', $e->getMessage());
        }

        return $res;
    }

    public function deleteSession($username)
    {
        $res = false;

        try {
            $this->_dbObj->deleteRecord('user_souniq_session', array("username = '$username'"));

            $res = true;
        } catch (DAALException $e) {
            Error::store('User', $e->getMessage());
        }

        return $res;
    }

    public function passSalt($pass, $salt=null)
    {
        if ($salt === null){
			$salt = substr(md5(uniqid(rand(), true)), 0, 9);
        } else {
			$salt = substr($salt, 0, 9);
		}

		return $salt . sha1($pass . $salt);
    }

    /**
    *  Get group list of a user
    *
    *  @param string $id User id
    *
    *  @return array Group list
    */
   public function getGroupList($id)
   {
       global $cfg;

       $sql = "SELECT
                           *
               FROM
                           " . $cfg['sys']['tblPrefix'] . "_sys_user_group
               JOIN
                           " . $cfg['sys']['tblPrefix'] . "_sys_group
               USING(group_id)
               WHERE
                           user_id = '$id'
               ORDER BY
                           group_name ASC";

       $res = array();
       try {
           $this->_dbObj->query($sql);

           $res = $this->_dbObj->fetchAll();
       } catch (DbException $e) {
           Error::store('User', $e->getMessage());
       }

       return $res;
   }

   /**
    * Get list of group id
    *
    * @param int $id User id
    *
    * @return array List of group id
    */
   public function getGroupIdList($id)
   {
       global $cfg;

       $res    = array();

       $groups = $this->getGroupList($id);

       for ($i = 0; $i < sizeof($groups); $i++) {
           $res[$i] = $groups[$i]->group_id;
       }


       return $res;
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

    public function getDetail($id){
        
        global $cfg;

        $sql = "SELECT * FROM  " . $cfg['sys']['tblPrefix'] . "_sys_user WHERE user_id = '$id'";

        $res = null;

        try {
            $this->_dbObj->query($sql);

            $res = $this->_dbObj->fetch();
        } catch (DbException $e) { Error::store('User', $e->getMessage()); }

        return $res;
    }

    public function getMenuList($id) {
        
        global $cfg;

        $sql = "SELECT * FROM dp_sys_menu WHERE group_id = '$id'";
        $res = null;

        try {
            $this->_dbObj->query($sql);

            $res = $this->_dbObj->fetchAll();
        } catch (DbException $e) {
            Error::store('User', $e->getMessage());
        }

        return $res;
    }
}
