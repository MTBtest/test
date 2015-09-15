<?php
/**
 *      [Haidao] (C)2013-2099 Dmibox Science and technology co., LTD.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      http://www.haidao.la
 *      tel:400-600-2042
 */
class HomeBaseController extends BaseController {
    private $cart;
    public function _initialize() {
    	define('IN_PLUGIN', TRUE);
		define('PLUGIN_ID', 'hello');		
		parent::_initialize();
		//商品分类
		$this->cate= model('goods_category')->where('status=1 and show_in_nav=1')->order('parent_id ASC,sort ASC,id ASC')->select();
		$this->goods_category = list_to_tree($this->cate, 'id', 'parent_id', '_child', 0) ;
		//商品品牌
		$this->brandArr = model('brand')->where('status=1')->order('sort DESC')->select();

		if(C('site_state') == 0){
	        showmessage('站点已关闭');
	    }
    }
}