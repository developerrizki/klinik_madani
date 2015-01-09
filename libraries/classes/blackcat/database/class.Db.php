<?php
/**
 * BlackCat PHP 5 Framework
 *
 * Last updated: June 03, 2010, 11:20 PM
 *
 * @package   database
 * @author    Lorensius W. L. T <lorenz@londatiga.net>
 * @version   1.0
 * @copyright Copyright (c) 2010 Lorensius W. L. T
 */


/**
 * Database abstraction class
 *
 * This is an absract class for handling operations with database server.
 *
 * Currently supported database:
 * - Mysql
 * - PostgreSQL
 *
 * @package   database
 * @author    Lorensius W. L. T <lorenz@londatiga.net>
 * @version   1.0
 * @copyright Copyright (c) 2010 Lorensius W. L. T
 *
 */
abstract class Db
{

    /**
     * Database host name or ip address
     *
     * @var string
     */
    protected $_dbHost;

    /**
     * Database server TCP/IP port
     *
     * @var string
     */
    protected $_dbPort;

    /**
     * Database user name
     *
     * @var string
     */
    protected $_dbUser;

    /**
     * Database user's password
     *
     * @var string
     */
    protected $_dbPassword;

    /**
     * Database name
     *
     * @var string
     */
    protected $_dbName;

    /**
     * Connection resource
     *
     * @var mixed
     */
    protected $_connRes = null;

    /**
     * Query resource
     *
     * @var mixed
     */
    protected $_queryRes = null;

    /**
     * Query session name
     *
     * @var string
     */
    protected $_querySess;

    /**
     * Last inserted id for auto increament field
     *
     * @var int
     */
    protected $_lastInsertID = 0;

    /**
     * Debug mode, TRUE if debug is enabled and FALSE if disabled
     *
     * @var bool
     */
    protected $_debug = false;

    /**
     * Error message
     *
     * @var string
     */
    protected $_errorMsg;


    /**
     * Constructor.
     * Create a new instance of this class
     *
     * @param string $dbHost Database server hostname or ip address (optional)
     * @param string $dbName Database name (optional)
     * @param string $dbUser Database  user (optional)
     * @param string $dbPassword Database user's password (optional)
     * @param string $dbPort Database server port (optional)
     *
     * @return void
     */
    public function __construct($dbHost='', $dbUser='', $dbPassword='', $dbName='', $dbPort='')
    {
        $this->_dbHost     = $dbHost;
        $this->_dbUser     = $dbUser;
        $this->_dbPassword = $dbPassword;
        $this->_dbName     = $dbName;
        $this->_dbPort     = $dbPort;
    }

    /**
     * Set database connection parameters
     *
     * @param string $dbHost Database server hostname or ip address
     * @param string $dbName Database name
     * @param string $dbUser Database user
     * @param string $dbPassword Database user's password
     * @param string $dbPort Database server port
     *
     * @return void
     */
    public function setConnectionParameters($dbHost, $dbUser, $dbPassword, $dbName, $dbPort)
    {
        $this->_dbHost     = $dbHost;
        $this->_dbUser     = $dbUser;
        $this->_dbPassword = $dbPassword;
        $this->_dbName     = $dbName;
        $this->_dbPort     = $dbPort;
    }

    /**
     * Disable / enable sql debug
     *
     * @param bool $bool TRUE if enabled or FALSE otherwise
     *
     * @return void
     */
    public function enableDebug($bool = true)
    {
        $this->_debug = $bool;
    }

    /**
     * Establish connection with database server
     *
     * @return void
     */
    abstract public function connect();

    /**
     * Close connection with database server
     *
     * @return void
     */
    abstract public function disconnect();

    /**
     * Execute a query to database server
     *
     * @param string $sql SQL statement
     *
     * @return void
     */
    abstract public function execute($sql);

    /**
     * Get a row from query result
     *
     * @return array One row from query result
     */
    abstract public function getRow();

    /**
     * Get query result
     *
     * @return array All rows from query results
     */
    abstract public function getAll();

    /**
     * Select query session
     *
     * @param string $querySess Query session name
     *
     * @return void
     */
    public function selectQuerySession($querySess)
    {
        $this->_querySess = $querySess;
    }

    /**
     * Get next id for an integer type field in a table
     *
     * @param string $tableName Table name
     * @param string $idField Table field where next id to be inserted
     *
     * @return int Next id
     */
    abstract public function getNextID($tableName, $idField);

    /**
     * Get last inserted id for an integer type field in a table
     *
     * @return int Last inserted id
     */
    public function getLastInsertID()
    {
        return $this->_lastInsertID;
    }

    /**
     * Begin transaction
     *
     * @return void
     */
    public function beginTrans()
    {
    }

    /**
     * Commit transaction
     *
     * @return void
     */
    public function commitTrans()
    {
    }

    /**
     * Rollback transaction
     *
     * @return void
     */
    public function rollbackTrans()
    {
    }

    /**
     * Get number of rows from query result
     *
     * @return int Number of rows
     */
    abstract public function getNumRows();

    /**
     * Get number of fields of a table
     *
     * @return int Number of field
     */
    abstract public function getNumFields();

    /**
     * Get field name
     *
     * @param string $col Field offset
     *
     * @return string Field name
     */
    abstract public function getFieldName($col);

    /**
     * Insert record(s) into a table
     *
     * @param string $tableName Table name
     * @param array $record Value to be updated, with array format:
     *                      field name as key and field value as value
     *                      ex: array("ID = 1","Name = Lorenz"), etc
     *
     * @return void
     */
    abstract public function insertRecord($tableName, $record);

    /**
     * Update record(s) in a table
     *
     * @param string $tableName Table name
     * @param array $record Value to be updated, with format: array(dbfield = 'value')
     *                      ex: array(user_id = '$user', user_name = '$name')
     * @param array $where Where clause, with format: array(dbfield = 'value')
     *                      ex: array(user_id = '$user', user_name = '$name')
     *
     * @return void
     */

    abstract public function updateRecord($tableName, $record, $where = '');

    /**
     * Delete record (s) from a table
     *
     * @param string $tableName Table name
     * @param array $where Where clause, with format: array(dbfield = 'value')
     *                     ex: array(user_id = '$user', user_name = '$name')
     *
     * @return mixed Query result
     */
    abstract public function deleteRecord($tableName, $where);

    /**
     * Empty a table
     *
     * @param string $tableName Table name
     *
     * @return void
     */
    abstract public function emptyTable($tableName);

    /**
     * Get last error message from last database operation
     *
     * @return string Last error message
     */
    public function getLastError()
    {
        return $this->_errorMsg;
    }
}