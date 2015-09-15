<?php
return array(
    'DEFAULT_C_LAYER'      => 'Controller',
    'VAR_GROUP'            => 'm',
    'VAR_MODULE'           => 'c',
    'VAR_ACTION'           => 'a',
    'OUTPUT_ENCODE'        => TRUE,
	'APP_GROUP_LIST'       => '*',
    'DEFAULT_GROUP'        => 'Goods',
    'DEFAULT_MODULE'       => 'Index',
    'DEFAULT_ACTION'       => 'index',
    'ERROR_PAGE'		   => '?m=admin&c=public&a=404',

    'URL_MODEL'            => 0,
    'VAR_SESSION_ID'       => 'PHPSESSID',
    'uploadpath'           => './uploadfile/',
    'USER_AUTH_KEY'        => 'uid',
    'VAR_PAGE'             => 'page',
    'LOG_EXCEPTION_RECORD' => TRUE,
    'TMPL_CACHE_ON'        => TRUE, //开启模板缓存
    'URL_CASE_INSENSITIVE' => TRUE,
    'DATABASE_BACKUP_PATH' => 'backup',
    'SYSTEM_HOOK_LIST'     => 'system',//系统内置hook文件列表
    'LOAD_EXT_CONFIG'      => 'site,db,reg,info,mail,safe,upfile,mobile,cloud,theme,version,moneylog,weixin,kuaidi',
    'SHOW_PAGE_TRACE'	   =>TRUE,
);