<?php
/**
 *      第三方登录
 *      [Haidao] (C)2013-2099 Dmibox Science and technology co., LTD.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      http://www.haidao.la
 *      tel:400-600-2042
 */

class ThirdLoginController extends BaseController {

	public function _initialize() {
		parent::_initialize();
		$this->db = model('user');
		$this->oauth_db = model('user_oauth');
	}


	/**
	* 
	* 第三方登录后返回登录信息
	*
	* @date 2015-07-29
	* 
	*/
	public function login_return() {
		$url_forward = $_GET['url_forward'] ? htmlspecialchars_decode($_GET['url_forward']) : U('User/Index/index');
		if (IS_POST) {
			extract($_GET);
			if (empty($type))   showmessage('第三方登录类型有误！');
			if (empty($openid)) showmessage('第三方登录会员信息有误！');
			$sqlmap = array();
			$sqlmap['username'] = $username;
			$userInfo = $this->db->where($sqlmap)->find();
			if (!$userInfo || $userInfo['password'] !== md5($userInfo['valid'].$password)) {
				showmessage('用户名或密码错误');
			} else {
				switch ($userinfo['status']) {
					case '-1':
						showmessage('该账号已被删除！');
						break;
					case '0':
						showmessage('该账号未认证！');
						break;
					case '2':
						showmessage('该账号已被禁用！');
						break;
					default:
						// 绑定第三方账号
						$result = $this->check_bind($userInfo['id'] , $type);
						if ($result) {
							showmessage('该第三方登录账号已绑定！');
						}
						$data = array();
						$data['user_id']  = $userInfo['id'];
						$data['openid']   = $openid;
						$data['type']     = $type;
						$data['dateline'] = NOW_TIME;
						$_result = $this->oauth_db->add($data);
						if (!$_result) showmessage('第三方登录绑定失败，请从新绑定');
						$this->putlogin($userInfo['id'], $userInfo['username']);
						action_exp($userInfo['id'], 0);
						runhook('user_login_success');
						showmessage('第三方登录绑定成功', $url_forward, 1);
						break;
				}
			}
		} else {
			$result = $this->check_user($_GET['user_id'] , $_GET['login_code']);
			if (!$result) {	// 未绑定
				$_GET['openid'] = $_GET['user_id'];
				$SEO = seo(0 ,'第三方登录账号绑定');
				include template('login_return');
			} else {
				$userInfo = $this->db->find($result['user_id']);
				if (!$userInfo) showmessage('该用户不存在');
				switch ($userInfo['status']) {
					case '-1':
						showmessage('该账号已被删除！');
						break;
					case '0':
						showmessage('该账号未认证！');
						break;
					case '2':
						showmessage('该账号已被禁用！');
						break;
					default:
						$this->putlogin($userInfo['id'], $userInfo['username']);
						action_exp($userInfo['id'], 0);
						runhook('user_login_success');
						showmessage('登录成功', $url_forward, 1);
						break;
				}
			}
		}
	}

	/* 第三方登录注册并绑定 */
	public function reg_band() {
		$url_forward = $_GET['url_forward'] ? htmlspecialchars_decode($_GET['url_forward']) : U('User/Index/index');
		if (IS_AJAX) {
			if(C('reg_isreg') == 0) showmessage('站点已关闭注册，绑定失败');
			if (empty($_GET['type']))   showmessage('第三方登录类型有误！');
			if (empty($_GET['openid'])) showmessage('第三方登录会员信息有误！');
			$username = trim($_GET['username']);
			$password = trim($_GET['password']);
			$confirm_pwd = trim($_GET['confirm_pwd']);
			if ($username == '' || is_numeric($username) || is_email($username)) {
				showmessage('用户名不能为纯数字或邮箱');
			}
			if ($password == '' || $password !== $confirm_pwd) {
				showmessage('两次密码不一致');
			}
			$data = array();
			$data['username'] = $username;
			$data['valid'] = random(10);
			$data['password'] = $password ? md5($data['valid'].$password) : false;
			$result_uid = $this->db->update($data);
			if($result_uid){
				// 绑定第三方账号
				$_result = $this->check_bind($result_uid , $_GET['type']);
				if ($_result) {
					showmessage('该第三方登录账号已绑定！');
				}
				$data = array();
				$data['user_id']  = $result_uid;
				$data['openid']   = $_GET['openid'];
				$data['type']     = $_GET['type'];
				$data['dateline'] = NOW_TIME;
				$result_add = $this->oauth_db->add($data);
				if (!$result_add) showmessage('第三方登录绑定失败，请从新绑定');
				$this->putlogin($result_uid , $username);
				runhook('user_register_success');
                runhook('n_reg_success',array('user_id'=>$result_uid));
                runhook('user_login_success');
				showmessage('第三方登录绑定成功', $url_forward, 1);
			}else{
				showmessage($this->db->getError());
			}
		} else {
			showmessage('请勿非法访问！');
		}
	}

	/**
	* 
	* 验证通过 生成cookie 或session
	* @param  $arr 用户信息
	*/
	private function putlogin($uid, $uname) {
		if (empty($uid))  return FALSE;
		cookie('_uname', $uname);
		cookie('user_key', authcode($uid, 'ENCODE', C('site_key')));
		$data['last_session'] = session_id();
		model('user')->where(array('id' => $uid))->save($data);
		return true;
	}

	public function _empty() {
        $method = '_'.htmlspecialchars($_GET['method']);
        unset($_GET['method']);
		libfile('login_factory');
		$login_factory =  new login_factory(ACTION_NAME);
		if($_GET['login_code']=='qq'){
			$user_info = $login_factory->$method();
			$_GET['user_id'] = $user_info['openid'];
			$_GET['username'] = $user_info['username'];
		}else{
			$_GET['user_id']=$login_factory->$method();
		}
		unset($_GET['code']);
		if($_GET['user_id']){
			redirect(U('User/ThirdLogin/login_return',$_GET));
		}else{
			redirect(U('User/ThirdLogin/login_error'));
		}
	}

	public function login_error(){
		showmessage('登录失败，请检查第三方登录配置是否正确',U('User/Public/login'),0);
	}

	/* 检测第三方绑定信息 */
	private function check_user($openid , $type) {
		$sqlmap           = array();
		$sqlmap['openid'] = $openid;
		$sqlmap['type']   = $type;
		return $this->oauth_db->where($sqlmap)->find();
	}

	/*检查帐号是否绑定*/
	private function check_bind($user_id , $type) {
		$sqlmap           = array();
		$sqlmap['user_id'] = $user_id;
		$sqlmap['type']   = $type;
		return $this->oauth_db->where($sqlmap)->find();
	}
}