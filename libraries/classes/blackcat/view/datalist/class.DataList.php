<?php
/**
 * ElGato PHP 5 Framework
 *
 * Last updated: August 30, 2006, 09:22 PM
 *
 * @package   DataList
 * @author    Lorensius W. L. T <lorenz@londatiga.net>
 * @version   1.0
 * @copyright Copyright (c) 2006 - 2008 Lorensius W. L. T
 */

/**
 * SystemException class
 */
require_once CLASS_DIR . '/ElGato/System/class.SystemException.php';

/**
 * DAALException class
 */
require_once CLASS_DIR . '/ElGato/Db/class.DAALException.php';


/**
 * Data list class
 *
 * @package   DataList
 * @author    Lorensius W. L. T <lorenz@londatiga.net>
 * @version   1.0
 * @copyright Copyright (c) 2006 - 2008 Lorensius W. L. T
 *
 */
class DataList
{
    /**
     * Column list
     *
     * @var array
     */
    private $_listColumns = array();

    /**
     * SQL query results
     *
     * @var array
     */
    private $_rows        = array();

    /**
     * Database connection object
     *
     * @var object
     */
    private $_dbObj       = null;

    /**
     * SQL statement
     *
     * @var string
     */
    private $_sql;


    /**
     * Constructor.
     * Create a new instance of this class
     *
     * @param object $dbObj Database connection object (optional)
     *
     * @return void
     */
    public function __construct(&$dbObj = null)
    {
        $this->_dbObj = &$dbObj;

        try {
            if (Registry::exists('dall')) $this->_dbObj = Registry::get('dall');

        } catch (SystemException $e) {
            Error::store($e->getMessage());
        }
    }

    /**
     * Set database connection object
     *
     * @param object $dbObj Database connection object
     *
     * @return void
     */
    public function setConnection(&$dbObj)
    {
        if (is_object($dbObj)) {
            $this->_dbObj = &$dbObj;
        } else {
            if (Registry::exists('dall')) {
                $this->_rbacObj = Registry::get('rbac');
            } else {
                Error::store('Database connection assigned is not an object or null!');
            }
        }
    }

    /**
     * Set SQL statement
     *
     * @param string $sql SQL statement
     *
     * @return void
     */
    public function setQuery($sql)
    {
        $this->_sql = $sql;
    }

    /**
     * Add data list
     *
     * @param string $name Column name
     * @param array $colProp Column properties
     *
     * @return void
     */
    public function addColumn($name, $colProp = '')
    {
        if (!array_key_exists($name, $this->_listColumns)) {
            $this->_listColumns[$name]['title']   = (isset($colProp['title'])) ? $colProp['title'] : '';
            $this->_listColumns[$name]['value']   = (isset($colProp['value'])) ? $colProp['value'] : '';
            $this->_listColumns[$name]['number']  = (isset($colProp['number'])) ? $colProp['number'] : false;

            $this->_listColumns[$name]['dbField'] = '';
            $this->_listColumns[$name]['func']    = '';
            $this->_listColumns[$name]['ffunc']   = '';
        } else {
            Error::store("DataList: Column <i>$name</i> doesn't exists!");
        }
    }

    /**
     * Set database filed
     *
     * @param string $colName Column name
     * @param array $field Field name
     *
     * @return void
     */
    public function setDBField($colName, $field)
    {
        if (array_key_exists($colName, $this->_listColumns)) {
            $field = (!is_array($field)) ? ((!empty($field)) ? array($field) : array()) : $field;

            $this->_listColumns[$colName]['dbField'] = $field;
        } else {
            Error::store("DataList: Column <i>$colName</i> doesn't exists!");
        }
    }

    /**
     * Set column function
     *
     * @param string $colName Column name
     * @param string $func Function name
     * @param array $fargs Function arguments
     *              NB: It will be put after main arguments (dbField, or column value)
     *s
     * @return void
     */
    public function setColumnFunction($colName, $func, $fargs = '')
    {
        if (array_key_exists($colName, $this->_listColumns)) {
            if (function_exists($func)) {
                $fargs = (!is_array($fargs)) ? ((!empty($fargs)) ? array($fargs) : array()) : $fargs;

                $this->_listColumns[$colName]['func']['name'] = $func;
                $this->_listColumns[$colName]['func']['args'] = $fargs;
            } else {
                throw new DataListException("Function $func doesn't exists!");
            }
        } else {
            Error::store("DataList: Column <i>$colName</i> doesn't exists!");
        }
    }

