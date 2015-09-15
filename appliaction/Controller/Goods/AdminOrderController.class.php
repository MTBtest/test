<?php
class AdminOrderController extends AdminBaseController {
	protected $_config = array();
	/* 初始化 */
	public function _initialize() {
		parent::_initialize();
		$this->db        = model('order');
		$this->log_db    = model('order_log');
		$this->goods_db  = model('order_goods');
		$this->parcel_db = model('order_parcel');
		$this->return_db = model('order_return');
		$this->_config   = $this->load_config('status');
		$this->deliverys = getcache('deliverys', 'site');
	}

	/**
	 * 订单列表
	 */
	public function lists() {
		$type = isset($_GET['type'])?$_GET['type']:-1;
		$keyword = isset($_GET['keyword'])?$_GET['keyword']:'';
		$region_arr = getcache('region','region');
		$user_id = isset($_GET['user_id'])?$_GET['user_id']:0;
		if(IS_POST){
			$sqlmap = array();
			$field = "a.*,
				e.name AS delivery_name,
				f.pay_name AS payment_name";
			$join = "
				LEFT JOIN `__DELIVERY__` AS e ON a.delivery_id = e.id
				LEFT JOIN `__PAYMENT__` AS f ON a.pay_code = f.pay_code";
			//排序
			$_order=isset($_POST['order']) ? ($_POST['order']) : NULL;
			$_sort=isset($_POST['sort']) ? ($_POST['sort']) : NULL;
			if($_order && $_sort){
				$order[$_sort] = $_order;
			}else{
				$order['id'] = 'DESC';
			}
			//筛选
			if (isset($user_id) && $user_id > 0) {
				$sqlmap['a.user_id'] = $user_id;
			}
			if (isset($keyword) && $keyword) {
				$sqlmap['_string'] = "a.order_sn LIKE '%{$keyword}%' OR a.accept_name LIKE '%{$keyword}%' OR a.mobile LIKE '%{$keyword}%' ";
			}
		switch ($type) {
			/* 未处理 */
			case '6':
				$sqlmap['a.order_status'] = 0;
				break;
			/* 已发货 */
			case '5':
			    $sqlmap['a.order_status'] = 1;
				$sqlmap['a.delivery_status'] = 1;
				break;
			/* 代发货 */
			case '4':
			    $sqlmap['a.order_status'] = 1;
				$sqlmap['a.delivery_status'] = 0;
				break;
			/* 已作废 */
			case '3':
				$sqlmap['a.order_status'] = 3;
				break;
			case '1':
				$sqlmap['a.order_status'] = 2;
				break;
			default:

				break;
		}

			//分页
			$pagenum=isset($_GET['page']) ? intval($_GET['page']) : 1;
			$rowsnum=isset($_GET['rows']) && (int)($_GET['rows']) != 0 ? intval($_GET['rows']) : PAGE_SIZE;
			//计算总数
			$data['total'] = $this->db->alias('a')->where($sqlmap)->count();
			$data['rows']=$this->db->alias('a')->field($field)->join($join)->where($sqlmap)->limit(($pagenum-1)*$rowsnum.','.$rowsnum)->order($order)->select();
			if (!$data['rows']) $data['rows']=array();
			echo json_encode($data);
		}else{
			include $this->admin_tpl('admin_order_lists');
		}
	}

