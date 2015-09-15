<?php
/**
 *      文章列表标签
 *      [Haidao] (C)2013-2099 Dmibox Science and technology co., LTD.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      http://www.haidao.la
 *      tel:400-600-2042
 */
class article{
	function __construct() {
		$this->db = D('Article');
	}

	/* 获取总数 */
	public function count($attr) {
		return $this->db->where($this->build_map($attr))->count();
	}

	/* 列表方法 */
	public function lists($attr){

		extract($attr);		
		$order = (!empty($order)) ? $order : "`top` DESC,`sort` DESC,`id` DESC";
		/* 是否开启分页 */
		if (isset($page)) {
			$this->db->page($page, $limit);
		
		} else {
			$this->db->limit($limit);
		}
		
		return $this->db->where($this->build_map($attr))->order($order)->select();
	}

	/* 获取SQL查询语句 */
	private function build_map($data) {
		//extract($attr);
		/* 已审核 */
		$sqlmap = array('status' => 1);
		/* 置顶条件 */
		if (isset($top)) {
			$sqlmap['top'] = (int) $top;
		}
		/* 是否分类 */
		if (isset($data['catid'])) {
			$childs = $this->getChild($data['catid']);
			$catids[] = $data['catid'];
			foreach ($childs as $key => $value) {
				$catids[] = $value;
			}
			$sqlmap['category_id'] = array('IN',$catids);
		}
		if ($where) $sqlmap['_string'] = $where;
		return $sqlmap;
	}

	/* 获取指定分类的下级分类 */
	private function getChild($cid){
        $return = array();
        $ids = M('ArticleCategory')->where(array('parent_id'=>$cid))->getField('id',TRUE);
        $return = $ids;
        if(is_array($ids)){
            foreach ($ids as $key => $value) {
                $child = $this->getChild($value);
                if(!empty($child)){
                    $return = array_merge($return, $child);
                }
            }
        }
        return $return;
    }
}