    /**
     * Set column outer function
     *
     * @param string $colName Column name
     * @param string $func Function name
     * @param array $fargs Function arguments
     *        Note: It will be put after main arguments (dbField, or column value)
     *
     * @return void
     */
    public function setOutFunction($colName, $func, $fargs = '')
    {
        if (array_key_exists($colName, $this->_listColumns)) {
            if (function_exists($func)) {
                $fargs = (!is_array($fargs)) ? ((!empty($fargs)) ? array($fargs) : array()) : $fargs;

                $this->_listColumns[$colName]['ffunc']['name']  = $func;
                $this->_listColumns[$colName]['ffunc']['fargs'] = $fargs;
            } else {
                throw new DataListException("Function $func doesn't exists!");
            }
        } else {
            Error::store("DataList: Column <i>$colName</i> doesn't exists!");
        }
    }

    /**
     * Get column value
     *
     * @param string $colName Column name
     * @param array $data Query result row
     *
     * @return mixed Column value
     */
    public function getValue($colName, $data)
    {
        if (!array_key_exists($colName, $this->_listColumns)) {
            return "";
        }

        $colProp = $this->_listColumns[$colName];
        $value   = '';

        if ($colProp['func'] && function_exists($colProp['func']['name'])) {
            $func = $colProp['func']['name'];
            $args = $colProp['func']['args'];

            if (sizeof($colProp['dbField'])) {
                for ($i = 0; $i < sizeof($colProp['dbField']); $i++) {
                    $val[] = $data[$colProp['dbField'][$i]];
                }

                if (sizeof($args)) {
                    $val = array_merge($val, $args);
                }

                $value = call_user_func_array($func, $val);
            } elseif (sizeof($colProp['value'])) {
                if (sizeof($args)) {
                    array_unshift($args, $colProp['value']);

                    $value = call_user_func_array($func, $val);
                } else {
                    $value = call_user_func($func, $colProp['value']);
                }
            }
        } else {
            if (sizeof($colProp['dbField'])) {
                $val = '';
                for ($i = 0; $i < sizeof($colProp['dbField']); $i++) {
                    if (sizeof($colProp['dbField']) == 1) {
                        $val = $data[$colProp['dbField'][$i]];
                    } else {
                        $br   = ($i == sizeof($colProp) - 1) ? '' : '|';
                        $val .= $data[$colProp['dbField'][$i]].$br;
                    }
                }

                $value = $val;
            } else {
                $value = $colProp['value'];
            }
        }

        return $value;
    }

    /**
     * Get data list
     *
     * @return array Data list
     */
    public function getList()
    {
        $list  = array();

        if (sizeof($this->_listColumns)) {
            try {
                $this->_dbObj->execute($this->_sql);

                $this->_rows = $this->_dbObj->getAll();

                if (sizeof($this->_rows)) {
                    $no = 1;

                    for ($i = 0; $i < sizeof($this->_rows); $i++) {
                        $tmp = array();

                        foreach ($this->_listColumns as $colName => $colProp) {
                            $value = $this->getValue($colName, $this->_rows[$i]);

                            if ($colProp['number']) {
                                $value = $no;
                            }

                            if ($colProp['ffunc']) {
                                if (sizeof($colProp['ffunc']['fargs'])) {
                                    $value = call_user_func_array($colProp['ffunc']['name'],
                                                                  array_unshift($colProp['ffunc']['fargs'],
                                                                                $value)
                                                                 );
                                } else {
                                    $value = call_user_func($colProp['ffunc']['name'], $value);
                                }
                            }

                            $tmp[] = $value;
                        }

                        $list[$i]  = $tmp;

                        $no++;
                    }
                }
            } catch (DAALException $e) {
                Error::store($e->getMessage());
            }
        }

        return $list;
    }

    /**
     * Get all column titles
     *
     * @return array Column titles
     */
    public function getTitles()
    {
        $titles = array();

        if (sizeof($this->_listColumns)) {
            foreach ($this->_listColumns as $colName => $colProp) {
                $titles[] = $colProp['title'];
            }
        }

        return $titles;
    }
}
