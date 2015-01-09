<?php
/**
 * ElGato PHP 5 Framework
 *
 * Last updated: Aug 30, 2009, 11:03 PM
 *
 * @package   View
 * @author    Lorensius W. L. T <lorenz@londatiga.net>
 * @version   1.0
 * @copyright Copyright (c) 2005-2009 Lorensius W. L. T
 */

/**
 * Group menu handling class
 *
 * @package   View
 * @author    Lorensius W. L. T <lorenz@londatiga.net>
 * @version   1.0
 * @copyright Copyright (c) 2005-2009 Lorensius W. L. T
 *
 */
class GMenu
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
        
        $this->_table = $cfg['sys']['tblPrefix'] . '_gmenu';
    }

    /**
     *  Get group menu list
     *  
     *  @return array Group menu list
     */                             
    public function getList()
    {
        global $cfg;
        
        $sql = 'SELECT
                            *
                FROM
                            ' . $this->_table . '
                ORDER BY
                            gm_order ASC';

        $res = array();
        
        try {
            $this->_dbObj->query($sql);
            
            $res = ($this->_dbObj->getNumRows()) ? $this->_dbObj->fetchAll() : array();
        } catch (DAALException $e) { Error::store($e->getMessage()); }

        return $res;
    }
  
    /**
     *  Get group menu hash
     *  
     *  @return array Group menu hash
     */                             
    public function getHash()
    {
        global $cfg;
        
        $sql = 'SELECT
                            *
                FROM
                            ' . $this->_table . '
                ORDER BY
                            gm_order ASC';

        $res = array();
        
        try {
            $this->_dbObj->query($sql);
            
            $data = $this->_dbObj->fetchAll();
            
            for ($i = 0; $i < sizeof($data); $i++) {
                $res[$data[$i]['gm_id']] = $data[$i]['gm_name'];
            }
        } catch (DAALException $e) { Error::store($e->getMessage()); }

        return $res;
    }


    /**
     *  Get group menu list by user group
     *       
     *  @param string $group User group name
     *       
     *  @return array Group menu list
     */                             
    public function getListByUserGroup($group)
    {
        global $cfg;
        
        $sql = "SELECT
                            *
                FROM
                            " . $this->_table . "
                WHERE
                            group_name = '$group'
                ORDER BY
                            gm_order ASC";

        $res = array();
        
        try {
            $this->_dbObj->query($sql);
            
            $res = ($this->_dbObj->getNumRows()) ? $this->_dbObj->fetchAll() : array();
        } catch (DAALException $e) { Error::store($e->getMessage()); }

        return $res;
    }
 
     /**
     *  Get group menu hash by user group
     *       
     *  @param string $group User group name
     *       
     *  @return array Group menu hash
     */  
    public function getHashByUserGroup($group)
    {
        global $cfg;
        
        $sql = "SELECT
                            *
                FROM
                            " . $this->_table . "
                JOIN
                            " . $cfg['sys']['tblPrefix'] . "_group
                USING(group_name)
                WHERE
                            group_name = '$group'
                ORDER BY
                            gm_order ASC";

        $res = array();
       
        try {
            $this->_dbObj->query($sql);
            
            $data = $this->_dbObj->fetchAll();
            
            for ($i = 0; $i < sizeof($data); $i++) {
                $res[$data[$i]['gm_id']] = $data[$i]['gm_name'];
            }
        } catch (DAALException $e) { Error::store($e->getMessage()); }

        return $res;
    }
       
    /**
     * Get group menu's detail
     * 
     * @param int $mid Group menu id
     * 
     * @return array Group menu's detail
     */                             
    public function getDetail($id)
    {
        global $cfg;
        
        $sql = "SELECT
                            *
                FROM
                            " . $this->_table . "
                WHERE
                            gm_id = '$id'";

        $res = array();
        
        try {
            $this->_dbObj->query($sql);
            
            $res = ($this->_dbObj->getNumRows()) ? $this->_dbObj->fetch() : array();
        } catch (DAALException $e) { Error::store($e->getMessage()); }

        return $res;
    }
    
    /**
     * Get next order number
     * 
     * @param string $group Group name
     * 
     * @return int Next order number
     */                                                     
    public function getNextOrderNumber($group)
    {
        global $cfg;
        
        $sql = "SELECT
                            *
                FROM
                            " . $this->_table . "
                WHERE
                            group_name = '$group'
                ORDER BY
                            gm_order DESC
                LIMIT 0,1";

        $next = 1;
        
        try {
            $this->_dbObj->query($sql);
            
            $data = $this->_dbObj->fetch();
            
            $next = (is_array($data) && sizeof($data)) ? $data['gm_order'] + 1 : 1;
        } catch (DAALException $e) { Error::store($e->getMessage()); }

        return $next;
    }    
    
    /**
     * Add group menu
     * 
     * @return bool TRUE if success or FALSE if failed
     */                   
    public function add()
    {
        global $cfg;

        $sessObj    = Registry::get('session');
  
        $name       = addslashes(strip_tags(HTTP::getVar('name')));
        $group      = HTTP::getVar('group');
        $gm         = HTTP::getVar('gm');
        $pos        = HTTP::getVar('pos');
        $res        = false;

        $this->_dbObj->beginTrans();
       
        try {
            if ($pos == 'E') {
                $order  = $this->getNextOrderNumber($group);
            } elseif ($pos == 'S' || ($pos == 'M' && empty($gm))) {
            	$gmenus = $this->getListByUserGroup($group);

	            $j = 2;
        	    for ($i = 0; $i < sizeof($gmenus); $i++) {
                     $this->_dbObj->updateRecord($this->_table,
        		                                 array("gm_order = '$j'") , 
                                                 array("gm_id = '" . $gmenus[$i]['gm_id'] . "'"));
        		     $j++;
        	    }

                $order = 1;
            } else {
                $gmenus = $this->getListByUserGroup($group);
                
                $j     = 1;
	            for ($i = 0; $i < sizeof($gmenus); $i++) {
	                $this->_dbObj->updateRecord($this->_table,
		                                        array("gm_order = '$j'") , 
                                                array("gm_id = '" . $gmenus[$i]['gm_id'] . "'"));

        	        if ($gm == $gmenus[$i]['gm_id']) {
        	            $j++;
                	    $order = $j;
        	        }

	               $j++;
	            }
            }
            
            $id         = $this->_dbObj->getNextID($this->_table, 'gm_id');
                
            $value[]    = "gm_id        = '$id'";
            $value[]    = "group_name   = '$group'";
            $value[]    = "gm_name      = '$name'";
            $value[]    = "gm_order     = '$order'";
    
            $this->_dbObj->insertRecord($this->_table, $value);

            $this->_dbObj->commitTrans();
            
            $res = true;
        } catch (DAALException $e) { 
            Error::store($e->getMessage()); 
            
            $this->_dbObj->rollbackTrans();
        }
        
        return $res;
    }
    
    /**
     * Update group menu
     * 
     * @return bool TRUE if success or FALSE if failed
     */   
    public function update()
    {
        global $cfg;
    
        $sessObj    = Registry::get('session');
  
        $name       = addslashes(strip_tags(HTTP::getVar('name')));
        $group      = HTTP::getVar('group');
        $id         = HTTP::getVar('id');
    
        $res        = false;
    
        try {
            $value[] = "gm_name       = '$name'";
            $value[] = "group_name    = '$group'";

            $this->_dbObj->updateRecord($this->_table, $value, array("gm_id = '$id'"));

            $res = true;
        } catch (DAALException $e) { Error::store($e->getMessage()); }
        
        return $res;
    }
    
    /**
     * Delete group menu
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
                    
                    $this->_dbObj->deleteRecord($cfg['sys']['tblPrefix'] . '_imenu', array("gm_id = '$cb[$i]'"));
                    $this->_dbObj->deleteRecord($this->_table, array("gm_id = '$cb[$i]'"));
                    
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
            
                $this->_dbObj->deleteRecord($cfg['sys']['tblPrefix'] . '_imenu', array("gm_id = '$cb'"));
                $this->_dbObj->deleteRecord($this->_table, array("gm_id = '$cb'"));

                $this->_dbObj->commitTrans();
                
                $res = true;
            } catch (DAALException $e) {
                Error::store($e->getMessage()); 
            
                $this->_dbObj->rollbackTrans();
            }
        }
        
        return $res;
    }
    
    /**
     * Order menu
     * 
     * @return bool TRUE if success or FALSE if failed
     */
    static public function order()
    {
        global $cfg;

        $dbObj      = Registry::get('daal');
        $menu       = HTTP::getVar('menu');
        $res        = false;
          
        if (is_array($menu) && sizeof($menu)) {
            $dbObj->beginTrans();
            
            try {    
                for ($i = 1; $i <= sizeof($menu); $i++) {
                    $id = $menu[$i-1];
                    
                    $dbObj->updateRecord($cfg['sys']['tblPrefix'] . '_gmenu', array("gm_order = '$i'"), array("gm_id = '$id'"));
                }
                
                $dbObj->commitTrans();
                
                $res = true;
            } catch (DAALException $e) { 
                Error::store($e->getMessage());
                
                $dbObj->rollbackTrans();
            }
        } else { return true; }    
    
        return $res;
    }                                                
}