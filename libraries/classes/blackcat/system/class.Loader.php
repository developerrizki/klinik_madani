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
 * File handler class
 */
require_once CLASS_DIR . '/blackcat/io/class.File.php';

/**
 * SystemException class
 */
require_once CLASS_DIR . '/blackcat/system/class.SystemException.php';

/**
 * Loader class
 *
 * @package   System
 * @author    Lorensius W. L. T <lorenz@londatiga.net>
 * @version   1.0
 * @copyright Copyright (c) 2010 Lorensius W. L. T
 *
 */
final class Loader
{
    /**
     * Load a class from a file
     *
     * @param string $class Class name
     * @param string $dir Directory / path (optional)
     *
     * @throws SystemException If class can not be loaded
     *
     * @return void
     */
    static public function loadClass($class, $dir = '')
    {
        if (class_exists($class, false)) {
            return;
        }

        $separator = (!empty($dir)) ? ((strrpos($dir, '/') == strlen($dir) - 1) ? '' : '/') : '';
        $fileName  = $dir . $separator . "class.$class.php";

        try {
            $fileObj = new BCFile($fileName);

            $fileObj->load();
        } catch (IOException $e) {
            throw new SystemException("Could not load class <i>$class</i>!");
        }

        if (!class_exists($class, false)) 
            throw new SystemException("Class <i>$class</i> doesn't exist!");
    }
    
    /**
     * Load a lib file
     *
     * @param string $file File name
     *
     * @return void
     */
    static public function loadLib($file)
    {
        if (!file_exists($file)) return;
       
        try {
            $fileObj = new BCFile($file);

            $fileObj->load();
        } catch (IOException $e) {
            throw new SystemException("Could not load lib file <i>$file</i>!");
        }
    }
}