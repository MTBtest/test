<?php
/**
 *      [Haidao] (C)2013-2099 Dmibox Science and technology co., LTD.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      http://www.haidao.la
 *      tel:400-600-2042
 */
define('IN_APP', TRUE);
/* 编码定义 */
define('CHARSET', 'utf-8');
//程序目录
defined('DOC_ROOT') or define('DOC_ROOT', str_replace("\\", '/', dirname(__FILE__) ).'/');
/* 应用名称*/
define('APP_NAME', 'appliaction');
/* 应用目录*/
define('APP_PATH', DOC_ROOT.'appliaction/');
define('LIB_PATH', APP_PATH);
/* 扩展目录*/
define('EXTEND_PATH', APP_PATH.'Library/');
define('TAGLIB_PATH', APP_PATH.'Taglib/');
/* 配置文件目录*/
define('CONF_PATH', DOC_ROOT.'config/');
/* 模板目录 */
define('TMPL_PATH', DOC_ROOT.'template/');
/* 数据目录*/
define('RUNTIME_PATH', DOC_ROOT.'data/');
define('LOG_PATH', RUNTIME_PATH.'logs/');
define('TEMP_PATH', RUNTIME_PATH.'temp/');
define('CACHE_PATH', RUNTIME_PATH.'caches/');
define('DATA_PATH', RUNTIME_PATH.'data/');
/* 插件目录 */
define('PLUGIN_PATH', DOC_ROOT.'plugin/');
/* DEBUG & 开发者模式 */
define('APP_DEBUG', true);
include APP_PATH.'Framework/framework.php';