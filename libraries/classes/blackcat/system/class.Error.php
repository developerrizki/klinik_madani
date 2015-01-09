<?php
/**
 * BlackCat PHP 5 Framework
 *
 * Last updated: June 05, 2010, 10:56 PM
 *
 * @package   system
 * @author    Lorensius W. L. T <lorenz@londatiga.net>
 * @version   1.0
 * @copyright Copyright (c) 2010 Lorensius W. L. T
 */

/**
 * Error store class.
 * Save all error messages registered in system
 *
 * @package   system
 * @author    Lorensius W. L. T <lorenz@londatiga.net>
 * @version   1.0
 * @copyright Copyright (c) 2010 Lorensius W. L. T
 *
 */
final class Error
{
    /**
     * System error store
     *
     * @var array
     */
    static private $_error = array();


    /**
     * Save error message
     *
     * @param string $key Error key 
     * @param string $error Error message
     *
     * @return void
     */
    static public function store($key, $error)
    {
        self::$_error[$key][] = $error;
    }

    /**
     * Get error messages for a key
     *
     * @param string $key Error key
     * 
     * @return string Last error message
     */
    static public function get($key)
    {
        return (!empty(self::$_error[$key])) ? self::$_error[$key] : '';
    }

    /**
     * Get all error messages
     *
     * @return array All error messages
     */
    static public function getAll()
    {
        return self::$_error;
    }

    /**
     * Empty error store
     *
     * @return void
     */
    static public function emptyStore()
    {
        reset(self::$_error);
    }
}