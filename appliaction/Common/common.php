<?php
/**
 * 系统公共库文件
 * 主要定义系统公共函数库
 */

/**
 * 获取缓存
 
 * @param  string $file 文件名
 * @param  string $dir  目录名
 * @return mixed
 */
function getcache($file, $dir = NULL) {
	 $fileName = (!is_null($dir) ? 'caches_'.$dir.'/'.$file : $file);
	 return F($fileName);
}

/**
 * 设置缓存
 
 * @param  string	$file	缓存名
 * @param  mixed	 $value  缓存值
 * @param  string	$dir	 目录名
 * @return mixed
 */
function setcache($file, $value = NULL, $dir = NULL) {
	 $fileName = (!is_null($dir) ? 'caches_'.$dir.'/'.$file : $file);
	 return F($fileName, $value);
}

/* 删除缓存文件 */
function delcache($file, $dir = NULL) {
	 $fileName = (!is_null($dir) ? 'caches_'.$dir.'/'.$file : $file);
	 return F($fileName, NULL);
}

function daddslashes($string, $force = 1) {
	 if(is_array($string)) {
		  $keys = array_keys($string);
		  foreach($keys as $key) {
				$val = $string[$key];
				unset($string[$key]);
				$string[addslashes($key)] = daddslashes($val, $force);
		  }
	 } else {
		  $string = addslashes($string);
	 }
	 return $string;
}

function dstripslashes($string) {
	if(empty($string)) return $string;
	if(is_array($string)) {
		foreach($string as $key => $val) {
			$string[$key] = dstripslashes($val);
		}
	} else {
		$string = stripslashes($string);
	}
	return $string;
}

function dhtmlspecialchars($string, $flags = null) {
	if(is_array($string)) {
		foreach($string as $key => $val) {
			$string[$key] = dhtmlspecialchars($val, $flags);
		}
	} else {
		if($flags === null) {
			$string = str_replace(array('&', '"', '<', '>'), array('&amp;', '&quot;', '&lt;', '&gt;'), $string);
			if(strpos($string, '&amp;#') !== false) {
				$string = preg_replace('/&amp;((#(\d{3,5}|x[a-fA-F0-9]{4}));)/', '&\\1', $string);
			}
		} else {
			if(PHP_VERSION < '5.4.0') {
				$string = htmlspecialchars($string, $flags);
			} else {
				if(strtolower(CHARSET) == 'utf-8') {
					$charset = 'UTF-8';
				} else {
					$charset = 'ISO-8859-1';
				}
				$string = htmlspecialchars($string, $flags, $charset);
			}
		}
	}
	return $string;
}

/**
 * 获取跳转地址
 */
function dreferer($default = '') {
	 $referer = !empty($_GET['referer']) ? $_GET['referer'] : $_SERVER['HTTP_REFERER'];
	 $referer = substr($referer, -1) == '?' ? substr($referer, 0, -1) : $referer;
	 return $referer;
}

/**
 * 随机字符串
 *
 * @return bool
 **/
function random($length, $numeric = 0) {
	 $seed = base_convert(md5(microtime().$_SERVER['DOCUMENT_ROOT']), 16, $numeric ? 10 : 35);
	 $seed = $numeric ? (str_replace('0', '', $seed).'012340567890') : ($seed.'zZ'.strtoupper($seed));
	 if($numeric) {
		  $hash = '';
	 } else {
		  $hash = chr(rand(1, 26) + rand(0, 1) * 32 + 64);
		  $length--;
	 }
	 $max = strlen($seed) - 1;
	 for($i = 0; $i < $length; $i++) {
		  $hash .= $seed{mt_rand(0, $max)};
	 }
	 return $hash;
}

/**
 * 获取类库地址
 * @param  [type] $libname [description]
 * @param  string $folder  [description]
 * @return [type]			 [description]
 */
function libfile($libname, $folder = '', $suffix = '.class.php', $import = TRUE) {
    $libpath = EXTEND_PATH.$folder;
    if(strstr($libname, '/')) {
        list($pre, $name) = explode('/', $libname);
        $path = "{$libpath}{$pre}/{$pre}_{$name}";
    } else {
        $path = "{$libpath}{$libname}";
    }
    $result = (file_exists($path.$suffix)) ? realpath($path.$suffix) : false;
    if ($result == TRUE && $import === TRUE) {
        require_cache($path.$suffix);
    }
    return $result;
}
/**
 * JS动态加载
 */
function jsfile($name){
	libfile('jspack');
	$js = new jspack();
	return $js->import(JS_PATH,$name);
}

/**
 * 函数钩子
 * @return [type] [description]
 */
function runhook($hookid, $param, $type = 'string') {
	 libfile('hook');
	 $hook =  new hook();
	 return $hook->run($hookid, $param, $type);
}

/**
 * 生成SEO
 * @param $catid		  栏目ID
 * @param $title		  标题
 * @param $description  描述
 * @param $keyword		关键词
 */
function seo($catid = '', $title = '', $description = '', $keyword = '') {
	if (!empty($title))$title = strip_tags($title);
	if (!empty($description)) $description = strip_tags($description);
	if (!empty($keyword)) $keyword = str_replace(' ', ',', strip_tags($keyword));
	$cat = array();
	if (!empty($catid)) {
		$categorys = getcache('goods_category', 'goods');
		$cat = $categorys[$catid];
	}	
	$site_title = getconfig('site_name');
	$site_subtitle = getconfig('site_subtitle');
	$site_description = getconfig('site_description');
	if($site_subtitle) {
		$site_title .= ' - '.$site_subtitle;
	}
	$seo['site_title'] = $site_title . ' - Powered by Haidao';
	$seo['keyword'] = !empty($keyword) ? $keyword : getconfig('site_keywords');
	$seo['description'] = isset($description) && !empty($description) ? $description : (isset($cat['descript']) && !empty($cat['descript']) ? $cat['descript'] : (isset($site_description) && !empty($site_description) ? $site_description : ''));
	$seo['title'] =  (isset($title) && !empty($title) ? $title.' - ' : '').(isset($cat['title']) && !empty($cat['title']) ? $cat['title'].' - ' : (isset($cat['name']) && !empty($cat['name']) ? $cat['name'].' - ' : ''));
	foreach ($seo as $k=>$v) {
		$seo[$k] = str_replace(array("\n","\r"),	'', $v);
	}
	return $seo;
}

