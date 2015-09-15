<?php 
/**
* 获取导航
*/
class navigation
{
	function __construct() {
		$this->db = D('Navigation');
	}

	public function lists($attr){
		extract($attr);
		$sqlmap = array();
		$sqlmap['enable'] = 1;
		$order = (!empty($order)) ? $order : '`order` DESC, `id` DESC';
		$lists = $this->db->where($sqlmap)->limit($limit)->order($order)->select();
		return $lists;
	}
}