<?php
/**
 * BlackCat PHP 5 Framework
 *
 * Last updated: June 05, 2010, 11:40 PM
 *
 * @package   controller
 * @author    Lorensius W. L. T <lorenz@londatiga.net>
 * @version   1.0
 * @copyright Copyright (c) 2010 Lorensius W. L. T
 */

/**
 * Loader class
 */
require_once(CLASS_DIR . '/blackcat/system/class.Loader.php');

/**
 * Loader class
 */
require_once(CLASS_DIR . '/blackcat/view/class.View.php');

/**
 * SystemException class
 */
require_once(CLASS_DIR . '/blackcat/system/class.SystemException.php');

/**
 * Dispatcher class
 *
 * @package   controller
 * @author    Lorensius W. L. T <lorenz@londatiga.net>
 * @version   1.0
 * @copyright Copyright (c) 2010 Lorensius W. L. T
 *
 */
class Dispatcher
{
    /**
     * Controller
     *
     * @var string
     */
    private $_controller;

    /**
     * URL Parameters
     *
     * @var array
     */
    private $_params;

    /**
     * Action
     *
     * @var string
     */
    private $_action;


    /**
     * Dispatch url
     *
     * @return void
     */
    public function dispatch() {
        $url = $_GET['url'];

        $this->_parseURL($url);

        if (empty($this->_controller)) $this->_controller = 'Home';

        $name              = $this->_controller;
        $this->_controller = $this->_controller . 'Controller';

        try {
            Loader::loadClass(ucfirst($this->_controller) , ROOT_DIR . '/controllers');

            if (class_exists($this->_controller)) {
                $controllerObj  = new $this->_controller;

                $controllerObj->setParams($this->_params);
                $controllerObj->setName(strtolower($name));

                if (method_exists($controllerObj, $this->_action)) {
                    $action = $this->_action;
                    $out    = $controllerObj->$action();
                } else {
                    $out    = $controllerObj->index();
                }
            } else {
                $this->pageNotFound();

                Error::store('Controller', 'Controller class <i>' . $this->_controller . '</i> not found!');
            }

            echo $out;
        } catch (SystemException $e) {
            $this->pageNotFound();

            Error::store('Controller', 'Controller file <i>' . $this->_controller . '</i> not found!');
        }
    }

    /**
     * Parse URL
     *
     * @return void
     */
    private function _parseURL($url)
    {
        $route = Router::get('/' . $url);

        if ($route) {
            if (!empty($route['controller'])) $this->_controller = $route['controller'];

            if (!empty($route['action'])) $this->_action = $route['action'];

            if (!empty($route['params'])) $this->_params = $route['params'];
        } else {
            if (strpos($url, "/") === FALSE) {
                $this->_controller  = $url;
            } else {
                $urls               = explode("/", $url);

                $this->_controller  = strtolower($urls[0]);
                $this->_action      = '';
                $this->_params      = array();

                if (sizeof($urls) > 1) $this->_action = $urls[1];

                if (sizeof($urls) > 2) {

                    for ($i = 2; $i < sizeof($urls); $i++) {
                        $this->_params[] = $urls[$i];
                    }
                }
            }
        }
    }

    /**
     * Show page not found
     *
     * @return void
     */
    private function pageNotFound()
    {
        global $cfg;

        $view = new View();

        $view->setPath(THEME_DIR . '/' . $cfg['sys']['theme'] . '/templates');
        $view->setTemplate('page_not_found');
        $view->parse();

        $view->render();
    }
}

?>