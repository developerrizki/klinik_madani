<?php
/**
 * BlackCat PHP 5 Framework
 *
 * Last updated: June 04, 2010, 08:05 AM
 *
 * @package   auth
 * @author    Lorensius W. L. T <lorenz@londatiga.net>
 * @version   2.0
 * @copyright Copyright (c) 2010 Lorensius W. L. T
 */

/**
 * BlackCatException class
 */
require_once CLASS_DIR . '/blackcat/exception/class.BlackCatException.php';

/**
 * Authentication exception class.
 *
 * @package   Auth
 * @author    Lorensius W. L. T <lorenz@londatiga.net>
 * @version   1.0
 * @copyright Copyright (c) 2010 Lorensius W. L. T
 *
 */
class AuthException extends BlackCatException
{
    /**
     * Constructor.
     * Create new instance of this class
     *
     * @param string $message Error message
     * @param int $code Error code (optional)
     *
     * @return void
     */
    public function __construct($message, $code = 0)
    {
        $message = "Auth [$code]: $message";

        parent::__construct($message, $code);
    }
}