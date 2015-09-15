<?php
/**
 * 
 * 会员中心 
 * @author wj 
 * @date  2014-10-13
 *
 */
class IndexController extends UserBaseController {
    public function _initialize() {
        parent::_initialize();
        $this->db = model('Order');
        $this->order_goods_db = model('OrderGoods');

    }
    public function index() {
    	$sqlmap = $in_trade = $ok_trade = $collect = $historys = array();
    	$sqlmap['user_id'] = $this->userid;
    	/* 进行中的交易 */
    	$sqlmap['order_status'] = array("LT", 2);
    	$in_trade = $this->db->where($sqlmap)->limit(3)->order("id DESC")->select();
    	/* 已完成的交易 */
    	$sqlmap['order_status'] = 2;
    	
    	$ok_ids = $this->db->where($sqlmap)->getField('id', TRUE);
    	if($ok_ids) {
    		$map = array();
    		$map['order_id'] = array("IN", $ok_ids);
    		$ok_trade = $this->order_goods_db->where($map)->limit(10)->select();
    	}
    	/* 收藏的商品 */
    	$coll_ids = model('UserCollect')->where(array('user_id' => $this->userid))->order("id DESC")->getField('goods_id', TRUE);
    	if($coll_ids) {
    		$map = array();
    		$map['id'] = array("IN", $coll_ids);
    		$collect = model('Goods')->where($map)->limit(10)->select();
    		foreach ($collect as $key => $value) {
    			$v = model('Goods')->detail($value['id']);
    			$collect[$key] = $v;
    		}
    	}

    	/* 浏览记录 */
    	$_history = cookie('_history');
    	if($_history){
    		$map = array();
    		$map['id'] = array("IN", $_history);
    		$historys = model('Goods')->where($map)->limit(10)->select();
    		foreach ($historys as $key => $value) {
    			$v = model('Goods')->detail($value['id']);
    			$historys[$key] = $v;
    		}
    	}    	
        $SEO = seo(0,"我的会员中心");
        include template('index');  
    }
}