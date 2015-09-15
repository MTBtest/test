<?php
class OrderController extends UserBaseController
{
	protected $errMsg = '';

	public function _initialize() {
		parent::_initialize();
		$this->db = model('order');
		$this->goods_db = model('order_goods');
		$this->_config = array(
			array('待付款', '待确认', '待发货', '待收货', '已完成'),
			array('待确认', '待发货', '待收货', '已完成'),
		);
		$this->deliverys = getcache('deliverys', 'site');
		libfile('Page');
	}

	/* 订单管理 */
	public function manage() {
		extract($_GET);
		$sqlmap = array();
		$sqlmap['user_id'] = $this->userid;
		$sqlmap['order_status'] = array("NOT IN", '3');//屏蔽已作废订单
		switch ($type) {
			/* 待付款 */
			case 'pay':
				$sqlmap['pay_type'] = 0;
				$sqlmap['pay_status'] = 0;
				$meta_title = '待付款交易';
				break;
			/* 待发货：已确定 ** 未发货 */
			case 'delivery':
				$sqlmap['order_status'] = 1;
				$sqlmap['delivery_status'] = 0;
				$meta_title = '待发货交易';
				break;
			/* 待确认收货：先支付后发货 && 已确认 && 已发货 */
			case 'finish':
				$sqlmap['pay_type'] = 0;
				$sqlmap['order_status'] = 1;
				$sqlmap['delivery_status'] = 1;
				$meta_title = '待确认收货';
				break;
			default:
				$meta_title = '所有订单';
				break;
		}

		$_create_time = 0;
		switch ($time) {
			/* 最近一周 */
			case 'week':
				$_create_time = NOW_TIME - (86400 * 7);
				break;
			/* 最近一月 */
			case 'month':
				$_create_time = NOW_TIME - (86400 * 30);
				break;

			/* 最近三个月 */
			case 'month3':
				$_create_time = NOW_TIME - (86400 * 30 * 3);
				break;
			/* 最近六个月 */
			case 'month6':
				$_create_time = NOW_TIME - (86400 * 30 * 6);
				break;
			/* 最近一年 */
			case 'year':
				$_create_time = NOW_TIME - (86400 * 30 * 12);
				break;
			default:
				break;
		}
		if($_create_time) {
			$sqlmap['create_time'] = array("EGT", $_create_time);
		}

		$count = $this->db->where($sqlmap)->count();
		$lists = $this->db->where($sqlmap)->page(PAGE, $this->pagesize)->order("create_time DESC")->select();
		foreach ($lists as $key => $value) {
			$value = $this->getInfo($value['order_sn']);
			$value['_config'] = $this->_config[$value['pay_type']];
			$lists[$key] = $value;
		}
		$pages = pages($count, $this->pagesize);
		$SEO = seo(0, $meta_title);
		include template('order');
	}

	public function detail() {
		extract($_GET);
		$rs = $this->getInfo($order_sn);
		if(!$rs) {
			showmessage($this->errMsg);
		}
		$delivery = $rs['_delivery'];
		$_config = $this->_config[$rs['pay_type']];
		$tracks = model('order_track')->fetch_all_by_order_sn($order_sn);
		$SEO = seo(0, '订单详情');
		include template('order_detail');
	}

	/* 用户确认收货 */
	public function confirm() {
		extract($_GET);
		$rs = $this->getInfo($order_sn);
		if(!$rs) {
			showmessage($this->errMsg);
		}
		if($rs['user_id'] != $this->userid) {
			showmessage('您无操作该订单的权限');
		}
		/* 检测当前状态是否为已发货 */
		if($rs['pay_type'] == 0 && $rs['current_step'] == 3) {
			$info['id'] = $rs['id'];
			$info['order_status'] = 2;
			$info['completion_time'] = NOW_TIME;
			$result = $this->db->update($info);
			if(!$result) {
				showmessage($this->db->getError());
			}
			/* 写入日志 */
			model('order_log')->update(array('order_sn' => $order_sn, 'action' => '订单完成', 'msg' => '用户确认收货'));
			runhook('order_finished', $order_sn);
			showmessage('确认收货成功', '', 1);
		} else {
			showmessage('订单异常，请勿非法提交');
		}
	}

