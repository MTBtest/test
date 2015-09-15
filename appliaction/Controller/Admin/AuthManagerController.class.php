<?php
class AuthManagerController extends AdminBaseController {
/**
 *	  权限管理首页
 *	  [Haidao] (C)2013-2099 Dmibox Science and technology co., LTD.
 *	  This is NOT a freeware, use is subject to license terms
 *
 *	  http://www.haidao.la
 *	  tel:400-600-2042
 */
	public function _initialize() {
		parent::_initialize();
		$this->db = model('AuthManager');
	}
	public function index(){

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
				//$data['rows'][$key]['group_name'] = M('')
			}
			if (!$data['rows']) $data['rows']=array();
			echo json_encode($data);
		}else{
			include $this->admin_tpl('auth_manager_index');
		}
	}
/**
 *	  创建管理员用户组
 *	  [Haidao] (C)2013-2099 Dmibox Science and technology co., LTD.
 *	  This is NOT a freeware, use is subject to license terms
 *
 *	  http://www.haidao.la
 *	  tel:400-600-2042
 */
	public function creategropup(){
		if (IS_POST) {
			//默认数据
			$_POST['module'] =  'admin';
			$_POST['type']   =  1;
			$_POST['stae']   =  1;
			$db=model('Auth_manager');
			if($db->create()){
				if (empty($_GET['id'])){
					$db->add();
				}else{
					$db->save();
				}
				
				
				$arr["status"] = "y";
				$arr["info"] = "ok";
				$arr["url"] = U('AuthManager/index');
				die(json_encode($arr));
			}  else {
				$arr["status"] = "n";
				$arr["info"] = $db->getError();
				die(json_encode($arr));
				
			}
		}else{
			$validform = '';
			if (empty($_GET['id'])){
				$meta_title = '新增用户组';
			}else{
				$meta_title = '编辑用户组';
				$db=model('Auth_manager');
				$info=$db->where('id='.$_GET['id'])->find();
			}
			
	  
			include $this->admin_tpl('auth_manager_creategropup');

		}
		
	}
/**
 *	  状态修改
 *	  [Haidao] (C)2013-2099 Dmibox Science and technology co., LTD.
 *	  This is NOT a freeware, use is subject to license terms
 *
 *	  http://www.haidao.la
 *	  tel:400-600-2042
 */
	public function changeStatus($method=null){
		if ( empty($_GET['id']) ) {
		  showmessage('请选择要操作的数据!');
		} 
		$id=$_GET['id'];
		$db=model('Auth_manager');
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
/**
 *	  访问授权页面
 *	  [Haidao] (C)2013-2099 Dmibox Science and technology co., LTD.
 *	  This is NOT a freeware, use is subject to license terms
 *
 *	  http://www.haidao.la
 *	  tel:400-600-2042
 */
	public function access(){
		$this->updateRules();
		$group_id=$_GET['group_id'];
		$auth_group = model('AuthManager')->get_byId($group_id);
		$rule=str2arr($auth_group['rules']);
		$result=model('AuthManager')->getList();
		
		$list=$result['list'];
		$node_list=list_to_tree($list);
		$child_rules = $result['child_rules'];
		$this_group=$auth_group[(int)$_GET['group_id']];
		$group_name=$_GET['group_name'];
		$meta_title = '访问授权';
		include $this->admin_tpl('auth_manager_access');
	}
/**
 *	  后台节点配置的url作为规则存入auth_rule
 *	  执行新节点的插入,已有节点的更新,无效规则的删除三项任务
 *	  [Haidao] (C)2013-2099 Dmibox Science and technology co., LTD.
 *	  This is NOT a freeware, use is subject to license terms
 *
 *	  http://www.haidao.la
 *	  tel:400-600-2042
 */
	public function updateRules(){
		if (IS_POST) {
			$arr=$_POST['rules'];

			$group_id=$_GET['group_id'];
			if ( empty($group_id) ) showmessage('请选择要操作的分组!');
			$ids=arr2str($arr, ',');
			
			$db=model('AdminAuthGroup');
			$data['rules']=$ids;
			$result = $db->where('id='.$group_id)->save($data);
			if ($result) {
				showmessage('更新权限成功');
			}
		}
	}


}	
