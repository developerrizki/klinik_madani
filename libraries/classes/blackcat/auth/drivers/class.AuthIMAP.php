<?php
/**
 * BlackCat PHP 5 Framework
 *
 * Last updated: June 05, 2010, 10:31 PM
 *
 * @package     auth
 * @subpackage  drivers
 * @author      Lorensius W. L. T <lorenz@londatiga.net>
 * @version     1.0
 * @copyright   Copyright (c) 2010 Lorensius W. L. T
 */


/**
 * IMAP authentication driver class
 *
 * @package    auth
 * @subpackage drivers
 * @author     Lorensius W. L. T <lorenz@londatiga.net>
 * @version    1.0
 * @copyright  Copyright (c) 2010 Lorensius W. L. T
 *
 */
class AuthIMAP
{
    /**
     * IMAP Server host name or ip address
     *
     * @var string
     */
    private $_imapServer;

    /**
     * IMAP folder
     *
     * @var string
     */
    private $_imapFolder;

    /**
     * IMAP Port
     *
     * @var string
     */
    private $_imapPort;


    /**
     * Constructor.
     * Create a new instance of this class
     *
     * @param string $imapServer IMAP server hostname or ip address
     * @param string $imapFolder IMAP folder (eg INBOX)
     * @param string $imapPort   IMAP port
     *
     * @return void
     */
    public function __construct($imapServer = '', $imapFolder = '', $imapPort = '')
    {
        $this->_imapServer = $imapServer;
        $this->_imapFolder = $imapFolder;
        $this->_imapPort   = $imapPort;
    }

    /**
     * Set IMAP parameters
     *
     * @param string $imapServer IMAP server hostname or ip address
     * @param string $imapFolder IMAP folder (eg INBOX)
     * @param string $imapPort   IMAP port
     *
     * @return void
     */
    public function setParameters($imapServer, $imapFolder, $imapPort)
    {
        $this->_imapServer = $imapServer;
        $this->_imapFolder = $imapFolder;
        $this->_imapPort   = $imapPort;
    }

    /**
     * Verify user id and password
     *
     * @param string $userID User ID
     * @param string $userPassword User password
     *
     * @return bool TRUE on success or FALSE otherwise
     */
    public function verify($userID, $userPassword)
    {
        $server = (empty($this->_imapServer)) ? ($_SERVER["REMOTE_ADDR"] OR getenv("REMOTE_ADDR")) : $this->_imapServer;
        $folder = (empty($this->_imapFolder)) ? 'INBOX' : $this->_imapFolder;
        $port   = (empty($this->_imapPort)) ? '143' : $this->_imapPort;

        $connstr = '{' . $server . ':' . $port . '}' .imap_utf7_encode($folder);
        $stream  = @imap_open($connstr, $userID, $userPassword);

        if ($stream) {
            imap_close($stream);
            return true;
        } else {
            return false;
        }
    }
}