/**
 * 远程请求函数
 */
function _dfsockopen($url, $limit = 0, $post = '', $cookie = '', $bysocket = FALSE, $ip = '', $timeout = 15, $block = TRUE, $encodetype  = 'URLENCODE', $allowcurl = TRUE, $position = 0) {
	$return = '';
	$matches = parse_url($url);
	$scheme = $matches['scheme'];
	$host = $matches['host'];
	$path = $matches['path'] ? $matches['path'].($matches['query'] ? '?'.$matches['query'] : '') : '/';
	$port = !empty($matches['port']) ? $matches['port'] : 80;

	if(function_exists('curl_init') && function_exists('curl_exec') && $allowcurl) {
		$ch = curl_init();
		$ip && curl_setopt($ch, CURLOPT_HTTPHEADER, array("Host: ".$host));
		curl_setopt($ch, CURLOPT_URL, $scheme.'://'.($ip ? $ip : $host).':'.$port.$path);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		if($post) {
			curl_setopt($ch, CURLOPT_POST, 1);
			if($encodetype == 'URLENCODE') {
				curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
			} else {
				parse_str($post, $postarray);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $postarray);
			}
		}
		if($cookie) {
			curl_setopt($ch, CURLOPT_COOKIE, $cookie);
		}
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
		$data = curl_exec($ch);
		$status = curl_getinfo($ch);
		$errno = curl_errno($ch);
		curl_close($ch);
		if($errno || $status['http_code'] != 200) {
			return;
		} else {
			return !$limit ? $data : substr($data, 0, $limit);
		}
	}

	if($post) {
		$out = "POST $path HTTP/1.0\r\n";
		$header = "Accept: */*\r\n";
		$header .= "Accept-Language: zh-cn\r\n";
		$boundary = $encodetype == 'URLENCODE' ? '' : '; boundary='.trim(substr(trim($post), 2, strpos(trim($post), "\n") - 2));
		$header .= $encodetype == 'URLENCODE' ? "Content-Type: application/x-www-form-urlencoded\r\n" : "Content-Type: multipart/form-data$boundary\r\n";
		$header .= "User-Agent: $_SERVER[HTTP_USER_AGENT]\r\n";
		$header .= "Host: $host:$port\r\n";
		$header .= 'Content-Length: '.strlen($post)."\r\n";
		$header .= "Connection: Close\r\n";
		$header .= "Cache-Control: no-cache\r\n";
		$header .= "Cookie: $cookie\r\n\r\n";
		$out .= $header.$post;
	} else {
		$out = "GET $path HTTP/1.0\r\n";
		$header = "Accept: */*\r\n";
		$header .= "Accept-Language: zh-cn\r\n";
		$header .= "User-Agent: $_SERVER[HTTP_USER_AGENT]\r\n";
		$header .= "Host: $host:$port\r\n";
		$header .= "Connection: Close\r\n";
		$header .= "Cookie: $cookie\r\n\r\n";
		$out .= $header;
	}

	$fpflag = 0;
	if(!$fp = @fsocketopen(($ip ? $ip : $host), $port, $errno, $errstr, $timeout)) {
		$context = array(
			'http' => array(
				'method' => $post ? 'POST' : 'GET',
				'header' => $header,
				'content' => $post,
				'timeout' => $timeout,
			),
		);
		$context = stream_context_create($context);
		$fp = @fopen($scheme.'://'.($ip ? $ip : $host).':'.$port.$path, 'b', false, $context);
		$fpflag = 1;
	}

	if(!$fp) {
		return '';
	} else {
		stream_set_blocking($fp, $block);
		stream_set_timeout($fp, $timeout);
		@fwrite($fp, $out);
		$status = stream_get_meta_data($fp);
		if(!$status['timed_out']) {
			while (!feof($fp) && !$fpflag) {
				if(($header = @fgets($fp)) && ($header == "\r\n" ||  $header == "\n")) {
					break;
				}
			}

			if($position) {
				for($i=0; $i<$position; $i++) {
					$char = fgetc($fp);
					if($char == "\n" && $oldchar != "\r") {
						$i++;
					}
					$oldchar = $char;
				}
			}

			if($limit) {
				$return = stream_get_contents($fp, $limit);
			} else {
				$return = stream_get_contents($fp);
			}
		}
		@fclose($fp);
		return $return;
	}
}

/**
 * xss过滤函数
 *
 * @param $string
 * @return string
 */
function remove_xss($string) { 
	 $string = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S', '', $string);

	 $parm1 = Array('javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link', 'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer', 'layer', 'bgsound', 'title', 'base');

	 $parm2 = Array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload');

	 $parm = array_merge($parm1, $parm2); 

	for ($i = 0; $i < sizeof($parm); $i++) { 
		$pattern = '/'; 
		for ($j = 0; $j < strlen($parm[$i]); $j++) { 
			if ($j > 0) { 
				$pattern .= '('; 
				$pattern .= '(&#[x|X]0([9][a][b]);?)?'; 
				$pattern .= '|(&#0([9][10][13]);?)?'; 
				$pattern .= ')?'; 
			}
			$pattern .= $parm[$i][$j]; 
		}
		$pattern .= '/i';
		$string = preg_replace($pattern, '', $string); 
	}
	return $string;
}

