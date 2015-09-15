<?php
/**
 *      [Haidao] (C)2013-2099 Dmibox Science and technology co., LTD.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      http://www.haidao.la
 *      tel:400-600-2042
 */
class OrderTrackModel extends SystemModel {
    protected $_validate = array(
        array('order_sn','require','订单号不能为空'),
        array('track_msg','require','跟踪内容不能为空'),
    );


    protected $_auto = array(
        array('dateline', NOW_TIME, Model:: MODEL_BOTH, 'string'),
        array('clientip', 'get_client_ip', Model:: MODEL_BOTH, 'function'),
    );

    /* 根据订单号查询列表 */
    public function fetch_all_by_order_sn($order_sn = '') {
    	$sqlmap = array();
    	$sqlmap['order_sn'] = $order_sn;
    	return $this->where($sqlmap)->order("`dateline` DESC")->select();
    }
}