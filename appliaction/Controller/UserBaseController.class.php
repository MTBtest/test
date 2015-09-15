<?php
/**
 *	  [Haidao] (C)2013-2099 Dmibox Science and technology co., LTD.
 *	  This is NOT a freeware, use is subject to license terms
 *
 *	  http://www.haidao.la
 *	  tel:400-600-2042
 */
class UserBaseController extends BaseController {
	public function _initialize() {
		parent::_initialize();
		$this->userid = (int) authcode(cookie('user_key'), 'DECODE', C('site_key'));
		$this->userinfo = getUserInfo();
		if (!$this->userid || !$this->userinfo) {
			showmessage('您的登录信息已过期', U('Public/login', array('url_forward' => urlencode(__SELF__)) ));
		}
		/* 统计订单信息 */
		$o_count = $sqlmap = array();
		$sqlmap['user_id'] = $this->userid;
		// 待付款：先支付后发货 && 未支付
		$pay_map = array();
		$pay_map['pay_type'] = 0;
		$pay_map['pay_status'] = 0;
		$pay_map['order_status'] = array("LT", 3);
		$pay_map = array_merge($sqlmap, $pay_map);
		$o_count['pay'] = (int) model('order')->where($pay_map)->count();
		// 待发货：已确认 && 待发货
		$delivery_map = array();
		$delivery_map['order_status'] = 1;
		$delivery_map['delivery_status'] = 0;
		$delivery_map = array_merge($sqlmap, $delivery_map);
		$o_count['delivery'] = (int) model('order')->where($delivery_map)->count();
		// 已发货：先支付后发货 && 已确认 ** 已发货
		$finish_map = array();
		$finish_map['pay_type'] = 0;
		$finish_map['order_status'] = 1;
		$finish_map['delivery_status'] = 1;
		$finish_map = array_merge($sqlmap, $finish_map);
		$o_count['finish'] = (int) model('order')->where($finish_map)->count();		
		// 待评价：已完成 && 待评价
        $o_count['comment'] = model('order')->where(array('user_id' => $this->userid, 'order_status' => 2, 'is_comment' => 0))->count();
		$this->o_count = $o_count;
		$this->pagesize = 10;
    }
}