/**
 * 打印输出调试
 * @param type $array
 */
function p($array) {
	 dump($array, 1, '<pre>', 0);
}

/**
 * 判断字符串的包含
 * @param type $str
 * @param type $needle
 * @return boolean
 */
function checkinstr($str, $needle) {
	 $tmparray = explode($needle, $str);
	 if (count($tmparray) > 1) {
		  return true;
	 } else {
		  return false;
	 }
}

/**
 * 检测前台用户是否登录
 * @return integer 0-未登录，大于0-当前登录用户ID
 */
function is_login() {
	 return (int) authcode(cookie('user_key'), 'DECODE', C('site_key'));
}

function getUserInfo($uid = 0) {
	 $uid = ($uid > 0) ? $uid : is_login();
	 if ($uid < 1) return FALSE;
	 return M('User')->getById($uid);
}

/**
 * 检测当前用户是否为管理员
 * @return boolean true-管理员，false-非管理员
 */
function is_administrator($uid = null) {
	 $uid = is_null($uid) ? is_login() : $uid;
	 return $uid && (intval($uid) === C('USER_ADMINISTRATOR'));
}

/**
 * 自动检查权限
 * @param type $rule
 * @param type $uid
 * @param type $relation
 * @return boolean
 */
function authcheck($rule, $uid, $relation = 'or') {
	 if (in_array($uid, C('ADMIN_LIST')) || in_array($rule, C('NOT_AUTH_ACTION'))) {
		  return true;	 //如果是，则直接返回真值，不需要进行权限验证
	 } else {
		  libfile('Auth');
		  $auth = new Auth();
		  return $auth->authCheck($rule, $uid, $relation) ? true : false;
	 }
}

/**
 * 字符串转换为数组，主要用于把分隔符调整到第二个参数
 * @param  string $str  要分割的字符串
 * @param  string $glue 分割符
 * @return array
 */
function str2arr($str, $glue = ',') {
	 return explode($glue, $str);
}

/**
 * 数组转换为字符串，主要用于把分隔符调整到第二个参数
 * @param  array  $arr  要连接的数组
 * @param  string $glue 分割符
 * @return string
 */
function arr2str($arr, $glue = ',') {
	 return implode($glue, $arr);
}

/**
 * 对查询结果集进行排序
 * @access public
 * @param array $list 查询结果
 * @param string $field 排序的字段名
 * @param array $sortby 排序类型
 * asc正向排序 desc逆向排序 nat自然排序
 * @return array
 */
function list_sort_by($list, $field, $sortby = 'asc') {
	 if (is_array($list)) {
		  $refer = $resultSet = array();
		  foreach ($list as $i => $data)
				$refer[$i] = &$data[$field];
		  switch ($sortby) {
				case 'asc': // 正向排序
					 asort($refer);
					 break;
				case 'desc':// 逆向排序
					 arsort($refer);
					 break;
				case 'nat': // 自然排序
					 natcasesort($refer);
					 break;
		  }
		  foreach ($refer as $key => $val)
				$resultSet[] = &$list[$key];
		  return $resultSet;
	 }
	 return false;
}

/**
 * 字符串加解密
 * @param  string  $string	 原始字符串
 * @param  string  $operation 加解密类型
 * @param  string  $key		 密钥
 * @param  integer $expiry	 有效期
 
 * @return string
 */
function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0) {
	 $ckey_length = 4;
	 $key = md5($key != '' ? $key : C('site_key'));
	 $keya = md5(substr($key, 0, 16));
	 $keyb = md5(substr($key, 16, 16));
	 $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';

	 $cryptkey = $keya.md5($keya.$keyc);
	 $key_length = strlen($cryptkey);

	 $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
	 $string_length = strlen($string);

	 $result = '';
	 $box = range(0, 255);

	 $rndkey = array();
	 for($i = 0; $i <= 255; $i++) {
		  $rndkey[$i] = ord($cryptkey[$i % $key_length]);
	 }

	 for($j = $i = 0; $i < 256; $i++) {
		  $j = ($j + $box[$i] + $rndkey[$i]) % 256;
		  $tmp = $box[$i];
		  $box[$i] = $box[$j];
		  $box[$j] = $tmp;
	 }

	 for($a = $j = $i = 0; $i < $string_length; $i++) {
		  $a = ($a + 1) % 256;
		  $j = ($j + $box[$a]) % 256;
		  $tmp = $box[$a];
		  $box[$a] = $box[$j];
		  $box[$j] = $tmp;
		  $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
	 }

	 if($operation == 'DECODE') {
		  if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
				return substr($result, 26);
		  } else {
				return '';
		  }
	 } else {
		  return $keyc.str_replace('=', '', base64_encode($result));
	 }
}

/**
 * 数据签名认证
 * @param  array  $data 被认证的数据
 * @return string		 签名
 * @author 
 */
function data_auth_sign($data) {
	 //数据类型检测
	 if (!is_array($data)) {
		  $data = (array) $data;
	 }
	 ksort($data); //排序
	 $code = http_build_query($data); //url编码并生成query字符串
	 $sign = sha1($code); //生成签名
	 return $sign;
}

/**
 * 把返回的数据集转换成Tree
 * @param array $list 要转换的数据集
 * @param string $pid parent标记字段
 * @param string $level level标记字段
 * @return array
 * @author 
 */
