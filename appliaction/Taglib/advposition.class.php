<?php 
/**
* 首页轮播
*/
class advposition
{
	function __construct() {
		$this->db = model('Adv');
	}
	public function lists($data){
	    $map =array();
		$map['starttime'] = array("LT", NOW_TIME);
        $map['endtime'] = array("GT", NOW_TIME);
		$map['status'] = array('EQ',1);
		$map['position_id'] = array('EQ',$data['position_id']);
		
		foreach ($data as $key => $value) {
			$map[$key] = array('EQ',$value);
		}
		
		if(array_key_exists("order",$data)){
			$order = $data['order'];
		}
		if(array_key_exists("limit",$data)) {
			$limit = $data['limit'];
		}
		$data=$this->db->where($map)->limit($limit)->order($order)->select();
		return dstripslashes($data);
	}
}