	/**
	 * 更新订单数据
	 * @param  $data : 确认发货时所传参数
	 */
	public function update() {
		extract($_GET);
		switch ($action) {
			/* 确认订单 */
			case 'order':
				$result = $this->setOrder($order_sn, $order_status);
				$type = $this->_config['log_order_status'][$order_status];
				if (!$result) break;
				/* 通知推送 */
				runhook('n_confirm_order',array('order_sn' => $order_sn));
				break;
			/* 确认发货 */
			case 'delivery':
				$msg = $this->editDelivery();
				$result = $this->setDelivery($order_sn, $delivery_status,$delivery_sn);
				$type = $this->_config['log_delivery_status'][$delivery_status];
				/* 减少库存 */	
				if (getconfig('site_inventorysetup') == 2) {
					$order_goods = model('order_goods')->where(array('order_id'=>$order_id))->getField('goods_id,product_id,shop_number');
					foreach ($order_goods as $k => $v) {
						model('goods')->setDecNumber($v['goods_id'], $v['product_id'], $v['shop_number']);
					}
				}
				if (!$result) break;
				/* 通知推送 */
				runhook('n_order_delivery',array('order_sn' => $order_sn));
				break;
			/* 支付状态 */
			case 'pay':
				$result = $this->setPay($order_sn, $pay_status);
				$type = $this->_config['log_pay_status'][$pay_status];
				break;
			/* 退款 */
			case 'refund':
					$result = $this->refund($order_sn);
				$type   = $this->_config['log_pay_status'][$pay_status];
				break;
			/* 订单退货 */
			case 'return':
				if ($return_status == 1) {
					$result = $this->setDelivery($order_sn, $delivery_status,'');
					$msg    = '订单退货已成功';
				} else {
					$msg    = '订单退货审核未通过';
					$result = TRUE;
				}
				$type   = $this->_config['log_delivery_status'][$delivery_status];
				// 更改退换货表里的状态
				if ($result) {
					$this->set_order_return($order_sn , $return_status , $return_text);
				}
				break;
			default:
				showmessage('请勿非法访问', '', 1);
				break;
		}
		if(!$result) showmessage('操作失败');
		$this->write_log($order_sn, $type, $msg);
		showmessage('操作成功', '', 1);
	}
   /*
	退款
	*/
	public function refund($order_sn){
	   	$sqlmap = array();
		$sqlmap['order_sn'] = $order_sn;
		$result = $this->db->where($sqlmap)->save(array('pay_status' => 2));
		return (!$result) ? $this->db->getError() : TRUE;
	}
    /**
	 * 生成发货单
	 */
    public function setparcel(){
    	$sqlmap=array();
	    	$sqlmap['order_sn']=$_GET['order_sn'];
	    	$orderinfo=$this->db->where($sqlmap)->field('id,accept_name,mobile,address,province,city,area,delivery_txt,real_amount')->find();
    	$ordermap=array();
	    	$ordermap['order_id']=$orderinfo['id'];
	    	$goodsinfo=array();
	    	$ordergoodsinfo=$this->goods_db->join("hd_goods ON hd_order_goods.goods_id=hd_goods.id")->join("hd_goods_products ON hd_goods.id=hd_goods_products.goods_id and hd_order_goods.spec_array=hd_goods_products.spec_array")->where($ordermap)->field('*,hd_order_goods.shop_price')->select();
	    	$total_number=array();
	    	foreach ($ordergoodsinfo as $k => $v) {
	    		$goodsinfo[$k]['goods_name']=$v['name'];
	    		$goodsinfo[$k]['shop_price']=$v['shop_price'];
	    		$goodsinfo[$k]['shop_number']=$v['shop_number'];
	    		$goodsinfo[$k]['products_sn']=$v['products_sn'];
	    		$total_number[]=$v['shop_number'];
	    	}
	    	$goodslist=addslashes(json_encode($goodsinfo));
	    	$data=array();
	    	$data['order_sn']=$_GET['order_sn'];
	    	$data['address']=$orderinfo['address'];
	    	$data['total_number']=array_sum($total_number);
	    	$data['real_amount']=$orderinfo['real_amount'];
	    	$data['accept_name']=$orderinfo['accept_name'];
	    	$data['mobile']=$orderinfo['mobile'];
	    	$data['province']=$orderinfo['province'];
	    	$data['city']=$orderinfo['city'];
	    	$data['area']=$orderinfo['area'];
	    	$data['delivery_txt']=$orderinfo['delivery_txt'];
	    	$data['goods_list']=$goodslist;
	    	$result = $this->parcel_db->update($data);
	    	if (!$result) showmessage('生成发货单失败');
	    	return TRUE;
    }
	/**
	 * 添加订单
	 */
	public function add() {
		if (IS_POST) {
			$jsonstring = $_POST;
			$_POST['order_sn'] = build_order_no();
			self::update();
		} else {
			$submit_url=U('AdminOrder/add');
			$region=model('Region')->select();
			$payment=model('Payment')->select();
			include $this->admin_tpl('admin_order_add');

		}
	}

