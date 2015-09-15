<?php
class OrderParcelLogModel extends SystemModel {
	protected $_validate = array(
	);

	protected $_auto = array(
		array('user_id', '_getUserId' , Model:: MODEL_INSERT, 'callback'),
		array('user_name', '_getUserName' , Model:: MODEL_INSERT, 'callback'),
		array('dateline', NOW_TIME , Model:: MODEL_INSERT, 'string'),
		array('clientip', 'get_client_ip', Model:: MODEL_INSERT, 'function'),
	);
	
	public function _getUserId() {
		return (defined('IN_ADMIN')) ? session('ADMIN_ID') : (int) authcode(cookie('user_key'), 'DECODE', C('site_key'));
	}
	
	public function _getUserName() {
		return (defined('IN_ADMIN')) ? session('ADMIN_UNAME') : cookie('_uname');
	}
	
	
}

?>