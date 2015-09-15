<?php
/**
 *      [Haidao] (C)2013-2099 Dmibox Science and technology co., LTD.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      http://www.haidao.la
 *      tel:400-600-2042
 */
libfile('notify_abstract');
class sms extends notify_abstract {
	public $order_info = array();

	public function __construct($param) {
		if (!empty($param['config'])) $this->set_config($param['config']);
		$this->param = $param;
		$this->order_info = model('order')->where(array('order_sn' => $param['param']['order_sn']))->find();
		$this->template = model('notify_template')->find($param['param']['id']);
		$this->template = json_decode($this->template['template'],TRUE);
		$user_id = ($param['param']['user_id']) ? $param['param']['user_id'] : $this->order_info['user_id'];
		$this->userInfo = getUserInfo($user_id);
		$this->$param['param']['id']();
	}

	/**
	* 确认订单
	* @access public
	* @param  $message[mobile]  : 手机号码
	* @param  $message[tpl_vars]: 模版变量【site_name:站点名称,username:用户名,order_sn:订单号】
	*/
	public function n_confirm_order() {
		$message = array();
		$message['mobile']                = $this->order_info['mobile'];
		$message['tpl_vars']['site_name'] = C('site_name');
		$message['tpl_vars']['username']  = $this->userInfo['username'];
		$message['tpl_vars']['order_sn']  = $this->param['param']['order_sn'];
        $this->send($message);
	}

	/**
	* 订单发货
	* @access public
	* @param  $message[mobile]  : 手机号码
	* @param  $message[tpl_vars]: 模版变量【delivery_name:快递名称,delivery_sn:快递单号,order_sn:订单号】
	*/
	public function n_order_delivery() {
		$message = array();
		$message['mobile']                    = $this->order_info['mobile'];
		$message['tpl_vars']['delivery_name'] = $this->order_info['delivery_txt'];
		$message['tpl_vars']['delivery_sn']   = $this->order_info['delivery_sn'];
		$message['tpl_vars']['order_sn']      = $this->param['param']['order_sn'];
		$this->send($message);
	}

	/**
	* 下单成功
	* @access public
	* @param  $message[mobile]  : 手机号码
	* @param  $message[tpl_vars]: 模版变量【site_name:站点名, order_sn:订单号】
	*/
	public function n_order_success() {
		$message = array();
		$message['mobile']                = $this->order_info['mobile'];
		$message['tpl_vars']['site_name'] = C('site_name');
		$message['tpl_vars']['order_sn']  = $this->param['param']['order_sn'];
		$this->send($message);
	}


	/**
	* 付款成功
	* @access public
	* @param  $message[mobile]  : 手机号码
	* @param  $message[tpl_vars]: 模版变量【real_amount:实付金额】
	*/
	public function n_pay_success() {
		$message = array();
		$message['mobile']                  = $this->order_info['mobile'];
		$message['tpl_vars']['real_amount'] = $this->order_info['real_amount'];
		$this->send($message);	
	}

	/**
	* 充值成功
	* @access public
	* @param  $message[mobile]  : 手机号码
	* @param  $message[tpl_vars]: 模版变量【username:用户名, total_fee:充值金额】
	*/
	public function n_recharge_success() {
		$pay_info = model('pay')->where(array('trade_sn' => $this->param['param']['order_sn']))->find();
		$message  = array();
		$message['mobile']                = $this->userInfo['mobile_phone'];
		$message['tpl_vars']['username']  = $this->userInfo['username'];
		$message['tpl_vars']['total_fee'] = $pay_info['total_fee'];
        $this->send($message);
	}

	/*余额变动  [暂无模版...待浩哥添加后完善] */
	public function n_money_change() {
		$money_log = model('user_moneylog')->find($this->param['param']['log_id']);
		$message = array();
		$message['mobile'] = $this->userInfo['mobile_phone'];
		$message['tpl_vars']['user_money']  = $this->userInfo['user_money'];
		$message['tpl_vars']['update_money']  = ((count(explode('-',$money_log['money'])) > 1) ? '-' : '+').$money_log['money'];
        $this->send($message);
	}

	/** 
	* 执行发送 
	* @param  $message[tpl_id]  : 短信模版ID
	* @param  $message[sms_sign] : 短信签名
	* @param  $message[cloud]	: 站点绑定资料【token:通信token,identifier:站点标识】
	*/
	public function send(&$message) {
		$message['tpl_id']              = $this->template['sms'];
		$message['sms_sign']            = $this->param['config']['sms_sign'];
		$message['cloud']['token']      = C('__CLOUD__.token');
		$message['cloud']['identifier'] = C('__CLOUD__.identifier');
		libfile('cloud');
		$_cloud = new cloud();
		$_cloud->send_sms($message);
	}
	
}