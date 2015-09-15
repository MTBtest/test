<?php
//测试链接数据库
function check_mysql()
{
	$is_connect = false;

	$db_host = url_get('db_address');
	$db_name = url_get('db_name');
	$db_user = url_get('db_user');
	$db_pwd  = url_get('db_pwd');

	if($db_host != '' && function_exists('mysql_connect')){
		$conn = @mysql_connect($db_host,$db_user,$db_pwd);
	}

	if($conn)
	{
		if(!@mysql_select_db($db_name)){
			$sqlstr = "create database `{$db_name}`";
			if(!@mysql_query($sqlstr)){
				echo json_encode(array('status'=>0,'info'=>'创建数据库失败,请检查权限'));
				exit();
			}else{
				echo json_encode(array('status'=>1,'info'=>'创建数据库成功'));
				exit();
			}
		}else{
			echo json_encode(array('status'=>1,'info'=>'连接数据库成功'));
			exit();
		}
	}
	else
	{
		echo json_encode(array('status'=>0,'info'=>'数据库连接失败,请检查用户名和密码'));
	}
}

//$conn = mysql_connect("localhost","root","password") or die("无法连接数据库");
//    mysql_create_db("webjx") or die("无法创建数据库");
//    $sqlstr = "create database other_webjx";
//    mysql_query($sqlstr) or die("无法创建,一般请检查权限什么的");

//解析备份文件中的SQL
function parseSQL($fileName,$status = true){
	global $db_pre;
	$lines=file($fileName);
	$lines[0]=str_replace(chr(239).chr(187).chr(191),"",$lines[0]);//去除BOM头
	$flage=true;
	$sqls=array();
	$sql="";
	foreach($lines as $line)
	{
		$line=trim($line);
		$char=substr($line,0,1);
		if($char!='#' && strlen($line)>0)
		{
			$prefix=substr($line,0,2);
			switch($prefix)
			{
				case '/*':
				{
				$flage=(substr($line,-3)=='*/;'||substr($line,-2)=='*/')?true:false;
				break 1;
				}
				case '--': break 1;
				default : 
				{				
					if($flage)
					{
						$sql.=$line;
						if(substr($line,-1)==";")
						{
							$sql = str_replace('hd_',$db_pre,$sql);
							$sqls[]=$sql;
							$sql="";
						}
					}
					if(!$flage)$flage=(substr($line,-3)=='*/;'||substr($line,-2)=='*/')?true:false;
				}
			}
		}
	}
	return $sqls;
}
function execSQL($sqls){
	$flag=true;
	if(is_array($sqls))
	{
		$total = count($sqls);
		$num = 0;
		foreach($sqls as $sql)
		{
			$result   = mysql_query($sql);
			if($flag) $num++;			
			$percent = ($num/$total)*100;
			@sqlCallBack($sql,$result,$percent,$is_test);
		}	
	}
	return $flag;
}

//sql回调函数
function sqlCallBack($sql,$result,$percent,$is_test = false){
	global $db_pre;
	if(preg_match_all("/(create|drop|insert)([^`]+`)(\w+)(`.*)/i",$sql,$out)){
		$sql = $out[1][0].$out[2][0].$per.$out[4][0].$out[5][0];
		$op = strtolower($out[1][0]);
		$message = '';
		//动作
		if($op=='create' && $sql)$message= "创建表 ".($out[3][0])." ";
		else if($op=='drop' && $sql)$message= "校验表 ".($out[3][0])." ";
		else if($op=='insert' && $sql)$message= "写入表 ".($out[3][0])." ";
		//判断sql执行结果
		if($result){
			$isError  = false;
			$message .= '...成功';
		}else{
			$isError  = true;
			$message .= '...失败! '.mysql_error();
		}
		
		$percent = $percent == 100 ? 99 :sprintf("%.2f",$percent) ;
		$return_info = array(
			'isError' => $isError,
			'message' => $message,
			'percent' => $percent
		);
	}
	if($return_info){
		showProgress($return_info);
		usleep(1000);
	}
}