	/* 查看&编辑订单 */
	public function edit() {
		$dialog = TRUE;
		$validform = TRUE;
		$info = $this->db->detail($_GET['order_sn']);
		$info['user_name'] = get_nickname($info['user_id']);
		$info['_delivery'] = $this->deliverys[$info['delivery_id']];
		$info['_goods'] = $this->goods_db->where(array('order_id' => $info['id']))->select();
		$info['front_id'] = $this->db->where(array("id" => array("LT", $info['id'])))->order("id DESC")->find();
		$info['after_id'] = $this->db->where(array("id" => array("GT", $info['id'])))->order("id ASC")->find();
		$payment = getcache('payment', 'pay');
		$deliverys = $this->deliverys;
		include $this->admin_tpl('admin_order_edit');
	}

	/* 修改订单价格 */
	public function editPrice() {
		extract($_GET);
		$real_amount = sprintf('%.2f', $real_amount);
		if(empty($order_sn)) showmessage('订单号参数错误');
		$sqlmap = array();
		$sqlmap['order_sn'] = $order_sn;
		$result = $this->db->where($sqlmap)->setField('real_amount', $real_amount);
		if(!$result) {
			showmessage('订单价格修改失败');
		} else {
			$this->write_log($order_sn, '修改价格', '从「'.$oldPrice.'」修改到「'.$real_amount.'」');
			showmessage('订单价格修改成功', '', 1);
		}

	}

	/* 修改配送方式 */
	public function editDelivery(){
		$order_sn = trim($_GET['order_sn']);
		if(empty($order_sn)) showmessage('订单号参数错误！');
		if (!trim($_GET['delivery_sn'])) showmessage('请填写快递单号');
		$order_info = $this->db->detail($_GET['order_sn']);
		$sqlmap = array();
		$sqlmap['order_sn'] = $order_sn;
		$result = $this->db->where($sqlmap)->setField(array('delivery_id'=>$_GET['delivery_id'],'delivery_txt'=>$_GET['delivery_txt'],'delivery_sn'=>$_GET['delivery_sn']));
		if(!$result) showmessage('订单配送方式修改失败');
		if ($order_info['delivery_id'] != $_GET['delivery_id']) {
			$msg = '从「'.$order_info['delivery_txt'].'」修改到「'.$_GET['delivery_txt'].'」;快递单号：'.trim($_GET['delivery_sn']);
		} else {
			$msg = '快递单号：'.trim($_GET['delivery_sn']);
		}
		return $msg;
	}

	/* 快递查询 */
	public function kuaidi(){
		extract($_GET);
		if (IS_POST) {
			libfile('kuaidi');
			$kuaidi = new kuaidi();
			$result = $kuaidi->query($com, $nu);
			if(!$result) {
				showmessage($kuaidi->getError());
			} else {
				showmessage('查询成功', '', 1, '', '', '', $result);
			}
		} else {
			include $this->admin_tpl('admin_order_kuaidi');
		}
	}

