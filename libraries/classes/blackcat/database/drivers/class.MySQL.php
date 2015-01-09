<?php
/**
 * BlackCat PHP 5 Framework
 *
 * Last updated: June 03, 2010, 11:33 PM
 *
 * @package    database
 * @subpackage drivers
 * @author     Lorensius W. L. T <lorenz@londatiga.net>
 * @version    1.0
 * @copyright  Copyright (c) 2010 Lorensius W. L. T
 */

/**
 * Database abstraction class
 */
require_once CLASS_DIR . '/blackcat/database/class.Db.php';

/**
 * DbException class
 */
require_once CLASS_DIR . '/blackcat/database/class.DbException.php';


/**
 * MySQL handler class.
 * Handling database operation with MySQL server.
 *
 * @package    database
 * @subpackage drivers
 * @author     Lorensius W. L. T <lorenz@londatiga.net>
 * @version    5.0
 * @copyright  Copyright (c) 2010 Lorensius W. L. T
 *
 */

final class MySQL extends Db
{
    /**
     * Result type
     *
     * @var string
     */
    public $resType;
    
    
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
    public function __construct($dbHost='', $dbName='', $dbUser='', $dbPassword='', $dbPort='')
    { 
        parent::__construct($dbHost, $dbName, $dbUser, $dbPassword, $dbPort);
        
        $this->resType = 'object';
    }

    /**
     * Establish connection with database server
     *
     * @throws DbException If problem occured
     *
     * @return void
     */
    public function connect()
    {
        $this->_connRes = @mysql_pconnect($this->_dbHost, $this->_dbUser, $this->_dbPassword);

        if (!$this->_connRes) 
            throw new DbException("Can't connect to database server!", 0);
    }

    /**
     * Close connection with database server
     *
     * @return void
     */
    public function disconnect()
    {
        if ($this->_connRes != null) {
            @mysql_close($this->_connRes);
            
            $this->_connRes = null;
        }
    }

    /**
     * Alias for function disconnect
     */
    public function close()
    {
        $this->disconnect();
    }

    /**
     * Execute an sql query
     *
     * @param string $sql SQL statement
     *
     * @throws DbException If problem occured
     *
     * @return void
     */
    public function execute($sql)
    {
        if ($this->_connRes == null)  $this->connect();
        
        if (empty($this->_dbName) || !mysql_select_db($this->_dbName, $this->_connRes)) 
            throw new DbException('Database does not exists!', mysql_errno()); 

        $this->_querySess                   = (empty($this->_querySess)) ? 'sess' : $this->_querySess;
        $this->_queryRes[$this->_querySess] = mysql_query($sql, $this->_connRes);

        if ($this->_debug) {
            $status = ($this->_queryRes[$this->_querySess]) ? "<font color='#00ff00'>[OK]</font>" : "<font color='#ff3300'>[FAILED]</font>";
            echo "$sql $status<br>";
        }

        if (!$this->_queryRes[$this->_querySess]) {
            throw new DbException("Incorrect query: $sql", mysql_errno());
        } else {
            if (preg_match("/^\s*insert\s/i", $sql)) 
                $this->_lastInsertId = mysql_insert_id();
        }
    }

    /**
     * Alias for function execute
     */
    public function query($sql)
    {
        $this->execute($sql);
    }

    /**
     * Get a row from query result
     *
     * @param int $opt Array result type: MYSQL_ASSOC, MYSQL_NUM, MYSQL_BOTH (optional)
     *
     * @return array One row from query result
     */
    public function getRow($opt = MYSQL_BOTH)
    {
        if ($this->resType == 'object')
            $data = mysql_fetch_object($this->_queryRes[$this->_querySess]);
        else
            $data = mysql_fetch_array($this->_queryRes[$this->_querySess], $opt);

        return $data;
    }

    /**
     * Alias for function getRow
     */
    public function fetch($opt = MYSQL_BOTH)
    {
        return $this->getRow($opt);
    }

    /**
     * Get query result
     *
     * @param int $opt Result type: MYSQL_ASSOC, MYSQL_NUM, MYSQL_BOTH (optional)
     *
     * @return array All rows from query results
     */
    public function getAll($opt = MYSQL_BOTH)
    {
        $data = array();
        while ($tmp = $this->getRow($opt)) {
           $data[]  = $tmp;
        }

        return $data;
    }

    /**
     * Alias for function getAll
     */
    public function fetchAll($opt = MYSQL_BOTH)
    {
        return $this->getAll($opt);
    }

    /**
     * Get query result by row index
     *
     * @param int $id Row index
     * @param string $field Field name
     *
     * @return mixed One record of query results
     */
    public function getResult($id, $field)
    {
        $data = mysql_result($this->_queryRes[$this->_querySess], $id, $field);

        return $data;
    }

    /**
     * Get number of rows of query results
     *
     * @return int number of rows of query results
     */
    function getNumRows()
    {
        $data = mysql_num_rows($this->_queryRes[$this->_querySess]);

        return $data;
    }

    /**
     * Get number of fields
     *
     * @return int Number of fields
     */
    function getNumFields()
    {
        $data = mysql_num_fields($this->_queryRes[$this->_querySess]);

        return $data;
    }

