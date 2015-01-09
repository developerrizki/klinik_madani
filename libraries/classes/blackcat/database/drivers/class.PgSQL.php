<?php
/**
 * BlackCat PHP 5 Framework
 *
 * Last updated: March 26, 2011, 10:05 PM
 *
 * @package    database
 * @subpackage drivers
 * @author     Lorensius W. L. T <lorenz@londatiga.net>
 * @version    1.0
 * @copyright  Copyright (c) 2010 Lorensius W. L. T
 */

/**
 * Db class
 */
require_once CLASS_DIR . '/blackcat/database/class.Db.php';

/**
 * DbException
 */
require_once CLASS_DIR . '/blackcat/database/class.DbException.php';


/**
 * PostgreSQL handler class.
 * Handling database operation with PostgreSQL server.
 *
 * @package    Db
 * @subpackage Drivers
 * @author     Lorensius W. L. T <lorenz@londatiga.net>
 * @version    3.0
 * @copyright  Copyright (c) 2004-2007 PT. 3TRUST
 *
 */
final class PgSQL extends Db
{
    /**
     * Result type
     *
     * @var string
     */
    public $resType;

    /**
     * Constructor.
     * Creates a new instance of this class
     *
     * @param string $dbHost PostgreSQL server hostname or ip address (optional)
     * @param string $dbName PostgreSQL database name (optional)
     * @param string $dbUser PostgreSQL server user (optional)
     * @param string $dbPassword PostgreSQL server user's password (optional)
     * @param string $dbPort PostgreSQL server port (optional)
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
     * @throws DbException If problem occured while connecting to database server
     *
     * @return void
     */
    function connect()
    {
        $strConn  = 'host = '     . $this->_dbHost     . ' ';
        $strConn .= 'port = '     . $this->_dbPort     . ' ';
        $strConn .= 'dbname = '   . $this->_dbName     . ' ';
        $strConn .= 'user = '     . $this->_dbUser     . ' ';
        $strConn .= 'password = ' . $this->_dbPassword . ' ';

        $this->_connRes = pg_connect($strConn);

        if (!$this->_connRes) {
            throw new DbException('Failed while making connection to database server!');
        }
    }

