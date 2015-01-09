<?php
/**
 * BlackCat PHP 5 Framework
 *
 * Last updated: June 07, 2010, 01:08 AM
 *
 * @package   controller
 * @author    Lorensius W. L. T <lorenz@londatiga.net>
 * @version   1.0
 * @copyright Copyright (c) 2010 Lorensius W. L. T
 */


/**
 * Router class
 *
 * @package   controller
 * @author    Lorensius W. L. T <lorenz@londatiga.net>
 * @version   1.0
 * @copyright Copyright (c) 2010 Lorensius W. L. T
 *
 */
final class Router
{
    /**
     * Routes
     *
     * @var array
     */
    static private $_routes = array();
    
    /**
     * Add route
     *
     * @param string $path Route path
     * @param array $controller Controller handler
     *
     * @return void
     */
    static public function add($path, $controller)
    {
        self::$_routes[$path] = $controller;
    }
    
    /**
     * Get route
     *
     * @param string $path ROute path
     *
     * @return array Controller handler
     */
    static public function get($path)
    {
        return (array_key_exists($path, self::$_routes)) ? self::$_routes[$path] : NULL;
    }
}
?>