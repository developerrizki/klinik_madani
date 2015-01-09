<?php
/**
* ElGato PHP 5 Framework
*
* Last updated: May 22, 2011, 05:21 PM
*
* @package   rbac
* @author    Lorensius W. L. T <lorenz@londatiga.net>
* @version   1.1.0
* @copyright Copyright (c) 2010-2012 Lorensius W. L. T
*/

/**
* RBAC class
*
* @package   rbac
* @author    Lorensius W. L. T <lorenz@londatiga.net>
* @version   1.1.0
* @copyright Copyright (c) 2010-2012 Lorensius W. L. T
*
*/
class RBAC extends Model
{
   /**
    * Constructor.
    * Create a new instance of this class
    *
    * @return void
    */
   public function __construct()
   {
       global $cfg;

       parent::__construct();

       $this->_table = $cfg['sys']['tblPrefix'] . '_groups_tasks';
   }

   /**
    * Authorize access of a group to a specific task.
    *
    * @param array $groups Array of group id
    * @param string $module Module name
    * @param int $task Task id
    *
    * @return boolean TRUE if a authorized and vice versa
    */
   public function authorize($groups, $module, $task)
   {
       global $cfg;

       $length = sizeof($groups);

       $where  = ($length > 1) ? '(' : '';

       for ($i = 0; $i < $length; $i++) {
           $where .= "group_id = '$groups[$i]'" . (($i == $length- 1) ? '' : ' AND ');
       }

       $where .= ($length > 1) ? ')' : '';

       $sql = "SELECT
                           *
               FROM
                           " . $cfg['sys']['tblPrefix'] . "_sys_group_task
               JOIN
                           " . $cfg['sys']['tblPrefix'] . "_sys_task
               USING(task_id)
               WHERE
                           module_name = '$module'
                           AND
                           task_name = '$task'
                           AND
                           $where";

       $res = false;

       try {
           $this->_dbObj->query($sql);

           $res = ($this->_dbObj->getNumRows()) ? true : false;
       }  catch (DAALException $e) {
           Error::store('RBAC', $e->getMessage());
       }

       return $res;
   }
}