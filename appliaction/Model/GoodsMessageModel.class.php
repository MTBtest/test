<?php 
/**
 *      到货通知
 *      [Haidao] (C)2013-2099 Dmibox Science and technology co., LTD.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      http://www.haidao.la
 *      tel:400-600-2042
 */
class GoodsMessageModel extends SystemModel{
	protected $_validate  = array();
	protected $_auto = array(
        array('dateline', NOW_TIME, Model:: MODEL_INSERT, 'string'), 
        array('status','0', Model:: MODEL_INSERT, 'string'),
        array('clientip','get_client_ip', Model:: MODEL_INSERT, 'function'),
	);
	/**
	 * 获取详细列表数据
	 * @param type $map
	 * @return type array
	 */
	public function getList($map, $group) {
		$count = $this -> where($map) -> group($group) -> getField('id', true);
		$count = count($count);
		libfile('Page');
		$pagesize = $_GET['pagesize'];
		$pagesize = $pagesize ? $pagesize : getconfig('page_num');
		$Page = new Page($count, $pagesize);
		$Page -> listRows = $pagesize;
		$result = $this -> field('id,goods_id,product_id, count(id) AS count') -> where($map) -> group($group) -> order('id DESC') -> limit($Page -> firstRow, $Page -> listRows) -> select();
		$list['list'] = $result;
		$show = $Page -> show();
		$list['page'] = $show;

		return $list;
	}

	/**
	 * 获取详细列表数据
	 * @param type $map
	 * @return type array
	 */
	public function getDetail($map) {
		$count = $this -> where($map) -> group($group) -> getField('id', true);
		$count = count($count);
		libfile('Page');
		$pagesize = $_GET['pagesize'];
		$pagesize = $pagesize ? $pagesize : getconfig('page_num');
		$Page = new Page($count, $pagesize);
		$Page -> listRows = $pagesize;
		$result = $this -> where($map) -> order('id DESC') -> limit($Page -> firstRow, $Page -> listRows) -> select();
		$list['list'] = $result;
		$show = $Page -> show();
		$list['page'] = $show;
		return $list;
	}

}