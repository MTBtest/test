<?php
/**
 *      [Haidao] (C)2013-2099 Dmibox Science and technology co., LTD.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      http://www.haidao.la
 *      tel:400-600-2042
 */
class MenuModel extends SystemModel {
    protected $_auto = array(
        array('dateline', NOW_TIME, Model:: MODEL_BOTH, 'string'),
    );
    public function update($data) {
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
    public function lists($parent_id = 0, $order = 'sort ASC', $field = true) {
        $map = array('parent_id' => $parent_id);
        return $this->field($field)->where($map)->order($order)->select();
    }
    public function formatCat($data) {
        foreach ($data as $key => $value) {

            $level = $value['level'];
            $tnum1 = $data[$key + 1]['level'];
            $_t = "├";

            if ($tnum1 == 0 || $level != $tnum1 && $level != 0) {
                $_t = "└";
            }

            unset($_n);
            for ($i = 0; $i < $level; $i++) {
                $_n.="│";
            }
            if($value['level']==0){
            $result[$key]['text'] = $_n . $_t . $value['name'];
            $result[$key]['value'] = $value['id'];
            }
        }
        return $result;
    }
  
}