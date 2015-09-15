<?php
/**
 *      商品咨询
 *      [Haidao] (C)2013-2099 Dmibox Science and technology co., LTD.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      http://www.haidao.la
 *      tel:400-600-2042
 */
class MemberConsultController extends AdminBaseController {
    public function _initialize() {
        parent::_initialize();
        $this->db = model('Consult');  		
    }

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
    		include $this->admin_tpl('member_consult_list');
    	}
    }
	

    /* 咨询回复 */
    public function reply() {
    	$id = (int) $_GET['id'];
    	$rs = $this->db->getById($id);
    	if (!$rs) showmessage('数据不存在');
    	$good_name = model('goods')->getFieldById($rs['goods_id'], 'name');
    	if ($rs['user_id'] > 0) {
            $user_name = model('user')->getFieldById($rs['user_id'], 'username');
    		$user_name = ($user_name) ? $user_name : '用户不存在' ;
    	} else {
    		$user_name = '游客';
    	}
    	if (IS_POST) {
    		$_GET['reply_time'] = NOW_TIME;
    		$result = $this->db->update($_GET);
    		if (!$result) {
    			showmessage($this->db->getError());
    		}
    		showmessage('咨询回复成功', U('lists'), 1);
    	} else {
    		include $this->admin_tpl('member_consult_reply');
    	}
    }

    /* 咨询删除 */
    public function delete() {
    	$id = (array) $_GET['id'];
    	if(empty($id)) showmessage('参数错误');
    	$sqlmap = array();
    	$sqlmap['id'] = array("IN", $id);
    	$this->db->where($sqlmap)->delete();
    	showmessage('咨询数据删除成功', U('lists'), 1);
    }
}