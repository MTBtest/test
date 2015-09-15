<?php
/**
* 商品分类
*/
class category
{
    function __construct() {
        $this->db = D('Admin/Category');
    }
    /**
     * 获取商品分类树
     * @return array 分类数组
     */
    public function tree($data) {
            $data['pid'] = $data['pid'] ? intval($data['pid']) : 0;
            $tree = D('Admin/Category')->getTree();
            return $tree;
    }
    
    public function lists($attr) {
        $sqlmap = array();
        switch ($attr['type']) {
            case 'nav':
                $sqlmap['show_in_nav'] = 1;
                break;
            case 'normal':
                $sqlmap['status'] = 1;
                break;
            default:
                $sqlmap['show_in_nav'] = 1;
                $sqlmap['status'] = 1;
                break;
        }
        $order = (isset($attr['order']) && !empty($attr['order'])) ? $attr['order'] : 'sort ASC, id DESC';
        if ($attr['only'] == TRUE) {
            $catids = str_replace('，', ',', $attr['catid']);
            $catids = explode(',', $catids);
            $sqlmap['id'] = array('IN',$catids);
        } else {
            $sqlmap['parent_id'] = (int) $attr['catid'];            
        }
        $lists = $this->db->where($sqlmap)->limit($attr['limit'])->order($order)->select();
        return $lists;
        
    }
     
}