	/* 设定订单状态 */
	private function setOrder($order_sn, $order_status){
		$sqlmap = array();
		$sqlmap['order_sn'] = $order_sn;
		$info['order_status'] = (int) $order_status;
		if($order_status == 2) {
			$info['pay_status'] = 1;
			$info['completion_time'] = NOW_TIME;
		} elseif($order_status == 1) {
			$info['confirm_time'] = NOW_TIME;
		}
		$result = $this->db->where($sqlmap)->save($info);
		if(!$result) {
			return $this->db->getError();
		} else {
			if($order_status == 1) {
				$this->setparcel();
				model('order_track')->update(array('order_sn' => $order_sn, 'track_msg' => '您的订单正在配货'));
			} elseif($order_status == 2) {
				runhook('order_finished', $order_sn);
			} elseif($order_status == 4) {
				$rs = model('order')->where(array('order_sn' => $order_sn))->find();
				// 减去冻结金并加上会员余额
				if ($rs['balance_amount'] > 0) {
					model('user')->where(array('id' => $rs['user_id']))->setDec('freeze_money',$rs['balance_amount']);
					model('user')->where(array('id' => $rs['user_id']))->setInc('user_money',$rs['balance_amount']);
					// 写入财务变动记录
					$data = array();
					$data['user_id'] = $rs['user_id'];
					$data['money'] = $rs['balance_amount'];
					$data['msg'] = '取消订单，并退回冻结金额';
					$data['dateline'] = NOW_TIME;
					model('user_moneylog')->add($data);
				}
			}
			return TRUE;
		}
	}

	/* 设定订单发货状态 */
	private function setDelivery($order_sn, $delivery_status,$delivery_sn) {
        $pays = getcache('payment', 'pay');
		$sqlmap = array();
		$sqlmap['order_sn'] = $order_sn;
        $o_info  = $this->db->where($sqlmap)->find();
        $info['delivery_status'] = (int) $delivery_status;
        if($delivery_status == 1) {
			$info['delivery_sn']=$delivery_sn;
			$info['send_time'] = NOW_TIME;
			if($pays[$o_info['pay_code']]) {
				libfile('pay_factory');
				$product_info = array(
					'trade_no' 		=> 	$o_info['trade_no'],
					'delivery_txt' 	=> 	$o_info['delivery_txt'],
					'delivery_sn' 	=> 	$info['delivery_sn']
				);
				$pay_factory =  new pay_factory($o_info['pay_code']);
				$pay_factory->set_productinfo($product_info);
				$pay_factory->_delivery();
			}
		}
		$result = $this->db->where($sqlmap)->save($info);
		if($result) {
			if ($delivery_status == 1) {
				$track_msg = '您的订单已经发货';
			} elseif ($delivery_status == 2) {
				$track_msg = '您的订单已退货';
			}
			model('order_track')->update(array('order_sn' => $order_sn, 'track_msg' => $track_msg));
		} else {
			return $this->db->getError();
		}
		return (!$result) ? $this->db->getError() : TRUE;
	}

	/* 设定订单支付状态 */
	private function setPay($order_sn, $pay_status) {
		$sqlmap = array();
		$sqlmap['order_sn'] = $order_sn;
		$info['pay_status'] = (int) $pay_status;
		if($pay_status == 1) {
			model('order_track')->update(array('order_sn' => $order_sn, 'track_msg' => '您的订单已付款，请等待系统确认'));
			$info['pay_time'] = NOW_TIME;
		}
		$result = $this->db->where($sqlmap)->save($info);
		return (!$result) ? $this->db->getError() : TRUE;
	}

	/**
	 * 显示订单日志
	 */
	public function view_log(){
		libfile('Dir');
		extract($_GET);
		if(empty($order_sn)) showmessage('订单号参数错误');
		$sqlmap = array();
		$sqlmap['order_sn'] = $order_sn;
		$result = $this->log_db->where($sqlmap)->order("id DESC")->select();
		if(!$result) {
			showmessage('暂无任何订单操作日志信息');
		} else {
			foreach ($result as $key => $value) {
				$value['dateline'] = mdate($value['dateline'], 'Y/m/d H:i');
				$result[$key] = $value;
			}
			$return['data'] = $result;
			showmessage('订单操作日志查阅成功', '', 1, 1 ,1 ,1, $return);
		}
	}

	/* 写入订单日志 */
	private function write_log($order_sn, $action, $msg) {
		$log = array(
			'order_sn' => $order_sn,
			'action' => $action,
			'msg' => $msg,
		);
		$this->log_db->update($log);
	}

