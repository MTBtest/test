<?php
class attr_spec {
	public function __construct() {
		$this->db = D("GoodsCategory");
		$this->model = D('Model');
	}
	
	public function lists($data){
		extract($data);		
		if(isset($data['order'])){
			$order = $data['order'];
		}
		$sqlmap = array();
		$sqlmap['_string'] = "FIND_IN_SET('{$catid}',cat_ids)";
		$attr_ids = $this->model->where($sqlmap)->getField('id',TRUE);		
		$sqlmap = array();
		$sqlmap['model_id'] = array("IN", $attr_ids);
		$attr_list = M('Attribute')->where($sqlmap)->order($order)->select();
		
		$result = array();		
		foreach($attr_list AS $k => $v) {
			if($v['spec_ids']) {
				$spec_ids = str2arr($v['spec_ids']);
				foreach($spec_ids as $spec_id) {
					$v = $this->getSpec($spec_id);
					if(!$v) continue;
					$v['att_type'] = 'spec';
                    $result[$v['att_type'].'_'.$v['id']]=$v;
				}
				 
			} 
			
		} 
		foreach($attr_list AS $k => $v){
			if(!$v['spec_ids']) {
				$v['att_type'] = 'att';
				if($v['type'] == 3 ){
					$_inputValue = array();
					$_inputValue = M('GoodsAttribute')->where(array('attribute_id'=>$v['id']))->getField('attribute_value',true);
					$v['value'] = $_inputValue;
				}else{
					$v['value'] = str2arr($v['value']);
				}
				$result[$v['att_type'].'_'.$v['id']] = $v;
			}
		}
		if($_GET['attr']) {
			foreach($_GET['attr'] as $k => $v) {
				$_GET['attr'][$k] = urldecode($v);
			}
		}		
		return $result;
	}
	
	
		
	private function getSpec($spec_id) {
		$rs = D('Spec')->getById($spec_id);
		if(!$rs) return array();
		$rs['search'] = 1;
		$rs['value'] = str2arr($rs['value']);
		return $rs;
	}
}