<?php 
class PushSmsController extends AdminBaseController
{
	public function _initialize() {
		parent::_initialize();

	}

	public function lists(){
		include $this->admin_tpl('push_sms');
	}
}