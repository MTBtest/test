<?php
/**
 *      [Haidao] (C)2013-2099 Dmibox Science and technology co., LTD.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      http://www.haidao.la
 *      tel:400-600-2042
 */
class CouponsListModel extends SystemModel {
    protected $sqlmap;
    public function total($params = array()) {
        if(!$this->sqlmap && count($params) > 0) $this->buildMap($params);
        $total = $this->where($this->sqlmap)->count();
        return $total ? $total : 0;
    }
    
    /**
     * 获取我的优惠券
     * @param type $params
     */
    public function lists($params = array()) {
        $DB = & $this;
        $sqlmap = $this->buildMap($params);
        $result = $lists = array();
        // 分页大小
        if(isset($params['page'])) {
            $result['total'] = $this->total($params);
            $DB->page($params['page']);
        }        
        // 排序方式
        $order = (isset($params['sort'])) ? $params['sort'] : '`status` ASC,`end_time` DESC';
        $limit = (isset($params['limit'])) ? (int) $params['limit'] : 8;        
        $items = $DB->where($sqlmap)->order($order)->limit($limit)->select();
        foreach ($items as $key => $v) {
            // 使用规则
            $items[$key]['limit'] = model('coupons')->getFieldById($v['cid'],'limit');
            $items[$key]['start_time'] = mdate($v['start_time'],'Y-m-d');
            $items[$key]['end_time'] = mdate($v['end_time'],'Y-m-d');
            $items[$key]['end_time1'] = $v['end_time'];
        }
        if(!$items) return array('status' => 'error', 'info' => '没有符合条件的内容');
            foreach ($items as $item) {
                $item['goods_info'] = model('goods')->detail($item['goods_id']);
                $lists[] = $item;
            }
        if(isset($result['total'])) {
            $result['lists'] = $lists;
        } else {
            $result = $lists;
        }
        return $result;
    }
    
     /**
     * 生成查询条件
     * @param type $params
     */
    private function buildMap($params = array()) {
        $sqlmap = array();
        // 用户ID
        if((int)$params['user_id'] > 0) {
            $sqlmap['user_id'] = $params['user_id'];
        }
        // 优惠券状态(type => 1：未使用；2：已使用；3：已过期)
        if ((int)$params['type'] == 1) {            
            $sqlmap['status'] = 1;
            $sqlmap['end_time'] = array('GT',NOW_TIME);                       
        }elseif((int)$params['type'] == 2) {
            $sqlmap['status'] = array('EQ',2);
        }else{
            $sqlmap['status'] = array('NEQ',2);
            $sqlmap['end_time'] = array('lT',NOW_TIME);
        }
        // 优惠券存在的才查找
        $coupons_ids = model('coupons')->getField('id', TRUE);
        $sqlmap['cid'] = array("IN", $coupons_ids);
        $this->sqlmap = $sqlmap;
        return $sqlmap;
    }

    /**
     * 
     * 获取未使用的张数
     * @param  $uid 用户id
     */
    public function getCount($uid){
        $coupons_ids = M('Coupons')->where($sqlmap)->getField('id', TRUE);
        $sqlmap = array();
        $sqlmap['user_id'] = $uid;
        $sqlmap['cid'] = array("IN", $coupons_ids);
        $sqlmap['start_time'] = array("LT", NOW_TIME);
        $sqlmap['end_time'] = array("GT", NOW_TIME);
        $sqlmap['status'] = 1;
        return (int)$this->where($sqlmap)->count();
    }


    /**
     * 
     * 检查状态
     * @param  $key 序列号
     */
    public function checkKey($key) {
        $map = array(); 
        $map['sn'] = $key;
        $list = $this->where($map)->find();
        if(empty($list)){//不存在
            return false;
        }
        if ($list['status']==1){//已发放
            return false;
        }
        if ($list['status']==2){//已使用
            return false;
        }
        if ($list['status']==3){//已警用
            return false;
        }
        if ($list['end_time']<time()){//已过期
            return false;
        }
        return $list['id'];//可以使用
        
    }    

}