function list_to_tree($list, $pk = 'id', $pid = 'parentid', $child = '_child', $root = 0) {
	 // 创建Tree
	 $tree = array();
	 if (is_array($list)) {
		  // 创建基于主键的数组引用
		  $refer = array();
		  foreach ($list as $key => $data) {
				$refer[$data[$pk]] = & $list[$key];
		  }
		  foreach ($list as $key => $data) {
				// 判断是否存在parent
				$parentId = $data[$pid];
				if ($root == $parentId) {
					 $tree[$data[$pk]] = & $list[$key];
				} else {
					 if (isset($refer[$parentId])) {
						  $parent = & $refer[$parentId];
						  $parent[$child][$data[$pk]] = & $list[$key];
					 }
				}
		  }
	 }
	 return $tree;
}

/**
 * 将list_to_tree的树还原成列表
 * @param  array $tree  原来的树
 * @param  string $child 孩子节点的键
 * @param  string $order 排序显示的键，一般是主键 升序排列
 * @param  array  $list  过渡用的中间数组，
 * @return array		  返回排过序的列表数组
 * @author yangweijie <yangweijiester@gmail.com>
 */
function tree_to_list($tree, $child = '_child', $order = 'id', &$list = array()) {
	 if (is_array($tree)) {
		  $refer = array();
		  foreach ($tree as $key => $value) {
				$reffer = $value;
				if (isset($reffer[$child])) {
					 unset($reffer[$child]);
					 tree_to_list($value[$child], $child, $order, $list);
				}
				$list[] = $reffer;
		  }
		  $list = list_sort_by($list, $order, $sortby = 'asc');
	 }
	 return $list;
}

/**
 * 格式化字节大小
 * @param  number $size		字节数
 * @param  string $delimiter 数字和单位分隔符
 * @return string				格式化后的带单位的大小
 * @author 
 */
function format_bytes($size, $delimiter = '') {
	 $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
	 for ($i = 0; $size >= 1024 && $i < 5; $i++)
		  $size /= 1024;
	 return round($size, 2) . $delimiter . $units[$i];
}

/**
 * 设置跳转页面URL
 * 使用函数再次封装，方便以后选择不同的存储方式（目前使用cookie存储）
 * @author 
 */
function set_redirect_url($url) {
	 cookie('redirect_url', $url);
}

/**
 * 获取跳转页面URL
 * @return string 跳转页URL
 * @author 
 */
function get_redirect_url() {
	 $url = cookie('redirect_url');
	 return empty($url) ? __APP__ : $url;
}

/**
 * 根据用户ID获取用户昵称
 * @param  integer $uid 用户ID
 * @return string		 用户昵称
 */
function get_nickname($uid = 0) {
	$name = M('User')->getFieldById($uid, 'username');
	 return $name;
}

/**
 * 获取分类信息并缓存分类
 * @param  integer $id	 分类ID
 * @param  string  $field 要获取的字段名
 * @return string			分类信息
 */
function get_category($id, $field = null) {
	 static $list;

	 /* 非法分类ID */
	 if (empty($id) || !is_numeric($id)) {
		  return '';
	 }

	 /* 读取缓存数据 */
	 if (empty($list)) {
		  $list = S('sys_category_list');
	 }

	 /* 获取分类名称 */
	 if (!isset($list[$id])) {
		  $cate = M('Category')->find($id);
		  if (!$cate || 1 != $cate['status']) { //不存在分类，或分类被禁用
				return '';
		  }
		  $list[$id] = $cate;
		  S('sys_category_list', $list); //更新缓存
	 }
	 return is_null($field) ? $list[$id] : $list[$id][$field];
}

/**
 * 分类添加层级
 * @staticvar array $tree
 * @param type $list
 * @param type $parent_id
 * @param type $level
 * @return type
 */
function getTree($list,$parent_id,$level=0){
	 static $tree=array();
	 foreach ($list as $row){
		  if($row['parent_id']==$parent_id){
				$row['level']=$level;
				$tree[]=$row;
				getTree($list, $row['id'],$level+1);
		  }
	 }
	 return $tree;
}
/* 根据ID获取分类标识 */

function get_category_name($id) {
	 return get_category($id, 'name');
}

/* 根据ID获取分类名称 */

function get_category_title($id) {
	 return get_category($id, 'title');
}

/**
 * 操作日志
 * @param string $msg 日志内容
 * @return bool
 */
function action_log($msg = '') {
	 return model('admin_action_log')->write_log($msg);
}


//基于数组创建目录和文件
function create_dir_or_files($files) {
	 foreach ($files as $key => $value) {
		  if (substr($value, -1) == '/') {
				mkdir($value);
		  } else {
				@file_put_contents($value, '');
		  }
	 }
}

if (!function_exists('array_column')) {

	 function array_column(array $input, $columnKey, $indexKey = null) {
		  $result = array();
		  if (null === $indexKey) {
				if (null === $columnKey) {
					 $result = array_values($input);
				} else {
					 foreach ($input as $row) {
						  $result[] = $row[$columnKey];
					 }
				}
		  } else {
				if (null === $columnKey) {
					 foreach ($input as $row) {
						  $result[$row[$indexKey]] = $row;
					 }
				} else {
					 foreach ($input as $row) {
						  $result[$row[$indexKey]] = $row[$columnKey];
					 }
				}
		  }
		  return $result;
	 }

}

/**
 * 获取表名（不含表前缀）
 * @param string $model_id
 * @return string 表名
 * @author huajie <banhuajie@163.com>
 */
function get_table_name($model_id = null) {
	 if (empty($model_id)) {
		  return false;
	 }
	 $Model = M('Model');
	 $name = '';
	 $info = $Model->getById($model_id);
	 if ($info['extend'] != 0) {
		  $name = $Model->getFieldById($info['extend'], 'name') . '_';
	 }
	 $name .= $info['name'];
	 return $name;
}

