<?php
class AdminUserController extends AdminBaseController {
	public function _initialize() {
		parent::_initialize();
		$this->db = model('AdminUser');
	}
/**
 *	  管理员列表
 *	  [Haidao] (C)2013-2099 Dmibox Science and technology co., LTD.
 *	  This is NOT a freeware, use is subject to license terms
 *
 *	  http://www.haidao.la
 *	  tel:400-600-2042
 */
	public function index(){

		$group_list=model('AdminAuthGroup')->where(array('status'=>1))->getField('id,title,description',true);
		if(IS_POST){
			$sqlmap = array();
			$order = array();
			
			//排序
			$_order=isset($_POST['order']) ? ($_POST['order']) : NULL;
			$_sort=isset($_POST['sort']) ? ($_POST['sort']) : NULL;
			if($_order && $_sort){
				$order[$_sort] = $_order;
			}else{
				$order['id'] = 'ASC';
			}
			$pagenum=isset($_POST['page']) ? intval($_POST['page']) : 1;
			$rowsnum=isset($_POST['rows']) && (int)($_POST['rows']) != 0 ? intval($_POST['rows']) : PAGE_SIZE;
		
			$data['total'] = $this->db->where($sqlmap)->count();	//计算总数 
			$data['rows'] = $this->db->where($sqlmap)->limit(($pagenum-1)*$rowsnum.','.$rowsnum)->order($order)->select();
			foreach ($data['rows'] as $key => $value) {
				$data['rows'][$key]['group_name'] = $group_list[$value['group_id']]['title'];
			}
			if (!$data['rows']) $data['rows']=array();
			echo json_encode($data);
		}else{
			include $this->admin_tpl('adminuser_index');
		}
		
			
	}
	
	public function add(){
		$validform = TRUE;
		$dialog = TRUE;
		if (IS_POST) {
			$user=$this->db;
			$_POST['valid'] = random(10);
			$_POST['password'] = $_POST['password'].$_POST['valid'];
			unset($_POST['password2']);
			if($user->create($_POST)){
				$user->add();
				showmessage('添加管理员成功',U('index'),1);
			}  else {
				showmessage('添加管理员失败',U('index'),0);
			}
			
		}  else {
			include $this->admin_tpl('adminuser_add');
		}
	}
	
	public function setgroup(){
		$ids = array();$sqlmap = array();
		$ids = $_GET['ids'];
		$data['group_id'] = arr2str($ids);
		$sqlmap['id'] = $_GET['user_id'];
		$r = $this->db->where($sqlmap)->save($data);
		showmessage('更新用户组成功',null,1);
	}
	/**
	 * 状态修改
	 * @author nbnat.com
	 */
	public function changeStatus($method=null){
		if ( empty($_GET['id']) ) {
		  showmessage('请选择要操作的数据!');
		} 
		$id=$_GET['id'];
		$method = $_GET['method'];
		$db=model('AdminUser');
		switch ( strtolower($method) ){
			case 'forbidgroup':
				$data['status']=0;
				$db->where('id='.$id)->save($data);
			  showmessage('成功禁用',null,1);
				break;
			case 'resumegroup':
				$data['status']=1;
				$db->where('id='.$id)->save($data);
			  showmessage('成功启用',null,1);
				break;
			case 'deletegroup':
				$db->where('id='.$id)->delete();
			  showmessage('成功删除',null,1);
				break;
			default:
			  showmessage($method.'参数非法');
		}
	}
	
	public function reuserpwd(){
		$validform = TRUE;
		$id = $_GET['id'];
		if(!in_array(ADMIN_ID, getconfig('ADMIN_LIST'))){
			showmessage('对不起,你不是超级管理员');
		}
		if(IS_POST){
			$admin_valid = $this->db->getFieldById(ADMIN_ID,'valid,password');
			$adminpassword = $_GET['adminpassword'];
			$data['id'] = $_GET['id'];
			$data['valid'] = random(10);
			$data['password'] = md5($_GET['npassword'].$data['valid']);
			$admin_password = md5($adminpassword.key($admin_valid));
			if($admin_password != current($admin_valid)){
				showmessage('管理员密码不正确');
			}
			$this->db->save($data);
			showmessage("管理员{$_GET['user']}的密码更新成功",U('index'),1);
			
		}else{
			$user = $this->db->getFieldById($id,'id,name');
			include $this->admin_tpl('reuserpwd');
		}
		
	}
}	
