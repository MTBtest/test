<?php
/**
 *      商品评论标签
 *      [Haidao] (C)2013-2099 Dmibox Science and technology co., LTD.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      http://www.haidao.la
 *      tel:400-600-2042
 */
class comment
{
	function __construct() {
		$this->db = model('comment');
	}

	public function count($attr) {
		return $this->db->where($this->build_map($attr))->count();
	}
	public function people_count($attr){
		 //$this->db->where($this->build_map($attr))->count('distinct(user_id)');
	    return  $this->db->where($this->build_map($attr))->count('distinct(user_id)');
	}


	public function hot_ment($data){
		$map = array();
		if (!empty($data['limit'])) {
			$limit=$data['limit'];
		}
		$sql = "SELECT `goods_id`,contents,user_name,user_id,COUNT(`goods_id`) AS num FROM `cz_comment` WHERE status=1 GROUP BY `goods_id` ORDER BY num DESC LIMIT ".$limit;
		$data=M()->query($sql);
		$ids=array();
		$userid = array();
		foreach ($data as $k => $v) {
			$ids[]=$v['goods_id'];
			$userid[]=$v['user_id'];
		}

		$name =array();
		$name['id'] = array('IN',$ids);
		$user_id =array();
		$user_id['id'] =  array('IN',$userid);
		$goods=D('Goods')->field('name')->where($name)->select();
		foreach ($goods as $k => $v) {
			$data[$k]['goods_name'] =$v['name'];
		}
		$name=D('User')->field('ico')->where($user_id)->select();
		foreach ($name as $k => $v) {
			$data[$k]['ico'] =$v['ico'];
		}
		return $data;
	}

	public function lists($attr) {
		$order = (isset($order)) ? $order : "`sort` DESC, `id` DESC";
		if (!isset($this->sqlmap)) {
			$this->sqlmap = $this->build_map($attr);
		}
		if (isset($page)) {
			$this->db->page($page, $limit);
		} else {
			$this->db->limit($limit);
		}

		$data=$this->db->where($this->sqlmap)->select();
		return $data;
	}

	public function build_map($attr) {
		extract($attr);
		$sqlmap = array();
		/* 审核状态 */
		if (isset($status)) {
			$sqlmap['status'] = (int) $status;
		} else {
			$sqlmap['status'] = 1;
		}
		/* 商品ID */
		if (isset($goods_id)) {
			if(preg_match('#,#', $goods_id)) {				
				$sqlmap['goods_id'] = array("IN", explode(",", $goods_id));
			} else {
				$sqlmap['goods_id'] = $goods_id;
			}
		}
		/* 产品ID */
		if (isset($product_id)) {
			if(preg_match('#,#', $product_id)) {				
				$sqlmap['product_id'] = array("IN", explode(",", $product_id));
			} else {
				$sqlmap['product_id'] = $product_id;
			}
		}
		/* 订单ID */
		if (isset($order_id)) {
			$sqlmap['order_id'] = $order_id;
		}
		/* 指定咨询用户 */
		if (isset($user_id) && is_numeric($user_id)) {
			$sqlmap['user_id'] = $user_id;
		}
		/* 是否仅已回复 */
		// if (isset($isreply)) {
		// 	$sqlmap['reply_time'] = array('GT', 0);
		// }
		$this->sqlmap = $sqlmap;
		return $sqlmap;
	}
}