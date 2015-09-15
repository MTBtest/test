<?php
/**
 *      [Haidao] (C)2013-2099 Dmibox Science and technology co., LTD.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      http://www.haidao.la
 *      tel:400-600-2042
 */
class UserCollectModel extends SystemModel {
    protected $sqlmap;   
    public function total($params = array()) {
        if(!$this->sqlmap && count($params) > 0) $this->buildMap($params);
        return $this->where($this->sqlmap)->count();
    }
    
    /**
     * 获取收藏夹列表
     * @param type $params
     */
    public function lists($params = array()) {
        $DB = & $this;
        $this->buildMap($params);
        $result = $lists = array();
        // 排序方式
        $order = (isset($params['sort'])) ? $params['sort'] : '`add_time` DESC';
        $limit = (isset($params['limit'])) ? (int) $params['limit'] : 8;  
        
        // 分页大小
        if(isset($params['page'])) {
            $result['total'] = $this->total($params);
            $result['page'] = pages($result['total'], $limit);
            $DB->page($params['page']);
        }              
        $items = $DB->where($this->sqlmap)->order($order)->limit($limit)->select();
        if(!$items) {
            $this->error = '没有符合条件的内容';
            return FALSE;
        }
        foreach ($items as $item) {
            $item['goods_info'] = model('goods')->detail($item['goods_id']);
            $lists[] = $item;
        }
        $result['lists'] = $lists;
        return $result;
    }
    
    private function buildMap($params = array()) {
        $sqlmap = array();
        // 用户ID
        if((int)$params['user_id'] > 0) {
            $sqlmap['user_id'] = $params['user_id'];
        }
        // 商品ID
        if((int) $params['goods_id'] > 0) {
            $sqlmap['goods_id'] = $params['goods_id'];
        }
        // TUDO::收藏时间
        $type = (int) $params['type'];
        $time = 0;
        switch ($type) {
            case '1':
                $time = time() - 86400 * 7;
                break;
            case '2':
                $time = time()  - 86400 * 30;
                break;
            case '3':
                $time = time()  - 86400 * 90;
                break;
            case '4':
                $time = time()  - 86400 * 180;
                break;
            case '5':
                $time = time()  - 86400 * 365;
                break;
            default:
                break;
        }
        if($time >　0) {
            $sqlmap['add_time'] = array('GT',$time);
        }
        $this->sqlmap = $sqlmap;
        return $sqlmap;
    }
}
