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
 * System exception class
 */
require_once CLASS_DIR . '/blackcat/system/class.SystemException.php';

/**
 * Template class
 */
require_once CLASS_DIR . '/blackcat/view/class.View.php';

/**
  * HTTP class
  */
require_once CLASS_DIR . '/blackcat/transport/class.HTTP.php';


/**
 * System class
 *
 * @package   system
 * @author    Lorensius W. L. T <lorenz@londatiga.net>
 * @version   1.0
 * @copyright Copyright (c) 2010 Lorensius W. L. T
 *
 */
class System
{
    /**
     * Database connection object reference
     *
     * @var object
     */
    private $_dbObj = null;

    /**
     * Session object reference
     *
     * @var object
     */
    private $_sessObj = null;

    /**
     * Debug mode
     *
     * @var boolean
     */
    private $_debug = false;


    /**
     * Constructor.
     * Create an instance of this class
     *
     * @param object $dbObj Database connection object
     * @param object $sessObj Session object
     *
     * @return void
     */
    public function __construct(&$dbObj, &$sessObj)
    {
        $this->_dbObj   = &$dbObj;
        $this->_sessObj = &$sessObj;
    }
    
    /**
     * Enable/disable debugging mode
     *
     * @param bool $debug TRUE if debugging mode is enabled and vice versa
     *
     * @return void
     */
    public function enableDebug($debug = true)
    {
        $this->_debug = $debug;
    }

    /**
     * Check is user is logged in, if not redirect to login page
     *
     * @return void
     */
    public function requireLogin()
    { 
        if (!$this->_sessObj->getUserID()) {
            if (!empty($_SERVER['HTTP_REFERER']))  
                $this->_sessObj->setVar('loginMsg', 'Please login to continue!');
                
            HTTP::redirect(ROOT_URL . '/login');
        }
    }
    
    /**
     * Display debugging results
     *
     * @return void
     */
    private function debug()
    {
        $html  = '';
        $debug = $this->_sessObj->getVar('debug');
        $style = 'border:solid 1px #ff0000;background:#ff0000;padding-top:5px;font:11px verdana, helvetica, arial, sans-serif;';
        
        if (is_array($debug) && sizeof($debug)) {
            $err    = '';
            for ($i = 0; $i < sizeof($debug); $i++) {
                 $err .= ($debug[$i]) ? "--> $debug[$i]<br>" : '';
            }

            if ($err) {
                $html = "\r\n<div style='$style' id='debug'>\r\n"
                      . "   <span style='color:#fff;font-weight:bold'>- DEBUGGING RESULTS -</span>\r\n"
                      . "   <div style='margin-top:5px;padding:5px;background:#fff'>\r\n"
                      . "   $err\r\n"
                      . "   </div>\r\n"
                      . "</div>\r\n";
            }
        }

        return $html;
    }
    
    /**
     * Finalize ahole system
     *
     * @param bool $clear TRUE if all errors registered in system error store will be cleared and vice versa
     *
     * @return void
     */
    public function finalize($clear = true)
    {
        global $cfg;

        $themeObj = Registry::get('theme');
        $html     = $themeObj->toString();
        $strrplc  = '';
        
        if ($this->_debug === true) {
            $error   = Error::getAll();
            $serror  = $this->_sessObj->getVar('debug'); 
                
            $serror  = (!is_array($serror)) ? array() : $serror; 
            $error   = array_merge($error, $serror);
                
            // $this->_sessObj->setVar('debug', $error);
                    
            $strrplc = $this->debug(); 
        }
            
        $timerStart = explode(' ' , $GLOBALS['timerStart']);
        $timerStart = $timerStart[1] + $timerStart[0];
        $timerEnd   = microtime();
        $timerEnd   = explode(' ', $timerEnd);
        $timerEnd   = $timerEnd[1] + $timerEnd[0];
        $ptime      = '<!-- Page generation time '  .(round(($timerEnd - $timerStart),2)) . ' seconds //-->';
        $html       = str_replace('[DEBUG]', $strrplc, $html);
        $html       = str_replace('[META]', $ptime, $html);
        $html       = (!$html) ? $this->debug() : $html;
        
        echo $html;

        $this->_dbObj->close();

        if ($clear) $this->_sessObj->setVar('debug', '');
        
        $this->_sessObj->updateVar();
        
        exit;
    }
}