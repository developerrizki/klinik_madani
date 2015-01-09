<?php
/**
 * BlackCat PHP 5 Framework
 *
 * Last updated: June 05, 2010, 11:16 PM
 *
 * @package   io
 * @author    Lorensius W. L. T <lorenz@londatiga.net>
 * @version   1.0
 * @copyright Copyright (c) 2010 Lorensius W. L. T
 */

/**
 * BlackCatException
 */
require_once CLASS_DIR . '/blackcat/exception/class.BlackCatException.php';

/**
 * Input output exception class
 *
 * @package   IO
 * @author    Lorensius W. L. T <lorenz@londatiga.net>
 * @version   1.0
 * @copyright Copyright (c) 2010 Lorensius W. L. T
 *
 */
class IOException extends BlackCatException
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
        $message = "IO [$code]: $message";

        parent::__construct($message, $code);
    }
}