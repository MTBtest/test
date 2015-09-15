<?php
class NotifyController extends BaseController {
	public function _initialize() {
		parent::_initialize();
	}
	/**
	 * 支付回调数据处理
	 */
	public function _empty() {
        $method = '_'.htmlspecialchars($_GET['method']);
        unset($_GET['method']);
		libfile('pay_factory');
		$pay_factory =  new pay_factory(ACTION_NAME);
		$ret = $pay_factory->$method();
		if($ret !== FALSE) {
			runhook('pay_success', $ret);
			showmessage('支付成功',U('User/index/index'), 1);
		} else {
			runhook('pay_error');
			showmessage('支付失败',U('User/index/index'), 0);
		}
	}
}