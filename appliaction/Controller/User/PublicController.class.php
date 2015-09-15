<?php
class PublicController extends BaseController {
	public function _initialize() {
		parent::_initialize();
		$this->db = model('User');
	}

	public function login() {
			if(strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') != false ){
		    	libfile('Wechat');
		    	$options=array();
			    $options['token']=C('weixin_token');
			    $options['encodingAesKey']=md5(C('weixin_token'));
			    $options['appid']=C('appid');
			    $options['appsecret']=C('appsecret');
			    $weObj=new Wechat($options);
                if($_GET['bind']){
                	 if (!isset($_GET['code'])){
                	 	$redirectUrl = 'http://'.$_SERVER['HTTP_HOST'].U('login',array('bind'=>'account'));
                        $url = $weObj->getOauthRedirect($redirectUrl,1,'snsapi_base');
                        Header("Location: $url");
                	 }else{
                	 	$userinfo =$weObj->getOauthAccessToken();
                        $_GET['wechat_openid'] = authcode($userinfo['openid'],'ENCODE');
                	 }
                }
            }	
			$SEO=seo(0,"用户登录");
			// 第三方登录配置信息
			$logins = getcache('login','site');
			include template('login');
	}
	
	/**
	 * 会员注册
	 */
	public function reg() {
		if (IS_POST) {
			if(C('reg_isreg') == 0) {
				showmessage('站点已关闭注册');
			}
			$data = array();
			$data['username'] = $_GET['username'];
			$data['mobile_phone'] = $_GET['mobile_phone'];
			$data['email'] = $_GET['email'];
			$data['valid'] = random(10);
			$data['password'] = $_GET['password']?md5($data['valid'].$_GET['password']):false;
			$data['userpassword2']=md5($data['valid'].$_GET['userpassword2']);		
			$result = $this->db->update($data);
			if($result){
				runhook('user_register_success');
				$this->putlogin($result, $_GET['username']);
                action_exp($userinfo['id'], 0);
                runhook('n_reg_success',array('user_id'=>$result));
                runhook('user_login_success');
				showmessage('注册成功',U('User/Index/index'),1);
			}else{
				showmessage($this->db->getError());
			}
			
		} else {
			if (is_login()>0) {
				redirect(U("User/Index/index"));
			}
			$logins = getcache('login','site');
			$SEO = seo(0, '用户注册');
			include template('reg');
		}
	}
	/**
	 * 
	 * 验证注册
	 */
	public function checkReg() {
		if (C('reg_isreg') != 1) {//判断是否允许注册
			echo '非法操作！';
			exit;
		}
		$userModel = model('User');
		if (C('reg_regvalidation') == 0) {//是否需要验证
			$_POST['status'] = 1; //不需要验证，用户状态设置为1 表示可以使用
			$valiStats = false;
		} else {
			$_POST['status'] = 0; //表示未验证
			$valiStats = true;
		}
		$_POST['pay_points'] = C('reg_regintegral'); //赠送的积分
		$res = $userModel->create();
		if (!$res) {//字段验证失败
			echo $userModel->getError();
			exit;
		}
		$uid = $userModel->add();
		if ($uid <= 0) {
			echo '注册失败！';
		} else {
			if ($valiStats) {//需要验证
			} else {
				if (C('reg_regcoupons') != 0) {//赠送优惠券
					//赠送优惠劵流程
					$this->zsj($uid); //这个自己写
				}
				$userInf = array('uid' => $uid, 'uname' => $this->_post('user_name'),'ico'=>'');
				$this->loginOk($userInf);
			}
		}
	}
	
	public function logout() {
		cookie('_uname', null);
		cookie('user_key', null);
		runhook('user_logout_success');
		redirect(U('Public/login'));
	}

	/**
	 * 
	 * 使用AJAX判断字段唯一性
	 */
	public function ajaxCheckKey() {

		$val = $this->_post('param'); //值
		$key = $this->_post('name'); //键
		if($key == 'username'){
			if(is_numeric($val)){
				echo '{"info":"用户名不能为纯数字","status":"n"}';
				return false;
			}
			if(is_email($val)){
				echo '{"info":"邮箱不能作为用户名","status":"n"}';
				return false;
			}
		}
		$res = model('User/User')->checkKey($val, $key);
		if ($res) {
			echo '{"info":"通过信息验证","status":"y"}';
		} else {
			if ($this->_post('name') == 'mobile_phone') {
				$mes = '手机号码已存在';
			} elseif ($this->_post('name') == 'email') {
				$mes = '邮箱已存在';
			} elseif ($this->_post('name') == 'username') {
				$mes = '用户名存在';
			}
			echo '{"info":"' . $mes . '","status":"n"}';
		}
	}

