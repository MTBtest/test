<?php 
/**
 * 区域划分
 *      [Haidao] (C)2013-2099 Dmibox Science and technology co., LTD.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      http://www.haidao.la
 *      tel:400-600-2042
 */
class ZoneController extends AdminBaseController
{
	public function _initialize() {
		parent::_initialize();
		$this->db = model('zone');
		$this->region_db = model('region');
	}

	/* 区域管理 */
	public function manage() {
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
			$data['rows']=$this->db->limit(($pagenum-1)*$rowsnum.','.$rowsnum)->order($order)->select();
			foreach ($data['rows'] as $k => $v) {
				$provinces = explode(',', $v['provinces']);
				$regions = $this->region_db->where(array('area_id' => array("IN", $provinces)))->getField('area_name', TRUE);
				$data['rows'][$k]['region'] = implode(",", $regions);
			}
			if (!$data['rows']) $data['rows']=array();
			echo json_encode($data);
		}else{
			include $this->admin_tpl('zone_manage');
		}
	}

	public function add() {
		if (IS_POST) {
			if (is_array($_GET['provinces'])) {
				$_GET['provinces'] = implode(',', $_GET['provinces']);
			}
			$rs = $this->db->update($_GET);
			if (!$rs) {
				showmessage($this->db->getError());
			} else {
				showmessage('区域添加成功', U('manage'), 1);
			}
		} else {
			$this->regionids = $this->db->getField('provinces', TRUE);
			$this->regionids = implode(',', $this->regionids);
			$this->regionids = explode(',', $this->regionids);
			// 所有的一级地区
			$region_lists = $this->region_db->where(array('parent_id' => '1'))->select();
			include $this->admin_tpl('zone_add');
		}
	}

	public function edit() {
		$id = (int) $_GET['id'];
		if (IS_POST) {
			if (is_array($_GET['provinces'])) {
				$_GET['provinces'] = implode(',', $_GET['provinces']);
			}
			$rs = $this->db->update($_GET);
			if (!$rs) {
				showmessage('区域编辑失败');
			} else {
				showmessage('区域编辑成功', U('manage'), 1);
			}
		} else {
			$this->regionids = $this->db->where(array('id' => array("NEQ", $id)))->getField('provinces', TRUE);
			$this->regionids = implode(',', $this->regionids);
			$this->regionids = explode(',', $this->regionids);			
			$info = $this->db->getById($id);			
			$provinces = explode(',', $info['provinces']);
			$region_lists = $this->region_db->where(array('parent_id' => '1'))->select();
			include $this->admin_tpl('zone_edit');
		}
	}	

	/* 删除区域 */
	public function delete() {
		$id = (array)$_GET['id'];
		if(!empty($id)){
			$sqlmap = array();
			$sqlmap['id'] = array('IN',$id);
			$this->db->where($sqlmap)->delete();
			showmessage('区域数据删除成功！',null,1); 
		}else{
			showmessage('非法操作，请联系管理员！'); 
		}
	}
}