/**
 * 获取属性信息并缓存
 * @param  integer $id	 属性ID
 * @param  string  $field 要获取的字段名
 * @return string			属性信息
 */
function get_model_attribute($model_id, $group = true) {
	 static $list;

	 /* 非法ID */
	 if (empty($model_id) || !is_numeric($model_id)) {
		  return '';
	 }

	 /* 读取缓存数据 */
	 if (empty($list)) {
		  $list = S('attribute_list');
	 }

	 /* 获取属性 */
	 if (!isset($list[$model_id])) {
		  $map = array('model_id' => $model_id);
		  $extend = M('Model')->getFieldById($model_id, 'extend');

		  if ($extend) {
				$map = array('model_id' => array("in", array($model_id, $extend)));
		  }
		  $info = M('Attribute')->where($map)->select();
		  $list[$model_id] = $info;
		  //S('attribute_list', $list); //更新缓存
	 }

	 $attr = array();
	 foreach ($list[$model_id] as $value) {
		  $attr[$value['id']] = $value;
	 }

	 if ($group) {
		  $sort = M('Model')->getFieldById($model_id, 'field_sort');

		  if (empty($sort)) { //未排序
				$group = array(1 => array_merge($attr));
		  } else {
				$group = json_decode($sort, true);

				$keys = array_keys($group);
				foreach ($group as &$value) {
					 foreach ($value as $key => $val) {
						  $value[$key] = $attr[$val];
						  unset($attr[$val]);
					 }
				}

				if (!empty($attr)) {
					 $group[$keys[0]] = array_merge($group[$keys[0]], $attr);
				}
		  }
		  $attr = $group;
	 }
	 return $attr;
}

/**
 * 调用系统的API接口方法（静态方法）
 * api('User/getName','id=5'); 调用公共模块的User接口的getName方法
 * api('Admin/User/getName','id=5');  调用Admin模块的User接口
 * @param  string  $name 格式 [模块名]/接口名/方法名
 * @param  array|string  $vars 参数
 */
function api($name, $vars = array()) {
	 $array = explode('/', $name);
	 $method = array_pop($array);
	 $classname = array_pop($array);
	 $module = $array ? array_pop($array) : 'Common';
	 $callback = $module . '\\Api\\' . $classname . 'Api::' . $method;
	 if (is_string($vars)) {
		  parse_str($vars, $vars);
	 }
	 return call_user_func_array($callback, $vars);
}
/**
 * 多维数组转一维
 * @param array $a 要转换的数组
 */
function arrayChange($a){
	 static $arr2;
	 foreach($a as $v){
		  if(is_array($v)){
				arrayChange($v);
		  }else{
				$arr2[]=$v;
		  }
	 }
	 return $arr2;
}
/**
 * 根据条件字段获取指定表的数据
 * @param mixed $value 条件，可用常量或者数组
 * @param string $condition 条件字段
 * @param string $field 需要返回的字段，不传则返回整个数据
 * @param string $table 需要查询的表
 * @author huajie <banhuajie@163.com>
 */
function get_table_field($value = null, $condition = 'id', $field = null, $table = null) {
	 if (empty($value) || empty($table)) {
		  return false;
	 }

	 //拼接参数
	 $map[$condition] = $value;
	 $info = M(ucfirst($table))->where($map);
	 if (empty($field)) {
		  $info = $info->field(true)->find();
	 } else {
		  $info = $info->getField($field);
	 }
	 return $info;
}

/**
 * 返回状态文字
 */
function status_text($status) {
	 if ($status == 1)
		  $txt = '正常';
	 if ($status == 0)
		  $txt = '禁用';
	 return $txt;
}
/**
 * 根据数字填充至字串前
 * @param type $num
 * @param type $str
 * @return type
 */
function space($num,$str='&nbsp;&nbsp;'){
	 for($i=0;$i<$num;$i++){
		  $_temp.=$str;
	 }
	 return $_temp;
}

/**
 * 通用改变状态
 * @param type $obj
 * @param type $list
 * @param type $val
 */
function checkstatus($obj,$field,$list,$val='none'){
		  if(!empty($list)){
				$sta = M($obj);
				if($val=='none'){
						  $data[$field]=array('exp',' 1-status ');
				}else{
						  $data[$field] = intval($val);
				}
				if(is_array($list)){
					 foreach($list as $value){
						  $sta->where(array('id'=>$value))->save($data);
					 }				  
				}else{
					$sta->where(array('id'=>$list))->save($data); 
				}
				return true;
		  }else{
				return false;
		  }

}
//批量删除通用函数
function checkdelete($obj,$list){
		  if(!empty($list)){
					 $sc = M($obj);
					 $sc->where(array('id'=>array('in',$list)))->delete();
					 return true;
		  }else{
					 return false;
		  }
}
/**
 * 发送邮件
 */
function  SendMail($address,$title,$message) {
	 libfile('Smtp');
	 $map=array();
	 $map['code']='email';
	 $config_json=model('notify')->where($map)->getField('config');
     $config=json_decode($config_json,true);
     $smtpserver = $config['mail_smtpserver'];
	 $port		 = $config['mail_smtpport'];
	 $smtpuser	= $config['mail_mailuser'];
	 $smtppwd	 = $config['mail_mailpass'];
	 $from      = $config['mail_formmail'];
	 $mailtype	= "HTML";
	 $smtp = new Smtp($smtpserver, $port, true, $smtpuser, $smtppwd, $address);
	 $send = $smtp->sendmail($address, $from, $title, $message, $mailtype);
 
	 // 发送邮件。
	 return $send;
}
/**
 * 根据日期生成唯一订单号
 */
