<?php
/**
 * ElGato PHP 5 Framework
 *
 * Last updated: January 08, 2009, 03:01 PM
 *
 * @package   View
 * @author    Lorensius W. L. T <lorenz@londatiga.net>
 * @version   1.0
 * @copyright Copyright (c) 2005-2009 Lorensius W. L. T
 */

/**
 * Menu handling class
 *
 * @package   View
 * @author    Lorensius W. L. T <lorenz@londatiga.net>
 * @version   1.0
 * @copyright Copyright (c) 2005-2009 Lorensius W. L. T
 *
 */
class Menu
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
        
        $this->_table = $cfg['sys']['tblPrefix'] . '_menu';
    }

    /**
     *  Get menu list
     *  
     *  @return array Menu list
     */                             
    public function getList()
    {
        global $cfg;
        
        $sql = 'SELECT
                            *
                FROM
                            ' . $this->_table . '
                ORDER BY
                            menu_order ASC';

        $res = array();
        
        try {
            $this->_dbObj->query($sql);
            
            $res = ($this->_dbObj->getNumRows()) ? $this->_dbObj->fetchAll() : array();
        } catch (DAALException $e) { Error::store($e->getMessage()); }

        return $res;
    }
  
    /**
     *  Get menu hash
     *  
     *  @return array Menu hash
     */                             
    public function getHash()
    {
        global $cfg;
        
        $sql = 'SELECT
                            *
                FROM
                            ' . $this->_table . '
                ORDER BY
                            menu_order ASC';

        $res = array();
        
        try {
            $this->_dbObj->query($sql);
            
            $data = $this->_dbObj->fetchAll();
            
            for ($i = 0; $i < sizeof($data); $i++) {
                $res[$data[$i]['menu_id']] = $data[$i]['menu_name'];
            }
        } catch (DAALException $e) { Error::store($e->getMessage()); }

        return $res;
    }
    
    /**
     * Get list of menu by group menu
     * 
     * @param int $group Group menu id
     * 
     * @return array Menu list
     */
    public function getListByGroup($group)
    {
        global $cfg;
        
        $sql = "SELECT
                            *
                FROM
                            " . $this->_table . "
                WHERE
                            gm_id = '$group'
                ORDER BY
                            menu_order ASC";

        $res = array();
        
        try {
            $this->_dbObj->query($sql);
            
            $res = ($this->_dbObj->getNumRows()) ? $this->_dbObj->fetchAll() : array();
        } catch (DAALException $e) { Error::store($e->getMessage()); }

        return $res;
    }
 
    /**
     * Get menu hash by group menu
     * 
     * @param int $group Group menu id
     *  
     * @return array Menu hash
     */                             
    public function getHashByGroup($group)
    {
        global $cfg;
        
        $sql = "SELECT
                            *
                FROM
                            " . $this->_table . "
                WHERE
                            gm_id = '$group'
                ORDER BY
                            menu_order ASC";

        $res = array();
        
        try {
            $this->_dbObj->query($sql);
            
            $data = $this->_dbObj->fetchAll();
            
            for ($i = 0; $i < sizeof($data); $i++) {
                $res[$data[$i]['menu_id']] = $data[$i]['menu_name'];
            }
        } catch (DAALException $e) { Error::store($e->getMessage()); }

        return $res;
    }

    /**
     * Get list of menu by user group
     * 
     * @param string $group User group id
     * 
     * @return array Menu list
     */
    public function getListByUserGroup($group)
    {
        global $cfg;
        
        $sql = "SELECT
                            *
                FROM
                            " . $this->_table . "
                JOIN
                            " . $cfg['sys']['tblPrefix'] . "_group_task
                USING(task_name)
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
     * Get menu's detail
     * 
     * @param int $id Menu id
     * 
     * @return array Menu's detail
     */                             
    public function getDetail($id)
    {
        global $cfg;
        
        $sql = "SELECT
                            *
                FROM
                            " . $this->_table . "
                WHERE
                            menu_id = '$id'";

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
     * @param int $parent Parent menu
     * 
     * @return int Next order number
     */                             
    public function getNextOrderNumber($group)
    {
        global $cfg;
        
        $sql = "SELECT
                            menu_order
                FROM
                            " . $this->_table . "
                WHERE
                            gm_id = '$group'
                ORDER BY
                            menu_order DESC
                LIMIT 0,1";

        $next = 1;
        
        try {
            $this->_dbObj->query($sql);
            
            $data = $this->_dbObj->fetch();
         
            $next = (is_array($data) && sizeof($data)) ? $data['menu_order'] + 1 : 1;
        } catch (DAALException $e) { Error::store($e->getMessage()); }

        return $next;   
    }
    
    /**
     * Add menu
     * 
     * @return bool TRUE if success or FALSE if failed
     */                   
    public function add()
    {
        global $cfg;

        $sessObj    = Registry::get('session');
  
        $name       = addslashes(strip_tags(HTTP::getVar('name')));
        $group      = HTTP::getVar('group');
        $task       = HTTP::getVar('task');
        $gm         = HTTP::getVar('gm');
        $menu       = HTTP::getVar('menu');
        $pos        = HTTP::getVar('pos');
        $enable     = HTTP::getVar('enable');
        $res        = false;

        $this->_dbObj->beginTrans();
      
        try {
            if ($pos == 'E') {
                $order  = $this->getNextOrderNumber($gm); 
            } elseif ($pos == 'S' || ($pos == 'M' && empty($menu))) {
            	$menus = $this->getListByGroup($gm);

	            $j = 2;
        	    for ($i = 0; $i < sizeof($menus); $i++) {
                     $this->_dbObj->updateRecord($this->_table,
        		                                 array("menu_order = '$j'") , 
                                                 array("menu_id = '" . $menus[$i]['menu_id'] . "'"));
        		     $j++;
        	    }

                $order = 1;
            } else {
                $menus = $this->getListByGroup($gm);
                
                $j     = 1; 
	            for ($i = 0; $i < sizeof($menus); $i++) { 
	                $this->_dbObj->updateRecord($this->_table,
		                                        array("menu_order = '$j'") , 
                                                array("menu_id = '" . $menus[$i]['menu_id'] . "'"));

        	        if ($menu == $menus[$i]['menu_id']) {
        	            $j++;
                	    $order = $j;
        	        }

	               $j++;
	            }
            }
            
            $id         = $this->_dbObj->getNextID($this->_table, 'menu_id');
                
            $value[]    = "menu_id      = '$id'";
            $value[]    = "task_id      = '$task'";
            $value[]    = "gm_id        = '$gm'";
            $value[]    = "menu_name    = '$name'";
            $value[]    = "menu_order   = '$order'";
            $value[]    = "menu_enabled = '$enable'";
    
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
     * Update menu
     * 
     * @return bool TRUE if success or FALSE if failed
     */   
    public function update()
    {
        global $cfg;
    
        $dbObj      = Registry::get('daal');
        $menuObj    = Registry::get('menu');
        $sessObj    = Registry::get('session');
        
        $name       = addslashes(strip_tags(HTTP::getVar('name')));
        $group      = HTTP::getVar('group');
        $task       = HTTP::getVar('task');
        $gm         = HTTP::getVar('gm');
        $menu       = HTTP::getVar('menu');
        $pos        = HTTP::getVar('pos');
        $enable     = HTTP::getVar('enable');
        $id         = HTTP::getVar('id');
    
        $res        = false;
    
        try {
            $value[]    = "menu_id      = '$id'";
            $value[]    = "task_id      = '$task'";
            $value[]    = "gm_id        = '$gm'";
            $value[]    = "menu_name    = '$name'";
            $value[]    = "menu_enabled = '$enable'";
    
            $this->_dbObj->updateRecord($this->_table, $value, array("menu_id = '$id'"));

            $res = true;
        } catch (DAALException $e) { Error::store($e->getMessage()); }
        
        return $res;
    }
    
    /**
     * Delete menu
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

                    $this->_dbObj->deleteRecord($this->_table, array("menu_id = '$cb[$i]'"));

                    $num++; 
                }
                
                $this->_dbObj->commitTrans();

                $res = true;
            } catch (DAALException $e) { $dbObj->rollbackTrans(); }
        } else {
            try {
                $this->_dbObj->deleteRecord($this->_table, array("menu_id = '$cb'"));

                $res = true;
            } catch (DAALException $e) { }
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
                    
                    $dbObj->updateRecord($cfg['sys']['tblPrefix'] . '_menu', array("menu_order = '$i'"), array("menu_id = '$id'"));
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