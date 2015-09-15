<?php 
class goods {
	function __construct() {
		$this->db = D('Goods');
	}

	public function count($data){
		$map = $this->build_map($data);
		return M('Goods')->where($map)->count();
	}

	public function lists($data) {
		if(isset($data['order'])){
			$order = $data['order'];
		}
		if (!empty($data['page'])) {
			$this->db->page($data['page'],$data['limit']);
		}
		if (!empty($data['limit'])) {
			$this->db->limit($data['limit']);
		}
		$map = $this->build_map($data);
		$lists = $this->db->where($map)->order($order)->select();
		foreach($lists as $key =>  $value) {
			$lists[$key] = $this->db->detail($value['id']);
		}
		return $lists;
	}

	public function page($data){
		libfile('Page');
		if(isset($data['order'])) $order = $data['order'];
		$map = $this->build_map($data);
		$count=$this->db->where($map)->count();
        $pagenum=getconfig('site_listnum');
        $page = new Page($count, $pagenum);
        $page->setConfig('prev','');
        $page->setConfig('header','');
        $page->setConfig('next','');
        $page->setConfig('theme','<span class="text"><i>%nowPage%</i> /%totalPage% </span> %upPage% %downPage%');
		$map = $this->build_map($data);
		$lists = $this->db->where($map)->limit($page->firstRow . ',' . $page->listRows)->order($order)->select();
		foreach($lists as $key =>  $value) {
			$lists[$key] = $this->db->detail($value['id']);
		}
		$lists['page']=$page->show();
		return $lists;
	}

	public function history($attr) {
		$goodsModel = M('Goods');
		$_history = cookie('_history');
		if(empty($_history)) return FALSE;
	        if (!empty($attr['limit'])) {
				$goodsModel->limit($attr['limit']);
			}
		$map=array();
		$map['id'] = array('IN',str2arr($_history));
		$data=$goodsModel->where($map)->select();
		foreach($data as $key =>  $value) {
			$data[$key] = $this->db->detail($value['id']);
		}
		return $data;
	}
	

	public function build_map($data){
		extract($data);
		$map['status'] = 1;
		$map['_string'] = '1 = 1';		
		$_query = array();
		if($data['where']) {
			$_query[] = $data['where'];
		}		
		if(isset($data['statusext'])){
			$_query[] = " find_in_set('".$data['statusext']."',status_ext)";
		}
		
		if(isset($data['status'])){
			$map['status'] = $data['status'];
		}
		if(!empty($data['catid'])){
			$join = array("find_in_set('".$data['catid']."',cat_ids)");
			$ids = D('Admin/Category')->getChild(intval($data['catid']));
			foreach ($ids as $key => $value) {
				$join[] = "find_in_set('{$value}',cat_ids)";
			}
			$join_str = implode(' OR ', $join);
			$_query[] = '('.$join_str.')';
		}
		
		if($_query) {
			$map['_string'] = JOIN(" AND ", $_query);
		}
		
		if (isset($data['brand_id']) && is_numeric($data['brand_id']) && $data['brand_id'] > 0) {
			$map['brand_id'] = $data['brand_id'];
		}
		if($price) {
			$_sqlmap = $p_map = array();
			list($p_min, $p_max) = str2arr($price, ',');
			if($p_min > 0) {
				$_sqlmap['shop_price'][] = array("EGT", $p_min);
			}
			if($p_max > 0) {
				$_sqlmap['shop_price'][] = array("ELT", $p_max);
			}
			$_goods_ids = M('GoodsProducts')->where($_sqlmap)->group('goods_id')->getField('goods_id', TRUE);
			$_goods_ids = ($_goods_ids) ? $_goods_ids : array(-1);
			if($goods_ids) {
				$goods_ids = str2arr($goods_ids);
				$goods_ids = array_intersect($_goods_ids, $goods_ids);
				$goods_ids = ($goods_ids) ? $goods_ids : -1;
				
			} else {
				$goods_ids = $_goods_ids;
			}
		}
		if($goods_ids) {
			$map['id'] = array("IN", $goods_ids);
		}
		return $map;
	}
}