	/* 用户取消订单 */
	public function cancel() {
		extract($_GET);
		$rs = $this->getInfo($order_sn);
		if(!$rs) showmessage($this->errMsg);
		if($rs['user_id'] != $this->userid) showmessage('您无操作该订单的权限');
		if ((($rs['order_status'] == 0 && $rs['pay_type'] == 1) || ($rs['pay_type'] == 0 && $rs['pay_status'] != 1)) && $rs['delivery_status'] == 0 && $rs['order_status'] < 2) {
			$info = array();
			$info['id'] = $rs['id'];
			$info['order_status'] = 4;
			$result = $this->db->update($info);
			if(!$result) showmessage($this->db->getError());
			// 减去冻结金并加上会员余额
			if ($rs['balance_amount'] > 0) {
				model('user')->where(array('id' => $this->userid))->setDec('freeze_money',$rs['balance_amount']);
				model('user')->where(array('id' => $this->userid))->setInc('user_money',$rs['balance_amount']);
				// 写入财务变动记录
				$data = array();
				$data['user_id'] = $this->userid;
				$data['money'] = $rs['balance_amount'];
				$data['msg'] = '取消订单，并退回冻结金额';
				$data['dateline'] = NOW_TIME;
				model('user_moneylog')->add($data);
			}
			$map['order_id']=array('eq',$rs['id']);
			if (getconfig('site_inventorysetup') == 1) {
				$goods_order_list=$this->goods_db->where($map)->select();
			foreach ($goods_order_list as $k => $v) {
				model('goods')->setIncNumber($v['goods_id'], $v['product_id'], $v['shop_number']);
			}
		}
		model('order_log')->update(array('order_sn' => $order_sn, 'action' => '订单取消', 'msg' => '用户取消订单'));
		runhook('order_canceled', $order_sn);
		showmessage('取消订单成功', '', 1);

	}else {
			showmessage('订单异常，请勿非法提交');
		}
	}

	private function getInfo($order_sn = '') {
		if(empty($order_sn)) {
			$this->errMsg = '参数错误';
			return FALSE;
		}
		$sqlmap = array();
		$sqlmap['user_id'] = $this->userid;
		$sqlmap['order_sn'] = $order_sn;
		$rs = $this->db->where($sqlmap)->find();
		if(!$rs || $rs['order_status'] == 3) {
			$this->errMsg = '订单不存在或没有权限';
			return FALSE;
		}
		$rs['_goods_info'] = $this->goods_db->where(array('order_id' => $rs['id']))->select();
        foreach ($rs['_goods_info'] as $key => $value) {
            $value['spec_text'] = '';
            $value['spec_array'] = unserialize($value['spec_array']);
            foreach ($value['spec_array'] as $k => $spec) {
                $value['spec_text'] .= $spec['name'].':'.$spec['value'].';';
            }
            $rs['_goods_info'][$key] = $value;
        }
		$rs['_comment_info_'] = array();
		$tmp_comment = model('comment')->where(array('order_id' => $rs['id'], 'user_id' => $this->userid))->field('goods_id, product_id')->select();
		foreach ($tmp_comment as $key => $value) {
			$rs['_comment_info_'][] = arr2str(array_values($value));
		}
		$rs['_delivery'] = $this->deliverys[$rs['delivery_id']];
		// 获取是否有正在进行的退换货信息
		$sqlmap = array();
		$sqlmap['order_id']      = $rs['id'];
		$sqlmap['user_id']       = $this->userid;
		$sqlmap['return_status'] = '0';
		$rs['_order_return_ing'] = model('order_return')->where($sqlmap)->find();
		// 获取已通过的退换货信息
		$sqlmap['return_status'] = '1';
		$rs['_order_return_pass'] = model('order_return')->where($sqlmap)->find();
		/* 检测订单状态 */
		$rs['current_step'] = 0;
		/* 先支付后发货 */
		if($rs['pay_type'] == 0) {
			if($rs['order_status'] == 2) {
				// 已完成
				$rs['current_step'] = 4;
			} elseif($rs['order_status'] == 1 && $rs['delivery_status'] == 1) {
				// 已发货
				$rs['current_step'] = 3;
			} elseif($rs['order_status'] == 1 && $rs['delivery_status'] == 0) {
				// 待发货
				$rs['current_step'] = 2;
			} elseif($rs['order_status'] == 0 && $rs['pay_status'] == 1) {
				// 待确认
				$rs['current_step'] = 1;
			}
		} else{
			if($rs['order_status'] == 2) {
				// 已完成
				$rs['current_step'] = 3;
			} elseif($rs['order_status'] == 1 && $rs['delivery_status'] == 1) {
				// 已发货
				$rs['current_step'] = 2;
			} elseif($rs['order_status'] == 1) {
				// 待发货
				$rs['current_step'] = 1;
			}
		}
		$rs['invoice_title'] = unserialize($rs['invoice_title']);
		return $rs;
	}

