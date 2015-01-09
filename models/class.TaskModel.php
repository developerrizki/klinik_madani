<?php
/**
 * Task Model
 *
 * Last updated: May 28, 2012, 05:22 PM
 *
 * @author Lorensius W. L. T <lorenz@londatiga.net>
 *
 */
class TaskModel extends Model
{
    public function __construct()
    {
        global $cfg;

        parent::__construct();

        $this->_table = $cfg['sys']['tblPrefix'] . '_sys_task';
    }

    public function getDetail($id)
    {
        return $this->find(array('filter' => array("task_id = '$id'")));
    }

    public function exist($id)
    {
        $detail = $this->getDetail($id);

        return ($detail) ? true : false;
    }

    public function existInModule($module, $task)
    {
        return $this->find(array('filter' => array("module_name = '$module'", "task_name = '$task'")));
    }
}