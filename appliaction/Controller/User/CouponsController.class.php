<?php
/**
 *      会员中心 - 我的收藏夹
 *      [Haidao] (C)2013-2099 Dmibox Science and technology co., LTD.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      http://www.haidao.la
 *      tel:400-600-2042
 */
class CouponsController extends UserBaseController
{
    public function _initialize() {
        parent::_initialize();
        $this->db = model('Coupons');
    }


     /**
    * 
    * 代金卷
    * @author wj
    * @date 2014-10-17
    * 
    */
   public function couponsList(){
        $sqlmap =array();
        $sqlmap['user_id'] = array('EQ',$this->userid);
        $sqlmap['status'] = array('NEQ',3);
        $type =I('type','all');
        
        if ($type==1){
           // $sql .= ' and status=1 and end_time>'.time();
            $sqlmap['status'] = array('EQ',1);
            $sqlmap['end_time'] = array('GT',time());
        }elseif($type == 2){
           // $sql .= ' and (status=2 or end_time<'.time().')';
            $smap['status'] = array('EQ',2);
            $smap['end_time'] = array('lT',time());
            $smap['_logic']='OR';
            $sqlmap['_complex']=$smap;
        }elseif ($type== 'all'){
            $type='all';
        }
		$coupons_ids = model('Coupons')->getField('id', TRUE);
		$sqlmap['cid'] = array("IN", $coupons_ids);

		$count = model('CouponsList')->where($sqlmap)->count();
		libfile('Page');
		$Page = new Page($count,8);
        $Page->setConfig('prev','');
        $Page->setConfig('next','');
        $Page->setConfig('theme','%upPage% %linkPage% %downPage%');
        $Page->listRows = 8;
		$list['list'] = model('CouponsList')->where($sqlmap)->page(PAGE,8)->order('id DESC')->select();
		$list['page'] = $Page->show();
		//var_dump(model('CouponsList')->getLastSql());
        $list['c'] =$count;
        $list = $list;
       $SEO=seo(0,"我的优惠券信息");
       include template('coupons');
   }
    /**
    * 
    * 添加代金卷
    * @author wj
    * @date 2014-10-17
    * 
    */
   public function addCoupons(){
        $key = $_GET['key'];
        if (empty($key)){
            showmessage('请输入序列号','',1);
        }
        $userInfoList = getUserInfo($this->userid);//获取用户详细信息
        $r = model('CouponsList')->checkKey($key);
        if (!$r){
            showmessage('该序列号不存在或已使用','',1);
        }
        $map['id'] = array('eq',$r);
        $data['user_id'] = $userInfoList['id'];
        $data['sn'] = $key;
        $data['user_id'] = $userInfoList['id'];
        $data['to_time'] = time();
        $data['user_name'] = $userInfoList['username'];
        $data['status'] = 1;
        $res = model('coupons_list')->where($map)->save($data);
        if ($res>0){
            showmessage('添加成功！','',1);
        }else{
            showmessage('添加失败！','',1);
        }
        
   }


   public function score(){
        $SEO=seo(0,"我的积分信息");
        $score = model('user_pointslog')->where(array('user_id'=>$this->userid))->order('time DESC')->select();
        include template('score');
   }
   
    
   
}