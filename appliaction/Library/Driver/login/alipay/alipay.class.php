<?php
/**
 *      [Haidao] (C)2013-2099 Dmibox Science and technology co., LTD.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      http://www.haidao.la
 *      tel:400-600-2042
 */
libfile('login_abstract');
class alipay extends login_abstract {

	public function __construct($config = array()) {
		if (!empty($config)) $this->set_config($config);
		$this->config['service']     = 'alipay.auth.authorize';
		$this->config['gateway_url'] = 'https://mapi.alipay.com/gateway.do?';
		$this->config['return_url']  = return_url('alipay' , 'login');
	}

	public function getPrepareData(){
		// 接口系统级参数
		$prepare_data['service']        = $this->config['service'];
		$prepare_data['_input_charset'] = CHARSET;
		$prepare_data['return_url']     = $this->config['return_url'];
		$prepare_data['target_service'] = 'user.auth.quick.login';
		$prepare_data['partner']        = $this->config['partner'];

		// 排序
		$prepare_data = arg_sort($prepare_data);

		$prepare_data['sign']           = build_mysign($prepare_data,$this->config['key'],'MD5');
		$prepare_data['sign_type']      = 'MD5';
		return $prepare_data;
	}
	public function _login(){
		return TRUE;
	}
	public function _return(){
		return TRUE;
	}

	public function getCodeUrl(){
		return TRUE;
	}


}

