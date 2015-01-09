<?php
/**
 * ElGato PHP 5 Framework
 *
 * Last updated: Sept 05, 2009, 10:50 AM
 *
 * @package   View
 * @author    Lorensius W. L. T <lorenz@londatiga.net>
 * @version   1.0
 * @copyright Copyright (c) 2005-2009 Lorensius W. L. T
 */

/**
 * Page handling class
 *
 * @package   View
 * @author    Lorensius W. L. T <lorenz@londatiga.net>
 * @version   1.0
 * @copyright Copyright (c) 2005-2009 Lorensius W. L. T
 *
 */
class Page
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
     * @param object$this->_dbObj Database connection object
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
        
        $this->_table = $cfg['sys']['tblPrefix'] . '_page';
    }
    
    /**
     * Get page list
     *
     * @return array List of pages
     */
    public function getList($limit)
    {
        $limit = (!empty($limit)) ? "LIMIT 0,$limit" : '';
        
        $sql = "SELECT
                        *
                FROM
                        " . $this->_table . "
                ORDER BY
                        page_id ASC 
                $limit";
                      
        $res = array();
          
        try {
            $this->_dbObj->execute($sql);
            
            $res = ($dbObj->getNumRows()) ?$this->_dbObj->fetchAll() : array();
        } catch (DAALException $e) { Error::store($e->getMessage()); }
        
        return $res;
    }

    /**
     * Get page hash
     *
     * @return array Hash of pages
     */
    public function getHash()
    {
        $limit = (!empty($limit)) ? "LIMIT 0,$limit" : '';
        
        $sql = "SELECT
                        *
                FROM
                        " . $this->_table . "
                ORDER BY
                        page_id ASC 
                $limit";
                      
        $res = array();
          
        try {
            $this->_dbObj->execute($sql);
            
            $data = $this->_dbObj->fetchAll();
            
            for ($i = 0; $i < sizeof($data); $i++) {
                $res[$data[$i]['page_id']] = $data[$i]['page_name'];
            }
        } catch (DAALException $e) { Error::store($e->getMessage()); }
        
        return $res;
    }
    
    /**
     * Get page detail
     * 
     * @param int $id Page's id
     *           
     * @return array Page's detail
     */
    public function getDetail($id)
    { 
        $sql   = "SELECT
                            *                            
                  FROM
                            " . $this->_table . "
                  WHERE
                            page_id = '$id'";
                            
        $res = array();
          
        try {
            $this->_dbObj->execute($sql);
            
            $res = ($this->_dbObj->getNumRows()) ?$this->_dbObj->fetch() : array();
        } catch (DAALException $e) { Error::store($e->getMessage()); }
        
        return $res;  
    }

    /**
     * Add page
     * 
     * @return bool TRUE if success or FALSE if failed
     */                   
    public function add()
    {
        $name       = addslashes(strip_tags(HTTP::getVar('name')));
        $content    = addslashes(HTTP::getVar('elm'));
        $res        = false;
    
        try {    
            $id         =$this->_dbObj->getNextID($this->_table, 'page_id');
            
            $value      = array();
            $value[]    = "page_id             = '$id'";
            $value[]    = "page_name           = '$name'";
            $value[]    = "page_content        = '$content'";
            $value[]    = "page_created_by     = '" . USER_ID . "'";
            $value[]    = "page_created_date   = '" . date('Y-m-d H:i:s') . "'";
            $value[]    = "page_updated_by     = '" . USER_ID . "'";
            $value[]    = "page_updated_date   = '" . date('Y-m-d H:i:s') . "'";
            
           $this->_dbObj->insertRecord($this->_table, $value);

            $res = true;
        } catch (DAALException $e) { Error::store($e->getMessage()); }
        
        return $res;
    }

    /**
     * Update page
     * 
     * @return bool TRUE if success or FALSE if failed
     */   
    public function update()
    {
        $name       = addslashes(strip_tags(HTTP::getVar('name')));
        $content    = addslashes(HTTP::getVar('elm'));
        $id         = HTTP::getVar('id');        
        $res        = false;
        
        try {
            $value      = array();
            $value[]    = "page_name           = '$name'";
            $value[]    = "page_content        = '$content'";
            $value[]    = "page_updated_by     = '" . USER_ID . "'";
            $value[]    = "page_updated_date   = '" . date('Y-m-d H:i:s') . "'";
      
           $this->_dbObj->updateRecord($this->_table, $value, array("page_id = '$id'"));

            $res     = true;
        } catch (DAALException $e) { Error::store($e->getMessage()); }
        
        return $res;
    }
    
    /**
     * Delete page
     *
     * @var mixed $cb Item to delete
     * 
     * @return bool TRUE if success or FALSE if failed
     */   
    public function delete($cb)
    {
        $res = false;
      
        if (empty($cb)) return $res;
        
        if (strpos($cb, ':')) {
            $cb = explode(':', $cb);
      
            $this->_dbObj->beginTrans();
            
            try {
                $num = 0;
                for ($i = 0; $i < sizeof($cb); $i++) {
                    if (empty($cb[$i])) continue;

                    $this->_dbObj->deleteRecord($this->_table, array("page_id = '$cb[$i]'"));

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
                $this->_dbObj->deleteRecord($this->_table, array("page_id = '$cb'"));

                $res = true;
            } catch (DAALException $e) { Error::store($e->getMessage()); }
        }
        
        return $res;
    }         
}