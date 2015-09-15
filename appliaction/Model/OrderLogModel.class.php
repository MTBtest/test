<?php
/**
 *	  订单模型
 *	  [Haidao] (C)2013-2099 Dmibox Science and technology co., LTD.
 *	  This is NOT a freeware, use is subject to license terms
 *
 *	  http://www.haidao.la
 *	  tel:400-600-2042
 */
class OrderLogModel extends SystemModel {
	protected $_validate = array(
	);

	protected $_auto = array(
		array('user_id', '_getUserId' , Model:: MODEL_INSERT, 'callback'),
		array('user_name', '_getUserName' , Model:: MODEL_INSERT, 'callback'),
		array('issystem', '_getSystem' , Model:: MODEL_INSERT, 'callback'),
		array('dateline', NOW_TIME , Model:: MODEL_INSERT, 'string'),
		array('clientip', 'get_client_ip', Model:: MODEL_INSERT, 'function'),
	);
	
	public function _getUserId() {
		return (defined('IN_ADMIN')) ? session('ADMIN_ID') : (int) authcode(cookie('user_key'), 'DECODE', C('site_key'));
	}
	
	public function _getUserName() {
		return (defined('IN_ADMIN')) ? session('ADMIN_UNAME') : cookie('_uname');
	}
	
	public function _getSystem() {
		return (defined('IN_ADMIN')) ? 1 : 0;
	}
}