	/* 打印快递信息 */
	public function print_kd($order_id = 0) {
		if ((int)$order_id < 1 ) showmessage('您的订单号有误');
		/* 查找该订单的快递图片名称 */
		$info = model('order')->where(array('id'=>$order_id))->find();

		/* 读取该快递模版的编辑信息 */
		$content = json_decode(model('print_tpl_delivery')->getFieldByDelivery_id($info['delivery_id'],'content'),TURE);
		// 替换值
		foreach ($content['list'] as $k => $v) {
			$str = str_replace('left','x',json_encode($v));
			// $str = str_replace('top','y',$str);
			// $str = str_replace('txt','typeText',$str);
			$str = str_replace('{accept_name}',$info['accept_name'],$v);
			$str = str_replace('{province}',getAreaNameById($info['city']),$str);
			$str = str_replace('{address}',getAreaNameById($info['province']).' '.getAreaNameById($info['city']).' '.getAreaNameById($info['area']).' '.$info['address'],$str);
			$str = str_replace('{mobile}',$info['mobile'],$str);
			$str = str_replace('{postscript}',$info['postscript'],$str);
			$str = str_replace('{insured}',$info['insured'],$str);
			$str = str_replace('{real_amount}',$info['real_amount'],$str);
			$str = str_replace('{payable_amount}',$info['payable_amount'],$str);
			$str = str_replace('{from_site_company}',C('from_site_company'),$str);
			$str = str_replace('{from_tel}',C('from_tel'),$str);
			$str = str_replace('{from_address}',C('from_address'),$str);
			$content['list'][$k] = $str;
		}
		include $this->admin_tpl('admin_order_print_kd');
	}

	/* 订单退货管理 */
	public function order_return() {
		$type    = isset($_GET['type']) ? $_GET['type'] : -2;
		$keyword = trim($_GET['keyword']);
		$user_id = isset($_GET['user_id']) ? $_GET['user_id'] : 0;
		if(IS_POST){
			$sqlmap = array();

			if ($type != -2) {
				$sqlmap['return_status'] = $type;
			}

			if (!empty($keyword)) {
				$orderMap = array();
				$orderMap['order_sn|accept_name|mobile'] = array('LIKE','%'.$keyword.'%');
				$ids = $this->db->where($orderMap)->getField('id',TRUE);
				$sqlmap['order_id'] = array('IN', $ids);
			}

			//分页
			$pagenum=isset($_GET['page']) ? intval($_GET['page']) : 1;
			$rowsnum=isset($_GET['rows']) && (int)($_GET['rows']) != 0 ? intval($_GET['rows']) : PAGE_SIZE;
			//计算总数
			$data['total'] = $this->return_db->where($sqlmap)->count();
			$data['rows'] = $this->return_db->where($sqlmap)->limit(($pagenum-1)*$rowsnum.','.$rowsnum)->order('rid DESC')->select();
			foreach ($data['rows'] as $key => $row) {
				// 订单信息
				$_order = $this->db->find($row['order_id']);
				$row = array_merge($data['rows'][$key],$_order);
				if (!empty($row['return_imgs'])) {
					$row['return_imgs'] = json_decode($row['return_imgs'],TRUE);
				}
				// 用户名
				$row['user_name'] = model('User')->getFieldById($row['user_id'],'username');
				$data['rows'][$key] = $row;
			}
			if (!$data['rows']) $data['rows']=array();
			echo json_encode($data);
		}else{
			include $this->admin_tpl('order_return');
		}
	}

	/* 设置订单退换状态 */
	private function set_order_return($order_sn ,$return_status , $return_text) {
        $order_info = $this->db->where(array('order_sn' => $order_sn))->field("id,user_id")->find();
        $sqlmap = array();
		$sqlmap['order_id']      = $order_info['id'];
		$sqlmap['user_id']       = $order_info['user_id'];
		$sqlmap['return_status'] = 0;
        $order_return = $this->return_db->where($sqlmap)->find();
        if (!$order_return) return FALSE;
        $data = array();
		$data['return_status']       = $return_status;
		$data['return_text']        = $return_text;
		$data['return_examine_date'] = NOW_TIME;
        $this->return_db->where(array('rid' => $order_return['rid']))->save($data);
	}
}