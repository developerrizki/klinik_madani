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
 * Public menu handling class
 *
 * @package   View
 * @author    Lorensius W. L. T <lorenz@londatiga.net>
 * @version   1.0
 * @copyright Copyright (c) 2005-2009 Lorensius W. L. T
 *
 */
class PMenu
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
        
        $this->_table = $cfg['sys']['tblPrefix'] . '_pmenu';
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
                            pmenu_id ASC";

        $res = array();
        
        try {
            $this->_dbObj->query($sql);
            
            $res = ($this->_dbObj->getNumRows()) ? $this->_dbObj->fetchAll() : array();
        } catch (DAALException $e) { Error::store($e->getMessage()); }

        return $res;
    }
    
    /**
     *  Get menu has
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
                            pmenu_level ASC";

        $res = array();
        
        try {
            $this->_dbObj->query($sql);
            
            $data = $this->_dbObj->fetchAll();
            
            for ($i = 0; $i < sizeof($data); $i++) {
                $res[$data[$i]['pmenu_id']] = $data[$i]['pmenu_label'];
            }
        } catch (DAALException $e) { Error::store($e->getMessage()); }

        return $res;
    }
    
    /**
     * Get list of menu by level
     * 
     * @param int $level Menu level
     * 
     * @return array Menu list
     */
    public function getListByLevel($level)
    {
        global $cfg;
        
        $sql = "SELECT
                            *
                FROM
                            " . $this->_table . "
                WHERE
                            pmenu_level = '$level'
                ORDER BY
                            pmenu_order ASC";

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
     *  
     * @return array Menu hash
     */                             
    public function getHashByLevel($level)
    {
        global $cfg;
        
        $sql = "SELECT
                            *
                FROM
                            " . $this->_table . "
                WHERE
                            pmenu_level = '$level'
                ORDER BY
                            pmenu_order ASC";

        $res = array();
        
        try {
            $this->_dbObj->query($sql);
            
            $data = $this->_dbObj->fetchAll();
            
            for ($i = 0; $i < sizeof($data); $i++) {
                $res[$data[$i]['pmenu_id']] = $data[$i]['pmenu_label'];
            }
        } catch (DAALException $e) { Error::store($e->getMessage()); }

        return $res;
    }
      
    /**
     *  Get child menu
     *  
     *  @param int $mid Parent menu
     *            
     *  @return array Child menu
     */                             
    public function getChild($mid='')
    {
        global $cfg;
        
        if (empty($mid)) {
        	$list = $this->getListByLevel(1);
        	$mid  = $list[0]['pmenu_id'];
        }
        
        $sql = "SELECT
                            *
                FROM
                            " . $this->_table . "
                WHERE
                            pmenu_parent = '$mid'
                ORDER BY
                            pmenu_order ASC";

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
    public function getChildHash($mid='')
    {
        global $cfg;
        
        $sql = "SELECT
                            *
                FROM
                            " . $this->_table . "
                WHERE
                            pmenu_parent = '$mid'
                ORDER BY
                            pmenu_order ASC";

        $res = array();
        
        try {
            $this->_dbObj->query($sql);
            
            $data = $this->_dbObj->fetchAll();
            
            for ($i = 0; $i < sizeof($data); $i++) {
                $res[$data[$i]['pmenu_id']] = $data[$i]['pmenu_label'];
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
            $root   = (empty($menu['pmenu_parent'])) ? false : true;
            $mid    = (empty($menu['pmenu_parent'])) ? $menu['pmenu_id'] : $menu['pmenu_parent'];
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
            $root     = (empty($menu['pmenu_parent'])) ? false : true;
            $parent[] = $menu;
            $mid      = $menu['pmenu_parent'];
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
                            pmenu_label LIKE '%$name%'
                ORDER BY
                            pmenu_order ASC";

        $res = '';
        
        try {
            $this->_dbObj->query($sql);
            
            $data = $this->_dbObj->fetchAll();
            $res  = (is_array($data) && sizeof($data)) ? $data[0]['pmenu_id'] : '';  
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
                WHERE
                            pmenu_id = '$mid'";

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
    public function getNextOrderNumber($parent='')
    {
        global $cfg;
        
        if (empty($parent)) {
            $list = $this->getListByLevel(1);
            $next = (is_array($list) && sizeof($list)) ? $list[sizeof($list)-1]['pmenu_order'] + 1 : 1;
            
            return $next;
        } else { 
            $sql = "SELECT
                                *
                    FROM
                                " . $this->_table . "
                    WHERE
                                pmenu_parent = '$mid'
                    ORDER BY
                                pmenu_order DESC
                    LIMIT 0,1";

            $next = 1;
        
            try {
                $this->_dbObj->query($sql);
            
                $data = $this->_dbObj->fetch();
            
                $next = (is_array($data) && sizeof($data)) ? $data['pmenu_order'] + 1 : 1;
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
        $link       = HTTP::getVar('link');
        $page       = HTTP::getVar('page');
        $pmenu      = HTTP::getVar('pmenu');
        $pos        = HTTP::getVar('pos');
        $menu       = HTTP::getVar('menu');
        $res        = false;
        
        if (empty($link) && empty($page)) {
            $sessObj->setVar('formError', 'Please define menu content first!');
            return false;
        }

        //get level
        if (empty($pmenu)) {
            $level = 1;
        } else {
            $detail = $this->getDetail($pmenu);
            $level  = $detail['pmenu_level'] + 1;
        }

        $this->_dbObj->beginTrans();
        
        try {
            if ($pos == 'E') {
                $order  = $this->getNextOrderNumber($pmenu);
            } elseif ($pos == 'S' || ($pos == 'M' && empty($menu))) {
            	$menus = (empty($pmenu)) ? $this->getListByLevel(1) : $this->getChild($pmenu);

	            $j = 2;
        	    for ($i = 0; $i < sizeof($menus); $i++) {
                     $this->_dbObj->updateRecord($this->_table,
                                                 array("pmenu_order = '$j'") ,
                                                 array("pmenu_id = '" . $menus[$i]['pmenu_id'] . "'"));
        		     $j++;
        	    }

                $order = 1;
            } else {
                $menus = (empty($pmenu)) ? $this->getListByLevel(1) : $this->getChild($pmenu);
                
                $j  = 1;
	            for ($i = 0; $i < sizeof($menus); $i++) {
	                $this->_dbObj->updateRecord($this->_table,
                                                array("pmenu_order = '$j'") ,
                                                array("pmenu_id = '" . $menus[$i]['pmenu_id'] . "'"));

        	        if ($menu == $menus[$i]['pmenu_id']) {
        	            $j++;
                	    $order = $j;
        	        }

	               $j++;
	            }
            }
            
            $id         = $this->_dbObj->getNextID($this->_table, 'pmenu_id');
            $page       = (empty($page)) ? 'null' : "'$page'";
            $pmenu      = (empty($pmenu)) ? 0 : $pmenu;
            
            $value[]    = "pmenu_id             = '$id'";
            $value[]    = "page_id              = $page";
            $value[]    = "pmenu_parent         = '$pmenu'";
            $value[]    = "pmenu_link           = '$link'";
            $value[]    = "pmenu_order          = '$order'";
            $value[]    = "pmenu_level          = '$level'";
            $value[]    = "pmenu_label          = '$label'";

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
        $link       = HTTP::getVar('link');
        $page       = HTTP::getVar('page');
        $id         = HTTP::getVar('id');
        $page       = (empty($page)) ? 'null' : "'$page'";
        $res        = false;
    
        try {
            $value[]    = "page_id          = $page";
            $value[]    = "pmenu_label      = '$label'";
            $value[]    = "pmenu_link       = '$link'";
        
            $this->_dbObj->updateRecord($this->_table, $value, array("pmenu_id = '$id'"));

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

                    $this->_dbObj->deleteRecord($this->_table, array("pmenu_id = '$cb[$i]'"));

                    $num++; 
                }
                
                $this->_dbObj->commitTrans();

                $res = true;
            } catch (DAALException $e) { $this->_dbObj->rollbackTrans(); }
        } else {
            try {
                $this->_dbObj->deleteRecord($this->_table, array("pmenu_id = '$cb'"));

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
    public function order($menu)
    {
        global $cfg;

        $res = false;
          
        if (is_array($menu) && sizeof($menu)) {
            $this->_dbObj->beginTrans();
            
            try {    
                for ($i = 1; $i <= sizeof($menu); $i++) {
                    $id = $menu[$i-1];
                    
                    $this->_dbObj->updateRecord($this->_table, array("pmenu_order = '$i'"), array("pmenu_id = '$id'"));
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