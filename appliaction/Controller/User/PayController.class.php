<?php
/**
 *      会员中心 - 我的收藏夹
 *      [Haidao] (C)2013-2099 Dmibox Science and technology co., LTD.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      http://www.haidao.la
 *      tel:400-600-2042
 */
class PayController extends UserBaseController
{
    public function _initialize() {
        parent::_initialize();
        $this->db = model('pay');
        load('@.pay');
    }
    
    /* 账户余额充值 */
    public function init() {
        $SEO = seo(0,'账户余额充值');
        $userInfoList = getUserInfo($this->userid);//获取用户详细信息
    	$payment = getcache('payment', 'pay');
        if($payment['bank']['enabled']) $banks = explode(',', $payment['bank']['config']['banks']);
        if (IS_POST) {
            extract($_GET);
            if(empty($pay_code)) showmessage('请选择支付方式');
            if(!$payment[$pay_code]) showmessage('选择的支付方式不存在');
            if($pay_bank && !in_array($pay_bank, $banks)) showmessage('选择的支付网银错误');
            $total_fee = sprintf("%.2f",$_GET['total_fee']);
            if ($total_fee == 0) showmessage('充值金额不能为0');
            if ($total_fee < C('lowest')) showmessage('单次充值金额必须大于等于'.C('lowest').'元');
            $product_info = array();
            $product_info['user_id'] = $this->userid;
            $product_info['code'] = $pay_code;
            $product_info['trade_sn'] = 'cz'.create_sn();
            $product_info['total_fee'] = $total_fee;
            $product_info['subject'] = '账户余额充值';
            $product_info['dateline'] = NOW_TIME;
            $product_info['pay_bank'] = $pay_bank;
            $result = $this->db->update($product_info);
            if ($result) {
                libfile('pay_factory');
                $pay_factory =  new pay_factory($pay_code);
                $pay_factory->set_productinfo($product_info);
                if($pay_code=='wechat_qr'){
                    $trade_sn=$product_info['trade_sn'];
                    $code_url=$pay_factory->get_code_url();
                    include template('goods/wechat_recharge');
                }else{
                $pay_url = $pay_factory->get_code();
                redirect($pay_url);
             }
                
            } else {
                showmessage('充值请求创建失败，请稍候重试。');
            }
        } else {
            $applie = defined('IS_MOBILE') ? 'wap' : 'pc';
            foreach ($payment as $code => $pay) {
                if(!$pay['enabled'] || !$pay['isonline'] || $code == 'bank' || !preg_match("/".$applie."/",$pay['applies']) || !in_array($code, C('pays'))) {
                    unset($payment[$code]);continue;
                }
                $pay['config'] = json_decode($pay['config'], TRUE);
                $payment[$code] = $pay;
            }
            $banks = (in_array('bank',C('pays'))) ? $banks : FALSE;
            include template('pay');
        }
    }

    /* 充值成功 */
    public function pay_success(){
        showmessage('充值成功！请查收账户余额！',U('User/Index/index'),1);
    }

    /* 检测是否支付成功 */
    public function is_success() {
        $status = $this->db->where(array('user_id' => $this->userid))->order('id DESC')->getField('status');
        exit(json_encode(array('status' => $status)));
    }
    public function get_recharge_status($trade_sn) {
        $sqlmap=array();
        $sqlmap['user_id']=$this->userid;
        $sqlmap['trade_sn']=$trade_sn;
        $status = $this->db->where($sqlmap)->order('id DESC')->getField('status');
        exit(json_encode(array('status' => $status)));
    }
}