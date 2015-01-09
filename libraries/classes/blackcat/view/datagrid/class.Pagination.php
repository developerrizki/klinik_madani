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
 * Query string class
 */
require_once CLASS_DIR . '/blackcat/transport/class.QueryString.php';

/**
 * Error handler class
 */
require_once CLASS_DIR . '/blackcat/system/class.Error.php';


/**
 * Pagination system class
 *
 * @package     view
 * @subpackage  datagrid
 * @author      Lorensius W. L. T <lorenz@londatiga.net>
 * @version     1.0
 * @copyright   Copyright (c) 2010 Lorensius W. L. T
 */
class Pagination
{
    /**
     * Grid ID
     * 
     * @var string
     */
    private $_gid;
                       
    /**
     * Flag for enable/disable ajax
     *
     * @var bool
     */
    private $_enableAjax = true;

    /**
     * Query string object
     *
     * @var object
     */
    private $_queryStringObj;

    /**
     * Php self
     *
     * @var string
     */
    private $_phpSelf;


    /**
     * Constructor.
     * Create a new instance of this class
     *
     * @param string $gid Grid ID
     *           
     * @return void
     */
    public function __construct($gid, $qs)
    {
        $this->_queryStringObj = $qs;
        $this->_gid            = $gid;
        $this->_phpSelf        = $_SERVER['REQUEST_URI'];
        $this->_phpSelf       .= (!preg_match("/\?/", $this->_phpSelf)) ? '?' : '&';
    }

    /**
     * Enable loading grid's data using AJAX
     *
     * @param bool $bool TRUE if ajax is enabled and vice versa
     *
     * @return void
     */
    public function enableAJAX($bool)
    {
        $this->_enableAjax = $bool;

        if ($bool) $this->_queryStringObj->update('gajax', 1);
    }

    /**
     * Set HTTP GET parameter
     *
     * @param array $param HTTP GET parameter, with format: array(key => value)
     *
     * @return void
     */
    public function setHTTPGetParam($param)
    {
        if (is_array($param) && sizeof($param)) {
            foreach ($param as $key => $val) {
                if ($key) {
                    $this->_queryStringObj->update($key, $val);
                }
            }
        }
    }

    /**
     * Create pagination
     *
     * @param int $totalRecords Total records
     * @param int $recordsPerPage Number of records per page
     *
     * @return void
     */
    public function toString($totalRecords, $recordsPerPage)
    {
        $page         = ($this->_queryStringObj->getValue('page')) ? $this->_queryStringObj->getValue('page') : 1;
        $numberOfPage = ceil($totalRecords / $recordsPerPage);

        if ($numberOfPage > 5) {
            $startPage  = $page - 2;
            $endPage    = $page + 2;

            if ($endPage > $numberOfPage) {
                $endPage   = $numberOfPage;
                $startPage = $numberOfPage - 4;
            } else {
                if ($startPage <= 0) {
                    $startPage = 1;
                    $endPage   = $startPage + 4;
                }
            }

            $nav = '';
            for ($i = $startPage; $i <= $endPage; $i++) {
                if ($page == $i) {
                    $nav .= "<font color=\"red\">$i</font>&nbsp;";
                } else {
                    $this->_queryStringObj->update('page', $i);

                    $attr = $this->getAnchorAttribut($this->_enableAjax, $this->_phpSelf . $this->_queryStringObj->toString());

                    $nav .= "<a $attr>$i</a> &nbsp;";
                }
            }

            if ($page == 1) {
                $this->_queryStringObj->update('page', $page + 1);

                $attrNext = $this->getAnchorAttribut($this->_enableAjax, $this->_phpSelf . $this->_queryStringObj->toString());

                $this->_queryStringObj->update('page', $numberOfPage);

                $attrEnd  = $this->getAnchorAttribut($this->_enableAjax, $this->_phpSelf . $this->_queryStringObj->toString());

                $nextLeft  = '<< <';
                $nextRight = "<a $attrNext>></a> <a $attrEnd>>></a>";
            } elseif ($page > 1 && $page < $numberOfPage) {
                $this->_queryStringObj->update('page', $page + 1);

                $attrNext = $this->getAnchorAttribut($this->_enableAjax, $this->_phpSelf . $this->_queryStringObj->toString());

                $this->_queryStringObj->update('page', $numberOfPage);

                $attrEnd  = $this->getAnchorAttribut($this->_enableAjax, $this->_phpSelf . $this->_queryStringObj->toString());

                $nextRight = "<a $attrNext>></a> <a $attrEnd>>></a>";

                $this->_queryStringObj->update('page', $page - 1);

                $attrPrev  = $this->getAnchorAttribut($this->_enableAjax, $this->_phpSelf . $this->_queryStringObj->toString());

                $this->_queryStringObj->update('page', 0);

                $attrStart = $this->getAnchorAttribut($this->_enableAjax, $this->_phpSelf . $this->_queryStringObj->toString());

                $nextLeft = "<a $attrStart><<</a> <a $attrPrev><</a>";
            } else {
                $this->_queryStringObj->update('page', $page - 1);

                $attrPrev  = $this->getAnchorAttribut($this->_enableAjax,  $this->_phpSelf . $this->_queryStringObj->toString());

                $this->_queryStringObj->update('page', 0);

                $attrStart = $this->getAnchorAttribut($this->_enableAjax, $this->_phpSelf . $this->_queryStringObj->toString());

                $nextRight = '> >>';
                $nextLeft = "<a $attrStart><<</a> <a $attrPrev><</a>";
            }
        } else {
            $nextRight = '> >>';
            $nextLeft  = '<< <';

            $nav = '';
            for ($i = 1; $i <= $numberOfPage; $i++) {
                if ($page== $i) {
                    $nav .= "<font color=\"red\">$i</font>&nbsp;";
                } else {
                    $this->_queryStringObj->update('page', $i);

                    $attr  = $this->getAnchorAttribut($this->_enableAjax, $this->_phpSelf . $this->_queryStringObj->toString());

                    $nav  .= "<a $attr>$i</a> &nbsp;";
                }
            }
        }

        return "Page $nextLeft $nav $nextRight of $numberOfPage ($totalRecords rows)";
    }

    /**
     * Get anchor attribut
     *
     * @param bool $ajax Flag for enable/disable AJAX
     * @param string $url Destination URL
     *
     * @return string
     */
    private function getAnchorAttribut($ajax, $url)
    { //die($url);

        $gid  = $this->_gid;
        $attr = ($ajax) ? "onClick=\"loadDataContainer('$url', '$gid')\" href=\"#\"" : "href=\"$url\"";

        return $attr;
    }

}