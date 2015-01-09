<?php
/**
 * BlackCat PHP 5 Framework
 *
 * Last updated: June 06, 2010, 12:50 AM
 *
 * @package     view
 * @subpackage  datagrid
 * @author      Lorensius W. L. T <lorenz@londatiga.net>
 * @version     1.0
 * @copyright   Copyright (c) 2010 Lorensius W. L. T
 */

/**
 * SystemException class
 */
require_once CLASS_DIR . '/blackcat/system/class.SystemException.php';

/**
 * DAALException class
 */
require_once CLASS_DIR . '/blackcat/database/class.DbException.php';

/**
 * QueryString class
 */
require_once CLASS_DIR . '/blackcat/transport/class.QueryString.php';

/**
 * Paging class
 */
require_once CLASS_DIR . '/blackcat/view/datagrid/class.Pagination.php';

/**
 * Form factory class
 */
require_once CLASS_DIR . '/blackcat/view/form/class.FormFactory.php';

/**
 * Error handler class
 */
require_once CLASS_DIR . '/blackcat/system/class.Error.php';

/**
 * HTTP GET/POST class
 */
require_once CLASS_DIR . '/blackcat/transport/class.HTTP.php';

/**
 * DBGrid class
 *
 * @package     view
 * @subpackage  datagrid
 * @author      Lorensius W. L. T <lorenz@londatiga.net>
 * @version     1.0
 * @copyright   Copyright (c) 2010 Lorensius W. L. T
 */
class DBGrid
{
    /** Grid ID
     *
     * @var string
     */
    private $_gid;

    /**
     * Grid title
     *
     * @var string
     */
    private $_title;

    /**
     * Form action
     *
     * @var string
     */
    private $_formAction = array();

    /**
     * Flag for enable/disable pagination
     *
     * @var bool
     */
    private $_enablePagination = false;

    /**
     * Show/hide pagination
     *
     * @var @bool
     */
    private $_showPagination = true;

    /**
     * Total rows
     *
     * @var int
     */
    private $_totalRows = 0;

    /**
     * Flag for enable/disable loading grid data using AJAX
     *
     * @var bool
     */
    private $_enableAJAX = true;

    /**
     * Flag for enable/disable default action button
     *
     * @var bool
     */
    private $_enableDefaultButton = false;

    /**
     * Flag for enable/disable default toolbox
     *
     * @var bool
     */
    private $_enableDefaultTool = false;

    /**
     * Default tool option
     *
     * @var array
     */
    private $_defaultTool = array();

    /**
     * Number of records per page
     *
     * @var string
     */
    private $_recordsPerPage;

    /**
     * Toolbox item
     *
     * @var array
     */
    private $_toolboxItem = array();

    /**
     * Action button
     *
     * @var array
     */
    private $_actionButton = array();

    /**
     * List columns
     *
     * @var array
     */
    private $_listColumns = array();

    /**
     * Self url
     *
     * @var string
     */
    private $_phpSelf;

    /**
     * SQL query statement
     *
     * @var string
     */
    private $_sql;

    /**
     * Flag to indicate wheter grid has rows or not
     *
     * @var bool
     */
    private $_hasRow = false;

    /**
     * Query results
     *
     * @var bool
     */
    private $_rows = array();

    /**
     * Default SQL sorting parameter
     *
     * @var array
     */
    private $_defaultSortParam = array();

    /**
     * HTTP additional GET parameter
     *
     * @var array
     */
    private $_httpGetParam = array();

    /**
     * Current grid state
     *
     * @var string
     */
    private $_currentState;

    /**
     * Number of record to be displayed (not for pagination)
     *
     * @var integer
     */
    private $_recordLimit;

    /**
     * Print template
     *
     * @var string
     */
    private $_printTemplate;

    /**
     * Database connection object
     *
     * @var object
     */
    private $_dbObj = null;

    /**
     * Query string object
     *
     * @var object
     */
    private $_queryStringObj = null;

    /**
     * RBAC Object
     *
     * @var object
     */
    private $_rbacObj = null;

    /**
     * Enable RBAC
     *
     * @param bool
     */
    private $_enableRBAC = false;

    /**
     * System module (for RBAC)
     *
     * @param String
     */
    private $_module;

    /**
     * User groups (RBAC)
     *
     * @param array
     */
    private $_groups;

    /**
     * Constructor.
     * Create a new instance of this class
     *
     * @param string $gid Grid ID
     *
     * @return void
     */
    public function __construct($gid = '1')
    {
        $this->_defaultSortParam['order']   = '';
        $this->_defaultSortParam['sortby']  = '';

        $this->_phpSelf                     = $_SERVER['PHP_SELF'];
        $this->_phpSelf                    .= (!preg_match("/\?/", $this->_phpSelf)) ? '?' : '&';

        $this->_gid                         = $gid;

        try {
            if (Registry::exists('db')) {
                $dbObj                  = Registry::get('db');
                $this->_dbObj           = clone $dbObj;
                $this->_dbObj->resType  = 'array';
            }

            Loader::loadClass('QueryString', CLASS_DIR . '/blackcat/transport');

            $this->_queryStringObj = new QueryString;
        } catch (SystemException $e) {
            Error::store('DBGrid', $e->getMessage());
        }
    }

    /**
     * Enable pagination
     *
     * @param bool $bool TRUE if pagination is enabled and vice versa
     *
     * @return void
     */
    public function enablePagination($bool, $show=true)
    {
        $this->_enablePagination = $bool;
        $this->_showPagination = $show;
    }

    /**
     * Enable loading data grid using AJAX
     *
     * @param bool $bool TRUE if enabled and vice versa
     *
     * @return void
     */
    public function enableAJAX($bool)
    {
        $this->_enableAJAX = $bool;
    }

    /**
     * Enable default action button (delete & select all)
     *
     * @param bool $bool TRUE if enabled and vice versa
     *
     * @return void
     */
    public function enableDefaultButton($bool)
    {
        $this->_enableDefaultButton = $bool;
    }

