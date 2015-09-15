<?php
/**
 *      商品咨询标签
 *      [Haidao] (C)2013-2099 Dmibox Science and technology co., LTD.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      http://www.haidao.la
 *      tel:400-600-2042
 */
class consult
{
	function __construct() {
		$this->db = D('Consult');
	}

	public function count($attr) {
		return $this->db->where($this->build_map($attr))->count();
	}

	public function lists($attr) {
		extract($attr);
		$order = (isset($order)) ? $order : "`sort` DESC, `id` DESC";
		if (!isset($this->sqlmap)) {
			$this->sqlmap = $this->build_map($attr);
		}
		if (isset($page)) {
			$this->db->page($page, $limit);
		} else {
			$this->db->limit($limit);
		}
		$data = $this->db->where($this->sqlmap)->order($order)->select();
		$uids = array();
        foreach ($data as $k => $v) {
        	if($v['user_id'] < 1) continue;
            $uids[] = $v['user_id'];
        }

        $uids = array_unique($uids);
        if ($uids) {
        	$map = array();
        	$map['id'] = array("IN", $uids);
        	$u_list = D('User')->where($map)->getField('id, ico, username');
        }
        if ($u_list) {
        	foreach ($data as $k => $v) {
        		if ($v['user_id']) {
        			$v['ico'] = $u_list[$v['user_id']]['ico'];
        			$v['user_name'] = $u_list[$v['user_id']]['username'];
        		}
        		$data[$k] = $v;
        	}
        }
		return $data ;
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
		/* 指定咨询用户 */
		if (isset($user_id) && is_numeric($user_id)) {
			$sqlmap['user_id'] = $user_id;
		}
		/* 是否仅已回复 */
		if (isset($isreply)) {
			$sqlmap['reply_time'] = array('GT', 0);
		}
		$this->sqlmap = $sqlmap;
		return $sqlmap;
	}
}