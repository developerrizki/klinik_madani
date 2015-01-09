<?php
/**
 * BlackCat PHP 5 Framework
 *
 * Last updated: June 05, 2010, 11:16 PM
 *
 * @package   model
 * @author    Lorensius W. L. T <lorenz@londatiga.net>
 * @version   1.0
 * @copyright Copyright (c) 2010 Lorensius W. L. T
 */


/**
 * Model class
 *
 * @package    model
 * @author     Lorensius W. L. T <lorenz@londatiga.net>
 * @version    1.0
 * @copyright  Copyright (c) 2010 Lorensius W. L. T
 *
 */
class Model
{
    /**
     * Database table name
     *
     * @var string
     */
    protected $_table;

    /**
     * Table primary key
     */
    protected $_id;

    /**
     * Database object
     *
     * @var string
     */
    protected $_dbObj;


    /**
     * Constructor.
     * Create a new instance of this class
     *
     * @return void
     */
    public function __construct()
    {
        $this->_dbObj = Registry::get('db');
    }

    /**
     * Set database source
     *
     * @param object $dbObj Database object
     *
     * @return void
     */
    public function setDataSource(&$dbObj)
    {
        $this->_dbObj = &$dbObj;
    }

    /**
     * Set table name
     *
     * @param string $table Table name
     *
     * @return void
     */
    public function setTable($table)
    {
        $this->_table = $table;
    }

    /**
     * Get sql statement
     *
     * @param array $args SQL arguments
     *
     * @return string SQL statement
     */
    private function getSQL($args)
    {
        $field  = '*';
        $filter = '';
        $orderby= '';
        $sort   = '';

        if (is_array($args) && sizeof($args)) {
            $args = $args[0];

            foreach ($args as $key => $val) {
                if ($key == 'field') {
                    $field = implode(',', $val);
                } elseif ($key == 'filter') {
                    $filter =  ' WHERE ' . ((is_array($val) && sizeof($val)) ? implode(' AND ', $val) : $val);
                } elseif ($key == 'orderby') {
                    if (!empty($val))
                        $orderby = ' ORDER BY ' . ((is_array($val) && sizeof($val)) ? implode(',', $val) : $val);
                } elseif ($key == 'sort') {
                    $sort = $val;
                }
            }
        }

        $sql  = "SELECT $field FROM " . $this->_table . " $filter $orderby $sort";

        return $sql;
    }

    /**
     * Find an entry in table
     *
     * @return object Table entry
     */
    public function find()
    {
        $args   = func_get_args();

        $sql    = $this->getSQL($args) . ' LIMIT 0,1';

        $data   = '';

        try {
            $this->_dbObj->query($sql);

            $data = $this->_dbObj->fetch();
        } catch (DbException $e) {
            Error::store($e->getMessage());
        }

        return $data;
    }

    /**
     * Find entries in table
     *
     * @return object Table entries
     */
    public function findAll()
    {
        $args   = func_get_args();

        $sql    = $this->getSQL($args);

        $data   = '';

        try {
            $this->_dbObj->query($sql);

            $data = $this->_dbObj->fetchAll();
        } catch (DbException $e) {
            Error::store('Model', $e->getMessage());
        }

        return $data;
    }

    /**
     * Get next id
     *
     * @return int Next id
     */
    public function nextID()
    {
        return $this->_dbObj->getNextID($this->_table, $this->_id);
    }

    /**
     * Insert record into table
     *
     * @param array $values Record value
     *
     * @return bool TRUE if success or FALSE if failed
     */
    public function insert($values)
    {
        $res = false;

        try {
            $this->_dbObj->insertRecord($this->_table, $values);
            $res = true;
        } catch (DbException $e) {
            Error::store('Model', $e->getMessage());
        }

        return $res;
    }

    /**
     * Get last insert id for current session of INSERT query (serial type column)
     *
     * @return int Last insert id
    */
    public function getLastInsertId()
    {
        $res = 0;
        
        try {
            $this->_dbObj->execute("SELECT currval(pg_get_serial_sequence('".$this->_table."', '".$this->_id."')) AS lastid");
    
            $data = $this->_dbObj->fetch();
            $res  = $data->lastid;
        } catch (DbException $e) {
            Error::store('Model', $e->getMessage());
        }
        
        return $res;
    }
    
    /**
     * Update entry (ies) in table
     *
     * @param array $values Record value
     * @param array $filter SQL filter
     *
     * @return bool TRUE if success or FALSE if failed
     */
    public function update($values, $filter)
    {
        $res = false;

        try {
            $this->_dbObj->updateRecord($this->_table, $values, $filter);
            $res = true;
        } catch (DbException $e) {
            Error::store('Model', $e->getMessage());
        }

        return $res;
    }

    /**
     * Delete entry (ies) from table
     *
     * @param array $filter SQL filter
     *
     * @return bool TRUE if success or FALSE if failed
     */
    public function delete($filter)
    {
        $res = false;
        try {
            $this->_dbObj->deleteRecord($this->_table, $filter);
            $res = true;
        } catch (DbException $e) {
            Error::store('Model', $e->getMessage());
        }

        return $res;
    }

    /**
     * Empty table
     *
     * @return bool TRUE if success or FALSE if failed
     */
    public function deleteAll()
    {
        $res = false;

        try {
            $this->_dbObj->query("TRUNCATE TABLE " . $this->_table);
            $res = true;
        } catch (DbException $e) {
            Error::store('Model', $e->getMessage());
        }

        return $res;
    }
}
?>