<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: class_template.php 34486 2014-05-08 01:31:08Z nemohou $
 */

if(!defined('IN_APP')) {
	exit('Access Denied');
}

class Template {

	var $subtemplates  = array();
	var $csscurmodules = '';
	var $replacecode   = array('search' => array(), 'replace' => array());
	var $blocks        = array();
	var $language      = array();
	var $file          = '';
	var $config        = array();

	function __construct() {
		$this->config['taglib_begin']    = C('TAGLIB_BEGIN');
		$this->config['taglib_end']      = C('TAGLIB_END');
		$this->config['taglib_name']     = C('TAGLIB_NAME');
	}

	function parse_template($tplfile, $cachefile) {
		$suffix = C('TMPL_TEMPLATE_SUFFIX');
		$basefile = basename($tplfile, $suffix);
		if($fp = @fopen($tplfile, 'r')) {
			$template = @fread($fp, filesize($tplfile));
			fclose($fp);
		} elseif($fp = @fopen($filename = substr($tplfile, 0, -4).'.php', 'r')) {
			$template = $this->getphptemplate(@fread($fp, filesize($filename)));
			fclose($fp);
		} else {
			$this->error('模板文件不存在', $tplfile);
		}
		$var_regexp = "((\\\$[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*(\-\>)?[a-zA-Z0-9_\x7f-\xff]*)(\[[a-zA-Z0-9_\-\.\"\'\[\]\$\x7f-\xff]+\])*)";
		$const_regexp = "([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)";

		$this->subtemplates = array();
		for($i = 1; $i <= 3; $i++) {
			if(strexists($template, '{subtemplate')) {
				$template = preg_replace("/[\n\r\t]*(\<\!\-\-)?\{subtemplate\s+([a-z0-9_:\/]+)\}(\-\-\>)?[\n\r\t]*/ies", "\$this->loadsubtemplate('\\2')", $template);
			}
		}


		$template = "<? if(!defined('IN_APP')) exit('Access Denied');?>\n$template";
		/* 广告位 */
		$template = preg_replace("/[\n\r\t]*\{ad\/(.+?)\}[\n\r\t]*/ie", "\$this->adtags('\\1')", $template);
		$template = preg_replace("/[\n\r\t]*\{ad\s+([a-zA-Z0-9_\[\]]+)\/(.+?)\}[\n\r\t]*/ie", "\$this->adtags('\\2', '\\1')", $template);

		$template = preg_replace ( "/\{template\s+(.+)\}/", "<?php include template('\\1'); ?>", $template );
		$template = preg_replace ( "/\{include\s+(.+)\}/", "<?php include \\1; ?>", $template );
		$template = preg_replace ( "/\{php\s+(.+)\}/", "<?php \\1?>", $template );	

		$template = preg_replace ( "/\{if\s+(.+?)\}/", "<?php if(\\1) { ?>", $template );
		$template = preg_replace ( "/\{else\}/", "<?php } else { ?>", $template );
		$template = preg_replace ( "/\{elseif\s+(.+?)\}/", "<?php } elseif (\\1) { ?>", $template );
		$template = preg_replace ( "/\{\/if\}/", "<?php } ?>", $template );
		//for 循环
		$template = preg_replace("/\{for\s+(.+?)\}/","<?php for(\\1) { ?>",$template);
		$template = preg_replace("/\{\/for\}/","<?php } ?>",$template);
		//++ --
		$template = preg_replace("/\{\+\+(.+?)\}/","<?php ++\\1; ?>",$template);
		$template = preg_replace("/\{\-\-(.+?)\}/","<?php ++\\1; ?>",$template);
		$template = preg_replace("/\{(.+?)\+\+\}/","<?php \\1++; ?>",$template);
		$template = preg_replace("/\{(.+?)\-\-\}/","<?php \\1--; ?>",$template);
		$template = preg_replace ( "/\{loop\s+(\S+)\s+(\S+)\}/", "<?php \$n=1;if(is_array(\\1)) foreach(\\1 AS \\2) { ?>", $template );
		$template = preg_replace ( "/\{loop\s+(\S+)\s+(\S+)\s+(\S+)\}/", "<?php \$n=1; if(is_array(\\1)) foreach(\\1 AS \\2 => \\3) { ?>", $template );
		$template = preg_replace ( "/\{\/loop\}/", "<?php \$n++;}unset(\$n); ?>", $template );
		$template = preg_replace ( "/\{([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff:]*\(([^{}]*)\))\}/", "<?php echo \\1;?>", $template );
		$template = preg_replace ( "/\{\\$([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff:]*\(([^{}]*)\))\}/", "<?php echo \\1;?>", $template );
		$template = preg_replace ( "/\{(\\$[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)\}/", "<?php echo \\1;?>", $template );
		$template = preg_replace("/\{(\\$[a-zA-Z0-9_\[\]\'\"\$\x7f-\xff]+)\}/es", "\$this->addquote('<?php echo \\1;?>')",$template);
		$template = preg_replace ( "/\{([A-Z_\x7f-\xff][A-Z0-9_\x7f-\xff]*)\}/s", "<?php echo \\1;?>", $template );

		$template = preg_replace("/\{".$this->config['taglib_name'].":(\w+)\s+([^}]+)\}/ie", "self::begin_tag('$1','$2', '$0')", $template);

		$template = preg_replace("/\{\/".$this->config['taglib_name']."\}/ie", "self::end_tag()", $template);



		$template = preg_replace("/\{$const_regexp\}/s", "<?=\\1?>", $template);
		if(!empty($this->replacecode)) {
			$template = str_replace($this->replacecode['search'], $this->replacecode['replace'], $template);
		}
		$template = preg_replace("/ \?\>[\n\r]*\<\? /s", " ", $template);
		
		$cachedir = dirname($cachefile);
		if(!dir_create($cachedir)) {
			$this->error('模板缓存目录不可写', $cachedir);
		}
		if(!@$fp = fopen($cachefile, 'w')) {
			$this->error('模板缓存文件不可写', $cachefile);
		}

		$template = preg_replace("/[\n\r\t]*\{block\s+([a-zA-Z0-9_\[\]]+)\}(.+?)\{\/block\}/ies", "\$this->stripblock('\\1', '\\2')", $template);
		$template = preg_replace("/\<\?(\s{1})/is", "<?php\\1", $template);
		$template = preg_replace("/\<\?\=(.+?)\?\>/is", "<?php echo \\1;?>", $template);
		flock($fp, 2);
		fwrite($fp, $template);
		fclose($fp);
	}

