<?php
include 'init.php';

$dispatcher = new Dispatcher();
$dispatcher->dispatch();
$sysObj->finalize();

?>