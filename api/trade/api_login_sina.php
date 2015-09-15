<?php
/**
 *      [Haidao] (C)2013-2099 Dmibox Science and technology co., LTD.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      http://www.haidao.la
 *      tel:400-600-2042
 */
define('__APP__', '../../index.php');
$_GET['m'] = 'User';
$_GET['c'] = 'ThirdLogin';
$_filename = basename(__FILE__, '.php');
preg_match('/^api_([A-Za-z]+)_(\w*)/', $_filename, $matches);
$_GET['a'] = $matches[2];
$_GET['method'] = $matches[1];
$_GET['login_code'] = $matches[2];
require '../../index.php';