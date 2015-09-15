<?php 
/**
 *      [Haidao] (C)2013-2099 Dmibox Science and technology co., LTD.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      http://www.haidao.la
 *      tel:400-600-2042
 */
class system
{
	function system_init(&$param) {
		$_group = parse_name(GROUP_NAME, 0);
		$_module = parse_name(MODULE_NAME, 0);
		$_action = parse_name(ACTION_NAME, 0);
		runhook(strtolower($_group.'_'.$_module.'_'.$_action));
		libfile('notify_factory');
	}
	
	/* 登录成功 */
	function user_login_success(&$param) {
		$all_item = json_decode(cookie('Cart'), TRUE);
		if($all_item) {
			$info = array();
			foreach($all_item as $key => $item) {
				list($goods_id, $products_id, $num) = str2arr($item);
				$r = array();
				$r['user_id'] = is_login();
				$r['goods_id'] = $goods_id;
				$r['products_id'] = $products_id;
				$result = model('cart')->where($r)->find();
				if($result) {
					model('cart')->where($r)->setInc('num', $num);
				} else {
					$r['num'] = $num;
					$r['key'] = $key;
					model('cart')->update($r);
				}
			}
		}
		return false;
	}

	/* 订单提交成功 */
	public function buy_submit_success(&$param) {
		if(empty($param)) return false;
		$info = array();
		$info['order_sn'] = $param['order_sn'];
		$info['user_id'] = $param['user_id'];
		$info['user_name'] = cookie('_uname');
		$info['action'] = '创建订单';
		$info['issystem'] = '0';		
		model('order_log')->update($info);
		$track_msg = ($param['pay_type'] == 1) ? '请等待系统确认' : '系统正在等待付款';
		model('order_track')->update(array('order_sn' => $param['order_sn'], 'track_msg' => '您提交了订单，'.$track_msg));
	}
	
	/**
	 * 订单完成
	 * 1、赠送积分；
	 * 2、奖励经验&变更等级
	 */
	function order_finished(&$order_sn) {
		$sqlmap = array();
		$sqlmap['order_sn'] = $order_sn;
		$order_info = D('Order')->where($sqlmap)->find();
		if(!$order_info) return;
		/* 积分赠送 */
		if($order_info['give_point']) {
			action_point($order_info['user_id'], $order_info['give_point'], '订单完成积分赠送（订单号：'.$order_sn.'）');
		}
		/* 经验奖励 */
		action_exp($order_info['user_id'], $order_info['payable_amount']);
		/* 奖励优惠券 */
		if(is_numeric($order_info['give_coupons_id']) && $order_info['give_coupons_id'] > 0) {
			$this->_giveCoupons($order_sn, $order_info['give_coupons_id']);
		}
		/* 商品活动处理 */
		$goods_infos = model('order_goods')->where(array('order_id' => $order_info['id']))->field('id,goods_id,sum(shop_number) AS shop_number,give_coupons_id')->group('goods_id')->select();
		if($goods_infos) {
			foreach ($goods_infos as $v) {
				if($v['give_coupons_id'] > 0) {
					$this->_giveCoupons($order_sn, $v['give_coupons_id']);
				}
				model('goods')->where(array('id' => $v['goods_id']))->setInc('sales_number', $v['shop_number']);
			}
		}
		return TRUE;
	}

	/*赠送优惠券*/
	private function _giveCoupons($order_sn = '', $coupons_id = 0) {
		$sqlmap = array();
		$sqlmap['order_sn'] = $order_sn;
		$order_info = model('order')->where($sqlmap)->find();
		$coupons_id = (int) $coupons_id;
		if(!$order_info || $coupons_id < 1) return FALSE;
		$cp_base = model('coupons')->getById($coupons_id);		
		if($cp_base && $cp_base['start_time'] < NOW_TIME && $cp_base['end_time'] > NOW_TIME) {
			$cp_code = array();
			$cp_code['cid'] = $coupons_id;
			$cp_code['sn'] = random(10);
			$cp_code['password'] = random(6);
			$cp_code['name'] = $cp_base['name'];
			$cp_code['value'] = $cp_base['value'];
			$cp_code['start_time'] = $cp_base['start_time'];
			$cp_code['end_time'] = $cp_base['end_time'];
			$cp_code['to_time'] = NOW_TIME;
			$cp_code['user_id'] = $order_info['user_id'];
			$cp_code['user_name'] = $order_info['user_name'];
			$cp_code['use_order'] = $order_sn;
			$cp_code['status'] = 1;
			model('coupons_list')->add($cp_code);
		}
		return TRUE;
	}
	
