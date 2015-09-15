<?php
/**
 *      会员中心 - 我的收藏夹
 *      [Haidao] (C)2013-2099 Dmibox Science and technology co., LTD.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      http://www.haidao.la
 *      tel:400-600-2042
 */
class ApiController extends ApiBaseController
{
    public function _initialize() {
        parent::_initialize();
        $this->user = model('User');
    }

    /**
    * 
    * 会员注册[wap]
    * @param  $info 表单提交信息
    * @author 老孔
    * @date 2015-04-22
    */
    public function reg_wap() {
    	if(C('reg_isreg') == 0) showmessage('站点已关闭注册');
    	$info = I('post.');
    	if (!$info['password']) showmessage('请输入密码');
    	$info['valid'] = random(10);
		$info['password'] = md5($info['valid'].$info['password']);
		$info['userpassword2'] = md5($info['valid'].$info['userpassword2']);
    	$result = $this->user->update($info);
    	if (!$result) showmessage($this->user->getError());
    	runhook('user_register_success');
		$this->putlogin($result, $_GET['username']);
		showmessage('注册成功',U('User/Index/index'),1);
    }

    /**
    * 
    * 验证通过 生成cookie 或session
    * @author 老孔
    * @date 2015-04-22
    */
    private function putlogin($uid, $uname) {
        if (empty($uid)) return FALSE;
        cookie('_uid', $uid);
        cookie('_uname', $uname);
        cookie('user_key', authcode($uid, 'ENCODE', C('site_key')));
        $data['last_session'] = session_id();
        $this->user->where('id='.$uid.'')->save($data);
        return true;
    }

    /**
    * 
    * 会员登录
    * @param  $info 登录提交信息
    * @author 老孔
    * @date 2015-04-22
    */
    public function login_wap() {
    	$info = $_GET;
        $url_forward = $_GET['url_forward'] ? htmlspecialchars_decode($_GET['url_forward']) : U('User/Index/index');
    	if (!$info['account']) showmessage('请输入用户名/手机/邮箱！');
    	if (!$info['password']) showmessage('请输入密码！');
    	if ($info['cookie'] == 1) cookie('_uname',$info['account']);
    	/* 判断登录用户名类型 */
    	if (preg_match("/^1[34578]\d{9}$/", $info['account'])) {
    		$type = "mobile_phone";
    	}elseif(preg_match("/^([0-9A-Za-z\\-_\\.]+)@([0-9a-z]+\\.[a-z]{2,3}(\\.[a-z]{2})?)$/i", $info['account'])) {
    		$type = "email";
    	}else{
    		$type = "username";
    	}
    	$userInfo = $this->user->where(array($type => $info['account']))->find();
    	if (!$userInfo || $userInfo['password'] !== md5($userInfo['valid'].$info['password'])) {
            showmessage('用户名或密码错误');
        } else {
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
					// 绑定微信openid
					if ($info['wechat_openid']) {
						$wechat_openid = urldecode(authcode(trim($info['wechat_openid']),'DECODE'));
						$is_wechat = model('user_oauth')->where(array('openid' => $wechat_openid))->count();
						if ($is_wechat == 0) {
							$wechat_data = array();
							$wechat_data['user_id'] = $userInfo['id'];
							$wechat_data['openid'] = $wechat_openid;
							$wechat_data['type'] = 'wechat';
							$wechat_data['dateline'] = NOW_TIME;
							$rs=model('user_oauth')->add($wechat_data);
                            if($rs){
                                showmessage('绑定微信号成功', $url_forward, 1);
                            }
						}
					}
					showmessage('登录成功', $url_forward, 1);
                    break;
            }
        }
    }

    /* 第三方登录 */
    public function third_login () {
        $logins = getcache('login','site');
        if (empty($logins[$_GET['login_code']])) {
            showmessage('该登录方式不存在');
        }
        if ($logins[$_GET['login_code']]['enabled'] != 1) {
            showmessage('未开启该登录，请尝试其他登录方式');
        }
        libfile('login_factory');
        $login_factory = new login_factory($logins[$_GET['login_code']]['login_code']);
        $login_url = $login_factory->get_code();
        if (empty($login_url)) showmessage('第三方登录地址链接有误！请联系管理员');
        showmessage('第三方登录地址获取成功！' , $login_url, 1);
    }
}