    /**
     * Enable default toolbox item (add, search, print)
     *
     * @param bool $bool TRUE if enabled and vice versa
     * @param bool $add TRUE if add is enabled and vice versa
     * @param bool $print TRUE if print is enabled and vice versa
     * @param bool $search TRUE if search is enabled and vice versa
     *
     * @return void
     */
    public function enableDefaultTool($bool, $add=true, $print=true, $search=true)
    {
        $this->_enableDefaultTool = $bool;

        $this->_defaultTool['add']    = $add;
        $this->_defaultTool['print']  = $print;
        $this->_defaultTool['search'] = $search;
    }

    /**
     * Set RBAC object
     *
     * @param object $rbacjObj RBAC object
     * @param string $module Module
     *
     * @return void
     */
    public function setRBAC($rbacObj, $module)
    {
        $this->_rbacObj     = &$rbacObj;
        $this->_enableRBAC  = true;
        $this->_module      = $module;

        if (!is_object($this->_rbacObj)) {
            if (Registry::exists('rbac')) {
                $this->_rbacObj     = Registry::get('rbac');
             } else {
                Error::store('DBGrid', 'RBAC object assigned is null or not an object!');
             }
        }

        $sessObj         = Registry::get('session');
        $userObj	     = Registry::get('user');
		$this->_groups   = $userObj->getGroupIdList($sessObj->getUserID());
    }

    /**
     * Set grid title
     *
     * @param string $title Grid title
     *
     * @return void
     */
    public function setTitle($title)
    {
        $this->_title = $title;
    }

    /**
     * Set SQL query statement
     *
     * @param string $sql SQL query statement
     *
     * @return void
     */
    public function setQuery($sql)
    {
        $this->_sql = $sql;
    }

    /**
     * Set database connection object
     *
     * @param object $dbObj Database connection object
     *
     * @return void
     */
    public function setConnection($dbObj)
    {
        if (is_object($dbObj)) {
            $this->_dbObj = clone $dbObj;

            $this->_dbObj->resType = 'table';
        } else {
            Error::store('DBGrid', 'Database connection assigned is not an object or null!');
        }
    }

    /**
     * Set default SQL sorting parameter
     *
     * @param array $param Default SQL sorting parameter, format: array('order' => '', 'sortby' => '')
     *
     * @return void
     */
    public function setDefaultSortParam($param)
    {
        $this->_defaultSortParam['sortby'] = (isset($param['sortby'])) ? $param['sortby'] : '';
        $this->_defaultSortParam['order']  = (isset($param['order'])) ? $param['order'] : '';
    }

    /**
     * Set additional HTTP GET parameter
     *
     * @param array $param Additional HTTP GET parameter
     *
     * @return void
     */
    public function setHTTPGetParam($param)
    {
        if (is_array($param) && sizeof($param)) {
            $this->_httpGetParam = $param;

            foreach ($param as $key => $val) {
                if (!empty($key)) {
                    $this->_queryStringObj->update($key, $val);
                }
            }
        }
    }

    /**
     * Set number of records per page
     *
     * @param int $records Number of records per page
     *
     * @return void
     */
    public function setPaginationParam($records) {
        $this->_recordsPerPage  = $records;
    }

    /**
     * Add form into grid
     *
     * @param string $name Form name
     * @param string $action Form action
     *
     * @return void
     */
    public function addForm($name, $action)
    {
        $this->_formAction['name']   = $name;
        $this->_formAction['action'] = $action;
    }

    /**
     * Add toolbox item
     *
     * @param array $item Toolbox item, with format: array('title' => '', 'link' => '', 'content' => '', 'task' => '')
     *
     * @return void
     */
    public function addToolboxItem($item)
    {
        $this->_toolboxItem[] = $item;
    }

    /**
     * Add action button
     *
     * @param array $button Action button
     *
     * @return void
     */
    public function addActionButton($item)
    {
        $this->_actionButton[] = $item;
    }

    /**
     * Add column into the grid
     *
     * @param string $name Column name
     * @param array $colProp Column properties, with format :
     *                       array('title' => '', 'value' => '', 'sorting' => '', 'hide' => '')
     *
     * @return void
     */
    public function addColumn($name, $colProp = '')
    {
        if (!array_key_exists($name, $this->_listColumns)) {
            $this->_listColumns[$name]['title']     = (isset($colProp['title'])) ? $colProp['title'] : '';
            $this->_listColumns[$name]['value']     = (isset($colProp['value'])) ? $colProp['value'] : '';
            $this->_listColumns[$name]['sorting']   = (isset($colProp['sorting'])) ? $colProp['sorting'] : false;
            $this->_listColumns[$name]['print']     = (isset($colProp['print'])) ? $colProp['print'] : false;
            $this->_listColumns[$name]['hide']      = (isset($colProp['hide'])) ? $colProp['hide'] : false;
            $this->_listColumns[$name]['number']    = (isset($colProp['number'])) ? $colProp['number'] : false;
            $this->_listColumns[$name]['key']       = (isset($colProp['key'])) ? $colProp['key'] : false;
            $this->_listColumns[$name]['colspan']   = (isset($colProp['colspan'])) ? $colProp['colspan'] : 0;
            $this->_listColumns[$name]['hiddenVal'] = (isset($colProp['hiddenVal'])) ? $colProp['hiddenVal'] : '';
            $this->_listColumns[$name]['footer']    = (isset($colProp['footer'])) ? $colProp['footer'] : '';
            $this->_listColumns[$name]['count']     = (isset($colProp['count'])) ? $colProp['count'] : '';

            $this->_listColumns[$name]['dbField'] = array();
            $this->_listColumns[$name]['scField'] = array();
            $this->_listColumns[$name]['func']    = '';
            $this->_listColumns[$name]['ffunc']   = '';
            $this->_listColumns[$name]['TWidth']  = '';
            $this->_listColumns[$name]['THeight'] = '25';
	        $this->_listColumns[$name]['TValign'] = 'middle';
            $this->_listColumns[$name]['TAlign']  = 'center';
            $this->_listColumns[$name]['CWidth']  = '';
            $this->_listColumns[$name]['CHeight'] = '25';
	        $this->_listColumns[$name]['CValign'] = 'middle';
            $this->_listColumns[$name]['CAlign']  = 'center';

            $this->_listColumns[$name]['task']      = '';
            $this->_listColumns[$name]['link']      = '';
            $this->_listColumns[$name]['element']   = '';
            $this->_listColumns[$name]['endCell']   = '';
        } else {
            Error::store('DBGrid', "Column <i>$name</i> already exists");
        }
    }

