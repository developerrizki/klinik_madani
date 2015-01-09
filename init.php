<?php
/**
*Author   : Lorensius W. L. T                                           |
*Email    : lorenz@londatiga.net                                        |
*Homepage : http://www.londatiga.net                                    |
*/

if (phpversion() < 5) {
    echo 'Sorry, this application only runs in PHP 5 environment';
    exit;
}

date_default_timezone_set('Asia/Jakarta');

//Start output buffering
ob_start();

//start processing time
$timerStart = microtime();

//define constants
define('ROOT_DIR',      str_replace("/usr", "", dirname(__FILE__)));
define('ROOT_URL',      substr($_SERVER['PHP_SELF'], 0, - (strlen($_SERVER['SCRIPT_FILENAME']) - strlen(ROOT_DIR))));
define('SERVER_NAME',   $_SERVER['SERVER_NAME']);
define('FILE_NAME',     substr($_SERVER['PHP_SELF'], strrpos($_SERVER['PHP_SELF'], '/') + 1));
define('LIB_DIR',       ROOT_DIR . '/libraries');
define('CLASS_DIR',     LIB_DIR  . '/classes');
define('FUNCTION_DIR',  LIB_DIR  . '/functions');
define('SESS_DIR',      ROOT_DIR . '/_session');
define('USER_DIR',      ROOT_DIR . '/_uservar');
define('CONFIG_DIR',    ROOT_DIR . '/configs');
define('THEME_DIR',     ROOT_DIR . '/themes');

include_once(FUNCTION_DIR . '/lib.php');
// include_once(FUNCTION_DIR . '/lib.geocode.php');

include_once CONFIG_DIR . '/config_system.php';
include_once CONFIG_DIR . '/config_db.php';
include_once CONFIG_DIR . '/config_var.php';

include_once CLASS_DIR . '/XPM4/MAIL.php';

//class
include_once CLASS_DIR . '/blackcat/system/class.Registry.php';
include_once CLASS_DIR . '/blackcat/system/class.Loader.php';
include_once CLASS_DIR . '/blackcat/system/class.Error.php';
include_once CLASS_DIR . '/blackcat/system/class.System.php';
include_once CLASS_DIR . '/blackcat/database/class.DbConnection.php';
include_once CLASS_DIR . '/blackcat/transport/class.HTTP.php';
include_once CLASS_DIR . '/blackcat/transport/class.QueryString.php';
include_once CLASS_DIR . '/blackcat/controller/class.Dispatcher.php';
include_once CLASS_DIR . '/blackcat/controller/class.Controller.php';
include_once CLASS_DIR . '/blackcat/controller/class.Router.php';
include_once CLASS_DIR . '/blackcat/model/class.Model.php';
include_once CLASS_DIR . '/blackcat/view/class.View.php';
include_once CLASS_DIR . '/blackcat/system/class.Session.php';
include_once CLASS_DIR . '/blackcat/auth/class.Auth.php';
include_once CLASS_DIR . '/blackcat/user/class.User.php';
include_once CLASS_DIR . '/blackcat/user/class.Group.php';
include_once CLASS_DIR . '/blackcat/rbac/class.RBAC.php';
include_once CLASS_DIR . '/class.Agen.php';
include_once CLASS_DIR . '/class.RequestSignature.php';

include_once THEME_DIR . '/' . $cfg['sys']['theme'] .'/lib/class.Theme.php';

//other functions
include ROOT_DIR . '/functions/mkdir.php';
include ROOT_DIR . '/functions/rmdir.php';
include ROOT_DIR . '/functions/datename.php';
include ROOT_DIR . '/functions/scaleimage.php';

try {
    $dbObj      = DbConnection::getInstance('MySQL');
    
    $dbObj->setConnectionParameters($cfg['db']['host'],
                                    $cfg['db']['user'],
                                    $cfg['db']['password'],
                                    $cfg['db']['db'],
                                    '3306');
    $dbObj->connect();

    $sessObj    = new Session($cfg['sys']['sessionExpired'], $cfg['sys']['sessionLoginExpired']);

    $sessObj->init();

    $themeObj   = new Theme($cfg['sys']['theme']);
    $sysObj     = new System($dbObj, $sessObj);

    // $sysObj->enableDebug();

    Registry::set('db',         $dbObj);
    Registry::set('system',     $sysObj);
    Registry::set('theme',      $themeObj);
    Registry::set('session',    $sessObj);

    $userObj    = new User();
    $rbacObj    = new RBAC($dbObj);
    
    Registry::set('user',     $userObj);
    Registry::set('rbac',     $rbacObj);
    
    Router::add('/',                     array('controller' => 'User'));
    Router::add('/logout',               array('controller' => 'User', 'action' => 'logout'));


} catch (BlackCatException $e) { die ($e->getMessage()); }
