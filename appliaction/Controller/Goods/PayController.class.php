<?php 
class PayController extends UserBaseController
{
    public function _initialize() {
        parent::_initialize();
        $this->order_db=model('order');
    }
    public function wechatWapPay($order_sn,$pay_code){
        if(empty($order_sn)) showmessage('请勿非法访问');
        $u_info = getUserInfo();
        $rs = $this->order_db->where(array('order_sn' => $order_sn))->find();
        if(!$rs || $rs['user_id'] != $u_info['id']) showmessage('抱歉，您无法查看此订单');
        $payment = getcache('payment', 'pay');
        if($rs['pay_status'] == 1) showmessage("该订单已支付");
            // 本次应付总金额
            $total_fee = round($rs['real_amount'] - $rs['balance_amount'], 2);
        if (C('enable') == 1 && $user_money_check == 1 && $u_info['user_money'] > 0) {
                // 实际扣除金额
                $pay_money = ($u_info['user_money'] < $total_fee) ? $u_info['user_money'] : $total_fee;
                $total_fee = $total_fee - $pay_money;
        }
        $this->order_db->update(array('id' => $rs['id'], 'pay_code' => $pay_code));
        $product_info = array();
        $product_info['trade_sn'] = $rs['order_sn'];
        $product_info['total_fee'] = $total_fee;
        $product_info['subject'] = '订单号：'.$rs['order_sn'];
        $product_info['pay_bank'] = $pay_bank;
        libfile('pay_factory');
        $pay_factory =  new pay_factory($pay_code);
        $pay_factory->set_productinfo($product_info);
        $jsApiData_json=$pay_factory->get_code_url();
        include template('wechat_wap_pay');
    }  
}