   /**
	* 
	* 验证通过 生成cookie 或session
	* @param  $arr 用户信息
	* @author wj
	* @date 2014-10-13
	*/
	private function putlogin($uid, $uname) {
		if (empty($uid)) {
			return FALSE;
		}
		cookie('_uname', $uname);
		cookie('user_key', authcode($uid, 'ENCODE', C('site_key')));
		$data['last_session'] = session_id();
		model('User')->where('id='.$uid.'')->save($data);
		return true;
	}

	/*找回密码 */
	public function repwd(){
		if(IS_POST){
            if (!is_email($_GET['email'])) showmessage('邮箱格式有误');
			$email = model('User')->where(array('email'=>array('EQ',$_GET['email'])))->Find();
			if($email){
				$repwd_key = base64_encode(authcode($email['email'], 'ENCODE', $email['valid'], 3600 * 5)) ;
				$data['id'] = $email['id'];
				$data['repwd_key'] = $repwd_key;
				model('User')->update($data);
				runhook('n_back_pwd',array('user_id'=>$email['id'],'repwd_key'=>$repwd_key));
			}else{
				showmessage('没有找到相应邮箱!',NULL,0);
			}
		}else{
			$SEO = seo(0,"找回密码");
			include template('repwd');
		}
	}
	/*验证邮件*/
	public function findEmail(){
		$val = $this->_post('param'); //值
		$key = $this->_post('name'); //键
		$res = model('User/User')->checkKey($val, $key);
		if($res){
			echo '{"info":"邮箱不存在","status":"n"}';
		}else{
			echo '{"info":"邮箱正确","status":"y"}';
		}
	}
	/*通过邮件找回密码*/
	public function setrepwd(){
		if(IS_POST){
			$id = (int)$_POST['id'];
            if ($id < 1) showmessage('您的信息有误');
            $passowrd = trim($_POST['password']);
            $passowrd1 = trim($_POST['password1']);
            if (!$passowrd) showmessage('请输入新密码',U('User/Public/setrepwd',array('repwd_key'=>$_POST['repwd_key'])));
            if (!$passowrd1) showmessage('请输入确认密码',U('User/Public/setrepwd',array('repwd_key'=>$_POST['repwd_key'])));
            if ($passowrd != $passowrd1) showmessage('两次输入的密码不一致',U('User/Public/setrepwd',array('repwd_key'=>$_POST['repwd_key'])));
            $userinfo = model('user')->find($id);
            if ($userinfo['email'] != $_POST['email']) showmessage('您的信息有误，请重新获取链接',U('User/Public/repwd'));
            if ($_POST['repwd_key'] != $userinfo['repwd_key']) showmessage('找回密码的链接无效，请重新获取',U('User/Public/repwd'));

			$data = array();
            $data['id'] = $id;
			$data['valid'] = random(10);
			$data['password'] = md5($data['valid'].$_POST['password']);
			$data['repwd_key'] = NULL;
			$r = model('user')->save($data);
			if(!$r) showmessage('找回密码失败,请重新找回!',U('User/Public/repwd'));
			showmessage('密码已经成功更改,请重新登录!',U('User/Public/login'),1);			
		}else{
			$repwd_key = $_GET['repwd_key'];
			if (empty($repwd_key)){
				showmessage('对不起,地址不完整,请复制完整地址到浏览器地址栏访问');
			}
			$rs = model('user')->where(array('repwd_key'=>array('EQ',$repwd_key)))->Find();
			if(!isset($rs)){
				showmessage('无此用户!',U('User/Public/repwd'));
			}
			$repwd_de_key = base64_decode($repwd_key);
			$email = authcode($repwd_de_key,'DECODE',$rs['valid']);
			if($rs['email'] != $email){
				showmessage('找回密码的时间已经过期,请重新找回!');
			}
			include template('setrepwd');
		}		
	}
}
