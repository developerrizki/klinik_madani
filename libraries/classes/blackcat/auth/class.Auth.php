<?php
/**
 * BlackCat PHP 5 Framework
 *
 * Last updated: June 04, 2010, 08:07 AM
 *
 * @package   database
 * @author    Lorensius W. L. T <lorenz@londatiga.net>
 * @version   2.0
 * @copyright Copyright (c) 2010 Lorensius W. L. T
 */

/**
 * Loader class
 */
require_once CLASS_DIR . '/blackcat/system/class.Loader.php';

/**
 * IOException class
 */
require_once CLASS_DIR . '/blackcat/system/class.SystemException.php';

/**
 * AuthException class
 */
require_once CLASS_DIR . '/blackcat/auth/class.AuthException.php';


/**
 * User authentication and verification class.
 *
 * Currently supported authentication methods are:
 * - Database
 * - IMAP
 *
 * @package    auth
 * @author     Lorensius W. L. T <lorenz@londatiga.net>
 * @version    2.0
 * @copyright  Copyright (c) 2010 Lorensius W. L. T
 */
class Auth
{
    /**
     * Get authentication instance
     *
     * @param string $type Authentication type
     *
     * @throws AuthException If driver does not exists
     *
     * @return void
     */
    static public function getInstance($type)
    {
        try {
            $atype = 'Auth' . strtoupper($type);

            Loader::loadClass($atype, CLASS_DIR . '/blackcat/auth/drivers');

            return new $atype;
        } catch (IOException $e) {
            throw new AuthException("Can not load authentication <i>$atype</i> driver!", 1);
        }

        return null;
    }
}