<?php
/**
 *      会员中心 - 我的收藏夹
 *      [Haidao] (C)2013-2099 Dmibox Science and technology co., LTD.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      http://www.haidao.la
 *      tel:400-600-2042
 */
class UserController extends UserBaseController
{
    public function _initialize() {
        parent::_initialize();
        $this->db = model('User');
    }
    
    /**
     * 
     * 个人资料
     * @author wj 
     * @date  2014-10-14
     * 
     */
    public function userInfo(){      
        if (IS_POST){
            $uid = $this->userid;
            $data['id'] = $uid;
            $data['sex'] = $_GET['sex'];
            $data['true_name'] = $_GET['true_name'];
            $birthday = $_GET['birthday'];
            $data['birthday'] = strtotime($birthday);
            $data['qq'] = $_GET['qq'];
            $res = M('User')->save($data);
            if ($res>0){
                showmessage('个人信息修改成功！',U('user/userinfo'),1);
            }else{
                showmessage('个人信息修改失败！',U('user/userInfo'),0);
            }
            
        }else{
             $SEO=seo(0,"个人资料信息");
			 $userInfoList = getUserInfo($this->userid);
             include template('userinfo');
        }
    }

    public function update_name(){
        if (empty($_GET['name'])) {            
            showmessage('昵称不能为空');           
        }
        if($_GET['name'] == $this->userinfo['username']) {
            showmessage('昵称不能与当前昵称一致');
        }
        //验证修改的昵称是否使用
        if (!$this->db->unique_name($_GET['name'], $this->userid)) {
            showmessage('昵称已被使用！');
        }
        $result = $this->db->update(array('id' => $this->userid, 'username' => $_GET['name']), FALSE);
        if ($result) {
            cookie('_uname', null);
            cookie('user_key', null);
            runhook('user_logout_success');
            showmessage('修改成功', U('Public/login'),1);
          
        }else{
           showmessage('修改失败');
           
        }
    }
    /**
     * 
     * 账号信息
     * @author wj 
     * @date  2014-10-14
     * 
     */
    public function manage(){
        $userInfoList = getUserInfo($this->userid);//获取用户详细信息
        $address  = model('UserAddress')->where(array('id'=>$userInfoList['address_id']))->find();
        $userInfoList['userAddress']['province'] = model('User')->getCityByid($address['province']);//获取地址名称
        $userInfoList['userAddress']['city'] = model('User')->getCityByid($address['city']);
        $userInfoList['userAddress']['district'] = model('User')->getCityByid($address['district']);
        $uid = $userInfoList['id'];
        if(!empty($userInfoList['mobile_phone'])){//处理手机号码
            $userInfoList['phone'] = substr($userInfoList['mobile_phone'], 0, 3).'*****'.substr($userInfoList['mobile_phone'], -3, 3);
        }else{
            $userInfoList['phone'] = '还未绑定手机号码！';
        }
        if(!empty($userInfoList['email'])){//处理邮箱
            $temp = explode('@', $userInfoList['email']);
            $userInfoList['nemail'] = substr($userInfoList['email'], 0, 3).'*****'.$temp[1];
        }else{
            $userInfoList['nemail'] = '还未绑定邮箱！';
        }
        $user=$userInfoList;
        $SEO=seo(0,"个人账号信息");
        include template('manage');
    }
    
    /**
    * 
    * 账户安全
    * @author wj
    * @date 2014-10-15
    */
   public function security(){
        $userInfoList = getUserInfo($this->userid);//获取用户详细信息
        if(IS_POST){
        	$valid = random(10);
            $password = $this->_post('pwd');
            $r = model('User')->chekPassword($userInfoList['id'],$password);//检查密码是否正确
            if (!$r) showmessage('旧密码错误！');
            $newpwd  = trim($this->_post('newpwd'));
            $newpwd2 = trim($this->_post('newpwd2'));
            if (!$newpwd) showmessage('请输入新密码');
            if (!$newpwd2) showmessage('请输入确认密码');
            if ($newpwd!=$newpwd2) showmessage('两次密码不一致,请重新输入');
			$data['valid'] = $valid;
            $data['id'] = $userInfoList['id'];
            $data['password'] = md5($valid.$newpwd);
            $res = model('User')->save($data);
            if (!$res>0) showmessage('修改失败！');
            showmessage('修改成功！',U('User/index/index'),1);            
        }else{
            $SEO=seo(0,"账户安全信息");
            include template('security');
        }
   }
   /**
    * 
    * 头像设置 展示页面
    * @author wj
    * @date 2014-10-15
    */
   public function avatar(){
    $userInfoList = getUserInfo($this->userid);//获取用户详细信息
    $ico = $userInfoList['ico'];
    $SEO=seo(0,"个人头像信息");
    include template('avatar');
   }
    /**
    * 
    * 头像设置 修改
    * @author wj
    * @date 2014-10-15
    */
   public function editAvatar(){
        $id = $_GET('id');
        if (empty($id)){
            showmessage('头像设置失败！');
        }
        $userInfoList = getUserInfo($id);//获取用户详细信息
        if(!empty($userInfoList['ico'])){
            showmessage('头像设置成功！','',1);
        }
        $map['id'] = array('eq',$userInfoList['id']);
        $data['ico'] = '/uploadfile/avatar/'.$userInfoList['id'].'_200.jpg';
        $res = model('User')->where($map)->save($data);
        if ($res>0){
            showmessage('头像设置成功！','',1);
        }else{
           showmessage('头像设置失败！','',1);
        }
   }
    /**
    * 
    * 修改电话 [wap]
    * @author  xvzhonglin
    * @date 2015-4-28
    */	
    public function update_phone() {
        if (IS_POST) {
            $update_phone = $_GET['update_phone'];
			if(preg_match('#^13[\d]{9}$|14^[0-9]\d{8}|^15[0-9]\d{8}$|^18[0-9]\d{8}$#', $update_phone))
				{
				$result = $this->db->where(array('id' => $this->userid))->setField('mobile_phone',$update_phone);
				if (!result) showmessage('请输入正确的手机号');
				showmessage('手机号码修改成功',U('User/User/userinfo'),1);
				} else {
				$mobile_phone = $this->db->getFieldById($this->userid,'mobile_phone');
				showmessage('请输入正确的手机号',U('User/User/update_phone'));
				}
				}else{
		include template ('update_phone');
        }		
    }
}