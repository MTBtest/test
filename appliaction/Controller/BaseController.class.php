<?php
/**
 *	  [Haidao] (C)2013-2099 Dmibox Science and technology co., LTD.
 *	  This is NOT a freeware, use is subject to license terms
 *
 *	  http://www.haidao.la
 *	  tel:400-600-2042
 */
class BaseController extends Action
{
    /* 系统全局初始化 */
    public function _initialize() {
        define('ROOT_PATH', __ROOT__.'/');
        $this->_xss_check();
        if(C('mobile_enabled')) {
            libfile('mobile');
            $mobile = new mobile;
            if($mobile->isMobile() === TRUE || stripos(strtolower($_SERVER['HTTP_HOST']), C('mobile_domain')) !== FALSE) {
                define('IS_MOBILE', TRUE);
                C('TMPL_THEME', 'wap');
                if(C('mobile_redirect') && C('mobile_domain') && stripos(strtolower($_SERVER['HTTP_HOST']), C('mobile_domain')) === FALSE && !defined('IN_ADMIN')) {
                    redirect((is_ssl() ? 'https://' : 'http://').C('mobile_domain'));
                }
            }
            if(strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') != false ){
                define('IS_WECHAT',TRUE);
            }
        }
    	/* 定义全局常量 */
        defined('JS_PATH')  or define('JS_PATH', ROOT_PATH.'statics/js/');
        defined('CSS_PATH') or define('CSS_PATH', ROOT_PATH.'statics/css/');
        defined('IMG_PATH') or define('IMG_PATH', ROOT_PATH.'statics/images/');
        defined('THEME_PATH') or define('THEME_PATH', ROOT_PATH.str_replace(DOC_ROOT, '', TMPL_PATH.C('TMPL_THEME').'/statics/'));
		defined('MONUNIT') or define('MONUNIT', getconfig('site_monetaryunit'));
        define('MAGIC_QUOTES_GPC', function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc());
        if(defined('MAGIC_QUOTES_GPC')) {
            $_GET = dstripslashes($_GET);
            $_POST = dstripslashes($_POST);
            $_COOKIE = dstripslashes($_COOKIE);
        }
        if(IS_POST && !empty($_POST)) {
            $_GET = array_merge($_GET, $_POST);
        }
        defined('PAGE') or define('PAGE', max(1, (int) $_GET[C('VAR_PAGE')]));
		foreach($_GET as $k => $v) {
			$_GET[$k] = daddslashes(dhtmlspecialchars($v));
		}
		if(!is_file('./install/install.lock') && is_dir('./install')){
			header('location:./install/index.php');
			exit();
        }
        /* 每个用户分配一个 key */
        cookie('user_key') or cookie('user_key', authcode('0', 'ENCODE'));
        runhook('system_init');
    }

	public function _empty() {
		showmessage('您的页面或地址有误');
	}
    
    private function _xss_check () {
		static $check = array('"', '>', '<', '\'', '(', ')', 'CONTENT-TRANSFER-ENCODING');
		if(isset($_GET['formhash']) && $_GET['formhash'] !== formhash()) {
			//system_error('request_tainting');
		}
		$temp = $_SERVER['REQUEST_URI'];
		if(!empty($temp)) {
			$temp = strtoupper(urldecode(urldecode($temp)));
			foreach ($check as $str) {
				if(strpos($temp, $str) !== false) {
                    die('您当前的访问请求当中含有非法字符，已经被系统拒绝');
				}
			}
		}
		return true;        
    }


    /**
	 * 加载后台模板
	 * @param  string $file   文件名
	 */
	final public static function admin_tpl($file = '') {
		$file = (empty($file)) ? MODULE_NAME.C('TMPL_FILE_DEPR').ACTION_NAME : $file;
		if(IN_ADMIN && $file == 'showmessage' || $file == 'header' || $file == 'copyright') {
			$_group_name = 'admin';
		}else{
			$_group_name = GROUP_NAME;
		}
        $tplfile = APP_PATH.C('DEFAULT_C_LAYER').DIRECTORY_SEPARATOR.ucwords($_group_name).DIRECTORY_SEPARATOR.'Template'.DIRECTORY_SEPARATOR.$file.'.tpl.php';
        if (!file_exists($tplfile)) {
            die('模板文件未找到：'.$tplfile);
        }
        return $tplfile;
    }

	final public static function load_config($file = '') {
		$file = CONF_PATH.$file.'.php';
		return include $file;
	}
}