function build_order_no() {
	 return date('YmdHis').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 6);
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
/**
 * 清理网站缓存有
 */
function clearCache(){
//  import("ORG.Io.Dir");
//  if(file_exists('./Runtime')){
//		Dir::delDir('./Runtime');
//  }
}

/**
 * 字符串截取函数
 * @param  string	$str	  待截取的字符串
 * @param  int		$length  保留长度
 * @param  int		$start	开始位置
 * @param  string	$charset 编码
 * @param  string	$suffix  默认后缀
 */
function msubstr($str, $length, $start=0, $charset="utf-8", $suffix=true){
	 if(function_exists("mb_substr")){
		  if($suffix){
				return mb_substr($str, $start, $length, $charset)."...";
		  }else{
				return mb_substr($str, $start, $length, $charset);
		  }
	 }elseif(function_exists('iconv_substr')) {
		  if($suffix){
				return iconv_substr($str,$start,$length,$charset)."...";
		  }else{
				return iconv_substr($str,$start,$length,$charset);
		  }
	 }
	 $re['utf-8']	= "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
	 $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
	 $re['gbk']	 = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
	 $re['big5']	= "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
	 preg_match_all($re[$charset], $str, $match);
	 $slice = join("",array_slice($match[0], $start, $length));
	 if($suffix){
		  return $slice."...";
	 } else {
		  return $slice;
	 }
}

/**
* 转化 \ 为 /
* 
* @param	 string  $path	路径
* @return	string  路径
*/
function dir_path($path) {
	 $path = str_replace('\\', '/', $path);
	 if(substr($path, -1) != '/') $path = $path.'/';
	 return $path;
}

function dir_create($path, $mode = 0777) {
	 if(is_dir($path)) return TRUE;
	 $ftp_enable = 0;
	 $path = dir_path($path);
	 $temp = explode('/', $path);
	 $cur_dir = '';
	 $max = count($temp) - 1;
	 for($i=0; $i<$max; $i++) {
		  $cur_dir .= $temp[$i].'/';
		  if (@is_dir($cur_dir)) continue;
		  @mkdir($cur_dir, 0777,true);
		  @chmod($cur_dir, 0777);
	 }
	 return is_dir($path);
}

/**
 * 编译模板缓存文件
 * @param  string $maintpl	  模板路径
 * @param  [type] $cachefile	缓存路径
 * @param  [type] $timecompare 缓存时间
 * @return [type]				  [description]
 */
function checktplrefresh($maintpl, $cachefile, $timecompare = 1) {
	static $tplrefresh, $timestamp;
	$tplrefresh = C('TMPL_CACHE_ON');
	$timestamp = NOW_TIME;
	if(empty($timecompare) || $tplrefresh === FALSE || !file_exists($cachefile) ||  @filemtime($cachefile) - $timestamp > $timecompare) {
		libfile('Template');
		$template = new Template();
		$template->parse_template($maintpl, $cachefile);
		return TRUE;
	}
	return FALSE;
}

/* 获取模板路径 */
function template($file = '', $module = '') {
    $oldfile = $file;
    $suffix = C('TMPL_TEMPLATE_SUFFIX');
    $module = strtolower((empty($module)) ? GROUP_NAME : $module);
    $theme_path = (defined('IS_MOBILE')) ? 'wap' : C('TMPL_THEME');
    if(strpos($file, ':') !== false) {
        list($theme, $file) = explode(':', $file);
        if ($theme != C('TMPL_THEME') && defined('IN_PLUGIN')) {
            $tpldir = 'plugin/'.PLUGIN_ID.'/template';
        } else {
            $tpldir = TMPL_PATH.$theme_path.'/'.$module;            
        }
    } elseif(strpos($file, '/') !== false) {
    	$tpldir = TMPL_PATH.$theme_path;
    }else {
        $tpldir = TMPL_PATH.$theme_path.'/'.$module;
    }
    $file .= IS_AJAX ? '_ajax' : '';//如果是ajax模板
    $filebak = $file;
    $tplfile = $tpldir.strtolower('/'.$file.$suffix);
    $md5file = strtolower(md5($tpldir.'/'.$file).$suffix);
    $cachefile = CACHE_PATH.substr($md5file, 0, -strlen($suffix)).'.php';
    if(!file_exists($tplfile) && !file_exists(substr($tplfile, 0, -4).'.php')) {
        $tplfile = TMPL_PATH.'default/'.strtolower($module.'/'.$filebak.C('TMPL_TEMPLATE_SUFFIX'));
    }
    /* 检查模板缓存 */
    checktplrefresh($tplfile, $cachefile, @filemtime($cachefile));
    return $cachefile;
}

/**
 * 检测字符串包含
 
 */
function strexists($string, $find) {
	 return !(strpos($string, $find) === FALSE);
}

/**
 * 提示信息页面跳转，跳转地址如果传入数组，页面会提示多个地址供用户选择，默认跳转地址为数组的第一个值，时间为5秒。
 * showmessage('登录成功', array('默认跳转地址'=>'http://www.phpcms.cn'));
 * @param string $msg 提示信息
 * @param mixed(string/array) $url_forward 跳转地址
 * @param int $ms 跳转等待时间
 */
function showmessage($msg, $url_forward = 'goback', $status = 0, $ms = 1500, $dialog = '', $returnjs = '', $extra = array()) {
	 $suffix = ($status == 1) ? '_success' : '_error';
	 runhook(strtolower(GROUP_NAME.'_'.MODULE_NAME.'_'.ACTION_NAME.$suffix));
	 if (IS_AJAX) {
		  $result = array();
		  $result['status'] = $status;
		  $result['info'] = $msg;
		  $result['url'] = $url_forward;
		  $result['ms'] = $ms;
		  if($extra) {
				$result = array_merge($result, $extra);
		  }
		  echo json_encode($result);
		  exit();
	 }
	 if(defined('IN_ADMIN')) {
	 	include A('Base')->admin_tpl('showmessage');
	 } else {
	 	include (template('showmessage', 'common'));
	 }
	 exit;
}

