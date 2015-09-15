<?php
/**
 *      文章分类列表标签
 *      [Haidao] (C)2013-2099 Dmibox Science and technology co., LTD.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      http://www.haidao.la
 *      tel:400-600-2042
 */
class artcat {
	function __construct() {
		$this->db = D('ArticleCategory');
	}

	/* 总数 */
	public function count($attr) {
		return $this->db->where($this->build_map($attr))->count();
	}

	/* 列表 */
	public function lists($attr) {
		extract($attr);		
		$order = (!empty($order)) ? $order : "`sort` DESC,`id` DESC";
		/* 是否开启分页 */
		if (isset($page)) {
			$this->db->page($page, $limit);
		} else {
			$this->db->limit($limit);
		}
		return $this->db->where($this->build_map($attr))->order($order)->select();
	}

	/* 生成条件 */
	private function build_map($attr) {
		extract($attr);
		$sqlmap = array('status' => 1);
		if (isset($parent_id) && is_numeric($parent_id)) {
			$sqlmap['parent_id'] = (int) $parent_id;
		}
		if($where) $sqlmap['_string'] = $where;
		$this->sqlmap = $sqlmap;
		return $sqlmap;
	}

	/* 树形分类 */
	public function tree($data){
		$sqlmap = array('status' => 1);
		$order = "`sort` desc,`id` desc";
		if(isset($data['order'])){
			$order = $data['order'];
		}
		if (isset($data['pid'])) {
			$pid = $data['pid'];
		}
		$list = $this->db->where($map)->order($order)->select();
		$tree = list_to_tree($list, 'id', 'parent_id', '_child', $pid);
		return $tree;
	}


}