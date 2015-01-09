<?php
/**
 * Role Model
 *
 * Last updated: May 28, 2012, 05:22 PM
 *
 * @author Lorensius W. L. T <lorenz@londatiga.net>
 *
 */
class RoleModel extends Model
{
    public function __construct()
    {
        global $cfg;

        parent::__construct();

        $this->_table = $cfg['sys']['tblPrefix'] . '_sys_group_task';
    }

    public function exist($group, $task)
    {
        return $this->find(array('filter' => array("group_id = '$group'", "task_id = '$task'")));
    }
}