    /**
     * Get field name
     *
     * @param int $col Field offset
     *
     * @return string Field name
     */
    function getFieldName($col)
    {
        $data = mysql_field_name($this->_queryRes[$this->_querySess], $col);

        return $data;
    }

    /**
     * Get next id for an integer type field in a table
     *
     * @param string $tableName Table name
     * @param string $idField Table field where next id to be inserted
     *
     * @throws DbException If problem occured
     *
     * @return int Next id
     */
    function getNextID($tableName, $idField)
    {
        $sql = "SELECT
                         MAX($idField) AS nextID
                FROM
                        $tableName";

        try {
            $this->execute($sql);
            return (int) $this->getResult(0, 'nextID') + 1;
        } catch (DbException $e) {
            throw $e;
        }

        return 0;
    }

    /**
     * Begin transaction
     *
     * @throws DbException If version of MySQL doesn't support transaction
     *
     * @return void
     */
    public function beginTrans()
    {
        try {
            $this->execute('SET AUTOCOMMIT=0');
            $this->execute('BEGIN');
        } catch (DALLException $e) {
            throw new DbException('This version of MySQL doesn\'t support transaction!<br />' . $e->getMessage(), 1);
        }
    }

    /**
     * Commit transaction
     *
     * @throws DbException If version of MySQL doesn't support transaction
     *
     * @return void
     */
    public function commitTrans()
    {
        try {
            $this->execute('COMMIT');
            $this->execute('SET AUTOCOMMIT=1');
        } catch (DALLException $e) {
            throw new DbException('This version of MySQL doesn\'t support transaction!<br />' . $e->getMessage(), 1);
        }
    }

    /**
     * Rollback transaction
     *
     * @throws DbException If version of MySQL doesn't support transaction
     *
     * @return void
     */
    public function rollbackTrans()
    {
        try {
            $this->execute('ROLLBACK');
            $this->execute('SET AUTOCOMMIT=1');
        } catch (DALLException $e) {
            throw new DbException('This version of MySQL doesn\'t support transaction!<br />' . $e->getMessage(), 1);
        }
    }

    /**
     * Insert record(s) into a table
     *
     * @param string $tableName Table name
     * @param array $record Value to be inserted, with format: array(dbfield = 'value')
     *                      ex: array(user_id = '$user', user_name = '$name')
     *
     * @throws DbException If problem occured
     *
     * @return void
     */
    function insertRecord($tableName, $record)
    {
        if (is_array($record) && sizeof($record)) {
            $sql = "INSERT INTO $tableName SET " . implode(', ', $record);

            try {
                $this->execute($sql);
            } catch (DbException $e) {
                throw $e;
            }
        }
    }

    /**
     * Update record(s) in a table
     *
     * @param string $tableName Table name
     * @param array $record Value to be updated, with format: array(dbfield = 'value')
     *                      ex: array(user_id = '$user', user_name = '$name')
     * @param array $where Where clause, with format: array(dbfield = 'value')
     *                      ex: array(user_id = '$user', user_name = '$name')
     *
     * @throws DbException If problem occured
     *
     * @return mixed Query result
     */
    function updateRecord($tableName, $record, $where = '')
    {
        if (is_array($record) && sizeof($record)) {
            $fv = '';
            for ($i = 0; $i < sizeof($record); $i++) {
                $fv .= $record[$i] . (($i == sizeof($record)-1) ? '' : ', ');
            }

            $wh = '';
            if (is_array($where) && sizeof($where)) {
                for ($i = 0; $i < sizeof($where); $i++) {
                    $wh .= $where[$i] . (($i == sizeof($where)-1) ? '' : ' AND ');
                }
            }

            $sql = "UPDATE $tableName SET $fv " . (($wh) ? "WHERE $wh" : '');

            try {
                $this->execute($sql);
            } catch (DbException $e) {
                throw $e;
            }
        }
    }

    /**
     * Delete record (s) from a table
     *
     * @param string $tableName Table name
     * @param array $where Where clause, with format: array(dbfield = 'value')
     *                     ex: array(user_id = '$user', user_name = '$name')
     *
     * @throws DbException If problem occured
     *
     * @return void
     */
    function deleteRecord($tableName, $where)
    {
        if (is_array($where) && sizeof($where)) {
            $wh = 'WHERE ';
            for ($i = 0; $i < sizeof($where); $i++) {
                $wh .= $where[$i] . (($i == sizeof($where)-1) ? '' : ' AND ');
            }
        }

        $sql = "DELETE FROM $tableName $wh";

        try {
            $this->execute($sql);
        } catch (DbException $e) {
            throw $e;
        }
    }

    /**
     * Empty a table
     *
     * @param string $tableName Table Name
     *
     * @throws DbException If problem occured
     *
     * @return void
     */
    function emptyTable($tableName)
    {
        try {
            $this->execute("TRUNCATE TABLE $tableName");
        } catch (DbException $e) {
            throw $e;
        }
    }

    /**
     * Enable debugging mode
     *
     * @param bool $debug TRUE if debugging mode is on and FALSE otherwise
     *
     * @return void
     */
    function enableDebug($debug = true)
    {
        $this->_debug = $debug;
    }
}