	/* 模块标签封装 */
	function begin_tag($op, $data, $html) {
		preg_match_all("/(\w+)\=[\"|']?([^\"|']+)[\"|']?/i", stripslashes($data), $matches, PREG_SET_ORDER);
		$arr = array('action','num','cache','page', 'pagesize', 'urlrule', 'return', 'start');
		$tools = array('json', 'xml', 'block', 'get');
		$datas = array();
		$tag_id = md5(stripslashes($html));
		$str_datas = 'op='.$op.'&tag_md5='.$tag_id;
		foreach ($matches as $v) {
			$str_datas .= $str_datas ? "&$v[1]=".($op == 'block' && strpos($v[2], '$') === 0 ? $v[2] : urlencode($v[2])) : "$v[1]=".(strpos($v[2], '$') === 0 ? $v[2] : urlencode($v[2]));
			if(in_array($v[1], $arr)) {
				$$v[1] = $v[2];
				continue;
			}
			$datas[$v[1]] = $v[2];
		}

		if (strpos($num, '$')===0) {
			$num = $num;
		} else {
			$num = isset($num) && intval($num) ? intval($num) : 20;
		}
		$cache = (isset($cache) && intval($cache) && APP_DEBUG === FALSE) ? intval($cache) : 0;
		$return = isset($return) && trim($return) ? trim($return) : 'data';
		$tag_cache = S($tag_id);
		if ($cache > 0 && $tag_cache) {
			return '<?php $'.$return.' = S("'.$tag_id.'"); ?>';
		}

		if (in_array($op,$tools)) {
			switch ($op) {
				case 'get':
					if (isset($start) && intval($start)) {
						$limit = intval($start).','.$num;
					} else {
						$limit = $num;
					}
					$sql = str_replace("{prefix}", C('DB_PREFIX'), $datas['sql']);
					$string .= '$'.$return.' = M()->query('.$sql.');';
					break;			
				default:
					# code...
					break;
			}
		} else {
			if (!isset($action) || empty($action)) return false;
			$string = '';
			if (MODULE_NAME == 'Plugin') {
				$taglib_file = APP_PATH.'plugin'.DIRECTORY_SEPARATOR.$op.'taglib'.DIRECTORY_SEPARATOR.$op.'.class.php';
			} else {
				$taglib_file = TAGLIB_PATH.$op.'.class.php';
			}
			if (file_exists($taglib_file)) {
				// if (!defined($op.'_tag')) {					
					$string .= "require_cache('".$taglib_file."');";
					$op = (!empty($taglib)) ? trim($taglib) : $op;
					$string .= '$'.$op.'_tag = new '.$op.'();';
					//define($op.'_tag', TRUE);
					
				// }		
				$string .= 'if(method_exists($'.$op.'_tag, \''.$action.'\')) {';
				if (isset($start) && intval($start)) {
					$datas['limit'] = intval($start).','.$num;
				} else {
					$datas['limit'] = $num;
				}
				if (isset($page)) {
					$datas['page'] = $page;
					$string .= '$count = $'.$op.'_tag->count('.self::array2html($datas).');';
					$string .= '$pages = pages($count, '.$num.', $page);';
				}
				$string .= '$'.$return.' = $'.$op.'_tag->'.$action.'('.self::array2html($datas).');';
				if ($cache > 0) {
					$string .= 'S("'.$tag_id.'", $'.$return.', '.$cache.');';
				}	
				$string .= '}';
			}
		}
		return '<?php '.$string.' ?>';
	}

