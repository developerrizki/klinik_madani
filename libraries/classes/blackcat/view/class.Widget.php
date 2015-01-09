<?php
/**
 * ElGato PHP 5 Framework
 *
 * Last updated: August 29, 2009, 05:08 PM
 *
 * @package   View
 * @author    Lorensius W. L. T <lorenz@londatiga.net>
 * @version   1.0
 * @copyright Copyright (c) 2005-2009 Lorensius W. L. T
 */

/**
 * Widget class
 *
 * @package   View
 * @author    Lorensius W. L. T <lorenz@londatiga.net>
 * @version   1.0
 * @copyright Copyright (c) 2005-2009 Lorensius W. L. T
 *
 */
class Widget
{
    /**
     * Database connection object
     *
     * @var object
     */
    private $_dbObj = null;

    /**
     * Table name
     * 
     * @var string
     */                   
    private $_table;
    

    /**
     * Constructor.
     * Create a new instance of this class
     *
     * @param object $dbObj Database connection object
     *
     * @return void
     */
    public function __construct(&$dbObj=null)
    {
        global $cfg;
        
        $this->_dbObj = &$dbObj;

        if ($this->_dbObj == null) {
            if (Registry::exists('daal')) {
                $this->_dbObj = Registry::get('daal');
            }
        }
        
        $this->_table = $cfg['sys']['tblPrefix'] . '_widget';
    }
    
    /**
     *  Get widget list
     *
     *  @param string $access Access type
     *  
     *  @return array Widget list
     */  
    public function getList($access)
    {
        global $cfg;
        
        $sql = "SELECT
                            *
                FROM
                            " . $this->_table . "
                WHERE
                            widget_access = '$access'
                ORDER BY
                            widget_id ASC";

        $res = array();
        
        try {
            $this->_dbObj->query($sql);
            
            $res = ($this->_dbObj->getNumRows()) ? $this->_dbObj->fetchAll() : array();
        } catch (DAALException $e) { Error::store($e->getMessage()); }

        return $res;
    }
    
    public function getListByGroup($group)
    {
        global $cfg;
        
        $sql = "SELECT
                            *
                FROM
                            " . $cfg['sys']['tblPrefix'] . "_widget_group
                JOIN
                            " . $this->_table . "
                USING(widget_id)
                WHERE
                            group_name = '$group'
                ORDER BY
                            widget_id ASC";

        $res = array();
        
        try {
            $this->_dbObj->query($sql);
            
            $res = ($this->_dbObj->getNumRows()) ? $this->_dbObj->fetchAll() : array();
        } catch (DAALException $e) { Error::store($e->getMessage()); }

        return $res;
    }
    
    /**
     *  Get widget hash
     *
     *  @param string $access Access type
     *  
     *  @return array Widget hash
     */                             
    public function getHash($access)
    {
        global $cfg;
        
        $sql = "SELECT
                            *
                FROM
                            " . $this->_table . "
                WHERE
                            widget_access = '$access'
                ORDER BY
                            widget_id ASC";

        $res = array();
        
        try {
            $this->_dbObj->query($sql);
            
            $data = $this->_dbObj->fetchAll();
            
            for ($i = 0; $i < sizeof($data); $i++) {
                $res[$data[$i]['widget_id']] = $data[$i]['widget_title'];
            }
        } catch (DAALException $e) { Error::store($e->getMessage()); }

        return $res;
    }
    
    /**
     *  Get widget's group list
     *
     *  @param int $id Widget id
     *  
     *  @return array Group list
     */                             
    public function getGroup($id)
    {
        global $cfg;
        
        $sql = "SELECT
                            *
                FROM
                            " . $cfg['sys']['tblPrefix'] . "_widget_group
                WHERE
                            widget_id = '$id'";

        $res = array();
        
        try {
            $this->_dbObj->query($sql);
            
            $data = $this->_dbObj->fetchAll();
            
            for ($i = 0; $i < sizeof($data); $i++) {
                $res[$i] = $data[$i]['group_name'];
            }
        } catch (DAALException $e) { Error::store($e->getMessage()); }

        return $res;
    }

