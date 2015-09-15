<?php
/**
 *      [Haidao] (C)2013-2099 Dmibox Science and technology co., LTD.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      http://www.haidao.la
 *      tel:400-600-2042
 */
libfile('login_abstract');
class wechat extends login_abstract {

	public function __construct($config = array()) {
		if (!empty($config)) $this->set_config($config);
		$this->config['gateway_url'] = 'https://open.weixin.qq.com/connect/qrconnect?';
		$this->config['return_url']  = return_url('wechat' , 'login');
	}
	public function getPrepareData(){
		// 接口系统级参数
		$prepare_data['appid']        = $this->config['app_id'];
		$prepare_data['scope']        = 'snsapi_login';
		$prepare_data['redirect_uri'] = $this->config['return_url'];
		$prepare_data['response_type'] = 'code';
		$prepare_data['state']    = md5(uniqid(rand(), TRUE));
		session('wechat_state',$prepare_data['state']);
		// 排序
		return $prepare_data;
	}
	public function _login(){
		if(session('wechat_state') !== $_GET['state']) return FALSE;	
        $_access_info =json_decode($this->access_token($_GET['code']),TRUE);
 		$uid = $_access_info['openid'];
 		return $uid;
	}
	public function access_token($code){
		$url='https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$this->config['app_id'].'&secret='.$this->config['app_key'].'&code='.$code.'&grant_type=authorization_code';
		return $this->http_get($url);
	}
	private function http_get($url){
		$oCurl = curl_init();
		if(stripos($url,"https://")!==FALSE){
			curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, FALSE);
			curl_setopt($oCurl, CURLOPT_SSLVERSION, 1); //CURL_SSLVERSION_TLSv1
		}
		curl_setopt($oCurl, CURLOPT_URL, $url);
		curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1 );
		$sContent = curl_exec($oCurl);
		$aStatus = curl_getinfo($oCurl);
		curl_close($oCurl);
		if(intval($aStatus["http_code"])==200){
			return $sContent;
		}else{
			return false;
		}
	}
	public function _return(){
		return TRUE;
	}

	public function getCodeUrl(){
		return TRUE;
	}


}

