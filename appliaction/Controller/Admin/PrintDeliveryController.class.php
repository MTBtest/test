<?php
/**
 *	  [Haidao] (C)2013-2099 Dmibox Science and technology co., LTD.
 *	  This is NOT a freeware, use is subject to license terms
 *
 *	  http://www.haidao.la
 *	  tel:400-600-2042
 */ 
class PrintDeliveryController extends AdminBaseController {
	public function _initialize() {
		parent::_initialize();
		$this->db = model('print_tpl_delivery');
	}

	public function index() {
		if(IS_POST) {
            //分页
            $_GET['pagenum'] = isset($_GET['page']) ? intval($_GET['page']) : 1;
            $_GET['rowsnum'] = isset($_GET['rows']) && (int)($_GET['rows']) != 0 ? intval($_GET['rows']) : PAGE_SIZE;
            $result = $this->db->lists($_GET);
            $this->ajaxReturn($result);
		} else {
			include $this->admin_tpl('print_delivery'); 
		}
	}
    
    public function add() { 
        $deliverys = $this->getDeliverys();
        if(IS_POST) {
            if ($_GET['delivery_id'] < 1) showmessage('请选择快递单模版');
            $result = $this->db->update($_GET);
            if(!$result) {
                showmessage($this->db->getError());
            } else {
                showmessage('快递单模板添加成功', U('index'), 1);
            }
        } else {
            include $this->admin_tpl('print_delivery_add');
        }
    }
    
    public function edit($delivery_id = 0) {
        $deliverys = $this->getDeliverys($delivery_id);
        $info = $this->db->where(array('delivery_id' => $_GET['delivery_id']))->find();
        if(IS_POST) {
            $result = $this->db->update($_GET);
            if(!$result) {
                showmessage($this->db->getError());
            } else {
                showmessage('快递单模板编辑成功', U('index'), 1);
            }
        } else {
            $content = json_decode($info['content'], TRUE);
            include $this->admin_tpl('print_delivery_edit');
        }
    }
    
    private function getDeliverys($delivery_id = 0) {
        $DB = $this->db;
        $deliverys = model("delivery")->getField("id, enname, name", TRUE);
        
        if($delivery_id > 0) {
            $DB->where(array("delivery_id" => array("NEQ", $delivery_id)));
        }
        $delivery_ids = $DB->getField("delivery_id", TRUE);
        foreach ($deliverys as $key => $value) {
            if($delivery_ids && in_array($key, $delivery_ids)) unset ($deliverys[$key]);
        }
        return $deliverys;
    }


    /* 删除 */
    public function delete() {
        $_GET['delivery_id'] = (array)$_GET['delivery_id'];
        $result = $this->db->delete_by_id($_GET['delivery_id']);
        if(!$result) {
            showmessage($this->db->getError(), NULL, 0);
        } else {
            showmessage('指定记录删除成功', NULL , 1);
        }
    }
}