	/* 发表评价 */
	public function comment() {
		extract($_GET);
		$rs = $this->getInfo($order_sn);
		if(!$rs) {
			showmessage($this->errMsg);
		}
		if($rs['user_id'] != $this->userid) {
			showmessage('您无操作该订单的权限');
		}
        if($rs['order_status'] != 2) {
            showmessage('无法对未完成的订单评价');
        }
		if(IS_POST) {
			$goods_id = (int) $goods_id;
			$product_id = (int) $product_id;
			$content = remove_xss($content);
			if($goods_id < 1) {
				showmessage('您评价的商品信息有误');
			}
            if(strlen($content) < 20) {
				showmessage('评论内容不能少于10个汉字');
			}
			$order_id = $rs['id'];
			$g_map = array();
			$g_map['order_id'] = $order_id;
			$g_map['goods_id'] = $goods_id;
			$g_map['product_id'] = $product_id;
			$g_map['user_id'] = $this->userid;
			if(model('comment')->where($g_map)->count()) {
				showmessage('您已评价过，请勿重复评价');
			}
			$goods_info = model('order_goods')->where($g_map)->find();
			if(!$goods_info) {
				showmessage('很抱歉，无法对该商品评价');
			}
			$info = $g_map;
			$info['user_name'] = $this->userinfo['username'];
			$info['content'] = $content;
			$info['time'] = NOW_TIME;
			$result = model('comment')->add($info);
			if(!$result) {
				showmessage('商品评价发表失败，请稍候重试。');
			}
            if(count($rs['_goods_info']) == model('comment')->where(array('order_id' => $rs['id']))->count()) {
                $this->db->update(array('id' => $rs['id'], 'is_comment' => 1), FALSE);
            }
			showmessage('商品评价发表成功，感谢您的参与。', '', 1);
		} else {
            foreach ($rs['_goods_info'] as $k => $r) {
                $r['is_comment'] = model('comment')->where(array('order_id' => $rs['id'], 'goods_id' => $r['goods_id'], 'product_id' => $r['product_id'], 'user_id' => $this->userid))->count();
                $rs['_goods_info'][$k] = $r;
            }
			include template('order_comment');
		}
	}

    /**
     * 发表评价
     */
    public function comment_post() {
		extract($_GET);
		$rs = $this->getInfo($order_sn);
		if(!$rs) {
			showmessage($this->errMsg);
		}
		if($rs['user_id'] != $this->userid) {
			showmessage('您无操作该订单的权限');
		}
        if($rs['order_status'] != 2) {
            showmessage('无法对未完成的订单评价');
        }
        $goods = model('order_goods')->where(array('order_id' => $rs['id'], 'goods_id' => $goods_id, 'product_id' => $product_id))->find();
        include template('order_comment_goods');
    }

    public function repeat_buy($order_sn = '') {
		libfile('Cart');
		$this->Cart = new Cart();
        $rs = $this->getInfo($order_sn);
        if($rs['user_id'] != $this->userid) {
            showmessage('您查看的订单不存在');
        }
        if($rs['order_status'] != 2 && $rs['order_status'] != 4) {
            showmessage('您查看的订单状态异常');
        }
        foreach($rs['_goods_info'] as $goods) {
            $this->Cart->add($goods['goods_id'], $goods['product_id'], $goods['shop_number'], $this->userid);
        }
        showmessage('您的订单商品已加入购物车', U('Goods/Cart/index'), 1);
    }

	/* 查询快递 */
	public function kuaidi() {
    	extract($_GET);
    	$rs = $this->getInfo($order_sn);
    	if (IS_POST) {
    		libfile('kuaidi');
			if(empty($com) || empty($nu)) {
				showmessage('参数错误');
			}
			$kuaidi = new kuaidi();
			$result = $kuaidi->query($com, $nu);
			if(!$result) {
				showmessage($kuaidi->getError());
			} else {
				showmessage('查询成功', '', 1, '', '', '', $result);
			}
		} else {
			$tracks = model('order_track')->fetch_all_by_order_sn($order_sn);
			include template('order_kuaidi');
		}
	}
}