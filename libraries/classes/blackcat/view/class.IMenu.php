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
 * Intranet menu handling class
 *
 * @package   View
 * @author    Lorensius W. L. T <lorenz@londatiga.net>
 * @version   1.0
 * @copyright Copyright (c) 2005-2009 Lorensius W. L. T
 *
 */
class IMenu
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
        
        $this->_table = $cfg['sys']['tblPrefix'] . '_imenu';
    }

    /**
     *  Get menu list
     *  
     *  @return array Menu list
     */                             
    public function getList()
    {
        global $cfg;
        
        $sql = "SELECT
                            *
                FROM
                            " . $this->_table . "
                ORDER BY
                            imenu_id ASC";

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
        
        $sql = "SELECT
                            *
                FROM
                            " . $this->_table . "
                ORDER BY
                            imenu_level ASC";

        $res = array();
        
        try {
            $this->_dbObj->query($sql);
            
            $data = $this->_dbObj->fetchAll();
            
            for ($i = 0; $i < sizeof($data); $i++) {
                $res[$data[$i]['imenu_id']] = $data[$i]['imenu_label'];
            }
        } catch (DAALException $e) { Error::store($e->getMessage()); }

        return $res;
    }
    
    /**
     *  Get menu hash by group menu
     *
     *  @param int $gm Group menu
     *  
     *  @return array Menu hash
     */                             
    public function getHashByGroupMenu($gm)
    {
        global $cfg;
        
        $sql = "SELECT
                            *
                FROM
                            " . $this->_table . "
                WHERE
                            gm_id = '$gm'
                ORDER BY
                            imenu_level ASC";

        $res = array();
        
        try {
            $this->_dbObj->query($sql);
            
            $data = $this->_dbObj->fetchAll();
            
            for ($i = 0; $i < sizeof($data); $i++) {
                $res[$data[$i]['imenu_id']] = $data[$i]['imenu_label'];
            }
        } catch (DAALException $e) { Error::store($e->getMessage()); }

        return $res;
    }
    
    /**
     * Get list of menu by level
     * 
     * @param int $level Menu level
     * @param int $gmenu Group menu
     * 
     * @return array Menu list
     */
    public function getListByLevel($level, $gmenu)
    {
        global $cfg;
        
        $sql = "SELECT
                            *
                FROM
                            " . $this->_table . "
                JOIN
                            " . $cfg['sys']['tblPrefix'] . "_task
                USING(task_id)
                JOIN
                            " . $cfg['sys']['tblPrefix'] . "_module
                USING(module_name)
                WHERE
                            imenu_level = '$level'
                            AND
                            gm_id = '$gmenu'
                ORDER BY
                            imenu_order ASC";

        $res = array();
        
        try {
            $this->_dbObj->query($sql);
            
            $res = ($this->_dbObj->getNumRows()) ? $this->_dbObj->fetchAll() : array();
        } catch (DAALException $e) { Error::store($e->getMessage()); }

        return $res;
    }
 
    /**
     * Get menu hash by level
     * 
     * @param int $level Menu level
     * @param int $gmenu Group menu
     *  
     * @return array Menu hash
     */                             
    public function getHashByLevel($level, $gmenu)
    {
        global $cfg;
        
        $sql = "SELECT
                            *
                FROM
                            " . $this->_table . "
                JOIN
                            " . $cfg['sys']['tblPrefix'] . "_task
                USING(task_id)
                JOIN
                            " . $cfg['sys']['tblPrefix'] . "_module
                USING(module_name)
                WHERE
                            imenu_level = '$level'
                            AND
                            gm_id = '$gmenu'
                ORDER BY
                            imenu_order ASC";

        $res = array();
        
        try {
            $this->_dbObj->query($sql);
            
            $data = $this->_dbObj->fetchAll();
            
            for ($i = 0; $i < sizeof($data); $i++) {
                $res[$data[$i]['imenu_id']] = $data[$i]['imenu_label'];
            }
        } catch (DAALException $e) { Error::store($e->getMessage()); }

        return $res;
    }
      
    /**
     *  Get child menu
     *  
     *  @param int $parent Parent menu
     *            
     *  @return array Child menu
     */                             
    public function getChild($parent)
    {
        global $cfg;

        $sql = "SELECT
                            *
                FROM
                            " . $this->_table . "
                JOIN
                            " . $cfg['sys']['tblPrefix'] . "_task
                USING(task_id)
                JOIN
                            " . $cfg['sys']['tblPrefix'] . "_module
                USING(module_name)
                WHERE
                            imenu_parent = '$parent'
                ORDER BY
                            imenu_order ASC";

        $res = array();
        
        try {
            $this->_dbObj->query($sql);
            
            $res = ($this->_dbObj->getNumRows()) ? $this->_dbObj->fetchAll() : array();
        } catch (DAALException $e) { Error::store($e->getMessage()); }

        return $res;
    }
    
    /**
     * Get hash of child menu
     * 
     * @param $int $mid Parent menu
     * 
     * @return array Child menu hash
     */                             
    public function getChildHash($parent)
    {
        global $cfg;
        
        $sql = "SELECT
                            *
                FROM
                            " . $this->_table . "
                JOIN
                            " . $cfg['sys']['tblPrefix'] . "_task
                USING(task_id)
                JOIN
                            " . $cfg['sys']['tblPrefix'] . "_module
                USING(module_name)
                WHERE
                            imenu_parent = '$parent'
                ORDER BY
                            imenu_order ASC";

        $res = array();
        
        try {
            $this->_dbObj->query($sql);
            
            $data = $this->_dbObj->fetchAll();
            
            for ($i = 0; $i < sizeof($data); $i++) {
                $res[$data[$i]['imenu_id']] = $data[$i]['imenu_label'];
            }
        } catch (DAALException $e) { Error::store($e->getMessage()); }

        return $res;
    }
    
    /**
     * Get id of root menu
     * 
     * @param int $mid Menu id
     * 
     * @return int Root menu id
     */                             
    public function getRootId($mid)
    {
        global $cfg;
        
        $root = true;

        while ($root) {
            $menu   = $this->getDetail($mid);
            $root   = (empty($menu['imenu_parent'])) ? false : true;
            $mid    = (empty($menu['imenu_parent'])) ? $menu['imenu_id'] : $menu['imenu_parent'];
        }
        
        return $mid;
    }
    
    /** 
     * Get parent menu
     * 
     * @param int $mid Menu id
     *           
     * @return array Parent menu
     */                   
    public function getParent($mid)
    {
        global $cfg;
        
        $root   = true;
        $parent = array();
        while ($root) {
            $menu     = $this->getDetail($mid);
            $root     = (empty($menu['imenu_parent'])) ? false : true;
            $parent[] = $menu;
            $mid      = $menu['imenu_parent'];
        }
        
        return $parent;        
    }
    
    /**
     * Get menu id by label
     * 
     * @param string $name Menu label   
     * 
     * @return int Menu id
     */                          
    public function getIdByName($name)
    {
        global $cfg;
        
        $sql = "SELECT
                            *
                FROM
                            " . $this->_table . "
                WHERE
                            imenu_label LIKE '%$name%'
                ORDER BY
                            imenu_order ASC";

        $res = '';
        
        try {
            $this->_dbObj->query($sql);
            
            $data = $this->_dbObj->fetchAll();
            $res  = (is_array($data) && sizeof($data)) ? $data[0]['imenu_id'] : '';  
        } catch (DAALException $e) { Error::store($e->getMessage()); }

        return $res;    
    }

    /**
     * Get detail of menu
     * 
     * @param int $mid Menu id
     * 
     * @return array Detail of menu
     */                             
    public function getDetail($mid)
    {
        global $cfg;
        
        $sql = "SELECT
                            *
                FROM
                            " . $this->_table . "
                JOIN
                            " . $cfg['sys']['tblPrefix'] . "_task
                USING(task_id)
                JOIN
                            " . $cfg['sys']['tblPrefix'] . "_module
                USING(module_name)
                JOIN
                            " . $cfg['sys']['tblPrefix'] . "_gmenu
                USING(gm_id)
                WHERE
                            imenu_id = '$mid'";

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
     * @param int $gm Group menu
     * 
     * @return int Next order number
     */                             
    public function getNextOrderNumber($parent, $gm)
    {
        global $cfg;
        
        if (empty($parent)) {
            $list = $this->getListByLevel(1, $gm);
            $next = (is_array($list) && sizeof($list)) ? $list[sizeof($list)-1]['imenu_order'] + 1 : 1;
         
            return $next;
        } else { 
            $sql = "SELECT
                                *
                    FROM
                                " . $this->_table . "
                    WHERE
                                imenu_parent = '$parent'
                    ORDER BY
                                imenu_order DESC
                    LIMIT 0,1";

            $next = 1;
     
            try {
                $this->_dbObj->query($sql);
            
                $data = $this->_dbObj->fetch();
            
                $next = (is_array($data) && sizeof($data)) ? $data['imenu_order'] + 1 : 1;
            } catch (DAALException $e) { Error::store($e->getMessage()); }

            return $next;
        }    
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
        
        $label      = addslashes(strip_tags(HTTP::getVar('label')));
        $gm         = HTTP::getVar('gm');
        $pmenu      = HTTP::getVar('pmenu');
        $menu       = HTTP::getVar('menu');
        $task       = HTTP::getVar('task');
        $pos        = HTTP::getVar('pos');
        $enable     = HTTP::getVar('enable');
        $res        = false;
        
        $pmenu     = (empty($pmenu)) ? 0 : $pmenu;
        
        //get level
        if (empty($pmenu)) {
            $level = 1;
        } else {
            $detail = $this->getDetail($pmenu);
            $level  = $detail['imenu_level'] + 1;
        }

        $this->_dbObj->beginTrans();

        try {
            if ($pos == 'E') {
                $order  = $this->getNextOrderNumber($pmenu, $gm);
            } elseif ($pos == 'S' || ($pos == 'M' && empty($menu))) {
            	$menus = (empty($pmenu)) ? $this->getListByLevel(1, $gm) : $this->getChild($pmenu);

	            $j = 2;
        	    for ($i = 0; $i < sizeof($menus); $i++) {
                     $this->_dbObj->updateRecord($this->_table,
                                                 array("imenu_order  = '$j'") ,
                                                 array("imenu_id = '" . $menus[$i]['imenu_id'] . "'"));
        		     $j++;
        	    }

                $order = 1;
            } else { 
                $menus = (empty($pmenu)) ? $this->getListByLevel(1, $gm) : $this->getChild($pmenu);
                
                $j  = 1;
	            for ($i = 0; $i < sizeof($menus); $i++) {
	                $this->_dbObj->updateRecord($this->_table,
                                                array("imenu_order  = '$j'") ,
                                                array("imenu_id = '" . $menus[$i]['imenu_id'] . "'"));

        	        if ($menu == $menus[$i]['imenu_id']) {
        	            $j++;
                	    $order = $j;
        	        }

	               $j++;
	            }
            }
     
            $id         = $this->_dbObj->getNextID($this->_table, 'imenu_id');
   
            $value      = array();
            $value[]    = "imenu_id             = '$id'";
            $value[]    = "gm_id                = $gm";
            $value[]    = "task_id              = $task";
            $value[]    = "imenu_label          = '$label'";
            $value[]    = "imenu_level          = '$level'";
            $value[]    = "imenu_order          = '$order'";
            $value[]    = "imenu_parent         = '$pmenu'";

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

        $sessObj    = Registry::get('session');
        
        $label      = addslashes(strip_tags(HTTP::getVar('label')));
        $enable     = HTTP::getVar('enable');
        $id         = HTTP::getVar('id');
        $res        = false;

        try {
            $value      = array();
            $value[]    = "imenu_label      = '$label'";
            $value[]    = "imenu_enabled    = '$enable'";
        
            $this->_dbObj->updateRecord($this->_table, $value, array("imenu_id = '$id'"));

            $res = true;
        } catch (DAALException $e) { Error::store($e->getMessage()); }
        
        return $res;
    }
    
    /**
     * Delete menu
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

                    $this->_dbObj->deleteRecord($this->_table, array("imenu_id = '$cb[$i]'"));

                    $num++; 
                }
                
                $this->_dbObj->commitTrans();

                $res = true;
            } catch (DAALException $e) {
                Error::store($e->getMessage());
                
                $this->_dbObj->rollbackTrans();
            }
        } else {
            try {
                $this->_dbObj->deleteRecord($this->_table, array("imenu_id = '$cb'"));

                $res = true;
            } catch (DAALException $e) { Error::store($e->getMessage()); }
        }
        
        return $res;
    }
    
    /**
     * Order menu
     * 
     * @return bool TRUE if success or FALSE if failed
     */
    public function order($menu)
    {
        global $cfg;

        $res = false;
          
        if (is_array($menu) && sizeof($menu)) {
            $this->_dbObj->beginTrans();
            
            try {    
                for ($i = 1; $i <= sizeof($menu); $i++) {
                    $id = $menu[$i-1];
                    
                    $this->_dbObj->updateRecord($this->_table, array("imenu_order = '$i'"), array("imenu_id = '$id'"));
                }
                
                $this->_dbObj->commitTrans();
                
                $res = true;
            } catch (DAALException $e) { 
                Error::store($e->getMessage());
                
                $this->_dbObj->rollbackTrans();
            }
        } else { return true; }    
    
        return $res;
    }
}