<?php
/**
 * BlackCat PHP 5 Framework
 *
 * Last updated: June 03, 2010, 11:29 PM
 *
 * @package   database
 * @author    Lorensius W. L. T <lorenz@londatiga.net>
 * @version   1.0
 * @copyright Copyright (c) 2010 Lorensius W. L. T
 */

/**
 * BlackCatException class
 */
require_once CLASS_DIR . '/blackcat/exception/class.BlackCatException.php';


/**
 * Database exception class
 *
 * @package   database
 * @author    Lorensius W. L. T <lorenz@londatiga.net>
 * @version   1.0
 * @copyright Copyright (c) 2010 Lorensius W. L. T
 *
 */
class DbException extends BlackCatException
{
    /**
     * Constructor.
     * Create new instance of this class
     *
     * @param string $message Error message
     * @param int $code Error code
     *
     * @return void
     */
    public function __construct($message, $code = 0)
    {
        $message = "Database [$code]: $message";

        parent::__construct($message, $code);
    }
}