	/*支付成功*/
	public function pay_success($ret){
		// 判断是(充值|购买) 
		if (strpos($ret['out_trade_no'], 'cz')!== false) {	// 充值
			$pay_info = model('pay')->where(array('trade_sn' => $ret['out_trade_no']))->find();
			if ($pay_info['status'] == 0) {
				$result = model('pay')->where(array('id' => $pay_info['id']))->save(array('trade_no'=>$ret['trade_no'],'status'=>1));
				model('user')->where(array('id' => $pay_info['user_id']))->setInc('user_money',$pay_info['total_fee']);
		        // 写入明细变更记录
				$data             = array();
				$data['user_id']  = $pay_info['user_id'];
				$data['money']    = $pay_info['total_fee'];
				$data['msg']      ='会员余额充值';
				$data['dateline'] =NOW_TIME;
				model('user_moneylog')->add($data);
				/* 通知推送 */
				runhook('n_recharge_success',array('order_sn' => $ret['out_trade_no'],'user_id' => $pay_info['user_id']));
			}
			redirect(U('User/Pay/pay_success'));
		} else {
			$sqlmap = array();
	        $sqlmap['order_sn'] = $ret['out_trade_no'];
	        $sqlmap['pay_status'] = 0;
	        $order = model('order')->where($sqlmap)->find();
	        if($order) {
	        	/* 当使用余额支付时减去会员冻结金额 */
	        	if ($order['balance_amount'] > 0) {
	        		model('user')->where(array('id' => $order['user_id']))->setDec('freeze_money',$order['balance_amount']);
	        	}
	            model('order')->where(array('order_sn' => $ret['out_trade_no']))->save(array('trade_no' => $ret['trade_no'],'pay_status' => 1, 'pay_time' => NOW_TIME));
	            model('order_log')->update(array('order_sn' => $ret['out_trade_no'],'user_id' => $order['user_id'],'action' => '支付成功', 'msg' => '流水号('.$ret['trade_no'].')', 'issystem' =>1, 'dateline' => NOW_TIME), FALSE);
				model('order_track')->update(array('order_sn' => $ret['out_trade_no'], 'track_msg' => '您的订单已付款，请等待系统确认'));
				/* 通知推送 */
				runhook('n_pay_success',array('order_sn' => $ret['out_trade_no']));
	        }
	        redirect(U('Goods/Order/pay_success', array('order_sn' => $ret['out_trade_no'])));
		}
	}
	/*支付失败*/
	public function pay_error($return_data){
		
	}

// ---------------------------------------	通知	------------------------------------------
	/* 下单成功 */
	public function n_order_success(&$param) {
		if (!$param['order_sn']) die;
		$param['id'] = 'n_order_success';
        $notify_factory =  new notify_factory($param);
	}

	/* 付款成功 */
	public function n_pay_success($param) {
		if (!$param['order_sn']) die;
		$param['id'] = 'n_pay_success';
        $notify_factory =  new notify_factory($param);
	}

	/* 确认订单 */
	public function n_confirm_order(&$param) {
		if (!$param['order_sn']) die;
		$param['id'] = 'n_confirm_order';
        $notify_factory =  new notify_factory($param);
	}

	/* 订单发货 */
	public function n_order_delivery(&$param) {
		if (!$param['order_sn']) die;
		$param['id'] = 'n_order_delivery';
        $notify_factory =  new notify_factory($param);
	}

	/* 充值成功 */
	public function n_recharge_success(&$param) {
		if (!$param['order_sn']) die;
		$param['id'] = 'n_recharge_success';
        $notify_factory =  new notify_factory($param);
	}

	/* 余额变动 */
	public function n_money_change(&$param) {
		if (!$param['order_sn']) $param['order_sn'] = '不需要订单号';
		$param['id'] = 'n_money_change';
        $notify_factory =  new notify_factory($param);
	}

	/* 商品到货 */
	public function n_goods_arrival() {
		
	}

	/* 找回密码 */
	public function n_back_pwd(&$param) {
		if (!$param['user_id']) die;
		$param['id'] = 'n_back_pwd';
        $notify_factory =  new notify_factory($param);
	}

	/* 注册验证 */
	function n_reg_validate() {

	}

	/* 注册成功 */
	public function n_reg_success(&$param) {
		$param['id'] = 'n_reg_success';
        $notify_factory =  new notify_factory($param);
	}

}