	function end_tag() {
		return '';
	}

	function blocktags($parameter) {
		$bid = intval(trim($parameter));
		$this->blocks[] = $bid;
		$i = count($this->replacecode['search']);
		$this->replacecode['search'][$i] = $search = "<!--BLOCK_TAG_$i-->";
		$this->replacecode['replace'][$i] = "<?php block_display('$bid');?>";
		return $search;
	}

	function blockdatatags($parameter) {
		$bid = intval(trim($parameter));
		$this->blocks[] = $bid;
		$i = count($this->replacecode['search']);
		$this->replacecode['search'][$i] = $search = "<!--BLOCKDATA_TAG_$i-->";
		$this->replacecode['replace'][$i] = "";
		return $search;
	}

	function adtags($parameter, $varname = '') {
		$parameter = stripslashes($parameter);
		$i = count($this->replacecode['search']);
		$this->replacecode['search'][$i] = $search = "<!--AD_TAG_$i-->";
		$this->replacecode['replace'][$i] = "<?php ".(!$varname ? 'echo ' : '$'.$varname.'=')."adshow(\"$parameter\");?>";
		return $search;
	}

	function datetags($parameter) {
		$parameter = stripslashes($parameter);
		$i = count($this->replacecode['search']);
		$this->replacecode['search'][$i] = $search = "<!--DATE_TAG_$i-->";
		$this->replacecode['replace'][$i] = "<?php echo dgmdate($parameter);?>";
		return $search;
	}

	function avatartags($parameter) {
		$parameter = stripslashes($parameter);
		$i = count($this->replacecode['search']);
		$this->replacecode['search'][$i] = $search = "<!--AVATAR_TAG_$i-->";
		$this->replacecode['replace'][$i] = "<?php echo avatar($parameter);?>";
		return $search;
	}

	function evaltags($php) {
		$php = str_replace('\"', '"', $php);
		$i = count($this->replacecode['search']);
		$this->replacecode['search'][$i] = $search = "<!--EVAL_TAG_$i-->";
		$this->replacecode['replace'][$i] = "<? $php?>";
		return $search;
	}

	function hooktags($hookid, $key = '') {
		global $_G;
		$i = count($this->replacecode['search']);
		$this->replacecode['search'][$i] = $search = "<!--HOOK_TAG_$i-->";
		$dev = '';
		if(isset($_G['config']['plugindeveloper']) && $_G['config']['plugindeveloper'] == 2) {
			$dev = "echo '<hook>[".($key ? 'array' : 'string')." $hookid".($key ? '/\'.'.$key.'.\'' : '')."]</hook>';";
		}
		$key = $key !== '' ? "[$key]" : '';
		$this->replacecode['replace'][$i] = "<?php {$dev}if(!empty(\$_G['setting']['pluginhooks']['$hookid']$key)) echo \$_G['setting']['pluginhooks']['$hookid']$key;?>";
		return $search;
	}

	function stripphpcode($type, $code) {
		$this->phpcode[$type][] = $code;
		return '{phpcode:'.$type.'/'.(count($this->phpcode[$type]) - 1).'}';
	}

	function loadsubtemplate($file) {
		list($module, $file) = explode("/", $file);
		$tplfile = template($file, $module);
		$filename = $tplfile;
		if(($content = @implode('', file($filename))) || ($content = $this->getphptemplate(@implode('', file(substr($filename, 0, -4).'.php'))))) {
			$this->subtemplates[] = $tplfile;
			return $content;
		} else {
			return '<!-- '.$file.' -->';
		}
	}

	function getphptemplate($content) {
		$pos = strpos($content, "\n");
		return $pos !== false ? substr($content, $pos + 1) : $content;
	}

