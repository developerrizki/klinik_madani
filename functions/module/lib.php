<?php
require_once ROOT_DIR . '/models/class.ModuleModel.php';

function getTaskList($module)
{
    $modelObj = new ModuleModel();
    $taskList = $modelObj->getTaskList($module);

    $str      = '';
    for ($i = 0; $i < sizeof($taskList); $i++) {
        $str .= $taskList[$i]->task_name . (($i == sizeof($taskList)-1) ? '' : ', ');
    }

    return $str;
}
?>