<?php
/**
 * BlackCat PHP 5 Framework
 *
 * Last updated: June 04, 2010, 08:10 AM
 *
 * @package     auth
 * @subpackage  drivers
 * @author      Lorensius W. L. T <lorenz@londatiga.net>
 * @version     1.0
 * @copyright   Copyright (c) 2010 Lorensius W. L. T
 */

/**
 * Registry class
 */
require_once CLASS_DIR . '/blackcat/system/class.Registry.php';

/**
 * AuthException class
 */
require_once CLASS_DIR . '/blackcat/auth/class.AuthException.php';

/**
 * DAALException class
 */
require_once CLASS_DIR . '/blackcat/database/class.DbException.php';

/**
 * Database authentication driver class.
 *
 * @package    auth
 * @subpackage drivers
 * @author     Lorensius W. L. T <lorenz@londatiga.net>
 * @version    1.0
 * @copyright  Copyright (c) 2010 Lorensius W. L. T
 *
 */
class AuthDB
{
    /**
     * Database connection object
     *
     * @var object
     */
    private $_dbObj = null;

    /**
     * Password hashing method
     *
     * @var string
     */
    private $_hashType;


    /**
     * Constructor.
     * Create a new instance of this class
     *
     * @return void
     */
    public function __construct()
    {

        if (Registry::exists('db')) $this->_dbObj = Registry::get('db');
    }

    /**
     * Set database connection object
     *
     * @param object $dbObj Database connection object
     *
     * @throws AuthException If database connection assigned is not an object or null
     *
     * @return void
     */
    public function setConnection(&$dbObj)
    {
        $this->_dbObj = &$dbObj;

        if (!is_object($this->_dbObj)) {
            throw new AuthException('Database connection assigned is not an object or null');
        }
    }

    /**
     * Verify user id and password
     *
     * @param string $user User id
     * @param string $password password
     *
     * @throws AuthException If database connection is null or not an object
     *
     * @return bool TRUE on success or FALSE otherwise
     */
    public function verify($usermail, $password)
    {
        global $cfg;

        $usermail   = addslashes(preg_replace("/(-+)/", "", $usermail));
        $password   = addslashes(preg_replace("/(-+)/", "", $password));

        $sql        =  "SELECT
                                *
                        FROM
                                " . $cfg['sys']['tblPrefix'] . "_sys_user
                        WHERE
                                user_email = '$usermail'";

        $result     = false;

        try {
            $this->_dbObj->execute($sql);

            if ($this->_dbObj->getNumRows() != 0) {
                $user   = $this->_dbObj->fetch();

                $result = ($this->passSalt($password) == $user->user_password) ? true : false;

            }
        } catch (DbException $e) {
            throw new AuthException($e->getMessage());
        }

        return $result;
    }

     private function passSalt($pass)
    {
        // if ($salt === null)
        //     $salt = substr(md5(uniqid(rand(), true)), 0, 9);
        // else
        //     $salt = substr($salt, 0, 9);

        // return $salt . sha1($pass . $salt);

        return md5($pass);
    }

}