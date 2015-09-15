<?php
/**
 *      会员中心 - 我的收藏夹
 *      [Haidao] (C)2013-2099 Dmibox Science and technology co., LTD.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      http://www.haidao.la
 *      tel:400-600-2042
 */
class NoticeController extends UserBaseController
{
    public function _initialize() {
        parent::_initialize();
        $this->db = model('Sms');
    }


    /**
    * 站内信
    * @author wj
    * @date 2014-10-16
    */
   public function notice(){
       
        $stype = I('stype');
        $map['stype'] = array('eq',$stype);
        $map['user_id'] = array('eq',$this->userid);
        $type =I('type','all');
        if ($type == 'all') {
            $list = model('Sms')->getList($map);
        }else{
            $map['status'] = array('eq',$type);
            $list = model('Sms')->getList($map);
        }
        if ($stype == 0) {
            $msg = "我站内信信息";
        }elseif ($stype == 1) {
            $msg = "交易提醒";
        }else{
            $msg = "到货通知";
        }
        $SEO=seo(0,$msg);
        include template('notice');
       
   }




   public function change_status(){
        $id=I('id');
        $result=model('Sms')->where(array('id'=>$id))->save(array('status'=>1));
   }
    
   
}