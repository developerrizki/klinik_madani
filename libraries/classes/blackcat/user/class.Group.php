<?php
/**
 * BlackCat PHP 5 Framework
 *
 * Last updated: June 18, 2010, 07:41 PM
 *
 * @package   system
 * @author    Lorensius W. L. T <lorenz@londatiga.net>
 * @version   1.0
 * @copyright Copyright (c) 2010 Lorensius W. L. T
 */

/**
 * Group handling class
 *
 * @package   user
 * @author    Lorensius W. L. T <lorenz@londatiga.net>
 * @version   1.0
 * @copyright Copyright (c) 2010 Lorensius W. L. T
 *
 */
class Group extends Model
{
    /**
     * Constructor.
     * Create a new instance of this class
     *
     * @param object $dbObj Database connection object
     *
     * @return void
     */
    public function __construct()
    {
        global $cfg;
        
        parent::__construct();
        
        $this->_table = $cfg['sys']['tblPrefix'] . '_group';
    }

    /**
     * Get group hash
     * 
     * @return array Group hash
     */                   
    public function getHash()
    {
        $res    = array();
        
        $data   = $this->findAll(array('orderby' => array('group_name'), 'sort' => 'ASC'));
        
        if (!empty($data)) {
            for ($i = 0; $i < sizeof($data); $i++) {
                $res[$data[$i]->group_name] = $data[$i]->group_name;
            }
        }
        
        return $res;
    }
       
    /**
     *  Get user list for specified group
     *  
     *  @param string $group Group name
     *  
     *  @return array User list 
     */                             
    public function getUserList($group)
    {
        global $cfg;
        
        $sql = "SELECT
                            user_id,
                            user_name
                FROM
                            " . $cfg['sys']['tblPrefix'] . "_user_group
                JOIN
                            " . $cfg['sys']['tblPrefix'] . "_user
                USING(user_id)
                WHERE
                            group_name = '$group'";

        $res = array();
        
        try {
            $this->_dbObj->query($sql);
            
            $res = ($this->_dbObj->getNumRows()) ? $this->_dbObj->fetchAll() : array();
        }  catch (DAALException $e) { Error::store($e->getMessage()); }

        return $res;
    }
  
    /**
     *  Get user hash of a group
     *  
     *  @param string $group Group name
     *  
     *  @return array User hash 
     */                             
    public function getUserHash($group)
    {
        global $cfg;
        
        $sql = "SELECT
                            user_id,
                            user_name
                FROM
                            " . $cfg['sys']['tblPrefix'] . "_user_group
                JOIN
                            " . $cfg['sys']['tblPrefix'] . "_user
                USING(user_id)
                WHERE
                            group_name = '$group'";

        $res = array();
        
        try {
            $this->_dbObj->query($sql);
              
            if ($this->_dbObj->getNumRows()) {
                $data = $this->_dbObj->fetchAll();

                for ($i = 0; $i < sizeof($data); $i++) {
                    $res[$data[$i]->user_id] = $data[$i]->user_name;
                }
            }
        } catch (DAALException $e) { Error::store($e->getMessage()); }

        return $res;
    }
    
      
    /**
     * Get group's detail
     * 
     * @param string $group Group name
     * 
     * @return array Group's detail
     */
    public function getDetail($group)
    {
        return $this->find(array('filter' => "group_name = '$group'"));
    }
    
    /** 
     * Check if group already exists in database
     * 
     * @param string $group Group name
     * 
     * @return bool TRUE if exists or FALSE vice versa
     */                             
    public function exists($group)
    {
        $detail = $this->getDetail($group);
        
        return (is_array($detail) && sizeof($detail)) ? true : false;    
    }   

    /**
     * Add new group
     * 
     * @return bool TRUE if success or FALSE if failed
     */                   
    public function insertRecord()
    {
        global $cfg;

        $sessObj    = Registry::get('session');
    
        $group      = addslashes(strip_tags(HTTP::getVar('group')));
        $desc       = addslashes(strip_tags(HTTP::getVar('desc')));
       
        if ($this->exists($group)) {
            $sessObj->setVar('formError', "Group <b>$group</b> already exists!");
            return false;
        }
        
        $value      = array();
        $value[]    = "group_name         = '$group'";
        $value[]    = "group_description  = '$desc'";
        
        return $this->insert($value);
    }
    
    /**
     * Update group
     * 
     * @return bool TRUE if success or FALSE if failed
     */                  
    public function updateRecord()
    {
        global $cfg;

        $sessObj    = Registry::get('session');

        $group      = addslashes(strip_tags(HTTP::getVar('group')));
        $desc       = addslashes(strip_tags(HTTP::getVar('desc')));
        $id         = HTTP::getVar('id');
   
        if ($id != $group) {            
            if ($this->exist($group)) {
                $sessObj->setVar('formError', "Group <b>$group</b> already exists!");
                return false;
            }
        }
        
        $value      = array();
        $value[]    = "group_name         = '$group'";
        $value[]    = "group_description  = '$desc'";

        return $this->update($value, array("group_name = '$id'"));
    }
    
    /** 
     * Delete group
     * 
     * @return bool TRUE if success or FALSE if failed
     */                   
    public function deleteRecord($cb)
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

                    $this->_dbObj->deleteRecord($cfg['sys']['tblPrefix'] . '_user_group', array("group_name = '$cb[$i]'"));
                    $this->_dbObj->deleteRecord($this->_table, array("group_name = '$cb[$i]'"));
                    
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
                $this->_dbObj->deleteRecord($cfg['sys']['tblPrefix'] . '_user_group', array("group_name = '$cb'"));
                $this->_dbObj->deleteRecord($this->_table, array("group_name = '$cb'"));
 
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