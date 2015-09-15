<?php
/**
 *      [Haidao] (C)2013-2099 Dmibox Science and technology co., LTD.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      http://www.haidao.la
 *      tel:400-600-2042
 */
libfile('login_abstract');
class sina extends login_abstract {

	public function __construct($config = array()) {
		if (!empty($config)) $this->set_config($config);
		$this->config['gateway_url'] = 'https://api.weibo.com/oauth2/authorize?';
		$this->config['return_url']  = return_url('sina' , 'login');
	}

	public function getPrepareData(){
		// 接口系统级参数
		$prepare_data['client_id']    = $this->config['app_key'];
		$prepare_data['redirect_uri'] = $this->config['return_url'];
		return $prepare_data;
	}

	public function _login(){
        $_access_info = $this->access_token($this->config['return_url'],$_GET['code']);
 		$uid = $_access_info['uid'];
 		return $uid;
	}
	//获取access token
	public function access_token($callback_url, $code){
		$params=array(
			'grant_type'=>'authorization_code',
			'code'=>$code,
			'client_id'=>$this->config['app_key'],
			'client_secret'=>$this->config['app_secret'],
			'redirect_uri'=>$callback_url
		);
		$url='https://api.weibo.com/oauth2/access_token';
		return $this->http($url, http_build_query($params), 'POST');
	}
	//提交请求
	private function http($url, $postfields='', $method='GET', $headers=array()){
		$ci=curl_init();
		curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, FALSE); 
		curl_setopt($ci, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, 30);
		curl_setopt($ci, CURLOPT_TIMEOUT, 30);
		if($method=='POST'){
			curl_setopt($ci, CURLOPT_POST, TRUE);
			if($postfields!='')curl_setopt($ci, CURLOPT_POSTFIELDS, $postfields);
		}
		$headers[]='User-Agent: weibo.PHP(piscdong.com)';
		curl_setopt($ci, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ci, CURLOPT_URL, $url);
		$response=curl_exec($ci);
		curl_close($ci);
		$json_r=array();
		if($response!='')$json_r=json_decode($response, true);
		return $json_r;
	}

}