    /**
     * Get detail of widget
     * 
     * @param int $mid Widget id
     * 
     * @return array Detail of widget
     */                             
    public function getDetail($id)
    {
        global $cfg;
        
        $sql = "SELECT
                            *
                FROM
                            " . $this->_table . "
                WHERE
                            widget_id = '$id'";

        $res = array();
        
        try {
            $this->_dbObj->query($sql);
            
            $res = ($this->_dbObj->getNumRows()) ? $this->_dbObj->fetch() : array();
        } catch (DAALException $e) { Error::store($e->getMessage()); }

        return $res;
    }

    
    /**
     * Add widget
     * 
     * @return bool TRUE if success or FALSE if failed
     */                   
    public function add()
    {
        global $cfg;

        $sessObj    = Registry::get('session');
        
        $title      = addslashes(strip_tags(HTTP::getVar('title')));
        $access     = HTTP::getVar('access');
        $source     = HTTP::getVar('source');
        $box        = HTTP::getVar('box');
        $file       = HTTP::getVar('file');
        $html       = HTTP::getVar('elm');
        $group      = HTTP::getVar('group');
        $res        = false;

        if ($access == 'INTRANET') {
            if (empty($group)) {
                $sessObj->setVar('formError', 'Group is not selected for intranet widget');
                return false;
            }
        }
        
        if ($source == 'FILE') {
            if (empty($file)) {
                $sessObj->setVar('formError', 'File name is empty');
                return false;
            }
        }
        
        if ($source == 'HTML') {
            if (empty($html)) {
                $sessObj->setVar('formError', 'HTML content is empty');
                return false;
            }
        }
    
        $this->_dbObj->beginTrans();

        try {     
            $id         = $this->_dbObj->getNextID($this->_table, 'widget_id');
   
            $value      = array();
            $value[]    = "widget_id       = '$id'";
            $value[]    = "widget_access   = '$access'";
            $value[]    = "widget_box      = '$box'";
            $value[]    = "widget_title    = '$title'";
            $value[]    = "widget_source   = '$source'";
            $value[]    = "widget_file     = '$file'";
            $value[]    = "widget_html     = '$html'";

            $this->_dbObj->insertRecord($this->_table, $value);

            if ($access == 'INTRANET') {
                for ($i = 0; $i < sizeof($group); $i++) {
                    $value      = array();
                    $value[]    = "widget_id     = '$id'";
                    $value[]    = "group_name    = '$group[$i]'";
                    
                    $this->_dbObj->insertRecord($cfg['sys']['tblPrefix'] . '_widget_group', $value);
                }
            }
            
            $this->_dbObj->commitTrans();
            
            $res = true;
        } catch (DAALException $e) { 
            Error::store($e->getMessage()); 
            
            $this->_dbObj->rollbackTrans();
        }

        return $res;
    }
    
    /**
     * Update widget
     * 
     * @return bool TRUE if success or FALSE if failed
     */   
    public function update()
    {
        global $cfg;

        $sessObj    = Registry::get('session');
        
        $title      = addslashes(strip_tags(HTTP::getVar('title')));
        $access     = HTTP::getVar('access');
        $box        = HTTP::getVar('box');
        $source     = HTTP::getVar('source');
        $file       = HTTP::getVar('file');
        $html       = HTTP::getVar('elm');
        $group      = HTTP::getVar('group');
        $id         = HTTP::getVar('id');
        $res        = false;

        if ($access == 'INTRANET') {
            if (empty($group)) {
                $sessObj->setVar('formError', 'Group is not selected for intranet widget');
                return false;
            }
        }
        
        if ($source == 'FILE') {
            if (empty($file)) {
                $sessObj->setVar('formError', 'File name is empty');
                return false;
            }
            
            $html = '';
        }
        
        if ($source == 'HTML') {
            if (empty($html)) {
                $sessObj->setVar('formError', 'HTML content is empty');
                return false;
            }
            
            $file == '';
        }
    
        $this->_dbObj->beginTrans();

        try {     
            $value      = array();
            $value[]    = "widget_access   = '$access'";
            $value[]    = "widget_box      = '$box'";
            $value[]    = "widget_title    = '$title'";
            $value[]    = "widget_source   = '$source'";
            $value[]    = "widget_file     = '$file'";
            $value[]    = "widget_html     = '$html'";

            $this->_dbObj->updateRecord($this->_table, $value, array("widget_id = '$id'"));

            $this->_dbObj->deleteRecord($cfg['sys']['tblPrefix'] . '_widget_group', array("widget_id = '$id'"));
            
            if ($access == 'INTRANET') {
                for ($i = 0; $i < sizeof($group); $i++) {
                    $value      = array();
                    $value[]    = "widget_id     = '$id'";
                    $value[]    = "group_name    = '$group[$i]'";
                    
                    $this->_dbObj->insertRecord($cfg['sys']['tblPrefix'] . '_widget_group', $value);
                }
            }
            
            $this->_dbObj->commitTrans();
            
            $res = true;
        } catch (DAALException $e) { 
            Error::store($e->getMessage()); 
            
            $this->_dbObj->rollbackTrans();
        }
        
        return $res;
    }
    
    /**
     * Delete widget
     *
     * @var mixed $cb Item to delete
     * 
     * @return bool TRUE if success or FALSE if failed
     */   
    public function delete($cb)
    {
        global $cfg;

        $res = false;
      
        if (empty($cb)) return $res;
        
        if (strpos($cb, ':')) {
            $cb = explode(':', $cb);
      
            $this->_dbObj->beginTrans();
            
            try {
                $num = 0;
                for ($i = 0; $i < sizeof($cb); $i++) {
                    if (empty($cb[$i])) continue;

                    $this->_dbObj->deleteRecord($this->_table, array("widget_id = '$cb[$i]'"));
                    $this->_dbObj->deleteRecord($cfg['sys']['tblPrefix'] . '_widget_group', array("widget_id = '$cb[$i]'"));
                    
                    $num++; 
                }
                
                $this->_dbObj->commitTrans();

                $res = true;
            } catch (DAALException $e) {
                Error::store($e->getMessage());
                
                $this->_dbObj->rollbackTrans();
            }
        } else {
            $this->_dbObj->beginTrans();
            
            try {
                $this->_dbObj->deleteRecord($this->_table, array("widget_id = '$cb'"));
                $this->_dbObj->deleteRecord($cfg['sys']['tblPrefix'] . '_widget_group', array("widget_id = '$cb'"));
                
                $this->_dbObj->commitTrans();

                $res = true;
            } catch (DAALException $e) {
                Error::store($e->getMessage());
                
                $this->_dbObj->rollbackTrans();
            }
        }
        
        return $res;
    }
}