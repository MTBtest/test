<?php
class brand
{
    public function __construct() {
        $this->db = D('Brand');
    }
    
    // 获取品牌列表
    public function lists($data){
        extract($data);
        $sqlmap = array();        
        if ($status) $sqlmap['status'] = $status;                   // 是否显示
        if ($isrecommend) $sqlmap['isrecommend'] = $isrecommend;    //  是否推荐
        $sqlmorder = $order ? $order : "`sort` desc,`id` desc";     //排序
        // 商品分类ID
        if(isset($catid) && is_numeric($catid) && $catid > 0) {
            $catids = model('Category')->getChild($catid);
            if(empty($catids)) return FALSE;
            foreach ($catids as $catid) {
                $join[] = "find_in_set('{$catid}',cat_ids)";
            }
            $brand_map             = array();
            $brand_map['status']   = 1;
            $brand_map['brand_id'] = array("GT", 0);
            $brand_map['_string']  = implode(' OR ', $join);
            $brand_ids = model('Goods')->where($brand_map)->group('brand_id')->getField('brand_id', TRUE);
            if(!$brand_ids) return FALSE;
        }
        if($brand_ids) {
            $sqlmap['id'] = array("IN", $brand_ids);
        }
        if($where) {
            $sqlmap['_string'] = $where;
        }
        if(isset($page)) {
            $this->db->page($page, $limit);
        } else {
            $this->db->limit($limit);
        } 
        $result = $this->db->where($sqlmap)->order($sqlmorder)->select();
        return $result;
    }
}