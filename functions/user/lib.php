<?php
function getGroupList($group)
{
    $userObj   = new User();
    $groupList = $userObj->getGroupList($group);

    $str       = '';

    for ($i = 0; $i < sizeof($groupList); $i++) {
        $str .= $groupList[$i]->group_name . (($i == sizeof($groupList)-1) ? '' : ', ');
    }

    return $str;
}

function formatLastLog($time, $host)
{
    if (!$time) return 'Never logged in';

    return date('d/m/Y H:i', strtotime($time)) . ((!$host) ? '' : " from $host");
}
?>