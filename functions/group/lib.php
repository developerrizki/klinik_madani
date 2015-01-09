<?php
require_once ROOT_DIR . '/models/class.GroupModel.php';

function getUserList($group)
{
    $groupObj = new GroupModel();
    $userList = $groupObj->getUserList($group);

    $str      = '';
    for ($i = 0; $i < sizeof($userList); $i++) {
        $str .= $userList[$i]->user_name . (($i == sizeof($userList)-1) ? '' : ', ');
    }

    return $str;
}
?>