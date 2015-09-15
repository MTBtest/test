<?php
class OrderReturnController extends UserBaseController {
	
	public function _initialize() {
		parent::_initialize();
		$this->order_db = model('order');
		$this->goods_db = model('order_goods');
		$this->return_db = model('order_return');
		$this->_config = array(
			array('待付款', '待确认', '待发货', '待收货', '已完成'),
			array('待确认', '待发货', '待收货', '已完成'),
		);
		$this->deliverys = getcache('deliverys', 'site');
		libfile('Page');
	}

	/* 申请退货 */
	public function order_return() {
		extract($_GET);
		$order_info = $this->getInfo($order_sn);
		if(!$order_info) {
			showmessage($this->errMsg);
		}
		if (IS_POST) {
			unset($_GET['order_sn']);
			$_GET['order_id'] = $order_info['id'];
			$_GET['user_id']  = $this->userid;
			$result = $this->return_db->apply_return($_GET);
			if (!$result) {
				showmessage($this->return_db->getError());
			}
			showmessage('申请成功,请等待平台处理！',U('User/Order/manage'),1);
		} else {
			$order_info['_config'] = $this->_config[$order_info['pay_type']];
			$SEO = seo(0, '申请退货');
			include template('order_return');
		}
	}

	/* 删除图片 */
	public function delete_img() {
		$img_url = $_SERVER['DOCUMENT_ROOT'].$_GET['img_url'];
		if (file_exists($img_url)) {
			$result = unlink($img_url);
		}
		$result ? showmessage('文件删除成功','',1) : showmessage('文件删除失败') ;
	}

	/* 取消申请退货 */
	public function order_return_cancel() {
		$order_info = $this->getInfo($_GET['order_sn']);
		$sqlmap = array();
		$sqlmap['rid']           = $order_info['_order_return']['rid'];
		$sqlmap['user_id']       = $this->userid;
		$sqlmap['return_status'] = 0;
		$result = $this->return_db->where($sqlmap)->setField('return_status', -1 );
		if (!$result) showmessage('取消申请退货失败！');
		showmessage('取消申请成功',U('User/Order/manage'),1);
	}	
	
	/* 获取订单相关信息 */
	private function getInfo($order_sn = '') {
		if(empty($order_sn)) {
			$this->errMsg = '参数错误';
			return FALSE;
		}
		$sqlmap = array();
		$sqlmap['user_id'] = $this->userid;
		$sqlmap['order_sn'] = $order_sn;
		$rs = $this->order_db->where($sqlmap)->find();
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
		$rs['_order_return'] = model('order_return')->where($sqlmap)->find();
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
}