<?php 
class LoginController extends AdminBaseController
{
    protected $entrydir = '';
    protected $logins = array();

    public function _initialize() {
		parent::_initialize();
        libfile('Xml');
        $this->entrydir = EXTEND_PATH.'Driver/login/';
        $this->logins = getcache('login' ,'site');
	}

	/* 配置登录接口 */
	public function config($login_code = '') {
        $importfile = $this->entrydir . $login_code . '/config.xml';
        if(!file_exists($importfile)) {
            showmessage ('三方登录配置文件丢失');
        }
        $importtxt = @implode('', file($importfile));
        $xmldata = xml2array($importtxt);
		if (IS_POST) {
            $infoarr = array_merge_multi($xmldata, $_GET['info']);
            $xmldata = array2xml($infoarr);       
            $config = array();
            foreach($infoarr['config'] as $key => $value) {
                $config[$key] = $value['value'];
            }
            $data = array( $login_code => array(
                'login_code' => $infoarr['code'],
                'enabled'    => $infoarr['enabled'],
                'login_name' => $_GET['info']['login_name'],
                'config'     => $config,
                'login_desc' => $_GET['info']['login_desc']
            ));
            $data = array_merge_multi($this->logins , $data);
            setcache('login', $data, 'site');
            showmessage('操作成功', '',1);
		} else {
			include $this->admin_tpl('login_config');
		}
	}

    /* 卸载登录接口 */
    public function delete($login_code = '') {
        unset($this->logins[$login_code]);
        setcache('login', $this->logins, 'site');
        showmessage('操作成功', '',1);
    }
}