    /**
     * Set cell format
     *
     * @param string $colName Column name
     * @param array $format Cell format,
     *                      with format: array('TWidth'=>'', 'THeight'=>'', 'TValign'=>'', 'TAlign'=>'',
                                                'CWidth'=>'', 'CHeight'=>'', 'CValign'=>'', 'CAlign'=>'')
     *
     * @return void
     */
    public function setCellFormat($colName, $format)
    {
        if (array_key_exists($colName, $this->_listColumns)) {
            if (is_array($format) && sizeof($format)) {
                $colProp                 = $this->_listColumns[$colName];

                $colProp['TWidth']       = (isset($format['TWidth'])) ? $format['TWidth'] : $colProp['TWidth'];
                $colProp['THeight']      = (isset($format['THeight'])) ? $format['THeight'] : $colProp['THeight'];
		        $colProp['TValign']      = (isset($format['TValign'])) ? $format['TValign'] : $colProp['TValign'];
                $colProp['TAlign']       = (isset($format['TAlign'])) ? $format['TAlign'] : $colProp['TAlign'];
                $colProp['CWidth']       = (isset($format['CWidth'])) ? $format['CWidth'] : $colProp['CWidth'];
                $colProp['CHeight']      = (isset($format['CHeight'])) ? $format['CHeight'] : $colProp['CHeight'];
		        $colProp['CValign']      = (isset($format['CValign'])) ? $format['CValign'] : $colProp['CValign'];
                $colProp['CAlign']       = (isset($format['CAlign'])) ? $format['CAlign'] : $colProp['CAlign'];

                $this->_listColumns[$colName] = $colProp;
            }
        } else {
            Error::store('DBGrid', "Column <i>$colName</i> doesn't exists!");
        }
    }

    /**
     * Set database table field for a column
     *
     * @param string $colName Column name
     * @param array $field Database tabel field name
     *
     * @return void
     */
    public function setDBField($colName, $field)
    {
        if (array_key_exists($colName, $this->_listColumns)) {
            $field = (!is_array($field)) ? ((!empty($field)) ? array($field) : array()) : $field;

            $this->_listColumns[$colName]['dbField'] = $field;
        } else {
            Error::store('DBGrid', "Column <i>$colName</i> doesn't exists!");
        }
    }

    /**
     * Set search table field for a column
     *
     * @param string $colName Column name
     * @param string $filter Filter name
     * @param string $dbField Database field name for query, overwrite default dbField set with setDBField
     * @param string $sql SQL for search query, replace origanl query set useing setQuery
     *
     * @return void
     */
    public function setSearchParam($colName, $filter, $dbField='', $sql='')
    {
        if (array_key_exists($colName, $this->_listColumns) || $colName == 'all') {
            if (!empty($dbField) && !is_array($dbField)) $dbField = array($dbField);

            $this->_listColumns[$colName]['scField'] = array($filter, $dbField, $sql);
        } else {
            Error::store('DBGrid', "Column <i>$colName</i> doesn't exists!");
        }
    }

    /**
     * Set column link
     *
     * @param string $colName Column name
     * @param string $url URL
     * @param array $dbParam URL parameter with key and value pair from database field
     * @param array $adtParam Additional param with key and value specified before, format:
     *                        array('key'=>'value'), ex: array('year'=>2000, 'ID'=>'10').
     *
     * @return void
     */
    public function setColumnLink($colName, $url, $dbParam = '', $adtParam = '', $aParam='')
    {

        if (array_key_exists($colName, $this->_listColumns)) {
            $dbParam  = (!is_array($dbParam)) ? ((!empty($dbParam)) ? array($dbParam) : array()) : $dbParam;
            $adtParam = (!is_array($adtParam)) ? ((!empty($adtParam)) ? array($adtParam) : array()) : $adtParam;

            $this->_listColumns[$colName]['link']['url']      = $url;
            $this->_listColumns[$colName]['link']['dbParam']  = $dbParam;
            $this->_listColumns[$colName]['link']['adtParam'] = $adtParam;
            $this->_listColumns[$colName]['link']['aParam']   = $aParam;
        } else {
            Error::store('DBGrid', "Column <i>$colName</i> doesn't exists!");
        }
    }

    /**
     * Add column function
     * This function used to filter value that would be displayed on a column cell
     *
     * @param string $colName Column name
     * @param string $func Function name
     * @param array $fargs Function arguments
     *              NB: It will be put after main arguments (dbField, or column value)
     *
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
                Error::store('DBGrid', "Function <i>$func</i> doesn't exists!");
            }
        } else {
            Error::store('DBGrid', "Column <i>$colName</i> doesn't exists!");
        }
    }

    /**
     * Set column color
     */
    public function setColumnStyleFunction($colName, $func, $fargs = '')
    {
        if (array_key_exists($colName, $this->_listColumns)) {
            if (function_exists($func)) {
                $fargs = (!is_array($fargs)) ? ((!empty($fargs)) ? array($fargs) : array()) : $fargs;

                $this->_listColumns[$colName]['cfunc']['name'] = $func;
                $this->_listColumns[$colName]['cfunc']['args'] = $fargs;
            } else {
                Error::store('DBGrid', "Function <i>$func</i> doesn't exists!");
            }
        } else {
            Error::store('DBGrid', "Column <i>$colName</i> doesn't exists!");
        }
    }

