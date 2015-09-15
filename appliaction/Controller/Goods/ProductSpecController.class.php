<?php
/**
 * 规格管理
 *	  [Haidao] (C)2013-2099 Dmibox Science and technology co., LTD.
 *	  This is NOT a freeware, use is subject to license terms
 *
 *	  http://www.haidao.la
 *	  tel:400-600-2042
 */
class ProductSpecController extends AdminBaseController {

	public function _initialize() {
		parent::_initialize();
		$this->db = model('Spec');
	}

	/* 规格列表 */
	public function lists(){
//	  $sqlmap = array();
//	  $count  = $this->db->where($sqlmap)->count();
//		$pagesize = $_GET['pagesize'];
//		$pagesize = $pagesize ? $pagesize : getconfig('page_num');
//	  $page   = new Page($count, $pagesize);
//	  $lists  = $this->db->where($sqlmap)->order("`sort` ASC")->page(PAGE, $pagesize)->select();
//	  $meta_title = '规格列表';
//		$page = $page->show();
//	  include $this->admin_tpl("product_spec_lists");
		if(IS_POST){
			$sqlmap = array();
			
			$_order=isset($_POST['order']) ? ($_POST['order']) : NULL;
			$_sort=isset($_POST['sort']) ? ($_POST['sort']) : NULL;
			if($_order && $_sort){
				$order[$_sort] = $_order;
			}else{
				$order['sort'] = 'ASC';
				$order['id'] = 'DESC';
			}
			
			$pagenum=isset($_POST['page']) ? intval($_POST['page']) : 1;
			$rowsnum=isset($_POST['rows']) && (int)($_POST['rows']) != 0 ? intval($_POST['rows']) : PAGE_SIZE;
			$data['total'] = $this->db->count();	//计算总数 
			$data['rows']=$this->db->order($order)->limit(($pagenum-1)*$rowsnum.','.$rowsnum)->select();

			if (!$data['rows']) $data['rows']=array();
			echo json_encode($data);
		}else{
			include $this->admin_tpl('product_spec_lists');
		}
	}

	/* 改变状态 */
	public function ajax_status(){
		$id= intval($_GET['id']);
		if($id>0){
			$data['status']=array('exp',' 1-status ');;
			$this->db->where('id='.$id)->save($data);
			showmessage('恭喜你，成功改变状态！', '', 1);
		}else{
		   showmessage('非法操作，请联系管理员！'); 
		}
	}
	 /**
	 * 改变排序
	 */
	public function ajax_sort(){
		$id=intval($_GET['id']);
		$val=  intval($_GET['val']);
		if($id>0){
			$data['sort'] = $val;
			$this->db->where('id='.$id)->save($data);
			showmessage('恭喜你，成功改变排序！', '', 1); 
		}else{
		   showmessage('非法操作，请联系管理员！'); 
		}
	}
	 /**
	 * 删除品牌
	 */
	public function ajax_del(){
		$id = (array) $_GET['id'];
		$sqlmap = array();
		if(empty($id)) showmessage('参数错误');
		$sqlmap = array();
		$sqlmap['id'] = array("IN", $id);
		$this->db->where($sqlmap)->delete();
		showmessage('数据删除成功', U('lists'), 1);
	}
	/**
	 * 添加
	 */
	public function add(){
		if(IS_POST){
			$_GET['value'] = implode(',', array_unique($_GET['value']));
			if($this->db->create($_GET)){
			   $this->db->add();
			   showmessage('规则添加成功',U('lists'),1);
			}  else {
			   showmessage($this->db->getError(),NULL,0);
			}	 
		}  else {
			$validform = $dialog = TRUE;
			include $this->admin_tpl("product_spec_add");
		}
	}
	/**
	 * 编辑
	 */
	public function edit(){
		if(IS_POST){
			$_GET['value'] = implode(',', array_unique($_GET['value']));
			if($this->db->create($_GET)){
			   $this->db->save();
			   showmessage('规则编辑成功',U('lists'),1);
			}  else {
			   showmessage($this->db->getError(),NULL,0);
			}			
		}  else {
			$validform = $dialog = TRUE;
			$id = (int) $_GET['id'];
			$info = $this->db->getById($id);
			if(!$info) showmessage('参数错误或数据不存在');
			include $this->admin_tpl("product_spec_edit");			
		}
	}
}
