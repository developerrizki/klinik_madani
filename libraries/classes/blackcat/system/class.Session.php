<?php
/**
 * BlackCat PHP 5 Framework
 *
 * Last updated: June 05, 2010, 10:58 PM
 *
 * @package   system
 * @author    Lorensius W. L. T <lorenz@londatiga.net>
 * @version   1.0
 * @copyright Copyright (c) 2010 Lorensius W. L. T
 */

/**
 * SystemException class
 */
require_once CLASS_DIR . '/blackcat/system/class.SystemException.php';

/**
 * File class
 */
require_once CLASS_DIR . '/blackcat/io/class.File.php';


/**
 * Session management class
 *
 * @package   system
 * @author    Lorensius W. L. T <lorenz@londatiga.net>
 * @version   1.0
 * @copyright Copyright (c) 2010 Lorensius W. L. T
 *
 */
class Session
{
    /**
     * Session data, used to store system variables
     *
     * @var string
     */
    private $_session = array();

    /**
     * Session data, used to store user variables
     *
     * @var string
     */
    private $_userVar = array();

    /**
     * User id
     *
     * @var string
     */
    private $_userID;

    /**
     * User name
     *
     * @var string
     */
    private $_userName;

    /**
     * Session id
     *
     * @var string
     */
    private $_sessionID;

    /**
     * Session key
     *
     * @var string
     */
    private $_sessionKey;

    /** Maximum time (in minutes) of inactivity time before user have to re-login
     *
     * @var string
     */
    private $_sessionLoginExpired;

    /**
     * Maximum time (in hours) of inactivity time before the system destroy the session and creates a new one
     *
     * @var string
     */
    private $_sessionExpired;

    /**
     * Flag to indicate initialization is done 
     *
     * @var bool
     */
    private $_initDone = false;


    /**
     * Constructor.
     * Create an instance of this class

     * @param int $sessionExpired Maximum time of inactivity time before the system destroy the session
                  and creates a new one
     * @param int $sessionLoginExpired Maximum time (in minutes) of inactivity time before user have to re-login
     *
     * @return void
     */
    public function __construct($sessionExpired, $sessionLoginExpired)
    {
        $this->_sessionExpired      = $sessionExpired;
        $this->_sessionLoginExpired = $sessionLoginExpired;
    }

    /**
     * Initializate session
     *
     * @throws SystemException If error occured
     *
     * @return void
     */
    public function init()
    {
        if (!empty($this->_sessionID)) return;

        try {
            session_start();

            $this->_sessionID = md5(session_id());
            $sess             = $this->readData();

            if ($sess) { //old session (last saved data)
                $delay = time() - $sess['time'];
                if ($delay > $this->_sessionExpired * 3600) {
                    try {
                        $fileObj = new BCFile(SESS_DIR . '/sess_' . $this->_sessionID . '.php');
                        $fileObj->delete();
                    } catch (IOException $e) {
                        throw new SystemException('Can not delete expired session data!');
                    }
                }

                if ($delay > $this->_sessionLoginExpired * 60) {
                    $this->_userID   = '';
                    $this->_userName = '';
                    
                    $this->setVar('loginMsg', 'Session expired, please login to continue;');
                } else {
                    $this->_userID   = stripslashes($sess['userid']);
                    $this->_userName = stripslashes($sess['username']);
                    $this->_userVar  = $this->readUserData();
                }

                $this->_session      = unserialize(stripslashes($sess["data"]));
            } else { //new session
                if ($_COOKIE['logout']) {
                    setcookie('logout', '', 0, ROOT_URL . '/');
                }

                $this->_userID         = '';
                $this->_userName       = '';
                $this->_session        = array();
            }

            $this->writeData();
            $this->writeUserData();

            $this->_initDone           = true;
        } catch (SystemException $e) {
            throw $e;
        }
    }

    /**
     * Get User ID
     *
     * @return string User ID
     */
    public function getUserID()
    {
        return $this->_userID;
    }

    /**
     * Get User name
     *
     * @return string User name
     */
    public function getUserName()
    {
        return $this->_userName;
    }

    /**
     * Get Session ID
     *
     * @return string Session ID
     */
    public function getSessionID()
    {
        return $this->_sessionID;
    }

    /**
     * Get session data
     *
     * @param string $var Session variable
     *
     * @return mixed Session data
     */
    public function getVar($var)
    {
        return (isset($this->_session[$var])) ? $this->_session[$var] : '';
    }

    /**
     * Set session data
     *
     * @param string $var Session variable
     * @param mixed $val Session data
     *
     * @return void
     */
    public function setVar($var, $val)
    {
        $this->_session[$var] = $val;
    }

    /**
     * Update session data
     *
     * @throws SystemException If error occured
     *
     * @return void
     */
    public function updateVar()
    {
        try {
            $this->writeData();
            $this->writeUserData();
        } catch (SystemException $e) {
            throw $e;
        }
    }

