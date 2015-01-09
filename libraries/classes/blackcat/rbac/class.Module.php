<?php
/**
 * ElGato PHP 5 Framework
 *
 * Last updated: September 01, 2009, 01:37 PM
 *
 * @package   RBAC
 * @author    Lorensius W. L. T <lorenz@londatiga.net>
 * @version   1.0
 * @copyright Copyright (c) 2005-2009 Lorensius W. L. T
 */

/**
 * Module class
 *
 * @package   RBAC
 * @author    Lorensius W. L. T <lorenz@londatiga.net>
 * @version   1.0
 * @copyright Copyright (c) 2005-2009 Lorensius W. L. T
 *
 */
class Module
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
        
        $this->_table = $cfg['sys']['tblPrefix'] . '_module';
    }

    /**
     * Get module hash
     * 
     * @return array Module hash
     */                   
    public function getHash()
    {
        global $cfg;
        
        $sql = "SELECT
                            *
                FROM
                            " . $this->_table . "
                WHERE
                            module_primary = 'N'
                ORDER BY
                            module_name ASC";

        $res = array();
        
        try {
            $this->_dbObj->query($sql);
            
            if ($this->_dbObj->getNumRows()) {
                $data = $this->_dbObj->fetchAll();

                for ($i = 0; $i < sizeof($data); $i++) {
                    $res[$data[$i]['module_name']] = $data[$i]['module_name'];
                }
            }
        } catch (DAALException $e) { Error::store($e->getMessage()); }

        return $res;
    }
        
    /**
     * Get module's detail
     * 
     * @param int $module Module name
     * 
     * @return array Module's detail
     */
    public function getDetail($module)
    {
        global $cfg;
        
        $sql = "SELECT
                        *
                FROM
                        " . $this->_table . "
                WHERE
                        module_name = '$module'";
        
        $res = array();
        
        try {
            $this->_dbObj->query($sql);
            
            $res = ($this->_dbObj->getNumRows()) ? $this->_dbObj->fetch() : array();
        } catch (DAALException $e) { Error::store($e->getMessage()); }
        
        return $res;
    }
    
    /**
     * Check if a module already exists
     * 
     * @param string $module Module name
     *
     * @return bool TRUE if exists or FALSE vice versa
     */                         
    public function exists($module)
    {
        $detail = $this->getDetail($module);
        
        return (is_array($detail) && sizeof($detail)) ? true : false;    
    }  
    
    /**
     * Get module hash by group
     * 
     * @param string $group Group name
     * 
     * @return array Module hash
     */                             
    public function getHashByGroup($group)
    {
        global $cfg;
        
        $sql = "SELECT
                        DISTINCT(module_name) as module_name
                FROM
                        " . $cfg['sys']['tblPrefix'] . "_group_task
                JOIN
                        " . $cfg['sys']['tblPrefix'] . "_task
                USING(task_id)
                JOIN
                        " . $this->_table . "
                USING(module_name)
                WHERE
                        module_primary = 'N'
                        AND
                        group_name = '$group'
                ORDER BY
                        module_name ASC"; 
      
        $res = array();
        
        try {
            $this->_dbObj->query($sql);
            
            if ($this->_dbObj->getNumRows()) {
                $data = $this->_dbObj->fetchAll();

                for ($i = 0; $i < sizeof($data); $i++) {
                    $res[$data[$i]['module_name']] = $data[$i]['module_name'];
                }
            }
        } catch (DAALException $e) { Error::store($e->getMessage()); }
        
        return $res;    
    }
    
    /**
     * Get task list
     * 
     * @param string $module Module name
     * 
     * @return string Task list
     */                             
    public function getTaskList($module)
    {
        global $cfg;
        
        $sql = "SELECT
                        *
                FROM
                        " . $cfg['sys']['tblPrefix'] . "_task
                WHERE
                        module_name = '$module'";
       
        $res = array();
        
        try {
            $this->_dbObj->query($sql);
            
            $res = ($this->_dbObj->getNumRows()) ? $this->_dbObj->fetchAll() : array();
        } catch (DAALException $e) { Error::store($e->getMessage()); }
        
        return $res;
    }

    /**
     * Get task hash
     * 
     * @param string $module Module name
     * 
     * @return string Task hash
     */ 
    public function getTaskHash($module)
    {
        global $cfg;
        
        $sql = "SELECT
                        *
                FROM
                        " . $cfg['sys']['tblPrefix'] . "_task
                WHERE
                        module_name = '$module'";
       
        $res = array();
        
        try {
            $this->_dbObj->query($sql);
            
            if ($this->_dbObj->getNumRows()) {
                $data = $this->_dbObj->fetchAll();

                for ($i = 0; $i < sizeof($data); $i++) {
                    $res[$data[$i]['task_id']] = $data[$i]['task_name'];
                }
            }
        } catch (DAALException $e) { Error::store($e->getMessage()); }
        
        return $res;
    }
       
    /**
     * Add new module
     * 
     * @return bool TRUE if success or FALSE if failed
     */                   
    public function add()
    {
        global $cfg;

        $sessObj    = Registry::get('session');
    
        $module     = addslashes(strip_tags(HTTP::getVar('module')));
        $desc       = addslashes(strip_tags(HTTP::getVar('desc')));
        $res        = false;
        
        if ($this->exists($module)) {
            $sessObj->setVar('formError', "Module <b>$module</b> already exists!");
            return false;
        }
        
        try {
            $value      = array();
            $value[]    = "module_name         = '$module'";
            $value[]    = "module_description  = '$desc'";
    
            $this->_dbObj->insertRecord($this->_table, $value);

            $res = true;
        } catch (DAALException $e) { Error::store($e->getMessage()); }
        
        return $res;
    }
    
    /**
     * Update module
     * 
     * @return bool TRUE if success or FALSE if failed
     */                  
    public function update()
    {
        global $cfg;

        $sessObj    = Registry::get('session');

        $module     = addslashes(strip_tags(HTTP::getVar('module')));
        $desc       = addslashes(strip_tags(HTTP::getVar('desc')));
        $id         = HTTP::getVar('id');
        $res        = false;
        
        if ($id != $module) {            
            if ($this->exists($module)) {
                $sessObj->setVar('formError', "Module <b>$module</b> already exists!");
                return false;
            }
        }
        
        $this->_dbObj->beginTrans();
        
        try {
            $value      = array();
            $value[]    = "module_name         = '$module'";
            $value[]    = "module_description  = '$desc'";
        
            $this->_dbObj->updateRecord($this->_table, $value, array("module_name = '$id'"));
            $this->_dbObj->updateRecord($cfg['sys']['tblPrefix'] . '_task', 
                                        array("module_name = '$module'"), array("module_name = '$id'"));
            
            $this->_dbObj->commitTrans();
            
            $res = true;
        } catch (DAALException $e) {
            $this->_dbObj->rollbackTrans();
             
            Error::store($e->getMessage()); 
        }
        
        return $res;
    }
    
    /** 
     * Delete module
     * 
     * @return bool TRUE if success or FALSE if failed
     */                   
    public function delete($cb)
    {
        global $cfg;

        if (empty($cb)) return $res;
        
        $res = false;        
        if (strpos($cb, ':')) {
            $cb = explode(':', $cb);
      
            $this->_dbObj->beginTrans();
            
            try {
                $num = 0;
                for ($i = 0; $i < sizeof($cb); $i++) {
                    if (empty($cb[$i])) continue;

                    $this->_dbObj->deleteRecord($cfg['sys']['tblPrefix'] . '_task', array("module_name = '$cb[$i]'"));
                    $this->_dbObj->deleteRecord($this->_table, array("module_name = '$cb[$i]'"));
                    
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
            
                $this->_dbObj->deleteRecord($cfg['sys']['tblPrefix'] . '_task', array("module_name = '$cb'"));
                $this->_dbObj->deleteRecord($this->_table, array("module_name = '$cb'"));
                
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