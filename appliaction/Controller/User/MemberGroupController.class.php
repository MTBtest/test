<?php
/**
 * 会员组管理
 *      [Haidao] (C)2013-2099 Dmibox Science and technology co., LTD.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      http://www.haidao.la
 *      tel:400-600-2042
 */
class MemberGroupController extends AdminBaseController 
{
    public function _initialize() {
        parent::_initialize();
        $this->db = model('UserGroup');
    }
    /**
     * 列表管理
     */
    public function lists() {
    	if(IS_POST){
    		$sqlmap = array();
			$_order=isset($_POST['order']) ? ($_POST['order']) : NULL;
			$_sort=isset($_POST['sort']) ? ($_POST['sort']) : NULL;
			if($_order && $_sort){
				$order[$_sort] = $_order;
			}else{
				$order['id'] = 'DESC';
			}
			$pagenum=isset($_POST['page']) ? intval($_POST['page']) : 1;
			$rowsnum=isset($_POST['rows']) && (int)($_POST['rows']) != 0 ? intval($_POST['rows']) : PAGE_SIZE;
			$data['total'] = $this->db->count();    //计算总数 
			$data['rows']=$this->db->limit(($pagenum-1)*$rowsnum.','.$rowsnum)->order($order)->select();
			if (!$data['rows']) $data['rows']=array();
			echo json_encode($data);
    	}else{
    		include $this->admin_tpl('member_group_list');
    	}
    }
     /**
     * 删除品牌
     */
    public function ajax_del(){
        $id = (array) $_GET['id'];
    	if(empty($id)) showmessage('参数错误');
    	$sqlmap = array();
    	$sqlmap['id'] = array("IN", $id);
    	$this->db->where($sqlmap)->delete();
    	showmessage('咨询数据删除成功', U('lists'), 1);
    }

    /* 添加会员等级 */
    public function add(){
    	$validform = TRUE;
		$dialog = TRUE;
        if(IS_POST){
            $rs = $this->db->update($_GET);
			if (!$rs) {
				showmessage($this->db->getError());
			} else {
				showmessage('会员等级添加成功', U('lists'), 1);
			}
        }  else {
            include $this->admin_tpl('member_group_add');
        }
    }
    /**
     * 编辑
     */
    public function edit(){
		$validform = TRUE;
		$dialog = TRUE;
        $id = (int) $_GET['id'];
        $info = $this->db->getById($id);
        if(!$info) showmessage('参数错误');
        if(IS_POST){
            $rs = $this->db->update($_GET);
			if (!$rs) {
				showmessage($this->db->getError());
			} else {
				showmessage('会员等级编辑成功', U('lists'), 1);
			}
        } else {
            include $this->admin_tpl('member_group_edit');
        }
    }
}
