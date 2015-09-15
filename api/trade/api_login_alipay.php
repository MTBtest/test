<?php
/**
 *      [Haidao] (C)2013-2099 Dmibox Science and technology co., LTD.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      http://www.haidao.la
 *      tel:400-600-2042
 */
define('__APP__', '../../index.php');
$_filename = basename(__FILE__, '.php');
preg_match('/^api_([A-Za-z]+)_(\w*)/', $_filename, $matches);
$_GET['login_code'] = $matches[2];
require __APP__;
redirect(U('User/ThirdLogin/login_return',$_GET));