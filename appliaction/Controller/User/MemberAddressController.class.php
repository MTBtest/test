<?php

class MemberAddressController extends AdminBaseController {

	public function _initialize() {
		parent::_initialize();	
		$this->db = model('UserAddress');
	}
	
	/**
	 * 收货地址列表
	 */
	public function lists(){
		$user_id = $_GET['user_id'];
		if(IS_POST){
			$sqlmap = array();	
			$field = "a.*,b.area_name as prov_name,c.area_name as city_name,d.area_name as dist_name";
			$join = " LEFT JOIN `__REGION__` as b ON a.province=b.area_id";
			$join.= " LEFT JOIN `__REGION__` as c ON a.city=c.area_id";
			$join.= " LEFT JOIN `__REGION__` as d ON a.city=d.area_id";		
			$sqlmap['user_id']=I('user_id','0','intval');		
			$count = $this->db->where($sqlmap)->count();
			//分页
			$pagenum=isset($_GET['page']) ? intval($_GET['page']) : 1;
			$rowsnum=isset($_GET['rows']) && (int)($_GET['rows']) != 0 ? intval($_GET['rows']) : PAGE_SIZE;
			//计算总数 
			$data['total'] = $this->db->where($sqlmap)->count();	
			$data['rows']=$this->db->alias('a')->field($field)->join($join)->where($sqlmap)->limit(($pagenum-1)*$rowsnum.','.$rowsnum)->order($order)->select();
			if (!$data['rows']) $data['rows']=array();
			echo json_encode($data);
		}else{
			include $this->admin_tpl('member_address_list');
		}
	}
}