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
 * Registry class, store shared object.
 * Inspired by Registry class in Zend framework
 *
 * @package   System
 * @author    Lorensius W. L. T <lorenz@londatiga.net>
 * @version   1.0
 * @copyright Copyright (c) 2005-2008 Lorensius W. L. T
 *
 */
final class Registry
{
    /**
     * Registry for shared object
     *
     * @var array
     */
    static private $_registry = array();


    /**
     * Constructor
     */
    private function __construct()
    {
    }

    /**
     * Set registry value
     *
     * @param string $name Object name
     * @param object $object Object
     *
     * @throws SystemException If problem occured
     *
     * @return void
     */
    static public function set($name, $object)
    {
        if (!is_object($object)) 
            throw new SystemException("<i>$name</i> is not an object!");
        
        self::$_registry[$name] = $object;
    }

    /**
     * Get object from registry
     *
     * @param string $name Object's name
     *
     * @return object Object if exists otherwise NULL if object doesn't exist
     */
    static public function get($name)
    {
        return (array_key_exists($name, self::$_registry)) ? self::$_registry[$name] : NULL;
    }

    /**
     * Check if an object with specified name already exist in the registry
     *
     * @param string $name Object's name
     *
     * @return bool TRUE if exists or FALSE otherwise
     */
    static public function exists($name)
    {
        return (array_key_exists($name, self::$_registry));
    }
}