<?php
if(isset($_GET['echostr'])) exit($_GET['echostr']);
define('__APP__', '/index.php');
$_GET['m'] = 'Notify';
$_GET['c'] = 'Index';
$_GET['a'] = 'weixin';
require '../index.php';
?>