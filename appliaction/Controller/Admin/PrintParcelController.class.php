<?php
/**
 *	  [Haidao] (C)2013-2099 Dmibox Science and technology co., LTD.
 *	  This is NOT a freeware, use is subject to license terms
 *
 *	  http://www.haidao.la
 *	  tel:400-600-2042
 */ 
class PrintParcelController extends AdminBaseController {
	public function _initialize() {
		parent::_initialize();
		$this->db = model('print_tpl_parcel');
		libfile('form');
	}

	public function index() {
		if(IS_POST) {
			$result = $this->db->lists($_GET);
			$this->ajaxReturn($result);
		} else {
			include $this->admin_tpl('parcel_list'); 
		}
	}
	public function add() { 
        if(IS_POST) {
           $result = $this->db->update($_GET);
            if(!$result) {
                showmessage($this->db->getError());
            } else {
                showmessage('发货单模板添加成功', U('index'), 1);
            }
        } else {
            include $this->admin_tpl('parcel_add');
        }
    }
    public function edit($id = 0) {
       $info = $this->db->where(array('id' => $_GET['id']))->find();
        if(IS_POST) {
            $result = $this->db->update($_GET);
            if(!$result) {
                showmessage($this->db->getError());
            } else {
                showmessage('发货单模板编辑成功', U('index'), 1);
            }
        } else {
            $content = json_decode($info['content'], TRUE);
            include $this->admin_tpl('parcel_edit');
        }
    }
    public function delete($id=0){
        extract($_GET);
        $id = (array)$id;
        if(in_array(1, $id)) showmessage('默认模版不能删除');
    	$result=$this->db->delete_by_id($_GET['id']);
    	if(!$result) {
            showmessage($this->db->getError(), NULL, 0);
        } else {
            showmessage('指定记录删除成功', NULL , 1);
        }

    }
     public function set($id=0){ 
        if ((int)($id) < 1) showmessage('您的参数有误');
        $this->db->where(array('id'=>array('NEQ',$id)))->setField('status',0);
        $result = $this->db->where(array('id'=>$id))->setField('status',1);
        if(!$result) showmessage('该发货单模版已启用，请勿重复启用');
        showmessage('发货单模版已启用', NULL , 1);
    }
  
    
    
}

    
	
?>