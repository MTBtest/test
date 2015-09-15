<?php 
/**
* 首页轮播
*/
class focus
{
	function __construct() {
		$this->db = D('Focus');
	}
	public function lists($attr){
		extract($attr);
		$sqlmap = array();
		$sqlmap['status'] = 1;
		$order = (empty($order)) ? '`sort` DESC,`id` DESC' : '';
		return $this->db->where($sqlmap)->limit($limit)->order($order)->select();
	}
}