	function loadcsstemplate() {
		global $_G;
		$scripts = array(STYLEID.'_common');
		$content = $this->csscurmodules = '';
		$content = @implode('', file(DISCUZ_ROOT.'./data/cache/style_'.STYLEID.'_module.css'));
		$content = preg_replace("/\[(.+?)\](.*?)\[end\]/ies", "\$this->cssvtags('\\1','\\2')", $content);
		if($this->csscurmodules) {
			$this->csscurmodules = preg_replace(array('/\s*([,;:\{\}])\s*/', '/[\t\n\r]/', '/\/\*.+?\*\//'), array('\\1', '',''), $this->csscurmodules);
			if(@$fp = fopen(DISCUZ_ROOT.'./data/cache/style_'.STYLEID.'_'.$_G['basescript'].'_'.CURMODULE.'.css', 'w')) {
				fwrite($fp, $this->csscurmodules);
				fclose($fp);
			} else {
				exit('Can not write to cache files, please check directory ./data/ and ./data/cache/ .');
			}
			$scripts[] = STYLEID.'_'.$_G['basescript'].'_'.CURMODULE;
		}
		$scriptcss = '';
		foreach($scripts as $css) {
			$scriptcss .= '<link rel="stylesheet" type="text/css" href="'.$_G['setting']['csspath'].$css.'.css?{VERHASH}" />';
		}
		$scriptcss .= '{if $_G[uid] && isset($_G[cookie][extstyle]) && strpos($_G[cookie][extstyle], TPLDIR) !== false}<link rel="stylesheet" id="css_extstyle" type="text/css" href="$_G[cookie][extstyle]/style.css" />{elseif $_G[style][defaultextstyle]}<link rel="stylesheet" id="css_extstyle" type="text/css" href="$_G[style][defaultextstyle]/style.css" />{/if}';
		return $scriptcss;
	}

	function cssvtags($param, $content) {
		global $_G;
		$modules = explode(',', $param);
		foreach($modules as $module) {
			$module .= '::'; //fix notice
			list($b, $m) = explode('::', $module);
			if($b && $b == $_G['basescript'] && (!$m || $m == CURMODULE)) {
				$this->csscurmodules .= $content;
				return;
			}
		}
		return;
	}

	function transamp($str) {
		$str = str_replace('&', '&amp;', $str);
		$str = str_replace('&amp;amp;', '&amp;', $str);
		$str = str_replace('\"', '"', $str);
		return $str;
	}

	function addquote($var) {
		return str_replace("\\\"", "\"", preg_replace("/\[([a-zA-Z0-9_\-\.\x7f-\xff]+)\]/s", "['\\1']", $var));
	}


	function stripvtags($expr, $statement = '') {
		$expr = str_replace("\\\"", "\"", preg_replace("/\<\?\=(\\\$.+?)\?\>/s", "\\1", $expr));
		$statement = str_replace("\\\"", "\"", $statement);
		return $expr.$statement;
	}

	function stripscriptamp($s, $extra) {
		$extra = str_replace('\\"', '"', $extra);
		$s = str_replace('&amp;', '&', $s);
		return "<script src=\"$s\" type=\"text/javascript\"$extra></script>";
	}

	function stripblock($var, $s) {
		$s = str_replace('\\"', '"', $s);
		$s = preg_replace("/<\?=\\\$(.+?)\?>/", "{\$\\1}", $s);
		preg_match_all("/<\?=(.+?)\?>/e", $s, $constary);
		$constadd = '';
		$constary[1] = array_unique($constary[1]);
		foreach($constary[1] as $const) {
			$constadd .= '$__'.$const.' = '.$const.';';
		}
		$s = preg_replace("/<\?=(.+?)\?>/", "{\$__\\1}", $s);
		$s = str_replace('?>', "\n\$$var .= <<<EOF\n", $s);
		$s = str_replace('<?', "\nEOF;\n", $s);
		$s = str_replace("\nphp ", "\n", $s);
		return "<?\n$constadd\$$var = <<<EOF\n".$s."\nEOF;\n?>";
	}

	function error($message, $tplname) {
		echo $message.'<br/>';
		echo $tplname;
		// discuz_error::template_error($message, $tplname);
	}

	/**
	 * 转换数据为HTML代码
	 * @param array $data 数组
	 */
	private function array2html($data) {
		if (is_array($data)) {
			$str = 'array(';
			foreach ($data as $key=>$val) {
				if (is_array($val)) {
					$str .= "'$key'=>".self::array2html($val).",";
				} else {
					if (strpos($val, '$')===0) {
						$str .= "'$key'=>$val,";
					} else {
						$str .= "'$key'=>'".daddslashes($val)."',";
					}
				}
			}
			return $str.')';
		}
		return false;
	}

}

?>