<?php
// 通知模块
class IndexController extends BaseController {

    public function _initialize() {
		parent::_initialize();
		$notifys = getcache('notify', 'notify');
		$this->notify = $notifys[ACTION_NAME];
		libfile('Wechat');	
	}

	public function weixin() {
		if (!$this->notify['enabled'] || empty($this->notify['config'])) die;
		$wechat = new Wechat($this->notify['config']);
		$wechat->getRev()->valid();
		// 返回事件类型
		$getRevEvent = $wechat->getRevEvent();
		$type = $wechat->getRevType();
		$wechat->checkAuth();
		$msg = $wechat->getRevContent();
		$is_bind = model('user_oauth')->where(array('openid' => $wechat->getRevFrom()))->find();
		// 已绑定
		if ($is_bind > 0) {
			$wechat->text('您好，欢迎来到'.C('site_name').'!	您已绑定该微信帐号，前往商城:'.C('site_companyurl'))->reply();
			return false;
		}
		switch ( $type ) {
			case Wechat::MSGTYPE_EVENT:
				if ($getRevEvent['event'] == 'subscribe') {//关注事件
					$wechat->text('您好，欢迎来到'.C('site_name').'!	绑定'.C('site_name').'帐号:'.U('User/Public/login', array('wechat_openid'=> urlencode(authcode($wechat->getRevFrom(),'ENCODE'))), '', false, true))->reply();					
				}
				break;
			case Wechat::MSGTYPE_TEXT:	// 文本消息
				$wechat->text('请先绑定'.C('site_name').'帐号, '.U('User/Public/login', array('wechat_openid'=> urlencode(authcode($wechat->getRevFrom(),'ENCODE'))), '', false, true))->reply();			
				break;	
			default:
				break;
		}
		//setcache('wechat', $wechat->getRevFrom(), 'notify');
	}
}