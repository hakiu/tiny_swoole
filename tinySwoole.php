<?php

define('APP_PATH', realpath(__DIR__));

error_reporting(E_ALL ^ E_NOTICE);
date_default_timezone_set('Asia/Chongqing');

require APP_PATH.'/core/TinySwoole.php';

$tinySwoole = new TinySwoole();
$tinySwoole->bootstrap()->init()->run();