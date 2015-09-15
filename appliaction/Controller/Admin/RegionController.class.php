<?php

class RegionController extends AdminBaseController {

	public function _initialize() {
		parent::_initialize();
		 $this->db = model('Region');
	}

	/**
	 * 列表
	 */
	public function index(){
		include $this->admin_tpl('region_index');
	}

	public function add(){
		$region=$_POST['region'];
		$info=array();
		$info['area_name']=$region;
		$info['parent_id']=1;
		$info['sort']=100;
		$result=$this->db->update($info);
		$this->db->build_cache();
		if ($result) {
			showmessage('添加成功!',U('Region/index'),1);
		} else {
			showmessage('添加失败!');
		}
	}
	/**
	 * 地域列表
	 */
	public function area_child(){
		$parent_id = isset($_GET['id'])?$_GET['id']:1;
		$data	  = $this->db->lists($parent_id );
		foreach ($data as $key => $value) {
			$data[$key]['state'] = $this->has_child($value['area_id']) ? 'closed' : 'open';
		}
		echo json_encode($data);
	}
	
	function has_child($id){
		$rows = $this->db->where(array('parent_id'=>$id))->count();
		return $rows > 0 ? true : false;
	}
	 /**
	 * 改变排序
	 */
	public function ajax_sort(){
		$id=intval($_GET['id']);
		$val=  intval($_GET['val']);
		if($id>0){
			$region=model('region');
			$data['sort']=$val;
			$this->db->where('area_id='.$id)->save($data);
			$this->db->build_cache();
			showmessage('恭喜你，成功改变排序！'); 
		}else{
		   showmessage('非法操作，请联系管理员！'); 
		}
	}
	 /**
	 * 删除地域
	 */
	public function ajax_del(){
		$id=intval($_GET['id']);
		if($id>0){
			if(!$this->has_child($id)){
				$this->db->where('area_id='.$id)->delete();
				$this->db->build_cache();
				showmessage('恭喜你，删除区域成功！',null,1); 
			}else{
				showmessage('请先删除子地区！'); 
			}
			
		}else{
		   showmessage('非法操作，请联系管理员！'); 
		}
	}

	/**
	 * 添加编辑
	 */
	public function ajax_update(){	
		$data['area_id']   = $_GET['area_id'];
		$data['area_name'] = $_GET['area_name'];
		$data['sort'] = (int)$_GET['area_sort']==0?100:(int)$_GET['area_sort'];
		$data['parent_id'] = (int)$_GET['parent_id']==0?1:$_GET['parent_id'];
		
		if(!isset($data['area_id']))unset($data['area_id']);
		//添加
		if(!isset($data['area_id'])){
			$r = $this->db->update($data);
			showmessage("添加区域完成",U('Region/index'),1);
		}else{ 
			$r = $this->db->update($data);
			showmessage("编辑区域完成",U('Region/index'),1);
		}
	}   
}
