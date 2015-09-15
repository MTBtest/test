<?php
/**
 *	  [Haidao] (C)2013-2099 Dmibox Science and technology co., LTD.
 *	  This is NOT a freeware, use is subject to license terms
 *
 *	  http://www.haidao.la
 *	  tel:400-600-2042
 */


class SiteController extends AdminBaseController {
	public function _initialize() {
		parent::_initialize();
	}

	public function setup() {
		$showpage=$this->_get("showpage","intval",0);
		include $this->admin_tpl('site_setup');
	}
    
    /* 注册、三方登录 */
	public function reg() {
		// 获取三方登录登录配置文件
		libfile('Xml');
		$folders = glob(EXTEND_PATH.'Driver/login/*');
		foreach ($folders as $key => $folder) {
            $file = $folder. DIRECTORY_SEPARATOR .'config.xml';
            if(file_exists($file)) {
                $importtxt = @implode('', file($file));
                $xmldata = xml2array($importtxt);
                $xmls[$xmldata['code']] = $xmldata;
            }
        }
		$_logins = getcache('login','site');
		$logins = array_merge_multi($xmls , $_logins);
		include $this->admin_tpl('site_reg');
	}

	public function mail() {
	   include $this->admin_tpl('site_mail');
	}
    public function weixin() {
    	include $this->admin_tpl("weixin_bind");
	}
	public function insert() {
		$file = $this->_post('files');
		if(IS_POST) {
			unset($_POST['x']);
			unset($_POST['y']);
			unset($_POST['files']);
			unset($_POST[C('TOKEN_NAME')]);
			//此处对微信公众号设置进行判断，如果关闭，则删除微信端菜单
			if($_GET['ct']=='weixin'){
			if(C('weixin_status') == 0){
		    		R('Admin/Menu/delete_menu');
		    	}
		    }
			//生成密钥
			$site_key =  C('site_key') ;
			$site_key = isset($site_key) ? $site_key : generate_password();
			$_POST['site_key'] = $site_key;
            if (file_exists(CONF_PATH . $file)){
				if ($this->update_config(daddslashes($_POST), CONF_PATH . $file)) {
					showmessage('操作成功', '', -1);
				} else {
				  showmessage('操作失败');
				}	
			}else{
				$conf_file=file_put_contents(CONF_PATH . $file, '<?php return array(); ?>');
				if ($this->update_config(daddslashes($_POST), CONF_PATH . $file)) {
					showmessage('操作成功', '', -1);
				} else {
				  showmessage('操作失败');
				}	
			}		
		} else {
			showmessage('请勿非法访问');
		}

	}


    private function update_config($config, $config_file = '') {
        if(empty($config_file)) return false;
        if($fp = @fopen($config_file, 'wb')) {
            fwrite($fp, "<?php \nreturn " . stripslashes(var_export($config, true)) . ";");
            fclose($fp);
            return true;
        } else {
            return false;
        }
    }

	public function testmail() {
		if(IS_POST){
			libfile('Smtp');
			$map=array();
			$map['code']='email';
			$config_json=model('notify')->where($map)->getField('config');
		    $config=json_decode($config_json,true);
		    $smtpserver = $config['mail_smtpserver'];
			$port		 = $config['mail_smtpport'];
			$smtpuser	= $config['mail_mailuser'];
			$smtppwd	 = $config['mail_mailpass'];
			$mailtype = "TXT";
			$sender = $_GET['formuser'];
			$smtp = new Smtp($smtpserver, $port, true, $smtpuser, $smtppwd, $sender);
			$to = $_GET['touser'];
			$subject = '
			
			来自'.C('site_name').'测试邮件@'.date('Y-m-d H:i:s',  time());
			$code = C('site_url') . U('Index/resetpwd', array('uid' => $list['id'], 'code' => md5($list['id'] . $list['password'] . $list['email']), 'resettime' => time()));
			$body = '这是一封测试邮件';
			$send = $smtp->sendmail($to, $sender, $subject, $body, $mailtype);
			showmessage('请访问你的邮箱 ' . $list['email'] . ' 查看测试邮件!<br/>');
		}else{
			include $this->admin_tpl('site_testmail');
		}
	}

}