    /**
     * Add second column filter function
     * This function used to filter value that would be displayed on a column cell
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
                Error::store('DBGrid', "Function <i>$func</i> doesn't exists!");
            }
        } else {
            Error::store('DBGrid', "Column <i>$colName</i> doesn't exists!");
        }
    }

    /**
     * Add element into a column
     *
     * @param string $colName Column name
     * @param array $element Element with format: array('type'=>'', 'name'=>'', 'value'=>'', 'attr'=>'')
     * @param string $func Function to filter element
     * @param array $dbargs Database field as argument for function
     * @param array $adtargs Additional argument for function, will be placed right after database field argument
     *
     * @return void
     */
    public function setColumnElement($colName, $element, $func = '', $dbargs = '', $adtargs = '')
    {
        if (array_key_exists($colName, $this->_listColumns)) {
            $this->_listColumns[$colName]['element']['type']    = (isset($element['type'])) ? $element['type'] : '';
            $this->_listColumns[$colName]['element']['name']    = (isset($element['name'])) ? $element['name'] : '';
            $this->_listColumns[$colName]['element']['options'] = (isset($element['options'])) ? $element['options'] : '';
            $this->_listColumns[$colName]['element']['label']   = (isset($element['label'])) ? $element['label'] : '';
            $this->_listColumns[$colName]['element']['attr']    = (isset($element['attr'])) ? $element['attr'] : '';

            if (!empty($func)) {
                if (function_exists($func)) {
                    $dbargs  = (!is_array($dbargs)) ? ((!empty($dbargs)) ? array($dbargs) : array()) : $dbargs;
                    $adtargs = (!is_array($adtargs)) ? ((!empty($adtargs)) ? array($adtargs) : array()) : $adtargs;

                    $this->_listColumns[$colName]['element']['func']    = $func;
                    $this->_listColumns[$colName]['element']['dbargs']  = $dbargs;
                    $this->_listColumns[$colName]['element']['adtargs'] = $adtargs;
                } else {
                    Error::store('DBGrid', "Function <i>$func</i> doesn't exists!");
                }
            }

            $this->_listColumns[$colName]['value'] = (isset($element['value'])) ? $element['value'] : $this->_listColumns[$colName]['value'] ;
        } else {
            Error::store('DBGrid', "Column $colName doesn't exists!");
        }
    }

    /**
     * Set column task
     *
     * @param array $coltask Column task
     *
     * @return void
     */
    public function setColumnTask($colName, $task)
    {
        if (array_key_exists($colName, $this->_listColumns)) {
            $this->_listColumns[$colName]['task'] = $task;
        } else {
            Error::store('DBGrid', "Column <i>$colName</i> doesn't exists!");
        }
    }

    /**
     * Set print template file
     *
     * @param string $tmpl Path to template file
     *
     * @return void
     */
    public function setPrintTemplate($tmpl)
    {
        $this->_printTemplate = $tmpl;
    }

    /**
     * Enable values from a column to be counted
     *
     * @param string $colName Column name where it's value would be counted
     *
     * @return void
     */
    public function enableColumnCount($colName)
    {
        if (array_key_exists($colName, $this->_listColumns)) {
            $this->_listColumns[$colName]['count'] = true;
        } else {
            Error::store('DBGrid', "Column <i>$colName</i> doesn't exists!");
        }
    }

    /**
     * Set number of record to be displayed (not for pagination)
     *
     * @param int $limit Displayed record limit
     *
     * @return void
     */
    public function setRecordLimit($limit)
    {
        $this->_recordLimit = $limit;
    }

    /**
     * Get SQL sorting parameter
     *
     * @param string $colName Column name
     *
     * @return string SQL sorting parameter
     */
    private function getSQLSortParam($colName)
    {
        $res = '';

        if (array_key_exists($colName, $this->_listColumns) && sizeof($this->_listColumns[$colName]['dbField'])) {
            $dbField = $this->_listColumns[$colName]['dbField'];
            $res     = $dbField[0];
        }

        return $res;
    }

    /**
     * Get column ordering state
     *
     * @return string Column ordering state (asc or desc)
     */
    private function getOrderState()
    {
        $order = (HTTP::getVar('order')) ? HTTP::getVar('order') : $this->_defaultSortParam['order'];

        return ($order == 'asc') ? 'desc' : 'asc';
    }

    /**
     * Get cell value
     *
     * @param string $colName Column name
     * @param array $data Database query results
     *
     * @return mixed Cell's value
     */
    private function getValue($colName, $data)
    {
        if (!array_key_exists($colName, $this->_listColumns)) {
            return '';
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
            } elseif (!empty($colProp['value'])) {
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
                        $raquo = '';
                        $br    = '';
                    } else {
                        $raquo = '&raquo; ';
                        $br    = ($i == sizeof($colProp) - 1) ? '' : '<br>';
                    }

                    $val .= $raquo . $data[$colProp['dbField'][$i]] . $br;
                }