//安装mysql数据库
function install_sql()
{
	global $db_pre;

	//安装配置信息
	$db_address   = url_get('db_address');
	$db_user      = url_get('db_user');
	$db_pwd       = url_get('db_pwd');
	$db_name      = url_get('db_name');
	$db_pre       = url_get('db_pre');
	
	$admin_user   = url_get('admin_user');
	$admin_pwd    = url_get('admin_pwd');
	$admin_email  = url_get('admin_email');
	
	$site_name 	  = url_get('site_name');
	$site_keywords 	= url_get('site_keywords');
	$site_description    	= url_get('site_description');
	
	//链接mysql数据库
	$mysql_link = @mysql_connect($db_address,$db_user,$db_pwd);

	if(!$mysql_link)
	{
		showProgress(array('isError' => true,'message' => 'mysql链接失败'.mysql_error()));
	}else{
		showProgress(array('isError' => false,'message' => '连接数据库成功'));
	}

	if(mysql_get_server_info($mysql_link) > '5.0.1') {
		mysql_query("SET sql_mode=''", $mysql_link);
	}

	//检测SQL安装文件
	$sql_file = ROOT_PATH.'./install/hdshop.sql';
	if(!file_exists($sql_file))
	{
		showProgress(array('isError' => true,'message' => '安装的SQL文件'.basename($sql_file).'不存在'));
	}else{
		showProgress(array('isError' => false,'message' => '解析SQL文件'));
	}

	//检测测试数据SQL文件
//	$sql_test_file = ROOT_PATH.'./install/hd_test.sql';
//	if(@$install_type == 'all' && !file_exists($sql_test_file))
//	{
//		showProgress(array('isError' => true,'message' => '测试数据SQL文件'.basename($sql_test_file).'不存在'));
//	}

	//执行SQL,创建数据库操作
	mysql_query("set names 'UTF8'");

	if(!@mysql_select_db($db_name))
	{
		$DATABASESQL = '';
		if(version_compare(mysql_get_server_info(), '4.1.0', '>='))
		{
	    	$DATABASESQL = "DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci";
		}
		if(!mysql_query('CREATE DATABASE `'.$db_name.'` '.$DATABASESQL))
		{
			showProgress(array('isError' => true,'message' => '用户权限受限，创建'.$db_name.'数据库失败，请手动创建数据表'));
		}
	}

	if(!@mysql_select_db($db_name))
	{
		showProgress(array('isError' => true,'message' => $db_name.'数据库不存在'.mysql_error()));
	}

	//安装SQL
	$sqls = parseSQL($sql_file);
	execSQL($sqls);
	
	//安装地区数据
	$sql_region_file = ROOT_PATH.'./install/region.sql';
	if(!file_exists($sql_region_file))
	{
		//showProgress(array('isError' => true,'message' => '地区数据SQL文件'.basename($sql_region_file).'不存在','percent' => 99.6));
	}else{
		showProgress(array('isError' => false,'message' => '更新地区完成','percent' => 99.6));
		$sqls = parseSQL($sql_region_file);
		execSQL($sqls);
	}
	
	//更新升级数据
	$sql_update_sql = ROOT_PATH.'./install/update.sql';
	if(!file_exists($sql_update_sql))
	{
		//showProgress(array('isError' => true,'message' => '无需更新','percent' => 98));
	}else{
		showProgress(array('isError' => false,'message' => '更新升级数据完成','percent' => 98));
		$sqls = parseSQL($sql_update_sql);
		execSQL($sqls);
	}
	

	//安装测试数据
	if(@$install_type == 'all')
	{
		parseSQL($sql_test_file);
	}

	//插入管理员数据
	$valid = generate_password(10);
	$adminSql = 'insert into `'.$db_pre.'admin_user` (`name`,`password`,`email`,`add_time`,`valid`) values ("'.$admin_user.'","'.md5($admin_pwd.$valid).'","'.$admin_email.'","'.time().'","'.$valid.'")';
	if(!mysql_query($adminSql))
	{
		showProgress(array('isError' => true,'message' => '创建管理员失败'.$adminSql.mysql_error(),'percent' => 99));
	}

	//写入数据库配置文件
	$configDefFile = ROOT_PATH.'./install/db.php';
	$configFile    = ROOT_PATH.'./config/db.php';
	$updateData    = array(
		'{DB_PREFIX}' 	=> $db_pre,
		'{DB_HOST}' 	=> $db_address,
		'{DB_USER}'    	=> $db_user,
		'{DB_PWD}'     	=> $db_pwd,
		'{DB_NAME}'    	=> $db_name,
	);

	$is_success = create_config($configFile,$configDefFile,$updateData);
	if(!$is_success)
	{
		showProgress(array('isError' => true,'message' => '更新数据库配置文件失败','percent' => 99.8));
	}else{
		showProgress(array('isError' => false,'message' => '更新数据库配置文件','percent' => 99.8));
	}

	//写入基本配置文件
	//写入数据库配置文件
	$configDefFile = ROOT_PATH.'./install/site.php';
	$configFile    = ROOT_PATH.'./config/site.php';
	$updateData    = array(
		'{SITE_NAME}' 		=> $site_name,
		'{SITE_KEYWORDS}' 	=> $site_keywords,
		'{SITE_DESCRIPTION}'=> $site_description,
		'{SITE_KEY}'		=> generate_password(),
	);

	$is_success = create_config($configFile,$configDefFile,$updateData);
	if(!$is_success)
	{
		showProgress(array('isError' => true,'message' => '更新基础配置文件失败','percent' => 99.9));
	}else{
		showProgress(array('isError' => false,'message' => '更新基础配置文件','percent' => 99.9));
	}

	//执行完毕
	showProgress(array('isError' => false,'message' => '安装完成','percent' => 100,'admin_user'=>$admin_user));

}

