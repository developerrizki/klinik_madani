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
 * Task class
 *
 * @package   RBAC
 * @author    Lorensius W. L. T <lorenz@londatiga.net>
 * @version   1.0
 * @copyright Copyright (c) 2005-2009 Lorensius W. L. T
 *
 */
class Task
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
        
        $this->_table = $cfg['sys']['tblPrefix'] . '_task';
    }
    
    /**
     * Get task's detail
     * 
     * @param int$task Task Id
     * 
     * @return array Task's detail
     */
    public function getDetail($task)
    {
        global $cfg;
        
        $sql = "SELECT
                        *
                FROM
                        " . $this->_table . "
                WHERE
                        task_id = '$task'";
        
        $res = array();
        
        try {
            $this->_dbObj->query($sql);
            
            $res = ($this->_dbObj->getNumRows()) ? $this->_dbObj->fetch() : array();
        } catch (DAALException $e) { Error::store($e->getMessage()); }
        
        return $res;
    }
 
    /**
     * Check if a task already exists in database
     * 
     * @param string $module Module name
     * @param string $task Task name
     * 
     * @return bool TRUE if exists or FALSE vice versa
     */                               
    public function exists($module, $task)
    {
        global $cfg;
        
        $sql = "SELECT
                        *
                FROM
                        " . $this->_table . "
                WHERE
                        module_name = '$module'
                        AND
                        task_name = '$task'";
        
        $res = array();
        
        try {
            $this->_dbObj->query($sql);
            
            $res = ($this->_dbObj->getNumRows()) ? true : false;
        } catch (DAALException $e) { Error::store($e->getMessage()); }
        
        return $res;
    }
  
    /**
     * Get task id by module name and task name
     * 
     * @param string $module Module name
     * @param string $task Task name
     * 
     * @return int Task id
     */                                  
    public function getId($module, $task)
    {
        global $cfg;
        
        $sql = "SELECT
                        task_id
                FROM
                        " . $this->_table . "
                WHERE
                        module_name = '$module'
                        AND
                        task_name = '$task'";
   
        $res = '';
        
        try {
            $this->_dbObj->query($sql);
            
            $data = $this->_dbObj->fetch();
            $res  = $data['task_id'];
        } catch (DAALException $e) { Error::store($e->getMessage()); }
        
        return $res;
    }
      
    /**
     * Get task list by module and group
     * 
     * @param string $module Module name
     * @param string $group Group name
     * 
     * @return array Task hash
     */                                  
    public function getHashByModuleGroup($module, $group)
    {
        global $cfg;
        
        $sql = "SELECT
                        task_name,
                        task_id
                FROM
                        " . $cfg['sys']['tblPrefix'] . "_group_task
                JOIN
                        " . $this->_table . "
                USING(task_id)
                WHERE
                        group_name = '$group'
                        AND
                        module_name = '$module'
                ORDER BY
                        task_name ASC"; 
     
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
     * Add new task
     * 
     * @return void
     */                   
    public function add()
    {
        global $cfg;

        $sessObj    = Registry::get('session');
    
        $module     = addslashes(strip_tags(HTTP::getVar('module')));
        $task       = addslashes(strip_tags(HTTP::getVar('task')));
        $res        = false;
        
        if ($this->exists($module, $task)) {
            $sessObj->setVar('formError', "Task <b>$task</b> already exists in module <b>$module</b>!");
            return false;
        }
        
        try {    
            $id         = $this->_dbObj->getNextID($this->_table, 'task_id');
            
            $value      = array();
            $value[]    = "task_id      = '$id'";
            $value[]    = "module_name  = '$module'";
            $value[]    = "task_name    = '$task'";
    
            $this->_dbObj->insertRecord($this->_table, $value);

            $res = true;
        } catch (DAALException $e) { Error::store($e->getMessage()); }
        
        return $res;
    }
    
    /**
     * Update task
     * 
     * @return bool TRUE if success or FALSE if failed
     */                  
    public function update()
    {
        global $cfg;

        $sessObj    = Registry::get('session');
        $taskObj    = Registry::get('task');
        
        $module     = addslashes(strip_tags(HTTP::getVar('module')));
        $task       = addslashes(strip_tags(HTTP::getVar('task')));
        $id         = HTTP::getVar('id');
        $res        = false;
        
        $detail     = $taskObj->getDetail($id);
        
        if ($detail['module_name'] != $module || $detail['task_name'] != $task) {            
            if ($this->exists($module, $task)) {
                $sessObj->setVar('formError', "Task <b>$task</b> already exists in module <b>$module</b>!");
                return false;
            }
        }
  
        try {
            $value      = array();
            $value[]    = "task_name    = '$task'";
            $value[]    = "module_name  = '$module'";
        
            $this->_dbObj->updateRecord($this->_table, $value, array("task_id = '$id'"));
     
            $res        = true;
        } catch (DAALException $e) {
            $this->_dbObj->rollbackTrans();
             
            Error::store($e->getMessage()); 
        }
        
        return $res;
    }
    
    /** 
     * Delete task
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
                    
                    $this->_dbObj->deleteRecord($cfg['sys']['tblPrefix'] . '_group_task', array("task_id = '$cb[$i]'"));
                    $this->_dbObj->deleteRecord($cfg['sys']['tblPrefix'] . '_imenu', array("task_id = '$cb[$i]'"));
                    $this->_dbObj->deleteRecord($this->_table, array("task_id = '$cb[$i]'"));

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
                
                $this->_dbObj->deleteRecord($cfg['sys']['tblPrefix'] . '_group_task', array("task_id = '$cb'"));
                $this->_dbObj->deleteRecord($cfg['sys']['tblPrefix'] . '_imenu', array("task_id = '$cb'"));
                $this->_dbObj->deleteRecord($this->_table, array("task_id = '$cb'"));
                
                $this->_dbObj->commitTrans();
                
                $res  = true;
            } catch (DAALException $e) {
                Error::store($e->getMessage());
                
                $this->_dbObj->rollbackTrans();
            }
        }
        
        return $res;
    }  
}