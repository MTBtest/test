<?php
/**
 *      [Haidao] (C)2013-2099 Dmibox Science and technology co., LTD.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      http://www.haidao.la
 *      tel:400-600-2042
 */
libfile('notify_abstract');
class weixin extends notify_abstract {
	public $order_info = array();
	public $wechat = '';
	public function __construct($param) {
		if (!empty($param['config'])) $this->set_config($param['config']);
		$this->param = $param;
		$this->order_info = model('order')->where(array('order_sn' => $param['param']['order_sn']))->find();
		$this->template = model('notify_template')->find($param['param']['id']);
		$this->template = json_decode($this->template['template'],TRUE);
		$user_id = ($param['param']['user_id']) ? $param['param']['user_id'] : $this->order_info['user_id'];
		$this->openid = model('user_oauth')->where(array('user_id'=> $user_id ,'type' => 'wechat'))->getfield('openid');
		if (!$this->openid) return FALSE;
		$this->$param['param']['id']($param);
	}

	/* 确认订单 */
	public function n_confirm_order($param) {
		$message = array();
		$message['touser'] = $this->openid;
		$message['template_id'] = $this->template['weixin'];
		$message['url'] = U('User/Order/detail', array('order_sn' => $param['param']['order_sn']), '',FALSE, TRUE);
		$message['topcolor'] = '#FF0000';
		//头部信息
		$message['data']['first']['value'] = '您的订单已经确认';
		//订单号
		$message['data']['orderno']['value'] = $param['param']['order_sn'];
		//订单金额
		$message['data']['amount']['value'] = $this->order_info['real_amount'].'元';
		//备注
		$message['data']['remark']['value'] = '您的订单正在配货';
        $this->send($message);
	}

	/* 订单发货 */	
	public function n_order_delivery($param) {
		$message = array();
		$message['touser'] = $this->openid;
		$message['template_id'] = $this->template['weixin'];
		$message['url'] = U('User/Order/detail', array('order_sn' => $param['param']['order_sn']), '',FALSE, TRUE);
		$message['topcolor'] = '#FF0000';
		//头部信息
		$message['data']['first']['value'] = '您的订单已发货';
		//订单号
		$message['data']['keyword1']['value'] = $param['param']['order_sn'];
		//快递公司
		$message['data']['keyword2']['value'] = $this->order_info['delivery_txt'];
		//快递单号
		$message['data']['keyword3']['value'] = ($this->order_info['delivery_sn']) ? $this->order_info['delivery_sn'] : '暂无';
		//备注
		$message['data']['remark']['value'] = '您的订单已经发货';
        $this->send($message);
	}

	/* 下单成功 */
	public function n_order_success() {
		$message = array();
		$message['touser'] = $this->openid;
		$message['template_id'] = $this->template['weixin'];
		$message['url'] = U('User/Order/detail', array('order_sn' => $this->order_info['order_sn']), '',FALSE, TRUE);
		$message['topcolor'] = '#FF0000';
		//头部信息
		$message['data']['first']['value'] = '您的订单提交已成功';
		//订单号
		$message['data']['keyword1']['value'] = $this->order_info['order_sn'];
		//下单时间
		$message['data']['keyword2']['value'] = mdate($this->order_info['create_time']);
		//订单金额
		$message['data']['keyword3']['value'] = $this->order_info['real_amount'].'元';
		//支付方式
		$message['data']['keyword4']['value'] = ($this->order_info['pay_code'] == 0) ? '在线支付' : '货到付款';
		//备注
		$message['data']['remark']['value'] = ($this->order_info['pay_code'] == 0) ? '您提交了订单，系统正在等待付款' : '您提交了订单，请等待系统确认';
        $this->send($message);
	}

	/* 付款成功 */
	public function n_pay_success() {
		$message = array();
		$message['touser'] = $this->openid;
		$message['template_id'] = $this->template['weixin'];
		$message['url'] = U('User/Order/detail', array('order_sn' => $this->order_info['order_sn']), '',FALSE, TRUE);
		$message['topcolor'] = '#FF0000';
		//头部信息
		$message['data']['first']['value'] = '您好，您的订单已付款成功';
		//订单号
		$message['data']['keyword1']['value'] = $this->order_info['order_sn'];
		//支付时间
		$message['data']['keyword2']['value'] = mdate($this->order_info['pay_time']);
		//支付金额
		$message['data']['keyword3']['value'] = $this->order_info['real_amount'].'元';
		//支付方式
		$message['data']['keyword4']['value'] = ($this->order_info['pay_code'] == 0) ? '在线支付' : '货到付款';
		//备注
		$message['data']['remark']['value'] = '感谢您的惠顾！';
        $this->send($message);
	}

	/* 充值成功 */
	public function n_recharge_success($param) {
		$pay_info = model('pay')->where(array('trade_sn' => $param['param']['order_sn']))->find();
		$message = array();
		$message['touser'] = $this->openid;
		$message['template_id'] = $this->template['weixin'];
		$message['url'] = U('User/Index/index', '', '',FALSE, TRUE);
		$message['topcolor'] = '#488bcb';
		//头部信息
		$message['data']['first']['value'] = '您好，您的账号已充值成功！';
		//会员账号
		$message['data']['accountType']['value'] = '会员账号';
		//用户名
		$message['data']['account']['value'] = getMemberfield($param['param']['user_id'],'username');
		//充值金额
		$message['data']['amount']['value'] = $pay_info['total_fee'].'元';
		//充值状态
		$message['data']['result']['value'] = ($pay_info['status'] == 1) ? '充值成功' : '充值有误';
		//备注
		$message['data']['remark']['value'] = '如有疑问，请致电联系我们。';
        $this->send($message);
	}

	/* 余额变动 */
	public function n_money_change() {
		$money_log = model('user_moneylog')->find($this->param['param']['log_id']);
		$message = array();
		$message['touser'] = $this->openid;
		$message['template_id'] = $this->template['weixin'];
		$message['url'] = U('User/Index/index', '', '',FALSE, TRUE);
		$message['topcolor'] = '#488bcb';
		//头部信息
		$message['data']['first']['value'] = '尊敬的用户'.getMemberfield($this->param['param']['user_id'],'username').'，您的账户余额发生变化';
		//变动金额
		$message['data']['keyword1']['value'] = ((count(explode('-',$money_log['money'])) > 1) ? '减少 ' : '增加 ').$money_log['money'].'元';
		//账户余额
		$message['data']['keyword2']['value'] = getMemberfield($this->param['param']['user_id'],'user_money').'元';
		//备注
		$message['data']['remark']['value'] = '变更时间：'.mdate($money_log['dateline']);
        $this->send($message);
	}
    public function n_reg_success() {
		
	}
	/* 执行发送模版信息 */
	public function send(&$message) {
		libfile('Wechat');
		$wechat = new Wechat($this->param['config']);
		$wechat->sendTemplateMessage($message);
	}
	
}