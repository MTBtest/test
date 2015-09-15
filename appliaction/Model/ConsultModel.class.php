<?php
/**
 *      咨询模型
 *      [Haidao] (C)2013-2099 Dmibox Science and technology co., LTD.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      http://www.haidao.la
 *      tel:400-600-2042
 */
class ConsultModel extends SystemModel {
    protected $_validate = array(
        array('question','require','内容不能为空！', Model::EXISTS_VALIDATE, 'regex', Model:: MODEL_INSERT), 
        array('goods_id','require','咨询商品不能为空！', Model::EXISTS_VALIDATE, 'regex', Model:: MODEL_BOTH), 
    );

    protected $_auto = array(
        array('time', NOW_TIME , Model:: MODEL_INSERT, 'string'), 
        array('status','0', Model:: MODEL_INSERT, 'string'),
        array('ip_address','get_client_ip', Model:: MODEL_INSERT, 'function'),
    );

    /* 统计数据 */
    public function total($params = array()) {
        if(!$this->sqlmap && count($params) > 0) $this->buildMap($params);
        return $this->where($this->sqlmap)->count();
    }

    /* 获取数据 */
    public function lists($params = array()) {
        $DB = & $this;
        $this->build_map($params);
        $result = $lists = array();
        // 排序方式
        $order = (isset($params['sort'])) ? $params['sort'] : '`time` DESC';
        $limit = (isset($params['limit'])) ? (int) $params['limit'] : 8;  
        
        // 分页大小
        if(isset($params['page'])) {
            $result['total'] = $this->total($params);
            $result['page'] = pages($result['total'], $limit);
            $DB->page($params['page']);
        }              
        $items = $DB->where($this->sqlmap)->order($order)->limit($limit)->select();
        if(!$items) return array('status' => 'error', 'info' => '没有符合条件的内容');
            foreach ($items as $item) {
                $item['ico'] = '';
                if($item['user_id'] > 0) {
                    $item['ico'] = model('user')->getFieldById($item['user_id'], 'ico');
                }
                $item['user_name'] = (empty($item['user_name'])) ? '游客' : $item['user_name'];
                $item['content'] = $item['question'];
                $item['dataline'] = mdate($item['time']);
                $lists[] = $item;
            }
        if(isset($result['total'])) {
            $result['lists'] = $lists;
        } else {
            $result = $lists;
        }
        return $result;
    }

    /* 生成查询条件 */
    private function build_map($params = array()) {
        $sqlmap = array();
        // 用户ID
        if((int)$params['user_id'] > 0) {
            $sqlmap['user_id'] = $params['user_id'];
        }
        // 商品ID
        if((int) $params['goods_id'] > 0) {
            $sqlmap['goods_id'] = $params['goods_id'];
        }
        // 产品ID
        if((int) $params['product_id'] > 0) {
            $sqlmap['product_id'] = $params['product_id'];
        }
        // 咨询状态
        if(isset($params['status']) && is_numeric($params['status'])) {
            $sqlmap['status'] = $params['status'];
        }
        $this->sqlmap = $sqlmap;
        return $sqlmap;
    }
}