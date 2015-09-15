<?php
/**
 *      [Haidao] (C)2013-2099 Dmibox Science and technology co., LTD.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      http://www.haidao.la
 *      tel:400-600-2042
 */
class PrintTplParcelModel extends SystemModel {
    protected $_validate = array(
        array('parcel_name','require','模板名称不能为空'),
      
    );


    protected $_auto = array(
        array('dateline', NOW_TIME, Model:: MODEL_BOTH, 'string'),
    );
    
    public function update() {
        $data = $this->create($data);
		if (empty($data)) {
			$this->error = $this->getError();
			return false;
		}
        $sqlmap = array();
        $sqlmap['id'] = $data['id'];
        if($this->where($sqlmap)->count()) {
            $result = $this->save($data);
        } else {
            $result = $this->add($data);
        }
        if(!$result) {
            $this->error('数据更新失败');
            return FALSE;
        }
        return TRUE;
    }


    public function lists($params) {
        $result = array();
        $result['total'] = $this->where()->count();
        $result['rows'] = $this->where()->select();
        return $result;    
    }
    
    public function delete_by_id($id) {
        if(empty($id)) {
            $this->error = '请指定要删除的记录';
            return FALSE;
        }
        $sqlmap = array();
        if(is_array($id)) {
            $sqlmap['id'] = array("IN", $id);
        } else {
            $sqlmap['id'] = $id;
        }
        $result = $this->where($sqlmap)->delete();
        if(!$result) {
            $this->error = '删除记录失败';
            return FALSE;
        }
        return TRUE;
    }
    
  
}