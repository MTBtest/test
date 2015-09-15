<?php
/**
 * @version        $Id$
 * @author         master@xuewl.com
 * @copyright      Copyright (c) 2007 - 2014, Chongqing xuewl Information Technology Co., Ltd.
 * @link           http://www.xuewl.com
**/
class CommentModel extends SystemModel{
	protected $_validate = array(
		array('user_id','require','非法操作！'),
		array('goods_id','require','请选择评论商品！'),
		array('order_id','require','请选择评论商品！'),
		array('user_name','require','非法操作！'),
		array('content','require','请填写评论内容！'),
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
        if(isset($params['order_id']) && !empty($params['order_id'])) {
        	$sqlmap['order_id'] = $params['order_id'];
        }
        $this->sqlmap = $sqlmap;
        return $sqlmap;
    }

}
?>