//输出json数据
function showProgress($return_info)
{
	echo '<script type="text/javascript">parent.update_progress('.JSON::encode($return_info).');</script>';
	flush();
	if($return_info['isError'] == true)
	{
		exit;
	}
}

//根据默认模板生成config文件
function create_config($config_file,$config_def_file,$updateData)
{
	$defaultData = file_get_contents($config_def_file);
	$configData  = str_replace(array_keys($updateData),array_values($updateData),$defaultData);
	return file_put_contents($config_file,$configData);
}

/*
 * 生成随机密码
 */
function generate_password( $length = 8 ) {
    $chars = 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ23456789';
    $password = '';
    for ($i = 0; $i < $length; $i++){
        $password .= $chars[mt_rand(0, strlen($chars) - 1)];
    }
    return $password;
}

//查询解决方案
function configInfo($item)
{
	$data = array(
		'mysql'=> 'http://www.baidu.com/#wd=php%20mysql%E6%89%A9%E5%B1%95&rsv_spt=1&rsv_bp=1&ie=utf-8&tn=baiduhome_pg&inputT=4031&f=8&bs=php%20mysql%E7%BB%84%E4%BB%B6&rsv_sug3=16&rsv_sug4=653&rsv_sug1=22&rsv_sug2=0&rsv_sug=2',
		'gd'=> 'http://www.baidu.com/#wd=php%20%E5%BC%80%E5%90%AF%20gd&rsv_spt=1&rsv_bp=1&ie=utf-8&tn=baiduhome_pg&inputT=1513&f=8&bs=php%20gd&rsv_sug3=23&rsv_sug4=914&rsv_sug1=34&rsv_sug2=0',
		'xml'=> 'http://www.baidu.com/#wd=php%20%E5%BC%80%E5%90%AF%20xml&rsv_spt=1&rsv_bp=1&ie=utf-8&tn=baiduhome_pg&inputT=1262&f=8&bs=php%20%E5%BC%80%E5%90%AF%20gd&rsv_sug3=27&rsv_sug4=1014&rsv_sug1=36&rsv_sug2=0&rsv_sug=1',
		'session'=> 'http://www.baidu.com/#wd=php%20%E5%BC%80%E5%90%AF%20session&rsv_spt=1&rsv_bp=1&ie=utf-8&tn=baiduhome_pg&inputT=7586&f=8&bs=php%20%E5%BC%80%E5%90%AF%20xml&rsv_sug3=34&rsv_sug4=1245&rsv_sug1=47&rsv_sug2=0',
		'iconv'=> 'http://www.baidu.com/#wd=php%20%E5%BC%80%E5%90%AF%20iconv&rsv_spt=1&rsv_bp=1&ie=utf-8&tn=baiduhome_pg&inputT=878&f=8&bs=php%20%E5%BC%80%E5%90%AF%20session&rsv_sug3=36&rsv_sug4=1315&rsv_sug1=49&rsv_n=2&rsv_sug=1',
		'zip'=> 'http://www.baidu.com/#wd=php%20%E5%BC%80%E5%90%AF%20zip&rsv_spt=1&rsv_bp=1&ie=utf-8&tn=baiduhome_pg&inputT=1823&f=8&bs=php%20%E5%BC%80%E5%90%AF%20iconv&rsv_sug3=43&rsv_sug4=1506&rsv_sug1=54&rsv_sug=2&rsv_sug2=0',
		'curl'=> 'http://www.baidu.com/#wd=php%20%E5%BC%80%E5%90%AF%20curl&rsv_spt=1&rsv_bp=1&ie=utf-8&tn=baiduhome_pg&inputT=886&f=8&bs=php%20%E5%BC%80%E5%90%AF%20zip&rsv_sug3=45&rsv_sug4=1587&rsv_sug1=58&rsv_n=2',
		'OpenSSL'=> 'http://www.baidu.com/#wd=php%20%E5%BC%80%E5%90%AF%20OpenSSL&rsv_spt=1&rsv_bp=1&ie=utf-8&tn=baiduhome_pg&inputT=909&f=8&bs=php%20%E5%BC%80%E5%90%AF%20curl&rsv_sug3=47&rsv_sug4=1667&rsv_sug1=61&rsv_n=2',
		'sockets'=> 'http://www.baidu.com/#wd=php%20%E5%BC%80%E5%90%AF%20sockets&rsv_spt=1&rsv_bp=1&ie=utf-8&tn=baiduhome_pg&inputT=862&f=8&bs=php%20%E5%BC%80%E5%90%AF%20OpenSSL&rsv_sug3=50&rsv_sug4=1767&rsv_sug1=63&rsv_n=2&rsv_sug=1',
		'safe_mode'=> 'http://www.baidu.com/#wd=php%20safe_mode%20%E5%85%B3%E9%97%AD&rsv_spt=1&rsv_bp=1&ie=utf-8&tn=baiduhome_pg&inputT=885&f=8&bs=php%20safe_mode%20%E5%85%B3%E9%97%AD&rsv_sug=1&rsv_sug3=7&rsv_sug4=237&rsv_sug1=11&rsv_n=2',
		'allow_url_fopen'=> 'http://www.baidu.com/#wd=php%20%E5%BC%80%E5%90%AF%20allow_url_fopen&rsv_spt=1&rsv_bp=1&ie=utf-8&tn=baiduhome_pg&inputT=1088&f=8&bs=php%20%E5%BC%80%E5%90%AF%20sockets&rsv_sug3=52&rsv_sug4=1844&rsv_sug1=65&rsv_n=2&rsv_sug=1',
		'memory_limit'=> 'http://www.baidu.com/#wd=php%20%E5%BC%80%E5%90%AF%20memory_limit&rsv_spt=1&rsv_bp=1&ie=utf-8&tn=baiduhome_pg&inputT=2508&f=8&bs=php%20%E5%BC%80%E5%90%AF%20allow_url_fopen&rsv_sug3=54&rsv_sug4=1921&rsv_sug1=69&rsv_n=2&rsv_sug=1',
		'asp_tags'=> 'http://www.baidu.com/#wd=asp_tags%20%E5%85%B3%E9%97%AD&rsv_spt=3&rsv_bp=1&ie=utf-8&tn=baiduhome_pg&inputT=1244&f=8&bs=php%20asp_tags%20%E5%85%B3%E9%97%AD&rsv_sug3=69&rsv_sug4=2382&rsv_sug1=75&rsv_sug=1&rsv_sug2=0',
	);

	if(isset($data[$item]))
	{
		return "<a href='".$data[$item]."' target='_blank'>立即解决</a>";
	}
}