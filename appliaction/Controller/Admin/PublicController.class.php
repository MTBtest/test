<?php
class PublicController extends BaseController {
    public function _initialize() {
        parent::_initialize();
        $this->db = model('AdminUser');
    }

    public function login() {

    	if(session('?ADMIN_ID')){
    		$this->redirect(U('Index/index'));
    	}
        if (IS_POST) {
            if (strtolower(I('userverify')) != session('verify')) {
                showmessage('验证码不正确');
            }
            //和数据库校对
            $username = $_GET['username'];
            $userpass = $_GET['userpass'];
            $result = $this->db->login($username, $userpass);
            if (!$result) {
                showmessage($this->db->getError());
            } else {
            	/* 检查缓存 */
				R('Admin/Cache/check_cache');
                showmessage('后台登录成功', U('Index/index'), 1);
            }
        } else {
            include $this->admin_tpl('login');
        }
    }

    /**
     * 验证码
     */
    public function verify() {
        libfile('Verify');
        ob_clean();
        $verify = new Verify($width=90,$height=24,$fontSize=14);
        $verify->doimage();
    }

    /**
     * 退出登录
     */
    public function logout() {
        $this->db->logout();
        $this->redirect(U('Public/login'));
    }

    /**
     * 清除缓存
     */
    public function clearCache(){
        clearCache();
		showmessage('更新缓存成功', '', 1);
    }
}
