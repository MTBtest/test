<?php
/**
 *	  退换货模型
 *	  [Haidao] (C)2013-2099 Dmibox Science and technology co., LTD.
 *	  This is NOT a freeware, use is subject to license terms
 *
 *	  http://www.haidao.la
 *	  tel:400-600-2042
 */
class OrderReturnModel extends SystemModel {

	/* 申请退换货 */
	public function apply_return($params) {
		$params['goods_ids']   = array_filter($params['goods_ids']);
		$params['return_nums'] = array_filter($params['return_nums']);
		if ((int)$params['return_type'] < 1) {
			$this->error = '请选择提服务类型';
			return FALSE;
		}
		if (empty($params['goods_ids'])) {
			$this->error = '商品ID不能为空';
			return FALSE;
		}
		if (empty($params['return_nums'])) {
			$this->error = '请选择商品要退货的数量';
			return FALSE;
		}
		if (empty($params['return_delivery_name'])) {
			$this->error = '请填写快递方式';
			return FALSE;
		}
		if (empty($params['return_delivery_sn'])) {
			$this->error = '请填写快递单号';
			return FALSE;
		}
		if (empty($params['return_descript'])) {
			$this->error = '请填写问题描述';
			return FALSE;
		}
		if (is_array($params['return_imgs'])) {
			$params['return_imgs'] = json_encode($params['return_imgs']);
		}
		$params['return_date'] = NOW_TIME;

		// 过滤正在申请中的
		$sqlMap = array();
		$sqlMap['order_id']      = $params['order_id'];
		$sqlMap['return_status'] = 0;
		$applyings = $this->where($sqlMap)->count();
		if ($applyings > 0) {
			$this->error = '您已申请该服务';
			return FALSE;
		}
		$result = $this->add($params);
		if (!$result) {
			$this->error = '申请失败';
			return FALSE;
		}

		// 把退还的商品数量字段写入订单商品明细表
		foreach ($params['return_nums'] as $k => $num) {
			$goods_id = $params['goods_ids'][$k];
			model('order_goods')->where(array('id' => $goods_id,'order_id' =>$params['order_id'],'user_id' => $params['user_id']))->setField('return_nums',$num);
		}
		return $result;
	}
}