/**
 * 时间格式化
 * @param  int $timestamp 时间戳
 * @param  string  $format	 格式
 * @return string
 */
function mdate($timestamp=0, $format='Y-m-d H:i:s') {
	 $times = intval($timestamp);
	 if(!$times) return false;
	 return date($format,$times);
}

function pages($count, $num, $page){
	 libfile('Page');
	 $Page = new Page($count, $num);
	 $pages = $Page->show();
	 return $pages;
}


/**
 * 获取系统信息
 */
function get_sysinfo() {
	 $sys_info['os']				 = PHP_OS;
	 $sys_info['zlib']			  = function_exists('gzclose');//zlib
	 $sys_info['safe_mode']		= (boolean) ini_get('safe_mode');//safe_mode = Off
	 $sys_info['safe_mode_gid']  = (boolean) ini_get('safe_mode_gid');//safe_mode_gid = Off
	 $sys_info['timezone']		 = function_exists("date_default_timezone_get") ? date_default_timezone_get() : L('no_setting');
	 $sys_info['socket']			= function_exists('fsockopen') ;
	 $sys_info['web_server']	  = strpos($_SERVER['SERVER_SOFTWARE'], 'PHP')===false ? $_SERVER['SERVER_SOFTWARE'].'PHP/'.phpversion() : $_SERVER['SERVER_SOFTWARE'];
	 $sys_info['phpv']			  = phpversion(); 
	 $sys_info['mysqlv'] = mysql_get_server_info();
	 $sys_info['fileupload']	  = @ini_get('file_uploads') ? ini_get('upload_max_filesize') :'unknown';
	 $sys_info['mysqlsize']		= M()->query("select round(sum(DATA_LENGTH/1024/1024)+sum(DATA_LENGTH/1024/1024),2) as db_length from information_schema.tables 
where table_schema='".C('DB_NAME')."'");
	 $sys_info['mysqlsize']		= $sys_info['mysqlsize'][0]['db_length'];
	 return $sys_info;
}

/**
 * 当前路径
 * 返回指定栏目路径层级
 * @param $catid 栏目id
 * @param $symbol 栏目间隔符
 */
function catpos($catid, $symbol=' > ') {
	 $categorys = getcache('goods_category', 'goods');
	 $pos = '';
	 $parentids = D('Admin/Category')->getParent($catid, 0);	 
	 if (empty($parentids)) return FALSE;
	 asort($parentids);
	 foreach ($parentids as $parentid) {
		  $url = U('Goods/Index/lists', array('id' => $parentid));
		  $pos .= '<a href="'.$url.'">'.$categorys[$parentid]['name'].'</a>'.$symbol;
	 }
	 return $pos;
}

function catposArticle($catid, $symbol=' > ') {
	 $categorys = D('ArticleCategory')->select();
	 $pos = '';
	 $parentids = D('ArticleCategory')->field('name')->where(array('id'=>$catid))->select();  
	 if (empty($parentids)) return FALSE;
	 asort($parentids);
	 foreach ($parentids as $parentid) {
		  $url = U('Article/Index/lists', array('id' => $catid));
		  $pos .= $symbol.'<a href="'.$url.'">'.$parentid['name'].'</a>';
	 }
	 return $pos;
}


/**
 * 电子邮箱格式判断
 * @param  string $email 字符串
 * @return bool
 */
function is_email($email) {
	 if (!empty($email)) {
		  return preg_match('/^[a-z0-9]+([\+_\-\.]?[a-z0-9]+)*@([a-z0-9]+[\-]?[a-z0-9]+\.)+[a-z]{2,6}$/i', $email);
	 }
	 return FALSE;
}

function is_mobile($string){
	 if (!empty($string)) {
		  return preg_match('/^1[3|4|5|7|8][0-9]\d{8}$/', $string);
	 }
	 return FALSE;
}

function group_name($group_id){
	return model('user_group')->getFieldById($group_id, 'name');
}

function getconfig($name=null, $value=null) {
	return C($name, $value);
}

/**
 * 获取地区名称
 * @param int $id 地区ID
 */
function getAreaNameById($id) {
	return M('Region')->where(array('area_id' => $id))->getField('area_name');

}

/**
 * 获取订单商品
 * @param int $order_id 订单ID
 */
function getGoodsInfoByOrderId($order_id, $limit = 3){
	$sqlmap = $result = array();
	$sqlmap['order_id'] = $order_id;
	$result['nums'] = model('order_goods')->where($sqlmap)->count();
	$result['info'] = model('order_goods')->where($sqlmap)->limit($limit)->select();
	return $result;
}

function create_url($k, $v, $attr) {	
	$url = parse_url(__SELF__);
	parse_str($url['query'], $param);
	$param = dstripslashes($param);
	if(in_array($k, $attr)) {
		$param['attr'][$k] = $v;
	} else {
		$param[$k] = $v;
	}
	 $param['page'] = 0;
	$param = array_filter($param);
	$param['attr'] = array_filter($param['attr']);
	return urldecode($url['path'].'?'.http_build_query($param));
}

/**
 * 变更用户积分
 */
function action_point($uid, $point, $descript, $isrun = TRUE) {
	$uid = (int) $uid;
	$point = (int) $point;
	$log = array();
	$log['user_id'] = $uid;
	$log['pay_points'] = $point;
	$log['descript'] = $descript;
	$log['time'] = NOW_TIME;
	$result = D('UserPointslog')->add($log);
	if($result && $isrun === TRUE) {
		if($point > 0) {
			M('User')->where(array('id' => $uid))->setInc('pay_points', $point);
		} else {
			M('User')->where(array('id' => $uid))->setDec('pay_points', $point);
		}
		return TRUE;
	}
	return FALSE;
}

/**
 * 经验变更
 */
function action_exp($uid, $exp) {
	$uid = (int) $uid;
	$exp = (int) $exp;
	$oldxp = M('User')->getFieldById($uid, 'exp');
	$info['id'] = $uid;
	$info['exp'] = $oldxp + $exp;
	$sqlmap['min_points'] = array("ELT", $info['exp']);
	$sqlmap['max_points'] = array("GT", $info['exp']);
	$group_id = M('UserGroup')->where($sqlmap)->getField('id');
	if($group_id < 1) return FALSE;
	$info['group_id'] = $group_id;
	M('User')->save($info);
	return TRUE;	
}

/**
 * 优惠券变更
 */
function action_coupons($uid, $cid, $num = 1){
	$uid = (int) $uid;
	$cid = (int) $cid;
	$num = (int) $num;
	$cmap['cid'] = array("EQ", $cid);
	$cmap['status'] = array("EQ", 0);
	$crs = model('coupons_list')->where($cmap)->limit($num)->select();
	foreach ($crs as $key => $value) {
		$umap['id'] = $value['id'];
		$data['user_id'] = $uid;
		$data['status'] = '1';
		model('coupons_list')->where($umap)->save($data);
	}
	return TRUE;
}

/* 实例化数据表 */
function model($name, $layer = '', $baseurl) {
	$tablename = parse_name($name, 1);
	if(defined('IN_PLUGIN')) {
		$baseurl = PLUGIN_PATH.PLUGIN_ID.'/model';
	}
	return D($tablename, $layer, $baseurl);
}

/**
 * 执行SQL语句
 * @param  string $sqlquery SQL语句，支持多条
 * @return bool
 */
function sqlexecute($sqlquery = '') {
	 if(empty($sqlquery)) return FALSE;
	 $sqlquery = str_replace('{prefix}', C('DB_PREFIX'), $sqlquery);
	 $version = M()->query("select version() as v;");
	 $version = intval($version[0]['v']);
	 if($version > '4.1' && C('DEFAULT_CHARSET')) {
		  $sqlquery = preg_replace("/TYPE=(InnoDB|MyISAM|MEMORY)( DEFAULT CHARSET=[^; ]+)?/", "ENGINE=\\1 DEFAULT CHARSET=utf8;", $sqlquery);
	 }				
	 $sqlquery = str_replace("\r", "\n", $sqlquery);
	 $queriesarray = explode(";\n", trim($sqlquery));
	 foreach ($queriesarray as $query) {
		  if (substr($query, 0, 1) != '#' && substr($query, 0, 1) != '-') {
				M()->execute($query);
		  }
	 }
	 return TRUE;
}

function multi_array_sort(&$multi_array,$sort_key,$sort=SORT_DESC){
	 if(is_array($multi_array)){
		  foreach ($multi_array as $row_array){
				if(is_array($row_array)){
					 $key_array[] = $row_array[$sort_key];
				}else{
					 return false;
				}
		  }
	 } else {
		  return false;
	 }
	 array_multisort($key_array,$sort,$multi_array);
	 return $multi_array;
}


//[代金券]获取代金券数量
function getCouponsCount($id,$status){
	$where['cid'] = $id;
	if(isset($status)){
		$where['status']=$status;
	}
		return M('coupons_list')->where($where)->count();
	}

//获取会员表信息
function getMemberfield($id,$field){
	$where['id']=$id;
	$field=$field;
	return M('user')->where($where)->getField($field);
	
}

//获取商品及产品库存
function getGoodsNumber($goods_id,$products_id,$field){
	$r = D('Goods')->detail($goods_id,$products_id);
		if($products_id && $field == 'name'){
			$spec_arr = unserialize($r['spec_array']);
			foreach ($spec_arr as $k => $v) {
				$spec_arr_text .= '['.$v['name'].':'.$v['value'].'] ';
			}

			$r['name'] = $r['name'].' '.$spec_arr_text;
		}
		return $r[$field];
}

//格式化金额
function format_price($min,$max=0){
	$r = $min . '-' . $max;
	if($max == 0) $r = $min;
	if($min == $max) $_r = $min;
	return $r;
}

/**
* 将数组转换为字符串
*
* @param	array	$data		数组
* @param	bool	$isformdata	如果为0，则不使用new_stripslashes处理，可选参数，默认为1
* @return	string	返回字符串，如果，data为空，则返回空
*/
function array2string($data, $isformdata = 1) {
    if($data == '') return '';
    if($isformdata) $data = dstripslashes($data);
    return var_export($data, TRUE);
}

/**
* 将字符串转换为数组
*
* @param	string	$data	字符串
* @return	array	返回数组格式，如果，data为空，则返回空数组
*/
function string2array($data) {
    if($data == '') return array();
    @eval("\$array = $data;");
    return $array;
}

/**
 * 多维数组合并（支持多数组）
 * @return array
 */
function array_merge_multi ()
{
    $args = func_get_args();
    $array = array();
    foreach ( $args as $arg ) {
        if ( is_array($arg) ) {
            foreach ( $arg as $k => $v ) {
                if ( is_array($v) ) {
                    $array[$k] = isset($array[$k]) ? $array[$k] : array();
                    $array[$k] = array_merge_multi($array[$k], $v);
                } else {
                    $array[$k] = $v;
                }
            }
        }
    }
    return $array;
}