    /**
     * Close connection with database server
     *
     * @return void
     */
    public function disconnect()
    {
        if ($this->_connRes != null) {
            pg_close($this->_connRes);
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
     * Execute a query to database server
     *
     * @param string $sql SQL statement
     *
     * @throws DbException If problem occured
     *
     * @return void
     */
    function execute($sql)
    {
        if (phpversion() >= '4.2.0') {
            $this->_queryRes[$this->_querySess] = pg_query($this->_connRes,$sql);
        } else {
            $this->_queryRes[$this->_querySess] = pg_exec($this->_connRes,$sql);
        }

        if (!$this->_queryRes[$this->_querySess]) throw new DbException("Incorrect query: $sql");
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
     * @param int Result type: MYSQL_ASSOC, MYSQL_NUM, MYSQL_BOTH (optional)
     *
     * @return array One row from query result
     */
    public function getRow($opt = PGSQL_BOTH)
    {
        if ($this->resType == 'object')
            $data = pg_fetch_object($this->_queryRes[$this->_querySess]);
        else
            $data = pg_fetch_array($this->_queryRes[$this->_querySess], $opt);

        return $data;
    }

    /**
     * Alias for function fetch
     */
    public function fetch($opt = PGSQL_BOTH)
    {
        return $this->getRow($opt);
    }

    /**
     * Get query result
     *
     * @param int Result type: MYSQL_ASSOC, MYSQL_NUM, MYSQL_BOTH (optional)
     *
     * @return array All rows from query results
     */
    public function getAll($opt = PGSQL_BOTH)
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
    public function fetchAll($opt = PGSQL_BOTH)
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
        $data = pg_fetch_result($this->_queryRes[$this->_querySess], $field, $id);

        return $data;
    }

    /**
     * Get number of rows of query results
     *
     * @return int number of rows of query results
     */
    function getNumRows()
    {
        $data = pg_num_rows($this->_queryRes[$this->_querySess]);

        return $data;
    }

    /**
     * Get number of fields
     *
     * @return int Number of fields
     */
    function getNumFields()
    {
        $data = pg_num_fields($this->_queryRes[$this->_querySess]);

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
        $data = pg_field_name($this->_queryRes[$this->_querySess], $col);

        return $data;
    }

    /**
     * Get next id for a table
     *
     * @param string $tableName Table name
     * @param string $idField Table field where next id to be inserted
     *
     * @throws DbException If problem occured
     *
     * @return int Next id
     */
    function getNextID($tablename, $id)
    {
        $sql = "SELECT
                         MAX($id) AS nextID
                FROM
                        $tablename";

        $res = 0;
        try {
            $this->query($sql);

            $res = (int) $this->getResult(0, 'nextID') + 1;
        } catch (DbException $e) {
            throw $e;
        }

        return $res;
    }

    /**
     * Begin transaction
     *
     * @throws DbException If version of PostgreSQL doesn't support transaction
     *
     * @return void
     */
    public function beginTrans()
    {
        try {
         //   $this->execute('SET AUTOCOMMIT=0');
            $this->execute('BEGIN');
        } catch (DbException $e) {
            throw new DbException('This version of PostgreSQL doesn\'t support transaction <br />' . $e->getMessage());
        }
    }

    /**
     * Commit transaction
     *
     * @throws DbException If version of PostgreSQL doesn't support transaction
     *
     * @return void
     */
    public function commitTrans()
    {
         try {
            $this->execute('COMMIT');
         //   $this->execute('SET AUTOCOMMIT=1');
        } catch (DbException $e) {
            throw new DbException('This version of PostgreSQL doesn\'t support transaction! <br />' . $e->getMessage());
        }
    }

    /**
     * Rollback transaction
     *
     * @throws DbException If version of PostgreSQL doesn't support transaction
     *
     * @return void
     */
    public function rollbackTrans()
    {
         try {
            $this->execute('ROLLBACK');
         //   $this->execute('SET AUTOCOMMIT=1');
        } catch (DbException $e) {
            throw new DbException('This version of PostgreSQL doesn\'t support transaction! <br />' . $e->getMessage());
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
            $fields = "(";
            $values = "(";

            for ($i = 0; $i < sizeof($record); $i++) {
                $pos     = strpos($record[$i], "=");
                $field   = substr($record[$i], 0, $pos);
                $value   = substr($record[$i], $pos + 1);
                $fields .= $field . (($i == sizeof($record) - 1) ? "" : ",");
                $values .= $value . (($i == sizeof($record) - 1) ? "" : ",");
            }

            $fields .= ")";
            $values .= ")";

            $sql = "INSERT INTO $tableName$fields VALUES $values";

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
     * @return void
     */
    function updateRecord($tableName, $record, $where = '')
    {
        if (is_array($record) && sizeof($record)) {
            $fv = "";
            for ($i = 0; $i < sizeof($record); $i++) {
                $fv .= $record[$i] . (($i == sizeof($record)-1) ? "" : ", ");
            }

            $wh = "";
            if (is_array($where) && sizeof($where)) {
                for ($i = 0; $i < sizeof($where); $i++) {
                    $wh .= $where[$i] . (($i == sizeof($where)-1) ? "" : " AND ");
                }
            }

            $sql = "UPDATE $tableName SET $fv " . (($wh) ? "WHERE $wh" : "");

            try {
                $this->execute($sql);
            } catch (DbException $e) {
                throw $e;
            }
        }
    }

    /** Delete record (s) from a table
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
            $wh = "WHERE ";
            for ($i = 0; $i < sizeof($where); $i++) {
                $wh .= $where[$i] . (($i == sizeof($where)-1) ? "" : " AND ");
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
            $this->query("TRUNCATE TABLE $tableName");
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