                $value = $val;
            } else {
                $value = $colProp['value'];
            }
        }

        $link = $this->getLink($colName, $data);

        if (!empty($link) && !empty($value)) {
            $param = $this->_listColumns[$colName]['link']['aParam'];
            $sparam= '';
            $class = 'class="GridLink"';

            if (is_array($param)) {
                foreach ($param as $k => $v) {
                    $sparam .= "$k = '$v' ";
                }

                $class = (array_key_exists('class', $param)) ? '' : $class;
            }

            return "<a  $class href=\"$link\" $sparam>$value</a>";
        } else {
            return $value;
        }
    }

    private function getColumnStyle($colName, $data)
    {
        if (!array_key_exists($colName, $this->_listColumns)) {
            return '';
        }

        $colProp = $this->_listColumns[$colName];
        $value   = '';

        if ($colProp['cfunc'] && function_exists($colProp['cfunc']['name'])) {
            $func = $colProp['cfunc']['name'];
            $args = $colProp['cfunc']['args'];

            if (sizeof($colProp['dbField'])) {
                for ($i = 0; $i < sizeof($colProp['dbField']); $i++) {
                    $val[] = $data[$colProp['dbField'][$i]];
                }

                if (sizeof($args)) {
                    $val = array_merge($val, $args);
                }

                $value = call_user_func_array($func, $val);
            } elseif (!empty($colProp['value'])) {
                if (sizeof($args)) {
                    array_unshift($args, $colProp['value']);

                    $value = call_user_func_array($func, $val);
                } else {
                    $value = call_user_func($func, $colProp['value']);
                }
            }
        }

        return $value;
    }

    /**
     * Get cell's link
     *
     * @param string $colName Column name
     * @param array $data Database query result
     *
     * @return mixed Cell's link
     */
    public function getLink($colName, $value)
    {
        $link  = $this->_listColumns[$colName]['link'];
        $print = HTTP::getVar('print');

        if (!empty($link) && empty($print)) {
            $url      = (!empty($link['url'])) ? $link['url'] : $this->_phpSelf;
            $dbParam  = '';
            $adtParam = '';

            if (sizeof($link['dbParam'])) {
                $i = 0;

                foreach ($link['dbParam'] as $key => $val) {
                    if (preg_match('/javascript/', $url)) {
                        $dbParam .= '\'' . $value[$val] . '\'' . (($i == sizeof($link['dbParam'])-1) ? '' : ', ');
                    } else {
                        $dbParam .= $value[$val] . (($i == sizeof($link['dbParam'])-1) ? '' : '/');
                    }

                    $i++;
                }

                if (preg_match('/javascript/', $url)) {
                    $url  = preg_replace("/\(\)/", "($dbParam)", $url);
                } else {
                    $url .= "/$dbParam";
                }
            }

            if (preg_match('/deleteItem/', $url)) $link['adtParam'] = array($this->_gid);

            if (is_array($link['adtParam']) && sizeof($link['adtParam'])) {
                $i = 0;

                foreach ($link['adtParam'] as $key => $val) {
                    if (preg_match('/javascript/', $url)) {
                        $adtParam .= '\'' . $val . '\'' . (($i == sizeof($link['adtParam'])-1) ? '' : ', ');
                    } else {
                        $adtParam .= $key . '=' . $val . (($i == sizeof($link['adtParam'])-1) ? '' : '&');
                    }

                    $i++;
                }

                if (preg_match('/javascript/', $url)) {
                    $url = ($dbParam) ? preg_replace("/\)/", ",$adtParam)", $url) : preg_replace("/\(\)/", "($adtParam)", $url);
                } else {
                    $url .= ($adtParam) ? ((preg_match("/\?/", $url)) ? '&' : '?') . $adtParam : '';
                }
            }

            return $url;
        } else {
            return '';
        }
    }

    /**
     * Check if grid has value (query return results)
     *
     * @return bool TRUE If grid has value or FALSE otherwise
     */
    public function hasRow()
    {
        return $this->_hasRow;
    }

    /**
     * Get query results
     *
     * @return array query results
     */
    public function getRows()
    {
        return $this->_rows;
    }

    /**
     * Get sql state
     *
     * @return string SQL state
     */
    public function getSQL()
    {
        return $this->_sql;
    }

    public function getTotalRows()
    {
        return $this->_totalRows;
    }

    /**
     * Get toolbox container
     *
     * @return string Toolbox container
     */
    public function getToolboxContainer()
    {
        global $cfg;

        @$toolboxContainer = '';

        if ($this->_enableDefaultTool) {
            $_queryStringObj = new QueryString();

            $_queryStringObj->update('print', 1);
            $_queryStringObj->update('gajax', '');

            if (HTTP::getVar('search')) $_queryStringObj->update('search', 1);
            if (HTTP::getVar('filter')) $_queryStringObj->update('filter', HTTP::getVar('filter'));
            if (HTTP::getVar('keyword')) $_queryStringObj->update('keyword', HTTP::getVar('keyword'));

            $purl   = $this->_phpSelf . $_queryStringObj->toString();
            $add    = '';
            $print  = '';
            $search = '';

            if ($this->_defaultTool['add'])
                $add    = array('title' => 'Tambah',
                                'task'  => 'add',
                                'link'  => 'javascript:gridAdd()',
                                'image' => ROOT_URL . '/themes/'.$cfg['sys']['theme'].'/images/add.png');
            if ($this->_defaultTool['print'])
                $print  = array('title' => 'Cetak',
                                'link'  => 'javascript:gridPrint(\'' .  $purl . '\')',
                                'image' => ROOT_URL . '/themes/'.$cfg['sys']['theme'] . '/images/printer.png');
            if ($this->_defaultTool['search'])
                $search = array('title' => 'Pencarian',
                                'link'  => 'javascript:showGridSearch(' . $this->_gid . ')',
                                'image' => ROOT_URL . '/themes/'.$cfg['sys']['theme']. '/images/search.png');

            array_unshift($this->_toolboxItem, $add, $print, $search);
        }

        if (is_array($this->_toolboxItem) && sizeof($this->_toolboxItem)) {
            $toolboxContainer .= "<div id=\"GridToolboxContainer\">\n";

            for ($i = 0; $i < sizeof($this->_toolboxItem); $i++) {
                $item = $this->_toolboxItem[$i];

                if (!empty($item['task']) && $this->_enableRBAC) {
                    if (!$this->_rbacObj->authorize($this->_groups, $this->_module, $item['task'])) continue;
                }

                $img = (!empty($item['image'])) ? "<span style=\"vertical-align:middle\"><img src=\"$item[image]\"></span>" : '';

                @$toolboxContainer .= "           $img"
                                  .  "           <a href=". $item['link'] ."> ". $item['title'] ."</a>\n";
            }

            $toolboxContainer .= "</div>\n";
        }

        return $toolboxContainer;
    }

    /**
     * Get action button container
     *
     * @return string Action button container
     */
    public function getActionButtonContainer()
    {
        $actButtonContainer = '';

        if ($this->_enableDefaultButton) {

            $btnSave   = FormFactory::getInstance('Button', array('name'  => 'btn' . $this->_gid . '_delete',
                                                                  'value' => ' Hapus Pilihan ',
                                                                  'attr'  => array('OnClick' => "deleteMultiple('"
                                                                                . $this->_gid . "')",
                                                                                'disabled' => true,
                                                                                'class' => 'button primary')));
            $btnSelect = FormFactory::getInstance('Button', array('name'  => 'bselect',
                                                                  'value' => ' Piih Semua ',
                                                                  'attr'  => array('OnClick' => "CheckAll('". $this->_gid . "')")));

            $this->addActionButton(array('object' => $btnSave->toString(), 'task' => 'delete'));
            $this->addActionButton(array('object' => $btnSelect->toString(), 'task' => 'delete'));
        }

        if (is_array($this->_actionButton) && sizeof($this->_actionButton)) {
            $actButtonContainer .= "<div id=\"GridActionButtonContainer\">\n";

            for ($i = 0; $i < sizeof($this->_actionButton); $i++) {
                $button = $this->_actionButton[$i];

                if (!empty($button['task']) && $this->_enableRBAC) {
                    if (!$this->_rbacObj->authorize($this->_groups, $this->_module, $button['task'])) continue;
                }

                $actButtonContainer .= $button['object'] . '&nbsp;';
            }

            $actButtonContainer .= "</div>\n";
        }

        return $actButtonContainer;
    }

    /**
     * Get search container (only if default toolbox is enabled)
     *
     * @return string Search container
     */
    private function getSearchContainer()
    {
        $search = HTTP::getVar('search');
        $display= (empty($search)) ? 'none' : 'block';

        $str = '
        <div id="GridSearch_' . $this->_gid . '" class="GridSearch" style="display:'.$display.'">
        Filter
        <select name="gfilter_' . $this->_gid . '" id="gfilter_' . $this->_gid . '" class="input-medium">';

        if (sizeof($this->_listColumns)) {
            foreach ($this->_listColumns as $colName => $colProp) {
                if (!empty($colProp['dbField']) || $colName == 'all') {
                    if (!empty($colProp['scField']) || $colName == 'all') {
                        $selected = ($colName == HTTP::getVar('filter')) ? 'selected' : '';
                        $str     .= "<option value=\"$colName\" $selected>" . $colProp['scField'][0] . "</option>";
                    }
                }
            }
        }

        $checked = (HTTP::getVar('matchcase')) ? 'checked' : '';
        $keyword = HTTP::getVar('keyword');
        $items   = HTTP::getVar('items');

        $items   = (empty($items)) ? $this->_recordsPerPage : $items;
        $result  = (!empty($keyword)) ? "<br><br>Search result for <b><i>'$keyword'</i></b>" : '';

        $str .= '
        </select>
        Kata Kunci
        <input type="text" class="input-medium" value="' . $keyword . '" name="gkeyword_' . $this->_gid . '" id="gkeyword_' . $this->_gid . '" size="20">
        Item
        <input type="text" class="input-mini" value="' . $items . '" name="gitems_' . $this->_gid . '" id="gitems_' . $this->_gid . '">
        <input type="checkbox" class="text" style="position:relative; top:2px;" name="gmatchcase_' . $this->_gid . '" id="gmatchcase_' . $this->_gid . '" ' . $checked . '> Kesesuaian
        <input type="button" class="button primary" name="gsearch" id="gsearch" value=" Cari " onClick="searchGrid(' . $this->_gid . ')">
        ' . $result . '
        </div>';

        return $str;
    }

    /**
     * Get data container
     *
     * @return string Data container
     */
    public function getDataContainer()
    {
        global $cfg;

        $sessObj       = Registry::get('session');
        $print         = HTTP::getVar('print');
        $id            = $this->_gid;
        $dataContainer = '';

        if (HTTP::getVar('search')) $this->_queryStringObj->update('search', 1);
        if (HTTP::getVar('filter')) $this->_queryStringObj->update('filter', HTTP::getVar('filter'));
        if (HTTP::getVar('keyword')) $this->_queryStringObj->update('keyword', HTTP::getVar('keyword'));
        if (HTTP::getVar('matchcase')) $this->_queryStringObj->update('matchcase', HTTP::getVar('matchcase'));

        if (sizeof($this->_listColumns)) {
            $dataContainer   .= "<div id=\"GridData\">\n"
                             .  "    <table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"table table-striped table-bordered table-condensed\">\n"
                             .  "    <thead>"
                             .  "    <tr>\n";

            $order      = (HTTP::getVar('order')) ? HTTP::getVar('order') : $this->_defaultSortParam['order'];
            $sortby     = (HTTP::getVar('sortby')) ? HTTP::getVar('sortby') : $this->_defaultSortParam['sortby'];
            $page       = ($this->_queryStringObj->getValue('page')) ? $this->_queryStringObj->getValue('page') - 1 : 0;

            $this->_recordsPerPage = (HTTP::getVar('items')) ? HTTP::getVar('items') : $this->_recordsPerPage ;

            $this->_queryStringObj->update('gid',       $id);
            $this->_queryStringObj->update('order',     $this->getOrderState());
            $this->_queryStringObj->update('sortby',    $sortby);
            $this->_queryStringObj->update('items',     $this->_recordsPerPage);

            $qsCopy     = clone $this->_queryStringObj;

            $qsCopy->update('order', $order);

            $this->_currentState = $qsCopy->toString();

            if ($this->_enableAJAX) $this->_queryStringObj->update('gajax', 1);

            $colspan = 0;
            $i       = 1;
            $print   = HTTP::getVar('print');

            foreach ($this->_listColumns as $colName => $colProp) {
                if (!$colProp['print'] && !empty($print)) continue;

                if (!empty($colProp['task']) && $this->_enableRBAC) {
                    if (!$this->_rbacObj->authorize($this->_groups, $this->_module, $colProp['task'])) {
                        unset($this->_listColumns[$colName]);
                        continue;
                    }
                }

                if ($colProp['hide']) {
                    unset($this->_listColumns[$colName]);
                    continue;
                }

                $width  = ($colProp['TWidth']) ? "width=\"$colProp[TWidth]\"" : '';
                $height = ($colProp['THeight']) ? "height=\"$colProp[THeight]\"" : '';
                $valign = ($colProp['TValign']) ? "valign=\"$colProp[TValign]\"" : '';
                $align  = ($colProp['TAlign']) ? "align=\"$colProp[TAlign]\"" : '';
                $right  = 'style="padding-' . strtolower($colProp['TAlign']) . ':4px"';
                $style  = ($colProp['TAlign'] == 'left' || $colProp['TAlign'] == 'right') ? $right : '';
                $title  = $colProp['title'];
                $img    = '';

                if ($colProp['sorting'] && empty($print)) {
                    if ($sortby == $this->getSQLSortParam($colName) && HTTP::getVar('sortby')) {
                        $img   = "&nbsp;&nbsp;<img src='" . ROOT_URL . "/themes/" . $cfg['sys']['theme']
                               . "/images/$order.png' border='0'>";
                    }

                    $this->_queryStringObj->update('sortby', $this->getSQLSortParam($colName));

                    $curr = HTTP::getCurrentURL();

                    if (preg_match("/\./", $curr)) {
                         $url  = $this->_phpSelf . $this->_queryStringObj->toString();
                    } else {
                        $curl = HTTP::getCurrentURLPath();

                        $curl = $curl . ((strrpos($curl, '/', strlen($curl)-1)) ? '?' : '/?');

                        $url  = ROOT_URL . $curl . $this->_queryStringObj->toString();
                    }

                    $attr =  "href=\"$url\"";

                    if ($this->_enableAJAX) $attr =  "href=\"javascript:loadDataContainer('$url', '$id')\"";

                    $title = "<a $attr>$colProp[title]$img</a>";
                }

                if (!empty($colProp['colspan'])) {
                    $colspan        = $colProp['colspan'];
                    $dataContainer .= "        <th colspan=\"$colspan\">\n"
                                   .  "            $title\n"
                                   .  "        </th>\n";
                    $i              = 1;
                } else {
                    if ($i >= $colspan) {
                        $dataContainer .= "    <th $width>\n"
                                       .  "        $title\n"
                                       .  "    </th>\n";
                    }

                    $i++;
                }
            }

            $dataContainer .= "    </tr></thead>\r\n";
            $pgContainer    = '';

            try {
                $esql = '';

                if (HTTP::getVar('search') && $this->_enableDefaultTool) {
                    $filter    = HTTP::getVar('filter');
                    $keyword   = HTTP::getVar('keyword');
                    $matchcase = HTTP::getVar('matchcase');

                    $like      = (empty($matchcase)) ? "ILIKE '%$keyword%'" : "='$keyword'";

                    if (!empty($filter)) {
                        $dbField = $this->_listColumns[$filter]['dbField'];
                        $scField = $this->_listColumns[$filter]['scField'];
                        $dbField = (!empty($scField[1])) ? $scField[1] : $dbField;

                        if (!empty($scField[2])) $this->_sql = $scField[2];

                        $clause  = (preg_match('/WHERE/i', $this->_sql)) ? ' AND ' : 'WHERE';

                        if (sizeof($dbField) > 1) {
                            for ($i = 0; $i < sizeof($dbField); $i++) {
                                if ($i == 0)
                                    $esql .= " $clause ($dbField[$i] $like ";
                                else
                                    $esql .= " OR $dbField[$i] $like " . (($i == sizeof($dbField[0])) ? ')' : '');

                            }
                        } else {
                            $esql = " $clause $dbField[0] $like ";
                        }
                    }
                }

                $this->_sql .= $esql . ((!empty($order) && !empty($sortby)) ? " ORDER BY $sortby $order " : '');

                $this->_dbObj->execute($this->_sql);

                if ($this->_dbObj->getNumRows()) {
                    $this->_hasRow = true;
                    $totalRecords  = $this->_dbObj->getNumRows();
                    $this->_totalRows= $totalRecords;

                    if (($totalRecords > $this->_recordsPerPage) && $this->_enablePagination ) {

                        $pgObj   = new Pagination($id, $qsCopy);

                        $pgObj->setHTTPGetParam($this->_httpGetParam);
                        $pgObj->enableAJAX($this->_enableAJAX);

                        $limitStr     = ' LIMIT ' . $this->_recordsPerPage . ' OFFSET ' . ($page * $this->_recordsPerPage)   ;
                        $this->_sql  .= ' ' . $limitStr;

                        $pgNavigation = $pgObj->toString($totalRecords, $this->_recordsPerPage);

                        if ($this->_showPagination)
                        $pgContainer  = "<div id=\"GridPagination\">$pgNavigation</div>\n";

                        $this->_dbObj->execute($this->_sql);
                    } elseif (!empty($this->_recordLimit)) {
                        $limitStr     = ' LIMIT 0, ' . $this->_recordLimit;
                    }

                    $count     = array();
                    $hasEndRow = false;

                    $this->_rows = $this->_dbObj->getAll();
                    $no          = ($page * $this->_recordsPerPage) + 1;

                    $dataContainer .= "<tbody>\n";
                    for ($i = 0; $i < sizeof($this->_rows); $i++) {
                        $class          = ($i % 2 == 0) ? 'even' : 'odd';
                        $dataContainer .= "    <tr>\n";

                        foreach ($this->_listColumns as $colName => $colProp) {
                            if (!$colProp['print'] && !empty($print)) continue;

                            $value  = $this->getValue($colName, $this->_rows[$i]);
                            $hValue = $value;

                            if (!empty($colProp['element'])) {
                                if (!empty($colProp['element']['func'])) {
                                    $dbVal = array();

                                    if (sizeof($colProp['element']['dbargs'])) {
                                        for ($t = 0; $t < sizeof($colProp['element']['dbargs']); $t++) {
                                            $dbVal[] = $this->_rows[$i][$colProp['element']['dbargs'][$t]];
                                        }
                                    }

                                    $args = array_merge($dbVal, $colProp['element']['adtargs']);

                                    array_unshift($args, $colProp['element']['attr']);

                                    $colProp['element']['attr'] = call_user_func_array($colProp['element']['func'],
                                                                                       $args);
                                }

                                $ename   = $colProp['element']['name'];
                                $attr    = array('name'    => ($ename == 'cb') ? $ename . '_' . $id : $ename,
                                                 'value'   => $value,
                                                 'attr'    => $colProp['element']['attr'],
                                                 'options' => $colProp['element']['options']);
                                $element = FormFactory::getInstance($colProp['element']['type'], $attr);
                                $value   = (is_object($element)) ? $element->toString() : '';
                            } elseif (!empty($colProp['number'])) {
                                $value = $no;
                            } else {
                                if (!empty($colProp['count'])) {
                                    $count[$colName] += $value;
                                }
                            }

                            $cstyle    = $this->getColumnStyle($colName, $this->_rows[$i]);
                            $hasEndRow = (!empty($colProp['footer']) || $colProp['count']) ? true : $hasEndRow;
                            $height    = ($colProp['CHeight']) ? 'height="' . $colProp['CHeight'] . '"' : '';
                            $valign    = ($colProp['CValign']) ? 'valign="' . $colProp['CValign'] . '"' : '';
                            $align     = ($colProp['CAlign']) ? 'align="' . $colProp['CAlign'] . '"' : '';
                            $padding   = '';

                            if ($colProp['CAlign'] == 'left' || $colProp['CAlign'] == 'right') {
                                $padding = 'padding-' . strtolower($colProp['CAlign']) . ':4px';
                            }

                            if (!empty($colProp['ffunc'])) {
                                if (is_array($colProp['ffunc']['fargs']) && sizeof($colProp['ffunc']['fargs'])) {
                                    $value = call_user_func_array($colProp['ffunc']['name'],
                                                                  array_unshift($colProp['ffunc']['fargs'], $value)
                                                                 );
                                } else {
                                    $value = call_user_func($colProp['ffunc']['name'], $value);
                                }
                            }

                            $hiddenVal = '';
                            if (!empty($colProp['hiddenVal'])) {
                                $attr      = array('name' => $colProp['hiddenVal'] . '[]', 'value'=> $hValue);
                                $element   = FormFactory::getInstance('InputHidden', $attr);
                                $hiddenVal = (is_object($element)) ? $element->toString() : '';
                            }

                            $style = (!empty($cstyle) || !empty($padding)) ? "style='$cstyle;$padding;'" : '';

                            $dataContainer .= "        <td $align $style>\n"
                                           .  "            $hiddenVal $value\n"
                                           .  "        </td>\n";
                        }

                        $dataContainer .= "    </tr>\n";
                        $no++;
                    }

                    if ($hasEndRow) {
                        $dataContainer .= "    <tr class=\"footer\">\n";

                        foreach ($this->_listColumns as $colName => $colProp) {
                            if (!$colProp['print'] && !empty($print)) continue;

                            $value = '';

                            if (!empty($colProp['footer'])) {
                                $value = $colProp['footer'];
                                $align = 'align="right"';
                                $style = 'style="padding-right:4px"';
                            } elseif ($colProp['count']) {
                                $value = $count[$colName];
                            }

                            if (!empty($colProp['ffunc'])) {
                                if (is_array($colProp['ffunc']['fargs']) && sizeof($colProp['ffunc']['fargs'])) {
                                    $value = call_user_func_array($colProp['ffunc']['name'],
                                                                  array_unshift($colProp['ffunc']['fargs'], $value)
                                                                 );
                                } else {
                                    $value = call_user_func($colProp['ffunc']['name'], $value);
                                }
                            }

                            $dataContainer .= "        <td $align $style>\n"
                                           .  "           $value\n"
                                           .  "        </td>\n";
                        }

                        $dataContainer .= "    </tr>\n";
                    }
                } else {
                    $dataContainer .= "    <tr>\n"
                                   .  "        <td height=\"25\" colspan=\""
                                   .  sizeof($this->_listColumns)
                                   .  "\" class=\"even\" align=\"center\" valign=\"middle\" style=\"padding:5px;\">"
                                   .  "            TIDAK ADA DATA\r\n"
                                   .  "        </td>\n"
                                   .  "    </tr>\n";
                }

                $dataContainer .= "    </tbody></table>\n"
                               .  "</div>\n";


                $dataContainer  = (empty($print)) ? "$dataContainer\n$pgContainer\n" : $dataContainer;

            } catch (DbException $e) {
                Error::store('DBGrid', $e->getMessage());

                $dataContainer .= "    <tr>\n"
                                   .  "        <td colspan=\""
                                   .  sizeof($this->_listColumns)
                                   .  "\"  align=\"center\" valign=\"middle\" style=\"padding:10px;\">"
                                   .  "            TIDAK ADA DATA\r\n"
                                   .  "        </td>\n"
                                   .  "    </tr>\n";


                $dataContainer .= "    </table>\n"
                               .  "</div>\n";
            }
        }

        // echo $dataContainer;
        // exit();

        return $dataContainer;
    }

    /**
     * Get HTML tag of the grid
     *
     * @return string HTML tag of the grid
     */
    public function toString($status = '')
    {
        global $cfg;

        $id      = $this->_gid;
        $sf      = '';
        $ef      = '';

        $data    = $this->getDataContainer();
        $print   = HTTP::getVar('print');

        $this->_queryStringObj->update('gajax', '');

        if (!empty($print)) {
            $str = "<!-- Generated by DBGrid class version 3.0 //-->\n\n"
                 . "<div id=\"GridContainer\">\n"
                 . "<!-- Start Grid //-->\n"
                 . "$data\n"
                 . "<!-- End Grid //-->\n"
                 . "</div>\n";

            $tmpl = new tmpl();

            $tmpl->setTemplate('list',  $this->_printTemplate);
            $tmpl->setValue('ROOT_URL', ROOT_URL);
            $tmpl->setValue('DATA', 	$str);
            $tmpl->parse('list');

            die($tmpl->toString());
        } else {
            $ajax = (HTTP::getVar('gajax') && $this->_enableAJAX && ($this->_gid == HTTP::getVar('gid'))) ? true : false;

            if (!empty($this->_formAction['name'])) {
                $sf = '<form name="' . $this->_formAction['name'] . '" id="' . $this->_formAction['name']
                    . '" method="post" action="' . $this->_formAction['action'] . '">'
                    . '<input type="hidden" name="gaid_' . $id . '" id="gaid_' . $id . '" value="1">';
                $ef = '</form>';
            }

            $tool   = $this->getToolboxContainer();
            $search = ($this->_enableDefaultTool) ? $this->getSearchContainer() : '';
            $action = ($this->_hasRow) ? $this->getActionButtonContainer() : '';

            $str    = "<!-- Generated by DBGrid class version 3.0 //-->\n\n"
                    . "<div id=\"GridContainer\">\n"
                 //   . ((!$ajax || !empty($status)) ? "<div id=\"status_$id\" class=\"status\">$status</div>\n" : '')
                    . "<div id=\"GridContainer_$id\">\n"
                    . "<!-- Start Grid //-->\n"
                    . "$sf\n"
                    . "$tool\n$search\n$data\n$action\n"
                    . "$ef\n"
                    . "<!-- End Grid //-->\n"
                    . "</div>\n"
                    . "</div>\n";

            if ($ajax) {
                die($str);
            } else {
                $themeObj = Registry::get('theme');

                // $themeObj->addCSS(ROOT_URL . '/themes/' . $cfg['sys']['theme'] . '/css/grid.css');
                // $themeObj->addCSS(ROOT_URL . '/themes/' . $cfg['sys']['theme'] . '/css/form.css');
                // $themeObj->addJavaScript(ROOT_URL . '/jscript/ngrid.js');

                echo"
                <script language='javascript' src='". ROOT_URL ."/jscript/ngrid.js'></script>
                <link rel='stylesheet' type='text/css' href='". ROOT_URL ."/themes/".$cfg['sys']['theme']."/css/grid.css'>
                <link rel='stylesheet' type='text/css' href='". ROOT_URL ."/themes/".$cfg['sys']['theme']."/css/form.css'>
                ";

            }
        }

        return $str;
    }
}