    /**
     * Log user into session
     *
     * @param string $userID User ID
     * @param string $userName User name
     *
     * @return void
     */
    public function login($userID, $userName)
    {
        try {
            $this->_userID   = $userID;
            $this->_userName = $userName;

            $this->writeData();

            $this->_userVar  = $this->readUserData();

            $this->writeUserData();
        } catch (SystemException $e) {
            throw $e;
        }
    }

    /**
     * Logout user
     *
     * @throws SystemException If error occured
     *
     * @return void
     */
    public function logout()
    {
        try {
            $fileObj     = new BCFile(SESS_DIR . '/sess_' . $this->_sessionID . '.php');
            $fileObj->delete();

            $this->_userVar = '';
            $this->writeUserData();
        } catch (IOException $ie) {
            throw new SystemException('Can not delete session file!');
        } catch (SystemException $se) {
            throw $se;
        }

        $this->_userID   = '';
        $this->_userName = '';

        session_destroy();

        setcookie('logout', '1', 0, ROOT_URL . '/');
        setcookie('PHPSESSID', '', 0, ROOT_URL . '/');
    }

    /**
     * Set user variable
     *
     * @param string $var Variable name
     * @param mixed $val Variable value
     *
     * @return void
     */
    public function setUserVar($var, $val)
    {
        $this->_userVar[$var] = $val ;
    }

    /**
     * Get user variable value
     *
     * @param string $var Variable name
     *
     * @return mixed Variable value
     */
    public function getUserVar($var)
    {
        return  (isset($this->_userVar[$var])) ? $this->_userVar[$var] : '';
    }

    /**
     * Write session data into session file
     *
     * @throws SystemException If session file could not be written
     *
     * @return void
     */
    private function writeData()
    {
        $sessionFile   = SESS_DIR . '/sess_' . $this->_sessionID . '.php';
        $currTimestmp  = time();
        $data          = "# Session File\r\n"
                       . "\$sd[\"time\"]     = $currTimestmp;\r\n"
                       . "\$sd[\"userid\"]   = \"" . addslashes($this->_userID) . "\";\r\n"
                       . "\$sd[\"username\"] = \"" . addslashes($this->_userName) . "\";\r\n\r\n"
                       . "# Session data\r\n";

        if (is_array($this->_session) && sizeof($this->_session)) {
            foreach ($this->_session as $key => $val) {
                if ($val === '') {
                    unset($this->_session[$key]);
                }
            }

            if (sizeof($this->_session)) {
                $data .= "\$sd[\"data\"]     =\"" . addslashes(serialize($this->_session)) . "\";\r\n";
            }
        } else {
            $data .= "\$sd[\"data\"]     =\"" . serialize(array()) . "\";\r\n";
        }

        $data = "<?php\r\n $data \r\n";

        try {
            $fileObj = new BCFile($sessionFile);

            $fileObj->write($data);
        } catch (IOException $e) {
            throw new SystemException('Can not write session file!');
        }
    }

    /**
     * Write user's data into session file
     *
     * @throws SystemException If user's session file could not be written
     *
     * @return void
     */
    private function writeUserData()
    {
        if ($this->_userID) {
            if (is_array($this->_userVar) && sizeof($this->_userVar)) {
                foreach ($this->_userVar as $key => $val) {
                    if ($val === '') {
                        unset($this->_userVar[$key]);
                    }
                }

                if (sizeof($this->_userVar)) {
                    $data .= "\$ud = \"" . addslashes(serialize($this->_userVar)) . "\";\r\n";
                }
            } else {
                $data = "\$ud = \"" . serialize(array()) . "\";\r\n";
            }

            $data = "<?php\r\n$data\r\n";

            try {
                $fileObj = new BCFile(USER_DIR . '/user_' . md5($this->_userID) . '.php');

                $fileObj->write($data);
            } catch (IOException $e) {
                throw new SystemException('Can not write user\'s session file!');
            }
        }
    }

    /**
     * Read session data
     *
     * @throws SystemException If session file could not be read
     *
     * @return array Session data
     */
    private function readData()
    {
        $fileName = SESS_DIR . '/sess_' . $this->_sessionID . '.php';
        $fileObj  = new BCFile($fileName);

        if ($fileObj->exists()) {
            if ($fileObj->isReadable()) {
                include_once($fileName);

                return $sd;
            } else {
                throw new SystemException('Can not read session file!');
            }
        }

        return array();
    }

    /**
     * Read user data from file
     *
     * @throws SystemException If user's session file could not be read
     *
     * @return array User data
     */
    private function readUserData()
    {
        if ($this->_userID) {
            $fileName = USER_DIR . '/user_' . md5($this->_userID) . '.php';
            $fileObj  = new BCFile($fileName);

            if ($fileObj->exists) {
                if ($fileObj->isReadable()) {
                    include_once($fileName);

                    return unserialize(stripslashes($ud));
                } else {
                    throw new SystemException('Can not read user\'s session file!');
                }
            }
       }

       return array();
   }
}