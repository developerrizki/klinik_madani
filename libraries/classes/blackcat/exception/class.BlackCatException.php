<?php
/**
 * BlackCat PHP 5 Framework
 *
 * Last updated: June 05, 2010, 11:16 PM
 *
 * @package   exception
 * @author    Lorensius W. L. T <lorenz@londatiga.net>
 * @version   1.0
 * @copyright Copyright (c) 2010 Lorensius W. L. T
 */


/**
 * Exception class
 *
 * @package    exception
 * @author     Lorensius W. L. T <lorenz@londatiga.net>
 * @version    1.0
 * @copyright  Copyright (c) 2010 Lorensius W. L. T
 *
 */
class BlackCatException extends Exception
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
        parent::__construct($message, $code);
    }
}