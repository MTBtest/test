<?php
/**
 *      帮助中心列表
 *      [Haidao] (C)2013-2099 Dmibox Science and technology co., LTD.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      http://www.haidao.la
 *      tel:400-600-2042
 */
class help {
	function __construct() {
		$this->db = D('Help');
	}

	/* 统计数量 */
	public function count($attr) {
		return $this->db->where($this->build_map($attr))->count();
	}

	/* 获取列表 */
	public function lists($attr){
		extract($attr);
		$order = (isset($order)) ? $order : "`sort` ASC, `id` DESC";
		$this->sqlmap = $this->build_map($attr);
		if (isset($page)) {
			$this->db->page($page, $limit);
		} else {
			$this->db->limit($limit);
		}
		return $this->db->where($this->sqlmap)->order($order)->select();
	}

	/* 生成条件 */
	public function build_map($attr) {
		extract($attr);
		$sqlmap = array();
		$sqlmap['status'] = 1;
		/* 指定分类 */
		if (isset($fpid)) {
			if(preg_match('#,#', $fpid)) {				
				$sqlmap['fpid'] = array("IN", explode(",", $fpid));
			} else {
				$sqlmap['fpid'] = $fpid;
			}
		}
		$this->sqlmap = $sqlmap;
		return $sqlmap;
	}
}