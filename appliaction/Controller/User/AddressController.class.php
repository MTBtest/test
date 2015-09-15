<?php
/**
 *	  会员中心 - 我的收藏夹
 *	  [Haidao] (C)2013-2099 Dmibox Science and technology co., LTD.
 *	  This is NOT a freeware, use is subject to license terms
 *
 *	  http://www.haidao.la
 *	  tel:400-600-2042
 */
class AddressController extends UserBaseController
{	
	public function _initialize() {
		parent::_initialize();
		$this->db = model('user_address');
        $this->user_db = model('user');   
    }
	
	/* 地区更改 */
	public function update() {
		$_GET['id'] = $_GET['id'];
        $info = $this->db->find($_GET['id']);
        if ($_GET['id'] && ($info['user_id'] != $this->userid)) showmessage('您无权修改该收货地址');
        $_GET['user_id'] = $this->userid;
		$result = $this->db->update($_GET);
		if(!$result) {
			showmessage($this->db->getError());
		} else {
			$id = ($_GET['id'] > 1) ?  $_GET['id'] : $result;
			$row = $this->db->detail($id);
			if ($_GET['status'] == "1") {
				$data['id'] = $this->userid;
				$data['address_id'] = $id;
				model('user')->update($data, FALSE);//修改 默认地址
			}
			showmessage('操作成功', U('User/Address/Address'), 1, '', '', '', $row);
		}
	}
	
	/**
	 * @author wj
	 * @date 2014-10-15
	 */
    public function address(){
        $model = model('UserAddress');
        $region = model('Region')->where(array('parent_id'=>1))->select();
        $map['user_id'] = $this->userid;
        $addressList = $model->getInfo($map);//获取地址列表
        unset($map);
        foreach($addressList as $k=>$v){//将地址编号替换为名称
            $province = $this->user_db->getCityByid($v['province']);
            $city = $this->user_db->getCityByid($v['city']);
            $district = $this->user_db->getCityByid($v['district']);
            $addressList[$k]['addressInfo'] = $province.' '.$city.' '.$district.' '.$v['address'];
        }
        if (IS_POST){
            if ($_GET['address_name']  == NULL) showmessage('收件人不能为空！');
            if ($_GET['address'] == NULL) showmessage('收货地址不能为空！');
            if ($_GET['zipcode'] == NULL) showmessage('邮编不能为空！');
            if (!is_numeric($_GET['zipcode'])) showmessage('请输入长度为6的数字！');
            if (!empty($_GET['mobile'])) {
                $mobile=is_mobile($_GET['mobile']);
                if (!$mobile) showmessage('请输入正确的手机号码!');                
            }
            if ($_GET['province']==-1) showmessage('请选择收货人省份！');
            if ($_GET['city']==-1) showmessage('请选择收货人城市！');
            if ($_GET['district']==-1) showmessage('请选择收货人地区！');            
            $this->update();
        }else{           
            $id = I('id');
            if (!empty($id)){//修改地址
                $map['id'] = array('eq',$id);
                $map['user_id'] = array('eq',$this->userid);
                $editList = model('UserAddress')->getInfo($map);
                $editList[0]['cityName'] = $this->user_db->getCityByid($editList[0]['city']);
                $editList[0]['provinceName'] = $this->user_db->getCityByid($editList[0]['province']);
                $editList[0]['districtName'] = $this->user_db->getCityByid($editList[0]['district']);
                $editList=$editList[0];
            }
            $SEO=seo(0,"我的收货地址信息");
            include template('address');            
        }        
    }
    
     /**
     * ajax获取地区
     *@author wj 
     *@date  2014-10-14
     */
    public function getArea() {
        $where['parent_id'] = $_GET['areaId'];
        $area = M('region')->where($where)->select();
        $this->ajaxReturn($area);
    }
    
    /**
     * 
     * ajax删除地址
     * @author wj 
     *@date  2014-10-14
     */
    public function addressDel(){
        $id = (int)$this->_post('id');
        if (!$id) showmessage('删除的对象不存在！');
        $map['id'] = array('eq',$id);
        $map['user_id'] = array('eq',$this->userid);
        $res = model('UserAddress')->where($map)->delete();
        $address_id = $this->user_db->getFieldById($this->userid,'address_id');
        if ($id == $address_id) {
            $address = $this->user_db->where(array('id'=>$this->userid))->setFIeld('address_id','');
        }
        if ($id == $address_id) {
            if (!$address) showmessage('删除失败！');
            showmessage('删除成功！',U('User/address/address'),1);
        }else {
            if (!$res) showmessage('删除失败！');
            showmessage('删除成功！',U('User/address/address'),1);
        }
    }
    
    /**
    * 
    * 设置默认地址
    * 
    * @author wj
    * @date 2014-10-15
    */
    public function addressDefaut(){
        $id = I('id');
        if (empty($id)){
            showmessage('设置失败！');
        }
        $data['address_id'] =$id;
        $map['id'] = array('eq',$this->userid);
        $res = $this->user_db->where($map)->save($data);
        if ($res>0){
            showmessage('设置成功！','',1);
        }else{
            showmessage('设置失败！','',1);
        }
    }
  
    /**
    * 
    * 编辑收货地址 [wap]
    * @author 老孔
    * @date 2015-04-23
    */  
    public function edit() {
        if (IS_POST){
            if ($_POST['address_name']  == NULL) showmessage('收货人不能为空！');
            if ($_POST['address'] == NULL) showmessage('收货地址不能为空！');
            if ($_POST['zipcode'] == NULL) showmessage('邮编不能为空！');
            if (!is_numeric($_POST['zipcode'])) showmessage('请输入长度为6的数字！');
            if (!empty($_POST['mobile'])) {
                $mobile=is_mobile($_POST['mobile']);
                if (!$mobile) showmessage('请输入正确的手机号码!');
            }
            if ($_POST['province']==-1) showmessage('请选择收货人省份！');
            if ($_POST['city']==-1) showmessage('请选择收货人城市！');
            if ($_POST['district']==-1) showmessage('请选择收货人地区！');
            $this->update();
        }else{
            $id = (int)I('get.id');
            if (!$id) showmessage('该收货地址有误');
            $info = $this->db->getInfo(array('id' => $id));
            $info = $info[0];
            if ($info['user_id'] != $this->userid) showmessage('您无权访问该收货地址');
            /* 获取城市数据 */
            $region = model('region')->where(array('parent_id' => 1))->select();
            $city = model('region')->where(array('parent_id' => $info['province']))->select();
            $district = model('region')->where(array('parent_id' => $info['city']))->select();
            include template('address_edit');
        }        
    }

    /**
    * 
    * 添加收货地址 [wap]
    * @author 老孔
    * @date 2015-04-23
    */  
    public function add() {        
        /* 获取城市数据 */
        $region = model('region')->where(array('parent_id' => 1))->select();
        $city = model('region')->where(array('parent_id' => $info['province']))->select();
        $district = model('region')->where(array('parent_id' => $info['city']))->select();
        include template('address_add');
    }
}