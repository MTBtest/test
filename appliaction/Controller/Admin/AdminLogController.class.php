<?php
 /**
 *	  管理员日志列表
 *	  [Haidao] (C)2013-2099 Dmibox Science and technology co., LTD.
 *	  This is NOT a freeware, use is subject to license terms
 *
 *	  http://www.haidao.la
 *	  tel:400-600-2042
 */
class AdminLogController extends AdminBaseController {
	public function _initialize() {
		parent::_initialize();
		$this->db = model('admin_action_log');
	}

	public function index() {
		$user_id = isset($_GET['user_id'])?$_GET['user_id']:0;
		$stime = $_GET['stime'];
		$etime = $_GET['etime'];
		$user_arr = M('admin_user')->field('id,name')->select();;
		if(IS_POST){
			$sqlmap = array();
			$order = array();
			//筛选
			if (isset($user_id) && $user_id > 0) {
				$sqlmap['l.user_id'] = $user_id;
			}
			if(isset($stime) && !empty($stime)) {
				$dateline[] = array("GT", strtotime($stime.' 00:00:00'));
			}
			if(isset($etime) && !empty($etime)) {
				$dateline[] = array("LT", strtotime($etime.' 23:59:59'));
			}
			$dateline[] = 'AND';
			if(count($dateline) == 3){
				$sqlmap['l.dateline'] = $dateline;
			}
			//排序
			$_order=isset($_POST['order']) ? ($_POST['order']) : NULL;
			$_sort=isset($_POST['sort']) ? ($_POST['sort']) : NULL;
			if($_order && $_sort){
				$order[$_sort] = $_order;
			}else{
				$order['id'] = 'DESC';
			}
			$pagenum=isset($_POST['page']) ? intval($_POST['page']) : 1;
			$rowsnum=isset($_POST['rows']) && (int)($_POST['rows']) != 0 ? intval($_POST['rows']) : PAGE_SIZE;
			$field = 'l.*,a.name as admin_name';
			$join = 'LEFT JOIN __ADMIN_USER__ AS a ON l.user_id=a.id';
			$data['total'] = $this->db->alias('l')->where($sqlmap)->count();	//计算总数 
			$data['rows']=$this->db->alias('l')->field($field)->join($join)->where($sqlmap)->limit(($pagenum-1)*$rowsnum.','.$rowsnum)->order($order)->select();
			if (!$data['rows']) $data['rows']=array();
			$data['search']['s_user_id'] = $user_id;
			echo json_encode($data);
		}else{
			include $this->admin_tpl('adminlog_index');
		}
	}

	/* 删除日志 */
	public function delete(){
		$id = (array)$_GET['id'];
		if(empty($id)) {
			showmessage('请选择要删除的项目');
		}
		$sqlmap = array();
		$sqlmap['id'] = array("IN", $id);
		$result = $this->db->where($sqlmap)->delete();
		if(!$result) {
			showmessage('日志删除失败');
		} else {
			showmessage('日志删除成功', U('index'), 1);
		}
	}

	/* 清空日志 */
	public function clear() {
		$result = $this->db->where(array("id" => array("GT", 0)))->delete();
		if(!$result) {
			showmessage('日志清空失败');
		} else {
			showmessage('日志清空成功', U('index'), 1);
		}		
	}
}