<?php
/**
 * BlackCat PHP 5 Framework
 *
 * Last updated: June 05, 2010, 11:13 PM
 *
 * @package   system
 * @author    Lorensius W. L. T <lorenz@londatiga.net>
 * @version   1.0
 * @copyright Copyright (c) 2010 Lorensius W. L. T
 */

/**
 * BlackCatException class
 */
require_once(CLASS_DIR . '/blackcat/exception/class.BlackCatException.php');


/**
 * System exception class
 *
 * @package   system
 * @author    Lorensius W. L. T <lorenz@londatiga.net>
 * @version   1.0
 * @copyright Copyright (c) 2010 Lorensius W. L. T
 *
 */
class SystemException extends BlackCatException
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
        $message = "System [$code]: $message";

        parent::__construct($message, $code);
    }
}