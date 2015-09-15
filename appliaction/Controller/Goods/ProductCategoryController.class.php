<?php

class ProductCategoryController extends AdminBaseController {
	public function _initialize() {
		parent::_initialize();
		$this->db = model('Category');
	}
	/**
	 * 添加
	 */
	public function add(){
		$validform = TRUE;
		if(IS_POST) {			
			if($this->db->create($_POST)){
			   $this->db->add();
			   $this->db->build_cache();
			   showmessage('添加分类成功',  U('lists'),1);
			}  else {
			   showmessage($cat->getError(),NULL,0);
			}	 
		}else{
			$data=$this->db->order('parent_id ASC,sort ASC,id ASC')->select();
			$info= getTree($data,0);
			$info = $this->db->formatCat($info);
			include $this->admin_tpl("product_category_add");
		}
		
	}
	/**
	 * 编辑
	 */
	public function edit(){
		$dialog = TRUE;
		$validform = TRUE;	
		if(IS_POST){
			if($_POST['old_pid']==0 && $_POST['old_pid']!=$_POST['parent_id'])showmessage('不允许移动主分类');
			$ptree=$this->findFather($_POST['parent_id']);
			if(in_array($_POST['old_pid'],$ptree)){
				showmessage("父分类不能移动到子分类",NULL,0);
			}
			if($this->db->create()){
			   $this->db->save();
			   $this->db->build_cache();
			   showmessage('ok',  U('lists'),1);
			}  else {
			   showmessage($this->db->getError(),NULL,0);
			}
		}  else {
			$data = $this->db->order('parent_id ASC,sort ASC,id ASC')->select();
			$info = getTree($data,0);
			$tree = $this->db->formatCat($info);
			$data = $this->db->getById($_GET['id']);
			include $this->admin_tpl("product_category_edit");
			
		}
	}
	/**
	 * 列表
	 */
	public function lists(){
		include $this->admin_tpl("product_category_lists");
	}
	/**
	 * 列表
	 */
	public function cat_child(){
		$parent_id = isset($_GET['id'])?$_GET['id']:0;
		$data	= $this->db->lists($parent_id );
		foreach ($data as $key => $value) {
			$data[$key]['state'] = $this->has_child($value['id']) ? 'closed' : 'open';
		}
		echo json_encode($data);
	}
	
	function has_child($id){
		$rows = $this->db->where(array('parent_id'=>$id))->count();
		return $rows > 0 ? true : false;
	}
	/* 取所有父类ID*/
	public function findFather($pid){
		static $flist=array(); 
		$row = $this->db->where('id='.$pid)->find();
		if ((int)$row['parent_id'] != 0)
		 {
			$classFID  = $row['parent_id'];
			$flist[] = $classFID;
			$this->findFather($classFID);
		}
		return $flist;
		
	}
	/**
	 * 地域列表
	 */
	public function child(){
		$parent_id = intval(I("aid"));
		$data	  = $this->db->lists($parent_id );
	   
		echo json_encode($data);
	}
	
	 /**
	 * 改变排序2014-10-28 16:05:55
	 */
	public function ajax_sort(){
		$id=intval($_GET['id']);
		$val=  intval($_GET['val']);
		if($id>0){
			$data['sort']=$val;
			$this->db->where('id='.$id)->save($data);
			showmessage('恭喜你，成功改变排序！'); 
		}else{
			showmessage('非法操作，请联系管理员！'); 
		}
	}
	 /**
	 * 删除2014-10-28 16:05:14
	 */
	public function ajax_del(){
		$id=intval($_GET['id']);
		if($id>0){
			if(!$this->has_child($id)){
				$this->db->where('id='.$id)->delete();
				$this->db->build_cache();
				showmessage('恭喜你，删除分类成功！',null,1); 
			}else{
				showmessage('请先删除子分类！'); 
			}
			
		}else{
		   showmessage('非法操作，请联系管理员！'); 
		}

	}
	/**
	 * 是否导航2014-10-28 15:59:07
	 */
	public function ajax_show_in_nav(){
		$id=intval($_GET['id']);
		if($id == 0) showmessage('非法操作，请联系管理员！',NULL,0);
		$map['id'] = $id;
		$data['show_in_nav'] = array('exp',' 1-show_in_nav ');
		if ($this->db->updateKey($map,$data)){
			showmessage('恭喜你，成功改变状态！',NULL,1);
		}else{
			showmessage('非法操作，请联系管理员！',NULL,0);
		}
	   
	}
	/**
	 * 改变状态2014-10-28 15:58:57
	 */
	public function ajax_status(){
		$id=intval($_GET['id']);
		if($id == 0) showmessage('非法操作，请联系管理员！',NULL,0);
		$map['id'] = $id;
		$data['status'] = array('exp',' 1-status ');
		if ($this->db->updateKey($map,$data)){
			$this->success('恭喜你，成功改变状态！');
		}else{
			$this->error('非法操作，请联系管理员！');
		}

	}

   
}
